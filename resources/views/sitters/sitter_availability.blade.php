<x-app-layout>
    <!-- ========================================== -->
    <!-- HEADER SLOT                               -->
    <!-- ========================================== -->
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('sitter.dashboard') }}" 
               class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                    {{ __('messages.sav_title') }}
                </h1>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.sav_subtitle') }}</p>
            </div>
        </div>
    </x-slot>

    <!-- ========================================== -->
    <!-- MAIN CONTENT                              -->
    <!-- ========================================== -->
    <div class="py-4 sm:py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200">
        <div class="w-full sm:max-w-6xl mx-auto px-2 sm:px-16 lg:px-24">

            @php
                $allSlots = $availabilities ?? collect();
                $totalSlots = $allSlots->count();
                $availableSlots = $allSlots->where('is_available', true)->count();
                $unavailableSlots = $allSlots->where('is_available', false)->count();
                $todaySlots = $allSlots->filter(fn($s) => \Carbon\Carbon::parse($s->date)->isToday());
                $todayAvailable = $todaySlots->where('is_available', true)->count();
                $todayUnavailable = $todaySlots->where('is_available', false)->count();
            @endphp

            <!-- MESSAGE ALERTS -->
            @if(session('status'))
                <div class="mb-4 sm:mb-6 p-3 sm:p-4 rounded-xl border border-green-500 bg-green-50 dark:bg-green-950/20 dark:border-green-700">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl shrink-0">✅</span>
                        <p class="font-bold text-green-700 dark:text-green-400">{{ session('status') }}</p>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 sm:mb-6 p-3 sm:p-4 rounded-xl border border-red-500 bg-red-50 dark:bg-red-950/20 dark:border-red-700">
                    <div class="flex items-start gap-3">
                        <span class="text-2xl shrink-0">❌</span>
                        <div>
                            @foreach($errors->all() as $error)
                                <p class="font-bold text-red-700 dark:text-red-400">{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- ========================================== -->
            <!-- ① SUMMARY CARDS                           -->
            <!-- ========================================== -->
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-6">
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sav_total_slots') }}</p>
                    <h3 class="font-bold text-2xl mt-1 text-[#1B3B36] dark:text-white">{{ $totalSlots }}</h3>
                    <p class="text-xs text-neutral-400 mt-1">{{ __('messages.sav_all_registered') }}</p>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sav_available') }}</p>
                    <h3 class="font-bold text-2xl mt-1 text-green-600 dark:text-green-400">{{ $availableSlots }}</h3>
                    <p class="text-xs text-neutral-400 mt-1">{{ __('messages.sav_ready_booking') }}</p>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sav_booked_slots') }}</p>
                    <h3 class="font-bold text-2xl mt-1 text-amber-600 dark:text-amber-400">0</h3>
                    <p class="text-xs text-amber-600 dark:text-amber-400 mt-1">{{ __('messages.sav_confirmed_bookings') }}</p>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sav_unavailable') }}</p>
                    <h3 class="font-bold text-2xl mt-1 text-red-600 dark:text-red-400">{{ $unavailableSlots }}</h3>
                    <p class="text-xs text-neutral-400 mt-1">{{ __('messages.sav_blocked_time') }}</p>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- ② TODAY'S SCHEDULE                        -->
            <!-- ========================================== -->
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6 mb-4 sm:mb-6">
                <h2 class="font-bold text-base sm:text-lg text-[#1B3B36] dark:text-white flex items-center gap-2 mb-4 flex-wrap">
                    <svg class="w-5 h-5 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ __('messages.sav_todays_schedule') }}
                    <span class="text-xs text-neutral-400 font-medium">{{ date('F d, Y') }}</span>
                </h2>

                <div class="space-y-2">
                    @forelse($todaySlots as $slot)
                        <div class="flex items-center justify-between gap-3 p-3 rounded-xl 
                            {{ $slot->is_available 
                                ? 'bg-primary/5 border border-primary/20' 
                                : 'bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-800/50' }}">
                            <div class="flex flex-wrap items-center gap-2 sm:gap-4 min-w-0">
                                <span class="text-sm font-bold text-neutral-500 dark:text-neutral-400">
                                    {{ \Carbon\Carbon::parse($slot->start_time)->format('g:i A') }}
                                </span>
                                <span class="text-xs sm:text-sm font-bold {{ $slot->is_available ? 'text-primary' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $slot->is_available ? __('messages.sav_available') : __('messages.sav_unavailable') }}
                                </span>
                                <span class="text-xs text-neutral-400 truncate">
                                    {{ \Carbon\Carbon::parse($slot->start_time)->format('g:i A') }} – {{ \Carbon\Carbon::parse($slot->end_time)->format('g:i A') }}
                                </span>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold shrink-0
                                {{ $slot->is_available 
                                    ? 'bg-primary/10 text-primary' 
                                    : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">
                                {{ $slot->is_available ? __('messages.sav_available') : __('messages.sav_unavailable') }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-8 text-neutral-400">
                            <p class="text-sm">{{ __('messages.sav_no_slots_today') }}</p>
                        </div>
                    @endforelse
                </div>

                <!-- Slot Utilization -->
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800 grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sav_slots_today') }}</p>
                        <p class="text-xl font-bold text-[#1B3B36] dark:text-white">{{ $todaySlots->count() }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sav_booked') }}</p>
                        <p class="text-xl font-bold text-amber-600 dark:text-amber-400">0</p>
                    </div>
                    <div>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sav_available') }}</p>
                        <p class="text-xl font-bold text-green-600 dark:text-green-400">{{ $todayAvailable }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sav_unavailable') }}</p>
                        <p class="text-xl font-bold text-red-600 dark:text-red-400">{{ $todayUnavailable }}</p>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- ③ ADD AVAILABILITY                        -->
            <!-- ========================================== -->
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6 mb-4 sm:mb-6">
                <h2 class="font-bold text-base sm:text-lg text-[#1B3B36] dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ __('messages.sav_add_availability') }}
                </h2>

                <form method="POST" action="{{ route('sitter.availability.store') }}" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                        <!-- Date -->
                        <div class="col-span-2 sm:col-span-1">
                            <label for="date" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('messages.sav_date') }}
                            </label>
                            <input type="date" id="date" name="date" 
                                   value="{{ old('date', date('Y-m-d')) }}"
                                   min="{{ date('Y-m-d') }}"
                                   class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 px-3 py-2.5 sm:px-4 sm:py-2.5 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary text-sm font-medium">
                        </div>

                        <!-- Start Time -->
                        <div>
                            <label for="start_time" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('messages.sav_start') }}
                            </label>
                            <input type="time" id="start_time" name="start_time" 
                                   value="{{ old('start_time', '08:00') }}"
                                   class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 px-3 py-2.5 sm:px-4 sm:py-2.5 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary text-sm font-medium">
                        </div>

                        <!-- End Time -->
                        <div>
                            <label for="end_time" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('messages.sav_end') }}
                            </label>
                            <input type="time" id="end_time" name="end_time" 
                                   value="{{ old('end_time', '17:00') }}"
                                   class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 px-3 py-2.5 sm:px-4 sm:py-2.5 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary text-sm font-medium">
                        </div>

                        <!-- Status -->
                        <div class="col-span-2 sm:col-span-2 lg:col-span-1">
                            <label for="status" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('messages.sav_status') }}
                            </label>
                            <select id="status" name="status"
                                    class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 px-3 py-2.5 sm:px-4 sm:py-2.5 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary text-sm font-medium">
                                <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>{{ __('messages.sav_available') }}</option>
                                <option value="unavailable" {{ old('status') == 'unavailable' ? 'selected' : '' }}>{{ __('messages.sav_unavailable') }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Save Button -->
                    <div class="pt-2">
                        <button type="submit" 
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-primary hover:bg-primary-600 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ __('messages.sav_save_availability') }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- ========================================== -->
            <!-- ④ AVAILABILITY TIMELINE                   -->
            <!-- ========================================== -->
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6 mb-4 sm:mb-6">
                <h2 class="font-bold text-base sm:text-lg text-[#1B3B36] dark:text-white flex items-center gap-2 mb-4 flex-wrap">
                    <svg class="w-5 h-5 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    {{ __('messages.sav_timeline') }}
                    <span class="text-xs text-neutral-400 font-medium">{{ date('F d, Y') }}</span>
                </h2>

                <div class="space-y-2">
                    @forelse($allSlots->take(10) as $slot)
                        <div class="flex items-center gap-3 sm:gap-4 p-3 rounded-xl 
                            {{ $slot->is_available 
                                ? 'bg-green-50 dark:bg-green-950/20 border border-green-200 dark:border-green-800/50' 
                                : 'bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-800/50' }}">
                            <span class="text-sm font-bold text-neutral-500 dark:text-neutral-400 shrink-0">
                                {{ \Carbon\Carbon::parse($slot->start_time)->format('g:i A') }}
                            </span>
                            <span class="text-xs sm:text-sm font-bold {{ $slot->is_available ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400' }} shrink-0">
                                {{ $slot->is_available ? '🟢 ' . __('messages.sav_available') : '🔴 ' . __('messages.sav_unavailable') }}
                            </span>
                            <span class="text-xs text-neutral-500 dark:text-neutral-400 truncate">
                                {{ \Carbon\Carbon::parse($slot->date)->format('M d, Y') }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-8 text-neutral-400">
                            <p class="text-sm">{{ __('messages.sav_no_availability_yet') }}</p>
                        </div>
                    @endforelse
                </div>

                <!-- Legend -->
                <div class="flex flex-wrap items-center gap-4 mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                    <div class="flex items-center gap-1.5">
                        <span class="text-sm">🟢</span>
                        <span class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sav_available') }}</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-sm">🟡</span>
                        <span class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sav_booked') }}</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-sm">🔴</span>
                        <span class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sav_unavailable') }}</span>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- ⑤ UPCOMING BOOKINGS                       -->
            <!-- ========================================== -->
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6 mb-4 sm:mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-bold text-base sm:text-lg text-[#1B3B36] dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ __('messages.sav_upcoming_bookings') }}
                    </h2>
                    <a href="{{ route('sitter.tasks.index') }}" class="text-xs text-primary font-semibold hover:underline shrink-0">{{ __('messages.sav_view_all') }}</a>
                </div>

                <div class="text-center py-6 text-neutral-400">
                    <p class="text-sm">{{ __('messages.sav_no_upcoming') }}</p>
                    <p class="text-xs mt-1">{{ __('messages.sav_no_upcoming_desc') }}</p>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- ⑥ AVAILABILITY SCHEDULE                   -->
            <!-- ========================================== -->
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6 mb-4 sm:mb-6"
                x-data="{ 
                    showModal: false, 
                    slotId: null, 
                    slotDate: '', 
                    slotTime: '' 
                }"
                @open-delete-modal.window="
                    showModal = true; 
                    slotId = $event.detail.id; 
                    slotDate = $event.detail.date; 
                    slotTime = $event.detail.time;
                ">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-bold text-base sm:text-lg text-[#1B3B36] dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        {{ __('messages.sav_schedule') }}
                    </h2>
                    <span class="text-xs text-neutral-400 shrink-0">{{ $totalSlots }} {{ __('messages.sav_slots') }}</span>
                </div>

                <div class="space-y-3">
                    @forelse($allSlots as $slot)
                        <div class="flex items-center justify-between gap-3 p-3 sm:p-4 rounded-xl border border-gray-100 dark:border-neutral-800 hover:shadow-md transition">
                            <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center shrink-0
                                    {{ $slot->is_available 
                                        ? 'bg-primary/10 text-primary' 
                                        : 'bg-red-100/20 text-red-600' }}">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-bold text-sm text-[#1B3B36] dark:text-white truncate">
                                        {{ \Carbon\Carbon::parse($slot->date)->format('F d, Y') }}
                                    </p>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400 truncate">
                                        {{ \Carbon\Carbon::parse($slot->start_time)->format('g:i A') }} — {{ \Carbon\Carbon::parse($slot->end_time)->format('g:i A') }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                                <span class="inline-flex items-center px-2 py-1 sm:px-2.5 sm:py-1 rounded-full text-[10px] font-bold 
                                    {{ $slot->is_available 
                                        ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' 
                                        : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">
                                    {{ $slot->is_available ? __('messages.sav_available') : __('messages.sav_unavailable') }}
                                </span>

                                <button type="button"
                                        @click="$dispatch('open-delete-modal', {
                                            id: {{ $slot->id }},
                                            date: '{{ \Carbon\Carbon::parse($slot->date)->format('F d, Y') }}',
                                            time: '{{ \Carbon\Carbon::parse($slot->start_time)->format('g:i A') }} — {{ \Carbon\Carbon::parse($slot->end_time)->format('g:i A') }}'
                                        })"
                                        class="p-1.5 rounded-lg text-neutral-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-neutral-400">
                            <p class="text-sm">{{ __('messages.sav_no_availability_set') }}</p>
                            <p class="text-xs mt-1">{{ __('messages.sav_no_availability_set_desc') }}</p>
                        </div>
                    @endforelse
                </div>

                <!-- ========================================== -->
                <!-- DELETE CONFIRMATION MODAL                  -->
                <!-- ========================================== -->
                <div x-show="showModal" 
                    x-cloak
                    class="fixed inset-0 z-50 flex items-center justify-center p-4">

                    <!-- Backdrop -->
                    <div class="fixed inset-0 bg-black/50"
                        @click="showModal = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"></div>

                    <!-- Modal Content -->
                    <div class="relative bg-white dark:bg-neutral-900 rounded-2xl shadow-xl max-w-md w-full p-5 sm:p-6"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95">

                        <!-- Icon -->
                        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 mx-auto mb-4">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </div>

                        <!-- Title -->
                        <h3 class="text-lg font-black text-center text-[#1B3B36] dark:text-white">
                            {{ __('messages.sav_delete_title') }}
                        </h3>

                        <!-- Description -->
                        <p class="text-sm text-center text-neutral-500 dark:text-neutral-400 mt-2">
                            {{ __('messages.sav_delete_desc') }}
                        </p>

                        <!-- Slot Details -->
                        <div class="mt-4 p-3 rounded-xl bg-neutral-50 dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700">
                            <p class="text-sm font-bold text-[#1B3B36] dark:text-white text-center" x-text="slotDate"></p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 text-center mt-1" x-text="slotTime"></p>
                        </div>

                        <!-- Actions -->
                        <div class="mt-6 flex flex-col-reverse sm:flex-row gap-2 sm:gap-3">
                            <button type="button"
                                    @click="showModal = false"
                                    class="w-full sm:flex-1 inline-flex items-center justify-center px-4 py-2.5 border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-sm rounded-xl hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                                {{ __('messages.sav_cancel') }}
                            </button>

                            <form :action="'/sitter/availability/' + slotId" method="POST" class="w-full sm:flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold text-sm rounded-xl transition">
                                    {{ __('messages.sav_delete') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- ⑦ CALENDAR VIEW                           -->
            <!-- ========================================== -->
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6"
                x-data="calendarView({
                    availabilities: {{ $availabilities->map(fn($a) => [
                        'date' => \Carbon\Carbon::parse($a->date)->format('Y-m-d'),
                        'is_available' => (bool) $a->is_available,
                        'start_time' => $a->start_time,
                        'end_time' => $a->end_time,
                    ])->toJson() }},
                    labels: {
                        available: @js(__('messages.sav_available')),
                        unavailable: @js(__('messages.sav_unavailable')),
                    }
                })"
                x-init="init()">

                <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                    <h2 class="font-bold text-base sm:text-lg text-[#1B3B36] dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ __('messages.sav_calendar_reference') }}
                    </h2>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="prevMonth()"
                                class="p-1.5 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                            <svg class="w-4 h-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <span class="text-sm font-bold text-[#1B3B36] dark:text-white min-w-[110px] sm:min-w-[120px] text-center"
                            x-text="monthName"></span>
                        <button type="button" @click="nextMonth()"
                                class="p-1.5 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                            <svg class="w-4 h-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Calendar Grid -->
                <div class="grid grid-cols-7 gap-1 text-center">
                    <div class="text-[10px] sm:text-xs font-bold text-neutral-400 py-2">{{ __('messages.sav_sun') }}</div>
                    <div class="text-[10px] sm:text-xs font-bold text-neutral-400 py-2">{{ __('messages.sav_mon') }}</div>
                    <div class="text-[10px] sm:text-xs font-bold text-neutral-400 py-2">{{ __('messages.sav_tue') }}</div>
                    <div class="text-[10px] sm:text-xs font-bold text-neutral-400 py-2">{{ __('messages.sav_wed') }}</div>
                    <div class="text-[10px] sm:text-xs font-bold text-neutral-400 py-2">{{ __('messages.sav_thu') }}</div>
                    <div class="text-[10px] sm:text-xs font-bold text-neutral-400 py-2">{{ __('messages.sav_fri') }}</div>
                    <div class="text-[10px] sm:text-xs font-bold text-neutral-400 py-2">{{ __('messages.sav_sat') }}</div>

                    <template x-for="(day, idx) in days" :key="idx">
                        <div class="py-1.5 sm:py-2 rounded-lg relative transition cursor-default"
                            :class="dayClass(day)"
                            :title="day.tooltip">
                            <span class="text-xs sm:text-sm font-medium" x-text="day.day"></span>
                        </div>
                    </template>
                </div>

                <!-- Legend -->
                <div class="flex flex-wrap items-center gap-3 sm:gap-4 mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                    <div class="flex items-center gap-1.5">
                        <div class="w-3 h-3 rounded-full bg-primary"></div>
                        <span class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sav_available') }}</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                        <span class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sav_booked') }}</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <div class="w-3 h-3 rounded-full bg-red-500"></div>
                        <span class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sav_unavailable') }}</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <div class="w-3 h-3 rounded-full border-2 border-dashed border-gray-300 dark:border-neutral-600"></div>
                        <span class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sav_today') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function calendarView(config) {
        return {
            availabilities: config.availabilities || [],
            labels: config.labels || { available: 'Available', unavailable: 'Unavailable' },
            currentDate: new Date(),
            monthName: '',
            days: [],

            init() {
                this.render();
            },

            render() {
                const year = this.currentDate.getFullYear();
                const month = this.currentDate.getMonth();

                this.monthName = new Date(year, month).toLocaleString('en-US', {
                    month: 'long', year: 'numeric'
                });

                const firstDay = new Date(year, month, 1).getDay();
                const daysInMonth = new Date(year, month + 1, 0).getDate();
                const prevMonthDays = new Date(year, month, 0).getDate();
                const days = [];

                for (let i = firstDay - 1; i >= 0; i--) {
                    days.push({
                        day: prevMonthDays - i,
                        date: null,
                        isCurrentMonth: false,
                        status: null,
                        tooltip: ''
                    });
                }

                for (let i = 1; i <= daysInMonth; i++) {
                    const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
                    const slot = this.availabilities.find(a => a.date === dateStr);

                    days.push({
                        day: i,
                        date: dateStr,
                        isCurrentMonth: true,
                        isToday: this.isToday(year, month, i),
                        status: slot ? (slot.is_available ? 'available' : 'unavailable') : null,
                        tooltip: slot
                            ? (slot.is_available ? `${this.labels.available} ${slot.start_time}–${slot.end_time}` : this.labels.unavailable)
                            : ''
                    });
                }

                const remainingCells = 42 - days.length;
                for (let i = 1; i <= remainingCells; i++) {
                    days.push({
                        day: i,
                        date: null,
                        isCurrentMonth: false,
                        status: null,
                        tooltip: ''
                    });
                }

                this.days = days;
            },

            isToday(year, month, day) {
                const today = new Date();
                return today.getFullYear() === year &&
                    today.getMonth() === month &&
                    today.getDate() === day;
            },

            prevMonth() {
                this.currentDate = new Date(
                    this.currentDate.getFullYear(),
                    this.currentDate.getMonth() - 1,
                    1
                );
                this.render();
            },

            nextMonth() {
                this.currentDate = new Date(
                    this.currentDate.getFullYear(),
                    this.currentDate.getMonth() + 1,
                    1
                );
                this.render();
            },

            dayClass(day) {
                if (!day.isCurrentMonth) {
                    return 'text-neutral-300 dark:text-neutral-700';
                }
                if (day.status === 'available') {
                    return 'bg-primary/20 text-primary font-bold';
                }
                if (day.status === 'unavailable') {
                    return 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 font-bold';
                }
                if (day.isToday) {
                    return 'border-2 border-dashed border-primary text-[#1B3B36] dark:text-white font-bold';
                }
                return 'text-neutral-500 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800';
            }
        };
    }
    </script>
    @endpush
</x-app-layout>