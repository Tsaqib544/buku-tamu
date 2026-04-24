<?php
require_once __DIR__ . '/../config/config.php';
requireLogin();

$conn = getConnection();
$msg = '';
$msgType = 'success';

// Handle DELETE
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM tamu WHERE id = ?");
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        $msg = 'Data tamu berhasil dihapus.';
    } else {
        $msg = 'Gagal menghapus data.'; $msgType = 'danger';
    }
    $stmt->close();
}

// Search & Filter
$search = trim($_GET['q'] ?? '');
$filterStatus = $_GET['status'] ?? '';
$filterDate   = $_GET['date'] ?? '';

$where = [];
$params = [];
$types  = '';

if ($search) {
    $like = '%' . $search . '%';
    $where[] = "(nama_tamu LIKE ? OR instansi LIKE ? OR yang_dituju LIKE ?)";
    $params = array_merge($params, [$like, $like, $like]);
    $types .= 'sss';
}
if ($filterStatus) {
    $where[] = "status = ?";
    $params[] = $filterStatus;
    $types .= 's';
}
if ($filterDate) {
    $where[] = "tanggal_kunjungan = ?";
    $params[] = $filterDate;
    $types .= 's';
}

$sql = "SELECT t.*, u.nama as petugas FROM tamu t LEFT JOIN users u ON t.created_by = u.id";
if ($where) $sql .= " WHERE " . implode(' AND ', $where);
$sql .= " ORDER BY t.tanggal_kunjungan DESC, t.jam_masuk DESC";

$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$conn->close();

$pageTitle = 'Data Tamu';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h4><i class="bi bi-people me-2 text-primary"></i>Data Tamu</h4>
    <a href="<?= BASE_URL ?>/pages/tamu_form.php" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Tambah Tamu
    </a>
</div>

<?php if ($msg): ?>
<div class="alert alert-<?= $msgType ?> alert-dismissible fade show alert-auto-dismiss">
    <i class="bi bi-<?= $msgType=='success'?'check-circle':'exclamation-triangle' ?>-fill me-2"></i><?= $msg ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Filter Form -->
<div class="card form-card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <input type="text" name="q" class="form-control form-control-sm"
                       placeholder="Cari nama, instansi, yang dituju..."
                       value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="hadir" <?= $filterStatus=='hadir'?'selected':'' ?>>Hadir</option>
                    <option value="menunggu" <?= $filterStatus=='menunggu'?'selected':'' ?>>Menunggu</option>
                    <option value="selesai" <?= $filterStatus=='selesai'?'selected':'' ?>>Selesai</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="date" class="form-control form-control-sm"
                       value="<?= htmlspecialchars($filterDate) ?>">
            </div>
            <div class="col-md-auto">
                <button type="submit" class="btn btn-primary btn-sm px-3">
                    <i class="bi bi-search me-1"></i>Cari
                </button>
                <a href="<?= BASE_URL ?>/pages/tamu.php" class="btn btn-outline-secondary btn-sm ms-1">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card table-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Nama Tamu</th>
                        <th>Instansi</th>
                        <th>Keperluan</th>
                        <th>Yang Dituju</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Status</th>
                        <th style="width:120px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="text-muted"><?= $no++ ?></td>
                        <td>
                            <div class="fw-semibold"><?= htmlspecialchars($row['nama_tamu']) ?></div>
                            <?php if ($row['nomor_hp']): ?>
                            <small class="text-muted"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($row['nomor_hp']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($row['instansi'] ?: '-') ?></td>
                        <td style="max-width:180px">
                            <span title="<?= htmlspecialchars($row['keperluan']) ?>">
                                <?= htmlspecialchars(substr($row['keperluan'], 0, 45)) . (strlen($row['keperluan']) > 45 ? '...' : '') ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($row['yang_dituju']) ?></td>
                        <td><?= date('d/m/Y', strtotime($row['tanggal_kunjungan'])) ?></td>
                        <td style="font-size:.82rem">
                            <?= substr($row['jam_masuk'], 0, 5) ?>
                            <?php if ($row['jam_keluar']): ?>
                                <br><small class="text-muted">→ <?= substr($row['jam_keluar'], 0, 5) ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $badge = ['hadir'=>'success','menunggu'=>'warning','selesai'=>'secondary'];
                            $label = ['hadir'=>'Hadir','menunggu'=>'Menunggu','selesai'=>'Selesai'];
                            ?>
                            <span class="badge bg-<?= $badge[$row['status']] ?> badge-status">
                                <?= $label[$row['status']] ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= BASE_URL ?>/pages/tamu_form.php?id=<?= $row['id'] ?>"
                               class="btn btn-sm btn-outline-warning" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="<?= BASE_URL ?>/pages/tamu.php?delete=<?= $row['id'] ?>"
                               class="btn btn-sm btn-outline-danger ms-1" title="Hapus"
                               onclick="return confirm('Hapus data tamu <?= addslashes(htmlspecialchars($row['nama_tamu'])) ?>?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if ($no === 1): ?>
                    <tr><td colspan="9" class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>Tidak ada data tamu ditemukan
                    </td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
