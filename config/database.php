<?php
class Database {
    private $host;
    private $port;
    private $db_name;
    private $username;
    private $password;
    private $conn;

    public function __construct() {
        // 1. Cek jika ada MYSQL_URL atau DATABASE_URL (Railway format URL)
        $db_url = getenv('MYSQL_URL') ?: (getenv('DATABASE_URL') ?: ($_ENV['MYSQL_URL'] ?? ($_ENV['DATABASE_URL'] ?? null)));
        
        if ($db_url) {
            $parsed = parse_url($db_url);
            $this->host = $parsed['host'] ?? 'localhost';
            $this->port = $parsed['port'] ?? 3306;
            $this->username = $parsed['user'] ?? 'root';
            $this->password = $parsed['pass'] ?? '';
            $this->db_name = isset($parsed['path']) ? ltrim($parsed['path'], '/') : 'petcare_db';
        } else {
            // 2. Cek individual variables dari Railway / Environment
            $this->host = getenv('MYSQLHOST') ?: ($_ENV['MYSQLHOST'] ?? (getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? 'localhost')));
            $this->port = getenv('MYSQLPORT') ?: ($_ENV['MYSQLPORT'] ?? (getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?? 3306)));
            $this->db_name = getenv('MYSQLDATABASE') ?: ($_ENV['MYSQLDATABASE'] ?? (getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? 'petcare_db')));
            $this->username = getenv('MYSQLUSER') ?: ($_ENV['MYSQLUSER'] ?? (getenv('DB_USER') ?: ($_ENV['DB_USER'] ?? 'root')));
            
            // Password bisa berupa string kosong pada instalasi Laragon standar
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
            $this->password = ($pass_env !== false && $pass_env !== null) ? $pass_env : '';
        }
    }

    public function getConnection() {
        $this->conn = null;
        
        try {
            $dsn = "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            $this->conn = new PDO(
                $dsn,
                $this->username,
                $this->password,
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                )
            );
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        
        return $this->conn;
    }
}

