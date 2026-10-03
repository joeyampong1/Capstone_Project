<!DOCTYPE html>
@php
    $userSetting  = auth()->check() ? auth()->user()->setting : null;
    $userDarkMode = $userSetting?->dark_mode ?? false;
    $userFontSize = $userSetting?->font_size ?? 'default';
    $userLocale   = auth()->check() ? (auth()->user()->locale ?? 'en') : 'en';

    $fontSizePx = match($userFontSize) {
        'small'       => '14px',
        'large'       => '18px',
        'extra_large' => '20px',
        default       => '16px',
    };
@endphp

<html lang="{{ str_replace('_', '-', $userLocale) }}"
      class="{{ $userDarkMode ? 'dark' : '' }}"
      style="font-size: {{ $fontSizePx }};"
      data-font-size="{{ $userFontSize }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'PetNanny') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])


        <!-- ========================================== -->
        <!-- ALPINE STORE INIT (BEFORE Alpine loads)   -->
        <!-- ========================================== -->
        @auth
        <script>
            // Initialize Alpine stores EARLY — before Alpine.js loads
            document.addEventListener('alpine:init', () => {
                // App store — current viewing mode (owner/sitter/admin)
                Alpine.store('app', {
                    role: @json(
                        auth()->check()
                            ? (
                                auth()->user()->isAdmin()
                                    ? 'admin'
                                    : (
                                        (auth()->user()->is_sitter && auth()->user()->sitter_status === 'approved')
                                            ? 'sitter'
                                            : 'owner'
                                    )
                            )
                            : 'guest'
                    )
                });

                // Notifications store
                Alpine.store('notif', {
                    unread: {{ $unreadNotificationsCount ?? 0 }}
                });

                // Messages store
                Alpine.store('msg', {
                    unread: {{ $unreadMessagesCount ?? 0 }}
                });

                // Bookings store
                Alpine.store('booking', {
                    count: {{ $pendingBookingsCount ?? 0 }}
                });
            });
        </script>
        @endauth
    </head>

    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-neutral-900">

            <!-- Alpine State -->
            <div
                x-data="{
                    sidebarOpen: JSON.parse(localStorage.getItem('sidebarOpen') ?? 'false'),
                    isDesktop: window.innerWidth >= 1024
                }"
                x-init="
                    const update = () => isDesktop = window.innerWidth >= 1024;
                    update();
                    window.addEventListener('resize', update);
                    $watch('sidebarOpen', value => localStorage.setItem('sidebarOpen', JSON.stringify(value)));
                "
            >
                <!-- Sidebar (dynamic: admin / owner / sitter) -->
                @include('layouts.sidebar')

                <!-- Main Content Wrapper -->
                <div :class="isDesktop ? (sidebarOpen ? 'ml-64' : 'ml-16') : ''"
                     class="transition-all duration-300 min-h-screen">

                    <!-- Navigation (dynamic: admin / owner / sitter) -->
                    @include('layouts.navigation')

                    <!-- Page Heading -->
                    @isset($header)
                        <header class="bg-white dark:bg-neutral-800 border-b border-gray-200 dark:border-neutral-700 shadow-sm">
                            <div class="w-full mx-auto py-6 px-2 sm:px-16 lg:px-24">
                                {{ $header }}
                            </div>
                        </header>
                    @endisset

                    <!-- Page Content -->
                    <main>
                        {{-- ========================================== --}}
                        {{-- FLASH MESSAGES                             --}}
                        {{-- ========================================== --}}
                        <div class="w-full mx-auto px-2 sm:px-16 lg:px-24 pt-4">

                            {{-- Success --}}
                            @if(session('status'))
                                <div x-data="{ show: true }" x-show="show"
                                    x-init="setTimeout(() => show = false, 5000)"
                                    x-transition.opacity.duration.500ms
                                    class="mb-4 p-4 rounded-xl bg-green-50 dark:bg-green-950/20 border border-green-200 dark:border-green-800">
                                    <div class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-green-600 dark:text-green-400 shrink-0 mt-0.5"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <p class="text-sm text-green-700 dark:text-green-400 font-bold">
                                            {{ session('status') }}
                                        </p>
                                    </div>
                                </div>
                            @endif

                            {{-- Error --}}
                            @if(session('error'))
                                <div class="mb-4 p-4 rounded-xl bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800">
                                    <div class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <div class="min-w-0">
                                            <p class="text-sm text-amber-800 dark:text-amber-300 font-bold">
                                                {{ session('error') }}
                                            </p>
                                            @if(str_contains(strtolower(session('error')), 'pet'))
                                                <a href="{{ route('mypets.create') }}"
                                                class="inline-block mt-1 text-xs font-bold text-amber-700 dark:text-amber-400 underline hover:no-underline">
                                                    → Add a pet now
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>

                        {{ $slot }}
                    </main>

                </div>

                <!-- Backdrop (mobile only) -->
                <div x-show="sidebarOpen && !isDesktop"
                     x-transition:enter="transition-opacity ease-linear duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition-opacity ease-linear duration-300"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 z-30 bg-black/50"
                     @click="sidebarOpen = false">
                </div>
            </div>

        </div>

        @stack('scripts')

        {{-- ========================================== --}}
        {{-- DARK MODE TOGGLE — saves to DB             --}}
        {{-- ========================================== --}}
        @auth
        <script>
            window.toggleDarkMode = function() {
                const html = document.documentElement;
                const isDark = html.classList.toggle('dark');

                fetch('{{ route('settings.ui-preferences') }}', {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ dark_mode: isDark }),
                }).catch(() => {});
            };
        </script>
        @endauth

        <!-- ========================================== -->
        <!-- INLINE JAVASCRIPT                          -->
        <!-- ========================================== -->
        <script>
            (function() {
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initToggle);
                } else {
                    initToggle();
                }

                function initToggle() {
                    const toggleBtn = document.getElementById('mobileToggleBtn');
                    const iconOpen = document.getElementById('toggleIconOpen');
                    const iconClose = document.getElementById('toggleIconClose');

                    if (!toggleBtn) return;

                    function toggleSidebar() {
                        const sidebar = document.querySelector('aside');
                        if (!sidebar) return;

                        const isOpen = sidebar.classList.contains('translate-x-0');

                        if (isOpen) {
                            sidebar.classList.remove('translate-x-0');
                            sidebar.classList.add('-translate-x-full');
                            iconOpen.classList.remove('hidden');
                            iconClose.classList.add('hidden');
                        } else {
                            sidebar.classList.remove('-translate-x-full');
                            sidebar.classList.add('translate-x-0');
                            iconOpen.classList.add('hidden');
                            iconClose.classList.remove('hidden');
                        }
                    }

                    toggleBtn.addEventListener('click', toggleSidebar);

                    document.addEventListener('click', function(e) {
                        const backdrop = document.querySelector('.fixed.inset-0.z-30.bg-black\\/50');
                        if (backdrop && e.target === backdrop) {
                            const sidebar = document.querySelector('aside');
                            if (sidebar && sidebar.classList.contains('translate-x-0')) {
                                sidebar.classList.remove('translate-x-0');
                                sidebar.classList.add('-translate-x-full');
                                iconOpen.classList.remove('hidden');
                                iconClose.classList.add('hidden');
                            }
                        }
                    });
                }
            })();
        </script>

        <!-- ========================================== -->
        <!-- BADGE POLLING — Live update sa counts     -->
        <!-- ========================================== -->
        @auth
        <script>
            (function() {
                async function pollBadges() {
                    try {
                        const res = await fetch('/badges/poll', {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!res.ok) return;
                        const data = await res.json();

                        if (data.success && window.Alpine) {
                            // Update notifications badge
                            if (Alpine.store('notif')) {
                                Alpine.store('notif').unread = data.notifications ?? 0;
                            }
                            // Update messages badge
                            if (Alpine.store('msg')) {
                                Alpine.store('msg').unread = data.messages ?? 0;
                            }
                            // Update bookings badge
                            if (Alpine.store('booking')) {
                                Alpine.store('booking').count = data.bookings ?? 0;
                            }
                        }
                    } catch (e) {
                        // silent fail
                    }
                }

                function startPolling() {
                    pollBadges();
                    setInterval(pollBadges, 15000);
                }

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', startPolling);
                } else {
                    startPolling();
                }
            })();
        </script>
        @endauth

    </body>
</html>
