$(document).ready(function () {
    console.log("Game initialized.");

    // Scenario Configuration
    const scenarios = {
        'kim_ji_eun': {
            name: '김지은',
            bg: 'area/minto/1.jpg',
            char: 'npc_girl/kim_ji_eun/1.png',
            systemMsg: '신촌 민들레영토.<br>당신은 지은과 마주보고 앉아있다.'
        },
        'lee_seo_hyun': {
            name: '이서현',
            bg: 'area/canmore/1.jpg',
            char: 'npc_girl/lee_seo_hyun/1.png',
            systemMsg: '신촌 캔모아.<br>그네 의자에 앉은 서현과 합석하게 되었다.'
        },
        'yoon_chae_rim': {
            name: '윤채림',
            bg: 'area/hongik_book/1.jpg',
            char: 'npc_girl/yoon_chae_rim/1.png',
            systemMsg: '홍익문고 앞.<br>누군가를 기다리는 채림에게 말을 걸었다.'
        }
    };

    let currentScenarioId = 'kim_ji_eun';
    let conversationHistory = []; // Array of {role: 'user'|'model', content: string}
    let chatCount = 0;
    let isGameOver = false;
    let gameTime = null; // Variable to hold the game's current time

    function updateGameClock() {
        if (!gameTime) {
            // If gameTime is not initialized, use current real time as a fallback or for initial display
            gameTime = new Date();
        } else {
            // Increment gameTime by 1 second
            gameTime.setSeconds(gameTime.getSeconds() + 1);
        }

        const now = gameTime;

        const year = now.getFullYear();
        const month = now.getMonth() + 1;
        const date = now.getDate();
        let hours = now.getHours();
        const minutes = now.getMinutes();
        const seconds = now.getSeconds();
        const ampm = hours >= 12 ? '오후' : '오전';

        hours = hours % 12;
        hours = hours ? hours : 12;

        const timeString = `${year}년 ${month}월 ${date}일 ${ampm} ${hours}시`;
        $('#gameClock').text(timeString);
    }

    setInterval(updateGameClock, 1000);
    updateGameClock(); // Initial call

    // settings
    const MAX_HISTORY_BYTES = 10000; // 10KB limit

    // UI Elements
    const $chatLog = $('#chatLog');
    const $userInput = $('#userInput');
    const $sendBtn = $('#sendBtn');

    // Initialize Game based on URL Parameter
    const urlParams = new URLSearchParams(window.location.search);
    const scenarioId = urlParams.get('scenario');

    if (scenarioId && scenarios[scenarioId]) {
        initGame(scenarioId);
    } else {
        // If no scenario or invalid, redirect to index (if not already there)
        // Check if we are physically on play.php to avoid loop if index.php included this (it doesn't, but safe)
        if (window.location.pathname.includes('play.php')) {
            alert("잘못된 접근입니다. 캐릭터를 다시 선택해주세요.");
            window.location.href = './';
        }
    }

    function initGame(scenarioId) {
        console.log("Starting scenario:", scenarioId);
        currentScenarioId = scenarioId;
        const data = scenarios[scenarioId];

        // 0. Initialize Game Clock (10:00 AM)
        gameTime = new Date();
        gameTime.setHours(10, 0, 0, 0);
        updateGameClock();

        // 1. Update Assets
        $('#bgImage').attr('src', data.bg);
        // 2. 동적 placeholder 적용 (200자 제한은 HTML 속성으로 이미 적용)
        $('#userInput').attr('placeholder', `${data.name}에게 할말을 입력하세요`); $('#charImage').attr('src', data.char);

        // 2. Reset Game State (New Session)
        conversationHistory = [];
        chatCount = 0;
        isGameOver = false;
        $('.character-layer').show();
        // Placeholder for future character expression logic

        // 3. Clear & Init Chat Log
        $chatLog.empty();
        $chatLog.append(`
            <div class="message system">
                <span class="badge bg-secondary">SYSTEM</span> ${data.systemMsg}
            </div>
        `);

        // 4. Reset Affinity
        updateAffinity(0);

        // Clear Persistence for new game session
        localStorage.removeItem('zman_history');
        localStorage.removeItem('zman_affinity');
        localStorage.removeItem('zman_chat_count');

        // Focus Input
        $userInput.focus();
    }

    // Event Listeners
    $sendBtn.on('click', sendMessage);

    $userInput.on('keypress', function (e) {
        if (e.which === 13) { // Enter key
            sendMessage();
        }
    });

    /* =========================================
       BGM Player Logic
       ========================================= */
    let bgmAudio = new Audio();
    let bgmIndex = 0;
    // Load mute state from localStorage (default: false)
    let isBgmMuted = localStorage.getItem('zman_bgm_muted') === 'true';

    // Shuffle Playlist
    function shuffleArray(array) {
        for (let i = array.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [array[i], array[j]] = [array[j], array[i]];
        }
    }

    function updateBgmButton() {
        if (bgmAudio.paused) {
            $('#bgmToggleBtn').text('🔇');
        } else {
            $('#bgmToggleBtn').text('🔊');
        }
    }

    function tryPlayBgm() {
        if (isBgmMuted) {
            updateBgmButton();
            return;
        }

        let playPromise = bgmAudio.play();
        if (playPromise !== undefined) {
            playPromise.then(_ => {
                console.log("BGM started.");
                updateBgmButton();
            }).catch(error => {
                console.log("BGM Autoplay prevented. Waiting for interaction.");
                updateBgmButton(); // Show muted initially if blocked
                // Add one-time listener to Resume
                $(document).one('click keydown touchstart', function () {
                    if (!bgmAudio.paused) return;
                    bgmAudio.play().then(updateBgmButton);
                });
            });
        }
    }

    if (typeof bgmPlaylist !== 'undefined' && bgmPlaylist.length > 0) {
        shuffleArray(bgmPlaylist);

        // Initial Track
        bgmAudio.src = bgmPlaylist[bgmIndex];
        bgmAudio.volume = 0.3; // -10dB approx

        tryPlayBgm();

        // Loop Logic
        bgmAudio.addEventListener('ended', function () {
            bgmIndex = (bgmIndex + 1) % bgmPlaylist.length;
            bgmAudio.src = bgmPlaylist[bgmIndex];
            if (!isBgmMuted) {
                bgmAudio.play().catch(e => console.log("Loop play failed", e));
            }
        });
    }

    // Toggle Button
    $('#bgmToggleBtn').on('click', function () {
        if (bgmAudio.paused) {
            isBgmMuted = false;
            bgmAudio.play().then(updateBgmButton);
        } else {
            isBgmMuted = true;
            bgmAudio.pause();
            updateBgmButton();
        }
        localStorage.setItem('zman_bgm_muted', isBgmMuted);
    });



    async function sendMessage() {
        const text = $userInput.val().trim();
        if (text === "") return;

        // Append Player Message locally
        appendMessage('user', text);
        addToHistory('user', text);

        // Clear input and disable UI
        $userInput.val('');

        // Game Over Check
        if (isGameOver) {
            const data = scenarios[currentScenarioId];
            setTimeout(() => {
                appendMessage('system', `${data.name}은(는) 이제 더이상 당신 곁에 있지 않습니다.`);
                playBeep(220, 0.3); // Low sad beep
            }, 500);
            return;
        }

        setLoading(true);

        // Increment Chat Count
        chatCount++;
        // Note: We are NOT saving to localStorage for persistence in this session-based MVP 
        // to avoid complexity with multi-scenario storage. 
        // (Or we could overwrite the same keys, effectively supporting only 1 active game at a time)
        localStorage.setItem('zman_chat_count', chatCount);

        try {
            // Get current game time string
            const timeString = $('#gameClock').text();

            const response = await fetch('api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    message: text,
                    history: conversationHistory,
                    scenarioId: currentScenarioId, // Pass ID
                    timeString: timeString
                })
            });

            if (!response.ok) {
                const errText = await response.text();
                console.error("API Error (HTTP " + response.status + "):", errText);
                throw new Error("HTTP " + response.status);
            }

            const reader = response.body.getReader();
            const decoder = new TextDecoder();
            let accumulatedJson = "";
            let fullRawCapture = "";

            while (true) {
                const { done, value } = await reader.read();
                if (done) break;

                const chunk = decoder.decode(value, { stream: true });
                fullRawCapture += chunk;

                // Process SSE lines
                const lines = chunk.split('\n');
                for (const line of lines) {
                    if (line.startsWith('data: ')) {
                        const jsonStr = line.substring(6).trim();
                        if (jsonStr === '[DONE]') continue; // Standard SSE end, though Gemini usually sends valid JSON or nothing

                        try {
                            const data = JSON.parse(jsonStr);
                            if (data.candidates && data.candidates[0].content && data.candidates[0].content.parts) {
                                const partText = data.candidates[0].content.parts[0].text;
                                if (partText) {
                                    accumulatedJson += partText;
                                }
                            }
                        } catch (e) {
                            // Ignore parse errors for partial lines (though SSE lines should be complete ideally)
                            // or simple keep-alives
                        }
                    }
                }
            }

            // Stream finished, Parse Full JSON
            try {
                if (!accumulatedJson) throw new Error("Empty response from AI");

                // Sometimes Gemini adds markdown code blocks around JSON ```json ... ```
                let cleanerJson = accumulatedJson.replace(/```json/g, '').replace(/```/g, '').trim();

                // HOTFIX: Gemini sometimes double-quotes keys like ""affinity"" or ""reply""
                cleanerJson = cleanerJson.replace(/""([a-zA-Z0-9_]+)""/g, '"$1"');

                let result;
                try {
                    result = JSON.parse(cleanerJson);
                } catch (e) {
                    console.warn("First JSON parse failed, attempting recovery...", e);

                    // Recovery 1: Find the last valid start of the JSON object (in case of restarts/garbage)
                    const regex = /\{\s*"reply"/g;
                    let match;
                    let lastIndex = -1;
                    while ((match = regex.exec(cleanerJson)) !== null) {
                        lastIndex = match.index;
                    }

                    if (lastIndex !== -1) {
                        const subJson = cleanerJson.substring(lastIndex);
                        try {
                            result = JSON.parse(subJson);
                            console.log("Recovered JSON via substring logic.");
                        } catch (e2) {
                            console.warn("Recovery 1 failed.");
                        }
                    }

                    // Recovery 2: Regex Extraction (Hail Mary)
                    if (!result) {
                        const replyMatch = cleanerJson.match(/"reply"\s*:\s*"((?:[^"\\]|\\.)*)"/);
                        const affinityMatch = cleanerJson.match(/"affinity"\s*:\s*(\d+)/);

                        if (replyMatch) {
                            result = {
                                reply: replyMatch[1].replace(/\\"/g, '"').replace(/\\n/g, '\n'), // Unescape basic chars
                                affinity: affinityMatch ? parseInt(affinityMatch[1]) : 0
                            };
                            console.log("Recovered JSON via Regex extraction.");
                        } else {
                            // If all failed, throw original error to trigger catch block
                            throw e;
                        }
                    }
                }

                if (result.reply) {
                    playBeep(880, 0.1); // High beep for NPC reply
                    appendMessage('model', result.reply);
                    addToHistory('model', result.reply);
                }

                // Update Affinity if present
                if (typeof result.affinity !== 'undefined') {
                    updateAffinity(result.affinity);
                }
            } catch (e) {
                console.error("JSON Parse Error:", e, "Accumulated:", accumulatedJson, "Raw Response:", fullRawCapture);
                appendMessage('system', "⛔ ERROR: 다시 채팅을 입력해주세요. (JSON 처리 실패)");
            }

        } catch (error) {
            console.error("Fetch Error:", error);
            appendMessage('system', "Network Error: " + error.message);
        } finally {
            setLoading(false);
            $userInput.focus();
        }
    }

    function updateAffinity(score) {
        // Clamp score 0-5
        let rawScore = Math.max(0, Math.min(5, parseInt(score) || 0));
        localStorage.setItem('zman_affinity', rawScore); // Save real score persistence

        // Game Over Check
        if (chatCount > 10 && rawScore <= 0) {
            if (!isGameOver) {
                const charName = scenarios[currentScenarioId].name;
                appendMessage('system', `더 이상 대화를 이어갈 수 없다고 판단한 ${charName}은(는) 자리를 떠났다.`);
                setGameOver(true);
            }
        } else {
            setGameOver(false);
        }

        // Display Logic (Masking)
        // If chatCount <= 10, display at least 1 heart unless actual score is >= 2
        let displayScore = rawScore;
        if (chatCount <= 10) {
            if (displayScore < 1) {
                displayScore = 1;
            }
        }

        const fullHeart = '❤️';
        const emptyHeart = '🤍';

        let heartsHtml = '';
        for (let i = 0; i < 5; i++) {
            if (i < displayScore) {
                heartsHtml += `<span class="heart-icon filled">${fullHeart}</span>`;
            } else {
                heartsHtml += `<span class="heart-icon empty">${emptyHeart}</span>`;
            }
        }

        $('#affinityDisplay').html(heartsHtml);
    }

    function setGameOver(state) {
        isGameOver = state;
        const $inputGroup = $userInput.parent(); // Assuming userInput is inside a group or container

        if (state) {
            $('.character-layer').hide(); // Hide character

            // Hide Input and Send Button
            $userInput.hide();
            $sendBtn.hide();

            // Remove existing home button if any to prevent duplicates
            $('#gameOverHomeBtn').remove();

            // Add Red Home Button
            const homeBtnHtml = `
                <button id="gameOverHomeBtn" class="btn btn-danger w-100 p-3 fw-bold" style="border-radius: 0;">
                    메인으로 돌아가기
                </button>
            `;

            // we want to place it where userInput was.
            // If .input-group exists, we might want to hide the whole group and show the button, 
            // OR append to the parent. Let's append to the parent of userInput context commonly used in these chat apps.
            // Looking at standard bootstrap structure: <div class="input-group"> <input> <button> </div>
            // It's safer to hide the siblings and append to parent, or hide parent and append to container.
            // Let's assume $userInput and $sendBtn are siblings.

            if ($userInput.parent().hasClass('input-group')) {
                $userInput.parent().hide();
                $userInput.parent().parent().append(homeBtnHtml);
            } else {
                // Fallback
                $userInput.after(homeBtnHtml);
            }

            // Bind Click
            $('#gameOverHomeBtn').on('click', function () {
                window.location.href = './';
            });

        } else {
            // Restore UI
            if ($('#gameScreen').is(':visible')) {
                $('.character-layer').show();
            }

            $('#gameOverHomeBtn').remove();

            if ($userInput.parent().hasClass('input-group')) {
                $userInput.parent().show();
            }
            $userInput.show();
            $sendBtn.show();
        }
    }

    // Expose Debug Function
    window.debugChat = function () {
        console.log("=== Debug: Conversation History ===");
        console.table(conversationHistory);
        console.log("Current Scenario:", currentScenarioId);
        console.log("Raw History Array:", JSON.stringify(conversationHistory, null, 2));
        console.log("===================================");
    };



    function appendMessage(role, text) {
        let html = '';
        const charName = scenarios[currentScenarioId].name;

        if (role === 'model') {
            html = `
                <div class="message npc-container">
                    <div class="npc-name">${charName}</div>
                    <div class="message-bubble npc">${text}</div>
                </div>`;
        } else if (role === 'user') {
            html = `
                <div class="message player-container">
                    <div class="message-bubble player">${text}</div>
                </div>`;
        } else {
            html = `<div class="message system">${text}</div>`;
        }

        $chatLog.append(html);
        scrollToBottom();
    }

    function addToHistory(role, content) {
        conversationHistory.push({ role: role, content: content });
        // Enforce limit
        let json = JSON.stringify(conversationHistory);
        while (new Blob([json]).size > MAX_HISTORY_BYTES && conversationHistory.length > 0) {
            // Remove oldest message (try to remove pairs if possible to keep context sense, but strict byte limit means just shift)
            conversationHistory.shift();
            json = JSON.stringify(conversationHistory);
        }
        localStorage.setItem('zman_history', json);
    }

    function setLoading(isLoading) {
        $sendBtn.prop('disabled', isLoading);
        $userInput.prop('disabled', isLoading);
        const charName = scenarios[currentScenarioId].name;

        if (isLoading) {
            playBeep(440, 0.1); // Low beep for user send
            $sendBtn.text('...');
            const typingHtml = `
                <div class="message npc-container typing-msg">
                    <div class="npc-name">${charName}</div>
                    <div class="message-bubble npc">
                        <div class="typing-indicator">
                            <span></span><span></span><span></span>
                        </div>
                    </div>
                </div>`;
            $chatLog.append(typingHtml);
            scrollToBottom();
        } else {
            $sendBtn.text('말하기');
            $('.typing-msg').remove();
        }
    }

    function playBeep(frequency, duration) {
        // Simple Web Audio API Beep
        try {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (!AudioContext) return;

            const ctx = new AudioContext();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();

            osc.type = 'sine';
            osc.frequency.value = frequency;
            osc.connect(gain);
            gain.connect(ctx.destination);

            osc.start();
            gain.gain.exponentialRampToValueAtTime(0.00001, ctx.currentTime + duration);
            osc.stop(ctx.currentTime + duration);
        } catch (e) {
            console.error("Audio play failed", e);
        }
    }

    function scrollToBottom() {
        $chatLog.scrollTop($chatLog[0].scrollHeight);
    }

    // Expose Debug Function
    window.debugChat = function () {
        console.log("=== Debug: Conversation History ===");
        console.table(conversationHistory);
        console.log("Current Scenario:", currentScenarioId);
        console.log("Raw History Array:", JSON.stringify(conversationHistory, null, 2));
        console.log("===================================");
    };
});
