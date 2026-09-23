@php
    /** @var \App\Models\UserSupportMessage $message */
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.messages.index') }}"
               class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div class="min-w-0">
                <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                    {{ __('messages.msg_title') }}
                </h1>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">{{ $message->message_code }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200">
        <div class="w-full sm:max-w-4xl mx-auto px-2 sm:px-8">

            @if (session('success'))
                <div class="mb-4 rounded-2xl border border-green-200 dark:border-green-800/50 bg-green-50 dark:bg-green-950/20 text-green-800 dark:text-green-300 px-4 py-3 text-sm font-medium flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            {{-- Sender info --}}
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-5 mb-4">
                <div class="flex items-start justify-between gap-3 flex-wrap">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold shrink-0">
                            {{ strtoupper(substr($message->user?->f_name ?? 'U', 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-[#1B3B36] dark:text-white truncate">
                                {{ trim(($message->user?->f_name ?? '') . ' ' . ($message->user?->l_name ?? '')) ?: 'User' }}
                            </p>
                            <p class="text-xs text-neutral-500 truncate">{{ $message->user?->email ?? __('messages.msg_no_email') }}</p>
                            <p class="text-[10px] text-neutral-400 mt-0.5">
                                {{ $message->user?->is_sitter ? __('messages.msg_role_sitter') : __('messages.msg_role_owner') }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        @php
                            $statusClass = match($message->status) {
                                'open'        => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                'in_progress' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                'resolved'    => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                default       => 'bg-neutral-100 text-neutral-700',
                            };
                            $typeClass = $message->type === 'report'
                                ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400'
                                : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400';
                            $statusLabel = match($message->status) {
                                'open'        => __('messages.inbox_stat_open'),
                                'in_progress' => __('messages.inbox_stat_in_progress'),
                                'resolved'    => __('messages.inbox_stat_resolved'),
                                default       => ucfirst(str_replace('_', ' ', $message->status)),
                            };
                        @endphp
                        <div class="flex flex-wrap gap-1.5 justify-end">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold {{ $typeClass }}">
                                {{ $message->type === 'report' ? __('messages.msg_label_report') : __('messages.msg_label_contact') }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </div>
                        <p class="text-[10px] text-neutral-400 mt-1.5">{{ $message->created_at->format('M d, Y g:i A') }}</p>
                    </div>
                </div>
            </div>

            {{-- Message content --}}
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6 mb-4">
                <h2 class="font-bold text-base text-[#1B3B36] dark:text-white mb-4">{{ $message->subject }}</h2>

                <div class="space-y-4 text-sm">
                    <div>
                        <p class="text-xs text-neutral-500 uppercase tracking-wider font-bold mb-1">{{ __('messages.msg_category') }}</p>
                        <p class="font-medium text-[#1B3B36] dark:text-white">{{ $message->category }}</p>
                    </div>

                    <div>
                        <p class="text-xs text-neutral-500 uppercase tracking-wider font-bold mb-1">{{ __('messages.msg_message') }}</p>
                        <p class="text-neutral-700 dark:text-neutral-300 whitespace-pre-line leading-relaxed">{{ $message->message }}</p>
                    </div>

                    @if ($message->steps_to_reproduce)
                        <div>
                            <p class="text-xs text-neutral-500 uppercase tracking-wider font-bold mb-1">{{ __('messages.msg_steps') }}</p>
                            <p class="text-neutral-700 dark:text-neutral-300 whitespace-pre-line leading-relaxed bg-neutral-50 dark:bg-neutral-800/50 p-3 rounded-lg">{{ $message->steps_to_reproduce }}</p>
                        </div>
                    @endif

                    @if ($message->device_info)
                        <div>
                            <p class="text-xs text-neutral-500 uppercase tracking-wider font-bold mb-1">{{ __('messages.msg_device') }}</p>
                            <p class="text-neutral-700 dark:text-neutral-300">{{ $message->device_info }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Existing reply --}}
            @if ($message->admin_reply)
                <div class="bg-blue-50 dark:bg-blue-950/20 rounded-2xl border border-blue-200 dark:border-blue-800/50 p-4 sm:p-5 mb-4">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-full bg-blue-100/30 flex items-center justify-center text-blue-600 dark:text-blue-400 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-1">
                                {{ __('messages.msg_previous_reply') }}
                                @if ($message->repliedBy)
                                    · {{ $message->repliedBy?->f_name ?? __('messages.msg_admin_fallback') }}
                                @endif
                            </p>
                            <p class="text-sm text-neutral-700 dark:text-neutral-300 whitespace-pre-line">{{ $message->admin_reply }}</p>
                            @if ($message->replied_at)
                                <p class="text-[10px] text-neutral-400 mt-2">{{ $message->replied_at->format('M d, Y g:i A') }} · {{ $message->replied_at->diffForHumans() }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- Reply form --}}
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6 mb-4">
                <h2 class="font-bold text-base text-[#1B3B36] dark:text-white mb-4">
                    {{ $message->admin_reply ? __('messages.msg_update_reply') : __('messages.msg_send_reply') }}
                </h2>

                <form method="POST" action="{{ route('admin.messages.reply', $message->id) }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.msg_message') }}</label>
                        <textarea name="admin_reply" rows="5" required maxlength="5000"
                                  placeholder="{{ __('messages.msg_reply_ph') }}"
                                  class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 px-4 py-2.5 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary text-sm font-medium resize-none">{{ old('admin_reply', $message->admin_reply) }}</textarea>
                        @error('admin_reply')
                            <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.msg_update_status') }}</label>
                        <select name="action" required
                                class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 px-4 py-2.5 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary text-sm font-medium">
                            <option value="in_progress" @selected($message->status === 'in_progress')>{{ __('messages.msg_status_in_progress') }}</option>
                            <option value="resolved"    @selected($message->status === 'resolved')>{{ __('messages.msg_status_resolved') }}</option>
                        </select>
                    </div>

                    <button type="submit"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary hover:bg-primary-600 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        {{ __('messages.msg_send_reply') }}
                    </button>
                </form>
            </div>

            {{-- Danger Zone --}}
            <div class="bg-red-50 dark:bg-red-950/20 rounded-2xl border border-red-200 dark:border-red-800/50 p-4 sm:p-5">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="min-w-0">
                        <h3 class="font-bold text-sm text-red-700 dark:text-red-400">{{ __('messages.msg_danger_zone') }}</h3>
                        <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ __('messages.msg_danger_desc') }}</p>
                    </div>
                    <form method="POST" action="{{ route('admin.messages.destroy', $message->id) }}"
                          onsubmit="return confirm('{{ __('messages.msg_confirm_delete') }}')"
                          class="shrink-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            {{ __('messages.msg_delete') }}
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>