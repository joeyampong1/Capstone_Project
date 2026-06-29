<x-app-layout>
    <!-- ========================================== -->
    <!-- HEADER SLOT                               -->
    <!-- ========================================== -->
    <x-slot name="header">
        <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
            Apply as a Sitter
        </h1>
        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">Complete your sitter profile to start accepting bookings</p>
    </x-slot>

    <!-- ========================================== -->
    <!-- MAIN CONTENT                              -->
    <!-- ========================================== -->
    <div class="py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200">
        <div class="px-12 sm:px-16 lg:px-24 max-w-3xl mx-auto">

            <!-- Current Status (if already applied) -->
            @if(auth()->user()->is_sitter)
                <div class="mb-6 p-4 rounded-xl border 
                    {{ auth()->user()->sitter_status === 'approved' ? 'border-green-500 bg-green-50 dark:bg-green-950/20 dark:border-green-700' : 
                       (auth()->user()->sitter_status === 'pending' ? 'border-yellow-500 bg-yellow-50 dark:bg-yellow-950/20 dark:border-yellow-700' : 
                       'border-red-500 bg-red-50 dark:bg-red-950/20 dark:border-red-700') }}">
                    <div class="flex items-center gap-3">
                        @if(auth()->user()->sitter_status === 'approved')
                            <span class="text-2xl">✅</span>
                            <div>
                                <p class="font-bold text-green-700 dark:text-green-400">Your sitter application is approved!</p>
                                <p class="text-sm text-green-600 dark:text-green-300">You can now accept bookings.</p>
                            </div>
                        @elseif(auth()->user()->sitter_status === 'pending')
                            <span class="text-2xl">⏳</span>
                            <div>
                                <p class="font-bold text-yellow-700 dark:text-yellow-400">Your application is under review.</p>
                                <p class="text-sm text-yellow-600 dark:text-yellow-300">We'll notify you once approved.</p>
                            </div>
                        @elseif(auth()->user()->sitter_status === 'rejected')
                            <span class="text-2xl">❌</span>
                            <div>
                                <p class="font-bold text-red-700 dark:text-red-400">Your application was rejected.</p>
                                <p class="text-sm text-red-600 dark:text-red-300">Please update your information and reapply.</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Application Form -->
            <div class="bg-white dark:bg-neutral-900 rounded-3xl shadow-xl border border-gray-200/60 dark:border-neutral-800 p-6 sm:p-8">
                <h2 class="text-lg font-black text-[#1B3B36] dark:text-white mb-6">Sitter Details</h2>

                <form method="POST" action="{{ route('sitter.application.store') }}" class="space-y-6">
                    @csrf

                    <!-- Bio -->
                    <div>
                        <label for="bio" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                            About You
                        </label>
                        <textarea id="bio" name="bio" rows="4" 
                            placeholder="Tell pet owners why you'd be a great sitter..."
                            class="mt-1 block w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium">{{ old('bio', auth()->user()->bio ?? '') }}</textarea>
                        <p class="mt-1 text-xs text-neutral-400">Share your experience, availability, and passion for pets.</p>
                        <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('bio')" />
                    </div>

                    <!-- Rate Per Visit -->
                    <div>
                        <label for="rate_per_visit" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                            Rate per Visit (₱)
                        </label>
                        <input id="rate_per_visit" name="rate_per_visit" type="number" step="0.01" min="0"
                            value="{{ old('rate_per_visit', auth()->user()->rate_per_visit ?? '') }}"
                            placeholder="e.g. 250"
                            class="mt-1 block w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium">
                        <p class="mt-1 text-xs text-neutral-400">Recommended: ₱150 – ₱500 per visit.</p>
                        <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('rate_per_visit')" />
                    </div>

                    <!-- Pet Types -->
                    <div>
                        <label for="pet_types" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                            Types of Pets You Accept
                        </label>
                        <input id="pet_types" name="pet_types" type="text"
                            value="{{ old('pet_types', auth()->user()->pet_types ?? '') }}"
                            placeholder="e.g. dogs, cats, birds"
                            class="mt-1 block w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium">
                        <p class="mt-1 text-xs text-neutral-400">Separate with commas (e.g., dogs, cats, rabbits).</p>
                        <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('pet_types')" />
                    </div>

                    <!-- Food Preference -->
                    <div>
                        <label for="food_preference" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                            Food Arrangement
                        </label>
                        <select id="food_preference" name="food_preference"
                            class="mt-1 block w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium">
                            <option value="owner_provides" {{ old('food_preference', auth()->user()->food_preference ?? '') == 'owner_provides' ? 'selected' : '' }}>Owner provides food</option>
                            <option value="sitter_provides" {{ old('food_preference', auth()->user()->food_preference ?? '') == 'sitter_provides' ? 'selected' : '' }}>Sitter provides food (+₱100/visit)</option>
                            <option value="flexible" {{ old('food_preference', auth()->user()->food_preference ?? '') == 'flexible' ? 'selected' : '' }}>Flexible (both options)</option>
                        </select>
                        <p class="mt-1 text-xs text-neutral-400">Choose your preferred food arrangement for pets.</p>
                        <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('food_preference')" />
                    </div>

                    <!-- Can Provide Food (checkbox) -->
                    <div class="flex items-center gap-3">
                        <input id="can_provide_food" name="can_provide_food" type="checkbox" value="1"
                            {{ old('can_provide_food', auth()->user()->can_provide_food ?? false) ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-gray-300 dark:border-neutral-800 text-primary focus:ring-primary/30 dark:bg-neutral-950 focus:ring-offset-0 transition cursor-pointer">
                        <label for="can_provide_food" class="text-sm font-bold text-gray-700 dark:text-gray-300">
                            I can provide food for pets
                        </label>
                    </div>
                    <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('can_provide_food')" />

                    <!-- Submit -->
                    <div class="pt-4">
                        <button type="submit" class="w-full flex justify-center items-center px-4 py-3.5 bg-primary hover:bg-primary-600 text-white text-sm font-black rounded-xl shadow-md hover:shadow-lg active:scale-[0.99] transition-all duration-150">
                            {{ auth()->user()->is_sitter ? 'Update Application' : 'Submit Application' }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Back to Dashboard -->
            <div class="mt-6 text-center">
                <a href="{{ route('dashboard') }}" class="text-sm font-bold text-primary hover:underline">
                    ← Back to Dashboard
                </a>
            </div>

        </div>
    </div>
</x-app-layout>