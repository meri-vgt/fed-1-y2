<?php
/**
 * Database configuration for Criminal Minds blog platform
 */

// Database configuration - Update these values to match your PHPMyAdmin setup
define('DB_HOST', 'localhost');
define('DB_NAME', 'criminal_minds');
define('DB_USER', 'root');
define('DB_PASS', '');

// SQLite database file path (fallback if MySQL is not available)
define('SQLITE_DB_PATH', __DIR__ . '/../database/criminal_minds.sqlite');

// Flag to indicate if database is available
$databaseAvailable = null;

// Create database connection
function getDatabaseConnection() {
    static $connection = null;
    global $databaseAvailable;
    
    // If we've already determined database is not available, return false
    if ($databaseAvailable === false) {
        return false;
    }
    
    if ($connection === null) {
        // Try PDO first
        if (extension_loaded('pdo')) {
            // Check if pdo_mysql is available
            if (extension_loaded('pdo_mysql')) {
                try {
                    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
                    $options = [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ];
                    
                    $connection = new PDO($dsn, DB_USER, DB_PASS, $options);
                    $databaseAvailable = true;
                    return $connection;
                } catch (PDOException $e) {
                    // PDO failed, try MySQLi
                    error_log('PDO Connection failed: ' . $e->getMessage());
                }
            } else {
                // pdo_mysql extension is not loaded
                error_log('PDO MySQL driver not available');
            }
        }
        
        // Try MySQLi
        if (extension_loaded('mysqli')) {
            try {
                $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                
                if ($connection->connect_error) {
                    throw new Exception('Connection failed: ' . $connection->connect_error);
                }
                
                $connection->set_charset('utf8mb4');
                $databaseAvailable = true;
                return $connection;
            } catch (Exception $e) {
                // MySQLi failed too
                error_log('MySQLi Connection failed: ' . $e->getMessage());
            }
        } else {
            error_log('MySQLi extension not available');
        }
        
        // Try SQLite as a last resort
        if (true) { // Always try SQLite
            try {
                // Create the database directory if it doesn't exist
                $dbDir = dirname(SQLITE_DB_PATH);
                if (!is_dir($dbDir)) {
                    mkdir($dbDir, 0755, true);
                }
                
                $connection = new PDO('sqlite:' . SQLITE_DB_PATH);
                $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                
                // Create the posts table if it doesn't exist
                $connection->exec('CREATE TABLE IF NOT EXISTS posts (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    title TEXT NOT NULL,
                    content TEXT NOT NULL,
                    status TEXT NOT NULL DEFAULT "draft",
                    author TEXT NOT NULL DEFAULT "Onbekend",
                    date TEXT NOT NULL,
                    created_at TEXT NOT NULL,
                    updated_at TEXT DEFAULT NULL,
                    featured INTEGER NOT NULL DEFAULT 0
                )');
                
                $databaseAvailable = true;
                return $connection;
            } catch (PDOException $e) {
                error_log('SQLite Connection failed: ' . $e->getMessage());
            }
        } else {
            error_log('PDO SQLite driver not available');
        }
        
        // If all database options failed, set database as unavailable
        $databaseAvailable = false;
        return false;
    }
    
    return $connection;
}

// Check if database is available
function isDatabaseAvailable() {
    global $databaseAvailable;
    
    // If we haven't checked yet, try to connect
    if ($databaseAvailable === null) {
        getDatabaseConnection();
    }
    
    return $databaseAvailable === true;
}

// Initialize database (create tables if they don't exist)
function initializeDatabase() {
    // This function is now handled by the SQL file import
    // You should import the criminal_minds.sql file into PHPMyAdmin
    return true;
}
?>