<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('owner.sitter.profile', ['id' => $sitter->id]) }}"
               class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                    {{ __('messages.cb_title') }}
                </h1>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.cb_subtitle') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200"
         x-data="bookingForm({
             baseRate: {{ $sitter->sitterProfile->base_rate ?? 350 }},
             initialStartDate: '{{ old('start_date', date('Y-m-d')) }}',
             initialEndDate: '{{ old('end_date', date('Y-m-d', strtotime('+2 days'))) }}',
             initialVisitsPerDay: {{ old('visit_per_day', 2) }},
             initialFoodPreference: '{{ old('food_preference', 'owner_provides') }}',
             initialFoodBudget: {{ old('food_budget', 0) }}
         })"
         x-init="init()">
        <div class="w-full sm:max-w-4xl mx-auto px-2 sm:px-16 lg:px-24">

            @if($errors->any())
                <div class="mb-4 p-4 rounded-xl bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-800">
                    <p class="font-bold text-red-700 dark:text-red-400 mb-1">{{ __('messages.cb_errors_intro') }}</p>
                    <ul class="list-disc list-inside text-xs text-red-600 dark:text-red-400 space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-neutral-900 rounded-2xl sm:rounded-3xl shadow-sm sm:shadow-xl border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-8">

                <form method="POST" action="{{ route('mybookings.store') }}" class="space-y-6 sm:space-y-8">
                    @csrf
                    <input type="hidden" name="sitter_id" value="{{ $sitter->id }}">

                    {{-- 1. SELECTED SITTER --}}
                    <div>
                        <label class="block text-sm font-bold text-[#1B3B36] dark:text-white mb-2">
                            {{ __('messages.cb_step1_title') }}
                        </label>
                        @php
                            $sitterFullName = trim($sitter->f_name . ' ' . $sitter->l_name) ?: 'Sitter';
                            $sitterInitial  = strtoupper(substr($sitter->f_name ?? 'S', 0, 1));
                            $rating         = number_format($sitter->sitterProfile->average_ratings ?? 0, 1);
                            $baseRate       = number_format($sitter->sitterProfile->base_rate ?? 0, 0);
                        @endphp
                        <div class="flex items-center gap-3 sm:gap-4 p-3 sm:p-4 rounded-xl border-2 border-primary/30 bg-primary/5 dark:bg-primary/10">
                            @if($sitter->profile_photo && file_exists(public_path('storage/' . $sitter->profile_photo)))
                                <img src="{{ asset('storage/' . $sitter->profile_photo) }}"
                                     alt="{{ $sitterFullName }}"
                                     class="w-12 h-12 sm:w-14 sm:h-14 rounded-full object-cover border-2 border-primary/20 shrink-0">
                            @else
                                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-primary flex items-center justify-center text-white font-black text-xl sm:text-2xl shrink-0">
                                    {{ $sitterInitial }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <p class="text-base font-black text-[#1B3B36] dark:text-white">{{ $sitterFullName }}</p>
                                <div class="flex items-center flex-wrap gap-x-2 gap-y-0.5 text-xs sm:text-sm text-neutral-600 dark:text-neutral-400">
                                    <span>⭐ {{ $rating }}</span>
                                    <span class="text-neutral-300">•</span>
                                    <span>₱{{ $baseRate }} / visit</span>
                                    @if($sitter->id_validation_status === 'verified')
                                        <span class="text-neutral-300">•</span>
                                        <span class="text-xs text-green-600 font-semibold">{{ __('messages.cb_id_verified') }}</span>
                                    @endif
                                </div>
                                <p class="text-xs text-neutral-500 mt-0.5">📍 {{ $sitter->location ?? __('messages.bc_location_not_set') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- 2. SELECT PET --}}
                    <div>
                        <label class="block text-sm font-bold text-[#1B3B36] dark:text-white mb-2">
                            {{ __('messages.cb_step2_title') }} <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5 sm:gap-3">
                            @forelse($pets as $pet)
                                @php
                                    $emoji = match($pet->petType?->name ?? '') {
                                        'Dog' => '🐶', 'Cat' => '🐱', 'Bird' => '🐦',
                                        'Rabbit' => '🐰', 'Hamster' => '🐹', 'Fish' => '🐟',
                                        'Reptile' => '🦎', default => '🐾',
                                    };
                                @endphp
                                <label class="flex items-center gap-3 p-3 rounded-xl border-2 border-gray-200 dark:border-neutral-700 hover:border-primary cursor-pointer transition has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                                    <input type="radio" name="pet_id" value="{{ $pet->id }}" required
                                           {{ old('pet_id') == $pet->id ? 'checked' : '' }}
                                           class="w-4 h-4 text-primary focus:ring-primary shrink-0">
                                    <div class="min-w-0 flex-1">
                                        <div class="text-2xl">{{ $emoji }}</div>
                                        <p class="text-sm font-bold text-[#1B3B36] dark:text-white truncate">{{ $pet->name }}</p>
                                        <p class="text-xs text-neutral-500 truncate">
                                            {{ $pet->petType?->name ?? __('messages.cb_pet_unknown') }} • {{ $pet->age }} {{ __('messages.cb_pet_yrs') }} • {{ ucfirst($pet->size ?? 'small') }}
                                        </p>
                                    </div>
                                </label>
                            @empty
                                <div class="col-span-full text-center py-6">
                                    <p class="text-sm text-neutral-500">
                                        {{ __('messages.cb_no_pets') }}
                                        <a href="{{ route('mypets.create') }}" class="text-primary font-bold hover:underline">{{ __('messages.cb_add_pet_first') }}</a>
                                    </p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- 3. DATE RANGE --}}
                    <div>
                        <label class="block text-sm font-bold text-[#1B3B36] dark:text-white mb-2">
                            {{ __('messages.cb_step3_title') }} <span class="text-red-500">*</span>
                        </label>
                        <p class="text-[11px] text-neutral-500 dark:text-neutral-400 mb-3">
                            {{ __('messages.cb_step3_desc') }}
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                            <div>
                                <label for="start_date" class="block text-xs text-neutral-500 dark:text-neutral-400 mb-1.5">
                                    {{ __('messages.cb_start_date') }}
                                </label>
                                <input type="date" id="start_date" name="start_date" x-model="startDate"
                                       min="{{ date('Y-m-d') }}" required
                                       class="w-full px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-950 text-sm text-neutral-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                            </div>

                            <div>
                                <label for="end_date" class="block text-xs text-neutral-500 dark:text-neutral-400 mb-1.5">
                                    {{ __('messages.cb_end_date') }}
                                </label>
                                <input type="date" id="end_date" name="end_date" x-model="endDate"
                                       min="{{ date('Y-m-d') }}" required
                                       class="w-full px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-950 text-sm text-neutral-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                            </div>
                        </div>

                        <div x-show="days > 0" x-cloak class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">
                            📅 {{ __('messages.cb_total_duration') }} <strong x-text="days"></strong> {{ __('messages.cb_days') }}
                        </div>
                    </div>

                    {{-- 4. VISITS PER DAY --}}
                    <div>
                        <label class="block text-sm font-bold text-[#1B3B36] dark:text-white mb-2">
                            {{ __('messages.cb_step4_title') }} <span class="text-red-500">*</span>
                        </label>
                        <p class="text-[11px] text-neutral-500 dark:text-neutral-400 mb-3">
                            {{ __('messages.cb_step4_desc') }}
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                            <div>
                                <label for="visit_preset" class="block text-xs text-neutral-500 dark:text-neutral-400 mb-1.5">
                                    {{ __('messages.cb_visits_count') }}
                                </label>
                                <select id="visit_preset" x-model="visitPreset" @change="onPresetChange()"
                                        class="w-full px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-950 text-sm text-neutral-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                                    <option value="1">{{ __('messages.cb_visits_1') }}</option>
                                    <option value="2">{{ __('messages.cb_visits_2') }}</option>
                                    <option value="3">{{ __('messages.cb_visits_3') }}</option>
                                    <option value="4">{{ __('messages.cb_visits_4') }}</option>
                                    <option value="custom">{{ __('messages.cb_visits_custom') }}</option>
                                </select>

                                <template x-if="visitPreset === 'custom'">
                                    <div class="mt-2">
                                        <input type="number" x-model.number="visitsPerDay" min="1" max="20"
                                               placeholder="{{ __('messages.cb_visits_custom_ph') }}"
                                               class="w-full px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border-2 border-primary/30 bg-primary/5 dark:bg-primary/10 text-sm text-neutral-800 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition">
                                        <p class="text-[10px] text-neutral-400 mt-1">{{ __('messages.cb_visits_custom_hint') }}</p>
                                    </div>
                                </template>

                                <input type="hidden" name="visit_per_day" :value="visitsPerDay">
                            </div>

                            <div>
                                <label class="block text-xs text-neutral-500 dark:text-neutral-400 mb-1.5">
                                    {{ __('messages.cb_rate_label') }}
                                </label>
                                <input type="text" :value="'₱' + baseRate.toFixed(2)" readonly
                                       class="w-full px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-100 dark:bg-neutral-800 text-sm text-neutral-500 dark:text-neutral-400 cursor-not-allowed">
                            </div>
                        </div>
                    </div>

                    {{-- 5. TIME SCHEDULE --}}
                    <div>
                        <label class="block text-sm font-bold text-[#1B3B36] dark:text-white mb-2">
                            {{ __('messages.cb_step5_title') }} <span class="text-red-500">*</span>
                        </label>
                        <p class="text-[11px] text-neutral-500 dark:text-neutral-400 mb-3">
                            {{ __('messages.cb_step5_desc') }}
                        </p>

                        <div class="space-y-2.5">
                            <template x-for="(time, idx) in visitTimes" :key="idx">
                                <div class="flex items-center gap-3 p-2.5 sm:p-3 rounded-xl bg-neutral-50 dark:bg-neutral-800/50 border border-gray-100 dark:border-neutral-800">
                                    <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-black text-sm shrink-0">
                                        <span x-text="idx + 1"></span>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-bold text-[#1B3B36] dark:text-white">
                                            {{ __('messages.cb_visit_n', ['n' => '']) }}<span x-text="idx + 1"></span>
                                            <span class="text-neutral-400 font-normal"
                                                  x-text="'(' + getTimeLabel(visitTimes[idx]) + ')'"></span>
                                        </p>
                                        <p class="text-[10px] text-neutral-400" x-text="getTimePeriod(visitTimes[idx])"></p>
                                    </div>

                                    <input type="time" x-model="visitTimes[idx]" :name="`schedule_times[${idx}]`" required
                                           class="px-3 py-2 rounded-lg border border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-sm text-neutral-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition shrink-0">
                                </div>
                            </template>
                        </div>

                        @error('schedule_times')
                            <p class="text-xs text-red-500 mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 6. FOOD ARRANGEMENT --}}
                    <div>
                        <label class="block text-sm font-bold text-[#1B3B36] dark:text-white mb-2">
                            {{ __('messages.cb_step6_title') }} <span class="text-red-500">*</span>
                        </label>
                        <p class="text-[11px] text-neutral-500 dark:text-neutral-400 mb-3">
                            {{ __('messages.cb_step6_desc') }}
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                            <div>
                                <label for="food_preference" class="block text-xs text-neutral-500 dark:text-neutral-400 mb-1.5">
                                    {{ __('messages.cb_food_provider') }}
                                </label>
                                <select id="food_preference" name="food_preference" x-model="foodPreference" @change="onFoodChange()" required
                                        class="w-full px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-950 text-sm text-neutral-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                                    <option value="owner_provides">{{ __('messages.cb_food_owner') }}</option>
                                    <option value="sitter_provides">{{ __('messages.cb_food_sitter') }}</option>
                                    <option value="flexible">{{ __('messages.cb_food_flexible') }}</option>
                                </select>
                            </div>

                            <div>
                                <label for="food_budget" class="block text-xs text-neutral-500 dark:text-neutral-400 mb-1.5">
                                    {{ __('messages.cb_food_budget_label') }}
                                    <span x-show="foodPreference !== 'sitter_provides'" x-cloak
                                          class="text-neutral-400 font-normal italic">{{ __('messages.cb_not_applicable') }}</span>
                                </label>
                                <input type="number" id="food_budget" name="food_budget" x-model.number="foodBudget"
                                       min="0" step="0.01"
                                       placeholder="{{ __('messages.cb_food_budget_ph') }}"
                                       :disabled="foodPreference !== 'sitter_provides'"
                                       :class="foodPreference !== 'sitter_provides'
                                            ? 'bg-gray-100 dark:bg-neutral-800 cursor-not-allowed opacity-60'
                                            : 'bg-gray-50 dark:bg-neutral-950'"
                                       class="w-full px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 text-sm text-neutral-800 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                            </div>
                        </div>

                        <div class="mt-3 p-3 rounded-xl bg-amber-50 dark:bg-amber-950/20 border border-amber-100 dark:border-amber-900/50">
                            <p class="text-[11px] text-amber-700 dark:text-amber-400 leading-relaxed">
                                <template x-if="foodPreference === 'owner_provides'">
                                    <span>{{ __('messages.cb_food_owner_note') }}</span>
                                </template>
                                <template x-if="foodPreference === 'sitter_provides'">
                                    <span>{{ __('messages.cb_food_sitter_note') }}</span>
                                </template>
                                <template x-if="foodPreference === 'flexible'">
                                    <span>{{ __('messages.cb_food_flexible_note') }}</span>
                                </template>
                            </p>
                        </div>
                    </div>

                    {{-- 7. TASKS --}}
                    <div>
                        <label class="block text-sm font-bold text-[#1B3B36] dark:text-white mb-2">
                            {{ __('messages.cb_step7_title') }} <span class="text-xs text-neutral-400 font-medium">{{ __('messages.cb_optional') }}</span>
                        </label>
                        <p class="text-[11px] text-neutral-500 dark:text-neutral-400 mb-3">
                            {{ __('messages.cb_step7_desc') }}
                        </p>

                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <input type="text" x-model="newTask" @keydown.enter.prevent="addTask()"
                                       placeholder="{{ __('messages.cb_task_ph') }}"
                                       class="flex-1 min-w-0 px-3 py-2.5 sm:px-4 sm:py-2.5 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-950 text-sm text-neutral-800 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                                <button type="button" @click="addTask()"
                                        class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-primary text-white hover:bg-primary-600 transition shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </button>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <template x-for="(task, idx) in tasks" :key="idx">
                                    <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-medium bg-primary/10 text-primary">
                                        <span x-text="task"></span>
                                        <button type="button" @click="removeTask(idx)" class="text-primary/50 hover:text-red-500 transition">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                        <input type="hidden" name="tasks[]" :value="task">
                                    </span>
                                </template>
                            </div>
                        </div>
                        <p class="text-[10px] text-neutral-400 mt-1">{{ __('messages.cb_task_hint') }}</p>
                    </div>

                    {{-- 8. SPECIAL INSTRUCTIONS --}}
                    <div>
                        <label for="instructions" class="block text-sm font-bold text-[#1B3B36] dark:text-white mb-2">
                            {{ __('messages.cb_step8_title') }} <span class="text-xs text-neutral-400 font-medium">{{ __('messages.cb_optional') }}</span>
                        </label>
                        <textarea id="instructions" name="instructions" rows="3"
                                  placeholder="{{ __('messages.cb_special_ph') }}"
                                  class="w-full px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-950 text-sm text-neutral-800 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition resize-none">{{ old('instructions') }}</textarea>
                    </div>

                    {{-- PAYMENT SUMMARY --}}
                    <div class="bg-neutral-50 dark:bg-neutral-800/30 rounded-2xl p-4 sm:p-6 border border-gray-100 dark:border-neutral-700">
                        <h3 class="text-sm font-black text-[#1B3B36] dark:text-white uppercase tracking-wider mb-4">
                            {{ __('messages.cb_payment_summary') }}
                        </h3>
                        <div class="space-y-2 text-sm">

                            <div class="flex justify-between gap-2">
                                <span class="text-neutral-600 dark:text-neutral-400">
                                    {{ __('messages.cb_sitter_rate_label', ['rate' => '', 'visits' => '']) }}<span x-text="baseRate.toFixed(2)"></span>
                                </span>
                                <span class="font-bold text-neutral-800 dark:text-white shrink-0">
                                    ₱<span x-text="subtotal.toFixed(2)"></span>
                                </span>
                            </div>

                            <template x-if="foodCost > 0">
                                <div class="flex justify-between gap-2">
                                    <span class="text-neutral-600 dark:text-neutral-400">
                                        {{ __('messages.cb_food_cost_label', ['budget' => '', 'visits' => '']) }}<span x-text="foodBudget.toFixed(2)"></span>
                                    </span>
                                    <span class="font-bold text-neutral-800 dark:text-white shrink-0">
                                        +₱<span x-text="foodCost.toFixed(2)"></span>
                                    </span>
                                </div>
                            </template>

                            <div class="border-t border-gray-200 dark:border-neutral-700 my-2"></div>

                            <div class="flex justify-between gap-2 border-t-2 border-primary pt-3">
                                <span class="text-base font-black text-[#1B3B36] dark:text-white">{{ __('messages.cb_total_pay') }}</span>
                                <span class="text-base font-black text-primary shrink-0">
                                    ₱<span x-text="totalAmount.toFixed(2)"></span>
                                </span>
                            </div>

                            <div class="mt-3 text-xs text-neutral-500 dark:text-neutral-400">
                                <span x-text="days"></span> {{ __('messages.cb_days') }} ×
                                <span x-text="visitsPerDay"></span> visit(s)/day =
                                <strong x-text="totalVisits"></strong> {{ __('messages.cb_days') }}
                            </div>

                            <div class="mt-4 p-3 rounded-xl bg-blue-50 dark:bg-blue-950/20 border border-blue-200 dark:border-blue-800">
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="text-xs text-blue-700 dark:text-blue-300 leading-relaxed">
                                        <strong>{{ __('messages.cb_payment_notice_title') }}</strong> {{ __('messages.cb_payment_notice_desc') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- FORM ACTIONS --}}
                    <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-3 pt-4 border-t border-gray-100 dark:border-neutral-800">
                        <a href="{{ route('owner.sitter.profile', ['id' => $sitter->id]) }}"
                           class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 sm:px-8 sm:py-3.5 border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-sm rounded-xl hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                            {{ __('messages.cb_cancel') }}
                        </a>

                        <button type="submit"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 sm:px-8 sm:py-3.5 bg-primary hover:bg-primary-600 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ __('messages.cb_confirm') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function bookingForm(config) {
        return {
            baseRate: config.baseRate || 350,
            startDate: config.initialStartDate,
            endDate: config.initialEndDate,
            visitsPerDay: config.initialVisitsPerDay || 2,
            visitPreset: (config.initialVisitsPerDay >= 1 && config.initialVisitsPerDay <= 4)
                ? String(config.initialVisitsPerDay)
                : 'custom',
            visitTimes: [],
            foodPreference: config.initialFoodPreference || 'owner_provides',
            foodBudget: config.initialFoodBudget || 0,
            tasks: [],
            newTask: '',

            init() {
                this.syncVisitTimes(this.visitsPerDay);
                this.$watch('visitsPerDay', (val) => {
                    this.syncVisitTimes(val);
                });
            },

            get days() {
                if (!this.startDate || !this.endDate) return 0;
                const start = new Date(this.startDate);
                const end = new Date(this.endDate);
                const diffMs = end - start;
                const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24)) + 1;
                return diffDays > 0 ? diffDays : 0;
            },

            get totalVisits() { return this.days * (this.visitsPerDay || 0); },
            get subtotal() { return this.baseRate * this.totalVisits; },

            get foodCost() {
                if (this.foodPreference !== 'sitter_provides') return 0;
                return this.foodBudget * this.totalVisits;
            },

            get totalAmount() { return this.subtotal + this.foodCost; },

            syncVisitTimes(count) {
                const defaults = ['08:00', '18:00', '12:00', '21:00', '07:00', '20:00'];
                const current = Array.isArray(this.visitTimes) ? this.visitTimes : [];
                const newTimes = [];
                for (let i = 0; i < count; i++) {
                    newTimes.push(current[i] || defaults[i] || '12:00');
                }
                this.visitTimes = newTimes;
            },

            onPresetChange() {
                if (this.visitPreset !== 'custom') {
                    this.visitsPerDay = parseInt(this.visitPreset) || 1;
                } else {
                    this.visitsPerDay = this.visitsPerDay || 1;
                }
            },

            onFoodChange() {
                if (this.foodPreference !== 'sitter_provides') {
                    this.foodBudget = 0;
                }
            },

            getTimeLabel(time) {
                if (!time) return '—';
                const [h, m] = time.split(':');
                const hour = parseInt(h);
                const ampm = hour >= 12 ? 'PM' : 'AM';
                const h12 = hour % 12 || 12;
                return `${h12}:${m} ${ampm}`;
            },

            getTimePeriod(time) {
                if (!time) return '';
                const hour = parseInt(time.split(':')[0]);
                if (hour >= 5 && hour < 11)  return '{{ __('messages.cb_morning') }}';
                if (hour >= 11 && hour < 14) return '{{ __('messages.cb_midday') }}';
                if (hour >= 14 && hour < 18) return '{{ __('messages.cb_afternoon') }}';
                if (hour >= 18 && hour < 22) return '{{ __('messages.cb_evening') }}';
                return '{{ __('messages.cb_night') }}';
            },

            addTask() {
                const task = this.newTask.trim();
                if (task && !this.tasks.includes(task)) {
                    this.tasks.push(task);
                    this.newTask = '';
                }
            },

            removeTask(index) {
                this.tasks.splice(index, 1);
            },
        }
    }
    </script>
    @endpush
</x-app-layout>