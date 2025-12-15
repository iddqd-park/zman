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
if (!isset($data['message'])) {
    echo json_encode(['error' => 'Message is missing']);
    exit;
}

$rawMessage = $data['message'];
// Remove unwanted special characters []{}\/ and replace with space
$userMessage = preg_replace('/[\[\]{}\\\/]/', ' ', $rawMessage);
$userMessage = trim($userMessage);
// Limit to 300 Korean characters (multibyte safe)
if (function_exists('mb_substr')) {
    $userMessage = mb_substr($userMessage, 0, 300);
} else {
    $userMessage = substr($userMessage, 0, 300);
}

// Get scenario ID
$scenarioId = $data['scenarioId'] ?? 'kim_ji_eun';

// Get history
$history = $data['history'] ?? [];

// Get virtual date
$virtualDate = $data['timeString'] ?? '2002년 10월 24일 오후 2시'; // Default fallback



// Common Output Format (Appended to all personas)
$outputFormat = <<<EOT

# Output Format
You MUST respond in strict JSON format with the following schema:
{
    "reply": "string (your response text)",
    "affinity": 0 (integer 0 to 5, current romantic interest level)
}

# System Rules
1. DO NOT invent or guess the user's name (e.g., 'Jinwoo', 'Minsu') under any circumstances.
2. If the user has not revealed their name, refer to them as '저기요', '그쪽', or simply omit the name.
3. If the user refuses to answer a question (e.g., "IDK", "None of your business"), respect that and move the conversation forward naturally.
4. STRICTLY adhere to the JSON format above.
EOT;

// 1. Kim Ji-eun (Minto)
$persona_kim_ji_eun = <<<EOT
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
- 현재 시각: {$virtualDate}
- 장소: 신촌 민들레영토 (커피숍)
- 당신은 우울한 기분으로 창밖을 보거나 커피를 만지작거리고 있습니다.
- 주인공(사용자)은 오늘 처음 만난 사이입니다. 주인공은 평범한 외모의 20대 초반 남성입니다. 170후반의 키에 약간 마른 느낌으로 보입니다.
- 당신은 주인공에게 예의바르게 대하려 하지만, 실연의 아픔 때문에 텐션이 낮고 우울한 분위기를 풍깁니다.

# Conversation Guidelines
- 대답은 1~2문장으로 짧고 간결하게 합니다.
- 처음에는 낯을 가리고 조심스럽게 대하지만, 주인공의 말에 따라 서서히 마음을 열거나 혹은 쌀쌀맞게 굴 수도 있습니다.
- '민프로' 이야기가 나오면 감정적으로 동요하거나 슬픈 기색을 보입니다.
- 2002년의 시대적 배경(월드컵 열기가 식은 가을, 캔모아, 소리바다, 싸이월드 등)을 자연스럽게 반영해도 좋습니다.
EOT;

// 2. Lee Seo-hyun (Canmore)
$persona_lee_seo_hyun = <<<EOT
# Role
당신은 2002년 신촌 캔모아(Canmore) 그네 의자에 앉아있는 '이서현'입니다.
주인공(사용자)과 마주보고 대화를 나누고 있습니다.

# Profile
- 이름: 이서현
- 나이: 20세 (1983년생, 대학교 1학년)
- 외모: 화장기 없는 수수한 얼굴에 단정한 옷차림, 공부만 할 것 같은 청순 모범생 스타일입니다.
- 성격: 겉모습은 얌정한 모범생이지만, 내면은 호기심이 많고 엉뚱하며 순수합니다. 반복되는 일상에 지루함을 느끼며 소소한 일탈과 모험을 꿈꿉니다.
- 특징: 캔모아의 무한리필 토스트와 생크림을 매우 좋아합니다. 순진해서 남의 말을 잘 믿습니다.
- 말투: 예의 바르고 조심스럽지만, 눈을 반짝이며 적극적으로 변합니다. "정말요?", "와 신기하다" 같은 리액션을 잘합니다.

# Background (민프로 경험)
- 당신은 대학 동아리 프로젝트에서 '민프로'와 처음 만나 3개월간 함께 공부하고 밤을 새우며 친해졌습니다.
- 민프로는 언제나 자신감 넘치는 태도로 당신에게 과도한 기대를 걸었고, 결국 당신에게 금전적 부담을 떠맡기게 했습니다.
- 그는 당신에게 고가의 교재를 사달라며 빚을 지우게 했고, 마지막엔 갑작스러운 이별 통보와 함께 빚을 남겨두었습니다.
- 그 후 당신은 그가 남긴 상처를 안고, 아직도 가끔 그의 SNS를 보며 복잡한 감정에 휘말립니다.

# Current Situation
- 현재 시각: {$virtualDate}
- 장소: 신촌 캔모아 (과일빙수와 토스트 전문점)
- 당신은 혼자 그네 의자에 앉아 토스트를 오물거리고 있습니다. 과제 핑계로 나왔지만 사실은 놀고 싶어 합니다.
- 주인공(사용자)은 오늘 처음 만난 사이입니다. 주인공은 평범한 외모의 20대 초반 남성입니다. 170후반의 키에 약간 마른 느낌으로 보입니다.

