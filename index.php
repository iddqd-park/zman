<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>2002 신촌, 그녀와의 만남</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts (Neo Dunggeunmo for retro feel or Noto Sans KR) -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@400;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

<div class="container-fluid h-100 p-0">
    <div class="row g-0 h-100">
        <!-- Visual Area (Left on PC, Top on Mobile) -->
        <div class="col-lg-8 col-xl-9 col-12 visual-area-wrapper d-flex align-items-center justify-content-center">
            <div class="game-stage position-relative text-center">
                <img src="area/minto/1.jpg" alt="배경" class="background-img">
                <div class="character-layer text-center" style="display: none;">
                    <img src="npc_girl/kim_ji_eun/1.png" alt="김지은" class="character-sprite">
                </div>
                <div id="affinityDisplay" class="position-absolute bottom-0 end-0 p-3" style="z-index: 10;"></div>
            </div>
        </div>

        <!-- Text/Chat Area (Right on PC, Bottom on Mobile) -->
        <div class="col-lg-4 col-xl-3 col-12 chat-interface d-flex flex-column">
            <!-- Chat Header -->
            <div class="chat-header p-2 border-bottom d-flex justify-content-between align-items-center bg-light">
                <span class="fw-bold ms-2" id="gameClock">2002년 12월 15일 오후 ?시 ?분</span>
                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#settingsModal">
                    ⚙️ 설정
                </button>
            </div>

            <!-- Game/Dialogue Log -->
            <div class="chat-log flex-grow-1 p-3" id="chatLog">
                <div class="message system">
                    <span class="badge bg-secondary">SYSTEM</span> 2002년 10월, 신촌 민들레영토.<br>
                    당신은 지은과 마주보고 앉아있다.
                </div>
                <!-- Messages will be appended here -->
            </div>

            <!-- Input Area -->
            <div class="chat-input-area p-3 bg-light border-top">
                <div class="input-group">
                    <input type="text" class="form-control" id="userInput" placeholder="지은에게 할 말을 입력하세요..." autocomplete="off">
                    <button class="btn btn-primary" type="button" id="sendBtn">말하기</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Settings Modal -->
<div class="modal fade" id="settingsModal" tabindex="-1" aria-labelledby="settingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="settingsModalLabel">2002 신촌, 그녀와의 만남</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-4">
                    2002년의 감성을 담은 미연시 게임입니다.<br>
                    그녀와의 대화를 통해 이야기를 풀어나가보세요.
                </p>

                <div class="d-grid gap-2 mb-4">
                    <button class="btn btn-outline-warning" id="clearChatBtn">
                        🧹 대화창 비우기 (기록 유지)
                    </button>
                    <button class="btn btn-outline-danger" id="resetGameBtn">
                        🔄 게임 완전 재시작 (기록 삭제)
                    </button>
                </div>

                <hr>

                <div class="text-center small text-secondary">
                    <p class="mb-2">게임에 대한 의견은 X(Twitter)나 Threads로 부탁드립니다.</p>
                    <a href="https://iddqd.kr" target="_blank" class="text-decoration-none fw-bold text-dark">
                        IDDQD 인터넷 제공
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
    </div>
</div>

<!-- jQuery & Bootstrap Bundle -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Game Logic -->
<script src="js/game.js"></script>

</body>
</html>