<x-app-layout>
<x-slot name="header">
    <div class="flex items-center gap-3">
        <a href="{{ url()->previous() }}" 
           class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">{{ __('messages.msg_index_title') }}</h1>
            <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.msg_chat_with', ['name' => trim($otherUser->f_name . ' ' . $otherUser->l_name) ?: 'User']) }}</p>
        </div>
    </div>
</x-slot>

    @php
        $otherFullName = trim($otherUser->f_name . ' ' . $otherUser->l_name) ?: 'User';
        $otherInitial = strtoupper(substr($otherUser->f_name ?? 'U', 0, 1));
        $myInitial = strtoupper(substr(auth()->user()->f_name ?? 'U', 0, 1));
        $lastMessageId = $messages->last()->id ?? 0;
    @endphp

    <div class="bg-white dark:bg-neutral-950 antialiased text-neutral-800 dark:text-neutral-200 h-[calc(100vh-140px)] sm:h-[calc(100vh-170px)] lg:h-[calc(100vh-180px)] overflow-hidden">
    <div class="w-full sm:max-w-4xl mx-auto px-2 sm:px-16 lg:px-24 h-full py-2 sm:py-6">

            <div class="bg-white dark:bg-neutral-900 rounded-2xl sm:rounded-3xl shadow-sm sm:shadow-xl border border-gray-200/60 dark:border-neutral-800 overflow-hidden flex flex-col h-full"
                x-data='chatApp({
                    otherId: {{ $otherUser->id }},
                    lastId: {{ $lastMessageId }},
                    myId: {{ auth()->id() }},
                    csrfToken: "{{ csrf_token() }}",
                    initialMessages: @json($messagesJson ?? [])
                })'
                x-init='init()'>

                <!-- CHAT HEADER -->
                <div class="flex items-center gap-4 p-3 sm:p-5 border-b border-gray-100 dark:border-neutral-800 flex-shrink-0">
                    <div class="relative">
                        @if($otherUser->profile_photo && file_exists(public_path('storage/' . $otherUser->profile_photo)))
                            <img src="{{ asset('storage/' . $otherUser->profile_photo) }}" 
                                 alt="{{ $otherFullName }}" 
                                 class="w-10 h-10 sm:w-12 sm:h-12 rounded-full object-cover border-2 border-primary/20">
                        @else
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-primary flex items-center justify-center text-white font-black">
                                {{ $otherInitial }}
                            </div>
                        @endif
                        <span class="absolute bottom-0 right-0 w-2.5 h-2.5 sm:w-3 sm:h-3 bg-green-500 rounded-full border-2 border-white dark:border-neutral-900"></span>
                    </div>
                    <div>
                        <h2 class="font-black text-[#1B3B36] dark:text-white text-sm sm:text-base">{{ $otherFullName }}</h2>
                        <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400">
                            <span x-show="!isTyping">{{ __('messages.msg_online_status') }}</span>
                            <span x-show="isTyping" x-cloak class="text-primary font-semibold">{{ __('messages.msg_typing') }}</span>
                        </p>
                    </div>
                </div>

                <!-- MESSAGES THREAD -->
                <div x-ref="thread" 
                     class="flex-1 overflow-y-auto p-3 sm:p-6 space-y-3 sm:space-y-4 bg-neutral-50/50 dark:bg-neutral-950/30 min-h-0">

                    <!-- No messages yet -->
                    <template x-if="messages.length === 0">
                        <div class="flex flex-col items-center justify-center py-16 text-center">
                            <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mb-3">
                                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-neutral-600 dark:text-neutral-400">{{ __('messages.msg_no_messages') }}</p>
                            <p class="text-xs text-neutral-400 mt-1">{{ __('messages.msg_say_hi') }}</p>
                        </div>
                    </template>

                    <!-- Message loop -->
                    <template x-for="(msg, index) in messages" :key="msg.id">
                        <div class="group/message relative" 
                            @mouseenter="hoveredId = msg.id" 
                            @mouseleave="hoveredId = null">

                            <!-- Reply Preview -->
                            <template x-if="msg.reply_to">
                                <div :class="msg.is_mine ? 'ml-auto mr-12 max-w-[70%]' : 'ml-12 max-w-[70%]'">
                                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-t-xl bg-neutral-100 dark:bg-neutral-800 border-l-2 border-primary text-xs">
                                        <svg class="w-3 h-3 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                        </svg>
                                        <span class="font-bold text-primary shrink-0" x-text="msg.reply_to.sender_name + ':'"></span>
                                        <span class="text-neutral-500 dark:text-neutral-400 truncate" x-text="msg.reply_to.message"></span>
                                    </div>
                                </div>
                            </template>

                            <!-- Message Row -->
                            <div :class="msg.is_mine 
                                    ? 'flex items-end gap-2 ml-auto flex-row-reverse max-w-[90%] sm:max-w-[85%]' 
                                    : 'flex items-end gap-2 max-w-[90%] sm:max-w-[85%]'">

                                <!-- Avatar -->
                                <div :class="msg.is_mine 
                                        ? 'w-6 h-6 sm:w-8 sm:h-8 rounded-full bg-primary flex items-center justify-center text-[10px] sm:text-xs font-bold text-white shrink-0 mb-5' 
                                        : 'w-6 h-6 sm:w-8 sm:h-8 rounded-full bg-primary/10 flex items-center justify-center text-[10px] sm:text-xs font-bold text-primary shrink-0 mb-5'">
                                    <span x-text="msg.is_mine ? '{{ $myInitial }}' : '{{ $otherInitial }}'"></span>
                                </div>

                                <!-- Bubble + Timestamp + Reactions -->
                                <div class="min-w-0">
                                    <div :class="msg.is_mine 
                                        ? 'bg-primary text-white rounded-2xl rounded-tr-none px-3 py-2 sm:px-4 sm:py-3 shadow-sm' 
                                        : 'bg-white dark:bg-neutral-800 rounded-2xl rounded-tl-none px-3 py-2 sm:px-4 sm:py-3 shadow-sm border border-gray-100 dark:border-neutral-700'">

                                        <!-- Image Attachment -->
                                        <template x-if="msg.attachment_url && msg.message_type === 'image'">
                                            <img :src="msg.attachment_url" 
                                                :alt="'Attachment'" 
                                                class="max-w-full rounded-lg mb-1.5 cursor-pointer hover:opacity-90 transition"
                                                style="max-height: 240px;"
                                                @click="window.open(msg.attachment_url, '_blank')">
                                        </template>

                                        <!-- Document Attachment -->
                                        <template x-if="msg.attachment_url && msg.message_type !== 'image'">
                                            <a :href="msg.attachment_url" 
                                            target="_blank"
                                            class="flex items-center gap-2 px-3 py-2 rounded-lg mb-1.5 text-xs font-bold hover:underline">
                                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                {{ __('messages.msg_view_document') }}
                                            </a>
                                        </template>

                                        <!-- Text -->
                                        <p x-show="msg.message" 
                                        :class="msg.is_mine ? 'text-xs sm:text-sm leading-relaxed whitespace-pre-wrap break-words' : 'text-xs sm:text-sm text-neutral-800 dark:text-white leading-relaxed whitespace-pre-wrap break-words'" 
                                        x-text="msg.message"></p>
                                    </div>

                                    <span :class="msg.is_mine ? 'text-[9px] sm:text-[10px] text-neutral-400 mt-0.5 block text-right' : 'text-[9px] sm:text-[10px] text-neutral-400 mt-0.5 block'" x-text="msg.created_at"></span>

                                    <!-- Reactions Display -->
                                    <template x-if="msg.reactions && msg.reactions.length > 0">
                                        <div :class="msg.is_mine ? 'flex flex-wrap gap-1 mt-1 justify-end' : 'flex flex-wrap gap-1 mt-1'">
                                            <template x-for="(reaction, rIdx) in msg.reactions" :key="rIdx">
                                                <button type="button"
                                                        @click="toggleReaction(msg.id, reaction.emoji)"
                                                        :class="reaction.user_reacted 
                                                            ? 'bg-primary/20 border-primary text-primary' 
                                                            : 'bg-neutral-100 dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 text-neutral-600 dark:text-neutral-400'"
                                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full border text-xs hover:scale-110 transition">
                                                    <span x-text="reaction.emoji"></span>
                                                    <span class="font-bold text-[10px]" x-text="reaction.count"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </template>
                                </div>

                                <!-- Hover Actions -->
                                <div x-show="hoveredId === msg.id" 
                                    x-cloak
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 scale-90"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    class="flex items-center gap-1 shrink-0 mb-5">

                                    <!-- Reply button -->
                                    <button type="button"
                                            @click="startReply(msg)"
                                            class="w-7 h-7 rounded-full bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 text-neutral-500 hover:text-primary hover:border-primary shadow-md flex items-center justify-center transition"
                                            title="{{ __('messages.msg_reply') }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                        </svg>
                                    </button>

                                    <!-- React button -->
                                    <div class="relative" x-data="{ emojiPickerOpen: false }">
                                        <button type="button"
                                                @click="emojiPickerOpen = !emojiPickerOpen"
                                                class="w-7 h-7 rounded-full bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 text-neutral-500 hover:text-primary hover:border-primary shadow-md flex items-center justify-center transition"
                                                title="{{ __('messages.msg_react') }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </button>

                                        <!-- Emoji Picker -->
                                        <div x-show="emojiPickerOpen" 
                                            @click.away="emojiPickerOpen = false"
                                            x-cloak
                                            x-transition:enter="transition ease-out duration-150"
                                            x-transition:enter-start="opacity-0 scale-90"
                                            x-transition:enter-end="opacity-100 scale-100"
                                            :class="msg.is_mine ? 'right-0' : 'left-0'"
                                            class="absolute bottom-9 z-30 bg-white dark:bg-neutral-800 rounded-full shadow-xl border border-gray-200 dark:border-neutral-700 px-2 py-1.5 flex items-center gap-1">
                                            <template x-for="emoji in ['👍','❤️','😂','😮','😢','🙏']" :key="emoji">
                                                <button type="button"
                                                        @click="toggleReaction(msg.id, emoji); emojiPickerOpen = false"
                                                        class="w-8 h-8 rounded-full hover:bg-neutral-100 dark:hover:bg-neutral-700 text-lg flex items-center justify-center transition hover:scale-125"
                                                        x-text="emoji"></button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    
                    <div class="h-2"></div>
                </div>

                <!-- MESSAGE INPUT -->
                <div class="p-2 sm:p-4 border-t border-gray-100 dark:border-neutral-800 bg-white dark:bg-neutral-900 flex-shrink-0">

                    <!-- Reply Preview -->
                    <template x-if="replyTo">
                        <div class="flex items-center gap-2 px-3 py-2 mb-2 rounded-xl bg-primary/5 border-l-4 border-primary">
                            <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-primary" x-text="'{{ __('messages.msg_replying_to') }} ' + replyTo.sender_name"></p>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 truncate" x-text="replyTo.message"></p>
                            </div>
                            <button type="button" @click="cancelReply()" 
                                    class="w-6 h-6 rounded-full hover:bg-neutral-100 dark:hover:bg-neutral-800 flex items-center justify-center text-neutral-400 hover:text-red-500 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </template>

                    <!-- Attachment Preview -->
                    <template x-if="pendingAttachment">
                        <div class="flex items-center gap-3 px-3 py-2 mb-2 rounded-xl bg-primary/5 border border-primary/20">
                            <!-- Image thumbnail -->
                            <template x-if="pendingAttachment.isImage">
                                <img :src="pendingAttachment.preview" 
                                     alt="Preview"
                                     class="w-14 h-14 rounded-lg object-cover border border-primary/20 shrink-0">
                            </template>

                            <!-- Document icon -->
                            <template x-if="!pendingAttachment.isImage">
                                <div class="w-14 h-14 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 shrink-0">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                            </template>

                            <!-- File info -->
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-neutral-800 dark:text-white truncate" x-text="pendingAttachment.name"></p>
                                <p class="text-[10px] text-neutral-500 dark:text-neutral-400 mt-0.5" x-text="pendingAttachment.size"></p>
                            </div>

                            <!-- Remove button -->
                            <button type="button" 
                                    @click="clearAttachment()"
                                    class="w-8 h-8 rounded-full hover:bg-red-100 dark:hover:bg-red-900/30 flex items-center justify-center text-neutral-400 hover:text-red-500 transition shrink-0"
                                    title="{{ __('messages.msg_remove_attachment') }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </template>

                    <form @submit.prevent="sendMessage" class="flex gap-2 sm:gap-3 items-center">
                        <!-- Plus Icon with Options -->
                        <div class="relative" x-data="{ attachOpen: false }">
                            <button type="button" 
                                    @click="attachOpen = !attachOpen"
                                    class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-500 dark:text-neutral-400 hover:bg-primary/10 hover:text-primary transition flex items-center justify-center flex-shrink-0 cursor-pointer">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                </svg>
                            </button>

                            <!-- Attachment options -->
                            <div x-show="attachOpen" 
                                 @click.away="attachOpen = false"
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 class="absolute bottom-12 left-0 z-30 bg-white dark:bg-neutral-800 rounded-2xl shadow-xl border border-gray-200 dark:border-neutral-700 py-2 w-48">
                                <button type="button"
                                        onclick="document.getElementById('fileInput').click(); attachOpen = false"
                                        class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-neutral-50 dark:hover:bg-neutral-700 transition text-left">
                                    <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-neutral-800 dark:text-white">{{ __('messages.msg_photo') }}</p>
                                        <p class="text-[10px] text-neutral-400">{{ __('messages.msg_photo_hint') }}</p>
                                    </div>
                                </button>
                                <button type="button"
                                        onclick="document.getElementById('fileInput').click(); attachOpen = false"
                                        class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-neutral-50 dark:hover:bg-neutral-700 transition text-left">
                                    <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-neutral-800 dark:text-white">{{ __('messages.msg_document') }}</p>
                                        <p class="text-[10px] text-neutral-400">{{ __('messages.msg_document_hint') }}</p>
                                    </div>
                                </button>
                            </div>
                        </div>

                        <!-- File Input -->
                        <input type="file" 
                               id="fileInput" 
                               class="hidden" 
                               accept="image/*,.pdf,.doc,.docx"
                               @change="handleFileSelect($event)">

                        <textarea x-model="newMessage"
                                  x-ref="input"
                                  @input="onInput"
                                  @keydown.enter="if (!$event.shiftKey) { $event.preventDefault(); sendMessage(); }"
                                  placeholder="{{ __('messages.msg_type_message') }}"
                                  rows="1"
                                  class="flex-1 px-3 py-2 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-950 text-sm sm:text-base text-neutral-800 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition min-h-[44px] max-h-32 resize-none overflow-y-auto"
                                  style="height: 44px;"
                        ></textarea>

                        <button type="submit" 
                                :disabled="sending"
                                class="bg-primary hover:bg-primary-600 text-white font-bold text-xs sm:text-sm px-4 py-2 sm:px-6 sm:py-3 rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5 flex items-center gap-1.5 sm:gap-2 flex-shrink-0 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            <span class="hidden sm:inline" x-text="sending ? '{{ __('messages.msg_sending') }}' : '{{ __('messages.msg_send') }}'">{{ __('messages.msg_send') }}</span>
                        </button>
                    </form>
                    <p class="text-[9px] sm:text-[10px] text-neutral-400 dark:text-neutral-500 text-center mt-1.5">
                        {{ __('messages.msg_press') }} <kbd class="px-1 py-0.5 rounded bg-neutral-100 dark:bg-neutral-800 text-[8px]">Enter</kbd> {{ __('messages.msg_to_send') }} <kbd class="px-1 py-0.5 rounded bg-neutral-100 dark:bg-neutral-800 text-[8px]">Shift+Enter</kbd> {{ __('messages.msg_for_newline') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

   @push('scripts')
    <script>
    window.chatApp = function(config) {
        return {
            messages: [],
            newMessage: '',
            sending: false,
            isTyping: false,
            hoveredId: null,
            replyTo: null,
            pendingAttachment: null,        
            lastId: config.lastId || 0,
            otherId: config.otherId,
            myId: config.myId,
            csrfToken: config.csrfToken || '',
            pollInterval: null,

            init() {
                console.log('[chatApp] init started', {
                    otherId: this.otherId,
                    messageCount: (config.initialMessages || []).length
                });

                this.messages = config.initialMessages || [];

                this.$nextTick(() => this.scrollToBottom());

                this.pollInterval = setInterval(() => this.pollNewMessages(), 3000);
            },

            startReply(msg) {
                this.replyTo = {
                    id: msg.id,
                    message: msg.message,
                    sender_name: msg.is_mine ? '{{ __('messages.msg_you') }}' : '{{ $otherFullName }}',
                };
                this.$refs.input.focus();
            },

            cancelReply() {
                this.replyTo = null;
            },

            handleFileSelect(event) {
                const file = event.target.files[0];
                if (!file) {
                    this.pendingAttachment = null;
                    return;
                }

                if (file.size > 20 * 1024 * 1024) {
                    alert('{{ __('messages.msg_file_too_large') }}');
                    event.target.value = '';
                    this.pendingAttachment = null;
                    return;
                }

                const isImage = file.type.startsWith('image/');
                const sizeInMB = (file.size / (1024 * 1024)).toFixed(2);
                const sizeInKB = (file.size / 1024).toFixed(2);
                const displaySize = file.size > 1024 * 1024 
                    ? sizeInMB + ' MB' 
                    : sizeInKB + ' KB';

                const attachment = {
                    name: file.name,
                    size: displaySize,
                    isImage: isImage,
                    preview: null,
                };

                if (isImage) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.pendingAttachment = { ...attachment, preview: e.target.result };
                    };
                    reader.readAsDataURL(file);
                } else {
                    this.pendingAttachment = attachment;
                }
            },

            clearAttachment() {
                this.pendingAttachment = null;
                const fileInput = document.getElementById('fileInput');
                if (fileInput) fileInput.value = '';
            },

            async sendMessage() {
                const text = this.newMessage.trim();
                const fileInput = document.getElementById('fileInput');
                const file = fileInput && fileInput.files && fileInput.files.length > 0
                    ? fileInput.files[0]
                    : null;

                if ((!text && !file) || this.sending) return;

                this.sending = true;

                try {
                    const formData = new FormData();
                    formData.append('_token', this.csrfToken);
                    formData.append('message', text);

                    if (this.replyTo) {
                        formData.append('reply_to_id', this.replyTo.id);
                    }

                    if (file) {
                        formData.append('attachment', file);
                    }

                    const res = await fetch(`/messages/${this.otherId}`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                        },
                        body: formData,
                    });

                    if (!res.ok) {
                        alert('{{ __('messages.msg_send_failed_status', ['status' => '']) }}'.replace(':status', res.status));
                        return;
                    }

                    const data = await res.json();

                    if (data.success) {
                        this.messages.push(data.message);
                        this.lastId = data.message.id;
                        this.newMessage = '';
                        this.replyTo = null;
                        this.pendingAttachment = null;

                        if (fileInput) {
                            fileInput.value = '';
                        }

                        this.$nextTick(() => this.scrollToBottom());
                    }
                } catch (e) {
                    alert('{{ __('messages.msg_send_failed_error', ['error' => '']) }}'.replace(':error', e.message));
                } finally {
                    this.sending = false;
                }
            },

            async toggleReaction(messageId, emoji) {
                try {
                    const res = await fetch(`/messages/${this.otherId}/react`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ message_id: messageId, emoji: emoji }),
                    });

                    const data = await res.json();

                    if (data.success) {
                        await this.reloadAllMessages();
                    }
                } catch (e) {
                    // silent
                }
            },

            async reloadAllMessages() {
                try {
                    const res = await fetch(`/messages/${this.otherId}/fetch?last_id=0`, {
                        headers: { 'Accept': 'application/json' },
                    });
                    const data = await res.json();
                    if (data.messages && data.messages.length > 0) {
                        this.messages = data.messages;
                        this.lastId = Math.max(...data.messages.map(m => m.id));
                    }
                } catch (e) {
                    // silent
                }
            },

            async pollNewMessages() {
                try {
                    const res = await fetch(`/messages/${this.otherId}/fetch?last_id=${this.lastId}`, {
                        headers: { 'Accept': 'application/json' },
                    });
                    const data = await res.json();

                    if (data.count > 0) {
                        data.messages.forEach(m => {
                            if (!this.messages.find(x => x.id === m.id)) {
                                this.messages.push(m);
                                this.lastId = Math.max(this.lastId, m.id);
                            }
                        });
                        this.$nextTick(() => this.scrollToBottom());
                    }
                } catch (e) {
                    // silent
                }
            },

            onInput() {
                const el = this.$refs.input;
                if (el) {
                    el.style.height = 'auto';
                    el.style.height = Math.min(el.scrollHeight, 128) + 'px';
                }
            },

            scrollToBottom() {
                const el = this.$refs.thread;
                if (el) {
                    el.scrollTop = el.scrollHeight;
                }
            }
        };
    };
    </script>
    @endpush
</x-app-layout>