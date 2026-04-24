<?php
// -------------------------------------------------------
// includes/config.php — Konfigurasi & Koneksi Database
// -------------------------------------------------------

define('DB_HOST', 'localhost');
define('DB_NAME', 'buku_tamu_digital');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHAR', 'utf8mb4');

define('APP_NAME', 'Buku Tamu Digital');
define('APP_VERSION', '1.0.0');

// Session config
define('SESSION_LIFETIME', 3600); // 1 jam

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHAR;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die('<div style="font-family:sans-serif;padding:2rem;color:red;">
                <strong>Koneksi database gagal:</strong> ' . htmlspecialchars($e->getMessage()) . '
                <br><small>Pastikan MySQL berjalan dan konfigurasi di includes/config.php sudah benar.</small>
                </div>');
        }
    }
    return $pdo;
}
