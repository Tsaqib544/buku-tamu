<?php
require_once __DIR__ . '/config/config.php';

if (isLoggedIn()) {
    header('Location: ' . BASE_URL . '/pages/dashboard.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = 'Username dan password wajib diisi.';
    } else {
        $conn = getConnection();
        $stmt = $conn->prepare("SELECT id, nama, username, password, role FROM users WHERE username = ? LIMIT 1");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        $conn->close();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama']    = $user['nama'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']    = $user['role'];
            session_regenerate_id(true);
            header('Location: ' . BASE_URL . '/pages/dashboard.php');
            exit();
        } else {
            $error = 'Username atau password salah.';
        }
    }
}

$pageTitle = 'Login';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login &mdash; <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<div class="login-wrapper">
    <div class="w-100 px-3">
        <div class="login-card card mx-auto">
            <div class="card-body p-4 p-md-5">
                <div class="login-logo shadow">
                    <i class="bi bi-journal-bookmark-fill"></i>
                </div>
                <h4 class="text-center fw-bold mb-1"><?= APP_NAME ?></h4>
                <p class="text-center text-muted mb-4" style="font-size:0.9rem">Masuk ke sistem manajemen tamu</p>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= $error ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Username</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                            <input type="text" name="username" class="form-control"
                                   placeholder="Masukkan username"
                                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" id="passwordField" class="form-control"
                                   placeholder="Masukkan password" required>
                            <button class="btn btn-outline-secondary" type="button"
                                    onclick="togglePassword()">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/script.js"></script>
<script>
function togglePassword() {
    const f = document.getElementById('passwordField');
    const i = document.getElementById('eyeIcon');
    if (f.type === 'password') {
        f.type = 'text';
        i.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        f.type = 'password';
        i.classList.replace('bi-eye-slash', 'bi-eye');
    }
}
</script>
</body>
</html>
