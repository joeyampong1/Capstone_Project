<x-app-layout>
    <!-- ========================================== -->
    <!-- HEADER SLOT                               -->
    <!-- ========================================== -->
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('claims.index') }}" 
               class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                    Protection Claim
                </h1>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">Submit a protection claim for an eligible booking</p>
            </div>
        </div>
    </x-slot>

    <!-- ========================================== -->
    <!-- MAIN CONTENT                              -->
    <!-- ========================================== -->
    <div class="py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            <form method="POST" action="#" class="space-y-6">
                @csrf

                <!-- ========================================== -->
                <!-- BOOKING INFORMATION CARD                   -->
                <!-- ========================================== -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-5 sm:p-6">
                    <h2 class="font-bold text-lg text-[#1B3B36] dark:text-white flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Booking Information
                    </h2>

                    <div class="space-y-4">
                        <!-- Booking Dropdown -->
                        <div>
                            <label for="booking_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                                Booking
                            </label>
                            <select id="booking_id" name="booking_id"
                                    class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 px-4 py-2.5 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary text-sm font-medium">
                                <option value="">Select Booking</option>
                                <option value="1">BK-2026-001 - Juan Dela Cruz - Mingming</option>
                                <option value="2">BK-2026-002 - Maria Santos - Bella</option>
                                <option value="3">BK-2026-003 - Ana Reyes - Max</option>
                            </select>
                            <p class="mt-1 text-xs text-neutral-400">Select a completed or active booking to file a claim</p>
                        </div>

                        <!-- Auto-filled fields -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="pet" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                                    Pet
                                </label>
                                <input type="text" id="pet" name="pet" 
                                       value="Mingming"
                                       readonly
                                       class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-100 dark:bg-neutral-800/50 px-4 py-2.5 text-[#1B3B36] dark:text-white cursor-not-allowed text-sm font-medium">
                                <p class="mt-1 text-xs text-neutral-400">Auto-filled from selected booking</p>
                            </div>
                            <div>
                                <label for="respondent" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                                    Respondent
                                </label>
                                <input type="text" id="respondent" name="respondent" 
                                       value="Maria Santos"
                                       readonly
                                       class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-100 dark:bg-neutral-800/50 px-4 py-2.5 text-[#1B3B36] dark:text-white cursor-not-allowed text-sm font-medium">
                                <p class="mt-1 text-xs text-neutral-400">Auto-filled from selected booking</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="service" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                                    Service
                                </label>
                                <input type="text" id="service" name="service" 
                                       value="Drop-in Visit"
                                       readonly
                                       class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-100 dark:bg-neutral-800/50 px-4 py-2.5 text-[#1B3B36] dark:text-white cursor-not-allowed text-sm font-medium">
                                <p class="mt-1 text-xs text-neutral-400">Auto-filled from selected booking</p>
                            </div>
                            <div>
                                <label for="booking_date" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                                    Booking Date
                                </label>
                                <input type="text" id="booking_date" name="booking_date" 
                                       value="Dec 10, 2026 - Dec 15, 2026"
                                       readonly
                                       class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-100 dark:bg-neutral-800/50 px-4 py-2.5 text-[#1B3B36] dark:text-white cursor-not-allowed text-sm font-medium">
                                <p class="mt-1 text-xs text-neutral-400">Auto-filled from selected booking</p>
                            </div>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                                Status
                            </label>
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                Completed
                            </span>
                            <p class="mt-1 text-xs text-neutral-400">Auto-filled from selected booking</p>
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- INCIDENT INFORMATION CARD                  -->
                <!-- ========================================== -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-5 sm:p-6">
                    <h2 class="font-bold text-lg text-[#1B3B36] dark:text-white flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Incident Information
                    </h2>

                    <div class="space-y-4">
                        <!-- Incident Date & Claim Type -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="incident_date" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                                    Incident Date
                                </label>
                                <input type="date" id="incident_date" name="incident_date" 
                                       value="{{ date('Y-m-d') }}"
                                       class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 px-4 py-2.5 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary text-sm font-medium">
                            </div>
                            <div>
                                <label for="claim_type" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                                    Claim Type
                                </label>
                                <select id="claim_type" name="claim_type"
                                        class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 px-4 py-2.5 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary text-sm font-medium">
                                    <option value="">Select Claim Type</option>
                                    <!-- Owner Claim Types -->
                                    <optgroup label="Owner Claims">
                                        <option value="missed_visit">Missed Visit</option>
                                        <option value="pet_injury">Pet Injury</option>
                                    </optgroup>
                                    <!-- Sitter Claim Types -->
                                    <optgroup label="Sitter Claims">
                                        <option value="pet_attack">Pet Attack</option>
                                        <option value="pet_injury">Pet Injury</option>
                                    </optgroup>
                                </select>
                                <p class="mt-1 text-xs text-neutral-400">Select the type of claim based on the incident</p>
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                                Description
                            </label>
                            <textarea id="description" name="description" rows="4" 
                                      placeholder="Describe the incident in detail..."
                                      class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 px-4 py-2.5 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary text-sm font-medium resize-none"></textarea>
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- CLAIM AMOUNT CARD                         -->
                <!-- ========================================== -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-5 sm:p-6">
                    <h2 class="font-bold text-lg text-[#1B3B36] dark:text-white flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Claim Amount
                    </h2>

                    <div>
                        <label for="claimed_amount" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                            Claimed Amount (₱)
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-neutral-500 dark:text-neutral-400 font-bold">₱</span>
                            <input type="number" id="claimed_amount" name="claimed_amount" 
                                   step="0.01" min="0"
                                   placeholder="0.00"
                                   class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 pl-8 pr-4 py-2.5 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary text-sm font-medium">
                        </div>
                        <p class="mt-1 text-xs text-neutral-400">Enter the amount you are claiming</p>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- EVIDENCE CARD                             -->
                <!-- ========================================== -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-5 sm:p-6">
                    <h2 class="font-bold text-lg text-[#1B3B36] dark:text-white flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Evidence
                    </h2>

                    <!-- Photo Evidence -->
                    <div class="mb-4">
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                            Photo Evidence
                        </label>
                        <div class="border-2 border-dashed border-gray-300 dark:border-neutral-700 rounded-xl p-6 text-center hover:border-primary/50 transition">
                            <input type="file" accept="image/*" multiple class="hidden" id="photo-upload">
                            <label for="photo-upload" class="cursor-pointer block">
                                <svg class="w-12 h-12 mx-auto text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">
                                    Click to upload photos
                                </p>
                                <p class="text-xs text-neutral-400">JPG, PNG, JPEG</p>
                            </label>
                        </div>
                        <p class="mt-1 text-xs text-neutral-400">Upload photos as evidence (optional)</p>
                    </div>

                    <!-- Supporting Documents -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                            Supporting Documents
                        </label>
                        <div class="border-2 border-dashed border-gray-300 dark:border-neutral-700 rounded-xl p-6 text-center hover:border-primary/50 transition">
                            <input type="file" accept=".pdf,.doc,.docx" multiple class="hidden" id="doc-upload">
                            <label for="doc-upload" class="cursor-pointer block">
                                <svg class="w-12 h-12 mx-auto text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">
                                    Click to upload documents
                                </p>
                                <p class="text-xs text-neutral-400">PDF, DOC, DOCX</p>
                                <p class="text-xs text-neutral-400 mt-1">Examples: Veterinary Receipt, Medical Certificate, Official Receipt</p>
                            </label>
                        </div>
                        <p class="mt-1 text-xs text-neutral-400">Upload supporting documents (optional)</p>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- CLAIM SUMMARY CARD                        -->
                <!-- ========================================== -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-5 sm:p-6">
                    <h2 class="font-bold text-lg text-[#1B3B36] dark:text-white flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Claim Summary
                    </h2>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Booking</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">BK-2026-001</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Claim Type</p>
                            <p class="font-bold text-[#1B3B36] dark:text-white">Pet Attack</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Claimed Amount</p>
                            <p class="font-bold text-primary">₱2,000</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Status</p>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                                Pending
                            </span>
                        </div>
                    </div>

                    <div class="mt-4 p-3 rounded-xl bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800/50">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <p class="text-sm font-bold text-amber-700 dark:text-amber-400">Administrator Review Required</p>
                        </div>
                        <p class="text-xs text-amber-600 dark:text-amber-300 mt-1">Your claim will be reviewed by our team before approval</p>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- GUIDELINES CARD                           -->
                <!-- ========================================== -->
                <div class="bg-blue-50 dark:bg-blue-950/20 rounded-2xl border border-blue-200 dark:border-blue-800/50 p-5 sm:p-6">
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white">Protection Claim Guidelines</h3>
                            <ul class="mt-2 space-y-1 text-sm text-neutral-600 dark:text-neutral-300">
                                <li>• Submit only valid protection claims.</li>
                                <li>• Provide complete and accurate incident details.</li>
                                <li>• Upload supporting evidence whenever possible.</li>
                                <li>• Claims are reviewed by the administrator before approval.</li>
                                <li>• Compensation, if approved, is based on the investigation results.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- ACTION BUTTONS                            -->
                <!-- ========================================== -->
                <div class="flex flex-col sm:flex-row items-center gap-3 pt-2">
                    <a href="{{ route('claims.index') }}"
                       class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-sm rounded-xl hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-primary hover:bg-primary-600 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Submit Protection Claim
                    </button>
                </div>

            </form>

        </div>
    </div>
</x-app-layout>