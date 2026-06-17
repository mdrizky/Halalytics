<!-- resources/views/components/ai-chat-widget.blade.php -->
    <div
    x-data="{ open: false, message: '', chatHistory: [], userRole: 'user', aiResponse: '', isLoading: false, aiUrl: '/ai/chat', promoChatUrl: '/promo/ai_chat' }"
    x-show="open"
    class="fixed bottom-4 right-4 z-50 w-96 h-96 bg-white shadow-lg rounded-lg flex flex-col transition-all duration-300"
    style=""
    x-cloak
    @open-chat.window.window="open = true"
    @close-chat.window.window="open = false"
    @new-message.window="addMessage($event.detail)"
    @ai-response.window="handleAiResponse($event.detail)"
>
    <div class="bg-primary-500 text-white p-4 rounded-t-lg flex justify-between items-center">
        <h3 class="font-semibold text-lg">Halalytics AI Assistant</h3>
        <button @click="open = false" class="text-white hover:text-gray-200 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    <div class="flex-1 overflow-y-auto p-4 space-y-4 chat-history"
         x-ref="chatHistoryContainer"
         @scroll.window="handleScroll">
        <template x-for="(msg, index) in chatHistory" :key="index">
            <div :class="{'ml-auto mr-4': msg.sender === 'user', 'mr-auto ml-4': msg.sender === 'ai'}">
                <div :class="{'bg-primary-100 text-primary-800': msg.sender === 'user', 'bg-gray-200 text-gray-800': msg.sender === 'ai'}"
                     class="rounded-lg p-3 shadow max-w-xs break-words">
                    <p x-text="msg.text"></p>
                    <p class="text-xs text-gray-500 mt-1" x-text="msg.timestamp"></p>
                </div>
            </div>
        </template>
        <div x-show="isLoading" class="text-center py-2">
            <div class="animate-pulse text-gray-500">AI sedang mengetik...</div>
        </div>
    </div>

    <div class="p-4 border-t flex items-center">
        <input type="text"
               x-model="message"
               @keydown.enter="sendMessage"
               placeholder="Ketik pesan Anda..."
               class="flex-1 border rounded-lg p-2 mr-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
        />
        <button @click="sendMessage"
                :disabled="!message.trim() || isLoading"
                class="bg-primary-500 text-white px-4 py-2 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-500">
            Kirim
        </button>
    </div>
</div>

<button id="chat-toggle-button"
        @click="open = !open"
        class="fixed bottom-4 right-4 bg-primary-500 text-white w-16 h-16 rounded-full shadow-lg flex items-center justify-center hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-500 z-50">
    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4a2 2 0 01-2 2H5a2 2 0 01-2-2V17a2 2 0 012-2h2V8a2 2 0 012-2h8a2 2 0 012-2z"></path></svg>
</button>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('chat', () => ({
            open: false,
            message: '',
            chatHistory: [],
            userRole: 'user', // Default role, can be dynamic
            aiResponse: '',
            isLoading: false,
            aiUrl: '/ai/chat',
            promoChatUrl: '/promo/ai_chat',
            csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            chatToggleBtn: document.getElementById('chat-toggle-button'),

            init() {
                this.$watch('open', (value) => {
                    if (value) {
                        this.scrollToBottom();
                        // Automatically fetch initial messages if available or trigger welcome message
                        if (this.chatHistory.length === 0) {
                            this.fetchInitialChat();
                        }
                    }
                });
                // Close chat if user clicks outside
                document.addEventListener('click', (event) => {
                    if (this.open && !this.$el.contains(event.target) && !this.chatToggleBtn.contains(event.target)) {
                        this.open = false;
                    }
                });
            },

            addMessage(messageData) {
                this.chatHistory.push(messageData);
                this.scrollToBottom();
            },

            sendMessage() {
                if (!this.message.trim() || this.isLoading) return;

                this.addMessage({ sender: 'user', text: this.message, timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) });

                this.isLoading = true;

                const targetUrl = this.aiUrl;


        fetch(targetUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ message: this.message, user_role: this.userRole, promo_context: this.open })
        })
        .then(response => {
            if (!response.ok) {
                // Handle HTTP error responses
                if (response.status === 419) {
                    // CSRF token mismatch or expired
                    return response.text().then(text => {
                        console.error('CSRF Error:', text);
                        this.addMessage({ sender: 'ai', text: 'Sesi Anda mungkin telah berakhir. Silakan refresh halaman dan coba lagi.', timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) });
                    });
                } else {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
            }
            return response.json();
        })
        .then(data => {
            this.aiResponse = data.reply || data.message || 'Maaf, terjadi kesalahan.';
            this.addMessage({ sender: 'ai', text: this.aiResponse, timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) });
            this.isLoading = false;
        })
        .catch(error => {
            console.error('Error sending message:', error);
            this.aiResponse = 'Gagal menghubungi AI. Silakan coba lagi.';
            this.addMessage({ sender: 'ai', text: this.aiResponse, timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) });
            this.isLoading = false;
        });

                this.message = '';
            },
            
            handleAiResponse(response) {
                this.aiResponse = response;
                this.addMessage({ sender: 'ai', text: this.aiResponse, timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) });
                this.isLoading = false;
            },

            scrollToBottom() {
                this.$nextTick(() => {
                    const container = this.$refs.chatHistoryContainer;
                    if (container) {
                        container.scrollTop = container.scrollHeight;
                    }
                });
            },
            
            fetchInitialChat() {
                // Placeholder for fetching initial chat history or welcome message
                // In a real app, you might fetch conversation history here
                this.addMessage({ sender: 'ai', text: 'Halo! Ada yang bisa saya bantu hari ini?', timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) });
            },
            
            handleScroll() {
                // Logic for loading more messages if needed (infinite scroll)
            }
        }));
    });
</script>
