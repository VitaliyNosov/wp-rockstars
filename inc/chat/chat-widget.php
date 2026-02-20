<?php
/**
 * Logic for displaying the Help/Chat Widget
 */

// Вывод HTML разметки и скриптов в футере
function rock_stars_render_chat_widget_html() {
    // Config moved to chat-init.php
    ?>

    <!-- Chat Widget Container -->
    <div id="chat-widget-app" style="position: fixed; bottom: 24px; right: 24px; z-index: 9999; font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, sans-serif;">
        
        <!-- Toggle Button -->
        <button id="chat-toggle-btn" 
                onclick="document.getElementById('chat-window').classList.toggle('open');"
                class="bg-blue-600 text-white"
                style="width: 56px; height: 56px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: none; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); outline: none; pointer-events: auto; background-color: #2563EB; color: white;"
                aria-label="<?php esc_attr_e( 'Open Chat Help', 'rock-stars' ); ?>">
            <!-- Icon Chat -->
            <svg id="icon-chat" style="width: 28px; height: 28px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
            </svg>
            <!-- Icon Close -->
            <svg id="icon-close" style="width: 28px; height: 28px; display: none;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Chat Window -->
        <div id="chat-window" class="chat-widget-window">
            
            <!-- Header -->
            <div style="padding: 16px; background: linear-gradient(to right, #2563EB, #1e40af); color: white; flex-shrink: 0; display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <h3 style="margin: 0; font-weight: bold; font-size: 1.125rem;"><?php esc_html_e( 'Help Assistant', 'rock-stars' ); ?></h3>
                    <p style="margin: 0; font-size: 0.75rem; opacity: 0.9;"><?php esc_html_e( 'Common answers and support', 'rock-stars' ); ?></p>
                </div>
                
                <!-- Mute Toggle -->
                <button id="rockstars-chat-mute-btn" type="button" title="<?php esc_attr_e( 'Toggle Sound', 'rock-stars' ); ?>" 
                        style="background: rgba(255,255,255,0.1); border: none; color: white; cursor: pointer; padding: 6px; border-radius: 8px; transition: background 0.2s;">
                    <!-- Volume Up Icon -->
                    <svg id="rockstars-icon-volume-up" style="width: 18px; height: 18px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                    </svg>
                    <!-- Volume Off Icon -->
                    <svg id="rockstars-icon-volume-off" style="width: 18px; height: 18px; display: none;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15zM17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2" />
                    </svg>
                </button>
            </div>

            <!-- Body (Scrollable) -->
            <div id="chat-messages" style="flex: 1; overflow-y: auto; padding: 16px; background-color: var(--chat-bg);">
                
                <!-- Welcome Message -->
                <div style="margin-bottom: 16px; display: flex; flex-direction: column; align-items: flex-start;">
                    <div style="background-color: var(--chat-msg-bg); color: var(--chat-text); padding: 12px; border-radius: 16px; border-top-left-radius: 0; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); max-width: 85%; font-size: 0.875rem; border: 1px solid var(--chat-border);">
                        <?php esc_html_e( 'Hello! 👋', 'rock-stars' ); ?> <br>
                        <?php esc_html_e( "Ask me a question, and I'll search our knowledge base for an answer.", 'rock-stars' ); ?>
                    </div>
                </div>

                <!-- Video Preview Container (Sticky/Overlay) -->
                <div id="chat-video-preview-container">
                    <video id="chat-video-preview" autoplay muted playsinline></video>
                </div>
                <!-- Audio Preview for Review -->
                <audio id="chat-audio-preview" controls style="display: none; width: 100%; margin-top: 10px;"></audio>

                <!-- Results Container -->
                <div id="chat-results-container"></div>
                
                <!-- Loading Indicator -->
                <div id="chat-loading" style="display: none; margin-bottom: 16px;">
                     <div style="display: flex; gap: 8px; padding: 12px; background-color: var(--chat-msg-bg); border-radius: 16px; border-top-left-radius: 0; width: fit-content; border: 1px solid var(--chat-border);">
                        <div style="width: 8px; height: 8px; background-color: #60a5fa; border-radius: 50%; animation: bounce 1.4s infinite ease-in-out both;"></div>
                        <div style="width: 8px; height: 8px; background-color: #60a5fa; border-radius: 50%; animation: bounce 1.4s infinite ease-in-out both; animation-delay: -0.32s;"></div>
                        <div style="width: 8px; height: 8px; background-color: #60a5fa; border-radius: 50%; animation: bounce 1.4s infinite ease-in-out both; animation-delay: -0.16s;"></div>
                    </div>
                </div>
                <!-- Inline style for bounce animation -->
                <style>
                    @keyframes bounce { 0%, 80%, 100% { transform: scale(0); } 40% { transform: scale(1); } }
                    
                    /* Hover Effects */
                    #chat-attach-btn:hover,
                    #chat-voice-btn:hover,
                    #chat-video-btn:hover {
                        color: var(--chat-accent) !important;
                    }
                    
                    /* Input Focus */
                    #chat-input:focus {
                        outline: none;
                        border: 1px solid var(--chat-accent) !important;
                        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
                    }
                </style>

            </div>

            <!-- Input Area -->
            <div style="padding: 16px; background-color: var(--chat-bg); border-top: 1px solid var(--chat-border); flex-shrink: 0;">
                <!-- File Preview -->
                <div id="chat-file-preview" style="display: none; margin-bottom: 8px; padding: 8px; background: var(--chat-input-bg); border-radius: 8px; font-size: 0.75rem; align-items: center; justify-content: space-between; border: 1px solid var(--chat-border);">
                    <div style="display: flex; align-items: center; gap: 6px; overflow: hidden;">
                        <svg style="width: 14px; height: 14px; flex-shrink: 0;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                        </svg>
                        <span class="file-name" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--chat-input-text);"></span>
                    </div>
                    <button type="button" id="chat-remove-file" style="background: none; border: none; color: var(--chat-error); cursor: pointer; padding: 0 4px; font-size: 1.1rem; line-height: 1;">&times;</button>
                </div>

                <div style="display: flex; align-items: center; gap: 8px; position: relative;">
                    <!-- Recording Overlay UI -->
                    <div id="chat-recording-ui" style="display: none; position: absolute; inset: 0; background: var(--chat-input-bg); z-index: 10; align-items: center; gap: 12px; padding: 0 8px; border-radius: 12px;">
                        <div id="chat-recording-indicator" style="display: flex; align-items: center; gap: 12px; flex: 1;">
                            <div class="recording-pulsate" style="width: 8px; height: 8px; background-color: #ef4444; border-radius: 50%;"></div>
                            <span id="chat-recording-timer" style="font-size: 0.8rem; font-family: monospace; color: var(--chat-input-text); min-width: 40px;">00:00</span>
                            <canvas id="chat-audio-visualizer" width="200" height="40" style="height: 20px; flex: 1; max-width: 100px;"></canvas>
                        </div>
                        
                        <div id="chat-review-indicator" style="display: none; align-items: center; gap: 8px; flex: 1; color: var(--chat-input-text); font-size: 0.875rem;">
                             <span id="chat-review-label">Preview Recording</span>
                        </div>

                        <div id="chat-recording-controls" style="display: flex; gap: 8px; align-items: center;">
                            <button id="chat-voice-cancel" type="button" title="<?php esc_attr_e( 'Cancel', 'rock-stars' ); ?>" style="background: none; border: none; color: #9ca3af; cursor: pointer; font-size: 1.2rem; padding: 4px;">&times;</button>
                            <button id="chat-voice-stop" type="button" title="<?php esc_attr_e( 'Stop Recording', 'rock-stars' ); ?>" style="width: 32px; height: 32px; background-color: #ef4444; border: none; border-radius: 50%; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <svg style="width: 16px; height: 16px;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8 7a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1H9a1 1 0 01-1-1V7z" clip-rule="evenodd" /></svg>
                            </button>
                        </div>

                        <div id="chat-review-controls" style="display: none; gap: 8px; align-items: center;">
                            <button id="chat-voice-retake" type="button" title="<?php esc_attr_e( 'Retake', 'rock-stars' ); ?>" style="background: none; border: none; color: #9ca3af; cursor: pointer; padding: 4px;">
                                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                            <button id="chat-voice-send" type="button" title="<?php esc_attr_e( 'Send', 'rock-stars' ); ?>" style="width: 32px; height: 32px; background-color: #2563EB; border: none; border-radius: 50%; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Attach Button -->
                    <button id="chat-attach-btn" type="button" title="<?php esc_attr_e( 'Attach file', 'rock-stars' ); ?>"
                            style="background: none; border: none; color: #9ca3af; cursor: pointer; padding: 4px; display: flex; align-items: center; transition: color 0.2s;">
                        <svg style="width: 20px; height: 20px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                        </svg>
                    </button>
                    <input type="file" id="chat-file-input" style="display: none;" accept="image/*,.pdf,.doc,.docx,.zip,.txt">

                    <!-- Voice Button -->
                    <button id="chat-voice-btn" type="button" title="<?php esc_attr_e( 'Record voice', 'rock-stars' ); ?>"
                            style="background: none; border: none; color: #9ca3af; cursor: pointer; padding: 4px; display: flex; align-items: center; transition: color 0.2s;">
                        <svg style="width: 20px; height: 20px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-20a3 3 0 013 3v8a3 3 0 01-3 3 3 3 0 01-3-3V5a3 3 0 013-3z" />
                        </svg>
                    </button>

                    <!-- Video Button -->
                    <button id="chat-video-btn" type="button" title="<?php esc_attr_e( 'Record video', 'rock-stars' ); ?>"
                            style="background: none; border: none; color: #9ca3af; cursor: pointer; padding: 4px; display: flex; align-items: center; transition: color 0.2s;">
                        <svg style="width: 20px; height: 20px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </button>

                    <div style="position: relative; flex: 1;">
                        <input type="text" id="chat-input" 
                               placeholder="<?php esc_attr_e( 'Type your question...', 'rock-stars' ); ?>" 
                               style="width: 100%; padding: 12px 40px 12px 16px; border-radius: 12px; background-color: var(--chat-input-bg); color: var(--chat-input-text); border: none; font-size: 0.875rem; box-sizing: border-box; transition: box-shadow 0.2s;">
                        <button id="chat-send-btn" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #2563EB; cursor: pointer;">
                            <svg style="width: 20px; height: 20px; transform: rotate(90deg);" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
    
    <!-- JS moved to inc/chat/js/chat-widget.js -->
    <?php
}
add_action( 'wp_footer', 'rock_stars_render_chat_widget_html' );
