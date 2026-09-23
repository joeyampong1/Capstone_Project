<x-app-layout>
    <!-- ========================================== -->
    <!-- HEADER SLOT                               -->
    <!-- ========================================== -->
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('mybookings.index') }}" 
               class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                    Make Payment
                </h1>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">Complete your booking payment</p>
            </div>
        </div>
    </x-slot>

    <!-- ========================================== -->
    <!-- MAIN CONTENT                              -->
    <!-- ========================================== -->
    <div class="py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200">
        <div class="max-w-4xl mx-auto px-12 sm:px-16 lg:px-24">

            <div class="bg-white dark:bg-neutral-900 rounded-3xl shadow-xl border border-gray-200/60 dark:border-neutral-800 p-6 sm:p-8">

                <form method="POST" action="#" class="space-y-8">
                    @csrf

                    <!-- ========================================== -->
                    <!-- BOOKING SUMMARY (read-only)               -->
                    <!-- ========================================== -->
                    <div>
                        <label class="block text-sm font-bold text-[#1B3B36] dark:text-white mb-2">
                            Booking Summary
                        </label>
                        <div class="p-4 rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <p class="text-xs text-neutral-400">Booking Reference</p>
                                    <p class="text-sm font-bold text-[#1B3B36] dark:text-white">BK-2026-001</p>
                                </div>
                                <div>
                                    <p class="text-xs text-neutral-400">Sitter</p>
                                    <p class="text-sm font-bold text-[#1B3B36] dark:text-white">Maria Santos</p>
                                </div>
                                <div>
                                    <p class="text-xs text-neutral-400">Pet</p>
                                    <p class="text-sm font-bold text-[#1B3B36] dark:text-white">Mingming (Cat)</p>
                                </div>
                                <div>
                                    <p class="text-xs text-neutral-400">Dates</p>
                                    <p class="text-sm font-bold text-[#1B3B36] dark:text-white">Dec 15-17, 2026</p>
                                </div>
                            </div>
                            <input type="hidden" name="booking_id" value="1">
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- PAYMENT BREAKDOWN                        -->
                    <!-- ========================================== -->
                    <div class="bg-neutral-50 dark:bg-neutral-800/30 rounded-2xl p-5 sm:p-6 border border-gray-100 dark:border-neutral-700">
                        <h3 class="text-sm font-black text-[#1B3B36] dark:text-white uppercase tracking-wider mb-4">
                            Payment Breakdown
                        </h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-neutral-600 dark:text-neutral-400">Total Amount</span>
                                <span class="font-bold text-neutral-800 dark:text-white">₱2,352.00</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-neutral-600 dark:text-neutral-400">Service Fee (8%)</span>
                                <span class="font-bold text-neutral-800 dark:text-white">₱168.00</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-neutral-600 dark:text-neutral-400">Protection Fund (2%)</span>
                                <span class="font-bold text-neutral-800 dark:text-white">₱42.00</span>
                            </div>
                            <div class="flex justify-between border-t border-gray-200 dark:border-neutral-700 pt-2 mt-2">
                                <span class="text-neutral-600 dark:text-neutral-400">Sitter Earnings</span>
                                <span class="font-bold text-green-600 dark:text-green-400">₱1,932.00</span>
                            </div>
                            <div class="flex justify-between border-t-2 border-primary pt-3 mt-2">
                                <span class="text-base font-black text-[#1B3B36] dark:text-white">Total to Pay</span>
                                <span class="text-base font-black text-primary">₱2,352.00</span>
                            </div>
                        </div>
                        <input type="hidden" name="amount" value="2352.00">
                        <input type="hidden" name="service_fee" value="210.00">
                        <input type="hidden" name="protection_fund" value="42.00">
                        <input type="hidden" name="net_platform_income" value="168.00">
                        <input type="hidden" name="sitter_amount" value="1932.00">
                    </div>

                    <!-- ========================================== -->
                    <!-- PAYMENT METHOD                            -->
                    <!-- ========================================== -->
                    <div>
                        <label class="block text-sm font-bold text-[#1B3B36] dark:text-white mb-2">
                            Payment Method <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <label class="flex items-center gap-3 p-3 rounded-xl border-2 border-gray-200 dark:border-neutral-700 hover:border-primary cursor-pointer transition has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                                <input type="radio" name="payment_method" value="GCash" class="w-4 h-4 text-primary focus:ring-primary">
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl">📱</span>
                                    <div>
                                        <p class="text-sm font-bold text-[#1B3B36] dark:text-white">GCash</p>
                                        <p class="text-xs text-neutral-500">Pay via GCash</p>
                                    </div>
                                </div>
                            </label>
                            <label class="flex items-center gap-3 p-3 rounded-xl border-2 border-gray-200 dark:border-neutral-700 hover:border-primary cursor-pointer transition has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                                <input type="radio" name="payment_method" value="GrabPay" class="w-4 h-4 text-primary focus:ring-primary">
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl">💳</span>
                                    <div>
                                        <p class="text-sm font-bold text-[#1B3B36] dark:text-white">GrabPay</p>
                                        <p class="text-xs text-neutral-500">Pay via GrabPay</p>
                                    </div>
                                </div>
                            </label>
                            <label class="flex items-center gap-3 p-3 rounded-xl border-2 border-gray-200 dark:border-neutral-700 hover:border-primary cursor-pointer transition has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                                <input type="radio" name="payment_method" value="Card" class="w-4 h-4 text-primary focus:ring-primary">
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl">💳</span>
                                    <div>
                                        <p class="text-sm font-bold text-[#1B3B36] dark:text-white">Credit/Debit Card</p>
                                        <p class="text-xs text-neutral-500">Pay via Card</p>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- PAYMENT STATUS (hidden - auto set)        -->
                    <!-- ========================================== -->
                    <input type="hidden" name="payment_status" value="pending">
                    <input type="hidden" name="payment_reference" value="PAY-2026-001">

                    <!-- ========================================== -->
                    <!-- FORM ACTIONS                             -->
                    <!-- ========================================== -->
                    <div class="flex flex-col sm:flex-row items-center gap-3 pt-4 border-t border-gray-100 dark:border-neutral-800">
                        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-primary hover:bg-primary-600 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Pay Now
                        </button>
                        <a href="{{ route('mybookings.index') }}" 
                           class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-sm rounded-xl hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                            Cancel
                        </a>
                    </div>

                    <!-- ========================================== -->
                    <!-- SECURE PAYMENT NOTICE                     -->
                    <!-- ========================================== -->
                    <div class="flex items-center justify-center gap-2 text-xs text-neutral-400 dark:text-neutral-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <span>Your payment is secure and encrypted</span>
                    </div>

                </form>

            </div>

        </div>
    </div>
</x-app-layout>