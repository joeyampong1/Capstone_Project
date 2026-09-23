<x-app-layout>
    <!-- ========================================== -->
    <!-- HEADER SLOT                               -->
    <!-- ========================================== -->
    <x-slot name="header">
        <div x-data class="flex items-center justify-between w-full">
            <div class="flex items-center gap-3">
                <a :href="$store.app.role === 'sitter' ? '{{ route('sitter.dashboard') }}' : '{{ route('dashboard') }}'" 
                   class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                        Protection Claims
                    </h1>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">View and manage your submitted protection claims</p>
                </div>
            </div>
            <!-- No action button here anymore -->
        </div>
    </x-slot>

    <!-- ========================================== -->
    <!-- MAIN CONTENT                              -->
    <!-- ========================================== -->
    <div x-data="{ filter: 'all' }" class="py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- ========================================== -->
            <!-- ① SUBMIT CLAIM CARD (NEW)                -->
            <!-- ========================================== -->
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-5 sm:p-6 mb-6 hover:shadow-md transition">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13l-3 3-3-3"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-[#1B3B36] dark:text-white">Submit a Protection Claim</h3>
                            <p class="text-sm text-neutral-500 dark:text-neutral-400">File a claim related to an existing booking</p>
                        </div>
                    </div>
                    <a href="{{ route('claims.create') }}" 
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-primary hover:bg-primary-600 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Submit Claim
                    </a>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- ② SUMMARY CARDS                          -->
            <!-- ========================================== -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
                <!-- Total Claims -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Total Claims</p>
                    <h3 class="font-bold text-2xl mt-1 text-[#1B3B36] dark:text-white">
                        <span x-show="$store.app.role === 'owner'">5</span>
                        <span x-show="$store.app.role === 'sitter'">3</span>
                    </h3>
                    <p class="text-xs text-neutral-400 mt-1">All claims</p>
                </div>

                <!-- Pending -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Pending</p>
                    <h3 class="font-bold text-2xl mt-1 text-amber-600 dark:text-amber-400">
                        <span x-show="$store.app.role === 'owner'">1</span>
                        <span x-show="$store.app.role === 'sitter'">1</span>
                    </h3>
                    <p class="text-xs text-amber-600 dark:text-amber-400 mt-1">Awaiting review</p>
                </div>

                <!-- Under Review -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Under Review</p>
                    <h3 class="font-bold text-2xl mt-1 text-blue-600 dark:text-blue-400">
                        <span x-show="$store.app.role === 'owner'">2</span>
                        <span x-show="$store.app.role === 'sitter'">1</span>
                    </h3>
                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">In investigation</p>
                </div>

                <!-- Approved -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Approved</p>
                    <h3 class="font-bold text-2xl mt-1 text-green-600 dark:text-green-400">
                        <span x-show="$store.app.role === 'owner'">1</span>
                        <span x-show="$store.app.role === 'sitter'">1</span>
                    </h3>
                    <p class="text-xs text-green-600 dark:text-green-400 mt-1">Compensation released</p>
                </div>

                <!-- Rejected -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Rejected</p>
                    <h3 class="font-bold text-2xl mt-1 text-red-600 dark:text-red-400">
                        <span x-show="$store.app.role === 'owner'">1</span>
                        <span x-show="$store.app.role === 'sitter'">0</span>
                    </h3>
                    <p class="text-xs text-red-500 dark:text-red-400 mt-1">Not approved</p>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- ③ CLAIM INFORMATION                      -->
            <!-- ========================================== -->
            <div class="bg-blue-50 dark:bg-blue-950/20 rounded-2xl border border-blue-200 dark:border-blue-800/50 p-5 sm:p-6 mb-6">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white">Protection Claim Information</h3>
                        <ul class="mt-2 space-y-1 text-sm text-neutral-600 dark:text-neutral-300">
                            <li>• Claims must be related to an existing booking.</li>
                            <li>• Upload supporting evidence to help the investigation.</li>
                            <li>• Claims are reviewed by the administrator.</li>
                            <li>• Approved claims may receive compensation based on the investigation.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- ④ FILTER TABS                            -->
            <!-- ========================================== -->
            <div class="mb-6">
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
                    <button @click="filter = 'under_review'" 
                            :class="filter === 'under_review' ? 'bg-blue-500 text-white shadow-md' : 'text-neutral-600 dark:text-neutral-400 hover:bg-white/50 dark:hover:bg-neutral-700'"
                            class="px-4 py-2 text-xs font-bold rounded-lg transition">
                        Under Review
                    </button>
                    <button @click="filter = 'approved'" 
                            :class="filter === 'approved' ? 'bg-green-500 text-white shadow-md' : 'text-neutral-600 dark:text-neutral-400 hover:bg-white/50 dark:hover:bg-neutral-700'"
                            class="px-4 py-2 text-xs font-bold rounded-lg transition">
                        Approved
                    </button>
                    <button @click="filter = 'rejected'" 
                            :class="filter === 'rejected' ? 'bg-red-500 text-white shadow-md' : 'text-neutral-600 dark:text-neutral-400 hover:bg-white/50 dark:hover:bg-neutral-700'"
                            class="px-4 py-2 text-xs font-bold rounded-lg transition">
                        Rejected
                    </button>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- ⑤ CLAIMS LIST                            -->
            <!-- ========================================== -->
            
            <!-- ========================================== -->
            <!-- OWNER MODE - Claims filed by owner        -->
            <!-- ========================================== -->
            <template x-if="$store.app.role === 'owner'">
                <div class="space-y-4 mb-6">

                    <!-- Claim 1 - Pending -->
                    <div x-show="filter === 'all' || filter === 'pending'" 
                         class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-5 sm:p-6 hover:shadow-md transition">
                        <div class="flex items-start justify-between">
                            <div class="space-y-2">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                                        Pending
                                    </span>
                                    <span class="text-xs text-neutral-500 dark:text-neutral-400">Submitted: Dec 16, 2026</span>
                                </div>
                                <p class="font-bold text-[#1B3B36] dark:text-white">Claim #: CLM-2026-001</p>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-sm">
                                    <p><span class="text-neutral-500 dark:text-neutral-400">Booking:</span> <span class="font-bold">BK-2026-001</span></p>
                                    <p><span class="text-neutral-500 dark:text-neutral-400">Claim Type:</span> <span class="font-bold">Pet Injury</span></p>
                                    <p><span class="text-neutral-500 dark:text-neutral-400">Incident Date:</span> <span class="font-bold">Dec 15, 2026</span></p>
                                    <p><span class="text-neutral-500 dark:text-neutral-400">Claimed Amount:</span> <span class="font-bold text-primary">₱2,500</span></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Status Date</p>
                                <p class="text-sm font-bold text-amber-600 dark:text-amber-400">Submitted: Dec 16</p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-wrap items-center gap-3 mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                            <a href="#" class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary/10 text-primary font-bold text-xs rounded-lg hover:bg-primary/20 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                View Details
                            </a>
                            <button class="inline-flex items-center gap-1.5 px-4 py-2 border-2 border-red-500 text-red-500 font-bold text-xs rounded-lg hover:bg-red-50 dark:hover:bg-red-950/20 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Cancel Claim
                            </button>
                        </div>
                    </div>

                    <!-- Claim 2 - Under Review -->
                    <div x-show="filter === 'all' || filter === 'under_review'" 
                         class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-5 sm:p-6 hover:shadow-md transition">
                        <div class="flex items-start justify-between">
                            <div class="space-y-2">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                        Under Review
                                    </span>
                                    <span class="text-xs text-neutral-500 dark:text-neutral-400">Submitted: Dec 20, 2026</span>
                                </div>
                                <p class="font-bold text-[#1B3B36] dark:text-white">Claim #: CLM-2026-002</p>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-sm">
                                    <p><span class="text-neutral-500 dark:text-neutral-400">Booking:</span> <span class="font-bold">BK-2026-005</span></p>
                                    <p><span class="text-neutral-500 dark:text-neutral-400">Claim Type:</span> <span class="font-bold">Missed Visit</span></p>
                                    <p><span class="text-neutral-500 dark:text-neutral-400">Incident Date:</span> <span class="font-bold">Dec 19, 2026</span></p>
                                    <p><span class="text-neutral-500 dark:text-neutral-400">Claimed Amount:</span> <span class="font-bold text-primary">₱800</span></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Status Date</p>
                                <p class="text-sm font-bold text-blue-600 dark:text-blue-400">Submitted: Dec 20</p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-wrap items-center gap-3 mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                            <a href="#" class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary/10 text-primary font-bold text-xs rounded-lg hover:bg-primary/20 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                View Details
                            </a>
                        </div>
                    </div>

                    <!-- Claim 3 - Approved -->
                    <div x-show="filter === 'all' || filter === 'approved'" 
                         class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-5 sm:p-6 hover:shadow-md transition">
                        <div class="flex items-start justify-between">
                            <div class="space-y-2">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                        Approved
                                    </span>
                                    <span class="text-xs text-neutral-500 dark:text-neutral-400">Released: Dec 25, 2026</span>
                                </div>
                                <p class="font-bold text-[#1B3B36] dark:text-white">Claim #: CLM-2026-003</p>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-sm">
                                    <p><span class="text-neutral-500 dark:text-neutral-400">Booking:</span> <span class="font-bold">BK-2026-010</span></p>
                                    <p><span class="text-neutral-500 dark:text-neutral-400">Claim Type:</span> <span class="font-bold">Pet Attack</span></p>
                                    <p><span class="text-neutral-500 dark:text-neutral-400">Incident Date:</span> <span class="font-bold">Dec 22, 2026</span></p>
                                    <p><span class="text-neutral-500 dark:text-neutral-400">Claimed Amount:</span> <span class="font-bold text-primary">₱5,000</span></p>
                                    <p><span class="text-neutral-500 dark:text-neutral-400">Approved Amount:</span> <span class="font-bold text-green-600">₱4,200</span></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Status Date</p>
                                <p class="text-sm font-bold text-green-600 dark:text-green-400">Released: Dec 25</p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-wrap items-center gap-3 mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                            <a href="#" class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary/10 text-primary font-bold text-xs rounded-lg hover:bg-primary/20 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                View Details
                            </a>
                        </div>
                    </div>

                    <!-- Claim 4 - Rejected -->
                    <div x-show="filter === 'all' || filter === 'rejected'" 
                         class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-5 sm:p-6 hover:shadow-md transition">
                        <div class="flex items-start justify-between">
                            <div class="space-y-2">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                        Rejected
                                    </span>
                                    <span class="text-xs text-neutral-500 dark:text-neutral-400">Submitted: Dec 18, 2026</span>
                                </div>
                                <p class="font-bold text-[#1B3B36] dark:text-white">Claim #: CLM-2026-004</p>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-sm">
                                    <p><span class="text-neutral-500 dark:text-neutral-400">Booking:</span> <span class="font-bold">BK-2026-012</span></p>
                                    <p><span class="text-neutral-500 dark:text-neutral-400">Claim Type:</span> <span class="font-bold">Pet Injury</span></p>
                                    <p><span class="text-neutral-500 dark:text-neutral-400">Incident Date:</span> <span class="font-bold">Dec 17, 2026</span></p>
                                    <p><span class="text-neutral-500 dark:text-neutral-400">Claimed Amount:</span> <span class="font-bold text-primary">₱1,500</span></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Status Date</p>
                                <p class="text-sm font-bold text-red-600 dark:text-red-400">Rejected: Dec 21</p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-wrap items-center gap-3 mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                            <a href="#" class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary/10 text-primary font-bold text-xs rounded-lg hover:bg-primary/20 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                View Details
                            </a>
                        </div>
                    </div>

                </div>
            </template>

            <!-- ========================================== -->
            <!-- SITTER MODE - Claims filed by sitter       -->
            <!-- ========================================== -->
            <template x-if="$store.app.role === 'sitter'">
                <div class="space-y-4 mb-6">

                    <!-- Sitter Claim 1 - Pending -->
                    <div x-show="filter === 'all' || filter === 'pending'" 
                         class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-5 sm:p-6 hover:shadow-md transition">
                        <div class="flex items-start justify-between">
                            <div class="space-y-2">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                                        Pending
                                    </span>
                                    <span class="text-xs text-neutral-500 dark:text-neutral-400">Submitted: Dec 19, 2026</span>
                                </div>
                                <p class="font-bold text-[#1B3B36] dark:text-white">Claim #: CLM-2026-005</p>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-sm">
                                    <p><span class="text-neutral-500 dark:text-neutral-400">Booking:</span> <span class="font-bold">BK-2026-008</span></p>
                                    <p><span class="text-neutral-500 dark:text-neutral-400">Claim Type:</span> <span class="font-bold">No Proof</span></p>
                                    <p><span class="text-neutral-500 dark:text-neutral-400">Incident Date:</span> <span class="font-bold">Dec 18, 2026</span></p>
                                    <p><span class="text-neutral-500 dark:text-neutral-400">Claimed Amount:</span> <span class="font-bold text-primary">₱350</span></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Status Date</p>
                                <p class="text-sm font-bold text-amber-600 dark:text-amber-400">Submitted: Dec 19</p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-wrap items-center gap-3 mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                            <a href="#" class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary/10 text-primary font-bold text-xs rounded-lg hover:bg-primary/20 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                View Details
                            </a>
                            <button class="inline-flex items-center gap-1.5 px-4 py-2 border-2 border-red-500 text-red-500 font-bold text-xs rounded-lg hover:bg-red-50 dark:hover:bg-red-950/20 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Cancel Claim
                            </button>
                        </div>
                    </div>

                    <!-- Sitter Claim 2 - Under Review -->
                    <div x-show="filter === 'all' || filter === 'under_review'" 
                         class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-5 sm:p-6 hover:shadow-md transition">
                        <div class="flex items-start justify-between">
                            <div class="space-y-2">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                        Under Review
                                    </span>
                                    <span class="text-xs text-neutral-500 dark:text-neutral-400">Submitted: Dec 22, 2026</span>
                                </div>
                                <p class="font-bold text-[#1B3B36] dark:text-white">Claim #: CLM-2026-006</p>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-sm">
                                    <p><span class="text-neutral-500 dark:text-neutral-400">Booking:</span> <span class="font-bold">BK-2026-015</span></p>
                                    <p><span class="text-neutral-500 dark:text-neutral-400">Claim Type:</span> <span class="font-bold">Pet Injury</span></p>
                                    <p><span class="text-neutral-500 dark:text-neutral-400">Incident Date:</span> <span class="font-bold">Dec 21, 2026</span></p>
                                    <p><span class="text-neutral-500 dark:text-neutral-400">Claimed Amount:</span> <span class="font-bold text-primary">₱1,200</span></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Status Date</p>
                                <p class="text-sm font-bold text-blue-600 dark:text-blue-400">Submitted: Dec 22</p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-wrap items-center gap-3 mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                            <a href="#" class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary/10 text-primary font-bold text-xs rounded-lg hover:bg-primary/20 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                View Details
                            </a>
                        </div>
                    </div>

                    <!-- Sitter Claim 3 - Approved -->
                    <div x-show="filter === 'all' || filter === 'approved'" 
                         class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-5 sm:p-6 hover:shadow-md transition">
                        <div class="flex items-start justify-between">
                            <div class="space-y-2">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                        Approved
                                    </span>
                                    <span class="text-xs text-neutral-500 dark:text-neutral-400">Released: Dec 28, 2026</span>
                                </div>
                                <p class="font-bold text-[#1B3B36] dark:text-white">Claim #: CLM-2026-007</p>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-sm">
                                    <p><span class="text-neutral-500 dark:text-neutral-400">Booking:</span> <span class="font-bold">BK-2026-018</span></p>
                                    <p><span class="text-neutral-500 dark:text-neutral-400">Claim Type:</span> <span class="font-bold">Missed Visit</span></p>
                                    <p><span class="text-neutral-500 dark:text-neutral-400">Incident Date:</span> <span class="font-bold">Dec 24, 2026</span></p>
                                    <p><span class="text-neutral-500 dark:text-neutral-400">Claimed Amount:</span> <span class="font-bold text-primary">₱2,000</span></p>
                                    <p><span class="text-neutral-500 dark:text-neutral-400">Approved Amount:</span> <span class="font-bold text-green-600">₱1,800</span></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Status Date</p>
                                <p class="text-sm font-bold text-green-600 dark:text-green-400">Released: Dec 28</p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-wrap items-center gap-3 mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                            <a href="#" class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary/10 text-primary font-bold text-xs rounded-lg hover:bg-primary/20 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                View Details
                            </a>
                        </div>
                    </div>

                </div>
            </template>

        </div>
    </div>
</x-app-layout>