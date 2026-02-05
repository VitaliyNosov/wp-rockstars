<?php
/**
 * Logic for displaying the Help/Chat Widget
 */

// Вывод HTML разметки и скриптов в футере
function render_chat_widget_html() {
    // Конфигурация для JS
    $config = array(
        'apiUrl'  => get_rest_url( null, 'wp/v2/qa' ),
        'siteUrl' => get_site_url(),
        'root'    => esc_url_raw( rest_url() ),
        'nonce'   => wp_create_nonce( 'wp_rest' )
    );

    // Helpers for Ngrok/Localhost mismatch
    $current_host = $_SERVER['HTTP_HOST'] ?? '';
    
    $fix_url_for_ngrok = function($url) use ($current_host) {
        if (!$url) return $url;
        $url_parts = parse_url($url);
        if (isset($url_parts['host']) && $current_host) {
            $old_authority = $url_parts['host'] . (isset($url_parts['port']) ? ':' . $url_parts['port'] : '');
            if ($old_authority !== $current_host) {
                $url = str_replace($old_authority, $current_host, $url);
            }
        }
        // Force HTTPS if we are on an Ngrok-like domain or if the current request is HTTPS
        if (strpos($current_host, 'ngrok') !== false || (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')) {
            $url = str_replace('http://', 'https://', $url);
        } else if (strpos($current_host, 'localhost') !== false) {
            // Keep http for localhost usually, unless forced
        }
        return $url;
    };

    // Apply fixes to main config
    $config['apiUrl'] = $fix_url_for_ngrok($config['apiUrl']);
    $config['siteUrl'] = $fix_url_for_ngrok($config['siteUrl']);
    $config['root'] = $fix_url_for_ngrok($config['root']);

    // Resolving Sound URL
    $sound_url = carbon_get_theme_option('chat_sound_url');
    if (!$sound_url) {
        $sound_id = carbon_get_theme_option('chat_sound_file');
        if ($sound_id) {
            $sound_url = wp_get_attachment_url($sound_id);
        }
    }
    
    if ($sound_url) {
        $config['notificationSound'] = $fix_url_for_ngrok($sound_url);
    }
    ?>
    <style>
        :root {
            --chat-bg: #ffffff;
            --chat-text: #1f2937;
            --chat-msg-bg: #ffffff;
            --chat-border: #f3f4f6;
            --chat-input-bg: #f3f4f6;
            --chat-input-text: #111827;
            --chat-accent: #2563EB; /* New Accent Color */
            --chat-error: #ef4444;    /* Red for errors */
            --chat-scrollbar-thumb: rgba(156, 163, 175, 0.5);
        }
        .dark {
            --chat-bg: #000000;
            --chat-text: #f3f4f6;
            --chat-msg-bg: #171717;
            --chat-border: #262626;
            --chat-input-bg: #171717;
            --chat-input-text: #ffffff;
            --chat-accent: #2563EB;
            --chat-scrollbar-thumb: rgba(75, 85, 99, 0.5);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translate3d(0, 20px, 0);
            }
            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .chat-widget-window {
            display: flex;
            visibility: hidden;
            flex-direction: column;
            position: absolute;
            bottom: 80px;
            right: 0;
            width: 350px;
            max-width: 90vw;
            height: 500px;
            max-height: 80vh;
            background: var(--chat-bg);
            color: var(--chat-text);
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            overflow: hidden;
            z-index: 10000;
            border: 1px solid rgba(0,0,0,0.1);
            transform-origin: bottom right;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            opacity: 0;
            transform: scale(0.95) translateY(20px);
            pointer-events: none;
        }
        .chat-widget-window.open {
            visibility: visible;
            opacity: 1;
            transform: scale(1) translateY(0);
            pointer-events: auto;
        }
        
        .dark .chat-widget-window {
            border-color: rgba(255,255,255,0.1);
        }

        #chat-toggle-btn {
            transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
            background-color: var(--chat-accent) !important; /* Force Accent */
        }
        #chat-toggle-btn:hover {
            transform: scale(1.1);
        }
        #chat-toggle-btn:active {
            transform: scale(0.9);
        }

        /* Scrollbar Styling */
        #chat-messages::-webkit-scrollbar {
            width: 6px;
        }
        #chat-messages::-webkit-scrollbar-track {
            background: transparent;
        }
        #chat-messages::-webkit-scrollbar-thumb {
            background-color: var(--chat-scrollbar-thumb);
            border-radius: 3px;
        }

        /* Form Validation Styles */
        .chat-form-input {
            width: 100%;
            padding: 10px; 
            margin-bottom: 12px; 
            border-radius: 8px; 
            border: 1px solid var(--chat-border); 
            background: var(--chat-input-bg); 
            color: var(--chat-input-text); 
            font-size: 0.85rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            outline: none;
            box-sizing: border-box;
        }
        .chat-form-input:focus {
            border-color: var(--chat-text); /* Neutral focus first */
        }
        .chat-form-input.valid {
            border-color: var(--chat-accent) !important;
        }
        .chat-form-input.invalid {
            border-color: var(--chat-error) !important;
        }
        
        .loading-dot:nth-child(2) { animation-delay: .2s; }
        .loading-dot:nth-child(3) { animation-delay: .4s; }

        /* Attachment UI */
        .chat-attachment-link {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            text-decoration: none;
            color: inherit;
            margin-top: 8px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: background 0.2s;
            font-size: 0.8rem;
        }
        .chat-attachment-link:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        .dark .chat-attachment-link {
            background: rgba(255, 255, 255, 0.05);
        }
        .chat-attachment-icon {
            flex-shrink: 0;
            width: 24px;
            height: 24px;
            background: var(--chat-accent);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        /* Voice Recording Styles */
        .recording-pulsate {
            animation: pulse-red 1.5s infinite;
        }
        @keyframes pulse-red {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }

        #chat-video-preview-container {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid #ef4444;
            position: absolute;
            bottom: 70px;
            right: 16px;
            background: #000;
            z-index: 20;
            display: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        #chat-video-preview {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scaleX(-1); /* Mirror effect for live */
        }
        #chat-video-preview.recorded {
            transform: none; /* No mirror for playback */
        }

        /* Media Players */
        .chat-media-container {
            margin-top: 10px;
            width: 100%;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .chat-media-audio {
            width: 100%;
            height: 40px;
            display: block;
            border-radius: 8px;
            filter: invert(0); /* Default */
        }
        .dark .chat-media-audio {
            filter: invert(0.9) hue-rotate(180deg); /* Make native player look better in dark mode */
            opacity: 0.8;
        }
        .chat-media-video {
            width: 100%;
            display: block;
            border-radius: 8px;
            max-height: 250px;
            background: #000;
        }
        .chat-attachment-card {
            padding: 8px;
            margin-top: 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: background 0.2s;
        }
        .chat-attachment-card:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        /* Review controls overlay */
        #chat-recording-actions {
            display: none;
            gap: 8px;
            align-items: center;
        }
    </style>

    <!-- Chat Widget Container -->
    <div id="chat-widget-app" style="position: fixed; bottom: 24px; right: 24px; z-index: 9999; font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, sans-serif;">
        
        <!-- Toggle Button -->
        <button id="chat-toggle-btn" 
                onclick="document.getElementById('chat-window').classList.toggle('open');"
                class="bg-blue-600 text-white"
                style="width: 56px; height: 56px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: none; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); outline: none; pointer-events: auto; background-color: #2563EB; color: white;"
                aria-label="Open Chat Help">
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
                    <h3 style="margin: 0; font-weight: bold; font-size: 1.125rem;">Help Assistant</h3>
                    <p style="margin: 0; font-size: 0.75rem; opacity: 0.9;">Common answers and support</p>
                </div>
                
                <!-- Mute Toggle -->
                <button id="rockstars-chat-mute-btn" type="button" title="Toggle Sound" 
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
                        Hello! 👋 <br>
                        Ask me a question, and I'll search our knowledge base for an answer.
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
                            <button id="chat-voice-cancel" type="button" title="Cancel" style="background: none; border: none; color: #9ca3af; cursor: pointer; font-size: 1.2rem; padding: 4px;">&times;</button>
                            <button id="chat-voice-stop" type="button" title="Stop Recording" style="width: 32px; height: 32px; background-color: #ef4444; border: none; border-radius: 50%; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <svg style="width: 16px; height: 16px;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8 7a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1H9a1 1 0 01-1-1V7z" clip-rule="evenodd" /></svg>
                            </button>
                        </div>

                        <div id="chat-review-controls" style="display: none; gap: 8px; align-items: center;">
                            <button id="chat-voice-retake" type="button" title="Retake" style="background: none; border: none; color: #9ca3af; cursor: pointer; padding: 4px;">
                                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                            <button id="chat-voice-send" type="button" title="Send" style="width: 32px; height: 32px; background-color: #2563EB; border: none; border-radius: 50%; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Attach Button -->
                    <button id="chat-attach-btn" type="button" title="Attach file"
                            style="background: none; border: none; color: #9ca3af; cursor: pointer; padding: 4px; display: flex; align-items: center; transition: color 0.2s;">
                        <svg style="width: 20px; height: 20px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                        </svg>
                    </button>
                    <input type="file" id="chat-file-input" style="display: none;" accept="image/*,.pdf,.doc,.docx,.zip,.txt">

                    <!-- Voice Button -->
                    <button id="chat-voice-btn" type="button" title="Record voice"
                            style="background: none; border: none; color: #9ca3af; cursor: pointer; padding: 4px; display: flex; align-items: center; transition: color 0.2s;">
                        <svg style="width: 20px; height: 20px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-20a3 3 0 013 3v8a3 3 0 01-3 3 3 3 0 01-3-3V5a3 3 0 013-3z" />
                        </svg>
                    </button>

                    <!-- Video Button -->
                    <button id="chat-video-btn" type="button" title="Record video"
                            style="background: none; border: none; color: #9ca3af; cursor: pointer; padding: 4px; display: flex; align-items: center; transition: color 0.2s;">
                        <svg style="width: 20px; height: 20px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </button>

                    <div style="position: relative; flex: 1;">
                        <input type="text" id="chat-input" 
                               placeholder="Type your question..." 
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

    <!-- Inline Script for Reliability -->
    <script>
    (function(){
        const chatWidgetConfig = <?php echo json_encode($config); ?>;
        
        class HelpWidget {
            constructor(config) {
                this.config = config;
                this.state = {
                    isOpen: false,
                    isLoading: false,
                    isOnline: true,
                    messages: [],
                    sessionId: this.getOrCreateSessionId(),
                    lastHistoryCount: 0,
                    pollInterval: null,
                    isMuted: localStorage.getItem('chat_muted') === 'true',
                    customAudio: this.config.notificationSound ? new Audio(this.config.notificationSound) : null,
                    selectedFile: null,
                    
                    // Voice/Video Recording State
                    isRecording: false,
                    recordingType: 'voice', // 'voice' or 'video'
                    mediaRecorder: null,
                    audioChunks: [],
                    recordingStartTime: 0,
                    timerInterval: null,
                    recordedFile: null,
                    recordingState: 'idle', // 'idle', 'recording', 'review'
                    audioContext: null,
                    analyser: null,
                    animationFrame: null
                };

                // DOM Elements
                this.dom = {
                    app: document.getElementById('chat-widget-app'),
                    toggleBtn: document.getElementById('chat-toggle-btn'),
                    window: document.getElementById('chat-window'),
                    input: document.getElementById('chat-input'),
                    sendBtn: document.getElementById('chat-send-btn'),
                    results: document.getElementById('chat-results-container'),
                    loading: document.getElementById('chat-loading'),
                    iconChat: document.getElementById('icon-chat'),
                    iconClose: document.getElementById('icon-close'),
                    messagesBody: document.getElementById('chat-messages'),
                    statusText: null, // Will be added in header
                    muteBtn: document.getElementById('rockstars-chat-mute-btn'),
                    iconVolUp: document.getElementById('rockstars-icon-volume-up'),
                    iconVolOff: document.getElementById('rockstars-icon-volume-off'),
                    
                    // File elements
                    attachBtn: document.getElementById('chat-attach-btn'),
                    fileInput: document.getElementById('chat-file-input'),
                    filePreview: document.getElementById('chat-file-preview'),
                    removeFileBtn: document.getElementById('chat-remove-file'),
                    
                    // Voice Elements
                    voiceBtn: document.getElementById('chat-voice-btn'),
                    recordingUI: document.getElementById('chat-recording-ui'),
                    recordingTimer: document.getElementById('chat-recording-timer'),
                    voiceStop: document.getElementById('chat-voice-stop'),
                    voiceCancel: document.getElementById('chat-voice-cancel'),

                    // Video Elements
                    videoBtn: document.getElementById('chat-video-btn'),
                    videoPreviewContainer: document.getElementById('chat-video-preview-container'),
                    videoPreview: document.getElementById('chat-video-preview'),
                    audioPreview: document.getElementById('chat-audio-preview'),

                    // New Controls
                    recordingIndicator: document.getElementById('chat-recording-indicator'),
                    recordingControls: document.getElementById('chat-recording-controls'),
                    reviewIndicator: document.getElementById('chat-review-indicator'),
                    reviewLabel: document.getElementById('chat-review-label'),
                    reviewControls: document.getElementById('chat-review-controls'),
                    voiceSend: document.getElementById('chat-voice-send'),
                    voiceRetake: document.getElementById('chat-voice-retake'),
                    visualizer: document.getElementById('chat-audio-visualizer')
                };

                this.init();
            }

            getOrCreateSessionId() {
                // Use sessionStorage: Persists on Page Refresh/Navigation, Clears on Tab Close
                let sess = sessionStorage.getItem('chat_session_id');
                if (!sess) {
                    sess = 'sess_' + Math.random().toString(36).substr(2, 9) + '_' + Date.now();
                    sessionStorage.setItem('chat_session_id', sess);
                }
                return sess;
            }

            async init() {
                if (!this.dom.app) return;
                
                // Add status indicator to header
                const headerP = this.dom.window.querySelector('header p') || this.dom.window.querySelector('div > p');
                if (headerP) {
                    this.dom.statusText = headerP;
                }

                this.bindEvents();
                await this.checkStatus();
                await this.loadHistory();
                
                // Start message polling (every 2s)
                this.startPolling();

                // Start status polling (every 30s)
                setInterval(() => this.checkStatus(), 30000);

                // Initialize Mute Button State
                this.updateMuteUI();
            }

            bindEvents() {
                this.dom.toggleBtn.addEventListener('click', () => this.toggle());
                
                this.dom.sendBtn.addEventListener('click', () => {
                    this.handleSearch(this.dom.input.value);
                });

                this.dom.input.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        this.handleSearch(this.dom.input.value);
                    }
                });

                // File Upload Events
                this.dom.attachBtn.addEventListener('click', () => {
                    this.dom.fileInput.click();
                });

                this.dom.fileInput.addEventListener('change', (e) => {
                    const file = e.target.files[0];
                    if (file) {
                        this.state.selectedFile = file;
                        this.showFilePreview(file.name);
                    }
                });

                this.dom.removeFileBtn.addEventListener('click', () => {
                    this.state.selectedFile = null;
                    this.dom.fileInput.value = '';
                    this.dom.filePreview.style.display = 'none';
                });

                // Voice/Video Recording Events
                if (this.dom.voiceBtn) {
                    this.dom.voiceBtn.addEventListener('click', () => this.startRecording('voice'));
                }
                if (this.dom.videoBtn) {
                    this.dom.videoBtn.addEventListener('click', () => this.startRecording('video'));
                }
                if (this.dom.voiceStop) {
                    this.dom.voiceStop.addEventListener('click', () => this.stopRecording(false));
                }
                if (this.dom.voiceCancel) {
                    this.dom.voiceCancel.addEventListener('click', () => this.stopRecording(true));
                }
                if (this.dom.voiceSend) {
                    this.dom.voiceSend.addEventListener('click', () => this.sendRecording());
                }
                if (this.dom.voiceRetake) {
                    this.dom.voiceRetake.addEventListener('click', () => this.resetRecording());
                }

                if (this.dom.muteBtn) {
                    this.dom.muteBtn.addEventListener('click', (e) => {
                        e.stopPropagation(); // Prevent closing if inside header (though it's a button so safe)
                        this.toggleMute();
                    });
                }

                document.addEventListener('click', (e) => {
                    if (this.state.isOpen && 
                        !this.dom.app.contains(e.target) && 
                        !this.dom.toggleBtn.contains(e.target)) {
                        this.toggle(false);
                    }
                });
            }

            // --- Recording Methods (Voice & Video) ---

            async startRecording(type = 'voice') {
                try {
                    const constraints = { 
                        audio: true 
                    };
                    if (type === 'video') {
                        constraints.video = { 
                            width: { ideal: 640 }, 
                            height: { ideal: 480 }, 
                            facingMode: 'user' 
                        };
                    }

                    let stream;
                    try {
                        console.log('[Chat Debug] Requesting combined stream...', constraints);
                        stream = await navigator.mediaDevices.getUserMedia(constraints);
                    } catch (e) {
                        if (type === 'video') {
                            console.warn('[Chat Debug] Combined getUserMedia failed, trying separate capture:', e.message);
                            const vStream = await navigator.mediaDevices.getUserMedia({ video: constraints.video });
                            const aStream = await navigator.mediaDevices.getUserMedia({ audio: true });
                            stream = new MediaStream([...vStream.getVideoTracks(), ...aStream.getAudioTracks()]);
                        } else {
                            throw e;
                        }
                    }

                    const tracks = stream.getTracks();
                    console.log('[Chat Debug] Stream acquired. Tracks:', tracks.map(t => `${t.kind}: ${t.label} (enabled: ${t.enabled}, muted: ${t.muted})`));
                    
                    if (type === 'video') {
                        const hasAudio = stream.getAudioTracks().length > 0;
                        if (!hasAudio) {
                            console.warn('[Chat Debug] Audio track still missing! Trying one last attempt for audio...');
                            try {
                                const extraAudio = await navigator.mediaDevices.getUserMedia({ audio: true });
                                extraAudio.getAudioTracks().forEach(track => stream.addTrack(track));
                                console.log('[Chat Debug] Extra audio track added manually.');
                            } catch (ae) {
                                console.error('[Chat Debug] Final attempt to get audio failed:', ae);
                            }
                        }
                    } else if (stream.getAudioTracks().length === 0) {
                        console.error('[Chat Debug] NO AUDIO TRACKS FOUND!');
                    }
                    
                    this.state.recordingType = type;
                    
                    this.state.recordingType = type;
                    
                    try {
                        const mimeType = this.getSupportedMimeType(type);
                        console.log('[Chat Debug] Using MimeType:', mimeType);
                        
                        const options = mimeType ? { 
                            mimeType,
                            audioBitsPerSecond: 128000,
                            videoBitsPerSecond: 2500000
                        } : undefined;
                        this.state.mediaRecorder = new MediaRecorder(stream, options);
                    } catch (e) {
                        console.error('Failed to create MediaRecorder:', e);
                        alert('Браузер не поддерживает запись медиа.');
                        this.resetRecording();
                        return;
                    }
                    
                    console.log('MediaRecorder created with mimeType:', this.state.mediaRecorder.mimeType);
                    this.state.audioChunks = [];

                    if (type === 'video' && this.dom.videoPreview) {
                        this.dom.videoPreview.srcObject = stream;
                        this.dom.videoPreviewContainer.style.display = 'block';
                    }

                    this.state.mediaRecorder.ondataavailable = (event) => {
                        if (event.data.size > 0) {
                            console.log('[Chat Debug] Chunk received:', event.data.size, 'type:', event.data.type);
                            this.state.audioChunks.push(event.data);
                        }
                    };

                    this.state.mediaRecorder.onstop = async () => {
                        console.log('MediaRecorder stopped. Chunks:', this.state.audioChunks.length);
                        if (this.state.isRecordingCanceled) {
                            this.resetRecording();
                            return;
                        }

                        if (this.state.audioChunks.length === 0) {
                            console.error('No recording data captured.');
                            alert('Запись не удалась: не получено данных. Попробуйте еще раз.');
                            this.resetRecording();
                            return;
                        }

                        const duration = Math.round((Date.now() - this.state.recordingStartTime) / 1000);
                        this.state.recordedDuration = duration;
                        console.log('[Chat Debug] Final recorded duration:', duration, 's');

                        const mimeType = this.state.mediaRecorder.mimeType || (type === 'video' ? 'video/webm' : 'audio/webm');
                        
                        // Map MIME type to a compatible extension for Telegram
                        let extension = 'webm';
                        if (mimeType.includes('mp4')) {
                            extension = 'mp4';
                        } else if (mimeType.includes('ogg')) {
                            extension = 'ogg';
                        } else if (mimeType.includes('webm')) {
                            extension = (type === 'video') ? 'webm' : 'weba'; // use .weba for audio-only webm
                        }
                        
                        const fileName = `${type}_${Date.now()}.${extension}`;
                        
                        const blob = new Blob(this.state.audioChunks, { type: mimeType });
                        console.log('Final blob size:', blob.size, 'type:', blob.type);
                        
                        if (this.state.recordedFileUrl) URL.revokeObjectURL(this.state.recordedFileUrl);
                        this.state.recordedFileUrl = URL.createObjectURL(blob);
                        this.state.recordedFile = new File([blob], fileName, { type: mimeType });
                        
                        this.enterReviewState();
                    };

                    this.state.mediaRecorder.start(1000); // Collect chunks every 1 second
                    this.state.isRecording = true;
                    this.state.recordingState = 'recording';
                    this.state.recordingStartTime = Date.now();
                    this.showRecordingUI();
                    this.startTimer();
                    this.startVisualizer(stream);

                } catch (err) {
                    console.error('Recording access denied or error:', err);
                    const msg = type === 'video' ? 'камеры и микрофона' : 'микрофона';
                    alert(`Для записи требуется доступ к ${msg}.`);
                }
            }

            stopRecording(cancel = false) {
                if (this.state.mediaRecorder && this.state.mediaRecorder.state === 'recording') {
                    this.state.isRecordingCanceled = cancel;
                    this.state.mediaRecorder.stop();
                    // Stop streaming
                    this.state.mediaRecorder.stream.getTracks().forEach(track => track.stop());
                }
            }

            enterReviewState() {
                this.state.recordingState = 'review';
                this.stopTimer();
                
                // Switch UI to review
                this.dom.recordingIndicator.style.display = 'none';
                this.dom.recordingControls.style.display = 'none';
                this.dom.reviewIndicator.style.display = 'flex';
                this.dom.reviewControls.style.display = 'flex';

                const isVideo = this.state.recordingType === 'video';
                if (this.dom.reviewLabel) {
                    this.dom.reviewLabel.textContent = isVideo ? 'Видео записано' : 'Голос записан';
                }

                if (isVideo && this.dom.videoPreview) {
                    this.dom.videoPreview.srcObject = null;
                    this.dom.videoPreview.src = this.state.recordedFileUrl;
                    this.dom.videoPreview.classList.add('recorded');
                    this.dom.videoPreview.muted = false;
                    this.dom.videoPreview.loop = true;
                    this.dom.videoPreview.play();
                } else if (!isVideo && this.dom.audioPreview) {
                     // Show audio player for voice
                     this.dom.audioPreview.src = this.state.recordedFileUrl;
                     this.dom.audioPreview.style.display = 'block';
                     this.dom.audioPreview.play();
                }
            }

            async sendRecording() {
                if (!this.state.recordedFile) return;
                
                const type = this.state.recordingType;
                const file = this.state.recordedFile;
                
                if (this.state.audioChunks.length === 0) {
                    console.error('[Chat Debug] Attempting to send empty recording.');
                    alert('Запись пуста. Пожалуйста, попробуйте еще раз.');
                    return;
                }
                
                const duration = this.state.recordedDuration || 0;
                this.resetRecording();
                await this.handleSearch('', file, type === 'voice', type === 'video', duration);
            }

            resetRecording() {
                this.state.isRecording = false;
                this.state.recordingState = 'idle';
                this.state.isRecordingCanceled = false;
                this.state.recordedFile = null;
                this.state.audioChunks = [];
                
                if (this.state.mediaRecorder && this.state.mediaRecorder.state === 'recording') {
                    this.state.mediaRecorder.stop();
                    this.state.mediaRecorder.stream.getTracks().forEach(track => track.stop());
                }

                this.hideRecordingUI();
                this.stopTimer();

                // UI Reset
                this.dom.recordingIndicator.style.display = 'flex';
                this.dom.recordingControls.style.display = 'flex';
                this.dom.reviewIndicator.style.display = 'none';
                this.dom.reviewControls.style.display = 'none';

                if (this.dom.videoPreview) {
                    this.dom.videoPreview.srcObject = null;
                    this.dom.videoPreview.src = '';
                    this.dom.videoPreview.classList.remove('recorded');
                    this.dom.videoPreview.muted = true;
                    this.dom.videoPreview.controls = false;
                    this.dom.videoPreviewContainer.style.display = 'none';
                }

                if (this.dom.audioPreview) {
                    this.dom.audioPreview.pause();
                    this.dom.audioPreview.src = '';
                    this.dom.audioPreview.style.display = 'none';
                }

                if (this.state.recordedFileUrl) {
                    URL.revokeObjectURL(this.state.recordedFileUrl);
                    this.state.recordedFileUrl = null;
                }

                if (this.state.animationFrame) {
                    cancelAnimationFrame(this.state.animationFrame);
                }
                if (this.state.audioContext) {
                    this.state.audioContext.close();
                    this.state.audioContext = null;
                }
            }

            async startVisualizer(stream) {
                try {
                    this.state.audioContext = new (window.AudioContext || window.webkitAudioContext)();
                    if (this.state.audioContext.state === 'suspended') {
                        await this.state.audioContext.resume();
                    }
                    const source = this.state.audioContext.createMediaStreamSource(stream);
                    this.state.analyser = this.state.audioContext.createAnalyser();
                    this.state.analyser.fftSize = 64;
                    source.connect(this.state.analyser);

                    const bufferLength = this.state.analyser.frequencyBinCount;
                    const dataArray = new Uint8Array(bufferLength);
                    const canvasCtx = this.dom.visualizer.getContext('2d');

                    const draw = () => {
                        this.state.animationFrame = requestAnimationFrame(draw);
                        this.state.analyser.getByteFrequencyData(dataArray);

                        canvasCtx.clearRect(0, 0, this.dom.visualizer.width, this.dom.visualizer.height);
                        const barWidth = (this.dom.visualizer.width / bufferLength) * 2.5;
                        let barHeight;
                        let x = 0;

                        for (let i = 0; i < bufferLength; i++) {
                            barHeight = dataArray[i] / 2;
                            canvasCtx.fillStyle = '#2563EB';
                            canvasCtx.fillRect(x, this.dom.visualizer.height - barHeight, barWidth, barHeight);
                            x += barWidth + 1;
                        }
                    };
                    draw();
                } catch (e) {
                    console.error('Visualizer error:', e);
                }
            }

            getSupportedMimeType(type) {
                const isVideo = type === 'video';
                const types = isVideo ? [
                    'video/webm;codecs=vp8,opus',
                    'video/webm;codecs=vp9,opus',
                    'video/webm',
                    'video/mp4'
                ] : [
                    'audio/ogg;codecs=opus',
                    'audio/ogg',
                    'audio/mp4',
                    'audio/webm;codecs=opus',
                    'audio/webm'
                ];

                for (const t of types) {
                    if (MediaRecorder.isTypeSupported(t)) {
                        return t;
                    }
                }
                console.warn('[Chat Debug] No preferred mimeType supported, using default.');
                return ''; // Let browser decide default
            }

            showRecordingUI() {
                if (this.dom.recordingUI) this.dom.recordingUI.style.display = 'flex';
                this.dom.input.disabled = true;
            }

            hideRecordingUI() {
                if (this.dom.recordingUI) this.dom.recordingUI.style.display = 'none';
                this.dom.input.disabled = false;
            }

            startTimer() {
                if (!this.dom.recordingTimer) return;
                this.dom.recordingTimer.textContent = '00:00';
                this.state.timerInterval = setInterval(() => {
                    const seconds = Math.floor((Date.now() - this.state.recordingStartTime) / 1000);
                    const m = Math.floor(seconds / 60).toString().padStart(2, '0');
                    const s = (seconds % 60).toString().padStart(2, '0');
                    this.dom.recordingTimer.textContent = `${m}:${s}`;
                }, 1000);
            }

            stopTimer() {
                if (this.state.timerInterval) {
                    clearInterval(this.state.timerInterval);
                    this.state.timerInterval = null;
                }
            }

            toggle(forceState = null) {
                this.state.isOpen = forceState !== null ? forceState : !this.state.isOpen;
                
                // Persist state
                sessionStorage.setItem('chat_is_open', this.state.isOpen ? 'true' : 'false');

                if (this.state.isOpen) {
                    this.dom.window.classList.add('open');
                    this.dom.iconChat.style.display = 'none';
                    this.dom.iconClose.style.display = 'block';
                    setTimeout(() => this.dom.input.focus(), 100);
                    
                    // Start history polling when open
                    this.startPolling();
                } else {
                    this.dom.window.classList.remove('open');
                    this.dom.iconChat.style.display = 'block';
                    this.dom.iconClose.style.display = 'none';
                    
                    // Stop polling when closed
                    this.stopPolling();
                }
            }

            async checkStatus() {
                try {
                    const response = await fetch(`${this.config.root}qa/v1/status`);
                    const data = await response.json();
                    this.state.isOnline = data.is_online;
                    if (this.dom.statusText) {
                        this.dom.statusText.innerHTML = this.state.isOnline 
                            ? '<span style="color: #4ade80;">●</span> Online' 
                            : '<span style="color: #9ca3af;">●</span> Offline';
                    }
                } catch (e) {
                    console.error('Status check failed', e);
                }
            }

            async loadHistory() {
                if (!this.state.sessionId) return;

                try {
                    // Add timestamp to prevent browser caching
                    const response = await fetch(`${this.config.root}qa/v1/history?session_id=${this.state.sessionId}&_t=${Date.now()}`);
                    const history = await response.json();
                    
                    if (Array.isArray(history) && history.length > this.state.lastHistoryCount) {
                        // Append only new messages
                        const newMessages = history.slice(this.state.lastHistoryCount);
                        let hasBotReply = false;

                        newMessages.forEach(msg => {
                            // 1. Deduplication Logic
                            const trimmedText = msg.text.trim();
                            
                            // A) Check against optimistic sent text
                            if (msg.role === 'user' && this.state.lastSentMessage === trimmedText) {
                                if (msg.attachment) {
                                    this.updateLastMessageAttachment(msg.attachment);
                                }
                                this.state.lastSentMessage = null; 
                                return;
                            }
                            
                            // B) Check against actual DOM (Final safety net - Deep Check)
                            // We check the last 5 messages to be sure we don't duplicate.
                            const lastBubbles = Array.from(this.dom.results.querySelectorAll('.chat-msg-bubble')).slice(-5);
                            const isDuplicate = lastBubbles.some(b => {
                                const raw = b.getAttribute('data-raw-text');
                                return raw === trimmedText;
                            });

                            if (isDuplicate) {
                                console.log('[Chat Debug] message deduped via DOM check:', trimmedText);
                                return;
                            }

                            if (msg.role === 'user') {
                                // Determine type hint from msg data
                                let typeHint = 'file';
                                if (msg.is_voice) typeHint = 'audio';
                                if (msg.is_video) typeHint = 'video';
                                if (!msg.is_voice && !msg.is_video && msg.attachment) {
                                    // Try to guess image
                                    const ext = msg.attachment.split('.').pop().toLowerCase();
                                    if (['jpg','jpeg','png','gif','webp'].includes(ext)) typeHint = 'image';
                                }

                                this.appendUserMessage(msg.text, false, msg.attachment, null, typeHint); 
                            } else {
                                this.appendBotMessage(msg.text, false, msg.attachment);
                            }
                            hasBotReply = true;
                        });
                        
                        // 2. Animation Logic
                        // If we received a reply from the bot, stop the loading dots
                        if (hasBotReply) {
                            this.toggleLoading(false);
                            
                            // Play sound if not initial load and not muted
                            if (this.state.lastHistoryCount > 0) {
                                try {
                                    this.playNotificationSound();
                                } catch (err) { 
                                    console.error('Sound error handled to prevent crash:', err);
                                }
                            }
                        }

                        this.state.lastHistoryCount = history.length;
                        this.scrollToBottom();
                    }
                } catch (e) {
                    // console.error('History load failed', e);
                }
            }

            startPolling() {
                this.stopPolling();
                this.pollInterval = setInterval(() => this.loadHistory(), 2000);
            }

            stopPolling() {
                if (this.pollInterval) {
                    clearInterval(this.pollInterval);
                    this.pollInterval = null;
                }
            }

            async handleSearch(query, recordedFile = null, isVoice = false, isVideo = false, duration = 0) {
                if (!query.trim() && !this.state.selectedFile && !recordedFile) return;

                console.log('[Chat Debug] handleSearch. Query:', query, 'File:', recordedFile || this.state.selectedFile, 'Duration:', duration);
                const fileToUpload = recordedFile || this.state.selectedFile;
                
                // Optimistic UI: show message immediately
                const tempUrl = fileToUpload ? URL.createObjectURL(fileToUpload) : null;
                
                let optimisticText = query;
                let typeHint = 'file'; // Default for regular uploads

                if (!optimisticText && fileToUpload && !isVoice && !isVideo) {
                    optimisticText = '📎 Attached File';
                    // Try to detect image for optimistic render
                    if (fileToUpload.type.startsWith('image/')) typeHint = 'image';
                }
                if (isVoice) {
                    optimisticText = '🎤 Voice Message';
                    typeHint = 'audio';
                }
                if (isVideo) {
                    optimisticText = '📹 Video Message';
                    typeHint = 'video';
                }
                
                this.appendUserMessage(optimisticText, true, tempUrl, fileToUpload ? fileToUpload.name : null, typeHint);

                this.dom.input.value = '';
                
                // Track last sent text for deduplication
                // FIX: Use the exact optimistic text we just showed, so when it comes back from server we recognize it.
                this.state.lastSentMessage = optimisticText;
                
                this.state.selectedFile = null;
                this.dom.fileInput.value = '';
                this.dom.filePreview.style.display = 'none';

                // If Online: Send directly to admin
                if (this.state.isOnline) {
                    this.toggleLoading(true);
                    // Update: use optimisticText as the message text content for consistency
                    await this.sendMessageDirectly(optimisticText, fileToUpload, isVoice, isVideo, duration);
                    // ...
                    // Note: We DO NOT turn off loading here. 
                    // We wait for the poll to find a reply (or timeout).
                    
                    // Safety timeout: stop loading after 60s if no reply
                    setTimeout(() => {
                        this.toggleLoading(false);
                    }, 60000);
                    
                    return;
                }

                // If Offline: Search Knowledge Base
                this.toggleLoading(true);

                try {
                    const [results] = await Promise.all([
                        this.fetchResults(query),
                        new Promise(resolve => setTimeout(resolve, 600))
                    ]);

                    this.toggleLoading(false);
                    this.renderResults(results);
                } catch (error) {
                    console.error('Widget Error:', error);
                    this.toggleLoading(false);
                    this.appendBotMessage("An error occurred. Please try again later.");
                }
            }

            async sendMessageDirectly(text, file = null, isVoice = false, isVideo = false, duration = 0) {
                console.log('sendMessageDirectly called:', { text, fileName: file ? file.name : 'null', isVoice, isVideo, duration });
                try {
                    const formData = new FormData();
                    formData.append('message', text);
                    formData.append('session_id', this.state.sessionId);
                    formData.append('duration', duration);
                    
                    if (file) {
                        formData.append('chat_file', file);
                        if (isVoice) {
                            formData.append('is_voice', '1');
                        }
                        if (isVideo) {
                            formData.append('is_video', '1');
                        }
                    }

                    const apiUrl = this.config.root + 'qa/v1/contact';
                    console.log('Fetching API:', apiUrl);

                    const response = await fetch(apiUrl, {
                        method: 'POST',
                        headers: {
                            'X-WP-Nonce': this.config.nonce
                        },
                        body: formData
                    });
                    
                    console.log('API Response status:', response.status);
                    const data = await response.json();
                    console.log('API Response data:', data);
                    console.log('[Chat Debug] Full Server Response:', JSON.stringify(data, null, 2));
                    if (data.success) {
                        // Success! We wait for polling to bring the answer.
                        // Force a poll sooner just in case
                        setTimeout(() => this.loadHistory(), 1000);
                    } else {
                        this.toggleLoading(false); // Stop on error
                        const errorMsg = data.message ? `⚠️ ${data.message}` : "⚠️ Message could not be delivered to admin.";
                        this.appendBotMessage(errorMsg);
                    }
                } catch (e) {
                    console.error('Direct Message Error:', e);
                    this.toggleLoading(false); // Stop on error
                    this.appendBotMessage("⚠️ Network error. Please try again.");
                }
            }

            async fetchResults(query) {
                const url = `${this.config.apiUrl}?search=${encodeURIComponent(query)}&per_page=5`;
                const response = await fetch(url);
                if (!response.ok) throw new Error('Network error');
                return await response.json();
            }

            renderResults(posts) {
                if (posts.length > 0) {
                    posts.forEach(post => {
                        this.appendBotResponse(post);
                    });
                } else {
                    // If offline, show specific message
                    if (!this.state.isOnline) {
                        this.appendBotMessage(this.state.offlineMsg || 'We are currently offline. Please leave a message.');
                    } else {
                        this.appendBotMessage('Sorry, I couldn\'t find an answer in the knowledge base.');
                    }
                    this.appendContactOption();
                }
            }

            // --- UI Rendering Methods ---

            appendUserMessage(text, scroll = true, attachment = null, attachmentName = null, typeHint = null) {
                const msgDiv = document.createElement('div');
                msgDiv.className = 'mb-4 flex flex-col items-end animate-fade-in-up';
                msgDiv.style.marginBottom = '16px';
                msgDiv.style.display = 'flex';
                msgDiv.style.flexDirection = 'column';
                msgDiv.style.alignItems = 'flex-end';

                let attachmentHtml = '';
                if (attachment) {
                    attachmentHtml = this.renderAttachment(attachment, attachmentName, typeHint);
                }

                msgDiv.innerHTML = `
                    <div class="chat-msg-bubble" data-raw-text="${this.escapeHtml(text)}" style="background-color: var(--chat-accent); color: white; padding: 12px; border-radius: 16px; border-top-right-radius: 0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); max-width: 85%; word-break: break-word; font-size: 0.875rem;">
                        ${text ? `<div>${this.escapeHtml(text)}</div>` : ''}
                        ${attachmentHtml}
                    </div>
                `;
                this.dom.results.appendChild(msgDiv);
                if (scroll) this.scrollToBottom();
            }

            appendBotMessage(text, scroll = true, attachment = null, attachmentName = null, typeHint = null) {
                const msgDiv = document.createElement('div');
                msgDiv.className = 'mb-4 flex flex-col items-start animate-fade-in-up';
                msgDiv.style.marginBottom = '16px';
                msgDiv.style.display = 'flex';
                msgDiv.style.flexDirection = 'column';
                msgDiv.style.alignItems = 'flex-start';

                let attachmentHtml = '';
                if (attachment) {
                    attachmentHtml = this.renderAttachment(attachment, attachmentName, typeHint);
                }

                // If text contains HTML tags and we trust it (it's from our own backend history)
                // we only escape if it DOESN'T look like HTML we already handled.
                // But for now, let's just use a hybrid approach or trust bot history.
                const contentHtml = (text && text.includes('<a ')) ? text : this.escapeHtml(text);

                msgDiv.innerHTML = `
                     <div class="chat-msg-bubble" data-raw-text="${this.escapeHtml(text)}" style="background-color: var(--chat-msg-bg); color: var(--chat-text); padding: 12px; border-radius: 16px; border-top-left-radius: 0; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); border: 1px solid var(--chat-border); max-width: 85%; font-size: 0.875rem;">
                        ${contentHtml}
                        ${attachmentHtml}
                    </div>
                `;
                this.dom.results.appendChild(msgDiv);
                if (scroll) this.scrollToBottom();
            }

            updateLastMessageAttachment(realUrl) {
                // Find the last user message bubble that has an attachment
                const links = this.dom.results.querySelectorAll('.chat-attachment-item');
                if (links.length > 0) {
                    const lastLink = links[links.length - 1];
                    const img = lastLink.querySelector('img');
                    if (img && img.src.startsWith('blob:')) {
                        const oldBlobUrl = img.src;
                        img.src = realUrl;
                        // Revoke blob URL to free memory
                        URL.revokeObjectURL(oldBlobUrl);
                    }
                }
            }

            renderAttachment(url, customName = null, typeHint = null) {
                const fileName = customName || url.split('/').pop();
                const isBlob = url.startsWith('blob:');
                
                // Determine Type
                let type = 'file';
                const audioExts = ['ogg', 'opus', 'mp3', 'wav', 'weba', 'm4a', 'aac'];
                const videoExts = ['webm', 'mp4', 'mkv', 'mov'];
                const imgExts   = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                
                const ext = fileName.split('.').pop().toLowerCase();
                
                if (typeHint) {
                    type = typeHint;
                } else {
                    if (imgExts.includes(ext) || (isBlob && !this.state.recordingType)) type = 'image';
                    if (audioExts.includes(ext) || (isBlob && this.state.recordingType === 'voice')) type = 'audio';
                    if (videoExts.includes(ext) || (isBlob && this.state.recordingType === 'video')) type = 'video';
                }

                if (type === 'image') {
                    const fallbackHtml = `
                        <div class="chat-attachment-fallback" style="display: none; align-items: center; gap: 8px; padding: 12px; background: rgba(0,0,0,0.05); border-radius: 8px; width: 100%; box-sizing: border-box;">
                            <div class="chat-attachment-icon" style="width: 20px; height: 20px;">
                                <svg style="width: 12px; height: 12px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <span style="font-size: 0.7rem; opacity: 0.8;">Image Preview</span>
                        </div>
                    `;

                    return `
                        <div class="chat-attachment-item chat-attachment-card" style="padding: 4px; display: flex; flex-direction: column; align-items: flex-start;">
                            <img src="${url}" 
                                 style="max-width: 100%; border-radius: 8px; margin-bottom: 4px; display: block;" 
                                 alt="Attached image"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            ${fallbackHtml}
                            <span style="padding: 4px 8px; opacity: 0.8; font-size: 0.7rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 100%; box-sizing: border-box;">${this.escapeHtml(fileName)}</span>
                        </div>
                    `;
                }

                if (type === 'audio') {
                    return `
                        <div class="chat-attachment-item chat-media-container" style="padding: 8px;">
                            <audio src="${url}" controls class="chat-media-audio"></audio>
                            <div style="padding: 4px 8px; opacity: 0.7; font-size: 0.65rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${this.escapeHtml(fileName)}</div>
                        </div>
                    `;
                }

                if (type === 'video') {
                    return `
                        <div class="chat-attachment-item chat-media-container" style="padding: 4px;">
                            <video src="${url}" controls class="chat-media-video"></video>
                            <div style="padding: 4px 8px; opacity: 0.7; font-size: 0.65rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${this.escapeHtml(fileName)}</div>
                        </div>
                    `;
                }

                return `
                    <div class="chat-attachment-item chat-attachment-card">
                        <div class="chat-attachment-icon">
                            <svg style="width: 14px; height: 14px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                            </svg>
                        </div>
                        <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${this.escapeHtml(fileName)}</span>
                    </div>
                `;
            }

            appendBotResponse(post) {
                const msgDiv = document.createElement('div');
                msgDiv.className = 'mb-4 flex flex-col items-start w-full animate-fade-in-up';
                msgDiv.style.marginBottom = '16px';
                msgDiv.style.width = '100%';

                msgDiv.innerHTML = `
                     <div style="background-color: var(--chat-msg-bg); color: var(--chat-text); padding: 16px; border-radius: 16px; border-top-left-radius: 0; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); border: 1px solid var(--chat-border); width: 100%; max-width: 95%; box-sizing: border-box;">
                        <div style="font-weight: bold; margin-bottom: 8px; color: var(--chat-accent); padding-bottom: 8px; border-bottom: 1px solid var(--chat-border); font-size: 0.9rem;">
                            ${post.title.rendered}
                        </div>
                        <div style="font-size: 0.85rem; opacity: 0.9; max-height: 160px; overflow-y: auto;">
                            ${post.content.rendered}
                        </div>
                    </div>
                `;
                this.dom.results.appendChild(msgDiv);
                this.scrollToBottom();
            }

            appendContactOption() {
                const msgDiv = document.createElement('div');
                msgDiv.className = 'mb-4 flex flex-col items-start animate-fade-in-up';
                msgDiv.style.marginBottom = '16px';
                msgDiv.style.width = '100%';

                const formId = 'contact-form-' + Date.now();
                
                msgDiv.innerHTML = `
                     <div style="background-color: var(--chat-msg-bg); color: var(--chat-text); padding: 16px; border-radius: 16px; border-top-left-radius: 0; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); border: 1px solid var(--chat-border); max-width: 95%; width: 100%; box-sizing: border-box;">
                        <p style="margin-top: 0; margin-bottom: 12px; font-weight: bold; font-size: 0.9rem;">Want to ask a human?</p>
                        <form id="${formId}" onsubmit="return false;" novalidate>
                            
                            <!-- Honeypot -->
                            <input type="text" name="website_url" style="display:none !important;" tabindex="-1" autocomplete="off">
                            
                            <input type="text" name="name" class="chat-form-input" placeholder="Your Name" required>
                            <input type="email" name="email" class="chat-form-input" placeholder="Email" required>
                            
                            <textarea name="msg" class="chat-form-input" placeholder="Your question..." style="min-height: 80px;" required></textarea>
                            
                            <button type="button" class="submit-btn" style="width: 100%; background: var(--chat-accent); color: white; padding: 12px; border-radius: 8px; border: none; cursor: pointer; font-size: 0.85rem; font-weight: bold; transition: opacity 0.2s;">Send Message</button>
                        </form>
                    </div>
                `;
                
                this.dom.results.appendChild(msgDiv);
                
                const form = document.getElementById(formId);
                const btn = form.querySelector('.submit-btn');
                const inputs = form.querySelectorAll('.chat-form-input');

                // Attach Validation Listeners
                inputs.forEach(input => {
                    input.addEventListener('input', () => this.validateField(input));
                    input.addEventListener('blur', () => this.validateField(input));
                });

                btn.onclick = () => this.handleContactSubmit(form, btn);
                
                this.scrollToBottom();
            }

            validateField(input) {
                let isValid = false;
                if (input.name === 'email') {
                    isValid = this.validateEmail(input.value);
                } else if (input.hasAttribute('required')) {
                    isValid = input.value.trim().length > 0;
                } else {
                     isValid = true; 
                }

                if (isValid) {
                    input.classList.remove('invalid');
                    if(input.value.trim().length > 0) input.classList.add('valid');
                } else {
                    input.classList.remove('valid');
                    input.classList.add('invalid');
                }
                return isValid;
            }

            async handleContactSubmit(form, btn) {
                const honeypot = form.querySelector('[name="website_url"]').value;
                const nameInput = form.querySelector('[name="name"]');
                const emailInput = form.querySelector('[name="email"]');
                const msgInput = form.querySelector('[name="msg"]');

                // 1. Honeypot check
                if (honeypot) return;

                // 2. Validate all
                let isFormValid = true;
                if (!this.validateField(nameInput)) isFormValid = false;
                if (!this.validateField(emailInput)) isFormValid = false;
                if (!this.validateField(msgInput)) isFormValid = false;

                if (!isFormValid) {
                    return; 
                }

                // Show loading animation
                btn.innerHTML = 'Sending<span class="loading-dot">.</span><span class="loading-dot">.</span><span class="loading-dot">.</span>';
                btn.disabled = true;
                btn.style.opacity = '0.7';

                try {
                    const response = await fetch(this.config.root + 'qa/v1/contact', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-WP-Nonce': this.config.nonce
                        },
                        body: JSON.stringify({ 
                            name: nameInput.value, 
                            email: emailInput.value, 
                            message: msgInput.value,
                            website_url: honeypot,
                            session_id: this.state.sessionId
                        })
                    });
                    
                    const data = await response.json();

                    if (data.success) {
                        form.innerHTML = '<div style="color: var(--chat-accent); font-weight: bold; text-align: center; padding: 20px; font-size: 0.9rem;">✅ Message sent!<br>We will contact you soon.</div>';
                        // Refresh history immediately to show the user's message as confirmed
                        setTimeout(() => this.loadHistory(), 1000);
                    } else {
                        btn.innerText = 'Error, try again';
                        btn.disabled = false;
                        btn.style.opacity = '1';
                    }
                } catch (e) {
                    console.error(e);
                    btn.innerText = 'Network Error';
                    btn.disabled = false;
                    btn.style.opacity = '1';
                }
            }

            validateEmail(email) {
                return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            }

            toggleLoading(show) {
                if (show) {
                    this.dom.loading.style.display = 'flex';
                } else {
                    this.dom.loading.style.display = 'none';
                }
                this.scrollToBottom();
            }

            scrollToBottom() {
                this.dom.messagesBody.scrollTop = this.dom.messagesBody.scrollHeight;
            }

            escapeHtml(text) {
                const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
                return text.replace(/[&<>"']/g, function(m) { return map[m]; });
            }

            toggleMute() {
                this.state.isMuted = !this.state.isMuted;
                localStorage.setItem('chat_muted', this.state.isMuted);
                this.updateMuteUI();
            }

            showFilePreview(name) {
                const nameEl = this.dom.filePreview.querySelector('.file-name');
                if (nameEl) nameEl.textContent = name;
                this.dom.filePreview.style.display = 'flex';
                this.scrollToBottom();
            }

            updateMuteUI() {
                if (!this.dom.muteBtn || !this.dom.iconVolUp || !this.dom.iconVolOff) return; // Safety check

                if (this.state.isMuted) {
                    this.dom.iconVolUp.style.display = 'none';
                    this.dom.iconVolOff.style.display = 'block';
                    this.dom.muteBtn.style.opacity = '0.5';
                } else {
                    this.dom.iconVolUp.style.display = 'block';
                    this.dom.iconVolOff.style.display = 'none';
                    this.dom.muteBtn.style.opacity = '1';
                }
            }

            playNotificationSound() {
                if (this.state.isMuted) return;

                // 1. Try Custom Sound first
                if (this.state.customAudio) {
                    this.state.customAudio.currentTime = 0;
                    this.state.customAudio.play()
                        .catch(e => {
                            console.warn('Custom audio play failed (fallback to default):', e);
                            this.playOscillatorFallback(); 
                        });
                    return;
                }

                this.playOscillatorFallback();
            }

            playOscillatorFallback() {
                // 2. Fallback to Oscillator (The "Ding")
                try {
                    const AudioContext = window.AudioContext || window.webkitAudioContext;
                    if (!AudioContext) return;

                    const ctx = new AudioContext();
                    const t = ctx.currentTime;
                    
                    // oscillator 1: Main tone
                    const osc = ctx.createOscillator();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(880, t); // A5
                    osc.frequency.exponentialRampToValueAtTime(440, t + 0.5); // Drop pitch slightly
                    
                    // Gain (Volume envelope)
                    const gain = ctx.createGain();
                    gain.gain.setValueAtTime(0.05, t);
                    gain.gain.exponentialRampToValueAtTime(0.00001, t + 0.5);

                    osc.connect(gain);
                    gain.connect(ctx.destination);

                    osc.start(t);
                    osc.stop(t + 0.5);
                } catch (e) {
                    console.error('Audio play failed', e);
                }
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initHelpWidget);
        } else {
            initHelpWidget();
        }

        // Init immediately
        function initHelpWidget() {
            if (window.rockStarsChatInitialized) return; 
            window.rockStarsChatInitialized = true;

            if (document.getElementById('chat-widget-app')) {
                new HelpWidget(chatWidgetConfig);
                // console.log('Chat Widget: Initialized');
            }
        }

    })();
    </script>
    <?php
}
add_action( 'wp_footer', 'render_chat_widget_html' );
