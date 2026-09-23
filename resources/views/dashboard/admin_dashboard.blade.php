<x-app-layout>
    <!-- HEADER SLOT -->
    <x-slot name="header">
        <div x-data="{ selectedMonth: '{{ now()->format('F Y') }}' }" class="flex flex-col sm:flex-row sm:items-center sm:justify-between w-full gap-3">
            <div>
                <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                    {{ __('messages.admin_dashboard_title') }}
                </h1>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.admin_dashboard_subtitle') }}</p>
            </div>

            <div class="flex items-center gap-2">
                <span class="text-xs sm:text-sm text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.admin_filter_label') }}</span>
                <select x-model="selectedMonth"
                        class="rounded-xl border border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-1.5 text-xs sm:text-sm font-medium text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary">
                    @for($i = 0; $i < 8; $i++)
                        @php $month = now()->startOfYear()->addMonths($i); @endphp
                        <option @selected($month->isSameMonth(now()))>{{ $month->format('F Y') }}</option>
                    @endfor
                </select>
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200">
        <div class="w-full sm:max-w-7xl mx-auto px-2 sm:px-16 lg:px-24">

            <!-- ROW 1: SUMMARY CARDS -->
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-3 sm:mb-4">

                <!-- Total Users -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.admin_total_users') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-[#1B3B36] dark:text-white mt-1">{{ $totalUsers }}</h3>
                            <p class="text-[10px] sm:text-xs text-green-600 dark:text-green-400 mt-1">{{ __('messages.admin_all_accounts') }}</p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-primary/10 flex items-center justify-center text-primary shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Pet Owners -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.admin_pet_owners') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-[#1B3B36] dark:text-white mt-1">{{ $totalOwners }}</h3>
                            <p class="text-[10px] sm:text-xs text-green-600 dark:text-green-400 mt-1">{{ __('messages.admin_registered') }}</p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-blue-100/20 flex items-center justify-center text-blue-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Pet Sitters -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.admin_pet_sitters') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-[#1B3B36] dark:text-white mt-1">{{ $totalSitters }}</h3>
                            <p class="text-[10px] sm:text-xs text-green-600 dark:text-green-400 mt-1">{{ __('messages.admin_verified') }}</p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-amber-100/20 flex items-center justify-center text-amber-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Pending Verification -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.admin_pending_verify') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-amber-600 dark:text-amber-400 mt-1">{{ $pendingVerification }}</h3>
                            <p class="text-[10px] sm:text-xs text-amber-600 dark:text-amber-400 mt-1">⏳ {{ __('messages.admin_review_short') }}</p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-amber-100/20 flex items-center justify-center text-amber-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ROW 2: 3 CARDS -->
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 mb-3 sm:mb-4">

                <!-- Active Bookings -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.admin_active_bookings') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-[#1B3B36] dark:text-white mt-1">{{ $activeBookings }}</h3>
                            <p class="text-[10px] sm:text-xs text-blue-600 dark:text-blue-400 mt-1">🟢 {{ __('messages.admin_ongoing') }}</p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-blue-100/20 flex items-center justify-center text-blue-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Pending Complaints -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.admin_complaints') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-red-600 dark:text-red-400 mt-1">{{ $pendingComplaints }}</h3>
                            <p class="text-[10px] sm:text-xs text-red-500 dark:text-red-400 mt-1">⚠️ {{ __('messages.admin_attention') }}</p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-red-100/20 flex items-center justify-center text-red-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Average Rating -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition col-span-2 sm:col-span-1">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.admin_avg_rating') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-amber-600 dark:text-amber-400 mt-1">{{ number_format($averageRating, 1) }} ★</h3>
                            <p class="text-[10px] sm:text-xs text-neutral-400 mt-1">{{ __('messages.admin_reviews_count', ['count' => $totalReviews]) }}</p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-amber-100/20 flex items-center justify-center text-amber-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ROW 3: SUMMARY CARDS -->
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-6">

                <!-- Cancelled Bookings -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.admin_cancelled') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-red-600 dark:text-red-400 mt-1">{{ $cancelledBookings }}</h3>
                            <p class="text-[10px] sm:text-xs text-red-500 dark:text-red-400 mt-1">{{ __('messages.admin_all_time') }}</p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-red-100/20 flex items-center justify-center text-red-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Bookings -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.admin_total_bookings') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-[#1B3B36] dark:text-white mt-1">{{ $totalBookings }}</h3>
                            <p class="text-[10px] sm:text-xs text-green-600 dark:text-green-400 mt-1">{{ __('messages.admin_all_time') }}</p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-indigo-100/20 flex items-center justify-center text-indigo-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Completed Bookings -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.admin_completed') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-green-600 dark:text-green-400 mt-1">{{ $completedBookings }}</h3>
                            <p class="text-[10px] sm:text-xs text-green-600 dark:text-green-400 mt-1">{{ __('messages.admin_all_time') }}</p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-green-100/20 flex items-center justify-center text-green-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- New Sitters This Month -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.admin_new_sitters') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-[#1B3B36] dark:text-white mt-1">{{ $newSittersThisMonth }}</h3>
                            <p class="text-[10px] sm:text-xs text-neutral-400 mt-1">{{ __('messages.admin_this_month') }}</p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-purple-100/20 flex items-center justify-center text-purple-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

            </div>

            <!-- CHARTS ROW — Booking Trend -->
            <div class="grid grid-cols-1 gap-4 sm:gap-6 mb-4 sm:mb-6">
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white">{{ __('messages.admin_booking_trend') }}</h3>
                        <span class="text-xs text-neutral-400">{{ __('messages.admin_last_7_months') }}</span>
                    </div>

                    <div class="relative h-40 sm:h-48">
                        <div class="absolute inset-0 flex items-end justify-between gap-1.5 sm:gap-2">
                            @foreach($monthlyTrend as $index => $month)
                                @php
                                    $barPercent = $maxTrend > 0
                                        ? max(8, round(($month['count'] / $maxTrend) * 100))
                                        : 8;

                                    $opacityMap = [30, 45, 60, 75, 90, 100, 100];
                                    $opacity = $opacityMap[$index] ?? 100;
                                @endphp
                                <div class="w-2 rounded-full transition-all"
                                     style="height: {{ $barPercent }}%; background-color: rgb(240 122 58 / {{ $opacity / 100 }});"
                                     title="{{ $month['count'] }} bookings"></div>
                            @endforeach
                        </div>

                        @php
                            $count = count($monthlyTrend);
                            $points = [];
                            $circles = [];

                            foreach ($monthlyTrend as $i => $m) {
                                $x = $count > 1 ? (($i + 0.5) / $count) * 100 : 50;
                                $barPercent = $maxTrend > 0
                                    ? max(8, ($m['count'] / $maxTrend) * 100)
                                    : 8;
                                $y = 100 - $barPercent;

                                $points[]  = round($x, 2) . ',' . round($y, 2);
                                $circles[] = ['x' => round($x, 2), 'y' => round($y, 2)];
                            }
                            $polylineStr = implode(' ', $points);
                        @endphp

                        <svg class="absolute inset-0 w-full h-full pointer-events-none"
                             viewBox="0 0 100 100"
                             preserveAspectRatio="none">
                            <polyline points="{{ $polylineStr }}"
                                      fill="none"
                                      stroke="#F07A3A"
                                      stroke-width="2"
                                      vector-effect="non-scaling-stroke"
                                      stroke-linecap="round"
                                      stroke-linejoin="round"/>
                            @foreach($circles as $c)
                                <circle cx="{{ $c['x'] }}" cy="{{ $c['y'] }}"
                                        r="2"
                                        fill="#F07A3A"
                                        vector-effect="non-scaling-stroke"/>
                            @endforeach
                        </svg>
                    </div>

                    <div class="flex justify-between gap-1.5 sm:gap-2 mt-3">
                        @foreach($monthlyTrend as $month)
                            <span class="flex-1 text-center text-[10px] text-neutral-400">{{ $month['label'] }}</span>
                        @endforeach
                    </div>

                    <div class="flex justify-between text-[10px] text-neutral-400 mt-1">
                        <span>0</span>
                        <span>{{ round($maxTrend / 4) }}</span>
                        <span>{{ round($maxTrend / 2) }}</span>
                        <span>{{ round($maxTrend * 3 / 4) }}</span>
                        <span>{{ $maxTrend }}</span>
                    </div>
                </div>
            </div>

            <!-- BOOKING STATUS + ACTIVITY PANEL -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 mb-4 sm:mb-6">

                <!-- Booking Status Distribution -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6">
                    <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white mb-4">{{ __('messages.admin_status_distribution') }}</h3>
                    <div class="flex flex-col sm:flex-row items-center gap-4 sm:gap-6">
                        @php
                            $p1 = $statusPercentages['completed'];
                            $p2 = $p1 + $statusPercentages['pending'];
                            $p3 = $p2 + $statusPercentages['cancelled'];
                        @endphp
                        <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-full relative flex items-center justify-center shrink-0"
                             style="background: conic-gradient(
                                 #F07A3A 0% {{ $p1 }}%,
                                 #f59e0b {{ $p1 }}% {{ $p2 }}%,
                                 #ef4444 {{ $p2 }}% {{ $p3 }}%,
                                 #3b82f6 {{ $p3 }}% 100%
                             );">
                            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-white dark:bg-neutral-900"></div>
                        </div>
                        <div class="space-y-1.5 text-sm">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-primary shrink-0"></span>
                                <span class="text-neutral-600 dark:text-neutral-300">{{ __('messages.status_completed') }} <span class="font-bold text-[#1B3B36] dark:text-white">{{ $statusPercentages['completed'] }}%</span></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-amber-500 shrink-0"></span>
                                <span class="text-neutral-600 dark:text-neutral-300">{{ __('messages.status_pending') }} <span class="font-bold text-[#1B3B36] dark:text-white">{{ $statusPercentages['pending'] }}%</span></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-red-500 shrink-0"></span>
                                <span class="text-neutral-600 dark:text-neutral-300">{{ __('messages.status_cancelled') }} <span class="font-bold text-[#1B3B36] dark:text-white">{{ $statusPercentages['cancelled'] }}%</span></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-blue-500 shrink-0"></span>
                                <span class="text-neutral-600 dark:text-neutral-300">{{ __('messages.status_in_progress') }} <span class="font-bold text-[#1B3B36] dark:text-white">{{ $statusPercentages['in_progress'] }}%</span></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6 lg:col-span-2">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white">{{ __('messages.admin_recent_activities') }}</h3>
                        <a href="{{ route('admin.complaints') }}" class="text-xs text-primary font-semibold hover:underline">{{ __('messages.admin_view_all') }}</a>
                    </div>
                    <div class="space-y-3">
                        @forelse($recentActivities as $activity)
                            @php
                                $colorMap = [
                                    'green' => ['bg-green-100/20', 'text-green-600'],
                                    'amber' => ['bg-amber-100/20', 'text-amber-600'],
                                    'blue'  => ['bg-blue-100/20',  'text-blue-600'],
                                    'red'   => ['bg-red-100/20',   'text-red-600'],
                                ];
                                [$bgColor, $textColor] = $colorMap[$activity['color']] ?? $colorMap['blue'];
                            @endphp
                            <div class="flex items-center gap-2 sm:gap-3 {{ !$loop->last ? 'pb-2 border-b border-gray-100 dark:border-neutral-800' : '' }}">
                                <div class="w-8 h-8 rounded-full {{ $bgColor }} flex items-center justify-center {{ $textColor }} shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $activity['icon'] }}"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs sm:text-sm text-[#1B3B36] dark:text-white font-medium truncate">{{ $activity['text'] }}</p>
                                    <p class="text-[10px] sm:text-xs text-neutral-400">{{ $activity['time']->diffForHumans() }}</p>
                                </div>
                                <span class="text-[10px] sm:text-xs {{ $textColor }} font-medium shrink-0">{{ $activity['status'] }}</span>
                            </div>
                        @empty
                            <p class="text-xs text-neutral-400 text-center py-6">{{ __('messages.admin_no_recent_activities') }}</p>
                        @endforelse
                    </div>
                </div>

            </div>

            <!-- RECENT BOOKING REQUESTS -->
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6 mb-4 sm:mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white">{{ __('messages.admin_recent_booking_requests') }}</h3>
                    <a href="{{ route('admin.bookings') }}" class="text-xs text-primary font-semibold hover:underline">{{ __('messages.admin_view_all') }}</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs sm:text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-neutral-800">
                                <th class="text-left py-2 px-2 sm:px-3 text-[10px] sm:text-xs font-bold uppercase text-neutral-400">{{ __('messages.admin_col_id') }}</th>
                                <th class="text-left py-2 px-2 sm:px-3 text-[10px] sm:text-xs font-bold uppercase text-neutral-400">{{ __('messages.admin_col_pet_owner') }}</th>
                                <th class="text-left py-2 px-2 sm:px-3 text-[10px] sm:text-xs font-bold uppercase text-neutral-400">{{ __('messages.admin_col_status') }}</th>
                                <th class="text-right py-2 px-2 sm:px-3 text-[10px] sm:text-xs font-bold uppercase text-neutral-400">{{ __('messages.admin_col_action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBookings as $booking)
                                @php
                                    $ownerName = trim(($booking->owner?->f_name ?? '') . ' ' . ($booking->owner?->l_name ?? '')) ?: 'Owner';

                                    $statusMap = [
                                        'pending'   => ['bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400', __('messages.status_pending')],
                                        'accepted'  => ['bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400', __('messages.status_confirmed')],
                                        'completed' => ['bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400', __('messages.status_completed')],
                                        'cancelled' => ['bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400', __('messages.status_cancelled')],
                                        'rejected'  => ['bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400', __('messages.status_rejected')],
                                    ];
                                    [$statusClass, $statusLabel] = $statusMap[$booking->status] ?? $statusMap['pending'];
                                @endphp
                                <tr class="border-b border-gray-100 dark:border-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition">
                                    <td class="py-2 px-2 sm:px-3 font-bold text-[#1B3B36] dark:text-white">
                                        {{ $booking->booking_reference }}
                                    </td>
                                    <td class="py-2 px-2 sm:px-3 text-neutral-600 dark:text-neutral-300">{{ $ownerName }}</td>
                                    <td class="py-2 px-2 sm:px-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $statusClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-2 sm:px-3 text-right">
                                        <a href="{{ route('admin.bookings') }}" class="text-primary font-semibold text-xs hover:underline">{{ __('messages.admin_view') }}</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-xs text-neutral-400">{{ __('messages.admin_no_bookings_yet') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- PENDING VERIFICATION + RECENT COMPLAINTS -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-4 sm:mb-6">

                <!-- Pending Sitter Verification -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white">{{ __('messages.admin_pending_verification') }}</h3>
                        <a href="{{ route('admin.verification.sitter') }}" class="text-xs text-primary font-semibold hover:underline">{{ __('messages.admin_view_all') }}</a>
                    </div>
                    <div class="space-y-3">
                        @forelse($pendingVerifications as $user)
                            @php
                                $name = trim(($user->f_name ?? '') . ' ' . ($user->l_name ?? '')) ?: 'User';
                            @endphp
                            <div class="flex items-start justify-between gap-2 p-3 rounded-xl border border-gray-100 dark:border-neutral-800">
                                <div class="min-w-0">
                                    <p class="font-bold text-[#1B3B36] dark:text-white text-sm truncate">{{ $name }}</p>
                                    <p class="text-xs text-neutral-500">{{ __('messages.admin_government_id') }}</p>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 mt-1">{{ __('messages.status_pending') }}</span>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <form method="POST" action="{{ route('admin.verification.sitter.approve', $user->id) }}">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 text-[10px] sm:text-xs font-bold bg-green-500 hover:bg-green-600 text-white rounded-lg transition">{{ __('messages.admin_approve') }}</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.verification.sitter.reject', $user->id) }}">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 text-[10px] sm:text-xs font-bold border-2 border-red-500 text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-lg transition">{{ __('messages.admin_reject') }}</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-neutral-400 text-center py-6">{{ __('messages.admin_no_pending_verifications') }}</p>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Complaints -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white">{{ __('messages.admin_recent_complaints') }}</h3>
                        <a href="{{ route('admin.complaints') }}" class="text-xs text-primary font-semibold hover:underline">{{ __('messages.admin_view_all') }}</a>
                    </div>
                    <div class="space-y-3">
                        @forelse($recentComplaints as $complaint)
                            @php
                                $complainantName = trim(($complaint->complainant?->f_name ?? '') . ' ' . ($complaint->complainant?->l_name ?? '')) ?: 'User';

                                $statusMap = [
                                    'pending'      => ['bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400', __('messages.status_pending')],
                                    'under_review' => ['bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400', __('messages.status_under_review')],
                                    'resolved'     => ['bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400', __('messages.status_resolved')],
                                    'dismissed'    => ['bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400', __('messages.status_dismissed')],
                                ];
                                [$statusClass, $statusLabel] = $statusMap[$complaint->status] ?? $statusMap['pending'];

                                $typeLabel = match($complaint->complaint_type) {
                                    'missed_visit'  => __('messages.complaint_missed_visit'),
                                    'poor_service'  => __('messages.complaint_poor_service'),
                                    'no_proof'      => __('messages.complaint_no_proof'),
                                    'rude_behavior' => __('messages.complaint_rude_behavior'),
                                    'others'        => __('messages.complaint_others'),
                                    default         => ucfirst($complaint->complaint_type ?? 'Other'),
                                };
                            @endphp
                            <div class="flex items-start justify-between gap-2 p-3 rounded-xl border border-gray-100 dark:border-neutral-800">
                                <div class="min-w-0">
                                    <p class="font-bold text-[#1B3B36] dark:text-white text-sm">CMP-{{ str_pad($complaint->id, 4, '0', STR_PAD_LEFT) }}</p>
                                    <p class="text-xs text-neutral-500 truncate">{{ $complainantName }} · {{ $typeLabel }}</p>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $statusClass }} mt-1">{{ $statusLabel }}</span>
                                </div>
                                <a href="{{ route('admin.complaints') }}" class="text-primary font-semibold text-xs hover:underline shrink-0">{{ __('messages.admin_review') }}</a>
                            </div>
                        @empty
                            <p class="text-xs text-neutral-400 text-center py-6">{{ __('messages.admin_no_recent_complaints') }}</p>
                        @endforelse
                    </div>
                </div>

            </div>

            <!-- QUICK ACTIONS -->
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6">
                <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white mb-4">{{ __('messages.admin_quick_actions') }}</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2.5 sm:gap-3">
                    <a href="{{ route('admin.verification.sitter') }}" class="flex items-center gap-2 p-2.5 sm:p-3 rounded-xl border border-gray-200 dark:border-neutral-700 hover:border-primary/50 hover:shadow-sm transition group">
                        <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary group-hover:scale-105 transition text-xs sm:text-sm shrink-0">✓</span>
                        <span class="text-xs sm:text-sm font-bold text-[#1B3B36] dark:text-white truncate">{{ __('messages.admin_verify_sitter') }}</span>
                    </a>
                    <a href="{{ route('admin.complaints') }}" class="flex items-center gap-2 p-2.5 sm:p-3 rounded-xl border border-gray-200 dark:border-neutral-700 hover:border-primary/50 hover:shadow-sm transition group">
                        <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-red-100/20 flex items-center justify-center text-red-600 group-hover:scale-105 transition text-xs sm:text-sm shrink-0">!</span>
                        <span class="text-xs sm:text-sm font-bold text-[#1B3B36] dark:text-white truncate">{{ __('messages.admin_complaints') }}</span>
                    </a>
                    <a href="{{ route('admin.users') }}" class="flex items-center gap-2 p-2.5 sm:p-3 rounded-xl border border-gray-200 dark:border-neutral-700 hover:border-primary/50 hover:shadow-sm transition group">
                        <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-blue-100/20 flex items-center justify-center text-blue-600 group-hover:scale-105 transition text-xs sm:text-sm shrink-0">👤</span>
                        <span class="text-xs sm:text-sm font-bold text-[#1B3B36] dark:text-white truncate">{{ __('messages.admin_users') }}</span>
                    </a>
                    <a href="{{ route('admin.reports') }}" class="flex items-center gap-2 p-2.5 sm:p-3 rounded-xl border border-gray-200 dark:border-neutral-700 hover:border-primary/50 hover:shadow-sm transition group">
                        <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-green-100/20 flex items-center justify-center text-green-600 group-hover:scale-105 transition text-xs sm:text-sm shrink-0">📊</span>
                        <span class="text-xs sm:text-sm font-bold text-[#1B3B36] dark:text-white truncate">{{ __('messages.admin_reports') }}</span>
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>