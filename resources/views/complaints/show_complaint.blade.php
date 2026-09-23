<x-app-layout>
    <x-slot name="header">
        <div x-data class="flex items-center gap-3">
            <a href="{{ route('complaints.index') }}"
               class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                    {{ __('messages.cp_show_title') }}
                </h1>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                    CMP-{{ str_pad($complaint->id, 6, '0', STR_PAD_LEFT) }}
                </p>
            </div>
        </div>
    </x-slot>

    @php
        $booking      = $complaint->booking;
        $pet          = $booking?->pet;
        $complainant  = $complaint->complainant;
        $respondent   = $complaint->respondent;

        $complainantName = trim(($complainant?->f_name ?? '') . ' ' . ($complainant?->l_name ?? '')) ?: 'User';
        $respondentName  = trim(($respondent?->f_name ?? '') . ' ' . ($respondent?->l_name ?? '')) ?: 'User';

        $complainantRole = ($booking && $booking->owner_id === $complaint->complainant_id) ? __('messages.cp_role_owner') : __('messages.cp_role_sitter');
        $respondentRole  = ($booking && $booking->owner_id === $complaint->respondent_id) ? __('messages.cp_role_owner') : __('messages.cp_role_sitter');

        $statusMap = [
            'pending'      => ['bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400', __('messages.cp_filter_pending')],
            'under_review' => ['bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',   __('messages.cp_filter_under_review')],
            'resolved'     => ['bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',__('messages.cp_filter_resolved')],
            'dismissed'    => ['bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',        __('messages.cp_filter_dismissed')],
        ];
        [$statusClass, $statusLabel] = $statusMap[$complaint->status] ?? $statusMap['pending'];

        $typeLabel = match($complaint->complaint_type) {
            'missed_visit'  => __('messages.complaint_missed_visit'),
            'poor_service'  => __('messages.complaint_poor_service'),
            'no_proof'      => __('messages.complaint_no_proof'),
            'rude_behavior' => __('messages.complaint_rude_behavior'),
            'others'        => __('messages.complaint_others'),
            default         => ucfirst($complaint->complaint_type),
        };

        $evidencePaths = $complaint->evidence_paths ?? [];
        if (!is_array($evidencePaths)) {
            $evidencePaths = [];
        }

        $getFileType = function ($path) {
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']))  return 'image';
            if (in_array($ext, ['mp4', 'mov', 'avi', 'webm']))           return 'video';
            return 'document';
        };
    @endphp

    <div class="py-4 sm:py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200"
         x-data="{
             lightboxOpen: false,
             lightboxSrc: '',
             lightboxType: 'image',

             openLightbox(src, type) {
                 this.lightboxSrc = src;
                 this.lightboxType = type;
                 this.lightboxOpen = true;
             }
         }">
        <div class="w-full sm:max-w-4xl mx-auto px-2 sm:px-16 lg:px-24">

            {{-- SUMMARY CARDS --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-6">
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.cp_card_complaint_id') }}</p>
                    <h3 class="font-bold text-base sm:text-lg mt-1 text-[#1B3B36] dark:text-white truncate">
                        CMP-{{ str_pad($complaint->id, 6, '0', STR_PAD_LEFT) }}
                    </h3>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.cp_card_booking') }}</p>
                    <h3 class="font-bold text-base sm:text-lg mt-1 text-[#1B3B36] dark:text-white truncate">
                        {{ $booking->booking_reference ?? 'N/A' }}
                    </h3>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.cp_card_type') }}</p>
                    <h3 class="font-bold text-base sm:text-lg mt-1 text-[#1B3B36] dark:text-white truncate">
                        {{ $typeLabel }}
                    </h3>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.cp_col_status') ?? __('messages.cp_filter_all') }}</p>
                    <span class="inline-block mt-1 {{ $statusClass }} px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full text-xs sm:text-sm font-semibold">
                        {{ $statusLabel }}
                    </span>
                </div>
            </div>

            {{-- PARTIES INVOLVED --}}
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6 mb-4 sm:mb-6">
                <h2 class="font-bold text-base sm:text-lg text-[#1B3B36] dark:text-white mb-4">{{ __('messages.cp_parties_header') }}</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Complainant --}}
                    <div class="p-4 rounded-xl bg-neutral-50 dark:bg-neutral-800/50 border border-gray-100 dark:border-neutral-700">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-neutral-500 dark:text-neutral-400 mb-2">
                            {{ __('messages.cp_label_complainant') }}
                        </p>
                        <div class="flex items-center gap-3">
                            @if($complainant && $complainant->profile_photo && file_exists(public_path('storage/' . $complainant->profile_photo)))
                                <img src="{{ asset('storage/' . $complainant->profile_photo) }}"
                                     alt="{{ $complainantName }}"
                                     class="w-10 h-10 rounded-full object-cover border border-primary/20 shrink-0">
                            @else
                                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-sm shrink-0">
                                    {{ strtoupper(substr($complainant?->f_name ?? 'U', 0, 1)) }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <p class="font-bold text-sm text-[#1B3B36] dark:text-white truncate">{{ $complainantName }}</p>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ $complainantRole }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Respondent --}}
                    <div class="p-4 rounded-xl bg-red-50 dark:bg-red-950/20 border border-red-100 dark:border-red-900/50">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-red-600 dark:text-red-400 mb-2">
                            {{ __('messages.cp_label_respondent') }}
                        </p>
                        <div class="flex items-center gap-3">
                            @if($respondent && $respondent->profile_photo && file_exists(public_path('storage/' . $respondent->profile_photo)))
                                <img src="{{ asset('storage/' . $respondent->profile_photo) }}"
                                     alt="{{ $respondentName }}"
                                     class="w-10 h-10 rounded-full object-cover border border-red-300 dark:border-red-800 shrink-0">
                            @else
                                <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center text-red-600 dark:text-red-400 font-bold text-sm shrink-0">
                                    {{ strtoupper(substr($respondent?->f_name ?? 'U', 0, 1)) }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <p class="font-bold text-sm text-[#1B3B36] dark:text-white truncate">{{ $respondentName }}</p>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ $respondentRole }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if($booking)
                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.cp_label_booking_ref') }}</p>
                                <p class="font-bold text-sm text-[#1B3B36] dark:text-white">
                                    {{ $booking->booking_reference }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.cp_label_pet') }}</p>
                                <p class="font-bold text-sm text-[#1B3B36] dark:text-white">
                                    🐾 {{ $pet->name ?? 'Pet' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('messages.cp_label_filed_on') }}</p>
                                <p class="font-bold text-sm text-[#1B3B36] dark:text-white">
                                    {{ $complaint->created_at->format('M j, Y g:i A') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- DESCRIPTION --}}
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6 mb-4 sm:mb-6">
                <h2 class="font-bold text-base sm:text-lg text-[#1B3B36] dark:text-white mb-3">{{ __('messages.cp_desc_header') }}</h2>

                <div class="p-4 rounded-xl bg-neutral-50 dark:bg-neutral-800/50 border border-gray-100 dark:border-neutral-700">
                    <p class="text-sm text-neutral-700 dark:text-neutral-300 leading-relaxed whitespace-pre-line">{{ $complaint->description }}</p>
                </div>
            </div>

            {{-- EVIDENCE --}}
            @if(count($evidencePaths) > 0)
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6 mb-4 sm:mb-6">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="font-bold text-base sm:text-lg text-[#1B3B36] dark:text-white">{{ __('messages.cp_evidence_header') }}</h2>
                        <span class="text-xs text-neutral-500 dark:text-neutral-400 font-medium">
                            {{ __('messages.cp_evidence_count', ['count' => count($evidencePaths)]) }}
                        </span>
                    </div>

                    <p class="text-[11px] text-neutral-500 dark:text-neutral-400 mb-3">
                        {{ __('messages.cp_evidence_click') }}
                    </p>

                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                        @foreach($evidencePaths as $idx => $path)
                            @php
                                $type = $getFileType($path);
                                $url = asset('storage/' . $path);
                            @endphp

                            @if($type === 'image')
                                <button type="button"
                                        @click="openLightbox('{{ $url }}', 'image')"
                                        class="group relative aspect-square rounded-xl overflow-hidden border-2 border-gray-200 dark:border-neutral-700 hover:border-primary transition">
                                    <img src="{{ $url }}"
                                         alt="Evidence {{ $idx + 1 }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition">
                                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition flex items-center justify-center pointer-events-none">
                                        <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-100 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/>
                                        </svg>
                                    </div>
                                </button>
                            @elseif($type === 'video')
                                <button type="button"
                                        @click="openLightbox('{{ $url }}', 'video')"
                                        class="group relative aspect-square rounded-xl overflow-hidden border-2 border-gray-200 dark:border-neutral-700 hover:border-primary transition bg-neutral-900">
                                    <video class="w-full h-full object-cover" muted>
                                        <source src="{{ $url }}">
                                    </video>
                                    <div class="absolute inset-0 bg-black/40 group-hover:bg-black/60 transition flex items-center justify-center">
                                        <div class="w-10 h-10 rounded-full bg-white/90 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-neutral-900 ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M8 5v14l11-7z"/>
                                            </svg>
                                        </div>
                                    </div>
                                </button>
                            @else
                                <a href="{{ $url }}"
                                   target="_blank"
                                   class="group relative aspect-square rounded-xl overflow-hidden border-2 border-gray-200 dark:border-neutral-700 hover:border-primary transition bg-neutral-100 dark:bg-neutral-800 flex flex-col items-center justify-center p-2">
                                    <svg class="w-8 h-8 text-neutral-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-[9px] text-neutral-500 dark:text-neutral-400 truncate w-full text-center">
                                        {{ strtoupper(pathinfo($path, PATHINFO_EXTENSION)) }}
                                    </p>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ADMIN NOTES + RESOLUTION --}}
            @if($complaint->admin_notes || $complaint->resolution)
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-6 mb-4 sm:mb-6">
                    <h2 class="font-bold text-base sm:text-lg text-[#1B3B36] dark:text-white mb-3">{{ __('messages.cp_admin_review') }}</h2>

                    @if($complaint->admin_notes)
                        <div class="mb-3">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-neutral-500 dark:text-neutral-400 mb-1">
                                {{ __('messages.cp_admin_notes') }}
                            </p>
                            <div class="p-3 rounded-xl bg-blue-50 dark:bg-blue-950/20 border border-blue-200 dark:border-blue-800">
                                <p class="text-sm text-blue-700 dark:text-blue-300 leading-relaxed whitespace-pre-line">{{ $complaint->admin_notes }}</p>
                            </div>
                        </div>
                    @endif

                    @if($complaint->resolution)
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-neutral-500 dark:text-neutral-400 mb-1">
                                {{ __('messages.cp_resolution') }}
                            </p>
                            <div class="p-3 rounded-xl bg-green-50 dark:bg-green-950/20 border border-green-200 dark:border-green-800">
                                <p class="text-sm text-green-700 dark:text-green-300 leading-relaxed whitespace-pre-line">{{ $complaint->resolution }}</p>
                                @if($complaint->resolved_at)
                                    <p class="text-[10px] text-green-600 dark:text-green-400 mt-2">
                                        {{ __('messages.cp_resolved_on', ['date' => $complaint->resolved_at->format('M j, Y g:i A')]) }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- STATUS INFO BOX --}}
            @if($complaint->status === 'pending')
                <div class="bg-amber-50 dark:bg-amber-950/20 rounded-2xl border border-amber-200 dark:border-amber-800/50 p-4 sm:p-5 mb-4 sm:mb-6">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-bold text-amber-800 dark:text-amber-300">{{ __('messages.cp_status_pending_title') }}</p>
                            <p class="text-xs text-amber-700 dark:text-amber-400 mt-0.5">
                                {{ __('messages.cp_status_pending_desc') }}
                            </p>
                        </div>
                    </div>
                </div>
            @elseif($complaint->status === 'under_review')
                <div class="bg-blue-50 dark:bg-blue-950/20 rounded-2xl border border-blue-200 dark:border-blue-800/50 p-4 sm:p-5 mb-4 sm:mb-6">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-bold text-blue-800 dark:text-blue-300">{{ __('messages.cp_status_review_title') }}</p>
                            <p class="text-xs text-blue-700 dark:text-blue-400 mt-0.5">
                                {{ __('messages.cp_status_review_desc') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- BACK BUTTON --}}
            <div class="flex flex-col-reverse sm:flex-row sm:items-center gap-2 sm:gap-3 pt-4 border-t border-gray-100 dark:border-neutral-800">
                <a href="{{ route('complaints.index') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 sm:px-8 sm:py-3.5 border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-sm rounded-xl hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                    {{ __('messages.cp_back_to_complaints') }}
                </a>
            </div>

        </div>

        {{-- LIGHTBOX MODAL --}}
        <div x-show="lightboxOpen"
             x-cloak
             x-transition.opacity
             @click="lightboxOpen = false"
             @keydown.escape.window="lightboxOpen = false"
             class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/90 backdrop-blur-sm">

            <button type="button"
                    @click.stop="lightboxOpen = false"
                    class="absolute top-4 right-4 p-2 rounded-full bg-white/10 hover:bg-white/20 text-white transition z-10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <template x-if="lightboxType === 'image'">
                <img :src="lightboxSrc"
                     @click.stop
                     alt="Evidence preview"
                     class="max-w-[90vw] max-h-[90vh] object-contain rounded-lg shadow-2xl">
            </template>

            <template x-if="lightboxType === 'video'">
                <video :src="lightboxSrc"
                       @click.stop
                       controls
                       autoplay
                       class="max-w-[90vw] max-h-[90vh] rounded-lg shadow-2xl">
                </video>
            </template>
        </div>
    </div>
</x-app-layout>