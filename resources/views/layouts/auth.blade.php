<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- ========================================== -->
    <!-- HEADER: Meta tags, title, and styles      -->
    <!-- ========================================== -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'PetNanny') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900" rel="stylesheet" />

    <!-- ========================================== -->
    <!-- ASSETS: Vite or CDN fallback              -->
    <!-- ========================================== -->
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

<body class="bg-secondary dark:bg-neutral-950 text-gray-800 dark:text-gray-100 min-h-screen font-sans antialiased overflow-hidden">

    <!-- ========================================== -->
    <!-- MAIN CONTAINER: Full width split layout    -->
    <!-- ========================================== -->
    <div class="flex min-h-screen">
        
        <!-- ========================================== -->
        <!-- LEFT SIDE: Branding / Collage (60%)        -->
        <!-- Hidden on mobile (lg:flex)                 -->
        <!-- ========================================== -->
        <div class="hidden lg:flex lg:w-3/5 bg-gradient-to-br from-primary-300 via-primary-400 to-accent-400 dark:from-primary-800 dark:via-neutral-700 dark:to-neutral-600 relative overflow-hidden flex-col justify-between p-12">
            
            <!-- DECORATIVE BLOBS: Background blur effects -->
            <div class="absolute top-[-300px] right-[-200px] w-[700px] h-[700px] bg-white/20 dark:bg-white/10 rounded-full blur-3xl pointer-events-none animate-pulse-slow"></div>
            <div class="absolute bottom-[-200px] left-[-150px] w-[500px] h-[500px] bg-white/15 dark:bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-white/15 dark:bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
            
            <!-- DECORATIVE PAW PRINTS: Scattered icons -->
            <div class="absolute top-20 right-12 text-white/20 dark:text-white/10 text-6xl">🐾</div>
            <div class="absolute bottom-32 left-12 text-white/20 dark:text-white/10 text-5xl">🐾</div>
            <div class="absolute top-1/2 right-8 text-white/15 dark:text-white/5 text-4xl">🐾</div>

            <!-- ========================================== -->
            <!-- TOP: Logo and Brand Name                   -->
            <!-- ========================================== -->
            <div class="relative z-10">
                <div class="flex items-center space-x-4">
                    <img src="{{ asset('assets/logo/PetNanny_Logo.png') }}" alt="PetNanny" class="h-16 w-auto object-contain drop-shadow-2xl rounded-lg">
                    <div>
                        <span class="text-4xl font-black text-neutral-900 tracking-tight" style="filter: drop-shadow(1.5px 1.5px 1px rgba(0, 0, 0, 0.22)) drop-shadow(4px 4px 10px rgba(0, 0, 0, 0.30));">
                            Pet<span class="text-primary" style="text-shadow: 0.75px 0.75px 0px rgba(0,0,0,0.55), -0.75px -0.75px 0px rgba(0,0,0,0.55), 0.75px -0.75px 0px rgba(0,0,0,0.55), -0.75px 0.75px 0px rgba(0,0,0,0.55);">Nanny</span>
                        </span>
                        <p class="text-neutral-700 dark:text-neutral-300 text-xs font-bold tracking-widest uppercase mt-0.5">Pet Sitting Platform</p>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- MIDDLE: Collage Image and Tagline          -->
            <!-- ========================================== -->
            <div class="relative z-10 flex-1 flex flex-col items-center justify-center">
                <!-- COLLAGE IMAGE CONTAINER -->
                <div class="w-full max-w-2xl mx-auto relative">
                    <!-- Glowing border effect -->
                    <div class="absolute -inset-1 bg-gradient-to-r from-white/30 to-white/10 rounded-3xl blur-xl"></div>
                    <!-- IMAGE: July Blog Photo Collage -->
                    <img src="{{ asset('assets/image/July-Blog-Photo_collage.png') }}" 
                         alt="Happy pets collage" 
                         class="relative w-full h-auto rounded-3xl shadow-2xl border-4 border-white/50 animate-float">
                    
                    <!-- FLOATING BADGE: Rating overlay on image -->
                    <div class="absolute -bottom-3 -right-3 bg-white/95 backdrop-blur-sm rounded-2xl px-4 py-2 shadow-xl border border-white/50">
                        <div class="flex items-center gap-2">
                            <span class="text-yellow-400 text-sm">★★★★★</span>
                            <span class="text-xs font-bold text-gray-800">4.9</span>
                            <span class="text-xs text-gray-500">(1.2k)</span>
                        </div>
                    </div>
                </div>

                <!-- TAGLINE: Main messaging -->
                <div class="mt-8 text-center">
                    <h3 class="text-3xl font-black text-neutral-800 dark:text-neutral-100 tracking-tight drop-shadow-sm">
                        Book Trusted Pet Sitters in Minutes
                    </h3>
                    <p class="text-neutral-700 dark:text-neutral-300 text-sm max-w-xs mx-auto mt-2 font-bold">
                        No drop-offs • No hassle • Just quality care
                    </p>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- BOTTOM: Trust Badges / Social Proof        -->
            <!-- ========================================== -->
            <div class="relative z-10 text-neutral-900/90 dark:text-white/90 text-sm flex items-center justify-center gap-8 pt-6 border-t border-neutral-900/10 dark:border-white/20">
                <div class="flex items-center gap-2">
                    <span class="text-yellow-300 dark:text-yellow-200 text-lg" style="text-shadow: 0 0 10px rgba(255, 255, 0, 0.3), 0 0 20px rgba(255, 255, 0, 0.1), 0 0 2px rgba(255,255,255,0.5);">★★★★★</span>
                    <span class="font-black text-neutral-900 dark:text-white">4.9</span>
                </div>
                <div class="w-px h-8 bg-neutral-900/20 dark:bg-white/20"></div>
                <div class="text-center">
                    <div class="font-black text-neutral-900 dark:text-white text-lg">1,200+</div>
                    <div class="text-neutral-700 dark:text-white/60 text-xs font-bold uppercase tracking-wider">Pets</div>
                </div>
                <div class="w-px h-8 bg-neutral-900/20 dark:bg-white/20"></div>
                <div class="text-center">
                    <div class="font-black text-neutral-900 dark:text-white text-lg">98%</div>
                    <div class="text-neutral-700 dark:text-white/60 text-xs font-bold uppercase tracking-wider">Satisfaction</div>
                </div>
            </div>
        </div>
        <!-- ========================================== -->
        <!-- END OF LEFT SIDE                          -->
        <!-- ========================================== -->

        <!-- ========================================== -->
        <!-- RIGHT SIDE: Login Card Container (40%)    -->
        <!-- Visible on all screen sizes               -->
        <!-- ========================================== -->
        <div class="w-full lg:w-2/5 flex items-center justify-center p-4 sm:p-8 bg-secondary dark:bg-neutral-950 relative min-h-screen lg:min-h-0 bg-cover bg-center bg-no-repeat" 
            style="background-image: url('{{ asset('assets/image/Background1.png') }}'); background-attachment: fixed;">

            <!-- OVERLAY: To make text readable on top of background image -->
            <div class="absolute inset-0 bg-white/80 dark:bg-neutral-950/80 backdrop-blur-sm"></div>
            
            <!-- DECORATIVE PAW PRINTS -->
            <div class="absolute top-12 right-12 text-primary-200/30 dark:text-primary-500/10 text-5xl rotate-12 z-10">🐾</div>
            <div class="absolute bottom-20 left-8 text-accent-200/30 dark:text-accent-500/10 text-4xl -rotate-12 z-10">🐾</div>
            <div class="absolute top-1/3 right-6 text-primary-200/20 dark:text-primary-500/5 text-3xl z-10">🐾</div>
            <div class="absolute bottom-1/3 left-12 text-accent-200/20 dark:text-accent-500/5 text-3xl z-10">🐾</div>
            
            <!-- DECORATIVE BLOBS -->
            <div class="absolute top-[-200px] right-[-200px] w-[500px] h-[500px] bg-primary-200/15 dark:bg-primary-500/5 rounded-full blur-3xl pointer-events-none z-0"></div>
            <div class="absolute bottom-[-150px] left-[-150px] w-[400px] h-[400px] bg-accent-200/15 dark:bg-accent-500/5 rounded-full blur-3xl pointer-events-none z-0"></div>
            
            <!-- RADIAL GRADIENT -->
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-primary-100/5 via-transparent to-transparent dark:from-primary-500/5 pointer-events-none z-0"></div>

            <!-- MOBILE LOGO -->
            <div class="absolute top-6 left-6 lg:hidden z-20">
                <a href="/" class="flex items-center space-x-2.5">
                    <img src="{{ asset('assets/logo/PetNanny_Logo.png') }}" alt="PetNanny Logo" class="h-10 w-auto object-contain drop-shadow-md rounded-lg">
                    <span class="text-xl font-black tracking-tight text-neutral-900 dark:text-white">
                        Pet<span class="text-primary">Nanny</span>
                    </span>
                </a>
            </div>

            <!-- ========================================== -->
            <!-- LOGIN CARD: The main form container        -->
            <!-- ========================================== -->
            <div class="w-full max-w-md relative z-10">
                @yield('content')
            </div>
        </div>
        <!-- ========================================== -->
        <!-- END OF RIGHT SIDE                         -->
        <!-- ========================================== -->

    </div>
    <!-- ========================================== -->
    <!-- END OF MAIN CONTAINER                     -->
    <!-- ========================================== -->

</body>
</html>