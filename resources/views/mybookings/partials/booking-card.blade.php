@php
    $user = auth()->user();
    $isOwnerView  = $viewAs === 'owner';

    $counterpart  = $isOwnerView ? $booking->sitter : $booking->owner;
    $cpName       = trim(($counterpart->f_name ?? '') . ' ' . ($counterpart->l_name ?? '')) ?: __('messages.bc_user_fallback');
    $cpInitial    = strtoupper(substr($counterpart->f_name ?? 'U', 0, 1));
    $cpLocation   = $counterpart->location ?? __('messages.bc_location_not_set');

    $pet    = $booking->pet;
    $emoji  = match($pet?->petType?->name ?? '') {
        'Dog' => '🐶', 'Cat' => '🐱', 'Bird' => '🐦',
        'Rabbit' => '🐰', 'Hamster' => '🐹', 'Fish' => '🐟',
        'Reptile' => '🦎', default => '🐾',
    };

    $statusMap = [
        'pending'   => ['bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400', __('messages.status_pending'),   'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
        'accepted'  => ['bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400', __('messages.status_confirmed'), 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
        'completed' => ['bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',    __('messages.status_completed'), 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2z'],
        'rejected'  => ['bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',        __('messages.status_declined'),  'M6 18L18 6M6 6l12 12'],
        'cancelled' => ['bg-neutral-200 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-400', __('messages.status_cancelled'), 'M6 18L18 6M6 6l12 12'],
    ];
    [$statusClass, $statusLabel, $statusIcon] = $statusMap[$booking->status] ?? $statusMap['pending'];

    if (!$isOwnerView && $booking->status === 'accepted') {
        $statusLabel = __('messages.status_accepted');
    }

    $dateRange = \Carbon\Carbon::parse($booking->start_date)->format('M d')
        . ' - ' . \Carbon\Carbon::parse($booking->end_date)->format('M d, Y');

    $foodText = $booking->food_preference === 'sitter_provides'
        ? __('messages.bc_food_sitter_provides', ['amount' => number_format($booking->food_budget, 0)])
        : __('messages.bc_food_owner_provides');
@endphp

<div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 p-4 sm:p-5 hover:shadow-md transition"
     x-data="{ menuOpen: false, rejectOpen: false }">

    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
        <div class="flex items-start gap-3 sm:gap-4 min-w-0 flex-1">
            {{-- Avatar --}}
            @if($counterpart->profile_photo && file_exists(public_path('storage/' . $counterpart->profile_photo)))
                <img src="{{ asset('storage/' . $counterpart->profile_photo) }}"
                     alt="{{ $cpName }}"
                     class="w-10 h-10 sm:w-12 sm:h-12 rounded-full object-cover border border-neutral-200 dark:border-neutral-700 flex-shrink-0">
            @else
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold flex-shrink-0">
                    {{ $cpInitial }}
                </div>
            @endif

            {{-- Info --}}
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2 flex-wrap">
                    <h4 class="font-bold text-[#1B3B36] dark:text-white text-sm sm:text-base truncate">
                        {{ $cpName }}
                    </h4>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold {{ $statusClass }}">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $statusIcon }}"/>
                        </svg>
                        {{ $statusLabel }}
                    </span>
                </div>

                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">
                    {{ __('messages.bc_visit_per_day', ['count' => $booking->visit_per_day]) }} •
                    {{ $isOwnerView ? $cpLocation : __('messages.bc_ref_prefix') . ' ' . $booking->booking_reference }}
                </p>

                <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-1">
                    {{ $dateRange }} • {{ __('messages.bc_total_visits', ['count' => $booking->total_visits]) }}
                </p>

                <div class="flex flex-wrap items-center gap-1 sm:gap-2 mt-2">
                    <span class="inline-flex items-center gap-0.5 text-[10px] text-neutral-400">
                        {{ $emoji }} {{ $pet?->name ?? 'Pet' }}
                        ({{ $pet?->petType?->name ?? __('messages.cb_pet_unknown') }})
                    </span>
                    <span class="text-[10px] text-neutral-300">•</span>
                    <span class="inline-flex items-center gap-0.5 text-[10px] text-neutral-400">
                        {{ $foodText }}
                    </span>
                </div>

                @if($booking->instructions)
                    <p class="text-[11px] text-neutral-500 dark:text-neutral-400 mt-1 line-clamp-2">
                        <span class="font-semibold">{{ __('messages.bc_notes_label') }}</span> {{ \Illuminate\Support\Str::limit($booking->instructions, 80) }}
                    </p>
                @endif
            </div>
        </div>

        {{-- Price --}}
        <div class="flex flex-row sm:flex-col items-center sm:items-end gap-2 sm:gap-1 flex-wrap shrink-0">
            <span class="text-[10px] text-neutral-400">₱{{ number_format($booking->base_rate, 0) }}/visit</span>
            <span class="text-[10px] text-neutral-400 font-medium">
                Total: <span class="text-primary font-bold">₱{{ number_format($booking->total_amount, 0) }}</span>
            </span>
        </div>
    </div>

    {{-- ACTION ROW --}}
    <div class="flex flex-wrap items-center gap-2 sm:gap-3 mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">

        {{-- SITTER view: Accept / Decline --}}
        @if(!$isOwnerView && $booking->status === 'pending')
            <form method="POST" action="{{ route('bookings.accept', $booking->id) }}" class="inline">
                @csrf
                <button type="submit"
                        class="px-3 sm:px-4 py-1.5 text-[10px] sm:text-xs font-bold bg-green-500 hover:bg-green-600 text-white rounded-lg transition">
                    {{ __('messages.bc_accept') }}
                </button>
            </form>

            <button type="button" @click="rejectOpen = true"
                    class="px-3 sm:px-4 py-1.5 text-[10px] sm:text-xs font-bold border-2 border-red-500 text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-lg transition">
                {{ __('messages.bc_decline') }}
            </button>
        @endif

        {{-- SITTER view: Mark Complete --}}
        @if(!$isOwnerView && $booking->status === 'accepted')
            <form method="POST" action="{{ route('bookings.complete', $booking->id) }}" class="inline">
                @csrf
                <button type="submit"
                        class="px-3 sm:px-4 py-1.5 text-[10px] sm:text-xs font-bold bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition">
                    {{ __('messages.bc_mark_complete') }}
                </button>
            </form>
        @endif

        {{-- OWNER view: Cancel button --}}
        @if($isOwnerView && in_array($booking->status, ['pending', 'accepted']))
            <button type="button"
                    @click="$dispatch('open-cancel-modal', {
                        bookingId: {{ $booking->id }},
                        bookingRef: '{{ $booking->booking_reference }}'
                    })"
                    class="text-xs text-red-500 font-semibold hover:underline">
                {{ __('messages.bc_cancel') }}
            </button>
        @endif

        <a href="{{ route('mybookings.show', $booking->id) }}"
           class="text-xs text-primary font-semibold hover:underline">
            {{ __('messages.bc_view_details') }}
        </a>

        {{-- Contact --}}
        @if(!$isOwnerView)
            <a href="{{ route('owner.messages', $booking->owner_id) }}"
               class="text-xs text-neutral-400 font-semibold hover:underline ml-auto">
                {{ __('messages.bc_contact_owner') }}
            </a>
        @else
            <a href="{{ route('owner.messages', $booking->sitter_id) }}"
               class="text-xs text-neutral-400 font-semibold hover:underline ml-auto">
                {{ __('messages.bc_contact_sitter') }}
            </a>
        @endif

        {{-- DOTS MENU --}}
        <div class="relative">
            <button type="button" @click="menuOpen = !menuOpen"
                    class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 hover:text-neutral-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                </svg>
            </button>

            <div x-show="menuOpen" @click.away="menuOpen = false"
                 x-transition
                 class="absolute right-0 bottom-full mb-1 w-44 bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 rounded-xl shadow-lg py-1 z-20">

                <a href="{{ route('mybookings.show', $booking->id) }}"
                   class="block px-3 py-2 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">
                    {{ __('messages.bc_view_full_details') }}
                </a>

                @if($isOwnerView && $booking->status === 'completed')
                    <a href="#" class="block px-3 py-2 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">
                        {{ __('messages.bc_leave_review') }}
                    </a>
                @endif

                @if(in_array($booking->status, ['completed', 'cancelled', 'rejected']))
                    <button type="button"
                            class="block w-full text-left px-3 py-2 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">
                        {{ __('messages.bc_archive') }}
                    </button>
                @endif

                @if($isOwnerView && in_array($booking->status, ['pending', 'accepted']))
                    <button type="button"
                            @click="menuOpen = false; $dispatch('open-cancel-modal', {
                                bookingId: {{ $booking->id }},
                                bookingRef: '{{ $booking->booking_reference }}'
                            })"
                            class="block w-full text-left px-3 py-2 text-xs text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30">
                        {{ __('messages.bc_cancel_booking') }}
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- REJECT MODAL (sitter only) --}}
    @if(!$isOwnerView)
        <div x-show="rejectOpen" x-transition
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
             @click.self="rejectOpen = false">
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-xl max-w-md w-full p-5 sm:p-6 border border-gray-200 dark:border-neutral-800"
                 @click.stop>
                <h3 class="text-base font-black text-[#1B3B36] dark:text-white mb-1">{{ __('messages.bc_decline_modal_title') }}</h3>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-4">
                    {{ __('messages.bc_decline_modal_desc') }}
                </p>

                <form method="POST" action="{{ route('bookings.reject', $booking->id) }}">
                    @csrf
                    <textarea name="reason" rows="3" required
                              placeholder="{{ __('messages.bc_decline_ph') }}"
                              class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-neutral-700 bg-gray-50 dark:bg-neutral-950 text-sm text-neutral-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary resize-none"></textarea>

                    <div class="flex gap-2 mt-4">
                        <button type="button" @click="rejectOpen = false"
                                class="flex-1 px-4 py-2.5 rounded-xl border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-xs hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                            {{ __('messages.bc_cancel') }}
                        </button>
                        <button type="submit"
                                class="flex-1 px-4 py-2.5 rounded-xl bg-red-500 hover:bg-red-600 text-white font-bold text-xs transition">
                            {{ __('messages.bc_decline_btn') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>