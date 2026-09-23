<x-app-layout>
    <!-- ========================================== -->
    <!-- HEADER SLOT                               -->
    <!-- ========================================== -->
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div class="flex items-center gap-3">
               
                <a href="javascript:void(0)" 
                onclick="history.back()" 
                class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition"
                aria-label="Go back">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                
                <div>
                    <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                        {{ __('messages.notif_title') }}
                    </h1>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.notif_subtitle') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span x-data 
                    x-show="$store.notif.unread > 0"
                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-primary/10 text-primary">
                    <span x-text="$store.notif.unread"></span>&nbsp;{{ __('messages.notif_unread') }}
                </span>
            </div>
        </div>
    </x-slot>
    <!-- ========================================== -->
    <!-- MAIN CONTENT                              -->
    <!-- ========================================== -->
    <div class="bg-white dark:bg-neutral-950 antialiased text-neutral-800 dark:text-neutral-200 
                h-[calc(100vh-140px)] sm:h-[calc(100vh-170px)] lg:h-[calc(100vh-180px)] flex flex-col"
         x-data="notificationsPage()"
         x-init="init()">

        <div class="w-full sm:max-w-4xl mx-auto px-2 sm:px-16 lg:px-24 h-full py-2 sm:py-6 flex flex-col">

            <!-- ========================================== -->
            <!-- TABS & ACTIONS                            -->
            <!-- ========================================== -->
            <div class="flex items-center justify-between mb-4 flex-shrink-0 gap-2">
                <div class="flex items-center gap-2 bg-neutral-100 dark:bg-neutral-800 p-1 rounded-xl">
                    <button @click="filter = 'all'"
                            :class="filter === 'all' 
                                ? 'bg-primary text-white shadow-sm' 
                                : 'text-neutral-600 dark:text-neutral-400 hover:bg-white/50 dark:hover:bg-neutral-700'"
                            class="px-3 sm:px-4 py-1.5 text-[10px] sm:text-xs font-bold rounded-lg transition">
                        {{ __('messages.notif_tab_all') }}
                    </button>
                    <button @click="filter = 'unread'"
                            :class="filter === 'unread' 
                                ? 'bg-primary text-white shadow-sm' 
                                : 'text-neutral-600 dark:text-neutral-400 hover:bg-white/50 dark:hover:bg-neutral-700'"
                            class="px-3 sm:px-4 py-1.5 text-[10px] sm:text-xs font-bold rounded-lg transition">
                        {{ __('messages.notif_tab_unread') }}
                    </button>
                </div>

                <button type="button" 
                        @click="markAllRead()"
                        x-show="$store.notif.unread > 0"
                        class="text-xs text-primary font-semibold hover:underline shrink-0">
                    {{ __('messages.notif_mark_all_read') }}
                </button>
            </div>

            <!-- ========================================== -->
            <!-- NOTIFICATION LIST                         -->
            <!-- ========================================== -->
            <div class="flex-1 overflow-y-auto min-h-0" x-ref="list">
                <div class="bg-white dark:bg-neutral-900 rounded-2xl sm:rounded-3xl shadow-sm sm:shadow-xl border border-gray-200/60 dark:border-neutral-800 divide-y divide-gray-100 dark:divide-neutral-800">

                    <!-- ========================================== -->
                    <!-- EMPTY STATE                               -->
                    <!-- ========================================== -->
                    <template x-if="filteredNotifications.length === 0">
                        <div class="flex flex-col items-center justify-center py-16 text-center">
                            <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mb-3">
                                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-neutral-600 dark:text-neutral-400">{{ __('messages.notif_empty_title') }}</p>
                            <p class="text-xs text-neutral-400 mt-1" 
                               x-text="filter === 'unread' ? @js(__('messages.notif_empty_unread')) : @js(__('messages.notif_empty_all'))"></p>
                        </div>
                    </template>

                    <!-- ========================================== -->
                    <!-- NOTIFICATION LOOP                         -->
                    <!-- ========================================== -->
                    <template x-for="notif in filteredNotifications" :key="notif.id">
                        <div class="relative group/notif" 
                             x-data="{ menuOpen: false }"
                             :class="notif.is_unread 
                                 ? 'bg-primary/[0.03] dark:bg-primary/[0.05]' 
                                 : ''">

                            <!-- ============================= -->
                            <!-- MAIN CLICKABLE ROW            -->
                            <!-- ============================= -->
                            <button type="button"
                                    @click="openModal(notif)"
                                    class="w-full flex items-start gap-3 sm:gap-4 p-4 sm:p-5 pr-14 sm:pr-16 
                                           hover:bg-primary/5 dark:hover:bg-neutral-800/50 
                                           transition text-left">

                                <!-- Icon (colored by type) -->
                                <div :class="notif.color.bg + ' ' + notif.color.text"
                                     class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              :d="getIconPath(notif.type)"/>
                                    </svg>
                                </div>

                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-baseline justify-between gap-2">
                                        <p class="text-sm text-neutral-800 dark:text-white">
                                            <span class="font-bold" x-text="notif.title"></span>
                                        </p>
                                        <span class="text-[10px] text-neutral-400 whitespace-nowrap shrink-0" 
                                              x-text="notif.time_ago"></span>
                                    </div>

                                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5 break-words line-clamp-2" 
                                       x-text="notif.message"></p>

                                    <div class="flex items-center gap-2 mt-2">
                                        <!-- Unread dot -->
                                        <span x-show="notif.is_unread" 
                                              class="inline-flex w-2 h-2 rounded-full bg-primary" 
                                              title="Unread"></span>

                                        <!-- View link -->
                                        <span class="text-xs text-primary font-semibold">
                                            {{ __('messages.notif_view_details') }}
                                        </span>
                                    </div>
                                </div>
                            </button>

                            <!-- ============================= -->
                            <!-- THREE-DOTS BUTTON (hover)     -->
                            <!-- ============================= -->
                            <button type="button"
                                    @click.prevent.stop="menuOpen = !menuOpen"
                                    :class="menuOpen ? 'opacity-100' : 'opacity-0 group-hover/notif:opacity-100'"
                                    class="absolute top-1/2 -translate-y-1/2 right-3 w-8 h-8 rounded-full 
                                           flex items-center justify-center
                                           bg-white dark:bg-neutral-800 shadow-md border border-gray-200 dark:border-neutral-700
                                           text-neutral-500 hover:text-primary hover:border-primary 
                                           transition-all duration-150">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                </svg>
                            </button>

                            <!-- ============================= -->
                            <!-- DROPDOWN MENU                 -->
                            <!-- ============================= -->
                            <div x-show="menuOpen" 
                                 @click.away="menuOpen = false"
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 class="absolute right-3 top-16 z-30 w-56
                                        bg-white dark:bg-neutral-800 rounded-2xl shadow-2xl 
                                        border border-gray-200 dark:border-neutral-700 py-1.5">

                                <!-- Mark as read (if unread) -->
                                <button type="button"
                                        x-show="notif.is_unread"
                                        @click.stop="menuOpen = false; markRead(notif.id).then(() => notif.is_unread = false)"
                                        class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-neutral-50 dark:hover:bg-neutral-700/50 transition text-left">
                                    <svg class="w-4 h-4 text-neutral-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-sm font-bold text-neutral-700 dark:text-neutral-200">{{ __('messages.notif_menu_mark_read') }}</span>
                                </button>

                                <!-- Mark as unread (if read) -->
                                <button type="button"
                                        x-show="!notif.is_unread"
                                        @click.stop="menuOpen = false; markUnread(notif.id).then(() => notif.is_unread = true)"
                                        class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-neutral-50 dark:hover:bg-neutral-700/50 transition text-left">
                                    <svg class="w-4 h-4 text-neutral-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-sm font-bold text-neutral-700 dark:text-neutral-200">{{ __('messages.notif_menu_mark_unread') }}</span>
                                </button>

                                <!-- Open notification (view modal) -->
                                <button type="button"
                                        @click.stop="menuOpen = false; openModal(notif)"
                                        class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-neutral-50 dark:hover:bg-neutral-700/50 transition text-left">
                                    <svg class="w-4 h-4 text-neutral-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <span class="text-sm font-bold text-neutral-700 dark:text-neutral-200">{{ __('messages.notif_menu_open') }}</span>
                                </button>

                                <hr class="my-1 border-neutral-100 dark:border-neutral-700">

                                <!-- Delete -->
                                <button type="button"
                                        @click.stop="if (confirm(@js(__('messages.notif_confirm_delete')))) { menuOpen = false; deleteNotif(notif.id); }"
                                        class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-red-50 dark:hover:bg-red-950/20 transition text-left">
                                    <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    <span class="text-sm font-bold text-red-500">{{ __('messages.notif_menu_delete') }}</span>
                                </button>
                            </div>
                        </div>
                    </template>

                </div>
            </div>

        </div>

        <!-- ========================================== -->
        <!-- NOTIFICATION DETAILS MODAL                -->
        <!-- ========================================== -->
        <div x-show="modalOpen" 
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:px-4 sm:py-8"
             @keydown.escape.window="modalOpen = false">

            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm"
                 @click="modalOpen = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"></div>

            <!-- Modal Content -->
            <div class="relative bg-white dark:bg-neutral-900 rounded-2xl sm:rounded-3xl shadow-2xl 
                        border border-gray-200 dark:border-neutral-800 
                        max-w-2xl w-full max-h-[92vh] overflow-y-auto"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">

                <template x-if="selectedNotif">
                    <div>
                        <!-- ============================= -->
                        <!-- MODAL HEADER                  -->
                        <!-- ============================= -->
                        <div class="flex items-start gap-3 sm:gap-4 p-4 sm:p-6 border-b border-gray-100 dark:border-neutral-800">
                            <!-- Icon -->
                            <div :class="selectedNotif.color.bg + ' ' + selectedNotif.color.text"
                                 class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          :d="getIconPath(selectedNotif.type)"/>
                                </svg>
                            </div>

                            <!-- Title + time -->
                            <div class="flex-1 min-w-0">
                                <h2 class="text-lg sm:text-xl font-black text-[#1B3B36] dark:text-white" 
                                    x-text="selectedNotif.title"></h2>
                                <p class="text-xs text-neutral-400 mt-1" 
                                   x-text="selectedNotif.time_ago"></p>
                            </div>

                            <!-- Close button -->
                            <button @click="modalOpen = false"
                                    class="p-2 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition shrink-0">
                                <svg class="w-5 h-5 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <!-- ============================= -->
                        <!-- MODAL BODY                    -->
                        <!-- ============================= -->
                        <div class="p-4 sm:p-6 space-y-4">

                            <!-- Actor (if naay actor) -->
                            <template x-if="selectedNotif.actor">
                                <div class="flex items-center gap-3 p-3 rounded-xl bg-neutral-50 dark:bg-neutral-800/50 border border-gray-100 dark:border-neutral-700">
                                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-sm shrink-0">
                                        <span x-text="selectedNotif.actor.initial"></span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.notif_from') }}</p>
                                        <p class="text-sm font-bold text-[#1B3B36] dark:text-white truncate" 
                                           x-text="selectedNotif.actor.name"></p>
                                    </div>
                                </div>
                            </template>

                            <!-- Message body -->
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-neutral-400 mb-2">{{ __('messages.notif_message') }}</p>
                                <p class="text-sm sm:text-base text-neutral-700 dark:text-neutral-200 leading-relaxed whitespace-pre-wrap break-words"
                                   x-text="selectedNotif.message"></p>
                            </div>

                            <!-- Type badge -->
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-neutral-400 mb-2">{{ __('messages.notif_type') }}</p>
                                <span :class="selectedNotif.color.bg + ' ' + selectedNotif.color.text"
                                      class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold capitalize">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              :d="getIconPath(selectedNotif.type)"/>
                                    </svg>
                                    <span x-text="selectedNotif.type.replace(/_/g, ' ')"></span>
                                </span>
                            </div>

                        </div>

                        <!-- ============================= -->
                        <!-- MODAL FOOTER / ACTIONS        -->
                        <!-- ============================= -->
                        <div class="p-4 sm:p-6 pt-2 flex flex-col-reverse sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-3 border-t border-gray-100 dark:border-neutral-800">

                            <!-- Delete button -->
                            <button type="button"
                                    @click="if (confirm(@js(__('messages.notif_confirm_delete')))) { deleteNotif(selectedNotif.id); modalOpen = false; }"
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 border-2 border-red-500 text-red-500 
                                           hover:bg-red-50 dark:hover:bg-red-950/20 font-bold text-sm rounded-xl transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                {{ __('messages.notif_delete') }}
                            </button>

                            <!-- Action buttons -->
                            <div class="flex flex-col-reverse sm:flex-row gap-2 sm:gap-3 w-full sm:w-auto">
                                <button type="button"
                                        @click="modalOpen = false"
                                        class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 
                                               border-2 border-gray-300 dark:border-neutral-700 
                                               text-neutral-600 dark:text-neutral-300 font-bold text-sm rounded-xl 
                                               hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                                    {{ __('messages.notif_close') }}
                                </button>

                                <!-- Redirect if action_url exists -->
                                <template x-if="selectedNotif.action_url">
                                    <a :href="selectedNotif.action_url"
                                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 
                                              bg-primary hover:bg-primary-600 text-white font-bold text-sm rounded-xl 
                                              shadow-md hover:shadow-lg transition">
                                        <span>{{ __('messages.notif_view_details_full') }}</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                        </svg>
                                    </a>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function notificationsPage() {
        return {
            // ==========================================
            // STATE
            // ==========================================
            notifications: @json($notificationsJson ?? []),
            filter: 'all',
            lastId: {{ $lastMessageId ?? 0 }},
            pollTimer: null,
            modalOpen: false,
            selectedNotif: null,

            // ==========================================
            // COMPUTED
            // ==========================================
            get filteredNotifications() {
                if (this.filter === 'unread') {
                    return this.notifications.filter(n => n.is_unread);
                }
                return this.notifications;
            },

            // ==========================================
            // INIT
            // ==========================================
            init() {
                this.$store.notif.unread = this.notifications.filter(n => n.is_unread).length;
                this.pollTimer = setInterval(() => this.poll(), 10000);

                window.addEventListener('beforeunload', () => {
                    if (this.pollTimer) clearInterval(this.pollTimer);
                });
            },

            // ==========================================
            // MODAL
            // ==========================================
            openModal(notif) {
                this.selectedNotif = notif;
                this.modalOpen = true;

                // Auto-mark as read
                if (notif.is_unread) {
                    this.markRead(notif.id).then(() => {
                        notif.is_unread = false;
                        this.syncBadge();
                    });
                }
            },

            // ==========================================
            // POLL
            // ==========================================
            async poll() {
                try {
                    const res = await fetch(`/notifications/poll?last_id=${this.lastId}`, {
                        headers: { 
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!res.ok) return;
                    const data = await res.json();

                    if (data.success) {
                        if (data.notifications && data.notifications.length > 0) {
                            data.notifications.forEach(n => {
                                if (!this.notifications.find(x => x.id === n.id)) {
                                    this.notifications.unshift(n);
                                    this.lastId = Math.max(this.lastId, n.id);
                                }
                            });
                        }
                        this.$store.notif.unread = data.unread_count ?? 0;
                    }
                } catch (e) {}
            },

            // ==========================================
            // MARK READ
            // ==========================================
            async markRead(id) {
                try {
                    const res = await fetch(`/notifications/${id}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.$store.notif.unread = data.unread_count ?? 0;
                    }
                } catch (e) {
                    console.error('markRead failed:', e);
                }
            },

            // ==========================================
            // MARK UNREAD
            // ==========================================
            async markUnread(id) {
                try {
                    const res = await fetch(`/notifications/${id}/unread`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.$store.notif.unread = data.unread_count ?? 0;
                    }
                } catch (e) {
                    console.error('markUnread failed:', e);
                }
            },

            // ==========================================
            // MARK ALL READ
            // ==========================================
            async markAllRead() {
                try {
                    const res = await fetch('/notifications/mark-all-read', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.notifications.forEach(n => n.is_unread = false);
                        this.$store.notif.unread = 0;
                    }
                } catch (e) {}
            },

            // ==========================================
            // DELETE
            // ==========================================
            async deleteNotif(id) {
                try {
                    const res = await fetch(`/notifications/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.notifications = this.notifications.filter(n => n.id !== id);
                        this.$store.notif.unread = data.unread_count ?? 0;
                    }
                } catch (e) {}
            },

            // ==========================================
            // SYNC BADGE
            // ==========================================
            syncBadge() {
                this.$store.notif.unread = this.notifications.filter(n => n.is_unread).length;
            },

            // ==========================================
            // ICON PATHS
            // ==========================================
            getIconPath(type) {
                const icons = {
                    // ==========================================
                    // BOOKINGS
                    // ==========================================
                    booking_created:     'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                    booking_confirmed:   'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                    booking_cancelled:   'M6 18L18 6M6 6l12 12',
                    booking_completed:   'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',

                    // ==========================================
                    // MESSAGES
                    // ==========================================
                    new_message:         'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',

                    // ==========================================
                    // REVIEWS & COMMENTS
                    // ==========================================
                    review_received:     'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
                    comment_received:    'M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z',

                    // ==========================================
                    // SITTER APPLICATIONS
                    // ==========================================
                    sitter_applied:      'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z',
                    sitter_approved:     'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                    sitter_rejected:     'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',

                    // ==========================================
                    // ID VERIFICATION
                    // ==========================================
                    id_verified:         'M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z',
                    id_rejected:         'M6 18L18 6M6 6l12 12',

                    // ==========================================
                    // COMPLAINTS
                    // ==========================================
                    complaint_filed:     'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
                    complaint_resolved:  'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',

                    // ==========================================
                    // SYSTEM (default fallback)
                    // ==========================================
                    system:              'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
                };

                return icons[type] || icons.system;
            },
        }
    }

    document.addEventListener('alpine:init', () => {
        if (!Alpine.store('notif')) {
            Alpine.store('notif', {
                unread: {{ $unreadCount ?? 0 }}
            });
        }
    });
    </script>
    @endpush
</x-app-layout>