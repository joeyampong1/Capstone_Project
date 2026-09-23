<x-app-layout>
    {{-- ========================================== --}}
    {{-- HEADER SLOT                               --}}
    {{-- ========================================== --}}
    <x-slot name="header">
        <div class="sticky top-0 z-10 bg-white dark:bg-neutral-900 flex items-center gap-3">
            <a href="{{ route('mypets.index') }}"
               class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                    {{ __('messages.mp_edit_title') }}
                </h1>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.mp_edit_subtitle') }}</p>
            </div>
        </div>
    </x-slot>

    {{-- ========================================== --}}
    {{-- MAIN CONTENT                              --}}
    {{-- ========================================== --}}
    <div class="py-4 sm:py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200">
        <div class="w-full sm:max-w-2xl mx-auto px-2 sm:px-16 lg:px-24">

            {{-- ========================================== --}}
            {{-- ERROR SUMMARY                             --}}
            {{-- ========================================== --}}
            @if($errors->any())
                <div class="mb-4 p-4 rounded-xl bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-800">
                    <p class="font-bold text-red-700 dark:text-red-400 mb-1">{{ __('messages.mp_errors_intro') }}</p>
                    <ul class="list-disc list-inside text-xs text-red-600 dark:text-red-400 space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ========================================== --}}
            {{-- FORM CARD                                 --}}
            {{-- ========================================== --}}
            <div class="bg-white dark:bg-neutral-900 rounded-2xl sm:rounded-3xl shadow-sm sm:shadow-xl border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-8">

                <form method="POST"
                      action="{{ route('mypets.update', $pet) }}"
                      enctype="multipart/form-data"
                      class="space-y-5 sm:space-y-6"
                      x-data="petPhotoUpload({
                          existingPhoto: {{ $pet->photo_path ? Js::from(asset('storage/' . $pet->photo_path)) : 'null' }}
                      })">
                    @csrf
                    @method('PUT')

                    {{-- ========================================== --}}
                    {{-- PET PHOTO UPLOAD                          --}}
                    {{-- ========================================== --}}
                    <div>
                        <label class="block text-sm font-bold text-[#1B3B36] dark:text-white mb-2">
                            {{ __('messages.mp_photo_label') }} <span class="text-xs text-neutral-400 font-medium">{{ __('messages.mp_optional') }}</span>
                        </label>

                        <div class="flex items-center gap-3 sm:gap-4">

                            {{-- Preview frame --}}
                            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-neutral-100 dark:bg-neutral-800 border-2 border-dashed border-gray-300 dark:border-neutral-700 flex items-center justify-center text-neutral-400 overflow-hidden shrink-0 relative">

                                {{-- Placeholder icon (kung walay photo at all) --}}
                                <svg x-show="!photoPreview"
                                     x-cloak
                                     class="w-8 h-8 sm:w-10 sm:h-10"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>

                                {{-- Preview image (existing OR bag-o) --}}
                                <img x-show="photoPreview"
                                     x-cloak
                                     :src="photoPreview"
                                     alt="{{ $pet->name }}"
                                     class="w-full h-full object-cover absolute inset-0">

                                {{-- Remove button (kung naay preview) --}}
                                <button type="button"
                                        x-show="photoPreview"
                                        x-cloak
                                        @click="clearPhoto()"
                                        class="absolute top-1 right-1 z-10 w-5 h-5 rounded-full bg-red-500 hover:bg-red-600 text-white flex items-center justify-center shadow-md transition">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            {{-- File input + info --}}
                            <div class="flex-1 min-w-0">
                                <label for="photo_path"
                                       class="inline-flex items-center gap-2 px-3 py-2.5 sm:px-4 sm:py-2.5 bg-neutral-100 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300 font-bold text-sm rounded-xl hover:bg-neutral-200 dark:hover:bg-neutral-700 transition cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span x-text="photoPreview ? @js(__('messages.mp_change_photo')) : @js(__('messages.mp_choose_photo'))"></span>
                                </label>

                                <input type="file"
                                       id="photo_path"
                                       name="photo_path"
                                       class="hidden"
                                       accept="image/*"
                                       @change="handleFile($event)">

                                <p class="text-[10px] text-neutral-400 mt-1"
                                   x-text="photoName || @js($pet->photo_path ? __('messages.mp_photo_hint_existing') : __('messages.mp_photo_hint'))"></p>
                            </div>
                        </div>

                        @error('photo_path')
                            <p class="text-xs text-red-500 mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="border-t border-gray-100 dark:border-neutral-800"></div>

                    {{-- ========================================== --}}
                    {{-- PET BASIC INFO                            --}}
                    {{-- ========================================== --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        {{-- Name --}}
                        <div>
                            <label for="name" class="block text-sm font-bold text-[#1B3B36] dark:text-white mb-1.5">
                                {{ __('messages.mp_name') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" placeholder="{{ __('messages.mp_name_ph') }}"
                                   value="{{ old('name', $pet->name) }}"
                                   class="w-full px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-950 text-sm text-neutral-800 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                        </div>

                        {{-- Type --}}
                        <div>
                            <label for="pet_type_id" class="block text-sm font-bold text-[#1B3B36] dark:text-white mb-1.5">
                                {{ __('messages.mp_type') }} <span class="text-red-500">*</span>
                            </label>
                            <select id="pet_type_id" name="pet_type_id" required
                                    class="w-full px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-950 text-sm text-neutral-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                                <option value="">{{ __('messages.mp_select_type') }}</option>
                                @foreach($petTypes as $petType)
                                    <option value="{{ $petType->id }}" @selected(old('pet_type_id', $pet->pet_type_id) == $petType->id)>
                                        {{ $petType->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Breed --}}
                    <div>
                        <label for="breed" class="block text-sm font-bold text-[#1B3B36] dark:text-white mb-1.5">
                            {{ __('messages.mp_breed') }} <span class="text-xs text-neutral-400 font-medium">{{ __('messages.mp_optional') }}</span>
                        </label>
                        <input type="text" id="breed" name="breed" placeholder="{{ __('messages.mp_breed_ph') }}"
                               value="{{ old('breed', $pet->breed) }}"
                               class="w-full px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-950 text-sm text-neutral-800 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                        {{-- Age --}}
                        <div>
                            <label for="age" class="block text-sm font-bold text-[#1B3B36] dark:text-white mb-1.5">
                                {{ __('messages.mp_age') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="age" name="age" placeholder="{{ __('messages.mp_age_ph') }}" min="0" max="30"
                                   value="{{ old('age', $pet->age) }}"
                                   class="w-full px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-950 text-sm text-neutral-800 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                        </div>

                        {{-- Size --}}
                        <div>
                            <label for="size" class="block text-sm font-bold text-[#1B3B36] dark:text-white mb-1.5">
                                {{ __('messages.mp_size') }} <span class="text-red-500">*</span>
                            </label>
                            <select id="size" name="size"
                                    class="w-full px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-950 text-sm text-neutral-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                                <option value="">{{ __('messages.mp_select_size') }}</option>
                                <option value="small"  @selected(old('size', $pet->size) === 'small')>{{ __('messages.mp_size_small') }}</option>
                                <option value="medium" @selected(old('size', $pet->size) === 'medium')>{{ __('messages.mp_size_medium') }}</option>
                                <option value="large"  @selected(old('size', $pet->size) === 'large')>{{ __('messages.mp_size_large') }}</option>
                            </select>
                        </div>

                        {{-- Temperament --}}
                        <div>
                            <label for="temperament" class="block text-sm font-bold text-[#1B3B36] dark:text-white mb-1.5">
                                {{ __('messages.mp_temperament') }} <span class="text-red-500">*</span>
                            </label>
                            <select id="temperament" name="temperament"
                                    class="w-full px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-950 text-sm text-neutral-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                                <option value="">{{ __('messages.mp_select_temperament') }}</option>
                                <option value="calm"        @selected(old('temperament', $pet->temperament) === 'calm')>{{ __('messages.mp_temp_calm') }}</option>
                                <option value="friendly"    @selected(old('temperament', $pet->temperament) === 'friendly')>{{ __('messages.mp_temp_friendly') }}</option>
                                <option value="playful"     @selected(old('temperament', $pet->temperament) === 'playful')>{{ __('messages.mp_temp_playful') }}</option>
                                <option value="energetic"   @selected(old('temperament', $pet->temperament) === 'energetic')>{{ __('messages.mp_temp_energetic') }}</option>
                                <option value="shy"         @selected(old('temperament', $pet->temperament) === 'shy')>{{ __('messages.mp_temp_shy') }}</option>
                                <option value="independent" @selected(old('temperament', $pet->temperament) === 'independent')>{{ __('messages.mp_temp_independent') }}</option>
                            </select>
                        </div>
                    </div>

                    {{-- ========================================== --}}
                    {{-- MEASUREMENTS                              --}}
                    {{-- ========================================== --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        {{-- Weight --}}
                        <div>
                            <label for="weight" class="block text-sm font-bold text-[#1B3B36] dark:text-white mb-1.5">
                                {{ __('messages.mp_weight') }} <span class="text-xs text-neutral-400 font-medium">{{ __('messages.mp_optional') }}</span>
                            </label>
                            <div class="flex gap-2">
                                <input type="number" step="0.01" id="weight" name="weight" placeholder="{{ __('messages.mp_weight_ph') }}"
                                       value="{{ old('weight', $pet->weight) }}"
                                       class="w-full min-w-0 px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-950 text-sm text-neutral-800 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                                <select name="weight_unit" class="w-16 sm:w-20 px-2 py-2.5 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-950 text-sm text-neutral-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition shrink-0">
                                    <option value="kg"  @selected(old('weight_unit', $pet->weight_unit) === 'kg')>kg</option>
                                    <option value="lbs" @selected(old('weight_unit', $pet->weight_unit) === 'lbs')>lbs</option>
                                </select>
                            </div>
                        </div>

                        {{-- Height --}}
                        <div>
                            <label for="height" class="block text-sm font-bold text-[#1B3B36] dark:text-white mb-1.5">
                                {{ __('messages.mp_height') }} <span class="text-xs text-neutral-400 font-medium">{{ __('messages.mp_optional') }}</span>
                            </label>
                            <div class="flex gap-2">
                                <input type="number" step="0.01" id="height" name="height" placeholder="{{ __('messages.mp_height_ph') }}"
                                       value="{{ old('height', $pet->height) }}"
                                       class="w-full min-w-0 px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-950 text-sm text-neutral-800 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                                <select name="height_unit" class="w-16 sm:w-20 px-2 py-2.5 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-950 text-sm text-neutral-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition shrink-0">
                                    <option value="ft" @selected(old('height_unit', $pet->height_unit) === 'ft')>ft</option>
                                    <option value="in" @selected(old('height_unit', $pet->height_unit) === 'in')>in</option>
                                    <option value="cm" @selected(old('height_unit', $pet->height_unit) === 'cm')>cm</option>
                                </select>
                            </div>
                        </div>

                        {{-- Length --}}
                        <div>
                            <label for="length" class="block text-sm font-bold text-[#1B3B36] dark:text-white mb-1.5">
                                {{ __('messages.mp_length') }} <span class="text-xs text-neutral-400 font-medium">{{ __('messages.mp_optional') }}</span>
                            </label>
                            <div class="flex gap-2">
                                <input type="number" step="0.01" id="length" name="length" placeholder="{{ __('messages.mp_length_ph') }}"
                                       value="{{ old('length', $pet->length) }}"
                                       class="w-full min-w-0 px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-950 text-sm text-neutral-800 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                                <select name="length_unit" class="w-16 sm:w-20 px-2 py-2.5 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-950 text-sm text-neutral-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition shrink-0">
                                    <option value="ft" @selected(old('length_unit', $pet->length_unit) === 'ft')>ft</option>
                                    <option value="in" @selected(old('length_unit', $pet->length_unit) === 'in')>in</option>
                                    <option value="cm" @selected(old('length_unit', $pet->length_unit) === 'cm')>cm</option>
                                </select>
                            </div>
                        </div>

                        {{-- Width --}}
                        <div>
                            <label for="width" class="block text-sm font-bold text-[#1B3B36] dark:text-white mb-1.5">
                                {{ __('messages.mp_width') }} <span class="text-xs text-neutral-400 font-medium">{{ __('messages.mp_optional') }}</span>
                            </label>
                            <div class="flex gap-2">
                                <input type="number" step="0.01" id="width" name="width" placeholder="{{ __('messages.mp_width_ph') }}"
                                       value="{{ old('width', $pet->width) }}"
                                       class="w-full min-w-0 px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-950 text-sm text-neutral-800 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                                <select name="width_unit" class="w-16 sm:w-20 px-2 py-2.5 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-950 text-sm text-neutral-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition shrink-0">
                                    <option value="ft" @selected(old('width_unit', $pet->width_unit) === 'ft')>ft</option>
                                    <option value="in" @selected(old('width_unit', $pet->width_unit) === 'in')>in</option>
                                    <option value="cm" @selected(old('width_unit', $pet->width_unit) === 'cm')>cm</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- ========================================== --}}
                    {{-- SPECIAL NEEDS / MEDICAL / DIETARY          --}}
                    {{-- ========================================== --}}
                    <div class="space-y-4">
                        <div>
                            <label for="special_needs" class="block text-sm font-bold text-[#1B3B36] dark:text-white mb-1.5">
                                {{ __('messages.mp_special_needs') }} <span class="text-xs text-neutral-400 font-medium">{{ __('messages.mp_optional') }}</span>
                            </label>
                            <textarea id="special_needs" name="special_needs" rows="2"
                                      placeholder="{{ __('messages.mp_special_needs_ph') }}"
                                      class="w-full px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-950 text-sm text-neutral-800 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition resize-none">{{ old('special_needs', $pet->special_needs) }}</textarea>
                        </div>

                        <div>
                            <label for="medical_conditions" class="block text-sm font-bold text-[#1B3B36] dark:text-white mb-1.5">
                                {{ __('messages.mp_medical_conditions') }} <span class="text-xs text-neutral-400 font-medium">{{ __('messages.mp_optional') }}</span>
                            </label>
                            <textarea id="medical_conditions" name="medical_conditions" rows="2"
                                      placeholder="{{ __('messages.mp_medical_ph') }}"
                                      class="w-full px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-950 text-sm text-neutral-800 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition resize-none">{{ old('medical_conditions', $pet->medical_conditions) }}</textarea>
                        </div>

                        <div>
                            <label for="dietary_restrictions" class="block text-sm font-bold text-[#1B3B36] dark:text-white mb-1.5">
                                {{ __('messages.mp_dietary') }} <span class="text-xs text-neutral-400 font-medium">{{ __('messages.mp_optional') }}</span>
                            </label>
                            <select id="dietary_restrictions" name="dietary_restrictions"
                                    class="w-full px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-950 text-sm text-neutral-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                                <option value="">{{ __('messages.mp_select_dietary') }}</option>
                                <option value="none"         @selected(old('dietary_restrictions', $pet->dietary_restrictions) === 'none')>{{ __('messages.mp_diet_none') }}</option>
                                <option value="dry_food"     @selected(old('dietary_restrictions', $pet->dietary_restrictions) === 'dry_food')>{{ __('messages.mp_diet_dry') }}</option>
                                <option value="wet_food"     @selected(old('dietary_restrictions', $pet->dietary_restrictions) === 'wet_food')>{{ __('messages.mp_diet_wet') }}</option>
                                <option value="raw"          @selected(old('dietary_restrictions', $pet->dietary_restrictions) === 'raw')>{{ __('messages.mp_diet_raw') }}</option>
                                <option value="prescription" @selected(old('dietary_restrictions', $pet->dietary_restrictions) === 'prescription')>{{ __('messages.mp_diet_prescription') }}</option>
                            </select>
                        </div>
                    </div>

                    {{-- ========================================== --}}
                    {{-- FORM ACTIONS                              --}}
                    {{-- ========================================== --}}
                    <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-3 pt-4 border-t border-gray-100 dark:border-neutral-800">
                        <a href="{{ route('mypets.index') }}"
                           class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 sm:py-3 border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-sm rounded-xl hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                            {{ __('messages.mp_cancel') }}
                        </a>
                        <button type="submit"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 sm:py-3 bg-primary hover:bg-primary-600 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ __('messages.mp_update_btn') }}
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </div>

    {{-- ========================================== --}}
    {{-- ALPINE FUNCTION — Pet Photo Upload         --}}
    {{-- ========================================== --}}
    @push('scripts')
    <script>
        function petPhotoUpload(config) {
            return {
                photoPreview: (config && config.existingPhoto) || null,
                photoName: '',

                handleFile(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    // Validate size (5MB)
                    if (file.size > 5 * 1024 * 1024) {
                        alert(@js(__('messages.mp_js_file_too_large')));
                        event.target.value = '';
                        return;
                    }

                    // Validate type
                    if (!file.type.startsWith('image/')) {
                        alert(@js(__('messages.mp_js_image_only')));
                        event.target.value = '';
                        return;
                    }

                    this.photoName = file.name;

                    // Live preview via FileReader
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.photoPreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                },

                clearPhoto() {
                    // Reset sa existing photo kung naa (dili null)
                    // Kay sa edit page, dili dapat ma-delete ang existing photo pinaagi lang sa X button
                    const existing = (config && config.existingPhoto) || null;
                    this.photoPreview = existing;
                    this.photoName = '';
                    const input = document.getElementById('photo_path');
                    if (input) input.value = '';
                }
            }
        }
    </script>
    @endpush
</x-app-layout>