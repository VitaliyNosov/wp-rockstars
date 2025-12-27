/**
 * Help Widget Application
 * Architecture: API-First (Vanilla JS + WP REST API)
 * Ready for React migration.
 */

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

        // Close on click outside
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

        // UI Updates
        this.appendUserMessage(query);
        this.dom.input.value = '';
        this.toggleLoading(true);

        try {
            const results = await this.fetchResults(query);
            this.toggleLoading(false);
            this.renderResults(results);
        } catch (error) {
            console.error('Widget Error:', error);
            this.toggleLoading(false);
            this.appendBotMessage("Произошла ошибка при поиске. Попробуйте позже.");
        }
    }

    async fetchResults(query) {
        // Future SPA: This logic remains the same, just changing the endpoint base
        const url = `${this.config.apiUrl}?search=${encodeURIComponent(query)}&per_page=5`;
        const response = await fetch(url);
        if (!response.ok) throw new Error('Network error');
        return await response.json();
    }

    renderResults(posts) {
        if (posts.length > 0) {
            posts.forEach(post => {
                // Stripping HTML tags for excerpt if needed, or using rendered content
                // For a more "chat-like" feel, we might want to keep it simple
                this.appendBotResponse(post);
            });
        } else {
            this.appendBotMessage("К сожалению, я не нашел точного ответа в базе знаний.");
        }
    }

    // --- UI Rendering Methods ---

    appendUserMessage(text) {
        const msgDiv = document.createElement('div');
        msgDiv.className = 'mb-4 flex flex-col items-end animate-fade-in-up';
        msgDiv.innerHTML = `
            <div class="bg-blue-600 text-white p-3 rounded-2xl rounded-tr-none text-sm shadow-md max-w-[85%] break-words">
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
             <div class="bg-white dark:bg-neutral-800 p-3 rounded-2xl rounded-tl-none text-sm text-gray-800 dark:text-gray-200 shadow-sm border border-gray-100 dark:border-neutral-700 max-w-[85%]">
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
             <div class="bg-white dark:bg-neutral-800 p-4 rounded-2xl rounded-tl-none text-sm text-gray-800 dark:text-gray-200 shadow-sm border border-gray-100 dark:border-neutral-700 w-full max-w-[95%]">
                <div class="font-bold mb-2 text-blue-600 dark:text-blue-400 border-b border-gray-100 dark:border-neutral-700 pb-2">
                    ${post.title.rendered}
                </div>
                <div class="prose dark:prose-invert text-xs mb-2 leading-relaxed opacity-90 max-h-40 overflow-y-auto custom-scrollbar">
                    ${post.content.rendered}
                </div>
            </div>
        `;
        this.dom.results.appendChild(msgDiv);
        this.scrollToBottom();
    }

    toggleLoading(show) {
        if (show) {
            this.dom.loading.classList.remove('hidden');
        } else {
            this.dom.loading.classList.add('hidden');
        }
        this.scrollToBottom();
    }

    scrollToBottom() {
        this.dom.messagesBody.scrollTop = this.dom.messagesBody.scrollHeight;
    }

    escapeHtml(text) {
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.replace(/[&<>"']/g, function (m) { return map[m]; });
    }
}

// Initialize
function initHelpWidget() {
    console.log('Chat Widget: Init Triggered');
    if (typeof chatWidgetConfig !== 'undefined') {
        console.log('Chat Widget: Config found', chatWidgetConfig);
        try {
            new HelpWidget(chatWidgetConfig);
            console.log('Chat Widget: Initialized Successfully');
        } catch (e) {
            console.error('Chat Widget: Init Error', e);
        }
    } else {
        console.error('Chat Widget: Config NOT found');
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initHelpWidget);
} else {
    initHelpWidget();
}
