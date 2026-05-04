<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>To'lovni amalga oshirish — Online Kitob Do'koni</title>
    <meta name="authenticated" content="1">
    @include('partials.head-tailwind-dark')
    <script src="{{ asset('js/cart.js') }}" defer></script>
</head>
<body class="bg-gray-100 dark:bg-gray-950 min-h-screen flex flex-col transition-colors duration-200">

    <header class="bg-white shadow-sm sticky top-0 shrink-0 dark:bg-gray-900 dark:border-b dark:border-gray-800 transition-colors duration-200">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center gap-3">
            <a href="{{ route('books.index') }}" class="font-bold text-blue-700 dark:text-blue-400">← Katalog</a>
            <span class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ auth()->user()->first_name ?? auth()->user()->name }}</span>
        </div>
    </header>

    <main class="flex-1 flex items-center justify-center p-6">
        <div class="bg-white dark:bg-gray-800 dark:border-gray-700 rounded-2xl shadow-md border border-gray-100 p-8 max-w-md text-center transition-colors duration-200">
            <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">To'lov oynasi</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-3">
                Bu yerda tez orada to'lov tizimi chiqadi (kartalar, Click, Payme va hokazo).
                Hozircha so'rov parametrlari saqlanishi uchun:
                <span class="font-mono text-xs bg-gray-100 dark:bg-gray-900 dark:text-gray-300 px-2 py-0.5 rounded break-all">
                    mode={{ $mode ?? '—' }}, book_id={{ $book_id ?? '—' }}, book_ids={{ $book_ids ?? '—' }}
                </span>
            </p>
            <a href="{{ route('checkout') }}{{ ($mode ?? null) !== null || ($book_id ?? null) !== null || ($book_ids ?? null) !== null ? '?' . http_build_query(array_filter(['mode' => $mode ?? null, 'book_id' => $book_id ?? null, 'book_ids' => $book_ids ?? null], fn ($v) => $v !== null && $v !== '')) : '' }}"
               class="mt-6 inline-block px-5 py-2.5 rounded-xl border-2 border-gray-200 dark:border-gray-600 font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/80">
                Orqaga
            </a>
        </div>
    </main>

    @include('partials.auth-status-flash')
</body>
</html>
