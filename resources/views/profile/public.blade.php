<x-app-layout>
    <!-- ========================================== -->
    <!-- HEADER SLOT – clean, no icons             -->
    <!-- ========================================== -->
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <!-- Back button -->
            <a href="{{ route('find.sitter') }}" 
               class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>

            <!-- User name (plain, no icons) -->
            <div>
                <h2 class="text-2xl font-black text-[#1B3B36] dark:text-white">
                    {{ $user->name }}
                </h2>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                    @if($user->is_sitter)
                        Pet Sitter • Level {{ $user->sitter_level }}
                    @else
                        Pet Owner
                    @endif
                </p>
            </div>
        </div>
    </x-slot>

    <!-- ========================================== -->
    <!-- MAIN CONTENT                              -->
    <!-- ========================================== -->
    <div class="py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- ========================================== -->
            <!-- PROFILE HEADER CARD                        -->
            <!-- ========================================== -->
            <div class="bg-white dark:bg-neutral-900 rounded-3xl shadow-xl border border-gray-200/60 dark:border-neutral-800 overflow-hidden">

                {{-- Cover Photo --}}
                <div class="relative h-48 sm:h-56 bg-gradient-to-r from-primary-100 to-accent-100 dark:from-primary-900/30 dark:to-accent-900/30">
                    @if($user->cover_photo)
                        <img src="{{ asset('storage/' . $user->cover_photo) }}" 
                             alt="Cover Photo" 
                             class="w-full h-full object-cover">
                    @endif
                </div>

                {{-- Avatar --}}
                <div class="relative px-6 sm:px-8">
                    <div class="absolute -top-12 left-6 sm:left-8">
                        <img src="{{ $user->profile_photo_url }}" 
                             alt="{{ $user->name }}" 
                             class="w-24 h-24 sm:w-28 sm:h-28 rounded-full object-cover border-4 border-white dark:border-neutral-900 shadow-lg">
                    </div>
                </div>

                {{-- User Info – with badges area containing gender + message icons --}}
                <div class="pt-16 pb-6 px-6 sm:px-8">
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                        <div>
                            <!-- Name (no icons) -->
                            <h2 class="text-2xl font-black text-[#1B3B36] dark:text-white">
                                {{ $user->name }}
                            </h2>

                            <!-- Location -->
                            <p class="text-sm text-neutral-500 dark:text-neutral-400 flex items-center gap-1 mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ $user->location ?? 'Location not set' }}
                            </p>

                            <!-- Badges + Gender & Message Icons -->
                            <div class="flex items-center flex-wrap gap-2 mt-2">
                                <!-- ID Verified badge -->
                                @if($user->id_validation_status === 'verified')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        ID Verified
                                    </span>
                                @endif

                                <!-- Role badges -->
                                @if($user->is_sitter)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-accent/10 text-accent dark:bg-accent/20">
                                        Sitter
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary/10 text-primary">
                                        Level {{ $user->sitter_level }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-secondary/30 text-neutral-600 dark:bg-neutral-800/30 dark:text-neutral-400">
                                        Pet Owner
                                    </span>
                                @endif

                                <!-- Gender icon (small badge) -->
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

                                <!-- Message icon (small clickable badge) -->
                                <a href="{{ route('owner.messages', ['id' => $user->id]) }}"
                                   class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-neutral-100 dark:bg-neutral-800 text-neutral-500 dark:text-neutral-400 hover:bg-primary/10 hover:text-primary transition"
                                   title="Send message">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>

                        <!-- Right‑side buttons (Message or Edit) -->
                        <div class="flex items-center gap-3">
                            @if(!auth()->user()->id == $user->id)
                                {{-- Message button (if viewing someone else) --}}
                                <a href="{{ route('owner.messages', ['id' => $user->id]) }}"
                                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary hover:bg-primary-600 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                    Message
                                </a>
                            @else
                                {{-- Edit Profile button (if viewing own profile) --}}
                                <a href="{{ route('profile.edit') }}"
                                   class="inline-flex items-center gap-2 px-4 py-2.5 border-2 border-primary text-primary hover:bg-primary/5 font-bold text-sm rounded-xl transition transform hover:-translate-y-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Edit Profile
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- BIO & DETAILS                             -->
            <!-- ========================================== -->
            <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- Left Column: Bio & Basic Info --}}
                <div class="lg:col-span-2 space-y-6">
                    
                    {{-- Bio --}}
                    <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 p-5 sm:p-6">
                        <h3 class="text-sm font-black text-[#1B3B36] dark:text-white uppercase tracking-wider">About</h3>
                        <p class="mt-3 text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                            {{ $user->bio ?? 'This user hasn\'t added a bio yet.' }}
                        </p>
                    </div>

                    {{-- Sitter Details (only if user is a sitter) --}}
                    @if($user->is_sitter)
                    <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 p-5 sm:p-6">
                        <h3 class="text-sm font-black text-[#1B3B36] dark:text-white uppercase tracking-wider">Sitter Details</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                            <div>
                                <p class="text-xs text-neutral-400">Rate per Visit</p>
                                <p class="text-base font-black text-primary">₱{{ number_format($user->rate_per_visit ?? 0) }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-400">Pets Accepted</p>
                                <p class="text-base font-bold text-neutral-800 dark:text-white">{{ $user->pet_types ?? 'Not specified' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-400">Food Arrangement</p>
                                <p class="text-base font-bold text-neutral-800 dark:text-white">
                                    @if($user->food_preference === 'owner_provides')
                                        Owner provides food
                                    @elseif($user->food_preference === 'sitter_provides')
                                        Sitter provides food (+₱100)
                                    @else
                                        Flexible
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-400">Member Since</p>
                                <p class="text-base font-bold text-neutral-800 dark:text-white">{{ $user->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- My Pets (if they have pets) --}}
                    <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 p-5 sm:p-6">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-black text-[#1B3B36] dark:text-white uppercase tracking-wider">My Pets</h3>
                            <span class="text-xs text-neutral-400">{{ $user->pets->count() ?? 0 }} pets</span>
                        </div>
                        @if($user->pets && $user->pets->count() > 0)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach($user->pets as $pet)
                                <div class="flex items-center gap-3 p-3 bg-neutral-50 dark:bg-neutral-800/30 rounded-xl border border-gray-100 dark:border-neutral-700">
                                    <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-xl">
                                        🐾
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-neutral-800 dark:text-white">{{ $pet->name }}</p>
                                        <p class="text-xs text-neutral-500">{{ $pet->type }} • {{ $pet->breed ?? 'Mixed' }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-neutral-400">No pets added yet.</p>
                        @endif
                    </div>
                </div>

                {{-- Right Column: Stats & Quick Info --}}
                <div class="space-y-6">
                    {{-- Stats --}}
                    <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 p-5 sm:p-6">
                        <h3 class="text-sm font-black text-[#1B3B36] dark:text-white uppercase tracking-wider">Stats</h3>
                        <div class="space-y-3 mt-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-neutral-600 dark:text-neutral-400">Reviews</span>
                                <span class="text-sm font-bold text-neutral-900 dark:text-white">{{ $user->reviews->count() ?? 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between border-t border-gray-100 dark:border-neutral-800 pt-2">
                                <span class="text-sm text-neutral-600 dark:text-neutral-400">Rating</span>
                                <span class="text-sm font-bold text-yellow-500">{{ number_format($user->average_rating ?? 0, 1) }} ★</span>
                            </div>
                            @if($user->is_sitter)
                            <div class="flex items-center justify-between border-t border-gray-100 dark:border-neutral-800 pt-2">
                                <span class="text-sm text-neutral-600 dark:text-neutral-400">Level</span>
                                <span class="text-sm font-bold text-primary">{{ $user->sitter_level }}</span>
                            </div>
                            @endif
                            <div class="flex items-center justify-between border-t border-gray-100 dark:border-neutral-800 pt-2">
                                <span class="text-sm text-neutral-600 dark:text-neutral-400">Member Since</span>
                                <span class="text-sm font-bold text-neutral-900 dark:text-white">{{ $user->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Contact Info --}}
                    <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 p-5 sm:p-6">
                        <h3 class="text-sm font-black text-[#1B3B36] dark:text-white uppercase tracking-wider">Contact</h3>
                        <div class="space-y-2 mt-3">
                            @if($user->contact_number)
                            <div class="flex items-center gap-2 text-sm text-neutral-600 dark:text-neutral-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                {{ $user->contact_number }}
                            </div>
                            @endif
                            @if($user->address)
                            <div class="flex items-start gap-2 text-sm text-neutral-600 dark:text-neutral-400">
                                <svg class="w-4 h-4 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ $user->address }}
                            </div>
                            @endif
                            <div class="flex items-center gap-2 text-sm text-neutral-600 dark:text-neutral-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                {{ $user->email }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>