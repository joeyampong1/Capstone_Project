<section>
    <form method="post" action="{{ route('password.update') }}" class="space-y-4 sm:space-y-6">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                {{ __('messages.pf_current_password') }}
            </label>
            <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password"
                   class="mt-1 block w-full px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium">
            <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->updatePassword->get('current_password')" />
        </div>

        <div>
            <label for="update_password_password" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                {{ __('messages.pf_new_password') }}
            </label>
            <input id="update_password_password" name="password" type="password" autocomplete="new-password"
                   class="mt-1 block w-full px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium">
            <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->updatePassword->get('password')" />
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                {{ __('messages.pf_confirm_password') }}
            </label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                   class="mt-1 block w-full px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium">
            <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->updatePassword->get('password_confirmation')" />
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
            <button type="submit" 
                    class="w-full sm:w-auto bg-primary hover:bg-primary-600 text-white font-bold text-sm 
                           px-6 py-2.5 sm:py-3 rounded-xl shadow-md hover:shadow-lg transition 
                           transform hover:-translate-y-0.5">
                {{ __('messages.pf_update_password_btn') }}
            </button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                   class="inline-flex items-center gap-1.5 text-sm font-medium text-green-600 dark:text-green-400 
                          justify-center sm:justify-start">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ __('messages.pf_saved') }}
                </p>
            @endif
        </div>
    </form>
</section>