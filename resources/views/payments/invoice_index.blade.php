<x-app-layout>
    <!-- ========================================== -->
    <!-- HEADER SLOT                               -->
    <!-- ========================================== -->
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" 
                   class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                        Invoices
                    </h1>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">View and download invoices for completed payments</p>
                </div>
            </div>

            <!-- Download All Button -->
            <button class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary hover:bg-primary-600 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Download All
            </button>
        </div>
    </x-slot>

    <!-- ========================================== -->
    <!-- MAIN CONTENT                              -->
    <!-- ========================================== -->
    <div x-data="{ 
        searchInvoice: '', 
        searchBooking: '', 
        methodFilter: 'all', 
        dateRange: '', 
        statusFilter: 'paid',
        selectedInvoice: null,
        showDetail: false
    }" class="py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- ========================================== -->
            <!-- ① SUMMARY CARDS                          -->
            <!-- ========================================== -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

                <!-- Total Invoices -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 font-medium">Total Invoices</p>
                            <h3 class="font-black text-2xl text-[#1B3B36] dark:text-white mt-1">15</h3>
                            <p class="text-xs text-neutral-400 mt-1">All invoices</p>
                        </div>
                        <div class="w-11 h-11 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Paid -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 font-medium">Paid</p>
                            <h3 class="font-black text-2xl text-green-600 dark:text-green-400 mt-1">15</h3>
                            <p class="text-xs text-green-600 dark:text-green-400 mt-1">All completed</p>
                        </div>
                        <div class="w-11 h-11 rounded-full bg-green-100/20 flex items-center justify-center text-green-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Amount -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 font-medium">Total Amount</p>
                            <h3 class="font-black text-2xl text-[#1B3B36] dark:text-white mt-1">₱18,250.00</h3>
                            <p class="text-xs text-neutral-400 mt-1">All time</p>
                        </div>
                        <div class="w-11 h-11 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- This Month -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 font-medium">This Month</p>
                            <h3 class="font-black text-2xl text-blue-600 dark:text-blue-400 mt-1">₱3,450.00</h3>
                            <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">July 2026</p>
                        </div>
                        <div class="w-11 h-11 rounded-full bg-blue-100/20 flex items-center justify-center text-blue-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ========================================== -->
            <!-- ② FILTER BAR                            -->
            <!-- ========================================== -->
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-5 mb-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                    <!-- Invoice ID Search -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Invoice ID</label>
                        <input type="text" x-model="searchInvoice"
                               placeholder="INV-2026-..."
                               class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 px-3 py-2 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary text-sm font-medium">
                    </div>

                    <!-- Booking ID Search -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Booking ID</label>
                        <input type="text" x-model="searchBooking"
                               placeholder="BK-2026-..."
                               class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 px-3 py-2 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary text-sm font-medium">
                    </div>

                    <!-- Payment Method -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Payment Method</label>
                        <select x-model="methodFilter"
                                class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 px-3 py-2 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary text-sm font-medium">
                            <option value="all">All</option>
                            <option value="gcash">GCash</option>
                            <option value="card">Card</option>
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Date Range</label>
                        <input type="month" x-model="dateRange"
                               class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 px-3 py-2 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary text-sm font-medium">
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Status</label>
                        <select x-model="statusFilter"
                                class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 px-3 py-2 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary text-sm font-medium">
                            <option value="paid">Paid</option>
                            <option value="all">All</option>
                        </select>
                    </div>

                    <!-- Apply Button -->
                    <div class="flex items-end">
                        <button class="w-full px-4 py-2 bg-primary hover:bg-primary-600 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Apply
                        </button>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- ③ INVOICE LIST                           -->
            <!-- ========================================== -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">

                <!-- Invoice Card 1 -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-5 hover:shadow-md transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-3">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Invoice No.</p>
                                <p class="font-bold text-[#1B3B36] dark:text-white">INV-2026-0001</p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 mt-1">Paid</span>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Total Paid</p>
                            <p class="text-xl font-black text-primary">₱2,350.00</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Booking</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">BK-2026-001</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Pet</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">🐱 Mingming</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Pet Sitter</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">Maria Santos</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Visit Date</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">July 20, 2026</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Payment Method</p>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">GCash</span>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                        <button @click="selectedInvoice = 1; showDetail = true" 
                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary/10 text-primary font-bold text-xs rounded-lg hover:bg-primary/20 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            View Details
                        </button>
                        <button class="inline-flex items-center gap-1.5 px-4 py-2 border-2 border-green-500 text-green-600 font-bold text-xs rounded-lg hover:bg-green-50 dark:hover:bg-green-950/20 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Download PDF
                        </button>
                    </div>
                </div>

                <!-- Invoice Card 2 -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-5 hover:shadow-md transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-3">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Invoice No.</p>
                                <p class="font-bold text-[#1B3B36] dark:text-white">INV-2026-0002</p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 mt-1">Paid</span>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Total Paid</p>
                            <p class="text-xl font-black text-primary">₱1,800.00</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Booking</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">BK-2026-003</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Pet</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">🐶 Bella</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Pet Sitter</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">John Cruz</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Visit Date</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">July 18, 2026</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Payment Method</p>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">Card</span>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                        <button @click="selectedInvoice = 2; showDetail = true" 
                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary/10 text-primary font-bold text-xs rounded-lg hover:bg-primary/20 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            View Details
                        </button>
                        <button class="inline-flex items-center gap-1.5 px-4 py-2 border-2 border-green-500 text-green-600 font-bold text-xs rounded-lg hover:bg-green-50 dark:hover:bg-green-950/20 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Download PDF
                        </button>
                    </div>
                </div>

            </div>

            <!-- Pagination -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 px-4 py-3 border-t border-gray-100 dark:border-neutral-800">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">Showing 1–10 of 15 invoices</p>
                <div class="flex items-center gap-1">
                    <button class="px-3 py-1.5 rounded-lg text-sm font-medium text-neutral-500 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">Previous</button>
                    <button class="px-3 py-1.5 rounded-lg text-sm font-bold bg-primary text-white">1</button>
                    <button class="px-3 py-1.5 rounded-lg text-sm font-medium text-neutral-600 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">2</button>
                    <button class="px-3 py-1.5 rounded-lg text-sm font-medium text-neutral-500 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">Next</button>
                </div>
            </div>

        </div>
    </div>

    <!-- ========================================== -->
    <!-- ④ INVOICE DETAILS MODAL                   -->
    <!-- ========================================== -->
    <div x-show="showDetail" 
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center px-4 bg-black/50 backdrop-blur-sm overflow-y-auto py-8"
         @click.away="showDetail = false">
        
        <div class="bg-white dark:bg-neutral-900 rounded-3xl shadow-2xl border border-gray-200 dark:border-neutral-800 max-w-4xl w-full max-h-[90vh] overflow-y-auto p-6 sm:p-8">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-black text-[#1B3B36] dark:text-white">Invoice Details</h2>
                <button @click="showDetail = false" 
                        class="p-2 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                    <svg class="w-5 h-5 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- ========================================== -->
            <!-- INVOICE HEADER                            -->
            <!-- ========================================== -->
            <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-6 border border-gray-100 dark:border-neutral-700 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center text-primary font-black text-xl">PN</div>
                            <div>
                                <h3 class="font-black text-xl text-[#1B3B36] dark:text-white">PetNanny</h3>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Official Invoice</p>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">Invoice No.</p>
                        <p class="font-black text-lg text-[#1B3B36] dark:text-white">INV-2026-0001</p>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">Paid</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- ========================================== -->
                <!-- LEFT: Invoice Information                  -->
                <!-- ========================================== -->
                <div class="space-y-4">

                    <!-- Invoice Information -->
                    <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-5 border border-gray-100 dark:border-neutral-700">
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">Invoice Information</p>
                        <div class="space-y-2 text-sm">
                            <div>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Invoice No.</p>
                                <p class="font-bold text-[#1B3B36] dark:text-white">INV-2026-0001</p>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Booking</p>
                                <p class="font-bold text-[#1B3B36] dark:text-white">BK-2026-001</p>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Invoice Date</p>
                                <p class="font-bold text-[#1B3B36] dark:text-white">July 20, 2026</p>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Payment Date</p>
                                <p class="font-bold text-[#1B3B36] dark:text-white">July 20, 2026</p>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Status</p>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">Paid</span>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Information -->
                    <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-5 border border-gray-100 dark:border-neutral-700">
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">Customer Information</p>
                        <div class="space-y-2 text-sm">
                            <div>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Owner</p>
                                <p class="font-bold text-[#1B3B36] dark:text-white">Juan Dela Cruz</p>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Email</p>
                                <p class="font-bold text-[#1B3B36] dark:text-white">juan@email.com</p>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Pet Sitter</p>
                                <p class="font-bold text-[#1B3B36] dark:text-white">Maria Santos</p>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- ========================================== -->
                <!-- RIGHT: Pet Information + Services          -->
                <!-- ========================================== -->
                <div class="space-y-4">

                    <!-- Pet Information -->
                    <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-5 border border-gray-100 dark:border-neutral-700">
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">Pet Information</p>
                        <div class="space-y-2 text-sm">
                            <div>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Pet Name</p>
                                <p class="font-bold text-[#1B3B36] dark:text-white">Mingming</p>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Species</p>
                                <p class="font-bold text-[#1B3B36] dark:text-white">Cat</p>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Breed</p>
                                <p class="font-bold text-[#1B3B36] dark:text-white">Persian</p>
                            </div>
                        </div>
                    </div>

                    <!-- Service Summary -->
                    <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-5 border border-gray-100 dark:border-neutral-700">
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">Service Summary</p>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-200 dark:border-neutral-700">
                                        <th class="text-left py-1.5 text-xs font-bold text-neutral-500">Description</th>
                                        <th class="text-center py-1.5 text-xs font-bold text-neutral-500">Qty</th>
                                        <th class="text-right py-1.5 text-xs font-bold text-neutral-500">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="py-1.5 font-bold text-[#1B3B36] dark:text-white">Pet Sitting Visit</td>
                                        <td class="py-1.5 text-center font-bold text-[#1B3B36] dark:text-white">6</td>
                                        <td class="py-1.5 text-right font-bold text-[#1B3B36] dark:text-white">₱2,100</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Charges -->
                    <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-5 border border-gray-100 dark:border-neutral-700">
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">Charges</p>
                        <div class="space-y-1.5 text-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-neutral-600 dark:text-neutral-400">Service Fee</span>
                                <span class="font-bold text-[#1B3B36] dark:text-white">₱2,100</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-neutral-600 dark:text-neutral-400">Platform Fee</span>
                                <span class="font-bold text-[#1B3B36] dark:text-white">₱250</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-neutral-600 dark:text-neutral-400">Protection Fee</span>
                                <span class="font-bold text-green-600 dark:text-green-400">Included</span>
                            </div>
                            <div class="flex items-center justify-between border-t border-gray-200 dark:border-neutral-700 pt-2 mt-2">
                                <span class="font-bold text-[#1B3B36] dark:text-white">Total Paid</span>
                                <span class="font-bold text-primary text-lg">₱2,350</span>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <!-- ========================================== -->
            <!-- PAYMENT INFORMATION + NOTES                -->
            <!-- ========================================== -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">

                <!-- Payment Information -->
                <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-5 border border-gray-100 dark:border-neutral-700">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">Payment Information</p>
                    <div class="space-y-2 text-sm">
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Payment Method</p>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">GCash</span>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Transaction ID</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">GC123456789</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Payment Status</p>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">Paid</span>
                        </div>
                    </div>
                </div>

                <!-- Invoice Notes -->
                <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-5 border border-gray-100 dark:border-neutral-700">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">Invoice Notes</p>
                    <ul class="space-y-1 text-sm text-neutral-600 dark:text-neutral-300">
                        <li>• Thank you for using PetNanny.</li>
                        <li>• This invoice serves as proof of payment.</li>
                    </ul>
                </div>

            </div>

            <!-- ========================================== -->
            <!-- ACTION BUTTONS                            -->
            <!-- ========================================== -->
            <div class="flex flex-col sm:flex-row items-center gap-3 mt-6 pt-6 border-t border-gray-100 dark:border-neutral-800">
                <button class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary hover:bg-primary-600 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download PDF
                </button>
                <button class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-sm rounded-xl hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Print Invoice
                </button>
                <button @click="showDetail = false" 
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-sm rounded-xl hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back
                </button>
            </div>

        </div>
    </div>

    <!-- ========================================== -->
    <!-- ⑤ EMPTY STATE (hidden)                    -->
    <!-- ========================================== -->
    <div x-show="false" class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-12 text-center">
        <svg class="w-16 h-16 mx-auto text-neutral-300 dark:text-neutral-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <h3 class="text-xl font-black text-[#1B3B36] dark:text-white">No invoices found</h3>
        <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">Invoices will appear after successful payments.</p>
        <a href="#" class="inline-block mt-4 px-6 py-3 bg-primary hover:bg-primary-600 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition">
            Browse Bookings
        </a>
    </div>

    <!-- ========================================== -->
    <!-- ⑥ INFORMATION CARD                       -->
    <!-- ========================================== -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
        <div class="bg-blue-50 dark:bg-blue-950/20 rounded-2xl border border-blue-200 dark:border-blue-800/50 p-5 sm:p-6">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white">Invoice Information</h3>
                    <ul class="mt-2 space-y-1 text-sm text-neutral-600 dark:text-neutral-300">
                        <li>• An invoice is generated after successful payment.</li>
                        <li>• Invoices can be downloaded anytime.</li>
                        <li>• Keep your invoice for future reference.</li>
                        <li>• Each invoice is linked to a completed booking.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>