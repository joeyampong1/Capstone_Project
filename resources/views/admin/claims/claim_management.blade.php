<x-app-layout>
    <!-- ========================================== -->
    <!-- HEADER SLOT                               -->
    <!-- ========================================== -->
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between w-full gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.dashboard') }}" 
                   class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div class="min-w-0">
                    <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                        Protection Claims
                    </h1>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">Manage all protection claim requests and compensation approvals</p>
                </div>
            </div>

            <!-- Export Button -->
            <button class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-sm rounded-xl hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Export Claims
            </button>
        </div>
    </x-slot>

    <!-- ========================================== -->
    <!-- MAIN CONTENT                              -->
    <!-- ========================================== -->
    <div x-data="{ 
        search: '', 
        statusFilter: 'all', 
        typeFilter: 'all', 
        dateRange: 'this_month',
        selectedClaim: null,
        showDetail: false,
        activeTab: 'details'
    }" class="py-4 sm:py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200">
        <div class="w-full sm:max-w-7xl mx-auto px-2 sm:px-16 lg:px-24">

            <!-- ========================================== -->
            <!-- ① STATISTICS CARDS                       -->
            <!-- ========================================== -->
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4 mb-4 sm:mb-6">

                <!-- Pending Claims -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">Pending</p>
                            <h3 class="font-black text-lg sm:text-2xl text-amber-600 dark:text-amber-400 mt-1">12</h3>
                            <p class="text-[10px] sm:text-xs text-amber-600 dark:text-amber-400 mt-1">Awaiting review</p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-amber-100/20 flex items-center justify-center text-amber-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Under Review -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">Under Review</p>
                            <h3 class="font-black text-lg sm:text-2xl text-blue-600 dark:text-blue-400 mt-1">7</h3>
                            <p class="text-[10px] sm:text-xs text-blue-600 dark:text-blue-400 mt-1">Investigating</p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-blue-100/20 flex items-center justify-center text-blue-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Approved -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">Approved</p>
                            <h3 class="font-black text-lg sm:text-2xl text-green-600 dark:text-green-400 mt-1">25</h3>
                            <p class="text-[10px] sm:text-xs text-green-600 dark:text-green-400 mt-1">Compensated</p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-green-100/20 flex items-center justify-center text-green-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Rejected -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">Rejected</p>
                            <h3 class="font-black text-lg sm:text-2xl text-red-600 dark:text-red-400 mt-1">5</h3>
                            <p class="text-[10px] sm:text-xs text-red-500 dark:text-red-400 mt-1">Denied</p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-red-100/20 flex items-center justify-center text-red-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Approved Amount -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition col-span-2 sm:col-span-1">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">Total Approved</p>
                            <h3 class="font-black text-lg sm:text-2xl text-primary mt-1">₱18,450</h3>
                            <p class="text-[10px] sm:text-xs text-green-600 dark:text-green-400 mt-1">Compensation</p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-primary/10 flex items-center justify-center text-primary shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
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
                               placeholder="Search claim ID, booking ID, or user..." 
                               class="w-full pl-9 pr-4 py-2 sm:py-2.5 text-xs sm:text-sm rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary transition">
                    </div>

                    <!-- Filters -->
                    <div class="grid grid-cols-2 sm:flex sm:flex-wrap items-center gap-2">
                        <!-- Status -->
                        <select x-model="statusFilter"
                                class="px-2.5 py-2 sm:px-3 sm:py-2.5 text-xs sm:text-sm rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary transition">
                            <option value="all">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="under_review">Under Review</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>

                        <!-- Claim Type -->
                        <select x-model="typeFilter"
                                class="px-2.5 py-2 sm:px-3 sm:py-2.5 text-xs sm:text-sm rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary transition">
                            <option value="all">All Types</option>
                            <option value="pet_injury">Pet Injury</option>
                            <option value="pet_attack">Pet Attack</option>
                            <option value="missed_visit">Missed Visit</option>
                        </select>

                        <!-- Date Range -->
                        <select x-model="dateRange"
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
                            Apply
                        </button>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- ③ CLAIMS TABLE                           -->
            <!-- ========================================== -->
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs sm:text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-800/30">
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">Claim ID</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">Booking</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">Type</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">Claimant</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">Respondent</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">Amount</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">Status</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">Submitted</th>
                                <th class="text-right py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Row 1: Pending - Pet Injury -->
                            <tr class="border-b border-gray-100 dark:border-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition">
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4 font-bold text-[#1B3B36] dark:text-white whitespace-nowrap">CLM-0001</td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4 font-bold text-[#1B3B36] dark:text-white whitespace-nowrap">BK-00125</td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 whitespace-nowrap">Pet Injury</span>
                                </td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-blue-100/20 flex items-center justify-center text-blue-600 font-bold text-xs shrink-0">MS</div>
                                        <span class="text-neutral-600 dark:text-neutral-300 truncate">Maria Santos</span>
                                    </div>
                                </td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-amber-100/20 flex items-center justify-center text-amber-600 font-bold text-xs shrink-0">JC</div>
                                        <span class="text-neutral-600 dark:text-neutral-300 truncate">John Cruz</span>
                                    </div>
                                </td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4 font-bold text-[#1B3B36] dark:text-white whitespace-nowrap">₱2,500</td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 whitespace-nowrap">Pending</span>
                                </td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4 text-neutral-500 dark:text-neutral-400 whitespace-nowrap">Jul 20, 2026</td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4 text-right">
                                    <button @click="selectedClaim = 1; showDetail = true" 
                                            class="inline-flex items-center gap-1 sm:gap-1.5 px-2 py-1 sm:px-3 sm:py-1.5 bg-primary/10 text-primary font-bold text-xs rounded-lg hover:bg-primary/20 transition whitespace-nowrap">
                                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        View
                                    </button>
                                </td>
                            </tr>

                            <!-- Row 2: Under Review - Missed Visit -->
                            <tr class="border-b border-gray-100 dark:border-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition">
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4 font-bold text-[#1B3B36] dark:text-white whitespace-nowrap">CLM-0002</td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4 font-bold text-[#1B3B36] dark:text-white whitespace-nowrap">BK-00128</td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 whitespace-nowrap">Missed Visit</span>
                                </td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-blue-100/20 flex items-center justify-center text-blue-600 font-bold text-xs shrink-0">JC</div>
                                        <span class="text-neutral-600 dark:text-neutral-300 truncate">Juan Dela Cruz</span>
                                    </div>
                                </td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-amber-100/20 flex items-center justify-center text-amber-600 font-bold text-xs shrink-0">AR</div>
                                        <span class="text-neutral-600 dark:text-neutral-300 truncate">Anna Reyes</span>
                                    </div>
                                </td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4 font-bold text-[#1B3B36] dark:text-white whitespace-nowrap">₱350</td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 whitespace-nowrap">Under Review</span>
                                </td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4 text-neutral-500 dark:text-neutral-400 whitespace-nowrap">Jul 18, 2026</td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4 text-right">
                                    <button @click="selectedClaim = 2; showDetail = true" 
                                            class="inline-flex items-center gap-1 sm:gap-1.5 px-2 py-1 sm:px-3 sm:py-1.5 bg-primary/10 text-primary font-bold text-xs rounded-lg hover:bg-primary/20 transition whitespace-nowrap">
                                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        View
                                    </button>
                                </td>
                            </tr>

                            <!-- Row 3: Approved - Pet Attack -->
                            <tr class="border-b border-gray-100 dark:border-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition">
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4 font-bold text-[#1B3B36] dark:text-white whitespace-nowrap">CLM-0003</td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4 font-bold text-[#1B3B36] dark:text-white whitespace-nowrap">BK-00132</td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 whitespace-nowrap">Pet Attack</span>
                                </td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-blue-100/20 flex items-center justify-center text-blue-600 font-bold text-xs shrink-0">MT</div>
                                        <span class="text-neutral-600 dark:text-neutral-300 truncate">Michael Tan</span>
                                    </div>
                                </td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-amber-100/20 flex items-center justify-center text-amber-600 font-bold text-xs shrink-0">SL</div>
                                        <span class="text-neutral-600 dark:text-neutral-300 truncate">Sarah Lim</span>
                                    </div>
                                </td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4 font-bold text-[#1B3B36] dark:text-white whitespace-nowrap">₱4,000</td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 whitespace-nowrap">Approved</span>
                                </td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4 text-neutral-500 dark:text-neutral-400 whitespace-nowrap">Jul 10, 2026</td>
                                <td class="py-2.5 sm:py-3 px-2 sm:px-4 text-right">
                                    <button @click="selectedClaim = 3; showDetail = true" 
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
                    <p class="text-xs sm:text-sm text-neutral-500 dark:text-neutral-400">Showing 1–10 of 49 claims</p>
                    <div class="flex items-center gap-1 overflow-x-auto">
                        <button class="px-2.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium text-neutral-500 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition whitespace-nowrap">Previous</button>
                        <button class="px-2.5 py-1.5 rounded-lg text-xs sm:text-sm font-bold bg-primary text-white">1</button>
                        <button class="px-2.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium text-neutral-600 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">2</button>
                        <button class="px-2.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium text-neutral-600 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">3</button>
                        <span class="px-2 text-neutral-400">...</span>
                        <button class="px-2.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium text-neutral-600 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">5</button>
                        <button class="px-2.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium text-neutral-500 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition whitespace-nowrap">Next</button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- ========================================== -->
    <!-- ④ CLAIM DETAILS MODAL                    -->
    <!-- ========================================== -->
    <div x-show="showDetail" 
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:px-4 sm:py-8 bg-black/50 backdrop-blur-sm overflow-y-auto"
         @click.away="showDetail = false">
        
        <div class="bg-white dark:bg-neutral-900 rounded-2xl sm:rounded-3xl shadow-2xl border border-gray-200 dark:border-neutral-800 max-w-6xl w-full max-h-[92vh] overflow-y-auto p-4 sm:p-8">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-4 sm:mb-6">
                <h2 class="text-lg sm:text-xl font-black text-[#1B3B36] dark:text-white">Claim Details</h2>
                <button @click="showDetail = false" 
                        class="p-2 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                    <svg class="w-5 h-5 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">

                <!-- LEFT: Claim Information + Parties -->
                <div class="lg:col-span-1 space-y-4">

                    <!-- Claim Information -->
                    <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">Claim Information</p>
                        <div class="space-y-2 text-sm">
                            <div>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Claim ID</p>
                                <p class="font-bold text-[#1B3B36] dark:text-white">CLM-0001</p>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Booking</p>
                                <p class="font-bold text-[#1B3B36] dark:text-white">BK-00125</p>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Incident Date</p>
                                <p class="font-bold text-[#1B3B36] dark:text-white">July 18, 2026</p>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Status</p>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">Pending</span>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Claim Type</p>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Pet Injury</span>
                            </div>
                        </div>
                    </div>

                    <!-- Parties -->
                    <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">Parties</p>
                        <div class="space-y-3">
                            <div>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Claimant</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <div class="w-8 h-8 rounded-full bg-blue-100/20 flex items-center justify-center text-blue-600 font-bold text-xs shrink-0">MS</div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-[#1B3B36] dark:text-white text-sm truncate">Maria Santos</p>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">Pet Owner</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Respondent</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <div class="w-8 h-8 rounded-full bg-amber-100/20 flex items-center justify-center text-amber-600 font-bold text-xs shrink-0">JC</div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-[#1B3B36] dark:text-white text-sm truncate">John Cruz</p>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">Pet Sitter</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- CENTER: Description + Evidence -->
                <div class="lg:col-span-1 space-y-4">

                    <!-- Description -->
                    <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">Description</p>
                        <p class="text-sm text-neutral-600 dark:text-neutral-300 leading-relaxed">
                            The pet suffered a leg injury during the scheduled visit. The sitter was unable to provide immediate medical attention.
                        </p>
                    </div>

                    <!-- Claimed Amount -->
                    <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">Claimed Amount</p>
                        <p class="text-xl sm:text-2xl font-black text-primary">₱2,500.00</p>
                        <p class="text-xs text-neutral-400 mt-1">Requested compensation</p>
                    </div>

                    <!-- Photo Evidence -->
                    <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">Photo Evidence</p>
                        <div class="grid grid-cols-3 gap-2">
                            <div class="aspect-square bg-white dark:bg-neutral-900 rounded-xl border border-gray-200 dark:border-neutral-700 flex items-center justify-center">
                                <svg class="w-7 h-7 sm:w-8 sm:h-8 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="aspect-square bg-white dark:bg-neutral-900 rounded-xl border border-gray-200 dark:border-neutral-700 flex items-center justify-center">
                                <svg class="w-7 h-7 sm:w-8 sm:h-8 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="aspect-square bg-white dark:bg-neutral-900 rounded-xl border border-gray-200 dark:border-neutral-700 flex items-center justify-center">
                                <svg class="w-7 h-7 sm:w-8 sm:h-8 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Supporting Documents -->
                    <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">Supporting Documents</p>
                        <div class="space-y-2">
                            <div class="flex items-center gap-3 p-2 bg-white dark:bg-neutral-900 rounded-lg border border-gray-200 dark:border-neutral-700">
                                <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span class="text-sm font-bold text-[#1B3B36] dark:text-white truncate">Veterinary Receipt.pdf</span>
                            </div>
                            <div class="flex items-center gap-3 p-2 bg-white dark:bg-neutral-900 rounded-lg border border-gray-200 dark:border-neutral-700">
                                <svg class="w-5 h-5 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span class="text-sm font-bold text-[#1B3B36] dark:text-white truncate">Medical Certificate.pdf</span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- RIGHT: Admin Notes + Actions -->
                <div class="lg:col-span-1 space-y-4">

                    <!-- Admin Investigation -->
                    <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">Admin Investigation</p>
                        <textarea rows="4" 
                                  placeholder="Add investigation notes..."
                                  class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-3 py-2.5 sm:px-4 sm:py-2.5 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary text-sm font-medium resize-none"></textarea>
                    </div>

                    <!-- Approved Amount -->
                    <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">Approved Compensation</p>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-neutral-500 dark:text-neutral-400 font-bold">₱</span>
                            <input type="number" 
                                   step="0.01" min="0"
                                   placeholder="0.00"
                                   class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 pl-8 pr-4 py-2.5 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary text-sm font-medium">
                        </div>
                    </div>

                    <!-- Booking Timeline -->
                    <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">Booking Timeline</p>
                        <div class="space-y-3 text-sm">
                            <div class="flex items-start gap-3">
                                <div class="w-2 h-2 rounded-full bg-primary mt-1.5 shrink-0"></div>
                                <div class="min-w-0">
                                    <p class="font-bold text-[#1B3B36] dark:text-white">Booking Created</p>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">July 15, 2026</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="w-2 h-2 rounded-full bg-blue-500 mt-1.5 shrink-0"></div>
                                <div class="min-w-0">
                                    <p class="font-bold text-[#1B3B36] dark:text-white">Booking Confirmed</p>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">July 16, 2026</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="w-2 h-2 rounded-full bg-green-500 mt-1.5 shrink-0"></div>
                                <div class="min-w-0">
                                    <p class="font-bold text-[#1B3B36] dark:text-white">Visit Started</p>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">July 18, 2026</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="w-2 h-2 rounded-full bg-red-500 mt-1.5 shrink-0"></div>
                                <div class="min-w-0">
                                    <p class="font-bold text-[#1B3B36] dark:text-white">Incident Happened</p>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">July 18, 2026</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="w-2 h-2 rounded-full bg-amber-500 mt-1.5 shrink-0"></div>
                                <div class="min-w-0">
                                    <p class="font-bold text-[#1B3B36] dark:text-white">Claim Submitted</p>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">July 20, 2026</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-neutral-700">
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">Actions</p>
                        <div class="space-y-2">
                            <button class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Approve Claim
                            </button>
                            <button class="w-full flex items-center justify-center gap-2 px-4 py-2.5 border-2 border-red-500 text-red-500 hover:bg-red-50 dark:hover:bg-red-950/20 font-bold text-sm rounded-xl transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Reject Claim
                            </button>
                            <button class="w-full flex items-center justify-center gap-2 px-4 py-2.5 border-2 border-blue-500 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-950/20 font-bold text-sm rounded-xl transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                                Request More Evidence
                            </button>
                        </div>
                    </div>

                </div>

            </div>

            <!-- GUIDELINES -->
            <div class="mt-4 sm:mt-6 p-3 sm:p-4 bg-blue-50 dark:bg-blue-950/20 rounded-2xl border border-blue-200 dark:border-blue-800/50">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="min-w-0">
                        <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white">Protection Claim Guidelines</h3>
                        <ul class="mt-1 space-y-1 text-xs sm:text-sm text-neutral-600 dark:text-neutral-300">
                            <li>• Claims must be linked to a booking.</li>
                            <li>• Supporting evidence helps speed up investigation.</li>
                            <li>• Approved compensation is determined after review.</li>
                            <li>• False or fraudulent claims may result in account penalties.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- COMPLAINTS VS CLAIMS COMPARISON -->
            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                <div class="p-3 sm:p-4 bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl border border-gray-100 dark:border-neutral-700">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <h4 class="font-bold text-sm text-[#1B3B36] dark:text-white">Complaints</h4>
                    </div>
                    <ul class="text-xs text-neutral-500 dark:text-neutral-400 space-y-1">
                        <li>• Reports user behavior / service issues</li>
                        <li>• No financial compensation involved</li>
                        <li>• Resolution = Warning / Action taken</li>
                        <li>• Focus: Customer service quality</li>
                    </ul>
                </div>
                <div class="p-3 sm:p-4 bg-blue-50 dark:bg-blue-950/20 rounded-2xl border border-blue-200 dark:border-blue-800/50">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <h4 class="font-bold text-sm text-[#1B3B36] dark:text-white">Protection Claims</h4>
                    </div>
                    <ul class="text-xs text-neutral-500 dark:text-neutral-400 space-y-1">
                        <li>• Requests financial compensation</li>
                        <li>• Pet Injury, Pet Attack, Missed Visit</li>
                        <li>• Resolution = Approve / Reject compensation</li>
                        <li>• Focus: Financial protection</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>

</x-app-layout>