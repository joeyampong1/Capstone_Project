<section>
    <form method="post" action="{{ route('profile.id') }}" enctype="multipart/form-data" class="space-y-4 sm:space-y-6">
        @csrf

        {{-- ============================================================ --}}
        {{-- STEP INDICATOR                                                --}}
        {{-- ============================================================ --}}
        <div class="flex items-center justify-between gap-1 sm:gap-2 mb-4 sm:mb-6">

            {{-- Step 1: Select & Upload ID --}}
            <div class="flex-1 text-center">
                <div class="flex flex-col items-center">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-[10px] sm:text-xs font-bold
                        {{ $user->gov_id_path ? 'bg-green-500 text-white' : 'bg-primary text-white' }}">
                        {{ $user->gov_id_path ? '✓' : '1' }}
                    </div>
                    <span class="text-[9px] sm:text-[10px] font-bold mt-1 leading-tight {{ $user->gov_id_path ? 'text-green-500' : 'text-primary' }}">
                        {{ __('messages.pf_step_upload_id') }}
                    </span>
                </div>
            </div>

            {{-- Connector 1 --}}
            <div class="flex-1 h-0.5 {{ $user->gov_id_path ? 'bg-green-500' : 'bg-gray-200' }}"></div>

            {{-- Step 2: Selfie --}}
            <div class="flex-1 text-center">
                <div class="flex flex-col items-center">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-[10px] sm:text-xs font-bold
                        {{ $user->selfie_photo ? 'bg-green-500 text-white' : ($user->gov_id_path ? 'bg-primary text-white' : 'bg-gray-200 text-gray-400') }}">
                        {{ $user->selfie_photo ? '✓' : '2' }}
                    </div>
                    <span class="text-[9px] sm:text-[10px] font-bold mt-1 leading-tight
                        {{ $user->selfie_photo ? 'text-green-500' : ($user->gov_id_path ? 'text-primary' : 'text-gray-400') }}">
                        {{ __('messages.pf_step_selfie_id') }}
                    </span>
                </div>
            </div>

            {{-- Connector 2 --}}
            <div class="flex-1 h-0.5 {{ $user->selfie_photo ? 'bg-green-500' : 'bg-gray-200' }}"></div>

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
        {{-- STEP 1: SELECT ID TYPE + UPLOAD ID (combined, no page reload) --}}
        {{-- ============================================================ --}}
        @if(!$user->gov_id_path)
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
                            <option value="passport"        {{ old('id_type') === 'passport'        ? 'selected' : '' }}>{{ __('messages.pf_id_passport') }}</option>
                            <option value="drivers_license" {{ old('id_type') === 'drivers_license' ? 'selected' : '' }}>{{ __('messages.pf_id_drivers_license') }}</option>
                            <option value="umid"            {{ old('id_type') === 'umid'            ? 'selected' : '' }}>{{ __('messages.pf_id_umid') }}</option>
                            <option value="postal_id"       {{ old('id_type') === 'postal_id'       ? 'selected' : '' }}>{{ __('messages.pf_id_postal') }}</option>
                            <option value="voters_id"       {{ old('id_type') === 'voters_id'       ? 'selected' : '' }}>{{ __('messages.pf_id_voters') }}</option>
                            <option value="national_id"     {{ old('id_type') === 'national_id'     ? 'selected' : '' }}>{{ __('messages.pf_id_national') }}</option>
                            <option value="other"           {{ old('id_type') === 'other'           ? 'selected' : '' }}>{{ __('messages.pf_id_other') }}</option>
                        </select>
                        <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('id_type')" />
                    </div>

                    {{-- 1b: File upload — revealed after ID type is selected --}}
                    <div id="gov_id_upload_wrapper"
                         class="{{ old('id_type') ? '' : 'hidden' }} space-y-3">

                        <p class="text-xs sm:text-sm text-neutral-500 dark:text-neutral-400">
                            {!! __('messages.pf_upload_id_hint', ['id' => '<span id="id_type_label" class="font-semibold text-[#1B3B36] dark:text-white">ID</span>']) !!}
                        </p>
                        <p class="text-xs text-neutral-400">{{ __('messages.pf_id_accepted') }}</p>
                        <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('gov_id_path')" />

                        <input id="gov_id_path" name="gov_id_path" type="file" accept="image/*" class="hidden">

                        {{-- Before file chosen --}}
                        <label id="gov_id_select_btn" for="gov_id_path"
                               class="inline-block bg-primary hover:bg-primary-600 text-white font-bold text-sm
                                      px-6 py-2.5 sm:px-8 sm:py-3 rounded-xl shadow-md hover:shadow-lg transition
                                      transform hover:-translate-y-0.5 cursor-pointer">
                            {{ __('messages.pf_select_id_btn') }}
                        </label>

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
        {{-- STEP 2: SELFIE WITH ID                                        --}}
        {{-- ============================================================ --}}
        @if($user->gov_id_path && !$user->selfie_photo)
            <div class="p-4 sm:p-6 rounded-xl bg-primary/5 border-2 border-dashed border-primary/30">
                <div class="text-center space-y-3 sm:space-y-4">
                    <div class="text-4xl sm:text-5xl">📸</div>
                    <h4 class="text-base sm:text-lg font-black text-[#1B3B36] dark:text-white">{{ __('messages.pf_take_selfie_id') }}</h4>
                    <p class="text-xs sm:text-sm text-neutral-500 dark:text-neutral-400 max-w-sm mx-auto">
                        {!! __('messages.pf_selfie_hint', ['id' => '<strong>' . ucfirst(str_replace('_', ' ', $user->id_type ?? 'ID')) . '</strong>']) !!}
                    </p>
                    <p class="text-xs text-neutral-400">{{ __('messages.pf_id_accepted') }}</p>
                    <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('selfie_photo')" />

                    <input id="selfie_photo" name="selfie_photo" type="file" accept="image/*" class="hidden">

                    {{-- Before file chosen --}}
                    <label id="selfie_select_btn" for="selfie_photo"
                           class="inline-block bg-primary hover:bg-primary-600 text-white font-bold text-sm
                                  px-6 py-2.5 sm:px-8 sm:py-3 rounded-xl shadow-md hover:shadow-lg transition
                                  transform hover:-translate-y-0.5 cursor-pointer">
                        {{ __('messages.pf_select_selfie_btn') }}
                    </label>

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
        @if($user->gov_id_path && $user->selfie_photo && $user->id_validation_status === 'unverified')
            <div class="p-4 sm:p-6 rounded-xl border-2 border-yellow-400 bg-yellow-50 dark:bg-yellow-950/20">
                <div class="text-center space-y-3 sm:space-y-4">
                    <div class="text-4xl sm:text-5xl">📋</div>
                    <h4 class="text-base sm:text-lg font-black text-yellow-700 dark:text-yellow-400">{{ __('messages.pf_ready_submit') }}</h4>
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
</section>

