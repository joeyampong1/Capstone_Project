<x-app-layout>
    <!-- ========================================== -->
    <!-- HEADER SLOT – sticky                       -->
    <!-- ========================================== -->
    <x-slot name="header">
        <div class="sticky top-0 z-10 bg-white dark:bg-neutral-900 flex items-center gap-3">
            <a href="{{ route('dashboard') }}" 
            class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                    My Pets
                </h1>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">Manage your pet profiles</p>
            </div>
        </div>
    </x-slot>

    <!-- ========================================== -->
    <!-- MAIN CONTENT                              -->
    <!-- ========================================== -->
    <div class="bg-white dark:bg-neutral-950 antialiased text-neutral-800 dark:text-neutral-200">
        <div class="max-w-4xl mx-auto px-12 sm:px-16 lg:px-24 py-6">

            <!-- ========================================== -->
            <!-- PET GRID                                  -->
            <!-- ========================================== -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                <!-- ========================================== -->
                <!-- PET CARD 1: Mingming                      -->
                <!-- ========================================== -->
                <div class="bg-white dark:bg-neutral-900 rounded-3xl shadow-sm border border-gray-100 dark:border-neutral-800 overflow-hidden hover:shadow-md transition">
                    <div class="relative h-40 bg-gradient-to-r from-primary-100/30 to-accent-100/30 dark:from-primary-900/20 dark:to-accent-900/20">
                        <div class="absolute inset-0 flex items-center justify-center text-5xl">🐱</div>
                    </div>
                    <div class="p-4">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="font-black text-[#1B3B36] dark:text-white text-lg">Mingming</h3>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Cat • 2 yrs • Small</p>
                            </div>
                            <div class="flex items-center gap-1">
                                <!-- Edit button -->
                                <button class="p-1.5 rounded-lg text-neutral-400 hover:text-primary hover:bg-primary/10 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <!-- Delete button -->
                                <button class="p-1.5 rounded-lg text-neutral-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-1.5 mt-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">Calm</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-green-50 text-green-600 dark:bg-green-900/30 dark:text-green-400">Owner Provides</span>
                        </div>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-2 line-clamp-2">Indoor cat. Feed wet food in the morning.</p>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- PET CARD 2: Choco                         -->
                <!-- ========================================== -->
                <div class="bg-white dark:bg-neutral-900 rounded-3xl shadow-sm border border-gray-100 dark:border-neutral-800 overflow-hidden hover:shadow-md transition">
                    <div class="relative h-40 bg-gradient-to-r from-primary-100/30 to-accent-100/30 dark:from-primary-900/20 dark:to-accent-900/20">
                        <div class="absolute inset-0 flex items-center justify-center text-5xl">🐶</div>
                    </div>
                    <div class="p-4">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="font-black text-[#1B3B36] dark:text-white text-lg">Choco</h3>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Shih Tzu • 5 yrs • Small</p>
                            </div>
                            <div class="flex items-center gap-1">
                                <button class="p-1.5 rounded-lg text-neutral-400 hover:text-primary hover:bg-primary/10 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button class="p-1.5 rounded-lg text-neutral-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-1.5 mt-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">Friendly</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-purple-50 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400">Neutered</span>
                        </div>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-2 line-clamp-2">Has mild allergies. Avoid chicken-based food.</p>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- PET CARD 3: Bantay                        -->
                <!-- ========================================== -->
                <div class="bg-white dark:bg-neutral-900 rounded-3xl shadow-sm border border-gray-100 dark:border-neutral-800 overflow-hidden hover:shadow-md transition">
                    <div class="relative h-40 bg-gradient-to-r from-primary-100/30 to-accent-100/30 dark:from-primary-900/20 dark:to-accent-900/20">
                        <div class="absolute inset-0 flex items-center justify-center text-5xl">🐕</div>
                    </div>
                    <div class="p-4">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="font-black text-[#1B3B36] dark:text-white text-lg">Bantay</h3>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Aspin • 3 yrs • Medium</p>
                            </div>
                            <div class="flex items-center gap-1">
                                <button class="p-1.5 rounded-lg text-neutral-400 hover:text-primary hover:bg-primary/10 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button class="p-1.5 rounded-lg text-neutral-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-1.5 mt-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">Playful</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-green-50 text-green-600 dark:bg-green-900/30 dark:text-green-400">Owner Provides</span>
                        </div>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-2 line-clamp-2">Loves belly rubs. Needs walk twice a day.</p>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- ADD NEW PET CARD (clickable)              -->
                <!-- ========================================== -->
                <a href="{{ route('mypets.create') }}" 
                   class="bg-white dark:bg-neutral-900 rounded-3xl border-2 border-dashed border-gray-300 dark:border-neutral-700 hover:border-primary hover:bg-primary/5 transition flex flex-col items-center justify-center min-h-[240px] p-6 group">
                    <div class="w-16 h-16 rounded-full bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center text-neutral-400 group-hover:bg-primary/10 group-hover:text-primary transition">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <h3 class="mt-3 font-bold text-neutral-600 dark:text-neutral-400 group-hover:text-primary transition">Add New Pet</h3>
                    <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-0.5">Create a new pet profile</p>
                </a>

            </div>

            <!-- ========================================== -->
            <!-- EMPTY STATE (hidden unless needed)        -->
            <!-- ========================================== -->
            @if(false) <!-- Toggle to true to see empty state -->
            <div class="bg-white dark:bg-neutral-900 rounded-3xl shadow-xl border border-gray-200/60 dark:border-neutral-800 p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-neutral-300 dark:text-neutral-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                </svg>
                <h3 class="text-xl font-black text-[#1B3B36] dark:text-white">No pets yet</h3>
                <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">Add your first pet to get started.</p>
                <a href="{{ route('mypets.create') }}" class="inline-block mt-4 px-6 py-3 bg-primary text-white font-bold text-sm rounded-xl hover:bg-primary-600 transition">
                    Add Your First Pet
                </a>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>