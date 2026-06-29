@extends('layouts.auth')

@section('content')
    <!-- ========================================== -->
    <!-- VERIFY EMAIL CARD CONTAINER                -->
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
                <h2 class="text-2xl font-black text-gray-900 dark:text-white">Verify Your Email</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Confirm your email address to get started</p>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- INFO MESSAGE                               -->
        <!-- ========================================== -->
        <div class="mb-6 p-4 bg-primary-50/50 dark:bg-primary-900/20 rounded-xl border border-primary-100 dark:border-primary-800/30 text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
            {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
        </div>

        <!-- ========================================== -->
        <!-- SUCCESS MESSAGE                            -->
        <!-- ========================================== -->
        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 p-3 bg-accent-50 dark:bg-accent-950/20 text-accent-700 dark:text-accent-400 rounded-xl border border-accent-200/50 text-sm font-semibold">
                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
            </div>
        @endif

        <!-- ========================================== -->
        <!-- VERIFY EMAIL FORM                          -->
        <!-- ========================================== -->
        <form method="POST" action="{{ route('verification.send') }}" class="space-y-5">
            @csrf

            <!-- RESEND BUTTON -->
            <div class="pt-2">
                <button type="submit" class="w-full flex justify-center items-center px-4 py-3.5 bg-primary hover:bg-primary-600 text-white text-sm font-black rounded-xl shadow-md hover:shadow-lg active:scale-[0.99] transition-all duration-150">
                    {{ __('Resend Verification Email') }}
                </button>
            </div>

            <!-- ========================================== -->
            <!-- LOGOUT LINK                               -->
            <!-- ========================================== -->
            <div class="text-center pt-4 border-t border-gray-100 dark:border-neutral-800/60">
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                    Not {{ Auth::user()->email ?? '' }}?
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-primary font-bold hover:underline">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </p>
            </div>

        </form>
        <!-- ========================================== -->
        <!-- FORM END                                  -->
        <!-- ========================================== -->
    </div>
    <!-- ========================================== -->
    <!-- END OF VERIFY EMAIL CARD                  -->
    <!-- ========================================== -->
@endsection