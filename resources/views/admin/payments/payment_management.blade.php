<x-app-layout>
    <!-- ========================================== -->
    <!-- HEADER SLOT                               -->
    <!-- ========================================== -->
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.dashboard') }}" 
               class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div class="min-w-0">
                <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                    Payment Management
                </h1>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">Monitor owner payments and sitter payouts</p>
            </div>
        </div>
    </x-slot>

    <!-- ========================================== -->
    <!-- MAIN CONTENT                              -->
    <!-- ========================================== -->
    <div x-data="{ 
        search: '', 
        statusFilter: 'all', 
        methodFilter: 'all', 
        bookingFilter: 'all',
        dateFilter: 'this_month',
        selectedPayment: null,
        showDetail: false
    }" class="py-4 sm:py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200">
        <div class="w-full sm:max-w-7xl mx-auto px-2 sm:px-16 lg:px-24">

            <!-- ========================================== -->
            <!-- ① STATISTICS CARDS                       -->
            <!-- ========================================== -->
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4 mb-4 sm:mb-6">

                <!-- Total Payments -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">Total Payments</p>
                            <h3 class="font-black text-base sm:text-2xl text-[#1B3B36] dark:text-white mt-1">₱158K</h3>
                            <p class="text-[10px] sm:text-xs text-green-600 dark:text-green-400 mt-1">↑ 8.5%</p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-primary/10 flex items-center justify-center text-primary shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Pending -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">Pending</p>
                            <h3 class="font-black text-base sm:text-2xl text-amber-600 dark:text-amber-400 mt-1">12</h3>
                            <p class="text-[10px] sm:text-xs text-amber-600 dark:text-amber-400 mt-1">Awaiting</p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-amber-100/20 flex items-center justify-center text-amber-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Paid -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">Paid</p>
                            <h3 class="font-black text-base sm:text-2xl text-blue-600 dark:text-blue-400 mt-1">94</h3>
                            <p class="text-[10px] sm:text-xs text-blue-600 dark:text-blue-400 mt-1">Completed</p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-blue-100/20 flex items-center justify-center text-blue-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Released -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">Released</p>
                            <h3 class="font-black text-base sm:text-2xl text-green-600 dark:text-green-400 mt-1">88</h3>
                            <p class="text-[10px] sm:text-xs text-green-600 dark:text-green-400 mt-1">Payouts</p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-green-100/20 flex items-center justify-center text-green-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Refunded -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition col-span-2 sm:col-span-1">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">Refunded</p>
                            <h3 class="font-black text-base sm:text-2xl text-red-600 dark:text-red-400 mt-1">3</h3>
                            <p class="text-[10px] sm:text-xs text-red-500 dark:text-red-400 mt-1">Refunded</p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-red-100/20 flex items-center justify-center text-red-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ========================================== -->
            <!-- ② SEARCH & FILTERS                       -->
            <!-- ========================================== -->
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-5 mb-4 sm:mb-6">
                <div class="flex flex-col lg:flex-row gap-3 sm:gap-4">
                    <!-- Search -->
                    <div class="flex-1 relative">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" 
                               x-model="search"
                               placeholder="Search payment..." 
                               class="w-full pl-9 pr-4 py-2 sm:py-2.5 text-xs sm:text-sm rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary transition">
                    </div>

                    <!-- Filters -->
                    <div class="grid grid-cols-2 sm:flex sm:flex-wrap items-center gap-2">
                        <!-- Payment Status -->
                        <select x-model="statusFilter"
                                class="px-2.5 py-2 sm:px-3 sm:py-2.5 text-xs sm:text-sm rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary transition">
                            <option value="all">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="released">Released</option>
                            <option value="refunded">Refunded</option>
                        </select>

                        <!-- Payment Method -->
                        <select x-model="methodFilter"
                                class="px-2.5 py-2 sm:px-3 sm:py-2.5 text-xs sm:text-sm rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary transition">
                            <option value="all">All Methods</option>
                            <option value="gcash">GCash</option>
                            <option value="card">Card</option>
                            <option value="bank">Bank Transfer</option>
                        </select>

                        <!-- Booking -->
                        <select x-model="bookingFilter"
                                class="px-2.5 py-2 sm:px-3 sm:py-2.5 text-xs sm:text-sm rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary transition">
                            <option value="all">All Bookings</option>
                            <option value="bk-001">BK-2026-001</option>
                            <option value="bk-002">BK-2026-002</option>
                            <option value="bk-003">BK-2026-003</option>
                        </select>

                        <!-- Date -->
                        <select x-model="dateFilter"
                                class="px-2.5 py-2 sm:px-3 sm:py-2.5 text-xs sm:text-sm rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary transition">
                            <option value="this_month">This Month</option>
                            <option value="last_month">Last Month</option>
                            <option value="this_quarter">This Quarter</option>
                            <option value="this_year">This Year</option>
                        </select>

                        <button class="col-span-2 sm:col-span-1 inline-flex items-center justify-center px-4 py-2 sm:py-2.5 bg-primary hover:bg-primary-600 text-white font-bold text-xs sm:text-sm rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Search
                        </button>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- ③ PAYMENTS TABLE                         -->
            <!-- ========================================== -->
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs sm:text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-800/30">
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">Payment ID</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">Booking</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">Owner</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">Sitter</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">Amount</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">Method</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">Status</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">Date</th>
                                <th class="text-right py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Row 1: Paid -->
                            <tr class="border-b border-gray-100 dark:border-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition">
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4 font-bold text-[#1B3B36] dark:text-white whitespace-nowrap">PAY-001</td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4 font-bold text-[#1B3B36] dark:text-white whitespace-nowrap">BK-2026-001</td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-blue-100/20 flex items-center justify-center text-blue-600 font-bold text-xs shrink-0">JC</div>
                                        <span class="text-neutral-600 dark:text-neutral-300 truncate">Juan Dela Cruz</span>
                                    </div>
                                </td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-amber-100/20 flex items-center justify-center text-amber-600 font-bold text-xs shrink-0">MS</div>
                                        <span class="text-neutral-600 dark:text-neutral-300 truncate">Maria Santos</span>
                                    </div>
                                </td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4 font-bold text-[#1B3B36] dark:text-white whitespace-nowrap">₱2,400</td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 whitespace-nowrap">GCash</span>
                                </td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 whitespace-nowrap">Paid</span>
                                </td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4 text-neutral-500 dark:text-neutral-400 whitespace-nowrap">Jul 25, 2026</td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4 text-right">
                                    <button @click="selectedPayment = 1; showDetail = true" 
                                            class="inline-flex items-center gap-1 sm:gap-1.5 px-2 py-1 sm:px-3 sm:py-1.5 bg-primary/10 text-primary font-bold text-xs rounded-lg hover:bg-primary/20 transition whitespace-nowrap">
                                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        View
                                    </button>
                                </td>
                            </tr>

                            <!-- Row 2: Released -->
                            <tr class="border-b border-gray-100 dark:border-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition">
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4 font-bold text-[#1B3B36] dark:text-white whitespace-nowrap">PAY-002</td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4 font-bold text-[#1B3B36] dark:text-white whitespace-nowrap">BK-2026-002</td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-blue-100/20 flex items-center justify-center text-blue-600 font-bold text-xs shrink-0">AR</div>
                                        <span class="text-neutral-600 dark:text-neutral-300 truncate">Ana Reyes</span>
                                    </div>
                                </td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-amber-100/20 flex items-center justify-center text-amber-600 font-bold text-xs shrink-0">JP</div>
                                        <span class="text-neutral-600 dark:text-neutral-300 truncate">John Cruz</span>
                                    </div>
                                </td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4 font-bold text-[#1B3B36] dark:text-white whitespace-nowrap">₱1,800</td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full text-[10px] font-bold bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400 whitespace-nowrap">Card</span>
                                </td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 whitespace-nowrap">Released</span>
                                </td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4 text-neutral-500 dark:text-neutral-400 whitespace-nowrap">Jul 22, 2026</td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4 text-right">
                                    <button @click="selectedPayment = 2; showDetail = true" 
                                            class="inline-flex items-center gap-1 sm:gap-1.5 px-2 py-1 sm:px-3 sm:py-1.5 bg-primary/10 text-primary font-bold text-xs rounded-lg hover:bg-primary/20 transition whitespace-nowrap">
                                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        View
                                    </button>
                                </td>
                            </tr>

                            <!-- Row 3: Pending -->
                            <tr class="border-b border-gray-100 dark:border-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition">
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4 font-bold text-[#1B3B36] dark:text-white whitespace-nowrap">PAY-003</td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4 font-bold text-[#1B3B36] dark:text-white whitespace-nowrap">BK-2026-003</td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-blue-100/20 flex items-center justify-center text-blue-600 font-bold text-xs shrink-0">PG</div>
                                        <span class="text-neutral-600 dark:text-neutral-300 truncate">Pedro Gomez</span>
                                    </div>
                                </td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-amber-100/20 flex items-center justify-center text-amber-600 font-bold text-xs shrink-0">AC</div>
                                        <span class="text-neutral-600 dark:text-neutral-300 truncate">Anna Cruz</span>
                                    </div>
                                </td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4 font-bold text-[#1B3B36] dark:text-white whitespace-nowrap">₱3,200</td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 whitespace-nowrap">GCash</span>
                                </td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 whitespace-nowrap">Pending</span>
                                </td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4 text-neutral-500 dark:text-neutral-400 whitespace-nowrap">Jul 20, 2026</td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4 text-right">
                                    <button @click="selectedPayment = 3; showDetail = true" 
                                            class="inline-flex items-center gap-1 sm:gap-1.5 px-2 py-1 sm:px-3 sm:py-1.5 bg-primary/10 text-primary font-bold text-xs rounded-lg hover:bg-primary/20 transition whitespace-nowrap">
                                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        View
                                    </button>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-3 sm:px-4 py-3 border-t border-gray-100 dark:border-neutral-800">
                    <p class="text-xs sm:text-sm text-neutral-500 dark:text-neutral-400">Showing 1–10 of 197 payments</p>
                    <div class="flex items-center gap-1 overflow-x-auto">
                        <button class="px-2.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium text-neutral-500 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition whitespace-nowrap">Previous</button>
                        <button class="px-2.5 py-1.5 rounded-lg text-xs sm:text-sm font-bold bg-primary text-white">1</button>
                        <button class="px-2.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium text-neutral-600 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">2</button>
                        <button class="px-2.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium text-neutral-600 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">3</button>
                        <span class="px-2 text-neutral-400">...</span>
                        <button class="px-2.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium text-neutral-600 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">20</button>
                        <button class="px-2.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium text-neutral-500 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition whitespace-nowrap">Next</button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- ========================================== -->
    <!-- ④ PAYMENT DETAILS MODAL                  -->
    <!-- ========================================== -->
    <div x-show="showDetail" 
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:px-4 sm:py-8 bg-black/50 backdrop-blur-sm overflow-y-auto"
         @click.away="showDetail = false">
        
        <div class="bg-white dark:bg-neutral-900 rounded-2xl sm:rounded-3xl shadow-2xl border border-gray-200 dark:border-neutral-800 max-w-5xl w-full max-h-[92vh] overflow-y-auto p-4 sm:p-8">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-4 sm:mb-6">
                <h2 class="text-lg sm:text-xl font-black text-[#1B3B36] dark:text-white">Payment Details</h2>
                <button @click="showDetail = false" 
                        class="p-2 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                    <svg class="w-5 h-5 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">

                <!-- LEFT: Booking Information -->
                <div class="lg:col-span-1 space-y-4">

                    <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">Booking Information</p>
                        <div class="space-y-2 text-sm">
                            <div>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Booking ID</p>
                                <p class="font-bold text-[#1B3B36] dark:text-white">BK-2026-001</p>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Owner</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <div class="w-6 h-6 rounded-full bg-blue-100/20 flex items-center justify-center text-blue-600 font-bold text-[10px] shrink-0">JC</div>
                                    <span class="font-bold text-[#1B3B36] dark:text-white truncate">Juan Dela Cruz</span>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Pet Sitter</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <div class="w-6 h-6 rounded-full bg-amber-100/20 flex items-center justify-center text-amber-600 font-bold text-[10px] shrink-0">MS</div>
                                    <span class="font-bold text-[#1B3B36] dark:text-white truncate">Maria Santos</span>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Pet</p>
                                <p class="font-bold text-[#1B3B36] dark:text-white">Buddy</p>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- CENTER: Payment Breakdown -->
                <div class="lg:col-span-1 space-y-4">

                    <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">Payment Breakdown</p>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-neutral-500 dark:text-neutral-400">Booking Amount</span>
                                <span class="font-bold text-[#1B3B36] dark:text-white shrink-0">₱2,400</span>
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-neutral-500 dark:text-neutral-400">Completed Visits</span>
                                <span class="font-bold text-green-600 dark:text-green-400 shrink-0">8</span>
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-neutral-500 dark:text-neutral-400">Missed Visits</span>
                                <span class="font-bold text-red-600 dark:text-red-400 shrink-0">1</span>
                            </div>
                            <div class="flex items-center justify-between gap-2 border-t border-gray-100 dark:border-neutral-800 pt-2">
                                <span class="text-neutral-500 dark:text-neutral-400">Platform Fee</span>
                                <span class="font-bold text-red-600 dark:text-red-400 shrink-0">-₱120</span>
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-neutral-500 dark:text-neutral-400">Protection Fee</span>
                                <span class="font-bold text-red-600 dark:text-red-400 shrink-0">-₱48</span>
                            </div>
                            <div class="flex items-center justify-between gap-2 border-t border-gray-100 dark:border-neutral-800 pt-2">
                                <span class="text-neutral-500 dark:text-neutral-400 font-bold">Final Payout</span>
                                <span class="font-bold text-primary text-lg shrink-0">₱1,920</span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- RIGHT: Payment Information -->
                <div class="lg:col-span-1 space-y-4">

                    <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">Payment Information</p>
                        <div class="space-y-2 text-sm">
                            <div>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Payment ID</p>
                                <p class="font-bold text-[#1B3B36] dark:text-white">PAY-001</p>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Method</p>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">GCash</span>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Reference Number</p>
                                <p class="font-bold text-[#1B3B36] dark:text-white break-all">GCX12345678</p>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Status</p>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">Paid</span>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Payment Date</p>
                                <p class="font-bold text-[#1B3B36] dark:text-white">July 25, 2026</p>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Timeline -->
                    <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">Payment Timeline</p>
                        <div class="space-y-3 text-sm">
                            <div class="flex items-start gap-3">
                                <div class="w-2 h-2 rounded-full bg-primary mt-1.5 shrink-0"></div>
                                <div class="min-w-0">
                                    <p class="font-bold text-[#1B3B36] dark:text-white">Booking Created</p>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">July 20, 2026</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="w-2 h-2 rounded-full bg-blue-500 mt-1.5 shrink-0"></div>
                                <div class="min-w-0">
                                    <p class="font-bold text-[#1B3B36] dark:text-white">Owner Paid</p>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">July 22, 2026</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="w-2 h-2 rounded-full bg-green-500 mt-1.5 shrink-0"></div>
                                <div class="min-w-0">
                                    <p class="font-bold text-[#1B3B36] dark:text-white">Payment Verified</p>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">July 23, 2026</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="w-2 h-2 rounded-full bg-amber-500 mt-1.5 shrink-0"></div>
                                <div class="min-w-0">
                                    <p class="font-bold text-[#1B3B36] dark:text-white">Visits Completed</p>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">July 28, 2026</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="w-2 h-2 rounded-full bg-green-500 mt-1.5 shrink-0"></div>
                                <div class="min-w-0">
                                    <p class="font-bold text-[#1B3B36] dark:text-white">Payout Released</p>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">July 29, 2026</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <!-- PAYMENT HISTORY -->
            <div class="mt-4 sm:mt-6 grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <div class="bg-green-50 dark:bg-green-950/20 rounded-2xl p-3 sm:p-4 border border-green-200 dark:border-green-800/50 text-center">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Owner Payment</p>
                    <div class="flex items-center justify-center gap-1 mt-1">
                        <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="font-bold text-green-600 dark:text-green-400 text-xs sm:text-sm">Paid</span>
                    </div>
                </div>
                <div class="bg-green-50 dark:bg-green-950/20 rounded-2xl p-3 sm:p-4 border border-green-200 dark:border-green-800/50 text-center">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Platform Fee</p>
                    <div class="flex items-center justify-center gap-1 mt-1">
                        <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="font-bold text-green-600 dark:text-green-400 text-xs sm:text-sm">Deducted</span>
                    </div>
                </div>
                <div class="bg-green-50 dark:bg-green-950/20 rounded-2xl p-3 sm:p-4 border border-green-200 dark:border-green-800/50 text-center">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Protection Fee</p>
                    <div class="flex items-center justify-center gap-1 mt-1">
                        <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="font-bold text-green-600 dark:text-green-400 text-xs sm:text-sm">Reserved</span>
                    </div>
                </div>
                <div class="bg-green-50 dark:bg-green-950/20 rounded-2xl p-3 sm:p-4 border border-green-200 dark:border-green-800/50 text-center">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Sitter Payout</p>
                    <div class="flex items-center justify-center gap-1 mt-1">
                        <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="font-bold text-green-600 dark:text-green-400 text-xs sm:text-sm">Released</span>
                    </div>
                </div>
            </div>

            <!-- QUICK ACTIONS -->
            <div class="mt-4 sm:mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-3 sm:p-4 bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl border border-gray-100 dark:border-neutral-700">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs text-neutral-500 dark:text-neutral-400 font-medium">Quick Actions:</span>
                    <button class="flex items-center gap-1.5 px-2.5 py-1.5 sm:px-3 sm:py-1.5 bg-primary/10 text-primary font-bold text-[10px] sm:text-xs rounded-lg hover:bg-primary/20 transition">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        View Booking
                    </button>
                    <button class="flex items-center gap-1.5 px-2.5 py-1.5 sm:px-3 sm:py-1.5 bg-red-100/20 text-red-600 font-bold text-[10px] sm:text-xs rounded-lg hover:bg-red-100/30 transition">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        View Complaint
                    </button>
                    <button class="flex items-center gap-1.5 px-2.5 py-1.5 sm:px-3 sm:py-1.5 bg-amber-100/20 text-amber-600 font-bold text-[10px] sm:text-xs rounded-lg hover:bg-amber-100/30 transition">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        View Claim
                    </button>
                </div>
                <button class="w-full sm:w-auto flex items-center justify-center gap-1.5 px-4 py-2 bg-primary hover:bg-primary-600 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download Receipt
                </button>
            </div>

            <!-- GUIDELINES -->
            <div class="mt-4 sm:mt-6 p-3 sm:p-4 bg-blue-50 dark:bg-blue-950/20 rounded-2xl border border-blue-200 dark:border-blue-800/50">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="min-w-0">
                        <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white">Payment Guidelines</h3>
                        <ul class="mt-1 space-y-1 text-xs sm:text-sm text-neutral-600 dark:text-neutral-300">
                            <li>• Payments are linked to completed bookings.</li>
                            <li>• Sitter payouts are calculated based on completed visits.</li>
                            <li>• Missed visits are deducted before payout.</li>
                            <li>• Platform and protection fees are deducted before payout.</li>
                            <li>• Released payments cannot be modified.</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>

</x-app-layout>