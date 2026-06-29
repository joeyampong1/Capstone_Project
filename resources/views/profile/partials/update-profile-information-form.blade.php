<section>
    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <!-- Full Name -->
        <div>
            <label for="name" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                {{ __('Full Name') }}
            </label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name"
                   class="mt-1 block w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium">
            <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('name')" />
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                {{ __('Email Address') }}
            </label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                   class="mt-1 block w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium">
            <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl border border-yellow-200 dark:border-yellow-800/30 text-sm text-yellow-800 dark:text-yellow-400">
                    <p>{{ __('Your email address is unverified.') }}</p>
                    <button form="send-verification" class="font-bold text-primary hover:underline">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-green-600">{{ __('A new verification link has been sent.') }}</p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Date of Birth -->
        <div>
            <label for="date_of_birth" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                {{ __('Date of Birth') }}
            </label>
            <input id="date_of_birth" name="date_of_birth" type="date" 
                   value="{{ old('date_of_birth', $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '') }}"
                   class="mt-1 block w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium">
            <p class="mt-1 text-xs text-neutral-400 dark:text-neutral-500">Will be auto‑filled from your uploaded ID.</p>
            <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('date_of_birth')" />
        </div>

        <!-- Gender -->
        <div>
            <label for="gender" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                {{ __('Gender') }}
            </label>
            <select id="gender" name="gender"
                    class="mt-1 block w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium">
                <option value="">Select gender</option>
                <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Female</option>
                <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Other</option>
            </select>
            <p class="mt-1 text-xs text-neutral-400 dark:text-neutral-500">Will be auto‑filled from your uploaded ID.</p>
            <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('gender')" />
        </div>

        <!-- Contact Number -->
        <div>
            <label for="contact_number" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                {{ __('Contact Number') }}
            </label>
            <input id="contact_number" name="contact_number" type="tel" 
                   value="{{ old('contact_number', $user->contact_number) }}"
                   placeholder="09xx xxx xxxx"
                   class="mt-1 block w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium">
            <p class="mt-1 text-xs text-neutral-400 dark:text-neutral-500">This will be used for booking notifications.</p>
            <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('contact_number')" />
        </div>

        <!-- Address -->
        <div>
            <label for="address" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                {{ __('Address') }}
            </label>
            <textarea id="address" name="address" rows="2"
                      class="mt-1 block w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium">{{ old('address', $user->address) }}</textarea>
            <p class="mt-1 text-xs text-neutral-400 dark:text-neutral-500">Your address for location‑based matching.</p>
            <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->get('address')" />
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="bg-primary hover:bg-primary-600 text-white font-bold text-sm px-6 py-3 rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5">
                {{ __('Save Changes') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                   class="text-sm font-medium text-green-600 dark:text-green-400">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>