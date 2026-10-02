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

    $statusClasses = fn (?string $s) => match ($s) {
        'pending'  => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
        'verified' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
        'rejected' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
        default    => 'bg-neutral-100 text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300',
    };

    $avatarClasses = fn (?string $s) => match ($s) {
        'pending'  => 'bg-amber-100/20 text-amber-600',
        'verified' => 'bg-green-100/20 text-green-600',
        'rejected' => 'bg-red-100/20 text-red-600',
        default    => 'bg-neutral-100/20 text-neutral-600',
    };

    $roleClasses = fn (?string $r) => match (strtolower((string) $r)) {
        'owner'  => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
        'sitter' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
        default  => 'bg-neutral-100 text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300',
    };

    $statusLabel = fn (?string $s) => match (strtolower((string) $s)) {
        'pending'  => __('messages.status_pending'),
        'verified' => __('messages.status_verified'),
        'rejected' => __('messages.status_rejected'),
        default    => ucfirst(str_replace('_', ' ', (string) ($s ?? 'unknown'))),
    };

    $roleLabel = fn (?string $r) => match (strtolower((string) $r)) {
        'owner'  => __('messages.role_owner'),
        'sitter' => __('messages.role_sitter'),
        default  => ucfirst((string) $r),
    };

    /* ------------------------------------------------------------------
     | Modal payload
     |------------------------------------------------------------------*/
    $modalRows = $verifications->map(function ($u) use ($initials, $statusLabel, $roleLabel) {
        $ocr = $u->ocr_result ?? [];
        $ocrSuccess = $ocr['success'] ?? false;

        return [
            'id'         => $u->id,
            'status'     => $u->id_validation_status,
            'status_label'=> $statusLabel($u->id_validation_status),
            'initials'   => $initials($u->full_name),
            'name'       => $u->full_name ?: '—',
            'email'      => $u->email ?? '—',
            'phone'      => $u->contact_number ?? '—',
            'role'       => $u->id_role,
            'role_label' => $roleLabel($u->id_role),
            'registered' => optional($u->created_at)->format('F j, Y') ?? '—',
            'id_type'    => match($u->id_type) {
                'passport'        => __('messages.pf_id_passport'),
                'drivers_license' => __('messages.pf_id_drivers_license'),
                'umid'            => __('messages.pf_id_umid'),
                'postal_id'       => __('messages.pf_id_postal'),
                'voters_id'       => __('messages.pf_id_voters'),
                'national_id'     => __('messages.pf_id_national'),
                'other'           => __('messages.pf_id_other'),
                default           => $u->id_type ?? '—',
            },
            'id_number'  => $ocr['id_number'] ?? '—',
            'id_front'   => $u->gov_id_url,
            'selfie'     => $u->selfie_url,
            'api' => [
                'face_match'      => $u->face_match_score,
                'doc_auth'        => $u->document_authenticity,
                'liveness'        => $u->liveness_detection,
                'id_expired'      => $u->id_expired,
                'name_match'      => $u->name_match,
                'birthdate_match' => $u->birthdate_match,
            ],
            'ocr' => [
                'success'    => $ocrSuccess,
                'name'       => $ocr['name']      ?? null,
                'id_number'  => $ocr['id_number'] ?? null,
                'dob'        => $ocr['dob']       ?? null,
                'raw_text'   => $ocr['raw_text']  ?? null,
                'error'      => $ocr['error']     ?? null,
            ],
            'notes' => $u->admin_notes ?? '',
        ];
    })->values();
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.dashboard') }}"
               class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div class="min-w-0">
                <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                    {{ __('messages.idv_title') }}
                </h1>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.idv_subtitle') }}</p>
            </div>
        </div>
    </x-slot>

    <div x-data="{
            search: '',
            statusFilter: 'all',
            resultFilter: 'all',
            roleFilter: 'all',
            rows: @js($modalRows),
            selected: null,
            showDetail: false,
            rejectModal: false,

            openDetail(id) {
                this.selected = this.rows.find(r => r.id === id) ?? null;
                this.showDetail = !!this.selected;
            },

            openRejectModal() {
                this.rejectModal = true;
            },

            confirmReject() {
                if (this.$refs.rejectForm) {
                    this.$refs.rejectForm.submit();
                }
            },

            overallResult(row) {
                const a = row.api || {};
                const norm = (v) => {
                    if (v === true || v === 1 || v === '1') return 'passed';
                    if (v === false || v === 0 || v === '0') return 'failed';
                    const s = String(v ?? '').toLowerCase();
                    if (['passed','pass','yes','true','matched','match'].includes(s)) return 'passed';
                    if (['failed','fail','no','unmatched','false'].includes(s)) return 'failed';
                    if (s === 'partial') return 'partial';
                    return 'unknown';
                };
                const states = [a.doc_auth, a.liveness, a.name_match, a.birthdate_match].map(norm);
                if (states.includes('failed'))  return 'failed';
                if (states.includes('partial')) return 'partial';
                if (states.every(s => s === 'passed')) return 'passed';
                return 'unknown';
            },
            matches(row) {
                const q = this.search.trim().toLowerCase();
                if (q && !(row.name.toLowerCase().includes(q) || row.email.toLowerCase().includes(q))) return false;
                if (this.statusFilter !== 'all' && row.status !== this.statusFilter) return false;
                if (this.roleFilter !== 'all' && String(row.role).toLowerCase() !== this.roleFilter) return false;
                if (this.resultFilter !== 'all' && this.overallResult(row) !== this.resultFilter) return false;
                return true;
            },
            get visibleCount() { return this.rows.filter(r => this.matches(r)).length; },

            approveUrl() { return this.selected ? '{{ route('admin.verification.id.approve', ['id' => '__ID__']) }}'.replace('__ID__', this.selected.id) : '#'; },
            rejectUrl()  { return this.selected ? '{{ route('admin.verification.id.reject',  ['id' => '__ID__']) }}'.replace('__ID__', this.selected.id) : '#'; },
            downloadUrl(){ return this.selected ? '{{ route('admin.verification.id.download',['id' => '__ID__']) }}'.replace('__ID__', this.selected.id) : '#'; }
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
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-6">

                <!-- Pending -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.idv_stat_pending') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-amber-600 dark:text-amber-400 mt-1">{{ number_format($stats['pending'] ?? 0) }}</h3>
                            <p class="text-[10px] sm:text-xs text-amber-600 dark:text-amber-400 mt-1">{{ __('messages.idv_label_awaiting') }}</p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-amber-100/20 flex items-center justify-center text-amber-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Verified -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.idv_stat_verified') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-green-600 dark:text-green-400 mt-1">{{ number_format($stats['verified'] ?? 0) }}</h3>
                            <p class="text-[10px] sm:text-xs text-green-600 dark:text-green-400 mt-1">{{ __('messages.idv_label_approved') }}</p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-green-100/20 flex items-center justify-center text-green-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Rejected -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.idv_stat_rejected') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-red-600 dark:text-red-400 mt-1">{{ number_format($stats['rejected'] ?? 0) }}</h3>
                            <p class="text-[10px] sm:text-xs text-red-500 dark:text-red-400 mt-1">{{ __('messages.idv_label_resubmit') }}</p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-red-100/20 flex items-center justify-center text-red-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Submitted -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.idv_stat_total') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-[#1B3B36] dark:text-white mt-1">{{ number_format($stats['total'] ?? 0) }}</h3>
                            <p class="text-[10px] sm:text-xs text-neutral-400 mt-1">{{ __('messages.idv_label_requests') }}</p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center text-neutral-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ② SEARCH + FILTERS -->
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-5 mb-4 sm:mb-6">
                <div class="flex flex-col lg:flex-row gap-3 sm:gap-4">
                    <!-- Search -->
                    <div class="flex-1 relative">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text"
                               x-model="search"
                               placeholder="{{ __('messages.idv_search_ph') }}"
                               class="w-full pl-9 pr-4 py-2 sm:py-2.5 text-xs sm:text-sm rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary transition">
                    </div>

                    <!-- Filters -->
                    <div class="grid grid-cols-2 sm:flex sm:flex-wrap items-center gap-2">
                        <select x-model="statusFilter"
                                class="px-2.5 py-2 sm:px-3 sm:py-2.5 text-xs sm:text-sm rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary transition">
                            <option value="all">{{ __('messages.idv_all_status') }}</option>
                            <option value="pending">{{ __('messages.status_pending') }}</option>
                            <option value="verified">{{ __('messages.status_verified') }}</option>
                            <option value="rejected">{{ __('messages.status_rejected') }}</option>
                        </select>

                        <select x-model="resultFilter"
                                class="px-2.5 py-2 sm:px-3 sm:py-2.5 text-xs sm:text-sm rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary transition">
                            <option value="all">{{ __('messages.idv_all_result') }}</option>
                            <option value="passed">{{ __('messages.idv_result_passed') }}</option>
                            <option value="failed">{{ __('messages.idv_result_failed') }}</option>
                            <option value="partial">{{ __('messages.idv_result_partial') }}</option>
                        </select>

                        <select x-model="roleFilter"
                                class="px-2.5 py-2 sm:px-3 sm:py-2.5 text-xs sm:text-sm rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary transition">
                            <option value="all">{{ __('messages.idv_all_roles') }}</option>
                            <option value="owner">{{ __('messages.role_owner') }}</option>
                            <option value="sitter">{{ __('messages.role_sitter') }}</option>
                        </select>

                        <button type="button"
                                class="col-span-2 sm:col-span-1 inline-flex items-center justify-center px-4 py-2 sm:py-2.5 bg-primary hover:bg-primary-600 text-white font-bold text-xs sm:text-sm rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            {{ __('messages.idv_search_btn') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- ③ VERIFICATION TABLE -->
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs sm:text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-800/30">
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">{{ __('messages.idv_col_user') }}</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">{{ __('messages.idv_col_role') }}</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">{{ __('messages.idv_col_submitted') }}</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">{{ __('messages.idv_col_status') }}</th>
                                <th class="text-right py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">{{ __('messages.idv_col_action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($verifications as $u)
                                <tr x-show="matches(rows.find(r => r.id === {{ $u->id }}))"
                                    class="border-b border-gray-100 dark:border-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition">
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                        <div class="flex items-center gap-2 sm:gap-3">
                                            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full {{ $avatarClasses($u->id_validation_status) }} flex items-center justify-center font-bold text-xs sm:text-sm shrink-0">
                                                {{ $initials($u->full_name) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-bold text-[#1B3B36] dark:text-white truncate">{{ $u->full_name ?: '—' }}</p>
                                                <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 truncate">{{ $u->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                        <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full text-[10px] font-bold {{ $roleClasses($u->id_role) }} whitespace-nowrap">
                                            {{ $roleLabel($u->id_role) }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4 text-neutral-500 dark:text-neutral-400 whitespace-nowrap">
                                        {{ optional($u->created_at)->format('M j, Y') }}
                                    </td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                        <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full text-[10px] font-bold {{ $statusClasses($u->id_validation_status) }} whitespace-nowrap">
                                            {{ $statusLabel($u->id_validation_status) }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4 text-right">
                                        <button type="button"
                                                @click="openDetail({{ $u->id }})"
                                                class="inline-flex items-center gap-1 sm:gap-1.5 px-2 py-1 sm:px-3 sm:py-1.5 bg-primary/10 text-primary font-bold text-xs rounded-lg hover:bg-primary/20 transition whitespace-nowrap">
                                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            {{ __('messages.idv_view') }}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-10 text-center text-neutral-500 dark:text-neutral-400 text-sm">
                                        {{ __('messages.idv_no_requests') }}
                                    </td>
                                </tr>
                            @endforelse

                            <tr x-show="rows.length > 0 && visibleCount === 0">
                                <td colspan="5" class="py-10 text-center text-neutral-500 dark:text-neutral-400 text-sm">
                                    {{ __('messages.idv_no_match') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-3 sm:px-4 py-3 border-t border-gray-100 dark:border-neutral-800">
                    <p class="text-xs sm:text-sm text-neutral-500 dark:text-neutral-400">
                        {{ __('messages.idv_showing', [
                            'from'  => $verifications->firstItem() ?? 0,
                            'to'    => $verifications->lastItem() ?? 0,
                            'total' => $verifications->total(),
                        ]) }}
                    </p>
                    <div class="flex items-center gap-1 overflow-x-auto">
                        @if ($verifications->onFirstPage())
                            <span class="px-2.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium text-neutral-400 whitespace-nowrap cursor-not-allowed">{{ __('messages.booking_previous') }}</span>
                        @else
                            <a href="{{ $verifications->previousPageUrl() }}"
                               class="px-2.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium text-neutral-500 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition whitespace-nowrap">{{ __('messages.booking_previous') }}</a>
                        @endif

                        @php
                            $current = $verifications->currentPage();
                            $last    = $verifications->lastPage();
                            $start   = max(1, $current - 2);
                            $end     = min($last, $current + 2);
                        @endphp

                        @if ($start > 1)
                            <a href="{{ $verifications->url(1) }}" class="px-2.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium text-neutral-600 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">1</a>
                            @if ($start > 2)<span class="px-2 text-neutral-400">...</span>@endif
                        @endif

                        @for ($page = $start; $page <= $end; $page++)
                            @if ($page == $current)
                                <span class="px-2.5 py-1.5 rounded-lg text-xs sm:text-sm font-bold bg-primary text-white">{{ $page }}</span>
                            @else
                                <a href="{{ $verifications->url($page) }}"
                                   class="px-2.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium text-neutral-600 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">{{ $page }}</a>
                            @endif
                        @endfor

                        @if ($end < $last)
                            @if ($end < $last - 1)<span class="px-2 text-neutral-400">...</span>@endif
                            <a href="{{ $verifications->url($last) }}" class="px-2.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium text-neutral-600 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">{{ $last }}</a>
                        @endif

                        @if ($verifications->hasMorePages())
                            <a href="{{ $verifications->nextPageUrl() }}"
                               class="px-2.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium text-neutral-500 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition whitespace-nowrap">{{ __('messages.booking_next') }}</a>
                        @else
                            <span class="px-2.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium text-neutral-400 whitespace-nowrap cursor-not-allowed">{{ __('messages.booking_next') }}</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ④ VERIFICATION DETAILS MODAL --}}
            <div x-show="showDetail"
                 x-cloak
                 class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:px-4 sm:py-8 bg-black/50 backdrop-blur-sm overflow-y-auto"
                 @click.away="showDetail = false"
                 @keydown.escape.window="showDetail = false">

                <div class="bg-white dark:bg-neutral-900 rounded-2xl sm:rounded-3xl shadow-2xl border border-gray-200 dark:border-neutral-800 max-w-5xl w-full max-h-[92vh] overflow-y-auto p-4 sm:p-8">

                    <div class="flex items-center justify-between mb-4 sm:mb-6">
                        <h2 class="text-lg sm:text-xl font-black text-[#1B3B36] dark:text-white">{{ __('messages.idv_modal_title') }}</h2>
                        <button type="button" @click="showDetail = false"
                                class="p-2 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                            <svg class="w-5 h-5 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6" x-show="selected">

                        <!-- LEFT: USER INFORMATION -->
                        <div class="lg:col-span-1 space-y-4">

                            <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                                <div class="flex items-center gap-3 sm:gap-4 mb-4">
                                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-lg sm:text-xl shrink-0"
                                         x-text="selected?.initials"></div>
                                    <div class="min-w-0">
                                        <p class="font-black text-[#1B3B36] dark:text-white text-base sm:text-lg truncate" x-text="selected?.name"></p>
                                        <p class="text-xs sm:text-sm text-neutral-500 dark:text-neutral-400" x-text="selected?.role_label"></p>
                                    </div>
                                </div>

                                <div class="space-y-2 text-sm">
                                    <div>
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.idv_email') }}</p>
                                        <p class="font-bold text-[#1B3B36] dark:text-white break-all" x-text="selected?.email"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.idv_phone') }}</p>
                                        <p class="font-bold text-[#1B3B36] dark:text-white" x-text="selected?.phone"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.idv_registered') }}</p>
                                        <p class="font-bold text-[#1B3B36] dark:text-white" x-text="selected?.registered"></p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">{{ __('messages.idv_status') }}</p>
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-bold"
                                      :class="{
                                          'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400': selected?.status === 'pending',
                                          'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': selected?.status === 'verified',
                                          'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': selected?.status === 'rejected',
                                      }"
                                      x-text="selected?.status_label"></span>
                            </div>

                        </div>

                        <!-- CENTER: ID + SELFIE -->
                        <div class="lg:col-span-1 space-y-4">

                            <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-3">{{ __('messages.idv_submitted_id') }}</p>
                                <div class="bg-white dark:bg-neutral-900 rounded-xl border border-gray-200 dark:border-neutral-700 p-3 sm:p-4 text-center">
                                    <div class="aspect-[4/3] bg-neutral-100 dark:bg-neutral-800 rounded-lg flex items-center justify-center overflow-hidden">
                                        <template x-if="selected?.id_front">
                                            <div class="relative group w-full h-full">
                                                <img :src="selected.id_front"
                                                    alt="{{ __('messages.idv_submitted_id') }}"
                                                    class="w-full h-full object-cover rounded-lg cursor-pointer transition group-hover:opacity-80"
                                                    @click="window.open(selected.id_front, '_blank')">

                                                <div class="absolute inset-0 flex items-center justify-center gap-3 opacity-0 group-hover:opacity-100 transition bg-black/40 rounded-lg pointer-events-none">
                                                    <button type="button"
                                                            @click.stop="window.open(selected.id_front, '_blank')"
                                                            class="p-2 bg-white/20 backdrop-blur-sm rounded-full text-white hover:bg-white/30 transition pointer-events-auto"
                                                            title="View Full Size">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                                        </svg>
                                                    </button>

                                                    <a :href="selected.id_front"
                                                    download="id_front.jpg"
                                                    @click.stop
                                                    class="p-2 bg-white/20 backdrop-blur-sm rounded-full text-white hover:bg-white/30 transition pointer-events-auto"
                                                    title="Download">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                        </svg>
                                                    </a>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="mt-3 text-left text-xs sm:text-sm">
                                        <p><span class="text-neutral-500 dark:text-neutral-400">{{ __('messages.idv_type') }}</span> <span class="font-bold" x-text="selected?.id_type"></span></p>
                                        <p><span class="text-neutral-500 dark:text-neutral-400">{{ __('messages.idv_id_number') }}</span> <span class="font-bold" x-text="selected?.id_number"></span></p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-3">{{ __('messages.idv_selfie') }}</p>
                                <div class="bg-white dark:bg-neutral-900 rounded-xl border border-gray-200 dark:border-neutral-700 p-3 sm:p-4 text-center">
                                    <div class="aspect-[4/3] bg-neutral-100 dark:bg-neutral-800 rounded-lg flex items-center justify-center overflow-hidden">
                                        <template x-if="selected?.selfie">
                                            <div class="relative group w-full h-full">
                                                <img :src="selected.selfie"
                                                    alt="{{ __('messages.idv_selfie') }}"
                                                    class="w-full h-full object-cover rounded-lg cursor-pointer transition group-hover:opacity-80"
                                                    @click="window.open(selected.selfie, '_blank')">

                                                <div class="absolute inset-0 flex items-center justify-center gap-3 opacity-0 group-hover:opacity-100 transition bg-black/40 rounded-lg pointer-events-none">
                                                    <button type="button"
                                                            @click.stop="window.open(selected.selfie, '_blank')"
                                                            class="p-2 bg-white/20 backdrop-blur-sm rounded-full text-white hover:bg-white/30 transition pointer-events-auto"
                                                            title="View Full Size">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                                        </svg>
                                                    </button>

                                                    <a :href="selected.selfie"
                                                    download="selfie.jpg"
                                                    @click.stop
                                                    class="p-2 bg-white/20 backdrop-blur-sm rounded-full text-white hover:bg-white/30 transition pointer-events-auto"
                                                    title="Download">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                        </svg>
                                                    </a>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- RIGHT: API RESULTS + ACTIONS -->
                        <div class="lg:col-span-1 space-y-4">

                            <!-- OCR EXTRACTION RESULT -->
                            <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                                <div class="flex items-center justify-between mb-3">
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.idv_ocr_title') }}</p>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold"
                                        :class="selected?.ocr?.success ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400'"
                                        x-text="selected?.ocr?.success ? '{{ __('messages.idv_ocr_success') }}' : '{{ __('messages.idv_ocr_failed') }}'"></span>
                                </div>

                                <template x-if="selected?.ocr?.success">
                                    <div class="space-y-2 text-sm">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-neutral-600 dark:text-neutral-400">{{ __('messages.idv_ocr_name') }}</span>
                                            <span class="font-bold text-[#1B3B36] dark:text-white text-right break-words" x-text="selected?.ocr?.name || '—'"></span>
                                        </div>
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-neutral-600 dark:text-neutral-400">{{ __('messages.idv_ocr_id_number') }}</span>
                                            <span class="font-bold text-[#1B3B36] dark:text-white text-right break-words" x-text="selected?.ocr?.id_number || '—'"></span>
                                        </div>
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-neutral-600 dark:text-neutral-400">{{ __('messages.idv_ocr_dob') }}</span>
                                            <span class="font-bold text-[#1B3B36] dark:text-white text-right break-words" x-text="selected?.ocr?.dob || '—'"></span>
                                        </div>

                                        <div class="mt-3 pt-3 border-t border-gray-200 dark:border-neutral-700">
                                            <p class="text-[10px] font-bold text-neutral-500 uppercase tracking-wider mb-1">{{ __('messages.idv_ocr_compare') }}</p>
                                            <div class="flex items-center gap-2 text-xs">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full font-bold"
                                                    :class="selected?.ocr?.name && selected?.name && selected.ocr.name.toLowerCase().includes(selected.name.toLowerCase().split(' ')[0])
                                                        ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                                                        : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400'"
                                                    x-text="selected?.ocr?.name && selected?.name && selected.ocr.name.toLowerCase().includes(selected.name.toLowerCase().split(' ')[0])
                                                        ? '{{ __('messages.idv_ocr_match_yes') }}'
                                                        : '{{ __('messages.idv_ocr_match_check') }}'"></span>
                                                <span class="text-neutral-500 dark:text-neutral-400 text-[10px]">{{ __('messages.idv_ocr_match_hint') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="!selected?.ocr?.success">
                                    <div class="text-xs text-amber-600 dark:text-amber-400">
                                        <p>{{ __('messages.idv_ocr_failed_desc') }}</p>
                                        <template x-if="selected?.ocr?.error">
                                            <p class="mt-1 text-[10px] text-neutral-500 break-all" x-text="'Error: ' + selected.ocr.error"></p>
                                        </template>
                                    </div>
                                </template>
                            </div>

                            <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-3">{{ __('messages.idv_api_title') }}</p>
                                <div class="space-y-3 text-sm">

                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-neutral-600 dark:text-neutral-400">{{ __('messages.idv_face_match') }}</span>
                                        <span class="font-bold shrink-0"
                                              :class="parseFloat(selected?.api?.face_match ?? 0) >= 80 ? 'text-green-600 dark:text-green-400' : 'text-amber-600 dark:text-amber-400'"
                                              x-text="selected?.api?.face_match != null ? (parseFloat(selected.api.face_match) + '%') : '—'"></span>
                                    </div>

                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-neutral-600 dark:text-neutral-400">{{ __('messages.idv_doc_auth') }}</span>
                                        <span class="font-bold shrink-0"
                                              :class="['passed','pass','yes','true','matched'].includes(String(selected?.api?.doc_auth ?? '').toLowerCase()) ? 'text-green-600 dark:text-green-400' : (String(selected?.api?.doc_auth ?? '').toLowerCase() === 'failed' ? 'text-red-600 dark:text-red-400' : 'text-neutral-500')"
                                              x-text="selected?.api?.doc_auth ? String(selected.api.doc_auth).charAt(0).toUpperCase() + String(selected.api.doc_auth).slice(1) : '—'"></span>
                                    </div>

                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-neutral-600 dark:text-neutral-400">{{ __('messages.idv_liveness') }}</span>
                                        <span class="font-bold shrink-0"
                                              :class="['passed','pass','yes','true'].includes(String(selected?.api?.liveness ?? '').toLowerCase()) ? 'text-green-600 dark:text-green-400' : (String(selected?.api?.liveness ?? '').toLowerCase() === 'failed' ? 'text-red-600 dark:text-red-400' : 'text-neutral-500')"
                                              x-text="selected?.api?.liveness ? String(selected.api.liveness).charAt(0).toUpperCase() + String(selected.api.liveness).slice(1) : '—'"></span>
                                    </div>

                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-neutral-600 dark:text-neutral-400">{{ __('messages.idv_id_expired') }}</span>
                                        <span class="font-bold shrink-0"
                                              :class="['yes','true','1'].includes(String(selected?.api?.id_expired ?? '').toLowerCase()) ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'"
                                              x-text="selected?.api?.id_expired == null ? '—' : (['yes','true','1'].includes(String(selected.api.id_expired).toLowerCase()) ? '{{ __('messages.idv_yes') }}' : '{{ __('messages.idv_no') }}')"></span>
                                    </div>

                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-neutral-600 dark:text-neutral-400">{{ __('messages.idv_name_match') }}</span>
                                        <span class="font-bold shrink-0"
                                              :class="['passed','pass','yes','true','matched','match'].includes(String(selected?.api?.name_match ?? '').toLowerCase()) ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'"
                                              x-text="selected?.api?.name_match ? (['matched','match','passed','pass','yes','true'].includes(String(selected.api.name_match).toLowerCase()) ? '{{ __('messages.idv_matched') }}' : '{{ __('messages.idv_not_matched') }}') : '—'"></span>
                                    </div>

                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-neutral-600 dark:text-neutral-400">{{ __('messages.idv_birthdate_match') }}</span>
                                        <span class="font-bold shrink-0"
                                              :class="['passed','pass','yes','true','matched','match'].includes(String(selected?.api?.birthdate_match ?? '').toLowerCase()) ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'"
                                              x-text="selected?.api?.birthdate_match ? (['matched','match','passed','pass','yes','true'].includes(String(selected.api.birthdate_match).toLowerCase()) ? '{{ __('messages.idv_matched') }}' : '{{ __('messages.idv_not_matched') }}') : '—'"></span>
                                    </div>

                                </div>
                            </div>

                            <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">{{ __('messages.idv_admin_notes') }}</p>
                                <textarea rows="3"
                                          x-model="selected.notes"
                                          placeholder="{{ __('messages.idv_admin_notes_ph') }}"
                                          class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-3 py-2.5 sm:px-4 sm:py-2.5 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary text-sm font-medium resize-none"></textarea>
                            </div>

                            <div class="space-y-2">
                                <div class="flex flex-col gap-2">

                                    {{-- Approve — hide kung verified/rejected --}}
                                    <form method="POST" :action="approveUrl()"
                                          x-show="!['rejected', 'verified'].includes(selected?.status)">
                                        @csrf
                                        <input type="hidden" name="admin_notes" :value="selected?.notes ?? ''">
                                        <button type="submit"
                                                class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-green-500 hover:bg-green-600 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ __('messages.idv_approve') }}
                                        </button>
                                    </form>

                                    {{-- Reject — hide kung verified/rejected --}}
                                    <form method="POST" :action="rejectUrl()"
                                          x-show="!['rejected', 'verified'].includes(selected?.status)"
                                          x-ref="rejectForm">
                                        @csrf
                                        <input type="hidden" name="admin_notes" :value="selected?.notes ?? ''">
                                        <button type="button"
                                                @click="openRejectModal()"
                                                class="w-full flex items-center justify-center gap-2 px-4 py-3 border-2 border-red-500 text-red-500 hover:bg-red-50 dark:hover:bg-red-950/20 font-bold text-sm rounded-xl transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            {{ __('messages.idv_reject') }}
                                        </button>
                                    </form>

                                </div>

                                {{-- Download --}}
                                <a :href="downloadUrl()"
                                   class="w-full flex items-center justify-center gap-2 px-4 py-3 border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-sm rounded-xl hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    {{ __('messages.idv_download') }}
                                </a>
                            </div>

                        </div>

                    </div>

                    <!-- GUIDELINES -->
                    <div class="mt-4 sm:mt-6 p-3 sm:p-4 bg-blue-50 dark:bg-blue-950/20 rounded-2xl border border-blue-200 dark:border-blue-800/50">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="min-w-0">
                                <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white">{{ __('messages.idv_guidelines') }}</h3>
                                <ul class="mt-1 space-y-1 text-xs sm:text-sm text-neutral-600 dark:text-neutral-300">
                                    <li>• {{ __('messages.idv_guide_1') }}</li>
                                    <li>• {{ __('messages.idv_guide_2') }}</li>
                                    <li>• {{ __('messages.idv_guide_3') }}</li>
                                    <li>• {{ __('messages.idv_guide_4') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ⑤ REJECT CONFIRMATION MODAL --}}
            <div x-show="rejectModal" x-cloak x-transition.opacity
                 class="fixed inset-0 z-[300] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
                 @click.away="rejectModal = false"
                 @keydown.escape.window="rejectModal = false">

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-neutral-800 max-w-md w-full p-5 sm:p-6"
                     @click.stop>

                    {{-- Header --}}
                    <div class="flex items-start gap-3 mb-4">
                        <div class="w-11 h-11 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center text-red-600 dark:text-red-400 shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-base font-black text-[#1B3B36] dark:text-white">
                                {{ __('messages.idv_reject_confirm_title') }}
                            </h3>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">
                                {{ __('messages.idv_reject_confirm_desc') }}
                            </p>
                        </div>
                    </div>

                    {{-- Warning --}}
                    <div class="p-3 mb-4 rounded-xl bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-800">
                        <p class="text-[11px] text-red-700 dark:text-red-400 font-bold">
                            ⚠️ {{ __('messages.idv_reject_warning') }}
                        </p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-2">
                        <button type="button" @click="rejectModal = false"
                                class="flex-1 px-4 py-2.5 rounded-xl border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-xs hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                            {{ __('messages.idv_cancel') }}
                        </button>

                        <button type="button" @click="confirmReject()"
                                class="flex-1 px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs transition shadow-md hover:shadow-lg">
                            {{ __('messages.idv_yes_reject') }}
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

</x-app-layout>
