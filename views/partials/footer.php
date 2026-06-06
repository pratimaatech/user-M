</main><!-- /.main-content -->
</div><!-- /.wrapper -->

<!-- jQuery -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<!-- Bootstrap 5 Bundle (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
 views/partials/footer.php
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<!-- Custom JS -->
<script src="assets/js/app.js"></script>

<script>
// ── Pass PHP session data to JavaScript ───────────────────────
const CSRF_TOKEN = <?= json_encode($_SESSION['csrf_token'] ?? '') ?>;
const BASE_URL   = <?= json_encode(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/') ?>;
</script>

<?php if (isset($_SESSION['flash_error'])): ?>
<script>
Swal.fire({ icon: 'error', title: 'Oops!', text: <?= json_encode($_SESSION['flash_error']) ?> });
</script>
<?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<!-- Page-specific scripts injected here -->
<?php if (isset($pageScripts)) echo $pageScripts; ?>

<script>
// ── Logout confirmation ───────────────────────────────────────
(function () {
    const btn = document.getElementById('logoutBtn');
    if (!btn) return;
    btn.addEventListener('click', function (e) {
        e.preventDefault();
        Swal.fire({
            title:              'Sign out?',
            text:               'You will be returned to the login page.',
            icon:               'question',
            showCancelButton:   true,
            confirmButtonColor: '#0d6efd',
            cancelButtonColor:  '#6c757d',
            confirmButtonText:  'Yes, sign out',
            cancelButtonText:   'Stay',
        }).then(function (result) {
            if (result.isConfirmed) {
                window.location.href = 'logout.php';
            }
        });
    });
})();
</script>

</body>
</html>
