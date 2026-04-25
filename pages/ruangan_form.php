<?php
require_once __DIR__ . '/../config/config.php';
requireLogin();
if (!isAdmin()) {
    header('Location: ' . BASE_URL . '/pages/ruangan.php');
    exit();
}

$conn = getConnection();
$id     = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;
$isEdit = $id > 0;
$errors = [];
$data   = [
    'kode_ruangan' => '',
    'nama_ruangan' => '',
    'lokasi'       => '',
    'kapasitas'    => 1,
    'fasilitas'    => '',
    'status'       => 'tersedia',
    'keterangan'   => '',
];

if ($isEdit) {
    $stmt = $conn->prepare("SELECT * FROM ruangan WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $fetched = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$fetched) {
        header('Location: ' . BASE_URL . '/pages/ruangan.php');
        exit();
    }
    $data = array_merge($data, $fetched);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'kode_ruangan' => strtoupper(trim($_POST['kode_ruangan'] ?? '')),
        'nama_ruangan' => trim($_POST['nama_ruangan'] ?? ''),
        'lokasi'       => trim($_POST['lokasi'] ?? ''),
        'kapasitas'    => (int)($_POST['kapasitas'] ?? 1),
        'fasilitas'    => trim($_POST['fasilitas'] ?? ''),
        'status'       => $_POST['status'] ?? 'tersedia',
        'keterangan'   => trim($_POST['keterangan'] ?? ''),
    ];

    if (!$data['kode_ruangan']) $errors[] = 'Kode ruangan wajib diisi.';
    if (!$data['nama_ruangan']) $errors[] = 'Nama ruangan wajib diisi.';
    if ($data['kapasitas'] < 1) $errors[] = 'Kapasitas minimal 1 orang.';

    if (empty($errors)) {
        if ($isEdit) {
            $stmt = $conn->prepare("UPDATE ruangan SET kode_ruangan=?, nama_ruangan=?, lokasi=?, kapasitas=?, fasilitas=?, status=?, keterangan=? WHERE id=?");
            $stmt->bind_param('sssisssi',
                $data['kode_ruangan'], $data['nama_ruangan'], $data['lokasi'],
                $data['kapasitas'], $data['fasilitas'], $data['status'],
                $data['keterangan'], $id
            );
        } else {
            $stmt = $conn->prepare("INSERT INTO ruangan (kode_ruangan, nama_ruangan, lokasi, kapasitas, fasilitas, status, keterangan) VALUES (?,?,?,?,?,?,?)");
            $stmt->bind_param('sssisss',
                $data['kode_ruangan'], $data['nama_ruangan'], $data['lokasi'],
                $data['kapasitas'], $data['fasilitas'], $data['status'],
                $data['keterangan']
            );
        }

        if ($stmt->execute()) {
            $conn->close();
            header('Location: ' . BASE_URL . '/pages/ruangan.php?msg=' . ($isEdit ? 'updated' : 'created'));
            exit();
        } else {
            // Cek duplikat kode
            if ($conn->errno == 1062) {
                $errors[] = 'Kode ruangan sudah digunakan, gunakan kode lain.';
            } else {
                $errors[] = 'Gagal menyimpan: ' . $stmt->error;
            }
        }
        $stmt->close();
    }
}

$conn->close();
$pageTitle = $isEdit ? 'Edit Ruangan' : 'Tambah Ruangan';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h4>
        <i class="bi bi-<?= $isEdit ? 'pencil' : 'plus-circle' ?> me-2 text-primary"></i>
        <?= $isEdit ? 'Edit Data Ruangan' : 'Tambah Ruangan Baru' ?>
    </h4>
    <a href="<?= BASE_URL ?>/pages/ruangan.php" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<?php if ($errors): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    <strong>Terdapat kesalahan:</strong>
    <ul class="mb-0 mt-1">
        <?php foreach ($errors as $e): ?><li><?= $e ?></li><?php endforeach; ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card form-card" style="max-width:620px">
    <div class="card-body p-4">
        <form method="POST">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Kode Ruangan <span class="text-danger">*</span></label>
                    <input type="text" name="kode_ruangan" class="form-control text-uppercase"
                           value="<?= htmlspecialchars($data['kode_ruangan']) ?>"
                           placeholder="Contoh: R-001" maxlength="20" required>
                    <div class="form-text">Harus unik, maks 20 karakter</div>
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Nama Ruangan <span class="text-danger">*</span></label>
                    <input type="text" name="nama_ruangan" class="form-control"
                           value="<?= htmlspecialchars($data['nama_ruangan']) ?>"
                           placeholder="Contoh: Ruang Rapat Utama" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Lokasi / Lantai</label>
                    <input type="text" name="lokasi" class="form-control"
                           value="<?= htmlspecialchars($data['lokasi']) ?>"
                           placeholder="Contoh: Lantai 2, Gedung A">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Kapasitas <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" name="kapasitas" class="form-control"
                               value="<?= (int)$data['kapasitas'] ?>" min="1" required>
                        <span class="input-group-text">orang</span>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Fasilitas</label>
                    <input type="text" name="fasilitas" class="form-control"
                           value="<?= htmlspecialchars($data['fasilitas']) ?>"
                           placeholder="Contoh: AC, Proyektor, Whiteboard, TV">
                    <div class="form-text">Pisahkan dengan koma</div>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select">
                        <option value="tersedia"    <?= $data['status']=='tersedia'?'selected':'' ?>>Tersedia</option>
                        <option value="digunakan"   <?= $data['status']=='digunakan'?'selected':'' ?>>Digunakan</option>
                        <option value="maintenance" <?= $data['status']=='maintenance'?'selected':'' ?>>Maintenance</option>
                    </select>
                </div>
                <div class="col-md-7">
                    <label class="form-label fw-semibold">Keterangan</label>
                    <input type="text" name="keterangan" class="form-control"
                           value="<?= htmlspecialchars($data['keterangan']) ?>"
                           placeholder="Catatan tambahan (opsional)">
                </div>
            </div>

            <hr class="my-4">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-2"></i><?= $isEdit ? 'Simpan Perubahan' : 'Tambah Ruangan' ?>
                </button>
                <a href="<?= BASE_URL ?>/pages/ruangan.php" class="btn btn-outline-secondary px-4">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
