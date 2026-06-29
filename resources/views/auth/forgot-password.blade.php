@extends('layouts.auth')

@section('content')
    <!-- ========================================== -->
    <!-- FORGOT PASSWORD CARD CONTAINER              -->
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
                <h2 class="text-2xl font-black text-gray-900 dark:text-white">Forgot Password</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Reset your password via email</p>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- INFO MESSAGE                               -->
        <!-- ========================================== -->
        <div class="mb-6 p-4 bg-primary-50/50 dark:bg-primary-900/20 rounded-xl border border-primary-100 dark:border-primary-800/30 text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
        </div>

        <!-- ========================================== -->
        <!-- SESSION STATUS MESSAGE                     -->
        <!-- Displays success/error messages            -->
        <!-- ========================================== -->
        <x-auth-session-status class="mb-4 p-3 bg-accent-50 dark:bg-accent-950/20 text-accent-700 dark:text-accent-400 rounded-xl border border-accent-200/50 text-sm font-semibold" :status="session('status')" />

        <!-- ========================================== -->
        <!-- FORGOT PASSWORD FORM START                 -->
        <!-- ========================================== -->
        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            <!-- ========================================== -->
            <!-- EMAIL FIELD                               -->
            <!-- ========================================== -->
            <div class="space-y-1.5">
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                    {{ __('Email Address') }}
                </label>
                <div class="relative rounded-xl shadow-sm">
                    <input id="email"
                           class="block w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium"
                           type="email"
                           name="email"
                           placeholder="name@example.com"
                           value="{{ old('email') }}"
                           required
                           autofocus
                           autocomplete="username" />
                </div>
                <x-input-error :messages="$errors->get('email')" class="text-xs font-semibold text-red-500 mt-1" />
            </div>

            <!-- ========================================== -->
            <!-- SUBMIT BUTTON & BACK TO LOGIN             -->
            <!-- ========================================== -->
            <div class="pt-2">
                <button type="submit" class="w-full flex justify-center items-center px-4 py-3.5 bg-primary hover:bg-primary-600 text-white text-sm font-black rounded-xl shadow-md hover:shadow-lg active:scale-[0.99] transition-all duration-150">
                    {{ __('Send Reset Link') }}
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
    <!-- END OF FORGOT PASSWORD CARD               -->
    <!-- ========================================== -->
@endsection