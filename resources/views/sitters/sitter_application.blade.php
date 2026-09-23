<x-app-layout>
    <!-- ========================================== -->
    <!-- HEADER SLOT                               -->
    <!-- ========================================== -->
    <x-slot name="header">
        <div>
            <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                {{ __('messages.sa_title') }}
            </h1>
            <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.sa_subtitle') }}</p>
        </div>
    </x-slot>

    <!-- ========================================== -->
    <!-- MAIN CONTENT                              -->
    <!-- ========================================== -->
    <div class="py-4 sm:py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200">
        <div class="w-full sm:max-w-3xl mx-auto px-2 sm:px-16 lg:px-24">

            @php
                $sitterProfile = auth()->user()->sitterProfile ?? null;
                $currentPetTypes = $sitterProfile->preferred_pet_types ?? [];
                $currentPetSizes = $sitterProfile->preferred_pet_sizes ?? [];
                $petTypes = \App\Models\PetType::all();
                $status = auth()->user()->sitter_status;
            @endphp

            <!-- Back to Dashboard Link (TOP LEFT) -->
            <div class="mb-4 sm:mb-6">
                <a href="{{ route('dashboard') }}" 
                   class="inline-flex items-center gap-1 text-sm text-neutral-500 hover:text-primary transition font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    {{ __('messages.sa_back_dashboard') }}
                </a>
            </div>

            <!-- MESSAGE ALERTS (Success / Error) -->
            @if(session('status'))
                <div class="mb-4 sm:mb-6 p-3 sm:p-4 rounded-xl border border-green-500 bg-green-50 dark:bg-green-950/20 dark:border-green-700">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl shrink-0">✅</span>
                        <p class="font-bold text-green-700 dark:text-green-400">{{ session('status') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 sm:mb-6 p-3 sm:p-4 rounded-xl border border-red-500 bg-red-50 dark:bg-red-950/20 dark:border-red-700">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl shrink-0">❌</span>
                        <p class="font-bold text-red-700 dark:text-red-400">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <!-- Current Status (if already applied) -->
            @if(auth()->user()->is_sitter)
                <div class="mb-4 sm:mb-6 p-3 sm:p-4 rounded-xl border 
                    {{ $status === 'approved' ? 'border-green-500 bg-green-50 dark:bg-green-950/20 dark:border-green-700' : 
                       ($status === 'pending' ? 'border-yellow-500 bg-yellow-50 dark:bg-yellow-950/20 dark:border-yellow-700' : 
                       'border-red-500 bg-red-50 dark:bg-red-950/20 dark:border-red-700') }}">
                    <div class="flex items-center gap-3">
                        @if($status === 'approved')
                            <span class="text-2xl shrink-0">✅</span>
                            <div>
                                <p class="font-bold text-green-700 dark:text-green-400">{{ __('messages.sa_status_approved_title') }}</p>
                                <p class="text-sm text-green-600 dark:text-green-300">{{ __('messages.sa_status_approved_desc') }}</p>
                            </div>
                        @elseif($status === 'pending')
                            <span class="text-2xl shrink-0">⏳</span>
                            <div>
                                <p class="font-bold text-yellow-700 dark:text-yellow-400">{{ __('messages.sa_status_pending_title') }}</p>
                                <p class="text-sm text-yellow-600 dark:text-yellow-300">{{ __('messages.sa_status_pending_desc') }}</p>
                            </div>
                        @elseif($status === 'rejected')
                            <span class="text-2xl shrink-0">❌</span>
                            <div>
                                <p class="font-bold text-red-700 dark:text-red-400">{{ __('messages.sa_status_rejected_title') }}</p>
                                <p class="text-sm text-red-600 dark:text-red-300">{{ __('messages.sa_status_rejected_desc') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Application Form -->
            <div class="bg-white dark:bg-neutral-900 rounded-2xl sm:rounded-3xl shadow-sm sm:shadow-xl border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-8">
                <h2 class="text-lg font-black text-[#1B3B36] dark:text-white mb-4 sm:mb-6">{{ __('messages.sa_sitter_details') }}</h2>

                <form method="POST" 
                      action="{{ $sitterProfile ? route('sitter.application.update') : route('sitter.application.store') }}" 
                      enctype="multipart/form-data" class="space-y-4 sm:space-y-6">
                    @csrf
                    @if($sitterProfile)
                        @method('PUT')
                    @endif

                    <!-- 1. ABOUT YOU (Bio) -->
                    <div>
                        <label for="bio" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                            {{ __('messages.sa_about_you') }}
                        </label>
                        <textarea id="bio" name="bio" rows="4" 
                            placeholder="{{ __('messages.sa_about_placeholder') }}"
                            class="mt-1 block w-full px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium">{{ old('bio', $sitterProfile->bio ?? '') }}</textarea>
                        <p class="mt-1 text-xs text-neutral-400">{{ __('messages.sa_about_hint') }}</p>
                        <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('bio')" />
                    </div>

                    <!-- 2. YEARS OF EXPERIENCE -->
                    <div>
                        <label for="years_experience" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                            {{ __('messages.sa_years_exp') }}
                        </label>
                        <input id="years_experience" name="years_experience" type="number" step="0.5" min="0" max="50"
                            value="{{ old('years_experience', $sitterProfile->experience_years ?? '') }}"
                            placeholder="{{ __('messages.sa_years_placeholder') }}"
                            class="mt-1 block w-full px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium">
                        <p class="mt-1 text-xs text-neutral-400">{{ __('messages.sa_years_hint') }}</p>
                        <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('years_experience')" />
                    </div>

                    <!-- 3. SITTER TYPE / LEVEL -->
                    <div>
                        <label for="sitter_level" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                            {{ __('messages.sa_sitter_type') }}
                        </label>
                        <select id="sitter_level" name="sitter_level"
                            class="mt-1 block w-full px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium">
                            <option value="1" {{ old('sitter_level', $sitterProfile->sitter_type ?? '1') == 1 ? 'selected' : '' }}>
                                {{ __('messages.sa_st1') }}
                            </option>
                            <option value="2" {{ old('sitter_level', $sitterProfile->sitter_type ?? '') == 2 ? 'selected' : '' }}>
                                {{ __('messages.sa_st2') }}
                            </option>
                            <option value="3" {{ old('sitter_level', $sitterProfile->sitter_type ?? '') == 3 ? 'selected' : '' }}>
                                {{ __('messages.sa_st3') }}
                            </option>
                        </select>
                        <p class="mt-1 text-xs text-neutral-400">{{ __('messages.sa_sitter_type_hint') }}</p>
                        <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('sitter_level')" />
                    </div>

                    <!-- 4. BASE RATE (Rate per Visit) -->
                    <div>
                        <label for="rate_per_visit" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                            {{ __('messages.sa_rate_per_visit') }}
                        </label>
                        <input id="rate_per_visit" name="rate_per_visit" type="number" step="0.01" min="0"
                            value="{{ old('rate_per_visit', $sitterProfile->base_rate ?? '') }}"
                            placeholder="{{ __('messages.sa_rate_placeholder') }}"
                            class="mt-1 block w-full px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium">
                        <p class="mt-1 text-xs text-neutral-400">{{ __('messages.sa_rate_hint') }}</p>
                        <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('rate_per_visit')" />
                    </div>

                    <!-- 5. FOOD PREFERENCE -->
                    <div>
                        <label for="food_preference" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                            {{ __('messages.sa_food_arrangement') }}
                        </label>
                        <select id="food_preference" name="food_preference"
                            class="mt-1 block w-full px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium">
                            <option value="owner_provides" {{ old('food_preference', $sitterProfile->food_preference ?? '') == 'owner_provides' ? 'selected' : '' }}>{{ __('messages.sa_food_owner') }}</option>
                            <option value="sitter_provides" {{ old('food_preference', $sitterProfile->food_preference ?? '') == 'sitter_provides' ? 'selected' : '' }}>{{ __('messages.sa_food_sitter') }}</option>
                            <option value="flexible" {{ old('food_preference', $sitterProfile->food_preference ?? '') == 'flexible' ? 'selected' : '' }}>{{ __('messages.sa_food_flexible') }}</option>
                        </select>
                        <p class="mt-1 text-xs text-neutral-400">{{ __('messages.sa_food_arrangement_hint') }}</p>
                        <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('food_preference')" />
                    </div>

                    <!-- 6. FOOD BUDGET (Conditional) -->
                    <div id="food_budget_container" 
                         class="{{ old('food_preference', $sitterProfile->food_preference ?? '') == 'sitter_provides' ? '' : 'hidden' }}">
                        <label for="food_budget" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                            {{ __('messages.sa_food_budget') }}
                        </label>
                        <input id="food_budget" name="food_budget" type="number" step="0.01" min="0"
                            value="{{ old('food_budget', $sitterProfile->food_budget ?? '100') }}"
                            placeholder="{{ __('messages.sa_food_budget_placeholder') }}"
                            class="mt-1 block w-full px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium">
                        <p class="mt-1 text-xs text-neutral-400">{{ __('messages.sa_food_budget_hint') }}</p>
                        <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('food_budget')" />
                    </div>

                    <!-- ========================================== -->
                    <!-- 7. PREFERRED PET TYPES                     -->
                    <!-- ========================================== -->
                    <div class="pt-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-3">
                            {{ __('messages.sa_pet_types') }}
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3">
                            @foreach($petTypes as $petType)
                                <label class="flex items-center p-2.5 sm:p-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 cursor-pointer hover:border-primary/50 transition">
                                    <input type="checkbox" name="preferred_pet_types[]" value="{{ $petType->id }}" 
                                           class="sr-only peer"
                                           {{ in_array($petType->id, old('preferred_pet_types', $currentPetTypes)) ? 'checked' : '' }}>
                                    <div class="w-full text-center peer-checked:bg-primary/10 peer-checked:border-primary peer-checked:text-primary rounded-lg p-2 border border-transparent transition">
                                        <span class="text-2xl block mb-1">{{ $petType->icon }}</span>
                                        <span class="text-xs font-bold">{{ $petType->name }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        <p class="mt-1 text-xs text-neutral-400">{{ __('messages.sa_pet_types_hint') }}</p>
                    </div>

                    <!-- ========================================== -->
                    <!-- 8. PREFERRED PET SIZES                     -->
                    <!-- ========================================== -->
                    <div class="pt-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-3">
                            {{ __('messages.sa_pet_sizes') }}
                        </label>
                        <div class="flex flex-wrap gap-2.5 sm:gap-3">
                            @foreach(['small', 'medium', 'large', 'giant'] as $size)
                                <label class="inline-flex items-center px-3 py-2 sm:px-4 sm:py-2 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 cursor-pointer hover:border-primary/50 transition">
                                    <input type="checkbox" name="preferred_pet_sizes[]" value="{{ $size }}" 
                                           class="sr-only peer"
                                           {{ in_array($size, old('preferred_pet_sizes', $currentPetSizes)) ? 'checked' : '' }}>
                                    <span class="peer-checked:text-primary font-bold text-sm">
                                        {{ __('messages.sa_size_' . $size) }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        <p class="mt-1 text-xs text-neutral-400">{{ __('messages.sa_pet_sizes_hint') }}</p>
                    </div>

                    <!-- ========================================== -->
                    <!-- 9. PET CAPACITY                            -->
                    <!-- ========================================== -->
                    <div class="pt-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-3">
                            {{ __('messages.sa_pet_capacity') }}
                        </label>
                        <div class="grid grid-cols-2 gap-3 sm:gap-4">
                            <div>
                                <label for="min_pets_capacity" class="block text-[10px] font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400 mb-0.5">
                                    {{ __('messages.sa_min') }}
                                </label>
                                <input id="min_pets_capacity" name="min_pets_capacity" type="number" min="0" max="20"
                                    value="{{ old('min_pets_capacity', $sitterProfile->min_pets_capacity ?? '') }}"
                                    placeholder="{{ __('messages.sa_min_placeholder') }}"
                                    class="mt-1 block w-full px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium">
                                <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('min_pets_capacity')" />
                            </div>
                            <div>
                                <label for="max_pets_capacity" class="block text-[10px] font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400 mb-0.5">
                                    {{ __('messages.sa_max') }}
                                </label>
                                <input id="max_pets_capacity" name="max_pets_capacity" type="number" min="1" max="20"
                                    value="{{ old('max_pets_capacity', $sitterProfile->max_pets_capacity ?? '') }}"
                                    placeholder="{{ __('messages.sa_max_placeholder') }}"
                                    class="mt-1 block w-full px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium">
                                <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('max_pets_capacity')" />
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-neutral-400">{{ __('messages.sa_capacity_hint') }}</p>
                    </div>

                    <!-- 10. UPLOAD CERTIFICATES (Multiple) -->
                    <div x-data="{
                            files: [],
                            handleFiles(e) {
                                const newFiles = Array.from(e.target.files);
                                newFiles.forEach(file => {
                                    this.files.push({
                                        name: file.name,
                                        size: (file.size / 1024).toFixed(2) + ' KB',
                                        isImage: file.type.startsWith('image/'),
                                        preview: null,
                                        file: file
                                    });
                                    if (file.type.startsWith('image/')) {
                                        const reader = new FileReader();
                                        const idx = this.files.length - 1;
                                        reader.onload = (evt) => { this.files[idx].preview = evt.target.result; };
                                        reader.readAsDataURL(file);
                                    }
                                });
                            },
                            removeFile(index) {
                                this.files.splice(index, 1);
                                const dt = new DataTransfer();
                                this.files.forEach(f => dt.items.add(f.file));
                                this.$refs.fileInput.files = dt.files;
                            }
                        }">
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                            {{ __('messages.sa_upload_certs') }}
                        </label>

                        <div class="mt-1 border-2 border-dashed border-gray-300 dark:border-neutral-700 rounded-xl p-4 sm:p-6 text-center hover:border-primary/50 transition">

                            <input type="file"
                                x-ref="fileInput"
                                @change="handleFiles"
                                name="certificates[]"
                                accept=".pdf,.jpg,.jpeg,.png"
                                class="hidden"
                                id="cert-upload"
                                multiple>

                            <label for="cert-upload" x-show="files.length === 0" class="cursor-pointer block">
                                <svg class="w-10 h-10 sm:w-12 sm:h-12 mx-auto text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">
                                    {{ __('messages.sa_upload_certs_click') }}
                                </p>
                                <p class="text-xs text-neutral-400">{{ __('messages.sa_upload_certs_desc') }}</p>
                            </label>

                            <div x-show="files.length > 0" x-cloak class="space-y-3">
                                <label for="cert-upload" class="cursor-pointer inline-flex items-center gap-1 text-xs font-bold text-primary hover:text-primary-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    {{ __('messages.sa_add_more') }}
                                </label>

                                <template x-for="(file, index) in files" :key="index">
                                    <div class="flex items-center justify-between gap-2 sm:gap-3 p-2.5 sm:p-3 bg-white dark:bg-neutral-900 rounded-lg border border-gray-200 dark:border-neutral-700">
                                        <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                                            <template x-if="file.isImage && file.preview">
                                                <img :src="file.preview" alt="Preview" class="w-9 h-9 sm:w-10 sm:h-10 object-cover rounded-md border border-gray-300 shrink-0">
                                            </template>
                                            <template x-if="!file.isImage">
                                                <svg class="w-9 h-9 sm:w-10 sm:h-10 text-neutral-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </template>
                                            <div class="text-left min-w-0">
                                                <p x-text="file.name" class="text-sm font-bold text-neutral-800 dark:text-neutral-200 truncate"></p>
                                                <p x-text="file.size" class="text-xs text-neutral-500"></p>
                                            </div>
                                        </div>
                                        <button type="button" @click="removeFile(index)" class="text-xs font-bold text-red-500 hover:text-red-700 shrink-0">
                                            {{ __('messages.sa_remove') }}
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <p class="mt-1 text-xs text-neutral-400">{{ __('messages.sa_upload_certs_hint') }}</p>
                        <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('certificates')" />
                        <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('certificates.*')" />
                    </div>

                    <!-- ========================================== -->
                    <!-- 11. SUBMIT APPLICATION                     -->
                    <!-- ========================================== -->
                    <div class="pt-4">
                        <button type="submit" 
                                class="w-full flex justify-center items-center px-4 py-3 sm:py-3.5 bg-primary hover:bg-primary-600 text-white text-sm font-black rounded-xl shadow-md hover:shadow-lg active:scale-[0.99] transition-all duration-150">
                            {{ $sitterProfile ? __('messages.sa_update_application') : __('messages.sa_submit_application') }}
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <!-- JavaScript: Toggle Food Budget + File Upload Preview -->
    @push('scripts')
    <script>
    (function () {
        function initSitterForm() {
            var foodPreference = document.getElementById('food_preference');
            var foodBudgetContainer = document.getElementById('food_budget_container');

            if (foodPreference && foodBudgetContainer) {
                foodPreference.addEventListener('change', function () {
                    if (this.value === 'sitter_provides') {
                        foodBudgetContainer.classList.remove('hidden');
                    } else {
                        foodBudgetContainer.classList.add('hidden');
                    }
                });
            }

            var certUpload       = document.getElementById('cert-upload');
            var certUploadLabel  = document.getElementById('cert-upload-label');
            var certPreview      = document.getElementById('cert-preview');
            var certPreviewImg   = document.getElementById('cert-preview-img');
            var certPreviewIcon  = document.getElementById('cert-preview-icon');
            var certPreviewName  = document.getElementById('cert-preview-name');
            var certPreviewSize  = document.getElementById('cert-preview-size');
            var certRemove       = document.getElementById('cert-remove');

            if (!certUpload) return;

            function formatFileSize(size) {
                if (!size) return '0 KB';
                var kb = size / 1024;
                if (kb < 1024) return kb.toFixed(2) + ' KB';
                return (kb / 1024).toFixed(2) + ' MB';
            }

            function resetCertPreview() {
                certUpload.value = '';
                certUploadLabel.classList.remove('hidden');
                certPreview.classList.add('hidden');
                certPreviewImg.src = '';
                certPreviewImg.classList.add('hidden');
                certPreviewIcon.classList.add('hidden');
                certPreviewName.textContent = '';
                certPreviewSize.textContent = '';
            }

            certUpload.addEventListener('change', function (e) {
                var file = e.target.files && e.target.files[0];
                if (!file) {
                    resetCertPreview();
                    return;
                }
                certPreviewName.textContent = file.name;
                certPreviewSize.textContent = formatFileSize(file.size);

                if (file.type && file.type.startsWith('image/')) {
                    var reader = new FileReader();
                    reader.onload = function (evt) {
                        certPreviewImg.src = evt.target.result;
                        certPreviewImg.classList.remove('hidden');
                        certPreviewIcon.classList.add('hidden');
                    };
                    reader.readAsDataURL(file);
                } else {
                    certPreviewImg.src = '';
                    certPreviewImg.classList.add('hidden');
                    certPreviewIcon.classList.remove('hidden');
                }

                certUploadLabel.classList.add('hidden');
                certPreview.classList.remove('hidden');
            });

            if (certRemove) {
                certRemove.addEventListener('click', function () {
                    resetCertPreview();
                });
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initSitterForm);
        } else {
            initSitterForm();
        }
    })();
    </script>
    @endpush

</x-app-layout>