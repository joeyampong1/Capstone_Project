<aside id="mainSidebar"
       x-data
       :class="{
           'w-64': sidebarOpen,
           'w-16': !sidebarOpen && isDesktop,
           '-translate-x-full': !sidebarOpen && !isDesktop,
           'translate-x-0': sidebarOpen && !isDesktop
       }"
       class="fixed top-0 left-0 h-full
              bg-white dark:bg-neutral-900
              border-r border-gray-200 dark:border-neutral-800
              z-40
              overflow-y-auto
              shadow-lg
              transition-all duration-300 ease-in-out
              lg:translate-x-0">
    
    <!-- Sidebar Header -->
    <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200 dark:border-neutral-800">
                <button @click="sidebarOpen = !sidebarOpen" 
                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-neutral-500 hover:text-primary hover:bg-primary/10 focus:outline-none transition duration-150 ease-in-out flex-shrink-0 mr-auto -ml-70">
                    <!-- Show ">" when sidebar is closed -->
                    <svg x-show="!sidebarOpen" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                    </svg>
                    <!-- Show "<" when sidebar is open -->
                    <svg x-show="sidebarOpen" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

        <!-- Close button (mobile only) -->
        <button @click="sidebarOpen = false" 
                class="p-1.5 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition lg:hidden">
            <svg class="w-5 h-5 text-neutral-600 dark:text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Navigation Links -->
    <nav class="p-4 space-y-1">
        <!-- Payments -->
        <a href="#" class="flex items-center gap-3 px-3 py-3 rounded-xl transition text-neutral-600 dark:text-neutral-300 hover:bg-primary/5 hover:text-primary">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
            <span x-show="sidebarOpen || !isDesktop" class="text-base font-bold whitespace-nowrap">Payments</span>
        </a>
        
        <!-- Visit Logs (sitter only) -->
        @if(auth()->user()->is_sitter)
        <a href="#" class="flex items-center gap-3 px-3 py-3 rounded-xl transition text-neutral-600 dark:text-neutral-300 hover:bg-primary/5 hover:text-primary">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <span x-show="sidebarOpen || !isDesktop" class="text-base font-bold whitespace-nowrap">Visit Logs</span>
        </a>
        @endif
        
        <!-- Task Reports -->
        <a href="#" class="flex items-center gap-3 px-3 py-3 rounded-xl transition text-neutral-600 dark:text-neutral-300 hover:bg-primary/5 hover:text-primary">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
            </svg>
            <span x-show="sidebarOpen || !isDesktop" class="text-base font-bold whitespace-nowrap">Task Reports</span>
        </a>
        
        <!-- Invoices -->
        <a href="#" class="flex items-center gap-3 px-3 py-3 rounded-xl transition text-neutral-600 dark:text-neutral-300 hover:bg-primary/5 hover:text-primary">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span x-show="sidebarOpen || !isDesktop" class="text-base font-bold whitespace-nowrap">Invoices</span>
        </a>
        
        <hr class="border-gray-200 dark:border-neutral-800 my-2">
        
        <!-- Help & Support -->
        <a href="#" class="flex items-center gap-3 px-3 py-3 rounded-xl transition text-neutral-600 dark:text-neutral-300 hover:bg-primary/5 hover:text-primary">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-5.657-1.414a1 1 0 011.414 1.414"/>
            </svg>
            <span x-show="sidebarOpen || !isDesktop" class="text-base font-bold whitespace-nowrap">Help & Support</span>
        </a>
        
        <!-- Settings -->
        <a href="#" class="flex items-center gap-3 px-3 py-3 rounded-xl transition text-neutral-600 dark:text-neutral-300 hover:bg-primary/5 hover:text-primary">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span x-show="sidebarOpen || !isDesktop" class="text-base font-bold whitespace-nowrap">Settings</span>
        </a>
    </nav>
</aside>