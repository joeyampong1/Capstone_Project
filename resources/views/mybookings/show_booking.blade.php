<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('mybookings.index') }}"
               class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                    {{ __('messages.sb_title') }}
                </h1>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                    {{ $booking->booking_reference }}
                </p>
            </div>
        </div>
    </x-slot>

    @php
        $user       = auth()->user();
        $isOwner    = $user->id === $booking->owner_id;
        $isSitter   = $user->id === $booking->sitter_id;
        $counterpart = $isOwner ? $booking->sitter : $booking->owner;

        $cpName     = trim(($counterpart->f_name ?? '') . ' ' . ($counterpart->l_name ?? '')) ?: __('messages.bc_user_fallback');
        $cpInitial  = strtoupper(substr($counterpart->f_name ?? 'U', 0, 1));

        $pet        = $booking->pet;
        $totalVisits     = $booking->visits->count();
        $completedVisits = $booking->visits->where('status', 'completed')->count();
        $progressPct     = $totalVisits > 0 ? round(($completedVisits / $totalVisits) * 100) : 0;

        $bookingStatusMap = [
            'pending'   => ['bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400', __('messages.status_pending')],
            'accepted'  => ['bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400', __('messages.status_confirmed')],
            'rejected'  => ['bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',         __('messages.status_rejected')],
            'cancelled' => ['bg-neutral-200 text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300', __('messages.status_cancelled')],
            'completed' => ['bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',     __('messages.status_completed')],
        ];
        [$bookingStatusClass, $bookingStatusLabel] = $bookingStatusMap[$booking->status] ?? $bookingStatusMap['pending'];

        $canCancel = $isOwner
            && in_array($booking->status, ['pending', 'accepted'])
            && now()->addDay()->lt($booking->start_date);
    @endphp

    <div class="py-4 sm:py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200"
         x-data="{ cancelOpen: false }">
        <div class="w-full sm:max-w-4xl mx-auto px-2 sm:px-16 lg:px-24">

            {{-- ========================================== --}}
            {{-- SUMMARY CARDS                              --}}
            {{-- ========================================== --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-6">

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sb_booking_ref') }}</p>
                    <h3 class="font-bold text-base sm:text-lg mt-1 text-[#1B3B36] dark:text-white truncate">
                        {{ $booking->booking_reference }}
                    </h3>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sb_pet') }}</p>
                    <h3 class="font-bold text-base sm:text-lg mt-1 text-[#1B3B36] dark:text-white truncate">
                        🐾 {{ $pet->name ?? 'Pet' }}
                    </h3>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">
                        {{ $isOwner ? __('messages.sb_sitter') : __('messages.sb_owner') }}
                    </p>
                    <h3 class="font-bold text-base sm:text-lg mt-1 text-[#1B3B36] dark:text-white truncate">
                        {{ $cpName }}
                    </h3>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sb_status') }}</p>
                    <span class="inline-block mt-1 px-3 py-1 rounded-full text-xs sm:text-sm font-semibold {{ $bookingStatusClass }}">
                        {{ $bookingStatusLabel }}
                    </span>
                </div>
            </div>

            {{-- ========================================== --}}
            {{-- COUNTERPART INFO                           --}}
            {{-- ========================================== --}}
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-5 mb-4 sm:mb-6">
                <div class="flex items-center gap-4">
                    @if($counterpart->profile_photo && file_exists(public_path('storage/' . $counterpart->profile_photo)))
                        <img src="{{ asset('storage/' . $counterpart->profile_photo) }}"
                             alt="{{ $cpName }}"
                             class="w-14 h-14 rounded-full object-cover border-2 border-primary/20 shrink-0">
                    @else
                        <div class="w-14 h-14 rounded-full bg-primary/10 flex items-center justify-center text-primary font-black text-xl shrink-0">
                            {{ $cpInitial }}
                        </div>
                    @endif

                    <div class="min-w-0 flex-1">
                        <p class="font-black text-[#1B3B36] dark:text-white text-base truncate">{{ $cpName }}</p>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">
                            {{ $isOwner ? __('messages.sb_assigned_sitter') : __('messages.sb_pet_owner') }}
                        </p>
                        @if($counterpart->location)
                            <p class="text-xs text-neutral-500 mt-0.5">📍 {{ $counterpart->location }}</p>
                        @endif
                    </div>

                    <a href="{{ route('owner.messages', $counterpart->id) }}"
                       class="shrink-0 px-4 py-2 rounded-xl bg-primary hover:bg-primary-600 text-white font-bold text-xs transition">
                        {{ __('messages.sb_contact') }}
                    </a>
                </div>
            </div>

            {{-- ========================================== --}}
            {{-- SCHEDULE SUMMARY                           --}}
            {{-- ========================================== --}}
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6 mb-4 sm:mb-6">
                <h2 class="font-bold text-base sm:text-lg text-[#1B3B36] dark:text-white mb-4">{{ __('messages.sb_schedule_title') }}</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sb_date_range') }}</p>
                        <p class="font-bold text-[#1B3B36] dark:text-white">
                            {{ $booking->start_date->format('M d, Y') }} —
                            {{ $booking->end_date->format('M d, Y') }}
                        </p>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">
                            {{ __('messages.sb_days_visits_per_day', ['days' => $booking->days_count, 'per_day' => $booking->visit_per_day]) }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sb_total_visits_label') }}</p>
                        <p class="font-bold text-[#1B3B36] dark:text-white">
                            {{ __('messages.sb_visits_count', ['count' => $booking->total_visits]) }}
                        </p>
                    </div>
                </div>

                @if($booking->schedule_times)
                    <div>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">{{ __('messages.sb_time_schedule_label') }}</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($booking->schedule_times as $time)
                                @php
                                    $hour = (int) explode(':', $time)[0];
                                    $label = \Carbon\Carbon::parse($time)->format('g:i A');
                                    $period = $hour >= 5 && $hour < 11 ? __('messages.cb_morning')
                                        : ($hour >= 11 && $hour < 14 ? __('messages.cb_midday')
                                        : ($hour >= 14 && $hour < 18 ? __('messages.cb_afternoon')
                                        : ($hour >= 18 && $hour < 22 ? __('messages.cb_evening') : __('messages.cb_night'))));
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-primary/10 text-primary font-bold text-xs">
                                    {{ $label }}
                                    <span class="text-primary/60 font-normal">{{ $period }}</span>
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- ========================================== --}}
            {{-- PROGRESS                                   --}}
            {{-- ========================================== --}}
            @if($totalVisits > 0)
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-5 mb-4 sm:mb-6">
                    <div class="flex justify-between items-center mb-2">
                        <h2 class="font-bold text-base text-[#1B3B36] dark:text-white">{{ __('messages.sb_visit_progress') }}</h2>
                        <span class="font-bold text-sm text-[#1B3B36] dark:text-white shrink-0">
                            {{ __('messages.sb_progress_count', ['completed' => $completedVisits, 'total' => $totalVisits]) }}
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-neutral-700 rounded-full h-3">
                        <div class="bg-primary h-3 rounded-full transition-all"
                             style="width: {{ $progressPct }}%"></div>
                    </div>
                </div>
            @endif

            {{-- ========================================== --}}
            {{-- PAYMENT DETAILS                            --}}
            {{-- ========================================== --}}
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6 mb-4 sm:mb-6">
                <h2 class="font-bold text-base sm:text-lg text-[#1B3B36] dark:text-white mb-4">{{ __('messages.sb_payment_details') }}</h2>

                <div class="space-y-2 text-sm">
                    <div class="flex justify-between gap-2">
                        <span class="text-neutral-600 dark:text-neutral-400">
                            {{ __('messages.sb_sitter_rate_label', ['rate' => number_format($booking->base_rate, 2), 'visits' => $booking->total_visits]) }}
                        </span>
                        <span class="font-bold text-neutral-800 dark:text-white">
                            ₱{{ number_format($booking->subtotal, 2) }}
                        </span>
                    </div>

                    @if($booking->food_preference === 'sitter_provides' && $booking->food_budget > 0)
                        <div class="flex justify-between gap-2">
                            <span class="text-neutral-600 dark:text-neutral-400">
                                {{ __('messages.sb_food_cost_label', ['budget' => number_format($booking->food_budget, 2), 'visits' => $booking->total_visits]) }}
                            </span>
                            <span class="font-bold text-neutral-800 dark:text-white">
                                +₱{{ number_format($booking->food_cost, 2) }}
                            </span>
                        </div>
                    @endif

                    <div class="border-t border-gray-200 dark:border-neutral-700 my-2"></div>

                    <div class="flex justify-between gap-2">
                        <span class="text-base font-black text-[#1B3B36] dark:text-white">{{ __('messages.sb_total') }}</span>
                        <span class="text-base font-black text-primary">
                            ₱{{ number_format($booking->total_amount, 2) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- ========================================== --}}
            {{-- TASKS + INSTRUCTIONS                       --}}
            {{-- ========================================== --}}
            @if(!empty($booking->tasks) || $booking->instructions)
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6 mb-4 sm:mb-6">
                    <h2 class="font-bold text-base sm:text-lg text-[#1B3B36] dark:text-white mb-4">{{ __('messages.sb_tasks_instructions') }}</h2>

                    @if(!empty($booking->tasks))
                        <div class="mb-4">
                            <p class="text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-2">{{ __('messages.sb_tasks_every_visit') }}</p>
                            <ul class="space-y-1.5">
                                @foreach($booking->tasks as $task)
                                    <li class="flex items-start gap-2 text-sm text-neutral-700 dark:text-neutral-300">
                                        <span class="text-primary shrink-0 mt-0.5">•</span>
                                        <span>{{ $task }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if($booking->instructions)
                        <div>
                            <p class="text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-2">{{ __('messages.sb_special_instructions') }}</p>
                            <p class="text-sm text-neutral-700 dark:text-neutral-300 leading-relaxed p-3 rounded-xl bg-neutral-50 dark:bg-neutral-800/50 border border-gray-100 dark:border-neutral-800">
                                {{ $booking->instructions }}
                            </p>
                        </div>
                    @endif
                </div>
            @endif

            {{-- ========================================== --}}
            {{-- VISITS TIMELINE                            --}}
            {{-- ========================================== --}}
            @if($totalVisits > 0)
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6 mb-4 sm:mb-6">
                    <h2 class="font-bold text-base sm:text-lg text-[#1B3B36] dark:text-white mb-5">
                        {{ __('messages.sb_visits_title', ['count' => $totalVisits]) }}
                    </h2>

                    <div class="space-y-3">
                        @foreach($booking->visits as $visit)
                            @php
                                $visitStatusMap = [
                                    'pending'     => ['bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400', __('messages.sb_visit_status_pending')],
                                    'in_progress' => ['bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400', __('messages.sb_visit_status_in_progress')],
                                    'completed'   => ['bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400', __('messages.sb_visit_status_completed')],
                                    'missed'      => ['bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400', __('messages.sb_visit_status_missed')],
                                ];
                                [$vStatusClass, $vStatusLabel] = $visitStatusMap[$visit->status] ?? $visitStatusMap['pending'];
                            @endphp

                            <div class="flex items-start gap-3 p-3 sm:p-4 rounded-xl bg-neutral-50 dark:bg-neutral-800/50 border border-gray-100 dark:border-neutral-800">
                                <div class="w-9 h-9 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-black text-sm shrink-0">
                                    {{ $visit->visit_number }}
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2 flex-wrap">
                                        <p class="font-bold text-sm text-[#1B3B36] dark:text-white">
                                            {{ $visit->scheduled_datetime->format('M d, Y • g:i A') }}
                                        </p>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $vStatusClass }}">
                                            {{ $vStatusLabel }}
                                        </span>
                                    </div>

                                    @if($visit->check_in)
                                        <p class="text-[11px] text-green-600 mt-1">
                                            ✅ {{ __('messages.sb_checked_in') }} {{ $visit->check_in->format('g:i A') }}
                                            @if($visit->check_out)
                                                • {{ __('messages.sb_checked_out') }} {{ $visit->check_out->format('g:i A') }}
                                                @if($visit->duration_minutes)
                                                    ({{ __('messages.sb_minutes', ['count' => $visit->duration_minutes]) }})
                                                @endif
                                            @endif
                                        </p>
                                    @endif

                                    @if($visit->lateness_minutes > 0)
                                        <p class="text-[11px] text-amber-600 mt-0.5">
                                            ⚠️ {{ __('messages.sb_late_by', ['min' => $visit->lateness_minutes]) }}
                                        </p>
                                    @endif

                                    @if($visit->notes)
                                        <p class="text-[11px] text-neutral-500 italic mt-1 line-clamp-2">
                                            "{{ $visit->notes }}"
                                        </p>
                                    @endif
                                </div>

                                @if($visit->photo_proof_path)
                                    <div class="shrink-0">
                                        <img src="{{ asset('storage/' . $visit->photo_proof_path) }}"
                                             alt="Visit photo"
                                             class="w-12 h-12 rounded-lg object-cover border border-gray-200 dark:border-neutral-700 cursor-pointer"
                                             onclick="window.open('{{ asset('storage/' . $visit->photo_proof_path) }}', '_blank')">
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ========================================== --}}
            {{-- CANCELLATION REASON                        --}}
            {{-- ========================================== --}}
            @if(in_array($booking->status, ['cancelled', 'rejected']) && $booking->cancellation_reason)
                <div class="bg-red-50 dark:bg-red-950/20 rounded-2xl border border-red-200 dark:border-red-800 p-4 sm:p-5 mb-4 sm:mb-6">
                    <p class="text-xs font-bold text-red-700 dark:text-red-400 uppercase tracking-wider mb-1">
                        {{ $booking->status === 'rejected' ? __('messages.sb_rejection_reason') : __('messages.sb_cancellation_reason') }}
                    </p>
                    <p class="text-sm text-red-700 dark:text-red-300 leading-relaxed">
                        {{ $booking->cancellation_reason }}
                    </p>
                    @if($booking->cancelled_by)
                        <p class="text-[11px] text-red-600 dark:text-red-400 mt-1">
                            {{ __('messages.sb_by') }} {{ ucfirst($booking->cancelled_by) }}
                        </p>
                    @endif
                </div>
            @endif

            {{-- ========================================== --}}
            {{-- ACTIONS                                    --}}
            {{-- ========================================== --}}
            <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-3 pt-4 border-t border-gray-100 dark:border-neutral-800">

                <a href="{{ route('mybookings.index') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-sm rounded-xl hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                    {{ __('messages.sb_back_to_bookings') }}
                </a>

                @if($canCancel)
                    <button type="button"
                            @click="cancelOpen = true"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 border-2 border-red-500 text-red-500 hover:bg-red-50 dark:hover:bg-red-950/20 font-bold text-sm rounded-xl transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        {{ __('messages.sb_cancel_booking') }}
                    </button>
                @endif
            </div>

            {{-- ========================================== --}}
            {{-- CANCEL MODAL                               --}}
            {{-- ========================================== --}}
            @if($canCancel)
                <div x-show="cancelOpen"
                     x-cloak
                     x-transition.opacity
                     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
                     @click.away="cancelOpen = false">

                    <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-neutral-800 max-w-md w-full p-5 sm:p-6"
                         @click.stop
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100">

                        <div class="flex items-start gap-3 mb-4">
                            <div class="w-11 h-11 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center text-red-600 dark:text-red-400 shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-base font-black text-[#1B3B36] dark:text-white">{{ __('messages.sb_cancel_modal_title') }}</h3>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">
                                    {{ __('messages.sb_cancel_modal_desc') }}
                                </p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('mybookings.cancel', $booking->id) }}">
                            @csrf

                            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1.5">
                                {{ __('messages.sb_cancel_reason') }} <span class="text-red-500">*</span>
                            </label>
                            <textarea name="reason"
                                      rows="4"
                                      required
                                      placeholder="{{ __('messages.sb_cancel_reason_ph') }}"
                                      class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-3 py-2.5 text-[#1B3B36] dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 text-sm resize-none"></textarea>

                            <p class="text-[10px] text-neutral-400 mt-1">
                                {{ $isOwner ? __('messages.sb_cancel_note_sitter') : __('messages.sb_cancel_note_owner') }}
                            </p>

                            <div class="flex gap-2 mt-4">
                                <button type="button"
                                        @click="cancelOpen = false"
                                        class="flex-1 px-4 py-2.5 rounded-xl border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-xs hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                                    {{ __('messages.sb_keep_booking') }}
                                </button>
                                <button type="submit"
                                        class="flex-1 px-4 py-2.5 rounded-xl bg-red-500 hover:bg-red-600 text-white font-bold text-xs transition shadow-md hover:shadow-lg">
                                    {{ __('messages.sb_yes_cancel') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>