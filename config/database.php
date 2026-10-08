<?php
class Database {
    private $host;
    private $port;
    private $db_name;
    private $username;
    private $password;
    private $conn;

    public function __construct() {
        $get = function($key) {
            $val = getenv($key);
            if ($val !== false && $val !== null && $val !== '') return $val;
            if (isset($_ENV[$key]) && $_ENV[$key] !== '') return $_ENV[$key];
            if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') return $_SERVER[$key];
            return null;
        };

        // 1. Utamakan MYSQL_PRIVATE_URL (Jaringan internal Railway, latensi < 1ms)
        $db_url = $get('MYSQL_PRIVATE_URL') ?: ($get('MYSQL_URL') ?: $get('DATABASE_URL'));
        
        if ($db_url) {
            $parsed = parse_url($db_url);
            $this->host = $parsed['host'] ?? '127.0.0.1';
            $this->port = $parsed['port'] ?? 3306;
            $this->username = $parsed['user'] ?? 'root';
            $this->password = $parsed['pass'] ?? '';
            $this->db_name = isset($parsed['path']) ? ltrim($parsed['path'], '/') : 'petcare_db';
        } else {
            // 2. Individual environment variables
            $this->host = $get('MYSQLHOST') ?: ($get('DB_HOST') ?: '127.0.0.1');
            $this->port = $get('MYSQLPORT') ?: ($get('DB_PORT') ?: 3306);
            $this->db_name = $get('MYSQLDATABASE') ?: ($get('DB_NAME') ?: 'petcare_db');
            $this->username = $get('MYSQLUSER') ?: ($get('DB_USER') ?: 'root');
            
            $pass = $get('MYSQLPASSWORD');
            if ($pass === null) {
                $pass = $get('DB_PASS');
            }
            $this->password = ($pass !== null) ? $pass : '';
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
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_PERSISTENT => true, // Menggunakan persistent connection agar tidak handshake ulang di tiap request
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                )
            );
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        
        return $this->conn;
    }
}
