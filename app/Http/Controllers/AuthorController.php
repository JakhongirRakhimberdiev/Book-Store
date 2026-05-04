<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function index(Request $request)
    {
        $authors = Author::withCount('books')->get();

        if ($request->wantsJson()) {
            return response()->json($authors);
        }

        return view('authors.index', compact('authors'));
    }

    public function show(Request $request, $id)
    {
        $author = Author::with('books')->find($id);

        if (!$author) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Muallif topilmadi'], 404);
            }
            abort(404, 'Muallif topilmadi');
        }

        if ($request->wantsJson()) {
            return response()->json($author);
        }

        return view('authors.show', compact('author'));
    }

    public function store(Request $request)
    {
        $author = Author::create($request->all());
        return response()->json([
            'message' => 'Muallif muvaffaqiyatli qo\'shildi!',
            'data' => $author,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $author = Author::findOrFail($id);
        $author->update($request->all());

        return response()->json([
            'message' => 'Muallif ma\'lumotlari yangilandi!',
            'data' => $author,
        ]);
    }

    public function destroy($id)
    {
        $author = Author::findOrFail($id);
        $author->delete();

        return response()->json(['message' => 'Muallif bazadan o\'chirildi']);
    }
}
