<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'PetNanny') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,900" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.tailwindcss.com"></script>
            <script>
                tailwind.config = {
                    theme: {
                        extend: {
                            fontFamily: { sans: ['Figtree', 'sans-serif'] },
                            colors: {
                                primary: { 50: '#fef3ed', 100: '#fce3d4', 200: '#f8c5a8', 300: '#f5a67d', 400: '#f18851', 500: '#f07a3a', 600: '#d8652a', 700: '#b85222', 800: '#943f1a', 900: '#703012', DEFAULT: '#f07a3a' },
                                accent: { 50: '#edf7f3', 100: '#d4eee6', 200: '#a9ddcc', 300: '#7ecbb3', 400: '#53ba99', 500: '#39ac8c', 600: '#2d8a70', 700: '#216854', 800: '#154638', 900: '#0a231c', DEFAULT: '#39ac8c' },
                                secondary: { DEFAULT: '#f3ede6' }
                            }
                        }
                    }
                }
            </script>
        @endif
    </head>

    <body class="bg-secondary dark:bg-neutral-950 text-gray-800 dark:text-gray-100 flex min-h-screen flex-col font-sans selection:bg-primary-200">

        <header class="w-full bg-white/80 dark:bg-neutral-900/80 backdrop-blur-lg border-b border-gray-200/80 dark:border-neutral-800 sticky top-0 z-50 shadow-sm transition-all">
            <div class="w-full mx-auto px-12 sm:px-16 lg:px-24">
                <div class="flex justify-between h-16 items-center">

                    <div class="flex items-center space-x-3 cursor-pointer">
                        <img src="{{ asset('assets/logo/PetNanny_Logo.png') }}" alt="PetNanny Logo" class="h-9 w-auto object-contain">
                        <span class="text-xl font-black tracking-tight text-neutral-900 dark:text-white hidden sm:block">
                            Pet<span class="text-primary">Nanny</span>
                        </span>
                    </div>

                    @if (Route::has('login'))
                        <nav class="flex items-center gap-2 sm:gap-3">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center px-4 py-2 text-xs sm:text-sm font-bold bg-accent text-white rounded-xl shadow-sm hover:bg-accent-600 transition">
                                    Go to Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-3 py-2 text-xs sm:text-sm font-semibold text-gray-700 dark:text-gray-300 hover:text-primary dark:hover:text-primary-400 transition">
                                    Log in
                                </a>

                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-4 py-2 text-xs sm:text-sm font-bold bg-primary text-white rounded-xl shadow-sm hover:bg-primary-600 transition">
                                        Register Free
                                    </a>
                                @endif
                            @endauth
                        </nav>
                    @endif
                </div>
            </div>
        </header>
        
        <!-- ============================================ -->
        <!-- HERO SECTION                                 -->
        <!-- Main landing area with CTA and sitter cards   -->
        <!-- ============================================ -->
        <section class="relative overflow-hidden bg-gradient-to-br from-primary-50 via-white to-accent-50/30 dark:from-neutral-950 dark:via-neutral-900 dark:to-neutral-800 py-16 sm:py-24">
            
            <!-- DECORATIVE BLOBS: Background blur effects -->
            <div class="absolute top-[-300px] right-[-200px] w-[700px] h-[700px] bg-primary-200/20 dark:bg-primary-500/5 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute bottom-[-200px] left-[-150px] w-[600px] h-[600px] bg-accent-200/20 dark:bg-accent-500/5 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row items-center justify-between gap-12 lg:gap-16">

                    <!-- HERO LEFT CONTENT -->
                    <div class="flex-1 text-center lg:text-left">
                        <!-- BADGE: Trust badge -->
                        <div class="inline-flex items-center gap-2 bg-primary-100/80 dark:bg-primary-900/40 text-primary-700 dark:text-primary-300 text-xs px-3 py-1.5 rounded-full font-bold uppercase tracking-wider mb-6">
                            🐾 Trusted Pet Care Platform
                        </div>

                        <!-- HEADLINE -->
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-gray-900 dark:text-white leading-tight tracking-tight">
                            Trusted care for your pets,
                            <br>
                            <span class="text-primary underline decoration-accent-400/50 decoration-4 underline-offset-8">one visit</span> at a time.
                        </h1>

                        <!-- SUBTEXT -->
                        <p class="mt-4 text-lg text-gray-600 dark:text-gray-400 leading-relaxed max-w-2xl mx-auto lg:mx-0">
                            A dual-role platform built around decentralized scheduling,
                            multi-tiered credential validation, automatic late-deductions,
                            and unified community protection funds.
                        </p>

                        <!-- CTA BUTTONS -->
                        <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="bg-primary hover:bg-primary-600 text-white font-bold text-sm px-8 py-4 rounded-xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5 text-center flex-1 sm:flex-none">
                                    🚀 Post a Sitting Request
                                </a>
                            @endif
                            <a href="#features" class="bg-white dark:bg-neutral-800 border-2 border-gray-200 dark:border-neutral-700 text-gray-700 dark:text-gray-200 font-bold text-sm px-8 py-4 rounded-xl hover:bg-gray-50 dark:hover:bg-neutral-700 transition transform hover:-translate-y-0.5 text-center flex-1 sm:flex-none">
                                Explore Sitters →
                            </a>
                        </div>

                        <!-- STATS CARDS -->
                        <div class="mt-10 grid grid-cols-3 gap-4 max-w-lg mx-auto lg:mx-0">
                            <div class="bg-white/70 dark:bg-neutral-800/50 backdrop-blur-sm rounded-2xl p-4 border border-gray-200/50 dark:border-neutral-700/50">
                                <p class="text-2xl font-black text-primary">₱150-300</p>
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mt-0.5">Per Visit</p>
                            </div>
                            <div class="bg-white/70 dark:bg-neutral-800/50 backdrop-blur-sm rounded-2xl p-4 border border-gray-200/50 dark:border-neutral-700/50">
                                <p class="text-2xl font-black text-accent">10%</p>
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mt-0.5">Platform Fee</p>
                            </div>
                            <div class="bg-white/70 dark:bg-neutral-800/50 backdrop-blur-sm rounded-2xl p-4 border border-gray-200/50 dark:border-neutral-700/50">
                                <p class="text-2xl font-black text-accent">2%</p>
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mt-0.5">Injury Fund</p>
                            </div>
                        </div>
                    </div>

                    <!-- HERO RIGHT: Sitter Cards -->
                    <div class="w-full max-w-[400px] lg:max-w-[440px] flex-shrink-0">
                        <div class="bg-white dark:bg-neutral-900 rounded-3xl border border-gray-200/80 dark:border-neutral-800 shadow-2xl p-6 space-y-4">
                            
                            <!-- CARD HEADER -->
                            <div class="flex justify-between items-center border-b border-gray-100 dark:border-neutral-800 pb-3">
                                <h3 class="font-extrabold text-sm text-gray-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                    Active Local Sitters
                                </h3>
                                <span class="text-xs text-primary font-bold hover:underline cursor-pointer">Filter →</span>
                            </div>

                            <!-- SITTER CARD 1: Kristine M. (Level 3) -->
                            <div class="flex items-start p-4 bg-primary-50/50 dark:bg-neutral-800/40 border border-primary-100 dark:border-neutral-800 rounded-2xl space-x-3.5">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-400 to-primary-500 flex-shrink-0 flex items-center justify-center font-bold text-white shadow-sm">
                                    KM
                                </div>
                                <div class="flex-grow min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <h4 class="font-bold text-sm text-gray-900 dark:text-white truncate">Kristine M.</h4>
                                        <span class="bg-accent text-white text-[9px] font-black px-2 py-0.5 rounded-md uppercase tracking-wide shrink-0">Level 3</span>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">⭐ 5.0 (54 Visits)</p>
                                    <div class="flex gap-1.5 mt-2 flex-wrap">
                                        <span class="bg-white dark:bg-neutral-800 px-2 py-0.5 rounded-md text-[10px] font-medium text-gray-600 dark:text-gray-300 border border-gray-100 dark:border-neutral-700">Dogs Only</span>
                                        <span class="bg-white dark:bg-neutral-800 px-2 py-0.5 rounded-md text-[10px] font-medium text-gray-600 dark:text-gray-300 border border-gray-100 dark:border-neutral-700">₱250/Visit</span>
                                    </div>
                                </div>
                            </div>

                            <!-- SITTER CARD 2: John D. (Level 1) -->
                            <div class="flex items-start p-4 bg-gray-50 dark:bg-neutral-800/20 border border-gray-100 dark:border-neutral-800/80 rounded-2xl space-x-3.5">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-accent-400 to-accent-500 flex-shrink-0 flex items-center justify-center font-bold text-white shadow-sm">
                                    JD
                                </div>
                                <div class="flex-grow min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <h4 class="font-bold text-sm text-gray-900 dark:text-white truncate">John D.</h4>
                                        <span class="bg-gray-400 text-white text-[9px] font-black px-2 py-0.5 rounded-md uppercase tracking-wide shrink-0">Level 1</span>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">⭐ 3.0 Baseline Default</p>
                                    <div class="flex gap-1.5 mt-2 flex-wrap">
                                        <span class="bg-white dark:bg-neutral-800 px-2 py-0.5 rounded-md text-[10px] font-medium text-gray-600 dark:text-gray-300 border border-gray-100 dark:border-neutral-700">Cats, Birds</span>
                                        <span class="bg-white dark:bg-neutral-800 px-2 py-0.5 rounded-md text-[10px] font-medium text-gray-600 dark:text-gray-300 border border-gray-100 dark:border-neutral-700">₱150/Visit</span>
                                    </div>
                                </div>
                            </div>

                            <!-- VIEW ALL LINK -->
                            <div class="pt-2">
                                <a href="#" class="block text-center text-sm font-bold text-primary hover:underline">View All Sitters →</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================ -->
        <!-- FEATURES SECTION                             -->
        <!-- 6 feature cards with icons                   -->
        <!-- ============================================ -->
        <section id="features" class="py-16 sm:py-20 bg-white dark:bg-neutral-900/50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- SECTION HEADER -->
                <div class="text-center mb-12">
                    <h2 class="text-3xl sm:text-4xl font-black text-gray-900 dark:text-white">
                        Why Pet Owners <span class="text-primary">Love PetNanny</span>
                    </h2>
                    <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">
                        Designed for convenience, trust, and peace of mind
                    </p>
                </div>

                <!-- FEATURES GRID -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    
                    <!-- FEATURE 1: Verified Sitters -->
                    <div class="bg-secondary/50 dark:bg-neutral-800/30 rounded-2xl p-6 border border-gray-200/50 dark:border-neutral-700/50 hover:shadow-lg transition">
                        <div class="w-12 h-12 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Verified Sitters</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Every sitter undergoes ID validation and background checks for your peace of mind.</p>
                    </div>

                    <!-- FEATURE 2: Real-time Updates -->
                    <div class="bg-secondary/50 dark:bg-neutral-800/30 rounded-2xl p-6 border border-gray-200/50 dark:border-neutral-700/50 hover:shadow-lg transition">
                        <div class="w-12 h-12 rounded-xl bg-accent-100 dark:bg-accent-900/30 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Real‑time Updates</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Get instant notifications, photo proof, and visit logs for every sitting session.</p>
                    </div>

                    <!-- FEATURE 3: Per-Visit Payment -->
                    <div class="bg-secondary/50 dark:bg-neutral-800/30 rounded-2xl p-6 border border-gray-200/50 dark:border-neutral-700/50 hover:shadow-lg transition">
                        <div class="w-12 h-12 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Per‑Visit Payment</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Pay only for each visit. No daily minimums, no hidden charges.</p>
                    </div>

                    <!-- FEATURE 4: Protection Fund -->
                    <div class="bg-secondary/50 dark:bg-neutral-800/30 rounded-2xl p-6 border border-gray-200/50 dark:border-neutral-700/50 hover:shadow-lg transition">
                        <div class="w-12 h-12 rounded-xl bg-accent-100 dark:bg-accent-900/30 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Protection Fund</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">2% of service fee goes to a protection fund for sitter injury claims.</p>
                    </div>

                    <!-- FEATURE 5: Flexible Booking -->
                    <div class="bg-secondary/50 dark:bg-neutral-800/30 rounded-2xl p-6 border border-gray-200/50 dark:border-neutral-700/50 hover:shadow-lg transition">
                        <div class="w-12 h-12 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Flexible Booking</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Choose your sitter based on location, ratings, and pet specialization.</p>
                    </div>

                    <!-- FEATURE 6: Open Community -->
                    <div class="bg-secondary/50 dark:bg-neutral-800/30 rounded-2xl p-6 border border-gray-200/50 dark:border-neutral-700/50 hover:shadow-lg transition">
                        <div class="w-12 h-12 rounded-xl bg-accent-100 dark:bg-accent-900/30 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Open Community</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Public comments and transparent reviews build trust across the community.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================ -->
        <!-- HOW IT WORKS SECTION                         -->
        <!-- 3-step process                              -->
        <!-- ============================================ -->
        <section class="py-16 sm:py-20 bg-secondary/30 dark:bg-neutral-900/30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- SECTION HEADER -->
                <div class="text-center mb-12">
                    <h2 class="text-3xl sm:text-4xl font-black text-gray-900 dark:text-white">
                        How <span class="text-primary">PetNanny</span> Works
                    </h2>
                    <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">
                        Getting started is simple – just three easy steps
                    </p>
                </div>

                <!-- STEPS GRID -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- STEP 1: Create Account -->
                    <div class="text-center">
                        <div class="w-16 h-16 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center mx-auto mb-4 text-2xl font-black text-primary">1</div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Create Account</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Register for free in minutes. One account works for both owners and sitters.</p>
                    </div>

                    <!-- STEP 2: Book a Sitter -->
                    <div class="text-center">
                        <div class="w-16 h-16 rounded-full bg-accent-100 dark:bg-accent-900/30 flex items-center justify-center mx-auto mb-4 text-2xl font-black text-accent">2</div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Book a Sitter</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Browse verified sitters, check availability, and send a booking request.</p>
                    </div>

                    <!-- STEP 3: Relax & Track -->
                    <div class="text-center">
                        <div class="w-16 h-16 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center mx-auto mb-4 text-2xl font-black text-primary">3</div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Relax & Track</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Receive real‑time updates, photos, and visit logs while you're away.</p>
                    </div>
                </div>

                <!-- CTA Button -->
                <div class="text-center mt-10">
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="inline-block bg-primary hover:bg-primary-600 text-white font-bold text-sm px-8 py-4 rounded-xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5">
                            Start Your First Booking →
                        </a>
                    @endif
                </div>
            </div>
        </section>

        <!-- ============================================ -->
        <!-- STATS / SOCIAL PROOF SECTION                 -->
        <!-- 4 stat cards                                -->
        <!-- ============================================ -->
        <section class="py-16 bg-white dark:bg-neutral-900/50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                    <div>
                        <p class="text-3xl sm:text-4xl font-black text-primary">1,200+</p>
                        <p class="text-sm font-bold text-gray-500 uppercase tracking-wider mt-1">Happy Pets</p>
                    </div>
                    <div>
                        <p class="text-3xl sm:text-4xl font-black text-accent">98%</p>
                        <p class="text-sm font-bold text-gray-500 uppercase tracking-wider mt-1">Satisfaction Rate</p>
                    </div>
                    <div>
                        <p class="text-3xl sm:text-4xl font-black text-primary">150+</p>
                        <p class="text-sm font-bold text-gray-500 uppercase tracking-wider mt-1">Verified Sitters</p>
                    </div>
                    <div>
                        <p class="text-3xl sm:text-4xl font-black text-accent">⭐ 4.9</p>
                        <p class="text-sm font-bold text-gray-500 uppercase tracking-wider mt-1">Average Rating</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================ -->
        <!-- CTA / FINAL SECTION                          -->
        <!-- Final call-to-action with gradient           -->
        <!-- ============================================ -->
        <section class="py-16 sm:py-20 bg-gradient-to-br from-primary-500 via-primary-600 to-accent-600 dark:from-primary-900 dark:via-primary-800 dark:to-accent-800">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
                <h2 class="text-3xl sm:text-4xl font-black">
                    Ready to find the perfect sitter <br>for your furry family?
                </h2>
                <p class="mt-4 text-lg text-white/80 max-w-2xl mx-auto">
                    Join thousands of pet owners who trust PetNanny for reliable, home-based pet care.
                </p>
                <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="bg-white text-primary hover:bg-gray-100 font-bold text-sm px-8 py-4 rounded-xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5">
                            Get Started – It's Free
                        </a>
                    @endif
                    <a href="{{ route('login') }}" class="bg-white/20 backdrop-blur-sm border-2 border-white/30 text-white hover:bg-white/30 font-bold text-sm px-8 py-4 rounded-xl transition transform hover:-translate-y-0.5">
                        Sign In
                    </a>
                </div>
            </div>
        </section>

        <!-- ============================================ -->
        <!-- FOOTER                                       -->
        <!-- ============================================ -->
        <footer class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 border-t border-gray-200/60 dark:border-neutral-800 text-center text-xs font-medium text-gray-500 dark:text-neutral-500 flex flex-col sm:flex-row justify-between items-center gap-2">
            <div>&copy; 2026 PetNanny Platform. All rights reserved. | Capstone Project</div>
            <div class="flex gap-4">
                <a href="#" class="hover:text-primary transition">Privacy Policy</a>
                <a href="#" class="hover:text-primary transition">Terms of Service</a>
                <a href="#" class="hover:text-primary transition">Contact</a>
            </div>
        </footer>

    </body>
</html>