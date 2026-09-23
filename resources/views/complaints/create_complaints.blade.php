<x-app-layout>
    <!-- HEADER SLOT -->
    <x-slot name="header">
        <div x-data class="flex items-center justify-between w-full">
            <div class="flex items-center gap-3">
                <a href="{{ route('complaints.index') }}"
                   class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                        {{ __('messages.cp_create_title') }}
                    </h1>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                        {{ auth()->user()->is_sitter ? __('messages.cp_create_subtitle_sitter') : __('messages.cp_create_subtitle_owner') }}
                    </p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200"
         x-data="complaintForm({
             bookings: {{ Js::from($bookings->map(fn($b) => [
                 'id'          => $b->id,
                 'reference'   => $b->booking_reference,
                 'pet_name'    => $b->pet?->name ?? 'Pet',
                 'owner_name'  => trim(($b->owner?->f_name ?? '') . ' ' . ($b->owner?->l_name ?? '')) ?: 'Owner',
                 'sitter_name' => trim(($b->sitter?->f_name ?? '') . ' ' . ($b->sitter?->l_name ?? '')) ?: 'Sitter',
                 'owner_id'    => $b->owner_id,
                 'sitter_id'   => $b->sitter_id,
             ])) }},
             userId: {{ auth()->id() }},
             preselectedBookingId: '{{ $preselectedBookingId ?? '' }}'
         })"
         x-init="init()">
        <div class="w-full sm:max-w-2xl mx-auto px-2 sm:px-16 lg:px-24">

            {{-- ERROR SUMMARY --}}
            @if($errors->any())
                <div class="mb-4 p-4 rounded-xl bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-800">
                    <p class="font-bold text-red-700 dark:text-red-400 mb-1">{{ __('messages.cp_errors_intro') }}</p>
                    <ul class="list-disc list-inside text-xs text-red-600 dark:text-red-400 space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- COMPLAINT FORM CARD -->
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-8">
                <h2 class="text-base sm:text-lg font-black text-[#1B3B36] dark:text-white mb-4 sm:mb-6">{{ __('messages.cp_details_header') }}</h2>

                {{-- Kung wala'y bookings --}}
                @if($bookings->isEmpty())
                    <div class="p-5 rounded-xl bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800 text-center">
                        <svg class="w-12 h-12 mx-auto text-amber-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm text-amber-700 dark:text-amber-400 font-bold mb-1">
                            {{ __('messages.cp_no_bookings_title') }}
                        </p>
                        <p class="text-xs text-amber-600 dark:text-amber-400">
                            {{ auth()->user()->is_sitter
                                ? __('messages.cp_no_bookings_sitter')
                                : __('messages.cp_no_bookings_owner') }}
                        </p>
                        <a href="{{ route('mybookings.index') }}"
                           class="inline-block mt-3 text-xs font-bold text-primary hover:underline">
                            → {{ __('messages.cp_view_my_bookings') }}
                        </a>
                    </div>
                @else
                    <form method="POST"
                          action="{{ route('complaints.store') }}"
                          enctype="multipart/form-data"
                          class="space-y-5 sm:space-y-6">
                        @csrf

                        <!-- Booking Dropdown -->
                        <div>
                            <label for="booking_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('messages.cp_booking_label') }} <span class="text-red-500">*</span>
                            </label>
                            <select id="booking_id"
                                    name="booking_id"
                                    x-model="selectedBookingId"
                                    @change="onBookingChange()"
                                    required
                                    class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 px-3 py-2.5 sm:px-4 sm:py-3 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary text-sm font-medium">
                                <option value="">{{ __('messages.cp_select_booking') }}</option>
                                @foreach($bookings as $booking)
                                    @php
                                        $isOwner    = $booking->owner_id === auth()->id();
                                        $counterpart = $isOwner ? $booking->sitter : $booking->owner;
                                        $cpName     = trim(($counterpart?->f_name ?? '') . ' ' . ($counterpart?->l_name ?? '')) ?: 'User';
                                        $petName    = $booking->pet?->name ?? 'Pet';
                                    @endphp
                                    <option value="{{ $booking->id }}">
                                        {{ $booking->booking_reference }} — {{ $cpName }} — {{ $petName }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-neutral-400">{{ __('messages.cp_booking_hint') }}</p>
                        </div>

                        <!-- Respondent (Auto-filled) -->
                        <div>
                            <label for="respondent" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('messages.cp_respondent_label') }}
                            </label>
                            <input type="text"
                                   id="respondent"
                                   name="respondent"
                                   :value="respondentName"
                                   readonly
                                   placeholder="{{ __('messages.cp_respondent_ph') }}"
                                   class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-100 dark:bg-neutral-800/50 px-3 py-2.5 sm:px-4 sm:py-3 text-[#1B3B36] dark:text-white cursor-not-allowed text-sm font-medium">
                            <p class="mt-1 text-xs text-neutral-400">{{ __('messages.cp_respondent_hint') }}</p>
                        </div>

                        <!-- Complaint Type -->
                        <div>
                            <label for="complaint_type" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('messages.cp_type_label') }} <span class="text-red-500">*</span>
                            </label>
                            <select id="complaint_type"
                                    name="complaint_type"
                                    required
                                    class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 px-3 py-2.5 sm:px-4 sm:py-3 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary text-sm font-medium">
                                <option value="">{{ __('messages.cp_select_type') }}</option>
                                <option value="missed_visit"  @selected(old('complaint_type') === 'missed_visit')>{{ __('messages.complaint_missed_visit') }}</option>
                                <option value="poor_service"  @selected(old('complaint_type') === 'poor_service')>{{ __('messages.complaint_poor_service') }}</option>
                                <option value="no_proof"      @selected(old('complaint_type') === 'no_proof')>{{ __('messages.complaint_no_proof') }}</option>
                                <option value="rude_behavior" @selected(old('complaint_type') === 'rude_behavior')>{{ __('messages.complaint_rude_behavior') }}</option>
                                <option value="others"        @selected(old('complaint_type') === 'others')>{{ __('messages.complaint_others') }}</option>
                            </select>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('messages.cp_desc_label') }} <span class="text-red-500">*</span>
                            </label>
                            <textarea id="description"
                                      name="description"
                                      rows="5"
                                      required
                                      maxlength="3000"
                                      placeholder="{{ __('messages.cp_desc_ph') }}"
                                      class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 px-3 py-2.5 sm:px-4 sm:py-3 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary text-sm font-medium resize-none">{{ old('description') }}</textarea>
                        </div>

                        <!-- Evidence Upload -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('messages.cp_evidence_label') }}
                            </label>

                            <input type="file"
                                   name="evidence[]"
                                   id="evidence-upload"
                                   accept="image/*,video/*,.pdf,.doc,.docx"
                                   multiple
                                   class="hidden"
                                   @change="handleFiles($event)">

                            <div class="grid grid-cols-3 gap-2 sm:gap-3">
                                {{-- Photos --}}
                                <div class="border-2 border-dashed border-gray-300 dark:border-neutral-700 rounded-xl p-3 sm:p-4 text-center hover:border-primary/50 transition cursor-pointer"
                                     @click="document.getElementById('evidence-upload').click()">
                                    <svg class="w-6 h-6 sm:w-8 sm:h-8 mx-auto text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 mt-1 font-medium">{{ __('messages.cp_evidence_photos') }}</p>
                                </div>

                                {{-- Videos --}}
                                <div class="border-2 border-dashed border-gray-300 dark:border-neutral-700 rounded-xl p-3 sm:p-4 text-center hover:border-primary/50 transition cursor-pointer"
                                     @click="document.getElementById('evidence-upload').click()">
                                    <svg class="w-6 h-6 sm:w-8 sm:h-8 mx-auto text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 mt-1 font-medium">{{ __('messages.cp_evidence_videos') }}</p>
                                </div>

                                {{-- Docs --}}
                                <div class="border-2 border-dashed border-gray-300 dark:border-neutral-700 rounded-xl p-3 sm:p-4 text-center hover:border-primary/50 transition cursor-pointer"
                                     @click="document.getElementById('evidence-upload').click()">
                                    <svg class="w-6 h-6 sm:w-8 sm:h-8 mx-auto text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400 mt-1 font-medium">{{ __('messages.cp_evidence_docs') }}</p>
                                </div>
                            </div>

                            {{-- Selected files list --}}
                            <div x-show="previewFiles.length > 0" x-cloak class="mt-3">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-xs text-neutral-600 dark:text-neutral-400 font-bold">
                                        <span x-text="previewFiles.length"></span> {{ __('messages.cp_files_selected', ['count' => '']) }}
                                    </p>
                                    <button type="button"
                                            @click="clearFiles()"
                                            class="text-[11px] text-red-500 hover:text-red-700 font-bold underline">
                                        {{ __('messages.cp_clear_all') }}
                                    </button>
                                </div>

                                <div class="space-y-1.5">
                                    <template x-for="(file, idx) in previewFiles" :key="idx">
                                        <div class="flex items-center gap-2 p-2 rounded-lg bg-neutral-100 dark:bg-neutral-800">
                                            <span class="w-6 h-6 rounded-full bg-primary text-white text-[10px] font-bold flex items-center justify-center shrink-0"
                                                  x-text="idx + 1"></span>
                                            <span class="text-xs text-neutral-700 dark:text-neutral-300 truncate flex-1"
                                                  x-text="file.name"></span>
                                            <span class="text-[10px] text-neutral-400 shrink-0"
                                                  x-text="formatSize(file.size)"></span>
                                            <button type="button"
                                                    @click="removeFile(idx)"
                                                    class="w-6 h-6 rounded-full bg-red-500 hover:bg-red-600 text-white flex items-center justify-center shrink-0">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <p class="mt-1 text-xs text-neutral-400">{{ __('messages.cp_evidence_hint') }}</p>
                        </div>

                        <!-- Buttons -->
                        <div class="flex flex-col-reverse sm:flex-row sm:items-center gap-2 sm:gap-3 pt-4 border-t border-gray-100 dark:border-neutral-800">
                            <a href="{{ route('complaints.index') }}"
                               class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 sm:px-8 sm:py-3.5 border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-sm rounded-xl hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                                {{ __('messages.cp_cancel') }}
                            </a>
                            <button type="submit"
                                    :disabled="!selectedBookingId"
                                    :class="!selectedBookingId
                                        ? 'bg-neutral-300 dark:bg-neutral-700 cursor-not-allowed'
                                        : 'bg-primary hover:bg-primary-600 shadow-md hover:shadow-lg hover:-translate-y-0.5'"
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 sm:px-8 sm:py-3.5 text-white font-bold text-sm rounded-xl transition transform">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                {{ __('messages.cp_submit') }}
                            </button>
                        </div>
                    </form>
                @endif
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
    function complaintForm(config) {
        return {
            bookings: config.bookings || [],
            userId: config.userId,
            selectedBookingId: config.preselectedBookingId || '',
            respondentName: '',
            previewFiles: [],

            init() {
                if (this.selectedBookingId) {
                    this.onBookingChange();
                }
            },

            onBookingChange() {
                const booking = this.bookings.find(b => b.id == this.selectedBookingId);

                if (!booking) {
                    this.respondentName = '';
                    return;
                }

                if (booking.owner_id === this.userId) {
                    this.respondentName = booking.sitter_name || '{{ __('messages.cp_role_sitter') }}';
                } else {
                    this.respondentName = booking.owner_name || '{{ __('messages.cp_role_owner') }}';
                }
            },

            handleFiles(event) {
                const newFiles = Array.from(event.target.files);
                if (!newFiles.length) return;

                this.previewFiles = [...this.previewFiles, ...newFiles];

                try {
                    const dt = new DataTransfer();
                    this.previewFiles.forEach(f => dt.items.add(f));
                    event.target.files = dt.files;
                } catch (e) {
                    console.warn('DataTransfer not supported', e);
                }
            },

            removeFile(index) {
                this.previewFiles.splice(index, 1);

                const input = document.getElementById('evidence-upload');
                if (input) {
                    try {
                        const dt = new DataTransfer();
                        this.previewFiles.forEach(f => dt.items.add(f));
                        input.files = dt.files;
                    } catch (e) {
                        console.warn('DataTransfer not supported', e);
                    }
                }
            },

            clearFiles() {
                this.previewFiles = [];
                const input = document.getElementById('evidence-upload');
                if (input) input.value = '';
            },

            formatSize(bytes) {
                if (bytes < 1024) return bytes + ' B';
                if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
                return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
            }
        }
    }
    </script>
    @endpush
</x-app-layout>