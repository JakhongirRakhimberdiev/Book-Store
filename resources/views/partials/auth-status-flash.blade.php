{{-- Laravel flash `status`: cart.js ichidagi showAuthStatusToast bilan 3s ko'rinadi --}}
@if (session('status'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    var msg = @json(session('status'));
    function fire() {
        if (typeof window.showAuthStatusToast === 'function') {
            window.showAuthStatusToast(msg);
            return true;
        }
        return false;
    }
    if (fire()) return;
    function trySoon() {
        if (fire()) return;
        setTimeout(function () {
            fire();
            setTimeout(fire, 400);
        }, 0);
    }
    trySoon();
});
</script>
@endif
