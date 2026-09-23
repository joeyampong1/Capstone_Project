<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('tasks.monitor') }}"
               class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                    {{ __('messages.otm_title') }}
                </h1>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                    {{ __('messages.otm_subtitle') }}
                </p>
            </div>
        </div>
    </x-slot>

    @php
        $pet    = $booking->pet;
        $sitter = $booking->sitter;

        $sitterName = trim(($sitter->f_name ?? '') . ' ' . ($sitter->l_name ?? '')) ?: __('messages.otm_sitter_default');

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

        $statusMap = [
            'pending'   => ['bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400', __('messages.otb_bstatus_pending')],
            'accepted'  => ['bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',    __('messages.otb_bstatus_active')],
            'completed' => ['bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',       __('messages.otb_bstatus_completed')],
            'cancelled' => ['bg-neutral-200 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300', __('messages.otb_bstatus_cancelled')],
            'rejected'  => ['bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400',           __('messages.otb_bstatus_rejected')],
        ];
        [$bookingStatusClass, $bookingStatusLabel] = $statusMap[$booking->status] ?? $statusMap['pending'];
    @endphp

    <div class="py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- BOOKING SUMMARY --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.otm_booking_ref') }}</p>
                    <h3 class="font-bold text-xl mt-1 text-[#1B3B36] dark:text-white truncate">{{ $booking->booking_reference }}</h3>
                </div>
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.otm_pet') }}</p>
                    <h3 class="font-bold text-xl mt-1 text-[#1B3B36] dark:text-white truncate">{{ $petEmoji }} {{ $pet->name ?? __('messages.otm_pet_default') }}</h3>
                </div>
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.otb_assigned_sitter') }}</p>
                    <h3 class="font-bold text-xl mt-1 text-[#1B3B36] dark:text-white truncate">{{ $sitterName }}</h3>
                </div>
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.otb_booking_status') }}</p>
                    <span class="inline-block mt-1 {{ $bookingStatusClass }} px-3 py-1 rounded-full text-sm font-semibold">{{ $bookingStatusLabel }}</span>
                </div>
            </div>

            {{-- PROGRESS --}}
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-5 mb-6">
                <div class="flex justify-between items-center mb-2">
                    <h2 class="font-bold text-base text-[#1B3B36] dark:text-white">{{ __('messages.otb_overall_progress') }}</h2>
                    <span class="font-bold text-sm text-[#1B3B36] dark:text-white">{{ __('messages.otb_visits_count', ['done' => $completedVisits, 'total' => $totalVisits]) }}</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-neutral-700 rounded-full h-3">
                    <div class="bg-primary h-3 rounded-full transition-all" style="width:{{ $progressPct }}%"></div>
                </div>

                @if($booking->instructions)
                    <div class="mt-4 p-3 rounded-xl bg-blue-50 dark:bg-blue-950/20 border border-blue-200 dark:border-blue-800">
                        <p class="text-[10px] font-bold text-blue-700 dark:text-blue-400 uppercase tracking-wider mb-1">{{ __('messages.otb_special_instructions') }}</p>
                        <p class="text-sm text-blue-700 dark:text-blue-300 leading-relaxed">{{ $booking->instructions }}</p>
                    </div>
                @endif
            </div>

            {{-- VISITS LIST (clickable) --}}
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-5 sm:p-6">
                <h2 class="font-bold text-xl text-[#1B3B36] dark:text-white mb-5">
                    {{ __('messages.otb_visit_history', ['count' => $totalVisits]) }}
                </h2>

                @if($totalVisits === 0)
                    <div class="text-center py-8">
                        <p class="text-sm text-neutral-500 dark:text-neutral-400 italic">{{ __('messages.otb_no_visits_scheduled') }}</p>
                    </div>
                @else
                    <div class="space-y-4">

                        @foreach($booking->visits as $visit)
                            @php
                                $vStatusMap = [
                                    'pending'     => ['bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400', __('messages.otb_vstatus_pending')],
                                    'in_progress' => ['bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400', __('messages.otb_vstatus_in_progress')],
                                    'completed'   => ['bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400', __('messages.otb_vstatus_completed')],
                                    'missed'      => ['bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400', __('messages.otb_vstatus_missed')],
                                ];
                                [$vStatusClass, $vStatusLabel] = $vStatusMap[$visit->status] ?? $vStatusMap['pending'];

                                $totalTasks     = count($booking->tasks ?? []);
                                $completedTasks = count($visit->completed_tasks ?? []);
                                $progressPct    = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

                                $isOverdue = $visit->status === 'pending' && $visit->scheduled_datetime->isPast();
                            @endphp

                            {{-- CLICKABLE CARD --}}
                            <a href="{{ route('task.monitor.visit', $visit->id) }}"
                               class="group block border border-gray-200 dark:border-neutral-700 rounded-xl p-4 sm:p-5 hover:shadow-lg hover:border-primary/30 transition">

                                {{-- Header --}}
                                <div class="flex flex-wrap items-start justify-between gap-2 mb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center font-black text-sm sm:text-base shrink-0">
                                            {{ $visit->visit_number }}
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-[#1B3B36] dark:text-white">
                                                {{ __('messages.otb_visit_x_of', ['num' => $visit->visit_number, 'total' => $booking->total_visits]) }}
                                            </h3>
                                            <p class="text-sm text-neutral-500 dark:text-neutral-400">
                                                {{ $visit->scheduled_datetime->format('F j, Y • g:i A') }}
                                            </p>
                                        </div>
                                    </div>
                                    <span class="{{ $vStatusClass }} px-3 py-1 rounded-full text-sm font-semibold shrink-0">
                                        {{ $vStatusLabel }}
                                    </span>
                                </div>

                                {{-- Info row --}}
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-neutral-500 dark:text-neutral-400 mb-3">
                                    @if($visit->check_in)
                                        <span class="text-green-600">✅ {{ __('messages.otb_checked_in_at', ['time' => $visit->check_in->format('g:i A')]) }}</span>
                                    @endif
                                    @if($visit->check_out)
                                        <span class="text-green-600">✅ {{ __('messages.otb_checked_out_at', ['time' => $visit->check_out->format('g:i A')]) }}</span>
                                    @endif
                                    @if($visit->duration_minutes)
                                        <span>⏱️ {{ __('messages.otb_duration_min', ['min' => $visit->duration_minutes]) }}</span>
                                    @endif
                                    @if($visit->photo_proof_path)
                                        <span class="text-green-600">📸 {{ __('messages.otb_photo_proof_uploaded') }}</span>
                                    @endif
                                    @if($visit->lateness_minutes > 0)
                                        <span class="text-amber-600">⚠️ {{ __('messages.otb_late_by', ['min' => $visit->lateness_minutes]) }}</span>
                                    @endif
                                </div>

                                {{-- Task progress mini bar --}}
                                @if($totalTasks > 0)
                                    <div class="mt-3">
                                        <div class="flex justify-between gap-2 text-xs mb-1">
                                            <span class="text-neutral-500 dark:text-neutral-400">{{ __('messages.otb_tasks_progress') }}</span>
                                            <span class="font-bold text-[#1B3B36] dark:text-white">{{ $completedTasks }} / {{ $totalTasks }}</span>
                                        </div>
                                        <div class="w-full bg-gray-200 dark:bg-neutral-700 rounded-full h-1.5">
                                            <div class="bg-primary h-1.5 rounded-full transition-all" style="width:{{ $progressPct }}%"></div>
                                        </div>
                                    </div>
                                @endif

                                {{-- Photo thumbnail preview (kung naa) --}}
                                @if($visit->photo_proof_path)
                                    <div class="mt-3 flex gap-2">
                                        <img src="{{ asset('storage/' . $visit->photo_proof_path) }}"
                                             alt="Visit #{{ $visit->visit_number }}"
                                             class="w-16 h-16 object-cover rounded-lg border border-gray-200 dark:border-neutral-700">
                                    </div>
                                @endif

                                {{-- Overdue warning --}}
                                @if($isOverdue)
                                    <div class="mt-3 p-2 rounded-lg bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-800">
                                        <p class="text-[11px] text-red-700 dark:text-red-400 font-semibold">
                                            ⚠️ {{ __('messages.otb_overdue', ['time' => $visit->scheduled_datetime->diffForHumans()]) }}
                                        </p>
                                    </div>
                                @endif

                                <div class="mt-3 flex justify-end">
                                    <span class="text-sm font-medium text-primary group-hover:underline">
                                        {{ __('messages.otb_view_details') }}
                                    </span>
                                </div>
                            </a>
                        @endforeach

                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>