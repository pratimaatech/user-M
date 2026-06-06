<?php

// ── Database credentials ─────────────────────────────────────
define('DB_HOST',    'localhost');
define('DB_NAME',    'user_management');
define('DB_USER',    'root');       
define('DB_PASS',    '');          
define('DB_CHARSET', 'utf8mb4');
// ─────────────────────────────────────────────────────────────

class Database
{
    private static ?Database $instance = null;
    private PDO $connection;


    private function __construct()
    {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_NAME,
            DB_CHARSET
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,   // Throw on error
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,         // Arrays by default
            PDO::ATTR_EMULATE_PREPARES   => false,                     // Real prepared stmts
            PDO::MYSQL_ATTR_FOUND_ROWS   => true,                      // Useful for UPDATE
        ];

        try {
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Log the real error server-side; show a generic message to the user
            error_log('[DB] Connection failed: ' . $e->getMessage());
            throw new RuntimeException('Database connection failed. Please try again later.👀');
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self(); 
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }

    private function __clone() {}
}
