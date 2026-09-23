<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="javascript:history.back()"
               class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                    {{ __('messages.cs_title') }}
                </h1>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.cs_subtitle') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            <form method="POST" action="{{ route('support.store') }}" class="space-y-6">
                @csrf

                <!-- CONTACT SUPPORT FORM -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-5 sm:p-6">
                    <h2 class="font-bold text-lg text-[#1B3B36] dark:text-white flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        {{ __('messages.cs_header') }}
                    </h2>

                    <div class="space-y-4">
                        <!-- Subject -->
                        <div>
                            <label for="subject" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('messages.cs_subject') }}
                            </label>
                            <input type="text" id="subject" name="subject" required maxlength="200"
                                   value="{{ old('subject') }}"
                                   placeholder="{{ __('messages.cs_subject_ph') }}"
                                   class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 px-4 py-2.5 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary text-sm font-medium">
                            @error('subject')
                                <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('messages.cs_category') }}
                            </label>
                            <select id="category" name="category" required
                                    class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 px-4 py-2.5 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary text-sm font-medium">
                                <option value="">{{ __('messages.cs_select_category') }}</option>
                                <option value="Booking Issues"          @selected(old('category') === 'Booking Issues')>{{ __('messages.cs_cat_booking') }}</option>
                                <option value="Payment Questions"       @selected(old('category') === 'Payment Questions')>{{ __('messages.cs_cat_payment') }}</option>
                                <option value="Account Management"      @selected(old('category') === 'Account Management')>{{ __('messages.cs_cat_account') }}</option>
                                <option value="Technical Support"       @selected(old('category') === 'Technical Support')>{{ __('messages.cs_cat_technical') }}</option>
                                <option value="Feedback & Suggestions"  @selected(old('category') === 'Feedback & Suggestions')>{{ __('messages.cs_cat_feedback') }}</option>
                                <option value="Other"                   @selected(old('category') === 'Other')>{{ __('messages.cs_cat_other') }}</option>
                            </select>
                            @error('category')
                                <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Message -->
                        <div>
                            <label for="message" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('messages.cs_message') }}
                            </label>
                            <textarea id="message" name="message" rows="6" required maxlength="5000"
                                      placeholder="{{ __('messages.cs_message_ph') }}"
                                      class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 px-4 py-2.5 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary text-sm font-medium resize-none">{{ old('message') }}</textarea>
                            @error('message')
                                <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Attachment -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('messages.cs_attachment') }}
                            </label>
                            <div class="border-2 border-dashed border-gray-300 dark:border-neutral-700 rounded-xl p-4 text-center hover:border-primary/50 transition">
                                <input type="file" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" multiple class="hidden" id="attachment-upload">
                                <label for="attachment-upload" class="cursor-pointer block">
                                    <svg class="w-8 h-8 mx-auto text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                    </svg>
                                    <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                                        {{ __('messages.cs_attachment_click') }}
                                    </p>
                                    <p class="text-xs text-neutral-400">{{ __('messages.cs_attachment_hint') }}</p>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CONTACT INFORMATION CARD -->
                <div class="bg-blue-50 dark:bg-blue-950/20 rounded-2xl border border-blue-200 dark:border-blue-800/50 p-5 sm:p-6">
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white">{{ __('messages.cs_info_header') }}</h3>
                            <ul class="mt-2 space-y-1 text-sm text-neutral-600 dark:text-neutral-300">
                                <li>• {{ __('messages.cs_info_1') }}</li>
                                <li>• {{ __('messages.cs_info_2') }}</li>
                                <li>• {{ __('messages.cs_info_3') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- ACTION BUTTONS -->
                <div class="flex flex-col sm:flex-row items-center gap-3 pt-2">
                    <a href="javascript:history.back()"
                       class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-sm rounded-xl hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                        {{ __('messages.cs_cancel') }}
                    </a>
                    <button type="submit"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-primary hover:bg-primary-600 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ __('messages.cs_send') }}
                    </button>
                </div>

            </form>

        </div>
    </div>
</x-app-layout>