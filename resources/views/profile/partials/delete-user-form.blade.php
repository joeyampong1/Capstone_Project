<section>
    <div class="space-y-3 sm:space-y-4">
        <header>
            <h2 class="text-lg font-black text-red-600 dark:text-red-400">{{ __('messages.pf_delete_account') }}</h2>
            <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                {{ __('messages.pf_delete_account_desc') }}
            </p>
        </header>

        <x-danger-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                         class="bg-red-600 hover:bg-red-700 text-white font-bold text-sm px-4 py-2.5 sm:px-6 sm:py-3 rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5">
            {{ __('messages.pf_delete_account') }}
        </x-danger-button>

        <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
            <form method="post" action="{{ route('profile.destroy') }}" class="p-5 sm:p-6">
                @csrf
                @method('delete')

                <h2 class="text-lg font-black text-[#1B3B36] dark:text-white">{{ __('messages.pf_delete_confirm_title') }}</h2>

                <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                    {{ __('messages.pf_delete_confirm_desc') }}
                </p>

                {{-- ✅ FIX ISSUE #41: Hide password field for Google users --}}
                @if(!auth()->user()->google_id && auth()->user()->password)
                    <div class="mt-6">
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">{{ __('messages.pf_password') }}</label>
                        <input id="password" name="password" type="password" placeholder="{{ __('messages.pf_password') }}"
                               class="mt-1 block w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium">
                        <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->userDeletion->get('password')" />
                    </div>
                @else
                    <div class="mt-6 p-4 rounded-xl bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800">
                        <div class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <p class="text-sm text-amber-700 dark:text-amber-400 font-bold">
                                {{ __('messages.pf_google_delete_warning') }}
                            </p>
                        </div>
                    </div>
                @endif

                <div class="mt-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-2 sm:gap-3">
                    <x-secondary-button x-on:click="$dispatch('close')"
                        class="w-full sm:w-auto bg-neutral-200 dark:bg-neutral-700 hover:bg-neutral-300 dark:hover:bg-neutral-600 text-gray-800 dark:text-white font-bold px-4 py-2.5 sm:px-6 sm:py-3 rounded-xl transition justify-center">
                        {{ __('messages.pf_cancel') }}
                    </x-secondary-button>

                    <x-danger-button
                        class="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white font-bold px-4 py-2.5 sm:px-6 sm:py-3 rounded-xl transition justify-center">
                        {{ __('messages.pf_delete_account') }}
                    </x-danger-button>
                </div>
            </form>
        </x-modal>
    </div>
</section>
