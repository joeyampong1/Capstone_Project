@php
    $fmt = fn ($n) => number_format((float) $n);
    $trendMax = collect($bookingTrend)->max('count') ?: 1;
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
                        {{ __('messages.reports_title') }}
                    </h1>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.reports_subtitle') }}</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div x-data="{
            reportType: 'all',
            dateFrom: '',
            dateTo: '',
            userType: 'all',
            status: 'all',

            get exportBase() {
                return '{{ route('admin.reports.export') }}';
            },
            buildExportUrl(format) {
                const params = new URLSearchParams({
                    format: format,
                    type: this.reportType === 'all' ? 'all' : this.reportType,
                });
                if (this.dateFrom) params.set('from', this.dateFrom);
                if (this.dateTo)   params.set('to',   this.dateTo);
                return this.exportBase + '?' + params.toString();
            },
            goExport(format) {
                window.location.href = this.buildExportUrl(format);
            },
            resetFilters() {
                this.reportType = 'all';
                this.dateFrom = '';
                this.dateTo = '';
                this.userType = 'all';
                this.status = 'all';
            }
         }"
         class="py-4 sm:py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200">
        <div class="w-full sm:max-w-7xl mx-auto px-2 sm:px-16 lg:px-24">

            @if (session('success'))
                <div class="mb-4 rounded-2xl border border-green-200 dark:border-green-800/50 bg-green-50 dark:bg-green-950/20 text-green-800 dark:text-green-300 px-4 py-3 text-sm font-medium">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 rounded-2xl border border-red-200 dark:border-red-800/50 bg-red-50 dark:bg-red-950/20 text-red-800 dark:text-red-300 px-4 py-3 text-sm font-medium">
                    {{ session('error') }}
                </div>
            @endif

            <!-- ① STATISTICS SUMMARY -->
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-6">

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.reports_total_users') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-[#1B3B36] dark:text-white mt-1">{{ $fmt($stats['total_users']) }}</h3>
                            <p class="text-[10px] sm:text-xs {{ ($stats['user_growth'] ?? 0) >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} mt-1">
                                {{ ($stats['user_growth'] ?? 0) >= 0 ? '↑' : '↓' }} {{ abs($stats['user_growth'] ?? 0) }} {{ __('messages.reports_growth_this_month') }}
                            </p>
                        </div>
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.reports_total_bookings') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-[#1B3B36] dark:text-white mt-1">{{ $fmt($stats['total_bookings']) }}</h3>
                            <p class="text-[10px] sm:text-xs text-blue-600 dark:text-blue-400 mt-1">{{ __('messages.reports_active_count', ['count' => $stats['active_bookings']]) }}</p>
                        </div>
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-blue-100/20 flex items-center justify-center text-blue-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.reports_completed') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-green-600 dark:text-green-400 mt-1">{{ $fmt($stats['completed_bookings']) }}</h3>
                            <p class="text-[10px] sm:text-xs text-green-600 dark:text-green-400 mt-1">
                                {{ $stats['total_bookings'] > 0 ? round(($stats['completed_bookings'] / $stats['total_bookings']) * 100, 1) : 0 }}%
                            </p>
                        </div>
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-green-100/20 flex items-center justify-center text-green-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.reports_complaints') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-amber-600 dark:text-amber-400 mt-1">{{ $fmt($stats['total_complaints']) }}</h3>
                            <p class="text-[10px] sm:text-xs text-amber-600 dark:text-amber-400 mt-1">{{ __('messages.reports_pending_count', ['count' => $stats['pending_complaints']]) }}</p>
                        </div>
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-amber-100/20 flex items-center justify-center text-amber-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ② REPORT FILTERS -->
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-5 mb-4 sm:mb-6">

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
                    <div class="col-span-2 lg:col-span-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.reports_filter_type') }}</label>
                        <select x-model="reportType"
                                class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 px-3 py-2 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary text-sm font-medium">
                            <option value="all">{{ __('messages.reports_all_reports') }}</option>
                            <option value="booking">{{ __('messages.reports_booking_report') }}</option>
                            <option value="user">{{ __('messages.reports_user_report') }}</option>
                            <option value="complaint">{{ __('messages.reports_complaint_report') }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.reports_from') }}</label>
                        <input type="date" x-model="dateFrom"
                               class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 px-3 py-2 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary text-sm font-medium">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.reports_to') }}</label>
                        <input type="date" x-model="dateTo"
                               class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 px-3 py-2 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary text-sm font-medium">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.reports_user_type') }}</label>
                        <select x-model="userType"
                                class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 px-3 py-2 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary text-sm font-medium">
                            <option value="all">{{ __('messages.reports_all') }}</option>
                            <option value="owner">{{ __('messages.reports_role_owner') }}</option>
                            <option value="sitter">{{ __('messages.reports_role_sitter') }}</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100 dark:border-neutral-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <button type="button" @click="resetFilters()"
                            class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-neutral-500 hover:text-primary text-xs sm:text-sm font-bold transition self-start sm:self-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        {{ __('messages.reports_reset_filters') }}
                    </button>

                    <div class="flex flex-col sm:flex-row gap-2 sm:ml-auto">
                        <button type="button" @click="goExport('pdf')"
                                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary-600 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5 whitespace-nowrap">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            {{ __('messages.reports_generate_pdf') }}
                        </button>
                        <button type="button" @click="goExport('excel')"
                                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5 whitespace-nowrap">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            {{ __('messages.reports_generate_excel') }}
                        </button>
                    </div>
                </div>

            </div>

            <!-- ③ REPORT CATEGORIES -->
            <h2 class="font-bold text-base sm:text-lg text-[#1B3B36] dark:text-white mb-3 sm:mb-4">{{ __('messages.reports_categories') }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 mb-4 sm:mb-6">

                {{-- Booking Report --}}
                <a href="{{ route('admin.bookings') }}"
                   class="block bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-5 hover:shadow-md hover:border-blue-400/50 transition cursor-pointer">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="w-10 h-10 rounded-full bg-blue-100/20 flex items-center justify-center text-blue-600 mb-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h3 class="font-bold text-[#1B3B36] dark:text-white">{{ __('messages.reports_label_booking') }}</h3>
                        </div>
                        <svg class="w-5 h-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                    <div class="grid grid-cols-2 gap-2 mt-3 text-xs sm:text-sm">
                        <div><span class="text-neutral-500 dark:text-neutral-400">{{ __('messages.reports_total_short') }}</span> <span class="font-bold text-[#1B3B36] dark:text-white">{{ $fmt($bookingStats['total']) }}</span></div>
                        <div><span class="text-neutral-500 dark:text-neutral-400">{{ __('messages.reports_done_short') }}</span> <span class="font-bold text-green-600">{{ $fmt($bookingStats['completed']) }}</span></div>
                        <div><span class="text-neutral-500 dark:text-neutral-400">{{ __('messages.reports_cancel_short') }}</span> <span class="font-bold text-red-600">{{ $fmt($bookingStats['cancelled']) }}</span></div>
                        <div><span class="text-neutral-500 dark:text-neutral-400">{{ __('messages.reports_pending_short') }}</span> <span class="font-bold text-amber-600">{{ $fmt($bookingStats['pending']) }}</span></div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-gray-100 dark:border-neutral-800 flex items-center justify-between gap-2">
                        <span class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 truncate">
                            {{ __('messages.reports_top') }} {{ $bookingStats['top_sitter_name'] ?: __('messages.reports_na') }}
                        </span>
                        <span class="text-xs font-bold text-primary hover:underline shrink-0">{{ __('messages.reports_view_arrow') }}</span>
                    </div>
                </a>

                {{-- User Report --}}
                <a href="{{ route('admin.users') }}"
                   class="block bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-5 hover:shadow-md hover:border-amber-400/50 transition cursor-pointer">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="w-10 h-10 rounded-full bg-amber-100/20 flex items-center justify-center text-amber-600 mb-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            <h3 class="font-bold text-[#1B3B36] dark:text-white">{{ __('messages.reports_label_user') }}</h3>
                        </div>
                        <svg class="w-5 h-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                    <div class="grid grid-cols-2 gap-2 mt-3 text-xs sm:text-sm">
                        <div><span class="text-neutral-500 dark:text-neutral-400">{{ __('messages.reports_reg_short') }}</span> <span class="font-bold text-[#1B3B36] dark:text-white">{{ $fmt($userStats['total']) }}</span></div>
                        <div><span class="text-neutral-500 dark:text-neutral-400">{{ __('messages.reports_verified_short') }}</span> <span class="font-bold text-green-600">{{ $fmt($userStats['verified']) }}</span></div>
                        <div><span class="text-neutral-500 dark:text-neutral-400">{{ __('messages.reports_sitters_short') }}</span> <span class="font-bold text-amber-600">{{ $fmt($userStats['sitters']) }}</span></div>
                        <div><span class="text-neutral-500 dark:text-neutral-400">{{ __('messages.reports_susp_short') }}</span> <span class="font-bold text-red-600">{{ $fmt($userStats['suspended']) }}</span></div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-gray-100 dark:border-neutral-800 flex items-center justify-between gap-2">
                        <span class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 truncate">
                            {{ __('messages.reports_pending_id', ['count' => $fmt($userStats['pending_verification'])]) }}
                        </span>
                        <span class="text-xs font-bold text-primary hover:underline shrink-0">{{ __('messages.reports_view_arrow') }}</span>
                    </div>
                </a>

                {{-- Complaint Report --}}
                <a href="{{ route('admin.complaints') }}"
                   class="block bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-5 hover:shadow-md hover:border-red-400/50 transition cursor-pointer">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="w-10 h-10 rounded-full bg-red-100/20 flex items-center justify-center text-red-600 mb-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <h3 class="font-bold text-[#1B3B36] dark:text-white">{{ __('messages.reports_label_complaint') }}</h3>
                        </div>
                        <svg class="w-5 h-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                    <div class="grid grid-cols-2 gap-2 mt-3 text-xs sm:text-sm">
                        <div><span class="text-neutral-500 dark:text-neutral-400">{{ __('messages.reports_total_short') }}</span> <span class="font-bold text-[#1B3B36] dark:text-white">{{ $fmt($complaintStats['total']) }}</span></div>
                        <div><span class="text-neutral-500 dark:text-neutral-400">{{ __('messages.reports_pending_short') }}</span> <span class="font-bold text-amber-600">{{ $fmt($complaintStats['pending']) }}</span></div>
                        <div><span class="text-neutral-500 dark:text-neutral-400">{{ __('messages.reports_resolved_short') }}</span> <span class="font-bold text-green-600">{{ $fmt($complaintStats['resolved']) }}</span></div>
                        <div><span class="text-neutral-500 dark:text-neutral-400">{{ __('messages.reports_dismissed_short') }}</span> <span class="font-bold text-red-600">{{ $fmt($complaintStats['dismissed']) }}</span></div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-gray-100 dark:border-neutral-800 flex items-center justify-between gap-2">
                        <span class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 truncate">
                            {{ __('messages.reports_common') }} {{ $complaintStats['common_type'] ?: __('messages.reports_na') }}
                        </span>
                        <span class="text-xs font-bold text-primary hover:underline shrink-0">{{ __('messages.reports_view_arrow') }}</span>
                    </div>
                </a>

            </div>

            <!-- ④ CHARTS -->
            <h2 class="font-bold text-base sm:text-lg text-[#1B3B36] dark:text-white mb-3 sm:mb-4">{{ __('messages.reports_analytics_overview') }}</h2>
            <div class="grid grid-cols-1 gap-4 sm:gap-6 mb-4 sm:mb-6">
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white">{{ __('messages.reports_booking_trend') }}</h3>
                        <span class="text-xs text-neutral-400">{{ __('messages.reports_last_n_months', ['n' => count($bookingTrend)]) }}</span>
                    </div>
                    <div class="flex items-end justify-between h-32 gap-1.5 sm:gap-2 pt-4">
                        @foreach ($bookingTrend as $t)
                            <div class="flex flex-col items-center gap-1 flex-1">
                                <div class="w-full bg-primary rounded-t-lg transition-all"
                                     style="height: {{ $trendMax > 0 ? max(6, round(($t['count'] / $trendMax) * 100)) : 6 }}%;"
                                     title="{{ $t['count'] }} bookings"></div>
                                <span class="text-[10px] text-neutral-400">{{ $t['month'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-4 sm:mb-6">

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white">{{ __('messages.reports_user_growth') }}</h3>
                        <span class="text-xs text-neutral-400">{{ __('messages.reports_current_breakdown') }}</span>
                    </div>
                    <div class="space-y-3">
                        @foreach ($userGrowth as $g)
                            <div>
                                <div class="flex justify-between gap-2 text-sm">
                                    <span class="text-neutral-600 dark:text-neutral-400">{{ $g['label'] }}</span>
                                    <span class="font-bold text-[#1B3B36] dark:text-white shrink-0">{{ $fmt($g['count']) }}</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-neutral-700 rounded-full h-2.5 mt-1">
                                    <div class="{{ $g['color'] }} rounded-full h-2.5" style="width:{{ $g['pct'] }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white">{{ __('messages.reports_complaint_dist') }}</h3>
                        <span class="text-xs text-neutral-400">{{ __('messages.reports_by_type') }}</span>
                    </div>
                    <div class="space-y-3">
                        @forelse ($complaintDistribution as $d)
                            <div>
                                <div class="flex justify-between gap-2 text-sm">
                                    <span class="text-neutral-600 dark:text-neutral-400">{{ $d['label'] }}</span>
                                    <span class="font-bold text-[#1B3B36] dark:text-white shrink-0">{{ $d['pct'] }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-neutral-700 rounded-full h-2.5 mt-1">
                                    <div class="{{ $d['color'] }} rounded-full h-2.5" style="width:{{ $d['pct'] }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-neutral-400 text-center py-4">{{ __('messages.reports_no_complaint_data') }}</p>
                        @endforelse
                    </div>
                </div>

            </div>

            <!-- ⑤ RECENT GENERATED REPORTS -->
            <h2 class="font-bold text-base sm:text-lg text-[#1B3B36] dark:text-white mb-3 sm:mb-4">{{ __('messages.reports_recent_generated') }}</h2>
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6 mb-4 sm:mb-6">
                @if (! empty($recentReports) && count($recentReports) > 0)
                    <div class="space-y-3">
                        @foreach ($recentReports as $r)
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-3 rounded-xl border border-gray-100 dark:border-neutral-800 hover:shadow-md transition">
                                <div class="flex items-center gap-3 sm:gap-4">
                                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full {{ $r['icon_bg'] }} flex items-center justify-center {{ $r['icon_color'] }} shrink-0">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-[#1B3B36] dark:text-white truncate">{{ $r['title'] }}</p>
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ $r['date'] }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between sm:justify-end gap-2 sm:gap-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold {{ $r['format_bg'] }}">{{ $r['format'] }}</span>
                                    <a href="{{ $r['url'] }}"
                                       class="inline-flex items-center gap-1.5 px-2.5 py-1.5 sm:px-3 sm:py-1.5 bg-primary/10 text-primary font-bold text-xs rounded-lg hover:bg-primary/20 transition">
                                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        {{ __('messages.reports_download') }}
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10">
                        <svg class="w-12 h-12 mx-auto text-neutral-300 dark:text-neutral-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400 mb-3">{{ __('messages.reports_no_reports_yet') }}</p>
                        <button type="button" @click="goExport('pdf')"
                                class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary hover:bg-primary-600 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            {{ __('messages.reports_generate_first') }}
                        </button>
                    </div>
                @endif
            </div>

            <!-- ⑥ GENERATED REPORTS HISTORY TABLE -->
            <h2 class="font-bold text-base sm:text-lg text-[#1B3B36] dark:text-white mb-3 sm:mb-4">{{ __('messages.reports_history') }}</h2>
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 overflow-hidden mb-4 sm:mb-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs sm:text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-800/30">
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">{{ __('messages.reports_col_id') }}</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">{{ __('messages.reports_col_type') }}</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">{{ __('messages.reports_col_generated_by') }}</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">{{ __('messages.reports_col_date') }}</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">{{ __('messages.reports_col_format') }}</th>
                                <th class="text-right py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">{{ __('messages.reports_col_action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($reportHistory as $h)
                                <tr class="border-b border-gray-100 dark:border-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition">
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4 font-bold text-[#1B3B36] dark:text-white whitespace-nowrap">
                                        REP-{{ str_pad($h->id ?? $loop->iteration, 3, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4 text-neutral-600 dark:text-neutral-300 whitespace-nowrap">{{ $h->report_type ?? '—' }}</td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4 text-neutral-600 dark:text-neutral-300">{{ $h->generator->f_name ?? __('messages.reports_admin_fallback') }}</td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4 text-neutral-500 dark:text-neutral-400 whitespace-nowrap">
                                        {{ optional($h->created_at ?? null)->format('M d, Y') ?? '—' }}
                                    </td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                        <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                            {{ strtoupper($h->format ?? 'PDF') }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4 text-right">
                                        <a href="{{ $h->download_url ?? '#' }}"
                                           class="inline-flex items-center gap-1 sm:gap-1.5 px-2 py-1 sm:px-3 sm:py-1.5 bg-primary/10 text-primary font-bold text-xs rounded-lg hover:bg-primary/20 transition whitespace-nowrap">
                                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                            {{ __('messages.reports_download') }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-10 text-center text-neutral-500 dark:text-neutral-400 text-sm">
                                        {{ __('messages.reports_no_history') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if (method_exists($reportHistory, 'hasPages') && $reportHistory->hasPages())
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-3 sm:px-4 py-3 border-t border-gray-100 dark:border-neutral-800">
                        <p class="text-xs sm:text-sm text-neutral-500 dark:text-neutral-400">
                            {{ __('messages.reports_showing', [
                                'from'  => $reportHistory->firstItem(),
                                'to'    => $reportHistory->lastItem(),
                                'total' => $reportHistory->total(),
                            ]) }}
                        </p>
                        <div class="flex items-center gap-1 overflow-x-auto">
                            {{ $reportHistory->links() }}
                        </div>
                    </div>
                @endif
            </div>

            <!-- ⑦ INFORMATION CARD -->
            <div class="bg-blue-50 dark:bg-blue-950/20 rounded-2xl border border-blue-200 dark:border-blue-800/50 p-4 sm:p-6">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="min-w-0">
                        <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white">{{ __('messages.reports_info_title') }}</h3>
                        <ul class="mt-2 space-y-1 text-xs sm:text-sm text-neutral-600 dark:text-neutral-300">
                            <li>• {{ __('messages.reports_info_1') }}</li>
                            <li>• {{ __('messages.reports_info_2') }}</li>
                            <li>• {{ __('messages.reports_info_3') }}</li>
                            <li>• {{ __('messages.reports_info_4') }}</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>