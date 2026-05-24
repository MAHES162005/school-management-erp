<?php
/**
 * School Management ERP - Homepage
 */

require_once 'config.php';
require_once 'functions.php';

if (isLoggedIn()) {
    // Redirect to appropriate dashboard based on role
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Modern School Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --success-color: #059669;
            --danger-color: #dc2626;
            --warning-color: #d97706;
            --dark-bg: #111827;
            --light-bg: #f9fafb;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #ffffff;
            color: #374151;
            line-height: 1.6;
        }

        /* Navigation */
        nav {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: white !important;
        }

        .nav-link {
            color: rgba(255,255,255,0.9) !important;
            transition: all 0.3s ease;
            margin-left: 10px;
        }

        .nav-link:hover {
            color: white !important;
            transform: translateY(-2px);
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 100px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            animation: slideInDown 0.8s ease;
        }

        .hero p {
            font-size: 1.3rem;
            margin-bottom: 30px;
            animation: slideInUp 0.8s ease;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            font-weight: 600;
            padding: 12px 30px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(37, 99, 235, 0.3);
        }

        /* Features Section */
        .features {
            padding: 80px 0;
            background-color: var(--light-bg);
        }

        .feature-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 30px;
            border-top: 4px solid var(--primary-color);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .feature-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .feature-card h3 {
            font-size: 1.3rem;
            margin-bottom: 10px;
            color: #111827;
        }

        /* Image Slider */
        .slider {
            position: relative;
            height: 400px;
            background-color: #000;
            border-radius: 10px;
            overflow: hidden;
            margin: 40px 0;
        }

        .slide {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.5s ease;
        }

        .slide.active {
            opacity: 1;
        }

        .slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .slide-controls {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10;
            display: flex;
            gap: 10px;
        }

        .slide-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: rgba(255,255,255,0.5);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .slide-dot.active {
            background-color: white;
            width: 30px;
            border-radius: 6px;
        }

        /* Announcements Section */
        .announcements {
            padding: 80px 0;
        }

        .announcement-item {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .announcement-item:nth-child(2) {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .announcement-item:nth-child(3) {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .announcement-item:hover {
            transform: translateX(5px);
        }

        /* Contact Section */
        .contact {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 80px 0;
            text-align: center;
        }

        .contact-item {
            margin: 20px 0;
        }

        .contact-item i {
            font-size: 2rem;
            margin-right: 10px;
        }

        /* Footer */
        footer {
            background-color: #111827;
            color: white;
            padding: 40px 0 20px;
            text-align: center;
        }

        /* Animations */
        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-20px);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2rem;
            }

            .hero p {
                font-size: 1rem;
            }

            .slider {
                height: 250px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-graduation-cap"></i> <?php echo APP_NAME; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#announcements">Announcements</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="container">
            <h1>Welcome to <?php echo APP_NAME; ?></h1>
            <p>Comprehensive Management System for Modern Educational Institutions</p>
            <a href="login.php" class="btn btn-light btn-lg">
                <i class="fas fa-sign-in-alt"></i> Get Started
            </a>
        </div>
    </section>

    <!-- Image Slider -->
    <section class="container mt-5">
        <div class="slider">
            <div class="slide active">
                <img src="https://via.placeholder.com/1200x400/2563eb/ffffff?text=School+Campus" alt="School Campus">
            </div>
            <div class="slide">
                <img src="https://via.placeholder.com/1200x400/059669/ffffff?text=Classroom" alt="Classroom">
            </div>
            <div class="slide">
                <img src="https://via.placeholder.com/1200x400/dc2626/ffffff?text=Library" alt="Library">
            </div>
            <div class="slide-controls">
                <span class="slide-dot active" onclick="currentSlide(0)"></span>
                <span class="slide-dot" onclick="currentSlide(1)"></span>
                <span class="slide-dot" onclick="currentSlide(2)"></span>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <h2 class="text-center mb-5" style="font-size: 2.5rem; font-weight: 700; color: #111827;">
                Why Choose Our ERP System?
            </h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3>Student Management</h3>
                        <p>Complete student information management from admission to graduation with document storage and tracking.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chalkboard-user"></i>
                        </div>
                        <h3>Teacher Portal</h3>
                        <p>Powerful tools for attendance marking, grade management, assignment creation, and student communication.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <h3>Analytics & Reports</h3>
                        <p>Generate comprehensive reports on attendance, performance, fees, and other institutional metrics.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <h3>Secure & Reliable</h3>
                        <p>Enterprise-grade security with encrypted data, role-based access control, and regular backups.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h3>Mobile Friendly</h3>
                        <p>Responsive design works seamlessly on all devices - desktop, tablet, and mobile phones.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <h3>Notifications</h3>
                        <p>Real-time notifications for announcements, events, assignments, and important updates.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Announcements Section -->
    <section class="announcements" id="announcements">
        <div class="container">
            <h2 class="text-center mb-5" style="font-size: 2.5rem; font-weight: 700; color: #111827;">
                Latest Announcements
            </h2>
            <div class="row">
                <div class="col-md-6">
                    <div class="announcement-item">
                        <h4><i class="fas fa-info-circle"></i> Summer Vacation</h4>
                        <p>Summer vacation starts from June 1st. School will reopen on July 15th. Have a great break!</p>
                        <small>Posted: May 25, 2024</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="announcement-item">
                        <h4><i class="fas fa-trophy"></i> Annual Sports Day</h4>
                        <p>Annual sports day scheduled for June 10th. All students are encouraged to participate!</p>
                        <small>Posted: May 20, 2024</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="announcement-item">
                        <h4><i class="fas fa-graduation-cap"></i> Final Exams Results</h4>
                        <p>Final exam results are now available. Please check your marks in the student portal.</p>
                        <small>Posted: May 18, 2024</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="announcement-item">
                        <h4><i class="fas fa-calendar"></i> Parent-Teacher Meeting</h4>
                        <p>PTM scheduled for May 30th. Parents are requested to meet their child's class teacher.</p>
                        <small>Posted: May 15, 2024</small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact" id="contact">
        <div class="container">
            <h2 class="mb-5" style="font-size: 2.5rem; font-weight: 700;">
                Get in Touch
            </h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <h4>Address</h4>
                            <p>123 School Street, Education City, ED 12345</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <div>
                            <h4>Phone</h4>
                            <p>+1 (555) 123-4567</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <h4>Email</h4>
                            <p>info@schoolerp.com</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2024 <?php echo APP_NAME; ?>. All rights reserved.</p>
            <p>
                <a href="#" style="color: #9ca3af; text-decoration: none;">Privacy Policy</a> | 
                <a href="#" style="color: #9ca3af; text-decoration: none;">Terms of Service</a> | 
                <a href="#" style="color: #9ca3af; text-decoration: none;">Contact Us</a>
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentSlideIndex = 0;

        function showSlides() {
            const slides = document.querySelectorAll('.slide');
            const dots = document.querySelectorAll('.slide-dot');

            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));

            slides[currentSlideIndex].classList.add('active');
            dots[currentSlideIndex].classList.add('active');
        }

        function currentSlide(index) {
            currentSlideIndex = index;
            showSlides();
        }

        // Auto-advance slides every 5 seconds
        setInterval(() => {
            currentSlideIndex = (currentSlideIndex + 1) % document.querySelectorAll('.slide').length;
            showSlides();
        }, 5000);

        showSlides();
    </script>
</body>
</html>