<x-app-layout>
    @php
        $otherFullName = trim($otherUser->f_name . ' ' . $otherUser->l_name) ?: 'User';
        $otherInitial = strtoupper(substr($otherUser->f_name ?? 'U', 0, 1));
        $myInitial = strtoupper(substr(auth()->user()->f_name ?? 'U', 0, 1));
        $lastMessageId = $messages->last()->id ?? 0;

        // Translation helper: uses the lang key when it exists, otherwise falls back to the English text.
        // Placeholders use the Laravel style (:name, :max, ...).
        $tr = function (string $key, string $fallback, array $replace = []) {
            if (\Illuminate\Support\Facades\Lang::has($key)) {
                return __($key, $replace);
            }
            $pairs = [];
            foreach ($replace as $k => $v) {
                $pairs[':' . $k] = $v;
            }
            return strtr($fallback, $pairs);
        };

        $greetSub  = $tr('messages.msg_greet_sub', "You're now connected on PetNanny");
        $greetBody = $tr('messages.msg_greet_body', "Welcome to PetNanny! Chat here to talk about bookings, schedules and your pet's needs.");
        $waveLabel = $tr('messages.msg_wave', 'Wave to say hi');
        $waveHint  = $tr('messages.msg_wave_hint', 'Send a wave to start the conversation');
        $cameraLabel = $tr('messages.msg_camera', 'Camera');
        $cameraHint  = $tr('messages.msg_camera_hint', 'Take a photo');

        // Everything is passed through Js::from so apostrophes / quotes can never break x-data
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
            'maxFiles'        => 10,   // max attachments per send
            'maxFileMb'       => 10,   // Cloudinary free plan limit per file
            'labels'          => [
                'you'         => __('messages.msg_you'),
                'replyingTo'  => __('messages.msg_replying_to'),
                'fileTooBig'  => __('messages.msg_file_too_large'),
                'sending'     => __('messages.msg_sending'),
                'send'        => __('messages.msg_send'),
                'tooMany'     => $tr('messages.msg_too_many_files', 'You can attach up to :max files at a time.', ['max' => 10]),
                'networkError'=> $tr('messages.msg_network_error', 'Network error. Please check your connection and try again.'),
                'sendFailed'  => $tr('messages.msg_send_failed', 'The message could not be sent.'),
                'serverLimit' => $tr('messages.msg_server_limit', 'The file is larger than the server allows. Try a smaller photo.'),
                'progress'    => $tr('messages.msg_sending_progress', 'Sending :done of :total...'),
                'recently'    => $tr('messages.msg_recently', 'recently'),
                'justNow'     => $tr('messages.msg_just_now', 'just now'),
                'minutesAgo'  => $tr('messages.msg_minutes_ago', ':countm ago'),
                'hoursAgo'    => $tr('messages.msg_hours_ago', ':counth ago'),
                'daysAgo'     => $tr('messages.msg_days_ago', ':countd ago'),
            ],
        ];
    @endphp

    <style>
        /* Waving hand animation for the greeting */
        .pn-wave { display: inline-block; transform-origin: 70% 70%; animation: pn-wave 2.2s ease-in-out infinite; }
        @@keyframes pn-wave {
            0%, 60%, 100% { transform: rotate(0deg); }
            10%, 30%, 50% { transform: rotate(14deg); }
            20%, 40%      { transform: rotate(-8deg); }
        }
        @@media (prefers-reduced-motion: reduce) {
            .pn-wave { animation: none; }
        }
    </style>

    {{--
        ROOT: height is set via JS (fitViewport) = visible screen (including keyboard) - top of root.
        The height classes below are only a fallback before JS loads.
        Note: no x-init here, Alpine calls init() automatically.
    --}}
    <div x-ref="root"
         class="bg-white dark:bg-neutral-950 antialiased text-neutral-800 dark:text-neutral-200 h-[calc(100dvh-110px)] sm:h-[calc(100vh-140px)] lg:h-[calc(100vh-150px)] overflow-hidden"
         x-data="chatApp({{ \Illuminate\Support\Js::from($chatConfig) }})">

        {{-- Mobile: no bottom padding so the input sits flush above the phone navigation. Desktop keeps its padding. --}}
        <div class="w-full sm:max-w-4xl mx-auto px-2 sm:px-16 lg:px-24 h-full pt-1.5 pb-0 sm:py-6">

            {{-- CARD (relative = where the emoji picker anchors) --}}
            <div x-ref="card"
                 class="relative bg-white dark:bg-neutral-900 rounded-t-2xl sm:rounded-3xl shadow-sm sm:shadow-xl border border-b-0 sm:border-b border-gray-200/60 dark:border-neutral-800 overflow-hidden flex flex-col h-full">

                <!-- FIXED CHAT HEADER (<- + user icon + name + status) -->
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

                <!-- MESSAGES THREAD (only this scrolls; locked while the emoji picker is open) -->
                <div x-ref="thread"
                     @scroll.passive="onThreadScroll()"
                     :class="anyEmojiPickerOpen ? 'overflow-hidden touch-none' : 'overflow-y-auto overflow-x-hidden'"
                     class="flex-1 p-3 sm:p-6 space-y-3 sm:space-y-4 bg-neutral-50/50 dark:bg-neutral-950/30 min-h-0 overscroll-contain">

                    <!-- GREETING HEADER: both profiles + welcome text. Always at the very top of the conversation. -->
                    <div class="flex flex-col items-center text-center pt-4 pb-6 px-4 select-none">
                        <div class="flex items-center -space-x-4 mb-3">
                            <!-- Current user -->
                            @if(auth()->user()->profile_photo)
                                <img src="{{ auth()->user()->profile_photo_url }}"
                                     alt="{{ auth()->user()->f_name }}"
                                     class="w-16 h-16 sm:w-20 sm:h-20 rounded-full object-cover border-4 border-white dark:border-neutral-900 shadow-md">
                            @else
                                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-primary flex items-center justify-center text-white text-xl sm:text-2xl font-black border-4 border-white dark:border-neutral-900 shadow-md">
                                    {{ $myInitial }}
                                </div>
                            @endif

                            <!-- Other user -->
                            @if($otherUser->profile_photo)
                                <img src="{{ $otherUser->profile_photo_url }}"
                                     alt="{{ $otherFullName }}"
                                     class="w-16 h-16 sm:w-20 sm:h-20 rounded-full object-cover border-4 border-white dark:border-neutral-900 shadow-md">
                            @else
                                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-primary/10 flex items-center justify-center text-primary text-xl sm:text-2xl font-black border-4 border-white dark:border-neutral-900 shadow-md">
                                    {{ $otherInitial }}
                                </div>
                            @endif
                        </div>

                        <p class="font-black text-[#1B3B36] dark:text-white text-base sm:text-lg leading-tight">{{ $otherFullName }}</p>
                        <p class="text-[11px] text-primary font-semibold mt-0.5">{{ $greetSub }}</p>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-3 max-w-xs leading-relaxed">{{ $greetBody }}</p>

                        <!-- Wave: only available while the conversation has not started -->
                        <div x-show="messages.length === 0" x-cloak class="mt-5 flex flex-col items-center">
                            <button type="button"
                                    @click="sendWave()"
                                    :disabled="sending"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-primary/10 text-primary font-bold text-sm hover:bg-primary hover:text-white shadow-sm transition disabled:opacity-50 disabled:cursor-not-allowed">
                                <span class="pn-wave text-xl leading-none">👋</span>
                                <span>{{ $waveLabel }}</span>
                            </button>
                            <p class="text-[10px] text-neutral-400 mt-2">{{ $waveHint }}</p>
                        </div>
                    </div>

                    <!-- Message loop -->
                    <template x-for="(msg, index) in messages" :key="msg.id">
                        <div class="group/message relative"
                             data-msg-row
                             @mouseenter="hoveredId = msg.id"
                             @mouseleave="hoveredId = null">

                            <!-- Reply preview -->
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

                            <!-- Message row -->
                            <div :class="msg.is_mine
                                    ? 'flex items-end gap-2 ml-auto flex-row-reverse max-w-[90%] sm:max-w-[85%]'
                                    : 'flex items-end gap-2 max-w-[90%] sm:max-w-[85%]'">

                                <!-- Avatar -->
                                <div :class="msg.is_mine
                                        ? 'w-6 h-6 sm:w-8 sm:h-8 rounded-full bg-primary flex items-center justify-center text-[10px] sm:text-xs font-bold text-white shrink-0 mb-5'
                                        : 'w-6 h-6 sm:w-8 sm:h-8 rounded-full bg-primary/10 flex items-center justify-center text-[10px] sm:text-xs font-bold text-primary shrink-0 mb-5'">
                                    <span x-text="msg.is_mine ? myInitial : otherInitial"></span>
                                </div>

                                <!-- Bubble + timestamp + reactions -->
                                <div class="min-w-0">
                                    <div class="relative inline-block max-w-full">

                                        <!-- Bubble -->
                                        <div data-bubble :class="bubbleClass(msg)">

                                            <!-- Image attachment (click to view full size) -->
                                            <template x-if="msg.attachment_url && msg.message_type === 'image'">
                                                <img :src="msg.attachment_url"
                                                     alt="Attachment"
                                                     loading="lazy"
                                                     @load="onImageLoad()"
                                                     @click="openMessageViewer(msg)"
                                                     :class="hasText(msg) ? 'mb-1.5' : ''"
                                                     class="max-w-full rounded-lg cursor-pointer hover:opacity-90 transition"
                                                     style="max-height: 240px;">
                                            </template>

                                            <!-- Document attachment -->
                                            <template x-if="msg.attachment_url && msg.message_type !== 'image'">
                                                <a :href="msg.attachment_url"
                                                   target="_blank"
                                                   :class="hasText(msg) ? 'mb-1.5' : ''"
                                                   class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-bold hover:underline">
                                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                    {{ __('messages.msg_view_document') }}
                                                </a>
                                            </template>

                                            <!-- Text (the "Attachment" placeholder is hidden) -->
                                            <p x-show="hasText(msg)"
                                               :class="textClass(msg)"
                                               x-text="msg.message"></p>
                                        </div>

                                        <!-- Reactions: overlap the bottom edge -->
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

                                <!-- Hover actions -->
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

                                    <!-- React button (the picker itself lives outside the loop) -->
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

                <!-- MESSAGE INPUT (flush at the bottom; safe-area padding for phones with a gesture bar) -->
                <div class="p-2 sm:p-4 border-t border-gray-100 dark:border-neutral-800 bg-white dark:bg-neutral-900 flex-shrink-0"
                     style="padding-bottom: max(0.5rem, env(safe-area-inset-bottom));">

                    <!-- Reply preview -->
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

                    <!-- ATTACHMENT STRIP: thumbnails above the input. Tap a photo to view it, tap the x to remove it. -->
                    <div x-show="pending.length > 0" x-cloak class="mb-2">
                        <div class="flex items-center gap-2.5 overflow-x-auto pt-2 pb-1.5 pr-2" style="scrollbar-width: none;">
                            <template x-for="item in pending" :key="item.id">
                                <div class="relative shrink-0">

                                    <!-- Photo thumbnail -->
                                    <button type="button"
                                            x-show="item.isImage"
                                            @click="openPendingViewer(item)"
                                            class="block w-14 h-14 rounded-xl overflow-hidden border border-primary/20 bg-neutral-100 dark:bg-neutral-800">
                                        <img :src="item.preview" alt="" class="w-full h-full object-cover">
                                    </button>

                                    <!-- Document tile -->
                                    <div x-show="!item.isImage"
                                         class="w-14 h-14 rounded-xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 flex flex-col items-center justify-center px-1">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span class="text-[8px] font-bold uppercase truncate w-full text-center" x-text="fileExt(item.name)"></span>
                                    </div>

                                    <!-- Busy overlay (compressing / uploading) -->
                                    <div x-show="item.status === 'processing' || item.status === 'sending'"
                                         class="absolute inset-0 rounded-xl bg-black/45 flex items-center justify-center pointer-events-none">
                                        <span class="w-5 h-5 rounded-full border-2 border-white/40 border-t-white animate-spin"></span>
                                    </div>

                                    <!-- Error ring -->
                                    <div x-show="item.status === 'error'"
                                         class="absolute inset-0 rounded-xl ring-2 ring-red-500 pointer-events-none"></div>

                                    <!-- Remove / change button -->
                                    <button type="button"
                                            @click.stop="removePending(item.id)"
                                            :disabled="item.status === 'sending'"
                                            class="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-neutral-800 dark:bg-neutral-200 text-white dark:text-neutral-900 border-2 border-white dark:border-neutral-900 flex items-center justify-center shadow disabled:opacity-40">
                                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <!-- Status line: progress or error -->
                        <p x-show="sending && sendProgress.total > 1" x-cloak
                           class="text-[11px] text-neutral-500 dark:text-neutral-400 mt-0.5"
                           x-text="progressText()"></p>
                        <p x-show="sendError" x-cloak
                           class="text-[11px] text-red-500 font-semibold mt-0.5"
                           x-text="sendError"></p>
                    </div>

                    <!-- Error line when there is no attachment strip (e.g. a text message failed) -->
                    <p x-show="sendError && pending.length === 0" x-cloak
                       class="text-[11px] text-red-500 font-semibold mb-1.5 px-1"
                       x-text="sendError"></p>

                    <form @submit.prevent="sendMessage" class="flex gap-2 sm:gap-3 items-center">
                        <!-- Plus button with options -->
                        <div class="relative">
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
                                        @click="$refs.photoInput.click(); attachOpen = false"
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
                                        @click="$refs.cameraInput.click(); attachOpen = false"
                                        class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-neutral-50 dark:hover:bg-neutral-700 transition text-left">
                                    <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-neutral-800 dark:text-white">{{ $cameraLabel }}</p>
                                        <p class="text-[10px] text-neutral-400">{{ $cameraHint }}</p>
                                    </div>
                                </button>
                                <button type="button"
                                        @click="$refs.docInput.click(); attachOpen = false"
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

                        <!-- Hidden file inputs: photos (multiple) and documents (multiple) -->
                        <input type="file" x-ref="photoInput" class="hidden"
                               accept="image/*" multiple
                               @change="handleFiles($event)">
                        <!-- Camera: opens the camera directly. Must NOT have "multiple", Android hides the camera when it is set. -->
                        <input type="file" x-ref="cameraInput" class="hidden"
                               accept="image/*" capture="environment"
                               @change="handleFiles($event)">
                        <input type="file" x-ref="docInput" class="hidden"
                               accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" multiple
                               @change="handleFiles($event)">

                        {{-- text-base (16px) on mobile so iOS does not auto-zoom on focus --}}
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
                                :disabled="sending || processingCount > 0"
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

                <!-- EMOJI PICKER BACKDROP (tap outside = close) -->
                <div x-show="picker" x-cloak
                     @click="closePicker()"
                     class="absolute inset-0 z-40"></div>

                <!-- EMOJI PICKER: one instance, vertically anchored to the reacted message, centered horizontally -->
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

        <!-- IMAGE VIEWER (used for both chat photos and photos waiting to be sent) -->
        <template x-if="lightbox">
            <div class="fixed inset-0 flex items-center justify-center bg-black/90"
                 style="z-index: 100;"
                 @click.self="closeViewer()"
                 @keydown.escape.window="closeViewer()"
                 @keydown.arrow-left.window="stepViewer(-1)"
                 @keydown.arrow-right.window="stepViewer(1)">

                <!-- Top bar: counter + close -->
                <div class="absolute top-0 inset-x-0 flex items-center justify-between px-4 py-3 pointer-events-none"
                     style="padding-top: max(0.75rem, env(safe-area-inset-top));">
                    <span class="text-xs font-bold text-white/80" x-text="(lightbox.index + 1) + ' / ' + lightbox.items.length"></span>
                    <button type="button" @click="closeViewer()"
                            class="pointer-events-auto w-10 h-10 rounded-full bg-white/15 hover:bg-white/25 text-white flex items-center justify-center transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Previous -->
                <button type="button" x-show="lightbox.items.length > 1" @click.stop="stepViewer(-1)"
                        class="absolute left-2 sm:left-6 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/15 hover:bg-white/25 text-white flex items-center justify-center transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>

                <img :src="lightbox.items[lightbox.index].url"
                     alt=""
                     class="max-w-[94vw] max-h-[82vh] object-contain rounded-lg select-none">

                <!-- Next -->
                <button type="button" x-show="lightbox.items.length > 1" @click.stop="stepViewer(1)"
                        class="absolute right-2 sm:right-6 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/15 hover:bg-white/25 text-white flex items-center justify-center transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </template>
    </div>

    @push('scripts')
    <script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-firestore-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-database-compat.js"></script>

    <script>
    const chatFirebaseConfig = {
        apiKey:            "{{ config('firebase.web.api_key') }}",
        authDomain:        "{{ config('firebase.web.auth_domain') }}",
        projectId:         "{{ config('firebase.web.project_id') }}",
        storageBucket:     "{{ config('firebase.web.storage_bucket') }}",
        messagingSenderId: "{{ config('firebase.web.messaging_sender_id') }}",
        appId:             "{{ config('firebase.web.app_id') }}",
        databaseURL:       "{{ config('firebase.web.database_url') }}",
    };

    if (!firebase.apps.length) {
        firebase.initializeApp(chatFirebaseConfig);
    }
    const db   = firebase.firestore();
    const rtdb = firebase.database();

    window.chatApp = function(config) {
        return {
            // ----- conversation state -----
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
            attachOpen: false,

            // ----- attachments -----
            pending: [],                // [{ id, file, name, size, isImage, preview, status, error }]
            sendError: '',
            sendProgress: { done: 0, total: 0 },
            lightbox: null,             // { items: [{ url, name }], index }
            maxFiles: config.maxFiles || 10,
            maxFileBytes: (config.maxFileMb || 10) * 1024 * 1024,
            _seq: 0,

            // ----- misc -----
            lastId: config.lastId || 0,
            otherId: config.otherId,
            otherName: config.otherName,
            otherInitial: config.otherInitial,
            myInitial: config.myInitial,
            myId: config.myId,
            roomId: config.roomId,
            labels: config.labels || {},
            csrfToken: config.csrfToken || '',
            stick: true,                // true while the thread is scrolled near the bottom
            typingTimeout: null,
            typingDebounce: null,
            typingRef: null,
            otherTypingRef: null,
            firestoreUnsubscribe: null,
            pollInterval: null,
            presenceInterval: null,
            _fitRaf: null,
            _fitHandler: null,
            _origViewport: null,

            get anyEmojiPickerOpen() {
                return this.picker !== null;
            },

            get processingCount() {
                return this.pending.filter(function (p) { return p.status === 'processing'; }).length;
            },

            // Alpine calls init() automatically
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

            // Alpine calls destroy() automatically when the component is removed
            destroy() {
                this.unlockPage();
                clearInterval(this.pollInterval);
                clearInterval(this.presenceInterval);
                if (this.firestoreUnsubscribe) this.firestoreUnsubscribe();
                this.pending.forEach((p) => { if (p.preview) URL.revokeObjectURL(p.preview); });
            },

            // ===== PAGE LOCK + VIEWPORT FIT =====
            // 1) The whole page does not scroll (only the thread scrolls).
            // 2) The chat height matches the ACTUAL visible area (including when the keyboard is open),
            //    so the chat header is not pushed out and the input does not float.
            lockPage() {
                document.documentElement.style.overflow = 'hidden';
                document.documentElement.style.overscrollBehavior = 'none';
                document.body.style.overflow = 'hidden';
                document.body.style.overscrollBehavior = 'none';

                // Chrome Android: resize the layout when the keyboard opens (not just the visual viewport)
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

                // Keep the page from scrolling (this is what hides the header when the keyboard opens)
                if (window.scrollY !== 0 || window.scrollX !== 0) window.scrollTo(0, 0);

                const vv = window.visualViewport;
                const visibleBottom = vv ? (vv.offsetTop + vv.height) : window.innerHeight;
                const top = root.getBoundingClientRect().top;

                root.style.height = Math.max(visibleBottom - top, 200) + 'px';

                // While the input is focused (keyboard open), keep the thread at the bottom
                if (document.activeElement === this.$refs.input) {
                    this.$nextTick(() => this.scrollToBottom());
                }
                // Close the picker on resize so it never ends up in the wrong position
                if (this.picker) this.picker = null;
            },

            onInputFocus() {
                this.picker = null;
                this.attachOpen = false;
                // Give the keyboard a moment to open, then re-fit and scroll
                setTimeout(() => this.fitViewport(), 50);
                setTimeout(() => this.fitViewport(), 300);
                this.$nextTick(() => this.scrollToBottom());
            },

            // ===== THREAD SCROLL HELPERS =====
            onThreadScroll() {
                const el = this.$refs.thread;
                if (!el) return;
                this.stick = (el.scrollHeight - el.scrollTop - el.clientHeight) < 140;
            },

            // Photos change the thread height after they load: keep the view at the bottom if we were there
            onImageLoad() {
                if (this.stick && !this.anyEmojiPickerOpen) this.scrollToBottom();
            },

            scrollToBottom() {
                const el = this.$refs.thread;
                if (el) el.scrollTop = el.scrollHeight;
                this.stick = true;
            },

            // ===== MESSAGE DISPLAY HELPERS =====
            // Attachments are stored with the placeholder text "Attachment": do not show it in the bubble
            hasText(msg) {
                if (!msg.message) return false;
                if (msg.message_type && msg.message_type !== 'text' && msg.message === 'Attachment') return false;
                return true;
            },

            // A message made only of 1-3 emoji (like the wave) is shown bigger
            isBigEmoji(msg) {
                if (!this.hasText(msg)) return false;
                try {
                    const t = String(msg.message).trim();
                    return /^(?:\p{Extended_Pictographic}|\u200d|\ufe0f|\s)+$/u.test(t) && Array.from(t.replace(/\s/g, '')).length <= 6;
                } catch (e) {
                    return false;
                }
            },

            bubbleClass(msg) {
                const imageOnly = msg.message_type === 'image' && !this.hasText(msg);
                const pad = imageOnly ? 'p-1.5' : 'px-3 py-2 sm:px-4 sm:py-3';
                return msg.is_mine
                    ? 'bg-primary text-white rounded-2xl rounded-tr-none shadow-sm ' + pad
                    : 'bg-white dark:bg-neutral-800 rounded-2xl rounded-tl-none shadow-sm border border-gray-100 dark:border-neutral-700 ' + pad;
            },

            textClass(msg) {
                const size = this.isBigEmoji(msg) ? 'text-3xl leading-none' : 'text-xs sm:text-sm leading-relaxed';
                const color = msg.is_mine ? '' : ' text-neutral-800 dark:text-white';
                return size + color + ' whitespace-pre-wrap break-words';
            },

            // ===== EMOJI PICKER (anchored to the message, centered horizontally) =====
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

                // Default: above the message. If there is no room, below it (past the reactions badge)
                let top = (b.top - card.top) - H - gap;
                if (top < minTop) top = (b.bottom - card.top) + gap + 14;

                top = Math.min(Math.max(top, minTop), maxTop);

                this.picker = { msgId: msg.id, top: Math.round(top) };
            },

            closePicker() {
                this.picker = null;
            },

            // ===== IMAGE VIEWER =====
            openViewer(items, index) {
                if (!items.length) return;
                this.picker = null;
                this.lightbox = { items: items, index: index };
            },

            closeViewer() {
                this.lightbox = null;
            },

            stepViewer(dir) {
                if (!this.lightbox) return;
                const n = this.lightbox.items.length;
                this.lightbox.index = (this.lightbox.index + dir + n) % n;
            },

            // Viewer for photos already in the conversation (all photos, starting at the tapped one)
            openMessageViewer(msg) {
                const imgs = this.messages.filter(function (m) { return m.message_type === 'image' && m.attachment_url; });
                const idx = imgs.findIndex(function (m) { return m.id === msg.id; });
                this.openViewer(imgs.map(function (m) { return { url: m.attachment_url, name: '' }; }), Math.max(idx, 0));
            },

            // Viewer for photos waiting in the attachment strip
            openPendingViewer(item) {
                const imgs = this.pending.filter(function (p) { return p.isImage && p.preview; });
                const idx = imgs.findIndex(function (p) { return p.id === item.id; });
                this.openViewer(imgs.map(function (p) { return { url: p.preview, name: p.name }; }), Math.max(idx, 0));
            },

            // ===== ATTACHMENTS =====
            fileExt(name) {
                const m = /\.([a-z0-9]+)$/i.exec(name || '');
                return m ? m[1].slice(0, 4) : 'file';
            },

            formatSize(bytes) {
                if (bytes >= 1024 * 1024) return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
                return (bytes / 1024).toFixed(0) + ' KB';
            },

            isImageFile(file) {
                return (file.type || '').toLowerCase().startsWith('image/')
                    || /\.(jpe?g|png|gif|webp|heic|heif)$/i.test(file.name || '');
            },

            // Shrinks big photos (camera photos are often 4-12 MB) to max 1600px JPEG before upload.
            // This keeps each request small (PHP / server limits) and saves Cloudinary free-plan quota.
            // If the browser cannot decode the file (e.g. HEIC), the original file is returned untouched.
            async compressImage(file) {
                const type = (file.type || '').toLowerCase();
                if (!type.startsWith('image/') || type === 'image/gif' || type === 'image/svg+xml') return file;

                const MAX_DIM = 1600;
                try {
                    let bitmap;
                    if (window.createImageBitmap) {
                        bitmap = await createImageBitmap(file);
                    } else {
                        bitmap = await new Promise(function (resolve, reject) {
                            const img = new Image();
                            const url = URL.createObjectURL(file);
                            img.onload = function () { URL.revokeObjectURL(url); resolve(img); };
                            img.onerror = function () { URL.revokeObjectURL(url); reject(new Error('decode')); };
                            img.src = url;
                        });
                    }

                    const scale = Math.min(1, MAX_DIM / Math.max(bitmap.width, bitmap.height));
                    const w = Math.max(1, Math.round(bitmap.width * scale));
                    const h = Math.max(1, Math.round(bitmap.height * scale));

                    const canvas = document.createElement('canvas');
                    canvas.width = w;
                    canvas.height = h;
                    const ctx = canvas.getContext('2d');
                    ctx.fillStyle = '#ffffff';   // JPEG has no transparency
                    ctx.fillRect(0, 0, w, h);
                    ctx.drawImage(bitmap, 0, 0, w, h);
                    if (bitmap.close) bitmap.close();

                    const blob = await new Promise(function (resolve) { canvas.toBlob(resolve, 'image/jpeg', 0.82); });
                    if (!blob) return file;
                    if (scale === 1 && blob.size >= file.size) return file;   // already small enough

                    const newName = (file.name || 'photo').replace(/\.[^.]+$/, '') + '.jpg';
                    return new File([blob], newName, { type: 'image/jpeg', lastModified: Date.now() });
                } catch (e) {
                    return file;
                }
            },

            updatePending(id, patch) {
                const item = this.pending.find(function (p) { return p.id === id; });
                if (item) Object.assign(item, patch);
            },

            // Called by both file inputs (photos and documents)
            async handleFiles(event) {
                const input = event.target;
                const files = Array.from(input.files || []);
                input.value = '';   // allows picking the same file again later
                if (!files.length) return;

                this.sendError = '';

                for (const file of files) {
                    if (this.pending.length >= this.maxFiles) {
                        this.sendError = (this.labels.tooMany || '').replace(':max', this.maxFiles);
                        break;
                    }

                    const isImage = this.isImageFile(file);
                    const id = ++this._seq;

                    this.pending.push({
                        id: id,
                        file: file,
                        name: file.name,
                        size: this.formatSize(file.size),
                        isImage: isImage,
                        preview: isImage ? URL.createObjectURL(file) : null,
                        status: isImage ? 'processing' : 'ready',
                        error: null,
                    });

                    if (isImage) {
                        // Compress in the background; the thumbnail shows a spinner meanwhile
                        this.compressImage(file).then((out) => {
                            const current = this.pending.find(function (p) { return p.id === id; });
                            if (!current) return;   // removed while compressing

                            if (out !== file) {
                                if (current.preview) URL.revokeObjectURL(current.preview);
                                current.preview = URL.createObjectURL(out);
                            }
                            current.file = out;
                            current.size = this.formatSize(out.size);
                            current.name = out.name;

                            if (out.size > this.maxFileBytes) {
                                current.status = 'error';
                                this.sendError = (this.labels.fileTooBig || '') + ' (' + out.name + ')';
                            } else {
                                current.status = 'ready';
                            }
                        });
                    } else if (file.size > this.maxFileBytes) {
                        this.updatePending(id, { status: 'error' });
                        this.sendError = (this.labels.fileTooBig || '') + ' (' + file.name + ')';
                    }
                }
            },

            removePending(id) {
                const item = this.pending.find(function (p) { return p.id === id; });
                if (item && item.preview) URL.revokeObjectURL(item.preview);
                this.pending = this.pending.filter(function (p) { return p.id !== id; });
                if (this.pending.every(function (p) { return p.status !== 'error'; })) this.sendError = '';
            },

            progressText() {
                return (this.labels.progress || '')
                    .replace(':done', Math.min(this.sendProgress.done + 1, this.sendProgress.total))
                    .replace(':total', this.sendProgress.total);
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

                this.presenceInterval = setInterval(() => {
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
                                if (!this.anyEmojiPickerOpen && this.stick) {
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

            // ===== SENDING =====
            // One request = one message (text OR one attachment). Sending files one by one keeps every
            // request small (works with low PHP/server upload limits) and lets the UI show progress.
            // Resolves to { ok: true, message } or { ok: false, error }.
            async postMessage(opts) {
                const fd = new FormData();
                fd.append('_token', this.csrfToken);
                fd.append('message', opts.text || '');
                if (opts.replyId) fd.append('reply_to_id', opts.replyId);
                if (opts.file) fd.append('attachment', opts.file, opts.file.name);
                if (opts.silent) fd.append('silent', '1');   // no notification for this one

                let res;
                try {
                    res = await fetch(`/messages/${this.otherId}`, {
                        method: 'POST',
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },
                        body: fd,
                    });
                } catch (e) {
                    console.error('Send message error:', e);
                    return { ok: false, error: this.labels.networkError };
                }

                let data = null;
                try { data = await res.json(); } catch (_) { data = null; }

                if (!res.ok) {
                    const firstError = data && data.errors ? Object.values(data.errors).flat()[0] : null;
                    let error = firstError || (data && data.message) || null;
                    if (!error && res.status === 413) error = this.labels.serverLimit;
                    if (!error) error = this.labels.sendFailed + ' (HTTP ' + res.status + ')';
                    return { ok: false, error: error };
                }

                if (!data || !data.success) {
                    return { ok: false, error: (data && data.message) || this.labels.sendFailed };
                }

                return { ok: true, message: data.message };
            },

            // Adds a sent message to the thread (the Firestore listener may already have added it)
            appendSent(message) {
                if (!message || this.messages.find(m => m.id === message.id)) return;
                message.is_mine = true;
                this.messages.push(message);
                this.lastId = Math.max(this.lastId, message.id);
                this.$nextTick(() => this.scrollToBottom());
            },

            async sendMessage() {
                if (this.sending || this.processingCount > 0) return;

                const text = this.newMessage.trim();
                const items = this.pending.slice();

                if (!text && items.length === 0) return;

                this.sending = true;
                this.sendError = '';
                this.picker = null;
                this.attachOpen = false;
                this.setMyTyping(false);

                // Photos / documents first, then the text as the last message
                const jobs = items.map(function (it) { return { item: it }; });
                if (text) jobs.push({ text: text });

                this.sendProgress = { done: 0, total: jobs.length };
                let replyId = this.replyTo ? this.replyTo.id : null;
                let allSent = true;

                for (let i = 0; i < jobs.length; i++) {
                    const job = jobs[i];
                    const isLast = i === jobs.length - 1;

                    if (job.item) this.updatePending(job.item.id, { status: 'sending' });

                    const result = await this.postMessage({
                        text: job.text || '',
                        file: job.item ? job.item.file : null,
                        replyId: replyId,
                        silent: !isLast,   // only the last message notifies the other user
                    });

                    if (!result.ok) {
                        allSent = false;
                        this.sendError = result.error;
                        if (job.item) this.updatePending(job.item.id, { status: 'error' });
                        break;
                    }

                    replyId = null;   // only the first message is a reply
                    this.appendSent(result.message);
                    this.sendProgress.done = i + 1;

                    if (job.item) {
                        this.removePending(job.item.id);
                    } else {
                        this.newMessage = '';
                        const el = this.$refs.input;
                        if (el) el.style.height = '44px';
                    }
                }

                if (allSent) this.replyTo = null;
                this.sending = false;
            },

            // One-time wave: only offered while the conversation is empty
            async sendWave() {
                if (this.sending || this.messages.length > 0) return;
                this.sending = true;
                this.sendError = '';

                const result = await this.postMessage({ text: '👋' });
                if (result.ok) {
                    this.appendSent(result.message);
                } else {
                    this.sendError = result.error;
                }
                this.sending = false;
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
                        if (!this.anyEmojiPickerOpen && this.stick) {
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
                    message: this.hasText(msg) ? msg.message : (msg.message_type === 'image' ? '📷' : '📎'),
                    sender_name: msg.is_mine ? this.labels.you : this.otherName,
                };
                this.$refs.input.focus({ preventScroll: true });
            },

            cancelReply() { this.replyTo = null; },

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

            formatLastSeen(ts) {
                const t = this.labels || {};
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
