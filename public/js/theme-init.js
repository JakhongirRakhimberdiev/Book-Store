/* FOUCdan oldin: localhostStorage bo'yicha HTML elementiga «dark» qo'shiladi */
(function () {
    try {
        if (localStorage.getItem('kitobdukoni-theme') === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    } catch (e) { /* noop */ }
})();
