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
                    {{ __('messages.ri_title') }}
                </h1>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">{{ __('messages.ri_subtitle') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 bg-white dark:bg-neutral-950 min-h-screen antialiased text-neutral-800 dark:text-neutral-200">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            <form method="POST" action="{{ route('report.store') }}" class="space-y-6">
                @csrf

                <!-- REPORT ISSUE FORM -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-200/60 dark:border-neutral-800 p-5 sm:p-6">
                    <h2 class="font-bold text-lg text-[#1B3B36] dark:text-white flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        {{ __('messages.ri_header') }}
                    </h2>

                    <div class="space-y-4">
                        <!-- Issue Title -->
                        <div>
                            <label for="issue_title" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('messages.ri_issue_title') }}
                            </label>
                            <input type="text" id="issue_title" name="issue_title" required maxlength="200"
                                   value="{{ old('issue_title') }}"
                                   placeholder="{{ __('messages.ri_issue_title_ph') }}"
                                   class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 px-4 py-2.5 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary text-sm font-medium">
                            @error('issue_title')
                                <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Issue Category -->
                        <div>
                            <label for="issue_category" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('messages.ri_issue_category') }}
                            </label>
                            <select id="issue_category" name="issue_category" required
                                    class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 px-4 py-2.5 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary text-sm font-medium">
                                <option value="">{{ __('messages.ri_select_category') }}</option>
                                <option value="Bug / Technical Error"       @selected(old('issue_category') === 'Bug / Technical Error')>{{ __('messages.ri_cat_bug') }}</option>
                                <option value="UI / Layout Issue"           @selected(old('issue_category') === 'UI / Layout Issue')>{{ __('messages.ri_cat_ui') }}</option>
                                <option value="Performance / Loading Issue" @selected(old('issue_category') === 'Performance / Loading Issue')>{{ __('messages.ri_cat_performance') }}</option>
                                <option value="Payment Error"               @selected(old('issue_category') === 'Payment Error')>{{ __('messages.ri_cat_payment') }}</option>
                                <option value="Feature Not Working"         @selected(old('issue_category') === 'Feature Not Working')>{{ __('messages.ri_cat_feature') }}</option>
                                <option value="Other"                       @selected(old('issue_category') === 'Other')>{{ __('messages.ri_cat_other') }}</option>
                            </select>
                            @error('issue_category')
                                <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('messages.ri_description') }}
                            </label>
                            <textarea id="description" name="description" rows="4" required maxlength="5000"
                                      placeholder="{{ __('messages.ri_description_ph') }}"
                                      class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 px-4 py-2.5 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary text-sm font-medium resize-none">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Steps to Reproduce -->
                        <div>
                            <label for="steps_to_reproduce" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('messages.ri_steps') }}
                            </label>
                            <textarea id="steps_to_reproduce" name="steps_to_reproduce" rows="3" maxlength="5000"
                                      placeholder="{{ __('messages.ri_steps_ph') }}"
                                      class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 px-4 py-2.5 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary text-sm font-medium resize-none">{{ old('steps_to_reproduce') }}</textarea>
                            <p class="mt-1 text-xs text-neutral-400">{{ __('messages.ri_steps_hint') }}</p>
                            @error('steps_to_reproduce')
                                <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Screenshot -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('messages.ri_screenshot') }}
                            </label>
                            <div class="border-2 border-dashed border-gray-300 dark:border-neutral-700 rounded-xl p-4 text-center hover:border-primary/50 transition">
                                <input type="file" accept="image/*" multiple class="hidden" id="screenshot-upload">
                                <label for="screenshot-upload" class="cursor-pointer block">
                                    <svg class="w-8 h-8 mx-auto text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                                        {{ __('messages.ri_screenshot_click') }}
                                    </p>
                                    <p class="text-xs text-neutral-400">{{ __('messages.ri_screenshot_hint') }}</p>
                                </label>
                            </div>
                        </div>

                        <!-- Browser / Device Info -->
                        <div>
                            <label for="device_info" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('messages.ri_device') }}
                            </label>
                            <input type="text" id="device_info" name="device_info" maxlength="200"
                                   value="{{ old('device_info') }}"
                                   placeholder="{{ __('messages.ri_device_ph') }}"
                                   class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 px-4 py-2.5 text-[#1B3B36] dark:text-white focus:border-primary focus:ring-primary text-sm font-medium">
                            <p class="mt-1 text-xs text-neutral-400">{{ __('messages.ri_device_hint') }}</p>
                            @error('device_info')
                                <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- GUIDELINES CARD -->
                <div class="bg-amber-50 dark:bg-amber-950/20 rounded-2xl border border-amber-200 dark:border-amber-800/50 p-5 sm:p-6">
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div>
                            <h3 class="font-bold text-sm text-[#1B3B36] dark:text-white">{{ __('messages.ri_guidelines') }}</h3>
                            <ul class="mt-2 space-y-1 text-sm text-neutral-600 dark:text-neutral-300">
                                <li>• {{ __('messages.ri_guide_1') }}</li>
                                <li>• {{ __('messages.ri_guide_2') }}</li>
                                <li>• {{ __('messages.ri_guide_3') }}</li>
                                <li>• {{ __('messages.ri_guide_4') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- ACTION BUTTONS -->
                <div class="flex flex-col sm:flex-row items-center gap-3 pt-2">
                    <a href="javascript:history.back()"
                       class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 font-bold text-sm rounded-xl hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                        {{ __('messages.ri_cancel') }}
                    </a>
                    <button type="submit"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-primary hover:bg-primary-600 text-white font-bold rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ __('messages.ri_submit') }}
                    </button>
                </div>

            </form>

        </div>
    </div>
</x-app-layout>