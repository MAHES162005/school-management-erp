<?php
/**
 * School Management ERP - Database Configuration
 * 
 * @version 1.0.0
 * @author School ERP Team
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'school_erp');
define('DB_PORT', 3306);

// Application Configuration
define('APP_NAME', 'School Management ERP');
define('APP_URL', 'http://localhost/school-management-erp');
define('APP_VERSION', '1.0.0');

// Security Configuration
define('SESSION_TIMEOUT', 1800); // 30 minutes
define('PASSWORD_HASH_ALGO', PASSWORD_ARGON2ID);
define('HASH_OPTIONS', ['memory_cost' => 1024, 'time_cost' => 2, 'threads' => 2]);

// Email Configuration
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'your-email@gmail.com');
define('MAIL_PASSWORD', 'your-app-password');
define('MAIL_FROM', 'noreply@schoolerp.com');
define('MAIL_FROM_NAME', 'School ERP');

// File Upload Configuration
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'xls', 'xlsx']);
define('UPLOAD_DIR', 'uploads/');

// Pagination
define('ITEMS_PER_PAGE', 15);

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors to users
ini_set('log_errors', 1);
ini_set('error_log', 'logs/error.log');

// Session Configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Set to 1 for HTTPS
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');

// Create logs directory if not exists
if (!is_dir('logs')) {
    @mkdir('logs', 0755, true);
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Establish Database Connection
try {
    $mysqli = new mysqli(
        DB_HOST,
        DB_USER,
        DB_PASSWORD,
        DB_NAME,
        DB_PORT
    );

    // Check connection
    if ($mysqli->connect_error) {
        throw new Exception('Database Connection Error: ' . $mysqli->connect_error);
    }

    // Set charset
    $mysqli->set_charset('utf8mb4');

    // Enable error reporting
    $mysqli->report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

} catch (Exception $e) {
    error_log('Database Connection Failed: ' . $e->getMessage());
    die('Database connection failed. Please try again later.');
}

?>