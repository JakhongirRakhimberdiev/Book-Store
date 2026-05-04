<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="authenticated" content="{{ auth()->check() ? '1' : '0' }}">
    <title>{{ $author->name }} — Muallif haqida</title>
    @include('partials.head-tailwind-dark')
    <script src="{{ asset('js/cart.js') }}" defer></script>
</head>
<body class="bg-gray-100 dark:bg-gray-950 transition-colors duration-200">

    {{-- Yuqori header --}}
    <header class="bg-white shadow-sm sticky top-0 z-40 dark:bg-gray-900 dark:border-b dark:border-gray-800 transition-colors duration-200">
        <div class="container mx-auto px-4 py-3 flex items-center justify-between">
            <a href="{{ route('books.index') }}" class="flex items-center gap-2 text-xl font-bold text-blue-700 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-7 h-7">
                    <path d="M5 4a2 2 0 0 1 2-2h11v18H7a2 2 0 0 1-2-2V4zm2 14h11v-2H7v2zM18 0H7a4 4 0 0 0-4 4v16a4 4 0 0 0 4 4h11V0z"/>
                </svg>
                <span class="hidden sm:inline">Online Kitob Do'koni</span>
            </a>

            <div class="flex items-end gap-3 sm:gap-4">
                <a href="{{ route('cart.index') }}" id="cart-link"
                   aria-label="Savatcha" title="Savatcha"
                   class="group flex flex-col items-center text-gray-700 hover:text-blue-600 dark:text-gray-200 dark:hover:text-blue-400 transition active:scale-95">
                    <span class="relative inline-flex items-center justify-center w-11 h-11 rounded-full bg-gray-100 text-gray-700 shadow-sm dark:bg-gray-800 dark:text-gray-200
                                 group-hover:bg-blue-600 group-hover:text-white transition">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="w-5 h-5">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                        </svg>
                        <span id="cart-count"
                              class="hidden absolute -top-1 -right-1 min-w-[20px] h-5 px-1 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center">0</span>
                    </span>
                    <span class="text-[11px] font-semibold leading-none mt-1.5 tracking-wide dark:text-gray-300">Savatcha</span>
                </a>

                @auth
                    @include('partials.logged-in-account-menu')
                @else
                    <a href="{{ route('login') }}" id="account-link"
                       aria-label="Kirish" title="Kirish"
                       class="group flex flex-col items-center text-gray-700 hover:text-blue-600 dark:text-gray-200 dark:hover:text-blue-400 transition active:scale-95">
                        <span class="inline-flex items-center justify-center w-11 h-11 rounded-full bg-gray-100 text-gray-700 shadow-sm dark:bg-gray-800 dark:text-gray-200
                                     group-hover:bg-blue-600 group-hover:text-white transition">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                 class="w-5 h-5">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </span>
                        <span class="text-[11px] font-semibold leading-none mt-1.5 tracking-wide dark:text-gray-300">Kirish</span>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <div class="container mx-auto px-4 py-8">

        <div class="mb-6">
            <a href="{{ route('books.index') }}"
               aria-label="Kutubxonaga qaytish"
               title="Kutubxonaga qaytish"
               class="inline-flex items-center justify-center w-11 h-11 rounded-full bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 shadow-md dark:border dark:border-gray-700
                      hover:bg-blue-600 hover:text-white hover:shadow-lg hover:-translate-x-0.5
                      active:scale-95 transition-all duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                     class="w-5 h-5">
                    <path d="M19 12H5"></path>
                    <path d="M12 19l-7-7 7-7"></path>
                </svg>
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 dark:border dark:border-gray-700 rounded-2xl shadow-md overflow-hidden mb-10">
            <div class="md:flex">
                <div class="md:w-1/3 bg-gray-50 dark:bg-gray-900/50 flex items-center justify-center p-6">
                    @if($author->image_url)
                        <img src="{{ asset($author->image_url) }}"
                             alt="{{ $author->name }}"
                             class="w-64 h-64 rounded-full object-cover bg-white dark:bg-gray-800 shadow-lg ring-4 ring-white dark:ring-gray-700"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="hidden w-64 h-64 rounded-full bg-blue-100 dark:bg-blue-900/40 dark:text-blue-300 items-center justify-center text-blue-600 text-7xl font-bold shadow-lg ring-4 ring-white dark:ring-gray-700">
                            {{ mb_substr($author->name, 0, 1) }}
                        </div>
                    @else
                        <div class="w-64 h-64 rounded-full bg-blue-100 dark:bg-blue-900/40 dark:text-blue-300 flex items-center justify-center text-blue-600 text-7xl font-bold shadow-lg ring-4 ring-white dark:ring-gray-700">
                            {{ mb_substr($author->name, 0, 1) }}
                        </div>
                    @endif
                </div>

                <div class="md:w-2/3 p-8">
                    <h1 class="text-4xl font-bold text-gray-900 dark:text-gray-100 mb-4">{{ $author->name }}</h1>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                        @if($author->birth_date)
                            <div>
                                <span class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Tug'ilgan sana</span>
                                <p class="text-gray-800 dark:text-gray-200 font-medium">{{ $author->birth_date }}</p>
                            </div>
                        @endif
                        @if($author->death_date)
                            <div>
                                <span class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Vafot sanasi</span>
                                <p class="text-gray-800 dark:text-gray-200 font-medium">
                                    {{ $author->death_date }}
                                    @if($author->age !== null)
                                        <span class="text-gray-500 dark:text-gray-400 font-normal">({{ $author->age }} yosh)</span>
                                    @endif
                                </p>
                            </div>
                        @endif
                        @if($author->birth_place)
                            <div>
                                <span class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Tug'ilgan joyi</span>
                                <p class="text-gray-800 dark:text-gray-200 font-medium">{{ $author->birth_place }}</p>
                            </div>
                        @endif
                        @if($author->nationality)
                            <div>
                                <span class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Millati</span>
                                <p class="text-gray-800 dark:text-gray-200 font-medium">{{ $author->nationality }}</p>
                            </div>
                        @endif
                        <div>
                            <span class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Kitoblar soni</span>
                            <p class="text-gray-800 dark:text-gray-200 font-medium">{{ $author->books->count() }} ta</p>
                        </div>
                    </div>

                    @if($author->biography)
                        <div class="mb-5">
                            <h2 class="text-xl font-semibold text-blue-700 dark:text-blue-400 mb-2">Hayoti va ijodi</h2>
                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $author->biography }}</p>
                        </div>
                    @endif

                    @if($author->legacy)
                        <div>
                            <h2 class="text-xl font-semibold text-blue-700 dark:text-blue-400 mb-2">Merosi</h2>
                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $author->legacy }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div>
            <h2 class="text-3xl font-bold text-blue-800 dark:text-blue-400 mb-6">
                Muallifning kitoblari
                <span class="text-base font-normal text-gray-500 dark:text-gray-400">(bazadagi mavjudlari)</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse($author->books as $book)
                    <div class="bg-white dark:bg-gray-800 dark:border dark:border-gray-700 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition flex flex-col h-full">
                        <img src="{{ asset($book->image_url) }}" class="h-64 w-full object-contain p-2"
                             onerror="this.style.opacity='0.3'">
                        <div class="p-5 flex flex-col flex-1">
                            <h3 class="block text-xl leading-tight font-bold text-gray-900 dark:text-gray-100">{{ $book->title }}</h3>
                            <div class="mt-1 uppercase tracking-wide text-xs text-gray-500 dark:text-gray-400 font-medium">{{ $book->genre }}</div>
                            <p class="mt-2 text-gray-500 dark:text-gray-400 text-sm">{{ Str::limit($book->description, 100) }}</p>

                            <div class="mt-4 flex items-center justify-between">
                                <span class="text-green-600 dark:text-green-400 font-bold text-xl">{{ number_format($book->price) }} so'm</span>
                                <span class="text-xs bg-gray-200 dark:bg-gray-700 dark:text-gray-200 px-2 py-1 rounded">Qoldiq: {{ $book->stock }} ta</span>
                            </div>
                            <p class="text-gray-400 dark:text-gray-500 text-xs mt-2">Nashr yili: {{ $book->year }}</p>

                            <div class="mt-auto pt-4 flex gap-2">
                                <button type="button"
                                        data-book-id="{{ $book->id }}"
                                        class="add-to-cart-btn group w-12 h-12 flex-shrink-0 inline-flex items-center justify-center rounded-lg
                                               border-2 border-blue-600 text-blue-600 bg-white dark:bg-gray-800 dark:border-blue-500 dark:text-blue-400
                                               hover:bg-blue-600 hover:text-white dark:hover:bg-blue-600 dark:hover:text-white transition active:scale-95 shadow-sm"
                                        aria-label="Savatga qo'shish" title="Savatga qo'shish">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                         class="w-5 h-5">
                                        <circle cx="9" cy="21" r="1"></circle>
                                        <circle cx="20" cy="21" r="1"></circle>
                                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                    </svg>
                                </button>
                                <button type="button"
                                        data-book-id="{{ $book->id }}"
                                        title="To'lovga o'tish (kirish kerak)"
                                        class="buy-now-btn flex-1 inline-flex items-center justify-center px-4 py-2 rounded-lg
                                               bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold
                                               hover:from-blue-700 hover:to-indigo-700 shadow-sm transition active:scale-95">
                                    Hoziroq xarid qilish
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-10 text-gray-500 dark:text-gray-400">
                        Bu muallifning bazada hozircha kitoblari yo'q.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    @include('partials.auth-status-flash')
</body>
</html>
