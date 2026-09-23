@php
    $fmt = fn ($n) => number_format((float) $n);
    $trendMax = collect($bookingTrend)->max('count') ?: 1;

    // Localized range labels
    $rangeLabels = [
        'today' => __('messages.analytics_range_today'),
        'week'  => __('messages.analytics_range_week'),
        'month' => __('messages.analytics_range_month'),
        'year'  => __('messages.analytics_range_year'),
    ];
    $rangeLabel = $rangeLabels[$range ?? 'month'] ?? __('messages.analytics_range_month');
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between w-full gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.dashboard') }}"
                   class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div class="min-w-0">
                    <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                        {{ __('messages.analytics_title') }}
                    </h1>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.analytics_subtitle') }}</p>
                </div>
            </div>

            {{-- Date range filter --}}
            <div x-data="{ range: '{{ $range ?? 'month' }}' }"
                 class="flex items-center gap-1.5 sm:gap-2 overflow-x-auto pb-1 sm:pb-0 -mx-1 px-1 sm:mx-0 sm:px-0">
                @foreach (['today', 'week', 'month', 'year'] as $key)
                    <a href="{{ route('admin.analytics', ['range' => $key]) }}"
                       class="px-2.5 sm:px-3 py-1.5 text-[10px] sm:text-xs font-bold rounded-lg transition whitespace-nowrap
                              {{ ($range ?? 'month') === $key
                                    ? 'bg-primary text-white shadow-md'
                                    : 'text-neutral-600 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800' }}">
                        {{ $rangeLabels[$key] }}
                    </a>
                @endforeach

                <a href="{{ route('admin.analytics', ['range' => $range ?? 'month']) }}"
                   class="inline-flex items-center gap-1 sm:gap-1.5 px-2.5 sm:px-3 py-1.5 bg-primary hover:bg-primary-600 text-white font-bold text-[10px] sm:text-xs rounded-lg shadow-md hover:shadow-lg transition whitespace-nowrap shrink-0">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    {{ __('messages.analytics_refresh') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div x-data="{ mounted: false }"
         x-init="$nextTick(() => setTimeout(() => mounted = true, 80))"
         class="py-4 sm:py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200">
        <div class="w-full sm:max-w-7xl mx-auto px-2 sm:px-16 lg:px-24">

            <!-- ① KPI CARDS (Row 1) -->
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 mb-3 sm:mb-4">

                {{-- Total Users --}}
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.analytics_total_users') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-[#1B3B36] dark:text-white mt-1">{{ $fmt($kpis['total_users']) }}</h3>
                            <p class="text-[10px] sm:text-xs {{ $kpis['user_growth'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} mt-1">
                                {{ $kpis['user_growth'] >= 0 ? '↑' : '↓' }} {{ abs($kpis['user_growth']) }} {{ __('messages.analytics_growth_this', ['range' => $rangeLabel]) }}
                            </p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-primary/10 flex items-center justify-center text-primary shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Active Sitters --}}
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.analytics_active_sitters') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-[#1B3B36] dark:text-white mt-1">{{ $fmt($kpis['active_sitters']) }}</h3>
                            <p class="text-[10px] sm:text-xs text-green-600 dark:text-green-400 mt-1">{{ __('messages.analytics_verified_pct', ['pct' => $kpis['verified_sitter_pct']]) }}</p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-amber-100/20 flex items-center justify-center text-amber-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Total Bookings --}}
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition col-span-2 sm:col-span-1">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.booking_total') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-[#1B3B36] dark:text-white mt-1">{{ $fmt($kpis['total_bookings']) }}</h3>
                            <p class="text-[10px] sm:text-xs {{ $kpis['booking_growth'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} mt-1">
                                {{ $kpis['booking_growth'] >= 0 ? '↑' : '↓' }} {{ abs($kpis['booking_growth']) }}%
                            </p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-blue-100/20 flex items-center justify-center text-blue-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Row 2 KPI -->
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 mb-4 sm:mb-6">

                {{-- Completed --}}
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.status_completed') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-green-600 dark:text-green-400 mt-1">{{ $fmt($kpis['completed_bookings']) }}</h3>
                            <p class="text-[10px] sm:text-xs text-green-600 dark:text-green-400 mt-1">{{ $kpis['completion_rate'] }}%</p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-green-100/20 flex items-center justify-center text-green-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Cancelled --}}
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.status_cancelled') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-red-600 dark:text-red-400 mt-1">{{ $fmt($kpis['cancelled_bookings']) }}</h3>
                            <p class="text-[10px] sm:text-xs text-red-500 dark:text-red-400 mt-1">{{ $kpis['cancellation_rate'] }}%</p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-red-100/20 flex items-center justify-center text-red-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Complaints --}}
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition col-span-2 sm:col-span-1">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.admin_complaints') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-amber-600 dark:text-amber-400 mt-1">{{ $fmt($kpis['total_complaints']) }}</h3>
                            <p class="text-[10px] sm:text-xs text-amber-600 dark:text-amber-400 mt-1">
                                {{ __('messages.analytics_complaints_pending', ['n' => $kpis['pending_complaints']]) }}
                            </p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-amber-100/20 flex items-center justify-center text-amber-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ② BOOKING ANALYTICS -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 mb-4 sm:mb-6">

                <!-- Booking Trend Chart (animated bars) -->
                <div class="lg:col-span-2 bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white">{{ __('messages.analytics_booking_trend') }}</h3>
                        <span class="text-xs text-neutral-400">{{ __('messages.analytics_last_n_months', ['n' => count($bookingTrend)]) }}</span>
                    </div>
                    <div class="h-40 sm:h-48 relative">
                        <div class="flex items-end justify-between h-32 sm:h-40 gap-1.5 sm:gap-2 pt-4">
                            @foreach ($bookingTrend as $i => $t)
                                @php
                                    $pct = $trendMax > 0 ? max(4, round(($t['count'] / $trendMax) * 100)) : 4;
                                @endphp
                                <div class="flex flex-col items-center gap-1 flex-1 h-full justify-end">
                                    <div class="w-full bg-primary/{{ 20 + min(60, $i * 10) }} rounded-t-lg transition-all duration-[900ms] ease-out origin-bottom"
                                         :style="mounted ? 'height: {{ $pct }}%; opacity: 1;' : 'height: 0%; opacity: 0.2;'"
                                         style="height: 0%; opacity: 0.2;"
                                         title="{{ $t['count'] }} bookings"></div>
                                    <span class="text-[10px] text-neutral-400">{{ $t['month'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Booking Status bars (animated) -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6">
                    <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white mb-4">{{ __('messages.analytics_booking_status') }}</h3>
                    <div class="space-y-3">
                        @php
                            $statusTotal = max(1, $bookingStatus['completed'] + $bookingStatus['pending'] + $bookingStatus['cancelled']);
                        @endphp
                        @foreach ([
                            ['label' => __('messages.status_completed'), 'value' => $bookingStatus['completed'], 'color' => 'bg-green-500'],
                            ['label' => __('messages.status_pending'),   'value' => $bookingStatus['pending'],   'color' => 'bg-amber-500'],
                            ['label' => __('messages.status_cancelled'), 'value' => $bookingStatus['cancelled'], 'color' => 'bg-red-500'],
                        ] as $row)
                            @php $pct = round(($row['value'] / $statusTotal) * 100, 1); @endphp
                            <div>
                                <div class="flex justify-between gap-2 text-sm">
                                    <span class="text-neutral-600 dark:text-neutral-400">{{ $row['label'] }}</span>
                                    <span class="font-bold text-[#1B3B36] dark:text-white shrink-0">{{ $fmt($row['value']) }}</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-neutral-700 rounded-full h-2.5 mt-1 overflow-hidden">
                                    <div class="{{ $row['color'] }} rounded-full h-2.5 transition-all duration-[900ms] ease-out"
                                         :style="mounted ? 'width: {{ $pct }}%;' : 'width: 0%;'"
                                         style="width: 0%;"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            <!-- ④ USER ANALYTICS -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 mb-4 sm:mb-6">

                <!-- User Growth (animated) -->
                <div class="lg:col-span-2 bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6">
                    <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white mb-4">{{ __('messages.analytics_user_growth') }}</h3>
                    <div class="space-y-3">
                        @php $totalUsers = max(1, $userGrowthData['owners'] + $userGrowthData['sitters'] + $userGrowthData['admins']); @endphp
                            @foreach ([
                                ['label' => __('messages.analytics_label_owners'),  'value' => $userGrowthData['owners'],  'color' => 'bg-blue-500'],
                                ['label' => __('messages.analytics_label_sitters'), 'value' => $userGrowthData['sitters'], 'color' => 'bg-amber-500'],
                                ['label' => __('messages.analytics_label_admins'),  'value' => $userGrowthData['admins'],  'color' => 'bg-primary'],
                            ] as $row)
                            @php $pct = round(($row['value'] / $totalUsers) * 100, 1); @endphp
                            <div>
                                <div class="flex justify-between gap-2 text-sm">
                                    <span class="text-neutral-600 dark:text-neutral-400">{{ $row['label'] }}</span>
                                    <span class="font-bold text-[#1B3B36] dark:text-white shrink-0">{{ $fmt($row['value']) }}</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-neutral-700 rounded-full h-3 mt-1 overflow-hidden">
                                    <div class="{{ $row['color'] }} rounded-full h-3 transition-all duration-[900ms] ease-out"
                                         :style="mounted ? 'width: {{ $pct }}%;' : 'width: 0%;'"
                                         style="width: 0%;"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Verified Accounts (animated) -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6">
                    <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white mb-4">{{ __('messages.analytics_verified_accounts') }}</h3>
                    <div class="space-y-3">
                        @foreach ([
                            ['label' => __('messages.analytics_label_verified'), 'value' => $verification['verified'], 'pct' => $verification['verified_pct'], 'color' => 'bg-green-500', 'text' => 'text-green-600 dark:text-green-400'],
                            ['label' => __('messages.status_pending'),          'value' => $verification['pending'],  'pct' => $verification['pending_pct'],  'color' => 'bg-amber-500', 'text' => 'text-amber-600 dark:text-amber-400'],
                        ] as $row)
                            <div>
                                <div class="flex justify-between gap-2 text-sm">
                                    <span class="text-neutral-600 dark:text-neutral-400">{{ $row['label'] }}</span>
                                    <span class="font-bold {{ $row['text'] }} shrink-0">{{ $row['pct'] }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-neutral-700 rounded-full h-2.5 mt-1 overflow-hidden">
                                    <div class="{{ $row['color'] }} rounded-full h-2.5 transition-all duration-[900ms] ease-out"
                                         :style="mounted ? 'width: {{ $row['pct'] }}%;' : 'width: 0%;'"
                                         style="width: 0%;"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            <!-- ⑤ BOOKING DISTRIBUTION -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 mb-4 sm:mb-6">

                <!-- By Pet Type -->
                <div class="lg:col-span-2 bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6">
                    <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white mb-4">{{ __('messages.analytics_by_pet_type') }}</h3>
                    <div class="space-y-3">
                        @php $petTotal = max(1, $petType['dogs'] + $petType['cats'] + $petType['others']); @endphp
                        @foreach ([
                            ['label' => __('messages.analytics_label_dogs'),   'value' => $petType['dogs'],   'color' => 'bg-blue-500'],
                            ['label' => __('messages.analytics_label_cats'),   'value' => $petType['cats'],   'color' => 'bg-amber-500'],
                            ['label' => __('messages.analytics_label_others'), 'value' => $petType['others'], 'color' => 'bg-primary'],
                        ] as $row)
                            @php $pct = round(($row['value'] / $petTotal) * 100, 1); @endphp
                            <div>
                                <div class="flex justify-between gap-2 text-sm">
                                    <span class="text-neutral-600 dark:text-neutral-400">{{ $row['label'] }}</span>
                                    <span class="font-bold text-[#1B3B36] dark:text-white shrink-0">{{ $fmt($row['value']) }}</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-neutral-700 rounded-full h-3 mt-1 overflow-hidden">
                                    <div class="{{ $row['color'] }} rounded-full h-3 transition-all duration-[900ms] ease-out"
                                         :style="mounted ? 'width: {{ $pct }}%;' : 'width: 0%;'"
                                         style="width: 0%;"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- By Schedule -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6">
                    <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white mb-4">{{ __('messages.analytics_by_schedule') }}</h3>
                    <div class="space-y-3">
                        @php $schedTotal = max(1, $schedule['morning'] + $schedule['afternoon'] + $schedule['evening']); @endphp
                        @foreach ([
                            ['label' => __('messages.analytics_label_morning'),   'value' => $schedule['morning'],   'color' => 'bg-primary/50'],
                            ['label' => __('messages.analytics_label_afternoon'), 'value' => $schedule['afternoon'], 'color' => 'bg-primary/70'],
                            ['label' => __('messages.analytics_label_evening'),   'value' => $schedule['evening'],   'color' => 'bg-primary'],
                        ] as $row)
                            @php $pct = round(($row['value'] / $schedTotal) * 100, 1); @endphp
                            <div>
                                <div class="flex justify-between gap-2 text-sm">
                                    <span class="text-neutral-600 dark:text-neutral-400">{{ $row['label'] }}</span>
                                    <span class="font-bold text-[#1B3B36] dark:text-white shrink-0">{{ $fmt($row['value']) }}</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-neutral-700 rounded-full h-2.5 mt-1 overflow-hidden">
                                    <div class="{{ $row['color'] }} rounded-full h-2.5 transition-all duration-[900ms] ease-out"
                                         :style="mounted ? 'width: {{ $pct }}%;' : 'width: 0%;'"
                                         style="width: 0%;"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            <!-- ⑥ COMPLAINT ANALYTICS -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 mb-4 sm:mb-6">

                <div class="lg:col-span-2 bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6">
                    <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white mb-4">{{ __('messages.analytics_complaint_dist') }}</h3>
                    @if (count($complaintDistribution) > 0)
                        <div class="space-y-3">
                            @foreach ($complaintDistribution as $d)
                                <div>
                                    <div class="flex justify-between gap-2 text-sm">
                                        <span class="text-neutral-600 dark:text-neutral-400">{{ $d['label'] }}</span>
                                        <span class="font-bold text-[#1B3B36] dark:text-white shrink-0">{{ $d['pct'] }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-neutral-700 rounded-full h-3 mt-1 overflow-hidden">
                                        <div class="{{ $d['color'] }} rounded-full h-3 transition-all duration-[900ms] ease-out"
                                             :style="mounted ? 'width: {{ $d['pct'] }}%;' : 'width: 0%;'"
                                             style="width: 0%;"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-neutral-400 text-center py-6">{{ __('messages.analytics_no_complaint_data') }}</p>
                    @endif
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6">
                    <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white mb-4">{{ __('messages.analytics_complaint_status') }}</h3>
                    <div class="space-y-3">
                        @php $compTotal = max(1, $complaintStatus['resolved'] + $complaintStatus['pending'] + $complaintStatus['dismissed']); @endphp
                        @foreach ([
                            ['label' => __('messages.analytics_label_resolved'),  'value' => $complaintStatus['resolved'],  'color' => 'bg-green-500', 'text' => 'text-green-600 dark:text-green-400'],
                            ['label' => __('messages.status_pending'),            'value' => $complaintStatus['pending'],   'color' => 'bg-amber-500', 'text' => 'text-amber-600 dark:text-amber-400'],
                            ['label' => __('messages.analytics_label_dismissed'), 'value' => $complaintStatus['dismissed'], 'color' => 'bg-red-500',   'text' => 'text-red-600 dark:text-red-400'],
                        ] as $row)
                            @php $pct = round(($row['value'] / $compTotal) * 100, 1); @endphp
                            <div>
                                <div class="flex justify-between gap-2 text-sm">
                                    <span class="text-neutral-600 dark:text-neutral-400">{{ $row['label'] }}</span>
                                    <span class="font-bold {{ $row['text'] }} shrink-0">{{ $fmt($row['value']) }}</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-neutral-700 rounded-full h-2.5 mt-1 overflow-hidden">
                                    <div class="{{ $row['color'] }} rounded-full h-2.5 transition-all duration-[900ms] ease-out"
                                         :style="mounted ? 'width: {{ $pct }}%;' : 'width: 0%;'"
                                         style="width: 0%;"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            <!-- ⑧ TOP PERFORMING PET SITTERS -->
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 overflow-hidden mb-4 sm:mb-6">
                <div class="px-4 sm:px-5 py-4 border-b border-gray-100 dark:border-neutral-800 flex items-center justify-between">
                    <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white">{{ __('messages.analytics_top_sitters') }}</h3>
                    <a href="{{ route('admin.users') }}" class="text-xs text-primary font-semibold hover:underline">{{ __('messages.admin_view_all') }}</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs sm:text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-800/30">
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400">{{ __('messages.analytics_col_rank') }}</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400">{{ __('messages.analytics_col_sitter') }}</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400">{{ __('messages.analytics_col_completed') }}</th>
                                <th class="text-right py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400">{{ __('messages.analytics_col_rating') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($topSitters as $i => $s)
                                <tr class="border-b border-gray-100 dark:border-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition">
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4 font-bold text-[#1B3B36] dark:text-white">#{{ $i + 1 }}</td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs shrink-0">
                                                {{ strtoupper(substr($s->f_name ?? 'U', 0, 1)) }}
                                            </div>
                                            <span class="font-bold text-[#1B3B36] dark:text-white truncate">
                                                {{ trim(($s->f_name ?? '') . ' ' . ($s->l_name ?? '')) ?: '—' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4 font-bold text-[#1B3B36] dark:text-white">{{ $s->completed_count }}</td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4 text-right">
                                        <span class="text-amber-600 dark:text-amber-400 font-bold">{{ number_format($s->average_rating ?? 0, 1) }} ⭐</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-10 text-center text-neutral-500 dark:text-neutral-400 text-sm">
                                        {{ __('messages.analytics_no_sitter_data') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ⑨ MOST ACTIVE PET OWNERS -->
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 overflow-hidden mb-4 sm:mb-6">
                <div class="px-4 sm:px-5 py-4 border-b border-gray-100 dark:border-neutral-800 flex items-center justify-between">
                    <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white">{{ __('messages.analytics_most_active_owners') }}</h3>
                    <a href="{{ route('admin.users') }}" class="text-xs text-primary font-semibold hover:underline">{{ __('messages.admin_view_all') }}</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs sm:text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-800/30">
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400">{{ __('messages.analytics_col_owner') }}</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400">{{ __('messages.analytics_col_bookings') }}</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400">{{ __('messages.analytics_col_completed') }}</th>
                                <th class="text-right py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400">{{ __('messages.analytics_col_rate') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($topOwners as $o)
                                <tr class="border-b border-gray-100 dark:border-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition">
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-full bg-blue-100/20 flex items-center justify-center text-blue-600 font-bold text-xs shrink-0">
                                                {{ strtoupper(substr($o->f_name ?? 'U', 0, 1)) }}
                                            </div>
                                            <span class="font-bold text-[#1B3B36] dark:text-white truncate">
                                                {{ trim(($o->f_name ?? '') . ' ' . ($o->l_name ?? '')) ?: '—' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4 font-bold text-[#1B3B36] dark:text-white">{{ $o->booking_count }}</td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4 text-green-600 dark:text-green-400 font-bold">{{ $o->completed_count }}</td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4 text-right font-bold text-[#1B3B36] dark:text-white">
                                        {{ $o->booking_count > 0 ? round(($o->completed_count / $o->booking_count) * 100, 1) : 0 }}%
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-10 text-center text-neutral-500 dark:text-neutral-400 text-sm">
                                        {{ __('messages.analytics_no_owner_data') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ⑩ RECENT ACTIVITIES + PLATFORM HEALTH -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 mb-4 sm:mb-6">

                <!-- Recent Activities -->
                <div class="lg:col-span-2 bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6">
                    <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white mb-4">{{ __('messages.analytics_recent_activities') }}</h3>
                    @if (count($recentActivities) > 0)
                        <div class="space-y-3">
                            @foreach ($recentActivities as $act)
                                <div class="flex items-start gap-2 sm:gap-3 pb-3 border-b border-gray-100 dark:border-neutral-800 last:border-0 last:pb-0">
                                    <div class="w-8 h-8 rounded-full {{ $act['icon_bg'] }} flex items-center justify-center {{ $act['icon_color'] }} shrink-0">
                                        {!! $act['icon'] !!}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs sm:text-sm text-[#1B3B36] dark:text-white font-medium truncate">{{ $act['title'] }}</p>
                                        <p class="text-[10px] sm:text-xs text-neutral-400">{{ $act['time'] }}</p>
                                    </div>
                                    <span class="text-[10px] sm:text-xs {{ $act['badge_color'] }} font-medium shrink-0">{{ $act['badge'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-neutral-400 text-center py-6">{{ __('messages.analytics_no_recent_acts') }}</p>
                    @endif
                </div>

                <!-- Platform Health -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6">
                    <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white mb-4">{{ __('messages.analytics_platform_health') }}</h3>
                    <div class="space-y-4">
                        @foreach ($health as $h)
                            <div>
                                <div class="flex justify-between gap-2 text-sm">
                                    <span class="text-neutral-600 dark:text-neutral-400">{{ $h['label'] }}</span>
                                    <span class="font-bold {{ $h['text'] }} shrink-0">{{ $h['display'] }}</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-neutral-700 rounded-full h-2 mt-1 overflow-hidden">
                                    <div class="{{ $h['color'] }} rounded-full h-2 transition-all duration-[900ms] ease-out"
                                         :style="mounted ? 'width: {{ $h['pct'] }}%;' : 'width: 0%;'"
                                         style="width: 0%;"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            <!-- ⑪ QUICK INSIGHTS -->
            <div class="bg-blue-50 dark:bg-blue-950/20 rounded-2xl border border-blue-200 dark:border-blue-800/50 p-4 sm:p-6 mb-4 sm:mb-6">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="min-w-0">
                        <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white">{{ __('messages.analytics_platform_insights') }}</h3>
                        <ul class="mt-2 space-y-1 text-xs sm:text-sm text-neutral-600 dark:text-neutral-300">
                            @foreach ($insights as $insight)
                                <li>• {!! $insight !!}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <!-- ⑫ FOOTER -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs text-neutral-500 dark:text-neutral-400 pt-2">
                <div>
                    <span class="font-medium">{{ __('messages.analytics_last_updated') }}</span>
                    <span class="font-bold text-[#1B3B36] dark:text-white">{{ now()->format('F j, Y g:i A') }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-block w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                    <span class="font-medium">{{ __('messages.analytics_auto_refresh') }}</span>
                    <a href="{{ route('admin.analytics', ['range' => $range ?? 'month']) }}"
                       class="inline-flex items-center gap-1.5 px-2.5 py-1.5 sm:px-3 sm:py-1.5 bg-primary/10 text-primary font-bold text-xs rounded-lg hover:bg-primary/20 transition">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        {{ __('messages.analytics_refresh') }}
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>