<x-app-layout>
    <!-- ========================================== -->
    <!-- HEADER SLOT                               -->
    <!-- ========================================== -->
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('sitter.dashboard') }}" 
               class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                    Payments
                </h1>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">Track your earnings, completed visits, and payout history</p>
            </div>
        </div>
    </x-slot>

    <!-- ========================================== -->
    <!-- MAIN CONTENT                              -->
    <!-- ========================================== -->
    <div class="py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- ========================================== -->
            <!-- ① SUMMARY CARDS                          -->
            <!-- ========================================== -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Total Earnings -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Total Earnings</p>
                    <h3 class="font-bold text-2xl mt-1 text-[#1B3B36] dark:text-white">₱18,250</h3>
                    <p class="text-xs text-green-600 dark:text-green-400 mt-1">Lifetime earnings</p>
                </div>

                <!-- Completed Visits -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Completed Visits</p>
                    <h3 class="font-bold text-2xl mt-1 text-[#1B3B36] dark:text-white">58</h3>
                    <p class="text-xs text-neutral-400 mt-1">All completed visits</p>
                </div>

                <!-- Pending Release -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Pending Release</p>
                    <h3 class="font-bold text-2xl mt-1 text-amber-600 dark:text-amber-400">₱2,350</h3>
                    <p class="text-xs text-amber-600 dark:text-amber-400 mt-1">Awaiting payout</p>
                </div>

                <!-- Missed Visits -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Missed Visits</p>
                    <h3 class="font-bold text-2xl mt-1 text-red-600 dark:text-red-400">3</h3>
                    <p class="text-xs text-red-500 dark:text-red-400 mt-1">Deducted earnings</p>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- ② FILTER TABS                            -->
            <!-- ========================================== -->
            <div x-data="{ filter: 'all' }" class="mb-6">
                <div class="flex flex-wrap items-center gap-2 p-1 bg-neutral-100 dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700">
                    <button @click="filter = 'all'" 
                            :class="filter === 'all' ? 'bg-primary text-white shadow-md' : 'text-neutral-600 dark:text-neutral-400 hover:bg-white/50 dark:hover:bg-neutral-700'"
                            class="px-4 py-2 text-xs font-bold rounded-lg transition">
                        All
                    </button>
                    <button @click="filter = 'pending'" 
                            :class="filter === 'pending' ? 'bg-amber-500 text-white shadow-md' : 'text-neutral-600 dark:text-neutral-400 hover:bg-white/50 dark:hover:bg-neutral-700'"
                            class="px-4 py-2 text-xs font-bold rounded-lg transition">
                        Pending
                    </button>
                    <button @click="filter = 'processing'" 
                            :class="filter === 'processing' ? 'bg-purple-500 text-white shadow-md' : 'text-neutral-600 dark:text-neutral-400 hover:bg-white/50 dark:hover:bg-neutral-700'"
                            class="px-4 py-2 text-xs font-bold rounded-lg transition">
                        Processing
                    </button>
                    <button @click="filter = 'released'" 
                            :class="filter === 'released' ? 'bg-green-500 text-white shadow-md' : 'text-neutral-600 dark:text-neutral-400 hover:bg-white/50 dark:hover:bg-neutral-700'"
                            class="px-4 py-2 text-xs font-bold rounded-lg transition">
                        Released
                    </button>
                    <button @click="filter = 'deducted'" 
                            :class="filter === 'deducted' ? 'bg-red-500 text-white shadow-md' : 'text-neutral-600 dark:text-neutral-400 hover:bg-white/50 dark:hover:bg-neutral-700'"
                            class="px-4 py-2 text-xs font-bold rounded-lg transition">
                        Deducted
                    </button>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- ③ PAYMENT CARDS                          -->
            <!-- ========================================== -->
            <div class="space-y-4 mb-6">

                <!-- Payment Card 1 - Released -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-5 sm:p-6 hover:shadow-md transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-3">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Payment ID</p>
                                <p class="font-bold text-[#1B3B36] dark:text-white">PAY-2026-00015</p>
                            </div>
                            <div class="flex items-center gap-4 mt-1">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                    Released
                                </span>
                                <span class="text-xs text-neutral-500 dark:text-neutral-400">July 25, 2026</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Net Earnings</p>
                            <p class="text-xl font-black text-[#1B3B36] dark:text-white">₱1,320</p>
                        </div>
                    </div>

                    <!-- Booking Info -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Booking</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">BK-2026-0015</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Owner</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">Juan Dela Cruz</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Pet</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">🐱 Mingming</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Duration</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">3 Days</p>
                        </div>
                    </div>

                    <!-- Visit Summary -->
                    <div class="grid grid-cols-3 gap-4 mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Scheduled Visits</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">6</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Completed</p>
                            <p class="font-bold text-green-600 dark:text-green-400">5</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Missed</p>
                            <p class="font-bold text-red-600 dark:text-red-400">1</p>
                        </div>
                    </div>

                    <!-- Mini Visit Progress -->
                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-neutral-500 dark:text-neutral-400">Visit Progress</span>
                                <div class="flex items-center gap-1">
                                    <span class="text-sm">●</span>
                                    <span class="text-sm">●</span>
                                    <span class="text-sm">●</span>
                                    <span class="text-sm">●</span>
                                    <span class="text-sm">●</span>
                                    <span class="text-sm text-neutral-300 dark:text-neutral-600">○</span>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-[#1B3B36] dark:text-white">5 / 6 Visits Completed</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-neutral-700 rounded-full h-1.5 mt-1">
                            <div class="bg-primary h-1.5 rounded-full" style="width:83%"></div>
                        </div>
                    </div>

                    <!-- Earnings Breakdown -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Rate per Visit</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">₱300</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Gross Earnings</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">₱1,800</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Missed Deduction</p>
                            <p class="font-bold text-red-600 dark:text-red-400">-₱300</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Platform Fee</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">-₱150</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Protection Fee (2%)</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">-₱30</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Net Earnings</p>
                            <p class="font-bold text-green-600 dark:text-green-400">₱1,320</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-wrap items-center gap-3 mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                        <a href="#"
                           class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary/10 text-primary font-bold text-xs rounded-lg hover:bg-primary/20 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            View Details
                        </a>
                        <button class="inline-flex items-center gap-1.5 px-4 py-2 border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-xs rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Download Receipt
                        </button>
                    </div>
                </div>

                <!-- Payment Card 2 - Processing -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-5 sm:p-6 hover:shadow-md transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-3">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Payment ID</p>
                                <p class="font-bold text-[#1B3B36] dark:text-white">PAY-2026-00014</p>
                            </div>
                            <div class="flex items-center gap-4 mt-1">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">
                                    Processing
                                </span>
                                <span class="text-xs text-neutral-500 dark:text-neutral-400">July 24, 2026</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Net Earnings</p>
                            <p class="text-xl font-black text-[#1B3B36] dark:text-white">₱2,100</p>
                        </div>
                    </div>

                    <!-- Booking Info -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Booking</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">BK-2026-0014</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Owner</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">Maria Santos</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Pet</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">🐶 Bella</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Duration</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">5 Days</p>
                        </div>
                    </div>

                    <!-- Visit Summary -->
                    <div class="grid grid-cols-3 gap-4 mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Scheduled Visits</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">8</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Completed</p>
                            <p class="font-bold text-green-600 dark:text-green-400">7</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Missed</p>
                            <p class="font-bold text-red-600 dark:text-red-400">1</p>
                        </div>
                    </div>

                    <!-- Mini Visit Progress -->
                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-neutral-500 dark:text-neutral-400">Visit Progress</span>
                                <div class="flex items-center gap-1">
                                    <span class="text-sm">●</span>
                                    <span class="text-sm">●</span>
                                    <span class="text-sm">●</span>
                                    <span class="text-sm">●</span>
                                    <span class="text-sm">●</span>
                                    <span class="text-sm">●</span>
                                    <span class="text-sm">●</span>
                                    <span class="text-sm text-neutral-300 dark:text-neutral-600">○</span>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-[#1B3B36] dark:text-white">7 / 8 Visits Completed</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-neutral-700 rounded-full h-1.5 mt-1">
                            <div class="bg-primary h-1.5 rounded-full" style="width:87%"></div>
                        </div>
                    </div>

                    <!-- Earnings Breakdown -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Rate per Visit</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">₱350</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Gross Earnings</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">₱2,800</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Missed Deduction</p>
                            <p class="font-bold text-red-600 dark:text-red-400">-₱350</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Platform Fee</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">-₱210</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Protection Fee (2%)</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">-₱49</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Net Earnings</p>
                            <p class="font-bold text-green-600 dark:text-green-400">₱2,191</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-wrap items-center gap-3 mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                        <a href="#"
                           class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary/10 text-primary font-bold text-xs rounded-lg hover:bg-primary/20 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            View Details
                        </a>
                        <button class="inline-flex items-center gap-1.5 px-4 py-2 border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-xs rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Download Receipt
                        </button>
                    </div>
                </div>

                <!-- Payment Card 3 - Pending -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-5 sm:p-6 hover:shadow-md transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-3">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Payment ID</p>
                                <p class="font-bold text-[#1B3B36] dark:text-white">PAY-2026-00013</p>
                            </div>
                            <div class="flex items-center gap-4 mt-1">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                                    Pending
                                </span>
                                <span class="text-xs text-neutral-500 dark:text-neutral-400">July 23, 2026</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Net Earnings</p>
                            <p class="text-xl font-black text-[#1B3B36] dark:text-white">₱1,020</p>
                        </div>
                    </div>

                    <!-- Booking Info -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Booking</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">BK-2026-0013</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Owner</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">Ana Reyes</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Pet</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">🐹 Max</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Duration</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">4 Days</p>
                        </div>
                    </div>

                    <!-- Visit Summary -->
                    <div class="grid grid-cols-3 gap-4 mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Scheduled Visits</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">4</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Completed</p>
                            <p class="font-bold text-green-600 dark:text-green-400">3</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Missed</p>
                            <p class="font-bold text-red-600 dark:text-red-400">1</p>
                        </div>
                    </div>

                    <!-- Mini Visit Progress -->
                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-neutral-500 dark:text-neutral-400">Visit Progress</span>
                                <div class="flex items-center gap-1">
                                    <span class="text-sm">●</span>
                                    <span class="text-sm">●</span>
                                    <span class="text-sm">●</span>
                                    <span class="text-sm text-neutral-300 dark:text-neutral-600">○</span>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-[#1B3B36] dark:text-white">3 / 4 Visits Completed</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-neutral-700 rounded-full h-1.5 mt-1">
                            <div class="bg-primary h-1.5 rounded-full" style="width:75%"></div>
                        </div>
                    </div>

                    <!-- Earnings Breakdown -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Rate per Visit</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">₱300</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Gross Earnings</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">₱1,200</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Missed Deduction</p>
                            <p class="font-bold text-red-600 dark:text-red-400">-₱300</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Platform Fee</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">-₱90</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Protection Fee (2%)</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">-₱18</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Net Earnings</p>
                            <p class="font-bold text-green-600 dark:text-green-400">₱792</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-wrap items-center gap-3 mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                        <a href="#"
                           class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary/10 text-primary font-bold text-xs rounded-lg hover:bg-primary/20 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            View Details
                        </a>
                        <button class="inline-flex items-center gap-1.5 px-4 py-2 border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-xs rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Download Receipt
                        </button>
                    </div>
                </div>

                <!-- Payment Card 4 - Deducted -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-5 sm:p-6 hover:shadow-md transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-3">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Payment ID</p>
                                <p class="font-bold text-[#1B3B36] dark:text-white">PAY-2026-00012</p>
                            </div>
                            <div class="flex items-center gap-4 mt-1">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                    Deducted
                                </span>
                                <span class="text-xs text-neutral-500 dark:text-neutral-400">July 22, 2026</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Net Earnings</p>
                            <p class="text-xl font-black text-red-600 dark:text-red-400">-₱150</p>
                        </div>
                    </div>

                    <!-- Booking Info -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Booking</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">BK-2026-0012</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Owner</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">Pedro Lopez</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Pet</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">🐰 Coco</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Duration</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">2 Days</p>
                        </div>
                    </div>

                    <!-- Visit Summary -->
                    <div class="grid grid-cols-3 gap-4 mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Scheduled Visits</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">3</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Completed</p>
                            <p class="font-bold text-green-600 dark:text-green-400">2</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Missed</p>
                            <p class="font-bold text-red-600 dark:text-red-400">1</p>
                        </div>
                    </div>

                    <!-- Mini Visit Progress -->
                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-neutral-500 dark:text-neutral-400">Visit Progress</span>
                                <div class="flex items-center gap-1">
                                    <span class="text-sm">●</span>
                                    <span class="text-sm">●</span>
                                    <span class="text-sm text-neutral-300 dark:text-neutral-600">○</span>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-[#1B3B36] dark:text-white">2 / 3 Visits Completed</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-neutral-700 rounded-full h-1.5 mt-1">
                            <div class="bg-red-500 h-1.5 rounded-full" style="width:66%"></div>
                        </div>
                    </div>

                    <!-- Earnings Breakdown -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Rate per Visit</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">₱250</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Gross Earnings</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">₱750</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Missed Deduction</p>
                            <p class="font-bold text-red-600 dark:text-red-400">-₱250</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Platform Fee</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">-₱75</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Protection Fee (2%)</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">-₱15</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Net Earnings</p>
                            <p class="font-bold text-red-600 dark:text-red-400">-₱150</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-wrap items-center gap-3 mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                        <a href="#"
                           class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary/10 text-primary font-bold text-xs rounded-lg hover:bg-primary/20 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            View Details
                        </a>
                        <button class="inline-flex items-center gap-1.5 px-4 py-2 border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-xs rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Download Receipt
                        </button>
                    </div>
                </div>

            </div>

            <!-- ========================================== -->
            <!-- ④ PAYMENT INFORMATION                     -->
            <!-- ========================================== -->
            <div class="bg-blue-50 dark:bg-blue-950/20 rounded-2xl border border-blue-200 dark:border-blue-800/50 p-5 sm:p-6">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white">Payment Information</h3>
                        <ul class="mt-2 space-y-1 text-sm text-neutral-600 dark:text-neutral-300">
                            <li>• Payments are calculated based on completed visits.</li>
                            <li>• Missed visits are automatically deducted from your earnings.</li>
                            <li>• Applicable service deductions are applied before payout.</li>
                            <li>• Released payments cannot be modified.</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>