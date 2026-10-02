<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between w-full gap-3">
            <div class="flex items-center gap-3">
                <a href="javascript:history.back()"
                   class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div class="min-w-0">
                    <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                        {{ __('messages.um_title') }}
                    </h1>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.um_subtitle') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:flex sm:items-center gap-2">
                <button type="button"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-primary hover:bg-primary-600 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ __('messages.um_add_admin') }}
                </button>
                <a href="{{ route('admin.users.export', request()->query()) }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-sm rounded-xl hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    {{ __('messages.um_export_users') }}
                </a>
            </div>
        </div>
    </x-slot>

     <div class="py-4 sm:py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200"
            x-data="{ suspendModal: false, banModal: false, deleteModal: false, promoteModal: false, demoteModal: false, actionUserId: null, actionUserName: '', actionUrl: '' }">
        <div class="w-full sm:max-w-7xl mx-auto px-2 sm:px-16 lg:px-24">

            @if(session('status'))
                <div class="mb-4 p-4 rounded-xl bg-green-50 dark:bg-green-950/20 border border-green-200 dark:border-green-800">
                    <p class="text-sm text-green-700 dark:text-green-400 font-bold">✓ {{ session('status') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 rounded-xl bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-800">
                    <p class="text-sm text-red-700 dark:text-red-400 font-bold">⚠️ {{ session('error') }}</p>
                </div>
            @endif

            <!-- ① SUMMARY CARDS -->
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-6">

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.um_stat_total') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-[#1B3B36] dark:text-white mt-1">{{ $stats['total'] }}</h3>
                            <p class="text-[10px] sm:text-xs text-green-600 dark:text-green-400 mt-1">{{ __('messages.um_label_all_accounts') }}</p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-primary/10 flex items-center justify-center text-primary shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.um_stat_owners') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-[#1B3B36] dark:text-white mt-1">{{ $stats['owners'] }}</h3>
                            <p class="text-[10px] sm:text-xs text-green-600 dark:text-green-400 mt-1">{{ __('messages.um_label_registered') }}</p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-blue-100/20 flex items-center justify-center text-blue-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.um_stat_sitters') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-[#1B3B36] dark:text-white mt-1">{{ $stats['sitters'] }}</h3>
                            <p class="text-[10px] sm:text-xs text-green-600 dark:text-green-400 mt-1">{{ __('messages.um_label_applicants') }}</p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-amber-100/20 flex items-center justify-center text-amber-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ __('messages.um_stat_restricted') }}</p>
                            <h3 class="font-black text-lg sm:text-2xl text-red-600 dark:text-red-400 mt-1">{{ $stats['suspended'] + $stats['banned'] }}</h3>
                            <p class="text-[10px] sm:text-xs text-red-500 dark:text-red-400 mt-1">⚠️ {{ __('messages.um_label_suspended_banned') }}</p>
                        </div>
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-red-100/20 flex items-center justify-center text-red-600 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ② SEARCH + FILTERS -->
            <form method="GET" action="{{ route('admin.users') }}"
                  class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-5 mb-4 sm:mb-6">
                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                    <div class="flex-1 relative">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text"
                               name="search"
                               value="{{ $search }}"
                               placeholder="{{ __('messages.um_search_ph') }}"
                               class="w-full pl-9 pr-4 py-2 sm:py-2.5 text-xs sm:text-sm rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary transition">
                    </div>

                    <div class="grid grid-cols-2 sm:flex sm:flex-wrap items-center gap-2">
                        <select name="role"
                                class="px-2.5 py-2 sm:px-3 sm:py-2.5 text-xs sm:text-sm rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary transition">
                            <option value="all"     @selected($roleFilter === 'all')>{{ __('messages.um_all_roles') }}</option>
                            <option value="admin"   @selected($roleFilter === 'admin')>{{ __('messages.um_role_admin') }}</option>
                            <option value="owner"   @selected($roleFilter === 'owner')>{{ __('messages.um_role_owner') }}</option>
                            <option value="sitter"  @selected($roleFilter === 'sitter')>{{ __('messages.um_role_sitter') }}</option>
                        </select>

                        <select name="status"
                                class="px-2.5 py-2 sm:px-3 sm:py-2.5 text-xs sm:text-sm rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary transition">
                            <option value="all"          @selected($statusFilter === 'all')>{{ __('messages.um_all_status') }}</option>
                            <option value="active"       @selected($statusFilter === 'active')>{{ __('messages.um_status_active') }}</option>
                            <option value="suspended"    @selected($statusFilter === 'suspended')>{{ __('messages.um_status_suspended') }}</option>
                            <option value="banned"       @selected($statusFilter === 'banned')>{{ __('messages.um_status_banned') }}</option>
                        </select>

                        <select name="verification"
                                class="px-2.5 py-2 sm:px-3 sm:py-2.5 text-xs sm:text-sm rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary transition">
                            <option value="all"       @selected($verificationFilter === 'all')>{{ __('messages.um_all_verify') }}</option>
                            <option value="verified"  @selected($verificationFilter === 'verified')>{{ __('messages.um_verified') }}</option>
                            <option value="pending"   @selected($verificationFilter === 'pending')>{{ __('messages.um_pending') }}</option>
                            <option value="rejected"  @selected($verificationFilter === 'rejected')>{{ __('messages.um_rejected') }}</option>
                        </select>

                        <select name="sort"
                                class="px-2.5 py-2 sm:px-3 sm:py-2.5 text-xs sm:text-sm rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary transition">
                            <option value="newest"     @selected($sortFilter === 'newest')>{{ __('messages.um_sort_newest') }}</option>
                            <option value="oldest"     @selected($sortFilter === 'oldest')>{{ __('messages.um_sort_oldest') }}</option>
                            <option value="name_asc"   @selected($sortFilter === 'name_asc')>{{ __('messages.um_sort_name_asc') }}</option>
                            <option value="name_desc"  @selected($sortFilter === 'name_desc')>{{ __('messages.um_sort_name_desc') }}</option>
                        </select>

                        <button type="submit"
                                class="px-4 py-2 sm:py-2.5 bg-primary hover:bg-primary-600 text-white font-bold text-xs sm:text-sm rounded-xl transition col-span-2 sm:col-span-1">
                            {{ __('messages.um_apply') }}
                        </button>
                    </div>
                </div>
            </form>

            <!-- ③ USER TABLE -->
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs sm:text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-800/30">
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">{{ __('messages.um_col_user') }}</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">{{ __('messages.um_col_email') }}</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">{{ __('messages.um_col_role') }}</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">{{ __('messages.um_col_verification') }}</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">{{ __('messages.um_col_status') }}</th>
                                <th class="text-left py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">{{ __('messages.um_col_joined') }}</th>
                                <th class="text-right py-2.5 sm:py-3 px-2 sm:px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400 whitespace-nowrap">{{ __('messages.um_col_actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
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

                                    $verificationStatus = $user->id_validation_status ?? 'pending';
                                    $verificationMap = [
                                        'verified' => ['bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400', __('messages.um_verified')],
                                        'pending'  => ['bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400', __('messages.um_pending')],
                                        'rejected' => ['bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400', __('messages.um_rejected')],
                                    ];
                                    [$verClass, $verLabel] = $verificationMap[$verificationStatus] ?? $verificationMap['pending'];

                                    $accountStatus = $user->status ?? 'active';
                                    $statusMap = [
                                        'active'    => ['bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400', __('messages.um_status_active')],
                                        'suspended' => ['bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400', __('messages.um_status_suspended')],
                                        'banned'    => ['bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400', __('messages.um_status_banned')],
                                    ];
                                    [$statusClass, $statusLabel] = $statusMap[$accountStatus] ?? $statusMap['active'];

                                    $avatarColor = match(true) {
                                        $user->isAdmin() => 'bg-primary/10 text-primary',
                                        $user->is_sitter => 'bg-amber-100/20 text-amber-600',
                                        $accountStatus === 'banned' => 'bg-red-100/20 text-red-600',
                                        $accountStatus === 'suspended' => 'bg-red-100/20 text-red-600',
                                        default => 'bg-primary/10 text-primary',
                                    };
                                @endphp

                                <tr class="border-b border-gray-100 dark:border-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition">
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                        <div class="flex items-center gap-2 sm:gap-3">
                                            @if($user->profile_photo && file_exists(public_path('storage/' . $user->profile_photo)))
                                                <img src="{{ asset('storage/' . $user->profile_photo) }}"
                                                     alt="{{ $fullName }}"
                                                     class="w-8 h-8 sm:w-9 sm:h-9 rounded-full object-cover border border-primary/20 shrink-0">
                                            @else
                                                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full {{ $avatarColor }} flex items-center justify-center font-bold text-xs sm:text-sm shrink-0">
                                                    {{ $initials }}
                                                </div>
                                            @endif
                                            <div class="min-w-0">
                                                <p class="font-bold text-[#1B3B36] dark:text-white truncate">{{ $fullName }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4 text-neutral-600 dark:text-neutral-300 truncate">{{ $user->email }}</td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                        <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full text-[10px] font-bold {{ $roleClass }} whitespace-nowrap">
                                            {{ $roleLabel }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                        <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full text-[10px] font-bold {{ $verClass }} whitespace-nowrap">
                                            {{ $verLabel }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4">
                                        <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full text-[10px] font-bold {{ $statusClass }} whitespace-nowrap">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4 text-neutral-500 dark:text-neutral-400 whitespace-nowrap">
                                        {{ $user->created_at->format('M j, Y') }}
                                    </td>
                                    <td class="py-2.5 sm:py-3 px-2 sm:px-4 text-right">
                                        <div class="flex items-center justify-end gap-1 sm:gap-1.5">

                                            <a href="{{ route('admin.users.show', $user->id) }}"
                                               class="p-1.5 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition text-primary"
                                               title="{{ __('messages.um_tooltip_view') }}">
                                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>

                                            @if($accountStatus === 'active')
                                                <button type="button"
                                                        @click="suspendModal = true; actionUserId = {{ $user->id }}; actionUserName = '{{ addslashes($fullName) }}'; actionUrl = '{{ route('admin.users.suspend', $user->id) }}'"
                                                        class="p-1.5 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition text-amber-600"
                                                        title="{{ __('messages.um_tooltip_suspend') }}">
                                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                    </svg>
                                                </button>

                                                <button type="button"
                                                        @click="banModal = true; actionUserId = {{ $user->id }}; actionUserName = '{{ addslashes($fullName) }}'; actionUrl = '{{ route('admin.users.ban', $user->id) }}'"
                                                        class="p-1.5 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition text-red-600"
                                                        title="{{ __('messages.um_tooltip_ban') }}">
                                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-5.657-1.414a1 1 0 011.414 1.414"/>
                                                    </svg>
                                                </button>
                                            @else
                                                <form method="POST" action="{{ route('admin.users.restore', $user->id) }}" class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                            onclick="return confirm('{{ __('messages.um_confirm_restore', ['name' => addslashes($fullName)]) }}')"
                                                            class="p-1.5 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition text-green-600"
                                                            title="{{ __('messages.um_tooltip_restore') }}">
                                                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif

                                            {{-- DELETE BUTTON (BAG-O) --}}
                                            @if(! $user->isAdmin() && $user->id !== auth()->id())
                                                <button type="button"
                                                        @click="deleteModal = true; actionUserId = {{ $user->id }}; actionUserName = '{{ addslashes($fullName) }}'; actionUrl = '{{ route('admin.users.destroy', $user->id) }}'"
                                                        class="p-1.5 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition text-red-700"
                                                        title="{{ __('messages.um_tooltip_delete') }}">
                                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            @endif

                                            {{-- PROMOTE TO ADMIN --}}
                                            @if(! $user->isAdmin() && $user->status === 'active' && $user->id !== auth()->id())
                                                <button type="button"
                                                        @click="promoteModal = true; actionUserId = {{ $user->id }}; actionUserName = '{{ addslashes($fullName) }}'; actionUrl = '{{ route('admin.users.promote', $user->id) }}'"
                                                        class="p-1.5 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition text-purple-600"
                                                        title="{{ __('messages.um_tooltip_promote') }}">
                                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                                    </svg>
                                                </button>
                                            @endif

                                            {{-- DEMOTE FROM ADMIN --}}
                                            @if($user->isAdmin() && $user->id !== auth()->id() && \App\Models\User::where('role', 'admin')->count() > 1)
                                                <button type="button"
                                                        @click="demoteModal = true; actionUserId = {{ $user->id }}; actionUserName = '{{ addslashes($fullName) }}'; actionUrl = '{{ route('admin.users.demote', $user->id) }}'"
                                                        class="p-1.5 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition text-orange-600"
                                                        title="{{ __('messages.um_tooltip_demote') }}">
                                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                                    </svg>
                                                </button>
                                            @endif

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-sm text-neutral-500 dark:text-neutral-400">
                                        <div class="w-16 h-16 mx-auto rounded-full bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center text-neutral-400 mb-4">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-xl font-black text-[#1B3B36] dark:text-white">{{ __('messages.um_no_users') }}</h3>
                                        <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.um_no_users_desc') }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-3 sm:px-4 py-3 border-t border-gray-100 dark:border-neutral-800">
                        <p class="text-xs sm:text-sm text-neutral-500 dark:text-neutral-400">
                            {{ __('messages.um_showing', [
                                'from'  => $users->firstItem(),
                                'to'    => $users->lastItem(),
                                'total' => $users->total(),
                            ]) }}
                        </p>
                        <div class="flex items-center gap-1 overflow-x-auto">
                            {{ $users->links() }}
                        </div>
                    </div>
                @endif
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
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">
                            <strong x-text="actionUserName"></strong>
                        </p>
                    </div>
                </div>

                <form method="POST" :action="actionUrl">
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
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">
                            <strong x-text="actionUserName"></strong>
                        </p>
                    </div>
                </div>

                <div class="p-3 mb-4 rounded-xl bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-800">
                    <p class="text-[11px] text-red-700 dark:text-red-400 font-bold">
                        ⚠️ {{ __('messages.um_ban_warning') }}
                    </p>
                </div>

                <form method="POST" :action="actionUrl">
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

        {{-- DELETE MODAL (BAG-O) --}}
        <div x-show="deleteModal" x-cloak x-transition.opacity
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
             @click.away="deleteModal = false">

            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-neutral-800 max-w-md w-full p-5 sm:p-6"
                 @click.stop>

                <div class="flex items-start gap-3 mb-4">
                    <div class="w-11 h-11 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center text-red-700 dark:text-red-400 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-black text-[#1B3B36] dark:text-white">{{ __('messages.um_delete_title') }}</h3>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">
                            <strong x-text="actionUserName"></strong>
                        </p>
                    </div>
                </div>

                <div class="p-3 mb-4 rounded-xl bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-800">
                    <p class="text-[11px] text-red-700 dark:text-red-400 font-bold">
                        ⚠️ {{ __('messages.um_delete_warning') }}
                    </p>
                </div>

                <form method="POST" :action="actionUrl"
                      @submit="if (!confirm('{{ __('messages.um_delete_confirm_js') }}')) $event.preventDefault()">
                    @csrf
                    @method('DELETE')

                    <div class="flex gap-2 mt-4">
                        <button type="button" @click="deleteModal = false"
                                class="flex-1 px-4 py-2.5 rounded-xl border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-xs hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                            {{ __('messages.um_cancel') }}
                        </button>
                        <button type="submit"
                                class="flex-1 px-4 py-2.5 rounded-xl bg-red-700 hover:bg-red-800 text-white font-bold text-xs transition shadow-md hover:shadow-lg">
                            {{ __('messages.um_yes_delete') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- PROMOTE MODAL --}}
        <div x-show="promoteModal" x-cloak x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
            @click.away="promoteModal = false">

            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-neutral-800 max-w-md w-full p-5 sm:p-6"
                @click.stop>

                <div class="flex items-start gap-3 mb-4">
                    <div class="w-11 h-11 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-black text-[#1B3B36] dark:text-white">{{ __('messages.um_promote_title') }}</h3>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">
                            <strong x-text="actionUserName"></strong>
                        </p>
                    </div>
                </div>

                <div class="p-3 mb-4 rounded-xl bg-purple-50 dark:bg-purple-950/20 border border-purple-200 dark:border-purple-800">
                    <p class="text-[11px] text-purple-700 dark:text-purple-400 font-bold">
                        ⚠️ {{ __('messages.um_promote_warning') }}
                    </p>
                </div>

                <form method="POST" :action="actionUrl">
                    @csrf

                    <div class="flex gap-2 mt-4">
                        <button type="button" @click="promoteModal = false"
                                class="flex-1 px-4 py-2.5 rounded-xl border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-xs hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                            {{ __('messages.um_cancel') }}
                        </button>
                        <button type="submit"
                                class="flex-1 px-4 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs transition shadow-md hover:shadow-lg">
                            {{ __('messages.um_yes_promote') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- DEMOTE MODAL --}}
        <div x-show="demoteModal" x-cloak x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
            @click.away="demoteModal = false">

            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-neutral-800 max-w-md w-full p-5 sm:p-6"
                @click.stop>

                <div class="flex items-start gap-3 mb-4">
                    <div class="w-11 h-11 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center text-orange-600 dark:text-orange-400 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-black text-[#1B3B36] dark:text-white">{{ __('messages.um_demote_title') }}</h3>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">
                            <strong x-text="actionUserName"></strong>
                        </p>
                    </div>
                </div>

                <div class="p-3 mb-4 rounded-xl bg-orange-50 dark:bg-orange-950/20 border border-orange-200 dark:border-orange-800">
                    <p class="text-[11px] text-orange-700 dark:text-orange-400 font-bold">
                        ⚠️ {{ __('messages.um_demote_warning') }}
                    </p>
                </div>

                <form method="POST" :action="actionUrl">
                    @csrf

                    <div class="flex gap-2 mt-4">
                        <button type="button" @click="demoteModal = false"
                                class="flex-1 px-4 py-2.5 rounded-xl border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-xs hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                            {{ __('messages.um_cancel') }}
                        </button>
                        <button type="submit"
                                class="flex-1 px-4 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs transition shadow-md hover:shadow-lg">
                            {{ __('messages.um_yes_demote') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
