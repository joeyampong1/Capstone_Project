{{-- ========================================== --}}
{{-- MAIN REVIEW MODAL                          --}}
{{-- ========================================== --}}
<div x-show="showDetail"
     x-cloak
     x-transition.opacity
     class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:px-4 sm:py-8 bg-black/50 backdrop-blur-sm overflow-y-auto"
     @click.away="showDetail = false">

    <div class="bg-white dark:bg-neutral-900 rounded-2xl sm:rounded-3xl shadow-2xl border border-gray-200 dark:border-neutral-800 max-w-6xl w-full max-h-[92vh] overflow-y-auto p-4 sm:p-8"
         @click.stop>

        {{-- Modal Header --}}
        <div class="flex items-center justify-between mb-4 sm:mb-6">
            <h2 class="text-lg sm:text-xl font-black text-[#1B3B36] dark:text-white">{{ __('messages.sv_modal_title') }}</h2>
            <button type="button" @click="showDetail = false"
                    class="p-2 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                <svg class="w-5 h-5 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">

            {{-- LEFT: APPLICANT INFO --}}
            <div class="lg:col-span-1 space-y-4">

                <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                    <div class="flex items-center gap-3 sm:gap-4 mb-4">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-lg sm:text-xl shrink-0"
                             x-text="selected.initials"></div>
                        <div class="min-w-0">
                            <p class="font-black text-[#1B3B36] dark:text-white text-base sm:text-lg truncate" x-text="selected.name"></p>
                            <p class="text-xs sm:text-sm text-neutral-500 dark:text-neutral-400">{{ __('messages.sv_role_applicant') }}</p>
                        </div>
                    </div>

                    <div class="space-y-2 text-sm">
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sv_email') }}</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white break-all" x-text="selected.email"></p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sv_phone') }}</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white" x-text="selected.phone"></p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sv_location') }}</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white" x-text="selected.location"></p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sv_registered_since') }}</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white" x-text="selected.registered_at"></p>
                        </div>
                    </div>
                </div>

                {{-- Experience + Sitter Type --}}
                <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">{{ __('messages.sv_experience') }}</p>
                    <p class="font-black text-lg text-[#1B3B36] dark:text-white">
                        <span x-text="selected.experience_years"></span>
                        <span x-text="selected.experience_years == 1 ? '{{ __('messages.sv_year') }}' : '{{ __('messages.sv_years') }}'"></span>
                    </p>

                    {{-- Sitter Type Badge --}}
                    <div class="mt-3 pt-3 border-t border-gray-200 dark:border-neutral-700">
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-1.5">{{ __('messages.sv_sitter_type') }}</p>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold"
                              :class="{
                                  'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400': selected.sitter_type === 'small_pets',
                                  'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': selected.sitter_type === 'large_pets',
                                  'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400': selected.sitter_type === 'exotic_pets',
                                  'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400': selected.sitter_type === 'all_pets'
                              }">
                            <span x-text="selected.sitter_type_icon"></span>
                            <span x-text="selected.sitter_type_label"></span>
                        </span>
                    </div>

                    <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-3 leading-relaxed italic"
                       x-text="selected.bio ? '\"' + selected.bio + '\"' : '{{ __('messages.sv_no_bio') }}'"></p>
                </div>

                {{-- Performance Summary --}}
                <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">{{ __('messages.sv_perf_summary') }}</p>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-neutral-600 dark:text-neutral-400">{{ __('messages.sv_avg_rating') }}</span>
                            <span class="font-bold text-amber-600 dark:text-amber-400 shrink-0">
                                ⭐ <span x-text="Number(selected.average_rating || 0).toFixed(1)"></span>
                            </span>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-neutral-600 dark:text-neutral-400">{{ __('messages.sv_completed_bookings') }}</span>
                            <span class="font-bold text-[#1B3B36] dark:text-white shrink-0" x-text="selected.completed_bookings ?? 0"></span>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-neutral-600 dark:text-neutral-400">{{ __('messages.sv_completed_visits') }}</span>
                            <span class="font-bold text-[#1B3B36] dark:text-white shrink-0" x-text="selected.completed_visits ?? 0"></span>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-neutral-600 dark:text-neutral-400">{{ __('messages.sv_cancelled') }}</span>
                            <span class="font-bold text-amber-600 dark:text-amber-400 shrink-0" x-text="selected.cancelled_count ?? 0"></span>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-neutral-600 dark:text-neutral-400">{{ __('messages.sv_missed_visits') }}</span>
                            <span class="font-bold text-green-600 dark:text-green-400 shrink-0" x-text="selected.missed_visits ?? 0"></span>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-neutral-600 dark:text-neutral-400">{{ __('messages.sv_complaints') }}</span>
                            <span class="font-bold text-green-600 dark:text-green-400 shrink-0" x-text="selected.complaints_count ?? 0"></span>
                        </div>
                    </div>
                </div>

                {{-- Services --}}
                <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">{{ __('messages.sv_services_offered') }}</p>
                    <div class="grid grid-cols-2 gap-1">
                        <template x-for="(svc, i) in (selected.services || [])" :key="i">
                            <span class="flex items-center gap-2 text-sm text-[#1B3B36] dark:text-white">
                                <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span x-text="svc"></span>
                            </span>
                        </template>
                        <template x-if="!selected.services || selected.services.length === 0">
                            <span class="text-xs text-neutral-400 col-span-2">{{ __('messages.sv_no_services') }}</span>
                        </template>
                    </div>
                </div>

                {{-- Pets Accepted --}}
                <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">{{ __('messages.sv_pets_accepted') }}</p>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="(pet, i) in (selected.pets_accepted || [])" :key="i">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary/10 text-primary font-bold text-xs rounded-full">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span x-text="pet"></span>
                            </span>
                        </template>
                        <template x-if="!selected.pets_accepted || selected.pets_accepted.length === 0">
                            <span class="text-xs text-neutral-400">{{ __('messages.sv_no_pets') }}</span>
                        </template>
                    </div>
                </div>

            </div>

            {{-- CENTER: DOCUMENTS --}}
            <div class="lg:col-span-1 space-y-4">
                <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-3">{{ __('messages.sv_supporting_docs') }}</p>
                    <div class="space-y-2 sm:space-y-3">
                        <template x-for="(doc, i) in (selected.documents || [])" :key="i">
                            <div class="bg-white dark:bg-neutral-900 rounded-xl border border-gray-200 dark:border-neutral-700 p-2.5 sm:p-3 flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg bg-blue-100/20 flex items-center justify-center text-blue-600 shrink-0">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-xs sm:text-sm text-[#1B3B36] dark:text-white truncate" x-text="doc.name"></p>
                                        <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400" x-text="doc.type || 'PDF'"></p>
                                    </div>
                                </div>
                                <a :href="doc.url" target="_blank"
                                   class="px-2.5 py-1 sm:px-3 sm:py-1 text-xs font-bold text-primary hover:underline shrink-0">
                                    {{ __('messages.sv_doc_view') }}
                                </a>
                            </div>
                        </template>
                        <template x-if="!selected.documents || selected.documents.length === 0">
                            <p class="text-xs text-neutral-400 text-center py-4">{{ __('messages.sv_no_docs') }}</p>
                        </template>
                    </div>

                    <div class="mt-3 p-3 bg-neutral-100 dark:bg-neutral-800 rounded-xl">
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sv_doc_count') }}</p>
                        <p class="font-bold text-[#1B3B36] dark:text-white">
                            <span x-text="(selected.documents || []).length"></span> {{ __('messages.sv_files_submitted') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- RIGHT: SUMMARY + ACTIONS --}}
            <div class="lg:col-span-1 space-y-4">

                <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">{{ __('messages.sv_app_summary') }}</p>
                    <div class="space-y-2 text-sm">
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sv_app_date') }}</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white" x-text="selected.applied_at"></p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sv_status') }}</p>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold"
                                  :class="statusClass(selected.status)">
                                <span x-text="statusLabel(selected.status)"></span>
                            </span>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sv_base_rate') }}</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">
                                ₱<span x-text="Number(selected.base_rate || 0).toFixed(0)"></span> {{ __('messages.sv_per_visit') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- ACTION BUTTONS --}}
                <div class="space-y-2">

                    {{-- Approve button --}}
                    <button type="button"
                            x-show="selected.status !== 'approved' && selected.status !== 'verified'"
                            @click="approveOpen = true"
                            class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-green-500 hover:bg-green-600 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ __('messages.sv_approve_btn') }}
                    </button>

                    {{-- Reject button --}}
                    <button type="button"
                            x-show="selected.status !== 'rejected'"
                            @click="rejectOpen = true"
                            class="w-full flex items-center justify-center gap-2 px-4 py-3 border-2 border-red-500 text-red-500 hover:bg-red-50 dark:hover:bg-red-950/20 font-bold text-sm rounded-xl transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        {{ __('messages.sv_reject_btn') }}
                    </button>

                    {{-- Download button --}}
                    <a :href="`{{ route('admin.verification.sitter.download', ['id' => '__ID__']) }}`.replace('__ID__', selected.id)"
                    class="w-full flex items-center justify-center gap-2 px-4 py-3 border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-sm rounded-xl hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        {{ __('messages.sv_download_all') }}
                    </a>

                </div>

            </div>

        </div>

        {{-- GUIDELINES --}}
        <div class="mt-4 sm:mt-6 p-3 sm:p-4 bg-blue-50 dark:bg-blue-950/20 rounded-2xl border border-blue-200 dark:border-blue-800/50">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="min-w-0">
                    <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white">{{ __('messages.sv_guidelines') }}</h3>
                    <ul class="mt-1 space-y-1 text-xs sm:text-sm text-neutral-600 dark:text-neutral-300">
                        <li>• {{ __('messages.sv_guide_1') }}</li>
                        <li>• {{ __('messages.sv_guide_2') }}</li>
                        <li>• {{ __('messages.sv_guide_3') }}</li>
                        <li>• {{ __('messages.sv_guide_4') }}</li>
                        <li>• {{ __('messages.sv_guide_5') }}</li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ========================================== --}}
{{-- APPROVE CONFIRMATION MODAL                  --}}
{{-- ========================================== --}}
<div x-show="approveOpen"
     x-cloak
     x-transition.opacity
     class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
     @click.away="approveOpen = false">

    <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-neutral-800 max-w-md w-full p-5 sm:p-6"
         @click.stop
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">

        <div class="flex items-start gap-3 mb-4">
            <div class="w-11 h-11 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <h3 class="text-base font-black text-[#1B3B36] dark:text-white">{{ __('messages.sv_approve_confirm_title') }}</h3>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">
                    {{ __('messages.sv_approve_confirm_desc') }}
                </p>
            </div>
        </div>

        <div class="p-3 rounded-xl bg-neutral-50 dark:bg-neutral-800/50 border border-gray-200 dark:border-neutral-700 mb-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-sm shrink-0"
                     x-text="selected.initials"></div>
                <div class="min-w-0">
                    <p class="font-bold text-sm text-[#1B3B36] dark:text-white truncate" x-text="selected.name"></p>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 truncate" x-text="selected.email"></p>
                </div>
            </div>
        </div>

        <form :action="`{{ route('admin.verification.sitter.approve', ['id' => '__ID__']) }}`.replace('__ID__', selected.id)" method="POST">
            @csrf

            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1.5">
                {{ __('messages.sv_admin_remarks') }} <span class="text-neutral-400 font-normal">{{ __('messages.sv_optional') }}</span>
            </label>
            <textarea name="remarks" rows="3"
                      placeholder="{{ __('messages.sv_approve_ph') }}"
                      class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-3 py-2.5 text-[#1B3B36] dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-500 text-sm resize-none"></textarea>

            <div class="flex gap-2 mt-4">
                <button type="button" @click="approveOpen = false"
                        class="flex-1 px-4 py-2.5 rounded-xl border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-xs hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                    {{ __('messages.sv_cancel') }}
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2.5 rounded-xl bg-green-500 hover:bg-green-600 text-white font-bold text-xs transition shadow-md hover:shadow-lg">
                    {{ __('messages.sv_yes_approve') }}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ========================================== --}}
{{-- REJECT MODAL (with reason)                  --}}
{{-- ========================================== --}}
<div x-show="rejectOpen"
     x-cloak
     x-transition.opacity
     class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
     @click.away="rejectOpen = false">

    <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-neutral-800 max-w-md w-full p-5 sm:p-6"
         @click.stop
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">

        <div class="flex items-start gap-3 mb-4">
            <div class="w-11 h-11 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center text-red-600 dark:text-red-400 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <div class="min-w-0">
                <h3 class="text-base font-black text-[#1B3B36] dark:text-white">{{ __('messages.sv_reject_confirm_title') }}</h3>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">
                    {{ __('messages.sv_reject_confirm_desc') }}
                </p>
            </div>
        </div>

        <div class="p-3 rounded-xl bg-neutral-50 dark:bg-neutral-800/50 border border-gray-200 dark:border-neutral-700 mb-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-sm shrink-0"
                     x-text="selected.initials"></div>
                <div class="min-w-0">
                    <p class="font-bold text-sm text-[#1B3B36] dark:text-white truncate" x-text="selected.name"></p>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 truncate" x-text="selected.email"></p>
                </div>
            </div>
        </div>

        <form :action="`{{ route('admin.verification.sitter.reject', ['id' => '__ID__']) }}`.replace('__ID__', selected.id)"
              method="POST"
              @submit="if(!rejectReason.trim()) { $event.preventDefault(); }">
            @csrf

            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1.5">
                {{ __('messages.sv_reject_reason') }} <span class="text-red-500">*</span>
            </label>
            <textarea name="reason" rows="4" required
                      x-model="rejectReason"
                      placeholder="{{ __('messages.sv_reject_ph') }}"
                      class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-3 py-2.5 text-[#1B3B36] dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 text-sm resize-none"></textarea>

            <p class="text-[10px] text-neutral-400 mt-1">
                {{ __('messages.sv_reject_note') }}
            </p>

            <div class="flex gap-2 mt-4">
                <button type="button" @click="rejectOpen = false; rejectReason = ''"
                        class="flex-1 px-4 py-2.5 rounded-xl border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-xs hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                    {{ __('messages.sv_cancel') }}
                </button>
                <button type="submit"
                        :disabled="!rejectReason.trim()"
                        :class="rejectReason.trim() ? 'bg-red-500 hover:bg-red-600' : 'bg-red-300 dark:bg-red-900/50 cursor-not-allowed'"
                        class="flex-1 px-4 py-2.5 rounded-xl text-white font-bold text-xs transition shadow-md hover:shadow-lg">
                    {{ __('messages.sv_yes_reject') }}
                </button>
            </div>
        </form>
    </div>
</div>
