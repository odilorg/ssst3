<div class="h-full flex flex-col bg-white dark:bg-gray-800 border-l border-gray-200 dark:border-gray-700">
    {{-- Header --}}
    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 3.5a1.5 1.5 0 013 0V4a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-.5a1.5 1.5 0 000 3h.5a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-.5a1.5 1.5 0 00-3 0v.5a1 1 0 01-1 1H6a1 1 0 01-1-1v-3a1 1 0 00-1-1h-.5a1.5 1.5 0 010-3H4a1 1 0 001-1V6a1 1 0 011-1h3a1 1 0 001-1v-.5z"/>
                </svg>
                AI Copilot
            </h3>
            <span class="text-xs text-gray-500 dark:text-gray-400">
                Cost: ${{ number_format($totalCost, 4) }}
            </span>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="p-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
        <div class="flex gap-2 flex-wrap">
            <button
                wire:click="enrichLead"
                class="px-3 py-1.5 text-xs font-medium bg-blue-100 hover:bg-blue-200 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 text-blue-700 dark:text-blue-300 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove wire:target="enrichLead">🔍 Enrich Lead</span>
                <span wire:loading wire:target="enrichLead">⏳ Enriching...</span>
            </button>

            <button
                wire:click="$set('showEmailModal', true)"
                class="px-3 py-1.5 text-xs font-medium bg-green-100 hover:bg-green-200 dark:bg-green-900/30 dark:hover:bg-green-900/50 text-green-700 dark:text-green-300 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed"
                wire:loading.attr="disabled"
            >
                ✉️ Generate Email
            </button>

            <button
                wire:click="suggestFollowup"
                class="px-3 py-1.5 text-xs font-medium bg-purple-100 hover:bg-purple-200 dark:bg-purple-900/30 dark:hover:bg-purple-900/50 text-purple-700 dark:text-purple-300 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove wire:target="suggestFollowup">📅 Suggest Follow-up</span>
                <span wire:loading wire:target="suggestFollowup">⏳ Thinking...</span>
            </button>
        </div>
    </div>

    {{-- Chat Messages --}}
    <div class="flex-1 overflow-y-auto p-4 space-y-4" id="chat-messages">
        @forelse($messages as $msg)
            <div class="flex {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-[85%] {{ $msg['role'] === 'user' ? 'bg-blue-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' }} rounded-lg px-4 py-2">
                    <div class="text-sm whitespace-pre-wrap">{!! nl2br(e($msg['content'])) !!}</div>
                    <div class="text-xs mt-1 {{ $msg['role'] === 'user' ? 'text-blue-100' : 'text-gray-500 dark:text-gray-400' }}">
                        {{ $msg['timestamp'] }}
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                <p class="text-sm">👋 Hi! I'm your AI assistant.</p>
                <p class="text-xs mt-2">Ask me anything about this lead or use the quick actions above.</p>
            </div>
        @endforelse

        @if($isLoading)
            <div class="flex justify-start">
                <div class="bg-gray-100 dark:bg-gray-700 rounded-lg px-4 py-2">
                    <div class="flex space-x-2">
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Input Area --}}
    <div class="p-4 border-t border-gray-200 dark:border-gray-700">
        <form wire:submit="sendMessage" class="flex gap-2">
            <input
                type="text"
                wire:model="message"
                placeholder="Ask me anything..."
                class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                wire:loading.attr="disabled"
            >
            <button
                type="submit"
                class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition disabled:opacity-50"
                wire:loading.attr="disabled"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
            </button>
        </form>
    </div>

    {{-- Email Modal --}}
    @if($showEmailModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Generate Email</h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Purpose</label>
                    <select wire:model="emailPurpose" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                        <option value="initial_outreach">Initial Outreach</option>
                        <option value="follow_up">Follow-up</option>
                        <option value="proposal">Send Proposal</option>
                        <option value="pricing">Pricing Discussion</option>
                        <option value="meeting_request">Meeting Request</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tone</label>
                    <select wire:model="emailTone" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                        <option value="professional">Professional</option>
                        <option value="friendly">Friendly</option>
                        <option value="direct">Direct</option>
                        <option value="formal">Formal</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-2 mt-6">
                <button
                    wire:click="generateEmail"
                    class="flex-1 px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition disabled:opacity-50"
                    wire:loading.attr="disabled"
                    wire:target="generateEmail"
                >
                    <span wire:loading.remove wire:target="generateEmail">Generate</span>
                    <span wire:loading wire:target="generateEmail">Generating...</span>
                </button>
                <button
                    wire:click="$set('showEmailModal', false)"
                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition"
                >
                    Cancel
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Auto-scroll script --}}
    <script>
        document.addEventListener('livewire:initialized', () => {
            const chatMessages = document.getElementById('chat-messages');
            if (chatMessages) {
                // Scroll to bottom on initial load
                chatMessages.scrollTop = chatMessages.scrollHeight;

                // Scroll to bottom when new messages arrive
                Livewire.on('message-sent', () => {
                    setTimeout(() => {
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    }, 100);
                });
            }
        });

        // Also scroll after Livewire updates
        document.addEventListener('livewire:update', () => {
            const chatMessages = document.getElementById('chat-messages');
            if (chatMessages) {
                setTimeout(() => {
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }, 100);
            }
        });
    </script>
</div>
