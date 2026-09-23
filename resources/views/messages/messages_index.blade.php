<x-app-layout>
    <x-slot name="header">
        <div class="sticky top-0 z-10 bg-white dark:bg-neutral-900 flex items-center gap-3">
            <a href="{{ route('find.sitter') }}" 
               class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                    {{ __('messages.mi_title') }}
                </h1>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                    {{ __('messages.mi_subtitle') }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="bg-white dark:bg-neutral-950 antialiased text-neutral-800 dark:text-neutral-200 h-[calc(100vh-140px)] sm:h-[calc(100vh-170px)] lg:h-[calc(100vh-180px)] flex flex-col"
         x-data="{ 
             tab: 'active',
             activeCount: {{ collect($conversations)->where('is_archived', false)->count() }},
             archivedCount: {{ collect($conversations)->where('is_archived', true)->count() }}
         }">
        <div class="w-full sm:max-w-4xl mx-auto px-2 sm:px-16 lg:px-24 h-full py-2 sm:py-6 flex flex-col">

            <!-- SEARCH BAR + TABS -->
            <div class="flex-shrink-0 pb-4 space-y-4">

                <!-- Search Bar -->
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" 
                           placeholder="{{ __('messages.mi_search_ph') }}" 
                           class="w-full pl-9 pr-4 py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-900 text-sm text-neutral-800 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                </div>

                <!-- Active / Archived Icons (Top Right) -->
                <div class="flex items-center justify-end gap-4">
                    
                    <!-- Active Tab Icon -->
                    <button type="button"
                            @click="tab = 'active'"
                            :class="tab === 'active' ? 'text-primary' : 'text-neutral-400 dark:text-neutral-500 hover:text-primary'"
                            class="relative transition"
                            title="{{ __('messages.mi_active_conversations') }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <span x-show="activeCount > 0"
                              class="absolute -top-0.5 -right-0.5 w-2 h-2 rounded-full bg-primary border border-white dark:border-neutral-900"></span>
                    </button>

                    <!-- Archived Tab Icon -->
                    <button type="button"
                            @click="tab = 'archived'"
                            :class="tab === 'archived' ? 'text-primary' : 'text-neutral-400 dark:text-neutral-500 hover:text-primary'"
                            class="relative transition"
                            title="{{ __('messages.mi_archived_conversations') }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                        </svg>
                        <span x-show="archivedCount > 0"
                              class="absolute -top-0.5 -right-0.5 w-2 h-2 rounded-full bg-primary border border-white dark:border-neutral-900"></span>
                    </button>
                </div>
            </div>

            <!-- CONVERSATION LIST -->
            <div class="flex-1 overflow-y-auto">
                <div class="bg-white dark:bg-neutral-900 rounded-3xl shadow-xl border border-gray-200/60 dark:border-neutral-800 divide-y divide-gray-100 dark:divide-neutral-800">

                    @foreach($conversations as $conv)
                        @php
                            $otherUser = $conv['other_user'];
                            $lastMsg = $conv['last_message'];
                            $unread = $conv['unread_count'];
                            $isArchived = $conv['is_archived'] ?? false;

                            $otherFullName = trim($otherUser->f_name . ' ' . $otherUser->l_name) ?: 'User';
                            $otherInitial = strtoupper(substr($otherUser->f_name ?? 'U', 0, 1));
                            $isSitter = $otherUser->is_sitter;
                            $timeAgo = $lastMsg->created_at->diffForHumans(null, true);
                            $preview = \Illuminate\Support\Str::limit($lastMsg->message, 50);
                            $isMyMessage = $lastMsg->sender_id === auth()->id();
                        @endphp

                        <!-- CONVERSATION ITEM -->
                        <div class="relative group/conv" 
                             x-data="{ menuOpen: false }"
                             x-show="(tab === 'active' && {{ $isArchived ? 'false' : 'true' }}) || (tab === 'archived' && {{ $isArchived ? 'true' : 'false' }})">

                            <a href="{{ route('owner.messages', ['id' => $otherUser->id]) }}" 
                               class="flex items-center gap-4 p-4 sm:p-5 pr-14 sm:pr-16 hover:bg-primary/5 dark:hover:bg-neutral-800/50 transition">

                                <!-- AVATAR -->
                                <div class="relative flex-shrink-0">
                                    @if($otherUser->profile_photo && file_exists(public_path('storage/' . $otherUser->profile_photo)))
                                        <img src="{{ asset('storage/' . $otherUser->profile_photo) }}" 
                                             alt="{{ $otherFullName }}" 
                                             class="w-12 h-12 rounded-full object-cover {{ $isArchived ? 'grayscale opacity-60' : '' }}">
                                    @else
                                        <div class="w-12 h-12 rounded-full bg-primary {{ $isArchived ? 'opacity-60' : '' }} flex items-center justify-center text-white font-black text-lg">
                                            {{ $otherInitial }}
                                        </div>
                                    @endif

                                    @if($unread > 0 && !$isArchived)
                                        <span class="absolute -top-1 -right-1 flex items-center justify-center min-w-[18px] h-[18px] px-1 bg-primary text-white text-[10px] font-bold rounded-full border-2 border-white dark:border-neutral-900">
                                            {{ $unread }}
                                        </span>
                                    @elseif(!$isArchived)
                                        <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white dark:border-neutral-900"></span>
                                    @endif
                                </div>

                                <!-- DETAILS -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-baseline justify-between gap-2">
                                        <h3 class="text-sm font-black text-[#1B3B36] dark:text-white truncate">
                                            {{ $otherFullName }}
                                        </h3>
                                        <span class="text-[10px] text-neutral-400 whitespace-nowrap">
                                            {{ $timeAgo }}
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-xs text-neutral-500 dark:text-neutral-400 truncate">
                                            @if($isMyMessage)
                                                <span class="text-neutral-400">{{ __('messages.mi_you_prefix') }}</span>
                                            @endif
                                            {{ $preview }}
                                        </span>
                                        @if($unread > 0 && !$isArchived)
                                            <span class="inline-flex w-2 h-2 rounded-full bg-primary flex-shrink-0" title="Unread"></span>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-2 mt-1">
                                        @if($isSitter)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-medium bg-accent/10 text-accent dark:bg-accent/20">
                                                {{ __('messages.mi_role_sitter') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-medium bg-secondary/30 text-neutral-600 dark:bg-neutral-800/30 dark:text-neutral-400">
                                                {{ __('messages.mi_role_owner') }}
                                            </span>
                                        @endif

                                        @if($isArchived)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-medium bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                                </svg>
                                                {{ __('messages.mi_archived_badge') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </a>

                            <!-- THREE-DOTS BUTTON -->
                            <button type="button"
                                    @click.prevent.stop="menuOpen = !menuOpen"
                                    :class="menuOpen ? 'opacity-100' : 'opacity-0 group-hover/conv:opacity-100'"
                                    class="absolute top-1/2 -translate-y-1/2 right-3 w-8 h-8 rounded-full 
                                           flex items-center justify-center
                                           bg-white dark:bg-neutral-800 shadow-md border border-gray-200 dark:border-neutral-700
                                           text-neutral-500 hover:text-primary hover:border-primary 
                                           transition-all duration-150">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                </svg>
                            </button>

                            <!-- DROPDOWN MENU -->
                            <div x-show="menuOpen" 
                                 @click.away="menuOpen = false"
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 class="absolute right-3 top-16 z-30 w-56
                                        bg-white dark:bg-neutral-800 rounded-2xl shadow-2xl 
                                        border border-gray-200 dark:border-neutral-700 py-1.5">

                                @if(!$isArchived)
                                    <button type="button"
                                            @click.stop="menuOpen = false; alert(@js(__('messages.mi_feature_coming_soon', ['feature' => __('messages.mi_mark_read')])))"
                                            class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-neutral-50 dark:hover:bg-neutral-700/50 transition text-left">
                                        <svg class="w-4 h-4 text-neutral-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="text-sm font-bold text-neutral-700 dark:text-neutral-200">{{ __('messages.mi_mark_read') }}</span>
                                    </button>
                                @endif

                                <a href="{{ route('owner.messages', ['id' => $otherUser->id]) }}"
                                   @click.stop="menuOpen = false"
                                   class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-neutral-50 dark:hover:bg-neutral-700/50 transition text-left">
                                    <svg class="w-4 h-4 text-neutral-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                    <span class="text-sm font-bold text-neutral-700 dark:text-neutral-200">{{ __('messages.mi_open_message') }}</span>
                                </a>

                                <hr class="my-1 border-neutral-100 dark:border-neutral-700">

                                <button type="button"
                                        @click.stop="menuOpen = false; alert(@js(__('messages.mi_feature_coming_soon', ['feature' => $isArchived ? __('messages.mi_unarchive_chat') : __('messages.mi_archive_chat')])))"
                                        class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-neutral-50 dark:hover:bg-neutral-700/50 transition text-left">
                                    <svg class="w-4 h-4 text-neutral-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                    </svg>
                                    <span class="text-sm font-bold text-neutral-700 dark:text-neutral-200">
                                        {{ $isArchived ? __('messages.mi_unarchive_chat') : __('messages.mi_archive_chat') }}
                                    </span>
                                </button>

                                <button type="button"
                                        @click.stop="if (confirm(@js(__('messages.mi_confirm_delete')))) { menuOpen = false; alert(@js(__('messages.mi_feature_coming_soon', ['feature' => __('messages.mi_delete_chat')]))); }"
                                        class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-red-50 dark:hover:bg-red-950/20 transition text-left">
                                    <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    <span class="text-sm font-bold text-red-500">{{ __('messages.mi_delete_chat') }}</span>
                                </button>

                                <button type="button"
                                        @click.stop="menuOpen = false; alert(@js(__('messages.mi_feature_coming_soon', ['feature' => __('messages.mi_report')])))"
                                        class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-neutral-50 dark:hover:bg-neutral-700/50 transition text-left">
                                    <svg class="w-4 h-4 text-neutral-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    <span class="text-sm font-bold text-neutral-700 dark:text-neutral-200">{{ __('messages.mi_report') }}</span>
                                </button>
                            </div>
                        </div>
                    @endforeach

                </div>

                <!-- EMPTY STATE -->
                <div x-show="(tab === 'active' && activeCount === 0) || (tab === 'archived' && archivedCount === 0)"
                     x-cloak
                     class="bg-white dark:bg-neutral-900 rounded-3xl shadow-xl border border-gray-200/60 dark:border-neutral-800 p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-neutral-300 dark:text-neutral-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <h3 class="text-xl font-black text-[#1B3B36] dark:text-white" 
                        x-text="tab === 'active' ? @js(__('messages.mi_empty_active_title')) : @js(__('messages.mi_empty_archived_title'))"></h3>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1"
                       x-text="tab === 'active' ? @js(__('messages.mi_empty_active_desc')) : @js(__('messages.mi_empty_archived_desc'))"></p>
                    <a x-show="tab === 'active'"
                       href="{{ route('find.sitter') }}" 
                       class="inline-block mt-4 px-6 py-3 bg-primary text-white font-bold text-sm rounded-xl hover:bg-primary-600 transition">
                        {{ __('messages.mi_find_sitter') }}
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>