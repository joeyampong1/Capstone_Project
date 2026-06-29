<section>
    <form method="post" action="{{ route('profile.id') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- ============================================================ --}}
        {{-- STEP INDICATOR                                                --}}
        {{-- ============================================================ --}}
        <div class="flex items-center justify-between gap-2 mb-6">

            {{-- Step 1: Select & Upload ID --}}
            <div class="flex-1 text-center">
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold
                        {{ $user->gov_id_path ? 'bg-green-500 text-white' : 'bg-primary text-white' }}">
                        {{ $user->gov_id_path ? '✓' : '1' }}
                    </div>
                    <span class="text-[10px] font-bold mt-1 {{ $user->gov_id_path ? 'text-green-500' : 'text-primary' }}">
                        Upload ID
                    </span>
                </div>
            </div>

            {{-- Connector 1 --}}
            <div class="flex-1 h-0.5 {{ $user->gov_id_path ? 'bg-green-500' : 'bg-gray-200' }}"></div>

            {{-- Step 2: Selfie --}}
            <div class="flex-1 text-center">
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold
                        {{ $user->selfie_photo ? 'bg-green-500 text-white' : ($user->gov_id_path ? 'bg-primary text-white' : 'bg-gray-200 text-gray-400') }}">
                        {{ $user->selfie_photo ? '✓' : '2' }}
                    </div>
                    <span class="text-[10px] font-bold mt-1
                        {{ $user->selfie_photo ? 'text-green-500' : ($user->gov_id_path ? 'text-primary' : 'text-gray-400') }}">
                        Selfie with ID
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
                            'verified' => 'Verified',
                            'pending'  => 'Verifying',
                            default    => 'Submit',
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
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold {{ $statusDot }}">
                        {{ $statusIcon }}
                    </div>
                    <span class="text-[10px] font-bold mt-1 {{ $statusText }}">
                        {{ $statusLabel }}
                    </span>
                </div>
            </div>

        </div>

        {{-- ============================================================ --}}
        {{-- STEP 1: SELECT ID TYPE + UPLOAD ID (combined, no page reload) --}}
        {{-- ============================================================ --}}
        @if(!$user->gov_id_path)
            <div class="p-6 rounded-xl bg-primary/5 border-2 border-dashed border-primary/30">
                <div class="text-center space-y-4">
                    <div class="text-5xl">🪪</div>
                    <h4 class="text-lg font-black text-[#1B3B36] dark:text-white">Upload Your Government ID</h4>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400 max-w-sm mx-auto">
                        First, choose your ID type. Then upload a clear photo of it.
                    </p>

                    {{-- 1a: ID type dropdown --}}
                    <div class="max-w-xs mx-auto">
                        <select id="id_type_select" name="id_type" required
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-neutral-800
                                       bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white
                                       focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary
                                       transition text-sm font-medium">
                            <option value="">— Select ID type —</option>
                            <option value="passport"        {{ old('id_type') === 'passport'        ? 'selected' : '' }}>Passport</option>
                            <option value="drivers_license" {{ old('id_type') === 'drivers_license' ? 'selected' : '' }}>Driver's License</option>
                            <option value="umid"            {{ old('id_type') === 'umid'            ? 'selected' : '' }}>UMID</option>
                            <option value="postal_id"       {{ old('id_type') === 'postal_id'       ? 'selected' : '' }}>Postal ID</option>
                            <option value="voters_id"       {{ old('id_type') === 'voters_id'       ? 'selected' : '' }}>Voter's ID</option>
                            <option value="national_id"     {{ old('id_type') === 'national_id'     ? 'selected' : '' }}>National ID</option>
                            <option value="other"           {{ old('id_type') === 'other'           ? 'selected' : '' }}>Other</option>
                        </select>
                        <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('id_type')" />
                    </div>

                    {{-- 1b: File upload — revealed after ID type is selected --}}
                    <div id="gov_id_upload_wrapper"
                         class="{{ old('id_type') ? '' : 'hidden' }} space-y-3">

                        <p class="text-sm text-neutral-500 dark:text-neutral-400">
                            Upload a clear photo of your
                            <span id="id_type_label" class="font-semibold text-[#1B3B36] dark:text-white">ID</span>.
                        </p>
                        <p class="text-xs text-neutral-400">Accepted: JPG, PNG • Max size: 5MB</p>
                        <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('gov_id_path')" />

                        <input id="gov_id_path" name="gov_id_path" type="file" accept="image/*" class="hidden">

                        {{-- Before file chosen --}}
                        <label id="gov_id_select_btn" for="gov_id_path"
                               class="inline-block bg-primary hover:bg-primary-600 text-white font-bold text-sm
                                      px-8 py-3 rounded-xl shadow-md hover:shadow-lg transition
                                      transform hover:-translate-y-0.5 cursor-pointer">
                            Select ID →
                        </label>

                        {{-- After file chosen --}}
                        <div id="gov_id_confirm_section" class="hidden space-y-3">
                            <p class="text-sm text-neutral-600 dark:text-neutral-300 font-medium">
                                <span class="text-green-600 dark:text-green-400">✓</span>
                                <span id="gov_id_filename"></span>
                            </p>
                            <button type="submit" name="action" value="upload_id"
                                    class="bg-primary hover:bg-primary-600 text-white font-bold text-sm
                                           px-8 py-3 rounded-xl shadow-md hover:shadow-lg transition
                                           transform hover:-translate-y-0.5">
                                Upload ID →
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
            <div class="p-6 rounded-xl bg-primary/5 border-2 border-dashed border-primary/30">
                <div class="text-center space-y-4">
                    <div class="text-5xl">📸</div>
                    <h4 class="text-lg font-black text-[#1B3B36] dark:text-white">Take a Selfie with Your ID</h4>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400 max-w-sm mx-auto">
                        Hold your <strong>{{ ucfirst(str_replace('_', ' ', $user->id_type ?? 'ID')) }}</strong>
                        next to your face. Both must be clearly visible.
                    </p>
                    <p class="text-xs text-neutral-400">Accepted: JPG, PNG • Max size: 5MB</p>
                    <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('selfie_photo')" />

                    <input id="selfie_photo" name="selfie_photo" type="file" accept="image/*" class="hidden">

                    {{-- Before file chosen --}}
                    <label id="selfie_select_btn" for="selfie_photo"
                           class="inline-block bg-primary hover:bg-primary-600 text-white font-bold text-sm
                                  px-8 py-3 rounded-xl shadow-md hover:shadow-lg transition
                                  transform hover:-translate-y-0.5 cursor-pointer">
                        Select Selfie →
                    </label>

                    {{-- After file chosen --}}
                    <div id="selfie_confirm_section" class="hidden space-y-3">
                        <p class="text-sm text-neutral-600 dark:text-neutral-300 font-medium">
                            <span class="text-green-600 dark:text-green-400">✓</span>
                            <span id="selfie_filename"></span>
                        </p>
                        <button type="submit" name="action" value="upload_selfie"
                                class="bg-primary hover:bg-primary-600 text-white font-bold text-sm
                                       px-8 py-3 rounded-xl shadow-md hover:shadow-lg transition
                                       transform hover:-translate-y-0.5">
                            Next →
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- ============================================================ --}}
        {{-- STEP 3: SUBMIT FOR VERIFICATION                               --}}
        {{-- ============================================================ --}}
        @if($user->gov_id_path && $user->selfie_photo && $user->id_validation_status === 'unverified')
            <div class="p-6 rounded-xl border-2 border-yellow-400 bg-yellow-50 dark:bg-yellow-950/20">
                <div class="text-center space-y-4">
                    <div class="text-5xl">📋</div>
                    <h4 class="text-lg font-black text-yellow-700 dark:text-yellow-400">Ready to Submit</h4>
                    <p class="text-sm text-yellow-600 dark:text-yellow-400 max-w-sm mx-auto">
                        Your ID and selfie have been uploaded. Submit them for review.
                    </p>
                    <button type="submit" name="action" value="submit_verification"
                            class="bg-primary hover:bg-primary-600 text-white font-bold text-sm
                                   px-8 py-3 rounded-xl shadow-md hover:shadow-lg transition
                                   transform hover:-translate-y-0.5">
                        Submit for Verification →
                    </button>
                </div>
            </div>
        @endif

        {{-- ============================================================ --}}
        {{-- PENDING                                                        --}}
        {{-- ============================================================ --}}
        @if($user->id_validation_status === 'pending')
            <div class="p-6 rounded-xl border-2 border-yellow-400 bg-yellow-50 dark:bg-yellow-950/20">
                <div class="flex flex-col items-center text-center space-y-3">
                    <div class="text-5xl">⏳</div>
                    <h4 class="text-lg font-black text-yellow-700 dark:text-yellow-400">Verification in Progress</h4>
                    <p class="text-sm text-yellow-600 dark:text-yellow-400 max-w-sm">
                        We're reviewing your submitted documents. This usually takes 1–2 business days.
                    </p>
                </div>
            </div>
        @endif

        {{-- ============================================================ --}}
        {{-- VERIFIED                                                       --}}
        {{-- ============================================================ --}}
        @if($user->id_validation_status === 'verified')
            <div class="p-6 rounded-xl border-2 border-green-500 bg-green-50 dark:bg-green-950/20">
                <div class="flex flex-col items-center text-center space-y-3">
                    <div class="text-5xl">✅</div>
                    <h4 class="text-lg font-black text-green-600 dark:text-green-400">Identity Verified!</h4>
                    <p class="text-sm text-green-600 dark:text-green-400 max-w-sm">
                        Your identity has been successfully verified. You can now:
                    </p>
                    <ul class="text-xs text-green-600 dark:text-green-400 text-left space-y-1">
                        <li>✓ Post sitting requests</li>
                        <li>✓ Apply to become a sitter</li>
                        <li>✓ Receive higher trust ratings</li>
                    </ul>
                </div>
            </div>
        @endif

        {{-- ============================================================ --}}
        {{-- REJECTED                                                       --}}
        {{-- ============================================================ --}}
        @if($user->id_validation_status === 'rejected')
            <div class="p-6 rounded-xl border-2 border-red-500 bg-red-50 dark:bg-red-950/20">
                <div class="flex flex-col items-center text-center space-y-3">
                    <div class="text-5xl">❌</div>
                    <h4 class="text-lg font-black text-red-600 dark:text-red-400">Verification Failed</h4>
                    <p class="text-sm text-red-600 dark:text-red-400 max-w-sm">
                        Your submission was rejected. Please upload a clear selfie holding your valid ID and try again.
                    </p>
                    <a href="#" onclick="document.getElementById('retry_form').submit()"
                       class="text-sm font-bold text-primary hover:underline">
                        Retry Verification →
                    </a>
                </div>
            </div>
        @endif

        {{-- Flash message --}}
        @if(session('status') === 'id-updated')
            <p x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
               class="text-sm font-medium text-green-600 dark:text-green-400">
                ✅ Photo uploaded successfully.
            </p>
        @endif

    </form>
</section>

<script>
(function () {
    const idLabels = {
        passport:        'Passport',
        drivers_license: "Driver's License",
        umid:            'UMID',
        postal_id:       'Postal ID',
        voters_id:       "Voter's ID",
        national_id:     'National ID',
        other:           'Government-issued ID',
    };

    // ── Step 1: reveal upload area when ID type is picked ──────────────
    const idTypeSelect   = document.getElementById('id_type_select');
    const uploadWrapper  = document.getElementById('gov_id_upload_wrapper');
    const idTypeLabel    = document.getElementById('id_type_label');

    function syncIdType(value) {
        if (value) {
            if (idTypeLabel) idTypeLabel.textContent = idLabels[value] ?? 'ID';
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