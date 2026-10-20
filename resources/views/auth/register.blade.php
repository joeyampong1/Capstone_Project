@extends('layouts.auth')

@section('content')
    <!-- ========================================== -->
    <!-- REGISTER CARD CONTAINER                    -->
    <!-- Card with glassmorphism effect             -->
    <!-- ========================================== -->
    <div class="w-full px-6 py-8 bg-white dark:bg-neutral-900 shadow-xl rounded-3xl border border-gray-200/80 dark:border-neutral-800 transition-all">

        <!-- ========================================== -->
        <!-- BRANDING / LOGO SECTION                    -->
        <!-- ========================================== -->
        <div class="flex flex-col items-center justify-center mb-8 space-y-3">
            <!-- LOGO: Clickable to homepage -->
            <a href="/" class="flex items-center space-x-2.5">
                <img src="{{ asset('assets/logo/PetNanny_Logo.png') }}" alt="PetNanny Logo" class="h-10 w-auto object-contain">
                <span class="text-2xl font-black tracking-tight text-neutral-900 dark:text-white">
                    Pet<span class="text-primary">Nanny</span>
                </span>
            </a>

            <!-- DIVIDER LINE -->
            <hr class="w-full border-gray-200 dark:border-neutral-800">

            <!-- WELCOME HEADER -->
            <div class="mt-4 text-center">
                <h2 class="text-2xl font-black text-gray-900 dark:text-white">Create Account</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Join PetNanny today</p>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- REGISTER FORM START                       -->
        <!-- ========================================== -->
        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <!-- NAME FIELDS: First, Middle, Last -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                    {{ __('Full Name') }}
                </label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <!-- First Name -->
                    <div>
                        <label for="f_name" class="block text-[10px] font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400 mb-0.5">
                            {{ __('First') }}
                        </label>
                        <input id="f_name" class="block w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium" type="text" name="f_name" placeholder="e.g. Juan" value="{{ old('f_name') }}" required autofocus autocomplete="given-name" />
                        <x-input-error :messages="$errors->get('f_name')" class="text-xs font-semibold text-red-500 mt-1" />
                    </div>
                    <!-- Middle Name -->
                    <div>
                        <label for="m_name" class="block text-[10px] font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400 mb-0.5">
                            {{ __('Middle') }}
                        </label>
                        <input id="m_name" class="block w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium" type="text" name="m_name" placeholder="(optional)" value="{{ old('m_name') }}" autocomplete="additional-name" />
                        <x-input-error :messages="$errors->get('m_name')" class="text-xs font-semibold text-red-500 mt-1" />
                    </div>
                    <!-- Last Name -->
                    <div>
                        <label for="l_name" class="block text-[10px] font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400 mb-0.5">
                            {{ __('Last') }}
                        </label>
                        <input id="l_name" class="block w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium" type="text" name="l_name" placeholder="e.g. Dela Cruz" value="{{ old('l_name') }}" required autocomplete="family-name" />
                        <x-input-error :messages="$errors->get('l_name')" class="text-xs font-semibold text-red-500 mt-1" />
                    </div>
                </div>
            </div>

            <!-- EMAIL FIELD -->
            <div class="space-y-1.5">
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                    {{ __('Email Address') }}
                </label>
                <div class="relative rounded-xl shadow-sm">
                    <input id="email" class="block w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium" type="email" name="email" placeholder="name@example.com" value="{{ old('email') }}" required autocomplete="username" />
                </div>
                <x-input-error :messages="$errors->get('email')" class="text-xs font-semibold text-red-500 mt-1" />
            </div>

            <!-- PASSWORD FIELD -->
            <div class="space-y-1.5">
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                    {{ __('Password') }}
                </label>
                <div class="relative rounded-xl shadow-sm">
                    <input id="password" class="block w-full px-4 py-3 pr-12 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium" type="password" name="password" placeholder="••••••••" required autocomplete="new-password" />

                    <button type="button" onclick="togglePasswordVisibility('password')" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 dark:text-gray-400 hover:text-primary dark:hover:text-primary-400 transition">
                        <svg id="eye-open-password" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg id="eye-closed-password" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="text-xs font-semibold text-red-500 mt-1" />
            </div>

            <!-- CONFIRM PASSWORD FIELD -->
            <div class="space-y-1.5">
                <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                    {{ __('Confirm Password') }}
                </label>
                <div class="relative rounded-xl shadow-sm">
                    <input id="password_confirmation" class="block w-full px-4 py-3 pr-12 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium" type="password" name="password_confirmation" placeholder="••••••••" required autocomplete="new-password" />

                    <button type="button" onclick="togglePasswordVisibility('password_confirmation')" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 dark:text-gray-400 hover:text-primary dark:hover:text-primary-400 transition">
                        <svg id="eye-open-confirm" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg id="eye-closed-confirm" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="text-xs font-semibold text-red-500 mt-1" />
            </div>

            <!-- SUBMIT BUTTON -->
            <div class="pt-2">
                <button type="submit" class="w-full flex justify-center items-center px-4 py-3.5 bg-primary hover:bg-primary-600 text-white text-sm font-black rounded-xl shadow-md hover:shadow-lg active:scale-[0.99] transition-all duration-150">
                    {{ __('Create Account') }}
                </button>
            </div>
        </form>
        <!-- ========================================== -->
        <!-- FORM END                                  -->
        <!-- ========================================== -->

        <!-- ========================================== -->
        <!-- SOCIAL LOGIN & LOGIN LINK                 -->
        <!-- ========================================== -->
        <div class="mt-6">
            <!-- DIVIDER -->
            <div class="relative flex items-center justify-center">
                <div class="absolute border-t border-gray-200 dark:border-neutral-800 w-full"></div>
                <div class="relative bg-white dark:bg-neutral-900 px-4 text-[10px] font-black uppercase tracking-widest text-gray-400">
                    OR
                </div>
            </div>

            <!-- GOOGLE LOGIN BUTTON -->
            <div class="mt-6">
                <a href="{{ route('auth.google') }}"
                   class="w-full flex items-center justify-center gap-3 px-4 py-3 bg-white dark:bg-neutral-950 border border-gray-200 dark:border-neutral-800 rounded-xl hover:bg-gray-50 dark:hover:bg-neutral-900 transition-all shadow-sm active:scale-[0.99]">
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                    </svg>
                    <span class="text-sm font-bold text-gray-700 dark:text-gray-300">Continue with Google</span>
                </a>
            </div>

            <!-- LOGIN LINK -->
            <div class="text-center pt-6">
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                    Already have an account?
                    <a href="{{ route('login') }}" class="text-primary font-bold hover:underline">Sign in</a>
                </p>
            </div>
        </div>
    </div>
    <!-- ========================================== -->
    <!-- END OF REGISTER CARD                      -->
    <!-- ========================================== -->

    <!-- ========================================== -->
    <!-- PASSWORD VISIBILITY TOGGLE SCRIPT         -->
    <!-- ========================================== -->
    <script>
        function togglePasswordVisibility(fieldId) {
            const passwordInput = document.getElementById(fieldId);
            const eyeOpen = document.getElementById('eye-open-' + fieldId);
            const eyeClosed = document.getElementById('eye-closed-' + fieldId);

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeClosed.classList.add('hidden');
                eyeOpen.classList.remove('hidden');
            }
        }
    </script>
@endsection
