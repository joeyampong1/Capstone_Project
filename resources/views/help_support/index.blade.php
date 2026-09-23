<x-app-layout>
    <x-slot name="header">
        <div x-data class="flex items-center justify-between w-full">
            <div class="flex items-center gap-3">
                <a href="javascript:history.back()"
                   class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                        {{ __('messages.hs_title') }}
                    </h1>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.hs_subtitle') }}</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 rounded-2xl border border-green-200 dark:border-green-800/50 bg-green-50 dark:bg-green-950/20 text-green-800 dark:text-green-300 px-4 py-3 text-sm font-medium flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            <!-- QUICK ACTION CARDS -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">

                <!-- FAQ Card -->
                <a href="#faq-section"
                   class="group bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-6 hover:shadow-md transition hover:border-blue-400/50">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-14 h-14 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 group-hover:scale-105 transition">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-lg text-[#1B3B36] dark:text-white mt-3">{{ __('messages.hs_faq_card_title') }}</h3>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.hs_faq_card_desc') }}</p>
                        <span class="mt-3 text-sm font-bold text-blue-600 dark:text-blue-400 group-hover:underline">{{ __('messages.hs_faq_card_btn') }}</span>
                    </div>
                </a>

                <!-- Contact Support Card -->
                <a href="{{ route('support.create') }}"
                   class="group bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-6 hover:shadow-md transition hover:border-green-400/50">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-14 h-14 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400 group-hover:scale-105 transition">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-lg text-[#1B3B36] dark:text-white mt-3">{{ __('messages.hs_contact_card_title') }}</h3>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.hs_contact_card_desc') }}</p>
                        <span class="mt-3 text-sm font-bold text-green-600 dark:text-green-400 group-hover:underline">{{ __('messages.hs_contact_card_btn') }}</span>
                    </div>
                </a>

                <!-- Report Problem Card -->
                <a href="{{ route('report.create') }}"
                   class="group bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-6 hover:shadow-md transition hover:border-amber-400/50">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-14 h-14 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400 group-hover:scale-105 transition">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-lg text-[#1B3B36] dark:text-white mt-3">{{ __('messages.hs_report_card_title') }}</h3>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.hs_report_card_desc') }}</p>
                        <span class="mt-3 text-sm font-bold text-amber-600 dark:text-amber-400 group-hover:underline">{{ __('messages.hs_report_card_btn') }}</span>
                    </div>
                </a>

            </div>

            <!-- MY MESSAGES TO SUPPORT -->
            @if (isset($messages) && $messages->count() > 0)
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-5 sm:p-6 mb-6">
                    <h2 class="font-bold text-lg text-[#1B3B36] dark:text-white flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        {{ __('messages.hs_my_messages') }}
                    </h2>

                    <div class="space-y-3">
                        @foreach ($messages as $msg)
                            @php
                                $statusClass = match($msg->status) {
                                    'open'        => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                    'in_progress' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                    'resolved'    => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                    default       => 'bg-neutral-100 text-neutral-700',
                                };
                                $statusLabel = match($msg->status) {
                                    'open'        => __('messages.hs_msg_pending'),
                                    'in_progress' => __('messages.hs_msg_in_progress'),
                                    'resolved'    => __('messages.hs_msg_resolved'),
                                    default       => ucfirst($msg->status),
                                };
                                $typeLabel = $msg->type === 'report' ? __('messages.hs_msg_type_report') : __('messages.hs_msg_type_contact');
                                $typeClass = $msg->type === 'report'
                                    ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400'
                                    : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400';
                            @endphp
                            <div class="border border-gray-100 dark:border-neutral-800 rounded-xl p-4">
                                <div class="flex flex-wrap items-center gap-2 mb-2">
                                    <span class="font-mono text-[10px] font-bold text-neutral-400">{{ $msg->message_code }}</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $typeClass }}">
                                        {{ $typeLabel }}
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                    <span class="ml-auto text-[10px] text-neutral-400">{{ $msg->created_at->diffForHumans() }}</span>
                                </div>

                                <p class="font-bold text-sm text-[#1B3B36] dark:text-white">{{ $msg->subject }}</p>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">{{ $msg->category }}</p>

                                <p class="text-xs text-neutral-600 dark:text-neutral-300 mt-2 line-clamp-2">{{ $msg->message }}</p>

                                @if ($msg->admin_reply)
                                    <div class="mt-3 p-3 rounded-lg bg-blue-50 dark:bg-blue-950/20 border border-blue-100 dark:border-blue-800/50">
                                        <p class="text-[10px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-1">{{ __('messages.hs_support_reply') }}</p>
                                        <p class="text-xs text-neutral-700 dark:text-neutral-300">{{ $msg->admin_reply }}</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- FAQ SECTION -->
            <div id="faq-section" class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-5 sm:p-6 mb-6">
                <h2 class="font-bold text-lg text-[#1B3B36] dark:text-white flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ __('messages.hs_faq_header') }}
                </h2>

                <div x-data="{ open: null }" class="space-y-2">
                    @for ($i = 1; $i <= 7; $i++)
                        <div class="border border-gray-200 dark:border-neutral-700 rounded-xl overflow-hidden">
                            <button @click="open = open === {{ $i }} ? null : {{ $i }}"
                                    class="w-full flex items-center justify-between px-4 py-3.5 text-left hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition">
                                <span class="font-bold text-[#1B3B36] dark:text-white text-sm">{{ __('messages.hs_faq_q' . $i) }}</span>
                                <svg class="w-5 h-5 text-neutral-500 transition-transform" :class="open === {{ $i }} ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="open === {{ $i }}" x-collapse class="px-4 pb-4 text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                                {{ __('messages.hs_faq_a' . $i) }}
                            </div>
                        </div>
                    @endfor
                </div>
            </div>

            <!-- SAFETY & COMMUNITY SECTION -->
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-5 sm:p-6 mb-6">
                <h2 class="font-bold text-lg text-[#1B3B36] dark:text-white flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    {{ __('messages.hs_safety_header') }}
                </h2>
                <ul class="space-y-2 text-sm text-neutral-600 dark:text-neutral-300">
                    <li class="flex items-start gap-2">
                        <span class="text-primary font-bold">•</span>
                        <span>{{ __('messages.hs_safety_1') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-primary font-bold">•</span>
                        <span>{{ __('messages.hs_safety_2') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-primary font-bold">•</span>
                        <span>{{ __('messages.hs_safety_3') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-primary font-bold">•</span>
                        <span>{{ __('messages.hs_safety_4') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-primary font-bold">•</span>
                        <span>{{ __('messages.hs_safety_5') }}</span>
                    </li>
                </ul>
            </div>

            <!-- CONTACT INFORMATION -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                <!-- Contact Info -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-5 sm:p-6">
                    <h2 class="font-bold text-lg text-[#1B3B36] dark:text-white flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        {{ __('messages.hs_contact_info_header') }}
                    </h2>
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 uppercase tracking-wider font-bold">{{ __('messages.hs_label_email') }}</p>
                            <p class="text-sm font-bold text-[#1B3B36] dark:text-white">support@petnanny.com</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 uppercase tracking-wider font-bold">{{ __('messages.hs_label_phone') }}</p>
                            <p class="text-sm font-bold text-[#1B3B36] dark:text-white">(+63) 912 345 6789</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 uppercase tracking-wider font-bold">{{ __('messages.hs_label_hours') }}</p>
                            <p class="text-sm font-bold text-[#1B3B36] dark:text-white">{!! str_replace('|', '<br>', e(__('messages.hs_hours_value'))) !!}</p>
                        </div>
                    </div>
                </div>

                <!-- Helpful Links -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-5 sm:p-6">
                    <h2 class="font-bold text-lg text-[#1B3B36] dark:text-white flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ __('messages.hs_resources_header') }}
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <a href="#" class="block p-4 rounded-xl border border-gray-200 dark:border-neutral-700 hover:border-primary/50 hover:shadow-sm transition">
                            <p class="font-bold text-sm text-[#1B3B36] dark:text-white">{{ __('messages.hs_resource_privacy_title') }}</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.hs_resource_privacy_desc') }}</p>
                        </a>
                        <a href="#" class="block p-4 rounded-xl border border-gray-200 dark:border-neutral-700 hover:border-primary/50 hover:shadow-sm transition">
                            <p class="font-bold text-sm text-[#1B3B36] dark:text-white">{{ __('messages.hs_resource_terms_title') }}</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.hs_resource_terms_desc') }}</p>
                        </a>
                        <a href="#" class="block p-4 rounded-xl border border-gray-200 dark:border-neutral-700 hover:border-primary/50 hover:shadow-sm transition">
                            <p class="font-bold text-sm text-[#1B3B36] dark:text-white">{{ __('messages.hs_resource_community_title') }}</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.hs_resource_community_desc') }}</p>
                        </a>
                        <a href="#" class="block p-4 rounded-xl border border-gray-200 dark:border-neutral-700 hover:border-primary/50 hover:shadow-sm transition">
                            <p class="font-bold text-sm text-[#1B3B36] dark:text-white">{{ __('messages.hs_resource_protection_title') }}</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.hs_resource_protection_desc') }}</p>
                        </a>
                    </div>
                </div>

            </div>

            <!-- APP INFORMATION -->
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-5 sm:p-6 mb-6">
                <div class="flex flex-col items-center text-center">
                    <div class="w-16 h-16 rounded-full bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center text-2xl font-black text-primary mb-3">
                        🐾
                    </div>
                    <h2 class="font-black text-xl text-[#1B3B36] dark:text-white">PetNanny</h2>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('messages.hs_app_version') }}</p>
                    <p class="text-sm text-neutral-600 dark:text-neutral-300 mt-2 max-w-md">
                        {{ __('messages.hs_app_description') }}
                    </p>
                    <p class="text-xs text-neutral-400 mt-4">{{ __('messages.hs_app_copyright') }}</p>
                </div>
            </div>

            <!-- FOOTER BUTTONS -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-4">
                <a href="{{ route('support.create') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary hover:bg-primary-600 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    {{ __('messages.hs_contact_card_title') }}
                </a>
                <a href="javascript:history.back()"
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-sm rounded-xl hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    {{ __('messages.hs_back') }}
                </a>
            </div>

        </div>
    </div>
</x-app-layout>