@extends('layouts.auth')

@section('content')
    <!-- ========================================== -->
    <!-- RESET PASSWORD CARD CONTAINER              -->
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
            
            <!-- PAGE HEADER -->
            <div class="mt-4 text-center">
                <h2 class="text-2xl font-black text-gray-900 dark:text-white">Reset Password</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Enter your new password below</p>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- RESET PASSWORD FORM START                 -->
        <!-- ========================================== -->
        <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- ========================================== -->
            <!-- EMAIL FIELD (readonly / disabled)         -->
            <!-- ========================================== -->
            <div class="space-y-1.5">
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                    {{ __('Email Address') }}
                </label>
                <div class="relative rounded-xl shadow-sm">
                    <input id="email"
                           class="block w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-100 dark:bg-neutral-800 text-gray-500 dark:text-gray-400 cursor-not-allowed text-sm font-medium"
                           type="email"
                           name="email"
                           value="{{ old('email', $request->email) }}"
                           required
                           readonly
                           autocomplete="username" />
                </div>
                <x-input-error :messages="$errors->get('email')" class="text-xs font-semibold text-red-500 mt-1" />
            </div>

            <!-- ========================================== -->
            <!-- NEW PASSWORD FIELD WITH EYE TOGGLE        -->
            <!-- ========================================== -->
            <div class="space-y-1.5">
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                    {{ __('New Password') }}
                </label>
                <div class="relative rounded-xl shadow-sm">
                    <input id="password"
                           class="block w-full px-4 py-3 pr-12 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium"
                           type="password"
                           name="password"
                           placeholder="••••••••"
                           required
                           autocomplete="new-password" />
                    
                    <!-- EYE TOGGLE BUTTON: Show/hide password -->
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

            <!-- ========================================== -->
            <!-- CONFIRM PASSWORD FIELD WITH EYE TOGGLE    -->
            <!-- ========================================== -->
            <div class="space-y-1.5">
                <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                    {{ __('Confirm New Password') }}
                </label>
                <div class="relative rounded-xl shadow-sm">
                    <input id="password_confirmation"
                           class="block w-full px-4 py-3 pr-12 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium"
                           type="password"
                           name="password_confirmation"
                           placeholder="••••••••"
                           required
                           autocomplete="new-password" />
                    
                    <!-- EYE TOGGLE BUTTON: Show/hide confirm password -->
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

            <!-- ========================================== -->
            <!-- SUBMIT BUTTON & BACK TO LOGIN             -->
            <!-- ========================================== -->
            <div class="pt-2">
                <button type="submit" class="w-full flex justify-center items-center px-4 py-3.5 bg-primary hover:bg-primary-600 text-white text-sm font-black rounded-xl shadow-md hover:shadow-lg active:scale-[0.99] transition-all duration-150">
                    {{ __('Reset Password') }}
                </button>
            </div>

            <!-- ========================================== -->
            <!-- BACK TO LOGIN LINK                        -->
            <!-- ========================================== -->
            <div class="text-center pt-4 border-t border-gray-100 dark:border-neutral-800/60">
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                    Remember your password?
                    <a href="{{ route('login') }}" class="text-primary font-bold hover:underline">Sign in</a>
                </p>
            </div>

        </form>
        <!-- ========================================== -->
        <!-- FORM END                                  -->
        <!-- ========================================== -->
    </div>
    <!-- ========================================== -->
    <!-- END OF RESET PASSWORD CARD                -->
    <!-- ========================================== -->

    <!-- ========================================== -->
    <!-- PASSWORD VISIBILITY TOGGLE SCRIPT         -->
    <!-- Toggles between password and text input    -->
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