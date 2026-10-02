<section x-data="{
        cameraOpen: false,
        cameraMode: 'id',
        facingMode: 'environment',
        stream: null,
        capturedImage: null,

        async openCamera(mode) {
            this.cameraMode = mode;
            this.facingMode = mode === 'selfie' ? 'user' : 'environment';
            this.cameraOpen = true;
            this.capturedImage = null;
            await this.startStream();
        },

        async startStream() {
            // Stop existing stream
            if (this.stream) {
                this.stream.getTracks().forEach(t => t.stop());
                this.stream = null;
            }

            try {
                this.stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: this.facingMode },
                    audio: false
                });
                this.$nextTick(() => {
                    if (this.$refs.video) this.$refs.video.srcObject = this.stream;
                });
            } catch (err) {
                alert('{{ __('messages.pf_camera_denied') }}');
                this.cameraOpen = false;
            }
        },

        async flipCamera() {
            this.facingMode = this.facingMode === 'user' ? 'environment' : 'user';
            await this.startStream();
        },

        closeCamera() {
            if (this.stream) {
                this.stream.getTracks().forEach(t => t.stop());
                this.stream = null;
            }
            this.cameraOpen = false;
            this.capturedImage = null;
        },

        capture() {
            const video = this.$refs.video;
            const canvas = this.$refs.canvas;
            if (!video || !canvas) return;

            canvas.width  = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);

            canvas.toBlob((blob) => {
                const file = new File([blob], `captured_${this.cameraMode}.jpg`, { type: 'image/jpeg' });
                const dt = new DataTransfer();
                dt.items.add(file);

                let input = null;

                if (this.cameraMode === 'selfie') {
                    input = document.getElementById('selfie_photo');
                } else {
                    const retryInput = document.getElementById('gov_id_path_retry');
                    if (retryInput && retryInput.offsetParent !== null) {
                        input = retryInput;
                    } else {
                        input = document.getElementById('gov_id_path');
                    }
                }

                if (input) {
                    input.files = dt.files;
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                }

                this.closeCamera();
            }, 'image/jpeg', 0.92);
        }
    }">

    <form method="post" action="{{ route('profile.id') }}" enctype="multipart/form-data" class="space-y-4 sm:space-y-6">
        @csrf

        {{-- ============================================================ --}}
        {{-- STEP INDICATOR                                                --}}
        {{-- ============================================================ --}}
        @php
            $ocrSuccess = $user->gov_id_path && ($user->ocr_result['success'] ?? false);
            $step2Ready = $ocrSuccess && ! $user->selfie_photo;
            $step3Ready = $ocrSuccess && $user->selfie_photo && $user->id_validation_status === 'unverified';
        @endphp

        <div class="flex items-center justify-between gap-1 sm:gap-2 mb-4 sm:mb-6">

            {{-- Step 1: Select & Upload ID --}}
            <div class="flex-1 text-center">
                <div class="flex flex-col items-center">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-[10px] sm:text-xs font-bold
                        {{ $ocrSuccess ? 'bg-green-500 text-white' : 'bg-primary text-white' }}">
                        {{ $ocrSuccess ? '✓' : '1' }}
                    </div>
                    <span class="text-[9px] sm:text-[10px] font-bold mt-1 leading-tight {{ $ocrSuccess ? 'text-green-500' : 'text-primary' }}">
                        {{ __('messages.pf_step_upload_id') }}
                    </span>
                </div>
            </div>

            {{-- Connector 1 --}}
            <div class="flex-1 h-0.5 {{ $ocrSuccess ? 'bg-green-500' : 'bg-gray-200' }}"></div>

            {{-- Step 2: Selfie --}}
            <div class="flex-1 text-center">
                <div class="flex flex-col items-center">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-[10px] sm:text-xs font-bold
                        {{ $user->selfie_photo ? 'bg-green-500 text-white' : ($step2Ready ? 'bg-primary text-white' : 'bg-gray-200 text-gray-400') }}">
                        {{ $user->selfie_photo ? '✓' : '2' }}
                    </div>
                    <span class="text-[9px] sm:text-[10px] font-bold mt-1 leading-tight
                        {{ $user->selfie_photo ? 'text-green-500' : ($step2Ready ? 'text-primary' : 'text-gray-400') }}">
                        {{ __('messages.pf_step_selfie_id') }}
                    </span>
                </div>
            </div>

            {{-- Connector 2 --}}
            <div class="flex-1 h-0.5 {{ $user->selfie_photo && $ocrSuccess ? 'bg-green-500' : 'bg-gray-200' }}"></div>

            {{-- Step 3: Submit / Status --}}
            <div class="flex-1 text-center">
                <div class="flex flex-col items-center">
                    @php
                        $statusDot = match($user->id_validation_status) {
                            'verified' => 'bg-green-500 text-white',
                            'pending'  => 'bg-yellow-500 text-white',
                            default    => 'bg-gray-200 text-gray-400',
                        };
                        $statusLabel = match($user->id_validation_status) {
                            'verified' => __('messages.pf_step_status_verified'),
                            'pending'  => __('messages.pf_step_status_verifying'),
                            default    => __('messages.pf_step_status_submit'),
                        };
                        $statusIcon = match($user->id_validation_status) {
                            'verified' => '✓',
                            'pending'  => '⏳',
                            default    => '3',
                        };
                        $statusText = match($user->id_validation_status) {
                            'verified' => 'text-green-500',
                            'pending'  => 'text-yellow-500',
                            default    => 'text-gray-400',
                        };
                    @endphp
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-[10px] sm:text-xs font-bold {{ $statusDot }}">
                        {{ $statusIcon }}
                    </div>
                    <span class="text-[9px] sm:text-[10px] font-bold mt-1 leading-tight {{ $statusText }}">
                        {{ $statusLabel }}
                    </span>
                </div>
            </div>

        </div>

        {{-- ============================================================ --}}
        {{-- STEP 1: SELECT ID TYPE + UPLOAD ID                            --}}
        {{-- ============================================================ --}}
        @if(! $user->gov_id_path || ! $ocrSuccess)
            <div class="p-4 sm:p-6 rounded-xl bg-primary/5 border-2 border-dashed border-primary/30">
                <div class="text-center space-y-3 sm:space-y-4">
                    <div class="text-4xl sm:text-5xl">🪪</div>
                    <h4 class="text-base sm:text-lg font-black text-[#1B3B36] dark:text-white">{{ __('messages.pf_upload_gov_id') }}</h4>
                    <p class="text-xs sm:text-sm text-neutral-500 dark:text-neutral-400 max-w-sm mx-auto">
                        {{ __('messages.pf_upload_gov_id_desc') }}
                    </p>

                    {{-- 1a: ID type dropdown --}}
                    <div class="max-w-xs mx-auto">
                        <select id="id_type_select" name="id_type" required
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-neutral-800
                                       bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white
                                       focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary
                                       transition text-sm font-medium">
                            <option value="">{{ __('messages.pf_select_id_type') }}</option>
                            <option value="passport"        {{ old('id_type', $user->id_type) === 'passport'        ? 'selected' : '' }}>{{ __('messages.pf_id_passport') }}</option>
                            <option value="drivers_license" {{ old('id_type', $user->id_type) === 'drivers_license' ? 'selected' : '' }}>{{ __('messages.pf_id_drivers_license') }}</option>
                            <option value="umid"            {{ old('id_type', $user->id_type) === 'umid'            ? 'selected' : '' }}>{{ __('messages.pf_id_umid') }}</option>
                            <option value="postal_id"       {{ old('id_type', $user->id_type) === 'postal_id'       ? 'selected' : '' }}>{{ __('messages.pf_id_postal') }}</option>
                            <option value="voters_id"       {{ old('id_type', $user->id_type) === 'voters_id'       ? 'selected' : '' }}>{{ __('messages.pf_id_voters') }}</option>
                            <option value="national_id"     {{ old('id_type', $user->id_type) === 'national_id'     ? 'selected' : '' }}>{{ __('messages.pf_id_national') }}</option>
                            <option value="other"           {{ old('id_type', $user->id_type) === 'other'           ? 'selected' : '' }}>{{ __('messages.pf_id_other') }}</option>
                        </select>
                        <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('id_type')" />
                    </div>

                    {{-- 1b: File upload — revealed after ID type is selected --}}
                    <div id="gov_id_upload_wrapper"
                         class="{{ old('id_type', $user->id_type) ? '' : 'hidden' }} space-y-3">

                        <p class="text-xs sm:text-sm text-neutral-500 dark:text-neutral-400">
                            {!! __('messages.pf_upload_id_hint', ['id' => '<span id="id_type_label" class="font-semibold text-[#1B3B36] dark:text-white">ID</span>']) !!}
                        </p>
                        <p class="text-xs text-neutral-400">{{ __('messages.pf_id_accepted') }}</p>
                        <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('gov_id_path')" />

                        <input id="gov_id_path" name="gov_id_path" type="file" accept="image/*" class="hidden">

                        {{-- Before file chosen --}}
                        <div id="gov_id_select_btn" class="flex flex-wrap items-center justify-center gap-2 sm:gap-3">
                            {{-- Camera --}}
                            <button type="button" @click="openCamera('id')"
                                    class="inline-flex items-center gap-2 bg-primary hover:bg-primary-600 text-white font-bold text-sm
                                           px-5 py-2.5 sm:px-6 sm:py-3 rounded-xl shadow-md hover:shadow-lg transition
                                           transform hover:-translate-y-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ __('messages.pf_open_camera') }}
                            </button>

                            {{-- Gallery --}}
                            <label for="gov_id_path"
                                   class="inline-flex items-center gap-2 bg-white dark:bg-neutral-800 border-2 border-gray-200 dark:border-neutral-700
                                          text-neutral-700 dark:text-neutral-200 font-bold text-sm
                                          px-5 py-2.5 sm:px-6 sm:py-3 rounded-xl hover:bg-gray-50 dark:hover:bg-neutral-700 transition
                                          cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ __('messages.pf_choose_gallery') }}
                            </label>
                        </div>

                        {{-- After file chosen --}}
                        <div id="gov_id_confirm_section" class="hidden space-y-3">
                            <p class="text-sm text-neutral-600 dark:text-neutral-300 font-medium">
                                <span class="text-green-600 dark:text-green-400">✓</span>
                                <span id="gov_id_filename"></span>
                            </p>
                            <button type="submit" name="action" value="upload_id"
                                    class="bg-primary hover:bg-primary-600 text-white font-bold text-sm
                                           px-6 py-2.5 sm:px-8 sm:py-3 rounded-xl shadow-md hover:shadow-lg transition
                                           transform hover:-translate-y-0.5">
                                {{ __('messages.pf_upload_id_btn') }}
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        @endif

        {{-- ============================================================ --}}
        {{-- OCR EXTRACTION RESULT                                        --}}
        {{-- ============================================================ --}}
        @if($user->gov_id_path && $user->ocr_result)
            @php $ocrSuccess = $user->ocr_result['success'] ?? false; @endphp

            @if($ocrSuccess)
                <div class="p-3 sm:p-4 rounded-xl bg-green-50 dark:bg-green-950/20 border border-green-200 dark:border-green-800">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-green-700 dark:text-green-400">
                                {{ __('messages.pf_ocr_success_title') }}
                            </p>
                            <p class="text-xs text-green-600 dark:text-green-400 mt-1">
                                {{ __('messages.pf_ocr_success_desc') }}
                            </p>

                            @if(!empty($user->ocr_result['name']) || !empty($user->ocr_result['id_number']))
                                <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-green-700 dark:text-green-300">
                                    @if(!empty($user->ocr_result['name']))
                                        <span><strong>{{ __('messages.pf_ocr_label_name') }}:</strong> {{ $user->ocr_result['name'] }}</span>
                                    @endif
                                    @if(!empty($user->ocr_result['id_number']))
                                        <span><strong>{{ __('messages.pf_ocr_label_id') }}:</strong> {{ $user->ocr_result['id_number'] }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div x-data="{ show: true }"
                    x-show="show"
                    x-init="setTimeout(() => show = false, 5000)"
                    x-transition.opacity.duration.500ms
                    class="p-3 sm:p-4 rounded-xl bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-amber-700 dark:text-amber-400">
                                {{ __('messages.pf_ocr_failed_title') }}
                            </p>
                            <p class="text-xs text-amber-600 dark:text-amber-400 mt-1">
                                {{ __('messages.pf_ocr_failed_desc') }}
                            </p>
                        </div>
                    </div>

                    {{-- Retry upload --}}
                    <form method="POST" action="{{ route('profile.id') }}" enctype="multipart/form-data" class="mt-4 space-y-3"
                        x-data="{
                            retryFileChosen: false,
                            retryFileName: '',
                            handleFileChange(e) {
                                this.retryFileChosen = e.target.files.length > 0;
                                this.retryFileName = e.target.files[0]?.name || '';
                            }
                        }">
                        @csrf
                        <input type="hidden" name="action" value="upload_id">
                        <input type="hidden" name="id_type" value="{{ $user->id_type }}">

                        {{-- File input (hidden) --}}
                        <input id="gov_id_path_retry" name="gov_id_path" type="file" accept="image/*" class="hidden"
                            @change="handleFileChange($event)">

                        {{-- State 1: Walang file — show Retake + Choose Different --}}
                        <div x-show="!retryFileChosen" class="flex flex-wrap items-center gap-2 sm:gap-3">
                            <button type="button" @click="openCamera('id')"
                                    class="inline-flex items-center gap-2 bg-primary hover:bg-primary-600 text-white font-bold text-xs sm:text-sm
                                        px-4 py-2 sm:px-5 sm:py-2.5 rounded-xl shadow-md hover:shadow-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                </svg>
                                {{ __('messages.pf_retry_camera') }}
                            </button>

                            <label for="gov_id_path_retry"
                                class="inline-flex items-center gap-2 bg-white dark:bg-neutral-800 border-2 border-gray-200 dark:border-neutral-700
                                        text-neutral-700 dark:text-neutral-200 font-bold text-xs sm:text-sm
                                        px-4 py-2 sm:px-5 sm:py-2.5 rounded-xl hover:bg-gray-50 dark:hover:bg-neutral-700 transition cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ __('messages.pf_retry_gallery') }}
                            </label>
                        </div>

                        {{-- State 2: May file na — show filename + Upload + Change --}}
                        <div x-show="retryFileChosen" x-cloak class="space-y-3">
                            <p class="text-sm text-neutral-600 dark:text-neutral-300 font-medium flex items-center gap-2">
                                <span class="text-green-600 dark:text-green-400">✓</span>
                                <span x-text="retryFileName"></span>
                            </p>

                            <div class="flex flex-wrap items-center gap-2">
                                {{-- Upload --}}
                                <button type="submit"
                                        class="inline-flex items-center gap-2 bg-primary hover:bg-primary-600 text-white font-bold text-xs sm:text-sm
                                            px-5 py-2.5 sm:px-6 sm:py-3 rounded-xl shadow-md hover:shadow-lg transition">
                                    {{ __('messages.pf_upload_id_btn') }}
                                </button>

                                {{-- Change file --}}
                                <label for="gov_id_path_retry"
                                    class="inline-flex items-center gap-2 bg-white dark:bg-neutral-800 border-2 border-gray-200 dark:border-neutral-700
                                            text-neutral-700 dark:text-neutral-200 font-bold text-xs sm:text-sm
                                            px-4 py-2 sm:px-5 sm:py-2.5 rounded-xl hover:bg-gray-50 dark:hover:bg-neutral-700 transition cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    {{ __('messages.pf_retry_gallery') }}
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
            @endif
        @endif

        {{-- ============================================================ --}}
        {{-- STEP 2: SELFIE WITH ID — gawas kung OCR success              --}}
        {{-- ============================================================ --}}
        @if($ocrSuccess && ! $user->selfie_photo)
            <div class="p-4 sm:p-6 rounded-xl bg-primary/5 border-2 border-dashed border-primary/30">
                <div class="text-center space-y-3 sm:space-y-4">
                    <div class="text-4xl sm:text-5xl">📸</div>
                    <h4 class="text-base sm:text-lg font-black text-[#1B3B36] dark:text-white">{{ __('messages.pf_take_selfie_id') }}</h4>
                    <p class="text-xs sm:text-sm text-neutral-500 dark:text-neutral-400 max-w-sm mx-auto">
                        {!! __('messages.pf_selfie_hint', ['id' => '<strong>' . ucfirst(str_replace('_', ' ', $user->id_type ?? 'ID')) . '</strong>']) !!}
                    </p>
                    <p class="text-xs text-neutral-400">{{ __('messages.pf_id_accepted') }}</p>
                    <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('selfie_photo')" />

                    <input id="selfie_photo" name="selfie_photo" type="file" accept="image/*" capture="user" class="hidden">

                    {{-- Before file chosen --}}
                    <div id="selfie_select_btn" class="flex flex-wrap items-center justify-center gap-2 sm:gap-3">
                        <button type="button" @click="openCamera('selfie')"
                                class="inline-flex items-center gap-2 bg-primary hover:bg-primary-600 text-white font-bold text-sm
                                       px-5 py-2.5 sm:px-6 sm:py-3 rounded-xl shadow-md hover:shadow-lg transition
                                       transform hover:-translate-y-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ __('messages.pf_open_camera') }}
                        </button>

                        <label for="selfie_photo"
                               class="inline-flex items-center gap-2 bg-white dark:bg-neutral-800 border-2 border-gray-200 dark:border-neutral-700
                                      text-neutral-700 dark:text-neutral-200 font-bold text-sm
                                      px-5 py-2.5 sm:px-6 sm:py-3 rounded-xl hover:bg-gray-50 dark:hover:bg-neutral-700 transition cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ __('messages.pf_choose_gallery') }}
                        </label>
                    </div>

                    {{-- After file chosen --}}
                    <div id="selfie_confirm_section" class="hidden space-y-3">
                        <p class="text-sm text-neutral-600 dark:text-neutral-300 font-medium">
                            <span class="text-green-600 dark:text-green-400">✓</span>
                            <span id="selfie_filename"></span>
                        </p>
                        <button type="submit" name="action" value="upload_selfie"
                                class="bg-primary hover:bg-primary-600 text-white font-bold text-sm
                                       px-6 py-2.5 sm:px-8 sm:py-3 rounded-xl shadow-md hover:shadow-lg transition
                                       transform hover:-translate-y-0.5">
                            {{ __('messages.pf_next_btn') }}
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- ============================================================ --}}
        {{-- STEP 3: SUBMIT FOR VERIFICATION                               --}}
        {{-- ============================================================ --}}
        @php
            // Check kung kompleto ang profile
            $profileComplete = auth()->user()->f_name
                && auth()->user()->l_name
                && auth()->user()->date_of_birth
                && auth()->user()->gender
                && auth()->user()->contact_number
                && auth()->user()->address;

            // Missing fields
            $missingFields = [];
            if (!auth()->user()->f_name)         $missingFields[] = __('messages.pf_first_name');
            if (!auth()->user()->l_name)         $missingFields[] = __('messages.pf_last_name');
            if (!auth()->user()->date_of_birth)  $missingFields[] = __('messages.pf_dob');
            if (!auth()->user()->gender)         $missingFields[] = __('messages.pf_gender');
            if (!auth()->user()->contact_number) $missingFields[] = __('messages.pf_contact_number');
            if (!auth()->user()->address)        $missingFields[] = __('messages.pf_address');
        @endphp

        @if($step3Ready && !$profileComplete)
            {{-- Profile Incomplete Warning --}}
            <div class="p-4 sm:p-6 rounded-xl border-2 border-red-400 bg-red-50 dark:bg-red-950/20">
                <div class="text-center space-y-3 sm:space-y-4">
                    <div class="text-4xl sm:text-5xl">⚠️</div>

                    <h4 class="text-base sm:text-lg font-black text-red-700 dark:text-red-400">
                        {{ __('messages.pf_profile_incomplete_title') }}
                    </h4>

                    <p class="text-xs sm:text-sm text-red-600 dark:text-red-400 max-w-md mx-auto">
                        {{ __('messages.pf_profile_incomplete_desc') }}
                    </p>

                    <ul class="text-left max-w-md mx-auto text-xs text-red-600 dark:text-red-400 space-y-1">
                        @foreach($missingFields as $field)
                            <li>• {{ __('messages.pf_missing_field') }}: <strong>{{ $field }}</strong></li>
                        @endforeach
                    </ul>

                    <a href="{{ route('profile.edit') }}"
                    class="inline-flex items-center gap-2 px-6 py-2.5 sm:px-8 sm:py-3 bg-primary hover:bg-primary-600 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        {{ __('messages.pf_complete_profile_btn') }}
                    </a>
                </div>
            </div>
        @elseif($step3Ready && $profileComplete)
            {{-- Step 3: Submit (kung kompleto na) --}}
            <div class="p-4 sm:p-6 rounded-xl border-2 border-yellow-400 bg-yellow-50 dark:bg-yellow-950/20">
                <div class="text-center space-y-3 sm:space-y-4">
                    <div class="text-4xl sm:text-5xl">📋</div>

                    <h4 class="text-base sm:text-lg font-black text-yellow-700 dark:text-yellow-400">
                        {{ __('messages.pf_ready_submit') }}
                    </h4>

                    <p class="text-xs sm:text-sm text-yellow-600 dark:text-yellow-400 max-w-sm mx-auto">
                        {{ __('messages.pf_ready_submit_desc') }}
                    </p>

                    <button type="submit" name="action" value="submit_verification"
                            class="bg-primary hover:bg-primary-600 text-white font-bold text-sm
                                px-6 py-2.5 sm:px-8 sm:py-3 rounded-xl shadow-md hover:shadow-lg transition
                                transform hover:-translate-y-0.5">
                        {{ __('messages.pf_submit_verification') }}
                    </button>
                </div>
            </div>
        @endif

        {{-- ============================================================ --}}
        {{-- PENDING                                                        --}}
        {{-- ============================================================ --}}
        @if($user->id_validation_status === 'pending')
            <div class="p-4 sm:p-6 rounded-xl border-2 border-yellow-400 bg-yellow-50 dark:bg-yellow-950/20">
                <div class="flex flex-col items-center text-center space-y-3">
                    <div class="text-4xl sm:text-5xl">⏳</div>
                    <h4 class="text-base sm:text-lg font-black text-yellow-700 dark:text-yellow-400">{{ __('messages.pf_verifying_progress') }}</h4>
                    <p class="text-xs sm:text-sm text-yellow-600 dark:text-yellow-400 max-w-sm">
                        {{ __('messages.pf_verifying_desc') }}
                    </p>
                </div>
            </div>
        @endif

        {{-- ============================================================ --}}
        {{-- VERIFIED                                                       --}}
        {{-- ============================================================ --}}
        @if($user->id_validation_status === 'verified')
            <div class="p-4 sm:p-6 rounded-xl border-2 border-green-500 bg-green-50 dark:bg-green-950/20">
                <div class="flex flex-col items-center text-center space-y-3">
                    <div class="text-4xl sm:text-5xl">✅</div>
                    <h4 class="text-base sm:text-lg font-black text-green-600 dark:text-green-400">{{ __('messages.pf_verified_title') }}</h4>
                    <p class="text-xs sm:text-sm text-green-600 dark:text-green-400 max-w-sm">
                        {{ __('messages.pf_verified_desc') }}
                    </p>
                    <ul class="text-xs text-green-600 dark:text-green-400 text-left space-y-1">
                        <li>{{ __('messages.pf_verified_item_1') }}</li>
                        <li>{{ __('messages.pf_verified_item_2') }}</li>
                        <li>{{ __('messages.pf_verified_item_3') }}</li>
                    </ul>
                </div>
            </div>
        @endif

        {{-- ============================================================ --}}
        {{-- REJECTED                                                       --}}
        {{-- ============================================================ --}}
        @if($user->id_validation_status === 'rejected')
            <div class="p-4 sm:p-6 rounded-xl border-2 border-red-500 bg-red-50 dark:bg-red-950/20">
                <div class="flex flex-col items-center text-center space-y-3">
                    <div class="text-4xl sm:text-5xl">❌</div>
                    <h4 class="text-base sm:text-lg font-black text-red-600 dark:text-red-400">{{ __('messages.pf_failed_title') }}</h4>
                    <p class="text-xs sm:text-sm text-red-600 dark:text-red-400 max-w-sm">
                        {{ __('messages.pf_failed_desc') }}
                    </p>
                    <a href="#" onclick="document.getElementById('retry_form').submit()"
                       class="text-sm font-bold text-primary hover:underline">
                        {{ __('messages.pf_retry') }}
                    </a>
                </div>
            </div>
        @endif

        {{-- Flash message --}}
        @if(session('status') === 'id-updated')
            <p x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
               class="text-sm font-medium text-green-600 dark:text-green-400">
                {{ __('messages.pf_photo_uploaded') }}
            </p>
        @endif

    </form>

    {{-- ============================================================ --}}
    {{-- CAMERA OVERLAY                                               --}}
    {{-- ============================================================ --}}
    <div x-show="cameraOpen" x-cloak x-transition.opacity
         class="fixed inset-0 z-[200] flex items-center justify-center p-3 sm:p-4 bg-black/80 backdrop-blur-sm">

        <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center justify-between px-4 sm:px-5 py-3 border-b border-gray-200 dark:border-neutral-800">
                <h3 class="text-sm font-black text-[#1B3B36] dark:text-white"
                    x-text="cameraMode === 'selfie'
                        ? '{{ __('messages.pf_selfie_camera_title') }}'
                        : '{{ __('messages.pf_id_camera_title') }}'"></h3>
                <button type="button" @click="closeCamera()"
                        class="p-1.5 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                    <svg class="w-5 h-5 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Video preview --}}
            <div class="relative bg-black">
                <video x-ref="video" autoplay playsinline class="w-full max-h-[60vh] object-contain"></video>

                {{-- ID guide frame --}}
                <template x-if="cameraMode === 'id'">
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <div class="w-[85%] aspect-[1.586/1] border-4 border-white/70 rounded-2xl shadow-2xl"></div>
                    </div>
                </template>

                {{-- Selfie guide (circle) --}}
                <template x-if="cameraMode === 'selfie'">
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <div class="w-[70%] aspect-square border-4 border-white/70 rounded-full shadow-2xl"></div>
                    </div>
                </template>

                <canvas x-ref="canvas" class="hidden"></canvas>
            </div>

            {{-- Controls --}}
            <div class="p-4 sm:p-5 flex items-center justify-center gap-3">
                <button type="button" @click="closeCamera()"
                        class="px-5 py-3 rounded-xl border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-xs hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                    {{ __('messages.pf_cancel') }}
                </button>

                {{-- FLIP BUTTON --}}
                <button type="button" @click="flipCamera()"
                        class="p-3 rounded-xl border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition"
                        title="Flip Camera">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </button>

                <button type="button" @click="capture()"
                        class="inline-flex items-center gap-2 px-8 py-3 bg-primary hover:bg-primary-600 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ __('messages.pf_capture_photo') }}
                </button>
            </div>
        </div>
    </div>

