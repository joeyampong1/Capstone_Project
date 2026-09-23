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
            <svg x-show="!sidebarOpen" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
            </svg>
            <svg x-show="sidebarOpen" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
            </svg>
        </button>

        <button @click="sidebarOpen = false"
                class="p-1.5 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition lg:hidden">
            <svg class="w-5 h-5 text-neutral-600 dark:text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <nav class="p-4 space-y-1">
        @auth
        @if(auth()->user()->isAdmin())
            <!-- ========================================== -->
            <!-- ADMIN SIDEBAR LINKS                       -->
            <!-- ========================================== -->

            <div x-show="sidebarOpen || !isDesktop" class="px-3 py-2 mb-2">
                <p class="text-[10px] font-black uppercase tracking-wider text-neutral-400 dark:text-neutral-500">{{ __('messages.sb_petnanny_admin') }}</p>
            </div>

            <!-- MAIN -->
            <div x-show="sidebarOpen || !isDesktop" class="px-3 py-1">
                <p class="text-[10px] font-black uppercase tracking-wider text-neutral-400 dark:text-neutral-500">{{ __('messages.sb_main') }}</p>
            </div>

            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}"
               :class="(!sidebarOpen && isDesktop) ? 'justify-center px-0' : 'px-3'"
               class="flex items-center gap-3 py-3 rounded-xl transition
               {{ request()->routeIs('admin.dashboard')
                   ? 'bg-primary/10 text-primary font-bold'
                   : 'text-neutral-600 dark:text-neutral-300 hover:bg-primary/5 hover:text-primary' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                <span x-show="sidebarOpen || !isDesktop" class="text-base font-bold whitespace-nowrap">{{ __('messages.sb_dashboard') }}</span>
            </a>

            <hr class="border-gray-200 dark:border-neutral-800 my-2">

            <!-- USER MANAGEMENT -->
            <div x-show="sidebarOpen || !isDesktop" class="px-3 py-1">
                <p class="text-[10px] font-black uppercase tracking-wider text-neutral-400 dark:text-neutral-500">{{ __('messages.sb_user_management') }}</p>
            </div>

            <!-- User Management -->
            <a href="{{ route('admin.users') }}"
               :class="(!sidebarOpen && isDesktop) ? 'justify-center px-0' : 'px-3'"
               class="flex items-center gap-3 py-3 rounded-xl transition
               {{ request()->routeIs('admin.users')
                   ? 'bg-primary/10 text-primary font-bold'
                   : 'text-neutral-600 dark:text-neutral-300 hover:bg-primary/5 hover:text-primary' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span x-show="sidebarOpen || !isDesktop" class="text-base font-bold whitespace-nowrap">{{ __('messages.sb_user_management') }}</span>
            </a>

            <!-- ID Verification -->
            <a href="{{ route('admin.verification.id') }}"
               :class="(!sidebarOpen && isDesktop) ? 'justify-center px-0' : 'px-3'"
               class="flex items-center gap-3 py-3 rounded-xl transition
               {{ request()->routeIs('admin.verification.id')
                   ? 'bg-primary/10 text-primary font-bold'
                   : 'text-neutral-600 dark:text-neutral-300 hover:bg-primary/5 hover:text-primary' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z"/>
                </svg>
                <span x-show="sidebarOpen || !isDesktop" class="text-base font-bold whitespace-nowrap">{{ __('messages.sb_id_verification') }}</span>
            </a>

            <!-- Pet Sitter Verification -->
            <a href="{{ route('admin.verification.sitter') }}"
               :class="(!sidebarOpen && isDesktop) ? 'justify-center px-0' : 'px-3'"
               class="flex items-center gap-3 py-3 rounded-xl transition
               {{ request()->routeIs('admin.verification.sitter')
                   ? 'bg-primary/10 text-primary font-bold'
                   : 'text-neutral-600 dark:text-neutral-300 hover:bg-primary/5 hover:text-primary' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span x-show="sidebarOpen || !isDesktop" class="text-base font-bold whitespace-nowrap">{{ __('messages.sb_sitter_verification') }}</span>
            </a>

            <hr class="border-gray-200 dark:border-neutral-800 my-2">

            <!-- BOOKING MANAGEMENT -->
            <div x-show="sidebarOpen || !isDesktop" class="px-3 py-1">
                <p class="text-[10px] font-black uppercase tracking-wider text-neutral-400 dark:text-neutral-500">{{ __('messages.sb_booking_management') }}</p>
            </div>

            <!-- Bookings -->
            <a href="{{ route('admin.bookings') }}"
               :class="(!sidebarOpen && isDesktop) ? 'justify-center px-0' : 'px-3'"
               class="flex items-center gap-3 py-3 rounded-xl transition
               {{ request()->routeIs('admin.bookings')
                   ? 'bg-primary/10 text-primary font-bold'
                   : 'text-neutral-600 dark:text-neutral-300 hover:bg-primary/5 hover:text-primary' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span x-show="sidebarOpen || !isDesktop" class="text-base font-bold whitespace-nowrap">{{ __('messages.sb_bookings') }}</span>
            </a>

            <hr class="border-gray-200 dark:border-neutral-800 my-2">

            <!-- CASE MANAGEMENT -->
            <div x-show="sidebarOpen || !isDesktop" class="px-3 py-1">
                <p class="text-[10px] font-black uppercase tracking-wider text-neutral-400 dark:text-neutral-500">{{ __('messages.sb_case_management') }}</p>
            </div>

            <!-- Complaints -->
            <a href="{{ route('admin.complaints') }}"
               :class="(!sidebarOpen && isDesktop) ? 'justify-center px-0' : 'px-3'"
               class="flex items-center gap-3 py-3 rounded-xl transition
               {{ request()->routeIs('admin.complaints')
                   ? 'bg-primary/10 text-primary font-bold'
                   : 'text-neutral-600 dark:text-neutral-300 hover:bg-primary/5 hover:text-primary' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span x-show="sidebarOpen || !isDesktop" class="text-base font-bold whitespace-nowrap">{{ __('messages.sb_complaints') }}</span>
            </a>

            <!-- Support Messages -->
            <a href="{{ route('admin.messages.index') }}"
               :class="(!sidebarOpen && isDesktop) ? 'justify-center px-0' : 'px-3'"
               class="flex items-center gap-3 py-3 rounded-xl transition
               {{ request()->routeIs('admin.messages.*')
                   ? 'bg-primary/10 text-primary font-bold'
                   : 'text-neutral-600 dark:text-neutral-300 hover:bg-primary/5 hover:text-primary' }}">
                <div class="relative shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    @php $msgCount = \App\Models\UserSupportMessage::openCount(); @endphp
                    @if ($msgCount > 0)
                        <span class="absolute -top-1.5 -right-1.5 inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full bg-red-500 text-white text-[10px] font-bold">
                            {{ $msgCount > 9 ? '9+' : $msgCount }}
                        </span>
                    @endif
                </div>
                <span x-show="sidebarOpen || !isDesktop" class="text-base font-bold whitespace-nowrap">{{ __('messages.sb_support_messages') }}</span>
            </a>

            <hr class="border-gray-200 dark:border-neutral-800 my-2">

            <!-- REPORTS -->
            <div x-show="sidebarOpen || !isDesktop" class="px-3 py-1">
                <p class="text-[10px] font-black uppercase tracking-wider text-neutral-400 dark:text-neutral-500">{{ __('messages.sb_reports') }}</p>
            </div>

            <!-- Reports -->
            <a href="{{ route('admin.reports') }}"
               :class="(!sidebarOpen && isDesktop) ? 'justify-center px-0' : 'px-3'"
               class="flex items-center gap-3 py-3 rounded-xl transition
               {{ request()->routeIs('admin.reports')
                   ? 'bg-primary/10 text-primary font-bold'
                   : 'text-neutral-600 dark:text-neutral-300 hover:bg-primary/5 hover:text-primary' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m4 2v-4m4 2v-6m0 6h6m-6 0v6m0-6h6"/>
                </svg>
                <span x-show="sidebarOpen || !isDesktop" class="text-base font-bold whitespace-nowrap">{{ __('messages.sb_reports') }}</span>
            </a>

            <!-- Analytics -->
            <a href="{{ route('admin.analytics') }}"
               :class="(!sidebarOpen && isDesktop) ? 'justify-center px-0' : 'px-3'"
               class="flex items-center gap-3 py-3 rounded-xl transition
               {{ request()->routeIs('admin.analytics')
                   ? 'bg-primary/10 text-primary font-bold'
                   : 'text-neutral-600 dark:text-neutral-300 hover:bg-primary/5 hover:text-primary' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span x-show="sidebarOpen || !isDesktop" class="text-base font-bold whitespace-nowrap">{{ __('messages.sb_analytics') }}</span>
            </a>

            <hr class="border-gray-200 dark:border-neutral-800 my-2">

            <!-- SYSTEM -->
            <div x-show="sidebarOpen || !isDesktop" class="px-3 py-1">
                <p class="text-[10px] font-black uppercase tracking-wider text-neutral-400 dark:text-neutral-500">{{ __('messages.sb_system') }}</p>
            </div>

            <!-- Help & Support -->
            <a href="{{ route('admin.help_support') }}"
               :class="(!sidebarOpen && isDesktop) ? 'justify-center px-0' : 'px-3'"
               class="flex items-center gap-3 py-3 rounded-xl transition
               {{ request()->routeIs('admin.help_support')
                   ? 'bg-primary/10 text-primary font-bold'
                   : 'text-neutral-600 dark:text-neutral-300 hover:bg-primary/5 hover:text-primary' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-5.657-1.414a1 1 0 011.414 1.414"/>
                </svg>
                <span x-show="sidebarOpen || !isDesktop" class="text-base font-bold whitespace-nowrap">{{ __('messages.sb_help_support') }}</span>
            </a>

            <!-- System Settings -->
            <a href="{{ route('settings.index') }}"
               :class="(!sidebarOpen && isDesktop) ? 'justify-center px-0' : 'px-3'"
               class="flex items-center gap-3 py-3 rounded-xl transition
               {{ request()->routeIs('settings.index')
                   ? 'bg-primary/10 text-primary font-bold'
                   : 'text-neutral-600 dark:text-neutral-300 hover:bg-primary/5 hover:text-primary' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span x-show="sidebarOpen || !isDesktop" class="text-base font-bold whitespace-nowrap">{{ __('messages.sb_system_settings') }}</span>
            </a>

            <!-- My Profile -->
            <a href="{{ route('profile.edit') }}"
               :class="(!sidebarOpen && isDesktop) ? 'justify-center px-0' : 'px-3'"
               class="flex items-center gap-3 py-3 rounded-xl transition
               {{ request()->routeIs('profile.*')
                   ? 'bg-primary/10 text-primary font-bold'
                   : 'text-neutral-600 dark:text-neutral-300 hover:bg-primary/5 hover:text-primary' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span x-show="sidebarOpen || !isDesktop" class="text-base font-bold whitespace-nowrap">{{ __('messages.sb_my_profile') }}</span>
            </a>

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button type="submit"
                        :class="(!sidebarOpen && isDesktop) ? 'justify-center px-0' : 'px-3'"
                        class="w-full flex items-center gap-3 py-3 rounded-xl transition text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/20">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span x-show="sidebarOpen || !isDesktop" class="text-base font-bold whitespace-nowrap">{{ __('messages.sb_logout') }}</span>
                </button>
            </form>

        @else
            <!-- ========================================== -->
            <!-- OWNER / SITTER SIDEBAR                     -->
            <!-- ========================================== -->

            <!-- SITTER-ONLY LINKS -->
            <template x-if="$store.app.role === 'sitter'">
                <div>
                    <!-- Sitter Dashboard -->
                    <a href="{{ route('sitter.dashboard') }}"
                       :class="(!sidebarOpen && isDesktop) ? 'justify-center px-0' : 'px-3'"
                       class="flex items-center gap-3 py-3 rounded-xl transition
                       {{ request()->routeIs('sitter.dashboard')
                           ? 'bg-primary/10 text-primary font-bold'
                           : 'text-neutral-600 dark:text-neutral-300 hover:bg-primary/5 hover:text-primary' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span x-show="sidebarOpen || !isDesktop" class="text-base font-bold whitespace-nowrap">{{ __('messages.sb_sitter_dashboard') }}</span>
                    </a>

                    <!-- My Tasks -->
                    <a href="{{ route('sitter.tasks.index') }}"
                       :class="(!sidebarOpen && isDesktop) ? 'justify-center px-0' : 'px-3'"
                       class="flex items-center gap-3 py-3 rounded-xl transition
                       {{ request()->routeIs('sitter.tasks.index') || request()->routeIs('sitter.tasks.show')
                           ? 'bg-primary/10 text-primary font-bold'
                           : 'text-neutral-600 dark:text-neutral-300 hover:bg-primary/5 hover:text-primary' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        <span x-show="sidebarOpen || !isDesktop" class="text-base font-bold whitespace-nowrap">{{ __('messages.sb_my_tasks') }}</span>
                    </a>

                    <!-- Availability -->
                    <a href="{{ route('sitter.availability') }}"
                       :class="(!sidebarOpen && isDesktop) ? 'justify-center px-0' : 'px-3'"
                       class="flex items-center gap-3 py-3 rounded-xl transition
                       {{ request()->routeIs('sitter.availability') || request()->routeIs('sitter.availability.*')
                           ? 'bg-primary/10 text-primary font-bold'
                           : 'text-neutral-600 dark:text-neutral-300 hover:bg-primary/5 hover:text-primary' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span x-show="sidebarOpen || !isDesktop" class="text-base font-bold whitespace-nowrap">{{ __('messages.sb_availability') }}</span>
                    </a>

                    <!-- Complaints -->
                    <a href="{{ route('complaints.index') }}"
                       :class="(!sidebarOpen && isDesktop) ? 'justify-center px-0' : 'px-3'"
                       class="flex items-center gap-3 py-3 rounded-xl transition
                       {{ request()->routeIs('complaints.*')
                           ? 'bg-primary/10 text-primary font-bold'
                           : 'text-neutral-600 dark:text-neutral-300 hover:bg-primary/5 hover:text-primary' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span x-show="sidebarOpen || !isDesktop" class="text-base font-bold whitespace-nowrap">{{ __('messages.sb_complaints') }}</span>
                    </a>

                    <hr class="border-gray-200 dark:border-neutral-800 my-2">

                    <!-- Help & Support -->
                    <a href="{{ route('help_support.index') }}"
                       :class="(!sidebarOpen && isDesktop) ? 'justify-center px-0' : 'px-3'"
                       class="flex items-center gap-3 py-3 rounded-xl transition
                       {{ request()->routeIs('help_support.*')
                           ? 'bg-primary/10 text-primary font-bold'
                           : 'text-neutral-600 dark:text-neutral-300 hover:bg-primary/5 hover:text-primary' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-5.657-1.414a1 1 0 011.414 1.414"/>
                        </svg>
                        <span x-show="sidebarOpen || !isDesktop" class="text-base font-bold whitespace-nowrap">{{ __('messages.sb_help_support') }}</span>
                    </a>

                    <!-- Settings -->
                    <a href="{{ route('settings.index') }}"
                       :class="(!sidebarOpen && isDesktop) ? 'justify-center px-0' : 'px-3'"
                       class="flex items-center gap-3 py-3 rounded-xl transition
                       {{ request()->routeIs('settings.index')
                           ? 'bg-primary/10 text-primary font-bold'
                           : 'text-neutral-600 dark:text-neutral-300 hover:bg-primary/5 hover:text-primary' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span x-show="sidebarOpen || !isDesktop" class="text-base font-bold whitespace-nowrap">{{ __('messages.sb_settings') }}</span>
                    </a>
                </div>
            </template>

            <!-- OWNER-ONLY LINKS -->
            <template x-if="$store.app.role === 'owner'">
                <div>
                    <!-- Task Monitor -->
                    <a href="{{ route('tasks.monitor') }}"
                       :class="(!sidebarOpen && isDesktop) ? 'justify-center px-0' : 'px-3'"
                       class="flex items-center gap-3 py-3 rounded-xl transition
                       {{ request()->routeIs('tasks.monitor') || request()->routeIs('task.monitor')
                           ? 'bg-primary/10 text-primary font-bold'
                           : 'text-neutral-600 dark:text-neutral-300 hover:bg-primary/5 hover:text-primary' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        <span x-show="sidebarOpen || !isDesktop" class="text-base font-bold whitespace-nowrap">{{ __('messages.sb_task_monitor') }}</span>
                    </a>

                    <!-- Complaints -->
                    <a href="{{ route('complaints.index') }}"
                       :class="(!sidebarOpen && isDesktop) ? 'justify-center px-0' : 'px-3'"
                       class="flex items-center gap-3 py-3 rounded-xl transition
                       {{ request()->routeIs('complaints.*')
                           ? 'bg-primary/10 text-primary font-bold'
                           : 'text-neutral-600 dark:text-neutral-300 hover:bg-primary/5 hover:text-primary' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span x-show="sidebarOpen || !isDesktop" class="text-base font-bold whitespace-nowrap">{{ __('messages.sb_complaints') }}</span>
                    </a>

                    <hr class="border-gray-200 dark:border-neutral-800 my-2">

                    <!-- Help & Support -->
                    <a href="{{ route('help_support.index') }}"
                       :class="(!sidebarOpen && isDesktop) ? 'justify-center px-0' : 'px-3'"
                       class="flex items-center gap-3 py-3 rounded-xl transition
                       {{ request()->routeIs('help_support.*')
                           ? 'bg-primary/10 text-primary font-bold'
                           : 'text-neutral-600 dark:text-neutral-300 hover:bg-primary/5 hover:text-primary' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-5.657-1.414a1 1 0 011.414 1.414"/>
                        </svg>
                        <span x-show="sidebarOpen || !isDesktop" class="text-base font-bold whitespace-nowrap">{{ __('messages.sb_help_support') }}</span>
                    </a>

                    <!-- Settings -->
                    <a href="{{ route('settings.index') }}"
                       :class="(!sidebarOpen && isDesktop) ? 'justify-center px-0' : 'px-3'"
                       class="flex items-center gap-3 py-3 rounded-xl transition
                       {{ request()->routeIs('settings.index')
                           ? 'bg-primary/10 text-primary font-bold'
                           : 'text-neutral-600 dark:text-neutral-300 hover:bg-primary/5 hover:text-primary' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span x-show="sidebarOpen || !isDesktop" class="text-base font-bold whitespace-nowrap">{{ __('messages.sb_settings') }}</span>
                    </a>
                </div>
            </template>

        @endif
        @endauth
    </nav>
</aside>