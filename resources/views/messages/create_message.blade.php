<x-app-layout>
    <!-- ========================================== -->
    <!-- HEADER SLOT                               -->
    <!-- ========================================== -->
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('owner.sitter.profile', ['id' => 1]) }}" 
               class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="font-extrabold text-2xl text-[#1B3B36] dark:text-white tracking-tight leading-none">
                    Messages
                </h1>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">Chat with Maria Santos</p>
            </div>
        </div>
    </x-slot>

    <!-- ========================================== -->
    <!-- MAIN CONTENT – reduced height             -->
    <!-- ========================================== -->
    <div class="bg-white dark:bg-neutral-950 antialiased text-neutral-800 dark:text-neutral-200 h-[calc(100vh-190px)] lg:h-[calc(100vh-180px)] overflow-hidden">
        <div class="max-w-4xl mx-auto px-12 sm:px-16 lg:px-24 h-full py-4 sm:py-6">

            <!-- ========================================== -->
            <!-- CHAT CARD – flex column                   -->
            <!-- ========================================== -->
            <div class="bg-white dark:bg-neutral-900 rounded-3xl shadow-xl border border-gray-200/60 dark:border-neutral-800 overflow-hidden flex flex-col h-full">

                <!-- ========================================== -->
                <!-- CHAT HEADER – fixed                      -->
                <!-- ========================================== -->
                <div class="flex items-center gap-4 p-3 sm:p-5 border-b border-gray-100 dark:border-neutral-800 flex-shrink-0">
                    <div class="relative">
                        <img src="{{ asset('assets/images/sitter-maria.jpg') }}" 
                             alt="Maria Santos" 
                             class="w-10 h-10 sm:w-12 sm:h-12 rounded-full object-cover border-2 border-primary/20">
                        <span class="absolute bottom-0 right-0 w-2.5 h-2.5 sm:w-3 sm:h-3 bg-green-500 rounded-full border-2 border-white dark:border-neutral-900"></span>
                    </div>
                    <div>
                        <h2 class="font-black text-[#1B3B36] dark:text-white text-sm sm:text-base">Maria Santos</h2>
                        <p class="text-[10px] sm:text-xs text-neutral-500 dark:text-neutral-400">Online • Usually responds in minutes</p>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- MESSAGES THREAD – scrollable              -->
                <!-- ========================================== -->
                <div class="flex-1 overflow-y-auto p-3 sm:p-6 space-y-3 sm:space-y-4 bg-neutral-50/50 dark:bg-neutral-950/30 min-h-0">

                    <!-- Message: Sitter -->
                    <div class="flex items-start gap-2 sm:gap-2.5 max-w-[85%] sm:max-w-[80%]">
                        <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-full bg-primary/10 flex items-center justify-center text-[10px] sm:text-xs font-bold text-primary shrink-0">M</div>
                        <div>
                            <div class="bg-white dark:bg-neutral-800 rounded-2xl rounded-tl-none px-3 py-2 sm:px-4 sm:py-3 shadow-sm border border-gray-100 dark:border-neutral-700">
                                <p class="text-xs sm:text-sm text-neutral-800 dark:text-white leading-relaxed">
                                    Hi there! 👋 I'm available on the dates you requested.
                                </p>
                            </div>
                            <span class="text-[9px] sm:text-[10px] text-neutral-400 mt-0.5 block">10:30 AM</span>
                        </div>
                    </div>

                    <!-- Message: Current User -->
                    <div class="flex items-start gap-2 sm:gap-2.5 max-w-[85%] sm:max-w-[80%] ml-auto flex-row-reverse">
                        <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-full bg-primary flex items-center justify-center text-[10px] sm:text-xs font-bold text-white shrink-0">Y</div>
                        <div>
                            <div class="bg-primary text-white rounded-2xl rounded-tr-none px-3 py-2 sm:px-4 sm:py-3 shadow-sm">
                                <p class="text-xs sm:text-sm leading-relaxed">
                                    That's great! I'd like to book you for 2 visits per day.
                                </p>
                            </div>
                            <span class="text-[9px] sm:text-[10px] text-neutral-400 mt-0.5 block text-right">10:32 AM</span>
                        </div>
                    </div>

                    <!-- Message: Sitter -->
                    <div class="flex items-start gap-2 sm:gap-2.5 max-w-[85%] sm:max-w-[80%]">
                        <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-full bg-primary/10 flex items-center justify-center text-[10px] sm:text-xs font-bold text-primary shrink-0">M</div>
                        <div>
                            <div class="bg-white dark:bg-neutral-800 rounded-2xl rounded-tl-none px-3 py-2 sm:px-4 sm:py-3 shadow-sm border border-gray-100 dark:border-neutral-700">
                                <p class="text-xs sm:text-sm text-neutral-800 dark:text-white leading-relaxed">
                                    Perfect! I can do 9 AM and 6 PM. Does that work for you?
                                </p>
                            </div>
                            <span class="text-[9px] sm:text-[10px] text-neutral-400 mt-0.5 block">10:35 AM</span>
                        </div>
                    </div>

                    <!-- Message: Current User -->
                    <div class="flex items-start gap-2 sm:gap-2.5 max-w-[85%] sm:max-w-[80%] ml-auto flex-row-reverse">
                        <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-full bg-primary flex items-center justify-center text-[10px] sm:text-xs font-bold text-white shrink-0">Y</div>
                        <div>
                            <div class="bg-primary text-white rounded-2xl rounded-tr-none px-3 py-2 sm:px-4 sm:py-3 shadow-sm">
                                <p class="text-xs sm:text-sm leading-relaxed">
                                    Yes, that's perfect! I'll send the booking request now.
                                </p>
                            </div>
                            <span class="text-[9px] sm:text-[10px] text-neutral-400 mt-0.5 block text-right">10:38 AM</span>
                        </div>
                    </div>

                    <!-- Message: Sitter -->
                    <div class="flex items-start gap-2 sm:gap-2.5 max-w-[85%] sm:max-w-[80%]">
                        <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-full bg-primary/10 flex items-center justify-center text-[10px] sm:text-xs font-bold text-primary shrink-0">M</div>
                        <div>
                            <div class="bg-white dark:bg-neutral-800 rounded-2xl rounded-tl-none px-3 py-2 sm:px-4 sm:py-3 shadow-sm border border-gray-100 dark:border-neutral-700">
                                <p class="text-xs sm:text-sm text-neutral-800 dark:text-white leading-relaxed">
                                    Awesome! Looking forward to taking care of your fur babies. 🐾
                                </p>
                            </div>
                            <span class="text-[9px] sm:text-[10px] text-neutral-400 mt-0.5 block">10:40 AM</span>
                        </div>
                    </div>

                    <!-- Scroll spacer -->
                    <div class="h-2"></div>
                </div>

                <!-- ========================================== -->
                <!-- MESSAGE INPUT – fixed at bottom           -->
                <!-- ========================================== -->
                <div class="p-2 sm:p-4 border-t border-gray-100 dark:border-neutral-800 bg-white dark:bg-neutral-900 flex-shrink-0">
                    <form class="flex gap-2 sm:gap-3 items-center">
                        <!-- Attachment button -->
                        <button type="button" 
                                onclick="document.getElementById('fileInput').click()" 
                                class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-500 dark:text-neutral-400 hover:bg-primary/10 hover:text-primary transition flex items-center justify-center flex-shrink-0 cursor-pointer">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                            </svg>
                        </button>
                        <input type="file" id="fileInput" class="hidden" accept="image/*,.pdf,.doc,.docx" multiple>

                        <!-- Text input (auto-expanding) -->
                        <textarea x-data="{ text: '', resize: function() { this.style.height = 'auto'; this.style.height = this.scrollHeight + 'px'; } }"
                                x-init="resize()"
                                x-on:input="resize()"
                                x-model="text"
                                placeholder="Type a message..."
                                rows="1"
                                class="flex-1 px-3 py-2 sm:px-4 sm:py-3 rounded-xl border border-gray-200 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-950 text-sm sm:text-base text-neutral-800 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition min-h-[44px] max-h-32 resize-none overflow-y-auto"
                                @keydown.enter.prevent="if (!$event.shiftKey) { $el.closest('form').dispatchEvent(new Event('submit')); }"
                        ></textarea>
                        
                        <!-- Send button -->
                        <button type="submit" class="bg-primary hover:bg-primary-600 text-white font-bold text-xs sm:text-sm px-4 py-2 sm:px-6 sm:py-3 rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5 flex items-center gap-1.5 sm:gap-2 flex-shrink-0">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            <span class="hidden sm:inline">Send</span>
                        </button>
                    </form>
                    <p class="text-[9px] sm:text-[10px] text-neutral-400 dark:text-neutral-500 text-center mt-1.5">Messages are end-to-end encrypted</p>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>