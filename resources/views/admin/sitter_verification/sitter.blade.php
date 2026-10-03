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
                    {{ __('messages.sv_title') }}
                </h1>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.sv_subtitle') }}</p>
            </div>
        </div>
    </x-slot>

    <div x-data="sitterVerification({
            applicants: {{ Js::from($applicantsData) }},
            activeStatus: '{{ $status }}'
         })"
         class="py-4 sm:py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200">
        <div class="w-full sm:max-w-7xl mx-auto px-2 sm:px-16 lg:px-24">

            {{-- ① STATISTICS CARDS --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-6">

                <a href="{{ route('admin.verification.sitter', ['status' => 'pending']) }}"
                   class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition
                          {{ $status === 'pending' ? 'ring-2 ring-amber-400' : '' }}">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.sv_stat_pending') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-amber-600 dark:text-amber-400 mt-1">
                                {{ $stats['pending'] }}
                            </h3>
                            <p class="text-[10px] sm:text-xs text-amber-600 dark:text-amber-400 mt-1">{{ __('messages.sv_label_awaiting') }}</p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-amber-100/20 flex items-center justify-center text-amber-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.verification.sitter', ['status' => 'verified']) }}"
                   class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition
                          {{ $status === 'verified' ? 'ring-2 ring-green-400' : '' }}">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.sv_stat_approved') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-green-600 dark:text-green-400 mt-1">
                                {{ $stats['approved'] }}
                            </h3>
                            <p class="text-[10px] sm:text-xs text-green-600 dark:text-green-400 mt-1">{{ __('messages.sv_label_verified') }}</p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-green-100/20 flex items-center justify-center text-green-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.verification.sitter', ['status' => 'rejected']) }}"
                   class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition
                          {{ $status === 'rejected' ? 'ring-2 ring-red-400' : '' }}">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.sv_stat_rejected') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-red-600 dark:text-red-400 mt-1">
                                {{ $stats['rejected'] }}
                            </h3>
                            <p class="text-[10px] sm:text-xs text-red-500 dark:text-red-400 mt-1">{{ __('messages.sv_label_resubmit') }}</p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-red-100/20 flex items-center justify-center text-red-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.verification.sitter', ['status' => 'all']) }}"
                   class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition
                          {{ $status === 'all' ? 'ring-2 ring-primary' : '' }}">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.sv_stat_total') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-[#1B3B36] dark:text-white mt-1">
                                {{ $stats['total'] }}
                            </h3>
                            <p class="text-[10px] sm:text-xs text-neutral-400 mt-1">{{ __('messages.sv_label_applications') }}</p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center text-neutral-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                    </div>
                </a>
            </div>

            {{-- ② SEARCH + FILTERS --}}
            <form method="GET" action="{{ route('admin.verification.sitter') }}"
                  class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-5 mb-4 sm:mb-6">
                <div class="flex flex-col lg:flex-row gap-3 sm:gap-4">
                    <div class="flex-1 relative">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" name="search" value="{{ $search }}"
                               placeholder="{{ __('messages.sv_search_ph') }}"
                               class="w-full pl-9 pr-4 py-2 sm:py-2.5 text-xs sm:text-sm rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary transition">
                    </div>

                    <div class="grid grid-cols-3 sm:flex sm:flex-wrap items-center gap-2">
                        <select name="status"
                                class="px-2.5 py-2 sm:px-3 sm:py-2.5 text-xs sm:text-sm rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary transition">
                            <option value="all"      @selected($status === 'all')>{{ __('messages.sv_all_status') }}</option>
                            <option value="pending"  @selected($status === 'pending')>{{ __('messages.sv_stat_pending') }}</option>
                            <option value="verified" @selected($status === 'verified')>{{ __('messages.sv_stat_approved') }}</option>
                            <option value="rejected" @selected($status === 'rejected')>{{ __('messages.sv_stat_rejected') }}</option>
                        </select>

                        <select name="date"
                                class="px-2.5 py-2 sm:px-3 sm:py-2.5 text-xs sm:text-sm rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary transition">
                            <option value="latest" @selected($dateSort === 'latest')>{{ __('messages.sv_latest') }}</option>
                            <option value="oldest" @selected($dateSort === 'oldest')>{{ __('messages.sv_oldest') }}</option>
                        </select>

                        <select name="experience"
                                class="px-2.5 py-2 sm:px-3 sm:py-2.5 text-xs sm:text-sm rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary transition">
                            <option value="all"   @selected($experience === 'all')>{{ __('messages.sv_all_exp') }}</option>
                            <option value="3+"    @selected($experience === '3+')>{{ __('messages.sv_exp_3plus') }}</option>
                            <option value="1-3"   @selected($experience === '1-3')>{{ __('messages.sv_exp_1to3') }}</option>
                            <option value="<1"    @selected($experience === '<1')>{{ __('messages.sv_exp_less1') }}</option>
                            <option value="none"  @selected($experience === 'none')>{{ __('messages.sv_exp_none') }}</option>
                        </select>

                        <button type="submit"
                                class="px-4 py-2 sm:py-2.5 bg-primary hover:bg-primary-600 text-white font-bold text-xs sm:text-sm rounded-xl transition col-span-3 sm:col-span-1">
                            {{ __('messages.sv_apply') }}
                        </button>
                    </div>
                </div>
            </form>

            {{-- ③ APPLICANTS TABLE --}}
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs sm:text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-800/30">
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">{{ __('messages.sv_col_applicant') }}</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">{{ __('messages.sv_col_experience') }}</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">{{ __('messages.sv_col_submitted') }}</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">{{ __('messages.sv_col_status') }}</th>
                                <th class="text-right py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">{{ __('messages.sv_col_action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($applicants as $applicant)
                                @php
                                    $name = trim(($applicant->f_name ?? '') . ' ' . ($applicant->l_name ?? '')) ?: 'User';
                                    $initials = strtoupper(substr($applicant->f_name ?? 'U', 0, 1));
                                    $expYears = $applicant->sitterProfile?->experience_years ?? 0;
                                    $statusKey = $applicant->sitter_status;

                                    $statusMap = [
                                        'pending'  => ['bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400', __('messages.sv_stat_pending')],
                                        'approved' => ['bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400', __('messages.sv_stat_approved')],
                                        'rejected' => ['bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400', __('messages.sv_stat_rejected')],
                                    ];

                                    [$statusClass, $statusLabel] = $statusMap[$statusKey] ?? $statusMap['pending'];

                                    $avatarColor = match($statusKey) {
                                        'approved' => 'bg-green-100/20 text-green-600',
                                        'rejected' => 'bg-red-100/20 text-red-600',
                                        default    => 'bg-amber-100/20 text-amber-600',
                                    };
                                @endphp
                                <tr class="border-b border-gray-100 dark:border-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition">
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                        <div class="flex items-center gap-2 sm:gap-3">
                                            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full {{ $avatarColor }} flex items-center justify-center font-bold text-xs sm:text-sm shrink-0">
                                                {{ $initials }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-bold text-[#1B3B36] dark:text-white truncate">{{ $name }}</p>
                                                <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 truncate">{{ $applicant->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            {{-- Experience --}}
                                            <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 whitespace-nowrap">
                                                {{ $expYears }} {{ $expYears == 1 ? __('messages.sv_year') : __('messages.sv_years') }}
                                            </span>

                                            {{-- Sitter Type Badge --}}
                                            @php
                                                $sitterType = $applicant->sitterProfile?->sitter_type ?? 'small_pets';
                                                $typeBadge = match($sitterType) {
                                                    'small_pets'  => ['icon' => '🐱', 'label' => 'Small',  'class' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'],
                                                    'large_pets'  => ['icon' => '🐕', 'label' => 'Large',  'class' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'],
                                                    'exotic_pets' => ['icon' => '🦜', 'label' => 'Exotic', 'class' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400'],
                                                    'all_pets'    => ['icon' => '🐾', 'label' => 'All',    'class' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400'],
                                                    default       => ['icon' => '🐱', 'label' => 'Small',  'class' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'],
                                                };
                                            @endphp
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full text-[10px] font-bold {{ $typeBadge['class'] }} whitespace-nowrap">
                                                <span>{{ $typeBadge['icon'] }}</span>
                                                {{ $typeBadge['label'] }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4 text-neutral-500 dark:text-neutral-400 whitespace-nowrap">
                                        {{ $applicant->updated_at?->format('M d, Y') }}
                                    </td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                        <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full text-[10px] font-bold {{ $statusClass }} whitespace-nowrap">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4 text-right">
                                        <button type="button"
                                                @click="openModal({{ $applicant->id }})"
                                                class="inline-flex items-center gap-1 sm:gap-1.5 px-2 py-1 sm:px-3 sm:py-1.5 bg-primary/10 text-primary font-bold text-xs rounded-lg hover:bg-primary/20 transition whitespace-nowrap">
                                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            {{ $statusKey === 'pending' ? __('messages.sv_review') : __('messages.sv_view') }}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center">
                                        <svg class="w-12 h-12 mx-auto text-neutral-300 dark:text-neutral-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                        <p class="text-sm text-neutral-500 dark:text-neutral-400">
                                            {{ __('messages.sv_no_applications') }}
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($applicants->hasPages())
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-3 sm:px-4 py-3 border-t border-gray-100 dark:border-neutral-800">
                        <p class="text-xs sm:text-sm text-neutral-500 dark:text-neutral-400">
                            {{ __('messages.sv_showing', [
                                'from'  => $applicants->firstItem(),
                                'to'    => $applicants->lastItem(),
                                'total' => $applicants->total(),
                            ]) }}
                        </p>
                        <div class="flex items-center gap-1 overflow-x-auto">
                            {{ $applicants->links() }}
                        </div>
                    </div>
                @endif
            </div>

        </div>

        {{-- ④ REVIEW MODAL --}}
        @include('admin.sitter_verification.partials.application-modal')
    </div>

    @push('scripts')
    <script>
        function sitterVerification(config) {
            return {
                applicants: config.applicants || [],
                selectedId: null,
                showDetail: false,
                approveOpen: false,
                rejectOpen: false,
                rejectReason: '',

                get selected() {
                    return this.applicants.find(a => a.id === this.selectedId) || {};
                },

                openModal(id) {
                    this.selectedId = id;
                    this.showDetail = true;
                    this.approveOpen = false;
                    this.rejectOpen = false;
                    this.rejectReason = '';
                },

                statusLabel(status) {
                    const map = {
                        pending:  '{{ __('messages.sv_stat_pending') }}',
                        approved: '{{ __('messages.sv_stat_approved') }}',
                        verified: '{{ __('messages.sv_stat_approved') }}',
                        rejected: '{{ __('messages.sv_stat_rejected') }}',
                    };
                    return map[status] || '{{ __('messages.sv_stat_pending') }}';
                },

                statusClass(status) {
                    const map = {
                        pending:  'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                        approved: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                        verified: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                        rejected: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                    };
                    return map[status] || map.pending;
                },
            }
        }
    </script>
    @endpush
</x-app-layout>
