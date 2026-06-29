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
                    My Bookings
                </h1>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">Manage all your bookings in one place</p>
            </div>
        </div>
    </x-slot>

    <div x-data="{ role: 'owner' }" class="bg-white dark:bg-neutral-950 antialiased text-neutral-800 dark:text-neutral-200 h-[calc(100vh-180px)] sm:h-[calc(100vh-150px)] flex flex-col">
        <div class="max-w-4xl mx-auto px-12 sm:px-16 lg:px-24 w-full flex flex-col h-full">

            <!-- ========================================== -->
            <!-- ROLE SWITCH                               -->
            <!-- ========================================== -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-0 mb-4 flex-shrink-0 pt-4 sm:pt-6">
                <div class="flex flex-wrap items-center gap-1 p-1 bg-neutral-100 dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700">
                    <button @click="role = 'owner'" 
                            :class="role === 'owner' ? 'bg-primary text-white shadow-md' : 'text-neutral-600 dark:text-neutral-400 hover:bg-white/50 dark:hover:bg-neutral-700'"
                            class="px-3 sm:px-4 py-1.5 sm:py-2 text-[10px] sm:text-xs font-bold rounded-lg transition">
                        Pet Owner
                    </button>
                    <button @click="role = 'sitter'" 
                            :class="role === 'sitter' ? 'bg-primary text-white shadow-md' : 'text-neutral-600 dark:text-neutral-400 hover:bg-white/50 dark:hover:bg-neutral-700'"
                            class="px-3 sm:px-4 py-1.5 sm:py-2 text-[10px] sm:text-xs font-bold rounded-lg transition">
                        Pet Sitter
                    </button>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- FILTER TABS + BADGE                       -->
            <!-- ========================================== -->
            <div class="flex items-center justify-between mb-4 flex-shrink-0 gap-2">
                <div class="flex items-center gap-2 bg-neutral-100 dark:bg-neutral-800 p-1 rounded-xl overflow-x-auto whitespace-nowrap flex-1 min-w-0">
                    <a href="#" class="px-3 sm:px-4 py-1.5 text-[10px] sm:text-xs font-bold rounded-lg transition bg-primary text-white shadow-sm">
                        All
                    </a>
                    <a href="#" class="px-3 sm:px-4 py-1.5 text-[10px] sm:text-xs font-bold rounded-lg transition text-neutral-600 dark:text-neutral-400 hover:bg-white/50 dark:hover:bg-neutral-700">
                        <span x-show="role === 'owner'">Pending</span>
                        <span x-show="role === 'sitter'">Pending</span>
                    </a>
                    <a href="#" class="px-3 sm:px-4 py-1.5 text-[10px] sm:text-xs font-bold rounded-lg transition text-neutral-600 dark:text-neutral-400 hover:bg-white/50 dark:hover:bg-neutral-700">
                        <span x-show="role === 'owner'">Confirmed</span>
                        <span x-show="role === 'sitter'">Accepted</span>
                    </a>
                    <a href="#" class="px-3 sm:px-4 py-1.5 text-[10px] sm:text-xs font-bold rounded-lg transition text-neutral-600 dark:text-neutral-400 hover:bg-white/50 dark:hover:bg-neutral-700">
                        <span x-show="role === 'owner'">Completed</span>
                        <span x-show="role === 'sitter'">Completed</span>
                    </a>
                </div>

                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-primary/10 text-primary flex-shrink-0">
                    <span x-show="role === 'owner'">3 total</span>
                    <span x-show="role === 'sitter'">2 pending</span>
                </span>
            </div>

            <!-- ========================================== -->
            <!-- BOOKING LIST                              -->
            <!-- ========================================== -->
            <div class="flex-1 overflow-y-auto">
                <div class="space-y-4 pb-4">

                    <!-- ========================================== -->
                    <!-- OWNER MODE                               -->
                    <!-- ========================================== -->
                    <div x-show="role === 'owner'">

                        <!-- Booking 1: Pending -->
                        <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 p-4 sm:p-5 hover:shadow-md transition">
                            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                                <div class="flex items-start gap-3 sm:gap-4">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400 flex-shrink-0">
                                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h4 class="font-bold text-[#1B3B36] dark:text-white text-sm sm:text-base">Maria Santos</h4>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">Pending</span>
                                        </div>
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">2 visits/day • Makati City</p>
                                        <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-1">Dec 15-17, 2026 • 9:00 AM & 6:00 PM</p>
                                        <div class="flex flex-wrap items-center gap-1 sm:gap-2 mt-2">
                                            <span class="inline-flex items-center gap-0.5 text-[10px] text-neutral-400">🐱 1 cat</span>
                                            <span class="inline-flex items-center gap-0.5 text-[10px] text-neutral-400">•</span>
                                            <span class="inline-flex items-center gap-0.5 text-[10px] text-neutral-400">Owner provides food</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-row sm:flex-col items-center sm:items-end gap-2 sm:gap-1 flex-wrap">
                                    <span class="text-[10px] text-neutral-400">₱350/day</span>
                                    <span class="text-[10px] text-neutral-400 font-medium">Total: ₱1,050</span>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-2 sm:gap-3 mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                                <a href="#" class="text-xs text-primary font-semibold hover:underline">View Details</a>
                                <a href="#" class="text-xs text-red-500 font-semibold hover:underline">Cancel</a>
                                <a href="#" class="text-xs text-neutral-400 font-semibold hover:underline ml-auto">Contact Sitter</a>
                            </div>
                        </div>

                        <!-- Booking 2: Confirmed -->
                        <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 p-4 sm:p-5 hover:shadow-md transition mt-4">
                            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                                <div class="flex items-start gap-3 sm:gap-4">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400 flex-shrink-0">
                                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h4 class="font-bold text-[#1B3B36] dark:text-white text-sm sm:text-base">Juan Dela Cruz</h4>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">Confirmed</span>
                                        </div>
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">3 visits/day • Quezon City</p>
                                        <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-1">Dec 18-20, 2026 • 8:00 AM, 1:00 PM & 6:00 PM</p>
                                        <div class="flex flex-wrap items-center gap-1 sm:gap-2 mt-2">
                                            <span class="inline-flex items-center gap-0.5 text-[10px] text-neutral-400">🐕 1 dog</span>
                                            <span class="inline-flex items-center gap-0.5 text-[10px] text-neutral-400">•</span>
                                            <span class="inline-flex items-center gap-0.5 text-[10px] text-neutral-400">Sitter provides food (+₱100)</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-row sm:flex-col items-center sm:items-end gap-2 sm:gap-1 flex-wrap">
                                    <span class="text-[10px] text-neutral-400">₱450/day</span>
                                    <span class="text-[10px] text-neutral-400 font-medium">Total: ₱1,350</span>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-2 sm:gap-3 mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                                <a href="#" class="text-xs text-primary font-semibold hover:underline">View Details</a>
                                <a href="#" class="text-xs text-neutral-400 font-semibold hover:underline ml-auto">Contact Sitter</a>
                            </div>
                        </div>

                        <!-- Booking 3: Completed -->
                        <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 p-4 sm:p-5 hover:shadow-md transition mt-4">
                            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                                <div class="flex items-start gap-3 sm:gap-4">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 flex-shrink-0">
                                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2z"/>
                                        </svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h4 class="font-bold text-[#1B3B36] dark:text-white text-sm sm:text-base">Anna Reyes</h4>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">Completed</span>
                                        </div>
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">1 visit/day • Pasig City</p>
                                        <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-1">Dec 10-12, 2026 • 10:00 AM</p>
                                        <div class="flex flex-wrap items-center gap-1 sm:gap-2 mt-2">
                                            <span class="inline-flex items-center gap-0.5 text-[10px] text-neutral-400">🐱 2 cats</span>
                                            <span class="inline-flex items-center gap-0.5 text-[10px] text-neutral-400">•</span>
                                            <span class="inline-flex items-center gap-0.5 text-[10px] text-neutral-400">Owner provides food</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-row sm:flex-col items-center sm:items-end gap-2 sm:gap-1 flex-wrap">
                                    <span class="text-[10px] text-neutral-400">₱350/day</span>
                                    <span class="text-[10px] text-neutral-400 font-medium">Total: ₱1,050</span>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-2 sm:gap-3 mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                                <a href="#" class="text-xs text-primary font-semibold hover:underline">View Details</a>
                                <a href="#" class="text-xs text-neutral-400 font-semibold hover:underline ml-auto">Leave Review</a>
                            </div>
                        </div>

                    </div>

                    <!-- ========================================== -->
                    <!-- SITTER MODE                              -->
                    <!-- ========================================== -->
                    <div x-show="role === 'sitter'">

                        <!-- Request 1 -->
                        <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 p-4 sm:p-5 hover:shadow-md transition">
                            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                                <div class="flex items-start gap-3 sm:gap-4">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold flex-shrink-0">JM</div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h4 class="font-bold text-[#1B3B36] dark:text-white text-sm sm:text-base">James Mitchell</h4>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">Pending</span>
                                        </div>
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">2 visits/day • Dec 20-22, 2026</p>
                                        <div class="flex flex-wrap items-center gap-1 sm:gap-2 mt-1">
                                            <span class="inline-flex items-center gap-0.5 text-[10px] text-neutral-400">🐕 1 dog</span>
                                            <span class="inline-flex items-center gap-0.5 text-[10px] text-neutral-400">•</span>
                                            <span class="inline-flex items-center gap-0.5 text-[10px] text-neutral-400">Needs medication</span>
                                        </div>
                                        <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-1">Special instructions: Needs oral medication at 8 AM and 8 PM.</p>
                                    </div>
                                </div>
                                <div class="flex flex-row sm:flex-col items-center sm:items-end gap-2 sm:gap-1 flex-wrap">
                                    <span class="text-[10px] text-neutral-400">₱350/day</span>
                                    <span class="text-[10px] text-neutral-400 font-medium">Total: ₱1,050</span>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-2 sm:gap-3 mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                                <button class="px-3 sm:px-4 py-1.5 text-[10px] sm:text-xs font-bold bg-green-500 hover:bg-green-600 text-white rounded-lg transition">Accept</button>
                                <button class="px-3 sm:px-4 py-1.5 text-[10px] sm:text-xs font-bold border-2 border-red-500 text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-lg transition">Decline</button>
                                <a href="#" class="text-xs text-neutral-400 font-semibold hover:underline ml-auto">View Details</a>
                            </div>
                        </div>

                        <!-- Request 2 -->
                        <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 p-4 sm:p-5 hover:shadow-md transition mt-4">
                            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                                <div class="flex items-start gap-3 sm:gap-4">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-accent/10 flex items-center justify-center text-accent font-bold flex-shrink-0">SL</div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h4 class="font-bold text-[#1B3B36] dark:text-white text-sm sm:text-base">Sarah Lim</h4>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">Pending</span>
                                        </div>
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">3 visits/day • Dec 21-23, 2026</p>
                                        <div class="flex flex-wrap items-center gap-1 sm:gap-2 mt-1">
                                            <span class="inline-flex items-center gap-0.5 text-[10px] text-neutral-400">🐱 2 cats</span>
                                            <span class="inline-flex items-center gap-0.5 text-[10px] text-neutral-400">•</span>
                                            <span class="inline-flex items-center gap-0.5 text-[10px] text-neutral-400">Indoor only</span>
                                        </div>
                                        <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-1">Special instructions: Please send photo updates per visit.</p>
                                    </div>
                                </div>
                                <div class="flex flex-row sm:flex-col items-center sm:items-end gap-2 sm:gap-1 flex-wrap">
                                    <span class="text-[10px] text-neutral-400">₱350/day</span>
                                    <span class="text-[10px] text-neutral-400 font-medium">Total: ₱1,050</span>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-2 sm:gap-3 mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                                <button class="px-3 sm:px-4 py-1.5 text-[10px] sm:text-xs font-bold bg-green-500 hover:bg-green-600 text-white rounded-lg transition">Accept</button>
                                <button class="px-3 sm:px-4 py-1.5 text-[10px] sm:text-xs font-bold border-2 border-red-500 text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-lg transition">Decline</button>
                                <a href="#" class="text-xs text-neutral-400 font-semibold hover:underline ml-auto">View Details</a>
                            </div>
                        </div>

                        <!-- Empty state -->
                        <div x-show="false" class="text-center py-12">
                            <svg class="w-12 h-12 mx-auto text-neutral-300 dark:text-neutral-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-sm text-neutral-500 dark:text-neutral-400">No incoming booking requests yet.</p>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>