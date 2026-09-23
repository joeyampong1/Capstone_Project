<nav x-data="{ open: false, searchOpen: false }" class="bg-white dark:bg-neutral-900 border-b border-neutral-100 dark:border-neutral-800 sticky top-0 z-50 shadow-sm">
    <div class="w-full mx-auto px-2 sm:px-16 lg:px-24">
        <div class="flex justify-between h-16 relative">

            <!-- ========================================== -->
            <!-- LEFT SIDE: toggle + logo + page title      -->
            <!-- ========================================== -->
            <div class="flex items-center gap-2 sm:gap-4">

                @auth
                    @if(auth()->user()->isAdmin())
                        <!-- Admin: Sidebar Toggle (mobile only) -->
                        <button @click="sidebarOpen = !sidebarOpen" 
                                class="lg:hidden inline-flex items-center justify-center w-9 h-9 rounded-xl text-neutral-500 hover:text-primary hover:bg-primary/10 focus:outline-none transition duration-150 ease-in-out">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        <!-- Admin: Logo -->
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('admin.home') }}" class="flex items-center">
                                <img src="{{ asset('assets/logo/PetNanny_Logo.png') }}" alt="PetNanny Logo" class="h-9 w-auto block dark:hidden">
                                <img src="{{ asset('assets/logo/PetNanny_Logo.png') }}" alt="PetNanny Logo" class="h-9 w-auto hidden dark:block invert">
                                <span class="ml-2 text-xl font-black text-[#1B3B36] dark:text-white hidden sm:block">
                                    Pet<span class="text-primary">Nanny</span>
                                </span>
                            </a>
                        </div>

                    @else
                        <!-- ========================================== -->
                        <!-- OWNER/SITTER: Toggle + Logo + Nav Links    -->
                        <!-- ========================================== -->
                        <div class="shrink-0 flex items-center gap-1">

                            <!-- Toggle Button (mobile only) -->
                            <button @click="sidebarOpen = !sidebarOpen" 
                                    class="lg:hidden inline-flex items-center justify-center w-8 h-8 rounded-lg text-neutral-500 hover:text-primary hover:bg-primary/10 transition duration-150">
                                <svg x-show="!sidebarOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                                <svg x-show="sidebarOpen" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>

                            <!-- Logo -->
                            <a href="{{ route('dashboard') }}" class="flex items-center">
                                <img src="{{ asset('assets/logo/PetNanny_Logo.png') }}" alt="PetNanny Logo" class="h-9 w-auto block dark:hidden">
                                <img src="{{ asset('assets/logo/PetNanny_Logo.png') }}" alt="PetNanny Logo" class="h-9 w-auto hidden dark:block invert">
                                <span class="ml-2 text-xl font-black text-[#1B3B36] dark:text-white hidden sm:block">
                                    Pet<span class="text-primary">Nanny</span>
                                </span>
                            </a>
                        </div>

                        <!-- Desktop Nav Links (owner/sitter) -->
                        <div class="hidden space-x-1 sm:-my-px sm:flex items-center">
                            <a href="{{ route('dashboard') }}" 
                               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-extrabold rounded-xl transition-all duration-150 
                               {{ request()->routeIs('dashboard') ? 'bg-primary text-white shadow-sm hover:bg-primary-600' : 'text-neutral-600 hover:text-primary hover:bg-primary-50 dark:text-neutral-300 dark:hover:text-white dark:hover:bg-neutral-800' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                {{ __('messages.nav_home') }}
                            </a>

                            <a href="{{ route('find.sitter') }}" 
                               class="inline-flex items-center gap-2.5 px-4 py-2 text-sm font-bold rounded-xl transition-all duration-150 
                               {{ request()->routeIs('find.sitter') ? 'bg-primary text-white shadow-sm hover:bg-primary-600' : 'text-neutral-600 hover:text-primary hover:bg-primary-50 dark:text-neutral-300 dark:hover:text-white dark:hover:bg-neutral-800' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                {{ __('messages.nav_find_sitters') }}
                            </a>

                            <!-- My Bookings — with BADGE -->
                            <a href="{{ route('mybookings.index') }}" 
                               class="inline-flex items-center gap-2.5 px-4 py-2 text-sm font-bold rounded-xl transition-all duration-150 relative
                               {{ request()->routeIs('mybookings.index') ? 'bg-primary text-white shadow-sm hover:bg-primary-600' : 'text-neutral-600 hover:text-primary hover:bg-primary-50 dark:text-neutral-300 dark:hover:text-white dark:hover:bg-neutral-800' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ __('messages.nav_my_bookings') }}
                                <!-- BOOKING BADGE -->
                                <span x-data
                                      x-show="$store.booking.count > 0"
                                      x-text="$store.booking.count > 99 ? '99+' : $store.booking.count"
                                      class="absolute -top-1 -right-1 flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[10px] font-extrabold bg-red-500 text-white rounded-full shadow-sm border-2 border-white dark:border-neutral-900">
                                </span>
                            </a>

                            <a href="{{ route('mypets.index') }}" 
                               class="inline-flex items-center gap-2.5 px-4 py-2 text-sm font-bold rounded-xl transition-all duration-150 
                               {{ request()->routeIs('mypets.index') ? 'bg-primary text-white shadow-sm hover:bg-primary-600' : 'text-neutral-600 hover:text-primary hover:bg-primary-50 dark:text-neutral-300 dark:hover:text-white dark:hover:bg-neutral-800' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                                </svg>
                                {{ __('messages.nav_my_pets') }}
                            </a>

                            <!-- ✅ Messages — with BADGE -->
                            <a href="{{ route('messages.index') }}" 
                               class="inline-flex items-center gap-2.5 px-4 py-2 text-sm font-bold rounded-xl transition-all duration-150 relative
                               {{ request()->routeIs('messages.index') ? 'bg-primary text-white shadow-sm hover:bg-primary-600' : 'text-neutral-600 hover:text-primary hover:bg-primary-50 dark:text-neutral-300 dark:hover:text-white dark:hover:bg-neutral-800' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                {{ __('messages.nav_messages') }}
                                <!-- MESSAGES BADGE -->
                                <span x-data
                                      x-show="$store.msg.unread > 0"
                                      x-text="$store.msg.unread > 99 ? '99+' : $store.msg.unread"
                                      class="absolute -top-1 -right-1 flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[10px] font-extrabold bg-red-500 text-white rounded-full shadow-sm border-2 border-white dark:border-neutral-900">
                                </span>
                            </a>

                            <!-- Notifications — with BADGE -->
                            <a href="{{ route('notifications.index') }}" 
                               class="inline-flex items-center gap-2.5 px-4 py-2 text-sm font-bold rounded-xl transition-all duration-150 relative
                               {{ request()->routeIs('notifications.index') ? 'bg-primary text-white shadow-sm hover:bg-primary-600' : 'text-neutral-600 hover:text-primary hover:bg-primary-50 dark:text-neutral-300 dark:hover:text-white dark:hover:bg-neutral-800' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                {{ __('messages.nav_notifications') }}
                                <!-- NOTIFICATIONS BADGE -->
                                <span x-data
                                      x-show="$store.notif.unread > 0"
                                      x-text="$store.notif.unread > 99 ? '99+' : $store.notif.unread"
                                      class="absolute -top-1 -right-1 flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[10px] font-extrabold bg-red-500 text-white rounded-full shadow-sm border-2 border-white dark:border-neutral-900">
                                </span>
                            </a>
                        </div>
                    @endif
                @endauth
            </div>

            <!-- ========================================== -->
            <!-- RIGHT SIDE: Admin / Owner-Sitter Controls  -->
            <!-- ========================================== -->

            @auth
                @if(auth()->user()->isAdmin())
                    <!-- ========================================== -->
                    <!-- ADMIN RIGHT SIDE                         -->
                    <!-- ========================================== -->
                    <div class="flex items-center gap-3">

                        <!-- Search Button (Mobile) -->
                        <button @click="searchOpen = !searchOpen" 
                                class="sm:hidden inline-flex items-center justify-center w-9 h-9 rounded-xl text-neutral-500 hover:text-primary hover:bg-primary/10 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>

                        <!-- Search Bar (Desktop) -->
                        <div class="hidden sm:flex items-center relative">
                            <div class="relative">
                                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input type="text" 
                                       placeholder="{{ __('messages.nav_search_placeholder') }}" 
                                       class="w-64 lg:w-80 pl-9 pr-4 py-2 text-sm rounded-xl border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-200 placeholder:text-neutral-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                            </div>
                        </div>

                        <!-- Dark Mode Toggle -->
                        <div x-data="{
                                dark: document.documentElement.classList.contains('dark'),
                                toggle() {
                                    this.dark = !this.dark;
                                    document.documentElement.classList.toggle('dark', this.dark);
                                    fetch('{{ route('settings.ui-preferences') }}', {
                                        method: 'PATCH',
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json',
                                        },
                                        body: JSON.stringify({ dark_mode: this.dark }),
                                    });
                                }
                            }">
                            <button @click="toggle()"
                                    class="p-2 rounded-xl hover:bg-neutral-100 dark:hover:bg-neutral-800 transition text-neutral-600 dark:text-neutral-300"
                                    title="Toggle dark mode">
                                <!-- Sun icon (shown when in dark mode) -->
                                <svg x-show="dark" x-cloak
                                    class="w-5 h-5"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                <!-- Moon icon (shown when in light mode) -->
                                <svg x-show="!dark" x-cloak
                                    class="w-5 h-5"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Notifications -->
                        <div x-data="{ notifOpen: false }" class="relative">
                            <button @click="notifOpen = !notifOpen" 
                                    class="relative p-2 rounded-xl hover:bg-neutral-100 dark:hover:bg-neutral-800 transition text-neutral-600 dark:text-neutral-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                <!-- DYNAMIC BADGE -->
                                <span x-data
                                      x-show="$store.notif.unread > 0"
                                      x-text="$store.notif.unread > 99 ? '99+' : $store.notif.unread"
                                      class="absolute -top-1 -right-1 flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[10px] font-extrabold bg-red-500 text-white rounded-full shadow-sm border-2 border-white dark:border-neutral-900">
                                </span>
                            </button>

                            <!-- Notifications Dropdown -->
                            <div x-show="notifOpen" @click.away="notifOpen = false" 
                                 class="absolute right-0 mt-2 w-80 bg-white dark:bg-neutral-900 rounded-2xl shadow-xl border border-gray-200 dark:border-neutral-800 py-2 z-50">
                                <div class="px-4 py-2 border-b border-gray-100 dark:border-neutral-800">
                                    <p class="font-bold text-[#1B3B36] dark:text-white">{{ __('messages.nav_notifications') }}</p>
                                </div>
                                <div class="max-h-64 overflow-y-auto">
                                    <a href="#" class="flex items-start gap-3 px-4 py-2.5 hover:bg-neutral-50 dark:hover:bg-neutral-800 transition">
                                        <div class="w-2 h-2 rounded-full bg-green-500 mt-2 shrink-0"></div>
                                        <div>
                                            <p class="text-sm text-[#1B3B36] dark:text-white font-medium">{{ __('messages.nav_notif_demo_1') }}</p>
                                            <p class="text-xs text-neutral-400">{{ __('messages.nav_time_2h') }}</p>
                                        </div>
                                    </a>
                                    <a href="#" class="flex items-start gap-3 px-4 py-2.5 hover:bg-neutral-50 dark:hover:bg-neutral-800 transition">
                                        <div class="w-2 h-2 rounded-full bg-amber-500 mt-2 shrink-0"></div>
                                        <div>
                                            <p class="text-sm text-[#1B3B36] dark:text-white font-medium">{{ __('messages.nav_notif_demo_2') }}</p>
                                            <p class="text-xs text-neutral-400">{{ __('messages.nav_time_5h') }}</p>
                                        </div>
                                    </a>
                                    <a href="#" class="flex items-start gap-3 px-4 py-2.5 hover:bg-neutral-50 dark:hover:bg-neutral-800 transition">
                                        <div class="w-2 h-2 rounded-full bg-blue-500 mt-2 shrink-0"></div>
                                        <div>
                                            <p class="text-sm text-[#1B3B36] dark:text-white font-medium">{{ __('messages.nav_notif_demo_3') }}</p>
                                            <p class="text-xs text-neutral-400">{{ __('messages.nav_time_1d') }}</p>
                                        </div>
                                    </a>
                                    <a href="#" class="flex items-start gap-3 px-4 py-2.5 hover:bg-neutral-50 dark:hover:bg-neutral-800 transition">
                                        <div class="w-2 h-2 rounded-full bg-green-500 mt-2 shrink-0"></div>
                                        <div>
                                            <p class="text-sm text-[#1B3B36] dark:text-white font-medium">{{ __('messages.nav_notif_demo_4') }}</p>
                                            <p class="text-xs text-neutral-400">{{ __('messages.nav_time_2d') }}</p>
                                        </div>
                                    </a>
                                    <a href="#" class="flex items-start gap-3 px-4 py-2.5 hover:bg-neutral-50 dark:hover:bg-neutral-800 transition">
                                        <div class="w-2 h-2 rounded-full bg-primary mt-2 shrink-0"></div>
                                        <div>
                                            <p class="text-sm text-[#1B3B36] dark:text-white font-medium">{{ __('messages.nav_notif_demo_5') }}</p>
                                            <p class="text-xs text-neutral-400">{{ __('messages.nav_time_2d') }}</p>
                                        </div>
                                    </a>
                                </div>
                                <div class="px-4 py-2 border-t border-gray-100 dark:border-neutral-800 text-center">
                                    <a href="{{ route('notifications.index') }}" class="text-xs text-primary font-semibold hover:underline">{{ __('messages.nav_view_all_notifications') }}</a>
                                </div>
                            </div>
                        </div>

                        <!-- Admin Profile Dropdown -->
                        <div x-data="{ profileOpen: false }" class="relative">
                            <button @click="profileOpen = !profileOpen" 
                                    class="flex items-center gap-2 px-3 py-1.5 rounded-xl hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                                <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold">
                                    {{ strtoupper(substr(auth()->user()->f_name ?? auth()->user()->email ?? 'U', 0, 1)) }}
                                </div>
                                
                                <span class="text-sm font-bold text-[#1B3B36] dark:text-white hidden sm:inline-block">{{ __('messages.nav_administrator') }}</span>
                                <svg class="w-4 h-4 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <!-- Profile Dropdown -->
                            <div x-show="profileOpen" @click.away="profileOpen = false" 
                                 class="absolute right-0 mt-2 w-56 bg-white dark:bg-neutral-900 rounded-2xl shadow-xl border border-gray-200 dark:border-neutral-800 py-2 z-50">
                                <div class="px-4 py-3 border-b border-gray-100 dark:border-neutral-800">
                                    <p class="font-bold text-[#1B3B36] dark:text-white">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ auth()->user()->email }}</p>
                                    <span class="inline-block mt-1 text-[10px] font-bold bg-primary/10 text-primary px-2 py-0.5 rounded-full">{{ __('messages.nav_admin_badge') }}</span>
                                </div>
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 hover:bg-neutral-50 dark:hover:bg-neutral-800 transition text-sm text-[#1B3B36] dark:text-white">
                                    <svg class="w-4 h-4 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    {{ __('messages.nav_my_profile') }}
                                </a>
                                <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-4 py-2.5 hover:bg-neutral-50 dark:hover:bg-neutral-800 transition text-sm text-[#1B3B36] dark:text-white">
                                    <svg class="w-4 h-4 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    {{ __('messages.nav_system_settings') }}
                                </a>
                                <hr class="border-gray-200 dark:border-neutral-800">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-3 w-full px-4 py-2.5 hover:bg-neutral-50 dark:hover:bg-neutral-800 transition text-sm text-red-600 dark:text-red-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        {{ __('messages.nav_logout') }}
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>

                    @else
                        <!-- ========================================== -->
                        <!-- OWNER/SITTER RIGHT SIDE (user + hamburger) -->
                        <!-- ========================================== -->
                        <div class="flex items-center gap-2 sm:gap-3">
                            
                            <!-- User Profile Dropdown -->
                            <div x-data="{ profileOpen: false }" class="relative flex items-center">
                                <button @click="profileOpen = !profileOpen" 
                                        class="inline-flex items-center gap-2 px-2 sm:px-3 py-1.5 border border-transparent text-sm font-extrabold rounded-xl text-neutral-600 dark:text-neutral-300 bg-neutral-50 dark:bg-neutral-800 hover:text-neutral-800 dark:hover:text-white hover:bg-neutral-100 dark:hover:bg-neutral-700 focus:outline-none transition ease-in-out duration-150">
                                    <div class="flex items-center gap-2">
                                        @php
                                            $user = auth()->user();
                                            $firstName = trim((string) ($user->f_name ?? ''));
                                            if ($firstName === '') {
                                                $firstName = 'User';
                                            }
                                            $displayName = $firstName;
                                        @endphp

                                        @if($user && $user->profile_photo && file_exists(public_path('storage/' . $user->profile_photo)))
                                            <img src="{{ asset('storage/' . $user->profile_photo) }}" 
                                                alt="{{ $user->f_name ?? $displayName }}" 
                                                class="w-8 h-8 rounded-full object-cover border-2 border-primary/20 shrink-0">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white border-2 border-primary/20 shrink-0">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                    <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM3.751 20.105a8.25 8.25 0 0116.498 0 .75.75 0 01-.437.695A18.683 18.683 0 0112 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 01-.437-.695z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        @endif

                                        <!-- Name — hidden sa mobile -->
                                        <span class="hidden sm:inline-block">
                                            {{ $displayName }}
                                        </span>
                                    </div>

                                    <!-- Chevron — hidden sa mobile -->
                                    <div class="hidden sm:block ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>

                                <div x-show="profileOpen" 
                                    @click.away="profileOpen = false" 
                                    x-transition:enter="transition ease-out duration-200" 
                                    x-transition:enter-start="transform opacity-0 scale-95" 
                                    x-transition:enter-end="transform opacity-100 scale-100" 
                                    x-transition:leave="transition ease-in duration-150" 
                                    x-transition:leave-start="transform opacity-100 scale-100" 
                                    x-transition:leave-end="transform opacity-0 scale-95" 
                                    class="absolute right-0 mt-2 w-56 bg-white dark:bg-neutral-900 rounded-2xl shadow-xl border border-gray-200 dark:border-neutral-800 py-2 z-50" style="display: none;">
                                    <a href="{{ route('public.profile', ['id' => auth()->id()]) }}" class="flex items-center gap-3 px-4 py-2.5 hover:bg-neutral-50 dark:hover:bg-neutral-800 transition text-sm text-[#1B3B36] dark:text-white font-bold">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        {{ __('messages.nav_my_profile') }}
                                    </a>

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center gap-3 w-full px-4 py-2.5 hover:bg-neutral-50 dark:hover:bg-neutral-800 transition text-sm text-red-500 font-bold">
                                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                            </svg>
                                            {{ __('messages.nav_log_out') }}
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Hamburger (mobile) — INSIDE sa same container -->
                            <div class="flex items-center sm:hidden">
                                <button @click="open = !open" 
                                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-neutral-800 focus:outline-none transition duration-150 ease-in-out">
                                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            
                        </div>
                    @endif
            @endauth

        </div>
    </div>

    <!-- ========================================== -->
    <!-- MOBILE SEARCH (Admin only)                 -->
    <!-- ========================================== -->
    @auth
        @if(auth()->user()->isAdmin())
            <div x-show="searchOpen" x-cloak class="px-4 pb-3 sm:hidden">
                <div class="relative">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" 
                           placeholder="{{ __('messages.nav_search_placeholder_mobile') }}" 
                           class="w-full pl-9 pr-4 py-2.5 text-sm rounded-xl border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-200 placeholder:text-neutral-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                </div>
            </div>
        @endif
    @endauth

    <!-- ========================================== -->
    <!-- MOBILE RESPONSIVE NAVIGATION MENU          -->
    <!-- (Owner/Sitter only)                       -->
    <!-- ========================================== -->
    @auth
        @if(!auth()->user()->isAdmin())
            <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
                <div class="pt-2 pb-3 space-y-1">

                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center gap-3 w-full ps-4 pe-4 py-2.5 border-l-4 border-transparent text-start text-base font-medium transition duration-150 ease-in-out
                       {{ request()->routeIs('dashboard') ? 'bg-primary/10 text-primary border-primary font-bold' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 dark:text-neutral-300 dark:hover:text-white dark:hover:bg-neutral-800' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        {{ __('messages.nav_home') }}
                    </a>

                    <a href="{{ route('find.sitter') }}" 
                       class="flex items-center gap-3 w-full ps-4 pe-4 py-2.5 border-l-4 border-transparent text-start text-base font-medium transition duration-150 ease-in-out
                       {{ request()->routeIs('find.sitter') ? 'bg-primary/10 text-primary border-primary font-bold' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 dark:text-neutral-300 dark:hover:text-white dark:hover:bg-neutral-800' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        {{ __('messages.nav_find_sitters') }}
                    </a>

                    <!-- My Bookings (mobile) — with BADGE -->
                    <a href="{{ route('mybookings.index') }}" 
                       class="flex items-center gap-3 w-full ps-4 pe-4 py-2.5 border-l-4 border-transparent text-start text-base font-medium transition duration-150 ease-in-out
                       {{ request()->routeIs('mybookings.index') ? 'bg-primary/10 text-primary border-primary font-bold' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 dark:text-neutral-300 dark:hover:text-white dark:hover:bg-neutral-800' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ __('messages.nav_my_bookings') }}
                        <span x-data
                              x-show="$store.booking.count > 0"
                              x-text="$store.booking.count > 99 ? '99+' : $store.booking.count"
                              class="ml-auto flex items-center justify-center min-w-[20px] h-5 px-1.5 text-[10px] font-extrabold bg-red-500 text-white rounded-full shadow-sm">
                        </span>
                    </a>

                    <a href="{{ route('mypets.index') }}" 
                       class="flex items-center gap-3 w-full ps-4 pe-4 py-2.5 border-l-4 border-transparent text-start text-base font-medium transition duration-150 ease-in-out
                       {{ request()->routeIs('mypets.index') ? 'bg-primary/10 text-primary border-primary font-bold' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 dark:text-neutral-300 dark:hover:text-white dark:hover:bg-neutral-800' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/></svg>
                        {{ __('messages.nav_my_pets') }}
                    </a>

                    <!-- Messages (mobile) — with BADGE -->
                    <a href="{{ route('messages.index') }}" 
                       class="flex items-center gap-3 w-full ps-4 pe-4 py-2.5 border-l-4 border-transparent text-start text-base font-medium transition duration-150 ease-in-out
                       {{ request()->routeIs('messages.index') ? 'bg-primary/10 text-primary border-primary font-bold' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 dark:text-neutral-300 dark:hover:text-white dark:hover:bg-neutral-800' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        {{ __('messages.nav_messages') }}
                        <span x-data
                              x-show="$store.msg.unread > 0"
                              x-text="$store.msg.unread > 99 ? '99+' : $store.msg.unread"
                              class="ml-auto flex items-center justify-center min-w-[20px] h-5 px-1.5 text-[10px] font-extrabold bg-red-500 text-white rounded-full shadow-sm">
                        </span>
                    </a>

                    <!-- Notifications (mobile) — with BADGE -->
                    <a href="{{ route('notifications.index') }}" 
                       class="flex items-center gap-3 w-full ps-4 pe-4 py-2.5 border-l-4 border-transparent text-start text-base font-medium transition duration-150 ease-in-out
                       {{ request()->routeIs('notifications.index') ? 'bg-primary/10 text-primary border-primary font-bold' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 dark:text-neutral-300 dark:hover:text-white dark:hover:bg-neutral-800' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        {{ __('messages.nav_notifications') }}
                        <span x-data
                              x-show="$store.notif.unread > 0"
                              x-text="$store.notif.unread > 99 ? '99+' : $store.notif.unread"
                              class="ml-auto flex items-center justify-center min-w-[20px] h-5 px-1.5 text-[10px] font-extrabold bg-red-500 text-white rounded-full shadow-sm">
                        </span>
                    </a>
                </div>

                <div class="pt-4 pb-1 border-t border-gray-200 dark:border-neutral-800">
                    <div class="px-4">
                        <div class="font-medium text-base text-gray-800 dark:text-white">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500 dark:text-neutral-400">{{ Auth::user()->email }}</div>
                    </div>

                    <div class="mt-3 space-y-1">
                        <x-responsive-nav-link :href="route('profile.edit')" 
                           class="{{ request()->routeIs('profile.edit') ? 'bg-primary/10 text-primary font-bold' : '' }}">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            {{ __('messages.nav_profile') }}
                        </x-responsive-nav-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                {{ __('messages.nav_log_out') }}
                            </x-responsive-nav-link>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endauth

</nav>