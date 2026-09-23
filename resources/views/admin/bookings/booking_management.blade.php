@php
    $bookingPaginator = $bookings instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator ? $bookings : null;
    $bookingRows = $bookingPaginator ? $bookingPaginator->items() : (is_iterable($bookings) ? $bookings : []);

    $initials = function (?string $name): string {
        $name = trim((string) $name);
        if ($name === '') return '?';
        $parts = preg_split('/\s+/', $name) ?: [];
        $first = mb_substr($parts[0] ?? '', 0, 1);
        $last  = count($parts) > 1 ? mb_substr(end($parts), 0, 1) : '';
        return mb_strtoupper($first . $last);
    };

    $statusClasses = fn (?string $s) => match (strtolower((string) $s)) {
        'pending'   => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
        'accepted', 'active', 'ongoing' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
        'completed' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
        'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
        default     => 'bg-neutral-100 text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300',
    };

    // Translated status label
    $statusLabel = fn (?string $s) => match (strtolower((string) $s)) {
        'pending'      => __('messages.status_pending'),
        'accepted'     => __('messages.status_accepted'),
        'active'       => __('messages.booking_active'),
        'ongoing'      => __('messages.status_in_progress'),
        'completed'    => __('messages.status_completed'),
        'cancelled'    => __('messages.status_cancelled'),
        'rejected'     => __('messages.status_rejected'),
        default        => ucfirst(str_replace('_', ' ', (string) ($s ?? 'unknown'))),
    };

    $bookingsData = collect($bookingRows)->map(function ($b) use ($initials, $statusLabel) {
        $owner  = $b->owner;
        $sitter = $b->sitter;
        $pet    = $b->pet;

        return [
            'id'              => $b->id,
            'booking_code'    => $b->booking_code ?? ('BK-' . str_pad($b->id, 6, '0', STR_PAD_LEFT)),
            'status'          => $b->status,
            'status_label'    => $statusLabel($b->status),
            'created_at'      => optional($b->created_at)->format('F j, Y'),
            'created_time'    => optional($b->created_at)->format('g:i A'),
            'visit_date'      => optional($b->visit_date ?? $b->created_at)->format('F j, Y'),
            'visit_time'      => $b->visit_time ?? '9:00 AM',
            'total'           => (float) ($b->subtotal ?? $b->total_amount ?? 0),
            'total_formatted' => '₱' . number_format((float) ($b->subtotal ?? $b->total_amount ?? 0), 2),
            'payout_formatted'=> '₱' . number_format((float) ($b->current_payout ?? 0), 2),
            'current_payout'  => (float) ($b->current_payout ?? 0),

            'owner' => [
                'id'       => $owner?->id,
                'name'     => trim(($owner->f_name ?? '') . ' ' . ($owner->l_name ?? '')) ?: '—',
                'initials' => $initials(trim(($owner->f_name ?? '') . ' ' . ($owner->l_name ?? ''))),
                'phone'    => $owner->contact_number ?? '—',
                'email'    => $owner->email ?? '—',
                'verified' => ($owner->id_validation_status ?? null) === 'verified',
            ],

            'sitter' => [
                'id'             => $sitter?->id,
                'name'           => trim(($sitter->f_name ?? '') . ' ' . ($sitter->l_name ?? '')) ?: '—',
                'initials'       => $initials(trim(($sitter->f_name ?? '') . ' ' . ($sitter->l_name ?? ''))),
                'rating'         => round((float) ($sitter->sitterProfile->average_ratings ?? 0), 1),
                'completed_jobs' => (int) ($sitter->sitterProfile->total_bookings ?? 0),
                'verified'       => ($sitter->sitter_status ?? null) === 'approved',
            ],

            'pet' => [
                'name'   => $pet->name ?? '—',
                'type'   => $pet->type ?? '—',
                'breed'  => $pet->breed ?? '—',
                'age'    => $pet->age ?? '—',
                'weight' => $pet->weight ?? '—',
            ],

            'services' => is_array($b->services ?? null)
                            ? $b->services
                            : (json_decode($b->services ?? '[]', true) ?: []),

            'visits' => collect($b->visits ?? [])->map(fn ($v) => [
                'id'     => $v->id,
                'label'  => __('messages.booking_visit_label', ['n' => $v->sequence ?? $v->id]),
                'status' => $v->status ?? 'pending',
                'date'   => optional($v->scheduled_at)->format('M d, Y g:i A'),
            ])->values()->all(),

            'completed_visits' => collect($b->visits ?? [])->where('status', 'completed')->count(),
            'total_visits'     => collect($b->visits ?? [])->count(),
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
                    {{ __('messages.booking_title') }}
                </h1>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.booking_subtitle') }}</p>
            </div>
        </div>
    </x-slot>

    <div x-data="{
            search: '',
            statusFilter: 'all',
            ownerFilter: 'all',
            sitterFilter: 'all',
            rows: @js($bookingsData),
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
                    const hay = [row.booking_code, row.owner?.name, row.sitter?.name, row.pet?.name]
                        .filter(Boolean).join(' ').toLowerCase();
                    if (!hay.includes(q)) return false;
                }
                if (this.statusFilter !== 'all' && String(row.status).toLowerCase() !== this.statusFilter) return false;
                if (this.ownerFilter !== 'all' && String(row.owner?.id) !== String(this.ownerFilter)) return false;
                if (this.sitterFilter !== 'all' && String(row.sitter?.id) !== String(this.sitterFilter)) return false;
                return true;
            },

            get visibleCount() {
                return this.rows.filter(r => this.matches(r)).length;
            },

            rowVisible(id) {
                const row = this.rows.find(r => r.id === id);
                return row ? this.matches(row) : false;
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

            <!-- ① STATISTICS CARDS -->
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4 mb-4 sm:mb-6">

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.booking_total') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-[#1B3B36] dark:text-white mt-1">{{ number_format($stats['total'] ?? 0) }}</h3>
                            <p class="text-[10px] sm:text-xs text-green-600 dark:text-green-400 mt-1">{{ __('messages.booking_all_time') }}</p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-primary/10 flex items-center justify-center text-primary shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.booking_pending') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-amber-600 dark:text-amber-400 mt-1">{{ number_format($stats['pending'] ?? 0) }}</h3>
                            <p class="text-[10px] sm:text-xs text-amber-600 dark:text-amber-400 mt-1">{{ __('messages.booking_awaiting') }}</p>
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
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.booking_active') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-blue-600 dark:text-blue-400 mt-1">{{ number_format($stats['active'] ?? 0) }}</h3>
                            <p class="text-[10px] sm:text-xs text-blue-600 dark:text-blue-400 mt-1">{{ __('messages.booking_ongoing') }}</p>
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
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.booking_completed') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-green-600 dark:text-green-400 mt-1">{{ number_format($stats['completed'] ?? 0) }}</h3>
                            <p class="text-[10px] sm:text-xs text-green-600 dark:text-green-400 mt-1">{{ __('messages.booking_finished') }}</p>
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
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.booking_cancelled') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-red-600 dark:text-red-400 mt-1">{{ number_format($stats['cancelled'] ?? 0) }}</h3>
                            <p class="text-[10px] sm:text-xs text-red-500 dark:text-red-400 mt-1">{{ __('messages.booking_cancelled') }}</p>
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
                        <input type="text" x-model="search" placeholder="{{ __('messages.booking_search_ph') }}"
                               class="w-full pl-9 pr-4 py-2 sm:py-2.5 text-xs sm:text-sm rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary transition">
                    </div>

                    <div class="grid grid-cols-2 sm:flex sm:flex-wrap items-center gap-2">
                        <select x-model="statusFilter"
                                class="px-2.5 py-2 sm:px-3 sm:py-2.5 text-xs sm:text-sm rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary transition">
                            <option value="all">{{ __('messages.booking_all_status') }}</option>
                            <option value="pending">{{ __('messages.status_pending') }}</option>
                            <option value="accepted">{{ __('messages.status_accepted') }}</option>
                            <option value="active">{{ __('messages.booking_active') }}</option>
                            <option value="completed">{{ __('messages.status_completed') }}</option>
                            <option value="cancelled">{{ __('messages.status_cancelled') }}</option>
                        </select>

                        <select x-model="ownerFilter"
                                class="px-2.5 py-2 sm:px-3 sm:py-2.5 text-xs sm:text-sm rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary transition">
                            <option value="all">{{ __('messages.booking_all_owners') }}</option>
                            @foreach ($owners as $o)
                                <option value="{{ $o->id }}">{{ trim($o->f_name . ' ' . $o->l_name) ?: $o->email }}</option>
                            @endforeach
                        </select>

                        <select x-model="sitterFilter"
                                class="px-2.5 py-2 sm:px-3 sm:py-2.5 text-xs sm:text-sm rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary transition">
                            <option value="all">{{ __('messages.booking_all_sitters') }}</option>
                            @foreach ($sitters as $s)
                                <option value="{{ $s->id }}">{{ trim($s->f_name . ' ' . $s->l_name) ?: $s->email }}</option>
                            @endforeach
                        </select>

                        <button type="button" @click="search = ''; statusFilter = 'all'; ownerFilter = 'all'; sitterFilter = 'all'"
                                class="col-span-2 sm:col-span-1 inline-flex items-center justify-center px-4 py-2 sm:py-2.5 bg-primary hover:bg-primary-600 text-white font-bold text-xs sm:text-sm rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            {{ __('messages.booking_reset') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- ③ BOOKING TABLE -->
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs sm:text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-800/30">
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">{{ __('messages.booking_col_id') }}</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">{{ __('messages.booking_col_owner') }}</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">{{ __('messages.booking_col_sitter') }}</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">{{ __('messages.booking_col_pet') }}</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">{{ __('messages.booking_col_visit_date') }}</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">{{ __('messages.booking_col_total') }}</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">{{ __('messages.booking_col_status') }}</th>
                                <th class="text-right py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">{{ __('messages.booking_col_action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($bookingRows as $b)
                                <tr x-show="rowVisible({{ $b->id }})"
                                    class="border-b border-gray-100 dark:border-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition">
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4 font-bold text-[#1B3B36] dark:text-white whitespace-nowrap">
                                        {{ $b->booking_code ?? ('BK-' . str_pad($b->id, 6, '0', STR_PAD_LEFT)) }}
                                    </td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-full bg-blue-100/20 flex items-center justify-center text-blue-600 font-bold text-xs shrink-0">
                                                {{ $initials(trim(($b->owner->f_name ?? '') . ' ' . ($b->owner->l_name ?? ''))) }}
                                            </div>
                                            <span class="text-neutral-600 dark:text-neutral-300 truncate">{{ trim(($b->owner->f_name ?? '') . ' ' . ($b->owner->l_name ?? '')) ?: '—' }}</span>
                                        </div>
                                    </td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-full bg-amber-100/20 flex items-center justify-center text-amber-600 font-bold text-xs shrink-0">
                                                {{ $initials(trim(($b->sitter->f_name ?? '') . ' ' . ($b->sitter->l_name ?? ''))) }}
                                            </div>
                                            <span class="text-neutral-600 dark:text-neutral-300 truncate">{{ trim(($b->sitter->f_name ?? '') . ' ' . ($b->sitter->l_name ?? '')) ?: '—' }}</span>
                                        </div>
                                    </td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4 font-bold text-[#1B3B36] dark:text-white">{{ $b->pet->name ?? '—' }}</td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4 text-neutral-600 dark:text-neutral-300 whitespace-nowrap">
                                        {{ optional($b->visit_date ?? $b->created_at)->format('M d, Y') }}
                                    </td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4 font-bold text-[#1B3B36] dark:text-white whitespace-nowrap">
                                        ₱{{ number_format((float) ($b->subtotal ?? $b->total_amount ?? 0), 0) }}
                                    </td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                        <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full text-[10px] font-bold {{ $statusClasses($b->status) }} whitespace-nowrap">
                                            {{ $statusLabel($b->status) }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4 text-right">
                                        <button type="button"
                                                @click="openDetail({{ $b->id }})"
                                                class="inline-flex items-center gap-1 sm:gap-1.5 px-2 py-1 sm:px-3 sm:py-1.5 bg-primary/10 text-primary font-bold text-xs rounded-lg hover:bg-primary/20 transition whitespace-nowrap">
                                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            {{ __('messages.booking_view') }}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-12 text-center text-neutral-500 dark:text-neutral-400 text-sm">
                                        {{ __('messages.booking_no_found') }}
                                    </td>
                                </tr>
                            @endforelse

                            <tr x-show="rows.length > 0 && visibleCount === 0">
                                <td colspan="8" class="py-10 text-center text-neutral-500 dark:text-neutral-400 text-sm">
                                    {{ __('messages.booking_no_match') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                @if ($bookingPaginator)
                    <!-- Pagination -->
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-3 sm:px-4 py-3 border-t border-gray-100 dark:border-neutral-800">
                        <p class="text-xs sm:text-sm text-neutral-500 dark:text-neutral-400">
                            {{ __('messages.booking_showing', [
                                'from'  => $bookingPaginator->firstItem() ?? 0,
                                'to'    => $bookingPaginator->lastItem() ?? 0,
                                'total' => $bookingPaginator->total(),
                            ]) }}
                        </p>
                        <div class="flex items-center gap-1 overflow-x-auto">
                            @if ($bookingPaginator->onFirstPage())
                                <span class="px-2.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium text-neutral-400 whitespace-nowrap cursor-not-allowed">{{ __('messages.booking_previous') }}</span>
                            @else
                                <a href="{{ $bookingPaginator->previousPageUrl() }}" class="px-2.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium text-neutral-500 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition whitespace-nowrap">{{ __('messages.booking_previous') }}</a>
                            @endif

                            @php
                                $current = $bookingPaginator->currentPage();
                                $last    = $bookingPaginator->lastPage();
                                $start   = max(1, $current - 2);
                                $end     = min($last, $current + 2);
                            @endphp

                            @if ($start > 1)
                                <a href="{{ $bookingPaginator->url(1) }}" class="px-2.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium text-neutral-600 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">1</a>
                                @if ($start > 2)<span class="px-2 text-neutral-400">...</span>@endif
                            @endif

                            @for ($page = $start; $page <= $end; $page++)
                                @if ($page == $current)
                                    <span class="px-2.5 py-1.5 rounded-lg text-xs sm:text-sm font-bold bg-primary text-white">{{ $page }}</span>
                                @else
                                    <a href="{{ $bookingPaginator->url($page) }}" class="px-2.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium text-neutral-600 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">{{ $page }}</a>
                                @endif
                            @endfor

                            @if ($end < $last)
                                @if ($end < $last - 1)<span class="px-2 text-neutral-400">...</span>@endif
                                <a href="{{ $bookingPaginator->url($last) }}" class="px-2.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium text-neutral-600 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">{{ $last }}</a>
                            @endif

                            @if ($bookingPaginator->hasMorePages())
                                <a href="{{ $bookingPaginator->nextPageUrl() }}" class="px-2.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium text-neutral-500 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition whitespace-nowrap">{{ __('messages.booking_next') }}</a>
                            @else
                                <span class="px-2.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium text-neutral-400 whitespace-nowrap cursor-not-allowed">{{ __('messages.booking_next') }}</span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

        </div>

        {{-- ④ BOOKING DETAILS MODAL --}}
        <div x-show="showDetail"
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:px-4 sm:py-8 bg-black/50 backdrop-blur-sm overflow-y-auto"
             @click.away="showDetail = false"
             @keydown.escape.window="showDetail = false">

            <div class="bg-white dark:bg-neutral-900 rounded-2xl sm:rounded-3xl shadow-2xl border border-gray-200 dark:border-neutral-800 max-w-6xl w-full max-h-[92vh] overflow-y-auto p-4 sm:p-8"
                 @click.stop>

                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <h2 class="text-lg sm:text-xl font-black text-[#1B3B36] dark:text-white">{{ __('messages.booking_details') }}</h2>
                    <button type="button" @click="showDetail = false"
                            class="p-2 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                        <svg class="w-5 h-5 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Tabs --}}
                <div class="flex flex-wrap items-center gap-1 sm:gap-2 border-b border-gray-100 dark:border-neutral-800 mb-4 sm:mb-6 overflow-x-auto">
                    <button @click="activeTab = 'details'"
                            :class="activeTab === 'details' ? 'bg-primary/10 text-primary font-bold border-b-2 border-primary' : 'text-neutral-500 hover:text-neutral-700 dark:hover:text-neutral-300'"
                            class="px-3 py-2 text-xs sm:px-4 sm:py-2.5 sm:text-sm font-bold transition whitespace-nowrap">{{ __('messages.booking_tab_details') }}</button>
                    <button @click="activeTab = 'visits'"
                            :class="activeTab === 'visits' ? 'bg-primary/10 text-primary font-bold border-b-2 border-primary' : 'text-neutral-500 hover:text-neutral-700 dark:hover:text-neutral-300'"
                            class="px-3 py-2 text-xs sm:px-4 sm:py-2.5 sm:text-sm font-bold transition whitespace-nowrap">{{ __('messages.booking_tab_visits') }}</button>
                </div>

                {{-- Details Tab --}}
                <div x-show="activeTab === 'details'" x-cloak>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6" x-show="selected.id">

                        {{-- LEFT --}}
                        <div class="lg:col-span-1 space-y-4">

                            <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">{{ __('messages.booking_info') }}</p>
                                <div class="space-y-2 text-sm">
                                    <div>
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.booking_col_id') }}</p>
                                        <p class="font-bold text-[#1B3B36] dark:text-white" x-text="selected.booking_code"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.booking_date') }}</p>
                                        <p class="font-bold text-[#1B3B36] dark:text-white" x-text="selected.created_at"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.booking_visit_sched') }}</p>
                                        <p class="font-bold text-[#1B3B36] dark:text-white">
                                            <span x-text="selected.visit_date"></span> · <span x-text="selected.visit_time"></span>
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.booking_col_status') }}</p>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold"
                                              :class="{
                                                  'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400': selected.status === 'pending',
                                                  'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400': ['active','accepted','ongoing'].includes(selected.status),
                                                  'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': selected.status === 'completed',
                                                  'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': selected.status === 'cancelled',
                                              }"
                                              x-text="selected.status_label"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">{{ __('messages.booking_owner_info') }}</p>
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 rounded-full bg-blue-100/20 flex items-center justify-center text-blue-600 font-bold text-sm shrink-0" x-text="selected.owner?.initials"></div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-[#1B3B36] dark:text-white truncate" x-text="selected.owner?.name"></p>
                                        <template x-if="selected.owner?.verified">
                                            <div class="flex items-center gap-1">
                                                <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                <span class="text-xs text-green-600 dark:text-green-400">{{ __('messages.booking_verified') }}</span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                <div class="space-y-1 text-sm">
                                    <p><span class="text-neutral-500 dark:text-neutral-400">{{ __('messages.booking_phone') }}</span> <span class="font-bold break-all" x-text="selected.owner?.phone"></span></p>
                                    <p><span class="text-neutral-500 dark:text-neutral-400">{{ __('messages.booking_email') }}</span> <span class="font-bold break-all" x-text="selected.owner?.email"></span></p>
                                </div>
                            </div>

                            <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">{{ __('messages.booking_sitter_info') }}</p>
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 rounded-full bg-amber-100/20 flex items-center justify-center text-amber-600 font-bold text-sm shrink-0" x-text="selected.sitter?.initials"></div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-[#1B3B36] dark:text-white truncate" x-text="selected.sitter?.name"></p>
                                        <template x-if="selected.sitter?.verified">
                                            <div class="flex items-center gap-1">
                                                <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                <span class="text-xs text-green-600 dark:text-green-400">{{ __('messages.booking_verified_sitter') }}</span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                <div class="space-y-1 text-sm">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-neutral-500 dark:text-neutral-400">{{ __('messages.booking_rating') }}</span>
                                        <span class="font-bold text-amber-600 dark:text-amber-400 shrink-0">⭐ <span x-text="selected.sitter?.rating"></span></span>
                                    </div>
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-neutral-500 dark:text-neutral-400">{{ __('messages.booking_completed_jobs') }}</span>
                                        <span class="font-bold text-[#1B3B36] dark:text-white shrink-0" x-text="selected.sitter?.completed_jobs"></span>
                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- CENTER --}}
                        <div class="lg:col-span-1 space-y-4">

                            <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">{{ __('messages.booking_pet_info') }}</p>
                                <div class="space-y-2 text-sm">
                                    <div>
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.booking_pet_name') }}</p>
                                        <p class="font-bold text-[#1B3B36] dark:text-white text-lg" x-text="selected.pet?.name"></p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.booking_pet_type') }}</p>
                                            <p class="font-bold text-[#1B3B36] dark:text-white" x-text="selected.pet?.type"></p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.booking_pet_breed') }}</p>
                                            <p class="font-bold text-[#1B3B36] dark:text-white" x-text="selected.pet?.breed"></p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.booking_pet_age') }}</p>
                                            <p class="font-bold text-[#1B3B36] dark:text-white" x-text="selected.pet?.age"></p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.booking_pet_weight') }}</p>
                                            <p class="font-bold text-[#1B3B36] dark:text-white" x-text="selected.pet?.weight"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">{{ __('messages.booking_services') }}</p>
                                <div class="grid grid-cols-2 gap-1">
                                    <template x-for="(svc, i) in (selected.services || [])" :key="i">
                                        <span class="flex items-center gap-2 text-sm text-[#1B3B36] dark:text-white">
                                            <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span x-text="svc"></span>
                                        </span>
                                    </template>
                                    <template x-if="!selected.services || selected.services.length === 0">
                                        <span class="text-xs text-neutral-400 col-span-2">{{ __('messages.booking_no_services') }}</span>
                                    </template>
                                </div>
                            </div>

                            <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">{{ __('messages.booking_visit_sched') }}</p>
                                <div class="space-y-2">
                                    <template x-for="(v, i) in (selected.visits || [])" :key="i">
                                        <div class="flex items-center justify-between gap-2 text-sm border-b border-gray-100 dark:border-neutral-800 pb-2 last:border-0">
                                            <span class="font-bold text-[#1B3B36] dark:text-white" x-text="v.label"></span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold shrink-0"
                                                  :class="{
                                                      'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': v.status === 'completed',
                                                      'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400': v.status === 'pending',
                                                      'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': v.status === 'cancelled',
                                                  }"
                                                  x-text="v.status.charAt(0).toUpperCase() + v.status.slice(1)"></span>
                                        </div>
                                    </template>
                                    <template x-if="!selected.visits || selected.visits.length === 0">
                                        <p class="text-xs text-neutral-400 text-center py-2">{{ __('messages.booking_no_visits') }}</p>
                                    </template>
                                </div>
                            </div>

                        </div>

                        {{-- RIGHT --}}
                        <div class="lg:col-span-1 space-y-4">

                            <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">{{ __('messages.booking_summary') }}</p>
                                <div class="space-y-2 text-sm">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-neutral-500 dark:text-neutral-400">{{ __('messages.booking_total_label') }}</span>
                                        <span class="font-bold text-[#1B3B36] dark:text-white shrink-0" x-text="selected.total_formatted"></span>
                                    </div>
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-neutral-500 dark:text-neutral-400">{{ __('messages.booking_completed_visits') }}</span>
                                        <span class="font-bold text-green-600 dark:text-green-400 shrink-0" x-text="selected.completed_visits"></span>
                                    </div>
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-neutral-500 dark:text-neutral-400">{{ __('messages.booking_remaining_visits') }}</span>
                                        <span class="font-bold text-amber-600 dark:text-amber-400 shrink-0" x-text="(selected.total_visits ?? 0) - (selected.completed_visits ?? 0)"></span>
                                    </div>
                                </div>
                            </div>

                            {{-- Admin Actions --}}
                            <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">{{ __('messages.booking_admin_actions') }}</p>
                                <div class="space-y-2">

                                    <a :href="'{{ url('admin/users') }}/' + (selected.owner?.id ?? '')"
                                       x-show="selected.owner?.id"
                                       class="w-full flex items-center justify-center gap-1.5 px-3 py-2.5 bg-blue-100/20 text-blue-600 font-bold text-xs rounded-lg hover:bg-blue-100/30 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        {{ __('messages.booking_view_owner') }}
                                    </a>

                                    <a :href="'{{ url('admin/users') }}/' + (selected.sitter?.id ?? '')"
                                       x-show="selected.sitter?.id"
                                       class="w-full flex items-center justify-center gap-1.5 px-3 py-2.5 bg-amber-100/20 text-amber-600 font-bold text-xs rounded-lg hover:bg-amber-100/30 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        {{ __('messages.booking_view_sitter') }}
                                    </a>

                                    <form method="POST"
                                          :action="'{{ url('admin/bookings') }}/' + (selected.id ?? '') + '/complete'"
                                          x-show="selected.id && ['active','accepted','ongoing'].includes(selected.status)"
                                          @submit="if (!confirm({{ Js::from(__('messages.booking_confirm_force')) }})) $event.preventDefault()">
                                        @csrf
                                        <button type="submit"
                                                class="w-full flex items-center justify-center gap-1.5 px-3 py-2.5 bg-green-100/20 text-green-600 font-bold text-xs rounded-lg hover:bg-green-100/30 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ __('messages.booking_force_complete') }}
                                        </button>
                                    </form>

                                    <form method="POST"
                                          :action="'{{ url('admin/bookings') }}/' + (selected.id ?? '') + '/cancel'"
                                          x-show="selected.id && ['pending','active','accepted','ongoing'].includes(selected.status)"
                                          @submit="if (!confirm({{ Js::from(__('messages.booking_confirm_cancel')) }})) $event.preventDefault()">
                                        @csrf
                                        <button type="submit"
                                                class="w-full flex items-center justify-center gap-1.5 px-3 py-2.5 border-2 border-red-500 text-red-500 font-bold text-xs rounded-lg hover:bg-red-50 dark:hover:bg-red-950/20 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            {{ __('messages.booking_cancel') }}
                                        </button>
                                    </form>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Visit Logs Tab --}}
                <div x-show="activeTab === 'visits'" x-cloak>
                    <div x-show="selected.visits && selected.visits.length > 0" class="space-y-3">
                        <template x-for="(v, i) in selected.visits" :key="i">
                            <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-xl p-3 sm:p-4 border border-gray-100 dark:border-neutral-700 flex items-center justify-between">
                                <div>
                                    <p class="font-bold text-[#1B3B36] dark:text-white" x-text="v.label"></p>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400" x-text="v.date || '—'"></p>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold"
                                      :class="{
                                          'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': v.status === 'completed',
                                          'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400': v.status === 'pending',
                                          'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': v.status === 'cancelled',
                                      }"
                                      x-text="v.status.charAt(0).toUpperCase() + v.status.slice(1)"></span>
                            </div>
                        </template>
                    </div>
                    <div x-show="!selected.visits || selected.visits.length === 0" class="text-center py-8 sm:py-12 text-neutral-500 dark:text-neutral-400">
                        <svg class="w-12 h-12 sm:w-16 sm:h-16 mx-auto text-neutral-300 dark:text-neutral-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <h3 class="text-base sm:text-lg font-bold text-[#1B3B36] dark:text-white">{{ __('messages.booking_no_visit_logs') }}</h3>
                        <p class="text-sm">{{ __('messages.booking_visit_logs_hint') }}</p>
                    </div>
                </div>

                {{-- Guidelines --}}
                <div class="mt-4 sm:mt-6 p-3 sm:p-4 bg-blue-50 dark:bg-blue-950/20 rounded-2xl border border-blue-200 dark:border-blue-800/50">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="min-w-0">
                            <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white">{{ __('messages.booking_guidelines') }}</h3>
                            <ul class="mt-1 space-y-1 text-xs sm:text-sm text-neutral-600 dark:text-neutral-300">
                                <li>• {{ __('messages.booking_guide_1') }}</li>
                                <li>• {{ __('messages.booking_guide_2') }}</li>
                                <li>• {{ __('messages.booking_guide_3') }}</li>
                                <li>• {{ __('messages.booking_guide_4') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</x-app-layout>