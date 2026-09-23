<section>
    <form method="post" action="{{ route('profile.location') }}" class="space-y-4 sm:space-y-6">
        @csrf
        @method('patch')

        <!-- City / Address -->
        <div>
            <label for="location" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                {{ __('messages.pf_city_municipality') }}
            </label>
            <input id="location" name="location" type="text" 
                   value="{{ old('location', $user->location) }}" 
                   placeholder="{{ __('messages.pf_city_ph') }}"
                   class="mt-1 block w-full px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium">
            <p class="mt-1 text-xs text-neutral-400 dark:text-neutral-500">{{ __('messages.pf_city_hint') }}</p>
            <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('location')" />
        </div>

        <!-- Latitude & Longitude (optional manual entry) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
            <div>
                <label for="latitude" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                    {{ __('messages.pf_latitude') }}
                </label>
                <input id="latitude" name="latitude" type="number" step="any" 
                       value="{{ old('latitude', $user->latitude) }}" 
                       placeholder="{{ __('messages.pf_latitude_ph') }}"
                       class="mt-1 block w-full px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium">
                <p class="mt-1 text-xs text-neutral-400 dark:text-neutral-500">{{ __('messages.pf_optional_geo') }}</p>
                <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('latitude')" />
            </div>

            <div>
                <label for="longitude" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                    {{ __('messages.pf_longitude') }}
                </label>
                <input id="longitude" name="longitude" type="number" step="any" 
                       value="{{ old('longitude', $user->longitude) }}" 
                       placeholder="{{ __('messages.pf_longitude_ph') }}"
                       class="mt-1 block w-full px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium">
                <p class="mt-1 text-xs text-neutral-400 dark:text-neutral-500">{{ __('messages.pf_optional_geo') }}</p>
                <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('longitude')" />
            </div>
        </div>

        <!-- Detect Location Button -->
        <div>
            <button type="button" id="detect-location" 
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 text-sm font-bold 
                           text-primary hover:text-primary-600 dark:text-primary-400 dark:hover:text-primary-300
                           bg-primary/5 hover:bg-primary/10 border border-primary/20
                           px-4 py-2.5 sm:py-2 rounded-xl transition">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                {{ __('messages.pf_detect_location') }}
            </button>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 pt-2">
            <button type="submit" 
                    class="w-full sm:w-auto bg-primary hover:bg-primary-600 text-white font-bold text-sm 
                           px-6 py-2.5 sm:py-3 rounded-xl shadow-md hover:shadow-lg transition 
                           transform hover:-translate-y-0.5">
                {{ __('messages.pf_update_location') }}
            </button>

            @if (session('status') === 'location-updated')
                <p x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                   class="text-sm font-medium text-green-600 dark:text-green-400 text-center sm:text-left">
                    {{ __('messages.pf_saved') }}
                </p>
            @endif
        </div>
    </form>
</section>

<script>
    document.getElementById('detect-location')?.addEventListener('click', function() {
        if (!navigator.geolocation) {
            alert(@js(__('messages.pf_geo_not_supported')));
            return;
        }
        navigator.geolocation.getCurrentPosition(
            function(position) {
                document.getElementById('latitude').value = position.coords.latitude;
                document.getElementById('longitude').value = position.coords.longitude;
                alert(@js(__('messages.pf_geo_success')));
            },
            function(error) {
                alert(@js(__('messages.pf_geo_error', ['error' => ''])) + ' ' + error.message);
            }
        );
    });
</script>