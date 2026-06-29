<x-app-layout>
    <!-- ========================================== -->
    <!-- HEADER SLOT – fixed (below nav)           -->
    <!-- ========================================== -->
    <x-slot name="header">
        <div style="position: fixed; top: 64px; left: 0; right: 0; z-index: 40; background: white; border-bottom: 1px solid #e5e7eb;" class="dark:bg-neutral-900 dark:border-neutral-800">
            <div class="w-full mx-auto px-12 sm:px-16 lg:px-24">
                <div class="flex items-center gap-3 py-4">
                    <a href="{{ route('mypets.index') }}" 
                    class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <div>
                        <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                            Add Pet
                        </h1>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">Create a new pet profile</p>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <!-- ========================================== -->
    <!-- MAIN CONTENT                              -->
    <!-- ========================================== -->
    <div class="py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200">
        <div class="max-w-2xl mx-auto px-12 sm:px-16 lg:px-24">

            <!-- ========================================== -->
            <!-- FORM CARD                                 -->
            <!-- ========================================== -->
            <div class="bg-white dark:bg-neutral-900 rounded-3xl shadow-xl border border-gray-200/60 dark:border-neutral-800 p-6 sm:p-8">

                <form method="POST" action="#" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- ========================================== -->
                    <!-- PET PHOTO UPLOAD (optional)               -->
                    <!-- ========================================== -->
                    <div>
                        <label class="block text-sm font-bold text-[#1B3B36] dark:text-white mb-2">
                            Pet Photo <span class="text-xs text-neutral-400 font-medium">(optional)</span>
                        </label>
                        <div class="flex items-center gap-4">
                            <!-- Photo preview placeholder -->
                            <div class="w-24 h-24 rounded-2xl bg-neutral-100 dark:bg-neutral-800 border-2 border-dashed border-gray-300 dark:border-neutral-700 flex items-center justify-center text-3xl text-neutral-400 overflow-hidden">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <label for="pet_photo" class="inline-flex items-center gap-2 px-4 py-2.5 bg-neutral-100 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300 font-bold text-sm rounded-xl hover:bg-neutral-200 dark:hover:bg-neutral-700 transition cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Choose Photo
                                </label>
                                <input type="file" id="pet_photo" name="pet_photo" class="hidden" accept="image/*">
                                <p class="text-[10px] text-neutral-400 mt-1">JPG, PNG or WEBP (max 5MB)</p>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 dark:border-neutral-800"></div>

                    <!-- ========================================== -->
                    <!-- PET BASIC INFO                           -->
                    <!-- ========================================== -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-bold text-[#1B3B36] dark:text-white mb-1.5">
                                Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" placeholder="Pet name" 
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-950 text-sm text-neutral-800 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                        </div>

                        <!-- Type -->
                        <div>
                            <label for="type" class="block text-sm font-bold text-[#1B3B36] dark:text-white mb-1.5">
                                Type <span class="text-red-500">*</span>
                            </label>
                            <select id="type" name="type" 
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-950 text-sm text-neutral-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                                <option value="">Select type</option>
                                <option value="dog">Dog</option>
                                <option value="cat">Cat</option>
                                <option value="bird">Bird</option>
                                <option value="rabbit">Rabbit</option>
                                <option value="hamster">Hamster</option>
                                <option value="fish">Fish</option>
                                <option value="reptile">Reptile</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>

                    <!-- Breed -->
                    <div>
                        <label for="breed" class="block text-sm font-bold text-[#1B3B36] dark:text-white mb-1.5">
                            Breed <span class="text-xs text-neutral-400 font-medium">(optional)</span>
                        </label>
                        <input type="text" id="breed" name="breed" placeholder="e.g., Shih Tzu, Persian" 
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-950 text-sm text-neutral-800 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Age -->
                        <div>
                            <label for="age" class="block text-sm font-bold text-[#1B3B36] dark:text-white mb-1.5">
                                Age <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="age" name="age" placeholder="Age" min="0" max="30"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-950 text-sm text-neutral-800 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                        </div>

                        <!-- Size -->
                        <div>
                            <label for="size" class="block text-sm font-bold text-[#1B3B36] dark:text-white mb-1.5">
                                Size <span class="text-red-500">*</span>
                            </label>
                            <select id="size" name="size" 
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-950 text-sm text-neutral-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                                <option value="">Select size</option>
                                <option value="small">Small</option>
                                <option value="medium">Medium</option>
                                <option value="large">Large</option>
                            </select>
                        </div>

                        <!-- Temperament -->
                        <div>
                            <label for="temperament" class="block text-sm font-bold text-[#1B3B36] dark:text-white mb-1.5">
                                Temperament <span class="text-red-500">*</span>
                            </label>
                            <select id="temperament" name="temperament" 
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-950 text-sm text-neutral-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                                <option value="">Select temperament</option>
                                <option value="calm">Calm</option>
                                <option value="friendly">Friendly</option>
                                <option value="playful">Playful</option>
                                <option value="energetic">Energetic</option>
                                <option value="shy">Shy</option>
                                <option value="independent">Independent</option>
                            </select>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- SPECIAL INSTRUCTIONS                     -->
                    <!-- ========================================== -->
                    <div>
                        <label for="special_instructions" class="block text-sm font-bold text-[#1B3B36] dark:text-white mb-1.5">
                            Special Instructions <span class="text-xs text-neutral-400 font-medium">(optional)</span>
                        </label>
                        <textarea id="special_instructions" name="special_instructions" rows="4" 
                                  placeholder="Allergies, medications, habits, feeding schedule, favorite toys..."
                                  class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-950 text-sm text-neutral-800 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition resize-none"></textarea>
                    </div>

                    <!-- ========================================== -->
                    <!-- FORM ACTIONS                             -->
                    <!-- ========================================== -->
                    <div class="flex items-center gap-3 pt-4 border-t border-gray-100 dark:border-neutral-800">
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-primary hover:bg-primary-600 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Add Pet
                        </button>
                        <a href="{{ route('mypets.index') }}" 
                           class="inline-flex items-center px-6 py-3 border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-sm rounded-xl hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                            Cancel
                        </a>
                    </div>

                </form>

            </div>

        </div>
    </div>
</x-app-layout>