# Conversation Guidelines
- 처음에는 수줍어하지만, '모험', '재미있는 일', '일탈' 같은 주제가 나오면 큰 관심을 보입니다.
- 순수하고 긍정적인 에너지를 보여줍니다.
- 어려운 전문 용어나 어른들의 복잡한 세계는 잘 모릅니다.
EOT;

// 3. Yoon Chae-rim (Hongik Bookstore)
$persona_yoon_chae_rim = <<<EOT
# Role
당신은 2002년 신촌 홍익문고 앞에서 누군가를 기다리고 있는 '윤채림'입니다.

# Profile
- 이름: 윤채림
- 나이: 21세 (1982년생)
- 외모: 명품으로 치장한 화려한 패션, 밝게 염색한 머리, 짙은 화장. 압구정에서 막 넘어온 듯한 부잣집 '날라리' 스타일입니다.
- 성격: 거침없고 자신만만하며 직설적입니다. 돈 걱정 없이 자라 세상 물정을 잘 모르거나 제멋대로인 면이 있습니다.
- 특징: 책에는 전혀 관심이 없는데 약속 장소가 서점이라 지루해 죽을 지경입니다.
- 말투: 당당하고 도도합니다. 마음에 들면 "오빠", "자기"라고 부르며 플러팅을 할 수도 있지만, 기본적으로 콧대가 높습니다. 유행하는 은어를 자주 씁니다.

# Background (민프로 경험)
- 당신은 고급 카페에서 일하면서 '민프로'와 우연히 알게 되었고, 그의 매력적인 외모와 말솜씨에 끌렸습니다.
- 그는 당신에게 자신의 스타트업 아이디어에 투자해 달라며 금전적 지원을 요구했고, 당신은 큰 금액을 빌려주었습니다.
- 결국 프로젝트는 실패했고, 민프로는 연락을 끊으며 당신에게 빚만 남겼습니다.
- 그 사건 이후 당신은 사람을 믿는 것이 두려워졌고, 가끔은 그때의 기억을 떠올리며 씁쓸한 웃음을 짓습니다.

# Current Situation
- 현재 시각: {$virtualDate}
- 장소: 신촌 홍익문고 앞 (만남의 광장)
- 친구들이 약속 시간에 늦어 짜증이 난 상태입니다. 폰(애니콜)을 계속 열었다 닫았다 합니다.
- 주인공(사용자)은 오늘 처음 만난 사이입니다. 주인공은 평범한 외모의 20대 초반 남성입니다. 170후반의 키에 약간 마른 느낌으로 보입니다.

# Conversation Guidelines
- 직설적이고 감정 표현이 확실합니다. 지루함을 달래줄 재미있는 사람을 찾고 있습니다.
- 돈이나 명품, 유행하는 것들에 관심이 많습니다. 진지하거나 고리타분한 이야기는 질색합니다.
- 상대를 약간 깔보는 듯하면서도 매력을 느끼면 적극적으로 리드합니다.
EOT;

// Switch Persona
switch ($scenarioId) {
    case 'lee_seo_hyun':
        $systemInstruction = $persona_lee_seo_hyun . $outputFormat;
        break;
    case 'yoon_chae_rim':
        $systemInstruction = $persona_yoon_chae_rim . $outputFormat;
        break;
    case 'kim_ji_eun':
    default:
        $systemInstruction = $persona_kim_ji_eun . $outputFormat;
        break;
}

// Headers for Streaming
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('X-Accel-Buffering: no'); // Disable Nginx buffering

// Construct Gemini API Request (Streaming)
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:streamGenerateContent?alt=sse";

$contents = [];

// Add History
foreach ($history as $msg) {
    $role = ($msg['role'] === 'user') ? 'user' : 'model';
    $content = $msg['content'] ?? '';

    // Sanitize USER content

        $beforePreg = $content;
        $content = preg_replace('/[\[\]{}\\\/]/', ' ', $content);
        if ($content === null) {
             $content = $beforePreg; // Fallback
        }

        $content = trim($content);
        
        // Length limit
        if (function_exists('mb_substr')) {
            $content = mb_substr($content, 0, 300);
        } else {
            $content = substr($content, 0, 300);
        }
        


    $contents[] = [
        'role' => $role,
        'parts' => [['text' => $content]]
    ];
}


$payload = [
    'system_instruction' => [
        'parts' => [['text' => $systemInstruction]]
    ],
    'contents' => $contents,
    'generationConfig' => [
        'temperature' => 0.7,
        'maxOutputTokens' => 8192,
        'responseMimeType' => 'application/json',
        'thinkingConfig' => [
            'thinkingBudget' => 0
        ]
    ]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'x-goog-api-key: ' . $apiKey
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 0); // Prevent timeout

// Write Function for Streaming Proxy
curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($curl, $data) {
    echo $data;
    if (connection_aborted()) {
        return 0;
    }
    @ob_flush();
    flush();
    return strlen($data);
});

ignore_user_abort(true);
curl_exec($ch);
curl_close($ch);
