<x-app-layout>
    <!-- ========================================== -->
    <!-- HEADER SLOT                               -->
    <!-- ========================================== -->
    <x-slot name="header">
        <div>
            <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                {{ __('messages.sa_title') }}
            </h1>
            <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.sa_subtitle') }}</p>
        </div>
    </x-slot>

    <!-- ========================================== -->
    <!-- MAIN CONTENT                              -->
    <!-- ========================================== -->
    <div class="py-4 sm:py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200"
         x-data="{
            step: 1,
            totalSteps: 4,
            confirmSubmit: false,
            next() { if (this.step < this.totalSteps) this.step++; },
            prev() { if (this.step > 1) this.step--; }
         }">
        <div class="w-full sm:max-w-3xl mx-auto px-2 sm:px-16 lg:px-24">

            @php
                $sitterProfile = auth()->user()->sitterProfile ?? null;
                $currentPetTypes = $sitterProfile->preferred_pet_types ?? [];
                $currentPetSizes = $sitterProfile->preferred_pet_sizes ?? [];
                $petTypes = \App\Models\PetType::all();
                $status = auth()->user()->sitter_status;
                $hasApplied = auth()->user()->sitter_applied_at !== null;
            @endphp

            <!-- Back to Dashboard Link (TOP LEFT) -->
            <div class="mb-4 sm:mb-6">
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center gap-1 text-sm text-neutral-500 hover:text-primary transition font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    {{ __('messages.sa_back_dashboard') }}
                </a>
            </div>

            <!-- MESSAGE ALERTS (Success / Error) -->
            @if(session('status'))
                <div class="mb-4 sm:mb-6 p-3 sm:p-4 rounded-xl border border-green-500 bg-green-50 dark:bg-green-950/20 dark:border-green-700">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl shrink-0">✅</span>
                        <p class="font-bold text-green-700 dark:text-green-400">{{ session('status') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 sm:mb-6 p-3 sm:p-4 rounded-xl border border-red-500 bg-red-50 dark:bg-red-950/20 dark:border-red-700">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl shrink-0">❌</span>
                        <p class="font-bold text-red-700 dark:text-red-400">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            {{-- ========================================== --}}
            {{-- PENDING — show status card only              --}}
            {{-- ========================================== --}}
            @if($status === 'pending' && $hasApplied)
                <div class="bg-white dark:bg-neutral-900 rounded-2xl sm:rounded-3xl shadow-sm sm:shadow-xl border border-gray-200/60 dark:border-neutral-800 p-6 sm:p-10">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center text-yellow-600 dark:text-yellow-400 mb-4">
                            <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>

                        <h2 class="text-xl sm:text-2xl font-black text-[#1B3B36] dark:text-white mb-2">
                            {{ __('messages.sa_pending_title') }}
                        </h2>

                        <p class="text-sm text-neutral-500 dark:text-neutral-400 max-w-md mb-6">
                            {{ __('messages.sa_pending_desc') }}
                        </p>

                        <div class="w-full max-w-md bg-yellow-50 dark:bg-yellow-950/20 border border-yellow-200 dark:border-yellow-800/50 rounded-xl p-4 mb-6">
                            <div class="flex items-center justify-between gap-2 text-sm">
                                <span class="text-yellow-700 dark:text-yellow-400 font-semibold">
                                    {{ __('messages.sa_pending_applied') }}
                                </span>
                                <span class="font-bold text-[#1B3B36] dark:text-white">
                                    {{ auth()->user()->sitter_applied_at?->format('M d, Y') }}
                                </span>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-2 w-full max-w-md">
                            <a href="{{ route('help_support.index') }}"
                               class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3 border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-sm rounded-xl hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ __('messages.sa_pending_contact') }}
                            </a>
                            <a href="{{ route('dashboard') }}"
                               class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3 bg-primary hover:bg-primary-600 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition">
                                {{ __('messages.sa_pending_dashboard') }}
                            </a>
                        </div>
                    </div>
                </div>

            {{-- ========================================== --}}
            {{-- APPROVED — show status card only             --}}
            {{-- ========================================== --}}
            @elseif($status === 'approved')
                <div class="bg-white dark:bg-neutral-900 rounded-2xl sm:rounded-3xl shadow-sm sm:shadow-xl border border-gray-200/60 dark:border-neutral-800 p-6 sm:p-10">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400 mb-4">
                            <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>

                        <h2 class="text-xl sm:text-2xl font-black text-[#1B3B36] dark:text-white mb-2">
                            {{ __('messages.sa_status_approved_title') }}
                        </h2>

                        <p class="text-sm text-neutral-500 dark:text-neutral-400 max-w-md mb-6">
                            {{ __('messages.sa_status_approved_desc') }}
                        </p>

                        <div class="flex flex-col sm:flex-row gap-2 w-full max-w-md">
                            <a href="{{ route('settings.index') }}"
                               class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3 bg-primary hover:bg-primary-600 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ __('messages.sa_approved_settings') }}
                            </a>
                            <a href="{{ route('sitter.dashboard') }}"
                               class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3 border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-sm rounded-xl hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                                {{ __('messages.sa_approved_dashboard') }}
                            </a>
                        </div>
                    </div>
                </div>

            {{-- ========================================== --}}
            {{-- REJECTED — show status card + reapply        --}}
            {{-- ========================================== --}}
            @elseif($status === 'rejected')
                <div class="bg-white dark:bg-neutral-900 rounded-2xl sm:rounded-3xl shadow-sm sm:shadow-xl border border-gray-200/60 dark:border-neutral-800 p-6 sm:p-10 mb-4 sm:mb-6">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center text-red-600 dark:text-red-400 mb-4">
                            <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>

                        <h2 class="text-xl sm:text-2xl font-black text-[#1B3B36] dark:text-white mb-2">
                            {{ __('messages.sa_status_rejected_title') }}
                        </h2>

                        <p class="text-sm text-neutral-500 dark:text-neutral-400 max-w-md mb-6">
                            {{ __('messages.sa_status_rejected_desc') }}
                        </p>

                        @if(auth()->user()->admin_notes)
                            <div class="w-full max-w-md bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-800/50 rounded-xl p-4 mb-6 text-left">
                                <p class="text-xs font-bold text-red-700 dark:text-red-400 uppercase tracking-wider mb-1">
                                    {{ __('messages.sa_rejected_reason') }}
                                </p>
                                <p class="text-sm text-red-600 dark:text-red-300">
                                    {{ auth()->user()->admin_notes }}
                                </p>
                            </div>
                        @endif

                        <a href="{{ route('sitter.application') }}?reapply=1"
                           class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary hover:bg-primary-600 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            {{ __('messages.sa_reapply_btn') }}
                        </a>
                    </div>
                </div>

            {{-- ========================================== --}}
            {{-- NEW — show the form                          --}}
            {{-- ========================================== --}}
            @else
                <!-- Application Form (Multi-Step Wizard) -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl sm:rounded-3xl shadow-sm sm:shadow-xl border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-8">
                    <h2 class="text-lg font-black text-[#1B3B36] dark:text-white mb-4 sm:mb-6">{{ __('messages.sa_sitter_details') }}</h2>

                    <!-- ========================================== -->
                    <!-- STEP PROGRESS INDICATOR                    -->
                    <!-- ========================================== -->
                    <div class="flex items-center justify-between gap-1 sm:gap-2 mb-6 sm:mb-8">
                        @php
                            $stepTitles = [
                                1 => 'About You',
                                2 => 'Rate & Food',
                                3 => 'Pet Preferences',
                                4 => 'Certificates',
                            ];
                        @endphp

                        @for ($i = 1; $i <= 4; $i++)
                            {{-- Step circle --}}
                            <div class="flex flex-col items-center flex-shrink-0">
                                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-[10px] sm:text-xs font-bold transition"
                                    :class="step >= {{ $i }} ? 'bg-primary text-white' : 'bg-gray-200 dark:bg-neutral-800 text-gray-400'">
                                    <template x-if="step > {{ $i }}">
                                        <span>✓</span>
                                    </template>
                                    <template x-if="step <= {{ $i }}">
                                        <span>{{ $i }}</span>
                                    </template>
                                </div>
                                <span class="text-[9px] sm:text-[10px] font-bold mt-1 leading-tight text-center hidden sm:block"
                                      :class="step >= {{ $i }} ? 'text-primary' : 'text-gray-400'">
                                    {{ $stepTitles[$i] }}
                                </span>
                            </div>

                            {{-- Connector --}}
                            @if ($i < 4)
                                <div class="flex-1 h-0.5 transition"
                                     :class="step > {{ $i }} ? 'bg-primary' : 'bg-gray-200 dark:bg-neutral-800'"></div>
                            @endif
                        @endfor
                    </div>

                    <form method="POST"
                          x-ref="applicationForm"
                          action="{{ $sitterProfile ? route('sitter.application.update') : route('sitter.application.store') }}"
                          enctype="multipart/form-data" class="space-y-4 sm:space-y-6">
                        @csrf
                        @if($sitterProfile)
                            @method('PUT')
                        @endif

                        <!-- ========================================== -->
                        <!-- STEP 1: ABOUT YOU + EXPERIENCE + SITTER TYPE -->
                        <!-- ========================================== -->
                        <div x-show="step === 1" x-transition.opacity class="space-y-4 sm:space-y-6">

                            <!-- 1. ABOUT YOU (Bio) -->
                            <div>
                                <label for="bio" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    {{ __('messages.sa_about_you') }}
                                </label>
                                <textarea id="bio" name="bio" rows="4"
                                    placeholder="{{ __('messages.sa_about_placeholder') }}"
                                    class="mt-1 block w-full px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium">{{ old('bio', $sitterProfile->bio ?? '') }}</textarea>
                                <p class="mt-1 text-xs text-neutral-400">{{ __('messages.sa_about_hint') }}</p>
                                <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('bio')" />
                            </div>

                            <!-- 2. YEARS OF EXPERIENCE -->
                            <div>
                                <label for="years_experience" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    {{ __('messages.sa_years_exp') }}
                                </label>
                                <input id="years_experience" name="years_experience" type="number" step="0.5" min="0" max="50"
                                    value="{{ old('years_experience', $sitterProfile->experience_years ?? '') }}"
                                    placeholder="{{ __('messages.sa_years_placeholder') }}"
                                    class="mt-1 block w-full px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium">
                                <p class="mt-1 text-xs text-neutral-400">{{ __('messages.sa_years_hint') }}</p>
                                <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('years_experience')" />
                            </div>

                            <!-- 3. SITTER TYPE -->
                            <div>
                                <label for="sitter_type" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    {{ __('messages.sa_sitter_type') }}
                                </label>
                                <select id="sitter_type" name="sitter_type"
                                    class="mt-1 block w-full px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium">
                                    <option value="small_pets" {{ old('sitter_type', $sitterProfile->sitter_type ?? 'small_pets') == 'small_pets' ? 'selected' : '' }}>
                                        🐱 {{ __('messages.sa_st_small') }}
                                    </option>
                                    <option value="large_pets" {{ old('sitter_type', $sitterProfile->sitter_type ?? '') == 'large_pets' ? 'selected' : '' }}>
                                        🐕 {{ __('messages.sa_st_large') }}
                                    </option>
                                    <option value="exotic_pets" {{ old('sitter_type', $sitterProfile->sitter_type ?? '') == 'exotic_pets' ? 'selected' : '' }}>
                                        🦜 {{ __('messages.sa_st_exotic') }}
                                    </option>
                                    <option value="all_pets" {{ old('sitter_type', $sitterProfile->sitter_type ?? '') == 'all_pets' ? 'selected' : '' }}>
                                        🐾 {{ __('messages.sa_st_all') }}
                                    </option>
                                </select>
                                <p class="mt-1 text-xs text-neutral-400">{{ __('messages.sa_sitter_type_hint') }}</p>
                                <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('sitter_type')" />
                            </div>
                        </div>

                        <!-- ========================================== -->
                        <!-- STEP 2: RATE + FOOD ARRANGEMENT           -->
                        <!-- ========================================== -->
                        <div x-show="step === 2" x-transition.opacity class="space-y-4 sm:space-y-6">

                            <!-- 4. BASE RATE (Rate per Visit) -->
                            <div>
                                <label for="rate_per_visit" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    {{ __('messages.sa_rate_per_visit') }}
                                </label>
                                <input id="rate_per_visit" name="rate_per_visit" type="number" step="0.01" min="0"
                                    value="{{ old('rate_per_visit', $sitterProfile->base_rate ?? '') }}"
                                    placeholder="{{ __('messages.sa_rate_placeholder') }}"
                                    class="mt-1 block w-full px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium">
                                <p class="mt-1 text-xs text-neutral-400">{{ __('messages.sa_rate_hint') }}</p>
                                <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('rate_per_visit')" />
                            </div>

                            <!-- 5. FOOD PREFERENCE -->
                            <div>
                                <label for="food_preference" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    {{ __('messages.sa_food_arrangement') }}
                                </label>
                                <select id="food_preference" name="food_preference"
                                    class="mt-1 block w-full px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium">
                                    <option value="owner_provides" {{ old('food_preference', $sitterProfile->food_preference ?? '') == 'owner_provides' ? 'selected' : '' }}>{{ __('messages.sa_food_owner') }}</option>
                                    <option value="sitter_provides" {{ old('food_preference', $sitterProfile->food_preference ?? '') == 'sitter_provides' ? 'selected' : '' }}>{{ __('messages.sa_food_sitter') }}</option>
                                    <option value="flexible" {{ old('food_preference', $sitterProfile->food_preference ?? '') == 'flexible' ? 'selected' : '' }}>{{ __('messages.sa_food_flexible') }}</option>
                                </select>
                                <p class="mt-1 text-xs text-neutral-400">{{ __('messages.sa_food_arrangement_hint') }}</p>
                                <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('food_preference')" />
                            </div>

                            <!-- 6. FOOD BUDGET (Conditional) -->
                            <div id="food_budget_container"
                                 class="{{ old('food_preference', $sitterProfile->food_preference ?? '') == 'sitter_provides' ? '' : 'hidden' }}">
                                <label for="food_budget" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    {{ __('messages.sa_food_budget') }}
                                </label>
                                <input id="food_budget" name="food_budget" type="number" step="0.01" min="0"
                                    value="{{ old('food_budget', $sitterProfile->food_budget ?? '100') }}"
                                    placeholder="{{ __('messages.sa_food_budget_placeholder') }}"
                                    class="mt-1 block w-full px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium">
                                <p class="mt-1 text-xs text-neutral-400">{{ __('messages.sa_food_budget_hint') }}</p>
                                <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('food_budget')" />
                            </div>
                        </div>

                        <!-- ========================================== -->
                        <!-- STEP 3: PET TYPES + SIZES + CAPACITY        -->
                        <!-- ========================================== -->
                        <div x-show="step === 3" x-transition.opacity class="space-y-4 sm:space-y-6">

                            <!-- 7. PREFERRED PET TYPES -->
                            <div class="pt-2">
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-3">
                                    {{ __('messages.sa_pet_types') }}
                                </label>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3">
                                    @foreach($petTypes as $petType)
                                        <label class="flex items-center p-2.5 sm:p-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 cursor-pointer hover:border-primary/50 transition">
                                            <input type="checkbox" name="preferred_pet_types[]" value="{{ $petType->id }}"
                                                   class="sr-only peer"
                                                   {{ in_array($petType->id, old('preferred_pet_types', $currentPetTypes)) ? 'checked' : '' }}>
                                            <div class="w-full text-center peer-checked:bg-primary/10 peer-checked:border-primary peer-checked:text-primary rounded-lg p-2 border border-transparent transition">
                                                <span class="text-2xl block mb-1">{{ $petType->icon }}</span>
                                                <span class="text-xs font-bold">{{ $petType->name }}</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                <p class="mt-1 text-xs text-neutral-400">{{ __('messages.sa_pet_types_hint') }}</p>
                            </div>

                            <!-- 8. PREFERRED PET SIZES -->
                            <div class="pt-2">
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-3">
                                    {{ __('messages.sa_pet_sizes') }}
                                </label>
                                <div class="flex flex-wrap gap-2.5 sm:gap-3">
                                    @foreach(['small', 'medium', 'large', 'giant'] as $size)
                                        <label class="inline-flex items-center px-3 py-2 sm:px-4 sm:py-2 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 cursor-pointer hover:border-primary/50 transition">
                                            <input type="checkbox" name="preferred_pet_sizes[]" value="{{ $size }}"
                                                   class="sr-only peer"
                                                   {{ in_array($size, old('preferred_pet_sizes', $currentPetSizes)) ? 'checked' : '' }}>
                                            <span class="peer-checked:text-primary font-bold text-sm">
                                                {{ __('messages.sa_size_' . $size) }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                <p class="mt-1 text-xs text-neutral-400">{{ __('messages.sa_pet_sizes_hint') }}</p>
                            </div>

                            <!-- 9. PET CAPACITY -->
                            <div class="pt-2">
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-3">
                                    {{ __('messages.sa_pet_capacity') }}
                                </label>
                                <div class="grid grid-cols-2 gap-3 sm:gap-4">
                                    <div>
                                        <label for="min_pets_capacity" class="block text-[10px] font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400 mb-0.5">
                                            {{ __('messages.sa_min') }}
                                        </label>
                                        <input id="min_pets_capacity" name="min_pets_capacity" type="number" min="0" max="20"
                                            value="{{ old('min_pets_capacity', $sitterProfile->min_pets_capacity ?? '') }}"
                                            placeholder="{{ __('messages.sa_min_placeholder') }}"
                                            class="mt-1 block w-full px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium">
                                        <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('min_pets_capacity')" />
                                    </div>
                                    <div>
                                        <label for="max_pets_capacity" class="block text-[10px] font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400 mb-0.5">
                                            {{ __('messages.sa_max') }}
                                        </label>
                                        <input id="max_pets_capacity" name="max_pets_capacity" type="number" min="1" max="20"
                                            value="{{ old('max_pets_capacity', $sitterProfile->max_pets_capacity ?? '') }}"
                                            placeholder="{{ __('messages.sa_max_placeholder') }}"
                                            class="mt-1 block w-full px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium">
                                        <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('max_pets_capacity')" />
                                    </div>
                                </div>
                                <p class="mt-1 text-xs text-neutral-400">{{ __('messages.sa_capacity_hint') }}</p>
                            </div>
                        </div>

                        <!-- ========================================== -->
                        <!-- STEP 4: UPLOAD CERTIFICATES                -->
                        <!-- ========================================== -->
                        <div x-show="step === 4" x-transition.opacity class="space-y-4 sm:space-y-6">

                            <!-- 10. UPLOAD CERTIFICATES (Multiple) -->
                            <div x-data="{
                                    files: [],
                                    handleFiles(e) {
                                        const newFiles = Array.from(e.target.files);
                                        newFiles.forEach(file => {
                                            this.files.push({
                                                name: file.name,
                                                size: (file.size / 1024).toFixed(2) + ' KB',
                                                isImage: file.type.startsWith('image/'),
                                                preview: null,
                                                file: file
                                            });
                                            if (file.type.startsWith('image/')) {
                                                const reader = new FileReader();
                                                const idx = this.files.length - 1;
                                                reader.onload = (evt) => { this.files[idx].preview = evt.target.result; };
                                                reader.readAsDataURL(file);
                                            }
                                        });
                                    },
                                    removeFile(index) {
                                        this.files.splice(index, 1);
                                        const dt = new DataTransfer();
                                        this.files.forEach(f => dt.items.add(f.file));
                                        this.$refs.fileInput.files = dt.files;
                                    }
                                }">
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    {{ __('messages.sa_upload_certs') }}
                                </label>

                                <div class="mt-1 border-2 border-dashed border-gray-300 dark:border-neutral-700 rounded-xl p-4 sm:p-6 text-center hover:border-primary/50 transition">

                                    <input type="file"
                                        x-ref="fileInput"
                                        @change="handleFiles"
                                        name="certificates[]"
                                        accept=".pdf,.jpg,.jpeg,.png"
                                        class="hidden"
                                        id="cert-upload"
                                        multiple>

                                    <label for="cert-upload" x-show="files.length === 0" class="cursor-pointer block">
                                        <svg class="w-10 h-10 sm:w-12 sm:h-12 mx-auto text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">
                                            {{ __('messages.sa_upload_certs_click') }}
                                        </p>
                                        <p class="text-xs text-neutral-400">{{ __('messages.sa_upload_certs_desc') }}</p>
                                    </label>

                                    <div x-show="files.length > 0" x-cloak class="space-y-3">
                                        <label for="cert-upload" class="cursor-pointer inline-flex items-center gap-1 text-xs font-bold text-primary hover:text-primary-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            {{ __('messages.sa_add_more') }}
                                        </label>

                                        <template x-for="(file, index) in files" :key="index">
                                            <div class="flex items-center justify-between gap-2 sm:gap-3 p-2.5 sm:p-3 bg-white dark:bg-neutral-900 rounded-lg border border-gray-200 dark:border-neutral-700">
                                                <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                                                    <template x-if="file.isImage && file.preview">
                                                        <img :src="file.preview" alt="Preview" class="w-9 h-9 sm:w-10 sm:h-10 object-cover rounded-md border border-gray-300 shrink-0">
                                                    </template>
                                                    <template x-if="!file.isImage">
                                                        <svg class="w-9 h-9 sm:w-10 sm:h-10 text-neutral-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                        </svg>
                                                    </template>
                                                    <div class="text-left min-w-0">
                                                        <p x-text="file.name" class="text-sm font-bold text-neutral-800 dark:text-neutral-200 truncate"></p>
                                                        <p x-text="file.size" class="text-xs text-neutral-500"></p>
                                                    </div>
                                                </div>
                                                <button type="button" @click="removeFile(index)" class="text-xs font-bold text-red-500 hover:text-red-700 shrink-0">
                                                    {{ __('messages.sa_remove') }}
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <p class="mt-1 text-xs text-neutral-400">{{ __('messages.sa_upload_certs_hint') }}</p>
                                <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('certificates')" />
                                <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('certificates.*')" />
                            </div>
                        </div>

                        <!-- ========================================== -->
                        <!-- 11. NAVIGATION BUTTONS (Next / Back / Submit) -->
                        <!-- ========================================== -->
                        <div class="pt-4 flex items-center gap-3">

                            {{-- Back button --}}
                            <button type="button"
                                    @click="prev()"
                                    x-show="step > 1"
                                    x-transition.opacity
                                    class="flex-1 sm:flex-none px-6 py-3 rounded-xl border-2 border-gray-300 dark:border-neutral-700 text-neutral-700 dark:text-neutral-200 font-bold text-sm hover:bg-gray-50 dark:hover:bg-neutral-800 transition">
                                ← Back
                            </button>

                            {{-- Next button --}}
                            <button type="button"
                                    @click="next()"
                                    x-show="step < totalSteps"
                                    x-transition.opacity
                                    class="flex-1 flex justify-center items-center gap-2 px-6 py-3 bg-primary hover:bg-primary-600 text-white text-sm font-black rounded-xl shadow-md hover:shadow-lg active:scale-[0.99] transition-all duration-150">
                                Next →
                            </button>

                            {{-- Submit button (last step only) — opens confirmation modal --}}
                            <button type="button"
                                    @click="confirmSubmit = true"
                                    x-show="step === totalSteps"
                                    x-transition.opacity
                                    class="flex-1 flex justify-center items-center px-4 py-3 sm:py-3.5 bg-primary hover:bg-primary-600 text-white text-sm font-black rounded-xl shadow-md hover:shadow-lg active:scale-[0.99] transition-all duration-150">
                                {{ $sitterProfile ? __('messages.sa_update_application') : __('messages.sa_submit_application') }}
                            </button>
                        </div>
                    </form>
                </div>
            @endif

        </div>

        {{-- ========================================== --}}
        {{-- SUBMIT CONFIRMATION MODAL                  --}}
        {{-- ========================================== --}}
        <div x-show="confirmSubmit"
             x-cloak
             x-transition.opacity
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
             @click.away="confirmSubmit = false">

            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-neutral-800 max-w-md w-full p-5 sm:p-6"
                 @click.stop
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">

                <div class="flex items-start gap-3 mb-4">
                    <div class="w-11 h-11 rounded-full bg-primary/10 flex items-center justify-center text-primary shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-black text-[#1B3B36] dark:text-white">
                            {{ __('messages.sa_confirm_title') }}
                        </h3>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                            {{ __('messages.sa_confirm_desc') }}
                        </p>
                    </div>
                </div>

                <div class="flex gap-2 mt-4">
                    <button type="button"
                            @click="confirmSubmit = false"
                            class="flex-1 px-4 py-2.5 rounded-xl border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-xs hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                        {{ __('messages.sa_cancel') }}
                    </button>
                    <button type="button"
                            @click="$refs.applicationForm.submit()"
                            class="flex-1 px-4 py-2.5 rounded-xl bg-primary hover:bg-primary-600 text-white font-bold text-xs transition shadow-md hover:shadow-lg">
                        {{ __('messages.sa_confirm_submit') }}
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- JavaScript: Toggle Food Budget -->
    @push('scripts')
    <script>
    (function () {
        function initSitterForm() {
            var foodPreference = document.getElementById('food_preference');
            var foodBudgetContainer = document.getElementById('food_budget_container');

            if (foodPreference && foodBudgetContainer) {
                foodPreference.addEventListener('change', function () {
                    if (this.value === 'sitter_provides') {
                        foodBudgetContainer.classList.remove('hidden');
                    } else {
                        foodBudgetContainer.classList.add('hidden');
                    }
                });
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initSitterForm);
        } else {
            initSitterForm();
        }
    })();
    </script>
    @endpush

</x-app-layout>
