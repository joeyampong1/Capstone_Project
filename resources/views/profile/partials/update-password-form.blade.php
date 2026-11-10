<section>
    @if(auth()->user()->google_id)
        {{-- Google User Notice --}}
        <div class="p-4 rounded-xl bg-blue-50 dark:bg-blue-950/20 border border-blue-200 dark:border-blue-800">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="min-w-0">
                    <p class="text-sm text-blue-700 dark:text-blue-400 font-bold">
                        {{ __('messages.pf_google_password_notice') }}
                    </p>
                </div>
            </div>
        </div>
    @else
        <form method="post" action="{{ route('password.update') }}" class="space-y-4 sm:space-y-6">
            @csrf
            @method('put')

            {{-- GLOBAL ERROR SUMMARY --}}
            @if($errors->updatePassword->any())
                <div class="p-4 rounded-xl bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-800">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-red-700 dark:text-red-400">Please fix the following errors:</p>
                            <ul class="list-disc list-inside text-xs text-red-600 dark:text-red-400 mt-1 space-y-0.5">
                                @foreach($errors->updatePassword->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <div>
                <label for="update_password_current_password" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                    {{ __('messages.pf_current_password') }}
                </label>
                <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" required
                       class="mt-1 block w-full px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium">
                <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->updatePassword->get('current_password')" />
            </div>

            <div>
                <label for="update_password_password" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                    {{ __('messages.pf_new_password') }}
                </label>
                <input id="update_password_password" name="password" type="password" autocomplete="new-password" required minlength="12"
                       class="mt-1 block w-full px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium">
                <p class="mt-1 text-xs text-neutral-400 dark:text-neutral-500">{{ __('messages.pf_password_hint') }}</p>
                <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->updatePassword->get('password')" />
            </div>

            <div>
                <label for="update_password_password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                    {{ __('messages.pf_confirm_password') }}
                </label>
                <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required minlength="12"
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
    @endif
</section>
