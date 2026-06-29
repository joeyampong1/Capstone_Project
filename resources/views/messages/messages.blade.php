<x-app-layout>
    <!-- ========================================== -->
    <!-- HEADER SLOT – sticky                       -->
    <!-- ========================================== -->
    <x-slot name="header">
        <div class="sticky top-0 z-10 bg-white dark:bg-neutral-900 flex items-center gap-3">
            <a href="{{ route('find.sitter') }}" 
               class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                    Messages
                </h1>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">Your conversations with sitters and owners</p>
            </div>
        </div>
    </x-slot>

    <!-- ========================================== -->
    <!-- MAIN CONTENT – fixed height, flex column   -->
    <!-- ========================================== -->
    <div class="bg-white dark:bg-neutral-950 antialiased text-neutral-800 dark:text-neutral-200 h-[calc(100vh-150px)] flex flex-col">
        <div class="max-w-4xl mx-auto px-12 sm:px-16 lg:px-24 w-full flex flex-col h-full">

            <!-- ========================================== -->
            <!-- SEARCH BAR                                -->
            <!-- ========================================== -->
            <div class="mb-4 flex-shrink-0 pt-4 sm:pt-6">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" 
                           placeholder="Search conversations..." 
                           class="w-full pl-9 pr-4 py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-900 text-sm text-neutral-800 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                </div>
            </div>

            <!-- ========================================== -->
            <!-- CONVERSATION LIST – scrollable            -->
            <!-- ========================================== -->
            <div class="flex-1 overflow-y-auto">
                <div class="bg-white dark:bg-neutral-900 rounded-3xl shadow-xl border border-gray-200/60 dark:border-neutral-800 divide-y divide-gray-100 dark:divide-neutral-800">

                    <!-- ========================================== -->
                    <!-- CONVERSATION: Maria Santos (Sitter)       -->
                    <!-- ========================================== -->
                    <a href="{{ route('owner.messages', ['id' => 1]) }}" 
                       class="flex items-center gap-4 p-4 sm:p-5 hover:bg-primary/5 dark:hover:bg-neutral-800/50 transition">
                        <div class="relative flex-shrink-0">
                            <img src="https://ui-avatars.com/api/?name=Maria+Santos&background=f07a3a&color=fff&size=48" 
                                 alt="Maria Santos" 
                                 class="w-12 h-12 rounded-full object-cover">
                            <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white dark:border-neutral-900"></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-baseline justify-between gap-2">
                                <h3 class="text-sm font-black text-[#1B3B36] dark:text-white truncate">Maria Santos</h3>
                                <span class="text-[10px] text-neutral-400 whitespace-nowrap">2 min ago</span>
                            </div>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-xs text-neutral-500 dark:text-neutral-400 truncate">Hi there! 👋 I'm available on the dates you requested.</span>
                                <span class="inline-flex w-2 h-2 rounded-full bg-primary flex-shrink-0" title="Unread"></span>
                            </div>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-medium bg-accent/10 text-accent dark:bg-accent/20">Pet Sitter</span>
                            </div>
                        </div>
                    </a>

                    <!-- ========================================== -->
                    <!-- CONVERSATION: Juan Dela Cruz (Owner)      -->
                    <!-- ========================================== -->
                    <a href="{{ route('owner.messages', ['id' => 2]) }}" 
                       class="flex items-center gap-4 p-4 sm:p-5 hover:bg-primary/5 dark:hover:bg-neutral-800/50 transition">
                        <div class="relative flex-shrink-0">
                            <img src="https://ui-avatars.com/api/?name=Juan+Dela+Cruz&background=4a90d9&color=fff&size=48" 
                                 alt="Juan Dela Cruz" 
                                 class="w-12 h-12 rounded-full object-cover">
                            <span class="absolute bottom-0 right-0 w-3 h-3 bg-gray-400 rounded-full border-2 border-white dark:border-neutral-900"></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-baseline justify-between gap-2">
                                <h3 class="text-sm font-black text-[#1B3B36] dark:text-white truncate">Juan Dela Cruz</h3>
                                <span class="text-[10px] text-neutral-400 whitespace-nowrap">1 hour ago</span>
                            </div>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-xs text-neutral-500 dark:text-neutral-400 truncate">Thanks for the update! See you tomorrow.</span>
                            </div>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-medium bg-secondary/30 text-neutral-600 dark:bg-neutral-800/30 dark:text-neutral-400">Pet Owner</span>
                            </div>
                        </div>
                    </a>

                    <!-- ========================================== -->
                    <!-- CONVERSATION: Anna Reyes (Owner)          -->
                    <!-- ========================================== -->
                    <a href="{{ route('owner.messages', ['id' => 3]) }}" 
                       class="flex items-center gap-4 p-4 sm:p-5 hover:bg-primary/5 dark:hover:bg-neutral-800/50 transition">
                        <div class="relative flex-shrink-0">
                            <img src="https://ui-avatars.com/api/?name=Anna+Reyes&background=e67e22&color=fff&size=48" 
                                 alt="Anna Reyes" 
                                 class="w-12 h-12 rounded-full object-cover">
                            <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white dark:border-neutral-900"></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-baseline justify-between gap-2">
                                <h3 class="text-sm font-black text-[#1B3B36] dark:text-white truncate">Anna Reyes</h3>
                                <span class="text-[10px] text-neutral-400 whitespace-nowrap">Yesterday</span>
                            </div>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-xs text-neutral-500 dark:text-neutral-400 truncate">How many visits can you do in a day?</span>
                                <span class="inline-flex w-2 h-2 rounded-full bg-primary flex-shrink-0" title="Unread"></span>
                            </div>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-medium bg-secondary/30 text-neutral-600 dark:bg-neutral-800/30 dark:text-neutral-400">Pet Owner</span>
                            </div>
                        </div>
                    </a>

                    <!-- ========================================== -->
                    <!-- CONVERSATION: Pedro Gomez (Sitter)        -->
                    <!-- ========================================== -->
                    <a href="{{ route('owner.messages', ['id' => 4]) }}" 
                       class="flex items-center gap-4 p-4 sm:p-5 hover:bg-primary/5 dark:hover:bg-neutral-800/50 transition">
                        <div class="relative flex-shrink-0">
                            <img src="https://ui-avatars.com/api/?name=Pedro+Gomez&background=27ae60&color=fff&size=48" 
                                 alt="Pedro Gomez" 
                                 class="w-12 h-12 rounded-full object-cover">
                            <span class="absolute bottom-0 right-0 w-3 h-3 bg-gray-400 rounded-full border-2 border-white dark:border-neutral-900"></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-baseline justify-between gap-2">
                                <h3 class="text-sm font-black text-[#1B3B36] dark:text-white truncate">Pedro Gomez</h3>
                                <span class="text-[10px] text-neutral-400 whitespace-nowrap">2 days ago</span>
                            </div>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-xs text-neutral-500 dark:text-neutral-400 truncate">Do you accept rabbits?</span>
                            </div>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-medium bg-accent/10 text-accent dark:bg-accent/20">Pet Sitter</span>
                            </div>
                        </div>
                    </a>

                </div>

                <!-- ========================================== -->
                <!-- EMPTY STATE (hidden unless enabled)        -->
                <!-- ========================================== -->
                @if(false)
                <div class="bg-white dark:bg-neutral-900 rounded-3xl shadow-xl border border-gray-200/60 dark:border-neutral-800 p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-neutral-300 dark:text-neutral-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <h3 class="text-xl font-black text-[#1B3B36] dark:text-white">No messages yet</h3>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">Start a conversation by contacting a sitter or owner.</p>
                    <a href="{{ route('find.sitter') }}" class="inline-block mt-4 px-6 py-3 bg-primary text-white font-bold text-sm rounded-xl hover:bg-primary-600 transition">
                        Find a Sitter
                    </a>
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>