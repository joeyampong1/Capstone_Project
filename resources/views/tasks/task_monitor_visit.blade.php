<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('task.monitor', ['booking' => $visit->booking_id]) }}"
               class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                    {{ __('messages.ovs_title') }}
                </h1>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                    {{ __('messages.ovs_subtitle', ['num' => $visit->visit_number, 'total' => $visit->booking->total_visits]) }}
                </p>
            </div>
        </div>
    </x-slot>

    @php
        $booking = $visit->booking;
        $pet     = $booking->pet;
        $owner   = $booking->owner;
        $sitter  = $booking->sitter;

        $ownerName = trim(($owner->f_name ?? '') . ' ' . ($owner->l_name ?? '')) ?: __('messages.ovs_owner_default');

        $petEmoji = match($pet->petType?->name ?? '') {
            'Dog' => '🐶', 'Cat' => '🐱', 'Bird' => '🐦',
            'Rabbit' => '🐰', 'Hamster' => '🐹', 'Fish' => '🐟',
            'Reptile' => '🦎', default => '🐾',
        };

        $statusMap = [
            'pending'     => ['bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400', __('messages.ovs_status_pending')],
            'in_progress' => ['bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400', __('messages.ovs_status_in_progress')],
            'completed'   => ['bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400', __('messages.ovs_status_completed')],
            'missed'      => ['bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400', __('messages.ovs_status_missed')],
        ];
        [$statusClass, $statusLabel] = $statusMap[$visit->status] ?? $statusMap['pending'];
    @endphp

    <div class="py-4 sm:py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200">
        <div class="w-full sm:max-w-4xl mx-auto px-2 sm:px-16 lg:px-24">

            {{-- ========================================== --}}
            {{-- BOOKING SUMMARY                            --}}
            {{-- ========================================== --}}
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-6">
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.ovs_booking_ref') }}</p>
                    <h3 class="font-bold text-base sm:text-lg mt-1 text-[#1B3B36] dark:text-white truncate">{{ $booking->booking_reference }}</h3>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.ovs_pet') }}</p>
                    <h3 class="font-bold text-base sm:text-lg mt-1 text-[#1B3B36] dark:text-white truncate">{{ $petEmoji }} {{ $pet->name ?? __('messages.ovs_pet_default') }}</h3>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.ovs_owner') }}</p>
                    <h3 class="font-bold text-base sm:text-lg mt-1 text-[#1B3B36] dark:text-white truncate">{{ $ownerName }}</h3>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.ovs_status') }}</p>
                    <span class="inline-block mt-1 {{ $statusClass }} px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full text-xs sm:text-sm font-semibold">
                        {{ $statusLabel }}
                    </span>
                </div>
            </div>

            {{-- ========================================== --}}
            {{-- VISIT INFORMATION                          --}}
            {{-- ========================================== --}}
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6 mb-4 sm:mb-6">
                <h2 class="font-bold text-base sm:text-lg text-[#1B3B36] dark:text-white mb-4">{{ __('messages.ovs_visit_info') }}</h2>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                    <div>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.ovs_visit_number') }}</p>
                        <p class="font-bold text-[#1B3B36] dark:text-white">{{ __('messages.ovs_visit_x_of', ['num' => $visit->visit_number, 'total' => $booking->total_visits]) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.ovs_scheduled_date') }}</p>
                        <p class="font-bold text-[#1B3B36] dark:text-white">{{ $visit->scheduled_datetime->format('F j, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.ovs_scheduled_time') }}</p>
                        <p class="font-bold text-[#1B3B36] dark:text-white">{{ $visit->scheduled_datetime->format('g:i A') }}</p>
                    </div>
                </div>

                @if($visit->lateness_minutes > 0)
                    <div class="mt-4 p-3 rounded-xl bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800">
                        <p class="text-xs text-amber-700 dark:text-amber-400">
                            {!! __('messages.ovs_late_msg', ['min' => $visit->lateness_minutes, 'pct' => $visit->late_deduction_percentage]) !!}
                        </p>
                    </div>
                @endif

                @if($booking->instructions)
                    <div class="mt-4 p-3 rounded-xl bg-blue-50 dark:bg-blue-950/20 border border-blue-200 dark:border-blue-800">
                        <p class="text-[10px] font-bold text-blue-700 dark:text-blue-400 uppercase tracking-wider mb-1">{{ __('messages.ovs_special_instructions') }}</p>
                        <p class="text-sm text-blue-700 dark:text-blue-300 leading-relaxed">{{ $booking->instructions }}</p>
                    </div>
                @endif
            </div>

            {{-- ========================================== --}}
            {{-- TIME LOG (READ ONLY)                       --}}
            {{-- ========================================== --}}
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6 mb-4 sm:mb-6">
                <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white mb-3">{{ __('messages.ovs_time_log') }}</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                    <div>
                        <label class="block text-xs text-neutral-500 dark:text-neutral-400 mb-1">{{ __('messages.ovs_check_in_time') }}</label>
                        <div class="w-full rounded-xl border {{ $visit->check_in ? 'border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-950/20' : 'border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50' }} px-3 py-2.5 sm:px-4 sm:py-2.5">
                            <p class="font-bold {{ $visit->check_in ? 'text-green-700 dark:text-green-400' : 'text-neutral-400 italic' }} text-sm">
                                {{ $visit->check_in ? $visit->check_in->format('F j, Y g:i A') : __('messages.ovs_not_yet_checked_in') }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs text-neutral-500 dark:text-neutral-400 mb-1">{{ __('messages.ovs_check_out_time') }}</label>
                        <div class="w-full rounded-xl border {{ $visit->check_out ? 'border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-950/20' : 'border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50' }} px-3 py-2.5 sm:px-4 sm:py-2.5">
                            <p class="font-bold {{ $visit->check_out ? 'text-green-700 dark:text-green-400' : 'text-neutral-400 italic' }} text-sm">
                                {{ $visit->check_out ? $visit->check_out->format('F j, Y g:i A') : __('messages.ovs_not_yet_checked_out') }}
                            </p>
                        </div>
                    </div>
                </div>

                @if($visit->duration_minutes)
                    <div class="mt-3 flex flex-wrap items-center gap-2 sm:gap-4 text-sm">
                        <span class="text-neutral-500 dark:text-neutral-400">{{ __('messages.ovs_duration') }}</span>
                        <span class="font-bold text-[#1B3B36] dark:text-white">{{ $visit->duration_minutes }} {{ __('messages.ovs_minutes') }}</span>
                    </div>
                @endif
            </div>

            {{-- ========================================== --}}
            {{-- TASKS (READ ONLY)                          --}}
            {{-- ========================================== --}}
            @if(!empty($booking->tasks))
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6 mb-4 sm:mb-6">
                    <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white mb-3">{{ __('messages.ovs_tasks') }}</h3>

                    <div class="space-y-2">
                        @foreach($booking->tasks as $task)
                            @php $isDone = in_array($task, $visit->completed_tasks ?? []); @endphp
                            <div class="flex items-center gap-2.5 sm:gap-3 p-2.5 sm:p-3 rounded-xl border {{ $isDone ? 'border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-950/20' : 'border-gray-200 dark:border-neutral-700' }}">
                                @if($isDone)
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-neutral-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="9" stroke-width="2"/>
                                    </svg>
                                @endif
                                <span class="text-sm {{ $isDone ? 'text-green-700 dark:text-green-400' : 'text-neutral-500 dark:text-neutral-400' }}">
                                    {{ $task }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    {{-- Completed count --}}
                    <div class="mt-3 text-xs text-neutral-500 dark:text-neutral-400">
                        {{ __('messages.ovs_tasks_completed', ['done' => count($visit->completed_tasks ?? []), 'total' => count($booking->tasks)]) }}
                    </div>
                </div>
            @endif

            {{-- ========================================== --}}
            {{-- PHOTO PROOF (with lightbox)                --}}
            {{-- ========================================== --}}
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6 mb-4 sm:mb-6"
                x-data="{
                    lightboxOpen: false,
                    lightboxSrc: '',

                    openLightbox(src) {
                        this.lightboxSrc = src;
                        this.lightboxOpen = true;
                    }
                }">

                <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white mb-3">{{ __('messages.ovs_photo_proof') }}</h3>

                @php
                    $allPhotos = $visit->photo_proof_paths ?? [];
                    if (!is_array($allPhotos)) $allPhotos = [];
                    if ($visit->photo_proof_path && !in_array($visit->photo_proof_path, $allPhotos)) {
                        array_unshift($allPhotos, $visit->photo_proof_path);
                    }
                @endphp

                @if(count($allPhotos) > 0)
                    <div>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-3">
                            {{ __('messages.ovs_click_photo_fullsize', ['count' => count($allPhotos)]) }}
                        </p>

                        <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                            @foreach($allPhotos as $idx => $path)
                                <button type="button"
                                        @click="openLightbox('{{ asset('storage/' . $path) }}')"
                                        class="group relative aspect-square rounded-xl overflow-hidden border-2 border-gray-200 dark:border-neutral-700 hover:border-primary transition cursor-pointer">

                                    <img src="{{ asset('storage/' . $path) }}"
                                        alt="Visit photo {{ $idx + 1 }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition">

                                    {{-- Hover overlay + zoom icon --}}
                                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition flex items-center justify-center pointer-events-none">
                                        <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-100 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/>
                                        </svg>
                                    </div>
                                </button>
                            @endforeach
                        </div>

                        @if($visit->photo_timestamp)
                            <p class="text-[11px] text-neutral-400 mt-2">
                                {{ __('messages.ovs_uploaded_at', ['time' => $visit->photo_timestamp->format('F j, Y g:i A')]) }}
                            </p>
                        @endif
                    </div>

                    {{-- ========================================== --}}
                    {{-- LIGHTBOX MODAL                             --}}
                    {{-- ========================================== --}}
                    <div x-show="lightboxOpen"
                        x-cloak
                        x-transition.opacity
                        @click="lightboxOpen = false"
                        @keydown.escape.window="lightboxOpen = false"
                        class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/90 backdrop-blur-sm cursor-zoom-out">

                        {{-- Close button --}}
                        <button type="button"
                                @click.stop="lightboxOpen = false"
                                class="absolute top-4 right-4 p-2 rounded-full bg-white/10 hover:bg-white/20 text-white transition z-10">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>

                        {{-- Full image --}}
                        <img :src="lightboxSrc"
                            @click.stop
                            alt="Photo preview"
                            class="max-w-[90vw] max-h-[90vh] object-contain rounded-lg shadow-2xl cursor-default">
                    </div>

                @else
                    <div class="border-2 border-dashed border-gray-300 dark:border-neutral-700 rounded-xl p-4 sm:p-6 text-center">
                        <svg class="w-10 h-10 sm:w-12 sm:h-12 mx-auto text-neutral-300 dark:text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400 italic">
                            {{ __('messages.ovs_no_photo_proof') }}
                        </p>
                    </div>
                @endif

            </div>

            {{-- ========================================== --}}
            {{-- SITTER NOTES (READ ONLY)                   --}}
            {{-- ========================================== --}}
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6 mb-4 sm:mb-6">
                <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white mb-3">{{ __('messages.ovs_sitter_notes') }}</h3>

                @if($visit->notes)
                    <div class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 px-3 py-2.5 sm:px-4 sm:py-3">
                        <p class="text-sm text-neutral-700 dark:text-neutral-300 leading-relaxed whitespace-pre-line">{{ $visit->notes }}</p>
                    </div>
                @else
                    <div class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 px-3 py-2.5 sm:px-4 sm:py-3">
                        <p class="text-sm text-neutral-400 italic">{{ __('messages.ovs_no_notes') }}</p>
                    </div>
                @endif
            </div>

            {{-- ========================================== --}}
            {{-- BACK ACTION                                --}}
            {{-- ========================================== --}}
            <div class="flex flex-col-reverse sm:flex-row sm:items-center gap-2 sm:gap-3 pt-4 border-t border-gray-100 dark:border-neutral-800">
                <a href="{{ route('task.monitor', ['booking' => $visit->booking_id]) }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 sm:px-8 sm:py-3.5 border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-sm rounded-xl hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                    {{ __('messages.ovs_back_visit_history') }}
                </a>
            </div>

        </div>
    </div>
</x-app-layout>