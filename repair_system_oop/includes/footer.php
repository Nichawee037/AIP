</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php if (!empty($_SESSION['flash'])): $f=$_SESSION['flash']; unset($_SESSION['flash']); ?>
<script>Swal.fire({icon:<?= json_encode($f['type']) ?>,text:<?= json_encode($f['message'], JSON_UNESCAPED_UNICODE) ?>,confirmButtonText:'ตกลง'});</script>
<?php endif; ?>
</body></html>
