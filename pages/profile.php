<?php
require_once __DIR__ . '/../config/config.php';
requireLogin();

$conn = getConnection();
$errors = []; $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama        = trim($_POST['nama'] ?? '');
    $password    = trim($_POST['password'] ?? '');
    $passwordConf = trim($_POST['password_confirm'] ?? '');

    if (!$nama) $errors[] = 'Nama wajib diisi.';
    if ($password && $password !== $passwordConf) $errors[] = 'Konfirmasi password tidak cocok.';
    if ($password && strlen($password) < 6) $errors[] = 'Password minimal 6 karakter.';

    if (empty($errors)) {
        $uid = $_SESSION['user_id'];
        if ($password) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET nama=?, password=? WHERE id=?");
            $stmt->bind_param('ssi', $nama, $hash, $uid);
        } else {
            $stmt = $conn->prepare("UPDATE users SET nama=? WHERE id=?");
            $stmt->bind_param('si', $nama, $uid);
        }
        $stmt->execute();
        $_SESSION['nama'] = $nama;
        $success = 'Profil berhasil diperbarui.';
        $stmt->close();
    }
}

$user = $conn->query("SELECT * FROM users WHERE id = " . (int)$_SESSION['user_id'])->fetch_assoc();
$conn->close();
$pageTitle = 'Profil Saya';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h4><i class="bi bi-person me-2 text-primary"></i>Profil Saya</h4>
</div>

<?php if ($success): ?>
<div class="alert alert-success alert-auto-dismiss alert-dismissible fade show">
    <i class="bi bi-check-circle-fill me-2"></i><?= $success ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if ($errors): ?>
<div class="alert alert-danger"><ul class="mb-0"><?php foreach($errors as $e): ?><li><?= $e ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<div class="card form-card" style="max-width:520px">
    <div class="card-body p-4">
        <div class="d-flex align-items-center gap-3 mb-4">
            <div class="stat-icon bg-primary bg-opacity-10 text-primary" style="width:60px;height:60px;font-size:1.8rem;border-radius:50%">
                <i class="bi bi-person-fill"></i>
            </div>
            <div>
                <div class="fw-bold fs-5"><?= htmlspecialchars($user['nama']) ?></div>
                <div class="text-muted"><?= htmlspecialchars($user['username']) ?></div>
                <span class="badge bg-<?= $user['role']=='admin'?'danger':'info' ?>"><?= ucfirst($user['role']) ?></span>
            </div>
        </div>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-semibold">Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($user['nama']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Username</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" disabled>
            </div>
            <hr>
            <p class="text-muted small">Kosongkan jika tidak ingin mengubah password</p>
            <div class="mb-3">
                <label class="form-label fw-semibold">Password Baru</label>
                <input type="password" name="password" class="form-control" minlength="6">
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Konfirmasi Password</label>
                <input type="password" name="password_confirm" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-save me-2"></i>Simpan Perubahan
            </button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
