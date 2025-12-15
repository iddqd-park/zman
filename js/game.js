$(document).ready(function () {
    console.log("Game initialized.");

    // Game Clock
    function updateGameClock() {
        const now = new Date();

        const year = now.getFullYear();
        const month = now.getMonth() + 1;
        const date = now.getDate();
        let hours = now.getHours();
        const minutes = now.getMinutes();
        const seconds = now.getSeconds();
        const ampm = hours >= 12 ? '오후' : '오전';

        hours = hours % 12;
        hours = hours ? hours : 12;

        const timeString = `${year}년 ${month}월 ${date}일 ${ampm} ${hours}시 ${minutes}분 ${seconds}초`;
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

    // State
    let conversationHistory = []; // Array of {role: 'user'|'model', content: string}

    // Initialize
    loadHistory();
    $userInput.focus();

    // Event Listeners
    $sendBtn.on('click', sendMessage);

    $userInput.on('keypress', function (e) {
        if (e.which === 13) { // Enter key
            sendMessage();
        }
    });

    // Clear Chat Screen (Visual Only)
    $('#clearChatBtn').on('click', function () {
        if (confirm('대화 화면만 지우시겠습니까? (대화 기억은 유지됩니다)')) {
            $chatLog.empty();
            // Restore System Message
            $chatLog.append(`
                <div class="message system">
                    <span class="badge bg-secondary">SYSTEM</span> 대화창이 청소되었습니다.
                </div>
            `);
            $('#settingsModal').modal('hide');
        }
    });

    // Reset Game (Full Reset)
    $('#resetGameBtn').on('click', function () {
        if (confirm('정말로 모든 기억을 지우고 처음부터 다시 시작하시겠습니까?')) {
            localStorage.removeItem('zman_history');
            location.reload();
        }
    });

    function sendMessage() {
        const text = $userInput.val().trim();
        if (text === "") return;

        // Append Player Message locally
        appendMessage('user', text);
        addToHistory('user', text);

        // Clear input and disable UI
        $userInput.val('');
        setLoading(true);

        // Call API
        $.ajax({
            url: 'api.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                message: text,
                history: conversationHistory.filter(msg => msg !== conversationHistory[conversationHistory.length - 1]) // Previous history
            }),
            success: function (response) {
                if (response.reply) {
                    playBeep(880, 0.1); // High beep for NPC reply
                    appendMessage('model', response.reply);
                    addToHistory('model', response.reply);
                } else if (response.error) {
                    let detailMsg = response.details;
                    if (typeof detailMsg === 'object') {
                        detailMsg = JSON.stringify(detailMsg);
                    }
                    appendMessage('system', `⛔ Error (${response.error}): ${detailMsg}`);
                    console.error("API Error Details:", response.details);
                }
            },
            error: function (xhr, status, error) {
                appendMessage('system', "Network Error: " + error);
            },
            complete: function () {
                setLoading(false);
                $userInput.focus();
            }
        });
    }

    function appendMessage(role, text) {
        let html = '';
        if (role === 'model') {
            html = `
                <div class="message npc-container">
                    <div class="npc-name">지은</div>
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
        saveHistory();
    }

    function saveHistory() {
        // Enforce Byte Limit
        let json = JSON.stringify(conversationHistory);
        while (new Blob([json]).size > MAX_HISTORY_BYTES && conversationHistory.length > 0) {
            // Remove oldest message (try to remove pairs if possible to keep context sense, but strict byte limit means just shift)
            conversationHistory.shift();
            json = JSON.stringify(conversationHistory);
        }
        localStorage.setItem('zman_history', json);
    }

    function loadHistory() {
        const saved = localStorage.getItem('zman_history');
        if (saved) {
            try {
                conversationHistory = JSON.parse(saved);
                conversationHistory.forEach(msg => {
                    appendMessage(msg.role, msg.content);
                });
            } catch (e) {
                console.error("Failed to parse history", e);
                localStorage.removeItem('zman_history');
            }
        }
    }

    function setLoading(isLoading) {
        $sendBtn.prop('disabled', isLoading);
        $userInput.prop('disabled', isLoading);

        if (isLoading) {
            playBeep(440, 0.1); // Low beep for user send
            $sendBtn.text('...');
            const typingHtml = `
                <div class="message npc-container typing-msg">
                    <div class="npc-name">지은</div>
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
});
