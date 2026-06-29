<nav x-data="{ open: false }" class="bg-white dark:bg-neutral-900 border-b border-neutral-100 dark:border-neutral-800 sticky top-0 z-50 shadow-sm">
    <div class="w-full mx-auto px-11 lg:px-24">
        <div class="flex justify-between h-16 relative">

            <!-- LEFT SIDE: toggle button + logo -->
            <div class="flex items-center gap-2 sm:gap-4">

                <!-- ========================================== -->
                <!-- LOGO                                       -->
                <!-- ========================================== -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center">
                        <img src="{{ asset('assets/logo/PetNanny_Logo.png') }}" alt="PetNanny Logo" class="h-9 w-auto block dark:hidden">
                        <img src="{{ asset('assets/logo/PetNanny_Logo.png') }}" alt="PetNanny Logo" class="h-9 w-auto hidden dark:block invert">
                        <span class="ml-2 text-xl font-black text-[#1B3B36] dark:text-white hidden sm:block">
                            Pet<span class="text-primary">Nanny</span>
                        </span>
                    </a>
                </div>

                <!-- ========================================== -->
                <!-- DESKTOP NAVIGATION LINKS                   -->
                <!-- Hidden on mobile, visible on sm+           -->
                <!-- ========================================== -->
                <div class="hidden space-x-1 sm:-my-px sm:flex items-center">

                    <!-- Home -->
                    <a href="{{ route('dashboard') }}" 
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-extrabold rounded-xl transition-all duration-150 
                    {{ request()->routeIs('dashboard') 
                        ? 'bg-primary text-white shadow-sm hover:bg-primary-600' 
                        : 'text-neutral-600 hover:text-primary hover:bg-primary-50 dark:text-neutral-300 dark:hover:text-white dark:hover:bg-neutral-800' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Home
                    </a>

                    <!-- Find Sitters -->
                    <a href="{{ route('find.sitter') }}" 
                    class="inline-flex items-center gap-2.5 px-4 py-2 text-sm font-bold rounded-xl transition-all duration-150 
                    {{ request()->routeIs('find.sitter') 
                        ? 'bg-primary text-white shadow-sm hover:bg-primary-600' 
                        : 'text-neutral-600 hover:text-primary hover:bg-primary-50 dark:text-neutral-300 dark:hover:text-white dark:hover:bg-neutral-800' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Find Sitters
                    </a>

                    <!-- My Bookings -->
                    <a href="{{ route('mybookings.index') }}" 
                    class="inline-flex items-center gap-2.5 px-4 py-2 text-sm font-bold rounded-xl transition-all duration-150 
                    {{ request()->routeIs('mybookings.index') 
                        ? 'bg-primary text-white shadow-sm hover:bg-primary-600' 
                        : 'text-neutral-600 hover:text-primary hover:bg-primary-50 dark:text-neutral-300 dark:hover:text-white dark:hover:bg-neutral-800' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        My Bookings
                    </a>

                    <!-- My Pets -->
                    <a href="{{ route('mypets.index') }}" 
                    class="inline-flex items-center gap-2.5 px-4 py-2 text-sm font-bold rounded-xl transition-all duration-150 
                    {{ request()->routeIs('mypets.index') 
                        ? 'bg-primary text-white shadow-sm hover:bg-primary-600' 
                        : 'text-neutral-600 hover:text-primary hover:bg-primary-50 dark:text-neutral-300 dark:hover:text-white dark:hover:bg-neutral-800' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                        </svg>
                        My Pets
                    </a>

                    <!-- Messages -->
                    <a href="{{ route('messages.index') }}" 
                    class="inline-flex items-center gap-2.5 px-4 py-2 text-sm font-bold rounded-xl transition-all duration-150 
                    {{ request()->routeIs('messages.index') 
                        ? 'bg-primary text-white shadow-sm hover:bg-primary-600' 
                        : 'text-neutral-600 hover:text-primary hover:bg-primary-50 dark:text-neutral-300 dark:hover:text-white dark:hover:bg-neutral-800' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        Messages
                    </a>

                    <!-- Notifications -->
                    <a href="{{ route('notifications.index') }}" 
                    class="inline-flex items-center gap-2.5 px-4 py-2 text-sm font-bold rounded-xl transition-all duration-150 
                    {{ request()->routeIs('notifications.index') 
                        ? 'bg-primary text-white shadow-sm hover:bg-primary-600' 
                        : 'text-neutral-600 hover:text-primary hover:bg-primary-50 dark:text-neutral-300 dark:hover:text-white dark:hover:bg-neutral-800' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        Notifications
                    </a>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- RIGHT SIDE: USER DROPDOWN & HAMBURGER      -->
            <!-- ========================================== -->

            <!-- ========================================== -->
            <!-- USER PROFILE DROPDOWN WITH AVATAR          -->
            <!-- ========================================== -->
            <div class="hidden sm:flex sm:items-center sm:ms-4">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-3 py-1.5 border border-transparent text-sm font-extrabold rounded-xl text-neutral-600 dark:text-neutral-300 bg-neutral-50 dark:bg-neutral-800 hover:text-neutral-800 dark:hover:text-white hover:bg-neutral-100 dark:hover:bg-neutral-700 focus:outline-none transition ease-in-out duration-150">
                            <div class="flex items-center gap-2">
                                @php
                                    $user = Auth::user();
                                    $avatarUrl = $user->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=f07a3a&color=fff&size=40';
                                @endphp
                                
                                @if($user->profile_photo && file_exists(public_path('storage/' . $user->profile_photo)))
                                    <img src="{{ asset('storage/' . $user->profile_photo) }}" 
                                         alt="{{ $user->name }}" 
                                         class="w-8 h-8 rounded-full object-cover border-2 border-primary/20">
                                @else
                                    <img src="{{ $avatarUrl }}" 
                                         alt="{{ $user->name }}" 
                                         class="w-8 h-8 rounded-full object-cover border-2 border-primary/20">
                                @endif
                                
                                <span class="hidden md:inline-block">{{ $user->name }}</span>
                            </div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('public.profile', ['id' => auth()->id()])" class="text-sm font-bold">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            My Profile
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-sm font-bold text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Log Out
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- ========================================== -->
            <!-- HAMBURGER (mobile menu toggle)             -->
            <!-- ========================================== -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-neutral-800 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MOBILE RESPONSIVE NAVIGATION MENU          -->
    <!-- ========================================== -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">

            <!-- Home -->
            <a href="{{ route('dashboard') }}" 
               class="flex items-center gap-3 w-full ps-4 pe-4 py-2.5 border-l-4 border-transparent text-start text-base font-medium transition duration-150 ease-in-out
               {{ request()->routeIs('dashboard') 
                  ? 'bg-primary/10 text-primary border-primary font-bold' 
                  : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 dark:text-neutral-300 dark:hover:text-white dark:hover:bg-neutral-800' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Home
            </a>

            <!-- Find Sitters -->
            <a href="{{ route('find.sitter') }}" 
               class="flex items-center gap-3 w-full ps-4 pe-4 py-2.5 border-l-4 border-transparent text-start text-base font-medium transition duration-150 ease-in-out
               {{ request()->routeIs('find.sitter') 
                  ? 'bg-primary/10 text-primary border-primary font-bold' 
                  : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 dark:text-neutral-300 dark:hover:text-white dark:hover:bg-neutral-800' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Find Sitters
            </a>

            <!-- My Bookings -->
            <a href="{{ route('mybookings.index') }}" 
            class="flex items-center gap-3 w-full ps-4 pe-4 py-2.5 border-l-4 border-transparent text-start text-base font-medium transition duration-150 ease-in-out
            {{ request()->routeIs('mybookings.index') 
                ? 'bg-primary/10 text-primary border-primary font-bold' 
                : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 dark:text-neutral-300 dark:hover:text-white dark:hover:bg-neutral-800' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                My Bookings
            </a>

            <!-- My Pets -->
            <a href="{{ route('mypets.index') }}"  
               class="flex items-center gap-3 w-full ps-4 pe-4 py-2.5 border-l-4 border-transparent text-start text-base font-medium transition duration-150 ease-in-out
               {{ request()->routeIs('mypets.index') 
                  ? 'bg-primary/10 text-primary border-primary font-bold' 
                  : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 dark:text-neutral-300 dark:hover:text-white dark:hover:bg-neutral-800' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/></svg>
                My Pets
            </a>

            <!-- Messages -->
            <a href="{{ route('messages.index') }}" 
            class="flex items-center gap-3 w-full ps-4 pe-4 py-2.5 border-l-4 border-transparent text-start text-base font-medium transition duration-150 ease-in-out 
            {{ request()->routeIs('messages.index') 
                ? 'bg-primary/10 text-primary border-primary font-bold' 
                : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 dark:text-neutral-300 dark:hover:text-white dark:hover:bg-neutral-800' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                Messages
            </a>

            <!-- Notifications -->
            <a href="{{ route('notifications.index') }}" 
            class="flex items-center gap-3 w-full ps-4 pe-4 py-2.5 border-l-4 border-transparent text-start text-base font-medium transition duration-150 ease-in-out
            {{ request()->routeIs('notifications.index') 
                ? 'bg-primary/10 text-primary border-primary font-bold' 
                : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 dark:text-neutral-300 dark:hover:text-white dark:hover:bg-neutral-800' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                Notifications
            </a>
        </div>

        <!-- ========================================== -->
        <!-- USER INFO & SETTINGS (mobile)              -->
        <!-- ========================================== -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-neutral-800">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500 dark:text-neutral-400">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" 
                   class="{{ request()->routeIs('profile.edit') ? 'bg-primary/10 text-primary font-bold' : '' }}">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Profile
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Log Out
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>