@php
    $user             = auth()->user();
    $isApprovedSitter = $user->sitter_status === 'approved';
    $isSitterMode     = (bool) $user->is_sitter;
@endphp

<x-app-layout>
    <!-- HEADER SLOT -->
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <!-- Left: Home Icon + Title -->
            <div class="flex items-center gap-3">
                <svg class="w-14 h-14 text-[#1B3B36] dark:text-white"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    stroke-width="2.5"
                    stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2z"/>
                </svg>
                <div>
                    <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                        {{ __('messages.od_title') }}
                    </h1>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                        {{ __('messages.od_subtitle') }}
                    </p>
                </div>
            </div>

            <!-- Right: Mode Indicator (read-only — controlled from Settings) -->
            @if($isApprovedSitter)
                <div class="flex flex-col items-end gap-1.5">
                    <div class="flex items-center gap-1 p-1 bg-neutral-100 dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 cursor-default select-none"
                        title="{{ __('messages.od_change_mode') }}">
                        <span class="px-4 py-2 text-xs font-bold rounded-lg transition
                            {{ ! $isSitterMode
                                ? 'bg-primary text-white shadow-md'
                                : 'text-neutral-400 dark:text-neutral-500' }}">
                            {{ __('messages.od_mode_owner') }}
                        </span>
                        <span class="px-4 py-2 text-xs font-bold rounded-lg transition
                            {{ $isSitterMode
                                ? 'bg-primary text-white shadow-md'
                                : 'text-neutral-400 dark:text-neutral-500' }}">
                            {{ __('messages.od_mode_sitter') }}
                        </span>
                    </div>
                    <a href="{{ route('settings.index') }}"
                    class="text-[10px] text-neutral-400 hover:text-primary transition inline-flex items-center gap-0.5">
                        {{ __('messages.od_change_mode') }}
                    </a>
                </div>
            @endif
        </div>
    </x-slot>

    <!-- MAIN CONTENT -->
    <div class="py-6 sm:py-12 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200">
        <div class="w-full mx-auto px-2 sm:px-16 lg:px-24 space-y-16">

            <!-- CARD 1: HERO VIEWPORT -->
            <div class="relative overflow-hidden bg-gradient-to-br from-[#FFF5F1] via-[#FFF9F6] to-[#F7FAF9] dark:from-neutral-900 dark:via-neutral-900/60 dark:to-transparent rounded-[32px] border border-[#FDE3D8]/40 dark:border-neutral-800 p-6 sm:p-14 lg:p-20 flex flex-col items-start justify-center min-h-[480px]">
                <div class="max-w-2xl space-y-6 relative z-10">

                    <!-- Tag label -->
                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-extrabold bg-[#FCECE6] dark:bg-orange-950/40 text-[#F17743]">
                        <span class="text-sm">🐾</span> {{ __('messages.od_tag_home_based') }}
                    </span>

                    <!-- Main heading -->
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-[#1B3B36] dark:text-white tracking-tight leading-[1.05]">
                        {{ __('messages.od_hero_title') }}
                    </h1>

                    <!-- Description -->
                    <p class="text-sm sm:text-base text-neutral-500 dark:text-neutral-400 leading-relaxed max-w-xl font-medium">
                        {{ __('messages.od_hero_desc') }}
                    </p>

                    <!-- CTA Buttons -->
                    <div class="flex flex-wrap items-center gap-4 pt-4">
                        {{-- Find a Sitter --}}
                        <a href="{{ route('find.sitter') }}"
                           class="inline-flex items-center gap-2 px-8 py-4 bg-primary hover:bg-primary-600 text-white font-bold text-sm rounded-xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5">
                            <span>{{ __('messages.od_find_sitter') }}</span>
                            <span class="inline-block transition-transform group-hover:translate-x-1">→</span>
                        </a>

                        {{-- Become a Sitter (hide if already approved) --}}
                        @if(! $isApprovedSitter)
                            <a href="{{ route('sitter.application') }}"
                               class="inline-flex items-center px-8 py-4 bg-white dark:bg-neutral-800 border-2 border-gray-200 dark:border-neutral-700 text-gray-700 dark:text-gray-200 font-bold text-sm rounded-xl hover:bg-gray-50 dark:hover:bg-neutral-700 transition transform hover:-translate-y-0.5">
                                {{ __('messages.od_become_sitter') }}
                            </a>
                        @else
                            <a href="{{ route('sitter.dashboard') }}"
                               class="inline-flex items-center gap-2 px-8 py-4 bg-white dark:bg-neutral-800 border-2 border-primary text-primary font-bold text-sm rounded-xl hover:bg-primary/5 transition transform hover:-translate-y-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                {{ __('messages.od_sitter_dashboard') }}
                            </a>
                        @endif
                    </div>

                </div>

                <!-- Glow effect -->
                <div class="absolute right-0 top-0 bottom-0 w-1/3 bg-gradient-to-l from-[#FCECE6]/30 via-transparent to-transparent pointer-events-none hidden md:block"></div>
            </div>

            <!-- CARD 2: WHY PETNANNY -->
            <div class="space-y-12 pt-4">
                <div class="space-y-2 text-center">
                    <h2 class="text-3xl font-black text-[#1B3B36] dark:text-white tracking-tight">
                        {{ __('messages.od_why_title') }}
                    </h2>
                    <p class="text-sm text-neutral-400 font-medium">
                        {{ __('messages.od_why_subtitle') }}
                    </p>
                </div>

                <!-- 4 Column Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white dark:bg-neutral-900 border border-neutral-100 dark:border-neutral-800/80 p-6 rounded-[24px] flex flex-col items-center text-center space-y-4 shadow-sm hover:shadow-md transition duration-200">
                        <div class="w-12 h-12 bg-[#FFF5F1] dark:bg-neutral-800 rounded-full flex items-center justify-center text-primary">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <h3 class="font-extrabold text-base text-[#1B3B36] dark:text-white tracking-tight">{{ __('messages.od_why_1_title') }}</h3>
                        <p class="text-xs text-neutral-400 dark:text-neutral-500 leading-relaxed font-medium">{{ __('messages.od_why_1_desc') }}</p>
                    </div>

                    <div class="bg-white dark:bg-neutral-900 border border-neutral-100 dark:border-neutral-800/80 p-6 rounded-[24px] flex flex-col items-center text-center space-y-4 shadow-sm hover:shadow-md transition duration-200">
                        <div class="w-12 h-12 bg-[#FFF5F1] dark:bg-neutral-800 rounded-full flex items-center justify-center text-primary">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <h3 class="font-extrabold text-base text-[#1B3B36] dark:text-white tracking-tight">{{ __('messages.od_why_2_title') }}</h3>
                        <p class="text-xs text-neutral-400 dark:text-neutral-500 leading-relaxed font-medium">{{ __('messages.od_why_2_desc') }}</p>
                    </div>

                    <div class="bg-white dark:bg-neutral-900 border border-neutral-100 dark:border-neutral-800/80 p-6 rounded-[24px] flex flex-col items-center text-center space-y-4 shadow-sm hover:shadow-md transition duration-200">
                        <div class="w-12 h-12 bg-[#FFF5F1] dark:bg-neutral-800 rounded-full flex items-center justify-center text-accent">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <h3 class="font-extrabold text-base text-[#1B3B36] dark:text-white tracking-tight">{{ __('messages.od_why_3_title') }}</h3>
                        <p class="text-xs text-neutral-400 dark:text-neutral-500 leading-relaxed font-medium">{{ __('messages.od_why_3_desc') }}</p>
                    </div>

                    <div class="bg-white dark:bg-neutral-900 border border-neutral-100 dark:border-neutral-800/80 p-6 rounded-[24px] flex flex-col items-center text-center space-y-4 shadow-sm hover:shadow-md transition duration-200">
                        <div class="w-12 h-12 bg-[#FFF5F1] dark:bg-neutral-800 rounded-full flex items-center justify-center text-accent">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        </div>
                        <h3 class="font-extrabold text-base text-[#1B3B36] dark:text-white tracking-tight">{{ __('messages.od_why_4_title') }}</h3>
                        <p class="text-xs text-neutral-400 dark:text-neutral-500 leading-relaxed font-medium">{{ __('messages.od_why_4_desc') }}</p>
                    </div>
                </div>
            </div>

            <!-- CARD 3: MY BOOKINGS (synced with Settings) -->
            <div class="bg-[#FCFAF9] dark:bg-neutral-900/40 rounded-[32px] border border-neutral-100 dark:border-neutral-800 p-6 sm:p-14 space-y-8 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="space-y-1">
                        <h2 class="text-3xl font-black text-[#1B3B36] dark:text-white tracking-tight">
                            {{ __('messages.od_bookings_title') }}
                        </h2>
                        <p class="text-sm text-neutral-400 font-medium">
                            @if($isSitterMode)
                                {{ __('messages.od_bookings_sitter_desc') }}
                            @else
                                {{ __('messages.od_bookings_owner_desc') }}
                            @endif
                        </p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-primary/10 text-primary">
                        @if($isSitterMode)
                            {{ __('messages.od_bookings_pending_count', ['count' => $sitterPendingCount]) }}
                        @else
                            {{ __('messages.od_bookings_active_count', ['count' => $ownerActiveCount]) }}
                        @endif
                    </span>
                </div>

                <!-- BOOKING LIST – Owner Mode -->
                @if(! $isSitterMode)
                    <div class="space-y-4">
                        @forelse($ownerBookings as $booking)
                            @php
                                $sitter = $booking->sitter;
                                $pet = $booking->pet;

                                $sitterName = trim(($sitter?->f_name ?? '') . ' ' . ($sitter?->l_name ?? '')) ?: __('messages.od_mode_sitter');

                                $statusMap = [
                                    'pending'   => ['bg-amber-100 dark:bg-amber-900/30', 'text-amber-600 dark:text-amber-400', __('messages.od_status_pending'),   'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                                    'accepted'  => ['bg-green-100 dark:bg-green-900/30', 'text-green-600 dark:text-green-400', __('messages.od_status_confirmed'), 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                                    'completed' => ['bg-blue-100 dark:bg-blue-900/30',   'text-blue-600 dark:text-blue-400',   __('messages.od_status_completed'), 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2z'],
                                ];
                                [$iconBg, $iconText, $statusLabel, $statusIcon] = $statusMap[$booking->status] ?? $statusMap['pending'];
                            @endphp

                            <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-gray-100 dark:border-neutral-800 p-5 flex items-center justify-between hover:shadow-md transition">
                                <div class="flex items-center gap-4 min-w-0">
                                    <div class="w-12 h-12 rounded-full {{ $iconBg }} flex items-center justify-center {{ $iconText }} shrink-0">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $statusIcon }}"/>
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="font-bold text-[#1B3B36] dark:text-white truncate">{{ $sitterName }}</h4>
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400 truncate">
                                            {{ __('messages.od_visits_per_day', ['count' => $booking->visit_per_day]) }}
                                            • {{ $pet->name ?? 'Pet' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right shrink-0 ml-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold {{ $iconBg }} {{ $iconText }}">
                                        {{ $statusLabel }}
                                    </span>
                                    <p class="text-[10px] text-neutral-400 mt-0.5">
                                        {{ $booking->start_date->format('M j, Y') }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 mx-auto text-neutral-300 dark:text-neutral-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('messages.od_no_active_bookings') }}</p>
                                <a href="{{ route('find.sitter') }}"
                                   class="inline-block mt-3 text-xs font-bold text-primary hover:underline">
                                    → {{ __('messages.od_find_a_sitter') }}
                                </a>
                            </div>
                        @endforelse
                    </div>
                @endif

                <!-- BOOKING LIST – Sitter Mode -->
                @if($isSitterMode)
                    <div class="space-y-4">
                        @if($canSwitchToSitter)
                            @forelse($sitterBookings as $booking)
                                @php
                                    $owner = $booking->owner;
                                    $pet = $booking->pet;

                                    $ownerName = trim(($owner?->f_name ?? '') . ' ' . ($owner?->l_name ?? '')) ?: __('messages.od_mode_owner');
                                    $ownerInitial = strtoupper(substr($owner?->f_name ?? 'O', 0, 1));

                                    $statusMap = [
                                        'pending'  => ['bg-amber-100 dark:bg-amber-900/30', 'text-amber-600 dark:text-amber-400', __('messages.od_status_pending'),   'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                                        'accepted' => ['bg-green-100 dark:bg-green-900/30', 'text-green-600 dark:text-green-400', __('messages.od_status_confirmed'), 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                                    ];
                                    [$iconBg, $iconText, $statusLabel, $statusIcon] = $statusMap[$booking->status] ?? $statusMap['pending'];

                                    $petEmoji = match($pet?->petType?->name ?? '') {
                                        'Dog' => '🐶', 'Cat' => '🐱', 'Bird' => '🐦',
                                        'Rabbit' => '🐰', 'Hamster' => '🐹', 'Fish' => '🐟',
                                        'Reptile' => '🦎', default => '🐾',
                                    };
                                @endphp

                                <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-gray-100 dark:border-neutral-800 p-5 hover:shadow-md transition">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold shrink-0">
                                                {{ $ownerInitial }}
                                            </div>
                                            <div class="min-w-0">
                                                <h4 class="font-bold text-[#1B3B36] dark:text-white truncate">{{ $ownerName }}</h4>
                                                <p class="text-xs text-neutral-500 dark:text-neutral-400 truncate">
                                                    {{ __('messages.od_visits_per_day', ['count' => $booking->visit_per_day]) }} •
                                                    {{ $booking->start_date->format('M j') }}-{{ $booking->end_date->format('M j') }}
                                                </p>
                                            </div>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold {{ $iconBg }} {{ $iconText }} shrink-0">
                                            {{ $statusLabel }}
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-3 mt-3 pt-3 border-t border-gray-100 dark:border-neutral-800">
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400 flex-1 truncate">
                                            {{ $petEmoji }} {{ $pet->name ?? 'Pet' }}
                                            @if(!empty($booking->tasks))
                                                • {{ __('messages.od_tasks_count', ['count' => count($booking->tasks)]) }}
                                            @endif
                                        </p>

                                        @if($booking->status === 'pending')
                                            <div class="flex items-center gap-2 shrink-0">
                                                <form method="POST" action="{{ route('bookings.accept', $booking->id) }}">
                                                    @csrf
                                                    <button type="submit"
                                                            class="px-4 py-1.5 text-xs font-bold bg-green-500 hover:bg-green-600 text-white rounded-lg transition">
                                                        {{ __('messages.od_accept') }}
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('bookings.reject', $booking->id) }}"
                                                      onsubmit="return confirm('{{ __('messages.od_confirm_decline') }}')">
                                                    @csrf
                                                    <button type="submit"
                                                            class="px-4 py-1.5 text-xs font-bold border-2 border-red-500 text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-lg transition">
                                                        {{ __('messages.od_decline') }}
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <a href="{{ route('sitter.tasks.show', $booking->id) }}"
                                               class="text-xs font-bold text-primary hover:underline shrink-0">
                                                {{ __('messages.od_view_tasks') }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <svg class="w-12 h-12 mx-auto text-neutral-300 dark:text-neutral-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('messages.od_no_incoming_bookings') }}</p>
                                </div>
                            @endforelse
                        @else
                            <div class="text-center py-8">
                                <p class="text-sm text-neutral-500 dark:text-neutral-400">
                                    {{ __('messages.od_need_approval') }}
                                </p>
                                <a href="{{ route('sitter.application') }}"
                                   class="inline-block mt-3 text-xs font-bold text-primary hover:underline">
                                    → {{ __('messages.od_apply_sitter') }}
                                </a>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- View All link -->
                <div class="text-center pt-2">
                    <a href="{{ route('mybookings.index') }}"
                       class="text-sm text-primary font-semibold hover:underline inline-flex items-center gap-1">
                        {{ __('messages.od_view_all_bookings') }}
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- CARD 4: HOW IT WORKS -->
            <div class="bg-[#FCFAF9] dark:bg-neutral-900/40 rounded-[32px] border border-neutral-100 dark:border-neutral-800 p-6 sm:p-14 space-y-12 shadow-sm">
                <div class="space-y-2 text-center">
                    <h2 class="text-3xl font-black text-[#1B3B36] dark:text-white tracking-tight">
                        {{ __('messages.od_how_title') }}
                    </h2>
                    <p class="text-sm text-neutral-400 font-medium">
                        {{ __('messages.od_how_subtitle') }}
                    </p>
                </div>

                <!-- 3 Steps Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-10 md:gap-6 relative">
                    <div class="flex flex-col items-center text-center space-y-4 relative z-10">
                        <div class="w-16 h-16 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-2xl font-black text-primary">1</div>
                        <h3 class="text-xl font-bold text-[#1B3B36] dark:text-white tracking-tight">{{ __('messages.od_step_1_title') }}</h3>
                        <p class="text-sm text-neutral-400 dark:text-neutral-500 leading-relaxed max-w-xs font-medium">{{ __('messages.od_step_1_desc') }}</p>
                    </div>

                    <div class="flex flex-col items-center text-center space-y-4 relative z-10">
                        <div class="w-16 h-16 rounded-full bg-accent-100 dark:bg-accent-900/30 flex items-center justify-center text-2xl font-black text-accent">2</div>
                        <h3 class="text-xl font-bold text-[#1B3B36] dark:text-white tracking-tight">{{ __('messages.od_step_2_title') }}</h3>
                        <p class="text-sm text-neutral-400 dark:text-neutral-500 leading-relaxed max-w-xs font-medium">{{ __('messages.od_step_2_desc') }}</p>
                    </div>

                    <div class="flex flex-col items-center text-center space-y-4 relative z-10">
                        <div class="w-16 h-16 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-2xl font-black text-primary">3</div>
                        <h3 class="text-xl font-bold text-[#1B3B36] dark:text-white tracking-tight">{{ __('messages.od_step_3_title') }}</h3>
                        <p class="text-sm text-neutral-400 dark:text-neutral-500 leading-relaxed max-w-xs font-medium">{{ __('messages.od_step_3_desc') }}</p>
                    </div>
                </div>
            </div>

            <!-- CTA FOOTER -->
            <div class="text-center py-8 space-y-5">
                <div class="space-y-1">
                    <h2 class="text-3xl font-black text-[#1B3B36] dark:text-white tracking-tight">
                        {{ __('messages.od_cta_title') }}
                    </h2>
                    <p class="text-sm text-neutral-400 font-medium">
                        {{ __('messages.od_cta_desc') }}
                    </p>
                </div>

                <a href="{{ route('find.sitter') }}"
                   class="inline-flex items-center gap-2 px-8 py-4 bg-primary hover:bg-primary-600 text-white font-bold text-sm rounded-xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5">
                    <span>☆ {{ __('messages.od_cta_explore') }}</span>
                </a>
            </div>

        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- ONBOARDING VERIFICATION MODAL                                --}}
    {{-- ============================================================ --}}
    @php
        $user = auth()->user();

        // Check kung kompleto ang profile
        $profileComplete = $user->f_name
            && $user->l_name
            && $user->date_of_birth
            && $user->gender
            && $user->contact_number
            && $user->address;

        // Initial step
        $initialStep = $user->gov_id_path ? 'verify' : 'intro';
    @endphp

    @if(auth()->user()->id_validation_status === 'unverified' && !auth()->user()->onboarding_dismissed)
        <div x-data="{
                step: '{{ $initialStep }}',
                close() {
                    fetch('{{ route('onboarding.dismiss') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                    });
                    this.$root.remove();
                }
            }"
            x-cloak
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm overflow-y-auto">

            {{-- INTRO STEP --}}
            <div x-show="step === 'intro'"
                x-transition.opacity
                class="bg-white dark:bg-neutral-900 rounded-2xl sm:rounded-3xl shadow-2xl border border-gray-200 dark:border-neutral-800 max-w-md w-full p-6 sm:p-8 my-8">

                <div class="text-center space-y-4">
                    <div class="w-16 h-16 mx-auto rounded-full bg-primary/10 flex items-center justify-center text-primary">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>

                    <div class="space-y-2">
                        <h2 class="text-2xl font-black text-[#1B3B36] dark:text-white tracking-tight">
                            {{ __('messages.onboard_title') }}
                        </h2>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">
                            {{ __('messages.onboard_desc') }}
                        </p>
                    </div>

                    <ul class="text-left space-y-2 py-4 text-sm text-neutral-600 dark:text-neutral-300">
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-primary shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ __('messages.onboard_item_1') }}
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-primary shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ __('messages.onboard_item_2') }}
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-primary shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ __('messages.onboard_item_3') }}
                        </li>
                    </ul>
                </div>

                {{-- Warning box — separate sa buttons --}}
                @if(!$profileComplete)
                    <div class="mb-3 p-3 rounded-xl bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800">
                        <div class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-amber-700 dark:text-amber-400">
                                    {{ __('messages.onboard_profile_incomplete_title') }}
                                </p>
                                <p class="text-xs text-amber-600 dark:text-amber-400 mt-1">
                                    {{ __('messages.onboard_profile_incomplete_desc') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Buttons row — 2 columns --}}
                <div class="flex flex-col sm:flex-row gap-2">
                    <button type="button"
                            @click="close()"
                            class="flex-1 px-4 py-3 rounded-xl border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-sm hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                        {{ __('messages.onboard_later') }}
                    </button>

                    @if($profileComplete)
                        <button type="button"
                                @click="step = 'verify'"
                                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 bg-primary hover:bg-primary-600 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition">
                            {{ __('messages.onboard_continue') }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </button>
                    @else
                        <a href="{{ route('profile.edit') }}"
                        class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 bg-primary hover:bg-primary-600 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition">
                            {{ __('messages.onboard_complete_profile') }}
                        </a>
                    @endif
                </div>
            </div>

            {{-- VERIFY STEP --}}
            <div x-show="step === 'verify'"
                x-cloak
                x-transition.opacity
                class="bg-white dark:bg-neutral-900 rounded-2xl sm:rounded-3xl shadow-2xl border border-gray-200 dark:border-neutral-800 max-w-2xl w-full max-h-[92vh] overflow-y-auto my-8">

                <div class="sticky top-0 bg-white dark:bg-neutral-900 border-b border-gray-100 dark:border-neutral-800 px-6 py-4 flex items-center justify-between z-10">
                    <div class="flex items-center gap-2">
                        <button type="button"
                                @click="step = 'intro'"
                                class="p-1.5 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                            <svg class="w-5 h-5 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                        </button>
                        <h2 class="text-lg font-black text-[#1B3B36] dark:text-white">
                            {{ __('messages.onboard_verify_title') }}
                        </h2>
                    </div>
                    <button type="button"
                            @click="close()"
                            class="p-1.5 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                        <svg class="w-5 h-5 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-6 sm:p-8">
                    @include('profile.partials.update-id-form')
                </div>
            </div>
        </div>
    @endif

</x-app-layout>
