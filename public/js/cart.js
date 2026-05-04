/**
 * Savatcha boshqaruvchisi.
 * Mehmonlar: localStorage kalit — "kitob-savat".
 * Kirishlangan: Laravel /cart/data bilan bir vaqtda saqlanadi.
 *
 *   window.Cart                — asosiy API
 *   Cart.whenReady           — Promise (kirishda serverdan yuklash yakunlandi)
 */
(function () {
    'use strict';

    const Cart = {
        KEY: 'kitob-savat',

        get() {
            try {
                const data = localStorage.getItem(this.KEY);
                const items = data ? JSON.parse(data) : [];
                return Array.isArray(items) ? items : [];
            } catch (e) {
                return [];
            }
        },

        /** Faqat yozuv va badge (persist serverga trigger qilmaydi) */
        silentReplace(items) {
            const clean = normalizeItems(items);
            localStorage.setItem(this.KEY, JSON.stringify(clean));
            notifyChange();
        },

        save(items) {
            const clean = normalizeItems(items);
            localStorage.setItem(this.KEY, JSON.stringify(clean));
            notifyChange();
            scheduleServerPersist();
        },

        has(bookId) {
            return !!this._find(bookId);
        },

        qtyOf(bookId) {
            const it = this._find(bookId);
            return it ? (it.qty || 1) : 0;
        },

        _find(bookId) {
            const id = String(bookId);
            return this.get().find(it => String(it.id) === id);
        },

        add(bookId, qty = 1) {
            const id = String(bookId);
            const items = this.get();
            const existing = items.find(it => String(it.id) === id);
            const addQty = Math.max(1, Number(qty) || 1);
            if (existing) {
                existing.qty = (existing.qty || 1) + addQty;
                existing.selected = true;
            } else {
                items.push({ id, qty: addQty, addedAt: Date.now(), selected: true });
            }
            this.save(items);
        },

        increment(bookId) {
            this.add(bookId, 1);
        },

        decrement(bookId) {
            const id = String(bookId);
            const items = this.get();
            const existing = items.find(it => String(it.id) === id);
            if (!existing) return;
            existing.qty = (existing.qty || 1) - 1;
            if (existing.qty <= 0) {
                this.save(items.filter(it => String(it.id) !== id));
            } else {
                this.save(items);
            }
        },

        setQty(bookId, qty) {
            const id = String(bookId);
            const n = Math.max(0, Math.floor(Number(qty) || 0));
            let items = this.get();
            if (n === 0) {
                this.save(items.filter(it => String(it.id) !== id));
                return;
            }
            const existing = items.find(it => String(it.id) === id);
            if (existing) {
                existing.qty = n;
            } else {
                items.push({ id, qty: n, addedAt: Date.now(), selected: true });
            }
            this.save(items);
        },

        remove(bookId) {
            const id = String(bookId);
            this.save(this.get().filter(it => String(it.id) !== id));
        },

        count() {
            return this.get().reduce((s, it) => s + (it.qty || 1), 0);
        },

        clear() {
            localStorage.removeItem(this.KEY);
            notifyChange();
            scheduleServerPersist();
        },

        /**
         * Buyurtma yakunlangan qatorlar bo‘yicha savatdan miqdorni kamaytiradi (faqat tanlangan/buy_now).
         * @param {readonly { id: string, qty?: number }[]} lines
         */
        removeOrderedLines(lines) {
            if (!Array.isArray(lines) || lines.length === 0) return;

            const deduct = new Map();
            lines.forEach((raw) => {
                const id = raw && raw.id != null ? String(raw.id).trim() : '';
                const q = Math.max(0, Math.floor(Number(raw.qty) || 0));
                if (!id || q <= 0) return;
                deduct.set(id, (deduct.get(id) || 0) + q);
            });

            if (deduct.size === 0) return;

            const items = this.get();
            const next = [];

            items.forEach((it) => {
                const id = String(it.id);
                let rm = deduct.get(id);
                if (rm != null && rm > 0) {
                    const have = Number(it.qty) || 1;
                    const take = Math.min(have, rm);
                    rm -= take;
                    deduct.set(id, rm);
                    const left = have - take;
                    if (left > 0) {
                        next.push({ ...it, qty: left });
                    }
                    return;
                }
                next.push(it);
            });

            this.save(next);
        },

        getSorted() {
            return this.get()
                .slice()
                .sort((a, b) => (b.addedAt || 0) - (a.addedAt || 0));
        },

        isSelected(bookId) {
            const it = this._find(bookId);
            if (!it) return false;
            return it.selected !== false;
        },

        setSelected(bookId, val) {
            const id = String(bookId);
            const items = this.get();
            const existing = items.find(it => String(it.id) === id);
            if (!existing) return;
            existing.selected = !!val;
            this.save(items);
        },

        selectAll(val) {
            const items = this.get().map(it => ({ ...it, selected: !!val }));
            this.save(items);
        },

        selectedItems() {
            return this.get().filter(it => it.selected !== false);
        },

        selectedCount() {
            return this.selectedItems().reduce((s, it) => s + (it.qty || 1), 0);
        },

        allSelected() {
            const items = this.get();
            return items.length > 0 && items.every(it => it.selected !== false);
        },
    };

    window.Cart = Cart;

    function normalizeItems(items) {
        return items.map(normOne);
    }

    function normOne(it) {
        return {
            id: String(it.id),
            qty: Math.max(1, Number(it.qty) || 1),
            selected: it.selected !== false,
            addedAt: typeof it.addedAt === 'number' && it.addedAt > 0 ? it.addedAt : Date.now(),
        };
    }

    /** Bir xil kitob uchun: miqdor va tanlashni biriktirib birlashtirish (qatiy qo'shilmaydi) */
    function mergeServerAndLocal(remote, local) {
        const map = new Map();

        remote.forEach((r) => {
            const n = normOne(r);
            map.set(String(n.id), { ...n });
        });

        local.forEach((l) => {
            const n = normOne(l);
            const key = String(n.id);
            if (!map.has(key)) {
                map.set(key, { ...n });
                return;
            }
            const ex = map.get(key);
            map.set(key, {
                ...ex,
                qty: Math.max(ex.qty, n.qty),
                selected: ex.selected !== false || n.selected !== false,
                addedAt: Math.max(ex.addedAt || 0, n.addedAt || 0),
            });
        });

        return Array.from(map.values());
    }

    function normalizeForCompare(items) {
        return normalizeItems(items)
            .map(it => ({ id: String(it.id), qty: it.qty, selected: !!it.selected, addedAt: it.addedAt }))
            .sort((a, b) => Number(a.id) - Number(b.id));
    }

    function itemsJsonEqual(a, b) {
        return JSON.stringify(normalizeForCompare(a)) === JSON.stringify(normalizeForCompare(b));
    }

    /** Kirishlanganligini sahifadagi meta orqali */
    function isAuthenticatedUser() {
        const m = document.querySelector('meta[name="authenticated"]');
        return !!(m && m.getAttribute('content') === '1');
    }

    function csrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    let persistTimer;

    async function pushCartToServer() {
        if (!isAuthenticatedUser()) return;
        const token = csrfToken();
        if (!token) return;

        const items = normalizeItems(Cart.get());

        await fetch('/cart/data', {
            method: 'PUT',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ items }),
        });
    }

    function scheduleServerPersist() {
        if (!isAuthenticatedUser()) return;

        clearTimeout(persistTimer);
        persistTimer = setTimeout(() => {
            void pushCartToServer().catch(() => {});
        }, 380);
    }

    /** Avvalo server va local merged -> LS + zarur bo'lsa DB yangilanadi */
    const hydrationDone = (async () => {
        if (!isAuthenticatedUser()) return;

        if (document.body?.dataset?.cartSkipRemoteHydrate === '1') {
            await Promise.resolve();
            return;
        }

        try {
            const token = csrfToken();
            if (!token) return;

            const res = await fetch('/cart/data', {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            let remoteItems = [];
            if (res.ok) {
                const data = await res.json();
                if (Array.isArray(data.items)) {
                    remoteItems = data.items;
                }
            }

            const merged = mergeServerAndLocal(remoteItems, Cart.get());

            Cart.silentReplace(merged);

            if (!itemsJsonEqual(merged, remoteItems)) {
                await fetch('/cart/data', {
                    method: 'PUT',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ items: merged }),
                });
            }
        } catch (e) {
            /* tarmoq/server xatosi: faqat mahalliy savat */
        }

        await Promise.resolve(); // mikro-queue
    })();

    Cart.whenReady = hydrationDone;

    /** Serverni darhol mavjud savat holati bilan sinxron qiladi (merge xato qayta-qayta to‘ldirib yubarmasligi uchun). */
    Cart.persistNow = async function persistNow() {
        if (!isAuthenticatedUser()) return;
        clearTimeout(persistTimer);
        await pushCartToServer();
    };

    function notifyChange() {
        document.dispatchEvent(new CustomEvent('cart:changed'));
    }

    function updateHeaderBadge() {
        const badge = document.getElementById('cart-count');
        if (!badge) return;
        const count = Cart.count();
        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : String(count);
            badge.classList.remove('hidden');
            badge.classList.add('flex');
        } else {
            badge.classList.add('hidden');
            badge.classList.remove('flex');
        }
    }

    window.updateCartBadge = updateHeaderBadge;

    function updateButtonsInCard(card) {
        const cartBtn = card.querySelector('.add-to-cart-btn');
        if (!cartBtn) return;

        const bookId = cartBtn.dataset.bookId;
        const inCart = Cart.has(bookId);
        const qty = Cart.qtyOf(bookId);

        if (inCart) {
            cartBtn.classList.remove('bg-white', 'text-blue-600', 'hover:bg-blue-600', 'hover:text-white');
            cartBtn.classList.add('bg-blue-600', 'text-white', 'hover:bg-blue-700');
            cartBtn.title = `Savatda: ${qty} ta. Yana qo'shish uchun bosing`;
            cartBtn.setAttribute('aria-label', `Yana qo'shish (savatda ${qty} ta)`);
        } else {
            cartBtn.classList.add('bg-white', 'text-blue-600', 'hover:bg-blue-600', 'hover:text-white');
            cartBtn.classList.remove('bg-blue-600', 'text-white', 'hover:bg-blue-700');
            cartBtn.title = "Savatga qo'shish";
            cartBtn.setAttribute('aria-label', "Savatga qo'shish");
        }
    }

    const CARD_SELECTOR = '.bg-white.rounded-xl';

    function updateAllBookCards() {
        document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
            const card = btn.closest(CARD_SELECTOR);
            if (card) updateButtonsInCard(card);
        });
    }

    window.updateAllBookCards = updateAllBookCards;

    function escapeAttr(s) {
        return String(s ?? '').replace(/"/g, '&quot;');
    }

    const TOAST_DURATION_MS = 3000;
    let toastEl;
    let toastTimer;
    let authToastTimer = null;

    function ensureToastEl() {
        if (toastEl) return toastEl;

        toastEl = document.createElement('div');
        toastEl.className =
            'fixed bottom-6 right-6 z-50 transform transition-all duration-300 ' +
            'translate-y-20 opacity-0 pointer-events-none';
        document.body.appendChild(toastEl);

        toastEl.addEventListener('mouseenter', () => clearTimeout(toastTimer));
        toastEl.addEventListener('mouseleave', () => {
            clearTimeout(toastTimer);
            toastTimer = setTimeout(hideToast, 1200);
        });

        return toastEl;
    }

    function hideToast() {
        if (!toastEl) return;
        toastEl.classList.add('translate-y-20', 'opacity-0', 'pointer-events-none');
    }

    function escapeHtml(s) {
        return String(s ?? '').replace(/[&<>"']/g, (c) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[c]));
    }

    function showAddedToCartToast({ title, image }) {
        const el = ensureToastEl();

        const safeTitle = escapeHtml(title || 'Kitob');
        const imgHtml = image
            ? `<img src="${escapeAttr(image)}" alt="" class="w-full h-full object-contain"
                    onerror="this.style.display='none'; this.parentElement.classList.add('bg-gray-200','dark:bg-gray-700');">`
            : '';

        el.innerHTML = `
            <div class="bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700
                        p-3 flex items-center gap-3 max-w-md">
                <div class="w-14 h-14 rounded-lg bg-gray-50 dark:bg-gray-900 flex-shrink-0 overflow-hidden flex items-center justify-center">
                    ${imgHtml}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-1.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"
                             class="w-3.5 h-3.5">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        Savatga qo'shildi
                    </div>
                    <div class="mt-0.5 text-sm font-bold text-gray-900 dark:text-gray-100 truncate" title="${safeTitle}">
                        ${safeTitle}
                    </div>
                </div>
                <a href="/cart"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg
                          bg-gradient-to-r from-emerald-500 to-green-600 text-white font-semibold text-sm
                          hover:from-emerald-600 hover:to-green-700 transition active:scale-95
                          shadow-md hover:shadow-lg whitespace-nowrap">
                    Savatga o'tish
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                         class="w-3.5 h-3.5">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </div>
        `;

        requestAnimationFrame(() => {
            el.classList.remove('translate-y-20', 'opacity-0', 'pointer-events-none');
        });

        clearTimeout(toastTimer);
        toastTimer = setTimeout(hideToast, TOAST_DURATION_MS);
    }

    /** Autentifikatsiya muvaffaqiyati bildirishnomasi ( boshqa sahifalarda Blade orqali ) */
    window.showAuthStatusToast = function (message) {
        const el = ensureToastEl();
        const txt = escapeHtml(message || '');
        el.innerHTML = `
            <div class="rounded-2xl shadow-2xl border border-emerald-100 dark:border-emerald-900/50 bg-white dark:bg-gray-800 p-4 pr-12 max-w-md relative">
                <button type="button" class="auth-toast-dismiss absolute top-3 right-3 p-1 rounded-lg text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200" aria-label="Yopish">✕</button>
                <div class="flex items-start gap-3">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-emerald-500 text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                             stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </span>
                    <div class="min-w-0">
                        <div class="text-xs font-bold uppercase tracking-wide text-emerald-600 dark:text-emerald-400">Muvaffaqiyatli</div>
                        <div class="mt-0.5 text-sm font-semibold text-gray-900 dark:text-gray-100 leading-snug">${txt}</div>
                    </div>
                </div>
            </div>
        `;
        const dismiss = () => { clearTimeout(authToastTimer); hideToast(); };
        const dismissBtn = el.querySelector('.auth-toast-dismiss');
        if (dismissBtn) dismissBtn.addEventListener('click', dismiss);

        clearTimeout(authToastTimer);
        requestAnimationFrame(() => {
            el.classList.remove('translate-y-20', 'opacity-0', 'pointer-events-none');
        });
        authToastTimer = setTimeout(dismiss, TOAST_DURATION_MS);
    };

    function extractBookInfo(card) {
        if (!card) return {};
        const titleEl = card.querySelector('h2, h3');
        const imgEl = card.querySelector('img');
        return {
            title: titleEl ? titleEl.textContent.trim() : '',
            image: imgEl ? imgEl.currentSrc || imgEl.src : '',
        };
    }

    function goToCheckoutOrLogin(targetPathQuery, fromCartCheckout) {
        const path = targetPathQuery || '/checkout';
        if (isAuthenticatedUser()) {
            window.location.href = path;
        } else {
            let q = '?intended=' + encodeURIComponent(path);
            if (fromCartCheckout) {
                q += '&from_cart=1';
            }
            window.location.href = '/login' + q;
        }
    }

    window.gotoCheckoutCart = function () {
        goToCheckoutOrLogin('/checkout?mode=cart', true);
    };

    window.gotoCheckoutBuyNow = function (bookId) {
        if (!bookId) return;
        goToCheckoutOrLogin('/checkout?mode=buy_now&book_id=' + encodeURIComponent(String(bookId)), false);
    };

    document.addEventListener('click', (e) => {
        const cartBtn = e.target.closest('.add-to-cart-btn');
        if (cartBtn) {
            e.preventDefault();
            const bookId = cartBtn.dataset.bookId;
            const card = cartBtn.closest(CARD_SELECTOR);
            const info = extractBookInfo(card);

            Cart.add(bookId, 1);
            updateHeaderBadge();
            if (card) updateButtonsInCard(card);
            showAddedToCartToast(info);
            return;
        }

        const buyBtn = e.target.closest('.buy-now-btn');
        if (buyBtn) {
            e.preventDefault();
            const bookId = buyBtn.dataset.bookId;
            if (!bookId) return;
            goToCheckoutOrLogin(`/checkout?mode=buy_now&book_id=${encodeURIComponent(bookId)}`, false);
            return;
        }
    });

    /** Kirish qilingan akkaunt menyusi — chiqish va tema tugmalari */
    function closeAllAccountMenus() {
        document.querySelectorAll('[data-account-menu]').forEach((wrap) => {
            const panel = wrap.querySelector('[data-account-menu-panel]');
            const btn = wrap.querySelector('[data-account-menu-trigger]');
            panel?.classList.add('hidden');
            if (btn) btn.setAttribute('aria-expanded', 'false');
        });
    }

    document.addEventListener('click', (e) => {
        const wrap = e.target.closest('[data-account-menu]');
        if (!wrap) {
            closeAllAccountMenus();
            return;
        }
        const trigger = e.target.closest('[data-account-menu-trigger]');
        if (trigger) {
            e.preventDefault();
            const panel = wrap.querySelector('[data-account-menu-panel]');
            const wasOpen = panel && !panel.classList.contains('hidden');
            closeAllAccountMenus();
            if (!wasOpen && panel) {
                panel.classList.remove('hidden');
                trigger.setAttribute('aria-expanded', 'true');
                refreshThemeToggleUI();
            }
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeAllAccountMenus();
    });

    /** Dark / Light rejim (localStorage: kitobdukoni-theme) */
    const THEME_STORAGE_KEY = 'kitobdukoni-theme';

    function isDarkMode() {
        return document.documentElement.classList.contains('dark');
    }

    function setDarkMode(on) {
        const root = document.documentElement;
        if (on) {
            root.classList.add('dark');
        } else {
            root.classList.remove('dark');
        }
        try {
            localStorage.setItem(THEME_STORAGE_KEY, on ? 'dark' : 'light');
        } catch (e) { /* noop */ }
        refreshThemeToggleUI();
    }

    function toggleSiteTheme() {
        setDarkMode(!isDarkMode());
    }

    function refreshThemeToggleUI() {
        const dark = isDarkMode();
        document.querySelectorAll('[data-theme-toggle]').forEach((btn) => {
            const label = btn.querySelector('[data-theme-toggle-label]');
            const moon = btn.querySelector('[data-theme-icon-moon]');
            const sun = btn.querySelector('[data-theme-icon-sun]');
            if (label) label.textContent = dark ? 'Light Mode' : 'Dark Mode';
            if (moon) moon.classList.toggle('hidden', dark);
            if (sun) sun.classList.toggle('hidden', !dark);
        });
    }

    window.toggleSiteTheme = toggleSiteTheme;
    window.refreshThemeToggleUI = refreshThemeToggleUI;

    document.addEventListener('click', (e) => {
        const tgl = e.target.closest('[data-theme-toggle]');
        if (!tgl) return;
        e.preventDefault();
        e.stopPropagation();
        toggleSiteTheme();
    });

    /** Chiqishda savat kalitini tozalash (keyingi browser foydalanuvchisi aralashmasin) */
    window.clearClientCartBeforeLogout = function () {
        try {
            localStorage.removeItem(Cart.KEY);
            notifyChange();
        } catch (err) { /* noop */ }
    };

    document.addEventListener('cart:changed', updateHeaderBadge);

    async function boot() {
        /* Tema ikonkalari savat serverga bog'liq emas — hydration osilib qolsa ham darhol yangilansin */
        refreshThemeToggleUI();
        if (Cart.whenReady && typeof Cart.whenReady.then === 'function') {
            await Cart.whenReady.catch(() => {});
        }
        refreshThemeToggleUI();
        updateHeaderBadge();
        updateAllBookCards();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => void boot());
    } else {
        void boot();
    }
})();
