<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        @if (($order['payment_method'] ?? '') === 'cash')
            Buyurtma qabul qilindi — Online Kitob Do'koni
        @else
            Onlayn toʻlov — Online Kitob Do'koni
        @endif
    </title>
    <meta name="authenticated" content="1">
    @include('partials.head-tailwind-dark')
    <script src="{{ asset('js/cart.js') }}" defer></script>
</head>
<body class="bg-gradient-to-br from-slate-50 via-emerald-50/40 to-teal-50 dark:from-gray-950 dark:via-gray-900 dark:to-slate-900 min-h-[100dvh] flex flex-col transition-colors duration-200"
      @if (($order['payment_method'] ?? '') === 'cash' && ! empty($order['lines'])) data-cart-skip-remote-hydrate="1" @endif>

    <header class="bg-white/90 dark:bg-gray-900/90 border-b border-gray-200/80 dark:border-gray-800 shrink-0">
        <div class="container mx-auto px-4 py-3 flex items-center justify-between gap-3">
            <a href="{{ route('books.index') }}" class="text-sm font-bold text-blue-700 dark:text-blue-400 hover:underline">← Katalog</a>
            <span class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ auth()->user()->first_name ?? auth()->user()->name }}</span>
        </div>
    </header>

    <main class="flex-1 flex items-center justify-center p-6">
        @if (($order['payment_method'] ?? '') === 'cash')
            {{-- Faqat naqd: qisqa xabar, savatga yo‘l --}}
            <div class="max-w-md w-full text-center">
                <div class="inline-flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-emerald-400 to-teal-600 text-white shadow-xl shadow-emerald-500/30 mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="w-10 h-10">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Buyurtma qabul qilindi</h1>
                <p class="mt-3 text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                    Tez orada operator siz bilan bog‘lanadi.
                </p>
                <div class="mt-8 flex justify-center">
                    <a href="{{ route('cart.index') }}"
                       class="inline-flex justify-center rounded-xl px-6 py-3 font-semibold border-2 border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 transition min-w-[13rem]">
                        Savatchani ko‘rish
                    </a>
                </div>
            </div>
        @else
            {{-- Ilovalar orqali — onlayn toʻlov hali yo‘q (#thanks2) --}}
            <div id="thanks2" class="max-w-lg w-full rounded-3xl border border-indigo-100/90 dark:border-indigo-900/50 bg-white/90 dark:bg-gray-900/80 backdrop-blur-sm shadow-xl shadow-indigo-900/10 dark:shadow-black/40 px-8 py-10 sm:px-10 text-center ring-1 ring-indigo-500/10">
                <div class="inline-flex h-20 w-20 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 via-violet-500 to-fuchsia-600 text-white shadow-lg shadow-indigo-500/35 mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-10 h-10">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <p class="text-xs font-bold uppercase tracking-[0.14em] text-indigo-600 dark:text-indigo-400 mb-2">Onlayn toʻlov</p>
                <h1 class="text-2xl sm:text-[1.65rem] font-extrabold text-gray-900 dark:text-gray-100 leading-tight">
                    Onlayn toʻlov tizimi tez orada ishga tushadi
                </h1>
                <p class="mt-4 text-gray-600 dark:text-gray-400 text-sm sm:text-base leading-relaxed">
                    Bildirgan ishonchingiz uchun rahmat.
                </p>
                <div class="mt-9 flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('books.index') }}"
                       class="inline-flex justify-center items-center rounded-xl px-6 py-3 font-semibold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 shadow-md transition">
                        Katalogga o‘tish
                    </a>
                    <a href="{{ route('checkout', ['resume' => 1]) }}"
                       class="inline-flex justify-center items-center rounded-xl px-6 py-3 font-semibold border-2 border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-800/80 transition">
                        Orqaga
                    </a>
                </div>
            </div>
        @endif
    </main>

    @if (($order['payment_method'] ?? '') === 'cash' && ! empty($order['lines']))
        @php($orderLines = $order['lines'])
        <script>
            document.addEventListener('DOMContentLoaded', async function () {
                var lines = @json($orderLines);
                async function run() {
                    if (window.Cart && typeof Cart.whenReady !== 'undefined') {
                        await Cart.whenReady.catch(function () {});
                    }
                    if (window.Cart && typeof Cart.removeOrderedLines === 'function' && Array.isArray(lines) && lines.length) {
                        Cart.removeOrderedLines(lines);
                    }
                    if (window.Cart && typeof Cart.persistNow === 'function') {
                        await Cart.persistNow();
                    }
                }
                await run();
            });
        </script>
    @endif
</body>
</html>
