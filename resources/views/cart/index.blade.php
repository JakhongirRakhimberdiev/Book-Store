<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="authenticated" content="{{ auth()->check() ? '1' : '0' }}">
    <title>Savatcha — Online Kitob Do'koni</title>
    @include('partials.head-tailwind-dark')
    <script src="{{ asset('js/cart.js') }}" defer></script>
    <style>
        /* Tanlanmagan kartochkani xira ko'rsatish (checkbox doim ravshan turadi) */
        .cart-item.is-unselected .cart-fade {
            opacity: 0.5;
            transition: opacity .2s ease;
        }
        .cart-item .cart-fade {
            transition: opacity .2s ease;
        }

        /* ───── Custom checkbox ikonkasi ───── */
        .checkbox-box .icon-check {
            opacity: 0;
            transition: opacity .15s ease;
        }
        .peer:checked ~ .checkbox-box .icon-check {
            opacity: 1;
        }
        .peer:focus-visible ~ .checkbox-box {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
        }

        /* "Hammasini tanlash" matni — gradientli yumshoq o'tish */
        .select-all-text {
            background-image: linear-gradient(to right, #1f2937, #374151); /* gray-800 → gray-700 */
            -webkit-background-clip: text;
                    background-clip: text;
            color: transparent;
            transition: opacity .25s ease, background-image .25s ease;
        }
        /* Hover (faqat hammasi tanlanmagan paytda) — ko'k-violet gradient.
           Ostchiziq yo'q — foydalanuvchi xohishi bo'yicha. */
        .group:hover:not(:has(input.peer:checked)) .select-all-text {
            background-image: linear-gradient(to right, #2563eb, #4f46e5); /* blue-600 → indigo-600 */
        }
        /* Hammasi tanlangan (master checked) — knopka "kerak emas" ko'rinishi:
           xira, hover effekti yo'q. */
        .group:has(input.peer:checked) .select-all-text {
            opacity: 0.5;
            background-image: linear-gradient(to right, #1f2937, #374151);
        }
        .dark .select-all-text {
            background-image: linear-gradient(to right, #f3f4f6, #d1d5db);
        }
        .dark .group:hover:not(:has(input.peer:checked)) .select-all-text {
            background-image: linear-gradient(to right, #93c5fd, #c4b5fd);
        }
        .dark .group:has(input.peer:checked) .select-all-text {
            background-image: linear-gradient(to right, #f3f4f6, #d1d5db);
        }
        .dark .peer:focus-visible ~ .checkbox-box {
            box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.35);
        }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-950 min-h-screen transition-colors duration-200">

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
                {{-- Savatcha tugmasi (joriy sahifa — aktiv holat) --}}
                <a href="{{ route('cart.index') }}" id="cart-link"
                   aria-label="Savatcha" title="Savatcha"
                   aria-current="page"
                   class="group flex flex-col items-center text-blue-600 dark:text-blue-400 transition active:scale-95">
                    <span class="relative inline-flex items-center justify-center w-11 h-11 rounded-full bg-blue-600 text-white shadow-sm dark:bg-blue-500
                                 group-hover:bg-blue-700 dark:group-hover:bg-blue-600 transition">
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

        {{-- Orqaga qaytish --}}
        <div class="mb-6">
            <a href="{{ route('books.index') }}"
               aria-label="Katalogga qaytish" title="Katalogga qaytish"
               class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 shadow dark:border dark:border-gray-700
                      hover:bg-blue-600 hover:text-white hover:-translate-x-0.5 active:scale-95 transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     class="w-5 h-5">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
            </a>
        </div>

        {{-- Bo'sh holat --}}
        <div id="cart-empty" class="hidden bg-white dark:bg-gray-800 dark:border dark:border-gray-700 rounded-2xl shadow p-10 sm:p-16 text-center">
            <div class="mx-auto w-20 h-20 rounded-full bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-500 dark:text-blue-400 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
                     class="w-10 h-10">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-1">Savatcha bo'sh</h2>
            <p class="text-gray-500 dark:text-gray-400 mb-6">Hali biror kitob qo'shilmagan.</p>
            <a href="{{ route('books.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg
                      bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold
                      hover:from-blue-700 hover:to-indigo-700 transition active:scale-95 shadow-md hover:shadow-lg">
                Katalogga o'tish
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                     class="w-4 h-4">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </a>
        </div>

        {{-- Yuklanmoqda --}}
        <div id="cart-loading" class="text-center py-16 text-gray-500 dark:text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                 class="w-8 h-8 mx-auto animate-spin text-blue-500 mb-2">
                <path d="M21 12a9 9 0 1 1-6.219-8.56"></path>
            </svg>
            Yuklanmoqda...
        </div>

        {{-- Asosiy: chap = ro'yxat, o'ng = hisob-kitob --}}
        <div id="cart-content" class="hidden grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                {{-- Chap: tanlash • O'ng: sanoq + o'chirish (bir yaxlit guruh) --}}
                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    {{-- Bosish maydoni: faqat checkbox + matn --}}
                    <label for="select-all-checkbox"
                           class="cart-select-all-pill group cursor-pointer shrink-0 inline-flex items-center gap-2.5
                                  select-none rounded-full border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2.5 shadow-sm hover:shadow-md transition self-start">
                        <input type="checkbox" id="select-all-checkbox" class="peer sr-only">
                        <span class="checkbox-box w-5 h-5 rounded-md border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900
                                     flex items-center justify-center relative
                                     peer-checked:bg-blue-600 peer-checked:border-blue-600
                                     group-hover:border-gray-400
                                     peer-checked:group-hover:border-blue-700
                                     transition shrink-0">
                            <svg class="icon-check absolute w-3 h-3 text-white"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </span>
                        <span class="select-all-text text-sm font-bold tracking-wide leading-none whitespace-nowrap">
                            Hammasini tanlash
                        </span>
                    </label>

                    {{-- Sanoq + o'chirish — bitta segmentli ko'rinish (muvofiqlik aniq) --}}
                    <div class="inline-flex items-stretch shrink-0 self-start sm:self-auto overflow-hidden rounded-full
                                shadow-md ring-1 ring-gray-300/90 dark:ring-gray-600">
                        <div class="inline-flex items-center gap-1 px-3.5 py-2 text-xs text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 whitespace-nowrap">
                            <span id="selected-distinct" class="font-bold text-gray-900 dark:text-gray-100 tabular-nums">0</span>
                            <span class="text-gray-400">/</span>
                            <span id="total-distinct" class="font-medium text-gray-700 dark:text-gray-300 tabular-nums">0</span>
                            <span class="text-gray-500 dark:text-gray-400">tanlangan</span>
                        </div>
                        <button type="button" id="clear-cart-btn"
                                class="clear-cart-btn inline-flex items-center gap-1.5 border-l border-gray-600/50
                                       bg-gradient-to-b from-gray-700 to-gray-800 px-3.5 py-2 text-xs font-bold text-white
                                       hover:from-gray-600 hover:to-gray-700 active:scale-[0.98] transition"
                                title="Barcha mahsulotlarni savatchadan olib tashlash"
                                aria-label="Savatchani tozalash">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"
                                 class="w-3.5 h-3.5 shrink-0 opacity-90">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                <line x1="14" y1="11" x2="14" y2="17"></line>
                            </svg>
                            <span class="whitespace-nowrap leading-none">Hammasini o'chirish</span>
                        </button>
                    </div>
                </div>

                <div class="space-y-4" id="cart-items"></div>
            </div>

            <aside class="lg:col-span-1">
                <div class="lg:sticky lg:top-24 bg-white dark:bg-gray-800 dark:border dark:border-gray-700 rounded-2xl shadow-md p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="w-5 h-5 text-blue-600 dark:text-blue-400">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="9" y1="9" x2="15" y2="9"></line>
                            <line x1="9" y1="13" x2="15" y2="13"></line>
                            <line x1="9" y1="17" x2="13" y2="17"></line>
                        </svg>
                        Xarid ma'lumotlari
                    </h2>

                    <dl class="space-y-3">
                        <div class="flex items-center justify-between text-sm">
                            <dt class="text-gray-500 dark:text-gray-400">Mahsulotlar soni</dt>
                            <dd class="font-semibold text-gray-900 dark:text-gray-100"><span id="summary-count">0</span> ta</dd>
                        </div>
                        <div class="border-t border-gray-100 dark:border-gray-600 pt-3 flex items-end justify-between">
                            <dt class="text-gray-700 dark:text-gray-300 font-medium">Jami:</dt>
                            <dd class="font-bold text-2xl text-blue-700 dark:text-blue-400"><span id="summary-total">0</span> <span class="text-base font-semibold">so'm</span></dd>
                        </div>
                    </dl>

                    <button id="checkout-btn" type="button"
                            class="mt-6 w-full inline-flex items-center justify-center px-4 py-3 rounded-xl
                                   bg-gradient-to-r from-emerald-500 to-green-600 text-white font-bold
                                   hover:from-emerald-600 hover:to-green-700 transition active:scale-95
                                   shadow-md hover:shadow-lg">
                        To‘lovni amalga oshirish
                    </button>
                </div>
            </aside>
        </div>
    </div>

    <script>
    (function () {
        const itemsEl          = document.getElementById('cart-items');
        const emptyEl          = document.getElementById('cart-empty');
        const loadingEl        = document.getElementById('cart-loading');
        const contentEl        = document.getElementById('cart-content');
        const sumCountEl       = document.getElementById('summary-count');
        const sumTotalEl       = document.getElementById('summary-total');
        const checkoutBtn      = document.getElementById('checkout-btn');
        const selectAllCheckbox = document.getElementById('select-all-checkbox');
        const selectedDistinctEl = document.getElementById('selected-distinct');
        const totalDistinctEl    = document.getElementById('total-distinct');
        const clearCartBtn       = document.getElementById('clear-cart-btn');

        const booksMap = new Map(); // id -> book ma'lumotlari

        function esc(s) {
            return String(s ?? '').replace(/[&<>"']/g, (c) => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
            }[c]));
        }

        function formatPrice(p) {
            return Number(p || 0).toLocaleString('en-US');
        }

        function showState({ empty = false, loading = false, content = false }) {
            emptyEl.classList.toggle('hidden', !empty);
            loadingEl.classList.toggle('hidden', !loading);
            contentEl.classList.toggle('hidden', !content);
        }

        async function fetchBooks(ids) {
            const results = await Promise.allSettled(
                ids.map(id => fetch(`/books/${encodeURIComponent(id)}`, {
                    headers: { 'Accept': 'application/json' }
                }).then(r => r.ok ? r.json() : null))
            );
            results.forEach(r => {
                if (r.status === 'fulfilled' && r.value && r.value.id != null) {
                    booksMap.set(String(r.value.id), r.value);
                }
            });
        }

        function itemCardHtml(item) {
            const book = booksMap.get(String(item.id));
            if (!book) return '';

            const qty       = Math.max(1, Number(item.qty) || 1);
            const unitPrice = Number(book.price || 0);
            const subtotal  = qty * unitPrice;
            const selected  = item.selected !== false;

            const img    = '/' + String(book.image_url || '').replace(/\\/g, '/');
            const author = book.author ? esc(book.author.name) : "Noma'lum";
            const genre  = book.genre ? esc(book.genre) : '';

            return `
                <div class="cart-item bg-white dark:bg-gray-800 dark:border dark:border-gray-700 rounded-xl shadow-sm hover:shadow-md transition p-4 sm:p-5 flex gap-4 relative ${selected ? '' : 'is-unselected'}"
                     data-book-id="${esc(item.id)}" data-unit-price="${esc(unitPrice)}">

                    {{-- Tanlash checkbox --}}
                    <label class="cart-item-check cursor-pointer flex items-start pt-1 select-none group/check">
                        <input type="checkbox" class="cart-item-checkbox peer sr-only" ${selected ? 'checked' : ''}>
                        <span class="checkbox-box w-5 h-5 rounded-md border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900
                                     flex items-center justify-center relative
                                     peer-checked:bg-blue-600 peer-checked:border-blue-600
                                     group-hover/check:border-gray-400
                                     peer-checked:group-hover/check:border-blue-700
                                     transition flex-shrink-0">
                            <svg class="icon-check absolute w-3 h-3 text-white"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </span>
                    </label>

                    {{-- Muqova --}}
                    <div class="flex-shrink-0 w-24 h-32 sm:w-28 sm:h-36 bg-gray-50 dark:bg-gray-900/60 rounded-lg overflow-hidden flex items-center justify-center cart-fade">
                        <img src="${esc(img)}" alt="" class="w-full h-full object-contain"
                             onerror="this.style.opacity='0.3'">
                    </div>

                    {{-- Markazi: ma'lumot + boshqaruv --}}
                    <div class="flex-1 min-w-0 flex flex-col cart-fade">
                        <h3 class="font-bold text-base sm:text-lg leading-tight text-gray-900 dark:text-gray-100 pr-8 line-clamp-2"
                            title="${esc(book.title)}">${esc(book.title)}</h3>
                        ${genre ? `<p class="text-[11px] uppercase tracking-wide text-gray-500 dark:text-gray-400 font-medium mt-1">${genre}</p>` : ''}
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Muallif: <span class="text-gray-700 dark:text-gray-200">${author}</span></p>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                            ${formatPrice(unitPrice)} <span class="text-xs">so'm / dona</span>
                        </p>

                        <div class="mt-auto pt-3 flex items-center justify-between flex-wrap gap-3">
                            {{-- Miqdor boshqaruvi --}}
                            <div class="inline-flex items-center bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden select-none">
                                <button type="button" class="qty-decrement w-9 h-9 flex items-center justify-center text-gray-600 dark:text-gray-200 hover:bg-blue-600 hover:text-white active:scale-95 transition"
                                        aria-label="Kamaytirish" title="Kamaytirish">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                         class="w-4 h-4">
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                </button>
                                <span class="qty-display min-w-[2.25rem] text-center font-bold text-gray-900 dark:text-gray-100">${qty}</span>
                                <button type="button" class="qty-increment w-9 h-9 flex items-center justify-center text-gray-600 dark:text-gray-200 hover:bg-blue-600 hover:text-white active:scale-95 transition"
                                        aria-label="Ko'paytirish" title="Ko'paytirish">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                         class="w-4 h-4">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                </button>
                            </div>

                            {{-- Subtotal --}}
                            <span class="subtotal-display font-bold text-lg text-blue-700 dark:text-blue-400">
                                ${formatPrice(subtotal)} <span class="text-sm">so'm</span>
                            </span>
                        </div>
                    </div>

                    {{-- Olib tashlash --}}
                    <button type="button" class="cart-remove absolute top-3 right-3 w-8 h-8 rounded-full text-gray-400 hover:bg-red-50 dark:hover:bg-red-900/40 hover:text-red-600 transition flex items-center justify-center active:scale-95"
                            aria-label="Olib tashlash" title="Olib tashlash">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="w-4 h-4">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
            `;
        }

        function recalcSummary() {
            const items = window.Cart.get();
            let selectedQty = 0;
            let selectedPrice = 0;
            let selectedDistinct = 0;

            items.forEach(it => {
                const book = booksMap.get(String(it.id));
                if (!book) return;
                if (it.selected === false) return; // faqat tanlanganlar
                const qty = Math.max(1, Number(it.qty) || 1);
                selectedQty      += qty;
                selectedPrice    += qty * Number(book.price || 0);
                selectedDistinct += 1;
            });

            // O'ng tomondagi xarid ma'lumotlari (faqat tanlanganlar)
            sumCountEl.textContent    = String(selectedQty);
            sumTotalEl.textContent    = formatPrice(selectedPrice);

            // Yuqori paneldagi "X / Y tanlangan"
            const totalDistinct = items.length;
            selectedDistinctEl.textContent = String(selectedDistinct);
            totalDistinctEl.textContent    = String(totalDistinct);

            // "To‘lovni amalga oshirish" — tanlangan bo'lmasa noaktiv
            updateCheckoutBtn(selectedDistinct > 0);

            // Master checkbox holati: hammasi/qisman/yo'q
            updateSelectAllState(selectedDistinct, totalDistinct);

            if (typeof window.updateCartBadge === 'function') {
                window.updateCartBadge();
            }
        }

        function updateCheckoutBtn(active) {
            if (active) {
                checkoutBtn.disabled = false;
                checkoutBtn.classList.remove('opacity-50', 'cursor-not-allowed', 'pointer-events-none');
            } else {
                checkoutBtn.disabled = true;
                checkoutBtn.classList.add('opacity-50', 'cursor-not-allowed', 'pointer-events-none');
            }
        }

        function updateSelectAllState(selectedDistinct, totalDistinct) {
            // Binary holat: faqat barcha tovarlar tanlangan bo'lsa — checked.
            // Qisman yoki hech biri tanlanmagan bo'lsa — unchecked (oq).
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = totalDistinct > 0 && selectedDistinct === totalDistinct;
        }

        function updateRow(card, qty) {
            const unitPrice = Number(card.dataset.unitPrice || 0);
            const qtyEl     = card.querySelector('.qty-display');
            const subEl     = card.querySelector('.subtotal-display');
            if (qtyEl) qtyEl.textContent = qty;
            if (subEl) subEl.innerHTML   = `${formatPrice(qty * unitPrice)} <span class="text-sm">so'm</span>`;
        }

        function renderAll(items) {
            const html = items.map(itemCardHtml).filter(Boolean).join('');
            itemsEl.innerHTML = html;
            recalcSummary();
        }

        async function init() {
            if (window.Cart && window.Cart.whenReady) {
                await window.Cart.whenReady.catch(() => {});
            }
            const items = window.Cart.getSorted();
            if (items.length === 0) {
                showState({ empty: true });
                return;
            }

            showState({ loading: true });

            await fetchBooks(items.map(it => it.id));

            // Bazadan o'chirilgan kitoblarni savatdan tozalaymiz
            const validItems = items.filter(it => booksMap.has(String(it.id)));
            if (validItems.length !== items.length) {
                const validIds = new Set(validItems.map(it => String(it.id)));
                const cleaned  = window.Cart.get().filter(it => validIds.has(String(it.id)));
                window.Cart.save(cleaned);
            }

            if (validItems.length === 0) {
                showState({ empty: true });
                return;
            }

            showState({ content: true });
            renderAll(validItems);
        }

        // ───── Click/change delegation: +/-, olib tashlash, tanlash ─────

        itemsEl.addEventListener('click', (e) => {
            const card = e.target.closest('.cart-item');
            if (!card) return;
            const bookId = card.dataset.bookId;

            if (e.target.closest('.qty-increment')) {
                window.Cart.increment(bookId);
                updateRow(card, window.Cart.qtyOf(bookId));
                recalcSummary();
                return;
            }

            if (e.target.closest('.qty-decrement')) {
                window.Cart.decrement(bookId);
                const newQty = window.Cart.qtyOf(bookId);
                if (newQty === 0) {
                    removeCardWithAnim(card);
                } else {
                    updateRow(card, newQty);
                    recalcSummary();
                }
                return;
            }

            if (e.target.closest('.cart-remove')) {
                window.Cart.remove(bookId);
                removeCardWithAnim(card);
                return;
            }
        });

        // Per-card checkbox o'zgarishini eshitish
        itemsEl.addEventListener('change', (e) => {
            const checkbox = e.target.closest('.cart-item-checkbox');
            if (!checkbox) return;
            const card = checkbox.closest('.cart-item');
            if (!card) return;
            const bookId = card.dataset.bookId;
            window.Cart.setSelected(bookId, checkbox.checked);
            card.classList.toggle('is-unselected', !checkbox.checked);
            recalcSummary();
        });

        // Master "Hammasini tanlash"
        selectAllCheckbox.addEventListener('change', (e) => {
            const val = e.target.checked;
            window.Cart.selectAll(val);
            // Barcha kartochkalardagi checkbox va xira holatni sinxronlash
            itemsEl.querySelectorAll('.cart-item').forEach(card => {
                const cb = card.querySelector('.cart-item-checkbox');
                if (cb) cb.checked = val;
                card.classList.toggle('is-unselected', !val);
            });
            recalcSummary();
        });

        /** Savatchani tozalash va bo'sh holat sahifasi (sku'shotdagi dizayn). */
        function clearCartAndShowEmpty() {
            window.Cart.clear();
            booksMap.clear();
            itemsEl.innerHTML = '';
            selectAllCheckbox.checked      = false;
            selectAllCheckbox.indeterminate = false;
            showState({ empty: true });
            if (typeof window.updateCartBadge === 'function') {
                window.updateCartBadge();
            }
        }

        clearCartBtn.addEventListener('click', clearCartAndShowEmpty);

        function removeCardWithAnim(card) {
            card.style.transition = 'opacity .2s ease, transform .2s ease';
            card.style.opacity    = '0';
            card.style.transform  = 'translateX(20px)';
            setTimeout(() => {
                card.remove();
                recalcSummary();
                if (window.Cart.count() === 0) {
                    showState({ empty: true });
                }
            }, 200);
        }

        // ───── "To‘lovni amalga oshirish" ─────

        checkoutBtn.addEventListener('click', () => {
            if (window.Cart.selectedCount() === 0) return;
            if (typeof window.gotoCheckoutCart === 'function') {
                window.gotoCheckoutCart();
            }
        });

        // ───── Init ─────

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => void init());
        } else {
            void init();
        }
    })();
    </script>

    @include('partials.auth-status-flash')
</body>
</html>
