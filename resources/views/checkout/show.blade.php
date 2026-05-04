@php
    $formatSum = fn (int $n) => number_format(max(0, $n), 0, '.', ',');

    $bookImgSrc = function ($url) {
        if (! $url) {
            return null;
        }
        return \Illuminate\Support\Str::startsWith($url, ['http://', 'https://'])
            ? $url
            : asset(ltrim((string) $url, '/'));
    };
@endphp
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buyurtmani rasmiylashtirish — Online Kitob Do'koni</title>
    <meta name="authenticated" content="1">
    @include('partials.head-tailwind-dark')
    <script src="{{ asset('js/cart.js') }}" defer></script>
    <script>
        window.__checkoutDistricts = @json($districtsByCode ?? []);
    </script>
    <script src="{{ asset('js/checkout-order.js') }}" defer></script>
</head>
<body class="bg-slate-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 min-h-[100dvh] flex flex-col transition-colors duration-200">

    <header class="bg-white/95 dark:bg-gray-900/95 backdrop-blur border-b border-gray-200/90 dark:border-gray-800 shrink-0 z-30">
        <div class="container mx-auto px-4 py-3 flex items-center justify-between gap-3">
            <a href="{{ route('books.index') }}" class="flex items-center gap-2 text-lg sm:text-xl font-bold text-blue-700 dark:text-blue-400 hover:text-blue-800 transition">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-7 h-7 shrink-0">
                    <path d="M5 4a2 2 0 0 1 2-2h11v18H7a2 2 0 0 1-2-2V4zm2 14h11v-2H7v2zM18 0H7a4 4 0 0 0-4 4v16a4 4 0 0 0 4 4h11V0z"/>
                </svg>
                <span class="hidden sm:inline">Online Kitob Do'koni</span>
            </a>
            <div class="flex items-center gap-2 shrink-0 text-xs sm:text-sm text-gray-600 dark:text-gray-300">
                <span class="max-w-[9rem] truncate">{{ auth()->user()->first_name ?? auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" onclick="if (typeof window.clearClientCartBeforeLogout === 'function') window.clearClientCartBeforeLogout();"
                            class="font-semibold text-gray-600 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 px-2 py-1 rounded-lg border border-transparent hover:border-red-300/60 transition">
                        Chiqish
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="flex-1 min-h-0 overflow-y-auto">
        <div class="container mx-auto px-4 py-6 sm:py-10 max-w-6xl">

            <div class="mb-8 text-center sm:text-left">
                <h1 class="text-2xl sm:text-3xl font-extrabold bg-gradient-to-r from-gray-900 via-blue-800 to-emerald-700 dark:from-gray-100 dark:via-blue-200 dark:to-emerald-300 bg-clip-text text-transparent">
                    Buyurtmani rasmiylashtirish
                </h1>
            </div>

            @if ($errors->any())
                <div class="mb-6 rounded-2xl bg-red-50 dark:bg-red-950/35 border border-red-200 dark:border-red-900/70 text-red-800 dark:text-red-200 px-4 py-3 text-sm shadow-sm">
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $msg)
                            <li>{{ $msg }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid lg:grid-cols-12 gap-8 lg:gap-10 items-start">
                {{-- Chap: forma --}}
                <div class="lg:col-span-7" id="checkout-form-panel">
                    <form id="checkout-order-form" method="POST" action="{{ route('checkout.order') }}" class="space-y-6" novalidate>
                        @csrf
                        <input type="hidden" name="mode" id="checkout-mode" value="{{ old('mode', $mode) }}">
                        <input type="hidden" name="book_id" value="{{ old('book_id', $mode === 'buy_now' && $buyBookId ? $buyBookId : '') }}">
                        <input type="hidden" name="book_ids" id="checkout-book-ids" value="{{ old('book_ids') }}">
                        <input type="hidden" name="order_lines_json" id="checkout-order-lines-json" value="{{ old('order_lines_json', '') }}">

                        <input type="hidden" name="phone" id="checkout-phone-normalized" value="{{ old('phone') }}" required>

                        {{-- Buyurtma beruvchi --}}
                        <section class="rounded-2xl border border-gray-200/90 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 sm:p-6 shadow-md">
                            <h3 class="text-base font-bold flex items-center gap-2 text-gray-900 dark:text-gray-100 mb-4">
                                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-indigo-600 text-white text-sm">1</span>
                                Buyurtma beruvchi
                            </h3>
                            <div class="space-y-4">
                                <div>
                                    <label for="checkout-phone-mask" class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Telefon raqami</label>
                                    <input type="tel" id="checkout-phone-mask" inputmode="numeric" autocomplete="tel"
                                           class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50/50 dark:bg-gray-900/50 px-4 py-2.5 text-sm font-mono tracking-wide focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 outline-none transition">
                                </div>
                                <div class="grid sm:grid-cols-3 gap-3">
                                    <div class="sm:col-span-1">
                                        <label for="checkout-last-name" class="block text-xs font-semibold mb-1">Familiya</label>
                                        <input name="last_name" id="checkout-last-name" required value="{{ old('last_name') }}"
                                               class="checkout-name-field w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900/40 px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/30 outline-none">
                                    </div>
                                    <div class="sm:col-span-1">
                                        <label for="checkout-first-name" class="block text-xs font-semibold mb-1">Ism</label>
                                        <input name="first_name" id="checkout-first-name" required value="{{ old('first_name') }}"
                                               class="checkout-name-field w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900/40 px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/30 outline-none">
                                    </div>
                                    <div class="sm:col-span-1">
                                        <label for="checkout-patronymic" class="block text-xs font-semibold mb-1">Otasining ismi</label>
                                        <input name="patronymic" id="checkout-patronymic" required value="{{ old('patronymic') }}"
                                               class="checkout-name-field w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900/40 px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/30 outline-none">
                                    </div>
                                </div>
                            </div>
                        </section>

                        {{-- Manzil --}}
                        <section class="rounded-2xl border border-gray-200/90 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 sm:p-6 shadow-md">
                            <h3 class="text-base font-bold flex items-center gap-2 mb-4">
                                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-teal-500 to-cyan-600 text-white text-sm">2</span>
                                Yetkazib berish manzili
                            </h3>
                            <div class="grid sm:grid-cols-2 gap-4 mb-4" id="checkout-region-row">
                                <div>
                                    <label for="checkout-region-code" class="block text-xs font-semibold mb-1">Viloyat</label>
                                    <select name="region_code" id="checkout-region-code" required
                                            class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900/40 px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/30 outline-none">
                                        <option value="" disabled @selected(! old('region_code'))>Tanlang</option>
                                        @foreach (($regions ?? []) as $region)
                                            <option value="{{ $region['code'] }}" @selected(old('region_code') === $region['code'])>{{ $region['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="checkout-district" class="block text-xs font-semibold mb-1">Tuman</label>
                                    <select name="district" id="checkout-district" required @if(old('district')) data-initial-district="{{ old('district') }}" @endif
                                            class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900/40 px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/30 outline-none disabled:opacity-50">
                                        <option value="">Avval viloyatni tanlang</option>
                                    </select>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div>
                                    <label for="checkout-settlement" class="block text-xs font-semibold mb-1">Aholi punkti (mahalla, qishloq)</label>
                                    <textarea name="settlement" id="checkout-settlement" required rows="2"
                                              class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900/40 px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/30 outline-none resize-y min-h-[2.75rem]">{{ old('settlement') }}</textarea>
                                </div>
                                <div>
                                    <label for="checkout-street" class="block text-xs font-semibold mb-1">Ko‘cha nomi</label>
                                    <input name="street" id="checkout-street" required value="{{ old('street') }}"
                                           class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900/40 px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/30 outline-none">
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label for="checkout-house-number" class="block text-xs font-semibold mb-1">Uy raqami</label>
                                        <input name="house_number" id="checkout-house-number" required value="{{ old('house_number') }}"
                                               inputmode="numeric" autocomplete="address-line2"
                                               class="checkout-house-flat w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900/40 px-3 py-2.5 text-sm tabular-nums focus:ring-2 focus:ring-emerald-500/30 outline-none">
                                    </div>
                                    <div>
                                        <label for="checkout-apartment" class="block text-xs font-semibold mb-1">Xonadon/Kvartira</label>
                                        <input name="apartment" id="checkout-apartment" value="{{ old('apartment') }}"
                                               inputmode="numeric" autocomplete="address-line3"
                                               class="checkout-house-flat w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900/40 px-3 py-2.5 text-sm tabular-nums focus:ring-2 focus:ring-emerald-500/30 outline-none">
                                    </div>
                                </div>
                            </div>
                        </section>

                        {{-- To‘lov --}}
                        <section class="rounded-2xl border border-gray-200/90 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 sm:p-6 shadow-md">
                            <h3 class="text-base font-bold flex items-center gap-2 mb-4">
                                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 text-white text-sm">3</span>
                                To‘lov usuli
                            </h3>
                            <div class="grid sm:grid-cols-2 gap-3 mb-5">
                                <label class="flex cursor-pointer items-start gap-3 rounded-xl border-2 border-gray-200 dark:border-gray-600 p-4 hover:border-emerald-300 dark:hover:border-emerald-500/50 transition has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/60 dark:has-[:checked]:bg-emerald-950/25">
                                    <input type="radio" name="payment_method" value="cash" id="checkout-pay-cash"
                                           {{ old('payment_method') === 'cash' ? 'checked' : '' }}
                                           {{ old('payment_method') === null || old('payment_method') === '' ? '' : '' }}
                                           class="mt-1 h-4 w-4 shrink-0 text-emerald-600 focus:ring-emerald-500">
                                    <span>
                                        <span class="block font-semibold text-gray-900 dark:text-gray-100">Mahsulotni olganda</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Naqd pul</span>
                                    </span>
                                </label>
                                <label class="flex cursor-pointer items-start gap-3 rounded-xl border-2 border-gray-200 dark:border-gray-600 p-4 hover:border-indigo-300 dark:hover:border-indigo-500/50 transition has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50/60 dark:has-[:checked]:bg-indigo-950/30">
                                    <input type="radio" name="payment_method" value="app" id="checkout-pay-app"
                                           {{ old('payment_method') === 'app' ? 'checked' : '' }}
                                           class="mt-1 h-4 w-4 shrink-0 text-indigo-600 focus:ring-indigo-500">
                                    <span>
                                        <span class="block font-semibold text-gray-900 dark:text-gray-100">Ilovalar orqali</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Onlayn to‘lov</span>
                                    </span>
                                </label>
                            </div>

                            <div id="checkout-app-list" class="hidden space-y-3" aria-hidden="true">
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Ilovani tanlang</p>
                                <div id="checkout-app-cards" class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                    @php
                                        $apps = [
                                            ['id' => 'click', 'label' => 'Click', 'file' => 'click.svg', 'whiteBg' => false, 'imgClass' => ''],
                                            /** PNG/JPG grafika atrofida bo‘sh joy ko‘proq bo‘lsa, Click bilan vizual tekislash uchun yengil zoom */
                                            ['id' => 'payme', 'label' => 'Payme', 'file' => 'payme.png', 'whiteBg' => true, 'imgClass' => 'scale-[1.2]'],
                                            ['id' => 'uzum_bank', 'label' => 'Uzum Bank', 'file' => 'uzum-bank.png', 'whiteBg' => true, 'imgClass' => 'scale-[1.36]'],
                                            ['id' => 'paynet', 'label' => 'Paynet', 'file' => 'paynet.jpg', 'whiteBg' => true, 'imgClass' => 'scale-[1.22]'],
                                        ];
                                    @endphp
                                    @foreach ($apps as $ap)
                                        @php
                                            $src = asset('images/payments/' . $ap['file']);
                                        @endphp
                                        <label data-payment-card class="group relative flex flex-col items-center justify-center gap-2.5 rounded-2xl border-2 border-gray-200/90 dark:border-gray-600 bg-white dark:bg-gray-900/50 p-3 sm:p-4 cursor-pointer shadow-sm transition-transform transition-shadow duration-200 ease-out hover:border-emerald-300/80 dark:hover:border-emerald-500/40 hover:shadow-md has-[:checked]:border-emerald-500 has-[:checked]:shadow-lg has-[:checked]:shadow-emerald-500/15 has-[:checked]:ring-2 has-[:checked]:ring-emerald-500/30 checkout-payment-card scale-100 opacity-100">
                                            {{-- peer avval — keyingi barcha qardoshlar peer-checked uchun (galochka eng oxirda bo‘lsa ham ishlaydi) --}}
                                            <input type="radio" name="payment_app" value="{{ $ap['id'] }}" class="peer sr-only"
                                                   {{ old('payment_app') === $ap['id'] ? 'checked' : '' }}>
                                            {{-- Bir xil "slot": kengligi cheklangan, rasm tug‘rilanmagan ichki grafik uchun katta ko‘rinadi --}}
                                            <span class="relative flex h-[4rem] w-[8rem] max-w-[95%] shrink-0 items-center justify-center rounded-xl {{ $ap['whiteBg'] ? 'bg-white dark:bg-white' : 'bg-gray-50 dark:bg-gray-800/80' }} px-2 py-1 ring-1 ring-gray-100 dark:ring-gray-700">
                                                <img src="{{ $src }}" alt="" class="relative z-[1] max-h-[3.65rem] max-w-full origin-center object-contain object-center transition-transform {{ $ap['imgClass'] ?? '' }}" loading="lazy" decoding="async">
                                            </span>
                                            <span class="text-xs font-bold text-center leading-tight text-gray-800 dark:text-gray-100">{{ $ap['label'] }}</span>
                                            <span class="pointer-events-none absolute right-1.5 top-1.5 z-40 flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-lg ring-2 ring-white dark:ring-gray-800 opacity-0 scale-50 transition-all duration-200 peer-checked:opacity-100 peer-checked:scale-100" aria-hidden="true">
                                                <svg class="h-4 w-4 -mr-px -mt-px" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </section>

                        <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3 pt-2">
                            <a href="{{ ($mode ?? '') === 'cart' ? route('cart.index') : route('books.index') }}"
                               class="inline-flex justify-center items-center rounded-xl border-2 border-gray-300 dark:border-gray-600 px-5 py-3 text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                                Orqaga
                            </a>
                            <button type="submit" id="checkout-submit-order" disabled
                                    class="inline-flex justify-center items-center rounded-xl px-8 py-3.5 text-sm font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 transition-[opacity,filter,box-shadow,transform] duration-200 disabled:opacity-35 disabled:grayscale disabled:cursor-not-allowed disabled:shadow-none enabled:cursor-pointer enabled:shadow-lg enabled:shadow-emerald-500/30 enabled:hover:brightness-110 active:enabled:scale-[0.99]">
                                Buyurtma berish
                            </button>
                        </div>
                    </form>
                </div>

                {{-- O‘ng: mahsulotlar --}}
                <div class="lg:col-span-5 space-y-4">
                    <div class="rounded-2xl border border-gray-200/80 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-lg shadow-gray-900/5 dark:shadow-black/40 p-5 lg:sticky lg:top-6">
                        <h2 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide flex items-center gap-2 mb-4">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-blue-600 text-white">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            </span>
                            Mahsulotlar
                        </h2>

                        @if (($mode ?? '') === 'buy_now' && isset($book) && $book)
                            @php
                                $qty = 1;
                                $lineTotal = (int) $book->price * $qty;
                                $src = $bookImgSrc($book->image_url);
                            @endphp
                            <div class="rounded-xl border dark:border-gray-600 bg-gray-50/70 dark:bg-gray-900/40 p-3 flex gap-3">
                                <div class="w-20 h-24 shrink-0 rounded-lg overflow-hidden bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-600">
                                    @if ($src)
                                        <img src="{{ $src }}" alt="" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-[10px] font-semibold uppercase text-gray-500 dark:text-gray-400 truncate">{{ $book->genre }}</p>
                                    <p class="font-bold text-sm leading-snug line-clamp-2">{{ $book->title }}</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">{{ $formatSum((int) $book->price) }} × {{ $qty }}</p>
                                </div>
                            </div>
                            <div class="mt-4 flex justify-between items-end border-t border-gray-200 dark:border-gray-600 pt-4">
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Jami</span>
                                <span class="text-2xl font-black text-blue-700 dark:text-blue-400 tabular-nums">{{ $formatSum($lineTotal) }} <span class="text-sm font-semibold">so‘m</span></span>
                            </div>
                        @else
                            <div id="checkout-cart-root">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Yuklanmoqda…</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>

    @if (($mode ?? '') === 'cart')
    <script>
    (function () {
        var root = document.getElementById('checkout-cart-root');
        var formPanel = document.getElementById('checkout-form-panel');
        var cartIndex = @json(route('cart.index'));
        var booksIndex = @json(route('books.index'));

        function fmt(n) { return Number(n || 0).toLocaleString('en-US'); }
        function esc(s) {
            return String(s ?? '').replace(/[&<>"']/g, function (c) {
                return { '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[c];
            });
        }
        function bookImgHref(url) {
            if (!url) return '';
            var u = String(url);
            if (u.startsWith('http://') || u.startsWith('https://')) return esc(u);
            return esc('/' + u.replace(/^\/+/, ''));
        }
        async function fetchBooks(ids) {
            var out = {};
            await Promise.all(ids.map(function (id) {
                return fetch('/books/' + encodeURIComponent(id), { headers: { 'Accept': 'application/json' } })
                    .then(function (r) { return r.ok ? r.json() : null; })
                    .then(function (b) { if (b && b.id != null) out[String(b.id)] = b; });
            }));
            return out;
        }
        function emptyState(html) {
            root.innerHTML = '<div class="text-sm text-gray-600 dark:text-gray-400 py-2">' + html + '</div>';
            if (formPanel) formPanel.classList.add('hidden');
        }
        function renderLines(booksById, items) {
            var total = 0;
            var lines = items.map(function (it) {
                var b = booksById[String(it.id)];
                if (!b) return '';
                var qty = Math.max(1, Number(it.qty) || 1);
                var unit = Number(b.price || 0);
                var line = unit * qty;
                total += line;
                var author = (b.author && b.author.name) ? esc(b.author.name) : '—';
                var genre = esc(b.genre || '');
                var title = esc(b.title || 'Kitob');
                var img = bookImgHref(b.image_url);
                var thumb = img
                    ? '<img src="' + img + '" alt="" class="w-full h-full object-cover">'
                    : '<svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14"/></svg>';
                return (
                    '<div class="rounded-xl border dark:border-gray-600 bg-gray-50/70 dark:bg-gray-900/40 p-3 flex gap-3 mb-2">' +
                    '<div class="w-16 h-20 shrink-0 rounded-lg overflow-hidden bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-600 flex items-center justify-center">' + thumb + '</div>' +
                    '<div class="min-w-0 flex-1">' +
                    '<p class="text-[10px] font-semibold uppercase text-gray-500 dark:text-gray-400 truncate">' + genre + '</p>' +
                    '<p class="font-bold text-sm leading-snug line-clamp-2">' + title + '</p>' +
                    '<p class="text-xs text-gray-600 dark:text-gray-300 mt-1">' + fmt(unit) + ' × ' + qty + '</p>' +
                    '</div></div>'
                );
            }).join('');
            root.innerHTML =
                '<div class="space-y-1">' + lines + '</div>' +
                '<div class="mt-4 flex justify-between items-end border-t border-gray-200 dark:border-gray-600 pt-4">' +
                '<span class="text-sm font-medium text-gray-600 dark:text-gray-300">Jami</span>' +
                '<span class="text-2xl font-black text-blue-700 dark:text-blue-400 tabular-nums">' + fmt(total) + ' <span class="text-sm font-semibold">so‘m</span></span>' +
                '</div>';
            if (formPanel) formPanel.classList.remove('hidden');
        }
        document.addEventListener('DOMContentLoaded', async function () {
            if (!window.Cart || !root) return;
            if (window.Cart.whenReady) await window.Cart.whenReady.catch(function () {});
            var items = Cart.getSorted().filter(function (it) {
                return it.selected !== false && (Number(it.qty) || 1) > 0;
            });
            if (!items.length) {
                emptyState('Tanlangan mahsulot yo\'q. <a href="' + cartIndex + '" class="text-emerald-600 dark:text-emerald-400 font-semibold underline">Savat</a> yoki <a href="' + booksIndex + '" class="underline">katalog</a>.');
                return;
            }
            var ids = items.map(function (it) { return it.id; });
            var booksById = await fetchBooks(ids);
            renderLines(booksById, items);
        });
    })();
    </script>
    @endif

    @include('partials.auth-status-flash')
</body>
</html>
