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
                <div class="character-layer text-center">
                    <img src="npc_girl/kim_ji_eun/1.png" alt="김지은" class="character-sprite">
                </div>
            </div>
        </div>

        <!-- Text/Chat Area (Right on PC, Bottom on Mobile) -->
        <div class="col-lg-4 col-xl-3 col-12 chat-interface d-flex flex-column">
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

<!-- jQuery & Bootstrap Bundle -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Game Logic -->
<script src="js/game.js"></script>

</body>
</html>