<x-app-layout>
    <!-- ========================================== -->
    <!-- HEADER SLOT                               -->
    <!-- ========================================== -->
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                    {{ __('messages.sd_title') }}
                </h1>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.sd_subtitle') }}</p>
            </div>
        </div>
    </x-slot>

    <!-- ========================================== -->
    <!-- MAIN CONTENT                              -->
    <!-- ========================================== -->
    <div class="py-4 sm:py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200">
        <div class="w-full sm:max-w-6xl mx-auto px-2 sm:px-16 lg:px-24">

            <!-- ========================================== -->
            <!-- STATS CARDS                               -->
            <!-- ========================================== -->
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-6">

                <!-- Card 1: Total Earnings -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 p-4 sm:p-5 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.sd_total_earnings') }}</p>
                            <p class="text-lg sm:text-2xl font-black text-[#1B3B36] dark:text-white mt-1">
                                ₱{{ number_format($totalEarnings, 0) }}
                            </p>
                            <p class="text-[10px] sm:text-xs text-green-600 dark:text-green-400 mt-1">
                                ✅ {{ $thisMonthCompletedCount }} {{ __('messages.sd_completed') }}
                            </p>
                        </div>
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Pending Payments -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 p-4 sm:p-5 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.sd_pending') }}</p>
                            <p class="text-lg sm:text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">
                                ₱{{ number_format($pendingPayments, 0) }}
                            </p>
                            <p class="text-[10px] sm:text-xs text-amber-600 dark:text-amber-400 mt-1">
                                ⏳ {{ $pendingBookingsCount }} {{ __('messages.sd_booking_s') }}
                            </p>
                        </div>
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Upcoming Income -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 p-4 sm:p-5 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.sd_upcoming') }}</p>
                            <p class="text-lg sm:text-2xl font-black text-blue-600 dark:text-blue-400 mt-1">
                                ₱{{ number_format($upcomingIncome, 0) }}
                            </p>
                            <p class="text-[10px] sm:text-xs text-blue-600 dark:text-blue-400 mt-1">
                                📅 {{ __('messages.sd_next_7_days') }}
                            </p>
                        </div>
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Card 4: This Month -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 p-4 sm:p-5 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.sd_this_month') }}</p>
                            <p class="text-lg sm:text-2xl font-black text-green-600 dark:text-green-400 mt-1">
                                ₱{{ number_format($thisMonthEarnings, 0) }}
                            </p>
                            <p class="text-[10px] sm:text-xs text-green-600 dark:text-green-400 mt-1">
                                ✅ {{ $thisMonthCompletedCount }} {{ __('messages.sd_done') }}
                            </p>
                        </div>
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ========================================== -->
            <!-- CHARTS & STATS ROW                        -->
            <!-- ========================================== -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-4 sm:mb-6">

                <!-- Chart Card: Monthly Earnings -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 p-4 sm:p-5 hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-black text-[#1B3B36] dark:text-white">{{ __('messages.sd_monthly_earnings') }}</h3>
                        <span class="text-xs text-neutral-400">{{ __('messages.sd_last_6_months') }}</span>
                    </div>

                    @if($maxEarning > 0)
                        <div class="flex items-end justify-between h-32 sm:h-40 gap-1.5 sm:gap-2">
                            @foreach($monthlyEarnings as $index => $month)
                                @php
                                    $heightPercent = $maxEarning > 0 ? round(($month['value'] / $maxEarning) * 100) : 0;
                                    $heightPercent = max($heightPercent, 4);
                                    $opacity = 20 + ($index * 16); // 20, 36, 52, 68, 84, 100
                                @endphp
                                <div class="flex flex-col items-center gap-1 flex-1">
                                    <div class="w-full rounded-t-lg transition-all"
                                         style="height: {{ $heightPercent }}%; background-color: rgb(234 88 12 / {{ $opacity / 100 }});"
                                         title="₱{{ number_format($month['value'], 0) }}"></div>
                                    <span class="text-[10px] text-neutral-400">{{ $month['label'] }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="flex justify-between text-[10px] text-neutral-400 mt-2">
                            <span>₱0</span>
                            <span>₱{{ number_format($maxEarning, 0) }}</span>
                        </div>
                    @else
                        <div class="flex items-center justify-center h-32 sm:h-40">
                            <p class="text-xs text-neutral-400 italic">{{ __('messages.sd_no_earnings_yet') }}</p>
                        </div>
                    @endif
                </div>

                <!-- Chart Card: Earnings Breakdown -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 p-4 sm:p-5 hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-black text-[#1B3B36] dark:text-white">{{ __('messages.sd_earnings_breakdown') }}</h3>
                        <span class="text-xs text-neutral-400">{{ __('messages.sd_by_category') }}</span>
                    </div>

                    @if($earningsByPetType->count() > 0)
                        <div class="space-y-3">
                            @foreach($earningsByPetType as $index => $item)
                                @php
                                    $percent = $totalByType > 0 ? round(($item->total / $totalByType) * 100) : 0;
                                    $colorClass = match($index) {
                                        0 => 'bg-primary',
                                        1 => 'bg-accent',
                                        default => 'bg-amber-400',
                                    };
                                @endphp
                                <div>
                                    <div class="flex justify-between gap-2 text-sm">
                                        <span class="text-neutral-600 dark:text-neutral-400">{{ $item->pet_type }} {{ __('messages.sd_sitting') }}</span>
                                        <span class="font-bold text-[#1B3B36] dark:text-white shrink-0">
                                            ₱{{ number_format($item->total, 0) }}
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-neutral-700 rounded-full h-2 mt-1">
                                        <div class="{{ $colorClass }} rounded-full h-2 transition-all" style="width: {{ $percent }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex items-center justify-center h-32 sm:h-40">
                            <p class="text-xs text-neutral-400 italic">{{ __('messages.sd_no_completed_bookings') }}</p>
                        </div>
                    @endif
                </div>

            </div>

            <!-- ========================================== -->
            <!-- RECENT TRANSACTIONS + UPCOMING VISITS     -->
            <!-- ========================================== -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">

                <!-- Recent Payments -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 p-4 sm:p-5 hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-black text-[#1B3B36] dark:text-white">{{ __('messages.sd_recent_payments') }}</h3>
                        {{-- <a href="{{ route('sitter.payments') }}" class="text-xs text-primary font-semibold hover:underline">{{ __('messages.sd_view_all') }}</a> --}}
                    </div>

                    @if($recentPayments->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentPayments as $payment)
                                @php
                                    $ownerName = trim(($payment->owner?->f_name ?? '') . ' ' . ($payment->owner?->l_name ?? '')) ?: __('messages.sd_owner_default');
                                @endphp
                                <div class="flex items-center justify-between gap-3 py-2 {{ !$loop->last ? 'border-b border-gray-100 dark:border-neutral-800' : '' }}">
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-[#1B3B36] dark:text-white truncate">{{ $ownerName }}</p>
                                        <p class="text-[10px] text-neutral-400">{{ __('messages.sd_booking') }} {{ $payment->booking_reference }}</p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <p class="text-sm font-bold text-green-600">
                                            +₱{{ number_format($payment->sitter_earnings, 0) }}
                                        </p>
                                        <p class="text-[10px] text-neutral-400">{{ $payment->updated_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex items-center justify-center h-32">
                            <p class="text-xs text-neutral-400 italic">{{ __('messages.sd_no_completed_payments') }}</p>
                        </div>
                    @endif
                </div>

                <!-- Upcoming Visits -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 p-4 sm:p-5 hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-black text-[#1B3B36] dark:text-white">{{ __('messages.sd_upcoming_visits') }}</h3>
                        <a href="{{ route('sitter.tasks.index') }}" class="text-xs text-primary font-semibold hover:underline">{{ __('messages.sd_view_all') }}</a>
                    </div>

                    @if($upcomingVisits->count() > 0)
                        <div class="space-y-3">
                            @foreach($upcomingVisits as $visit)
                                @php
                                    $booking = $visit->booking;
                                    $pet = $booking?->pet;
                                    $owner = $booking?->owner;
                                    $ownerName = trim(($owner?->f_name ?? '') . ' ' . ($owner?->l_name ?? '')) ?: __('messages.sd_owner_default');

                                    $scheduled = $visit->scheduled_datetime;

                                    if ($scheduled->isToday()) {
                                        $dateLabel = __('messages.sd_today');
                                        $dateColor = 'text-primary';
                                    } elseif ($scheduled->isTomorrow()) {
                                        $dateLabel = __('messages.sd_tomorrow');
                                        $dateColor = 'text-amber-600';
                                    } else {
                                        $dateLabel = $scheduled->format('M j');
                                        $dateColor = 'text-blue-600';
                                    }
                                @endphp

                                <div class="flex items-center justify-between gap-3 py-2 {{ !$loop->last ? 'border-b border-gray-100 dark:border-neutral-800' : '' }}">
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-[#1B3B36] dark:text-white truncate">
                                            {{ $pet->name ?? __('messages.sd_pet_default') }}
                                        </p>
                                        <p class="text-[10px] text-neutral-400 truncate">
                                            {{ $ownerName }} • {{ $booking->visit_per_day ?? 1 }} {{ __('messages.sd_visits_per_day') }}
                                        </p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <p class="text-xs font-bold {{ $dateColor }}">{{ $dateLabel }}</p>
                                        <p class="text-[10px] text-neutral-400">{{ $scheduled->format('g:i A') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex items-center justify-center h-32">
                            <p class="text-xs text-neutral-400 italic">{{ __('messages.sd_no_upcoming_visits') }}</p>
                        </div>
                    @endif
                </div>

            </div>

            <!-- ========================================== -->
            <!-- PERFORMANCE STATS                         -->
            <!-- ========================================== -->
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mt-4 sm:mt-6">

                {{-- Average Rating --}}
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 p-4 text-center hover:shadow-md transition">
                    <p class="text-xl sm:text-2xl font-black text-[#1B3B36] dark:text-white">
                        {{ number_format($averageRating, 1) }}
                    </p>
                    <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sd_avg_rating') }}</p>
                    <div class="flex items-center justify-center gap-0.5 mt-1">
                        @php
                            $fullStars = floor($averageRating);
                        @endphp
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-3 h-3 {{ $i <= $fullStars ? 'text-amber-400' : 'text-neutral-300 dark:text-neutral-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>
                    <p class="text-[10px] text-neutral-400 mt-1">{{ __('messages.sd_from_reviews', ['count' => $reviewsCount]) }}</p>
                </div>

                {{-- Completion Rate --}}
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 p-4 text-center hover:shadow-md transition">
                    <p class="text-xl sm:text-2xl font-black text-[#1B3B36] dark:text-white">{{ $completionRate }}%</p>
                    <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sd_completion_rate') }}</p>
                    <p class="text-[10px] text-green-600 dark:text-green-400 mt-1">
                        {{ __('messages.sd_visits_completed', ['done' => $completedVisits, 'total' => $totalVisits]) }}
                    </p>
                </div>

                {{-- Active Bookings --}}
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 p-4 text-center hover:shadow-md transition">
                    <p class="text-xl sm:text-2xl font-black text-[#1B3B36] dark:text-white">{{ $activeBookings }}</p>
                    <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sd_active_bookings') }}</p>
                    <p class="text-[10px] text-neutral-400 mt-1">{{ __('messages.sd_owners_count', ['count' => $uniqueOwners]) }}</p>
                </div>

                {{-- Total Visits --}}
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 p-4 text-center hover:shadow-md transition">
                    <p class="text-xl sm:text-2xl font-black text-[#1B3B36] dark:text-white">{{ $totalVisitsLast30Days }}</p>
                    <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sd_total_visits') }}</p>
                    <p class="text-[10px] text-neutral-400 mt-1">{{ __('messages.sd_last_30_days') }}</p>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>