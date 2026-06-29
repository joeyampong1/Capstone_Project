<x-app-layout>
<div class="min-h-screen bg-[#FAF8F5] dark:bg-neutral-950 pb-16">

    {{-- ================================================================ --}}
    {{-- BACK LINK                                                         --}}
    {{-- ================================================================ --}}
    <div class="max-w-4xl mx-auto px-4 pt-6 pb-2">
        <a href="{{ route('find.sitter') }}"
           class="inline-flex items-center gap-1 text-sm text-neutral-500 hover:text-primary transition font-medium">
            ← Back to sitters
        </a>
    </div>

    {{-- ================================================================ --}}
    {{-- MAIN PROFILE CARD                                                 --}}
    {{-- ================================================================ --}}
    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 overflow-hidden">

            {{-- Hero image (static) --}}
            <div class="relative h-64 sm:h-72 overflow-hidden">
                <div class="absolute inset-0 bg-cover bg-center bg-fixed"
                     style="background-image: url('{{ asset('assets/images/sitter-maria.jpg') }}');">
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent"></div>

                {{-- Name + badges --}}
                <div class="absolute bottom-0 left-0 p-5">
                    <h1 class="text-2xl font-black text-white leading-tight">
                        Maria Santos
                    </h1>
                    <div class="flex items-center flex-wrap gap-2 mt-1.5">
                        <span class="flex items-center gap-1 text-xs text-white/80 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Makati City
                        </span>
                        <span class="flex items-center gap-1 bg-green-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-full">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                            ID Verified
                        </span>
                    </div>
                </div>
            </div>

            {{-- Info section --}}
            <div class="p-5 sm:p-7 space-y-5">

                {{-- Rating + Daily Rate --}}
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-1.5">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                            <span class="text-sm font-bold text-neutral-700 dark:text-neutral-300 ml-1">4.8</span>
                        </div>
                        <p class="text-xs text-neutral-400 mt-0.5">24 reviews</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-neutral-400">Daily rate</p>
                        <p class="text-2xl font-black text-primary leading-tight">₱350</p>
                        <p class="text-[10px] text-neutral-400">+₱100/day if sitter provides food</p>
                    </div>
                </div>

                {{-- Bio --}}
                <p class="text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                    I love caring for pets and ensuring they feel safe and happy. With 2 years of experience, I treat every pet like family.
                </p>

                {{-- Info chips --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="flex items-start gap-2.5 bg-primary/5 rounded-xl p-3">
                        <svg class="w-4 h-4 text-primary mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <div>
                            <p class="text-xs font-bold text-primary">Food Arrangement</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">Flexible (both options)</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-2.5 bg-primary/5 rounded-xl p-3">
                        <svg class="w-4 h-4 text-primary mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <div>
                            <p class="text-xs font-bold text-primary">Available Dates</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">12 dates open</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-2.5 bg-primary/5 rounded-xl p-3">
                        <svg class="w-4 h-4 text-primary mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        <div>
                            <p class="text-xs font-bold text-primary">Pets Accepted</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">dogs, cats</p>
                        </div>
                    </div>
                </div>

                {{-- CTA Buttons --}}
                <div class="flex gap-3 pt-1">
                    {{-- Visit Profile Button (NEW) --}}
                    <a href="{{ route('public.profile', ['id' => 1]) }}"
                       class="flex-1 flex items-center justify-center gap-2 border-2 border-gray-300 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300
                              font-bold text-sm py-3.5 rounded-xl hover:bg-neutral-100 dark:hover:bg-neutral-800 transition transform hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Visit Profile
                    </a>

                    {{-- Message button --}}
                    <a href="{{ route('owner.messages', ['id' => 1]) }}"
                       class="flex-1 flex items-center justify-center gap-2 border-2 border-primary text-primary
                              font-bold text-sm py-3.5 rounded-xl hover:bg-primary/5 transition transform hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        Message
                    </a>

                    {{-- Book button --}}
                    <a href="#"
                       class="flex-[2] flex items-center justify-center gap-2 bg-primary hover:bg-primary-600
                              text-white font-bold text-sm py-3.5 rounded-xl shadow-md hover:shadow-lg
                              transition transform hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Book This Sitter
                    </a>
                </div>

            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- RATINGS & REVIEWS (static)                                    --}}
        {{-- ============================================================ --}}
        <div class="mt-4 bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 p-5 sm:p-7">
            <h2 class="text-base font-black text-neutral-800 dark:text-white flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                Ratings & Reviews (24)
            </h2>

            <div class="py-4 border-b border-gray-100 dark:border-neutral-800">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-xs font-bold text-primary shrink-0">J</div>
                        <div>
                            <p class="text-sm font-bold text-neutral-800 dark:text-white leading-tight">Juan Dela Cruz</p>
                            <div class="flex items-center gap-0.5 mt-0.5">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                        </div>
                    </div>
                    <span class="text-[11px] text-neutral-400 shrink-0">2 weeks ago</span>
                </div>
                <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-2 leading-relaxed pl-10">
                    "Maria was amazing with my two cats! She sent regular updates and photos. Highly recommend!"
                </p>
            </div>

            <div class="py-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-xs font-bold text-primary shrink-0">M</div>
                        <div>
                            <p class="text-sm font-bold text-neutral-800 dark:text-white leading-tight">Maria Santos</p>
                            <div class="flex items-center gap-0.5 mt-0.5">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                        </div>
                    </div>
                    <span class="text-[11px] text-neutral-400 shrink-0">1 month ago</span>
                </div>
                <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-2 leading-relaxed pl-10">
                    "Very professional and caring. My dog loved her! Will definitely book again."
                </p>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- COMMUNITY CHAT (static)                                       --}}
        {{-- ============================================================ --}}
        <div class="mt-4 bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 p-5 sm:p-7">
            <h2 class="text-base font-black text-neutral-800 dark:text-white flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                Community Chat (2)
            </h2>

            <div class="space-y-4">
                <div class="flex items-start gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-xs font-bold text-primary shrink-0 mt-0.5">A</div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-baseline justify-between gap-2">
                            <span class="text-sm font-bold text-neutral-800 dark:text-white leading-tight">Anna Reyes</span>
                            <span class="text-[11px] text-neutral-400 shrink-0">3 days ago</span>
                        </div>
                        <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-0.5 leading-relaxed">
                            How many visits can you do in a day?
                        </p>
                        <button type="button"
                                class="text-xs text-primary font-semibold mt-1 hover:underline">
                            ← Reply
                        </button>
                    </div>
                </div>

                <div class="flex items-start gap-2.5 pl-6 border-l-2 border-primary/20 ml-3">
                    <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-xs font-bold text-primary shrink-0 mt-0.5">M</div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-baseline justify-between gap-2">
                            <span class="text-sm font-bold text-neutral-800 dark:text-white leading-tight">Maria Santos</span>
                            <span class="text-[11px] text-neutral-400 shrink-0">2 days ago</span>
                        </div>
                        <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-0.5 leading-relaxed">
                            I can do up to 4 visits per day, depending on the location. 🐾
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-xs font-bold text-primary shrink-0 mt-0.5">P</div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-baseline justify-between gap-2">
                            <span class="text-sm font-bold text-neutral-800 dark:text-white leading-tight">Pedro Gomez</span>
                            <span class="text-[11px] text-neutral-400 shrink-0">1 week ago</span>
                        </div>
                        <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-0.5 leading-relaxed">
                            Do you accept rabbits?
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- end max-w-4xl --}}
</div>
</x-app-layout>