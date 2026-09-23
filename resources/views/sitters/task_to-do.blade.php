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
                    {{ __('messages.std_title') }}
                </h1>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.std_subtitle') }}</p>
            </div>
        </div>
    </x-slot>

    @php
        $booking = $visit->booking;
        $pet     = $booking->pet;
        $owner   = $booking->owner;

        $ownerName = trim(($owner->f_name ?? '') . ' ' . ($owner->l_name ?? '')) ?: __('messages.std_owner_default');

        $petEmoji = match($pet->petType?->name ?? '') {
            'Dog' => '🐶', 'Cat' => '🐱', 'Bird' => '🐦',
            'Rabbit' => '🐰', 'Hamster' => '🐹', 'Fish' => '🐟',
            'Reptile' => '🦎', default => '🐾',
        };

        $statusMap = [
            'pending'     => ['bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400', __('messages.std_status_pending')],
            'in_progress' => ['bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400', __('messages.std_status_in_progress')],
            'completed'   => ['bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400', __('messages.std_status_completed')],
            'missed'      => ['bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400', __('messages.std_status_missed')],
        ];
        [$statusClass, $statusLabel] = $statusMap[$visit->status] ?? $statusMap['pending'];

        // Combine photo paths
        $existingPhotos = $visit->photo_proof_paths ?? [];
        if (!is_array($existingPhotos)) {
            $existingPhotos = [];
        }

        // Include legacy single photo
        if ($visit->photo_proof_path && !in_array($visit->photo_proof_path, $existingPhotos)) {
            array_unshift($existingPhotos, $visit->photo_proof_path);
        }
    @endphp

    <div class="py-4 sm:py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200">
        <div class="w-full sm:max-w-4xl mx-auto px-2 sm:px-16 lg:px-24">

            {{-- ========================================== --}}
            {{-- BOOKING SUMMARY                            --}}
            {{-- ========================================== --}}
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-6">
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.std_booking_ref') }}</p>
                    <h3 class="font-bold text-base sm:text-lg mt-1 text-[#1B3B36] dark:text-white truncate">
                        {{ $booking->booking_reference }}
                    </h3>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.std_pet') }}</p>
                    <h3 class="font-bold text-base sm:text-lg mt-1 text-[#1B3B36] dark:text-white truncate">
                        {{ $petEmoji }} {{ $pet->name ?? __('messages.std_pet_default') }}
                    </h3>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.std_owner') }}</p>
                    <h3 class="font-bold text-base sm:text-lg mt-1 text-[#1B3B36] dark:text-white truncate">
                        {{ $ownerName }}
                    </h3>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.std_status') }}</p>
                    <span class="inline-block mt-1 {{ $statusClass }} px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full text-xs sm:text-sm font-semibold">
                        {{ $statusLabel }}
                    </span>
                </div>
            </div>

            {{-- ========================================== --}}
            {{-- VISIT INFORMATION                          --}}
            {{-- ========================================== --}}
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6 mb-4 sm:mb-6">
                <h2 class="font-bold text-base sm:text-lg text-[#1B3B36] dark:text-white mb-4">{{ __('messages.std_visit_info') }}</h2>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                    <div>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.std_visit_number') }}</p>
                        <p class="font-bold text-[#1B3B36] dark:text-white">
                            {{ __('messages.std_visit_x_of', ['num' => $visit->visit_number, 'total' => $booking->total_visits]) }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.std_scheduled_date') }}</p>
                        <p class="font-bold text-[#1B3B36] dark:text-white">
                            {{ $visit->scheduled_datetime->format('F j, Y') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.std_scheduled_time') }}</p>
                        <p class="font-bold text-[#1B3B36] dark:text-white">
                            {{ $visit->scheduled_datetime->format('g:i A') }}
                        </p>
                    </div>
                </div>

                @if($visit->lateness_minutes > 0)
                    <div class="mt-4 p-3 rounded-xl bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800">
                        <p class="text-xs text-amber-700 dark:text-amber-400">
                            {!! __('messages.std_late_msg', ['min' => $visit->lateness_minutes, 'pct' => $visit->late_deduction_percentage]) !!}
                        </p>
                    </div>
                @endif

                @if($booking->instructions)
                    <div class="mt-4 p-3 rounded-xl bg-blue-50 dark:bg-blue-950/20 border border-blue-200 dark:border-blue-800">
                        <p class="text-[10px] font-bold text-blue-700 dark:text-blue-400 uppercase tracking-wider mb-1">
                            {{ __('messages.std_special_instructions') }}
                        </p>
                        <p class="text-sm text-blue-700 dark:text-blue-300 leading-relaxed">
                            {{ $booking->instructions }}
                        </p>
                    </div>
                @endif
            </div>

            {{-- ========================================== --}}
            {{-- TIME LOG                                   --}}
            {{-- ========================================== --}}
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6 mb-4 sm:mb-6">
                <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white mb-3">{{ __('messages.std_time_log') }}</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">

                    {{-- Check In --}}
                    <div>
                        <label class="block text-xs text-neutral-500 dark:text-neutral-400 mb-1">{{ __('messages.std_check_in_time') }}</label>

                        @if($visit->check_in)
                            <div class="w-full rounded-xl border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-950/20 px-3 py-2.5 sm:px-4 sm:py-2.5">
                                <p class="font-bold text-green-700 dark:text-green-400 text-sm">
                                    {{ $visit->check_in->format('F j, Y g:i A') }}
                                </p>
                            </div>
                        @else
                            <form method="POST" action="{{ route('sitter.tasks.checkIn', $visit->id) }}">
                                @csrf
                                <button type="submit"
                                        onclick="return confirm(@js(__('messages.std_confirm_checkin')))"
                                        class="w-full px-4 py-2.5 sm:py-3 bg-green-500 hover:bg-green-600 text-white font-bold text-sm rounded-xl transition shadow-sm">
                                    {{ __('messages.std_check_in_now') }}
                                </button>
                            </form>
                        @endif
                    </div>

                    {{-- Check Out --}}
                    <div>
                        <label class="block text-xs text-neutral-500 dark:text-neutral-400 mb-1">{{ __('messages.std_check_out_time') }}</label>

                        @if($visit->check_out)
                            <div class="w-full rounded-xl border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-950/20 px-3 py-2.5 sm:px-4 sm:py-2.5">
                                <p class="font-bold text-green-700 dark:text-green-400 text-sm">
                                    {{ $visit->check_out->format('F j, Y g:i A') }}
                                </p>
                            </div>
                        @elseif($visit->check_in)
                            <form method="POST" action="{{ route('sitter.tasks.checkOut', $visit->id) }}">
                                @csrf
                                <button type="submit"
                                        onclick="return confirm(@js(__('messages.std_confirm_checkout')))"
                                        class="w-full px-4 py-2.5 sm:py-3 bg-red-500 hover:bg-red-600 text-white font-bold text-sm rounded-xl transition shadow-sm">
                                    {{ __('messages.std_check_out_now') }}
                                </button>
                            </form>
                        @else
                            <div class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 px-3 py-2.5 sm:px-4 sm:py-2.5">
                                <p class="text-sm text-neutral-400 italic">{{ __('messages.std_check_in_first') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                @if($visit->duration_minutes)
                    <div class="mt-3 flex flex-wrap items-center gap-2 sm:gap-4 text-sm">
                        <span class="text-neutral-500 dark:text-neutral-400">{{ __('messages.std_duration') }}</span>
                        <span class="font-bold text-[#1B3B36] dark:text-white">{{ $visit->duration_minutes }} {{ __('messages.std_minutes') }}</span>
                        <span class="text-xs text-neutral-400">{{ __('messages.std_auto_calculated') }}</span>
                    </div>
                @endif
            </div>

            {{-- ========================================== --}}
            {{-- TASKS CHECKLIST                            --}}
            {{-- ========================================== --}}
            @if(!empty($booking->tasks))
                <form method="POST" action="{{ route('sitter.tasks.updateTasks', $visit->id) }}"
                      class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6 mb-4 sm:mb-6">
                    @csrf

                    <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white mb-3">{{ __('messages.std_tasks_to_complete') }}</h3>

                    <div class="space-y-2">
                        @foreach($booking->tasks as $task)
                            @php
                                $isDone = in_array($task, $visit->completed_tasks ?? []);
                            @endphp
                            <label class="flex items-center gap-2.5 sm:gap-3 p-2.5 sm:p-3 rounded-xl border border-gray-200 dark:border-neutral-700 hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition cursor-pointer">
                                <input type="checkbox"
                                       name="completed_tasks[]"
                                       value="{{ $task }}"
                                       {{ $isDone ? 'checked' : '' }}
                                       class="w-5 h-5 rounded border-gray-300 text-primary focus:ring-primary shrink-0">
                                <span class="text-sm text-neutral-700 dark:text-neutral-300">{{ $task }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div class="mt-4 flex justify-end">
                        <button type="submit"
                                class="px-5 py-2.5 bg-primary hover:bg-primary-600 text-white font-bold text-xs rounded-xl transition shadow-sm">
                            {{ __('messages.std_save_tasks') }}
                        </button>
                    </div>
                </form>
            @endif

{{-- ========================================== --}}
{{-- PHOTO PROOF (multi-upload + lightbox)      --}}
{{-- ========================================== --}}
<div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6 mb-4 sm:mb-6"
     x-data="{
         lightboxOpen: false,
         lightboxSrc: '',
         selectedFiles: [],
         previewUrls: [],

         get selectedCount() {
             return this.selectedFiles.length;
         },

         openLightbox(src) {
             this.lightboxSrc = src;
             this.lightboxOpen = true;
         },

         handleFiles(event) {
             const newFiles = Array.from(event.target.files);
             if (!newFiles.length) return;

             // Append sa existing selection
             this.selectedFiles = [...this.selectedFiles, ...newFiles];

             // Sync ang input files via DataTransfer
             try {
                 const dt = new DataTransfer();
                 this.selectedFiles.forEach(f => dt.items.add(f));
                 event.target.files = dt.files;
             } catch (e) {
                 console.warn('DataTransfer not supported', e);
             }

             this.rebuildPreviews();
         },

         rebuildPreviews() {
             // Revoke old URLs to prevent memory leak
             this.previewUrls.forEach(url => {
                 try { URL.revokeObjectURL(url); } catch (e) {}
             });
             this.previewUrls = this.selectedFiles.map(f => URL.createObjectURL(f));
         },

         removeFile(index) {
             this.selectedFiles.splice(index, 1);

             const input = document.getElementById('photo-upload');
             if (input) {
                 try {
                     const dt = new DataTransfer();
                     this.selectedFiles.forEach(f => dt.items.add(f));
                     input.files = dt.files;
                 } catch (e) {
                     console.warn('DataTransfer not supported', e);
                 }
             }

             this.rebuildPreviews();
         },

         clearSelection() {
             this.selectedFiles = [];
             this.previewUrls = [];
             const input = document.getElementById('photo-upload');
             if (input) input.value = '';
         }
     }">

    <div class="flex items-center justify-between mb-3">
        <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white">{{ __('messages.std_photo_proof') }}</h3>
        @if(count($existingPhotos) > 0)
            <span class="text-xs text-neutral-500 dark:text-neutral-400 font-medium">
                {{ __('messages.std_photos_uploaded', ['count' => count($existingPhotos)]) }}
            </span>
        @endif
    </div>

    {{-- ========================================== --}}
    {{-- EXISTING PHOTOS — walay delete button       --}}
    {{-- ========================================== --}}
    @if(count($existingPhotos) > 0)
        <div class="mb-4">
            <p class="text-[11px] text-neutral-500 dark:text-neutral-400 mb-2">
                {{ __('messages.std_click_photo_fullsize') }}
            </p>

            <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                @foreach($existingPhotos as $idx => $path)
                    <button type="button"
                            @click="openLightbox('{{ asset('storage/' . $path) }}')"
                            class="group relative aspect-square rounded-xl overflow-hidden border-2 border-gray-200 dark:border-neutral-700 hover:border-primary transition">

                        <img src="{{ asset('storage/' . $path) }}"
                             alt="Visit photo {{ $idx + 1 }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition">

                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition flex items-center justify-center pointer-events-none">
                            <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-100 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/>
                            </svg>
                        </div>
                    </button>
                @endforeach
            </div>

            @if($visit->photo_timestamp)
                <p class="text-[11px] text-neutral-400 mt-2">
                    {{ __('messages.std_last_upload', ['time' => $visit->photo_timestamp->format('F j, Y g:i A')]) }}
                </p>
            @endif
        </div>
    @endif

    {{-- ========================================== --}}
    {{-- UPLOAD FORM                                --}}
    {{-- ========================================== --}}
    <form method="POST"
          action="{{ route('sitter.tasks.uploadPhoto', $visit->id) }}"
          enctype="multipart/form-data">
        @csrf

        {{-- Hidden input --}}
        <input type="file"
               name="photos[]"
               accept="image/*"
               multiple
               class="hidden"
               id="photo-upload"
               @change="handleFiles($event)">

        {{-- ========================================== --}}
        {{-- DASHED BOX                                 --}}
        {{-- ========================================== --}}
        <div class="border-2 border-dashed border-gray-300 dark:border-neutral-700 rounded-xl p-4 sm:p-5 transition"
             :class="previewUrls.length > 0 ? 'border-primary/50 bg-primary/5' : 'hover:border-primary/50'">

            {{-- STATE 1: Empty — clickable label to open picker --}}
            <div x-show="previewUrls.length === 0">
                <label for="photo-upload"
                       class="cursor-pointer block py-4 sm:py-6 text-center">
                    <svg class="w-10 h-10 sm:w-12 sm:h-12 mx-auto text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400 font-medium">
                        {{ __('messages.std_click_select_photos') }}
                    </p>
                    <p class="text-xs text-neutral-400 mt-0.5">
                        {{ __('messages.std_photo_limits') }}
                    </p>
                </label>
            </div>

            {{-- STATE 2: Naay selection — previews + individual remove --}}
            <div x-show="previewUrls.length > 0" x-cloak>

                {{-- Header row --}}
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs text-neutral-600 dark:text-neutral-400 font-bold">
                        <span x-text="selectedCount"></span> {{ __('messages.std_photos_selected') }}
                    </p>
                    <button type="button"
                            @click="clearSelection()"
                            class="text-[11px] text-red-500 hover:text-red-700 font-bold underline">
                        {{ __('messages.std_clear_all') }}
                    </button>
                </div>

                {{-- Preview grid with individual X buttons --}}
                <div class="grid grid-cols-3 sm:grid-cols-5 md:grid-cols-6 gap-2">
                    <template x-for="(url, idx) in previewUrls" :key="idx">
                        <div class="relative group aspect-square rounded-lg overflow-hidden border-2 border-primary/30 bg-white dark:bg-neutral-800">

                            <img :src="url" class="w-full h-full object-cover">

                            {{-- Number badge --}}
                            <span class="absolute top-1 left-1 w-5 h-5 rounded-full bg-primary text-white text-[10px] font-bold flex items-center justify-center shadow pointer-events-none"
                                  x-text="idx + 1"></span>

                            {{-- Individual remove button --}}
                            <button type="button"
                                    @click="removeFile(idx)"
                                    class="absolute top-1 right-1 w-6 h-6 rounded-full bg-red-500 hover:bg-red-600 text-white flex items-center justify-center shadow-md transition opacity-0 group-hover:opacity-100">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>

                {{-- Add more button --}}
                <div class="mt-3 flex items-center gap-2">
                    <label for="photo-upload"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary/10 hover:bg-primary/20 text-primary text-xs font-bold cursor-pointer transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        {{ __('messages.std_add_more_photos') }}
                    </label>
                    <span class="text-[10px] text-neutral-400">
                        {{ __('messages.std_add_hint') }}
                    </span>
                </div>

            </div>

        </div>

        {{-- Upload button --}}
        <div class="mt-4 flex justify-end">
            <button type="submit"
                    :disabled="selectedCount === 0"
                    :class="selectedCount === 0
                        ? 'bg-neutral-300 dark:bg-neutral-700 cursor-not-allowed'
                        : 'bg-primary hover:bg-primary-600 shadow-sm'"
                    class="inline-flex items-center gap-2 px-5 py-2.5 text-white font-bold text-xs rounded-xl transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                {{ __('messages.std_upload') }} <span x-text="selectedCount > 0 ? '(' + selectedCount + ')' : ''"></span>
            </button>
        </div>
    </form>

    {{-- ========================================== --}}
    {{-- LIGHTBOX MODAL                             --}}
    {{-- ========================================== --}}
    <div x-show="lightboxOpen"
         x-cloak
         x-transition.opacity
         @click="lightboxOpen = false"
         @keydown.escape.window="lightboxOpen = false"
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/90 backdrop-blur-sm cursor-zoom-out">

        <button type="button"
                @click.stop="lightboxOpen = false"
                class="absolute top-4 right-4 p-2 rounded-full bg-white/10 hover:bg-white/20 text-white transition z-10">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <img :src="lightboxSrc"
             @click.stop
             alt="Photo preview"
             class="max-w-[90vw] max-h-[90vh] object-contain rounded-lg shadow-2xl cursor-default">
    </div>

</div>

            {{-- ========================================== --}}
            {{-- SITTER NOTES                               --}}
            {{-- ========================================== --}}
            <form method="POST" action="{{ route('sitter.tasks.addNote', $visit->id) }}"
                  class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6 mb-4 sm:mb-6">
                @csrf

                <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white mb-3">{{ __('messages.std_sitter_notes') }}</h3>

                <textarea name="notes"
                          rows="4"
                          placeholder="{{ __('messages.std_notes_placeholder') }}"
                          class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 px-3 py-2.5 sm:px-4 sm:py-2.5 text-sm text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary resize-none">{{ old('notes', $visit->notes) }}</textarea>

                <div class="mt-4 flex justify-end">
                    <button type="submit"
                            class="px-5 py-2.5 bg-primary hover:bg-primary-600 text-white font-bold text-xs rounded-xl transition shadow-sm">
                        {{ __('messages.std_save_note') }}
                    </button>
                </div>
            </form>

            {{-- ========================================== --}}
            {{-- COMPLETE VISIT                             --}}
            {{-- ========================================== --}}
            @if(in_array($visit->status, ['pending', 'in_progress']))
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6 mb-4 sm:mb-6">
                    <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white mb-2">{{ __('messages.std_complete_visit') }}</h3>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-4">
                        {{ __('messages.std_complete_desc') }}
                    </p>

                    <form method="POST" action="{{ route('sitter.tasks.complete', $visit->id) }}">
                        @csrf
                        <button type="submit"
                                onclick="return confirm(@js(__('messages.std_confirm_complete')))"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 sm:px-8 sm:py-3.5 bg-green-500 hover:bg-green-600 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ __('messages.std_mark_completed') }}
                        </button>
                    </form>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>