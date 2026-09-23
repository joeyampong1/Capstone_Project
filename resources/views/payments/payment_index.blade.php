<x-app-layout>
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
                        Payments
                    </h1>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">Manage all your payments in one place</p>
                </div>
            </div>
            <a href="{{ route('payments.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary hover:bg-primary-600 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Make Payment
            </a>
        </div>
    </x-slot>

    <div class="bg-white dark:bg-neutral-950 antialiased text-neutral-800 dark:text-neutral-200 h-[calc(100vh-150px)] flex flex-col">
        <div class="max-w-4xl mx-auto px-12 sm:px-16 lg:px-24 w-full flex flex-col h-full">

            <!-- ========================================== -->
            <!-- FILTER TABS                               -->
            <!-- ========================================== -->
            <div class="flex items-center justify-between mb-4 flex-shrink-0 pt-4 sm:pt-6">
                <div class="flex items-center gap-2 bg-neutral-100 dark:bg-neutral-800 p-1 rounded-xl overflow-x-auto whitespace-nowrap">
                    <a href="#" class="px-3 sm:px-4 py-1.5 text-[10px] sm:text-xs font-bold rounded-lg transition bg-primary text-white shadow-sm">
                        All
                    </a>
                    <a href="#" class="px-3 sm:px-4 py-1.5 text-[10px] sm:text-xs font-bold rounded-lg transition text-neutral-600 dark:text-neutral-400 hover:bg-white/50 dark:hover:bg-neutral-700">
                        Pending
                    </a>
                    <a href="#" class="px-3 sm:px-4 py-1.5 text-[10px] sm:text-xs font-bold rounded-lg transition text-neutral-600 dark:text-neutral-400 hover:bg-white/50 dark:hover:bg-neutral-700">
                        Paid
                    </a>
                    <a href="#" class="px-3 sm:px-4 py-1.5 text-[10px] sm:text-xs font-bold rounded-lg transition text-neutral-600 dark:text-neutral-400 hover:bg-white/50 dark:hover:bg-neutral-700">
                        Released
                    </a>
                    <a href="#" class="px-3 sm:px-4 py-1.5 text-[10px] sm:text-xs font-bold rounded-lg transition text-neutral-600 dark:text-neutral-400 hover:bg-white/50 dark:hover:bg-neutral-700">
                        Refunded
                    </a>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-primary/10 text-primary flex-shrink-0">
                    3 total
                </span>
            </div>

            <!-- ========================================== -->
            <!-- PAYMENT LIST                              -->
            <!-- ========================================== -->
            <div class="flex-1 overflow-y-auto">
                <div class="space-y-4 pb-4">

                    <!-- Payment 1: Pending -->
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
                                        <h4 class="font-bold text-[#1B3B36] dark:text-white text-sm sm:text-base">PAY-2026-001</h4>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">Pending</span>
                                    </div>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">Booking: BK-2026-001 • Maria Santos</p>
                                    <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-1">Dec 15, 2026 • GCash</p>
                                    <div class="flex flex-wrap items-center gap-1 sm:gap-2 mt-2">
                                        <span class="inline-flex items-center gap-0.5 text-[10px] text-neutral-400">💰 ₱2,352.00</span>
                                        <span class="inline-flex items-center gap-0.5 text-[10px] text-neutral-400">•</span>
                                        <span class="inline-flex items-center gap-0.5 text-[10px] text-neutral-400">Sitter: ₱1,932.00</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-row sm:flex-col items-center sm:items-end gap-2 sm:gap-1 flex-wrap">
                                <span class="text-[10px] text-neutral-400">Due: Dec 15, 2026</span>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 sm:gap-3 mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                            <a href="#" class="text-xs text-primary font-semibold hover:underline">View Details</a>
                            <a href="#" class="text-xs text-red-500 font-semibold hover:underline">Cancel</a>
                            <a href="#" class="text-xs text-neutral-400 font-semibold hover:underline ml-auto">Download Receipt</a>
                        </div>
                    </div>

                    <!-- Payment 2: Paid -->
                    <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 p-4 sm:p-5 hover:shadow-md transition">
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                            <div class="flex items-start gap-3 sm:gap-4">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400 flex-shrink-0">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="font-bold text-[#1B3B36] dark:text-white text-sm sm:text-base">PAY-2026-002</h4>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">Paid</span>
                                    </div>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">Booking: BK-2026-002 • Juan Dela Cruz</p>
                                    <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-1">Dec 10, 2026 • GrabPay</p>
                                    <div class="flex flex-wrap items-center gap-1 sm:gap-2 mt-2">
                                        <span class="inline-flex items-center gap-0.5 text-[10px] text-neutral-400">💰 ₱1,350.00</span>
                                        <span class="inline-flex items-center gap-0.5 text-[10px] text-neutral-400">•</span>
                                        <span class="inline-flex items-center gap-0.5 text-[10px] text-neutral-400">Sitter: ₱1,105.00</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-row sm:flex-col items-center sm:items-end gap-2 sm:gap-1 flex-wrap">
                                <span class="text-[10px] text-neutral-400">Paid: Dec 10, 2026</span>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 sm:gap-3 mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                            <a href="#" class="text-xs text-primary font-semibold hover:underline">View Details</a>
                            <a href="#" class="text-xs text-neutral-400 font-semibold hover:underline ml-auto">Download Receipt</a>
                        </div>
                    </div>

                    <!-- Payment 3: Released -->
                    <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 p-4 sm:p-5 hover:shadow-md transition">
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                            <div class="flex items-start gap-3 sm:gap-4">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 flex-shrink-0">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="font-bold text-[#1B3B36] dark:text-white text-sm sm:text-base">PAY-2026-003</h4>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">Released</span>
                                    </div>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">Booking: BK-2026-003 • Anna Reyes</p>
                                    <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-1">Dec 5, 2026 • Card</p>
                                    <div class="flex flex-wrap items-center gap-1 sm:gap-2 mt-2">
                                        <span class="inline-flex items-center gap-0.5 text-[10px] text-neutral-400">💰 ₱1,050.00</span>
                                        <span class="inline-flex items-center gap-0.5 text-[10px] text-neutral-400">•</span>
                                        <span class="inline-flex items-center gap-0.5 text-[10px] text-neutral-400">Sitter: ₱868.00</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-row sm:flex-col items-center sm:items-end gap-2 sm:gap-1 flex-wrap">
                                <span class="text-[10px] text-neutral-400">Released: Dec 12, 2026</span>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 sm:gap-3 mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                            <a href="#" class="text-xs text-primary font-semibold hover:underline">View Details</a>
                            <a href="#" class="text-xs text-neutral-400 font-semibold hover:underline ml-auto">Download Receipt</a>
                        </div>
                    </div>

                    <!-- Payment 4: Refunded -->
                    <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 p-4 sm:p-5 hover:shadow-md transition">
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                            <div class="flex items-start gap-3 sm:gap-4">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center text-red-600 dark:text-red-400 flex-shrink-0">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="font-bold text-[#1B3B36] dark:text-white text-sm sm:text-base">PAY-2026-004</h4>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Refunded</span>
                                    </div>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">Booking: BK-2026-004 • Pedro Gomez</p>
                                    <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-1">Nov 28, 2026 • GCash</p>
                                    <div class="flex flex-wrap items-center gap-1 sm:gap-2 mt-2">
                                        <span class="inline-flex items-center gap-0.5 text-[10px] text-neutral-400">💰 ₱2,800.00</span>
                                        <span class="inline-flex items-center gap-0.5 text-[10px] text-neutral-400">•</span>
                                        <span class="inline-flex items-center gap-0.5 text-[10px] text-neutral-400">Sitter: ₱2,300.00</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-row sm:flex-col items-center sm:items-end gap-2 sm:gap-1 flex-wrap">
                                <span class="text-[10px] text-neutral-400">Refunded: Nov 30, 2026</span>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 sm:gap-3 mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                            <a href="#" class="text-xs text-primary font-semibold hover:underline">View Details</a>
                            <a href="#" class="text-xs text-neutral-400 font-semibold hover:underline ml-auto">Download Receipt</a>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>