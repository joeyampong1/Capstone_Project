<x-app-layout>
    <!-- ========================================== -->
    <!-- HEADER SLOT                               -->
    <!-- ========================================== -->
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <!-- Search Icon -->
            <svg class="w-12 h-12 text-[#1B3B36] dark:text-white"
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24" 
                stroke-width="2.5" 
                stroke-linecap="round" 
                stroke-linejoin="round">
                <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <div>
                <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                    Find a Sitter
                </h1>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                    Browse trusted pet sitters in your area.
                </p>
            </div>
        </div>
    </x-slot>

    <!-- ========================================== -->
    <!-- MAIN CONTENT                              -->
    <!-- ========================================== -->
    <div class="py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200">

        <!-- ========================================== -->
        <!-- HERO + SEARCH BAR                         -->
        <!-- ========================================== -->
        <div class="px-12 sm:px-16 lg:px-24 pt-6 pb-12">
            <div class="relative rounded-[2.5rem] overflow-hidden min-h-[420px] sm:min-h-[440px] lg:min-h-[480px] flex items-center px-6 sm:px-10 lg:px-16">
                
                <!-- Background Image -->
                <img src="{{ asset('assets/image/animie_findsitter_bg.png') }}" 
                     alt="" 
                     class="absolute inset-0 w-full h-full object-cover object-center" 
                     style="object-position: center 15%;">
                
                <!-- Gradient Overlay -->
                <div class="absolute inset-0 bg-gradient-to-r from-white/80 via-white/40 to-transparent dark:from-neutral-950/80 dark:via-neutral-950/40"></div>
                
                <!-- Hero Content -->
                <div class="max-w-xl relative z-10">
                    <div class="inline-flex items-center gap-1.5 bg-[#1B3B36]/5 text-[#1B3B36] text-xs font-bold px-3 py-1.5 rounded-full mb-6">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                        </svg>
                        Trusted pet care at home
                    </div>
                    <h1 class="text-4xl sm:text-5xl font-black text-[#1B3B36] tracking-tight leading-[1.1] mb-4">
                        Find Your Pet's <br><span class="text-primary">Perfect Nanny</span>
                    </h1>
                    <p class="text-neutral-600 text-sm font-medium max-w-sm mb-8 leading-relaxed">
                        Connect with verified, nearby sitters who care for your fur babies in your own home. No drop-offs needed.
                    </p>

                    <!-- Search Bar -->
                    <div class="bg-white p-2.5 rounded-2xl shadow-xl shadow-neutral-200/50 border border-neutral-100 flex flex-col sm:flex-row items-center gap-3 max-w-2xl w-full overflow-hidden">
                        
                        <!-- Location Input -->
                        <div class="flex items-center gap-2 pl-2 flex-1 w-full sm:w-auto border-b sm:border-b-0 sm:border-r border-neutral-100 pb-2 sm:pb-0">
                            <svg class="w-5 h-5 text-neutral-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <input type="text" placeholder="Enter your location..." 
                                   class="w-full bg-transparent text-sm font-medium outline-none text-neutral-700 placeholder-neutral-400 rounded-xl px-2 py-1.5 focus:ring-2 focus:ring-primary/20 focus:bg-white/50 transition">
                        </div>

                        <!-- Food Arrangement Dropdown -->
                        <div class="flex items-center gap-2 flex-1 w-full sm:w-auto border-b sm:border-b-0 sm:border-r border-neutral-100 pb-2 sm:pb-0">
                            <svg class="w-5 h-5 text-neutral-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <select class="w-full bg-transparent text-sm font-bold text-neutral-700 outline-none appearance-none cursor-pointer rounded-xl px-2 py-1.5 focus:ring-2 focus:ring-primary/20 focus:bg-white/50 transition">
                                <option>Any arrangement</option>
                                <option>Owner provides food</option>
                                <option>Sitter provides food</option>
                            </select>
                        </div>

                        <!-- Level Dropdown -->
                        <div class="flex items-center gap-2 flex-1 w-full sm:w-auto border-b sm:border-b-0 sm:border-r border-neutral-100 pb-2 sm:pb-0">
                            <svg class="w-5 h-5 text-neutral-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <select class="w-full bg-transparent text-sm font-bold text-neutral-700 outline-none appearance-none cursor-pointer rounded-xl px-2 py-1.5 focus:ring-2 focus:ring-primary/20 focus:bg-white/50 transition">
                                <option>Any Level</option>
                                <option>Level 1</option>
                                <option>Level 2</option>
                                <option>Level 3</option>
                            </select>
                        </div>

                        <!-- Search Button -->
                        <button class="bg-primary hover:bg-primary-600 text-white text-sm font-bold px-6 py-3 rounded-xl flex items-center gap-1.5 transition-all shadow-md shadow-primary/20 w-full sm:w-auto justify-center flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Search
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TRUST BADGES ROW                          -->
        <!-- ========================================== -->
        <div class="px-12 sm:px-16 lg:px-24 py-6 border-b border-neutral-100 bg-white dark:bg-neutral-900 dark:border-neutral-800">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                
                <!-- Badge 1: ID Verified -->
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-950/30 flex items-center justify-center text-amber-600 font-bold border border-amber-100 dark:border-amber-800">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-extrabold text-neutral-800 dark:text-white">ID Verified Sitters</h4>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 font-medium">Government ID validation</p>
                    </div>
                </div>

                <!-- Badge 2: Rated & Reviewed -->
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-950/30 flex items-center justify-center border border-amber-100 dark:border-amber-800">
                        <svg class="w-6 h-6 text-yellow-400 fill-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-extrabold text-neutral-800 dark:text-white">Rated and Reviewed</h4>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 font-medium">Community-driven trust</p>
                    </div>
                </div>

                <!-- Badge 3: Easy Booking -->
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-950/30 flex items-center justify-center text-amber-600 font-bold border border-amber-100 dark:border-amber-800">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-extrabold text-neutral-800 dark:text-white">Easy Booking</h4>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 font-medium">Check availability instantly</p>
                    </div>
                </div>

                <!-- Badge 4: Flexible Food -->
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-950/30 flex items-center justify-center text-amber-600 font-bold border border-amber-100 dark:border-amber-800">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-extrabold text-neutral-800 dark:text-white">Flexible Food</h4>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 font-medium">Your choice, your budget</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- ========================================== -->
        <!-- RECOMMENDED SITTERS GRID - SCROLLABLE     -->
        <!-- ========================================== -->
        <div class="px-12 sm:px-16 lg:px-24 py-12 bg-neutral-50/50 dark:bg-neutral-900/30">
            <div class="flex justify-between items-end mb-8">
                <div>
                    <h2 class="text-2xl font-black text-[#1B3B36] dark:text-white tracking-tight">Recommended Sitters</h2>
                    <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400 mt-1">Ranked by matching algorithm: location, food preference, and rating</p>
                </div>
                <span class="text-xs font-bold text-neutral-400 dark:text-neutral-500">10 sitters</span>
            </div>

            <!-- SCROLLABLE CARDS CONTAINER -->
            <div class="max-h-[600px] overflow-y-auto pr-2 pb-2 scrollbar-thin scrollbar-thumb-neutral-300 dark:scrollbar-thumb-neutral-700 scrollbar-track-transparent hover:scrollbar-thumb-neutral-400 dark:hover:scrollbar-thumb-neutral-600">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    
                    <!-- SITTER CARD 1: Maria Santos (Level 2 - Silver) -->
                    <a href="{{ route('owner.sitter.profile', ['id' => 1]) }}" class="block group">
                        <div class="bg-white dark:bg-neutral-900 rounded-[2rem] border border-neutral-200/60 dark:border-neutral-700 p-3 shadow-sm hover:shadow-md transition-all flex flex-col justify-between h-full">
                            <div>
                                <div class="relative h-[220px] rounded-[1.5rem] overflow-hidden bg-neutral-100 dark:bg-neutral-800 mb-4">
                                    <img src="{{ asset('assets/images/sitter-maria.jpg') }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="Maria Santos">
                                    
                                    <div class="absolute top-3 left-3 bg-[#059669] text-white text-[10px] font-extrabold px-2 py-1 rounded-md flex items-center gap-1 shadow-sm">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                        Verified
                                    </div>
                                    <div class="absolute top-3 right-3 bg-[#D1FAE5] text-[#065F46] text-[10px] font-extrabold px-2 py-1 rounded-md shadow-sm">
                                        90% match
                                    </div>

                                    <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/80 via-black/40 to-transparent pt-12 text-white">
                                        <h3 class="text-lg font-black tracking-tight leading-none">Maria Santos</h3>
                                        <p class="text-xs font-medium opacity-80 mt-1.5 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            Makati City
                                        </p>
                                    </div>
                                </div>

                                <div class="px-2 flex justify-between items-center mb-4">
                                    <div class="flex items-center gap-1 text-sm font-bold text-neutral-800 dark:text-white">
                                        <svg class="w-4 h-4 text-yellow-400 fill-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        <svg class="w-4 h-4 text-yellow-400 fill-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        <svg class="w-4 h-4 text-yellow-400 fill-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        <svg class="w-4 h-4 text-yellow-400 fill-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        <svg class="w-4 h-4 text-yellow-400 fill-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        <span class="ml-1 text-neutral-800 dark:text-white">4.8</span>
                                    </div>
                                    <span class="text-xs font-bold text-neutral-400 dark:text-neutral-500">24 reviews</span>
                                </div>

                                <!-- Food option row with Level Badge -->
                                <div class="px-2 text-xs font-bold text-neutral-500 dark:text-neutral-400 flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                        Flexible food options
                                    </div>
                                    <span class="bg-gray-300 text-gray-800 text-[9px] font-black px-2 py-0.5 rounded-md uppercase tracking-wide inline-flex items-center gap-1">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                                            <circle cx="12" cy="12" r="10" fill="#D1D5DB" stroke="#9CA3AF" stroke-width="1.5"/>
                                            <text x="12" y="15.5" text-anchor="middle" font-size="13" font-weight="bold" fill="#1F2937">2</text>
                                            <path d="M12 2L8 6v4h8V6l-4-4z" fill="#9CA3AF" opacity="0.3"/>
                                        </svg>
                                        Level 2
                                    </span>
                                </div>
                            </div>

                            <div class="border-t border-neutral-100 dark:border-neutral-800 pt-4 px-2 pb-2 flex justify-between items-center">
                                <span class="text-xs font-bold text-neutral-400 dark:text-neutral-500">Daily rate</span>
                                <span class="text-xl font-black text-primary">₱350</span>
                            </div>
                        </div>
                    </a>

                    <!-- SITTER CARD 2: John Dela Cruz (Level 1 - Bronze) -->
                    <a href="{{ route('owner.sitter.profile', ['id' => 2]) }}" class="block group">
                        <div class="bg-white dark:bg-neutral-900 rounded-[2rem] border border-neutral-200/60 dark:border-neutral-700 p-3 shadow-sm hover:shadow-md transition-all flex flex-col justify-between h-full">
                            <div>
                                <div class="relative h-[220px] rounded-[1.5rem] overflow-hidden bg-neutral-100 dark:bg-neutral-800 mb-4">
                                    <img src="{{ asset('assets/images/sitter-john.jpg') }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="John Dela Cruz">
                                    
                                    <div class="absolute top-3 left-3 bg-[#059669] text-white text-[10px] font-extrabold px-2 py-1 rounded-md flex items-center gap-1 shadow-sm">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                        Verified
                                    </div>
                                    <div class="absolute top-3 right-3 bg-[#FEF3C7] text-[#92400E] text-[10px] font-extrabold px-2 py-1 rounded-md shadow-sm">
                                        78% match
                                    </div>

                                    <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/80 via-black/40 to-transparent pt-12 text-white">
                                        <h3 class="text-lg font-black tracking-tight leading-none">John Dela Cruz</h3>
                                        <p class="text-xs font-medium opacity-80 mt-1.5 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            Quezon City
                                        </p>
                                    </div>
                                </div>

                                <div class="px-2 flex justify-between items-center mb-4">
                                    <div class="flex items-center gap-1 text-sm font-bold text-neutral-800 dark:text-white">
                                        <svg class="w-4 h-4 text-yellow-400 fill-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        <svg class="w-4 h-4 text-yellow-400 fill-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        <svg class="w-4 h-4 text-yellow-400 fill-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        <svg class="w-4 h-4 text-yellow-400 fill-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        <span class="ml-1 text-neutral-800 dark:text-white">4.2</span>
                                    </div>
                                    <span class="text-xs font-bold text-neutral-400 dark:text-neutral-500">18 reviews</span>
                                </div>

                                <!-- Food option row with Level Badge -->
                                <div class="px-2 text-xs font-bold text-neutral-500 dark:text-neutral-400 flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                        Owner provides food
                                    </div>
                                    <span class="bg-amber-600 text-white text-[9px] font-black px-2 py-0.5 rounded-md uppercase tracking-wide inline-flex items-center gap-1">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                                            <circle cx="12" cy="12" r="10" fill="#D97706" stroke="#F59E0B" stroke-width="1.5"/>
                                            <text x="12" y="15.5" text-anchor="middle" font-size="13" font-weight="bold" fill="white">1</text>
                                            <path d="M12 2L8 6v4h8V6l-4-4z" fill="#F59E0B" opacity="0.3"/>
                                        </svg>
                                        Level 1
                                    </span>
                                </div>
                            </div>

                            <div class="border-t border-neutral-100 dark:border-neutral-800 pt-4 px-2 pb-2 flex justify-between items-center">
                                <span class="text-xs font-bold text-neutral-400 dark:text-neutral-500">Daily rate</span>
                                <span class="text-xl font-black text-primary">₱280</span>
                            </div>
                        </div>
                    </a>

                    <!-- SITTER CARD 3: Kristine Mendoza (Level 3 - Gold) -->
                    <a href="{{ route('owner.sitter.profile', ['id' => 3]) }}" class="block group">
                        <div class="bg-white dark:bg-neutral-900 rounded-[2rem] border border-neutral-200/60 dark:border-neutral-700 p-3 shadow-sm hover:shadow-md transition-all flex flex-col justify-between h-full">
                            <div>
                                <div class="relative h-[220px] rounded-[1.5rem] overflow-hidden bg-neutral-100 dark:bg-neutral-800 mb-4">
                                    <img src="{{ asset('assets/images/sitter-kristine.jpg') }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="Kristine Mendoza">
                                    
                                    <div class="absolute top-3 left-3 bg-[#059669] text-white text-[10px] font-extrabold px-2 py-1 rounded-md flex items-center gap-1 shadow-sm">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                        Verified
                                    </div>
                                    <div class="absolute top-3 right-3 bg-[#D1FAE5] text-[#065F46] text-[10px] font-extrabold px-2 py-1 rounded-md shadow-sm">
                                        95% match
                                    </div>

                                    <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/80 via-black/40 to-transparent pt-12 text-white">
                                        <h3 class="text-lg font-black tracking-tight leading-none">Kristine Mendoza</h3>
                                        <p class="text-xs font-medium opacity-80 mt-1.5 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            Pasig City
                                        </p>
                                    </div>
                                </div>

                                <div class="px-2 flex justify-between items-center mb-4">
                                    <div class="flex items-center gap-1 text-sm font-bold text-neutral-800 dark:text-white">
                                        <svg class="w-4 h-4 text-yellow-400 fill-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        <svg class="w-4 h-4 text-yellow-400 fill-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        <svg class="w-4 h-4 text-yellow-400 fill-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        <svg class="w-4 h-4 text-yellow-400 fill-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        <svg class="w-4 h-4 text-yellow-400 fill-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        <span class="ml-1 text-neutral-800 dark:text-white">4.9</span>
                                    </div>
                                    <span class="text-xs font-bold text-neutral-400 dark:text-neutral-500">54 reviews</span>
                                </div>

                                <!-- Food option row with Level Badge -->
                                <div class="px-2 text-xs font-bold text-neutral-500 dark:text-neutral-400 flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                        Sitter provides food
                                    </div>
                                    <span class="bg-yellow-400 text-yellow-900 text-[9px] font-black px-2 py-0.5 rounded-md uppercase tracking-wide inline-flex items-center gap-1">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                                            <circle cx="12" cy="12" r="10" fill="#FBBF24" stroke="#F59E0B" stroke-width="1.5"/>
                                            <text x="12" y="15.5" text-anchor="middle" font-size="13" font-weight="bold" fill="#92400E">3</text>
                                            <path d="M12 2L8 6v4h8V6l-4-4z" fill="#F59E0B" opacity="0.3"/>
                                        </svg>
                                        Level 3
                                    </span>
                                </div>
                            </div>

                            <div class="border-t border-neutral-100 dark:border-neutral-800 pt-4 px-2 pb-2 flex justify-between items-center">
                                <span class="text-xs font-bold text-neutral-400 dark:text-neutral-500">Daily rate</span>
                                <span class="text-xl font-black text-primary">₱420</span>
                            </div>
                        </div>
                    </a>

                </div>
            </div>
        </div>

    </div>
</x-app-layout>