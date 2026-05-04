<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Live qidiruv: kitob nomi yoki muallif ismi
     * yozilgan jumla bilan BOSHLANADIGAN natijalar +
     * janr bo'yicha filtrlash.
     */
    public function index(Request $request)
    {
        $q     = trim((string) $request->input('q', ''));
        $genre = trim((string) $request->input('genre', ''));

        // Hech qanday filtr yo'q bo'lsa, bo'sh qaytarish
        // (frontend bunday holda asl ro'yxatni tiklaydi)
        if ($q === '' && $genre === '') {
            return response()->json([
                'authors' => [],
                'books'   => [],
            ]);
        }

        // MUALLIFLAR — faqat matn yozilganda chiqaramiz
        // (janr faqat kitoblarga xos)
        $authors = collect();
        if ($q !== '') {
            $authors = Author::where('name', 'ILIKE', $q . '%')
                ->withCount('books')
                ->orderBy('name')
                ->limit(20)
                ->get();
        }

        // KITOBLAR — q va genre AND tarzida birlashtiriladi
        $booksQuery = Book::with('author');

        if ($q !== '') {
            $prefix = $q . '%';
            $booksQuery->where(function ($query) use ($prefix) {
                $query->where('title', 'ILIKE', $prefix)
                      ->orWhereHas('author', function ($a) use ($prefix) {
                          $a->where('name', 'ILIKE', $prefix);
                      });
            });
        }

        if ($genre !== '') {
            // Kelgan qiymat — kategoriya nomi (masalan, "Roman").
            // Uni xom janrlar ro'yxatiga aylantirib (Tarixiy roman, Psixologik roman ...)
            // hammasi bo'yicha filtrlash.
            $booksQuery->whereIn('genre', Book::genresForCategory($genre));
        }

        $books = $booksQuery->orderBy('title')->limit(100)->get();

        return response()->json([
            'authors' => $authors,
            'books'   => $books,
        ]);
    }
}
