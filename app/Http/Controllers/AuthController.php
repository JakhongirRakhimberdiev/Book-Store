<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function create(Request $request)
    {
        if (Auth::check()) {
            return redirect()->to($this->sanitizeIntended($request->query('intended')));
        }

        return view('auth.login', [
            'intended' => $this->sanitizeIntended($request->query('intended')),
            /** Savatchadan to'lovga o'tish orqali login: pastda «Savatga qaytish». */
            'from_cart' => $request->boolean('from_cart'),
        ]);
    }

    public function store(Request $request)
    {
        if (Auth::check()) {
            return redirect()->to($this->sanitizeIntended($request->input('intended')));
        }

        $intended = $this->sanitizeIntended($request->input('intended'));

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100', 'regex:/^[\p{L}\'\-\s]+$/u'],
            'last_name'  => ['required', 'string', 'max:100', 'regex:/^[\p{L}\'\-\s]+$/u'],
            'email'      => ['required', 'string', 'email', 'max:255'],
            'password'   => ['required', 'string', 'min:8'],
        ], [
            'first_name.regex' => 'Ism faqat harflar, apostrof va defisdan tashkil topishi mumkin.',
            'last_name.regex'  => 'Familiya faqat harflar, apostrof va defisdan tashkil topishi mumkin.',
            'email.email'      => 'To\'g\'ri email manzil kiriting (@ belgisini qo\'shing).',
        ]);

        $email = mb_strtolower(trim($validated['email']));
        $first  = trim($validated['first_name']);
        $last   = trim($validated['last_name']);

        /** @var User|null $existing */
        $existing = User::query()->where('email', $email)->first();

        if (! $existing) {
            $user = User::query()->create([
                'first_name' => $first,
                'last_name'  => $last,
                'email'      => $email,
                'password'   => $validated['password'],
            ]);

            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->to($intended)->with('status', 'Muvaffaqiyatli ro\'yxatdan o\'tdingiz.');
        }

        if (! Hash::check($validated['password'], $existing->password)) {
            throw ValidationException::withMessages([
                'password' => 'Parol noto\'g\'ri.',
            ]);
        }

        if (! self::sameName($existing->first_name, $first) || ! self::sameName($existing->last_name, $last)) {
            throw ValidationException::withMessages([
                'email' => 'Bu email boshqa foydalanuvchiga tegishli yoki ism/familiya bazadagi yozuv bilan mos kelmaydi.',
            ]);
        }

        Auth::login($existing);
        $request->session()->regenerate();

        return redirect()->to($intended)->with('status', 'Tizimga muvaffaqiyatli kirildi.');
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('books.index');
    }

    protected function sanitizeIntended(?string $url): string
    {
        $default = route('books.index');

        if ($url === null || $url === '') {
            return $default;
        }

        $url = trim(urldecode($url));

        if ($url === '' || ! str_starts_with($url, '/') || str_starts_with($url, '//')) {
            return $default;
        }

        return $url;
    }

    private static function sameName(?string $a, string $b): bool
    {
        $norm = fn (?string $s) => mb_strtolower(preg_replace('/\s+/u', ' ', trim((string) $s)));

        return $norm($a) === $norm($b);
    }
}
