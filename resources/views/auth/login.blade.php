<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kirish — Online Kitob Do'koni</title>
    <meta name="authenticated" content="0">
    @include('partials.head-tailwind-dark')
</head>
<body class="bg-gradient-to-br from-slate-100 via-blue-50 to-indigo-100 dark:from-gray-950 dark:via-gray-900 dark:to-slate-900 min-h-[100dvh] flex flex-col transition-colors duration-200">

    <header class="bg-white/80 backdrop-blur border-b border-gray-200/80 dark:bg-gray-900/90 dark:border-gray-700/80 shrink-0 py-1.5">
        <div class="container mx-auto px-2 sm:px-4 flex items-center justify-between">
            <a href="{{ route('books.index') }}" class="flex items-center gap-1.5 text-sm font-bold text-blue-700 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 shrink-0">
                    <path d="M5 4a2 2 0 0 1 2-2h11v18H7a2 2 0 0 1-2-2V4zm2 14h11v-2H7v2zM18 0H7a4 4 0 0 0-4 4v16a4 4 0 0 0 4 4h11V0z"/>
                </svg>
                <span>Online Kitob Do'koni</span>
            </a>
        </div>
    </header>

    <main class="flex-1 min-h-0 overflow-y-auto overscroll-contain flex flex-col items-center justify-start sm:justify-center py-2 px-2 pt-3 sm:pt-6 sm:px-4">
        <div class="w-full max-w-[18rem] sm:max-w-sm md:max-w-md shrink-0">
            <div class="bg-white dark:bg-gray-800 dark:border-gray-700 rounded-xl sm:rounded-2xl shadow-lg sm:shadow-xl border border-gray-100 overflow-hidden transition-colors duration-200">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-3 py-2.5 sm:px-5 sm:py-4 text-white text-center">
                    <h1 class="text-base sm:text-xl font-bold leading-tight">Xush kelibsiz</h1>
                    <p class="text-blue-100/95 text-[10px] sm:text-xs mt-0.5 leading-tight">Tizimga kiring yoki ro'yxatdan o'ting</p>
                </div>

                <form method="POST" action="{{ route('login.store') }}" id="auth-form"
                      class="px-3 py-2.5 sm:px-5 sm:py-4 space-y-1.5 sm:space-y-2" autocomplete="off" novalidate>
                    @csrf
                    @php
                        /** Form xato qaytganda ham «savatdan kirish» saqlansin */
                        $persist_from_cart_login = ((string) old(
                            'from_cart',
                            (isset($from_cart) && $from_cart ? '1' : '0')
                        )) === '1';
                    @endphp
                    <input type="hidden" name="intended" value="{{ $intended ?? route('books.index') }}">
                    <input type="hidden" name="from_cart" value="{{ $persist_from_cart_login ? '1' : '0' }}">

                    <div>
                        <label for="first_name" class="block text-[11px] sm:text-xs font-semibold text-gray-700 dark:text-gray-200">Ism</label>
                        <input type="text" name="first_name" id="first_name"
                               value="{{ old('first_name') }}"
                               class="field-first w-full rounded-md border px-2.5 py-1.5 text-xs sm:text-sm outline-none ring-2 ring-transparent transition focus:border-blue-500 focus:ring-blue-500/25 dark:bg-gray-900/60 dark:text-gray-100 @error('first_name') border-red-400 bg-red-50 dark:bg-red-950/40 @else border-gray-200 dark:border-gray-600 @enderror"
                               maxlength="100" autocomplete="given-name">
                        <p class="hint-first mt-px text-[10px] sm:text-[11px] leading-tight text-gray-500 dark:text-gray-400 empty:hidden min-h-0"></p>
                        @error('first_name')
                            <p class="text-red-600 text-xs mt-0.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="last_name" class="block text-[11px] sm:text-xs font-semibold text-gray-700 dark:text-gray-200">Familiya</label>
                        <input type="text" name="last_name" id="last_name"
                               value="{{ old('last_name') }}"
                               class="field-last w-full rounded-md border px-2.5 py-1.5 text-xs sm:text-sm outline-none ring-2 ring-transparent transition focus:border-blue-500 focus:ring-blue-500/25 dark:bg-gray-900/60 dark:text-gray-100 @error('last_name') border-red-400 bg-red-50 dark:bg-red-950/40 @else border-gray-200 dark:border-gray-600 @enderror"
                               maxlength="100" autocomplete="family-name">
                        <p class="hint-last mt-px text-[10px] sm:text-[11px] leading-tight text-gray-500 dark:text-gray-400 empty:hidden min-h-0"></p>
                        @error('last_name')
                            <p class="text-red-600 text-xs mt-0.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-[11px] sm:text-xs font-semibold text-gray-700 dark:text-gray-200">Email</label>
                        <input type="email" name="email" id="email" inputmode="email"
                               value="{{ old('email') }}"
                               class="field-email w-full rounded-md border px-2.5 py-1.5 text-xs sm:text-sm outline-none ring-2 ring-transparent transition focus:border-blue-500 focus:ring-blue-500/25 dark:bg-gray-900/60 dark:text-gray-100 @error('email') border-red-400 bg-red-50 dark:bg-red-950/40 @else border-gray-200 dark:border-gray-600 @enderror"
                               maxlength="255" autocomplete="email">
                        <p class="hint-email mt-px text-[10px] sm:text-[11px] leading-tight text-gray-500 dark:text-gray-400 empty:hidden min-h-0"></p>
                        @error('email')
                            <p class="text-red-600 text-xs mt-0.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-[11px] sm:text-xs font-semibold text-gray-700 dark:text-gray-200">Parol</label>
                        <input type="password" name="password" id="password"
                               class="field-pass w-full rounded-md border px-2.5 py-1.5 text-xs sm:text-sm outline-none ring-2 ring-transparent transition focus:border-blue-500 focus:ring-blue-500/25 dark:bg-gray-900/60 dark:text-gray-100 @error('password') border-red-400 bg-red-50 dark:bg-red-950/40 @else border-gray-200 dark:border-gray-600 @enderror"
                               minlength="8" maxlength="72" autocomplete="new-password">
                        <p class="hint-pass mt-px text-[10px] sm:text-[11px] leading-tight text-gray-500 dark:text-gray-400 empty:hidden min-h-0"></p>
                        @error('password')
                            <p class="text-red-600 text-xs mt-0.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                            id="submit-btn"
                            class="submit-auth w-full inline-flex justify-center rounded-md sm:rounded-lg px-3 py-2 text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 shadow-md sm:shadow-lg hover:from-blue-700 hover:to-indigo-700 active:scale-[0.99] opacity-70 cursor-not-allowed transition mt-0.5"
                            disabled>
                        Davom etish
                    </button>
                </form>
            </div>

            <p class="text-center mt-2 text-[10px] sm:text-xs text-gray-600 dark:text-gray-400 pb-1 sm:pb-2 leading-tight">
                @if ($persist_from_cart_login)
                    <a href="{{ route('cart.index') }}" class="text-blue-600 dark:text-blue-400 font-semibold hover:underline">Savatga qaytish</a>
                @else
                    <a href="{{ route('books.index') }}" class="text-blue-600 dark:text-blue-400 font-semibold hover:underline">Katalogga qaytish</a>
                @endif
            </p>
        </div>
    </main>

    <script>
    (function () {
        const NAME_RX = /^[\p{L}'\-\s]+$/u;
        const form = document.getElementById('auth-form');
        const btn = document.getElementById('submit-btn');
        const f = document.getElementById('first_name');
        const l = document.getElementById('last_name');
        const e = document.getElementById('email');
        const p = document.getElementById('password');
        const hf = form.querySelector('.hint-first');
        const hl = form.querySelector('.hint-last');
        const he = form.querySelector('.hint-email');
        const hp = form.querySelector('.hint-pass');

        function borderOk(el, ok, neutral) {
            el.classList.remove('border-red-400', 'bg-red-50', 'border-green-400', 'bg-green-50/60');
            if (neutral) return;
            if (ok) el.classList.add('border-green-400', 'bg-green-50/60');
            else el.classList.add('border-red-400', 'bg-red-50');
        }

        function validFirst() {
            const v = f.value.trim();
            if (!v) { hf.textContent = ''; hf.classList.remove('text-emerald-600'); borderOk(f, false, true); return false; }
            if (v.length < 2 || !NAME_RX.test(v)) {
                hf.textContent = 'Faqat harflar (lotin yoki kirill), apostrof va defis.';
                hf.classList.remove('text-emerald-600'); borderOk(f, false, false); return false;
            }
            hf.textContent = '✓'; hf.classList.add('text-emerald-600'); borderOk(f, true, false); return true;
        }

        function validLast() {
            const v = l.value.trim();
            if (!v) { hl.textContent = ''; hl.classList.remove('text-emerald-600'); borderOk(l, false, true); return false; }
            if (v.length < 2 || !NAME_RX.test(v)) {
                hl.textContent = 'Faqat harflar (lotin yoki kirill), apostrof va defis.';
                hl.classList.remove('text-emerald-600'); borderOk(l, false, false); return false;
            }
            hl.textContent = '✓'; hl.classList.add('text-emerald-600'); borderOk(l, true, false); return true;
        }

        function validEmail() {
            const v = e.value.trim();
            if (!v) { he.textContent = ''; he.classList.remove('text-emerald-600'); borderOk(e, false, true); return false; }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) {
                he.textContent = 'Emailda @ va domen bo‘lishi kerak.';
                he.classList.remove('text-emerald-600'); borderOk(e, false, false); return false;
            }
            he.textContent = '✓'; he.classList.add('text-emerald-600'); borderOk(e, true, false); return true;
        }

        function validPass() {
            const v = p.value;
            if (!v.length) { hp.textContent = ''; hp.classList.remove('text-emerald-600'); borderOk(p, false, true); return false; }
            if (v.length < 8) {
                hp.textContent = 'Kamida 8 belgi.'; hp.classList.remove('text-emerald-600'); borderOk(p, false, false); return false;
            }
            if (!/[a-z\u0400-\u04FF]/i.test(v)) {
                hp.textContent = 'Kamida bitta harf bo‘lsin.'; hp.classList.remove('text-emerald-600'); borderOk(p, false, false); return false;
            }
            hp.textContent = '✓'; hp.classList.add('text-emerald-600'); borderOk(p, true, false); return true;
        }

        function toggleSubmit() {
            const ok = validFirst() && validLast() && validEmail() && validPass();
            btn.disabled = !ok;
            if (ok) { btn.classList.remove('opacity-70', 'cursor-not-allowed'); }
            else { btn.classList.add('opacity-70', 'cursor-not-allowed'); }
        }

        [f, l, e, p].forEach(el => {
            el.addEventListener('input', () => { validFirst(); validLast(); validEmail(); validPass(); toggleSubmit(); });
            el.addEventListener('blur', () => { validFirst(); validLast(); validEmail(); validPass(); toggleSubmit(); });
        });

        form.addEventListener('submit', (ev) => {
            if (!(validFirst() && validLast() && validEmail() && validPass())) { ev.preventDefault(); toggleSubmit(); }
        });

        toggleSubmit();
    })();
    </script>
</body>
</html>
