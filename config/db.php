<?php
/**
 * Database Configuration & Connection (PDO)
 * Online Voting System
 * Supports MySQL Server / XAMPP with seamless SQLite zero-config fallback
 */

// MySQL Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Set your MySQL password here if configured
define('DB_NAME', 'online_voting_db');
define('DB_CHARSET', 'utf8mb4');

// Application Settings
define('APP_NAME', 'CampusVote | Secure Online Voting System');
define('VOTE_SALT', 'c4mpu5_v0t3_s3cur1ty_s4lt_2026');

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 86400,
        'path' => '/',
        'domain' => '',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

/**
 * Returns a singleton PDO database connection instance.
 * @return PDO
 */
function getDBConnection() {
    static $pdo = null;

    if ($pdo === null) {
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        // 1. Try connecting to MySQL
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            return $pdo;
        } catch (PDOException $e) {
            // Check if root connection can auto-create the database
            try {
                $rootDsn = "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET;
                $rootPdo = new PDO($rootDsn, DB_USER, DB_PASS, $options);
                $sqlPath = __DIR__ . '/../database.sql';
                if (file_exists($sqlPath)) {
                    $sql = file_get_contents($sqlPath);
                    $rootPdo->exec($sql);
                    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
                    return $pdo;
                }
            } catch (Exception $mysqlEx) {
                // MySQL requires credentials or is offline
            }
        }

        // 2. Seamless Fallback: SQLite
        // Allows immediate testing out-of-the-box without waiting for MySQL setup
        try {
            $sqlitePath = __DIR__ . '/../database.sqlite';
            $pdo = new PDO("sqlite:" . $sqlitePath, null, null, $options);
            $GLOBALS['USING_SQLITE_FALLBACK'] = true;
            return $pdo;
        } catch (Exception $sqliteEx) {
            die("Database Initialization Error: " . htmlspecialchars($sqliteEx->getMessage()));
        }
    }

    return $pdo;
}
