<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>ZMAN</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts (Neo Dunggeunmo for retro feel or Noto Sans KR) -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@400;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="game-body">

    <!-- Main Game Screen -->
    <div id="gameScreen" class="container-fluid vh-100 p-0">
        <div class="row g-0 h-100">
            <!-- Left: Visual Area -->
            <div class="col-lg-8 col-xl-9 col-12 visual-area-wrapper d-flex align-items-center justify-content-center">
                <div class="game-stage position-relative text-center">
                    <img id="bgImage" src="area/minto/1.jpg" alt="배경" class="background-img">
                    <div class="character-layer text-center" style="display: none;">
                        <img id="charImage" src="npc_girl/kim_ji_eun/1.png" alt="김지은" class="character-sprite">
                    </div>
                    <div id="affinityDisplay" class="position-absolute bottom-0 end-0 p-3" style="z-index: 10;"></div>
                </div>
            </div>

            <!-- Right: Chat Interface -->
            <div class="col-lg-4 col-xl-3 col-12 chat-interface d-flex flex-column">
                <!-- Chat Header -->
                <div class="chat-header p-2 border-bottom d-flex justify-content-between align-items-center bg-light">
                    <span class="fw-bold ms-2" id="gameClock"></span>
                    <div class="d-flex align-items-center">
                        <!-- BGM Toggle -->
                        <button class="btn btn-sm btn-outline-secondary me-2 border-0" id="bgmToggleBtn" title="배경음악 끄기/켜기">
                            🔊
                        </button>

                        <button class="btn btn-sm btn-outline-danger ms-1" onclick="if(confirm('정말 나가시겠습니까?')) location.href='./'">
                            🚪 나가기
                        </button>
                    </div>
                </div>

                <!-- Game/Dialogue Log -->
                <div class="chat-log flex-grow-1 p-3" id="chatLog">
                    <div class="message system">
                        <span class="badge bg-secondary">SYSTEM</span> 신촌 민들레영토에서 당신은 지은과 마주보고 앉아있다.
                    </div>
                    <!-- Messages will be appended here -->
                </div>

                <!-- Input Area -->
                <div class="chat-input-area p-3 bg-light border-top">
                    <div class="input-group">
                        <input type="text" class="form-control" id="userInput" placeholder="" autocomplete="off" maxlength="200">
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
<?php
// Get BGM Files
$bgmFiles = glob('bgm/*.mp3');
// Base URL adjustments if needed, assuming relative path 'bgm/...' works from play.php
// Force encoding to ensure valid JSON
$bgmJson = json_encode($bgmFiles);
?>
<script>
    const bgmPlaylist = <?php echo $bgmJson; ?>;
</script>
<script src="js/game.js"></script>

</body>
</html>