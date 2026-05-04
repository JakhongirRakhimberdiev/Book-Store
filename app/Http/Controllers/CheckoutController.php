<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        if ($request->query('resume') === '1') {
            /** @var array<string, mixed>|null $snap */
            $snap = $request->session()->pull('checkout_form_snapshot');
            if (is_array($snap)) {
                $modeRes = ($snap['mode'] ?? '') === 'buy_now' ? 'buy_now' : 'cart';
                $q       = ['mode' => $modeRes];
                $bid     = trim((string) ($snap['book_id'] ?? ''));
                if ($modeRes === 'buy_now' && $bid !== '') {
                    $q['book_id'] = $bid;
                }

                return redirect()->route('checkout', $q)->withInput($snap);
            }

            return redirect()->route('checkout');
        }

        $mode = $request->query('mode', 'cart');
        if (! in_array($mode, ['cart', 'buy_now'], true)) {
            $mode = 'cart';
        }

        $bookId  = $request->query('book_id');
        $book    = null;
        $buyBookId = null;

        if ($mode === 'buy_now') {
            if ($bookId === null || $bookId === '') {
                return redirect()
                    ->route('books.index')
                    ->withErrors(['checkout' => 'Kitob tanlanmagan.']);
            }
            $book = Book::with('author')->find($bookId);
            if (! $book) {
                return redirect()
                    ->route('books.index')
                    ->withErrors(['checkout' => 'Tanlangan kitob topilmadi yoki olib tashlangan.']);
            }
            $buyBookId = $book->id;
        }

        $regionsCfg = config('uzbekistan_regions', []);
        $regions    = $regionsCfg['regions'] ?? [];
        $districts  = $regionsCfg['districts'] ?? [];

        return view('checkout.show', [
            'mode'            => $mode,
            'book'            => $book,
            'buyBookId'       => $buyBookId,
            'regions'         => $regions,
            'districtsByCode' => $districts,
        ]);
    }

    public function storeOrder(Request $request): RedirectResponse
    {
        $regionCodes = collect(config('uzbekistan_regions.regions', []))->pluck('code')->filter()->values()->all();

        $request->merge([
            'last_name'    => trim((string) $request->input('last_name', '')),
            'first_name'   => trim((string) $request->input('first_name', '')),
            'patronymic'   => trim((string) $request->input('patronymic', '')),
            'house_number' => trim((string) $request->input('house_number', '')),
            'apartment'    => trim((string) $request->input('apartment', '')),
        ]);

        $nameRule = ['required', 'string', 'max:120', 'regex:/^[\p{L}\s\'’\-]+$/u'];

        $validated = $request->validate([
            'mode'               => ['required', 'in:cart,buy_now'],
            'book_id'            => ['nullable', 'required_if:mode,buy_now', 'string', 'max:64', Rule::exists('books', 'id')],
            'book_ids'           => ['nullable', 'string', 'max:2000'],
            'order_lines_json'   => [$request->input('mode') === 'cart' ? 'required' : 'nullable', 'string', 'max:64000'],
            'phone'              => ['required', 'regex:/^\+998\d{9}$/'],
            'last_name'          => $nameRule,
            'first_name'         => $nameRule,
            'patronymic'         => $nameRule,
            'region_code'        => ['required', 'string', Rule::in($regionCodes)],
            'district'           => ['required', 'string', 'max:200'],
            'settlement'         => ['required', 'string', 'max:300'],
            'street'             => ['required', 'string', 'max:300'],
            'house_number'       => ['required', 'regex:/^\d{1,3}$/'],
            'apartment'          => ['nullable', 'string', 'regex:/^\d{0,3}$/'],
            'payment_method'     => ['required', 'in:cash,app'],
            'payment_app'        => ['nullable', 'required_if:payment_method,app', 'in:click,payme,uzum_bank,paynet'],
        ], [
            'phone.regex'       => 'Telefon raqam +998 bilan 9 ta raqam bo‘lishi kerak.',
            'last_name.regex'   => 'Familiyada faqat harflar, bo‘sh joy, tire yoki apostrof bo‘lishi mumkin.',
            'first_name.regex'  => 'Ismda faqat harflar, bo‘sh joy, tire yoki apostrof bo‘lishi mumkin.',
            'patronymic.regex'  => 'Otasining ismida faqat harflar, bo‘sh joy, tire yoki apostrof bo‘lishi mumkin.',
            'house_number.regex' => 'Uy raqami 1–3 ta raqam bo‘lishi kerak.',
            'apartment.regex'   => 'Xonadon/Kvartira bo‘sh yoki 1–3 ta raqam bo‘lishi kerak.',
            'order_lines_json.required' => 'Savat buyurtmasi uchun mahsulotlar ro‘yxati yuborilmadi.',
            'payment_app.required_if' => 'Ilovalar orqali to‘lovda bitta ilovani tanlang.',
        ]);

        $deductionLines = $this->buildOrderDeductionLines($validated, $request);

        if ($validated['mode'] === 'cart' && $deductionLines === []) {
            return back()
                ->withInput()
                ->withErrors(['order_lines_json' => 'Buyurtma uchun kamida bitta tanlangan mahsulot kerak.']);
        }

        if ($validated['mode'] === 'buy_now' && $deductionLines === []) {
            return back()
                ->withInput()
                ->withErrors(['book_id' => 'Kitob topilmadi yoki noto‘g‘ri.']);
        }

        if ($validated['payment_method'] === 'cash') {
            $request->session()->forget('checkout_form_snapshot');
        } else {
            $request->session()->put('checkout_form_snapshot', [
                'mode'               => $validated['mode'],
                'book_id'            => (string) $request->input('book_id', ''),
                'phone'              => $validated['phone'],
                'last_name'          => $validated['last_name'],
                'first_name'         => $validated['first_name'],
                'patronymic'         => $validated['patronymic'],
                'region_code'        => $validated['region_code'],
                'district'           => $validated['district'],
                'settlement'         => $validated['settlement'],
                'street'             => $validated['street'],
                'house_number'       => $validated['house_number'],
                'apartment'          => (string) ($request->input('apartment') ?? ''),
                'book_ids'           => (string) $request->input('book_ids', ''),
                'order_lines_json'   => (string) ($validated['order_lines_json'] ?? $request->input('order_lines_json', '')),
                'payment_method'     => $validated['payment_method'],
                'payment_app'        => (string) ($validated['payment_app'] ?? ''),
            ]);
        }

        // Naqd bo‘lsa keyinroq thanks sahifasida savatdan ayiramiz; onlayn (ilova) — hali toʻlov yoʻq, savat va forma saqlansin.
        $request->session()->flash('order_placed', [
            'payment_method' => $validated['payment_method'],
            'payment_app'    => $validated['payment_app'] ?? null,
            'lines'          => $validated['payment_method'] === 'cash' ? $deductionLines : [],
        ]);

        return redirect()->route('checkout.thanks');
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return list<array{id: string, qty: positive-int}>
     */
    private function buildOrderDeductionLines(array $validated, Request $request): array
    {
        $mode = $validated['mode'];

        if ($mode === 'buy_now') {
            $bid = trim((string) $request->input('book_id', ''));
            if ($bid !== '' && Book::query()->whereKey($bid)->exists()) {
                return [['id' => $bid, 'qty' => 1]];
            }

            return [];
        }

        $raw = trim((string) ($validated['order_lines_json'] ?? ''));
        $decoded = json_decode($raw, true);
        if (! is_array($decoded)) {
            return [];
        }

        $totals = [];
        foreach ($decoded as $row) {
            if (! is_array($row)) {
                continue;
            }
            $id = isset($row['id']) ? trim((string) $row['id']) : '';
            $qty = (int) ($row['qty'] ?? 0);
            if ($id === '' || $qty < 1 || $qty > 999) {
                continue;
            }
            if (! Book::query()->whereKey($id)->exists()) {
                continue;
            }
            $next = ($totals[$id] ?? 0) + $qty;
            $totals[$id] = min(999, $next);
        }

        /** @var list<array{id: string, qty: positive-int}> */
        return collect($totals)
            ->map(fn (int $qty, string $id) => ['id' => $id, 'qty' => $qty])
            ->values()
            ->all();
    }

    public function thanks(Request $request): View
    {
        return view('checkout.thanks', [
            'order' => $request->session()->get('order_placed', []),
        ]);
    }

    /**
     * Vaqtinchalik: keyinchalik haqiqiy to'lov sahifasi ulanadi.
     */
    public function payment(Request $request)
    {
        return view('checkout.payment', [
            'mode'     => $request->query('mode'),
            'book_id'  => $request->query('book_id'),
            'book_ids' => $request->query('book_ids'),
        ]);
    }
}
