<x-app-layout>
    <!-- ========================================== -->
    <!-- HEADER SLOT                               -->
    <!-- ========================================== -->
    <x-slot name="header">
        <div x-data="{ role: 'owner' }" class="flex items-center justify-between w-full">
            <!-- Left: Home Icon + Title -->
            <div class="flex items-center gap-3">
                <svg class="w-14 h-14 text-[#1B3B36] dark:text-white" 
                    fill="none" 
                    stroke="currentColor" 
                    viewBox="0 0 24 24" 
                    stroke-width="2.5" 
                    stroke-linecap="round" 
                    stroke-linejoin="round">
                    <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2z"/>
                </svg>
                <div>
                    <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                        Home
                    </h1>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                        Viewing main landing workspace presentation flow.
                    </p>
                </div>
            </div>

            <!-- Right: Role Switch (Owner / Sitter) -->
            <div class="flex items-center gap-1 p-1 bg-neutral-100 dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700">
                <button @click="role = 'owner'" 
                        :class="role === 'owner' ? 'bg-primary text-white shadow-md' : 'text-neutral-600 dark:text-neutral-400 hover:bg-white/50 dark:hover:bg-neutral-700'"
                        class="px-4 py-2 text-xs font-bold rounded-lg transition">
                    Pet Owner
                </button>
                <button @click="role = 'sitter'" 
                        :class="role === 'sitter' ? 'bg-primary text-white shadow-md' : 'text-neutral-600 dark:text-neutral-400 hover:bg-white/50 dark:hover:bg-neutral-700'"
                        class="px-4 py-2 text-xs font-bold rounded-lg transition">
                    Pet Sitter
                </button>
            </div>
        </div>
    </x-slot>

    <!-- ========================================== -->
    <!-- MAIN CONTENT                              -->
    <!-- ========================================== -->
    <div x-data="{ role: 'owner' }" class="py-12 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200">
        <div class="w-full mx-auto px-12 sm:px-16 lg:px-24 space-y-16">
            
            <!-- ========================================== -->
            <!-- CARD 1: HERO VIEWPORT                     -->
            <!-- ========================================== -->
            <div class="relative overflow-hidden bg-gradient-to-br from-[#FFF5F1] via-[#FFF9F6] to-[#F7FAF9] dark:from-neutral-900 dark:via-neutral-900/60 dark:to-transparent rounded-[32px] border border-[#FDE3D8]/40 dark:border-neutral-800 p-8 sm:p-14 lg:p-20 flex flex-col items-start justify-center min-h-[480px]">
                <div class="max-w-2xl space-y-6 relative z-10">
                    
                    <!-- Tag label -->
                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-extrabold bg-[#FCECE6] dark:bg-orange-950/40 text-[#F17743]">
                        <span class="text-sm">🐾</span> Home-based pet care
                    </span>
                    
                    <!-- Main heading -->
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-[#1B3B36] dark:text-white tracking-tight leading-[1.05]">
                        A Nanny for Your <span class="text-primary">Furry Family</span>
                    </h1>
                    
                    <!-- Description -->
                    <p class="text-sm sm:text-base text-neutral-500 dark:text-neutral-400 leading-relaxed max-w-xl font-medium">
                        Connect with trusted pet sitters who care for your pets at home. Drop-in visits, photo updates, and complete peace of mind.
                    </p>
                    
                    <!-- CTA Buttons -->
                    <div class="flex flex-wrap items-center gap-4 pt-4">
                        <a href="#" class="inline-flex items-center gap-2 px-8 py-4 bg-primary hover:bg-primary-600 text-white font-bold text-sm rounded-xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5">
                            <span>Find a Sitter</span>
                            <span class="inline-block transition-transform group-hover:translate-x-1">→</span>
                        </a>
                        <a href="#" class="inline-flex items-center px-8 py-4 bg-white dark:bg-neutral-800 border-2 border-gray-200 dark:border-neutral-700 text-gray-700 dark:text-gray-200 font-bold text-sm rounded-xl hover:bg-gray-50 dark:hover:bg-neutral-700 transition transform hover:-translate-y-0.5">
                            Become a Sitter
                        </a>
                    </div>
                </div>

                <!-- Glow effect -->
                <div class="absolute right-0 top-0 bottom-0 w-1/3 bg-gradient-to-l from-[#FCECE6]/30 via-transparent to-transparent pointer-events-none hidden md:block"></div>
            </div>

            <!-- ========================================== -->
            <!-- CARD 2: WHY PETNANNY                       -->
            <!-- ========================================== -->
            <div class="space-y-12 pt-4">
                <div class="space-y-2 text-center">
                    <h2 class="text-3xl font-black text-[#1B3B36] dark:text-white tracking-tight">
                        Why <span class="text-primary">PetNanny</span>?
                    </h2>
                    <p class="text-sm text-neutral-400 font-medium">
                        Everything your pet needs, right at home.
                    </p>
                </div>
                
                <!-- 4 Column Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Card 1: Find Trusted Sitters -->
                    <div class="bg-white dark:bg-neutral-900 border border-neutral-100 dark:border-neutral-800/80 p-6 rounded-[24px] flex flex-col items-center text-center space-y-4 shadow-sm hover:shadow-md transition duration-200">
                        <div class="w-12 h-12 bg-[#FFF5F1] dark:bg-neutral-800 rounded-full flex items-center justify-center text-primary">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <h3 class="font-extrabold text-base text-[#1B3B36] dark:text-white tracking-tight">Find Trusted Sitters</h3>
                        <p class="text-xs text-neutral-400 dark:text-neutral-500 leading-relaxed font-medium">Browse verified pet sitters in your area with ratings and reviews.</p>
                    </div>

                    <!-- Card 2: Flexible Scheduling -->
                    <div class="bg-white dark:bg-neutral-900 border border-neutral-100 dark:border-neutral-800/80 p-6 rounded-[24px] flex flex-col items-center text-center space-y-4 shadow-sm hover:shadow-md transition duration-200">
                        <div class="w-12 h-12 bg-[#FFF5F1] dark:bg-neutral-800 rounded-full flex items-center justify-center text-primary">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 002-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <h3 class="font-extrabold text-base text-[#1B3B36] dark:text-white tracking-tight">Flexible Scheduling</h3>
                        <p class="text-xs text-neutral-400 dark:text-neutral-500 leading-relaxed font-medium">Book drop-in visits that fit your schedule. Your pet stays home.</p>
                    </div>

                    <!-- Card 3: Safety First -->
                    <div class="bg-white dark:bg-neutral-900 border border-neutral-100 dark:border-neutral-800/80 p-6 rounded-[24px] flex flex-col items-center text-center space-y-4 shadow-sm hover:shadow-md transition duration-200">
                        <div class="w-12 h-12 bg-[#FFF5F1] dark:bg-neutral-800 rounded-full flex items-center justify-center text-accent">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <h3 class="font-extrabold text-base text-[#1B3B36] dark:text-white tracking-tight">Safety First</h3>
                        <p class="text-xs text-neutral-400 dark:text-neutral-500 leading-relaxed font-medium">ID verification, photo proof per visit, and a protection fund.</p>
                    </div>

                    <!-- Card 4: Home-Based Care -->
                    <div class="bg-white dark:bg-neutral-900 border border-neutral-100 dark:border-neutral-800/80 p-6 rounded-[24px] flex flex-col items-center text-center space-y-4 shadow-sm hover:shadow-md transition duration-200">
                        <div class="w-12 h-12 bg-[#FFF5F1] dark:bg-neutral-800 rounded-full flex items-center justify-center text-accent">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        </div>
                        <h3 class="font-extrabold text-base text-[#1B3B36] dark:text-white tracking-tight">Home-Based Care</h3>
                        <p class="text-xs text-neutral-400 dark:text-neutral-500 leading-relaxed font-medium">Your pet is cared for in the comfort of their own home.</p>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- CARD 3: MY BOOKINGS (switches based on role)-->
            <!-- ========================================== -->
            <div class="bg-[#FCFAF9] dark:bg-neutral-900/40 rounded-[32px] border border-neutral-100 dark:border-neutral-800 p-8 sm:p-14 space-y-8 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="space-y-1">
                        <h2 class="text-3xl font-black text-[#1B3B36] dark:text-white tracking-tight">
                            My <span class="text-primary">Bookings</span>
                        </h2>
                        <p class="text-sm text-neutral-400 font-medium">
                            <span x-show="role === 'owner'">View your booking statuses</span>
                            <span x-show="role === 'sitter'">Manage incoming booking requests</span>
                        </p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-primary/10 text-primary">
                        <span x-show="role === 'owner'">3 active</span>
                        <span x-show="role === 'sitter'">2 pending</span>
                    </span>
                </div>

                <!-- ========================================== -->
                <!-- BOOKING LIST – Owner Mode                  -->
                <!-- ========================================== -->
                <div x-show="role === 'owner'" class="space-y-4">
                    <!-- Booking 1: Pending -->
                    <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-gray-100 dark:border-neutral-800 p-5 flex items-center justify-between hover:shadow-md transition">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-[#1B3B36] dark:text-white">Maria Santos</h4>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">2 visits/day • Makati City</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">Pending</span>
                            <p class="text-[10px] text-neutral-400 mt-0.5">Dec 15, 2026</p>
                        </div>
                    </div>

                    <!-- Booking 2: Confirmed -->
                    <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-gray-100 dark:border-neutral-800 p-5 flex items-center justify-between hover:shadow-md transition">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-[#1B3B36] dark:text-white">Juan Dela Cruz</h4>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">3 visits/day • Quezon City</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">Confirmed</span>
                            <p class="text-[10px] text-neutral-400 mt-0.5">Dec 18, 2026</p>
                        </div>
                    </div>

                    <!-- Booking 3: Completed -->
                    <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-gray-100 dark:border-neutral-800 p-5 flex items-center justify-between hover:shadow-md transition">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-[#1B3B36] dark:text-white">Anna Reyes</h4>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">1 visit/day • Pasig City</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">Completed</span>
                            <p class="text-[10px] text-neutral-400 mt-0.5">Dec 10, 2026</p>
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- BOOKING LIST – Sitter Mode                 -->
                <!-- ========================================== -->
                <div x-show="role === 'sitter'" class="space-y-4">
                    <!-- Incoming Request 1 -->
                    <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-gray-100 dark:border-neutral-800 p-5 hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold">JM</div>
                                <div>
                                    <h4 class="font-bold text-[#1B3B36] dark:text-white">James Mitchell</h4>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">2 visits/day • Dec 20-22</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">Pending</span>
                        </div>
                        <div class="flex items-center gap-3 mt-3 pt-3 border-t border-gray-100 dark:border-neutral-800">
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 flex-1">🐕 1 dog • needs medication</p>
                            <div class="flex items-center gap-2">
                                <button class="px-4 py-1.5 text-xs font-bold bg-green-500 hover:bg-green-600 text-white rounded-lg transition">Accept</button>
                                <button class="px-4 py-1.5 text-xs font-bold border-2 border-red-500 text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-lg transition">Decline</button>
                            </div>
                        </div>
                    </div>

                    <!-- Incoming Request 2 -->
                    <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-gray-100 dark:border-neutral-800 p-5 hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full bg-accent/10 flex items-center justify-center text-accent font-bold">SL</div>
                                <div>
                                    <h4 class="font-bold text-[#1B3B36] dark:text-white">Sarah Lim</h4>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">3 visits/day • Dec 21-23</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">Pending</span>
                        </div>
                        <div class="flex items-center gap-3 mt-3 pt-3 border-t border-gray-100 dark:border-neutral-800">
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 flex-1">🐱 2 cats • indoor only</p>
                            <div class="flex items-center gap-2">
                                <button class="px-4 py-1.5 text-xs font-bold bg-green-500 hover:bg-green-600 text-white rounded-lg transition">Accept</button>
                                <button class="px-4 py-1.5 text-xs font-bold border-2 border-red-500 text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-lg transition">Decline</button>
                            </div>
                        </div>
                    </div>

                    <!-- Empty state for sitter (hidden if there are requests) -->
                    <div x-show="false" class="text-center py-8">
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">No incoming booking requests.</p>
                    </div>
                </div>

                <!-- View All link -->
                <div class="text-center pt-2">
                    <a href="#" class="text-sm text-primary font-semibold hover:underline inline-flex items-center gap-1">
                        View all bookings
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- CARD 4: HOW IT WORKS                      -->
            <!-- ========================================== -->
            <div class="bg-[#FCFAF9] dark:bg-neutral-900/40 rounded-[32px] border border-neutral-100 dark:border-neutral-800 p-8 sm:p-14 space-y-12 shadow-sm">
                <div class="space-y-2 text-center">
                    <h2 class="text-3xl font-black text-[#1B3B36] dark:text-white tracking-tight">
                        How <span class="text-primary">PetNanny</span> Works
                    </h2>
                    <p class="text-sm text-neutral-400 font-medium">
                        Three simple steps to happy pets.
                    </p>
                </div>

                <!-- 3 Steps Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-10 md:gap-6 relative">
                    <!-- Step 1 -->
                    <div class="flex flex-col items-center text-center space-y-4 relative z-10">
                        <div class="w-16 h-16 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-2xl font-black text-primary">1</div>
                        <h3 class="text-xl font-bold text-[#1B3B36] dark:text-white tracking-tight">Create Your Profile</h3>
                        <p class="text-sm text-neutral-400 dark:text-neutral-500 leading-relaxed max-w-xs font-medium">Sign up free and add your pets with their details and preferences.</p>
                    </div>

                    <!-- Step 2 -->
                    <div class="flex flex-col items-center text-center space-y-4 relative z-10">
                        <div class="w-16 h-16 rounded-full bg-accent-100 dark:bg-accent-900/30 flex items-center justify-center text-2xl font-black text-accent">2</div>
                        <h3 class="text-xl font-bold text-[#1B3B36] dark:text-white tracking-tight">Find a Sitter</h3>
                        <p class="text-sm text-neutral-400 dark:text-neutral-500 leading-relaxed max-w-xs font-medium">Browse sitters, check ratings, and find the perfect match.</p>
                    </div>

                    <!-- Step 3 -->
                    <div class="flex flex-col items-center text-center space-y-4 relative z-10">
                        <div class="w-16 h-16 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-2xl font-black text-primary">3</div>
                        <h3 class="text-xl font-bold text-[#1B3B36] dark:text-white tracking-tight">Book & Relax</h3>
                        <p class="text-sm text-neutral-400 dark:text-neutral-500 leading-relaxed max-w-xs font-medium">Confirm your booking, get visit updates with photos.</p>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- CTA FOOTER                                -->
            <!-- ========================================== -->
            <div class="text-center py-8 space-y-5">
                <div class="space-y-1">
                    <h2 class="text-3xl font-black text-[#1B3B36] dark:text-white tracking-tight">
                        Ready to Get Started?
                    </h2>
                    <p class="text-sm text-neutral-400 font-medium">
                        Join PetNanny today — it's free!
                    </p>
                </div>
                
                <a href="#" class="inline-flex items-center gap-2 px-8 py-4 bg-primary hover:bg-primary-600 text-white font-bold text-sm rounded-xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5">
                    <span>☆ Explore Sitters</span>
                </a>
            </div>

        </div>
    </div>
</x-app-layout>