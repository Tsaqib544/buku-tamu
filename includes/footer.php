</div><!-- end container -->

<footer class="<?= isLoggedIn() ? 'footer-logged' : 'd-none' ?> text-center text-muted py-3 mt-4 border-top">
    <small><?= APP_NAME ?> &mdash; Sistem Manajemen Tamu Digital</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/script.js"></script>
</body>

</html>