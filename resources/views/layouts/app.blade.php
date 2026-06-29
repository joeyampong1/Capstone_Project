<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            
            <!-- Alpine State -->
            <div
                x-data="{
                    sidebarOpen: false,
                    isDesktop: window.innerWidth >= 1024
                }"
                x-init="
                    const update = () => isDesktop = window.innerWidth >= 1024;
                    update();
                    window.addEventListener('resize', update);
                "
            >
                <!-- Sidebar -->
                @include('layouts.sidebar')

                <!-- Main Content Wrapper -->
                <div :class="isDesktop ? (sidebarOpen ? 'ml-64' : 'ml-16') : ''"
                     class="transition-all duration-300 min-h-screen">
                    
                    <!-- Navigation -->
                    @include('layouts.navigation')

                    <!-- Page Heading -->
                    @isset($header)
                        <header class="bg-white dark:bg-gray-800 shadow">
                            <div class="w-full mx-auto py-6 px-11 lg:px-24">
                                {{ $header }}
                            </div>
                        </header>
                    @endisset

                    <!-- Page Content -->
                    <main>
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

            <!-- ========================================== -->
            <!-- FLOATING TOGGLE BUTTON - MOBILE ONLY       -->
            <!-- PURE HTML + CSS + INLINE JS                -->
            <!-- ========================================== -->
            <button id="mobileToggleBtn" 
                    class="fixed bottom-6 left-4 z-50 lg:hidden 
                           bg-primary text-white p-3 rounded-full shadow-lg 
                           hover:bg-primary-600 transition-all duration-200
                           flex items-center justify-center"
                    style="width: 56px; height: 56px; box-shadow: 0 4px 15px rgba(0,0,0,0.3);">
                <svg id="toggleIconOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                </svg>
                <svg id="toggleIconClose" class="h-6 w-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
        </div>

        <!-- ========================================== -->
        <!-- INLINE JAVASCRIPT (no debug logs)          -->
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

        <!-- Alpine.js fallback (CDN) -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </body>
</html>