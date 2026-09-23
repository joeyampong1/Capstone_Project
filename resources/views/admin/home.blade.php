@php
    $admin = auth()->user();
    $firstName = $admin->f_name ?? 'Admin';
    $hour = (int) now()->format('H');
    if ($hour < 12)      $greeting = __('messages.ah_good_morning');
    elseif ($hour < 18)  $greeting = __('messages.ah_good_afternoon');
    else                 $greeting = __('messages.ah_good_evening');
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary to-primary/70 flex items-center justify-center text-white shadow-lg shadow-primary/30 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                        {{ __('messages.ah_title') }}
                    </h1>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.ah_subtitle') }}</p>
                </div>
            </div>

            {{-- Link to full dashboard --}}
            <a href="{{ route('admin.dashboard') }}"
               class="hidden sm:inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-800 hover:border-primary/50 text-neutral-700 dark:text-neutral-200 font-bold text-xs rounded-xl hover:shadow-md transition group">
                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                {{ __('messages.ah_full_dashboard') }}
                <svg class="w-3.5 h-3.5 text-neutral-400 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </x-slot>

    <div class="py-4 sm:py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200">
        <div class="w-full sm:max-w-7xl mx-auto px-2 sm:px-16 lg:px-24">

            {{-- ════════════════════════════════════════ --}}
            {{-- ① HERO WELCOME (Personal command center) --}}
            {{-- ════════════════════════════════════════ --}}
            <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl bg-gradient-to-br from-[#1B3B36] via-[#1B3B36] to-[#2a5a52] dark:from-[#0f2823] dark:via-[#0f2823] dark:to-[#1B3B36] p-5 sm:p-8 mb-6 shadow-xl">

                <div class="absolute -top-20 -right-20 w-64 h-64 rounded-full bg-primary/20 blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-20 -left-20 w-72 h-72 rounded-full bg-primary/10 blur-3xl pointer-events-none"></div>

                <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    <div class="min-w-0 flex-1">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 mb-4">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span>
                            <span class="text-[10px] sm:text-xs font-bold text-white/90 uppercase tracking-wider">{{ __('messages.ah_system_online') }}</span>
                        </div>

                        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-white leading-tight">
                            {{ $greeting }}, <span class="text-primary">{{ $firstName }}</span>! 👋
                        </h2>
                        <p class="text-sm sm:text-base text-white/70 mt-2 max-w-2xl">
                            {{ __('messages.ah_home_command_desc') }}
                        </p>

                        <div class="flex flex-wrap items-center gap-3 mt-5">
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white/10 backdrop-blur-sm border border-white/10">
                                <svg class="w-3.5 h-3.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-xs font-bold text-white">{{ now()->format('F j, Y') }}</span>
                            </div>
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white/10 backdrop-blur-sm border border-white/10">
                                <svg class="w-3.5 h-3.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-xs font-bold text-white">{{ now()->format('g:i A') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ════════════════════════════════════════ --}}
            {{-- ② NEEDS YOUR ATTENTION — Action Queue  --}}
            {{-- ════════════════════════════════════════ --}}
            <div class="mb-6">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-red-100/30 flex items-center justify-center text-red-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </div>
                        <h2 class="font-bold text-base text-[#1B3B36] dark:text-white">{{ __('messages.ah_needs_attention') }}</h2>
                    </div>
                    <span class="text-[10px] text-neutral-400 font-bold uppercase tracking-wider">{{ __('messages.ah_action_required') }}</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">

                    {{-- Sitter Applications --}}
                    @php $sitterCount = $stats['pendingSitterApps'] ?? 0; @endphp
                    <a href="{{ route('admin.verification.sitter') }}"
                       class="group relative bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border-2 {{ $sitterCount > 0 ? 'border-amber-400/60 dark:border-amber-500/40' : 'border-gray-200/60 dark:border-neutral-800' }} p-4 hover:shadow-xl hover:-translate-y-0.5 transition-all overflow-hidden">
                        @if($sitterCount > 0)
                            <div class="absolute top-0 right-0 w-20 h-20 bg-amber-400/10 rounded-full blur-2xl"></div>
                        @endif
                        <div class="relative">
                            <div class="flex items-start justify-between gap-2 mb-3">
                                <div class="w-10 h-10 rounded-xl bg-amber-100/40 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 shrink-0 group-hover:scale-110 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                </div>
                                @if($sitterCount > 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-500 text-white text-[10px] font-black uppercase tracking-wider">
                                        <span class="w-1 h-1 rounded-full bg-white animate-pulse"></span>
                                        {{ __('messages.ah_action') }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-[10px] text-neutral-500 dark:text-neutral-400 font-bold uppercase tracking-wider">{{ __('messages.ah_task_sitter_apps') }}</p>
                            <h3 class="font-black text-3xl text-[#1B3B36] dark:text-white mt-1">{{ $sitterCount }}</h3>
                            <p class="text-[10px] text-amber-600 dark:text-amber-400 mt-2 flex items-center gap-1 font-medium">
                                {{ $sitterCount > 0 ? __('messages.ah_task_review') : __('messages.ah_all_clear') }}
                                <svg class="w-3 h-3 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </p>
                        </div>
                    </a>

                    {{-- ID Verifications --}}
                    @php $idCount = $stats['pendingVerification'] ?? 0; @endphp
                    <a href="{{ route('admin.verification.id') }}"
                       class="group relative bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border-2 {{ $idCount > 0 ? 'border-blue-400/60 dark:border-blue-500/40' : 'border-gray-200/60 dark:border-neutral-800' }} p-4 hover:shadow-xl hover:-translate-y-0.5 transition-all overflow-hidden">
                        @if($idCount > 0)
                            <div class="absolute top-0 right-0 w-20 h-20 bg-blue-400/10 rounded-full blur-2xl"></div>
                        @endif
                        <div class="relative">
                            <div class="flex items-start justify-between gap-2 mb-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-100/40 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 shrink-0 group-hover:scale-110 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/>
                                    </svg>
                                </div>
                                @if($idCount > 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-blue-500 text-white text-[10px] font-black uppercase tracking-wider">
                                        <span class="w-1 h-1 rounded-full bg-white animate-pulse"></span>
                                        {{ __('messages.ah_action') }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-[10px] text-neutral-500 dark:text-neutral-400 font-bold uppercase tracking-wider">{{ __('messages.ah_task_id_verif') }}</p>
                            <h3 class="font-black text-3xl text-[#1B3B36] dark:text-white mt-1">{{ $idCount }}</h3>
                            <p class="text-[10px] text-blue-600 dark:text-blue-400 mt-2 flex items-center gap-1 font-medium">
                                {{ $idCount > 0 ? __('messages.ah_task_review') : __('messages.ah_all_clear') }}
                                <svg class="w-3 h-3 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </p>
                        </div>
                    </a>

                    {{-- Complaints --}}
                    @php $complaintCount = $stats['pendingComplaints'] ?? 0; @endphp
                    <a href="{{ route('admin.complaints') }}"
                       class="group relative bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border-2 {{ $complaintCount > 0 ? 'border-red-400/60 dark:border-red-500/40' : 'border-gray-200/60 dark:border-neutral-800' }} p-4 hover:shadow-xl hover:-translate-y-0.5 transition-all overflow-hidden">
                        @if($complaintCount > 0)
                            <div class="absolute top-0 right-0 w-20 h-20 bg-red-400/10 rounded-full blur-2xl"></div>
                        @endif
                        <div class="relative">
                            <div class="flex items-start justify-between gap-2 mb-3">
                                <div class="w-10 h-10 rounded-xl bg-red-100/40 dark:bg-red-900/30 flex items-center justify-center text-red-600 shrink-0 group-hover:scale-110 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                                @if($complaintCount > 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-500 text-white text-[10px] font-black uppercase tracking-wider">
                                        <span class="w-1 h-1 rounded-full bg-white animate-pulse"></span>
                                        {{ __('messages.ah_action') }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-[10px] text-neutral-500 dark:text-neutral-400 font-bold uppercase tracking-wider">{{ __('messages.ah_task_complaints') }}</p>
                            <h3 class="font-black text-3xl text-[#1B3B36] dark:text-white mt-1">{{ $complaintCount }}</h3>
                            <p class="text-[10px] text-red-600 dark:text-red-400 mt-2 flex items-center gap-1 font-medium">
                                {{ $complaintCount > 0 ? __('messages.ah_task_attention') : __('messages.ah_all_clear') }}
                                <svg class="w-3 h-3 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </p>
                        </div>
                    </a>

                    {{-- Messages --}}
                    @php $msgCount = $stats['openMessages'] ?? 0; @endphp
                    <a href="{{ route('admin.messages.index') }}"
                       class="group relative bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border-2 {{ $msgCount > 0 ? 'border-purple-400/60 dark:border-purple-500/40' : 'border-gray-200/60 dark:border-neutral-800' }} p-4 hover:shadow-xl hover:-translate-y-0.5 transition-all overflow-hidden">
                        @if($msgCount > 0)
                            <div class="absolute top-0 right-0 w-20 h-20 bg-purple-400/10 rounded-full blur-2xl"></div>
                        @endif
                        <div class="relative">
                            <div class="flex items-start justify-between gap-2 mb-3">
                                <div class="w-10 h-10 rounded-xl bg-purple-100/40 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 shrink-0 group-hover:scale-110 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                @if($msgCount > 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-purple-500 text-white text-[10px] font-black uppercase tracking-wider">
                                        <span class="w-1 h-1 rounded-full bg-white animate-pulse"></span>
                                        {{ __('messages.ah_action') }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-[10px] text-neutral-500 dark:text-neutral-400 font-bold uppercase tracking-wider">{{ __('messages.ah_task_messages') }}</p>
                            <h3 class="font-black text-3xl text-[#1B3B36] dark:text-white mt-1">{{ $msgCount }}</h3>
                            <p class="text-[10px] text-purple-600 dark:text-purple-400 mt-2 flex items-center gap-1 font-medium">
                                {{ $msgCount > 0 ? __('messages.ah_task_respond') : __('messages.ah_all_clear') }}
                                <svg class="w-3 h-3 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </p>
                        </div>
                    </a>

                </div>
            </div>

            {{-- ════════════════════════════════════════ --}}
            {{-- ③ RECENT ACTIVITY (Full-width)         --}}
            {{-- ════════════════════════════════════════ --}}
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 overflow-hidden mb-6">
                <div class="px-4 sm:px-6 py-4 border-b border-gray-100 dark:border-neutral-800 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white">{{ __('messages.ah_recent_activity') }}</h3>
                    </div>
                    <a href="{{ route('admin.dashboard') }}" class="text-xs text-primary font-bold hover:underline">{{ __('messages.ah_view_all') }}</a>
                </div>

                <div class="p-4 sm:p-6">
                    @if (!empty($recentActivities) && count($recentActivities) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            @foreach ($recentActivities as $activity)
                                <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition border border-transparent hover:border-gray-100 dark:hover:border-neutral-700">
                                    <div class="w-10 h-10 rounded-lg {{ $activity['icon_bg'] ?? 'bg-primary/10' }} flex items-center justify-center {{ $activity['icon_color'] ?? 'text-primary' }} shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $activity['icon'] ?? 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' }}"/>
                                        </svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs sm:text-sm text-[#1B3B36] dark:text-white font-medium">{{ $activity['text'] ?? '' }}</p>
                                        <p class="text-[10px] sm:text-xs text-neutral-400 mt-1">{{ $activity['time'] ?? '' }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-14 h-14 mx-auto rounded-full bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center text-neutral-400 mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                            </div>
                            <p class="text-xs text-neutral-400">{{ __('messages.ah_no_activity') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ════════════════════════════════════════ --}}
            {{-- ④ QUICK ACCESS — Common shortcuts      --}}
            {{-- ════════════════════════════════════════ --}}
            <div class="mb-6">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white">{{ __('messages.ah_quick_access') }}</h3>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-3">

                    <a href="{{ route('admin.users') }}"
                       class="group flex flex-col items-center justify-center gap-2 p-4 rounded-2xl bg-white dark:bg-neutral-900 border border-gray-200/60 dark:border-neutral-800 hover:border-primary/40 hover:shadow-md transition">
                        <div class="w-11 h-11 rounded-xl bg-primary/10 flex items-center justify-center text-primary group-hover:scale-110 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <p class="text-[11px] font-bold text-[#1B3B36] dark:text-white text-center">{{ __('messages.ah_users') }}</p>
                    </a>

                    <a href="{{ route('admin.bookings') }}"
                       class="group flex flex-col items-center justify-center gap-2 p-4 rounded-2xl bg-white dark:bg-neutral-900 border border-gray-200/60 dark:border-neutral-800 hover:border-blue-400/40 hover:shadow-md transition">
                        <div class="w-11 h-11 rounded-xl bg-blue-100/30 flex items-center justify-center text-blue-600 group-hover:scale-110 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <p class="text-[11px] font-bold text-[#1B3B36] dark:text-white text-center">{{ __('messages.ah_bookings') }}</p>
                    </a>

                    <a href="{{ route('admin.analytics') }}"
                       class="group flex flex-col items-center justify-center gap-2 p-4 rounded-2xl bg-white dark:bg-neutral-900 border border-gray-200/60 dark:border-neutral-800 hover:border-green-400/40 hover:shadow-md transition">
                        <div class="w-11 h-11 rounded-xl bg-green-100/30 flex items-center justify-center text-green-600 group-hover:scale-110 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <p class="text-[11px] font-bold text-[#1B3B36] dark:text-white text-center">{{ __('messages.ah_analytics') }}</p>
                    </a>

                    <a href="{{ route('admin.reports') }}"
                       class="group flex flex-col items-center justify-center gap-2 p-4 rounded-2xl bg-white dark:bg-neutral-900 border border-gray-200/60 dark:border-neutral-800 hover:border-purple-400/40 hover:shadow-md transition">
                        <div class="w-11 h-11 rounded-xl bg-purple-100/30 flex items-center justify-center text-purple-600 group-hover:scale-110 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <p class="text-[11px] font-bold text-[#1B3B36] dark:text-white text-center">{{ __('messages.ah_reports') }}</p>
                    </a>

                    <a href="{{ route('admin.help_support') }}"
                       class="group flex flex-col items-center justify-center gap-2 p-4 rounded-2xl bg-white dark:bg-neutral-900 border border-gray-200/60 dark:border-neutral-800 hover:border-primary/40 hover:shadow-md transition">
                        <div class="w-11 h-11 rounded-xl bg-primary/10 flex items-center justify-center text-primary group-hover:scale-110 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <p class="text-[11px] font-bold text-[#1B3B36] dark:text-white text-center">{{ __('messages.ah_help_support') }}</p>
                    </a>

                    <a href="{{ route('profile.edit') }}"
                       class="group flex flex-col items-center justify-center gap-2 p-4 rounded-2xl bg-white dark:bg-neutral-900 border border-gray-200/60 dark:border-neutral-800 hover:border-primary/40 hover:shadow-md transition">
                        <div class="w-11 h-11 rounded-xl bg-primary/10 flex items-center justify-center text-primary group-hover:scale-110 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <p class="text-[11px] font-bold text-[#1B3B36] dark:text-white text-center">{{ __('messages.ah_my_profile') }}</p>
                    </a>

                </div>
            </div>

            {{-- ════════════════════════════════════════ --}}
            {{-- ⑤ FOOTER STATUS                        --}}
            {{-- ════════════════════════════════════════ --}}
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs text-neutral-500 dark:text-neutral-400 pt-2">
                <div class="flex items-center gap-2">
                    <span class="inline-block w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                    <span class="font-medium">{{ __('messages.ah_system_healthy') }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <span>{{ __('messages.ah_last_login') }} {{ now()->format('M j, Y · g:i A') }}</span>
                    <span class="text-neutral-300 dark:text-neutral-700">·</span>
                    <span class="font-bold text-primary">PetNanny Admin</span>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>