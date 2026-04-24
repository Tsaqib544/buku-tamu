<?php
require_once __DIR__ . '/../config/config.php';
requireLogin();
if (!isAdmin()) { header('Location: ' . BASE_URL . '/pages/dashboard.php'); exit(); }

$conn = getConnection();
$id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;
$isEdit = $id > 0;
$errors = [];
$data = ['nama'=>'','username'=>'','role'=>'petugas'];

if ($isEdit) {
    $stmt = $conn->prepare("SELECT id,nama,username,role FROM users WHERE id=?");
    $stmt->bind_param('i',$id);
    $stmt->execute();
    $fetched = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$fetched) { header('Location: '.BASE_URL.'/pages/users.php'); exit(); }
    $data = $fetched;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['nama']     = trim($_POST['nama'] ?? '');
    $data['username'] = trim($_POST['username'] ?? '');
    $data['role']     = $_POST['role'] ?? 'petugas';
    $password         = trim($_POST['password'] ?? '');
    $passwordConf     = trim($_POST['password_confirm'] ?? '');

    if (!$data['nama'])     $errors[] = 'Nama wajib diisi.';
    if (!$data['username']) $errors[] = 'Username wajib diisi.';
    if (!$isEdit && !$password) $errors[] = 'Password wajib diisi untuk pengguna baru.';
    if ($password && $password !== $passwordConf) $errors[] = 'Konfirmasi password tidak cocok.';
    if ($password && strlen($password) < 6) $errors[] = 'Password minimal 6 karakter.';

    if (empty($errors)) {
        if ($isEdit) {
            if ($password) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET nama=?,username=?,role=?,password=? WHERE id=?");
                $stmt->bind_param('ssssi',$data['nama'],$data['username'],$data['role'],$hash,$id);
            } else {
                $stmt = $conn->prepare("UPDATE users SET nama=?,username=?,role=? WHERE id=?");
                $stmt->bind_param('sssi',$data['nama'],$data['username'],$data['role'],$id);
            }
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (nama,username,password,role) VALUES (?,?,?,?)");
            $stmt->bind_param('ssss',$data['nama'],$data['username'],$hash,$data['role']);
        }
        if ($stmt->execute()) {
            $conn->close();
            header('Location: ' . BASE_URL . '/pages/users.php');
            exit();
        } else {
            $errors[] = 'Gagal menyimpan: ' . $stmt->error;
        }
        $stmt->close();
    }
}
$conn->close();
$pageTitle = $isEdit ? 'Edit Pengguna' : 'Tambah Pengguna';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h4><i class="bi bi-person-plus me-2 text-primary"></i><?= $isEdit ? 'Edit Pengguna' : 'Tambah Pengguna' ?></h4>
    <a href="<?= BASE_URL ?>/pages/users.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
</div>

<?php if ($errors): ?>
<div class="alert alert-danger"><ul class="mb-0"><?php foreach($errors as $e): ?><li><?= $e ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<div class="card form-card" style="max-width:560px">
    <div class="card-body p-4">
        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($data['nama']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($data['username']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Role</label>
                <select name="role" class="form-select">
                    <option value="petugas" <?= $data['role']=='petugas'?'selected':'' ?>>Petugas</option>
                    <option value="admin"   <?= $data['role']=='admin'?'selected':'' ?>>Admin</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Password <?= $isEdit ? '<small class="text-muted fw-normal">(kosongkan jika tidak diubah)</small>' : '<span class="text-danger">*</span>' ?></label>
                <input type="password" name="password" class="form-control" <?= !$isEdit ? 'required' : '' ?> minlength="6">
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Konfirmasi Password</label>
                <input type="password" name="password_confirm" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-save me-2"></i><?= $isEdit ? 'Simpan Perubahan' : 'Tambah Pengguna' ?>
            </button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
