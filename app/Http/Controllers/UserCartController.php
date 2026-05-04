<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserCartController extends Controller
{
    /**
     * Joriy foydalanuvchining savatchasi (Cart.js formatiga mos).
     *
     * @return array{items: list<array{id: string, qty: int, selected: bool, addedAt: int}>}
     */
    public function show(Request $request)
    {
        $items = CartItem::query()
            ->where('user_id', $request->user()->id)
            ->orderByDesc('added_at')
            ->get()
            ->map(fn (CartItem $row) => [
                'id'       => (string) $row->book_id,
                'qty'      => max(1, (int) $row->quantity),
                'selected' => (bool) $row->selected,
                'addedAt'  => (int) ($row->added_at ??
                    ($row->created_at ? $row->created_at->getTimestamp() * 1000 : (int) round(microtime(true) * 1000))),
            ])
            ->values()
            ->all();

        return response()->json(['items' => $items]);
    }

    public function sync(Request $request)
    {
        $validated = $request->validate([
            'items'            => ['present', 'array'],
            'items.*.id'       => ['required', 'string', Rule::exists('books', 'id')],
            'items.*.qty'      => ['required', 'integer', 'min:1', 'max:999'],
            'items.*.selected' => ['sometimes', 'boolean'],
            'items.*.addedAt'  => ['sometimes', 'nullable', 'integer', 'min:0'],
        ]);

        $userId = $request->user()->id;

        CartItem::query()->where('user_id', $userId)->delete();

        $nowMs = (int) round(microtime(true) * 1000);

        foreach ($validated['items'] as $row) {
            CartItem::query()->create([
                'user_id'  => $userId,
                'book_id'  => (int) $row['id'],
                'quantity' => (int) $row['qty'],
                'selected' => array_key_exists('selected', $row) ? (bool) $row['selected'] : true,
                'added_at' => isset($row['addedAt']) ? (int) $row['addedAt'] : $nowMs,
            ]);
        }

        return response()->json(['ok' => true]);
    }
}
