<?php
/**
 * School Management ERP - Login Page
 */

require_once 'config.php';
require_once 'functions.php';
require_once 'authentication.php';

// Check if already logged in
if (isLoggedIn()) {
    $role = $_SESSION['role'];
    if ($role === 'admin') {
        header('Location: admin/dashboard.php');
    } elseif ($role === 'teacher') {
        header('Location: teacher/dashboard.php');
    } elseif ($role === 'student') {
        header('Location: student/dashboard.php');
    } elseif ($role === 'parent') {
        header('Location: parent/dashboard.php');
    }
    exit();
}

$error = '';
$success = '';

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'admin';

    $auth = new Authentication($mysqli);
    $result = $auth->login($email, $password, $role);

    if ($result['success']) {
        $success = 'Login successful! Redirecting...';
        header('Refresh: 2; url=' . APP_URL . '/' . $role . '/dashboard.php');
    } else {
        $error = $result['message'];
    }
}

// Handle session expiration message
$session_expired = $_GET['session_expired'] ?? false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
        }

        body {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            width: 100%;
            max-width: 450px;
            padding: 20px;
        }

        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }

        .login-header h1 {
            font-size: 1.8rem;
            margin-bottom: 5px;
            font-weight: 700;
        }

        .login-header p {
            font-size: 0.95rem;
            opacity: 0.9;
        }

        .login-body {
            padding: 40px 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #374151;
            font-size: 0.95rem;
        }

        .form-control {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
            outline: none;
        }

        .form-control::placeholder {
            color: #9ca3af;
        }

        .role-select {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        .role-option {
            position: relative;
        }

        .role-option input[type="radio"] {
            display: none;
        }

        .role-option label {
            display: block;
            padding: 12px;
            text-align: center;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 0;
            font-weight: 600;
        }

        .role-option input[type="radio"]:checked + label {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .role-option label:hover {
            border-color: var(--primary-color);
        }

        .btn-login {
            width: 100%;
            background-color: var(--primary-color);
            border: none;
            color: white;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn-login:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(37, 99, 235, 0.3);
            color: white;
        }

        .login-links {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 0.9rem;
        }

        .login-links a {
            color: var(--primary-color);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .login-links a:hover {
            text-decoration: underline;
        }

        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
            border: none;
        }

        .alert-danger {
            background-color: #fee;
            color: #c33;
        }

        .alert-success {
            background-color: #efe;
            color: #3c3;
        }

        .alert-warning {
            background-color: #ffe;
            color: #cc3;
        }

        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .remember-me input[type="checkbox"] {
            margin-right: 8px;
            cursor: pointer;
        }

        .remember-me label {
            margin-bottom: 0;
            cursor: pointer;
            font-weight: normal;
        }

        .demo-credentials {
            background-color: #f0f9ff;
            border-left: 4px solid var(--primary-color);
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            font-size: 0.9rem;
        }

        .demo-credentials h6 {
            margin-bottom: 10px;
            font-weight: 600;
            color: #374151;
        }

        .demo-credentials p {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <!-- Header -->
            <div class="login-header">
                <h1>
                    <i class="fas fa-graduation-cap"></i> <?php echo APP_NAME; ?>
                </h1>
                <p>Sign In to Your Account</p>
            </div>

            <!-- Login Form -->
            <div class="login-body">
                <!-- Session Expired Alert -->
                <?php if ($session_expired): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> Your session has expired. Please login again.
                </div>
                <?php endif; ?>

                <!-- Error Message -->
                <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-times-circle"></i> <?php echo escape($error); ?>
                </div>
                <?php endif; ?>

                <!-- Success Message -->
                <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo escape($success); ?>
                </div>
                <?php endif; ?>

                <form method="POST" action="" id="loginForm">
                    <!-- Role Selection -->
                    <label style="display: block; margin-bottom: 15px; font-weight: 600; color: #374151;">Select Your Role:</label>
                    <div class="role-select">
                        <div class="role-option">
                            <input type="radio" id="admin" name="role" value="admin" checked>
                            <label for="admin">
                                <i class="fas fa-crown"></i> Admin
                            </label>
                        </div>
                        <div class="role-option">
                            <input type="radio" id="teacher" name="role" value="teacher">
                            <label for="teacher">
                                <i class="fas fa-chalkboard-user"></i> Teacher
                            </label>
                        </div>
                        <div class="role-option">
                            <input type="radio" id="student" name="role" value="student">
                            <label for="student">
                                <i class="fas fa-book"></i> Student
                            </label>
                        </div>
                        <div class="role-option">
                            <input type="radio" id="parent" name="role" value="parent">
                            <label for="parent">
                                <i class="fas fa-users"></i> Parent
                            </label>
                        </div>
                    </div>

                    <!-- Email Field -->
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input 
                            type="email" 
                            class="form-control" 
                            id="email" 
                            name="email" 
                            placeholder="Enter your email" 
                            required
                        >
                    </div>

                    <!-- Password Field -->
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input 
                            type="password" 
                            class="form-control" 
                            id="password" 
                            name="password" 
                            placeholder="Enter your password" 
                            required
                        >
                    </div>

                    <!-- Remember Me -->
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember" value="1">
                        <label for="remember">Remember me</label>
                    </div>

                    <!-- Login Button -->
                    <button type="submit" name="login" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i> Sign In
                    </button>
                </form>

                <!-- Links -->
                <div class="login-links">
                    <a href="forgot-password.php">
                        <i class="fas fa-key"></i> Forgot Password?
                    </a>
                    <a href="index.php">
                        <i class="fas fa-home"></i> Back to Home
                    </a>
                </div>

                <!-- Demo Credentials -->
                <div class="demo-credentials">
                    <h6><i class="fas fa-info-circle"></i> Demo Credentials:</h6>
                    <p><strong>Admin:</strong> admin@school.com / admin@123</p>
                    <p><strong>Teacher:</strong> teacher@school.com / pass@123</p>
                    <p><strong>Student:</strong> student@school.com / pass@123</p>
                    <p><strong>Parent:</strong> parent@school.com / pass@123</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validate form before submission
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();

            if (!email || !password) {
                e.preventDefault();
                alert('Please enter both email and password');
                return false;
            }

            if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                e.preventDefault();
                alert('Please enter a valid email address');
                return false;
            }
        });

        // Auto-hide alerts after 5 seconds
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.display = 'none';
            }, 5000);
        });
    </script>
</body>
</html>