<x-app-layout>
    <!-- ========================================== -->
    <!-- HEADER SLOT                               -->
    <!-- ========================================== -->
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="javascript:history.back()"
               class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                    {{ __('messages.ss_title') }}
                </h1>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.ss_subtitle') }}</p>
            </div>
        </div>
    </x-slot>

    <!-- ========================================== -->
    <!-- MAIN CONTENT                              -->
    <!-- ========================================== -->
    <div class="py-4 sm:py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200">
        <div class="w-full sm:max-w-4xl mx-auto px-2 sm:px-16 lg:px-24">

            {{-- Flash messages --}}


            <div class="space-y-4 sm:space-y-6">

                <!-- ========================================== -->
                <!-- SITTER MODE (non-admin only)               -->
                <!-- ========================================== -->
                @if(auth()->user()->role !== 'admin')
                @php
                    $user            = auth()->user();
                    $isIdVerified    = $user->id_validation_status === 'verified';
                    $sitterStatus    = $user->sitter_status;
                    $hasApplied      = $user->sitter_applied_at !== null;
                    $isApproved      = $sitterStatus === 'approved';
                    $isPending       = $sitterStatus === 'pending' && $hasApplied;
                    $isRejected      = $sitterStatus === 'rejected';
                    $neverApplied    = ! $hasApplied && $sitterStatus === 'pending';
                @endphp
                <div x-data="{ sitterModal: false }"
                     class="bg-gradient-to-br from-primary/5 to-primary/10 dark:from-primary/10 dark:to-primary/5 rounded-2xl sm:rounded-3xl shadow-sm border border-primary/20 dark:border-primary/20 p-4 sm:p-8 hover:shadow-md transition">
                    <div class="flex items-start gap-3 sm:gap-4">
                        <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-full bg-primary/15 flex items-center justify-center text-primary shrink-0">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-black text-[#1B3B36] dark:text-white">{{ __('messages.ss_sitter_mode') }}</p>

                                    @if($isApproved)
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">
                                            {!! __('messages.ss_sitter_switch', [
                                                'sitter' => '<strong class="text-amber-600 dark:text-amber-400">' . __('messages.ss_sitter_word') . '</strong>',
                                                'owner'  => '<strong class="text-blue-600 dark:text-blue-400">' . __('messages.ss_owner_word') . '</strong>',
                                            ]) !!}
                                        </p>
                                    @elseif(! $isIdVerified)
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">
                                            {!! __('messages.ss_need_verified_id', [
                                                'id' => '<strong class="text-amber-600 dark:text-amber-400">' . __('messages.ss_verified_id_word') . '</strong>',
                                            ]) !!}
                                        </p>
                                    @elseif($isPending)
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">
                                            {!! __('messages.ss_sitter_pending', [
                                                'review' => '<strong class="text-amber-600 dark:text-amber-400">' . __('messages.ss_pending_review') . '</strong>',
                                            ]) !!}
                                        </p>
                                    @elseif($isRejected)
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">
                                            {!! __('messages.ss_sitter_rejected', [
                                                'rejected' => '<strong class="text-red-600 dark:text-red-400">' . __('messages.ss_rejected_word') . '</strong>',
                                            ]) !!}
                                        </p>
                                    @else
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">
                                            {{ __('messages.ss_sitter_become') }}
                                        </p>
                                    @endif
                                </div>

                                {{-- Toggle / Button --}}
                                <div class="shrink-0">
                                    @if($isApproved)
                                        {{-- Approved sitter: real toggle --}}
                                        <form method="POST" action="{{ route('settings.sitter-mode') }}">
                                            @csrf
                                            @method('PATCH')
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox"
                                                       onchange="this.form.submit()"
                                                       class="sr-only peer"
                                                       {{ auth()->user()->is_sitter ? 'checked' : '' }}>
                                                <div class="w-14 h-7 bg-gray-200 dark:bg-neutral-700 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-primary"></div>
                                            </label>
                                        </form>
                                    @else
                                        {{-- Not yet approved: click opens modal --}}
                                        <button type="button"
                                                @click="sitterModal = true"
                                                class="relative inline-flex items-center cursor-pointer">
                                            <div class="w-14 h-7 bg-gray-200 dark:bg-neutral-700 rounded-full relative after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all"></div>
                                        </button>
                                    @endif
                                </div>
                            </div>

                            {{-- Current status indicator --}}
                            <div class="mt-3 flex flex-wrap items-center gap-2">
                                @if($isApproved && auth()->user()->is_sitter)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        {{ __('messages.ss_status_sitter_view') }}
                                    </span>
                                @elseif($isApproved)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                        {{ __('messages.ss_status_owner_view') }}
                                    </span>
                                @elseif(! $isIdVerified)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        {{ __('messages.ss_status_verify_id') }}
                                    </span>
                                @elseif($isPending)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        {{ __('messages.ss_status_app_pending') }}
                                    </span>
                                @elseif($isRejected)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                        {{ __('messages.ss_status_app_rejected') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-neutral-100 text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-neutral-400"></span>
                                        {{ __('messages.ss_status_not_sitter') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- ========================================== --}}
                    {{-- APPLY AS SITTER MODAL                     --}}
                    {{-- ========================================== --}}
                    <div x-show="sitterModal"
                         x-cloak
                         x-transition.opacity
                         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
                         @click.away="sitterModal = false">

                        <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-neutral-800 max-w-md w-full p-5 sm:p-6"
                             @click.stop>

                            <div class="flex items-start gap-3 mb-4">
                                <div class="w-11 h-11 rounded-full {{ ! $isIdVerified ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400' : 'bg-primary/10 text-primary' }} flex items-center justify-center shrink-0">
                                    @if(! $isIdVerified)
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                    @else
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-base font-black text-[#1B3B36] dark:text-white">
                                        @if(! $isIdVerified)
                                            {{ __('messages.ss_modal_verify_id_title') }}
                                        @elseif($isPending)
                                            {{ __('messages.ss_modal_pending_title') }}
                                        @elseif($isRejected)
                                            {{ __('messages.ss_modal_reapply_title') }}
                                        @else
                                            {{ __('messages.ss_modal_become_title') }}
                                        @endif
                                    </h3>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                                        @if(! $isIdVerified)
                                            {{ __('messages.ss_modal_verify_id_desc') }}
                                        @elseif($isPending)
                                            {{ __('messages.ss_modal_pending_desc') }}
                                        @elseif($isRejected)
                                            {{ __('messages.ss_modal_reapply_desc') }}
                                        @else
                                            {{ __('messages.ss_modal_become_desc') }}
                                        @endif
                                    </p>
                                </div>
                            </div>

                            {{-- Requirements / Benefits --}}
                            @if(! $isIdVerified)
                                <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800/50 mb-4">
                                    <p class="text-[10px] font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wider mb-2">{{ __('messages.ss_you_need') }}</p>
                                    <ul class="space-y-1 text-xs text-amber-700 dark:text-amber-300">
                                        <li class="flex items-center gap-2">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            {{ __('messages.ss_need_gov_id') }}
                                        </li>
                                        <li class="flex items-center gap-2">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            {{ __('messages.ss_need_selfie') }}
                                        </li>
                                        <li class="flex items-center gap-2">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            {{ __('messages.ss_need_admin_review') }}
                                        </li>
                                    </ul>
                                </div>
                            @elseif(! $isPending)
                                <div class="p-3 rounded-xl bg-primary/5 border border-primary/20 mb-4">
                                    <p class="text-[10px] font-bold text-primary uppercase tracking-wider mb-2">{{ __('messages.ss_you_can') }}</p>
                                    <ul class="space-y-1 text-xs text-neutral-600 dark:text-neutral-300">
                                        <li class="flex items-center gap-2">
                                            <svg class="w-3.5 h-3.5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            {{ __('messages.ss_can_accept') }}
                                        </li>
                                        <li class="flex items-center gap-2">
                                            <svg class="w-3.5 h-3.5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            {{ __('messages.ss_can_set_rates') }}
                                        </li>
                                        <li class="flex items-center gap-2">
                                            <svg class="w-3.5 h-3.5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            {{ __('messages.ss_can_earn') }}
                                        </li>
                                    </ul>
                                </div>
                            @endif

                            <div class="flex gap-2">
                                <button type="button"
                                        @click="sitterModal = false"
                                        class="flex-1 px-4 py-2.5 rounded-xl border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-xs hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                                    {{ __('messages.ss_cancel') }}
                                </button>

                                @if(! $isIdVerified)
                                    <a href="{{ route('profile.edit') }}"
                                       class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs transition shadow-md hover:shadow-lg">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                        {{ __('messages.ss_verify_id_btn') }}
                                    </a>
                                @elseif($isPending)
                                    <a href="{{ route('help_support.index') }}"
                                       class="flex-1 px-4 py-2.5 rounded-xl bg-neutral-200 dark:bg-neutral-700 text-neutral-700 dark:text-neutral-200 font-bold text-xs transition text-center">
                                        {{ __('messages.ss_contact_support') }}
                                    </a>
                                @else
                                    <a href="{{ route('sitter.application') }}"
                                       class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-primary hover:bg-primary-600 text-white font-bold text-xs transition shadow-md hover:shadow-lg">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                        </svg>
                                        @if($isRejected)
                                            {{ __('messages.ss_reapply_btn') }}
                                        @else
                                            {{ __('messages.ss_continue_application') }}
                                        @endif
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- ========================================== -->
                <!-- PREFERENCES                               -->
                <!-- ========================================== -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl sm:rounded-3xl shadow-sm border border-gray-100 dark:border-neutral-800 p-4 sm:p-8 hover:shadow-md transition">
                    <h2 class="text-sm font-black text-[#1B3B36] dark:text-white uppercase tracking-wider mb-4 sm:mb-6">
                        {{ __('messages.ss_preferences') }}
                    </h2>

                    <!-- Push Notifications -->
                    <div x-data="{
                            enabled: {{ auth()->user()->setting->push_notifications ? 'true' : 'false' }},
                            toggle() {
                                this.enabled = !this.enabled;
                                fetch('{{ route('settings.ui-preferences') }}', {
                                    method: 'PATCH',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                    },
                                    body: JSON.stringify({ push_notifications: this.enabled }),
                                });
                            }
                         }"
                         class="flex items-center justify-between gap-3 py-2.5 sm:py-3 border-b border-gray-100 dark:border-neutral-800">
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-[#1B3B36] dark:text-white">{{ __('messages.ss_push_notifications') }}</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.ss_push_desc') }}</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                            <input type="checkbox" class="sr-only peer" :checked="enabled" @change="toggle()">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                        </label>
                    </div>

                    <!-- Email Notifications -->
                    <div x-data="{
                            enabled: {{ auth()->user()->setting->email_notifications ? 'true' : 'false' }},
                            toggle() {
                                this.enabled = !this.enabled;
                                fetch('{{ route('settings.ui-preferences') }}', {
                                    method: 'PATCH',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                    },
                                    body: JSON.stringify({ email_notifications: this.enabled }),
                                });
                            }
                         }"
                         class="flex items-center justify-between gap-3 py-2.5 sm:py-3 border-b border-gray-100 dark:border-neutral-800">
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-[#1B3B36] dark:text-white">{{ __('messages.ss_email_notifications') }}</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.ss_email_desc') }}</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                            <input type="checkbox" class="sr-only peer" :checked="enabled" @change="toggle()">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                        </label>
                    </div>

                    <!-- SMS Notifications -->
                    <div x-data="{
                            enabled: {{ auth()->user()->setting->sms_notifications ? 'true' : 'false' }},
                            toggle() {
                                this.enabled = !this.enabled;
                                fetch('{{ route('settings.ui-preferences') }}', {
                                    method: 'PATCH',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                    },
                                    body: JSON.stringify({ sms_notifications: this.enabled }),
                                });
                            }
                         }"
                         class="flex items-center justify-between gap-3 py-2.5 sm:py-3 border-b border-gray-100 dark:border-neutral-800">
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-[#1B3B36] dark:text-white">{{ __('messages.ss_sms_notifications') }}</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.ss_sms_desc') }}</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                            <input type="checkbox" class="sr-only peer" :checked="enabled" @change="toggle()">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                        </label>
                    </div>

                    <!-- Dark Mode -->
                    <div x-data="{
                            dark: {{ auth()->user()->setting->dark_mode ? 'true' : 'false' }},
                            toggle() {
                                this.dark = !this.dark;
                                document.documentElement.classList.toggle('dark', this.dark);
                                fetch('{{ route('settings.ui-preferences') }}', {
                                    method: 'PATCH',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                    },
                                    body: JSON.stringify({ dark_mode: this.dark }),
                                });
                            }
                         }"
                         class="flex items-center justify-between gap-3 py-2.5 sm:py-3 border-b border-gray-100 dark:border-neutral-800">
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-[#1B3B36] dark:text-white">{{ __('messages.ss_dark_mode') }}</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.ss_dark_desc') }}</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                            <input type="checkbox"
                                   class="sr-only peer"
                                   :checked="dark"
                                   @change="toggle()">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                        </label>
                    </div>

                    <!-- Language -->
                    <div class="flex items-center justify-between gap-3 py-2.5 sm:py-3 border-b border-gray-100 dark:border-neutral-800">
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-[#1B3B36] dark:text-white">
                                {{ __('messages.ss_language') }}
                            </p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">
                                {{ __('messages.ss_language_desc') }}
                            </p>
                        </div>

                        <div class="shrink-0"
                             x-data="{
                                 loading: false,
                                 async changeLocale(locale) {
                                     if (locale === '{{ auth()->user()->locale }}') return;
                                     this.loading = true;
                                     try {
                                         const formData = new FormData();
                                         formData.append('_method', 'PATCH');
                                         formData.append('locale', locale);

                                         const res = await fetch('{{ route('settings.language') }}', {
                                             method: 'POST',
                                             headers: {
                                                 'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                 'Accept': 'application/json',
                                                 'X-Requested-With': 'XMLHttpRequest',
                                             },
                                             body: formData,
                                         });
                                         if (res.ok) {
                                             window.location.reload();
                                         } else {
                                             alert(@js(__('messages.ss_change_lang_failed')));
                                             this.loading = false;
                                         }
                                     } catch (e) {
                                         alert(@js(__('messages.ss_network_error')));
                                         this.loading = false;
                                     }
                                 }
                             }">
                            <select @change="changeLocale($event.target.value)"
                                    :disabled="loading"
                                    class="px-3 py-2 sm:px-4 sm:py-2.5 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-950 text-sm text-neutral-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition min-w-[110px] sm:min-w-[140px] shrink-0 disabled:opacity-60 disabled:cursor-wait">
                                <option value="en"  {{ auth()->user()->locale === 'en'  ? 'selected' : '' }}>{{ __('messages.ss_lang_en') }}</option>
                                <option value="fil" {{ auth()->user()->locale === 'fil' ? 'selected' : '' }}>{{ __('messages.ss_lang_fil') }}</option>
                                <option value="es"  {{ auth()->user()->locale === 'es'  ? 'selected' : '' }}>{{ __('messages.ss_lang_es') }}</option>
                                <option value="fr"  {{ auth()->user()->locale === 'fr'  ? 'selected' : '' }}>{{ __('messages.ss_lang_fr') }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Font Size -->
                    <div x-data="{
                            fontSize: '{{ auth()->user()->setting->font_size }}',
                            changeSize(value) {
                                this.fontSize = value;
                                const sizes = {
                                    'small':       '14px',
                                    'default':     '16px',
                                    'large':       '18px',
                                    'extra_large': '20px',
                                };
                                document.documentElement.style.fontSize = sizes[value] || '16px';
                                document.documentElement.setAttribute('data-font-size', value);
                                fetch('{{ route('settings.ui-preferences') }}', {
                                    method: 'PATCH',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                    },
                                    body: JSON.stringify({ font_size: value }),
                                });
                            }
                         }"
                         class="flex items-center justify-between gap-3 py-2.5 sm:py-3">
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-[#1B3B36] dark:text-white">{{ __('messages.ss_font_size') }}</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.ss_font_desc') }}</p>
                        </div>
                        <select x-model="fontSize"
                                @change="changeSize($event.target.value)"
                                class="px-3 py-2 sm:px-4 sm:py-2.5 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-950 text-sm text-neutral-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition min-w-[110px] sm:min-w-[140px] shrink-0">
                            <option value="small">{{ __('messages.ss_size_small') }}</option>
                            <option value="default">{{ __('messages.ss_size_default') }}</option>
                            <option value="large">{{ __('messages.ss_size_large') }}</option>
                            <option value="extra_large">{{ __('messages.ss_size_xl') }}</option>
                        </select>
                    </div>

                </div>

                <!-- ========================================== -->
                <!-- PRIVACY & SECURITY                        -->
                <!-- ========================================== -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl sm:rounded-3xl shadow-sm border border-gray-100 dark:border-neutral-800 p-4 sm:p-8 hover:shadow-md transition">
                    <h2 class="text-sm font-black text-[#1B3B36] dark:text-white uppercase tracking-wider mb-4 sm:mb-6">
                        {{ __('messages.ss_privacy_security') }}
                    </h2>

                    <!-- Profile Visibility -->
                    <div class="flex items-center justify-between gap-3 py-2.5 sm:py-3 border-b border-gray-100 dark:border-neutral-800">
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-[#1B3B36] dark:text-white">{{ __('messages.ss_profile_visibility') }}</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.ss_profile_visibility_desc') }}</p>
                        </div>
                        <select @change="fetch('{{ route('settings.ui-preferences') }}', {
                                    method: 'PATCH',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                    },
                                    body: JSON.stringify({ profile_visibility: $event.target.value }),
                                })"
                                class="px-3 py-2 sm:px-4 sm:py-2.5 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-950 text-sm text-neutral-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition min-w-[110px] sm:min-w-[140px] shrink-0">
                            <option value="public"  {{ auth()->user()->setting->profile_visibility === 'public'  ? 'selected' : '' }}>{{ __('messages.ss_vis_public') }}</option>
                            <option value="private" {{ auth()->user()->setting->profile_visibility === 'private' ? 'selected' : '' }}>{{ __('messages.ss_vis_private') }}</option>
                            <option value="hidden"  {{ auth()->user()->setting->profile_visibility === 'hidden'  ? 'selected' : '' }}>{{ __('messages.ss_vis_hidden') }}</option>
                        </select>
                    </div>

                    <!-- Show Email -->
                    <div x-data="{
                            enabled: {{ auth()->user()->setting->show_email ? 'true' : 'false' }},
                            toggle() {
                                this.enabled = !this.enabled;
                                fetch('{{ route('settings.ui-preferences') }}', {
                                    method: 'PATCH',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                    },
                                    body: JSON.stringify({ show_email: this.enabled }),
                                });
                            }
                         }"
                         class="flex items-center justify-between gap-3 py-2.5 sm:py-3 border-b border-gray-100 dark:border-neutral-800">
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-[#1B3B36] dark:text-white">{{ __('messages.ss_show_email') }}</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.ss_show_email_desc') }}</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                            <input type="checkbox" class="sr-only peer" :checked="enabled" @change="toggle()">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                        </label>
                    </div>

                    <!-- Two-Factor Authentication -->
                    <div class="flex items-center justify-between gap-3 py-2.5 sm:py-3 border-b border-gray-100 dark:border-neutral-800">
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-[#1B3B36] dark:text-white">{{ __('messages.ss_2fa') }}</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.ss_2fa_desc') }}</p>
                        </div>
                        <button class="inline-flex items-center justify-center px-3 py-1.5 sm:px-4 sm:py-2 text-xs font-bold bg-primary text-white rounded-lg hover:bg-primary-600 transition min-w-[90px] sm:min-w-[100px] shrink-0">
                            {{ __('messages.ss_enable') }}
                        </button>
                    </div>

                    <!-- Active Sessions -->
                    <div class="flex items-center justify-between gap-3 py-2.5 sm:py-3">
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-[#1B3B36] dark:text-white">{{ __('messages.ss_active_sessions') }}</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.ss_active_sessions_desc') }}</p>
                        </div>
                        <button class="inline-flex items-center justify-center px-3 py-1.5 sm:px-4 sm:py-2 text-xs font-bold border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition min-w-[90px] sm:min-w-[100px] shrink-0">
                            {{ __('messages.ss_view_all') }}
                        </button>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- ACCOUNT                                   -->
                <!-- ========================================== -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl sm:rounded-3xl shadow-sm border border-gray-100 dark:border-neutral-800 p-4 sm:p-8 hover:shadow-md transition">
                    <h2 class="text-sm font-black text-[#1B3B36] dark:text-white uppercase tracking-wider mb-4 sm:mb-6">
                        {{ __('messages.ss_account') }}
                    </h2>

                    <!-- Email Address -->
                    <div class="flex items-center justify-between gap-3 py-2.5 sm:py-3 border-b border-gray-100 dark:border-neutral-800">
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-[#1B3B36] dark:text-white">{{ __('messages.ss_email_address') }}</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 truncate">{{ auth()->user()->email }}</p>
                        </div>
                        <button class="inline-flex items-center justify-center px-3 py-1.5 sm:px-4 sm:py-2 text-xs font-bold border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition min-w-[90px] sm:min-w-[100px] shrink-0">
                            {{ __('messages.ss_change') }}
                        </button>
                    </div>

                    <!-- Password -->
                    <div class="flex items-center justify-between gap-3 py-2.5 sm:py-3 border-b border-gray-100 dark:border-neutral-800">
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-[#1B3B36] dark:text-white">{{ __('messages.ss_password') }}</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.ss_password_last_changed') }}</p>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="inline-flex items-center justify-center px-3 py-1.5 sm:px-4 sm:py-2 text-xs font-bold border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition min-w-[90px] sm:min-w-[100px] shrink-0">
                            {{ __('messages.ss_update') }}
                        </a>
                    </div>

                    <!-- Account Type -->
                    <div class="flex items-center justify-between gap-3 py-2.5 sm:py-3 border-b border-gray-100 dark:border-neutral-800">
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-[#1B3B36] dark:text-white">{{ __('messages.ss_account_type') }}</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">
                                @if(auth()->user()->is_sitter)
                                    {{ __('messages.ss_role_sitter') }}
                                @elseif(auth()->user()->role === 'admin')
                                    {{ __('messages.ss_role_admin') }}
                                @else
                                    {{ __('messages.ss_role_owner') }}
                                @endif
                            </p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-primary/10 text-primary shrink-0">
                            @if(auth()->user()->is_sitter)
                                {{ __('messages.ss_level_x', ['level' => auth()->user()->sitter_level ?? 1]) }}
                            @elseif(auth()->user()->role === 'admin')
                                {{ __('messages.ss_badge_admin') }}
                            @else
                                {{ __('messages.ss_badge_verified') }}
                            @endif
                        </span>
                    </div>

                    <!-- Delete Account -->
                    <div class="flex items-center justify-between gap-3 py-2.5 sm:py-3">
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-red-600 dark:text-red-400">{{ __('messages.ss_delete_account') }}</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.ss_delete_account_desc') }}</p>
                        </div>
                        <button class="inline-flex items-center justify-center px-3 py-1.5 sm:px-4 sm:py-2 text-xs font-bold bg-red-500 hover:bg-red-600 text-white rounded-lg transition min-w-[100px] sm:min-w-[120px] shrink-0">
                            {{ __('messages.ss_delete') }}
                        </button>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- SAVE BUTTON                               -->
                <!-- ========================================== -->
                <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-2 pt-2">
                    <button class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-sm rounded-xl hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                        {{ __('messages.ss_cancel') }}
                    </button>
                    <button class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary-600 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ __('messages.ss_save_settings') }}
                    </button>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>