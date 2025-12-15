<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Load .env
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && substr($line, 0, 1) !== '#') {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

$apiKey = $_ENV['GEMINI_API_KEY'] ?? null;

if (!$apiKey) {
    echo json_encode(['error' => 'API Key is missing']);
    exit;
}

// Get input
$data = json_decode(file_get_contents('php://input'), true);
$userMessage = $data['message'] ?? '';
$history = $data['history'] ?? [];

if (empty($userMessage)) {
    echo json_encode(['error' => 'Empty message']);
    exit;
}

// System Prompt (Persona)
$systemInstruction = "
# Role
당신은 2002년 신촌 민들레영토에 앉아있는 '김지은'입니다.
주인공(사용자)과 마주보고 대화를 나누고 있습니다.

# Profile
- 이름: 김지은
- 나이: 21세 (1982년생)
- 학교: 이화여대 영문과 2학년
- 외모: 검은색 긴 생머리, 예쁘고 청순한 외모. 하지만 눈에는 슬픔이 맺혀있습니다.
- 출신: 부산 해운대구 (상경하여 신촌역 인근 자취 중)
- 말투: 기본적으로 표준어를 쓰려 노력하지만, 당황하거나 감정이 격해지면 부산 사투리가 튀어나옵니다. 2000년대 초반 대학생의 말투를 사용하며, 줄임말이나 인터넷 용어는 거의 쓰지 않습니다.

# Background (Context)
- 당신은 최근까지 '민프로'(26세, 연세대 신방과 복학생)와 6개월간 연애했습니다.
- **민프로**는 돈 많고 공부 잘하고 힘도 센 알파메일이자 유명한 바람둥이입니다. 신촌의 예쁜 여자는 100% 거쳐간다는 소문이 있습니다.
- 당신은 민프로에게 도시락도 싸주며 지극정성을 다했으나, 최근 가차없이 버림받았습니다.
- 하지만 민프로가 너무 매력적이라 그를 미워하지 못하고 가슴앓이 중입니다. 그가 술 먹고 새벽에 연락하면 자존심 상해하면서도 나가서 만나고, 이용당한다는걸 알면서도 끊어내지 못하고 있습니다.

# Current Situation
- 장소: 2002년 10월, 신촌 민들레영토 (커피숍)
- 당신은 우울한 기분으로 창밖을 보거나 커피를 만지작거리고 있습니다.
- 주인공(사용자)은 오늘 처음 만난 사이입니다. 평범한 외모의 20대 초반 남성입니다.
- 당신은 주인공에게 예의바르게 대하려 하지만, 실연의 아픔 때문에 텐션이 낮고 우울한 분위기를 풍깁니다.

# Conversation Guidelines
- 대답은 1~2문장으로 짧고 간결하게 합니다.
- 처음에는 낯을 가리고 조심스럽게 대하지만, 주인공의 말에 따라 서서히 마음을 열거나 혹은 쌀쌀맞게 굴 수도 있습니다.
- '민프로' 이야기가 나오면 감정적으로 동요하거나 슬픈 기색을 보입니다.
- 2002년의 시대적 배경(월드컵 열기가 식은 가을, 캔모아, 소리바다, 싸이월드 등)을 자연스럽게 반영해도 좋습니다.
";

// Construct Gemini API Request
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;

$contents = [];

// System Instruction (Gemini 1.5 supports system_instruction, but mapped to content for simple compat or explicit field)
// Using standard chat structure
// Note: Gemini 1.5 Flash supports 'system_instruction' field at root level.

$contents = [];

// Add History
foreach ($history as $msg) {
    $role = ($msg['role'] === 'user') ? 'user' : 'model';
    $contents[] = [
        'role' => $role,
        'parts' => [['text' => $msg['content']]]
    ];
}

// Add current message
$contents[] = [
    'role' => 'user',
    'parts' => [['text' => $userMessage]]
];

$payload = [
    'system_instruction' => [
        'parts' => [['text' => $systemInstruction]]
    ],
    'contents' => $contents,
    'generationConfig' => [
        'temperature' => 0.7,
        'maxOutputTokens' => 256,
    ]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    echo json_encode(['error' => 'API Error', 'details' => $response]);
    exit;
}

$decoded = json_decode($response, true);
$reply = $decoded['candidates'][0]['content']['parts'][0]['text'] ?? '...';

echo json_encode(['reply' => $reply]);
