<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ url()->previous() }}"
               class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-black text-[#1B3B36] dark:text-white">
                    {{ __('messages.search_results_title') }}
                </h2>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                    {{ __('messages.search_results_for', ['query' => $query]) }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200">
        <div class="w-full sm:max-w-7xl mx-auto px-4 sm:px-16 lg:px-24">

            @if($users->isEmpty())
                <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-gray-100 dark:border-neutral-800 p-12 text-center">
                    <div class="w-16 h-16 mx-auto bg-neutral-100 dark:bg-neutral-800 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-[#1B3B36] dark:text-white">{{ __('messages.search_no_accounts_title') }}</h3>
                    <p class="text-sm text-neutral-500 mt-1">{{ __('messages.search_no_accounts_desc', ['query' => $query]) }}</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                    @foreach($users as $user)
                        @php
                            $visibility = $user->setting->profile_visibility ?? 'public';
                        @endphp

                        <a href="{{ route('public.profile', $user->id) }}" class="group bg-white dark:bg-neutral-900 rounded-2xl p-5 border border-gray-200 dark:border-neutral-800 hover:border-primary/50 dark:hover:border-primary/50 hover:shadow-lg transition-all duration-300">
                            <div class="flex flex-col items-center text-center">
                                @if($user->profile_photo)
                                    <img src="{{ $user->profile_photo_url }}" class="w-20 h-20 rounded-full object-cover border-4 border-white dark:border-neutral-800 shadow-sm group-hover:scale-105 transition-transform">
                                @else
                                    <div class="w-20 h-20 rounded-full bg-primary/10 flex items-center justify-center text-primary text-2xl font-bold border-4 border-white dark:border-neutral-800 shadow-sm group-hover:scale-105 transition-transform">
                                        {{ strtoupper(substr($user->f_name ?? 'U', 0, 1)) }}
                                    </div>
                                @endif

                                <h3 class="font-bold text-lg text-[#1B3B36] dark:text-white mt-3 truncate w-full">{{ $user->full_name }}</h3>

                                <p class="text-sm text-gray-500 flex items-center justify-center gap-1 mt-1">
                                    @if($visibility === 'private')
                                        <span class="inline-flex items-center text-amber-600 bg-amber-50 dark:bg-amber-900/20 px-2 py-0.5 rounded-md text-xs font-bold">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                            {{ __('messages.vis_private') }}
                                        </span>
                                    @else
                                        <span class="text-xs font-medium {{ $user->is_sitter ? 'text-primary' : 'text-neutral-500' }}">
                                            {{ $user->is_sitter ? __('messages.role_sitter') : __('messages.role_owner') }}
                                        </span>
                                    @endif
                                </p>

                                @if($user->location)
                                    <p class="text-xs text-neutral-400 mt-2 truncate w-full">
                                        📍 {{ $user->location }}
                                    </p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-8 flex justify-center">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
