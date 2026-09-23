<x-app-layout>
    <!-- ========================================== -->
    <!-- HEADER SLOT                               -->
    <!-- ========================================== -->
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('find.sitter') }}"
            class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>

            <div>
                <h2 class="text-2xl font-black text-[#1B3B36] dark:text-white">
                    {{ __('messages.pf_title') }}
                </h2>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                    @if($user->is_sitter)
                        {{ __('messages.pf_role_sitter_level', ['level' => $user->sitterProfile->sitter_type ?? $user->sitter_level ?? 1]) }}
                    @else
                        {{ __('messages.pf_role_owner') }}
                    @endif
                </p>
            </div>
        </div>
    </x-slot>

    @php
        // ==========================================
        // COMPUTE STATS DIRECTLY FROM DB
        // ==========================================
        $fullName = trim($user->f_name . ' ' . $user->l_name) ?: 'User';

        $sitterProfile = $user->sitterProfile;

        // Reviews count
        $reviewsCount = $sitterProfile
            ? \App\Models\Review::where('sitter_id', $sitterProfile->id)->count()
            : 0;

        // Average rating — computed directly gikan sa reviews table
        $averageRating = $sitterProfile
            ? round((float) \App\Models\Review::where('sitter_id', $sitterProfile->id)->avg('rating'), 1)
            : 0.0;

        // Fallback sa sitter_profiles.average_ratings kung wala review
        if ($reviewsCount === 0 && $sitterProfile) {
            $averageRating = (float) ($sitterProfile->average_ratings ?? 0);
        }

        // Total bookings (accepted + completed)
        $totalBookings = $user->is_sitter
            ? \App\Models\Booking::where('sitter_id', $user->id)
                ->whereIn('status', ['accepted', 'completed'])
                ->count()
            : 0;

        // Reviews list for display
        $reviews = $sitterProfile
            ? \App\Models\Review::with(['reviewer', 'booking'])
                ->where('sitter_id', $sitterProfile->id)
                ->orderByDesc('created_at')
                ->limit(10)
                ->get()
            : collect();
    @endphp

    <!-- ========================================== -->
    <!-- MAIN CONTENT                              -->
    <!-- ========================================== -->
    <div class="py-4 sm:py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200">
        <div class="w-full sm:max-w-4xl mx-auto px-2 sm:px-16 lg:px-24">

            <!-- ========================================== -->
            <!-- PROFILE HEADER CARD                        -->
            <!-- ========================================== -->
            <div class="bg-white dark:bg-neutral-900 rounded-2xl sm:rounded-3xl shadow-sm sm:shadow-xl border border-gray-200/60 dark:border-neutral-800 overflow-hidden">

                {{-- Cover Photo --}}
                <div class="relative h-40 sm:h-56 bg-gradient-to-r from-primary-100 to-accent-100 dark:from-primary-900/30 dark:to-accent-900/30"></div>

                {{-- Avatar + Name Row --}}
                <div class="px-4 sm:px-8">
                    <div class="flex flex-col sm:flex-row sm:items-end gap-4 sm:gap-6">

                        {{-- Avatar --}}
                        <div class="relative z-10 -mt-16 sm:-mt-20 shrink-0">
                            @if($user->profile_photo && file_exists(public_path('storage/' . $user->profile_photo)))
                                <img src="{{ asset('storage/' . $user->profile_photo) }}"
                                    alt="{{ $fullName }}"
                                    class="w-28 h-28 sm:w-32 sm:h-32 rounded-full object-cover border-4 border-white dark:border-neutral-900 shadow-lg">
                            @else
                                <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-full bg-primary flex items-center justify-center text-white border-4 border-white dark:border-neutral-900 shadow-lg">
                                    <svg class="w-16 h-16 sm:w-20 sm:h-20" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM3.751 20.105a8.25 8.25 0 0116.498 0 .75.75 0 01-.437.695A18.683 18.683 0 0112 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 01-.437-.695z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            @endif
                        </div>

                        {{-- Name + Location --}}
                        <div class="flex-1 pb-2 min-w-0">
                            <h2 class="text-2xl sm:text-3xl font-black text-[#1B3B36] dark:text-white truncate">
                                {{ $fullName }}
                            </h2>
                            <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1 flex items-center gap-1">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ $user->location ?? __('messages.pf_location_not_set') }}
                            </p>
                        </div>
                    </div>

                    {{-- Bottom Section: Badges + Action Button --}}
                    <div class="flex items-center justify-between gap-3 mt-3 pb-6">

                        {{-- Badges --}}
                        <div class="flex items-center flex-wrap gap-2 min-w-0">
                            {{-- ID Verified --}}
                            @if($user->id_validation_status === 'verified')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    {{ __('messages.pf_id_verified') }}
                                </span>
                            @endif

                            {{-- Role --}}
                            @if($user->is_sitter)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-accent/10 text-accent dark:bg-accent/20">
                                    {{ __('messages.pf_badge_sitter') }}
                                </span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary/10 text-primary">
                                    {{ __('messages.pf_badge_level', ['level' => $sitterProfile->sitter_type ?? $user->sitter_level ?? 1]) }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-secondary/30 text-neutral-600 dark:bg-neutral-800/30 dark:text-neutral-400">
                                    {{ __('messages.pf_badge_owner') }}
                                </span>
                            @endif

                            {{-- Gender --}}
                            @if($user->gender === 'male' || $user->gender === 'female')
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-neutral-100 dark:bg-neutral-800 text-neutral-500 dark:text-neutral-400"
                                      title="{{ ucfirst($user->gender) }}">
                                    @if($user->gender === 'male')
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2C8.134 2 5 5.134 5 9c0 3.866 3.134 7 7 7s7-3.134 7-7c0-3.866-3.134-7-7-7zm0 12c-2.757 0-5-2.243-5-5s2.243-5 5-5 5 2.243 5 5-2.243 5-5 5zm0-9a1 1 0 00-1 1v2H9a1 1 0 000 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V6a1 1 0 00-1-1z"/>
                                        </svg>
                                    @else
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2C8.134 2 5 5.134 5 9c0 3.866 3.134 7 7 7s7-3.134 7-7c0-3.866-3.134-7-7-7zm0 12c-2.757 0-5-2.243-5-5s2.243-5 5-5 5 2.243 5 5-2.243 5-5 5zm-1 2v4H9a1 1 0 000 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2v-4a1 1 0 10-2 0z"/>
                                        </svg>
                                    @endif
                                </span>
                            @endif
                        </div>

                        {{-- Action Button --}}
                        <div class="shrink-0">
                            @if(auth()->id() !== $user->id)
                                <a href="{{ route('owner.messages', ['id' => $user->id]) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary hover:bg-primary-600 text-white font-bold text-xs rounded-lg shadow-sm hover:shadow-md transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                    {{ __('messages.pf_message') }}
                                </a>
                            @else
                                <a href="{{ route('profile.edit') }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 border-2 border-primary text-primary hover:bg-primary/5 font-bold text-xs rounded-lg transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    {{ __('messages.pf_edit_profile') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- BIO & DETAILS                             -->
            <!-- ========================================== -->
            <div class="mt-4 sm:mt-6 grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">

                {{-- Left Column --}}
                <div class="lg:col-span-2 space-y-4 sm:space-y-6">

                    {{-- Bio --}}
                    <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 p-4 sm:p-6">
                        <h3 class="text-sm font-black text-[#1B3B36] dark:text-white uppercase tracking-wider">{{ __('messages.pf_about') }}</h3>
                        <p class="mt-3 text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                            {{ $user->bio ?? $sitterProfile->bio ?? __('messages.pf_no_bio') }}
                        </p>
                    </div>

                    {{-- Sitter Details --}}
                    @if($user->is_sitter && $sitterProfile)
                    <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 p-4 sm:p-6">
                        <h3 class="text-sm font-black text-[#1B3B36] dark:text-white uppercase tracking-wider">{{ __('messages.pf_sitter_details') }}</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                            <div>
                                <p class="text-xs text-neutral-400">{{ __('messages.pf_rate_per_visit') }}</p>
                                <p class="text-base font-black text-primary">₱{{ number_format($sitterProfile->base_rate ?? 0, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-400">{{ __('messages.pf_years_experience') }}</p>
                                <p class="text-base font-bold text-neutral-800 dark:text-white">
                                    {{ __('messages.pf_years_count', ['count' => $sitterProfile->experience_years ?? 0]) }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-400">{{ __('messages.pf_pets_accepted') }}</p>
                                <p class="text-base font-bold text-neutral-800 dark:text-white">
                                    @php
                                        $petTypeIds = $sitterProfile->preferred_pet_types ?? [];
                                        $petTypeNames = \App\Models\PetType::whereIn('id', $petTypeIds)->pluck('name')->toArray();
                                    @endphp
                                    {{ !empty($petTypeNames) ? implode(', ', $petTypeNames) : __('messages.pf_not_specified') }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-400">{{ __('messages.pf_pet_sizes_accepted') }}</p>
                                <p class="text-base font-bold text-neutral-800 dark:text-white">
                                    @php $sizes = $sitterProfile->preferred_pet_sizes ?? []; @endphp
                                    {{ !empty($sizes) ? implode(', ', array_map('ucfirst', $sizes)) : __('messages.pf_not_specified') }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-400">{{ __('messages.pf_food_arrangement') }}</p>
                                <p class="text-base font-bold text-neutral-800 dark:text-white">
                                    @if($sitterProfile->food_preference === 'owner_provides')
                                        {{ __('messages.pf_food_owner') }}
                                    @elseif($sitterProfile->food_preference === 'sitter_provides')
                                        {{ __('messages.pf_food_sitter', ['amount' => number_format($sitterProfile->food_budget ?? 100, 0)]) }}
                                    @else
                                        {{ __('messages.pf_food_flexible') }}
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-400">{{ __('messages.pf_pet_capacity') }}</p>
                                <p class="text-base font-bold text-neutral-800 dark:text-white">
                                    {{ __('messages.pf_pets_range', ['min' => $sitterProfile->min_pets_capacity ?? 1, 'max' => $sitterProfile->max_pets_capacity ?? 3]) }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Pets --}}
                    <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-black text-[#1B3B36] dark:text-white uppercase tracking-wider">{{ __('messages.pf_pets') }}</h3>
                            <span class="text-xs text-neutral-400">{{ __('messages.pf_pets_count', ['count' => $user->pets->count() ?? 0]) }}</span>
                        </div>
                        @if($user->pets && $user->pets->count() > 0)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach($user->pets as $pet)
                                    <div class="flex items-center gap-3 p-3 bg-neutral-50 dark:bg-neutral-800/30 rounded-xl border border-gray-100 dark:border-neutral-700">
                                        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-xl overflow-hidden shrink-0">
                                            @if($pet->photo_path)
                                                <img src="{{ asset('storage/' . $pet->photo_path) }}" alt="{{ $pet->name }}" class="w-full h-full object-cover">
                                            @else
                                                🐾
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-bold text-neutral-800 dark:text-white truncate">{{ $pet->name }}</p>
                                            <p class="text-xs text-neutral-500 truncate">
                                                {{ $pet->petType?->name ?? __('messages.pf_unknown') }} • {{ $pet->breed ?? __('messages.pf_mixed') }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-neutral-400">{{ __('messages.pf_no_pets') }}</p>
                        @endif
                    </div>

                    {{-- ========================================== --}}
                    {{-- REVIEWS LIST                               --}}
                    {{-- ========================================== --}}
                    @if($user->is_sitter && $reviewsCount > 0)
                    <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 p-4 sm:p-6">
                        <h3 class="text-sm font-black text-[#1B3B36] dark:text-white uppercase tracking-wider mb-4">
                            {{ __('messages.pf_reviews_count', ['count' => $reviewsCount]) }}
                        </h3>

                        <div class="space-y-4">
                            @foreach($reviews as $review)
                                @php
                                    $reviewRating = (float) $review->rating;
                                    $reviewFullStars = floor($reviewRating);
                                    $reviewHalfStar = ($reviewRating - $reviewFullStars) >= 0.5;

                                    $reviewerName = trim(
                                        (optional($review->reviewer)->f_name ?? '') . ' ' .
                                        (optional($review->reviewer)->l_name ?? '')
                                    ) ?: __('messages.pf_anonymous');
                                @endphp

                                <div class="py-4 border-b border-gray-100 dark:border-neutral-800 last:border-b-0 last:pb-0">

                                    {{-- Header --}}
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
                                                            {{ $i <= $reviewFullStars ? 'text-amber-400' : ($i == $reviewFullStars + 1 && $reviewHalfStar ? 'text-amber-300' : 'text-neutral-300') }}"
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

                                    {{-- Booking ref --}}
                                    @if($review->booking)
                                        <p class="text-[10px] text-neutral-400 mt-1.5 pl-10">
                                            📋 {{ $review->booking->booking_reference }}
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Right Column: Stats & Contact --}}
                <div class="space-y-4 sm:space-y-6">
                    {{-- Stats --}}
                    <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 p-4 sm:p-6">
                        <h3 class="text-sm font-black text-[#1B3B36] dark:text-white uppercase tracking-wider">{{ __('messages.pf_stats') }}</h3>
                        <div class="space-y-3 mt-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('messages.pf_stat_reviews') }}</span>
                                <span class="text-sm font-bold text-neutral-900 dark:text-white">{{ $reviewsCount }}</span>
                            </div>
                            <div class="flex items-center justify-between border-t border-gray-100 dark:border-neutral-800 pt-2">
                                <span class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('messages.pf_stat_rating') }}</span>
                                <span class="text-sm font-bold text-yellow-500">
                                    {{ number_format($averageRating, 1) }} ★
                                </span>
                            </div>
                            @if($user->is_sitter && $sitterProfile)
                            <div class="flex items-center justify-between border-t border-gray-100 dark:border-neutral-800 pt-2">
                                <span class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('messages.pf_stat_total_bookings') }}</span>
                                <span class="text-sm font-bold text-neutral-900 dark:text-white">
                                    {{ $totalBookings }}
                                </span>
                            </div>
                            @endif
                            <div class="flex items-center justify-between border-t border-gray-100 dark:border-neutral-800 pt-2">
                                <span class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('messages.pf_stat_member_since') }}</span>
                                <span class="text-sm font-bold text-neutral-900 dark:text-white">
                                    {{ $user->created_at->format('M Y') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Contact Info --}}
                    <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 p-4 sm:p-6 overflow-hidden">
                        <h3 class="text-sm font-black text-[#1B3B36] dark:text-white uppercase tracking-wider">{{ __('messages.pf_contact') }}</h3>
                        <div class="space-y-3 mt-3">
                            @if($user->contact_number)
                            <div class="flex items-start gap-2 text-sm text-neutral-600 dark:text-neutral-400">
                                <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                <span class="break-words">{{ $user->contact_number }}</span>
                            </div>
                            @endif

                            @if($user->address || $user->location)
                            <div class="flex items-start gap-2 text-sm text-neutral-600 dark:text-neutral-400">
                                <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span class="break-words">{{ $user->address ?? $user->location }}</span>
                            </div>
                            @endif

                            <div class="flex items-start gap-2 text-sm text-neutral-600 dark:text-neutral-400">
                                <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <span class="break-all min-w-0">{{ $user->email }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>