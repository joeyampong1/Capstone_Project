<x-app-layout>
    <!-- ========================================== -->
    <!-- HEADER SLOT                               -->
    <!-- ========================================== -->
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <!-- Search Icon -->
            <svg class="w-12 h-12 text-[#1B3B36] dark:text-white"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                stroke-width="2.5"
                stroke-linecap="round"
                stroke-linejoin="round">
                <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <div>
                <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                    {{ __('messages.fs_title') }}
                </h1>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                    {{ __('messages.fs_subtitle') }}
                </p>
            </div>
        </div>
    </x-slot>

    <!-- ========================================== -->
    <!-- MAIN CONTENT                              -->
    <!-- ========================================== -->
    <div class="py-4 sm:py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200">

        <!-- ========================================== -->
        <!-- HERO + SEARCH BAR                         -->
        <!-- ========================================== -->
        <div class="px-2 sm:px-16 lg:px-24 pt-2 sm:pt-6 pb-6 sm:pb-12">
            <div class="relative rounded-2xl sm:rounded-[2.5rem] overflow-hidden min-h-[380px] sm:min-h-[440px] lg:min-h-[480px] flex items-center px-4 sm:px-10 lg:px-16">

                <!-- Background Image -->
                <img src="{{ asset('assets/image/animie_findsitter_bg.png') }}"
                     alt=""
                     class="absolute inset-0 w-full h-full object-cover object-center"
                     style="object-position: center 15%;">

                <!-- Gradient Overlay -->
                <div class="absolute inset-0 bg-gradient-to-r from-white/80 via-white/40 to-transparent dark:from-neutral-950/80 dark:via-neutral-950/40"></div>

                <!-- Hero Content -->
                <div class="max-w-xl relative z-10">
                    <div class="inline-flex items-center gap-1.5 bg-[#1B3B36]/5 text-[#1B3B36] text-xs font-bold px-3 py-1.5 rounded-full mb-6">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                        </svg>
                        {{ __('messages.fs_hero_badge') }}
                    </div>
                    <h1 class="text-3xl sm:text-5xl font-black text-[#1B3B36] tracking-tight leading-[1.1] mb-4">
                        {{ __('messages.fs_hero_title_line1') }} <br><span class="text-primary">{{ __('messages.fs_hero_title_line2') }}</span>
                    </h1>
                    <p class="text-neutral-600 text-sm font-medium max-w-sm mb-6 sm:mb-8 leading-relaxed">
                        {{ __('messages.fs_hero_desc') }}
                    </p>

                    <!-- Search Bar -->
                    <div class="bg-white p-2 sm:p-2.5 rounded-2xl shadow-xl shadow-neutral-200/50 border border-neutral-100 flex flex-col sm:flex-row items-center gap-2 sm:gap-3 max-w-2xl w-full overflow-hidden">

                        <!-- Location Input -->
                        <div class="flex items-center gap-2 pl-2 flex-1 w-full sm:w-auto border-b sm:border-b-0 sm:border-r border-neutral-100 pb-2 sm:pb-0">
                            <svg class="w-5 h-5 text-neutral-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <input type="text" placeholder="{{ __('messages.fs_location_ph') }}"
                                   class="w-full bg-transparent text-sm font-medium outline-none text-neutral-700 placeholder-neutral-400 rounded-xl px-2 py-1.5 focus:ring-2 focus:ring-primary/20 focus:bg-white/50 transition">
                        </div>

                        <!-- Food Arrangement Dropdown -->
                        <div class="flex items-center gap-2 flex-1 w-full sm:w-auto border-b sm:border-b-0 sm:border-r border-neutral-100 pb-2 sm:pb-0">
                            <svg class="w-5 h-5 text-neutral-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <select class="w-full bg-transparent text-sm font-bold text-neutral-700 outline-none appearance-none cursor-pointer rounded-xl px-2 py-1.5 focus:ring-2 focus:ring-primary/20 focus:bg-white/50 transition">
                                <option>{{ __('messages.fs_food_any') }}</option>
                                <option>{{ __('messages.fs_food_owner') }}</option>
                                <option>{{ __('messages.fs_food_sitter') }}</option>
                            </select>
                        </div>

                        <!-- Sitter Type Dropdown -->
                        <div class="flex items-center gap-2 flex-1 w-full sm:w-auto border-b sm:border-b-0 sm:border-r border-neutral-100 pb-2 sm:pb-0">
                            <svg class="w-5 h-5 text-neutral-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <select class="w-full bg-transparent text-sm font-bold text-neutral-700 outline-none appearance-none cursor-pointer rounded-xl px-2 py-1.5 focus:ring-2 focus:ring-primary/20 focus:bg-white/50 transition">
                                <option value="all">{{ __('messages.fs_level_any') }}</option>
                                <option value="small_pets">🐱 {{ __('messages.sa_st_small') }}</option>
                                <option value="large_pets">🐕 {{ __('messages.sa_st_large') }}</option>
                                <option value="exotic_pets">🦜 {{ __('messages.sa_st_exotic') }}</option>
                                <option value="all_pets">🐾 {{ __('messages.sa_st_all') }}</option>
                            </select>
                        </div>

                        <!-- Search Button -->
                        <button class="bg-primary hover:bg-primary-600 text-white text-sm font-bold px-6 py-3 rounded-xl flex items-center gap-1.5 transition-all shadow-md shadow-primary/20 w-full sm:w-auto justify-center flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            {{ __('messages.fs_search_btn') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TRUST BADGES ROW                          -->
        <!-- ========================================== -->
        <div class="px-2 sm:px-16 lg:px-24 py-4 sm:py-6 border-b border-neutral-100 bg-white dark:bg-neutral-900 dark:border-neutral-800">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">

                <!-- Badge 1: ID Verified -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-amber-50 dark:bg-amber-950/30 flex items-center justify-center text-amber-600 font-bold border border-amber-100 dark:border-amber-800 shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h4 class="text-xs sm:text-sm font-extrabold text-neutral-800 dark:text-white leading-tight">{{ __('messages.fs_badge_id_title') }}</h4>
                        <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.fs_badge_id_desc') }}</p>
                    </div>
                </div>

                <!-- Badge 2: Rated & Reviewed -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-amber-50 dark:bg-amber-950/30 flex items-center justify-center border border-amber-100 dark:border-amber-800 shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-yellow-400 fill-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h4 class="text-xs sm:text-sm font-extrabold text-neutral-800 dark:text-white leading-tight">{{ __('messages.fs_badge_rating_title') }}</h4>
                        <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.fs_badge_rating_desc') }}</p>
                    </div>
                </div>

                <!-- Badge 3: Easy Booking -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-amber-50 dark:bg-amber-950/30 flex items-center justify-center text-amber-600 font-bold border border-amber-100 dark:border-amber-800 shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h4 class="text-xs sm:text-sm font-extrabold text-neutral-800 dark:text-white leading-tight">{{ __('messages.fs_badge_booking_title') }}</h4>
                        <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.fs_badge_booking_desc') }}</p>
                    </div>
                </div>

                <!-- Badge 4: Flexible Food -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-amber-50 dark:bg-amber-950/30 flex items-center justify-center text-amber-600 font-bold border border-amber-100 dark:border-amber-800 shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h4 class="text-xs sm:text-sm font-extrabold text-neutral-800 dark:text-white leading-tight">{{ __('messages.fs_badge_food_title') }}</h4>
                        <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.fs_badge_food_desc') }}</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- ========================================== -->
        <!-- RECOMMENDED SITTERS GRID                  -->
        <!-- ========================================== -->
        <div class="px-2 sm:px-16 lg:px-24 py-8 sm:py-12 bg-neutral-50/50 dark:bg-neutral-900/30">
            <div class="flex justify-between items-end mb-6 sm:mb-8">
                <div>
                    <h2 class="text-xl sm:text-2xl font-black text-[#1B3B36] dark:text-white tracking-tight">{{ __('messages.fs_recommended_title') }}</h2>
                    <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.fs_recommended_desc') }}</p>
                </div>
                <span class="text-xs font-bold text-neutral-400 dark:text-neutral-500 shrink-0 ml-2">{{ __('messages.fs_sitters_count', ['count' => $sitters->count()]) }}</span>
            </div>

            <!-- SCROLLABLE CARDS CONTAINER -->
            <div class="max-h-[500px] sm:max-h-[600px] overflow-y-auto pr-2 pb-2 scrollbar-thin scrollbar-thumb-neutral-300 dark:scrollbar-thumb-neutral-700 scrollbar-track-transparent hover:scrollbar-thumb-neutral-400 dark:hover:scrollbar-thumb-neutral-600">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">

                    @forelse($sitters as $sitter)
                        @php
                            $profile = $sitter->sitterProfile;
                            $fullName = trim($sitter->f_name . ' ' . $sitter->l_name) ?: __('messages.sp_sitter_fallback');
                            $initials = strtoupper(substr($sitter->f_name ?? '', 0, 1)) . strtoupper(substr($sitter->l_name ?? '', 0, 1));
                            $rating = $profile->average_ratings ?? 0;
                            $fullStars = floor($rating);
                            $matchPct = $sitter->match_percentage ?? 0;

                            // Sitter Type — category-based
                            $typeInfo = match($profile->sitter_type) {
                                'small_pets'  => ['bg' => 'bg-blue-100',   'text' => 'text-blue-700',   'icon' => '🐱', 'label' => __('messages.sa_st_small_short')],
                                'large_pets'  => ['bg' => 'bg-green-100',  'text' => 'text-green-700',  'icon' => '🐕', 'label' => __('messages.sa_st_large_short')],
                                'exotic_pets' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'icon' => '🦜', 'label' => __('messages.sa_st_exotic_short')],
                                'all_pets'    => ['bg' => 'bg-amber-100',  'text' => 'text-amber-700',  'icon' => '🐾', 'label' => __('messages.sa_st_all_short')],
                                default       => ['bg' => 'bg-neutral-100','text' => 'text-neutral-700','icon' => '🐱', 'label' => __('messages.sa_st_small_short')],
                            };

                            $matchColor = $matchPct >= 85
                                ? 'bg-[#D1FAE5] text-[#065F46]'
                                : ($matchPct >= 70
                                    ? 'bg-[#FEF3C7] text-[#92400E]'
                                    : 'bg-neutral-200 text-neutral-700');

                            $foodLabel = match($profile->food_preference ?? '') {
                                'owner_provides', 'owner_provided'   => __('messages.fs_food_owner_full'),
                                'sitter_provides', 'sitter_provided' => __('messages.fs_food_sitter_full'),
                                'flexible'                            => __('messages.fs_food_flexible_full'),
                                default                               => __('messages.fs_food_not_set'),
                            };
                        @endphp

                        <a href="{{ route('owner.sitter.profile', ['id' => $sitter->id]) }}" class="block group">
                            <div class="bg-white dark:bg-neutral-900 rounded-2xl sm:rounded-[2rem] border border-neutral-200/60 dark:border-neutral-700 p-3 shadow-sm hover:shadow-md transition-all flex flex-col justify-between h-full">
                                <div>
                                    <div class="relative h-[220px] rounded-xl sm:rounded-[1.5rem] overflow-hidden bg-neutral-100 dark:bg-neutral-800 mb-4">
                                        {{-- Profile Photo --}}
                                        @if($sitter->profile_photo && file_exists(public_path('storage/' . $sitter->profile_photo)))
                                            <img src="{{ asset('storage/' . $sitter->profile_photo) }}"
                                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                                alt="{{ $fullName }}">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-primary/10">
                                                <span class="text-6xl font-black text-primary">{{ $initials ?: '?' }}</span>
                                            </div>
                                        @endif

                                        {{-- Verified Badge --}}
                                        @if($sitter->id_validation_status === 'verified')
                                            <div class="absolute top-3 left-3 bg-[#059669] text-white text-[10px] font-extrabold px-2 py-1 rounded-md flex items-center gap-1 shadow-sm">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                                </svg>
                                                {{ __('messages.fs_verified_badge') }}
                                            </div>
                                        @endif

                                        {{-- Match Badge --}}
                                        <div class="absolute top-3 right-3 {{ $matchColor }} text-[10px] font-extrabold px-2 py-1 rounded-md shadow-sm">
                                            {{ __('messages.fs_match_pct', ['pct' => $matchPct]) }}
                                        </div>

                                        {{-- Name + Location --}}
                                        <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/80 via-black/40 to-transparent pt-12 text-white">
                                            <h3 class="text-lg font-black tracking-tight leading-none">{{ $fullName }}</h3>
                                            <p class="text-xs font-medium opacity-80 mt-1.5 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                {{ $sitter->location ?? __('messages.sp_location_not_set') }}
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Rating + Reviews --}}
                                    <div class="px-2 flex justify-between items-center mb-4">
                                        <div class="flex items-center gap-1 text-sm font-bold text-neutral-800 dark:text-white">
                                            @for($i = 0; $i < 5; $i++)
                                                <svg class="w-4 h-4 {{ $i < $fullStars ? 'text-yellow-400 fill-yellow-400' : 'text-neutral-300 fill-neutral-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endfor
                                            <span class="ml-1 text-neutral-800 dark:text-white">{{ number_format($rating, 1) }}</span>
                                        </div>
                                        <span class="text-xs font-bold text-neutral-400 dark:text-neutral-500">{{ __('messages.fs_bookings_count', ['count' => $profile->total_bookings ?? 0]) }}</span>
                                    </div>

                                    {{-- Food Preference + Sitter Type Badge --}}
                                    <div class="px-2 text-xs font-bold text-neutral-500 dark:text-neutral-400 flex items-center justify-between mb-4">
                                        <div class="flex items-center gap-1 min-w-0">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                            <span class="truncate">{{ $foodLabel }}</span>
                                        </div>
                                        <span class="{{ $typeInfo['bg'] }} {{ $typeInfo['text'] }} text-[9px] font-black px-2 py-0.5 rounded-md uppercase tracking-wide inline-flex items-center gap-1 shrink-0 ml-2">
                                            <span>{{ $typeInfo['icon'] }}</span>
                                            {{ $typeInfo['label'] }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Rate --}}
                                <div class="border-t border-neutral-100 dark:border-neutral-800 pt-4 px-2 pb-2 flex justify-between items-center">
                                    <span class="text-xs font-bold text-neutral-400 dark:text-neutral-500">{{ __('messages.fs_per_visit') }}</span>
                                    <span class="text-xl font-black text-primary">₱{{ number_format($profile->base_rate ?? 0, 0) }}</span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full text-center py-16">
                            <div class="w-16 h-16 mx-auto rounded-full bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-black text-[#1B3B36] dark:text-white">{{ __('messages.fs_no_sitters_title') }}</h3>
                            <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.fs_no_sitters_desc') }}</p>
                        </div>
                    @endforelse

                </div>
            </div>
        </div>

    </div>
</x-app-layout>
