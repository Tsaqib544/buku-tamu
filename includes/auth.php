<?php
// -------------------------------------------------------
// includes/auth.php — Fungsi Autentikasi & Session
// -------------------------------------------------------

require_once __DIR__ . '/config.php';

// Mulai session dengan konfigurasi aman
function startSecureSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path'     => '/',
            'secure'   => false,   // Ubah ke true jika pakai HTTPS
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
        session_start();
    }
}

// Cek apakah user sudah login
function isLoggedIn(): bool {
    startSecureSession();
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Paksa redirect ke login jika belum login
function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/auth/login.php?msg=unauthorized');
        exit;
    }
    // Validasi session timeout
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_LIFETIME)) {
        logoutUser();
        header('Location: ' . BASE_URL . '/auth/login.php?msg=timeout');
        exit;
    }
    $_SESSION['last_activity'] = time();
}

// Paksa redirect ke dashboard jika sudah login
function requireGuest(): void {
    if (isLoggedIn()) {
        header('Location: ' . BASE_URL . '/pages/dashboard.php');
        exit;
    }
}

// Proses login
function loginUser(string $username, string $password): array {
    $pdo  = getDB();
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'Username atau password salah.'];
    }

    startSecureSession();
    session_regenerate_id(true); // Cegah session fixation

    $_SESSION['user_id']       = $user['id'];
    $_SESSION['user_nama']     = $user['nama'];
    $_SESSION['user_username'] = $user['username'];
    $_SESSION['user_role']     = $user['role'];
    $_SESSION['last_activity'] = time();

    return ['success' => true];
}

// Proses logout
function logoutUser(): void {
    startSecureSession();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    session_destroy();
}

// Ambil data user aktif
function currentUser(): array {
    startSecureSession();
    return [
        'id'       => $_SESSION['user_id']       ?? null,
        'nama'     => $_SESSION['user_nama']      ?? '',
        'username' => $_SESSION['user_username']  ?? '',
        'role'     => $_SESSION['user_role']      ?? '',
    ];
}

// Escape output HTML
function e(mixed $val): string {
    return htmlspecialchars((string)($val ?? ''), ENT_QUOTES, 'UTF-8');
}

// Flash message
function setFlash(string $type, string $msg): void {
    startSecureSession();
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function getFlash(): ?array {
    startSecureSession();
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}
