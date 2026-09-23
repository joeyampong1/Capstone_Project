@php
    /* ------------------------------------------------------------------
     | Row helpers
     |------------------------------------------------------------------*/
    $initials = function (?string $name): string {
        $name = trim((string) $name);
        if ($name === '') return '?';
        $parts = preg_split('/\s+/', $name) ?: [];
        $first = mb_substr($parts[0] ?? '', 0, 1);
        $last  = count($parts) > 1 ? mb_substr(end($parts), 0, 1) : '';
        return mb_strtoupper($first . $last);
    };

    $statusClasses = fn (?string $s) => match (strtolower((string) $s)) {
        'pending'      => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
        'under_review' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
        'resolved'     => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
        'dismissed'    => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
        default        => 'bg-neutral-100 text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300',
    };

    $typeClasses = fn (?string $t) => match (strtolower((string) $t)) {
        'missed_visit'  => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
        'poor_service'  => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
        'no_proof'      => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
        'rude_behavior' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
        default         => 'bg-neutral-100 text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300',
    };

    $statusLabel = fn (?string $s) => match (strtolower((string) $s)) {
        'pending'      => __('messages.status_pending'),
        'under_review' => __('messages.status_under_review'),
        'resolved'     => __('messages.status_resolved'),
        'dismissed'    => __('messages.status_dismissed'),
        default        => ucwords(str_replace('_', ' ', (string) ($s ?? 'unknown'))),
    };

    $typeLabel = fn (?string $t) => match (strtolower((string) $t)) {
        'missed_visit'  => __('messages.complaint_missed_visit'),
        'poor_service'  => __('messages.complaint_poor_service'),
        'no_proof'      => __('messages.complaint_no_proof'),
        'rude_behavior' => __('messages.complaint_rude_behavior'),
        'others'        => __('messages.complaint_others'),
        default         => ucwords(str_replace('_', ' ', (string) ($t ?? 'others'))),
    };

    /* ------------------------------------------------------------------
     | Modal payload
     |------------------------------------------------------------------*/
    $complaintsData = collect(method_exists($complaints, 'getCollection') ? $complaints->getCollection() : $complaints)
        ->map(function ($c) use ($initials) {
            $complainant = $c->complainant ?? $c->user;
            $respondent  = $c->respondent;

            return [
                'id'            => $c->id,
                'code'          => $c->complaint_code ?? ('CMP-' . str_pad($c->id, 6, '0', STR_PAD_LEFT)),
                'booking_code'  => $c->booking->booking_code ?? ('BK-' . str_pad($c->booking_id ?? 0, 6, '0', STR_PAD_LEFT)),
                'booking_id'    => $c->booking_id,
                'status'        => $c->status,
                'type'          => $c->type,
                'description'   => $c->description ?? '',
                'submitted_at'  => optional($c->created_at)->format('F j, Y'),
                'submitted_short'=> optional($c->created_at)->format('M d, Y'),
                'admin_notes'   => $c->admin_notes ?? '',
                'resolution'    => $c->resolution ?? '',

                'complainant' => [
                    'id'       => $complainant?->id,
                    'name'     => trim(($complainant->f_name ?? '') . ' ' . ($complainant->l_name ?? '')) ?: '—',
                    'initials' => $initials(trim(($complainant->f_name ?? '') . ' ' . ($complainant->l_name ?? ''))),
                    'email'    => $complainant->email ?? '—',
                    'role'     => ($complainant->is_sitter ?? false) ? __('messages.role_sitter') : __('messages.role_owner'),
                ],

                'respondent' => [
                    'id'       => $respondent?->id,
                    'name'     => trim(($respondent->f_name ?? '') . ' ' . ($respondent->l_name ?? '')) ?: '—',
                    'initials' => $initials(trim(($respondent->f_name ?? '') . ' ' . ($respondent->l_name ?? ''))),
                    'email'    => $respondent->email ?? '—',
                    'role'     => ($respondent->is_sitter ?? false) ? __('messages.role_sitter') : __('messages.role_owner'),
                ],

                'evidence' => collect($c->evidence ?? [])->map(fn ($e) => [
                    'type' => $e->type ?? 'file',
                    'url'  => $e->url ?? '#',
                    'name' => $e->name ?? __('messages.complaint_evidence'),
                ])->values()->all(),
            ];
        })->values();
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="javascript:history.back()"
            class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div class="min-w-0">
                <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                    {{ __('messages.complaint_title') }}
                </h1>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.complaint_subtitle') }}</p>
            </div>
        </div>
    </x-slot>

    <div x-data="{
            search: '',
            statusFilter: 'all',
            typeFilter: 'all',
            rows: @js($complaintsData),
            selectedId: null,
            showDetail: false,
            activeTab: 'details',

            get selected() {
                return this.rows.find(r => r.id === this.selectedId) || {};
            },
            openDetail(id) {
                this.selectedId = id;
                this.showDetail = true;
                this.activeTab = 'details';
            },
            matches(row) {
                const q = this.search.trim().toLowerCase();
                if (q) {
                    const hay = [row.code, row.booking_code, row.complainant?.name, row.respondent?.name, row.description]
                        .filter(Boolean).join(' ').toLowerCase();
                    if (!hay.includes(q)) return false;
                }
                if (this.statusFilter !== 'all' && String(row.status).toLowerCase() !== this.statusFilter) return false;
                if (this.typeFilter !== 'all' && String(row.type).toLowerCase() !== this.typeFilter) return false;
                return true;
            },
            get visibleCount() { return this.rows.filter(r => this.matches(r)).length; },
            rowVisible(id) {
                const row = this.rows.find(r => r.id === id);
                return row ? this.matches(row) : false;
            },
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

            <!-- ① STATISTICS CARDS -->
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4 mb-4 sm:mb-6">

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.complaint_stat_total') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-[#1B3B36] dark:text-white mt-1">{{ number_format($stats['total'] ?? 0) }}</h3>
                            <p class="text-[10px] sm:text-xs text-neutral-400 mt-1">{{ __('messages.complaint_label_complaints') }}</p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-primary/10 flex items-center justify-center text-primary shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.complaint_stat_pending') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-amber-600 dark:text-amber-400 mt-1">{{ number_format($stats['pending'] ?? 0) }}</h3>
                            <p class="text-[10px] sm:text-xs text-amber-600 dark:text-amber-400 mt-1">{{ __('messages.complaint_label_awaiting') }}</p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-amber-100/20 flex items-center justify-center text-amber-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.complaint_stat_under_review') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-blue-600 dark:text-blue-400 mt-1">{{ number_format($stats['under_review'] ?? 0) }}</h3>
                            <p class="text-[10px] sm:text-xs text-blue-600 dark:text-blue-400 mt-1">{{ __('messages.complaint_label_investigating') }}</p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-blue-100/20 flex items-center justify-center text-blue-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.complaint_stat_resolved') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-green-600 dark:text-green-400 mt-1">{{ number_format($stats['resolved'] ?? 0) }}</h3>
                            <p class="text-[10px] sm:text-xs text-green-600 dark:text-green-400 mt-1">{{ __('messages.complaint_label_completed') }}</p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-green-100/20 flex items-center justify-center text-green-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition col-span-2 sm:col-span-1">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.complaint_stat_dismissed') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-red-600 dark:text-red-400 mt-1">{{ number_format($stats['dismissed'] ?? 0) }}</h3>
                            <p class="text-[10px] sm:text-xs text-red-500 dark:text-red-400 mt-1">{{ __('messages.complaint_stat_dismissed') }}</p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-red-100/20 flex items-center justify-center text-red-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ② SEARCH & FILTERS -->
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-5 mb-4 sm:mb-6">
                <div class="flex flex-col lg:flex-row gap-3 sm:gap-4">
                    <div class="flex-1 relative">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" x-model="search" placeholder="{{ __('messages.complaint_search_ph') }}"
                               class="w-full pl-9 pr-4 py-2 sm:py-2.5 text-xs sm:text-sm rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary transition">
                    </div>

                    <div class="grid grid-cols-2 sm:flex sm:flex-wrap items-center gap-2">
                        <select x-model="statusFilter"
                                class="px-2.5 py-2 sm:px-3 sm:py-2.5 text-xs sm:text-sm rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary transition">
                            <option value="all">{{ __('messages.complaint_all_status') }}</option>
                            <option value="pending">{{ __('messages.status_pending') }}</option>
                            <option value="under_review">{{ __('messages.status_under_review') }}</option>
                            <option value="resolved">{{ __('messages.status_resolved') }}</option>
                            <option value="dismissed">{{ __('messages.status_dismissed') }}</option>
                        </select>

                        <select x-model="typeFilter"
                                class="px-2.5 py-2 sm:px-3 sm:py-2.5 text-xs sm:text-sm rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary transition">
                            <option value="all">{{ __('messages.complaint_all_types') }}</option>
                            <option value="missed_visit">{{ __('messages.complaint_missed_visit') }}</option>
                            <option value="poor_service">{{ __('messages.complaint_poor_service') }}</option>
                            <option value="no_proof">{{ __('messages.complaint_no_proof') }}</option>
                            <option value="rude_behavior">{{ __('messages.complaint_rude_behavior') }}</option>
                            <option value="others">{{ __('messages.complaint_others') }}</option>
                        </select>

                        <button type="button"
                                @click="search = ''; statusFilter = 'all'; typeFilter = 'all'"
                                class="col-span-2 sm:col-span-1 inline-flex items-center justify-center px-4 py-2 sm:py-2.5 bg-primary hover:bg-primary-600 text-white font-bold text-xs sm:text-sm rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            {{ __('messages.booking_reset') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- ③ COMPLAINTS TABLE -->
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs sm:text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-800/30">
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">{{ __('messages.complaint_col_id') }}</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">{{ __('messages.complaint_col_booking') }}</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">{{ __('messages.complaint_col_complainant') }}</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">{{ __('messages.complaint_col_respondent') }}</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">{{ __('messages.complaint_col_type') }}</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">{{ __('messages.complaint_col_status') }}</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">{{ __('messages.complaint_col_submitted') }}</th>
                                <th class="text-right py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">{{ __('messages.complaint_col_action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($complaints as $c)
                                @php
                                    $complainant = $c->complainant ?? $c->user;
                                    $respondent  = $c->respondent;
                                @endphp
                                <tr x-show="rowVisible({{ $c->id }})"
                                    class="border-b border-gray-100 dark:border-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition">
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4 font-bold text-[#1B3B36] dark:text-white whitespace-nowrap">
                                        {{ $c->complaint_code ?? ('CMP-' . str_pad($c->id, 6, '0', STR_PAD_LEFT)) }}
                                    </td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4 font-bold text-[#1B3B36] dark:text-white whitespace-nowrap">
                                        {{ $c->booking->booking_code ?? ('BK-' . str_pad($c->booking_id ?? 0, 6, '0', STR_PAD_LEFT)) }}
                                    </td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-full bg-blue-100/20 flex items-center justify-center text-blue-600 font-bold text-xs shrink-0">
                                                {{ $initials(trim(($complainant->f_name ?? '') . ' ' . ($complainant->l_name ?? ''))) }}
                                            </div>
                                            <span class="text-neutral-600 dark:text-neutral-300 truncate">
                                                {{ trim(($complainant->f_name ?? '') . ' ' . ($complainant->l_name ?? '')) ?: '—' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-full bg-amber-100/20 flex items-center justify-center text-amber-600 font-bold text-xs shrink-0">
                                                {{ $initials(trim(($respondent->f_name ?? '') . ' ' . ($respondent->l_name ?? ''))) }}
                                            </div>
                                            <span class="text-neutral-600 dark:text-neutral-300 truncate">
                                                {{ trim(($respondent->f_name ?? '') . ' ' . ($respondent->l_name ?? '')) ?: '—' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                        <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full text-[10px] font-bold {{ $typeClasses($c->type) }} whitespace-nowrap">
                                            {{ $typeLabel($c->type) }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                        <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full text-[10px] font-bold {{ $statusClasses($c->status) }} whitespace-nowrap">
                                            {{ $statusLabel($c->status) }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4 text-neutral-500 dark:text-neutral-400 whitespace-nowrap">
                                        {{ optional($c->created_at)->format('M d, Y') }}
                                    </td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4 text-right">
                                        <button type="button"
                                                @click="openDetail({{ $c->id }})"
                                                class="inline-flex items-center gap-1 sm:gap-1.5 px-2 py-1 sm:px-3 sm:py-1.5 bg-primary/10 text-primary font-bold text-xs rounded-lg hover:bg-primary/20 transition whitespace-nowrap">
                                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            {{ $c->status === 'pending' ? __('messages.complaint_review') : __('messages.complaint_view') }}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-12 text-center text-neutral-500 dark:text-neutral-400 text-sm">
                                        {{ __('messages.complaint_no_found') }}
                                    </td>
                                </tr>
                            @endforelse

                            <tr x-show="rows.length > 0 && visibleCount === 0">
                                <td colspan="8" class="py-10 text-center text-neutral-500 dark:text-neutral-400 text-sm">
                                    {{ __('messages.complaint_no_match') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-3 sm:px-4 py-3 border-t border-gray-100 dark:border-neutral-800">
                    <p class="text-xs sm:text-sm text-neutral-500 dark:text-neutral-400">
                        {{ __('messages.complaint_showing', [
                            'from'  => $complaints->firstItem() ?? 0,
                            'to'    => $complaints->lastItem() ?? 0,
                            'total' => $complaints->total(),
                        ]) }}
                    </p>
                    <div class="flex items-center gap-1 overflow-x-auto">
                        @if ($complaints->onFirstPage())
                            <span class="px-2.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium text-neutral-400 whitespace-nowrap cursor-not-allowed">{{ __('messages.booking_previous') }}</span>
                        @else
                            <a href="{{ $complaints->previousPageUrl() }}" class="px-2.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium text-neutral-500 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition whitespace-nowrap">{{ __('messages.booking_previous') }}</a>
                        @endif

                        @php
                            $current = $complaints->currentPage();
                            $last    = $complaints->lastPage();
                            $start   = max(1, $current - 2);
                            $end     = min($last, $current + 2);
                        @endphp

                        @if ($start > 1)
                            <a href="{{ $complaints->url(1) }}" class="px-2.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium text-neutral-600 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">1</a>
                            @if ($start > 2)<span class="px-2 text-neutral-400">...</span>@endif
                        @endif

                        @for ($page = $start; $page <= $end; $page++)
                            @if ($page == $current)
                                <span class="px-2.5 py-1.5 rounded-lg text-xs sm:text-sm font-bold bg-primary text-white">{{ $page }}</span>
                            @else
                                <a href="{{ $complaints->url($page) }}" class="px-2.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium text-neutral-600 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">{{ $page }}</a>
                            @endif
                        @endfor

                        @if ($end < $last)
                            @if ($end < $last - 1)<span class="px-2 text-neutral-400">...</span>@endif
                            <a href="{{ $complaints->url($last) }}" class="px-2.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium text-neutral-600 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">{{ $last }}</a>
                        @endif

                        @if ($complaints->hasMorePages())
                            <a href="{{ $complaints->nextPageUrl() }}" class="px-2.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium text-neutral-500 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition whitespace-nowrap">{{ __('messages.booking_next') }}</a>
                        @else
                            <span class="px-2.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium text-neutral-400 whitespace-nowrap cursor-not-allowed">{{ __('messages.booking_next') }}</span>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        {{-- ④ COMPLAINT DETAILS MODAL --}}
        <div x-show="showDetail"
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:px-4 sm:py-8 bg-black/50 backdrop-blur-sm overflow-y-auto"
             @click.away="showDetail = false"
             @keydown.escape.window="showDetail = false">

            <div class="bg-white dark:bg-neutral-900 rounded-2xl sm:rounded-3xl shadow-2xl border border-gray-200 dark:border-neutral-800 max-w-6xl w-full max-h-[92vh] overflow-y-auto p-4 sm:p-8"
                 @click.stop>

                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <h2 class="text-lg sm:text-xl font-black text-[#1B3B36] dark:text-white">{{ __('messages.complaint_details_title') }}</h2>
                    <button type="button" @click="showDetail = false"
                            class="p-2 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                        <svg class="w-5 h-5 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="flex flex-wrap items-center gap-1 sm:gap-2 border-b border-gray-100 dark:border-neutral-800 mb-4 sm:mb-6 overflow-x-auto">
                    <button @click="activeTab = 'details'"
                            :class="activeTab === 'details' ? 'bg-primary/10 text-primary font-bold border-b-2 border-primary' : 'text-neutral-500 hover:text-neutral-700 dark:hover:text-neutral-300'"
                            class="px-3 py-2 text-xs sm:px-4 sm:py-2.5 sm:text-sm font-bold transition whitespace-nowrap">{{ __('messages.complaint_tab_details') }}</button>
                    <button @click="activeTab = 'activity'"
                            :class="activeTab === 'activity' ? 'bg-primary/10 text-primary font-bold border-b-2 border-primary' : 'text-neutral-500 hover:text-neutral-700 dark:hover:text-neutral-300'"
                            class="px-3 py-2 text-xs sm:px-4 sm:py-2.5 sm:text-sm font-bold transition whitespace-nowrap">{{ __('messages.complaint_tab_activity') }}</button>
                </div>

                <div x-show="activeTab === 'details'" x-cloak>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6" x-show="selected.id">

                        <!-- LEFT -->
                        <div class="lg:col-span-1 space-y-4">

                            <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">{{ __('messages.complaint_info') }}</p>
                                <div class="space-y-2 text-sm">
                                    <div>
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.complaint_col_id') }}</p>
                                        <p class="font-bold text-[#1B3B36] dark:text-white" x-text="selected.code"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.complaint_col_booking') }}</p>
                                        <p class="font-bold text-[#1B3B36] dark:text-white" x-text="selected.booking_code"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.complaint_col_status') }}</p>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold"
                                              :class="{
                                                  'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400': selected.status === 'pending',
                                                  'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400': selected.status === 'under_review',
                                                  'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': selected.status === 'resolved',
                                                  'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': selected.status === 'dismissed',
                                              }"
                                              x-text="({
                                                  'pending': '{{ __('messages.status_pending') }}',
                                                  'under_review': '{{ __('messages.status_under_review') }}',
                                                  'resolved': '{{ __('messages.status_resolved') }}',
                                                  'dismissed': '{{ __('messages.status_dismissed') }}',
                                              })[selected.status] || selected.status"></span>
                                    </div>
                                    <div>
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.complaint_col_submitted') }}</p>
                                        <p class="font-bold text-[#1B3B36] dark:text-white" x-text="selected.submitted_at"></p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">{{ __('messages.complaint_users_involved') }}</p>
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.complaint_col_complainant') }}</p>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <div class="w-8 h-8 rounded-full bg-blue-100/20 flex items-center justify-center text-blue-600 font-bold text-xs shrink-0" x-text="selected.complainant?.initials"></div>
                                            <div class="min-w-0">
                                                <p class="font-bold text-[#1B3B36] dark:text-white text-sm truncate" x-text="selected.complainant?.name"></p>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400" x-text="selected.complainant?.role"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.complaint_col_respondent') }}</p>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <div class="w-8 h-8 rounded-full bg-amber-100/20 flex items-center justify-center text-amber-600 font-bold text-xs shrink-0" x-text="selected.respondent?.initials"></div>
                                            <div class="min-w-0">
                                                <p class="font-bold text-[#1B3B36] dark:text-white text-sm truncate" x-text="selected.respondent?.name"></p>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400" x-text="selected.respondent?.role"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">{{ __('messages.complaint_view_profiles') }}</p>
                                <div class="space-y-2">
                                    <a :href="'{{ url('admin/users') }}/' + (selected.complainant?.id ?? '')"
                                       x-show="selected.complainant?.id"
                                       class="w-full flex items-center justify-center gap-1.5 px-3 py-2.5 bg-blue-100/20 text-blue-600 font-bold text-xs rounded-lg hover:bg-blue-100/30 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        {{ __('messages.complaint_view_complainant') }}
                                    </a>
                                    <a :href="'{{ url('admin/users') }}/' + (selected.respondent?.id ?? '')"
                                       x-show="selected.respondent?.id"
                                       class="w-full flex items-center justify-center gap-1.5 px-3 py-2.5 bg-amber-100/20 text-amber-600 font-bold text-xs rounded-lg hover:bg-amber-100/30 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        {{ __('messages.complaint_view_respondent') }}
                                    </a>
                                </div>
                            </div>

                        </div>

                        <!-- CENTER -->
                        <div class="lg:col-span-1 space-y-4">

                            <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">{{ __('messages.complaint_details_section') }}</p>
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.complaint_type_label') }}</p>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold mt-1"
                                              :class="{
                                                  'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400': selected.type === 'missed_visit',
                                                  'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400': selected.type === 'poor_service',
                                                  'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400': selected.type === 'no_proof',
                                                  'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': selected.type === 'rude_behavior',
                                              }"
                                              x-text="({
                                                  'missed_visit': '{{ __('messages.complaint_missed_visit') }}',
                                                  'poor_service': '{{ __('messages.complaint_poor_service') }}',
                                                  'no_proof': '{{ __('messages.complaint_no_proof') }}',
                                                  'rude_behavior': '{{ __('messages.complaint_rude_behavior') }}',
                                                  'others': '{{ __('messages.complaint_others') }}',
                                              })[selected.type] || selected.type"></span>
                                    </div>
                                    <div>
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.complaint_description') }}</p>
                                        <p class="text-sm text-neutral-600 dark:text-neutral-300 leading-relaxed whitespace-pre-line" x-text="selected.description || '{{ __('messages.complaint_no_description') }}'"></p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">{{ __('messages.complaint_evidence') }}</p>
                                <div class="grid grid-cols-2 gap-2">
                                    <template x-for="(ev, i) in (selected.evidence || [])" :key="i">
                                        <a :href="ev.url" target="_blank"
                                           class="bg-white dark:bg-neutral-900 rounded-xl border border-gray-200 dark:border-neutral-700 p-2.5 sm:p-3 text-center hover:shadow-sm transition">
                                            <svg class="w-7 h-7 sm:w-8 sm:h-8 mx-auto text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <p class="text-xs font-bold text-[#1B3B36] dark:text-white mt-1 truncate" x-text="ev.name"></p>
                                        </a>
                                    </template>
                                    <template x-if="!selected.evidence || selected.evidence.length === 0">
                                        <div class="col-span-2 text-center py-4">
                                            <p class="text-xs text-neutral-400">{{ __('messages.complaint_no_evidence') }}</p>
                                        </div>
                                    </template>
                                </div>
                            </div>

                        </div>

                        <!-- RIGHT -->
                        <div class="lg:col-span-1 space-y-4">

                            <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">{{ __('messages.complaint_admin_notes') }}</p>
                                <textarea rows="3"
                                          x-model="selected.admin_notes"
                                          placeholder="{{ __('messages.complaint_notes_ph') }}"
                                          class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-3 py-2.5 sm:px-4 sm:py-2.5 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary text-sm font-medium resize-none"></textarea>
                            </div>

                            <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">{{ __('messages.complaint_resolution') }}</p>
                                <textarea rows="2"
                                          x-model="selected.resolution"
                                          placeholder="{{ __('messages.complaint_resolution_ph') }}"
                                          class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-3 py-2.5 sm:px-4 sm:py-2.5 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary text-sm font-medium resize-none"></textarea>
                            </div>

                            <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">{{ __('messages.complaint_actions') }}</p>
                                <div class="space-y-2">

                                    <form method="POST"
                                          :action="'{{ url('admin/complaints') }}/' + (selected.id ?? '') + '/review'"
                                          x-show="selected.id && selected.status === 'pending'">
                                        @csrf
                                        <input type="hidden" name="admin_notes" :value="selected.admin_notes ?? ''">
                                        <button type="submit"
                                                class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                            </svg>
                                            {{ __('messages.complaint_mark_review') }}
                                        </button>
                                    </form>

                                    <form method="POST"
                                          :action="'{{ url('admin/complaints') }}/' + (selected.id ?? '') + '/resolve'"
                                          x-show="selected.id && ['pending','under_review'].includes(selected.status)"
                                          @submit="if (!selected.resolution || !selected.resolution.trim()) { alert('{{ __('messages.complaint_need_resolution') }}'); $event.preventDefault(); }">
                                        @csrf
                                        <input type="hidden" name="admin_notes" :value="selected.admin_notes ?? ''">
                                        <input type="hidden" name="resolution" :value="selected.resolution ?? ''">
                                        <button type="submit"
                                                class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ __('messages.complaint_resolve') }}
                                        </button>
                                    </form>

                                    <form method="POST"
                                          :action="'{{ url('admin/complaints') }}/' + (selected.id ?? '') + '/dismiss'"
                                          x-show="selected.id && ['pending','under_review'].includes(selected.status)"
                                          @submit="if (!confirm('{{ __('messages.complaint_confirm_dismiss') }}')) $event.preventDefault()">
                                        @csrf
                                        <input type="hidden" name="admin_notes" :value="selected.admin_notes ?? ''">
                                        <button type="submit"
                                                class="w-full flex items-center justify-center gap-2 px-4 py-2.5 border-2 border-red-500 text-red-500 hover:bg-red-50 dark:hover:bg-red-950/20 font-bold text-sm rounded-xl transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            {{ __('messages.complaint_dismiss') }}
                                        </button>
                                    </form>

                                </div>
                            </div>

                        </div>

                    </div>

                    <!-- TIMELINE -->
                    <div class="mt-4 sm:mt-6 bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-3">{{ __('messages.complaint_timeline') }}</p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
                            <div class="text-center">
                                <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary mx-auto mb-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                </div>
                                <p class="text-[10px] sm:text-xs font-bold text-[#1B3B36] dark:text-white">{{ __('messages.complaint_tl_submitted') }}</p>
                                <p class="text-[10px] text-neutral-400" x-text="selected.submitted_short"></p>
                            </div>
                            <div class="text-center">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center mx-auto mb-2"
                                     :class="['under_review','resolved','dismissed'].includes(selected.status) ? 'bg-blue-100/20 text-blue-600' : 'bg-neutral-200 dark:bg-neutral-700 text-neutral-400'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                                <p class="text-[10px] sm:text-xs font-bold text-[#1B3B36] dark:text-white">{{ __('messages.complaint_tl_under_review') }}</p>
                                <p class="text-[10px] text-neutral-400" x-text="['under_review','resolved','dismissed'].includes(selected.status) ? '{{ __('messages.complaint_tl_done') }}' : '{{ __('messages.complaint_tl_pending') }}'"></p>
                            </div>
                            <div class="text-center">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center mx-auto mb-2"
                                     :class="['resolved','dismissed'].includes(selected.status) ? 'bg-green-100/20 text-green-600' : 'bg-neutral-200 dark:bg-neutral-700 text-neutral-400'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <p class="text-[10px] sm:text-xs font-bold text-[#1B3B36] dark:text-white">{{ __('messages.complaint_tl_decision') }}</p>
                                <p class="text-[10px] text-neutral-400" x-text="['resolved','dismissed'].includes(selected.status) ? (selected.status === 'resolved' ? '{{ __('messages.complaint_tl_resolved') }}' : '{{ __('messages.complaint_tl_dismissed') }}') : '{{ __('messages.complaint_tl_pending') }}'"></p>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Tab: Activity (placeholder) -->
                <div x-show="activeTab === 'activity'" x-cloak>
                    <div class="text-center py-8 sm:py-12 text-neutral-500 dark:text-neutral-400">
                        <svg class="w-12 h-12 sm:w-16 sm:h-16 mx-auto text-neutral-300 dark:text-neutral-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        <h3 class="text-base sm:text-lg font-bold text-[#1B3B36] dark:text-white">{{ __('messages.complaint_activity_log') }}</h3>
                        <p class="text-sm">{{ __('messages.complaint_activity_hint') }}</p>
                    </div>
                </div>

                <!-- GUIDELINES -->
                <div class="mt-4 sm:mt-6 p-3 sm:p-4 bg-blue-50 dark:bg-blue-950/20 rounded-2xl border border-blue-200 dark:border-blue-800/50">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="min-w-0">
                            <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white">{{ __('messages.complaint_guidelines') }}</h3>
                            <ul class="mt-1 space-y-1 text-xs sm:text-sm text-neutral-600 dark:text-neutral-300">
                                <li>• {{ __('messages.complaint_guide_1') }}</li>
                                <li>• {{ __('messages.complaint_guide_2') }}</li>
                                <li>• {{ __('messages.complaint_guide_3') }}</li>
                                <li>• {{ __('messages.complaint_guide_4') }}</li>
                                <li>• {{ __('messages.complaint_guide_5') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>