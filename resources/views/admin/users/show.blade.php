<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="javascript:history.back()"
            class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div class="min-w-0">
                <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                    {{ __('messages.ud_title') }}
                </h1>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                    {{ trim(($user->f_name ?? '') . ' ' . ($user->l_name ?? '')) ?: $user->email }}
                </p>
            </div>
        </div>
    </x-slot>

    @php
        $fullName = trim(($user->f_name ?? '') . ' ' . ($user->l_name ?? '')) ?: 'User';
        $initials = strtoupper(substr($user->f_name ?? 'U', 0, 1) . substr($user->l_name ?? '', 0, 1));

        if ($user->isAdmin()) {
            $roleLabel = __('messages.um_role_admin');
            $roleClass = 'bg-primary/20 text-primary dark:bg-primary/30 dark:text-primary';
        } elseif ($user->is_sitter) {
            $roleLabel = __('messages.um_role_sitter');
            $roleClass = 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400';
        } else {
            $roleLabel = __('messages.um_role_owner');
            $roleClass = 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400';
        }

        $verStatus = $user->id_validation_status ?? 'pending';
        $verMap = [
            'verified' => ['bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400', __('messages.um_verified')],
            'pending'  => ['bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400', __('messages.um_pending')],
            'rejected' => ['bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400', __('messages.um_rejected')],
        ];
        [$verClass, $verLabel] = $verMap[$verStatus] ?? $verMap['pending'];

        $accStatus = $user->status ?? 'active';
        $accMap = [
            'active'    => ['bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400', __('messages.um_status_active')],
            'suspended' => ['bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400', __('messages.um_status_suspended')],
            'banned'    => ['bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400', __('messages.um_status_banned')],
        ];
        [$accClass, $accLabel] = $accMap[$accStatus] ?? $accMap['active'];
    @endphp

    <div class="py-4 sm:py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200"
         x-data="{ suspendModal: false, banModal: false }">

        <div class="w-full sm:max-w-5xl mx-auto px-2 sm:px-16 lg:px-24">

            @if(session('status'))
                <div class="mb-4 p-4 rounded-xl bg-green-50 dark:bg-green-950/20 border border-green-200 dark:border-green-800">
                    <p class="text-sm text-green-700 dark:text-green-400 font-bold">✓ {{ session('status') }}</p>
                </div>
            @endif

            {{-- PROFILE CARD --}}
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 overflow-hidden mb-4 sm:mb-6">

                <div class="px-4 sm:px-6 pt-6 pb-4">
                    <div class="flex flex-col sm:flex-row sm:items-start gap-4 sm:gap-6">

                        {{-- Avatar --}}
                        <div class="shrink-0">
                            @if($user->profile_photo && file_exists(public_path('storage/' . $user->profile_photo)))
                                <img src="{{ asset('storage/' . $user->profile_photo) }}"
                                     alt="{{ $fullName }}"
                                     class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl object-cover border-2 border-primary/20">
                            @else
                                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-primary/10 flex items-center justify-center text-primary font-black text-2xl sm:text-3xl">
                                    {{ $initials }}
                                </div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-3 flex-wrap">
                                <div class="min-w-0">
                                    <h2 class="text-xl sm:text-2xl font-black text-[#1B3B36] dark:text-white truncate">
                                        {{ $fullName }}
                                    </h2>
                                    <p class="text-sm text-neutral-500 dark:text-neutral-400 break-all mt-0.5">
                                        {{ $user->email }}
                                    </p>
                                </div>

                                <div class="flex flex-wrap items-center gap-1.5">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold {{ $roleClass }}">
                                        {{ $roleLabel }}
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold {{ $verClass }}">
                                        {{ $verLabel }}
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold {{ $accClass }}">
                                        {{ $accLabel }}
                                    </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4">
                                <div>
                                    <p class="text-[10px] text-neutral-400 uppercase tracking-wider font-bold">{{ __('messages.ud_user_id') }}</p>
                                    <p class="text-sm font-bold text-[#1B3B36] dark:text-white">#{{ $user->id }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-neutral-400 uppercase tracking-wider font-bold">{{ __('messages.ud_joined') }}</p>
                                    <p class="text-sm font-bold text-[#1B3B36] dark:text-white">
                                        {{ $user->created_at?->format('M j, Y') }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-neutral-400 uppercase tracking-wider font-bold">{{ __('messages.ud_sitter_level') }}</p>
                                    <p class="text-sm font-bold text-[#1B3B36] dark:text-white">
                                        {{ $user->sitter_level ? 'Level ' . $user->sitter_level : __('messages.ud_na') }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-neutral-400 uppercase tracking-wider font-bold">{{ __('messages.ud_location') }}</p>
                                    <p class="text-sm font-bold text-[#1B3B36] dark:text-white truncate">
                                        {{ $user->location ?? __('messages.ud_not_set') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-wrap items-center gap-2 mt-5 pt-5 border-t border-gray-100 dark:border-neutral-800">

                        @if($accStatus === 'active')
                            <button type="button"
                                    @click="suspendModal = true"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded-xl transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                                {{ __('messages.ud_suspend_btn') }}
                            </button>

                            <button type="button"
                                    @click="banModal = true"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-bold text-xs rounded-xl transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-5.657-1.414a1 1 0 011.414 1.414"/>
                                </svg>
                                {{ __('messages.ud_ban_btn') }}
                            </button>
                        @else
                            <form method="POST" action="{{ route('admin.users.restore', $user->id) }}" class="inline">
                                @csrf
                                <button type="submit"
                                        onclick="return confirm('{{ __('messages.ud_confirm_restore', ['name' => addslashes($fullName)]) }}')"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-green-500 hover:bg-green-600 text-white font-bold text-xs rounded-xl transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    {{ __('messages.ud_restore_btn') }}
                                </button>
                            </form>
                        @endif

                    </div>
                </div>
            </div>

            {{-- STATS CARDS --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-6">

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                    <p class="text-[10px] text-neutral-500 dark:text-neutral-400 uppercase tracking-wider font-bold">{{ __('messages.ud_stat_complaints_filed') }}</p>
                    <h3 class="font-black text-xl text-[#1B3B36] dark:text-white mt-1">{{ $complaintsFiled }}</h3>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                    <p class="text-[10px] text-neutral-500 dark:text-neutral-400 uppercase tracking-wider font-bold">{{ __('messages.ud_stat_complaints_against') }}</p>
                    <h3 class="font-black text-xl {{ $complaintsAgainst > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }} mt-1">
                        {{ $complaintsAgainst }}
                    </h3>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                    <p class="text-[10px] text-neutral-500 dark:text-neutral-400 uppercase tracking-wider font-bold">{{ __('messages.ud_stat_pets') }}</p>
                    <h3 class="font-black text-xl text-[#1B3B36] dark:text-white mt-1">
                        {{ $user->pets->count() }}
                    </h3>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                    <p class="text-[10px] text-neutral-500 dark:text-neutral-400 uppercase tracking-wider font-bold">{{ __('messages.ud_stat_role') }}</p>
                    <h3 class="font-black text-xl text-[#1B3B36] dark:text-white mt-1">{{ $roleLabel }}</h3>
                </div>

            </div>

            {{-- PROFILE DETAILS --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 mb-4 sm:mb-6">

                <div class="lg:col-span-2 bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6">
                    <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white mb-4">{{ __('messages.ud_personal_info') }}</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-[10px] text-neutral-400 uppercase tracking-wider font-bold">{{ __('messages.ud_full_name') }}</p>
                            <p class="text-sm font-bold text-[#1B3B36] dark:text-white mt-1">{{ $fullName }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-neutral-400 uppercase tracking-wider font-bold">{{ __('messages.ud_email') }}</p>
                            <p class="text-sm font-bold text-[#1B3B36] dark:text-white mt-1 break-all">{{ $user->email }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-neutral-400 uppercase tracking-wider font-bold">{{ __('messages.ud_phone') }}</p>
                            <p class="text-sm font-bold text-[#1B3B36] dark:text-white mt-1">{{ $user->contact_number ?? __('messages.ud_not_set') }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-neutral-400 uppercase tracking-wider font-bold">{{ __('messages.ud_gender') }}</p>
                            <p class="text-sm font-bold text-[#1B3B36] dark:text-white mt-1 capitalize">{{ $user->gender ?? __('messages.ud_not_set') }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-neutral-400 uppercase tracking-wider font-bold">{{ __('messages.ud_dob') }}</p>
                            <p class="text-sm font-bold text-[#1B3B36] dark:text-white mt-1">
                                {{ $user->date_of_birth?->format('M j, Y') ?? __('messages.ud_not_set') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-[10px] text-neutral-400 uppercase tracking-wider font-bold">{{ __('messages.ud_address') }}</p>
                            <p class="text-sm font-bold text-[#1B3B36] dark:text-white mt-1">{{ $user->address ?? __('messages.ud_not_set') }}</p>
                        </div>
                        <div class="sm:col-span-2">
                            <p class="text-[10px] text-neutral-400 uppercase tracking-wider font-bold">{{ __('messages.ud_location') }}</p>
                            <p class="text-sm font-bold text-[#1B3B36] dark:text-white mt-1">{{ $user->location ?? __('messages.ud_not_set') }}</p>
                        </div>
                    </div>

                    @if($user->bio)
                        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                            <p class="text-[10px] text-neutral-400 uppercase tracking-wider font-bold mb-1">{{ __('messages.ud_bio') }}</p>
                            <p class="text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">{{ $user->bio }}</p>
                        </div>
                    @endif
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6">
                    <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white mb-4">{{ __('messages.ud_sitter_profile') }}</h3>

                    @if($user->sitterProfile)
                        <div class="space-y-3">
                            <div>
                                <p class="text-[10px] text-neutral-400 uppercase tracking-wider font-bold">{{ __('messages.ud_base_rate') }}</p>
                                <p class="text-sm font-bold text-primary mt-0.5">
                                    ₱{{ number_format($user->sitterProfile->base_rate ?? 0, 2) }}
                                </p>
                            </div>
                            <div>
                                <p class="text-[10px] text-neutral-400 uppercase tracking-wider font-bold">{{ __('messages.ud_experience') }}</p>
                                <p class="text-sm font-bold text-[#1B3B36] dark:text-white mt-0.5">
                                    {{ $user->sitterProfile->experience_years ?? 0 }} {{ __('messages.ud_years') }}
                                </p>
                            </div>
                            <div>
                                <p class="text-[10px] text-neutral-400 uppercase tracking-wider font-bold">{{ __('messages.ud_food_preference') }}</p>
                                <p class="text-sm font-bold text-[#1B3B36] dark:text-white mt-0.5 capitalize">
                                    {{ str_replace('_', ' ', $user->sitterProfile->food_preference ?? __('messages.ud_not_set')) }}
                                </p>
                            </div>
                            <div>
                                <p class="text-[10px] text-neutral-400 uppercase tracking-wider font-bold">{{ __('messages.ud_rating') }}</p>
                                <p class="text-sm font-bold text-amber-600 dark:text-amber-400 mt-0.5">
                                    ⭐ {{ number_format($user->sitterProfile->average_ratings ?? 0, 1) }}
                                </p>
                            </div>
                        </div>
                    @else
                        <p class="text-xs text-neutral-400 italic">{{ __('messages.ud_no_sitter_profile') }}</p>
                    @endif
                </div>

            </div>

            {{-- PETS LIST --}}
            @if($user->pets->count() > 0)
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6 mb-4 sm:mb-6">
                    <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white mb-4">
                        {{ __('messages.ud_pets_count', ['count' => $user->pets->count()]) }}
                    </h3>

                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                        @foreach($user->pets as $pet)
                            @php
                                $petEmoji = match($pet->petType?->name ?? '') {
                                    'Dog' => '🐶', 'Cat' => '🐱', 'Bird' => '🐦',
                                    'Rabbit' => '🐰', 'Hamster' => '🐹', 'Fish' => '🐟',
                                    'Reptile' => '🦎', default => '🐾',
                                };
                            @endphp
                            <div class="flex items-center gap-2.5 p-2.5 bg-neutral-50 dark:bg-neutral-800/50 rounded-xl border border-gray-100 dark:border-neutral-700">
                                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-xl overflow-hidden shrink-0">
                                    @if($pet->photo_path && file_exists(public_path('storage/' . $pet->photo_path)))
                                        <img src="{{ asset('storage/' . $pet->photo_path) }}"
                                             alt="{{ $pet->name }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        {{ $petEmoji }}
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-[#1B3B36] dark:text-white truncate">{{ $pet->name }}</p>
                                    <p class="text-[10px] text-neutral-500 truncate">
                                        {{ $pet->petType?->name ?? __('messages.ud_unknown_type') }} • {{ $pet->age }} {{ __('messages.ud_yrs') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- BACK BUTTON --}}
            <div class="pt-4 border-t border-gray-100 dark:border-neutral-800">
                <a href="javascript:history.back()"
                class="inline-flex items-center justify-center px-6 py-3 border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-sm rounded-xl hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                    {{ __('messages.ud_back_btn') }}
                </a>
            </div>

        </div>

        {{-- SUSPEND MODAL --}}
        <div x-show="suspendModal" x-cloak x-transition.opacity
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
             @click.away="suspendModal = false">

            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-neutral-800 max-w-md w-full p-5 sm:p-6"
                 @click.stop>

                <div class="flex items-start gap-3 mb-4">
                    <div class="w-11 h-11 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-black text-[#1B3B36] dark:text-white">{{ __('messages.um_suspend_title') }}</h3>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">{{ $fullName }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.users.suspend', $user->id) }}">
                    @csrf

                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1.5">
                        {{ __('messages.um_suspend_reason') }}
                    </label>
                    <textarea name="reason" rows="3" required
                              placeholder="{{ __('messages.um_suspend_ph') }}"
                              class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-3 py-2.5 text-[#1B3B36] dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-sm resize-none"></textarea>

                    <div class="flex gap-2 mt-4">
                        <button type="button" @click="suspendModal = false"
                                class="flex-1 px-4 py-2.5 rounded-xl border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-xs hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                            {{ __('messages.um_cancel') }}
                        </button>
                        <button type="submit"
                                class="flex-1 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs transition shadow-md hover:shadow-lg">
                            {{ __('messages.um_yes_suspend') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- BAN MODAL --}}
        <div x-show="banModal" x-cloak x-transition.opacity
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
             @click.away="banModal = false">

            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-neutral-800 max-w-md w-full p-5 sm:p-6"
                 @click.stop>

                <div class="flex items-start gap-3 mb-4">
                    <div class="w-11 h-11 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center text-red-600 dark:text-red-400 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-black text-[#1B3B36] dark:text-white">{{ __('messages.um_ban_title') }}</h3>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">{{ $fullName }}</p>
                    </div>
                </div>

                <div class="p-3 mb-4 rounded-xl bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-800">
                    <p class="text-[11px] text-red-700 dark:text-red-400 font-bold">
                        ⚠️ {{ __('messages.um_ban_warning') }}
                    </p>
                </div>

                <form method="POST" action="{{ route('admin.users.ban', $user->id) }}">
                    @csrf

                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1.5">
                        {{ __('messages.um_ban_reason') }}
                    </label>
                    <textarea name="reason" rows="3" required
                              placeholder="{{ __('messages.um_ban_ph') }}"
                              class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-3 py-2.5 text-[#1B3B36] dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 text-sm resize-none"></textarea>

                    <div class="flex gap-2 mt-4">
                        <button type="button" @click="banModal = false"
                                class="flex-1 px-4 py-2.5 rounded-xl border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-xs hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                            {{ __('messages.um_cancel') }}
                        </button>
                        <button type="submit"
                                class="flex-1 px-4 py-2.5 rounded-xl bg-red-500 hover:bg-red-600 text-white font-bold text-xs transition shadow-md hover:shadow-lg">
                            {{ __('messages.um_yes_ban') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>