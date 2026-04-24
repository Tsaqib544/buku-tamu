<?php
// auth/login.php — Halaman Login
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

define('BASE_URL', ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/buku-tamu');

requireGuest(); // Redirect ke dashboard jika sudah login

$error = '';

// Pesan URL parameter
$msgMap = [
    'unauthorized' => 'Silakan login terlebih dahulu.',
    'timeout' => 'Sesi Anda telah berakhir. Silakan login kembali.',
    'logout' => 'Anda telah berhasil logout.',
];
$urlMsg = $_GET['msg'] ?? '';
$infoMsg = $msgMap[$urlMsg] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF sederhana: cek token form
    if (!isset($_POST['_token']) || $_POST['_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        $error = 'Permintaan tidak valid. Silakan coba lagi.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $error = 'Username dan password wajib diisi.';
        } else {
            $result = loginUser($username, $password);
            if ($result['success']) {
                header('Location: ' . BASE_URL . '/pages/dashboard.php');
                exit;
            } else {
                $error = $result['message'];
            }
        }
    }
}

// Buat CSRF token
startSecureSession();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #1e3a5f 0%, #1a56db 60%, #3b82f6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: #fff;
            border-radius: 1.25rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .25);
            padding: 2.5rem 2rem;
        }

        .brand-icon {
            width: 60px;
            height: 60px;
            border-radius: 1rem;
            background: linear-gradient(135deg, #1e3a5f, #1a56db);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }

        .form-control:focus {
            border-color: #1a56db;
            box-shadow: 0 0 0 .2rem rgba(26, 86, 219, .2);
        }

        .btn-login {
            background: linear-gradient(135deg, #1e3a5f, #1a56db);
            border: none;
            color: #fff;
            font-weight: 600;
            padding: .65rem;
            transition: opacity .2s;
        }

        .btn-login:hover {
            opacity: .9;
            color: #fff;
        }

        .input-group-text {
            background: #f8fafc;
            border-color: #dee2e6;
        }

        /* .demo-box {
            background: #f1f5f9;
            border-radius: .5rem;
            font-size: .82rem;
            padding: .75rem 1rem;
        } */
    </style>
</head>

<body>
    <div class="login-card">
        <div class="text-center mb-4">
            <div class="brand-icon">
                <i class="bi bi-journal-bookmark-fill fs-2 text-white"></i>
            </div>
            <h4 class="fw-bold"><?= APP_NAME ?></h4>
            <p class="text-muted small mb-0">Masuk ke sistem manajemen tamu</p>
        </div>

        <?php if ($infoMsg): ?>
            <div class="alert alert-info py-2 small"><i class="bi bi-info-circle me-1"></i><?= e($infoMsg) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger py-2 small"><i class="bi bi-x-circle me-1"></i><?= e($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="_token" value="<?= e($_SESSION['csrf_token']) ?>">

            <div class="mb-3">
                <label class="form-label fw-semibold small">Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" name="username" class="form-control" placeholder="Masukkan username"
                        value="<?= e($_POST['username'] ?? '') ?>" required autofocus>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold small">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" id="passwordInput" class="form-control"
                        placeholder="Masukkan password" required>
                    <button type="button" class="input-group-text btn" id="togglePass"
                        style="cursor:pointer;border-left:0;">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-login w-100 rounded-pill">
                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
            </button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('togglePass').addEventListener('click', function () {
            const inp = document.getElementById('passwordInput');
            const ico = document.getElementById('eyeIcon');
            if (inp.type === 'password') {
                inp.type = 'text'; ico.className = 'bi bi-eye-slash';
            } else {
                inp.type = 'password'; ico.className = 'bi bi-eye';
            }
        });
    </script>
</body>

</html>