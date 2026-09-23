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

                <div class="mt-6">
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">{{ __('messages.pf_password') }}</label>
                    <input id="password" name="password" type="password" placeholder="{{ __('messages.pf_password') }}"
                           class="mt-1 block w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-950 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition text-sm font-medium">
                    <x-input-error class="mt-2 text-xs font-semibold text-red-500" :messages="$errors->userDeletion->get('password')" />
                </div>

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