(function () {
    'use strict';

    function digitsOnly(v) {
        return String(v || '').replace(/\D/g, '');
    }

    function normalizePhone(raw) {
        let d = digitsOnly(raw);
        if (d.startsWith('998')) d = d.slice(3);
        if (d.length > 9) d = d.slice(0, 9);
        return '+998' + d;
    }

    /** @param {string} nine 0–9 digits, max length 9 */
    function formatMask(nine) {
        const d = String(nine || '').replace(/\D/g, '').slice(0, 9);
        const g1 = d.slice(0, 2);
        const g2 = d.slice(2, 5);
        const g3 = d.slice(5, 7);
        const g4 = d.slice(7, 9);
        return (
            '+998 (' +
            g1 +
            '_'.repeat(2 - g1.length) +
            ') ' +
            g2 +
            '_'.repeat(3 - g2.length) +
            '-' +
            g3 +
            '_'.repeat(2 - g3.length) +
            '-' +
            g4 +
            '_'.repeat(2 - g4.length)
        );
    }

    function wirePhone(maskEl, hiddenEl) {
        if (!maskEl || !hiddenEl) return;

        let nine = '';
        const hm = /^\+998(\d{0,9})$/.exec(String(hiddenEl.value || '').trim());
        if (hm && hm[1]) nine = hm[1].slice(0, 9);

        function flushPhoneFromMaskVisual() {
            let d = digitsOnly(maskEl.value);
            if (d.startsWith('998')) {
                d = d.slice(3);
            }
            d = d.slice(0, 9);
            nine = d;
            hiddenEl.value = '+998' + nine;
            const want = formatMask(nine);
            if (maskEl.value !== want) {
                maskEl.value = want;
                const pos = maskEl.value.length;
                queueMicrotask(() => {
                    if (document.activeElement === maskEl) {
                        maskEl.setSelectionRange(pos, pos);
                    }
                });
            }
            if (typeof window.refreshCheckoutContinueState === 'function') {
                window.refreshCheckoutContinueState();
            }
        }

        function render() {
            maskEl.value = formatMask(nine);
            hiddenEl.value = '+998' + nine;
            queueMicrotask(() => {
                const end = maskEl.value.length;
                if (document.activeElement === maskEl) maskEl.setSelectionRange(end, end);
                if (typeof window.refreshCheckoutContinueState === 'function') {
                    window.refreshCheckoutContinueState();
                }
            });
        }

        /** Brauzer autofill koʻpincha maskani to‘ldirib qo‘yadi, beforeinput esa ishlamaydi — yashirin `phone` esa bo‘sh/buzyilgan qolardi. */
        maskEl.addEventListener('change', flushPhoneFromMaskVisual);
        maskEl.addEventListener('blur', flushPhoneFromMaskVisual);
        maskEl.addEventListener('input', flushPhoneFromMaskVisual);

        maskEl.addEventListener('beforeinput', (e) => {
            const t = e.inputType || '';
            if (t === 'insertFromPaste') {
                e.preventDefault();
                return;
            }
            if (
                t === 'insertText' ||
                t === 'insertReplacementText'
            ) {
                const raw = e.data != null ? String(e.data) : '';
                const incoming = digitsOnly(raw);
                if (!incoming) {
                    e.preventDefault();
                    return;
                }
                e.preventDefault();
                for (let i = 0; i < incoming.length && nine.length < 9; i++) {
                    nine += incoming[i];
                }
                render();
                return;
            }
            if (t === 'deleteContentBackward' || t === 'deleteContentForward' || t === 'deleteByCut') {
                e.preventDefault();
                if (nine.length) nine = nine.slice(0, -1);
                render();
                return;
            }
            if (t.startsWith('insert')) e.preventDefault();
        });

        maskEl.addEventListener('paste', (e) => {
            e.preventDefault();
            let t = digitsOnly(e.clipboardData?.getData('text') || '');
            if (t.startsWith('998')) t = t.slice(3);
            t = t.slice(0, 9);
            nine = t;
            render();
        });

        const supportsBeforeInput =
            typeof window !== 'undefined' &&
            'InputEvent' in window &&
            'onbeforeinput' in HTMLInputElement.prototype;

        /** Keydown fallback when beforeinput is not supported. */
        maskEl.addEventListener('keydown', (e) => {
            if (supportsBeforeInput) return;
            if (e.key !== 'Backspace' && e.key !== 'Delete') return;
            e.preventDefault();
            if (nine.length) nine = nine.slice(0, -1);
            render();
        });

        render();
        queueMicrotask(flushPhoneFromMaskVisual);
        window.addEventListener('load', () => queueMicrotask(flushPhoneFromMaskVisual), { once: true });
        hiddenEl.closest('form')?.addEventListener(
            'submit',
            () => {
                hiddenEl.value = normalizePhone('+998' + nine);
            },
            { passive: true },
        );
    }

    /** Ism‑familiya: unicode harflar, bo‘sh joy, apostrof, tire (server bilan mos). */
    const NAME_OK_LINE = /^[\p{L}\s'\-'’]+$/u;

    function sanitizeUnicodeName(raw) {
        return String(raw || '').normalize('NFKC').replace(/[^\p{L}\s'\-'’]/gu, '');
    }

    function clampDigitsIn(value, max) {
        return String(value || '').replace(/\D/g, '').slice(0, max);
    }

    /** @param {ParentNode|null} scope */
    function wireCheckoutNameFields(scope) {
        if (!scope) return;
        scope.querySelectorAll('.checkout-name-field').forEach((el) => {
            if (!(el instanceof HTMLInputElement)) return;
            const flush = () => {
                const s = sanitizeUnicodeName(el.value);
                if (el.value !== s) el.value = s;
            };
            el.addEventListener('input', flush);
            el.addEventListener('blur', flush);
        });
    }

    /** Uy/Xonadon: faqat raqam, maksimal 3 ta (4‑ni yozib bo‘lmaydi — xabar kerak emas). */
    /** @param {ParentNode|null} scope */
    function wireCheckoutHouseFlat(scope) {
        if (!scope) return;
        scope.querySelectorAll('.checkout-house-flat').forEach((el) => {
            if (!(el instanceof HTMLInputElement)) return;

            el.addEventListener('beforeinput', (e) => {
                const t = e.inputType || '';
                if (t === 'insertFromPaste') {
                    e.preventDefault();
                    const ss = el.selectionStart ?? 0;
                    const se = el.selectionEnd ?? 0;
                    const pasted = clampDigitsIn(e.clipboardData?.getData('text') || '', 512);
                    const merged = clampDigitsIn(el.value.slice(0, ss) + pasted + el.value.slice(se), 3);
                    el.value = merged;
                    const pos = merged.length;
                    queueMicrotask(() => {
                        el.setSelectionRange(pos, pos);
                        if (typeof window.refreshCheckoutContinueState === 'function') window.refreshCheckoutContinueState();
                    });
                    return;
                }

                if (t === 'insertText' || t === 'insertReplacementText') {
                    const data = e.data != null ? String(e.data) : '';
                    if (/\D/.test(data)) {
                        e.preventDefault();
                        return;
                    }
                    const ss = el.selectionStart ?? 0;
                    const se = el.selectionEnd ?? 0;
                    const next = clampDigitsIn(el.value.slice(0, ss) + data + el.value.slice(se), 99);
                    if (next.length > 3) e.preventDefault();
                }
            });

            el.addEventListener('input', () => {
                const s = clampDigitsIn(el.value, 3);
                if (el.value !== s) el.value = s;
            });
            el.addEventListener('blur', () => {
                const s = clampDigitsIn(el.value, 3);
                if (el.value !== s) el.value = s;
            });
        });
    }

    function syncPaymentAppCards() {
        const grid = document.getElementById('checkout-app-cards');
        if (!grid) return;
        const app = document.getElementById('checkout-pay-app');
        const labels = [...grid.querySelectorAll('[data-payment-card]')];
        const useApp = app instanceof HTMLInputElement && app.checked;
        /** @type {HTMLElement|null} */
        let selected = null;
        labels.forEach((lab) => {
            const inp = lab.querySelector('input[name="payment_app"]');
            if (inp instanceof HTMLInputElement && inp.checked && !inp.disabled && useApp) {
                selected = lab;
            }
        });
        labels.forEach((lab) => {
            const dim = Boolean(useApp && selected && lab !== selected);
            lab.classList.toggle('scale-95', dim);
            lab.classList.toggle('scale-100', !dim);
            lab.classList.toggle('opacity-90', dim);
            lab.classList.toggle('opacity-100', !dim);
        });
    }

    function wireDistricts(regionSel, districtSel, map) {
        if (!regionSel || !districtSel || !map) return;

        const initDist = districtSel.getAttribute('data-initial-district');
        if (initDist) {
            districtSel.dataset.wantedDistrict = initDist;
        }

        function refill() {
            const code = regionSel.value;
            const list = map[code] || [];
            districtSel.innerHTML = '';

            const ph = document.createElement('option');
            ph.value = '';
            ph.textContent = code ? 'Tumanni tanlang' : 'Avval viloyatni tanlang';
            ph.disabled = true;
            ph.selected = true;
            districtSel.appendChild(ph);

            list.forEach((name) => {
                const opt = document.createElement('option');
                opt.value = name;
                opt.textContent = name;
                districtSel.appendChild(opt);
            });

            districtSel.disabled = !code || !list.length;
            districtSel.required = !!(code && list.length);

            const wd = districtSel.dataset.wantedDistrict || '';
            if (wd && [...districtSel.options].some((o) => o.value === wd)) {
                districtSel.value = wd;
                delete districtSel.dataset.wantedDistrict;
                districtSel.removeAttribute('data-initial-district');
            }
            queueMicrotask(() => refreshSubmitBtn());
        }

        regionSel.addEventListener('change', refill);
        refill();
    }

    function wirePayment() {
        const cash = document.getElementById('checkout-pay-cash');
        const app = document.getElementById('checkout-pay-app');
        const box = document.getElementById('checkout-app-list');

        function apply() {
            if (!(app instanceof HTMLInputElement) || !(cash instanceof HTMLInputElement) || !(box instanceof HTMLElement)) return;
            const use = app.checked && !app.disabled;
            box.classList.toggle('hidden', !use);
            box.setAttribute('aria-hidden', use ? 'false' : 'true');
            box.querySelectorAll('input[name="payment_app"]').forEach((inp) => {
                if (!(inp instanceof HTMLInputElement)) return;
                inp.disabled = !use;
                inp.required = use;
                if (!use) inp.checked = false;
            });
            refreshSubmitBtn();
            syncPaymentAppCards();
        }

        cash.addEventListener('change', apply);
        app.addEventListener('change', apply);
        apply();
        cash.closest('label')?.addEventListener('click', () => queueMicrotask(apply));
        app.closest('label')?.addEventListener('click', () => queueMicrotask(apply));
        document.querySelectorAll('#checkout-app-cards input[name="payment_app"]').forEach((inp) => {
            inp.addEventListener('change', () =>
                queueMicrotask(() => {
                    syncPaymentAppCards();
                    refreshSubmitBtn();
                }),
            );
        });
        /** Label / karta ustiga bosishda `change` kechiksa — `click` bilan tugmani qayta tekshiramiz. */
        document.getElementById('checkout-app-cards')?.addEventListener('click', () => queueMicrotask(refreshSubmitBtn), true);
    }

    function refreshSubmitBtn() {
        const form = document.getElementById('checkout-order-form');
        const btn = document.getElementById('checkout-submit-order');
        const phoneH = document.getElementById('checkout-phone-normalized');
        const region = document.getElementById('checkout-region-code');
        const district = document.getElementById('checkout-district');
        const cash = document.getElementById('checkout-pay-cash');
        const app = document.getElementById('checkout-pay-app');
        const appBox = document.getElementById('checkout-app-list');

        if (!(form instanceof HTMLFormElement) || !(btn instanceof HTMLButtonElement) || !(phoneH instanceof HTMLInputElement)) return;

        const phoneOk = /^\+998\d{9}$/.test((phoneH.value || '').trim());

        const ln = document.getElementById('checkout-last-name');
        const fn = document.getElementById('checkout-first-name');
        const pm = document.getElementById('checkout-patronymic');
        const namesOk =
            NAME_OK_LINE.test(String(ln instanceof HTMLInputElement ? ln.value : '').trim()) &&
            NAME_OK_LINE.test(String(fn instanceof HTMLInputElement ? fn.value : '').trim()) &&
            NAME_OK_LINE.test(String(pm instanceof HTMLInputElement ? pm.value : '').trim()) &&
            !!String(ln instanceof HTMLInputElement ? ln.value : '').trim() &&
            !!String(fn instanceof HTMLInputElement ? fn.value : '').trim() &&
            !!String(pm instanceof HTMLInputElement ? pm.value : '').trim();

        const house = document.getElementById('checkout-house-number');
        const flat = document.getElementById('checkout-apartment');
        const hn = clampDigitsIn(house instanceof HTMLInputElement ? house.value : '', 3);
        const ap = clampDigitsIn(flat instanceof HTMLInputElement ? flat.value : '', 3);
        const addrOk =
            !!document.getElementById('checkout-settlement')?.value.trim() &&
            !!document.getElementById('checkout-street')?.value.trim() &&
            hn.length >= 1 &&
            hn.length <= 3 &&
            ap.length <= 3;

        const regOk = region instanceof HTMLSelectElement && !!region.value.trim();

        const dstOk =
            district instanceof HTMLSelectElement &&
            !district.disabled &&
            !!district.value.trim();

        const payCashOk = cash instanceof HTMLInputElement && cash.checked;
        const payAppSel = app instanceof HTMLInputElement && app.checked;

        let appPickOk = true;
        if (payAppSel && appBox && !appBox.classList.contains('hidden')) {
            const apps = [...form.querySelectorAll('input[name="payment_app"]')];
            appPickOk = apps.some(
                (r) => r instanceof HTMLInputElement && r.checked && !r.disabled,
            );
        }

        const payChosen = payCashOk || payAppSel;
        const ok = !!(phoneOk && namesOk && addrOk && regOk && dstOk && payChosen && appPickOk);

        btn.disabled = !ok;
    }

    function wireFormDelegation(form) {
        form.addEventListener('input', () => queueMicrotask(refreshSubmitBtn), true);
        form.addEventListener('change', () => queueMicrotask(refreshSubmitBtn), true);
        form.addEventListener('submit', (e) => {
            const hid = document.getElementById('checkout-phone-normalized');
            const mask = document.getElementById('checkout-phone-mask');
            const linesJson = document.getElementById('checkout-order-lines-json');
            if (!(hid instanceof HTMLInputElement) || !(mask instanceof HTMLInputElement)) return;
            hid.value = normalizePhone(mask.value);
            if (!/^\+998\d{9}$/.test(hid.value)) {
                e.preventDefault();
            }

            const modeEl = document.getElementById('checkout-mode');
            const mode = modeEl instanceof HTMLInputElement ? modeEl.value : '';
            if (linesJson instanceof HTMLInputElement) {
                if (mode === 'buy_now') {
                    const bidInp = document.querySelector('input[name="book_id"]');
                    const bid = bidInp instanceof HTMLInputElement ? String(bidInp.value || '').trim() : '';
                    linesJson.value = bid ? JSON.stringify([{ id: bid, qty: 1 }]) : '';
                } else if (
                    mode === 'cart' &&
                    window.Cart &&
                    typeof window.Cart.getSorted === 'function'
                ) {
                    const items = window.Cart.getSorted().filter((it) => it.selected !== false && (Number(it.qty) || 1) > 0);
                    linesJson.value = JSON.stringify(
                        items.map((it) => ({ id: String(it.id), qty: Math.max(1, Number(it.qty) || 1) })),
                    );
                    const idsInp = document.getElementById('checkout-book-ids');
                    if (idsInp instanceof HTMLInputElement) {
                        idsInp.value = items.map((it) => it.id).join(',');
                        if (!idsInp.value) {
                            e.preventDefault();
                        }
                    }
                }
            }

            refreshSubmitBtn();
        });

        refreshSubmitBtn();
    }

    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('checkout-order-form');
        if (!(form instanceof HTMLFormElement)) return;

        wirePhone(document.getElementById('checkout-phone-mask'), document.getElementById('checkout-phone-normalized'));

        wireCheckoutNameFields(form);
        wireCheckoutHouseFlat(form);

        wireDistricts(
            document.getElementById('checkout-region-code'),
            document.getElementById('checkout-district'),
            window.__checkoutDistricts || {},
        );

        wirePayment();
        wireFormDelegation(form);

        syncPaymentAppCards();
        queueMicrotask(refreshSubmitBtn);
    });

    window.refreshCheckoutContinueState = refreshSubmitBtn;
})();
