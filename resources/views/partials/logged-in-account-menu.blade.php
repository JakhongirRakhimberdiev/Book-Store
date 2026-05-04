{{-- Ism ustidagi menyu: Chiqish + Dark/Light (cart.js tema va menyuni boshqaradi) --}}
@php
    $hdrDisplayName = trim((string) (auth()->user()->first_name ?? ''));
    if ($hdrDisplayName === '') {
        $tok = preg_split('/\s+/u', trim((string) (auth()->user()->name ?? '')), -1, PREG_SPLIT_NO_EMPTY);
        $hdrDisplayName = $tok[0] ?? (auth()->user()->name ?: 'Akkaunt');
    }
@endphp
<div class="relative flex flex-col items-center" data-account-menu>
    <button type="button"
            id="account-link"
            data-account-menu-trigger
            aria-expanded="false"
            aria-haspopup="menu"
            aria-label="{{ $hdrDisplayName }}"
            class="group flex flex-col items-center text-emerald-700 hover:text-emerald-600 dark:text-emerald-400 dark:hover:text-emerald-300 transition active:scale-95 cursor-pointer rounded-xl focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 focus-visible:ring-offset-white dark:focus-visible:ring-offset-gray-950">
        <span class="inline-flex items-center justify-center w-11 h-11 rounded-full bg-emerald-100 text-emerald-700 shadow-sm dark:bg-emerald-900/60 dark:text-emerald-200
                     group-hover:bg-emerald-600 group-hover:text-white dark:group-hover:bg-emerald-600 dark:group-hover:text-white transition pointer-events-none">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                 class="w-5 h-5">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
        </span>
        <span class="text-[11px] font-semibold leading-none mt-1.5 tracking-wide max-w-[5.25rem] sm:max-w-none truncate text-center pointer-events-none">{{ $hdrDisplayName }}</span>
    </button>
    <div data-account-menu-panel
         class="hidden absolute top-full right-0 mt-2 w-[13.5rem] rounded-xl border border-gray-200 bg-white shadow-xl py-2 z-50 text-left dark:bg-gray-800 dark:border-gray-600 dark:shadow-black/40"
         role="menu">
        <form method="POST" action="{{ route('logout') }}" class="px-2 pb-1" role="none">
            @csrf
            <button type="submit"
                    onclick="if (typeof window.clearClientCartBeforeLogout === 'function') window.clearClientCartBeforeLogout();"
                    role="menuitem"
                    class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm font-bold text-white bg-gradient-to-r from-rose-500 to-red-600 hover:from-rose-600 hover:to-red-700 shadow-md active:scale-[0.98] transition">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/20">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                </span>
                Chiqish
            </button>
        </form>
        <div class="mx-2 mt-1 border-t border-gray-100 dark:border-gray-600 pt-1" role="none">
            <button type="button"
                    data-theme-toggle
                    role="menuitem"
                    class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm font-semibold text-gray-800 dark:text-gray-100 hover:bg-indigo-50 dark:hover:bg-gray-700/80 transition active:scale-[0.98]">
                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 text-white shadow-md">
                    <!-- Oy: light rejimda; quyosh: dark rejimda (bir xil katakda qatlam) -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round"
                         class="col-start-1 row-start-1 h-5 w-5 shrink-0 hidden pointer-events-none" data-theme-icon-moon>
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round"
                         class="col-start-1 row-start-1 h-5 w-5 shrink-0 hidden pointer-events-none" data-theme-icon-sun>
                        <circle cx="12" cy="12" r="5"></circle>
                        <line x1="12" y1="1" x2="12" y2="3"></line>
                        <line x1="12" y1="21" x2="12" y2="23"></line>
                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                        <line x1="1" y1="12" x2="3" y2="12"></line>
                        <line x1="21" y1="12" x2="23" y2="12"></line>
                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                    </svg>
                </span>
                <span data-theme-toggle-label class="leading-tight">Dark Mode</span>
            </button>
        </div>
    </div>
</div>
