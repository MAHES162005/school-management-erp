<?php
/**
 * School Management ERP - Helper Functions
 * 
 * @version 1.0.0
 * @author School ERP Team
 */

require_once 'config.php';

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if user has specific role
 * @param string $role
 * @return bool
 */
function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

/**
 * Redirect to login if not authenticated
 * @param string $role Optional role to check
 */
function requireLogin($role = null) {
    if (!isLoggedIn()) {
        header('Location: ' . APP_URL . '/login.php');
        exit();
    }

    if ($role && !hasRole($role)) {
        header('Location: ' . APP_URL . '/unauthorized.php');
        exit();
    }
}

/**
 * Hash password
 * @param string $password
 * @return string
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_ARGON2ID, HASH_OPTIONS);
}

/**
 * Verify password
 * @param string $password
 * @param string $hash
 * @return bool
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Generate CSRF Token
 * @return string
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF Token
 * @param string $token
 * @return bool
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Escape output to prevent XSS
 * @param string $text
 * @return string
 */
function escape($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Get current user info
 * @return array|null
 */
function getCurrentUser() {
    global $mysqli;
    
    if (!isLoggedIn()) {
        return null;
    }

    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];

    $table = 'users';
    if ($role === 'student') {
        $table = 'students';
    } elseif ($role === 'teacher') {
        $table = 'teachers';
    } elseif ($role === 'parent') {
        $table = 'parents';
    }

    $stmt = $mysqli->prepare("SELECT * FROM $table WHERE id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}

/**
 * Format date to readable format
 * @param string $date
 * @param string $format
 * @return string
 */
function formatDate($date, $format = 'F d, Y') {
    return date($format, strtotime($date));
}

/**
 * Get file size in human readable format
 * @param int $bytes
 * @return string
 */
function formatBytes($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));

    return round($bytes, 2) . ' ' . $units[$pow];
}

/**
 * Upload file safely
 * @param array $file $_FILES array
 * @param string $directory Upload directory
 * @return array ['success' => bool, 'filename' => string, 'error' => string]
 */
function uploadFile($file, $directory = 'uploads/') {
    // Validate file
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return ['success' => false, 'error' => 'No file provided'];
    }

    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'error' => 'File size exceeds maximum limit'];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_EXTENSIONS)) {
        return ['success' => false, 'error' => 'File type not allowed'];
    }

    // Generate unique filename
    $filename = uniqid() . '_' . time() . '.' . $ext;
    $filepath = $directory . $filename;

    // Create directory if not exists
    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
    }

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'filename' => $filename, 'path' => $filepath];
    } else {
        return ['success' => false, 'error' => 'Failed to upload file'];
    }
}

/**
 * Send JSON response
 * @param bool $success
 * @param string $message
 * @param array $data
 * @param int $statusCode
 */
function sendJSON($success, $message, $data = [], $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit();
}

/**
 * Log user activity
 * @param string $action
 * @param string $details
 */
function logActivity($action, $details = '') {
    global $mysqli;

    if (!isLoggedIn()) {
        return;
    }

    $user_id = $_SESSION['user_id'];
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = substr($_SERVER['HTTP_USER_AGENT'], 0, 255);
    $timestamp = date('Y-m-d H:i:s');

    try {
        $stmt = $mysqli->prepare(
            "INSERT INTO activity_logs (user_id, action, details, ip_address, user_agent, created_at) 
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param('isssss', $user_id, $action, $details, $ip_address, $user_agent, $timestamp);
        $stmt->execute();
        $stmt->close();
    } catch (Exception $e) {
        error_log('Activity logging failed: ' . $e->getMessage());
    }
}

/**
 * Send email notification
 * @param string $to Email address
 * @param string $subject Email subject
 * @param string $body Email body (HTML)
 * @return bool
 */
function sendEmail($to, $subject, $body) {
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= 'From: ' . MAIL_FROM_NAME . ' <' . MAIL_FROM . ">\r\n";

    try {
        return mail($to, $subject, $body, $headers);
    } catch (Exception $e) {
        error_log('Email sending failed: ' . $e->getMessage());
        return false;
    }
}

/**
 * Sanitize input
 * @param string $input
 * @return string
 */
function sanitizeInput($input) {
    return trim(stripslashes(htmlspecialchars($input, ENT_QUOTES, 'UTF-8')));
}

/**
 * Get attendance percentage
 * @param int $student_id
 * @param string $month Optional month filter
 * @return float
 */
function getAttendancePercentage($student_id, $month = null) {
    global $mysqli;

    $query = "SELECT 
        (SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) * 100.0 / COUNT(*)) as percentage
        FROM attendance 
        WHERE student_id = ?";
    
    $params = [$student_id];
    $types = 'i';

    if ($month) {
        $query .= " AND MONTH(attendance_date) = MONTH(?) AND YEAR(attendance_date) = YEAR(?)";
        $params[] = $month;
        $params[] = date('Y-m-d');
        $types .= 'ss';
    }

    $stmt = $mysqli->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    return round($result['percentage'] ?? 0, 2);
}

/**
 * Check session timeout
 */
function checkSessionTimeout() {
    if (isset($_SESSION['last_activity'])) {
        if ((time() - $_SESSION['last_activity']) > SESSION_TIMEOUT) {
            session_unset();
            session_destroy();
            header('Location: ' . APP_URL . '/login.php?session_expired=1');
            exit();
        }
    }
    $_SESSION['last_activity'] = time();
}

/**
 * Generate pagination
 * @param int $total Total records
 * @param int $page Current page
 * @param int $perPage Records per page
 * @return array
 */
function getPagination($total, $page = 1, $perPage = ITEMS_PER_PAGE) {
    $totalPages = ceil($total / $perPage);
    $offset = ($page - 1) * $perPage;

    return [
        'total' => $total,
        'page' => $page,
        'perPage' => $perPage,
        'totalPages' => $totalPages,
        'offset' => $offset,
        'hasNext' => $page < $totalPages,
        'hasPrev' => $page > 1
    ];
}

/**
 * Get student current enrollment
 * @param int $student_id
 * @return array|null
 */
function getStudentEnrollment($student_id) {
    global $mysqli;
    
    $query = "SELECT se.*, c.class_name, s.section_name 
              FROM student_enrollments se
              JOIN classes c ON se.class_id = c.id
              JOIN sections s ON se.section_id = s.id
              WHERE se.student_id = ? AND se.status = 'enrolled'
              LIMIT 1";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}

/**
 * Get class total students
 * @param int $class_id
 * @return int
 */
function getClassTotalStudents($class_id) {
    global $mysqli;
    
    $stmt = $mysqli->prepare("SELECT COUNT(*) as total FROM student_enrollments WHERE class_id = ? AND status = 'enrolled'");
    $stmt->bind_param('i', $class_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    return $result['total'] ?? 0;
}

/**
 * Get class present students for a date
 * @param int $class_id
 * @param string $date
 * @return int
 */
function getClassPresentStudents($class_id, $date) {
    global $mysqli;
    
    $stmt = $mysqli->prepare(
        "SELECT COUNT(*) as total FROM attendance a
         JOIN student_enrollments se ON a.student_id = se.student_id
         WHERE se.class_id = ? AND a.attendance_date = ? AND a.status = 'Present'"
    );
    $stmt->bind_param('is', $class_id, $date);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    return $result['total'] ?? 0;
}

?>