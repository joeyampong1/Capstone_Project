<x-app-layout>
    @php
        $otherFullName = trim($otherUser->f_name . ' ' . $otherUser->l_name) ?: 'User';
        $otherInitial = strtoupper(substr($otherUser->f_name ?? 'U', 0, 1));
        $myInitial = strtoupper(substr(auth()->user()->f_name ?? 'U', 0, 1));
        $lastMessageId = $messages->last()->id ?? 0;

        // Passed all via Js::from -> safe against apostrophes / quotes (won't break x-data)
        $chatConfig = [
            'otherId'         => $otherUser->id,
            'otherName'       => $otherFullName,
            'otherInitial'    => $otherInitial,
            'myInitial'       => $myInitial,
            'lastId'          => $lastMessageId,
            'myId'            => auth()->id(),
            'roomId'          => (string) $roomId,
            'csrfToken'       => csrf_token(),
            'initialMessages' => $messagesJson ?? [],
            'labels'          => [
                'you'         => __('messages.msg_you'),
                'replyingTo'  => __('messages.msg_replying_to'),
                'fileTooBig'  => __('messages.msg_file_too_large'),
                'sending'     => __('messages.msg_sending'),
                'send'        => __('messages.msg_send'),
            ],
        ];
    @endphp

    {{--
        ROOT: height is set via JS (fitViewport) = visible screen (including keyboard) - top of root.
        The height class below is just a fallback before JS loads.
    --}}
    <div x-ref="root"
         class="bg-white dark:bg-neutral-950 antialiased text-neutral-800 dark:text-neutral-200 h-[calc(100dvh-110px)] sm:h-[calc(100vh-140px)] lg:h-[calc(100vh-150px)] overflow-hidden"
         x-data="chatApp({{ \Illuminate\Support\Js::from($chatConfig) }})"
         x-init="init()">

        {{-- Mobile: walay padding sa ubos para flush sa phone navigation. Desktop: same ra sa una. --}}
        <div class="w-full sm:max-w-4xl mx-auto px-2 sm:px-16 lg:px-24 h-full pt-1.5 pb-0 sm:py-6">

            {{-- CARD (relative = where the emoji picker anchors) --}}
            <div x-ref="card"
                 class="relative bg-white dark:bg-neutral-900 rounded-t-2xl sm:rounded-3xl shadow-sm sm:shadow-xl border border-b-0 sm:border-b border-gray-200/60 dark:border-neutral-800 overflow-hidden flex flex-col h-full">

                <!-- FIXED CHAT HEADER (<- + User Icon + Name + Status) -->
                <div class="flex items-center gap-3 p-3 sm:p-5 border-b border-gray-100 dark:border-neutral-800 flex-shrink-0 select-none">
                    <a href="{{ url()->previous() }}"
                       class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>

                    <div class="relative shrink-0">
                        @if($otherUser->profile_photo)
                            <img src="{{ $otherUser->profile_photo_url }}"
                                 alt="{{ $otherFullName }}"
                                 class="w-10 h-10 sm:w-12 sm:h-12 rounded-full object-cover border-2 border-primary/20">
                        @else
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-primary flex items-center justify-center text-white font-black">
                                {{ $otherInitial }}
                            </div>
                        @endif
                        <span :class="otherOnline ? 'bg-green-500' : 'bg-neutral-400'"
                              class="absolute bottom-0 right-0 w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full border-2 border-white dark:border-neutral-900 transition-colors"></span>
                    </div>

                    <div class="min-w-0 flex-1">
                        <h2 class="font-black text-[#1B3B36] dark:text-white text-sm sm:text-base truncate leading-tight">{{ $otherFullName }}</h2>
                        <p class="text-[10px] sm:text-xs mt-0.5 truncate">
                            <span x-show="otherTyping" x-cloak class="text-primary font-semibold italic">
                                {{ __('messages.msg_typing') }}...
                            </span>
                            <span x-show="!otherTyping && otherOnline" x-cloak class="text-green-600 font-semibold">
                                ● {{ __('messages.msg_online') }}
                            </span>
                            <span x-show="!otherTyping && !otherOnline" x-cloak class="text-neutral-500 dark:text-neutral-400">
                                {{ __('messages.msg_last_seen') }} <span x-text="formatLastSeen(otherLastSeen)">{{ __('messages.msg_recently') }}</span>
                            </span>
                        </p>
                    </div>
                </div>

                <!-- MESSAGES THREAD (only this scrolls; locked when emoji picker is open) -->
                <div x-ref="thread"
                     :class="anyEmojiPickerOpen ? 'overflow-hidden touch-none' : 'overflow-y-auto overflow-x-hidden'"
                     class="flex-1 p-3 sm:p-6 space-y-3 sm:space-y-4 bg-neutral-50/50 dark:bg-neutral-950/30 min-h-0 overscroll-contain">

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
                             data-msg-row
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
                                    <span x-text="msg.is_mine ? myInitial : otherInitial"></span>
                                </div>

                                <!-- Bubble + Timestamp + Reactions -->
                                <div class="min-w-0">
                                    <div class="relative inline-block max-w-full">

                                        <!-- Bubble -->
                                        <div data-bubble
                                             :class="msg.is_mine
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

                                        <!-- Reactions: overlap bottom edge -->
                                        <template x-if="msg.reactions && msg.reactions.length > 0">
                                            <div :class="msg.is_mine
                                                ? 'absolute bottom-0 right-2 translate-y-1/2 flex flex-wrap gap-1 z-20'
                                                : 'absolute bottom-0 left-2 translate-y-1/2 flex flex-wrap gap-1 z-20'">
                                                <template x-for="(reaction, rIdx) in msg.reactions" :key="rIdx">
                                                    <div :class="reaction.user_reacted
                                                            ? 'bg-white dark:bg-neutral-700 border-primary text-primary'
                                                            : 'bg-white dark:bg-neutral-700 border-neutral-200 dark:border-neutral-600 text-neutral-600 dark:text-neutral-400'"
                                                         class="inline-flex items-center gap-0.5 px-1.5 py-[2px] rounded-full border shadow-md text-xs cursor-default">
                                                        <span x-text="reaction.emoji" class="text-[13px] leading-none"></span>
                                                        <span class="font-bold text-[10px] leading-none" x-text="reaction.count"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>

                                    <!-- Timestamp -->
                                    <span :class="msg.is_mine
                                        ? 'text-[9px] sm:text-[10px] text-neutral-400 mt-4 block text-right pr-1'
                                        : 'text-[9px] sm:text-[10px] text-neutral-400 mt-4 block pl-1'"
                                          x-text="msg.created_at"></span>
                                </div>

                                <!-- Hover Actions -->
                                <div x-show="hoveredId === msg.id || (picker && picker.msgId === msg.id)"
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

                                    <!-- React button (the picker is now outside the loop) -->
                                    <button type="button"
                                            @click="openPicker(msg, $event)"
                                            class="w-7 h-7 rounded-full bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 text-neutral-500 hover:text-primary hover:border-primary shadow-md flex items-center justify-center transition"
                                            title="{{ __('messages.msg_react') }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div class="h-2"></div>
                </div>

                <!-- MESSAGE INPUT (flush at the bottom; safe-area for phones with a gesture bar) -->
                <div class="p-2 sm:p-4 border-t border-gray-100 dark:border-neutral-800 bg-white dark:bg-neutral-900 flex-shrink-0"
                     style="padding-bottom: max(0.5rem, env(safe-area-inset-bottom));">

                    <!-- Reply Preview -->
                    <template x-if="replyTo">
                        <div class="flex items-center gap-2 px-3 py-2 mb-2 rounded-xl bg-primary/5 border-l-4 border-primary">
                            <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-primary" x-text="labels.replyingTo + ' ' + replyTo.sender_name"></p>
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
                            <template x-if="pendingAttachment.isImage">
                                <img :src="pendingAttachment.preview"
                                     alt="Preview"
                                     class="w-14 h-14 rounded-lg object-cover border border-primary/20 shrink-0">
                            </template>

                            <template x-if="!pendingAttachment.isImage">
                                <div class="w-14 h-14 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 shrink-0">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                            </template>

                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-neutral-800 dark:text-white truncate" x-text="pendingAttachment.name"></p>
                                <p class="text-[10px] text-neutral-500 dark:text-neutral-400 mt-0.5" x-text="pendingAttachment.size"></p>
                            </div>

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
                                        @click="document.getElementById('fileInput').click(); attachOpen = false"
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
                                        @click="document.getElementById('fileInput').click(); attachOpen = false"
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

                        {{-- text-base (16px) on mobile so iOS doesn't auto-zoom on focus --}}
                        <textarea x-model="newMessage"
                                  x-ref="input"
                                  @input="onInput"
                                  @focus="onInputFocus"
                                  @keydown.enter="if (!$event.shiftKey) { $event.preventDefault(); sendMessage(); }"
                                  placeholder="{{ __('messages.msg_type_message') }}"
                                  rows="1"
                                  class="flex-1 px-3 py-2 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-950 text-base text-neutral-800 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition min-h-[44px] max-h-32 resize-none overflow-y-auto"
                                  style="height: 44px;"
                        ></textarea>

                        <button type="submit"
                                :disabled="sending"
                                class="bg-primary hover:bg-primary-600 text-white font-bold text-xs sm:text-sm px-4 py-2 sm:px-6 sm:py-3 rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5 flex items-center gap-1.5 sm:gap-2 flex-shrink-0 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            <span class="hidden sm:inline" x-text="sending ? labels.sending : labels.send">{{ __('messages.msg_send') }}</span>
                        </button>
                    </form>

                    <p class="hidden sm:block text-[9px] sm:text-[10px] text-neutral-400 dark:text-neutral-500 text-center mt-1.5">
                        {{ __('messages.msg_press') }} <kbd class="px-1 py-0.5 rounded bg-neutral-100 dark:bg-neutral-800 text-[8px]">Enter</kbd> {{ __('messages.msg_to_send') }} <kbd class="px-1 py-0.5 rounded bg-neutral-100 dark:bg-neutral-800 text-[8px]">Shift+Enter</kbd> {{ __('messages.msg_for_newline') }}
                    </p>
                </div>

                <!-- EMOJI PICKER BACKDROP (click out = close) -->
                <div x-show="picker" x-cloak
                     @click="closePicker()"
                     class="absolute inset-0 z-40"></div>

                <!--
                    EMOJI PICKER:
                -->
                <div x-show="picker" x-cloak
                     class="absolute inset-x-0 z-50 flex justify-center pointer-events-none"
                     :style="picker ? ('top:' + picker.top + 'px') : ''">
                    <div x-show="picker"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-90"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="pointer-events-auto bg-white dark:bg-neutral-800 rounded-full shadow-xl border border-gray-200 dark:border-neutral-700 px-2.5 py-1.5 flex items-center gap-1 w-max max-w-[92%]">
                        <template x-for="emoji in emojis" :key="emoji">
                            <button type="button"
                                    @click="toggleReaction(picker.msgId, emoji)"
                                    class="w-8 h-8 rounded-full hover:bg-neutral-100 dark:hover:bg-neutral-700 text-lg flex items-center justify-center transition hover:scale-125 shrink-0"
                                    x-text="emoji"></button>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-firestore-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-database-compat.js"></script>

    <script>
    const firebaseConfig = {
        apiKey:            "{{ config('firebase.web.api_key') }}",
        authDomain:        "{{ config('firebase.web.auth_domain') }}",
        projectId:         "{{ config('firebase.web.project_id') }}",
        storageBucket:     "{{ config('firebase.web.storage_bucket') }}",
        messagingSenderId: "{{ config('firebase.web.messaging_sender_id') }}",
        appId:             "{{ config('firebase.web.app_id') }}",
        databaseURL:       "{{ config('firebase.web.database_url') }}",
    };

    if (!firebase.apps.length) {
        firebase.initializeApp(firebaseConfig);
    }
    const db   = firebase.firestore();
    const rtdb = firebase.database();

    window.chatApp = function(config) {
        return {
            messages: [],
            newMessage: '',
            sending: false,
            otherTyping: false,
            otherOnline: false,
            otherLastSeen: null,
            hoveredId: null,
            picker: null,               // { msgId, top } | null
            emojis: ['👍', '❤️', '😂', '😮', '😢', '🙏'],
            replyTo: null,
            pendingAttachment: null,
            lastId: config.lastId || 0,
            otherId: config.otherId,
            otherName: config.otherName,
            otherInitial: config.otherInitial,
            myInitial: config.myInitial,
            myId: config.myId,
            roomId: config.roomId,
            labels: config.labels || {},
            csrfToken: config.csrfToken || '',
            typingTimeout: null,
            typingDebounce: null,
            typingRef: null,
            otherTypingRef: null,
            firestoreUnsubscribe: null,
            pollInterval: null,
            _fitRaf: null,
            _fitHandler: null,
            _origViewport: null,

            get anyEmojiPickerOpen() {
                return this.picker !== null;
            },

            init() {
                this.messages = config.initialMessages || [];

                this.lockPage();
                this.fitViewport();
                this.$nextTick(() => this.scrollToBottom());

                this.setupPresence();
                this.setupFirestoreListener();
                this.setupTypingListener();
                this.markAsRead();

                this.pollInterval = setInterval(() => this.pollNewMessages(), 3000);
            },

            destroy() {
                this.unlockPage();
                clearInterval(this.pollInterval);
                if (this.firestoreUnsubscribe) this.firestoreUnsubscribe();
            },

            // ===== PAGE LOCK + VIEWPORT FIT =====
            // 1) Whole page won't scroll (only the thread scrolls)
            // 2) Chat height matches the ACTUAL visible area (including when the keyboard is open),
            //    so the chat header won't get pushed out and the input won't stay stuck/floating up.
            lockPage() {
                document.documentElement.style.overflow = 'hidden';
                document.documentElement.style.overscrollBehavior = 'none';
                document.body.style.overflow = 'hidden';
                document.body.style.overscrollBehavior = 'none';

                // Chrome Android: resizes the layout when the keyboard opens (not just the visual viewport)
                const meta = document.querySelector('meta[name="viewport"]');
                if (meta && !/interactive-widget/.test(meta.content)) {
                    this._origViewport = meta.content;
                    meta.content = meta.content + ', interactive-widget=resizes-content';
                }

                this._fitHandler = () => {
                    cancelAnimationFrame(this._fitRaf);
                    this._fitRaf = requestAnimationFrame(() => this.fitViewport());
                };
                window.addEventListener('resize', this._fitHandler);
                window.addEventListener('orientationchange', this._fitHandler);
                window.addEventListener('scroll', this._fitHandler, { passive: true });
                if (window.visualViewport) {
                    window.visualViewport.addEventListener('resize', this._fitHandler);
                    window.visualViewport.addEventListener('scroll', this._fitHandler);
                }
            },

            unlockPage() {
                document.documentElement.style.overflow = '';
                document.documentElement.style.overscrollBehavior = '';
                document.body.style.overflow = '';
                document.body.style.overscrollBehavior = '';

                const meta = document.querySelector('meta[name="viewport"]');
                if (meta && this._origViewport !== null) meta.content = this._origViewport;

                window.removeEventListener('resize', this._fitHandler);
                window.removeEventListener('orientationchange', this._fitHandler);
                window.removeEventListener('scroll', this._fitHandler);
                if (window.visualViewport) {
                    window.visualViewport.removeEventListener('resize', this._fitHandler);
                    window.visualViewport.removeEventListener('scroll', this._fitHandler);
                }
            },

            fitViewport() {
                const root = this.$refs.root;
                if (!root) return;

                // Prevent the page from scrolling (this is what hides the header when the keyboard opens)
                if (window.scrollY !== 0 || window.scrollX !== 0) window.scrollTo(0, 0);

                const vv = window.visualViewport;
                const visibleBottom = vv ? (vv.offsetTop + vv.height) : window.innerHeight;
                const top = root.getBoundingClientRect().top;

                root.style.height = Math.max(visibleBottom - top, 200) + 'px';

                // If the input is focused (keyboard open), keep the thread scrolled to the bottom
                if (document.activeElement === this.$refs.input) {
                    this.$nextTick(() => this.scrollToBottom());
                }
                // Kung mo-resize samtang naay picker, i-close para dili mo-mali ug posisyon
                if (this.picker) this.picker = null;
            },

            onInputFocus() {
                this.picker = null;
                // Close the picker on resize so it doesn't end up in the wrong position
                setTimeout(() => this.fitViewport(), 50);
                setTimeout(() => this.fitViewport(), 300);
                this.$nextTick(() => this.scrollToBottom());
            },

            // ===== EMOJI PICKER (anchored sa message, centered horizontally) =====
            openPicker(msg, ev) {
                if (this.picker && this.picker.msgId === msg.id) {
                    this.picker = null;
                    return;
                }

                const row = ev.currentTarget.closest('[data-msg-row]');
                const bubble = (row && row.querySelector('[data-bubble]')) || row;
                if (!bubble) return;

                const card = this.$refs.card.getBoundingClientRect();
                const thread = this.$refs.thread.getBoundingClientRect();
                const b = bubble.getBoundingClientRect();

                const H = 44;
                const gap = 6;

                const minTop = (thread.top - card.top) + 4;
                const maxTop = (thread.bottom - card.top) - H - 4;

                // Default: above the message. If there's no space, below it (past the reactions badge)
                let top = (b.top - card.top) - H - gap;
                if (top < minTop) top = (b.bottom - card.top) + gap + 14;

                top = Math.min(Math.max(top, minTop), maxTop);

                this.picker = { msgId: msg.id, top: Math.round(top) };
            },

            closePicker() {
                this.picker = null;
            },

            // ===== PRESENCE (Realtime DB) =====
            setupPresence() {
                const myRef = rtdb.ref('presence/' + this.myId);

                myRef.set({
                    status: 'online',
                    last_seen: firebase.database.ServerValue.TIMESTAMP,
                });
                myRef.onDisconnect().set({
                    status: 'offline',
                    last_seen: firebase.database.ServerValue.TIMESTAMP,
                });

                rtdb.ref('presence/' + this.otherId).on('value', (snap) => {
                    const data = snap.val();
                    if (!data || !data.last_seen) {
                        this.otherOnline = false;
                        this.otherLastSeen = null;
                        return;
                    }
                    const isOnline = data.status === 'online'
                        && (Date.now() - data.last_seen) < 60000;
                    this.otherOnline = isOnline;
                    this.otherLastSeen = data.last_seen;
                });

                setInterval(() => {
                    myRef.update({
                        status: 'online',
                        last_seen: firebase.database.ServerValue.TIMESTAMP,
                    });
                }, 30000);

                window.addEventListener('beforeunload', () => {
                    myRef.set({
                        status: 'offline',
                        last_seen: firebase.database.ServerValue.TIMESTAMP,
                    });
                });
            },

            // ===== TYPING (Realtime DB) =====
            setupTypingListener() {
                this.typingRef = rtdb.ref('typing/' + this.roomId + '/' + this.myId);
                this.otherTypingRef = rtdb.ref('typing/' + this.roomId + '/' + this.otherId);

                this.typingRef.onDisconnect().set({
                    is_typing: false,
                    updated_at: firebase.database.ServerValue.TIMESTAMP,
                });

                this.otherTypingRef.on('value', (snap) => {
                    const data = snap.val();
                    if (!data || !data.is_typing) {
                        this.otherTyping = false;
                        return;
                    }
                    if (data.updated_at && (Date.now() - data.updated_at) < 5000) {
                        this.otherTyping = true;
                        clearTimeout(this.typingTimeout);
                        this.typingTimeout = setTimeout(() => {
                            this.otherTyping = false;
                        }, 5000);
                    } else {
                        this.otherTyping = false;
                    }
                });
            },

            setMyTyping(v) {
                if (!this.typingRef) return;
                this.typingRef.set({
                    is_typing: v,
                    updated_at: firebase.database.ServerValue.TIMESTAMP,
                });
            },

            // ===== FIRESTORE REAL-TIME MESSAGES =====
            setupFirestoreListener() {
                const col = db.collection('chats').doc(this.roomId).collection('messages').orderBy('id', 'asc');

                this.firestoreUnsubscribe = col.onSnapshot((snap) => {
                    snap.docChanges().forEach((change) => {
                        if (change.type === 'added') {
                            const data = change.doc.data();
                            if (!this.messages.find(m => m.id === data.id)) {
                                if (data.is_mine === undefined) data.is_mine = data.sender_id === this.myId;
                                this.messages.push(data);
                                this.lastId = Math.max(this.lastId, data.id);
                                if (!this.anyEmojiPickerOpen) {
                                    this.$nextTick(() => this.scrollToBottom());
                                }
                            }
                        } else if (change.type === 'modified') {
                            const data = change.doc.data();
                            const idx = this.messages.findIndex(m => m.id === data.id);
                            if (idx !== -1) {
                                if (data.is_mine === undefined) data.is_mine = data.sender_id === this.myId;
                                this.messages[idx] = { ...this.messages[idx], ...data };
                            }
                        }
                    });
                });
            },

            // ===== SEND MESSAGE =====
            async sendMessage() {
                const text = this.newMessage.trim();
                const fi = document.getElementById('fileInput');
                const file = fi && fi.files && fi.files.length > 0 ? fi.files[0] : null;

                if ((!text && !file) || this.sending) return;
                this.sending = true;
                this.picker = null;
                this.setMyTyping(false);

                try {
                    const fd = new FormData();
                    fd.append('_token', this.csrfToken);
                    fd.append('message', text);
                    if (this.replyTo) fd.append('reply_to_id', this.replyTo.id);
                    if (file) fd.append('attachment', file);

                    const res = await fetch(`/messages/${this.otherId}`, {
                        method: 'POST',
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },
                        body: fd,
                    });

                    if (!res.ok) return;

                    const data = await res.json();
                    if (data.success) {
                        if (data.message && !this.messages.find(m => m.id === data.message.id)) {
                            data.message.is_mine = true;
                            this.messages.push(data.message);
                            this.lastId = Math.max(this.lastId, data.message.id);
                            this.$nextTick(() => this.scrollToBottom());
                        }

                        this.newMessage = '';
                        this.replyTo = null;
                        this.pendingAttachment = null;
                        if (fi) fi.value = '';

                        // Reset textarea height after sending
                        const el = this.$refs.input;
                        if (el) el.style.height = '44px';
                    }
                } catch (e) { /* silent */ }
                finally { this.sending = false; }
            },

            async markAsRead() {
                try {
                    await fetch(`/messages/${this.otherId}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': this.csrfToken,
                            'Accept': 'application/json',
                        },
                    });
                } catch (e) { /* silent */ }
            },

            async pollNewMessages() {
                try {
                    const res = await fetch(`/messages/${this.otherId}/fetch?last_id=${this.lastId}`, {
                        headers: { 'Accept': 'application/json' },
                    });
                    const data = await res.json();

                    if (data.messages && data.messages.length > 0) {
                        data.messages.forEach(m => {
                            if (!this.messages.find(x => x.id === m.id)) {
                                this.messages.push(m);
                                this.lastId = Math.max(this.lastId, m.id);
                            }
                        });
                        if (!this.anyEmojiPickerOpen) {
                            this.$nextTick(() => this.scrollToBottom());
                        }
                    }
                } catch (e) { /* silent */ }
            },

            async toggleReaction(messageId, emoji) {
                this.picker = null;
                try {
                    const idx = this.messages.findIndex(m => m.id === messageId);
                    if (idx !== -1) {
                        const msg = this.messages[idx];
                        if (!msg.reactions) msg.reactions = [];

                        const sameEmoji = msg.reactions.find(r => r.emoji === emoji && r.user_reacted);
                        const myPreviousReaction = msg.reactions.find(r => r.user_reacted);

                        if (sameEmoji) {
                            sameEmoji.count -= 1;
                            sameEmoji.user_reacted = false;
                            if (sameEmoji.count <= 0) {
                                msg.reactions = msg.reactions.filter(r => r.emoji !== emoji);
                            }
                        } else {
                            if (myPreviousReaction) {
                                myPreviousReaction.count -= 1;
                                myPreviousReaction.user_reacted = false;
                                if (myPreviousReaction.count <= 0) {
                                    msg.reactions = msg.reactions.filter(r => r.emoji !== myPreviousReaction.emoji);
                                }
                            }

                            const existing = msg.reactions.find(r => r.emoji === emoji);
                            if (existing) {
                                existing.count += 1;
                                existing.user_reacted = true;
                            } else {
                                msg.reactions.push({
                                    emoji: emoji,
                                    count: 1,
                                    user_reacted: true,
                                });
                            }
                        }

                        this.messages = [...this.messages];
                    }

                    const res = await fetch(`/messages/${this.otherId}/react`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ message_id: messageId, emoji: emoji }),
                    });

                    if (!res.ok) {
                        await this.reloadAllMessages();
                    }
                } catch (e) {
                    await this.reloadAllMessages();
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
                        if (!this.anyEmojiPickerOpen) {
                            this.$nextTick(() => this.scrollToBottom());
                        }
                    }
                } catch (e) { /* silent */ }
            },

            startReply(msg) {
                this.picker = null;
                this.replyTo = {
                    id: msg.id,
                    message: msg.message,
                    sender_name: msg.is_mine ? this.labels.you : this.otherName,
                };
                this.$refs.input.focus({ preventScroll: true });
            },

            cancelReply() { this.replyTo = null; },

            handleFileSelect(event) {
                const file = event.target.files[0];
                if (!file) { this.pendingAttachment = null; return; }
                if (file.size > 20 * 1024 * 1024) {
                    alert(this.labels.fileTooBig);
                    event.target.value = '';
                    this.pendingAttachment = null;
                    return;
                }
                const isImage = file.type.startsWith('image/');
                const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
                const sizeKB = (file.size / 1024).toFixed(2);
                const displaySize = file.size > 1024 * 1024 ? sizeMB + ' MB' : sizeKB + ' KB';
                const att = { name: file.name, size: displaySize, isImage, preview: null };
                if (isImage) {
                    const r = new FileReader();
                    r.onload = (e) => { this.pendingAttachment = { ...att, preview: e.target.result }; };
                    r.readAsDataURL(file);
                } else {
                    this.pendingAttachment = att;
                }
            },

            clearAttachment() {
                this.pendingAttachment = null;
                const fi = document.getElementById('fileInput');
                if (fi) fi.value = '';
            },

            onInput() {
                const el = this.$refs.input;
                if (el) {
                    el.style.height = 'auto';
                    el.style.height = Math.min(el.scrollHeight, 128) + 'px';
                }
                this.setMyTyping(true);
                clearTimeout(this.typingDebounce);
                this.typingDebounce = setTimeout(() => this.setMyTyping(false), 2000);
            },

            scrollToBottom() {
                const el = this.$refs.thread;
                if (el) el.scrollTop = el.scrollHeight;
            },

            formatLastSeen(ts) {
                const t = window.chatTranslations || {};
                if (!ts) return t.recently || 'recently';
                const sec = Math.floor((Date.now() - ts) / 1000);
                if (sec < 60) return t.justNow || 'just now';
                const min = Math.floor(sec / 60);
                if (min < 60) return (t.minutesAgo || ':countm ago').replace(':count', min);
                const hr = Math.floor(min / 60);
                if (hr < 24) return (t.hoursAgo || ':counth ago').replace(':count', hr);
                return (t.daysAgo || ':countd ago').replace(':count', Math.floor(hr / 24));
            },
        };
    };
    </script>
    @endpush
</x-app-layout>
