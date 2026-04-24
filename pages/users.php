<?php
require_once __DIR__ . '/../config/config.php';
requireLogin();
if (!isAdmin()) { header('Location: ' . BASE_URL . '/pages/dashboard.php'); exit(); }

$conn = getConnection();
$msg = ''; $msgType = 'success';

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    if ($delId == $_SESSION['user_id']) {
        $msg = 'Tidak bisa menghapus akun sendiri.'; $msgType = 'danger';
    } else {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param('i', $delId);
        $stmt->execute() ? $msg = 'Pengguna berhasil dihapus.' : ($msg = 'Gagal menghapus.' and $msgType = 'danger');
        $stmt->close();
    }
}

$users = $conn->query("SELECT u.*, (SELECT COUNT(*) FROM tamu WHERE created_by = u.id) as total_input FROM users u ORDER BY u.created_at DESC");
$conn->close();
$pageTitle = 'Kelola Pengguna';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h4><i class="bi bi-person-gear me-2 text-primary"></i>Kelola Pengguna</h4>
    <a href="<?= BASE_URL ?>/pages/user_form.php" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Tambah Pengguna
    </a>
</div>

<?php if ($msg): ?>
<div class="alert alert-<?= $msgType ?> alert-dismissible fade show alert-auto-dismiss">
    <?= $msg ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card table-card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th><th>Nama</th><th>Username</th><th>Role</th>
                    <th>Total Input</th><th>Dibuat</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; while ($u = $users->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td class="fw-semibold"><?= htmlspecialchars($u['nama']) ?></td>
                    <td><code><?= htmlspecialchars($u['username']) ?></code></td>
                    <td><span class="badge bg-<?= $u['role']=='admin'?'danger':'info' ?>"><?= ucfirst($u['role']) ?></span></td>
                    <td><?= $u['total_input'] ?> tamu</td>
                    <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                    <td>
                        <a href="<?= BASE_URL ?>/pages/user_form.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-warning">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <?php if ($u['id'] != $_SESSION['user_id']): ?>
                        <a href="?delete=<?= $u['id'] ?>" class="btn btn-sm btn-outline-danger ms-1"
                           onclick="return confirm('Hapus pengguna <?= addslashes(htmlspecialchars($u['nama'])) ?>?')">
                            <i class="bi bi-trash"></i>
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
