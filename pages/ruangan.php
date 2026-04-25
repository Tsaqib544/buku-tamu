<?php
require_once __DIR__ . '/../config/config.php';
requireLogin();

$conn = getConnection();
$msg = ''; $msgType = 'success';

// Handle DELETE
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM ruangan WHERE id = ?");
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        $msg = 'Data ruangan berhasil dihapus.';
    } else {
        $msg = 'Gagal menghapus data ruangan.'; $msgType = 'danger';
    }
    $stmt->close();
}

// Notifikasi dari redirect
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'created') $msg = 'Ruangan baru berhasil ditambahkan.';
    if ($_GET['msg'] === 'updated') $msg = 'Data ruangan berhasil diperbarui.';
}

// Search & Filter
$search       = trim($_GET['q'] ?? '');
$filterStatus = $_GET['status'] ?? '';

$where = []; $params = []; $types = '';

if ($search) {
    $like = '%' . $search . '%';
    $where[] = "(kode_ruangan LIKE ? OR nama_ruangan LIKE ? OR lokasi LIKE ?)";
    $params  = array_merge($params, [$like, $like, $like]);
    $types  .= 'sss';
}
if ($filterStatus) {
    $where[] = "status = ?";
    $params[] = $filterStatus;
    $types   .= 's';
}

$sql = "SELECT * FROM ruangan";
if ($where) $sql .= " WHERE " . implode(' AND ', $where);
$sql .= " ORDER BY kode_ruangan ASC";

$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Hitung statistik
$statTersedia   = $conn->query("SELECT COUNT(*) as c FROM ruangan WHERE status='tersedia'")->fetch_assoc()['c'];
$statDigunakan  = $conn->query("SELECT COUNT(*) as c FROM ruangan WHERE status='digunakan'")->fetch_assoc()['c'];
$statMaintenance= $conn->query("SELECT COUNT(*) as c FROM ruangan WHERE status='maintenance'")->fetch_assoc()['c'];
$totalRuangan   = $conn->query("SELECT COUNT(*) as c FROM ruangan")->fetch_assoc()['c'];

$conn->close();
$pageTitle = 'Data Ruangan';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h4><i class="bi bi-door-open me-2 text-primary"></i>Data Ruangan</h4>
    <?php if (isAdmin()): ?>
    <a href="<?= BASE_URL ?>/pages/ruangan_form.php" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Tambah Ruangan
    </a>
    <?php endif; ?>
</div>

<?php if ($msg): ?>
<div class="alert alert-<?= $msgType ?> alert-dismissible fade show alert-auto-dismiss">
    <i class="bi bi-<?= $msgType=='success'?'check-circle':'exclamation-triangle' ?>-fill me-2"></i><?= $msg ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Statistik -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-building"></i>
                </div>
                <div>
                    <div class="fs-3 fw-bold text-primary"><?= $totalRuangan ?></div>
                    <div class="text-muted" style="font-size:.82rem">Total Ruangan</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div>
                    <div class="fs-3 fw-bold text-success"><?= $statTersedia ?></div>
                    <div class="text-muted" style="font-size:.82rem">Tersedia</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-door-closed"></i>
                </div>
                <div>
                    <div class="fs-3 fw-bold text-warning"><?= $statDigunakan ?></div>
                    <div class="text-muted" style="font-size:.82rem">Digunakan</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-tools"></i>
                </div>
                <div>
                    <div class="fs-3 fw-bold text-danger"><?= $statMaintenance ?></div>
                    <div class="text-muted" style="font-size:.82rem">Maintenance</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="card form-card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-5">
                <input type="text" name="q" class="form-control form-control-sm"
                       placeholder="Cari kode, nama ruangan, lokasi..."
                       value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="tersedia"    <?= $filterStatus=='tersedia'?'selected':'' ?>>Tersedia</option>
                    <option value="digunakan"   <?= $filterStatus=='digunakan'?'selected':'' ?>>Digunakan</option>
                    <option value="maintenance" <?= $filterStatus=='maintenance'?'selected':'' ?>>Maintenance</option>
                </select>
            </div>
            <div class="col-md-auto">
                <button type="submit" class="btn btn-primary btn-sm px-3">
                    <i class="bi bi-search me-1"></i>Cari
                </button>
                <a href="<?= BASE_URL ?>/pages/ruangan.php" class="btn btn-outline-secondary btn-sm ms-1">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Tabel -->
<div class="card table-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Ruangan</th>
                        <th>Lokasi</th>
                        <th>Kapasitas</th>
                        <th>Fasilitas</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                        <?php if (isAdmin()): ?><th style="width:110px">Aksi</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars($row['kode_ruangan']) ?></span></td>
                        <td class="fw-semibold"><?= htmlspecialchars($row['nama_ruangan']) ?></td>
                        <td><i class="bi bi-geo-alt text-muted me-1"></i><?= htmlspecialchars($row['lokasi'] ?: '-') ?></td>
                        <td><i class="bi bi-people text-muted me-1"></i><?= $row['kapasitas'] ?> orang</td>
                        <td style="max-width:180px; font-size:.83rem" class="text-muted">
                            <?= htmlspecialchars($row['fasilitas'] ?: '-') ?>
                        </td>
                        <td>
                            <?php
                            $badge = ['tersedia'=>'success','digunakan'=>'warning','maintenance'=>'danger'];
                            $label = ['tersedia'=>'Tersedia','digunakan'=>'Digunakan','maintenance'=>'Maintenance'];
                            ?>
                            <span class="badge bg-<?= $badge[$row['status']] ?> badge-status">
                                <?= $label[$row['status']] ?>
                            </span>
                        </td>
                        <td class="text-muted" style="font-size:.83rem"><?= htmlspecialchars($row['keterangan'] ?: '-') ?></td>
                        <?php if (isAdmin()): ?>
                        <td>
                            <a href="<?= BASE_URL ?>/pages/ruangan_form.php?id=<?= $row['id'] ?>"
                               class="btn btn-sm btn-outline-warning" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="<?= BASE_URL ?>/pages/ruangan.php?delete=<?= $row['id'] ?>"
                               class="btn btn-sm btn-outline-danger ms-1" title="Hapus"
                               onclick="return confirm('Hapus ruangan <?= addslashes(htmlspecialchars($row['nama_ruangan'])) ?>?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endwhile; ?>
                    <?php if ($no === 1): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>Tidak ada data ruangan ditemukan
                    </td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