</section>

{{-- @formatter:off --}}
<script>
// @ts-nocheck
(function () {
    const idLabels = {
        passport:        @js(__('messages.pf_id_passport')),
        drivers_license: @js(__('messages.pf_id_drivers_license')),
        umid:            @js(__('messages.pf_id_umid')),
        postal_id:       @js(__('messages.pf_id_postal')),
        voters_id:       @js(__('messages.pf_id_voters')),
        national_id:     @js(__('messages.pf_id_national')),
        other:           @js(__('messages.pf_id_doc')),
    };

    // Step 1: reveal upload area when ID type is picked
    const idTypeSelect   = document.getElementById('id_type_select');
    const uploadWrapper  = document.getElementById('gov_id_upload_wrapper');
    const idTypeLabel    = document.getElementById('id_type_label');

    function syncIdType(value) {
        if (value) {
            if (idTypeLabel) idTypeLabel.textContent = idLabels[value] ?? @js(__('messages.pf_id_fallback'));
            uploadWrapper?.classList.remove('hidden');
        } else {
            uploadWrapper?.classList.add('hidden');
            resetGovIdInput();
        }
    }

    idTypeSelect?.addEventListener('change', function () {
        syncIdType(this.value);
    });

    if (idTypeSelect?.value) syncIdType(idTypeSelect.value);

    // Step 1: show filename + Upload button after file chosen
    function resetGovIdInput() {
        const fileInput = document.getElementById('gov_id_path');
        if (fileInput) fileInput.value = '';
        document.getElementById('gov_id_select_btn')?.classList.remove('hidden');
        document.getElementById('gov_id_confirm_section')?.classList.add('hidden');
    }

    // Note: with new layout, both btn and confirm are siblings, need to hide btn wrapper (which has class 'flex'), so we toggle 'hidden'
    document.getElementById('gov_id_path')?.addEventListener('change', function () {
        if (this.files.length > 0) {
            document.getElementById('gov_id_filename').textContent = this.files[0].name;
            document.getElementById('gov_id_select_btn').classList.add('hidden');
            document.getElementById('gov_id_select_btn').classList.remove('flex');
            document.getElementById('gov_id_confirm_section').classList.remove('hidden');
        } else {
            resetGovIdInput();
        }
    });

    // Step 2: selfie
    document.getElementById('selfie_photo')?.addEventListener('change', function () {
        if (this.files.length > 0) {
            document.getElementById('selfie_filename').textContent = this.files[0].name;
            document.getElementById('selfie_select_btn').classList.add('hidden');
            document.getElementById('selfie_select_btn').classList.remove('flex');
            document.getElementById('selfie_confirm_section').classList.remove('hidden');
        } else {
            document.getElementById('selfie_select_btn').classList.remove('hidden');
            document.getElementById('selfie_select_btn').classList.add('flex');
            document.getElementById('selfie_confirm_section').classList.add('hidden');
        }
    });
})();
</script>
{{-- @formatter:on --}}
