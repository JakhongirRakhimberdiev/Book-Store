<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <title>Online Kitob Do'koni</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold text-center text-blue-800 mb-8">Kutubxona Katalogi</h1>

        <div class="max-w-xl mx-auto mb-10">
            <form action="{{ route('books.index') }}" method="GET" class="flex gap-2">
                <input type="text" name="search" placeholder="Kitob nomi yoki muallif..." 
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       value="{{ request('search') }}">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Qidirish</button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($books as $book)
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition">
                    <img src="{{ asset($book->image_url) }}" class="h-64 w-full object-contain p-2">
                    <div class="p-5">
                        <div class="uppercase tracking-wide text-sm text-blue-500 font-semibold">{{ $book->genre }}</div>
                        <h2 class="block mt-1 text-lg leading-tight font-bold text-black">{{ $book->title }}</h2>
                        <p class="mt-2 text-gray-500 text-sm">{{ Str::limit($book->description, 100) }}</p>
                        
                        <div class="mt-4 flex items-center justify-between">
                            <span class="text-green-600 font-bold text-xl">{{ number_format($book->price) }} so'm</span>
                            <span class="text-xs bg-gray-200 px-2 py-1 rounded">Qoldiq: {{ $book->stock }} ta</span>
                        </div>
                        <p class="text-gray-400 text-xs mt-2">Muallif: {{ $book->author }} ({{ $book->year }})</p>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-10 text-gray-500">
                    Kechirasiz, bunday kitob topilmadi.
                </div>
            @endforelse
        </div>
    </div>

</body>
</html>