<?php
/**
 * Logic for displaying the Help/Chat Widget
 */

// Вывод HTML разметки и скриптов в футере
function render_chat_widget_html() {
    // Конфигурация для JS
    $config = array(
        'apiUrl' => get_rest_url( null, 'wp/v2/qa' ),
        'siteUrl' => get_site_url(),
        'root'   => esc_url_raw( rest_url() ),
        'nonce'  => wp_create_nonce( 'wp_rest' )
    );
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
        
        /* Loading Dots Animation */
        @keyframes dot-pulse {
            0% { opacity: .2; }
            20% { opacity: 1; }
            100% { opacity: .2; }
        }
        .loading-dot {
            animation: dot-pulse 1.4s infinite both;
            margin: 0 1px;
            font-size: 1.2em;
        }
        .loading-dot:nth-child(2) { animation-delay: .2s; }
        .loading-dot:nth-child(3) { animation-delay: .4s; }
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
            <div style="padding: 16px; background: linear-gradient(to right, #2563EB, #1e40af); color: white; flex-shrink: 0;">
                <h3 style="margin: 0; font-weight: bold; font-size: 1.125rem;">Help Assistant</h3>
                <p style="margin: 0; font-size: 0.75rem; opacity: 0.9;">Common answers and support</p>
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
                </style>

            </div>

            <!-- Input Area -->
            <div style="padding: 16px; background-color: var(--chat-bg); border-top: 1px solid var(--chat-border); flex-shrink: 0;">
                <div style="position: relative;">
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
                    messages: []
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
                    messagesBody: document.getElementById('chat-messages')
                };

                this.init();
            }

            init() {
                if (!this.dom.app) return;
                this.bindEvents();
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

                document.addEventListener('click', (e) => {
                    if (this.state.isOpen && 
                        !this.dom.app.contains(e.target) && 
                        !this.dom.toggleBtn.contains(e.target)) {
                        this.toggle(false);
                    }
                });
            }

            toggle(forceState = null) {
                this.state.isOpen = forceState !== null ? forceState : !this.state.isOpen;

                if (this.state.isOpen) {
                    this.dom.window.classList.add('open');
                    this.dom.iconChat.style.display = 'none';
                    this.dom.iconClose.style.display = 'block';
                    setTimeout(() => this.dom.input.focus(), 100);
                } else {
                    this.dom.window.classList.remove('open');
                    this.dom.iconChat.style.display = 'block';
                    this.dom.iconClose.style.display = 'none';
                }
            }

            async handleSearch(query) {
                if (!query.trim()) return;

                this.appendUserMessage(query);
                this.dom.input.value = '';
                
                // Show "typing" animation
                this.toggleLoading(true);

                try {
                    // Simulate "reading" delay (min 600ms)
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
                    // Show "Not Found" message and Contact Form button
                    this.appendBotMessage('Sorry, I couldn\'t find an answer in the knowledge base.');
                    this.appendContactOption();
                }
            }

            // --- UI Rendering Methods ---

            appendUserMessage(text) {
                const msgDiv = document.createElement('div');
                msgDiv.className = 'mb-4 flex flex-col items-end animate-fade-in-up';
                msgDiv.innerHTML = `
                    <div style="background-color: var(--chat-accent); color: white; padding: 12px; border-radius: 16px; border-top-right-radius: 0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); max-width: 85%; word-break: break-word;">
                        ${this.escapeHtml(text)}
                    </div>
                `;
                this.dom.results.appendChild(msgDiv);
                this.scrollToBottom();
            }

            appendBotMessage(text) {
                const msgDiv = document.createElement('div');
                msgDiv.className = 'mb-4 flex flex-col items-start animate-fade-in-up';
                msgDiv.innerHTML = `
                     <div style="background-color: var(--chat-msg-bg); color: var(--chat-text); padding: 12px; border-radius: 16px; border-top-left-radius: 0; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); border: 1px solid var(--chat-border); max-width: 85%;">
                        ${text}
                    </div>
                `;
                this.dom.results.appendChild(msgDiv);
                this.scrollToBottom();
            }

            appendBotResponse(post) {
                const msgDiv = document.createElement('div');
                msgDiv.className = 'mb-4 flex flex-col items-start w-full animate-fade-in-up';
                
                msgDiv.innerHTML = `
                     <div style="background-color: var(--chat-msg-bg); color: var(--chat-text); padding: 16px; border-radius: 16px; border-top-left-radius: 0; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); border: 1px solid var(--chat-border); width: 100%; max-width: 95%;">
                        <div style="font-weight: bold; margin-bottom: 8px; color: var(--chat-accent); padding-bottom: 8px; border-bottom: 1px solid var(--chat-border);">
                            ${post.title.rendered}
                        </div>
                        <div style="font-size: 0.75rem; opacity: 0.9; max-height: 160px; overflow-y: auto;">
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
                const formId = 'contact-form-' + Date.now();
                
                msgDiv.innerHTML = `
                     <div style="background-color: var(--chat-msg-bg); color: var(--chat-text); padding: 16px; border-radius: 16px; border-top-left-radius: 0; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); border: 1px solid var(--chat-border); max-width: 95%; width: 100%;">
                        <p style="margin-top: 0; margin-bottom: 12px; font-weight: bold;">Want to ask a human?</p>
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
                            website_url: honeypot 
                        })
                    });
                    
                    const data = await response.json();

                    if (data.success) {
                        form.innerHTML = '<div style="color: var(--chat-accent); font-weight: bold; text-align: center; padding: 20px;">✅ Message sent!<br>We will contact you soon.</div>';
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
        }

        // Init immediately
        function initHelpWidget() {
            if (document.getElementById('chat-widget-app')) {
                new HelpWidget(chatWidgetConfig);
                console.log('Chat Widget: Initialized (Inline)');
            } else {
                console.warn('Chat Widget container not found');
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initHelpWidget);
        } else {
            initHelpWidget();
        }

    })();
    </script>
    <?php
}
add_action( 'wp_footer', 'render_chat_widget_html' );
