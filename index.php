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
<body>

    <!-- Intro Screen -->
    <div id="introScreen" class="container-fluid min-vh-100 py-5 d-flex flex-column align-items-center justify-content-center bg-dark text-white position-relative" style="z-index: 2000;">
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold mb-4 intro-title">ZMAN</h1>
            <h2 class="lead text-light mb-4">2002년 신촌으로의 시간여행, 국내 최초 LLM NPC 미연시</h2>
            <p class="small text-white-50 mx-auto" style="max-width: 600px;">
                기본적인 대화만 제공하는 MVP 테스트 버전입니다. 회원가입은 필요 없으며, 대화 내용은 서버에 저장되지 않고 브라우저 창을 닫으면 초기화 됩니다. 버그 리포팅 및 각종 의견은 X (Twitter) 및 Threads를 통해서 연락 부탁드려요.
            </p>
        </div>

        <div class="row g-4 justify-content-center w-100 px-3" style="max-width: 1200px;">
            <!-- Scenario 1: Lee Seo-hyun -->
            <div class="col-12 col-md-4">
                <div class="card scenario-card h-100 bg-secondary border-0 text-white" role="button" onclick="location.href='play.php?scenario=lee_seo_hyun'">
                    <div class="card-img-top position-relative overflow-hidden" style="height: 300px;">
                        <img src="area/canmore/1.jpg" class="w-100 h-100 object-fit-cover opacity-50 scenario-bg">
                        <img src="npc_girl/lee_seo_hyun/1.png" class="position-absolute bottom-0 start-50 translate-middle-x scenario-char" style="height: 90%;">
                    </div>
                    <div class="card-body text-center">
                        <h3 class="card-title fw-bold">이서현</h3>
                        <p class="card-text text-light small">캔모아에서 일탈을 꿈꾸는 모범생</p>
                        <span class="badge bg-light text-dark">난이도: ★★☆☆☆</span>
                    </div>
                </div>
            </div>

            <!-- Scenario 2: Kim Ji-eun -->
            <div class="col-12 col-md-4">
                <div class="card scenario-card h-100 bg-secondary border-0 text-white" role="button" onclick="location.href='play.php?scenario=kim_ji_eun'">
                    <div class="card-img-top position-relative overflow-hidden" style="height: 300px;">
                        <img src="area/minto/1.jpg" class="w-100 h-100 object-fit-cover opacity-50 scenario-bg">
                        <img src="npc_girl/kim_ji_eun/1.png" class="position-absolute bottom-0 start-50 translate-middle-x scenario-char" style="height: 90%;">
                    </div>
                    <div class="card-body text-center">
                        <h3 class="card-title fw-bold">김지은</h3>
                        <p class="card-text text-light small">민들레영토의 슬픈 눈을 가진 그녀</p>
                        <span class="badge bg-light text-dark">난이도: ★★★☆☆</span>
                    </div>
                </div>
            </div>

            <!-- Scenario 3: Yoon Chae-rim -->
            <div class="col-12 col-md-4">
                <div class="card scenario-card h-100 bg-secondary border-0 text-white" role="button" onclick="location.href='play.php?scenario=yoon_chae_rim'">
                    <div class="card-img-top position-relative overflow-hidden" style="height: 300px;">
                        <img src="area/hongik_book/1.jpg" class="w-100 h-100 object-fit-cover opacity-50 scenario-bg">
                        <img src="npc_girl/yoon_chae_rim/1.png" class="position-absolute bottom-0 start-50 translate-middle-x scenario-char" style="height: 90%;">
                    </div>
                    <div class="card-body text-center">
                        <h3 class="card-title fw-bold">윤채림</h3>
                        <p class="card-text text-light small">홍익문고 앞의 화려한 압구정 날라리</p>
                        <span class="badge bg-light text-dark">난이도: ★★★★☆</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-5 text-center">
             <div class="d-flex gap-3 justify-content-center">
                <a href="https://x.com/iddqd_park" target="_blank" class="btn btn-outline-light btn-sm rounded-pill px-4">X (Twitter)</a>
                <a href="https://www.threads.net/@iddqd.park" target="_blank" class="btn btn-outline-light btn-sm rounded-pill px-4">Threads</a>
            </div>
            <p class="mt-3 text-white-50 small">
                <a href="https://iddqd.kr" target="_blank" class="text-white-50 text-decoration-none footer-link">IDDQD 인터넷 제공</a>
            </p>
        </div>
    </div>

<!-- jQuery & Bootstrap Bundle -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>