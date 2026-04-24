<?php
require_once __DIR__ . '/../config/config.php';
requireLogin();

$conn = getConnection();
$id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;
$isEdit = $id > 0;
$errors = [];
$data = [
    'nama_tamu'          => '',
    'instansi'           => '',
    'keperluan'          => '',
    'yang_dituju'        => '',
    'nomor_hp'           => '',
    'tanggal_kunjungan'  => date('Y-m-d'),
    'jam_masuk'          => date('H:i'),
    'jam_keluar'         => '',
    'status'             => 'menunggu',
    'keterangan'         => '',
];

if ($isEdit) {
    $stmt = $conn->prepare("SELECT * FROM tamu WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $fetched = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$fetched) {
        header('Location: ' . BASE_URL . '/pages/tamu.php');
        exit();
    }
    $data = array_merge($data, $fetched);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nama_tamu'         => trim($_POST['nama_tamu'] ?? ''),
        'instansi'          => trim($_POST['instansi'] ?? ''),
        'keperluan'         => trim($_POST['keperluan'] ?? ''),
        'yang_dituju'       => trim($_POST['yang_dituju'] ?? ''),
        'nomor_hp'          => trim($_POST['nomor_hp'] ?? ''),
        'tanggal_kunjungan' => $_POST['tanggal_kunjungan'] ?? '',
        'jam_masuk'         => $_POST['jam_masuk'] ?? '',
        'jam_keluar'        => $_POST['jam_keluar'] ?? '',
        'status'            => $_POST['status'] ?? 'menunggu',
        'keterangan'        => trim($_POST['keterangan'] ?? ''),
    ];

    if (!$data['nama_tamu'])         $errors[] = 'Nama tamu wajib diisi.';
    if (!$data['keperluan'])         $errors[] = 'Keperluan wajib diisi.';
    if (!$data['yang_dituju'])       $errors[] = 'Yang dituju wajib diisi.';
    if (!$data['tanggal_kunjungan']) $errors[] = 'Tanggal kunjungan wajib diisi.';
    if (!$data['jam_masuk'])         $errors[] = 'Jam masuk wajib diisi.';

    if (empty($errors)) {
        $jamKeluar = $data['jam_keluar'] ?: null;
        if ($isEdit) {
            $stmt = $conn->prepare("UPDATE tamu SET nama_tamu=?, instansi=?, keperluan=?, yang_dituju=?, nomor_hp=?, tanggal_kunjungan=?, jam_masuk=?, jam_keluar=?, status=?, keterangan=? WHERE id=?");
            $stmt->bind_param('ssssssssssi', $data['nama_tamu'], $data['instansi'], $data['keperluan'], $data['yang_dituju'], $data['nomor_hp'], $data['tanggal_kunjungan'], $data['jam_masuk'], $jamKeluar, $data['status'], $data['keterangan'], $id);
        } else {
            $userId = $_SESSION['user_id'];
            $stmt = $conn->prepare("INSERT INTO tamu (nama_tamu, instansi, keperluan, yang_dituju, nomor_hp, tanggal_kunjungan, jam_masuk, jam_keluar, status, keterangan, created_by) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param('ssssssssssi', $data['nama_tamu'], $data['instansi'], $data['keperluan'], $data['yang_dituju'], $data['nomor_hp'], $data['tanggal_kunjungan'], $data['jam_masuk'], $jamKeluar, $data['status'], $data['keterangan'], $userId);
        }
        if ($stmt->execute()) {
            $conn->close();
            header('Location: ' . BASE_URL . '/pages/tamu.php?msg=' . ($isEdit ? 'updated' : 'created'));
            exit();
        } else {
            $errors[] = 'Gagal menyimpan data: ' . $stmt->error;
        }
        $stmt->close();
    }
}

$conn->close();
$pageTitle = $isEdit ? 'Edit Tamu' : 'Tambah Tamu';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h4>
        <i class="bi bi-<?= $isEdit ? 'pencil' : 'plus-circle' ?> me-2 text-primary"></i>
        <?= $isEdit ? 'Edit Data Tamu' : 'Tambah Tamu Baru' ?>
    </h4>
    <a href="<?= BASE_URL ?>/pages/tamu.php" class="btn btn-outline-secondary btn-sm">
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

<div class="card form-card">
    <div class="card-body p-4">
        <form method="POST">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nama Tamu <span class="text-danger">*</span></label>
                    <input type="text" name="nama_tamu" class="form-control"
                           value="<?= htmlspecialchars($data['nama_tamu']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Instansi / Asal</label>
                    <input type="text" name="instansi" class="form-control"
                           value="<?= htmlspecialchars($data['instansi']) ?>"
                           placeholder="Nama perusahaan/instansi (opsional)">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nomor HP</label>
                    <input type="text" name="nomor_hp" class="form-control"
                           value="<?= htmlspecialchars($data['nomor_hp']) ?>"
                           placeholder="08xxxxxxxxxx">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Yang Dituju <span class="text-danger">*</span></label>
                    <input type="text" name="yang_dituju" class="form-control"
                           value="<?= htmlspecialchars($data['yang_dituju']) ?>" required>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Keperluan <span class="text-danger">*</span></label>
                    <textarea name="keperluan" class="form-control" rows="3" required><?= htmlspecialchars($data['keperluan']) ?></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Tanggal Kunjungan <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_kunjungan" class="form-control"
                           value="<?= htmlspecialchars($data['tanggal_kunjungan']) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Jam Masuk <span class="text-danger">*</span></label>
                    <input type="time" name="jam_masuk" class="form-control"
                           value="<?= htmlspecialchars(substr($data['jam_masuk'], 0, 5)) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Jam Keluar</label>
                    <input type="time" name="jam_keluar" class="form-control"
                           value="<?= htmlspecialchars(substr($data['jam_keluar'] ?? '', 0, 5)) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select">
                        <option value="menunggu" <?= $data['status']=='menunggu'?'selected':'' ?>>Menunggu</option>
                        <option value="hadir"    <?= $data['status']=='hadir'?'selected':'' ?>>Hadir</option>
                        <option value="selesai"  <?= $data['status']=='selesai'?'selected':'' ?>>Selesai</option>
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Keterangan Tambahan</label>
                    <input type="text" name="keterangan" class="form-control"
                           value="<?= htmlspecialchars($data['keterangan']) ?>"
                           placeholder="Catatan tambahan (opsional)">
                </div>
            </div>
            <hr class="my-4">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-2"></i><?= $isEdit ? 'Simpan Perubahan' : 'Tambah Tamu' ?>
                </button>
                <a href="<?= BASE_URL ?>/pages/tamu.php" class="btn btn-outline-secondary px-4">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
