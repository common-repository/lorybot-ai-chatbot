<button class="chatbot-toggler">
    <div class="chatbot-icon-second"></div>
</button>

<div class="chatbot" id="chatbot-container" style="transform: scale(0);">
    <div id="sseMessages" class="chatbot-sseMessages"></div>
    <div id="original_message" class="chatbot-original_message"></div>
    <div class="chatbot-header">
        <div class="profile">
            <div class="img"></div>
            <div class="text">
                <h5>LoryBot</h5>
                <p>Online</p>
            </div>
        </div>
        <button class="chatbot-close-btn">
            <span></span>
        </button>
    </div>

    <div class="chatbot-content">
        <div class="chatbot-chat-screen active">
            <div class="messages">
                <div class="chatbot-message">
                    <div class="profile"></div>
                    <ul>
                    <li>
                        <span> Hi, How can I help you today?? 👋 </span>
                    </li>
                    </ul>
                </div>
            </div>
            <div id="chatbot-message-form" class="chatbot-write-message">
                <div class="textbox">
                    <input id="message" type="text">
                    <button class="send-voice-btn"></button>
                </div>
                <button type="submit" class="send-message-btn" id="chatbot-message-form-button"></button>
            </div>
        </div>
    </div>
</div>  