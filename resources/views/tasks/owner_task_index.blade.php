<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div>
                <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                    {{ __('messages.otm_title') }}
                </h1>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.otm_subtitle') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200"
         x-data="{
             showReviewModal: false,
             reviewBookingId: null,
             reviewSitterName: '',
             reviewBookingRef: '',
             rating: 0,
             hoverRating: 0,
             comments: '',

             openReview(id, sitterName, ref) {
                 this.reviewBookingId = id;
                 this.reviewSitterName = sitterName;
                 this.reviewBookingRef = ref;
                 this.rating = 0;
                 this.hoverRating = 0;
                 this.comments = '';
                 this.showReviewModal = true;
             },

             closeReview() {
                 this.showReviewModal = false;
                 this.reviewBookingId = null;
                 this.rating = 0;
                 this.hoverRating = 0;
                 this.comments = '';
             },

             setRating(val) {
                 this.rating = val;
             },

             submitReview() {
                 if (this.rating === 0) {
                     alert(@js(__('messages.otm_alert_select_rating')));
                     return false;
                 }
                 if (!this.comments.trim()) {
                     alert(@js(__('messages.otm_alert_write_comment')));
                     return false;
                 }
                 return true;
             }
         }">
        <div class="w-full sm:max-w-5xl mx-auto px-2 sm:px-16 lg:px-24">

            {{-- ========================================== --}}
            {{-- BOOKING LIST (cards)                       --}}
            {{-- ========================================== --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">

                @forelse($bookings as $booking)
                    @php
                        $pet    = $booking->pet;
                        $sitter = $booking->sitter;

                        $totalVisits     = $booking->visits->count();
                        $completedVisits = $booking->visits->where('status', 'completed')->count();
                        $progressPct     = $totalVisits > 0
                            ? round(($completedVisits / $totalVisits) * 100)
                            : 0;

                        $sitterName = trim(($sitter->f_name ?? '') . ' ' . ($sitter->l_name ?? '')) ?: __('messages.otm_sitter_default');

                        $petEmoji = match($pet->petType?->name ?? '') {
                            'Dog' => '🐶', 'Cat' => '🐱', 'Bird' => '🐦',
                            'Rabbit' => '🐰', 'Hamster' => '🐹', 'Fish' => '🐟',
                            'Reptile' => '🦎', default => '🐾',
                        };

                        // Status badge based sa booking status
                        if ($booking->status === 'completed') {
                            $statusBadgeClass = 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400';
                            $statusBadgeLabel = __('messages.otm_status_completed');
                        } elseif ($booking->status === 'accepted') {
                            $statusBadgeClass = 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400';
                            $statusBadgeLabel = __('messages.otm_status_active');
                        } else {
                            $statusBadgeClass = 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400';
                            $statusBadgeLabel = ucfirst($booking->status);
                        }

                        // Check kung naay existing review
                        $hasReview = $booking->review()->exists();

                        // ✅ Check kung TANAN visits completed na
                        $allVisitsCompleted = $totalVisits > 0
                            && $completedVisits === $totalVisits
                            && $booking->status === 'completed';
                    @endphp

                    {{-- ========================================== --}}
                    {{-- BOOKING CARD                               --}}
                    {{-- ========================================== --}}
                    <div class="group bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-5 hover:shadow-lg hover:border-primary/30 transition duration-200">

                        {{-- Clickable link part --}}
                        <a href="{{ route('task.monitor', ['booking' => $booking->id]) }}" class="block">

                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.otm_booking_ref') }}</p>
                                    <h3 class="font-bold text-lg sm:text-xl text-[#1B3B36] dark:text-white truncate">
                                        {{ $booking->booking_reference }}
                                    </h3>
                                </div>
                                <span class="{{ $statusBadgeClass }} px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full text-xs sm:text-sm font-semibold shrink-0">
                                    {{ $statusBadgeLabel }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-2.5 sm:gap-3 mt-4">
                                <div class="min-w-0">
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.otm_pet') }}</p>
                                    <p class="font-bold text-[#1B3B36] dark:text-white truncate">
                                        {{ $petEmoji }} {{ $pet->name ?? __('messages.otm_pet_default') }}
                                    </p>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.otm_sitter') }}</p>
                                    <p class="font-bold text-[#1B3B36] dark:text-white truncate">{{ $sitterName }}</p>
                                </div>
                            </div>

                            {{-- Progress --}}
                            @if($totalVisits > 0)
                                <div class="mt-4">
                                    <div class="flex justify-between gap-2 text-sm">
                                        <span class="text-neutral-500 dark:text-neutral-400">{{ __('messages.otm_visit_progress') }}</span>
                                        <span class="font-bold text-[#1B3B36] dark:text-white shrink-0">
                                            {{ $completedVisits }} / {{ $totalVisits }} {{ __('messages.otm_visits') }}
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-neutral-700 rounded-full h-2 mt-1">
                                        <div class="bg-primary h-2 rounded-full transition-all"
                                             style="width:{{ $progressPct }}%"></div>
                                    </div>
                                </div>
                            @else
                                <div class="mt-4 text-xs text-neutral-400 italic">
                                    {{ __('messages.otm_no_visits_scheduled') }}
                                </div>
                            @endif
                        </a>

                        {{-- ========================================== --}}
                        {{-- ACTION ROW                                 --}}
                        {{-- ========================================== --}}
                        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800 flex items-center justify-between gap-2">

                            <a href="{{ route('task.monitor', ['booking' => $booking->id]) }}"
                               class="text-sm font-medium text-primary hover:underline">
                                {{ __('messages.otm_view_details') }}
                            </a>

                            {{-- ========================================== --}}
                            {{-- RATE BUTTON (3 states)                     --}}
                            {{-- ========================================== --}}
                            @if($allVisitsCompleted)
                                {{-- State 1: Tanan visits done --}}
                                @if($hasReview)
                                    {{-- Already reviewed --}}
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-bold shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                        </svg>
                                        {{ __('messages.otm_reviewed') }}
                                    </span>
                                @else
                                    {{-- Show rate button (clickable) --}}
                                    <button type="button"
                                            @click="openReview(
                                                {{ $booking->id }},
                                                '{{ addslashes($sitterName) }}',
                                                '{{ $booking->booking_reference }}'
                                            )"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold transition shadow-sm shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                        </svg>
                                        {{ __('messages.otm_rate_sitter') }}
                                    </button>
                                @endif
                            @else
                                {{-- State 2: Dili pa tanan visits done — disabled info badge --}}
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-neutral-100 dark:bg-neutral-800 text-neutral-500 dark:text-neutral-400 text-xs font-medium shrink-0 cursor-not-allowed"
                                      title="{{ __('messages.otm_complete_all_visits', ['total' => $totalVisits]) }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    {{ __('messages.otm_rate_count', ['done' => $completedVisits, 'total' => $totalVisits]) }}
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    {{-- ========================================== --}}
                    {{-- EMPTY STATE                                --}}
                    {{-- ========================================== --}}
                    <div class="md:col-span-2 text-center py-12">
                        <svg class="w-12 h-12 mx-auto text-neutral-300 dark:text-neutral-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">
                            {{ __('messages.otm_no_bookings') }}
                        </p>
                        <a href="{{ route('find.sitter') }}"
                           class="inline-block mt-3 text-xs font-bold text-primary hover:underline">
                            {{ __('messages.otm_find_sitter') }}
                        </a>
                    </div>
                @endforelse

            </div>

            {{-- ========================================== --}}
            {{-- REVIEW MODAL                               --}}
            {{-- ========================================== --}}
            <div x-show="showReviewModal"
                 x-cloak
                 x-transition.opacity
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
                 @click.away="closeReview()">

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-neutral-800 max-w-md w-full p-5 sm:p-6"
                     @click.stop
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100">

                    {{-- Header --}}
                    <div class="flex items-start gap-3 mb-5">
                        <div class="w-11 h-11 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400 shrink-0">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="text-base font-black text-[#1B3B36] dark:text-white">{{ __('messages.otm_rate_your_sitter') }}</h3>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">
                                <span x-text="reviewSitterName"></span> •
                                <span class="font-mono" x-text="reviewBookingRef"></span>
                            </p>
                        </div>
                        <button type="button"
                                @click="closeReview()"
                                class="p-1.5 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition shrink-0">
                            <svg class="w-4 h-4 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <form :action="`{{ url('bookings') }}/${reviewBookingId}/review`"
                          method="POST"
                          @submit="return submitReview()">
                        @csrf

                        {{-- Star rating --}}
                        <div class="mb-5 text-center">
                            <p class="text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-3">
                                {{ __('messages.otm_how_was_experience') }}
                            </p>

                            <div class="flex items-center justify-center gap-2">
                                <template x-for="star in [1,2,3,4,5]" :key="star">
                                    <button type="button"
                                            @click="setRating(star)"
                                            @mouseenter="hoverRating = star"
                                            @mouseleave="hoverRating = 0"
                                            class="transition-transform hover:scale-110">
                                        <svg class="w-10 h-10 transition-colors"
                                             :class="star <= (hoverRating || rating)
                                                ? 'text-amber-400'
                                                : 'text-neutral-300 dark:text-neutral-600'"
                                             fill="currentColor"
                                             viewBox="0 0 24 24">
                                            <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                        </svg>
                                    </button>
                                </template>
                            </div>

                            <p class="mt-2 text-xs text-amber-600 dark:text-amber-400 font-bold" x-show="rating > 0" x-cloak>
                                {{ __('messages.otm_x_of_5_stars', ['rating' => '']) }}<span x-text="rating"></span>
                            </p>
                            <p class="mt-2 text-xs text-neutral-400 italic" x-show="rating === 0">
                                {{ __('messages.otm_tap_star') }}
                            </p>

                            {{-- Hidden input --}}
                            <input type="hidden" name="rating" :value="rating">
                        </div>

                        {{-- Comments --}}
                        <div class="mb-5">
                            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1.5">
                                {{ __('messages.otm_your_review') }} <span class="text-red-500">*</span>
                            </label>
                            <textarea name="comments"
                                      x-model="comments"
                                      rows="4"
                                      required
                                      placeholder="{{ __('messages.otm_review_placeholder') }}"
                                      class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-3 py-2.5 text-[#1B3B36] dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-sm resize-none"></textarea>
                            <p class="text-[10px] text-neutral-400 mt-1">
                                {{ __('messages.otm_review_visible') }}
                            </p>
                        </div>

                        {{-- Actions --}}
                        <div class="flex gap-2">
                            <button type="button"
                                    @click="closeReview()"
                                    class="flex-1 px-4 py-2.5 rounded-xl border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-xs hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                                {{ __('messages.otm_cancel') }}
                            </button>
                            <button type="submit"
                                    class="flex-1 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs transition shadow-md hover:shadow-lg">
                                {{ __('messages.otm_submit_review') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>