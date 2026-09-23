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

    <div class="py-6 sm:py-12 bg-secondary/30 dark:bg-neutral-950/30">
        <div class="w-full sm:max-w-4xl mx-auto px-2 sm:px-16 lg:px-24 space-y-4 sm:space-y-6">

            <!-- ========================================== -->
            <!-- PROFILE PHOTO & COVER                      -->
            <!-- ========================================== -->
            <div class="bg-white dark:bg-neutral-900 rounded-3xl shadow-xl border border-gray-200/60 dark:border-neutral-800 overflow-hidden">
                <!-- Cover Photo -->
                <div class="relative h-40 sm:h-52 bg-gradient-to-r from-primary-100 to-accent-100 dark:from-primary-900/30 dark:to-accent-900/30 group">
                    
                    <!-- Cover Image (if uploaded) -->
                    @if(auth()->user()->cover_photo)
                        <img src="{{ asset('storage/' . auth()->user()->cover_photo) }}" 
                            alt="Cover Photo" 
                            class="w-full h-full object-cover">
                    @endif
                    
                    <!-- Cover Upload Overlay / Icon -->
                    <label for="cover_photo" 
                        class="absolute inset-0 flex flex-col items-center justify-center cursor-pointer 
                                bg-black/0 hover:bg-black/30 transition-all duration-300
                                {{ auth()->user()->cover_photo ? 'opacity-0 hover:opacity-100' : 'opacity-100' }}">
                        
                        <!-- Camera Icon -->
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
                        
                        <input type="file" id="cover_photo" name="cover_photo" class="hidden" accept="image/*">
                    </label>
                    
                    <!-- Avatar -->
                    <div class="absolute -bottom-12 left-6 sm:left-8 z-10">
                        <div class="relative">
                            <img src="{{ auth()->user()->profile_photo_url }}" 
                                alt="{{ auth()->user()->name }}" 
                                class="w-24 h-24 sm:w-28 sm:h-28 rounded-full object-cover border-4 border-white dark:border-neutral-900 shadow-lg">
                            <label for="profile_photo" class="absolute bottom-0 right-0 bg-primary hover:bg-primary-600 text-white rounded-full p-1.5 cursor-pointer shadow-md transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </label>
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

    <!-- Script for photo upload -->
    <script>
        document.getElementById('profile_photo')?.addEventListener('change', function(e) {
            const file = this.files[0];
            if (!file) return;
            const formData = new FormData();
            formData.append('profile_photo', file);
            fetch('{{ route('profile.photo') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            }).then(res => res.json()).then(data => {
                if (data.success) location.reload();
            }).catch(() => alert(@js(__('messages.pe_upload_failed'))));
        });
    </script>
</x-app-layout>