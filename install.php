<?php
// Script instalasi database untuk PetCare POS & Hotel Management System
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>
<html lang='id'>
<head>
    <meta charset='UTF-8'>
    <title>Instalasi Database - PetCare</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f8fafc; color: #1e293b; padding: 40px 20px; }
        .card { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        h1 { color: #0284c7; margin-top: 0; font-size: 24px; }
        .log-item { padding: 8px 12px; margin-bottom: 8px; border-radius: 6px; font-size: 14px; }
        .success { background: #dcfce7; color: #166534; }
        .warning { background: #fef9c3; color: #854d0e; }
        .error { background: #fee2e2; color: #991b1b; }
        .btn { display: inline-block; background: #0284c7; color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: bold; margin-top: 15px; }
        .btn:hover { background: #0369a1; }
        ul { padding-left: 20px; font-size: 14px; }
    </style>
</head>
<body>
<div class='card'>";

echo "<h1>🐾 Instalasi Database - PetCare System</h1>";

// Konfigurasi database dinamis (Mendukung Railway & Laragon Lokal)
$db_url = getenv('MYSQL_PRIVATE_URL') ?: (getenv('MYSQL_URL') ?: (getenv('DATABASE_URL') ?: ($_ENV['MYSQL_PRIVATE_URL'] ?? ($_ENV['MYSQL_URL'] ?? ($_ENV['DATABASE_URL'] ?? null)))));


if ($db_url) {
    $parsed = parse_url($db_url);
    $host = $parsed['host'] ?? 'localhost';
    $port = $parsed['port'] ?? 3306;
    $username = $parsed['user'] ?? 'root';
    $password = $parsed['pass'] ?? '';
    $db_name = isset($parsed['path']) ? ltrim($parsed['path'], '/') : 'petcare_db';
} else {
    $host = getenv('MYSQLHOST') ?: ($_ENV['MYSQLHOST'] ?? (getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? 'localhost')));
    $port = getenv('MYSQLPORT') ?: ($_ENV['MYSQLPORT'] ?? (getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?? 3306)));
    $db_name = getenv('MYSQLDATABASE') ?: ($_ENV['MYSQLDATABASE'] ?? (getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? 'petcare_db')));
    $username = getenv('MYSQLUSER') ?: ($_ENV['MYSQLUSER'] ?? (getenv('DB_USER') ?: ($_ENV['DB_USER'] ?? 'root')));
    
    $pass_env = getenv('MYSQLPASSWORD');
    if ($pass_env === false && isset($_ENV['MYSQLPASSWORD'])) {
        $pass_env = $_ENV['MYSQLPASSWORD'];
    }
    if ($pass_env === false) {
        $pass_env = getenv('DB_PASS');
        if ($pass_env === false && isset($_ENV['DB_PASS'])) {
            $pass_env = $_ENV['DB_PASS'];
        }
    }
    $password = ($pass_env !== false && $pass_env !== null) ? $pass_env : '';
}

try {
    // Coba koneksi langsung ke target database terlebih dahulu (standar Cloud / Railway)
    $connected = false;
    try {
        $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db_name;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $connected = true;
        echo "<div class='log-item success'>✅ Terhubung ke MySQL Database ($db_name) di $host:$port!</div>";
    } catch (PDOException $e) {
        // Jika database belum ada (umum di Laragon baru), coba buat database
        $pdo = new PDO("mysql:host=$host;port=$port", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `$db_name`");
        $connected = true;
        echo "<div class='log-item success'>✅ Database '$db_name' dibuat dan siap digunakan!</div>";
    }
    
    // Baca schema SQL (prioritaskan railway_schema.sql jika ada)
    $schema_file = __DIR__ . '/database/railway_schema.sql';
    if (!file_exists($schema_file)) {
        $schema_file = __DIR__ . '/database/petcare_schema.sql';
    }
    if (!file_exists($schema_file)) {
        throw new Exception("File schema tidak ditemukan di: $schema_file");
    }
    
    $schema = file_get_contents($schema_file);
    
    // Split per statement dengan semi-colon
    $statements = array_filter(array_map('trim', explode(";\n", $schema)));
    
    foreach ($statements as $statement) {
        $stmt = trim($statement);
        if (!empty($stmt)) {
            // Hindari eksekusi CREATE DATABASE / USE jika sedang terhubung ke Railway database khusus
            if (stripos($stmt, 'CREATE DATABASE') === 0 || stripos($stmt, 'USE ') === 0) {
                continue;
            }
            try {
                $pdo->exec($stmt);
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'already exists') === false) {
                    echo "<div class='log-item warning'>⚠️ Warning: " . htmlspecialchars($e->getMessage()) . "</div>";
                }
            }
        }
    }
    
    echo "<div class='log-item success'>✅ Seluruh tabel dan data awal PetCare berhasil diimpor!</div>";
    
    // Validasi instalasi
    $test_pdo = new PDO("mysql:host=$host;port=$port;dbname=$db_name;charset=utf8mb4", $username, $password);
    $test_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $userCount = $test_pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $cageCount = $test_pdo->query("SELECT COUNT(*) FROM kandang")->fetchColumn();
    $itemCount = $test_pdo->query("SELECT COUNT(*) FROM barang")->fetchColumn();
    
    echo "<div class='log-item success'>
            📊 <strong>Data Terverifikasi:</strong><br>
            • User Staff: $userCount akun<br>
            • Fasilitas Kandang: $cageCount unit<br>
            • Produk / Jasa: $itemCount item
          </div>";
    
    echo "<h3>🎉 Instalasi Sukses!</h3>";
    echo "<p><strong>Akun Staf Internal (Password: <code>password</code>):</strong></p>";
    echo "<ul>
            <li><strong>Admin:</strong> <code>admin</code></li>
            <li><strong>Kasir:</strong> <code>kasir1</code></li>
            <li><strong>Groomer:</strong> <code>groomer1</code></li>
          </ul>";
    echo "<p><strong>Akun Portal Mandiri Pelanggan (Password: <code>password</code>):</strong></p>";
    echo "<ul>
            <li><strong>Pelanggan 1:</strong> <code>081234567890</code> (Ahmad Fauzi)</li>
            <li><strong>Pelanggan 2:</strong> <code>085678901234</code> (Jessica Tan)</li>
          </ul>";
          
    echo "<div style='display: flex; gap: 10px; margin-top: 20px;'>
            <a href='login.php' class='btn'>Masuk ke PetCare POS (Staf)</a>
            <a href='portal/index.php' class='btn' style='background: #10b981;'>Buka Portal Pelanggan</a>
          </div>";
    
} catch (Exception $e) {
    echo "<div class='log-item error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<p><strong>Troubleshooting:</strong></p>";
    echo "<ul>
            <li>Pastikan Laragon MySQL service sudah dijalankan (Status: Started)</li>
            <li>Periksa user dan password database di Laragon (default: root / tanpa password)</li>
          </ul>";
}

echo "</div></body></html>";
?>
