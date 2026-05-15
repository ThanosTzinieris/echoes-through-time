document.addEventListener("DOMContentLoaded", function () {

    // =========================
    // DETECT LEVEL
    // =========================
    const levelElement = document.getElementById("level-data");
    const levelId = levelElement ? levelElement.dataset.level : null;

    // =========================
    // CHAT AUTO-SCROLL
    // =========================
    function scrollToBottom() {
        const chatLog = document.getElementById("chat-log");
        if (chatLog) {
            chatLog.scrollTop = chatLog.scrollHeight;
        }
    }

    const form = document.getElementById("question-form");
    const input = document.getElementById("player-question");
    const chatLog = document.getElementById("chat-log");

    // =========================
    // LOAD CHAT HISTORY
    // =========================
    if (chatLog) {
        fetch("../ett/load_chat.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "level_id=" + encodeURIComponent(levelId)
        })
        .then(response => response.json())
        .then(messages => {

            if (messages.length === 0) {
                const openingMsg = document.createElement("div");
                openingMsg.classList.add("message", "npc");
                openingMsg.textContent = "Tell me whose voice echoes through these words...";
                chatLog.appendChild(openingMsg);
            } else {
                messages.forEach(msg => {
                    const messageDiv = document.createElement("div");

                    if (msg.sender === "player") {
                        messageDiv.classList.add("message", "player");
                    } else if (msg.sender === "npc") {
                        messageDiv.classList.add("message", "npc");
                    }

                    messageDiv.textContent = msg.message;
                    chatLog.appendChild(messageDiv);
                });
            }

            scrollToBottom();
        });
    }

    // =========================
    // FORM SUBMIT
    // =========================
    if (form && input && chatLog) {
        form.addEventListener("submit", function (e) {
            e.preventDefault();

            const question = input.value;
            if (!question.trim()) return;

            const playerMsg = document.createElement("div");
            playerMsg.classList.add("message", "player");
            playerMsg.textContent = question;
            chatLog.appendChild(playerMsg);

            scrollToBottom();
            input.value = "";

            fetch("../ett/handle_question.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "question=" + encodeURIComponent(question) + "&level_id=" + encodeURIComponent(levelId)
            })
            .then(response => response.json())
            .then(data => {

                const npcMsg = document.createElement("div");
                npcMsg.classList.add("message", "npc");
                npcMsg.textContent = data.npc_response;
                chatLog.appendChild(npcMsg);

                scrollToBottom();
            });
        });
    }

    // =========================
    // SOLVED OVERLAY FADE-IN
    // =========================
    const overlay = document.querySelector(".solved-overlay");

    if (overlay && overlay.classList.contains("pending")) {

        // Ensure starting state (opacity: 0)
        overlay.classList.remove("pending");

        // Trigger animation
        requestAnimationFrame(() => {
            overlay.classList.add("show");
        });
    }

});