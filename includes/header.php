<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../config/config.php';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' : '' ?><?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>

<?php if (isLoggedIn()): ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>/pages/dashboard.php">
            <i class="bi bi-journal-bookmark-fill me-2"></i><?= APP_NAME ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>"
                       href="<?= BASE_URL ?>/pages/dashboard.php">
                        <i class="bi bi-speedometer2 me-1"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'tamu.php' ? 'active' : '' ?>"
                       href="<?= BASE_URL ?>/pages/tamu.php">
                        <i class="bi bi-people me-1"></i>Data Tamu
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'ruangan.php' ? 'active' : '' ?>"
                       href="<?= BASE_URL ?>/pages/ruangan.php">
                        <i class="bi bi-door-open me-1"></i>Ruangan
                    </a>
                </li>
                <?php if (isAdmin()): ?>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : '' ?>"
                       href="<?= BASE_URL ?>/pages/users.php">
                        <i class="bi bi-person-gear me-1"></i>Kelola Pengguna
                    </a>
                </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($_SESSION['nama'] ?? '') ?>
                        <span class="badge bg-light text-primary ms-1"><?= $_SESSION['role'] ?? '' ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/pages/profile.php">
                            <i class="bi bi-person me-2"></i>Profil Saya</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/pages/logout.php">
                            <i class="bi bi-box-arrow-right me-2"></i>Keluar</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<?php endif; ?>

<div class="<?= isLoggedIn() ? 'container-fluid py-4 px-4' : '' ?>">