<script>
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

    // ── Step 1: reveal upload area when ID type is picked ──────────────
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

    // Handle old() pre-selection on validation error page reload
    if (idTypeSelect?.value) syncIdType(idTypeSelect.value);

    // ── Step 1: show filename + Upload button after file chosen ─────────
    function resetGovIdInput() {
        const fileInput = document.getElementById('gov_id_path');
        if (fileInput) fileInput.value = '';
        document.getElementById('gov_id_select_btn')?.classList.remove('hidden');
        document.getElementById('gov_id_confirm_section')?.classList.add('hidden');
    }

    document.getElementById('gov_id_path')?.addEventListener('change', function () {
        if (this.files.length > 0) {
            document.getElementById('gov_id_filename').textContent = this.files[0].name;
            document.getElementById('gov_id_select_btn').classList.add('hidden');
            document.getElementById('gov_id_confirm_section').classList.remove('hidden');
        } else {
            resetGovIdInput();
        }
    });

    // ── Step 2: show filename + Next button after selfie chosen ─────────
    document.getElementById('selfie_photo')?.addEventListener('change', function () {
        if (this.files.length > 0) {
            document.getElementById('selfie_filename').textContent = this.files[0].name;
            document.getElementById('selfie_select_btn').classList.add('hidden');
            document.getElementById('selfie_confirm_section').classList.remove('hidden');
        } else {
            document.getElementById('selfie_select_btn').classList.remove('hidden');
            document.getElementById('selfie_confirm_section').classList.add('hidden');
        }
    });
})();
</script>