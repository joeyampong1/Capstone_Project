<x-app-layout>
    <x-slot name="header">
        <div x-data class="flex items-center justify-between w-full">
            <div class="flex items-center gap-3">
                <a :href="$store.app.role === 'sitter' ? '{{ route('sitter.dashboard') }}' : '{{ route('dashboard') }}'"
                   class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                        {{ __('messages.cp_index_title') }}
                    </h1>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.cp_index_subtitle') }}</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div x-data="{ filter: 'all' }" class="py-4 sm:py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200">
        <div class="w-full sm:max-w-6xl mx-auto px-2 sm:px-16 lg:px-24">

            @if(session('status'))
                <div class="mb-4 p-4 rounded-xl bg-green-50 dark:bg-green-950/20 border border-green-200 dark:border-green-800">
                    <p class="text-sm text-green-700 dark:text-green-400 font-bold">
                        ✓ {{ session('status') }}
                    </p>
                </div>
            @endif

            <!-- ① FILE COMPLAINT CARD -->
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6 mb-4 sm:mb-6 hover:shadow-md transition">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
                    <div class="flex items-center gap-3 sm:gap-4">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-primary/10 flex items-center justify-center text-primary shrink-0">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-bold text-base sm:text-lg text-[#1B3B36] dark:text-white">{{ __('messages.cp_file_card_title') }}</h3>
                            <p class="text-xs sm:text-sm text-neutral-500 dark:text-neutral-400">{{ __('messages.cp_file_card_desc') }}</p>
                        </div>
                    </div>
                    <a href="{{ route('complaints.create') }}"
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-primary hover:bg-primary-600 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        {{ __('messages.cp_file_btn') }}
                    </a>
                </div>
            </div>

            <!-- ② SUMMARY CARDS -->
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-6">
                <!-- Total Complaints -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4">
                    <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.cp_stat_total') }}</p>
                    <h3 class="font-bold text-lg sm:text-2xl mt-1 text-[#1B3B36] dark:text-white">
                        {{ $stats['total'] }}
                    </h3>
                    <p class="text-[10px] sm:text-xs text-neutral-400 mt-1">{{ __('messages.cp_stat_total_desc') }}</p>
                </div>

                <!-- Pending -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4">
                    <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.cp_stat_pending') }}</p>
                    <h3 class="font-bold text-lg sm:text-2xl mt-1 text-amber-600 dark:text-amber-400">
                        {{ $stats['pending'] }}
                    </h3>
                    <p class="text-[10px] sm:text-xs text-amber-600 dark:text-amber-400 mt-1">{{ __('messages.cp_stat_pending_desc') }}</p>
                </div>

                <!-- Under Review -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4">
                    <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.cp_stat_under_review') }}</p>
                    <h3 class="font-bold text-lg sm:text-2xl mt-1 text-blue-600 dark:text-blue-400">
                        {{ $stats['under_review'] }}
                    </h3>
                    <p class="text-[10px] sm:text-xs text-blue-600 dark:text-blue-400 mt-1">{{ __('messages.cp_stat_under_review_desc') }}</p>
                </div>

                <!-- Resolved -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4">
                    <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.cp_stat_resolved') }}</p>
                    <h3 class="font-bold text-lg sm:text-2xl mt-1 text-green-600 dark:text-green-400">
                        {{ $stats['resolved'] }}
                    </h3>
                    <p class="text-[10px] sm:text-xs text-green-600 dark:text-green-400 mt-1">{{ __('messages.cp_stat_resolved_desc') }}</p>
                </div>
            </div>

            <!-- ③ COMPLAINT INFORMATION -->
            <div class="bg-blue-50 dark:bg-blue-950/20 rounded-2xl border border-blue-200 dark:border-blue-800/50 p-4 sm:p-6 mb-4 sm:mb-6">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="min-w-0">
                        <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white">{{ __('messages.cp_info_header') }}</h3>
                        <ul class="mt-2 space-y-1 text-xs sm:text-sm text-neutral-600 dark:text-neutral-300">
                            <li>• {{ __('messages.cp_info_1') }}</li>
                            <li>• {{ __('messages.cp_info_2') }}</li>
                            <li>• {{ __('messages.cp_info_3') }}</li>
                            <li>• {{ __('messages.cp_info_4') }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- ④ FILTER TABS -->
            <div class="mb-4 sm:mb-6">
                <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 p-1 bg-neutral-100 dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 overflow-x-auto">
                    <button @click="filter = 'all'"
                            :class="filter === 'all' ? 'bg-primary text-white shadow-md' : 'text-neutral-600 dark:text-neutral-400 hover:bg-white/50 dark:hover:bg-neutral-700'"
                            class="px-3 sm:px-4 py-1.5 sm:py-2 text-[10px] sm:text-xs font-bold rounded-lg transition whitespace-nowrap">
                        {{ __('messages.cp_filter_all') }}
                    </button>
                    <button @click="filter = 'pending'"
                            :class="filter === 'pending' ? 'bg-amber-500 text-white shadow-md' : 'text-neutral-600 dark:text-neutral-400 hover:bg-white/50 dark:hover:bg-neutral-700'"
                            class="px-3 sm:px-4 py-1.5 sm:py-2 text-[10px] sm:text-xs font-bold rounded-lg transition whitespace-nowrap">
                        {{ __('messages.cp_filter_pending') }}
                    </button>
                    <button @click="filter = 'under_review'"
                            :class="filter === 'under_review' ? 'bg-blue-500 text-white shadow-md' : 'text-neutral-600 dark:text-neutral-400 hover:bg-white/50 dark:hover:bg-neutral-700'"
                            class="px-3 sm:px-4 py-1.5 sm:py-2 text-[10px] sm:text-xs font-bold rounded-lg transition whitespace-nowrap">
                        {{ __('messages.cp_filter_under_review') }}
                    </button>
                    <button @click="filter = 'resolved'"
                            :class="filter === 'resolved' ? 'bg-green-500 text-white shadow-md' : 'text-neutral-600 dark:text-neutral-400 hover:bg-white/50 dark:hover:bg-neutral-700'"
                            class="px-3 sm:px-4 py-1.5 sm:py-2 text-[10px] sm:text-xs font-bold rounded-lg transition whitespace-nowrap">
                        {{ __('messages.cp_filter_resolved') }}
                    </button>
                    <button @click="filter = 'dismissed'"
                            :class="filter === 'dismissed' ? 'bg-red-500 text-white shadow-md' : 'text-neutral-600 dark:text-neutral-400 hover:bg-white/50 dark:hover:bg-neutral-700'"
                            class="px-3 sm:px-4 py-1.5 sm:py-2 text-[10px] sm:text-xs font-bold rounded-lg transition whitespace-nowrap">
                        {{ __('messages.cp_filter_dismissed') }}
                    </button>
                </div>
            </div>

            {{-- ⑤ COMPLAINTS LIST --}}
            <div class="space-y-3 sm:space-y-4 mb-4 sm:mb-6">

                @forelse($complaints as $complaint)
                    @php
                        $booking = $complaint->booking;
                        $isOwnerInBooking = $booking && $booking->owner_id === auth()->id();

                        $respondent = $complaint->respondent;
                        $respondentName = trim(($respondent?->f_name ?? '') . ' ' . ($respondent?->l_name ?? '')) ?: 'User';
                        $respondentRole = $isOwnerInBooking ? __('messages.cp_role_sitter') : __('messages.cp_role_owner');

                        $complainantRole = $isOwnerInBooking ? __('messages.cp_role_owner') : __('messages.cp_role_sitter');

                        $statusMap = [
                            'pending'      => ['bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400', __('messages.cp_filter_pending')],
                            'under_review' => ['bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',   __('messages.cp_filter_under_review')],
                            'resolved'     => ['bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',__('messages.cp_filter_resolved')],
                            'dismissed'    => ['bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',        __('messages.cp_filter_dismissed')],
                        ];
                        [$statusClass, $statusLabel] = $statusMap[$complaint->status] ?? $statusMap['pending'];

                        $typeLabel = match($complaint->complaint_type) {
                            'missed_visit'  => __('messages.complaint_missed_visit'),
                            'poor_service'  => __('messages.complaint_poor_service'),
                            'no_proof'      => __('messages.complaint_no_proof'),
                            'rude_behavior' => __('messages.complaint_rude_behavior'),
                            'others'        => __('messages.complaint_others'),
                            default         => ucfirst($complaint->complaint_type),
                        };

                        $cardId = 'CMP-' . str_pad($complaint->id, 6, '0', STR_PAD_LEFT);
                    @endphp

                    <div x-show="filter === 'all' || filter === '{{ $complaint->status }}'"
                         class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6 hover:shadow-md transition">

                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 sm:gap-3 flex-wrap">
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.cp_card_complaint_id') }}</p>
                                    <p class="font-bold text-sm sm:text-base text-[#1B3B36] dark:text-white">{{ $cardId }}</p>
                                </div>
                                <div class="flex flex-wrap items-center gap-2 sm:gap-4 mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 sm:py-1 rounded-full text-[10px] font-bold {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                    <span class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400">
                                        {{ __('messages.cp_card_filed_on', ['date' => $complaint->created_at->format('M j, Y')]) }}
                                    </span>
                                </div>
                            </div>
                            <div class="sm:text-right">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.cp_card_type') }}</p>
                                <p class="text-sm font-bold text-[#1B3B36] dark:text-white">{{ $typeLabel }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                            <div>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.cp_card_booking') }}</p>
                                <p class="font-bold text-[#1B3B36] dark:text-white text-sm">
                                    {{ $booking->booking_reference ?? 'N/A' }}
                                </p>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.cp_card_respondent') }}</p>
                                <p class="font-bold text-red-600 dark:text-red-400 text-sm truncate">
                                    {{ $respondentName }} ({{ $respondentRole }})
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.cp_card_complainant') }}</p>
                                <p class="font-bold text-[#1B3B36] dark:text-white text-sm">
                                    {{ __('messages.cp_card_you') }} ({{ $complainantRole }})
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2 sm:gap-3 mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                            <a href="{{ route('complaints.show', $complaint->id) }}"
                               class="inline-flex items-center gap-1.5 px-3 sm:px-4 py-1.5 sm:py-2 bg-primary/10 text-primary font-bold text-xs rounded-lg hover:bg-primary/20 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                {{ $complaint->status === 'resolved' ? __('messages.cp_view_resolution') : __('messages.cp_view_details') }}
                            </a>
                        </div>
                    </div>
                @empty
                    {{-- EMPTY STATE --}}
                    <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-8 sm:p-12 text-center">
                        <svg class="w-16 h-16 mx-auto text-neutral-300 dark:text-neutral-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <h3 class="text-lg font-black text-[#1B3B36] dark:text-white">{{ __('messages.cp_empty_title') }}</h3>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">
                            {{ __('messages.cp_empty_desc') }}
                        </p>
                        <a href="{{ route('complaints.create') }}"
                           class="inline-block mt-4 px-6 py-3 bg-primary hover:bg-primary-600 text-white font-bold text-sm rounded-xl transition">
                            {{ __('messages.cp_empty_btn') }}
                        </a>
                    </div>
                @endforelse

            </div>

        </div>
    </div>
</x-app-layout>