<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('sitter.tasks.index') }}"
               class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                    {{ __('messages.sbv_title') }}
                </h1>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                    {{ $booking->booking_reference }} — {{ __('messages.sbv_visits_count', ['count' => $booking->visits->count()]) }}
                </p>
            </div>
        </div>
    </x-slot>

    @php
        $pet   = $booking->pet;
        $owner = $booking->owner;

        $ownerName = trim(($owner->f_name ?? '') . ' ' . ($owner->l_name ?? '')) ?: __('messages.sbv_owner_default');

        $petEmoji = match($pet->petType?->name ?? '') {
            'Dog' => '🐶', 'Cat' => '🐱', 'Bird' => '🐦',
            'Rabbit' => '🐰', 'Hamster' => '🐹', 'Fish' => '🐟',
            'Reptile' => '🦎', default => '🐾',
        };

        $totalVisits     = $booking->visits->count();
        $completedVisits = $booking->visits->where('status', 'completed')->count();
        $progressPct     = $totalVisits > 0
            ? round(($completedVisits / $totalVisits) * 100)
            : 0;
    @endphp

    <div class="py-4 sm:py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200">
        <div class="w-full sm:max-w-4xl mx-auto px-2 sm:px-16 lg:px-24">

            {{-- ========================================== --}}
            {{-- BOOKING INFO CARD                          --}}
            {{-- ========================================== --}}
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-5 mb-4 sm:mb-6">

                <div class="flex items-start justify-between gap-2 mb-4">
                    <div class="min-w-0">
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sbv_booking_ref') }}</p>
                        <h3 class="font-bold text-lg sm:text-xl text-[#1B3B36] dark:text-white truncate">
                            {{ $booking->booking_reference }}
                        </h3>
                    </div>
                    <span class="bg-primary/10 text-primary px-3 py-1 rounded-full text-xs font-semibold shrink-0">
                        {{ __('messages.sbv_visits_count', ['count' => $totalVisits]) }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="min-w-0">
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sbv_pet') }}</p>
                        <p class="font-bold text-[#1B3B36] dark:text-white truncate">
                            {{ $petEmoji }} {{ $pet->name ?? __('messages.sbv_pet_default') }}
                        </p>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.sbv_owner') }}</p>
                        <p class="font-bold text-[#1B3B36] dark:text-white truncate">{{ $ownerName }}</p>
                    </div>
                </div>

                {{-- Overall progress --}}
                <div class="mt-4">
                    <div class="flex justify-between gap-2 text-sm">
                        <span class="text-neutral-500 dark:text-neutral-400">{{ __('messages.sbv_overall_progress') }}</span>
                        <span class="font-bold text-[#1B3B36] dark:text-white shrink-0">
                            {{ __('messages.sbv_completed_x_of_y', ['done' => $completedVisits, 'total' => $totalVisits]) }}
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-neutral-700 rounded-full h-2 mt-1">
                        <div class="bg-primary h-2 rounded-full transition-all"
                             style="width:{{ $progressPct }}%"></div>
                    </div>
                </div>

                {{-- Instructions --}}
                @if($booking->instructions)
                    <div class="mt-4 p-3 rounded-xl bg-blue-50 dark:bg-blue-950/20 border border-blue-200 dark:border-blue-800">
                        <p class="text-[10px] font-bold text-blue-700 dark:text-blue-400 uppercase tracking-wider mb-1">
                            {{ __('messages.sbv_special_instructions') }}
                        </p>
                        <p class="text-sm text-blue-700 dark:text-blue-300 leading-relaxed">
                            {{ $booking->instructions }}
                        </p>
                    </div>
                @endif

                {{-- Tasks list --}}
                @if(!empty($booking->tasks))
                    <div class="mt-4">
                        <p class="text-[10px] font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-2">
                            {{ __('messages.sbv_tasks_every_visit') }}
                        </p>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($booking->tasks as $task)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-medium bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300">
                                    {{ $task }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- ========================================== --}}
            {{-- VISITS LIST                                --}}
            {{-- ========================================== --}}
            <div class="space-y-4">

                @forelse($booking->visits as $visit)
                    @php
                        $statusMap = [
                            'pending'     => ['bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400', __('messages.sbv_status_pending')],
                            'in_progress' => ['bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400', __('messages.sbv_status_in_progress')],
                            'completed'   => ['bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400', __('messages.sbv_status_completed')],
                            'missed'      => ['bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400', __('messages.sbv_status_missed')],
                        ];
                        [$statusClass, $statusLabel] = $statusMap[$visit->status] ?? $statusMap['pending'];

                        $isOverdue = $visit->status === 'pending' && $visit->scheduled_datetime->isPast();
                        $isToday   = $visit->scheduled_datetime->isToday();
                    @endphp

                    <a href="{{ route('sitter.tasks.visitDetail', $visit->id) }}"
                       class="group block bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-5 hover:shadow-lg hover:border-primary/30 transition">

                        {{-- Header --}}
                        <div class="flex items-start justify-between gap-2 mb-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center font-black text-sm sm:text-base shrink-0">
                                    {{ $visit->visit_number }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-bold text-[#1B3B36] dark:text-white text-sm sm:text-base">
                                        {{ __('messages.sbv_visit_x_of', ['num' => $visit->visit_number, 'total' => $booking->total_visits]) }}
                                    </p>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">
                                        📅 {{ $visit->scheduled_datetime->format('M j, Y g:i A') }}
                                    </p>
                                </div>
                            </div>

                            <span class="{{ $statusClass }} px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full text-[10px] sm:text-xs font-bold shrink-0">
                                {{ $statusLabel }}
                            </span>
                        </div>

                        {{-- Info row --}}
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-neutral-500 dark:text-neutral-400 mb-3">
                            @if($visit->duration_minutes)
                                <span>⏱️ {{ $visit->duration_minutes }} min</span>
                            @endif

                            @if($visit->check_in)
                                <span class="text-green-600">
                                    ✅ {{ __('messages.sbv_checked_in_at', ['time' => $visit->check_in->format('g:i A')]) }}
                                </span>
                            @endif

                            @if($visit->lateness_minutes > 0)
                                <span class="text-amber-600">
                                    ⚠️ {{ __('messages.sbv_late_by', ['min' => $visit->lateness_minutes]) }}
                                </span>
                            @endif

                            @if($visit->photo_proof_path)
                                <span class="text-green-600">📸 {{ __('messages.sbv_photo_uploaded') }}</span>
                            @endif
                        </div>

                        {{-- Tasks preview --}}
                        @if(!empty($booking->tasks))
                            <div class="pt-3 border-t border-gray-100 dark:border-neutral-800">
                                <p class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider mb-1.5">
                                    {{ __('messages.sbv_tasks_done_label', ['done' => count($visit->completed_tasks ?? []), 'total' => count($booking->tasks)]) }}
                                </p>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach(array_slice($booking->tasks, 0, 3) as $task)
                                        @php $isDone = in_array($task, $visit->completed_tasks ?? []); @endphp
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px]
                                            {{ $isDone ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 line-through' : 'bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300' }}">
                                            {{ $isDone ? '✓' : '○' }} {{ $task }}
                                        </span>
                                    @endforeach
                                    @if(count($booking->tasks) > 3)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] bg-neutral-100 text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400">
                                            {{ __('messages.sbv_more', ['count' => count($booking->tasks) - 3]) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- Overdue alert --}}
                        @if($isOverdue)
                            <div class="mt-3 p-2.5 rounded-lg bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-800">
                                <p class="text-[11px] text-red-700 dark:text-red-400 font-bold">
                                    ⚠️ {{ __('messages.sbv_overdue', ['time' => $visit->scheduled_datetime->diffForHumans()]) }}
                                </p>
                            </div>
                        @endif

                        <div class="mt-3 flex justify-end">
                            <span class="text-sm font-medium text-primary group-hover:underline">
                                {{ __('messages.sbv_open_visit') }}
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-12">
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">
                            {{ __('messages.sbv_no_visits') }}
                        </p>
                    </div>
                @endforelse

            </div>

        </div>
    </div>
</x-app-layout>