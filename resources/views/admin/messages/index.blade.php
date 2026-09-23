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
                        {{ __('messages.inbox_title') }}
                    </h1>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.inbox_subtitle') }}</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200">
        <div class="w-full sm:max-w-7xl mx-auto px-2 sm:px-16 lg:px-24">

            @if (session('success'))
                <div class="mb-4 rounded-2xl border border-green-200 dark:border-green-800/50 bg-green-50 dark:bg-green-950/20 text-green-800 dark:text-green-300 px-4 py-3 text-sm font-medium flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            {{-- Stats --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4 mb-4 sm:mb-6">
                @foreach ([
                    ['label' => __('messages.inbox_stat_total'),       'value' => $stats['total'],       'color' => 'text-[#1B3B36] dark:text-white'],
                    ['label' => __('messages.inbox_stat_open'),        'value' => $stats['open'],        'color' => 'text-red-600 dark:text-red-400'],
                    ['label' => __('messages.inbox_stat_in_progress'), 'value' => $stats['in_progress'], 'color' => 'text-amber-600 dark:text-amber-400'],
                    ['label' => __('messages.inbox_stat_resolved'),    'value' => $stats['resolved'],    'color' => 'text-green-600 dark:text-green-400'],
                    ['label' => __('messages.inbox_stat_contacts'),    'value' => $stats['contact'],     'color' => 'text-blue-600 dark:text-blue-400'],
                    ['label' => __('messages.inbox_stat_reports'),     'value' => $stats['report'],      'color' => 'text-purple-600 dark:text-purple-400'],
                ] as $s)
                    <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-3.5 sm:p-4">
                        <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ $s['label'] }}</p>
                        <h3 class="font-black text-lg sm:text-2xl {{ $s['color'] }} mt-1">{{ $s['value'] }}</h3>
                    </div>
                @endforeach
            </div>

            {{-- Filters --}}
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 mb-4 sm:mb-6">
                <form method="GET" class="flex flex-col lg:flex-row gap-3">
                    <div class="flex-1 relative">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" name="search" value="{{ $search }}"
                               placeholder="{{ __('messages.inbox_search_ph') }}"
                               class="w-full pl-9 pr-4 py-2 text-sm rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary transition">
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <select name="status" class="rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 px-3 py-2 text-sm font-medium text-[#1B3B36] dark:text-white">
                            <option value="all"         @selected($status === 'all')>{{ __('messages.inbox_all_status') }}</option>
                            <option value="open"        @selected($status === 'open')>{{ __('messages.inbox_stat_open') }}</option>
                            <option value="in_progress" @selected($status === 'in_progress')>{{ __('messages.inbox_stat_in_progress') }}</option>
                            <option value="resolved"    @selected($status === 'resolved')>{{ __('messages.inbox_stat_resolved') }}</option>
                        </select>
                        <select name="type" class="rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 px-3 py-2 text-sm font-medium text-[#1B3B36] dark:text-white">
                            <option value="all"     @selected($type === 'all')>{{ __('messages.inbox_all_types') }}</option>
                            <option value="contact" @selected($type === 'contact')>{{ __('messages.inbox_type_contact') }}</option>
                            <option value="report"  @selected($type === 'report')>{{ __('messages.inbox_type_report') }}</option>
                        </select>
                        <button type="submit"
                                class="inline-flex items-center justify-center px-4 py-2 bg-primary hover:bg-primary-600 text-white font-bold text-xs rounded-xl transition">
                            {{ __('messages.inbox_filter') }}
                        </button>
                        <a href="{{ route('admin.messages.index') }}" class="text-xs font-bold text-neutral-500 hover:text-primary hover:underline">
                            {{ __('messages.inbox_reset') }}
                        </a>
                    </div>
                </form>
            </div>

            {{-- Messages table --}}
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs sm:text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-800/30">
                                <th class="text-left py-3 px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400">{{ __('messages.inbox_col_code') }}</th>
                                <th class="text-left py-3 px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400">{{ __('messages.inbox_col_from') }}</th>
                                <th class="text-left py-3 px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400">{{ __('messages.inbox_col_type') }}</th>
                                <th class="text-left py-3 px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400">{{ __('messages.inbox_col_subject') }}</th>
                                <th class="text-left py-3 px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400">{{ __('messages.inbox_col_status') }}</th>
                                <th class="text-left py-3 px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400">{{ __('messages.inbox_col_date') }}</th>
                                <th class="text-right py-3 px-4 text-[10px] sm:text-xs font-bold uppercase text-neutral-400">{{ __('messages.inbox_col_action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($messages as $m)
                                @php
                                    $statusClass = match($m->status) {
                                        'open'        => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                        'in_progress' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                        'resolved'    => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                        default       => 'bg-neutral-100',
                                    };
                                    $typeClass = $m->type === 'report'
                                        ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400'
                                        : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400';
                                    $statusLabel = match($m->status) {
                                        'open'        => __('messages.inbox_stat_open'),
                                        'in_progress' => __('messages.inbox_stat_in_progress'),
                                        'resolved'    => __('messages.inbox_stat_resolved'),
                                        default       => ucfirst(str_replace('_', ' ', $m->status)),
                                    };
                                @endphp
                                <tr class="border-b border-gray-100 dark:border-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition">
                                    <td class="py-3 px-4 font-mono text-[10px] font-bold text-neutral-400 whitespace-nowrap">{{ $m->message_code }}</td>
                                    <td class="py-3 px-4">
                                        <p class="font-bold text-[#1B3B36] dark:text-white truncate max-w-[150px]">
                                            {{ trim(($m->user->f_name ?? '') . ' ' . ($m->user->l_name ?? '')) ?: 'User' }}
                                        </p>
                                        <p class="text-[10px] text-neutral-400 truncate max-w-[150px]">{{ $m->user->email }}</p>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $typeClass }}">
                                            {{ $m->type === 'report' ? __('messages.inbox_label_report') : __('messages.inbox_label_contact') }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-neutral-600 dark:text-neutral-300 truncate max-w-xs">{{ $m->subject }}</td>
                                    <td class="py-3 px-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $statusClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-neutral-500 whitespace-nowrap">{{ $m->created_at->format('M d, Y') }}</td>
                                    <td class="py-3 px-4 text-right">
                                        <a href="{{ route('admin.messages.show', $m->id) }}"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-primary/10 text-primary font-bold text-xs rounded-lg hover:bg-primary/20 transition whitespace-nowrap">
                                            {{ __('messages.inbox_view') }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-neutral-500 dark:text-neutral-400 text-sm">
                                        {{ __('messages.inbox_no_messages') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($messages->hasPages())
                    <div class="px-4 py-3 border-t border-gray-100 dark:border-neutral-800">
                        {{ $messages->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>