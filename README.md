# ZMAN (Project Z)

[한국어 문서](./README.ko.md)

**ZMAN** is an LLM-powered dating simulation game set in Seoul's Sinchon distrcit in the year 2002. It recreates the nostalgic vibe of the early 2000s, allowing players to engage in free-form conversations with AI-driven heroines.

# Our Philosophy
IDDQD Internet builds zero-DB, zero-signup tools powered by pure HTML/JS for instant browser execution. Even with AI features, we keep it stateless and record-free.

### [Play ZMAN](https://game.iddqd.kr/zman_tmp)

![ZMAN Splash](./splash.jpeg)

## Features
-   **LLM-Driven Conversations**: Powered by Google Gemini 2.5 Flash, NPCs respond dynamically to any input without predefined dialogue trees.
-   **Retro 2002 Aesthetic**: Immerse yourself in the early 2000s Korean culture, featuring iconic locations like "Canmore" and "Minto".
-   **Affinity System**: Your conversation skills directly impact the heroine's affection level. Be careful, or they might leave you!
-   **Stateless AI**: Although a PHP proxy is used to secure API keys, no conversation logs or user data are stored on the server. All progress is saved locally in your browser.
-   **Real-time Interaction**: Conversations evolve naturally, and unexpected events (like the appearance of a rival) can occur based on turn counts.

## Usage
**Objective**: Win the heart of one of the three heroines through conversation.

1.  **Select a Heroine**: Choose from three unique characters with different personalities and difficulty levels.
2.  **Chat**: Type your message to interact. The AI will analyze your intent and respond with text and an affection score change.
3.  **Manage Time & Affection**: Every turn advances the in-game clock. Keep the affection meter high to avoid a "Game Over."

## Characters
-   **Kim Ji-eun (Difficulty ★★)**: A literature student nursing a broken heart at a cafe.
-   **Lee Seo-hyun (Difficulty ★★★)**: A top student dreaming of a small deviation from her routine.
-   **Yoon Chae-rim (Difficulty ★★★★)**: A trendy fashionista from Gangnam waiting for her friend.

## Tech Stack
-   **Frontend**: HTML5, CSS3, JavaScript (jQuery), Bootstrap 5
-   **Backend**: PHP (Simple Proxy for Gemini API)
-   **AI Engine**: Google Gemini 2.5 Flash
-   **Audio**: Web Audio API
-   **Storage**: Browser LocalStorage

## Disclaimer
-   This game is a work of fiction. Characters and settings are simulated based on the year 2002.
-   AI responses are generated in real-time and may be unpredictable.

# Contact & Author
Park Sil-jang
- Dev Team Lead at IDDQD Internet. E-solution & E-game Lead. Bushwhacking Code Shooter. Currently executing mandates as Choi’s Schemer.
- HQ (EN): https://en.iddqd.kr/
- GitHub: https://github.com/iddqd-park
