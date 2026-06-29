<section>
    <form method="post" action="{{ route('profile.location') }}" class="space-y-6">
        @csrf
        @method('patch')

        <!-- City / Address -->
        <div>
            <label for="location" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                {{ __('City / Municipality') }}
            </label>
            <input id="location" name="location" type="text" 
                   value="{{ old('location', $user->location) }}" 
                   placeholder="e.g. Quezon City"
                   class="mt-1 block w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium">
            <p class="mt-1 text-xs text-neutral-400 dark:text-neutral-500">Your city helps us match you with nearby sitters.</p>
            <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('location')" />
        </div>

        <!-- Latitude & Longitude (optional manual entry) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="latitude" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                    {{ __('Latitude') }}
                </label>
                <input id="latitude" name="latitude" type="number" step="any" 
                       value="{{ old('latitude', $user->latitude) }}" 
                       placeholder="e.g. 14.5995"
                       class="mt-1 block w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium">
                <p class="mt-1 text-xs text-neutral-400 dark:text-neutral-500">Optional – auto‑filled if you use “Detect Location”.</p>
                <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('latitude')" />
            </div>

            <div>
                <label for="longitude" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                    {{ __('Longitude') }}
                </label>
                <input id="longitude" name="longitude" type="number" step="any" 
                       value="{{ old('longitude', $user->longitude) }}" 
                       placeholder="e.g. 120.9842"
                       class="mt-1 block w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium">
                <p class="mt-1 text-xs text-neutral-400 dark:text-neutral-500">Optional – auto‑filled if you use “Detect Location”.</p>
                <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('longitude')" />
            </div>
        </div>

        <!-- Detect Location Button -->
        <div>
            <button type="button" id="detect-location" 
                    class="inline-flex items-center gap-2 text-sm font-bold text-primary hover:text-primary-600 dark:text-primary-400 dark:hover:text-primary-300 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                {{ __('Detect My Current Location') }}
            </button>
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="bg-primary hover:bg-primary-600 text-white font-bold text-sm px-6 py-3 rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5">
                {{ __('Update Location') }}
            </button>

            @if (session('status') === 'location-updated')
                <p x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                   class="text-sm font-medium text-green-600 dark:text-green-400">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>

<script>
    document.getElementById('detect-location')?.addEventListener('click', function() {
        if (!navigator.geolocation) {
            alert('Geolocation is not supported by your browser.');
            return;
        }
        navigator.geolocation.getCurrentPosition(
            function(position) {
                document.getElementById('latitude').value = position.coords.latitude;
                document.getElementById('longitude').value = position.coords.longitude;
                // Optionally fill the city field using reverse geocoding (requires Google Maps API)
                // For now, just fill lat/lng.
                alert('Location detected! Latitude and longitude have been filled.');
            },
            function(error) {
                alert('Unable to retrieve location: ' + error.message);
            }
        );
    });
</script>