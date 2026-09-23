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
                    {{ __('messages.help_title') }}
                </h1>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.help_subtitle') }}</p>
            </div>
        </div>
    </x-slot>

    <div x-data="{
            search: '',
            activeFaq: null,
            activeTab: 'help',

            matchesFaq(text) {
                if (!this.search.trim()) return true;
                return text.toLowerCase().includes(this.search.trim().toLowerCase());
            }
         }"
         class="py-4 sm:py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200">
        <div class="w-full sm:max-w-7xl mx-auto px-2 sm:px-16 lg:px-24">

            @if (session('success'))
                <div class="mb-4 rounded-2xl border border-green-200 dark:border-green-800/50 bg-green-50 dark:bg-green-950/20 text-green-800 dark:text-green-300 px-4 py-3 text-sm font-medium flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 rounded-2xl border border-red-200 dark:border-red-800/50 bg-red-50 dark:bg-red-950/20 text-red-800 dark:text-red-300 px-4 py-3 text-sm font-medium">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Tabs --}}
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-1.5 mb-4 sm:mb-6 inline-flex">
                <button type="button"
                        @click="activeTab = 'help'"
                        :class="activeTab === 'help' ? 'bg-primary text-white shadow-sm' : 'text-neutral-600 dark:text-neutral-300 hover:text-primary'"
                        class="px-4 py-2 text-xs sm:text-sm font-bold rounded-xl transition">
                    {{ __('messages.help_tab_help') }}
                </button>
                <button type="button"
                        @click="activeTab = 'tickets'"
                        :class="activeTab === 'tickets' ? 'bg-primary text-white shadow-sm' : 'text-neutral-600 dark:text-neutral-300 hover:text-primary'"
                        class="px-4 py-2 text-xs sm:text-sm font-bold rounded-xl transition">
                    {{ __('messages.help_tab_tickets') }}
                    @if (($stats['open'] ?? 0) + ($stats['in_progress'] ?? 0) > 0)
                        <span class="ml-1 inline-flex items-center justify-center w-5 h-5 rounded-full bg-red-500 text-white text-[10px]">
                            {{ ($stats['open'] ?? 0) + ($stats['in_progress'] ?? 0) }}
                        </span>
                    @endif
                </button>
            </div>

            {{-- ================================================ --}}
            {{-- TAB: HELP & GUIDE                                --}}
            {{-- ================================================ --}}
            <div x-show="activeTab === 'help'">

                {{-- ① SEARCH BAR --}}
                <div class="bg-gradient-to-br from-primary/5 to-primary/10 dark:from-primary/10 dark:to-primary/5 rounded-2xl border border-primary/20 dark:border-primary/20 p-5 sm:p-8 mb-4 sm:mb-6">
                    <div class="max-w-2xl mx-auto text-center">
                        <div class="w-14 h-14 rounded-full bg-primary/15 flex items-center justify-center text-primary mx-auto mb-4">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h2 class="font-black text-xl sm:text-2xl text-[#1B3B36] dark:text-white">{{ __('messages.help_search_title') }}</h2>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.help_search_subtitle') }}</p>

                        <div class="relative mt-5">
                            <svg class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text"
                                   x-model="search"
                                   placeholder="{{ __('messages.help_search_ph') }}"
                                   class="w-full pl-11 pr-4 py-3 text-sm rounded-xl border border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary transition shadow-sm">
                        </div>
                    </div>
                </div>

                {{-- ② QUICK HELP CATEGORIES --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-6">
                    <a href="#faqs" class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-5 hover:shadow-md hover:border-primary/30 transition">
                        <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary mb-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white">{{ __('messages.help_cat_faqs') }}</h3>
                        <p class="text-[11px] sm:text-xs text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.help_cat_faqs_desc') }}</p>
                    </a>
                    <a href="#guide" class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-5 hover:shadow-md hover:border-primary/30 transition">
                        <div class="w-10 h-10 rounded-full bg-blue-100/20 flex items-center justify-center text-blue-600 mb-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white">{{ __('messages.help_cat_guide') }}</h3>
                        <p class="text-[11px] sm:text-xs text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.help_cat_guide_desc') }}</p>
                    </a>
                    <button type="button" @click="activeTab = 'tickets'"
                            class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-5 hover:shadow-md hover:border-primary/30 transition text-left">
                        <div class="w-10 h-10 rounded-full bg-green-100/20 flex items-center justify-center text-green-600 mb-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white">{{ __('messages.help_cat_contact') }}</h3>
                        <p class="text-[11px] sm:text-xs text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.help_cat_contact_desc') }}</p>
                    </button>
                    <a href="#docs" class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-5 hover:shadow-md hover:border-primary/30 transition">
                        <div class="w-10 h-10 rounded-full bg-purple-100/20 flex items-center justify-center text-purple-600 mb-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white">{{ __('messages.help_cat_docs') }}</h3>
                        <p class="text-[11px] sm:text-xs text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.help_cat_docs_desc') }}</p>
                    </a>
                </div>

                {{-- ③ FAQs --}}
                <div id="faqs" class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6 mb-4 sm:mb-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center text-primary shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-bold text-base text-[#1B3B36] dark:text-white">{{ __('messages.help_faqs_header') }}</h2>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.help_faqs_header_desc') }}</p>
                        </div>
                    </div>

                    @php
                        $faqs = [];
                        for ($i = 1; $i <= 8; $i++) {
                            $faqs[] = [
                                'q' => __('messages.help_faq_q' . $i),
                                'a' => __('messages.help_faq_a' . $i),
                            ];
                        }
                    @endphp

                    <div class="space-y-2">
                        @foreach($faqs as $i => $faq)
                            <div x-show="matchesFaq('{{ addslashes($faq['q']) }} {{ addslashes($faq['a']) }}')"
                                 x-transition
                                 class="border border-gray-100 dark:border-neutral-800 rounded-xl overflow-hidden">
                                <button type="button"
                                        @click="activeFaq = (activeFaq === {{ $i }} ? null : {{ $i }})"
                                        class="w-full flex items-center justify-between gap-3 px-4 py-3.5 text-left hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition">
                                    <span class="font-bold text-sm text-[#1B3B36] dark:text-white">{{ $faq['q'] }}</span>
                                    <svg class="w-4 h-4 text-neutral-400 shrink-0 transition-transform duration-200"
                                         :class="activeFaq === {{ $i }} ? 'rotate-180' : ''"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div x-show="activeFaq === {{ $i }}" x-collapse x-cloak
                                     class="px-4 pb-4 text-sm text-neutral-600 dark:text-neutral-300 leading-relaxed border-t border-gray-100 dark:border-neutral-800 pt-3">
                                    {{ $faq['a'] }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- ④ USER GUIDE --}}
                <div id="guide" class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6 mb-4 sm:mb-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-9 h-9 rounded-full bg-blue-100/20 flex items-center justify-center text-blue-600 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-bold text-base text-[#1B3B36] dark:text-white">{{ __('messages.help_guide_header') }}</h2>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.help_guide_header_desc') }}</p>
                        </div>
                    </div>

                    @php
                        $guides = [
                            ['title' => __('messages.help_guide_user_title'),     'icon_bg' => 'bg-blue-100/20',   'icon_color' => 'text-blue-600',   'steps' => [__('messages.help_guide_user_s1'), __('messages.help_guide_user_s2'), __('messages.help_guide_user_s3'), __('messages.help_guide_user_s4')]],
                            ['title' => __('messages.help_guide_id_title'),       'icon_bg' => 'bg-primary/10',    'icon_color' => 'text-primary',    'steps' => [__('messages.help_guide_id_s1'), __('messages.help_guide_id_s2'), __('messages.help_guide_id_s3'), __('messages.help_guide_id_s4')]],
                            ['title' => __('messages.help_guide_sitter_title'),   'icon_bg' => 'bg-amber-100/20',  'icon_color' => 'text-amber-600',  'steps' => [__('messages.help_guide_sitter_s1'), __('messages.help_guide_sitter_s2'), __('messages.help_guide_sitter_s3'), __('messages.help_guide_sitter_s4')]],
                            ['title' => __('messages.help_guide_booking_title'),  'icon_bg' => 'bg-green-100/20',  'icon_color' => 'text-green-600',  'steps' => [__('messages.help_guide_booking_s1'), __('messages.help_guide_booking_s2'), __('messages.help_guide_booking_s3'), __('messages.help_guide_booking_s4')]],
                            ['title' => __('messages.help_guide_complaint_title'),'icon_bg' => 'bg-red-100/20',    'icon_color' => 'text-red-600',    'steps' => [__('messages.help_guide_complaint_s1'), __('messages.help_guide_complaint_s2'), __('messages.help_guide_complaint_s3'), __('messages.help_guide_complaint_s4')]],
                            ['title' => __('messages.help_guide_reports_title'),  'icon_bg' => 'bg-purple-100/20', 'icon_color' => 'text-purple-600', 'steps' => [__('messages.help_guide_reports_s1'), __('messages.help_guide_reports_s2'), __('messages.help_guide_reports_s3'), __('messages.help_guide_reports_s4')]],
                        ];
                    @endphp

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 sm:gap-4">
                        @foreach($guides as $g)
                            <div class="border border-gray-100 dark:border-neutral-800 rounded-xl p-4 hover:shadow-sm transition">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-8 h-8 rounded-full {{ $g['icon_bg'] }} flex items-center justify-center {{ $g['icon_color'] }} shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                        </svg>
                                    </div>
                                    <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white">{{ $g['title'] }}</h3>
                                </div>
                                <ol class="space-y-1.5">
                                    @foreach($g['steps'] as $i => $step)
                                        <li class="flex gap-2.5 text-xs text-neutral-600 dark:text-neutral-300 leading-relaxed">
                                            <span class="w-5 h-5 rounded-full bg-neutral-100 dark:bg-neutral-800 text-neutral-500 text-[10px] font-bold flex items-center justify-center shrink-0 mt-0.5">{{ $i + 1 }}</span>
                                            <span>{{ $step }}</span>
                                        </li>
                                    @endforeach
                                </ol>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- ⑤ DOCUMENTATION --}}
                <div id="docs" class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-9 h-9 rounded-full bg-purple-100/20 flex items-center justify-center text-purple-600 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-bold text-base text-[#1B3B36] dark:text-white">{{ __('messages.help_docs_header') }}</h2>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.help_docs_header_desc') }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        <div class="p-4 rounded-xl border border-gray-100 dark:border-neutral-800">
                            <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white mb-2">{{ __('messages.help_doc_verif_title') }}</h3>
                            <ul class="space-y-1.5 text-xs text-neutral-600 dark:text-neutral-300">
                                <li>• {{ __('messages.help_doc_verif_1') }}</li>
                                <li>• {{ __('messages.help_doc_verif_2') }}</li>
                                <li>• {{ __('messages.help_doc_verif_3') }}</li>
                                <li>• {{ __('messages.help_doc_verif_4') }}</li>
                            </ul>
                        </div>
                        <div class="p-4 rounded-xl border border-gray-100 dark:border-neutral-800">
                            <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white mb-2">{{ __('messages.help_doc_complaint_title') }}</h3>
                            <ul class="space-y-1.5 text-xs text-neutral-600 dark:text-neutral-300">
                                <li>• {{ __('messages.help_doc_complaint_1') }}</li>
                                <li>• {{ __('messages.help_doc_complaint_2') }}</li>
                                <li>• {{ __('messages.help_doc_complaint_3') }}</li>
                                <li>• {{ __('messages.help_doc_complaint_4') }}</li>
                            </ul>
                        </div>
                        <div class="p-4 rounded-xl border border-gray-100 dark:border-neutral-800">
                            <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white mb-2">{{ __('messages.help_doc_booking_title') }}</h3>
                            <ul class="space-y-1.5 text-xs text-neutral-600 dark:text-neutral-300">
                                <li>• {{ __('messages.help_doc_booking_1') }}</li>
                                <li>• {{ __('messages.help_doc_booking_2') }}</li>
                                <li>• {{ __('messages.help_doc_booking_3') }}</li>
                                <li>• {{ __('messages.help_doc_booking_4') }}</li>
                            </ul>
                        </div>
                        <div class="p-4 rounded-xl border border-gray-100 dark:border-neutral-800">
                            <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white mb-2">{{ __('messages.help_doc_privacy_title') }}</h3>
                            <ul class="space-y-1.5 text-xs text-neutral-600 dark:text-neutral-300">
                                <li>• {{ __('messages.help_doc_privacy_1') }}</li>
                                <li>• {{ __('messages.help_doc_privacy_2') }}</li>
                                <li>• {{ __('messages.help_doc_privacy_3') }}</li>
                                <li>• {{ __('messages.help_doc_privacy_4') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ================================================ --}}
            {{-- TAB: MY TICKETS                                  --}}
            {{-- ================================================ --}}
            <div x-show="activeTab === 'tickets'" x-cloak>

                {{-- Stats --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-6">
                    <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                        <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.help_ticket_total') }}</p>
                        <h3 class="font-black text-lg sm:text-2xl text-[#1B3B36] dark:text-white mt-1">{{ $stats['total'] }}</h3>
                    </div>
                    <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                        <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.help_ticket_open') }}</p>
                        <h3 class="font-black text-lg sm:text-2xl text-red-600 dark:text-red-400 mt-1">{{ $stats['open'] }}</h3>
                    </div>
                    <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                        <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.help_ticket_in_progress') }}</p>
                        <h3 class="font-black text-lg sm:text-2xl text-amber-600 dark:text-amber-400 mt-1">{{ $stats['in_progress'] }}</h3>
                    </div>
                    <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                        <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.help_ticket_resolved') }}</p>
                        <h3 class="font-black text-lg sm:text-2xl text-green-600 dark:text-green-400 mt-1">{{ $stats['resolved'] }}</h3>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">

                    {{-- Submit form --}}
                    <div class="lg:col-span-1 bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-9 h-9 rounded-full bg-green-100/20 flex items-center justify-center text-green-600 shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="font-bold text-base text-[#1B3B36] dark:text-white">{{ __('messages.help_ticket_new') }}</h2>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.help_ticket_new_desc') }}</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('admin.help_support.store') }}" class="space-y-4">
                            @csrf

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.help_ticket_concern') }}</label>
                                <select name="concern_type" required
                                        class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 px-3 py-2.5 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary text-sm font-medium">
                                    <option value="">{{ __('messages.help_ticket_select') }}</option>
                                    <option value="Login / Access Issue">{{ __('messages.help_concern_login') }}</option>
                                    <option value="ID Verification Error">{{ __('messages.help_concern_id') }}</option>
                                    <option value="Booking System Error">{{ __('messages.help_concern_booking') }}</option>
                                    <option value="Reports / Analytics Bug">{{ __('messages.help_concern_reports') }}</option>
                                    <option value="User Management Issue">{{ __('messages.help_concern_user') }}</option>
                                    <option value="System Performance">{{ __('messages.help_concern_perf') }}</option>
                                    <option value="Other">{{ __('messages.help_concern_other') }}</option>
                                </select>
                                @error('concern_type') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.help_ticket_priority') }}</label>
                                <select name="priority" required
                                        class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 px-3 py-2.5 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary text-sm font-medium">
                                    <option value="Low">{{ __('messages.help_priority_low') }}</option>
                                    <option value="Normal" selected>{{ __('messages.help_priority_normal') }}</option>
                                    <option value="High">{{ __('messages.help_priority_high') }}</option>
                                    <option value="Critical">{{ __('messages.help_priority_critical') }}</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.help_ticket_subject') }}</label>
                                <input type="text" name="subject" required maxlength="200"
                                       placeholder="{{ __('messages.help_ticket_subject_ph') }}"
                                       value="{{ old('subject') }}"
                                       class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 px-3 py-2.5 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary text-sm font-medium">
                                @error('subject') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.help_ticket_description') }}</label>
                                <textarea name="description" rows="5" required maxlength="5000"
                                          placeholder="{{ __('messages.help_ticket_description_ph') }}"
                                          class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 px-3 py-2.5 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary text-sm font-medium resize-none">{{ old('description') }}</textarea>
                                @error('description') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary-600 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                {{ __('messages.help_ticket_submit') }}
                            </button>
                        </form>
                    </div>

                    {{-- Ticket history --}}
                    <div class="lg:col-span-2 bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-blue-100/20 flex items-center justify-center text-blue-600 shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="font-bold text-base text-[#1B3B36] dark:text-white">{{ __('messages.help_ticket_history') }}</h2>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.help_ticket_history_desc') }}</p>
                                </div>
                            </div>
                        </div>

                        @forelse ($tickets as $ticket)
                            @php
                                $statusClass = match($ticket->status) {
                                    'open'        => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                    'in_progress' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                    'resolved'    => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                    default       => 'bg-neutral-100 text-neutral-700',
                                };
                                $priorityClass = match($ticket->priority) {
                                    'critical' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                    'high'     => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                    'normal'   => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                    'low'      => 'bg-neutral-100 text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300',
                                    default    => 'bg-neutral-100 text-neutral-700',
                                };
                                $statusLabel = match($ticket->status) {
                                    'open'        => __('messages.help_ticket_open'),
                                    'in_progress' => __('messages.help_ticket_in_progress'),
                                    'resolved'    => __('messages.help_ticket_resolved'),
                                    default       => ucfirst(str_replace('_', ' ', $ticket->status)),
                                };
                                $priorityLabel = match($ticket->priority) {
                                    'low'      => __('messages.help_priority_low'),
                                    'normal'   => __('messages.help_priority_normal'),
                                    'high'     => __('messages.help_priority_high'),
                                    'critical' => __('messages.help_priority_critical'),
                                    default    => ucfirst($ticket->priority),
                                };
                            @endphp
                            <div class="border border-gray-100 dark:border-neutral-800 rounded-xl p-4 mb-3 hover:shadow-sm transition">
                                <div class="flex flex-wrap items-start justify-between gap-2 mb-2">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap mb-1">
                                            <span class="font-mono text-[10px] font-bold text-neutral-400">{{ $ticket->ticket_code }}</span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $priorityClass }}">
                                                {{ $priorityLabel }}
                                            </span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $statusClass }}">
                                                {{ $statusLabel }}
                                            </span>
                                        </div>
                                        <p class="font-bold text-sm text-[#1B3B36] dark:text-white truncate">{{ $ticket->subject }}</p>
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ $ticket->concern_type }}</p>
                                    </div>
                                    <span class="text-[10px] text-neutral-400 shrink-0">{{ $ticket->created_at->diffForHumans() }}</span>
                                </div>

                                <p class="text-xs text-neutral-600 dark:text-neutral-300 leading-relaxed line-clamp-3">
                                    {{ $ticket->description }}
                                </p>

                                @if ($ticket->admin_response)
                                    <div class="mt-3 p-3 rounded-lg bg-blue-50 dark:bg-blue-950/20 border border-blue-100 dark:border-blue-800/50">
                                        <p class="text-[10px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-1">{{ __('messages.help_ticket_response') }}</p>
                                        <p class="text-xs text-neutral-700 dark:text-neutral-300">{{ $ticket->admin_response }}</p>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <div class="w-16 h-16 rounded-full bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center text-neutral-400 mx-auto mb-4">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <p class="font-bold text-sm text-[#1B3B36] dark:text-white">{{ __('messages.help_ticket_empty') }}</p>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.help_ticket_empty_desc') }}</p>
                            </div>
                        @endforelse
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>