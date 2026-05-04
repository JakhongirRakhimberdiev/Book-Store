<?php

namespace App\Http\Controllers;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    // 1. GET - Barcha kitoblar ro'yxatini olish yoki nomi bo'yicha qidirish
    public function index(Request $request) {
        $query = Book::with('author');

        // Qidiruv mantiqi: kitob nomi yoki muallif ismi bo'yicha
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ILIKE', '%' . $search . '%')
                  ->orWhereHas('author', function ($a) use ($search) {
                      $a->where('name', 'ILIKE', '%' . $search . '%');
                  });
            });
        }

        $books = $query->get();

        // Agar so'rov API orqali bo'lsa JSON qaytaradi, aks holda chiroyli sahifa
        if ($request->wantsJson()) {
            return response()->json($books);
        }

        // Janrlar bo'yicha umumlashgan kategoriyalar
        // (masalan: "Roman" → Tarixiy/Psixologik roman va h.k.)
        $genres = Book::activeCategories();

        return view('books.index', compact('books', 'genres'));
    }
    
    // 2. POST - Yangi kitob qo'shish
    public function store(Request $request)
    {
        $book = Book::create($request->all());
        return response()->json([
            'message' => 'Kitob muvaffaqiyatli qo\'shildi!',
            'data' => $book
        ], 201);
    }
    // 3. GET - Bitta kitobni ID bo'yicha ko'rish (Rasm, tavsif va h.k.)
    public function show($id)
    {
        $book = Book::with('author')->find($id);
        if (!$book) return response()->json(['message' => 'Kitob topilmadi'], 404);
        
        return response()->json($book);
    }
    // 4. PUT - Qoldiqni yoki ma'lumotlarni yangilash
    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);
        
        // Faqat yuborilgan maydonlarni yangilaydi (masalan, faqat 'stock')
        $book->update($request->all());

        return response()->json([
            'message' => 'Ma\'lumotlar yangilandi!',
            'data' => $book
        ]);
    }
    // 5. DELETE - Kitobni o'chirish
    public function destroy($id)
    {
        $book = Book::findOrFail($id);
        $book->delete();

        return response()->json(['message' => 'Kitob bazadan o\'chirildi']);
    }
}
