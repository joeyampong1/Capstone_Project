<x-app-layout>
<div class="min-h-screen bg-[#FAF8F5] dark:bg-neutral-950 pb-8 sm:pb-16">

    @php
        $profile = $sitter->sitterProfile;
        $fullName = trim($sitter->f_name . ' ' . $sitter->l_name) ?: __('messages.sp_sitter_fallback');
        $rating = $profile->average_ratings ?? 0;
        $fullStars = floor($rating);
        $halfStar = ($rating - $fullStars) >= 0.5;
        $level = (int) ($profile->sitter_type ?? 1);

        // ✅ Fetch reviews via sitter_profile
        $reviews = \App\Models\Review::with(['reviewer', 'booking'])
            ->where('sitter_id', $profile?->id)
            ->orderByDesc('created_at')
            ->get();

        $reviewsCount = $reviews->count();
        $commentsCount = $sitter->comments ? $sitter->comments->count() : 0;

        $foodLabel = match($profile->food_preference ?? '') {
            'owner_provides' => __('messages.sp_food_owner'),
            'sitter_provides' => __('messages.sp_food_sitter'),
            'flexible' => __('messages.sp_food_flexible'),
            default => __('messages.sp_food_not_set_short'),
        };

        $petTypeIds = $profile->preferred_pet_types ?? [];
        $petTypeNames = \App\Models\PetType::whereIn('id', $petTypeIds)->pluck('name')->toArray();
        $petsLabel = !empty($petTypeNames) ? strtolower(implode(', ', $petTypeNames)) : __('messages.sp_pets_not_specified');

        $availableDatesCount = \App\Models\Availability::where('sitter_id', $sitter->id)
            ->where('date', '>=', now()->toDateString())
            ->where('is_available', true)
            ->count();
    @endphp

    {{-- BACK LINK --}}
    <div class="w-full sm:max-w-4xl mx-auto px-2 sm:px-16 lg:px-24 pt-4 sm:pt-6 pb-2">
        <a href="{{ route('find.sitter') }}"
           class="inline-flex items-center gap-1 text-sm text-neutral-500 hover:text-primary transition font-medium">
            {{ __('messages.sp_back_to_sitters') }}
        </a>
    </div>

    {{-- MAIN PROFILE CARD --}}
    <div class="w-full sm:max-w-4xl mx-auto px-2 sm:px-16 lg:px-24">
        <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 overflow-hidden">

            {{-- Hero image --}}
            <div class="relative h-52 sm:h-72 overflow-hidden">
                @if($sitter->profile_photo && file_exists(public_path('storage/' . $sitter->profile_photo)))
                    <div class="absolute inset-0 bg-cover bg-center"
                         style="background-image: url('{{ asset('storage/' . $sitter->profile_photo) }}');"></div>
                @else
                    <div class="absolute inset-0 bg-gradient-to-br from-primary/40 to-primary/10"></div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent"></div>

                <div class="absolute bottom-0 left-0 p-4 sm:p-5">
                    <h1 class="text-xl sm:text-2xl font-black text-white leading-tight">{{ $fullName }}</h1>
                    <div class="flex items-center flex-wrap gap-2 mt-1.5">
                        <span class="flex items-center gap-1 text-xs text-white/80 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ $sitter->location ?? __('messages.sp_location_not_set') }}
                        </span>
                        @if($sitter->id_validation_status === 'verified')
                            <span class="flex items-center gap-1 bg-green-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-full">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                                {{ __('messages.sp_id_verified') }}
                            </span>
                        @endif
                        <span class="flex items-center gap-1 bg-primary text-white text-[10px] font-bold px-2.5 py-1 rounded-full">
                            ST {{ $level }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Info section --}}
            <div class="p-4 sm:p-7 space-y-4 sm:space-y-5">

                {{-- Rating + Daily Rate --}}
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-1.5">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 sm:w-5 sm:h-5
                                    {{ $i <= $fullStars ? 'text-amber-400' : ($i == $fullStars + 1 && $halfStar ? 'text-amber-300' : 'text-neutral-300') }}"
                                    fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                            <span class="text-sm font-bold text-neutral-700 dark:text-neutral-300 ml-1">{{ number_format($rating, 1) }}</span>
                        </div>
                        <p class="text-xs text-neutral-400 mt-0.5">{{ __('messages.sp_reviews_count', ['count' => $reviewsCount]) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-neutral-400">{{ __('messages.sp_daily_rate') }}</p>
                        <p class="text-xl sm:text-2xl font-black text-primary leading-tight">₱{{ number_format($profile->base_rate ?? 0, 0) }}</p>
                        @if($profile->food_preference === 'sitter_provides')
                            <p class="text-[10px] text-neutral-400">{{ __('messages.sp_food_budget_note', ['amount' => number_format($profile->food_budget ?? 100, 0)]) }}</p>
                        @endif
                    </div>
                </div>

                {{-- Bio --}}
                <p class="text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                    {{ $profile->bio ?? __('messages.sp_no_bio') }}
                </p>

                {{-- Info chips --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 sm:gap-3">
                    <div class="flex items-start gap-2.5 bg-primary/5 rounded-xl p-3">
                        <svg class="w-4 h-4 text-primary mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <div>
                            <p class="text-xs font-bold text-primary">{{ __('messages.sp_food_arrangement') }}</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">{{ $foodLabel }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-2.5 bg-primary/5 rounded-xl p-3">
                        <svg class="w-4 h-4 text-primary mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <div>
                            <p class="text-xs font-bold text-primary">{{ __('messages.sp_available_dates') }}</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">{{ __('messages.sp_dates_open', ['count' => $availableDatesCount]) }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-2.5 bg-primary/5 rounded-xl p-3">
                        <svg class="w-4 h-4 text-primary mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        <div>
                            <p class="text-xs font-bold text-primary">{{ __('messages.sp_pets_accepted') }}</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">{{ $petsLabel }}</p>
                        </div>
                    </div>
                </div>

                {{-- CTA Buttons --}}
                <div class="flex flex-col-reverse sm:flex-row gap-2 sm:gap-3 pt-1">
                    <a href="{{ route('mybookings.create', ['sitter_id' => $sitter->id]) }}"
                       class="w-full sm:flex-[2] flex items-center justify-center gap-2 bg-primary hover:bg-primary-600
                              text-white font-bold text-sm py-3 sm:py-3.5 rounded-xl shadow-md hover:shadow-lg
                              transition transform hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ __('messages.sp_book_this_sitter') }}
                    </a>

                    <a href="{{ route('owner.messages', ['id' => $sitter->id]) }}"
                       class="w-full sm:flex-1 flex items-center justify-center gap-2 border-2 border-primary text-primary
                              font-bold text-sm py-3 sm:py-3.5 rounded-xl hover:bg-primary/5 transition transform hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        {{ __('messages.sp_message') }}
                    </a>

                    <a href="{{ route('public.profile', ['id' => $sitter->id]) }}"
                       class="w-full sm:flex-1 flex items-center justify-center gap-2 border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300
                              font-bold text-sm py-3 sm:py-3.5 rounded-xl hover:bg-neutral-100 dark:hover:bg-neutral-800 transition transform hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        {{ __('messages.sp_visit_profile') }}
                    </a>
                </div>

            </div>
        </div>

        {{-- ========================================== --}}
        {{-- RATINGS & REVIEWS — with real data          --}}
        {{-- ========================================== --}}
        <div class="mt-3 sm:mt-4 bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 p-4 sm:p-7">
            <h2 class="text-base font-black text-neutral-800 dark:text-white flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                {{ __('messages.sp_ratings_reviews', ['count' => $reviewsCount]) }}
            </h2>

            @forelse($reviews as $review)
                @php
                    $reviewRating   = (float) $review->rating;
                    $reviewFullStar = floor($reviewRating);
                    $reviewHalfStar = ($reviewRating - $reviewFullStar) >= 0.5;

                    $reviewerName = trim(
                        (optional($review->reviewer)->f_name ?? '') . ' ' .
                        (optional($review->reviewer)->l_name ?? '')
                    ) ?: __('messages.sp_anonymous');
                @endphp

                <div class="py-4 border-b border-gray-100 dark:border-neutral-800 last:border-b-0">

                    {{-- Header: Avatar + Name + Stars + Time --}}
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-2.5 min-w-0">

                            {{-- Reviewer avatar --}}
                            @if($review->reviewer && $review->reviewer->profile_photo && file_exists(public_path('storage/' . $review->reviewer->profile_photo)))
                                <img src="{{ asset('storage/' . $review->reviewer->profile_photo) }}"
                                     alt="{{ $reviewerName }}"
                                     class="w-8 h-8 rounded-full object-cover border border-primary/20 shrink-0">
                            @else
                                <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-xs font-bold text-primary shrink-0">
                                    {{ strtoupper(substr(optional($review->reviewer)->f_name ?? 'U', 0, 1)) }}
                                </div>
                            @endif

                            <div class="min-w-0">
                                <p class="text-sm font-bold text-neutral-800 dark:text-white leading-tight truncate">
                                    {{ $reviewerName }}
                                </p>

                                {{-- Stars --}}
                                <div class="flex items-center gap-0.5 mt-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-3.5 h-3.5
                                            {{ $i <= $reviewFullStar ? 'text-amber-400' : ($i == $reviewFullStar + 1 && $reviewHalfStar ? 'text-amber-300' : 'text-neutral-300') }}"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                    <span class="text-[11px] font-bold text-neutral-500 dark:text-neutral-400 ml-1">
                                        {{ number_format($reviewRating, 1) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <span class="text-[11px] text-neutral-400 shrink-0">
                            {{ $review->created_at->diffForHumans() }}
                        </span>
                    </div>

                    {{-- Comment --}}
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-2 leading-relaxed pl-10">
                        "{{ $review->comments }}"
                    </p>

                    {{-- Booking ref (small) --}}
                    @if($review->booking)
                        <p class="text-[10px] text-neutral-400 mt-1.5 pl-10">
                            📋 Booking: {{ $review->booking->booking_reference }}
                        </p>
                    @endif

                </div>
            @empty
                <p class="text-sm text-neutral-400 text-center py-6">
                    {{ __('messages.sp_no_reviews') }}
                </p>
            @endforelse
        </div>

        {{-- COMMUNITY CHAT --}}
        <div class="mt-3 sm:mt-4 bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 p-4 sm:p-7">
            <h2 class="text-base font-black text-neutral-800 dark:text-white flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                {{ __('messages.sp_community_chat', ['count' => $commentsCount]) }}
            </h2>

            @auth
                <form method="POST"
                    action="{{ route('sitter.comment.store', $sitter->id) }}"
                    class="mb-6"
                    x-data="{ hasText: false }">
                    @csrf
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-xs font-bold text-primary shrink-0 mt-0.5">
                            {{ strtoupper(substr(auth()->user()->f_name ?? 'U', 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <textarea name="body"
                                    rows="2"
                                    required
                                    maxlength="1000"
                                    placeholder="{{ __('messages.sp_comment_ph') }}"
                                    x-on:input="hasText = $event.target.value.trim().length > 0"
                                    class="w-full px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 text-sm text-neutral-800 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition resize-none">{{ old('body') }}</textarea>

                            @error('body')
                                <p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p>
                            @enderror

                            <div class="flex items-center justify-between mt-2 gap-2">
                                <p class="text-[10px] text-neutral-400">{{ __('messages.sp_be_respectful') }}</p>
                                <button type="submit"
                                        x-show="hasText"
                                        x-cloak
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 sm:px-4 sm:py-2 bg-primary hover:bg-primary-600 text-white font-bold text-xs rounded-lg shadow-sm hover:shadow-md transition shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                    </svg>
                                    <span class="hidden sm:inline">{{ __('messages.sp_post_comment') }}</span>
                                    <span class="sm:hidden">{{ __('messages.sp_post') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            @endauth

            <div class="space-y-4">
                @forelse(($sitter->comments ?? collect()) as $comment)
                    <div class="flex items-start gap-2.5" x-data="{ replyOpen: false }">
                        <a href="{{ route('public.profile', ['id' => $comment->user_id]) }}"
                        class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-xs font-bold text-primary shrink-0 mt-0.5 hover:bg-primary/20 transition"
                        title="View {{ trim(($comment->user->f_name ?? '') . ' ' . ($comment->user->l_name ?? '')) ?: 'User' }}'s profile">
                            {{ strtoupper(substr(optional($comment->user)->f_name ?? 'U', 0, 1)) }}
                        </a>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-baseline justify-between gap-2">
                                <a href="{{ route('public.profile', ['id' => $comment->user_id]) }}"
                                class="text-sm font-bold text-neutral-800 dark:text-white leading-tight hover:text-primary transition truncate">
                                    {{ trim((optional($comment->user)->f_name ?? '') . ' ' . (optional($comment->user)->l_name ?? '')) ?: __('messages.sp_anonymous') }}
                                </a>
                                <span class="text-[11px] text-neutral-400 shrink-0">
                                    {{ $comment->created_at->diffForHumans() }}
                                </span>
                            </div>
                            <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-0.5 leading-relaxed">
                                {{ $comment->body }}
                            </p>

                            @auth
                                <button type="button"
                                        @click="replyOpen = !replyOpen"
                                        class="text-xs text-primary font-semibold mt-1 hover:underline">
                                    <span x-show="!replyOpen">{{ __('messages.sp_reply') }}</span>
                                    <span x-show="replyOpen" x-cloak>{{ __('messages.sp_cancel_reply') }}</span>
                                </button>

                                <form x-show="replyOpen"
                                    x-cloak
                                    method="POST"
                                    action="{{ route('sitter.comment.store', $sitter->id) }}"
                                    class="mt-3">
                                    @csrf
                                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                    <div class="flex items-start gap-2">
                                        <div class="w-7 h-7 rounded-full bg-primary/10 flex items-center justify-center text-[10px] font-bold text-primary shrink-0">
                                            {{ strtoupper(substr(auth()->user()->f_name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div class="flex-1">
                                            <textarea name="body"
                                                    rows="2"
                                                    required
                                                    maxlength="1000"
                                                    placeholder="{{ __('messages.sp_reply_ph') }}"
                                                    class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition resize-none"></textarea>
                                            <button type="submit"
                                                    class="mt-2 inline-flex items-center gap-1 px-3 py-1.5 bg-primary hover:bg-primary-600 text-white font-bold text-[11px] rounded-lg transition">
                                                {{ __('messages.sp_reply_btn') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            @endauth

                            @if($comment->replies && $comment->replies->count() > 0)
                                <div class="mt-4 space-y-4 pl-4 sm:pl-6 border-l-2 border-primary/20 ml-1">
                                    @foreach($comment->replies as $reply)
                                        <div class="flex items-start gap-2.5">
                                            <a href="{{ route('public.profile', ['id' => $reply->user_id]) }}"
                                            class="w-7 h-7 rounded-full bg-primary/10 flex items-center justify-center text-[10px] font-bold text-primary shrink-0 mt-0.5 hover:bg-primary/20 transition"
                                            title="View {{ trim(($reply->user->f_name ?? '') . ' ' . ($reply->user->l_name ?? '')) ?: 'User' }}'s profile">
                                                {{ strtoupper(substr(optional($reply->user)->f_name ?? 'U', 0, 1)) }}
                                            </a>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-baseline justify-between gap-2">
                                                    <a href="{{ route('public.profile', ['id' => $reply->user_id]) }}"
                                                    class="text-sm font-bold text-neutral-800 dark:text-white leading-tight hover:text-primary transition truncate">
                                                        {{ trim((optional($reply->user)->f_name ?? '') . ' ' . (optional($reply->user)->l_name ?? '')) ?: __('messages.sp_anonymous') }}
                                                    </a>
                                                    <span class="text-[11px] text-neutral-400 shrink-0">
                                                        {{ $reply->created_at->diffForHumans() }}
                                                    </span>
                                                </div>
                                                <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-0.5 leading-relaxed">
                                                    {{ $reply->body }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-neutral-400 text-center py-6">
                        {{ __('messages.sp_no_comments') }}
                    </p>
                @endforelse
            </div>
        </div>

    </div>{{-- end wrapper --}}
</div>
</x-app-layout>