<?php
require_once __DIR__ . '/../config/config.php';
requireLogin();

$conn = getConnection();

$totalTamu   = $conn->query("SELECT COUNT(*) as c FROM tamu")->fetch_assoc()['c'];
$tamuHariIni = $conn->query("SELECT COUNT(*) as c FROM tamu WHERE tanggal_kunjungan = CURDATE()")->fetch_assoc()['c'];
$sedangHadir = $conn->query("SELECT COUNT(*) as c FROM tamu WHERE status = 'hadir'")->fetch_assoc()['c'];
$menunggu    = $conn->query("SELECT COUNT(*) as c FROM tamu WHERE status = 'menunggu'")->fetch_assoc()['c'];

$recentTamu      = $conn->query("SELECT t.*, u.nama as petugas FROM tamu t LEFT JOIN users u ON t.created_by = u.id ORDER BY t.created_at DESC LIMIT 5");
$ruanganTersedia = $conn->query("SELECT COUNT(*) as c FROM ruangan WHERE status='tersedia'")->fetch_assoc()['c'];
$totalRuangan    = $conn->query("SELECT COUNT(*) as c FROM ruangan")->fetch_assoc()['c'];

$conn->close();
$pageTitle = 'Dashboard';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div>
        <h4><i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard</h4>
        <small class="text-muted">Selamat datang, <?= htmlspecialchars($_SESSION['nama']) ?>!</small>
    </div>
    <small class="text-muted"><?= date('l, d F Y') ?></small>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <div class="fs-3 fw-bold text-primary"><?= $totalTamu ?></div>
                    <div class="text-muted" style="font-size:.82rem">Total Tamu</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div>
                    <div class="fs-3 fw-bold text-success"><?= $tamuHariIni ?></div>
                    <div class="text-muted" style="font-size:.82rem">Tamu Hari Ini</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-person-check"></i>
                </div>
                <div>
                    <div class="fs-3 fw-bold text-info"><?= $sedangHadir ?></div>
                    <div class="text-muted" style="font-size:.82rem">Sedang Hadir</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div>
                    <div class="fs-3 fw-bold text-warning"><?= $menunggu ?></div>
                    <div class="text-muted" style="font-size:.82rem">Menunggu</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ruangan Info -->
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-secondary bg-opacity-10 text-secondary">
                        <i class="bi bi-building"></i>
                    </div>
                    <div>
                        <div class="fw-bold">Ruangan &mdash; <span class="text-success"><?= $ruanganTersedia ?> tersedia</span> dari <?= $totalRuangan ?> total ruangan</div>
                        <div class="text-muted" style="font-size:.82rem">Klik untuk melihat detail ketersediaan ruangan</div>
                    </div>
                </div>
                <a href="<?= BASE_URL ?>/pages/ruangan.php" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-door-open me-1"></i>Lihat Ruangan
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Recent Table -->
<div class="card table-card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-clock-history me-2 text-primary"></i>Tamu Terbaru</h6>
        <a href="<?= BASE_URL ?>/pages/tamu.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Nama Tamu</th>
                        <th>Instansi</th>
                        <th>Keperluan</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($recentTamu->num_rows > 0): ?>
                        <?php while ($row = $recentTamu->fetch_assoc()): ?>
                        <tr>
                            <td class="fw-semibold"><?= htmlspecialchars($row['nama_tamu']) ?></td>
                            <td class="text-muted"><?= htmlspecialchars($row['instansi'] ?: '-') ?></td>
                            <td><?= htmlspecialchars(substr($row['keperluan'], 0, 40)) ?>...</td>
                            <td><?= date('d/m/Y', strtotime($row['tanggal_kunjungan'])) ?></td>
                            <td>
                                <?php
                                $badge = ['hadir'=>'success','menunggu'=>'warning','selesai'=>'secondary'];
                                $label = ['hadir'=>'Hadir','menunggu'=>'Menunggu','selesai'=>'Selesai'];
                                ?>
                                <span class="badge bg-<?= $badge[$row['status']] ?>">
                                    <?= $label[$row['status']] ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada data tamu</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
