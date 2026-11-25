<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <!-- Back button – goes to previous page -->
            <a href="javascript:void(0)"
               onclick="history.back()"
               class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition"
               aria-label="Go back">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h2 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                    {{ __('messages.pe_title') }}
                </h2>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.pe_subtitle') }}</p>
            </div>
        </div>
    </x-slot>

    <!-- Cropper.js styles (photo adjust modal) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
    <style>
        /* Round crop mask for the profile photo */
        .crop-round .cropper-view-box,
        .crop-round .cropper-face { border-radius: 50%; }
        /* Keep the image from being dragged/selected by the browser on touch devices */
        #cropImage { display: block; max-width: 100%; }
    </style>

    <div class="py-6 sm:py-12 bg-secondary/30 dark:bg-neutral-950/30">
        <div class="w-full sm:max-w-4xl mx-auto px-2 sm:px-16 lg:px-24 space-y-4 sm:space-y-6">

            <!-- ========================================== -->
            <!-- PROFILE PHOTO & COVER                      -->
            <!-- ========================================== -->
            <div class="bg-white dark:bg-neutral-900 rounded-3xl shadow-xl border border-gray-200/60 dark:border-neutral-800 overflow-hidden">
                <!-- Cover Photo -->
                <div class="relative h-40 sm:h-52 bg-gradient-to-r from-[#1B3B36] via-primary-700 to-accent-600 dark:from-neutral-800 dark:via-primary-950 dark:to-neutral-900 group">

                    @if(!auth()->user()->cover_photo)
                        <div class="absolute inset-0 flex items-center justify-center opacity-15 pointer-events-none select-none">
                            <svg class="w-28 h-28 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif

                    @if(auth()->user()->cover_photo_url && auth()->user()->cover_photo)
                        <img src="{{ auth()->user()->cover_photo_url }}"
                            alt="Cover Photo"
                            class="w-full h-full object-cover">
                    @endif

                    <!-- Cover Camera Trigger & Menu -->
                    <div class="absolute inset-0 z-10">
                        <button type="button"
                            id="coverCameraBtn"
                            aria-haspopup="menu"
                            aria-expanded="false"
                            class="w-full h-full flex flex-col items-center justify-center cursor-pointer
                                    bg-black/0 hover:bg-black/30 transition-all duration-300
                                    {{ auth()->user()->cover_photo ? 'opacity-0 hover:opacity-100 focus:opacity-100' : 'opacity-100' }}">

                            <svg class="w-12 h-12 text-white drop-shadow-lg {{ auth()->user()->cover_photo ? 'scale-75' : 'scale-100' }} transition-transform"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>

                            <span class="text-white text-xs font-bold mt-2 drop-shadow-md {{ auth()->user()->cover_photo ? 'opacity-0 group-hover:opacity-100' : 'opacity-100' }} transition-opacity">
                                {{ auth()->user()->cover_photo ? __('messages.pe_change_cover') : __('messages.pe_add_cover') }}
                            </span>
                        </button>

                        <div id="coverPhotoMenu"
                            role="menu"
                            class="hidden absolute top-3 right-3 z-30 w-44 rounded-2xl bg-white dark:bg-neutral-800 shadow-xl border border-gray-200/80 dark:border-neutral-700 py-1.5 text-left">
                            <button type="button"
                                id="coverPickOption"
                                role="menuitem"
                                class="w-full px-3.5 py-2 text-xs font-bold text-neutral-700 dark:text-neutral-200 hover:bg-neutral-100 dark:hover:bg-neutral-700 flex items-center gap-2 transition">
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ auth()->user()->cover_photo ? __('messages.pe_change_cover') : __('messages.pe_add_cover') }}
                            </button>
                            @if(auth()->user()->cover_photo)
                                <button type="button"
                                    id="removeCoverOption"
                                    role="menuitem"
                                    class="w-full px-3.5 py-2 text-xs font-bold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 flex items-center gap-2 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Remove cover
                                </button>
                            @endif
                        </div>

                        <input type="file" id="cover_photo" name="cover_photo" class="hidden" accept="image/*">
                    </div>

                    <!-- Avatar -->
                    <div class="absolute -bottom-12 left-6 sm:left-8 z-20">
                        <div class="relative">
                            @if(auth()->user()->profile_photo)
                                <img src="{{ auth()->user()->profile_photo_url }}"
                                    alt="{{ auth()->user()->name }}"
                                    class="w-24 h-24 sm:w-28 sm:h-28 rounded-full object-cover border-4 border-white dark:border-neutral-900 shadow-lg bg-neutral-100 dark:bg-neutral-800">
                            @else
                                <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-full flex items-center justify-center border-4 border-white dark:border-neutral-900 shadow-lg bg-primary text-white">
                                    <svg class="w-12 h-12 sm:w-14 sm:h-14" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 12c2.761 0 5-2.239 5-5s-2.239-5-5-5-5 2.239-5 5 2.239 5 5 5zm0 2c-3.866 0-7 2.239-7 5v1a1 1 0 001 1h12a1 1 0 001-1v-1c0-2.761-3.134-5-7-5z"/>
                                    </svg>
                                </div>
                            @endif

                            <button type="button"
                                id="profileCameraBtn"
                                aria-haspopup="menu"
                                aria-expanded="false"
                                class="absolute bottom-0 right-0 bg-primary hover:bg-primary-600 text-white rounded-full p-1.5 cursor-pointer shadow-md border-2 border-white dark:border-neutral-900 transition"
                                title="Photo options">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </button>

                            <div id="profilePhotoMenu"
                                role="menu"
                                class="hidden absolute bottom-10 left-0 z-30 w-44 rounded-2xl bg-white dark:bg-neutral-800 shadow-xl border border-gray-200/80 dark:border-neutral-700 py-1.5 text-left">
                                <button type="button"
                                    id="profilePickOption"
                                    role="menuitem"
                                    class="w-full px-3.5 py-2 text-xs font-bold text-neutral-700 dark:text-neutral-200 hover:bg-neutral-100 dark:hover:bg-neutral-700 flex items-center gap-2 transition">
                                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    {{ auth()->user()->profile_photo ? 'Change photo' : 'Upload photo' }}
                                </button>
                                @if(auth()->user()->profile_photo)
                                    <button type="button"
                                        id="removeProfileOption"
                                        role="menuitem"
                                        class="w-full px-3.5 py-2 text-xs font-bold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 flex items-center gap-2 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Remove photo
                                    </button>
                                @endif
                            </div>

                            <input type="file" id="profile_photo" name="profile_photo" class="hidden" accept="image/*">
                        </div>
                    </div>
                </div>

                <!-- User info -->
                <div class="pt-16 pb-6 px-6 sm:px-8">
                    <h3 class="text-xl font-black text-[#1B3B36] dark:text-white">{{ auth()->user()->name }}</h3>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ auth()->user()->email }}</p>
                    <div class="mt-2 flex items-center gap-2 flex-wrap">
                        <!-- Role Badge -->
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-accent/10 text-accent dark:bg-accent/20">
                            {{ auth()->user()->role === 'admin' ? __('messages.pe_badge_admin') : __('messages.pe_badge_user') }}
                        </span>
                        <!-- ID Validation Status -->
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ auth()->user()->id_validation_status === 'verified' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' :
                               (auth()->user()->id_validation_status === 'pending' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' :
                               'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400') }}">
                            {{ __('messages.pe_badge_id', ['status' => ucfirst(auth()->user()->id_validation_status)]) }}
                        </span>
                        @if(auth()->user()->isSitter())
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary/10 text-primary">
                                {{ __('messages.pf_badge_level', ['level' => auth()->user()->sitter_level]) }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- ID VERIFICATION                            -->
            <!-- ========================================== -->
            <div class="bg-white dark:bg-neutral-900 rounded-2xl sm:rounded-3xl shadow-sm sm:shadow-xl border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-8">
                <h3 class="text-lg font-black text-[#1B3B36] dark:text-white mb-4">{{ __('messages.pe_id_verification') }}</h3>
                @include('profile.partials.update-id-form')
            </div>

            <!-- ========================================== -->
            <!-- PROFILE INFORMATION                        -->
            <!-- ========================================== -->
            <div class="bg-white dark:bg-neutral-900 rounded-2xl sm:rounded-3xl shadow-sm sm:shadow-xl border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-8">
                <h3 class="text-lg font-black text-[#1B3B36] dark:text-white mb-4">{{ __('messages.pe_profile_information') }}</h3>
                @include('profile.partials.update-profile-information-form')
            </div>

            <!-- ========================================== -->
            <!-- SITTER APPLICATION                         -->
            <!-- ========================================== -->
            @if(auth()->user()->sitter_status === 'approved')
                <div class="bg-white dark:bg-neutral-900 rounded-2xl sm:rounded-3xl shadow-sm sm:shadow-xl border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-8">
                    <h3 class="text-lg font-black text-[#1B3B36] dark:text-white mb-4">{{ __('messages.sa_sitter_details') }}</h3>
                    @include('profile.partials.update-sitter-application-form')
                </div>
            @endif

            <!-- ========================================== -->
            <!-- LOCATION SETTINGS                          -->
            <!-- ========================================== -->
            <div class="bg-white dark:bg-neutral-900 rounded-2xl sm:rounded-3xl shadow-sm sm:shadow-xl border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-8">
                <h3 class="text-lg font-black text-[#1B3B36] dark:text-white mb-4">{{ __('messages.pe_location_contact') }}</h3>
                @include('profile.partials.update-location-form')
            </div>

            <!-- ========================================== -->
            <!-- UPDATE PASSWORD                            -->
            <!-- ========================================== -->
            <div class="bg-white dark:bg-neutral-900 rounded-2xl sm:rounded-3xl shadow-sm sm:shadow-xl border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-8">
                <h3 class="text-lg font-black text-[#1B3B36] dark:text-white mb-4">{{ __('messages.pe_update_password_title') }}</h3>
                @include('profile.partials.update-password-form')
            </div>

            <!-- ========================================== -->
            <!-- DELETE ACCOUNT                             -->
            <!-- ========================================== -->
            <div class="bg-white dark:bg-neutral-900 rounded-2xl sm:rounded-3xl shadow-sm sm:shadow-xl border border-gray-200/60 dark:border-neutral-800 p-4 sm:p-8">
                @include('profile.partials.delete-user-form')
            </div>

        </div>
    </div>

    <!-- ========================================== -->
    <!-- PHOTO ADJUST MODAL (crop / zoom / move)    -->
    <!-- ========================================== -->
    <div id="cropModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-black/70 p-3 sm:p-6" role="dialog" aria-modal="true" aria-labelledby="cropTitle">
        <div class="w-full max-w-2xl bg-white dark:bg-neutral-900 rounded-2xl sm:rounded-3xl shadow-2xl border border-gray-200/60 dark:border-neutral-800 overflow-hidden">

            <!-- Modal header -->
            <div class="px-4 sm:px-6 pt-4 sm:pt-5 pb-3">
                <h3 id="cropTitle" class="text-lg font-black text-[#1B3B36] dark:text-white">Adjust photo</h3>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">Drag to move. Pinch, scroll or use the slider to zoom.</p>
            </div>

            <!-- Cropper area -->
            <div id="cropStage" class="bg-neutral-900 w-full h-[50vh] sm:h-[420px] touch-none">
                <img id="cropImage" alt="Photo to crop">
            </div>

            <!-- Zoom controls -->
            <div class="px-4 sm:px-6 py-4 flex items-center gap-3">
                <button type="button" id="cropZoomOut"
                    class="w-9 h-9 shrink-0 inline-flex items-center justify-center rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition"
                    aria-label="Zoom out">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                </button>
                <input type="range" id="cropZoomRange" min="1" max="4" step="0.01" value="1" class="w-full accent-[#1B3B36]">
                <button type="button" id="cropZoomIn"
                    class="w-9 h-9 shrink-0 inline-flex items-center justify-center rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition"
                    aria-label="Zoom in">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </button>
            </div>

            <!-- Actions -->
            <div class="px-4 sm:px-6 pb-4 sm:pb-6 flex items-center justify-end gap-2">
                <button type="button" id="cropCancel"
                    class="px-4 py-2 rounded-xl text-sm font-bold bg-neutral-100 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-200 hover:bg-neutral-200 dark:hover:bg-neutral-700 transition">
                    Cancel
                </button>
                <button type="button" id="cropSave"
                    class="px-5 py-2 rounded-xl text-sm font-bold bg-primary hover:bg-primary-600 text-white shadow-md transition disabled:opacity-60 disabled:cursor-not-allowed">
                    Save photo
                </button>
            </div>
        </div>
    </div>

    <!-- Script for photo upload with crop/zoom -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
    <script>
        (function () {
            const modal     = document.getElementById('cropModal');
            const stage     = document.getElementById('cropStage');
            const cropImg   = document.getElementById('cropImage');
            const titleEl   = document.getElementById('cropTitle');
            const zoomRange = document.getElementById('cropZoomRange');
            const zoomInBtn = document.getElementById('cropZoomIn');
            const zoomOutBtn= document.getElementById('cropZoomOut');
            const cancelBtn = document.getElementById('cropCancel');
            const saveBtn   = document.getElementById('cropSave');

            const profileCameraBtn = document.getElementById('profileCameraBtn');
            const profilePhotoMenu = document.getElementById('profilePhotoMenu');
            const profilePickOption = document.getElementById('profilePickOption');
            const removeProfileOption = document.getElementById('removeProfileOption');

            const coverCameraBtn = document.getElementById('coverCameraBtn');
            const coverPhotoMenu = document.getElementById('coverPhotoMenu');
            const coverPickOption = document.getElementById('coverPickOption');
            const removeCoverOption = document.getElementById('removeCoverOption');

            const MAX_ZOOM = 4;
            let cropper    = null;
            let objectUrl  = null;
            let baseRatio  = 1;
            let current    = null;

            const CONFIG = {
                profile_photo: { title: 'Adjust profile photo', aspect: 1,     round: true,  maxSize: 800  },
                cover_photo:   { title: 'Adjust cover photo',   aspect: 3 / 1, round: false, maxSize: 1800 }
            };

            function closeMenus(exceptMenu) {
                [profilePhotoMenu, coverPhotoMenu].forEach(menu => {
                    if (menu && menu !== exceptMenu) {
                        menu.classList.add('hidden');
                    }
                });
                if (profileCameraBtn) profileCameraBtn.setAttribute('aria-expanded', String(profilePhotoMenu && !profilePhotoMenu.classList.contains('hidden')));
                if (coverCameraBtn) coverCameraBtn.setAttribute('aria-expanded', String(coverPhotoMenu && !coverPhotoMenu.classList.contains('hidden')));
            }

            function toggleMenu(btn, menu, inputId, hasPhoto) {
                if (!hasPhoto) {
                    closeMenus(null);
                    document.getElementById(inputId)?.click();
                    return;
                }
                const isHidden = menu.classList.contains('hidden');
                closeMenus(isHidden ? menu : null);
                if (isHidden) {
                    menu.classList.remove('hidden');
                    btn?.setAttribute('aria-expanded', 'true');
                } else {
                    menu.classList.add('hidden');
                    btn?.setAttribute('aria-expanded', 'false');
                }
            }

            profileCameraBtn?.addEventListener('click', function (e) {
                e.stopPropagation();
                toggleMenu(profileCameraBtn, profilePhotoMenu, 'profile_photo', {{ auth()->user()->profile_photo ? 'true' : 'false' }});
            });

            coverCameraBtn?.addEventListener('click', function (e) {
                e.stopPropagation();
                toggleMenu(coverCameraBtn, coverPhotoMenu, 'cover_photo', {{ auth()->user()->cover_photo ? 'true' : 'false' }});
            });

            profilePickOption?.addEventListener('click', function (e) {
                e.stopPropagation();
                closeMenus(null);
                document.getElementById('profile_photo')?.click();
            });

            coverPickOption?.addEventListener('click', function (e) {
                e.stopPropagation();
                closeMenus(null);
                document.getElementById('cover_photo')?.click();
            });

            document.addEventListener('click', function (e) {
                if (!profilePhotoMenu?.contains(e.target) && !coverPhotoMenu?.contains(e.target)) {
                    closeMenus(null);
                }
            });

            function openModal()  { modal.classList.remove('hidden'); document.body.classList.add('overflow-hidden'); }
            function closeModal() {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                if (cropper) { cropper.destroy(); cropper = null; }
                if (objectUrl) { URL.revokeObjectURL(objectUrl); objectUrl = null; }
                cropImg.removeAttribute('src');
                saveBtn.disabled = false;
                saveBtn.textContent = 'Save photo';
                current = null;
            }

            function getFitRatio() {
                if (!cropper) return 1;
                const imgData = cropper.getImageData();
                const boxData = cropper.getCropBoxData();
                if (!imgData.naturalWidth || !imgData.naturalHeight || !boxData.width || !boxData.height) {
                    return imgData.ratio || 1;
                }
                return Math.max(boxData.width / imgData.naturalWidth, boxData.height / imgData.naturalHeight);
            }

            function startCropper(file, fieldName, url) {
                const cfg = CONFIG[fieldName];
                current = { fieldName, url, originalName: file.name || 'photo' };

                titleEl.textContent = cfg.title;
                stage.classList.toggle('crop-round', cfg.round);

                objectUrl = URL.createObjectURL(file);
                cropImg.onload = function () {
                    openModal();
                    cropper = new Cropper(cropImg, {
                        aspectRatio: cfg.aspect,
                        viewMode: 1,
                        dragMode: 'move',
                        autoCropArea: 1,
                        cropBoxMovable: false,
                        cropBoxResizable: false,
                        toggleDragModeOnDblclick: false,
                        guides: false,
                        center: false,
                        highlight: false,
                        background: false,
                        responsive: true,
                        checkOrientation: true,
                        ready() {
                            baseRatio = getFitRatio();
                            zoomRange.value = 1;
                        },
                        zoom(e) {
                            const minRatio = getFitRatio();
                            const maxRatio = minRatio * MAX_ZOOM;
                            if (e.detail.ratio < minRatio || e.detail.ratio > maxRatio) {
                                e.preventDefault();
                                const clamped = Math.min(maxRatio, Math.max(minRatio, e.detail.ratio));
                                cropper.zoomTo(clamped);
                                zoomRange.value = (clamped / minRatio).toFixed(2);
                                return;
                            }
                            const v = e.detail.ratio / minRatio;
                            zoomRange.value = Math.min(MAX_ZOOM, Math.max(1, v)).toFixed(2);
                        }
                    });
                };
                cropImg.onerror = function () {
                    closeModal();
                    alert('This photo could not be opened. Please try a JPG or PNG image.');
                };
                cropImg.src = objectUrl;
            }

            zoomRange.addEventListener('input', function () {
                if (!cropper) return;
                const minRatio = getFitRatio();
                const sliderVal = Math.min(MAX_ZOOM, Math.max(1, parseFloat(this.value) || 1));
                cropper.zoomTo(minRatio * sliderVal);
            });
            zoomInBtn.addEventListener('click', function () {
                if (!cropper) return;
                const minRatio = getFitRatio();
                const nextVal = Math.min(MAX_ZOOM, (parseFloat(zoomRange.value) || 1) + 0.2);
                zoomRange.value = nextVal.toFixed(2);
                cropper.zoomTo(minRatio * nextVal);
            });
            zoomOutBtn.addEventListener('click', function () {
                if (!cropper) return;
                const minRatio = getFitRatio();
                const nextVal = Math.max(1, (parseFloat(zoomRange.value) || 1) - 0.2);
                zoomRange.value = nextVal.toFixed(2);
                cropper.zoomTo(minRatio * nextVal);
            });

            cancelBtn.addEventListener('click', closeModal);
            modal.addEventListener('click', function (e) { if (e.target === modal) closeModal(); });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeMenus(null);
                    if (!modal.classList.contains('hidden')) closeModal();
                }
            });

            saveBtn.addEventListener('click', async function () {
                if (!cropper || !current) return;
                saveBtn.disabled = true;
                saveBtn.textContent = 'Saving...';

                const cfg = CONFIG[current.fieldName];
                const canvas = cropper.getCroppedCanvas({
                    maxWidth: cfg.maxSize,
                    maxHeight: cfg.maxSize,
                    fillColor: '#ffffff',
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high'
                });

                if (!canvas) {
                    saveBtn.disabled = false;
                    saveBtn.textContent = 'Save photo';
                    alert(@js(__('messages.pe_upload_failed')));
                    return;
                }

                canvas.toBlob(async function (blob) {
                    if (!blob) {
                        saveBtn.disabled = false;
                        saveBtn.textContent = 'Save photo';
                        alert(@js(__('messages.pe_upload_failed')));
                        return;
                    }
                    const baseName = current.originalName.replace(/\.[^.]+$/, '') || 'photo';
                    const file = new File([blob], baseName + '.jpg', { type: 'image/jpeg', lastModified: Date.now() });
                    const ok = await uploadProfileImage(file, current.fieldName, current.url);
                    if (!ok) {
                        saveBtn.disabled = false;
                        saveBtn.textContent = 'Save photo';
                    }
                }, 'image/jpeg', 0.88);
            });

            async function uploadProfileImage(file, fieldName, url) {
                const formData = new FormData();
                formData.append(fieldName, file, file.name);

                try {
                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const data = await res.json();

                    if (res.ok && data.success) {
                        location.reload();
                        return true;
                    }
                    const validationErr = data.errors ? Object.values(data.errors).flat()[0] : null;
                    alert('Upload failed: ' + (validationErr || data.error || data.message || 'Unknown error'));
                    return false;
                } catch (err) {
                    alert(@js(__('messages.pe_upload_failed')));
                    return false;
                }
            }

            async function removePhoto(url, confirmMessage) {
                closeMenus(null);
                if (!confirm(confirmMessage)) return;

                try {
                    const res = await fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });

                    const data = await res.json();

                    if (res.ok && data.success) {
                        location.reload();
                    } else {
                        alert('Failed to remove photo: ' + (data.error || data.message || 'Server Error'));
                    }
                } catch (err) {
                    alert('Failed to remove photo: Server Error');
                }
            }

            removeProfileOption?.addEventListener('click', function (e) {
                e.stopPropagation();
                removePhoto('{{ route('profile.photo.remove') }}', 'Remove your profile photo and revert to the default icon?');
            });

            removeCoverOption?.addEventListener('click', function (e) {
                e.stopPropagation();
                removePhoto('{{ route('profile.cover.remove') }}', 'Remove your cover photo and revert to the default cover?');
            });

            function bindInput(id, fieldName, url) {
                document.getElementById(id)?.addEventListener('change', function () {
                    const file = this.files[0];
                    this.value = '';
                    if (file) startCropper(file, fieldName, url);
                });
            }

            bindInput('profile_photo', 'profile_photo', '{{ route('profile.photo') }}');
            bindInput('cover_photo',   'cover_photo',   '{{ route('profile.cover') }}');
        })();
    </script>
</x-app-layout>

