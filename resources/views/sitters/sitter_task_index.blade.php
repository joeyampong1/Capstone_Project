<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div>
                <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                    {{ __('messages.sti_title') }}
                </h1>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.sti_subtitle') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200">
        <div class="w-full sm:max-w-5xl mx-auto px-2 sm:px-16 lg:px-24">

            @php
                $overdueCount = \App\Models\Visit::whereHas('booking', function ($q) {
                    $q->where('sitter_id', auth()->id())
                      ->whereIn('status', ['accepted', 'completed']);
                })
                ->where('status', 'pending')
                ->where('scheduled_datetime', '<', now())
                ->count();
            @endphp

            {{-- ========================================== --}}
            {{-- STATS CARDS (Summary)                      --}}
            {{-- ========================================== --}}
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-6">
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sti_total_tasks') }}</p>
                    <h3 class="font-bold text-lg sm:text-2xl mt-1 text-[#1B3B36] dark:text-white">{{ $stats['total'] }}</h3>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sti_completed') }}</p>
                    <h3 class="font-bold text-lg sm:text-2xl mt-1 text-green-600 dark:text-green-400">{{ $stats['completed'] }}</h3>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sti_pending') }}</p>
                    <h3 class="font-bold text-lg sm:text-2xl mt-1 text-yellow-600 dark:text-yellow-400">{{ $stats['pending'] }}</h3>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sti_overdue') }}</p>
                    <h3 class="font-bold text-lg sm:text-2xl mt-1 text-red-600 dark:text-red-400">{{ $overdueCount }}</h3>
                </div>
            </div>

            {{-- ========================================== --}}
            {{-- BOOKING LIST (1 card per booking)          --}}
            {{-- ========================================== --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">

                @forelse($bookings as $booking)
                    @php
                        $pet   = $booking->pet;
                        $owner = $booking->owner;

                        $totalVisits     = $booking->visits->count();
                        $completedVisits = $booking->visits->where('status', 'completed')->count();
                        $pendingVisits   = $booking->visits->where('status', 'pending')->count();
                        $inProgressVisit = $booking->visits->firstWhere('status', 'in_progress');
                        $progressPct     = $totalVisits > 0
                            ? round(($completedVisits / $totalVisits) * 100)
                            : 0;

                        $ownerName = trim(($owner->f_name ?? '') . ' ' . ($owner->l_name ?? '')) ?: __('messages.sti_owner_default');

                        $petEmoji = match($pet->petType?->name ?? '') {
                            'Dog' => '🐶', 'Cat' => '🐱', 'Bird' => '🐦',
                            'Rabbit' => '🐰', 'Hamster' => '🐹', 'Fish' => '🐟',
                            'Reptile' => '🦎', default => '🐾',
                        };

                        // Next scheduled visit
                        $nextVisit = $booking->visits
                            ->whereIn('status', ['pending', 'in_progress'])
                            ->sortBy('scheduled_datetime')
                            ->first();

                        // Overall status badge (base sa visits)
                        if ($inProgressVisit) {
                            $statusBadgeClass = 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400';
                            $statusBadgeLabel = __('messages.sti_badge_in_progress');
                        } elseif ($completedVisits === $totalVisits && $totalVisits > 0) {
                            $statusBadgeClass = 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400';
                            $statusBadgeLabel = __('messages.sti_badge_completed');
                        } else {
                            $statusBadgeClass = 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400';
                            $statusBadgeLabel = __('messages.sti_badge_active');
                        }
                    @endphp

                    <a href="{{ route('sitter.tasks.show', $booking->id) }}"
                       class="group bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-5 hover:shadow-lg hover:border-primary/30 transition duration-200 block">

                        {{-- Header: Booking Ref + Status --}}
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sti_booking_ref') }}</p>
                                <h3 class="font-bold text-lg sm:text-xl text-[#1B3B36] dark:text-white truncate">
                                    {{ $booking->booking_reference }}
                                </h3>
                            </div>
                            <span class="{{ $statusBadgeClass }} px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full text-xs sm:text-sm font-semibold shrink-0">
                                {{ $statusBadgeLabel }}
                            </span>
                        </div>

                        {{-- Pet + Owner --}}
                        <div class="grid grid-cols-2 gap-2.5 sm:gap-3 mt-4">
                            <div class="min-w-0">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sti_pet') }}</p>
                                <p class="font-bold text-[#1B3B36] dark:text-white truncate">
                                    {{ $petEmoji }} {{ $pet->name ?? __('messages.sti_pet_default') }}
                                </p>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sti_owner') }}</p>
                                <p class="font-bold text-[#1B3B36] dark:text-white truncate">{{ $ownerName }}</p>
                            </div>
                        </div>

                        {{-- Visit Progress --}}
                        @if($totalVisits > 0)
                            <div class="mt-4">
                                <div class="flex justify-between gap-2 text-sm">
                                    <span class="text-neutral-500 dark:text-neutral-400">{{ __('messages.sti_visit_progress') }}</span>
                                    <span class="font-bold text-[#1B3B36] dark:text-white shrink-0">
                                        {{ $completedVisits }} / {{ $totalVisits }} {{ __('messages.sti_visits') }}
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-neutral-700 rounded-full h-2 mt-1">
                                    <div class="bg-primary h-2 rounded-full transition-all"
                                         style="width:{{ $progressPct }}%"></div>
                                </div>
                            </div>
                        @endif

                        {{-- Next Visit --}}
                        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                            <div class="flex items-center justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sti_next_visit') }}</p>

                                    @if($nextVisit)
                                        <p class="text-sm font-semibold text-[#1B3B36] dark:text-white truncate">
                                            @if($nextVisit->scheduled_datetime->isToday())
                                                {{ __('messages.sti_today_at', ['time' => $nextVisit->scheduled_datetime->format('g:i A')]) }}
                                            @elseif($nextVisit->scheduled_datetime->isTomorrow())
                                                {{ __('messages.sti_tomorrow_at', ['time' => $nextVisit->scheduled_datetime->format('g:i A')]) }}
                                            @else
                                                {{ $nextVisit->scheduled_datetime->format('M j, Y g:i A') }}
                                            @endif
                                        </p>
                                    @else
                                        <p class="text-sm font-semibold text-neutral-400 italic">
                                            {{ __('messages.sti_all_visits_completed') }}
                                        </p>
                                    @endif
                                </div>

                                @if($nextVisit && $nextVisit->scheduled_datetime->isToday())
                                    <span class="text-xs bg-primary/10 text-primary px-3 py-1 rounded-full font-medium shrink-0">
                                        {{ __('messages.sti_due_today') }}
                                    </span>
                                @elseif($inProgressVisit)
                                    <span class="text-xs bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 px-3 py-1 rounded-full font-medium shrink-0">
                                        {{ __('messages.sti_badge_in_progress') }}
                                    </span>
                                @elseif($nextVisit)
                                    <span class="text-xs bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 px-3 py-1 rounded-full font-medium shrink-0">
                                        {{ __('messages.sti_upcoming') }}
                                    </span>
                                @else
                                    <span class="text-xs bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 px-3 py-1 rounded-full font-medium shrink-0">
                                        {{ __('messages.sti_done') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- View details --}}
                        <div class="mt-3 flex justify-end">
                            <span class="text-sm font-medium text-primary group-hover:underline">
                                {{ __('messages.sti_view_visits', ['count' => $totalVisits]) }}
                            </span>
                        </div>
                    </a>
                @empty
                    {{-- Empty state --}}
                    <div class="md:col-span-2 text-center py-12">
                        <svg class="w-12 h-12 mx-auto text-neutral-300 dark:text-neutral-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">
                            {{ __('messages.sti_no_bookings') }}
                        </p>
                    </div>
                @endforelse

            </div>
        </div>
    </div>
</x-app-layout>