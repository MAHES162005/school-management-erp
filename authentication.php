<?php
/**
 * School Management ERP - Authentication & Session Handler
 * 
 * @version 1.0.0
 * @author School ERP Team
 */

require_once 'config.php';
require_once 'functions.php';

class Authentication {
    private $mysqli;
    private $maxAttempts = 5;
    private $lockoutDuration = 900; // 15 minutes

    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }

    /**
     * Authenticate user
     * @param string $email
     * @param string $password
     * @param string $role
     * @return array ['success' => bool, 'message' => string, 'user' => array]
     */
    public function login($email, $password, $role = 'admin') {
        // Validate input
        $email = sanitizeInput($email);
        $password = sanitizeInput($password);
        $role = sanitizeInput($role);

        // Check rate limiting
        $checkLockout = $this->checkLoginAttempts($email);
        if (!$checkLockout['allowed']) {
            return [
                'success' => false,
                'message' => 'Too many login attempts. Please try again after 15 minutes.'
            ];
        }

        // Determine table based on role
        $table = $this->getRoleTable($role);
        if (!$table) {
            return ['success' => false, 'message' => 'Invalid role'];
        }

        // Get user from database
        try {
            $stmt = $this->mysqli->prepare(
                "SELECT id, email, password, first_name, last_name, status FROM $table WHERE email = ? LIMIT 1"
            );
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();

            if (!$user) {
                $this->recordLoginAttempt($email, false);
                return ['success' => false, 'message' => 'Invalid email or password'];
            }

            // Check if account is active
            if ($user['status'] !== 'active') {
                return ['success' => false, 'message' => 'Your account has been deactivated'];
            }

            // Verify password
            if (!verifyPassword($password, $user['password'])) {
                $this->recordLoginAttempt($email, false);
                return ['success' => false, 'message' => 'Invalid email or password'];
            }

            // Successful login
            $this->recordLoginAttempt($email, true);
            
            // Regenerate session ID for security
            session_regenerate_id(true);

            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $role;
            $_SESSION['name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['login_time'] = time();
            $_SESSION['last_activity'] = time();

            // Log activity
            logActivity('LOGIN', 'User logged in successfully');

            return [
                'success' => true,
                'message' => 'Login successful',
                'user' => [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'name' => $user['first_name'] . ' ' . $user['last_name'],
                    'role' => $role
                ]
            ];

        } catch (Exception $e) {
            error_log('Login error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred. Please try again.'];
        }
    }

    /**
     * Logout user
     */
    public function logout() {
        logActivity('LOGOUT', 'User logged out');
        
        // Clear session
        $_SESSION = [];
        
        // Destroy session
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        
        session_destroy();
    }

    /**
     * Check login attempts for rate limiting
     * @param string $email
     * @return array
     */
    private function checkLoginAttempts($email) {
        try {
            $stmt = $this->mysqli->prepare(
                "SELECT attempts, last_attempt FROM login_attempts 
                 WHERE email = ? AND last_attempt > DATE_SUB(NOW(), INTERVAL 15 MINUTE) LIMIT 1"
            );
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            $stmt->close();

            if ($data && $data['attempts'] >= $this->maxAttempts) {
                return ['allowed' => false, 'attempts' => $data['attempts']];
            }

            return ['allowed' => true, 'attempts' => $data['attempts'] ?? 0];

        } catch (Exception $e) {
            error_log('Login attempts check error: ' . $e->getMessage());
            return ['allowed' => true, 'attempts' => 0];
        }
    }

    /**
     * Record login attempt
     * @param string $email
     * @param bool $success
     */
    private function recordLoginAttempt($email, $success = false) {
        if ($success) {
            // Clear failed attempts on successful login
            try {
                $stmt = $this->mysqli->prepare("DELETE FROM login_attempts WHERE email = ?");
                $stmt->bind_param('s', $email);
                $stmt->execute();
                $stmt->close();
            } catch (Exception $e) {
                error_log('Failed to clear login attempts: ' . $e->getMessage());
            }
        } else {
            // Record failed attempt
            try {
                $stmt = $this->mysqli->prepare(
                    "INSERT INTO login_attempts (email, attempts, last_attempt) 
                     VALUES (?, 1, NOW()) 
                     ON DUPLICATE KEY UPDATE attempts = attempts + 1, last_attempt = NOW()"
                );
                $stmt->bind_param('s', $email);
                $stmt->execute();
                $stmt->close();
            } catch (Exception $e) {
                error_log('Failed to record login attempt: ' . $e->getMessage());
            }
        }
    }

    /**
     * Get table name based on role
     * @param string $role
     * @return string|null
     */
    private function getRoleTable($role) {
        $tables = [
            'admin' => 'users',
            'teacher' => 'teachers',
            'student' => 'students',
            'parent' => 'parents'
        ];

        return $tables[$role] ?? null;
    }

    /**
     * Verify email and send reset link
     * @param string $email
     * @return array
     */
    public function requestPasswordReset($email) {
        $email = sanitizeInput($email);
        
        // Check if email exists in any table
        $roles = ['admin', 'teacher', 'student', 'parent'];
        $userFound = false;
        $role = null;

        foreach ($roles as $r) {
            $table = $this->getRoleTable($r);
            $stmt = $this->mysqli->prepare("SELECT id FROM $table WHERE email = ? LIMIT 1");
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $userFound = true;
                $role = $r;
                $stmt->close();
                break;
            }
            $stmt->close();
        }

        if (!$userFound) {
            return [
                'success' => false,
                'message' => 'Email not found in our system'
            ];
        }

        // Generate reset token
        $token = bin2hex(random_bytes(32));
        $token_hash = hash('sha256', $token);
        $expiry = date('Y-m-d H:i:s', time() + 3600); // 1 hour expiry

        try {
            $stmt = $this->mysqli->prepare(
                "INSERT INTO password_resets (email, token, role, expiry) 
                 VALUES (?, ?, ?, ?) 
                 ON DUPLICATE KEY UPDATE token = VALUES(token), expiry = VALUES(expiry)"
            );
            $stmt->bind_param('ssss', $email, $token_hash, $role, $expiry);
            $stmt->execute();
            $stmt->close();

            // Send email with reset link
            $resetLink = APP_URL . '/reset-password.php?token=' . $token . '&email=' . urlencode($email);
            $subject = APP_NAME . ' - Password Reset Request';
            $body = "<h2>Password Reset Request</h2>
                    <p>Click the link below to reset your password:</p>
                    <p><a href='$resetLink'>Reset Password</a></p>
                    <p>This link will expire in 1 hour.</p>
                    <p>If you didn't request this, please ignore this email.</p>";

            sendEmail($email, $subject, $body);

            return [
                'success' => true,
                'message' => 'Password reset link sent to your email'
            ];

        } catch (Exception $e) {
            error_log('Password reset error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred. Please try again.'
            ];
        }
    }

    /**
     * Reset password with token
     * @param string $token
     * @param string $email
     * @param string $newPassword
     * @return array
     */
    public function resetPassword($token, $email, $newPassword) {
        $email = sanitizeInput($email);
        $token_hash = hash('sha256', $token);

        try {
            // Verify token
            $stmt = $this->mysqli->prepare(
                "SELECT role FROM password_resets 
                 WHERE email = ? AND token = ? AND expiry > NOW() LIMIT 1"
            );
            $stmt->bind_param('ss', $email, $token_hash);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                return ['success' => false, 'message' => 'Invalid or expired reset link'];
            }

            $reset = $result->fetch_assoc();
            $role = $reset['role'];
            $stmt->close();

            // Validate password
            if (strlen($newPassword) < 8) {
                return ['success' => false, 'message' => 'Password must be at least 8 characters'];
            }

            // Hash new password
            $passwordHash = hashPassword($newPassword);
            $table = $this->getRoleTable($role);

            // Update password
            $stmt = $this->mysqli->prepare("UPDATE $table SET password = ? WHERE email = ?");
            $stmt->bind_param('ss', $passwordHash, $email);
            $stmt->execute();
            $stmt->close();

            // Delete reset token
            $stmt = $this->mysqli->prepare("DELETE FROM password_resets WHERE email = ?");
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->close();

            return ['success' => true, 'message' => 'Password reset successful. Please login.'];

        } catch (Exception $e) {
            error_log('Password reset error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred. Please try again.'];
        }
    }
}

?>