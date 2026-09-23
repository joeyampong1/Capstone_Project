<x-app-layout>
    {{-- ========================================== --}}
    {{-- HEADER SLOT – sticky                       --}}
    {{-- ========================================== --}}
    <x-slot name="header">
        <div class="sticky top-0 z-10 bg-white dark:bg-neutral-900 flex items-center gap-3">
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                    {{ __('messages.mp_index_title') }}
                </h1>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.mp_index_subtitle') }}</p>
            </div>
        </div>
    </x-slot>

    @php
        // Build ang data array para ma-pass sa Alpine
        $petsData = $pets->map(function ($pet) {
            $emoji = match($pet->petType?->name ?? '') {
                'Dog'     => '🐶',
                'Cat'     => '🐱',
                'Bird'    => '🐦',
                'Rabbit'  => '🐰',
                'Hamster' => '🐹',
                'Fish'    => '🐟',
                'Reptile' => '🦎',
                default   => '🐾',
            };

            $hasPhoto = $pet->photo_path && file_exists(public_path('storage/' . $pet->photo_path));

            return [
                'id'                   => $pet->id,
                'name'                 => $pet->name,
                'type'                 => $pet->petType?->name ?? __('messages.mp_unknown'),
                'breed'                => $pet->breed,
                'age'                  => $pet->age,
                'size'                 => $pet->size,
                'temperament'          => $pet->temperament,
                'weight'               => $pet->weight,
                'weight_unit'          => $pet->weight_unit,
                'height'               => $pet->height,
                'height_unit'          => $pet->height_unit,
                'length'               => $pet->length,
                'length_unit'          => $pet->length_unit,
                'width'                => $pet->width,
                'width_unit'           => $pet->width_unit,
                'special_needs'        => $pet->special_needs,
                'medical_conditions'   => $pet->medical_conditions,
                'dietary_restrictions' => $pet->dietary_restrictions,
                'photo_url'            => $hasPhoto ? asset('storage/' . $pet->photo_path) : null,
                'emoji'                => $emoji,
            ];
        })->values();
    @endphp

    {{-- ========================================== --}}
    {{-- MAIN CONTENT                              --}}
    {{-- ========================================== --}}
    <div class="py-4 sm:py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200">
        <div class="w-full sm:max-w-4xl mx-auto px-2 sm:px-16 lg:px-24"
             x-data="petsGrid({{ Js::from($petsData) }})">

            {{-- ========================================== --}}
            {{-- PET GRID                                  --}}
            {{-- ========================================== --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">

                @forelse($pets as $pet)
                    @php
                        $emoji = match($pet->petType?->name ?? '') {
                            'Dog'     => '🐶',
                            'Cat'     => '🐱',
                            'Bird'    => '🐦',
                            'Rabbit'  => '🐰',
                            'Hamster' => '🐹',
                            'Fish'    => '🐟',
                            'Reptile' => '🦎',
                            default   => '🐾',
                        };
                        $hasPhoto = $pet->photo_path && file_exists(public_path('storage/' . $pet->photo_path));
                    @endphp

                    {{-- ========================================== --}}
                    {{-- PET CARD (clickable)                       --}}
                    {{-- ========================================== --}}
                    <div @click="openDetail({{ $pet->id }})"
                         class="bg-white dark:bg-neutral-900 rounded-2xl sm:rounded-3xl shadow-sm border border-gray-100 dark:border-neutral-800 overflow-hidden hover:shadow-md hover:border-primary/30 transition cursor-pointer group">

                        {{-- Photo / Emoji banner --}}
                        <div class="relative h-36 sm:h-40 bg-gradient-to-r from-primary-100/30 to-accent-100/30 dark:from-primary-900/20 dark:to-accent-900/20">
                            @if($hasPhoto)
                                <img src="{{ asset('storage/' . $pet->photo_path) }}"
                                     alt="{{ $pet->name }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="absolute inset-0 flex items-center justify-center text-5xl">
                                    {{ $emoji }}
                                </div>
                            @endif

                            {{-- Hover overlay hint --}}
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition flex items-center justify-center opacity-0 group-hover:opacity-100">
                                <span class="px-3 py-1.5 rounded-full bg-white/90 dark:bg-neutral-900/90 text-[10px] font-bold text-neutral-700 dark:text-neutral-200 shadow-md">
                                    {{ __('messages.mp_click_to_view') }}
                                </span>
                            </div>
                        </div>

                        {{-- Body --}}
                        <div class="p-3.5 sm:p-4">

                            {{-- Name + Actions --}}
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <h3 class="font-black text-[#1B3B36] dark:text-white text-base sm:text-lg truncate">
                                        {{ $pet->name }}
                                    </h3>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400 truncate">
                                        {{ $pet->petType?->name ?? __('messages.mp_unknown') }}
                                        @if($pet->breed)
                                            • {{ $pet->breed }}
                                        @endif
                                        • {{ $pet->age }} {{ __('messages.mp_yrs') }}
                                        • {{ ucfirst($pet->size) }}
                                    </p>
                                </div>

                                <div class="flex items-center gap-1 shrink-0">
                                    {{-- Edit --}}
                                    <a href="{{ route('mypets.edit', $pet) }}"
                                       @click.stop
                                       class="p-1.5 rounded-lg text-neutral-400 hover:text-primary hover:bg-primary/10 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>

                                    {{-- Delete --}}
                                    <button type="button"
                                            @click.stop="$dispatch('open-delete-modal', { petId: {{ $pet->id }}, petName: '{{ $pet->name }}' })"
                                            class="p-1.5 rounded-lg text-neutral-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Badges --}}
                            <div class="flex flex-wrap gap-1.5 mt-2">
                                {{-- Breed badge --}}
                                @if($pet->breed)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-purple-50 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400">
                                        🐾 {{ $pet->breed }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-neutral-100 text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400 italic">
                                        {{ __('messages.mp_breed_not_set') }}
                                    </span>
                                @endif

                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                                    {{ ucfirst($pet->temperament) }}
                                </span>

                                @if($pet->dietary_restrictions && $pet->dietary_restrictions !== 'none')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-green-50 text-green-600 dark:bg-green-900/30 dark:text-green-400">
                                        {{ str_replace('_', ' ', ucfirst($pet->dietary_restrictions)) }}
                                    </span>
                                @endif
                            </div>

                            {{-- Special notes --}}
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-2 line-clamp-2">
                                @if($pet->special_needs)
                                    {{ $pet->special_needs }}
                                @elseif($pet->medical_conditions)
                                    {{ $pet->medical_conditions }}
                                @else
                                    {{ __('messages.mp_no_special_notes') }}
                                @endif
                            </p>

                        </div>
                    </div>
                @empty
                    {{-- Empty state --}}
                    <div class="sm:col-span-2 lg:col-span-3 bg-white dark:bg-neutral-900 rounded-2xl sm:rounded-3xl shadow-sm sm:shadow-xl border border-gray-200/60 dark:border-neutral-800 p-8 sm:p-12 text-center">
                        <svg class="w-16 h-16 mx-auto text-neutral-300 dark:text-neutral-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                        </svg>
                        <h3 class="text-xl font-black text-[#1B3B36] dark:text-white">{{ __('messages.mp_no_pets_title') }}</h3>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.mp_no_pets_desc') }}</p>
                        <a href="{{ route('mypets.create') }}"
                           class="inline-block mt-4 px-6 py-3 bg-primary text-white font-bold text-sm rounded-xl hover:bg-primary-600 transition">
                            {{ __('messages.mp_add_first_pet') }}
                        </a>
                    </div>
                @endforelse

                {{-- Add New Pet Card --}}
                <a href="{{ route('mypets.create') }}"
                   class="bg-white dark:bg-neutral-900 rounded-2xl sm:rounded-3xl border-2 border-dashed border-gray-300 dark:border-neutral-700 hover:border-primary hover:bg-primary/5 transition flex flex-col items-center justify-center min-h-[200px] sm:min-h-[240px] p-5 sm:p-6 group">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center text-neutral-400 group-hover:bg-primary/10 group-hover:text-primary transition">
                        <svg class="w-7 h-7 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <h3 class="mt-3 font-bold text-neutral-600 dark:text-neutral-400 group-hover:text-primary transition">
                        {{ __('messages.mp_add_new_pet') }}
                    </h3>
                    <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-0.5">
                        {{ __('messages.mp_add_new_pet_desc') }}
                    </p>
                </a>

            </div>

            {{-- ========================================== --}}
            {{-- PET DETAIL MODAL                           --}}
            {{-- ========================================== --}}
            <div x-show="showDetail"
                 x-cloak
                 x-transition.opacity
                 class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-black/50 backdrop-blur-sm overflow-y-auto"
                 @click.away="showDetail = false">

                <div class="bg-white dark:bg-neutral-900 rounded-2xl sm:rounded-3xl shadow-2xl border border-gray-200 dark:border-neutral-800 max-w-2xl w-full max-h-[92vh] overflow-y-auto"
                     @click.stop
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100">

                    {{-- Header photo --}}
                    <div class="relative h-48 sm:h-56 bg-gradient-to-r from-primary-100/40 to-accent-100/40 dark:from-primary-900/30 dark:to-accent-900/30">

                        <template x-if="selected.photo_url">
                            <img :src="selected.photo_url"
                                 :alt="selected.name"
                                 class="w-full h-full object-cover">
                        </template>

                        <template x-if="!selected.photo_url">
                            <div class="absolute inset-0 flex items-center justify-center text-7xl">
                                <span x-text="selected.emoji || '🐾'"></span>
                            </div>
                        </template>

                        {{-- Close button --}}
                        <button type="button"
                                @click="showDetail = false"
                                class="absolute top-3 right-3 w-8 h-8 rounded-full bg-black/40 hover:bg-black/60 backdrop-blur-sm text-white flex items-center justify-center transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Body --}}
                    <div class="p-5 sm:p-6">

                        {{-- Name + Type --}}
                        <div class="flex items-start justify-between gap-3 mb-4">
                            <div class="min-w-0">
                                <h2 class="font-black text-2xl text-[#1B3B36] dark:text-white truncate"
                                    x-text="selected.name"></h2>
                                <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-0.5">
                                    <span x-text="selected.type"></span>
                                    <template x-if="selected.breed">
                                        <span> • <span x-text="selected.breed"></span></span>
                                    </template>
                                </p>
                            </div>

                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400 shrink-0"
                                  x-text="selected.temperament ? selected.temperament.charAt(0).toUpperCase() + selected.temperament.slice(1) : ''"></span>
                        </div>

                        {{-- Info grid --}}
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">

                            <div class="p-3 rounded-xl bg-neutral-50 dark:bg-neutral-800/50 border border-gray-100 dark:border-neutral-800">
                                <p class="text-[10px] uppercase tracking-wider text-neutral-400 font-bold mb-0.5">{{ __('messages.mp_age') }}</p>
                                <p class="font-bold text-[#1B3B36] dark:text-white text-sm">
                                    <span x-text="selected.age"></span> <span class="text-xs text-neutral-500">{{ __('messages.mp_yrs') }}</span>
                                </p>
                            </div>

                            <div class="p-3 rounded-xl bg-neutral-50 dark:bg-neutral-800/50 border border-gray-100 dark:border-neutral-800">
                                <p class="text-[10px] uppercase tracking-wider text-neutral-400 font-bold mb-0.5">{{ __('messages.mp_size') }}</p>
                                <p class="font-bold text-[#1B3B36] dark:text-white text-sm capitalize" x-text="selected.size"></p>
                            </div>

                            <div class="p-3 rounded-xl bg-neutral-50 dark:bg-neutral-800/50 border border-gray-100 dark:border-neutral-800">
                                <p class="text-[10px] uppercase tracking-wider text-neutral-400 font-bold mb-0.5">{{ __('messages.mp_breed') }}</p>
                                <p class="font-bold text-[#1B3B36] dark:text-white text-sm truncate"
                                   x-text="selected.breed || '—'"></p>
                            </div>

                            <div class="p-3 rounded-xl bg-neutral-50 dark:bg-neutral-800/50 border border-gray-100 dark:border-neutral-800">
                                <p class="text-[10px] uppercase tracking-wider text-neutral-400 font-bold mb-0.5">{{ __('messages.mp_diet') }}</p>
                                <p class="font-bold text-[#1B3B36] dark:text-white text-sm truncate"
                                   x-text="selected.dietary_restrictions && selected.dietary_restrictions !== 'none'
                                            ? selected.dietary_restrictions.replace(/_/g, ' ')
                                            : @js(__('messages.mp_normal'))"></p>
                            </div>

                        </div>

                        {{-- Measurements --}}
                        <template x-if="selected.weight || selected.height || selected.length || selected.width">
                            <div class="mb-4">
                                <h3 class="text-xs font-black uppercase tracking-wider text-neutral-400 mb-2">
                                    {{ __('messages.mp_measurements') }}
                                </h3>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">

                                    <template x-if="selected.weight">
                                        <div class="p-2.5 rounded-lg bg-neutral-50 dark:bg-neutral-800/50 border border-gray-100 dark:border-neutral-800">
                                            <p class="text-[10px] text-neutral-400 font-semibold">{{ __('messages.mp_weight') }}</p>
                                            <p class="font-bold text-sm text-[#1B3B36] dark:text-white">
                                                <span x-text="selected.weight"></span>
                                                <span x-text="selected.weight_unit || ''"></span>
                                            </p>
                                        </div>
                                    </template>

                                    <template x-if="selected.height">
                                        <div class="p-2.5 rounded-lg bg-neutral-50 dark:bg-neutral-800/50 border border-gray-100 dark:border-neutral-800">
                                            <p class="text-[10px] text-neutral-400 font-semibold">{{ __('messages.mp_height') }}</p>
                                            <p class="font-bold text-sm text-[#1B3B36] dark:text-white">
                                                <span x-text="selected.height"></span>
                                                <span x-text="selected.height_unit || ''"></span>
                                            </p>
                                        </div>
                                    </template>

                                    <template x-if="selected.length">
                                        <div class="p-2.5 rounded-lg bg-neutral-50 dark:bg-neutral-800/50 border border-gray-100 dark:border-neutral-800">
                                            <p class="text-[10px] text-neutral-400 font-semibold">{{ __('messages.mp_length') }}</p>
                                            <p class="font-bold text-sm text-[#1B3B36] dark:text-white">
                                                <span x-text="selected.length"></span>
                                                <span x-text="selected.length_unit || ''"></span>
                                            </p>
                                        </div>
                                    </template>

                                    <template x-if="selected.width">
                                        <div class="p-2.5 rounded-lg bg-neutral-50 dark:bg-neutral-800/50 border border-gray-100 dark:border-neutral-800">
                                            <p class="text-[10px] text-neutral-400 font-semibold">{{ __('messages.mp_width') }}</p>
                                            <p class="font-bold text-sm text-[#1B3B36] dark:text-white">
                                                <span x-text="selected.width"></span>
                                                <span x-text="selected.width_unit || ''"></span>
                                            </p>
                                        </div>
                                    </template>

                                </div>
                            </div>
                        </template>

                        {{-- Special Needs --}}
                        <template x-if="selected.special_needs">
                            <div class="mb-3">
                                <h3 class="text-xs font-black uppercase tracking-wider text-neutral-400 mb-1.5">
                                    {{ __('messages.mp_special_needs') }}
                                </h3>
                                <p class="text-sm text-neutral-600 dark:text-neutral-300 leading-relaxed p-3 rounded-xl bg-amber-50 dark:bg-amber-950/20 border border-amber-100 dark:border-amber-900/50"
                                   x-text="selected.special_needs"></p>
                            </div>
                        </template>

                        {{-- Medical Conditions --}}
                        <template x-if="selected.medical_conditions">
                            <div class="mb-3">
                                <h3 class="text-xs font-black uppercase tracking-wider text-neutral-400 mb-1.5">
                                    {{ __('messages.mp_medical_conditions') }}
                                </h3>
                                <p class="text-sm text-neutral-600 dark:text-neutral-300 leading-relaxed p-3 rounded-xl bg-red-50 dark:bg-red-950/20 border border-red-100 dark:border-red-900/50"
                                   x-text="selected.medical_conditions"></p>
                            </div>
                        </template>

                        {{-- Action buttons --}}
                        <div class="flex flex-col-reverse sm:flex-row gap-2 pt-4 mt-4 border-t border-gray-100 dark:border-neutral-800">
                            <button type="button"
                                    @click="showDetail = false"
                                    class="w-full sm:w-auto px-5 py-2.5 rounded-xl border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-sm hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                                {{ __('messages.mp_close') }}
                            </button>

                            <a :href="`/mypets/${selected.id}/edit`"
                               class="w-full sm:flex-1 inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-primary hover:bg-primary-600 text-white font-bold text-sm shadow-md hover:shadow-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                {{ __('messages.mp_edit_pet') }}
                            </a>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ========================================== --}}
    {{-- DELETE CONFIRMATION MODAL                  --}}
    {{-- ========================================== --}}
    <div x-data="{ showModal: false, petId: null, petName: '' }"
         @open-delete-modal.window="showModal = true; petId = $event.detail.petId; petName = $event.detail.petName"
         x-show="showModal"
         x-cloak>

        <div class="fixed inset-0 bg-black/50 z-[60] flex items-center justify-center p-4"
             @click.self="showModal = false">

            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-xl max-w-sm w-full p-5 sm:p-6">

                <div class="flex items-start gap-3 mb-3">
                    <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center text-red-600 dark:text-red-400 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-lg font-black text-[#1B3B36] dark:text-white">{{ __('messages.mp_delete_title') }}</h3>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">
                            {{ __('messages.mp_delete_confirm') }} <strong x-text="petName"></strong>? {{ __('messages.mp_delete_warning') }}
                        </p>
                    </div>
                </div>

                <div class="mt-4 flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
                    <button type="button"
                            @click="showModal = false"
                            class="w-full sm:w-auto px-4 py-2 text-sm font-bold text-neutral-600 dark:text-neutral-300 border border-gray-200 dark:border-neutral-700 rounded-xl hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                        {{ __('messages.mp_cancel') }}
                    </button>

                    <form :action="'/mypets/' + petId" method="POST" class="w-full sm:w-auto">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full sm:w-auto px-4 py-2 text-sm font-bold text-white bg-red-600 hover:bg-red-700 rounded-xl transition">
                            {{ __('messages.mp_delete') }}
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- ALPINE FUNCTION                            --}}
    {{-- ========================================== --}}
    @push('scripts')
    <script>
        function petsGrid(petsData) {
            return {
                pets: petsData || [],
                selectedId: null,
                showDetail: false,

                get selected() {
                    return this.pets.find(p => p.id === this.selectedId) || {};
                },

                openDetail(id) {
                    this.selectedId = id;
                    this.showDetail = true;
                }
            }
        }
    </script>
    @endpush
</x-app-layout>