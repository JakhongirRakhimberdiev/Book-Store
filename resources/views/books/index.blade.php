<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="authenticated" content="{{ auth()->check() ? '1' : '0' }}">
    <title>Online Kitob Do'koni</title>
    @include('partials.head-tailwind-dark')
    <script src="{{ asset('js/cart.js') }}" defer></script>
    <style>
        /* Janrlar qatori uchun yengil scrollbar */
        .genres-scroll::-webkit-scrollbar { height: 6px; }
        .genres-scroll::-webkit-scrollbar-track { background: transparent; }
        .genres-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        .genres-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .dark .genres-scroll::-webkit-scrollbar-thumb { background: #475569; }
        .dark .genres-scroll::-webkit-scrollbar-thumb:hover { background: #64748b; }
    </style>
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
                {{-- Savatcha tugmasi (ikonka + yorliq, hammasi bitta knopka maydoni) --}}
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
        <div class="max-w-xl mx-auto mb-6">
            <form id="search-form" action="{{ route('books.index') }}" method="GET">
                <div class="relative w-full">
                    <input id="search-input" type="text" name="search" placeholder="Kitob nomi yoki muallif..."
                           class="w-full pl-4 pr-11 py-2.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400"
                           autocomplete="off"
                           value="{{ request('search') }}">
                    {{-- Lupa ikonkasi (input bo'sh bo'lganda ko'rinadi) --}}
                    <span id="search-icon"
                          class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="w-5 h-5">
                            <circle cx="11" cy="11" r="7"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </span>
                    {{-- Tozalash tugmasi (input yozilganda ko'rinadi) --}}
                    <button type="button" id="clear-btn"
                            class="hidden absolute right-3 top-1/2 -translate-y-1/2 w-6 h-6 flex items-center justify-center
                                   rounded-full text-gray-400 hover:text-white hover:bg-gray-400 dark:hover:bg-gray-500 transition"
                            aria-label="Tozalash">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                             class="w-4 h-4">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        {{-- Janrlar qatori --}}
        <div class="genres-scroll overflow-x-auto -mx-4 px-4 mb-8 pb-2">
            <div id="genres-row" class="flex gap-2 min-w-max">
                <button type="button" data-genre=""
                        class="genre-btn group inline-flex items-center gap-2 px-4 py-2 rounded-full border-2 font-medium text-sm whitespace-nowrap transition shadow-sm
                               bg-blue-600 text-white border-blue-600">
                    <span aria-hidden="true">📚</span>
                    Hammasi
                </button>
                @foreach($genres as $genre)
                    <button type="button" data-genre="{{ $genre }}"
                            class="genre-btn inline-flex items-center px-4 py-2 rounded-full border-2 font-medium text-sm whitespace-nowrap transition shadow-sm
                                   bg-white text-gray-700 border-gray-200 hover:border-blue-400 hover:text-blue-600 hover:shadow dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600 dark:hover:border-blue-500 dark:hover:text-blue-400">
                        {{ $genre }}
                    </button>
                @endforeach
            </div>
        </div>

        <div id="books-section" class="mb-10">
            <h2 id="books-heading" class="hidden text-2xl font-bold text-blue-700 dark:text-blue-400 mb-4">Kitoblar</h2>
            <div id="books-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse($books as $book)
                    <div class="bg-white dark:bg-gray-800 dark:border dark:border-gray-700 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition flex flex-col h-full">
                        <img src="{{ asset($book->image_url) }}" class="h-64 w-full object-contain p-2">
                        <div class="p-5 flex flex-col flex-1">
                            <h2 class="block text-xl leading-tight font-bold text-gray-900 dark:text-gray-100">{{ $book->title }}</h2>
                            <div class="mt-1 uppercase tracking-wide text-xs text-gray-500 dark:text-gray-400 font-medium">{{ $book->genre }}</div>
                            <p class="mt-2 text-gray-500 dark:text-gray-400 text-sm">{{ Str::limit($book->description, 100) }}</p>

                            <div class="mt-4 flex items-center justify-between">
                                <span class="text-green-600 dark:text-green-400 font-bold text-xl">{{ number_format($book->price) }} so'm</span>
                                <span class="text-xs bg-gray-200 dark:bg-gray-700 dark:text-gray-200 px-2 py-1 rounded">Qoldiq: {{ $book->stock }} ta</span>
                            </div>
                            <p class="text-gray-400 dark:text-gray-500 text-xs mt-2">
                                Muallif: 
                                @if($book->author)
                                    <a href="{{ route('authors.show', $book->author->id) }}"
                                       class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 hover:underline font-medium">
                                        {{ $book->author->name }}
                                    </a>
                                @else
                                    Noma'lum
                                @endif
                                ({{ $book->year }})
                            </p>

                            {{-- Xarid tugmalari (har doim pastki chegaraga yopishgan) --}}
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
                        Kechirasiz, bunday kitob topilmadi.
                    </div>
                @endforelse
            </div>
        </div>

        <div id="authors-section" class="hidden mb-10">
            <h2 class="text-2xl font-bold text-blue-700 dark:text-blue-400 mb-4">Mualliflar</h2>
            <div id="authors-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4"></div>
        </div>

        <div id="no-results" class="hidden text-center py-10 text-gray-500 dark:text-gray-400">
            Hech narsa topilmadi.
        </div>
    </div>

    <script>
    (function () {
        const input          = document.getElementById('search-input');
        const form           = document.getElementById('search-form');
        const clearBtn       = document.getElementById('clear-btn');
        const searchIcon     = document.getElementById('search-icon');
        const genreButtons   = document.querySelectorAll('.genre-btn');
        const authorsSection = document.getElementById('authors-section');
        const authorsGrid    = document.getElementById('authors-grid');
        const booksSection   = document.getElementById('books-section');
        const booksHeading   = document.getElementById('books-heading');
        const booksGrid      = document.getElementById('books-grid');
        const noResults      = document.getElementById('no-results');

        const originalBooksHtml = booksGrid.innerHTML;

        // Tailwind klasslari (active va passive holatlar)
        const ACTIVE_CLASSES   = ['bg-blue-600', 'text-white', 'border-blue-600'];
        const INACTIVE_CLASSES = [
            'bg-white', 'dark:bg-gray-800',
            'text-gray-700', 'dark:text-gray-200',
            'border-gray-200', 'dark:border-gray-600',
            'hover:border-blue-400', 'dark:hover:border-blue-500',
            'hover:text-blue-600', 'dark:hover:text-blue-400',
            'hover:shadow',
        ];

        let timer;
        let lastKey = '';
        let currentGenre = '';
        let currentController = null;

        form.addEventListener('submit', (e) => e.preventDefault());

        input.addEventListener('input', (e) => {
            const q = e.target.value.trim();
            toggleSearchIcons(q);

            clearTimeout(timer);
            timer = setTimeout(applyFilters, 150);
        });

        clearBtn.addEventListener('click', () => {
            input.value = '';
            toggleSearchIcons('');
            input.focus();
            applyFilters();
        });

        function toggleSearchIcons(q) {
            const empty = q === '';
            clearBtn.classList.toggle('hidden', empty);
            searchIcon.classList.toggle('hidden', !empty);
        }

        genreButtons.forEach((btn) => {
            btn.addEventListener('click', () => {
                currentGenre = btn.dataset.genre;
                updateActiveGenreButton();
                applyFilters();
            });
        });

        function updateActiveGenreButton() {
            genreButtons.forEach((btn) => {
                const isActive = btn.dataset.genre === currentGenre;
                btn.classList.remove(...ACTIVE_CLASSES, ...INACTIVE_CLASSES);
                btn.classList.add(...(isActive ? ACTIVE_CLASSES : INACTIVE_CLASSES));
            });
        }

        function applyFilters() {
            const q = input.value.trim();

            // Hech qanday filtr yo'q — asl ro'yxatni tikla
            if (q === '' && currentGenre === '') {
                resetToOriginal();
                return;
            }

            doSearch(q, currentGenre);
        }

        function resetToOriginal() {
            lastKey = '';
            authorsSection.classList.add('hidden');
            authorsGrid.innerHTML = '';
            booksHeading.classList.add('hidden');
            booksGrid.innerHTML = originalBooksHtml;
            booksSection.classList.remove('hidden');
            noResults.classList.add('hidden');

            // Asl HTML'dagi kartochkalarni ham savatcha holati bilan sinxronla
            if (typeof window.updateAllBookCards === 'function') {
                window.updateAllBookCards();
            }
        }

        async function doSearch(q, genre) {
            const key = q + '||' + genre;
            if (key === lastKey) return;
            lastKey = key;

            if (currentController) currentController.abort();
            currentController = new AbortController();

            const params = new URLSearchParams();
            if (q)     params.set('q', q);
            if (genre) params.set('genre', genre);

            try {
                const res = await fetch(`/search?${params.toString()}`, {
                    headers: { 'Accept': 'application/json' },
                    signal: currentController.signal,
                });
                if (!res.ok) throw new Error('Search failed');
                const data = await res.json();
                if (key !== lastKey) return; // eski natijalar tushib qolmasin
                renderResults(data);
            } catch (err) {
                if (err.name !== 'AbortError') console.error(err);
            }
        }

        function renderResults({ authors = [], books = [] }) {
            // Mualliflar
            if (authors.length > 0) {
                authorsGrid.innerHTML = authors.map(authorCard).join('');
                authorsSection.classList.remove('hidden');
            } else {
                authorsSection.classList.add('hidden');
                authorsGrid.innerHTML = '';
            }

            // Kitoblar
            if (books.length > 0) {
                booksGrid.innerHTML = books.map(bookCard).join('');
                booksHeading.classList.remove('hidden');
                booksSection.classList.remove('hidden');
            } else {
                booksGrid.innerHTML = '';
                booksHeading.classList.add('hidden');
                booksSection.classList.add('hidden');
            }

            // Hech narsa topilmasa
            noResults.classList.toggle('hidden', !(authors.length === 0 && books.length === 0));

            // Yangidan render qilingan kartochkalarning savatcha holatini sinxronlash
            if (typeof window.updateAllBookCards === 'function') {
                window.updateAllBookCards();
            }
        }

        function bookCard(book) {
            const authorBlock = book.author
                ? `<a href="/authors/${book.author.id}" class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 hover:underline font-medium">${esc(book.author.name)}</a>`
                : `Noma'lum`;

            const desc = (book.description || '').length > 100
                ? esc((book.description || '').slice(0, 100)) + '...'
                : esc(book.description || '');

            const img = '/' + String(book.image_url || '').replace(/\\/g, '/');

            return `
                <div class="bg-white dark:bg-gray-800 dark:border dark:border-gray-700 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition flex flex-col h-full">
                    <img src="${esc(img)}" class="h-64 w-full object-contain p-2" onerror="this.style.opacity='0.3'">
                    <div class="p-5 flex flex-col flex-1">
                        <h2 class="block text-xl leading-tight font-bold text-gray-900 dark:text-gray-100">${esc(book.title || '')}</h2>
                        <div class="mt-1 uppercase tracking-wide text-xs text-gray-500 dark:text-gray-400 font-medium">${esc(book.genre || '')}</div>
                        <p class="mt-2 text-gray-500 dark:text-gray-400 text-sm">${desc}</p>
                        <div class="mt-4 flex items-center justify-between">
                            <span class="text-green-600 dark:text-green-400 font-bold text-xl">${formatPrice(book.price)} so'm</span>
                            <span class="text-xs bg-gray-200 dark:bg-gray-700 dark:text-gray-200 px-2 py-1 rounded">Qoldiq: ${esc(book.stock)} ta</span>
                        </div>
                        <p class="text-gray-400 dark:text-gray-500 text-xs mt-2">
                            Muallif: ${authorBlock} (${esc(book.year)})
                        </p>

                        <div class="mt-auto pt-4 flex gap-2">
                            <button type="button" data-book-id="${esc(book.id)}"
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
                            <button type="button" data-book-id="${esc(book.id)}"
                                    title="To'lovga o'tish (kirish kerak)"
                                    class="buy-now-btn flex-1 inline-flex items-center justify-center px-4 py-2 rounded-lg
                                           bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold
                                           hover:from-blue-700 hover:to-indigo-700 shadow-sm transition active:scale-95">
                                Hoziroq xarid qilish
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }

        function authorCard(author) {
            const initial = String(author.name || '?').charAt(0).toUpperCase();
            const imgPath = author.image_url
                ? '/' + String(author.image_url).replace(/\\/g, '/')
                : '';
            const avatar = imgPath
                ? `<img src="${esc(imgPath)}" alt="${esc(author.name)}"
                        class="w-16 h-16 rounded-full object-cover bg-blue-100 dark:bg-blue-900/40 flex-shrink-0"
                        onerror="this.outerHTML='<div class=\\'w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-2xl font-bold flex-shrink-0\\'>${esc(initial)}</div>'">`
                : `<div class="w-16 h-16 rounded-full bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center text-blue-600 dark:text-blue-300 text-2xl font-bold flex-shrink-0">${esc(initial)}</div>`;

            const nationality = author.nationality
                ? `<p class="text-xs text-gray-500 dark:text-gray-400">${esc(author.nationality)}</p>` : '';

            return `
                <a href="/authors/${author.id}" class="bg-white dark:bg-gray-800 dark:border dark:border-gray-700 rounded-xl shadow-md hover:shadow-xl transition p-4 flex items-center gap-4 group">
                    ${avatar}
                    <div class="min-w-0">
                        <h3 class="font-bold text-base text-gray-900 dark:text-gray-100 group-hover:text-blue-700 dark:group-hover:text-blue-400 truncate">${esc(author.name)}</h3>
                        ${nationality}
                        <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">${esc(author.books_count ?? 0)} ta kitob</p>
                    </div>
                </a>
            `;
        }

        function formatPrice(p) {
            return Number(p || 0).toLocaleString('en-US');
        }

        function esc(s) {
            return String(s ?? '').replace(/[&<>"']/g, (c) => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
            }[c]));
        }

        // Sahifa ochilganda input bo'sh bo'lmasa, qidiruvni ishga tushir
        if (input.value.trim() !== '') {
            toggleSearchIcons(input.value.trim());
            applyFilters();
        }
    })();
    </script>

    @include('partials.auth-status-flash')
</body>
</html>
