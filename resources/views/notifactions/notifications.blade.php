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
                        Notifications
                    </h1>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">Stay updated with your latest activity</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-primary/10 text-primary">
                    3 unread
                </span>
            </div>
        </div>
    </x-slot>

    <!-- ========================================== -->
    <!-- MAIN CONTENT                              -->
    <!-- ========================================== -->
    <div class="bg-white dark:bg-neutral-950 antialiased text-neutral-800 dark:text-neutral-200 h-[calc(100vh-150px)] flex flex-col">
        <div class="max-w-4xl mx-auto px-12 sm:px-16 lg:px-24 w-full flex flex-col h-full">

            <!-- ========================================== -->
            <!-- TABS & ACTIONS                            -->
            <!-- ========================================== -->
            <div class="flex items-center justify-between mb-4 flex-shrink-0 pt-4 sm:pt-6">
                <div class="flex items-center gap-2 bg-neutral-100 dark:bg-neutral-800 p-1 rounded-xl">
                    <a href="#" 
                       class="px-4 py-1.5 text-xs font-bold rounded-lg transition bg-primary text-white shadow-sm">
                        All
                    </a>
                    <a href="#" 
                       class="px-4 py-1.5 text-xs font-bold rounded-lg transition text-neutral-600 dark:text-neutral-400 hover:bg-white/50 dark:hover:bg-neutral-700">
                        Unread
                    </a>
                </div>
                <button type="button" class="text-xs text-primary font-semibold hover:underline">
                    Mark all as read
                </button>
            </div>

            <!-- ========================================== -->
            <!-- NOTIFICATION LIST                         -->
            <!-- ========================================== -->
            <div class="flex-1 overflow-y-auto">
                <div class="bg-white dark:bg-neutral-900 rounded-3xl shadow-xl border border-gray-200/60 dark:border-neutral-800 divide-y divide-gray-100 dark:divide-neutral-800">

                    <!-- ========================================== -->
                    <!-- NOTIFICATION 1: Booking Confirmed         -->
                    <!-- ========================================== -->
                    <div class="flex items-start gap-4 p-4 sm:p-5 hover:bg-primary/5 dark:hover:bg-neutral-800/50 transition">
                        <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400 flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-baseline justify-between gap-2">
                                <p class="text-sm text-neutral-800 dark:text-white">
                                    <span class="font-bold">Booking confirmed</span> with Maria Santos
                                </p>
                                <span class="text-[10px] text-neutral-400 whitespace-nowrap">2 min ago</span>
                            </div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">Your booking for 2 visits per day has been confirmed.</p>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="inline-flex w-2 h-2 rounded-full bg-primary" title="Unread"></span>
                                <a href="#" class="text-xs text-primary font-semibold hover:underline">View booking</a>
                            </div>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- NOTIFICATION 2: New Message               -->
                    <!-- ========================================== -->
                    <div class="flex items-start gap-4 p-4 sm:p-5 hover:bg-primary/5 dark:hover:bg-neutral-800/50 transition">
                        <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-baseline justify-between gap-2">
                                <p class="text-sm text-neutral-800 dark:text-white">
                                    <span class="font-bold">New message</span> from Juan Dela Cruz
                                </p>
                                <span class="text-[10px] text-neutral-400 whitespace-nowrap">1 hour ago</span>
                            </div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">"Thanks for the update! See you tomorrow."</p>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="inline-flex w-2 h-2 rounded-full bg-primary" title="Unread"></span>
                                <a href="#" class="text-xs text-primary font-semibold hover:underline">Reply now</a>
                            </div>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- NOTIFICATION 3: Reminder                  -->
                    <!-- ========================================== -->
                    <div class="flex items-start gap-4 p-4 sm:p-5 hover:bg-primary/5 dark:hover:bg-neutral-800/50 transition">
                        <div class="w-10 h-10 rounded-full bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center text-yellow-600 dark:text-yellow-400 flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-baseline justify-between gap-2">
                                <p class="text-sm text-neutral-800 dark:text-white">
                                    <span class="font-bold">Reminder</span> – Upcoming visit tomorrow
                                </p>
                                <span class="text-[10px] text-neutral-400 whitespace-nowrap">3 hours ago</span>
                            </div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">You have a scheduled visit with Maria Santos at 9:00 AM.</p>
                            <div class="flex items-center gap-2 mt-2">
                                <a href="#" class="text-xs text-primary font-semibold hover:underline">View details</a>
                            </div>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- NOTIFICATION 4: Sitter Response           -->
                    <!-- ========================================== -->
                    <div class="flex items-start gap-4 p-4 sm:p-5 hover:bg-primary/5 dark:hover:bg-neutral-800/50 transition">
                        <div class="w-10 h-10 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400 flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-baseline justify-between gap-2">
                                <p class="text-sm text-neutral-800 dark:text-white">
                                    <span class="font-bold">Sitter responded</span> to your question
                                </p>
                                <span class="text-[10px] text-neutral-400 whitespace-nowrap">Yesterday</span>
                            </div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">Maria Santos replied to your question about food preferences.</p>
                            <div class="flex items-center gap-2 mt-2">
                                <a href="#" class="text-xs text-primary font-semibold hover:underline">View conversation</a>
                            </div>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- NOTIFICATION 5: Review Received           -->
                    <!-- ========================================== -->
                    <div class="flex items-start gap-4 p-4 sm:p-5 hover:bg-primary/5 dark:hover:bg-neutral-800/50 transition">
                        <div class="w-10 h-10 rounded-full bg-pink-100 dark:bg-pink-900/30 flex items-center justify-center text-pink-600 dark:text-pink-400 flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-baseline justify-between gap-2">
                                <p class="text-sm text-neutral-800 dark:text-white">
                                    <span class="font-bold">New review</span> from Anna Reyes
                                </p>
                                <span class="text-[10px] text-neutral-400 whitespace-nowrap">2 days ago</span>
                            </div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">"Maria was amazing with my two cats! Highly recommend!" ★★★★★</p>
                            <div class="flex items-center gap-2 mt-2">
                                <a href="#" class="text-xs text-primary font-semibold hover:underline">Read review</a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- ========================================== -->
            <!-- EMPTY STATE (hidden unless enabled)        -->
            <!-- ========================================== -->
            @if(false)
            <div class="flex-1 flex items-center justify-center">
                <div class="bg-white dark:bg-neutral-900 rounded-3xl shadow-xl border border-gray-200/60 dark:border-neutral-800 p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-neutral-300 dark:text-neutral-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <h3 class="text-xl font-black text-[#1B3B36] dark:text-white">No notifications yet</h3>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">We'll let you know when something happens.</p>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>