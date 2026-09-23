@php
    $user         = auth()->user();
    $isSitterMode = (bool) $user->is_sitter;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                    {{ __('messages.mbi_title') }}
                </h1>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.mbi_subtitle') }}</p>
            </div>
        </div>
    </x-slot>

    @php
        $userId = auth()->id();
        $ownerBookings  = $bookings->where('owner_id', $userId);
        $sitterBookings = $bookings->where('sitter_id', $userId);
    @endphp

    <div class="bg-white dark:bg-neutral-950 antialiased text-neutral-800 dark:text-neutral-200
                h-[calc(100vh-140px)] sm:h-[calc(100vh-170px)] lg:h-[calc(100vh-180px)]
                flex flex-col overflow-hidden">
        <div class="w-full sm:max-w-4xl mx-auto px-2 sm:px-16 lg:px-24 flex flex-col h-full">

            {{-- MODE INDICATOR --}}
            @if($canSwitchToSitter)
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-3
                            pt-2 sm:pt-6 mb-4 flex-shrink-0">
                    <div class="flex flex-wrap items-center gap-1 p-1 bg-neutral-100 dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 cursor-default select-none"
                         title="{{ __('messages.mbi_mode_tooltip') }}">
                        <span class="px-3 sm:px-4 py-1.5 sm:py-2 text-[10px] sm:text-xs font-bold rounded-lg transition
                            {{ ! $isSitterMode
                                ? 'bg-primary text-white shadow-md'
                                : 'text-neutral-400 dark:text-neutral-500' }}">
                            {{ __('messages.mbi_mode_owner') }}
                            @if($stats['owner_pending'] > 0)
                                <span class="ml-1 inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full bg-amber-500 text-white text-[9px] font-black">
                                    {{ $stats['owner_pending'] }}
                                </span>
                            @endif
                        </span>

                        <span class="px-3 sm:px-4 py-1.5 sm:py-2 text-[10px] sm:text-xs font-bold rounded-lg transition
                            {{ $isSitterMode
                                ? 'bg-primary text-white shadow-md'
                                : 'text-neutral-400 dark:text-neutral-500' }}">
                            {{ __('messages.mbi_mode_sitter') }}
                            @if($stats['sitter_pending'] > 0)
                                <span class="ml-1 inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full bg-amber-500 text-white text-[9px] font-black">
                                    {{ $stats['sitter_pending'] }}
                                </span>
                            @endif
                        </span>
                    </div>

                    <a href="{{ route('settings.index') }}"
                       class="text-[10px] text-neutral-400 hover:text-primary transition inline-flex items-center gap-0.5 sm:ml-2">
                        {{ __('messages.mbi_change_mode') }}
                    </a>
                </div>
            @endif

            {{-- FILTER TABS + BADGE --}}
            <div class="flex items-center justify-between pt-2 sm:pt-6 mb-4 flex-shrink-0 gap-2">
                @php
                    $tabs = [
                        'all'       => __('messages.mbi_tab_all'),
                        'pending'   => __('messages.mbi_tab_pending'),
                        'accepted'  => __('messages.mbi_tab_accepted'),
                        'completed' => __('messages.mbi_tab_completed'),
                    ];
                @endphp

                <div class="flex items-center gap-2 bg-neutral-100 dark:bg-neutral-800 p-1 rounded-xl overflow-x-auto whitespace-nowrap flex-1 min-w-0">
                    @foreach($tabs as $key => $label)
                        <a href="{{ route('mybookings.index', ['filter' => $key]) }}"
                           class="px-3 sm:px-4 py-1.5 text-[10px] sm:text-xs font-bold rounded-lg transition
                                  {{ $filter === $key
                                        ? 'bg-primary text-white shadow-sm'
                                        : 'text-neutral-600 dark:text-neutral-400 hover:bg-white/50 dark:hover:bg-neutral-700' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-primary/10 text-primary flex-shrink-0">
                    @if($isSitterMode && $canSwitchToSitter)
                        {{ __('messages.mbi_badge_pending', ['count' => $stats['sitter_pending']]) }}
                    @else
                        {{ __('messages.mbi_badge_total', ['count' => $stats['owner_total']]) }}
                    @endif
                </span>
            </div>

            {{-- BOOKING LIST --}}
            <div class="flex-1 overflow-y-auto min-h-0">
                <div class="space-y-4 pb-4">

                    {{-- OWNER MODE --}}
                    @if(! $isSitterMode)
                        <div class="space-y-4">
                            @forelse($ownerBookings as $booking)
                                @include('mybookings.partials.booking-card', ['booking' => $booking, 'viewAs' => 'owner'])
                            @empty
                                <div class="text-center py-12">
                                    <svg class="w-12 h-12 mx-auto text-neutral-300 dark:text-neutral-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="text-sm text-neutral-500 dark:text-neutral-400">
                                        {{ $filter === 'all' ? __('messages.mbi_no_bookings') : __('messages.mbi_no_filter_bookings', ['filter' => $tabs[$filter] ?? $filter]) }}
                                    </p>
                                    <a href="{{ route('find.sitter') }}"
                                       class="inline-block mt-3 text-xs font-bold text-primary hover:underline">
                                        → {{ __('messages.mbi_find_sitter') }}
                                    </a>
                                </div>
                            @endforelse
                        </div>
                    @endif

                    {{-- SITTER MODE --}}
                    @if($isSitterMode && $canSwitchToSitter)
                        <div class="space-y-4">
                            @forelse($sitterBookings as $booking)
                                @include('mybookings.partials.booking-card', ['booking' => $booking, 'viewAs' => 'sitter'])
                            @empty
                                <div class="text-center py-12">
                                    <svg class="w-12 h-12 mx-auto text-neutral-300 dark:text-neutral-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="text-sm text-neutral-500 dark:text-neutral-400">
                                        {{ $filter === 'all' ? __('messages.mbi_no_requests') : __('messages.mbi_no_filter_requests', ['filter' => $tabs[$filter] ?? $filter]) }}
                                    </p>
                                </div>
                            @endforelse
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>

    {{-- GLOBAL CANCEL MODAL --}}
    <div x-data="{ cancelOpen: false, cancelBookingId: null, cancelBookingRef: '' }"
         @open-cancel-modal.window="
            cancelOpen = true;
            cancelBookingId = $event.detail.bookingId;
            cancelBookingRef = $event.detail.bookingRef;
         "
         x-show="cancelOpen"
         x-cloak>

        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
             @click.self="cancelOpen = false">

            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-neutral-800 max-w-md w-full p-5 sm:p-6"
                 @click.stop>

                <div class="flex items-start gap-3 mb-4">
                    <div class="w-11 h-11 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center text-red-600 dark:text-red-400 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-black text-[#1B3B36] dark:text-white">{{ __('messages.mbi_cancel_modal_title') }}</h3>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">
                            {{ __('messages.mbi_cancel_modal_booking') }} <strong x-text="cancelBookingRef"></strong>
                        </p>
                    </div>
                </div>

                <form :action="`{{ url('mybookings') }}/${cancelBookingId}/cancel`" method="POST">
                    @csrf

                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1.5">
                        {{ __('messages.mbi_cancel_reason') }} <span class="text-red-500">*</span>
                    </label>
                    <textarea name="reason" rows="4" required
                              placeholder="{{ __('messages.mbi_cancel_reason_ph') }}"
                              class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-3 py-2.5 text-[#1B3B36] dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 text-sm resize-none"></textarea>

                    <p class="text-[10px] text-neutral-400 mt-1">
                        {{ __('messages.mbi_cancel_note') }}
                    </p>

                    <div class="flex gap-2 mt-4">
                        <button type="button" @click="cancelOpen = false"
                                class="flex-1 px-4 py-2.5 rounded-xl border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-xs hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                            {{ __('messages.mbi_keep_booking') }}
                        </button>
                        <button type="submit"
                                class="flex-1 px-4 py-2.5 rounded-xl bg-red-500 hover:bg-red-600 text-white font-bold text-xs transition shadow-md hover:shadow-lg">
                            {{ __('messages.mbi_yes_cancel') }}
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

</x-app-layout>