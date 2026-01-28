<?php
// Database connection
$servername = "sql303.infinityfree.com";
$username = "if0_39529641";
$password = "nIzoiCglOv";
$dbname   = "if0_39529641_online_exam_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch dynamic statistics from database
$students_count = 0;
$faculty_count = 0;
$exams_count = 0;

// Count students
$student_query = "SELECT COUNT(*) as count FROM student";
$student_result = $conn->query($student_query);
if ($student_result && $student_result->num_rows > 0) {
    $row = $student_result->fetch_assoc();
    $students_count = $row['count'];
}

// Count faculty/staff
$faculty_query = "SELECT COUNT(*) as count FROM staff";
$faculty_result = $conn->query($faculty_query);
if ($faculty_result && $faculty_result->num_rows > 0) {
    $row = $faculty_result->fetch_assoc();
    $faculty_count = $row['count'];
}

// Count exams
$exam_query = "SELECT COUNT(*) as count FROM exams";
$exam_result = $conn->query($exam_query);
if ($exam_result && $exam_result->num_rows > 0) {
    $row = $exam_result->fetch_assoc();
    $exams_count = $row['count'];
}

// Close connection
$conn->close();

// Page variables
$page_title = "Online Examination System - Home";
$current_year = date('Y');

// Statistics array with dynamic data
$stats = [
    'students' => $students_count,
    'faculty' => $faculty_count,
    'exams' => $exams_count,
    'success_rate' => 99 // Keep this static as requested
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --warning-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Animated Background */
        .hero-section {
            background: var(--primary-gradient);
            min-height: 100vh;
            position: relative;
            display: flex;
            align-items: center;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><radialGradient id="a" cx="50%" cy="50%" r="50%"><stop offset="0%" style="stop-color:rgba(255,255,255,0.1)"/><stop offset="100%" style="stop-color:rgba(255,255,255,0)"/></radialGradient></defs><circle cx="200" cy="200" r="100" fill="url(%23a)"><animate attributeName="cx" values="200;800;200" dur="20s" repeatCount="indefinite"/></circle><circle cx="800" cy="600" r="150" fill="url(%23a)"><animate attributeName="cy" values="600;200;600" dur="25s" repeatCount="indefinite"/></circle><circle cx="500" cy="400" r="80" fill="url(%23a)"><animate attributeName="r" values="80;120;80" dur="15s" repeatCount="indefinite"/></circle></svg>') center/cover;
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        /* Navigation */
        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            background: rgba(255, 255, 255, 0.98) !important;
            box-shadow: 0 2px 30px rgba(0, 0, 0, 0.15);
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-link {
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-link:hover {
            color: #667eea !important;
            transform: translateY(-2px);
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            left: 50%;
            background: var(--primary-gradient);
            transition: all 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
            left: 0;
        }

        .btn-auth {
            background: var(--primary-gradient);
            border: none;
            color: white;
            padding: 10px 25px;
            border-radius: 25px;
            font-weight: bold;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-auth:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .btn-auth.outline {
            background: transparent;
            border: 2px solid #667eea;
            color: #667eea;
        }

        .btn-auth.outline:hover {
            background: var(--primary-gradient);
            color: white;
        }

        /* Hero Content */
        .hero-content {
            position: relative;
            z-index: 2;
            color: white;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
            animation: slideInFromLeft 1s ease-out;
        }

        .hero-subtitle {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            opacity: 0.9;
            animation: slideInFromLeft 1s ease-out 0.2s both;
        }

        .hero-buttons {
            animation: slideInFromLeft 1s ease-out 0.4s both;
        }

        .hero-buttons .btn {
            margin: 0 10px 10px 0;
            padding: 15px 30px;
            font-size: 1.1rem;
            border-radius: 30px;
            transition: all 0.3s ease;
        }

        .hero-image {
            animation: slideInFromRight 1s ease-out 0.3s both;
        }

        .hero-image img {
            max-width: 100%;
            height: auto;
            filter: drop-shadow(0 20px 40px rgba(0, 0, 0, 0.3));
        }

        /* Features Section */
        .features-section {
            padding: 100px 0;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }

        .feature-card {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            height: 100%;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-gradient);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        .feature-card:hover::before {
            transform: scaleX(1);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 25px;
            background: var(--primary-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            transition: all 0.3s ease;
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .feature-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
        }

        .feature-description {
            color: #666;
            line-height: 1.6;
        }

        /* Stats Section */
        .stats-section {
            background: var(--primary-gradient);
            padding: 80px 0;
            color: white;
        }

        .stat-item {
            text-align: center;
            padding: 20px;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            display: block;
            margin-bottom: 10px;
            counter-reset: number;
            animation: countUp 2s ease-out;
        }

        .stat-label {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        /* How It Works Section */
        .how-it-works {
            padding: 100px 0;
            background: white;
        }

        .step-card {
            text-align: center;
            padding: 30px 20px;
            position: relative;
        }

        .step-number {
            width: 60px;
            height: 60px;
            background: var(--primary-gradient);
            border-radius: 50%;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: bold;
            margin: 0 auto 25px;
            position: relative;
            z-index: 2;
        }

        .step-card::after {
            content: '';
            position: absolute;
            top: 30px;
            left: 100%;
            width: 100px;
            height: 2px;
            background: linear-gradient(to right, #667eea, transparent);
            z-index: 1;
        }

        .step-card:last-child::after {
            display: none;
        }

        .step-title {
            font-size: 1.3rem;
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
        }

        .step-description {
            color: #666;
            line-height: 1.6;
        }

        /* CTA Section */
        .cta-section {
            background: var(--secondary-gradient);
            padding: 100px 0;
            color: white;
            text-align: center;
        }

        .cta-title {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .cta-subtitle {
            font-size: 1.2rem;
            margin-bottom: 40px;
            opacity: 0.9;
        }

        /* Footer */
        .footer {
            background: #2c3e50;
            color: white;
            padding: 60px 0 30px;
        }

        .footer-section h5 {
            margin-bottom: 25px;
            font-weight: bold;
            color: #ecf0f1;
        }

        .footer-section ul {
            list-style: none;
            padding: 0;
        }

        .footer-section ul li {
            margin-bottom: 10px;
        }

        .footer-section ul li a {
            color: #bdc3c7;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-section ul li a:hover {
            color: #3498db;
        }

        .footer-section ul li i {
            margin-right: 8px;
        }

        .social-icons a {
            display: inline-block;
            width: 40px;
            height: 40px;
            background: #3498db;
            color: white;
            text-align: center;
            line-height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            transition: all 0.3s ease;
        }

        .social-icons a:hover {
            background: #2980b9;
            transform: translateY(-3px);
        }

        .footer-bottom {
            border-top: 1px solid #34495e;
            margin-top: 40px;
            padding-top: 20px;
            text-align: center;
            color: #95a5a6;
        }

        /* Animations */
        @keyframes slideInFromLeft {
            0% {
                transform: translateX(-100px);
                opacity: 0;
            }
            100% {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideInFromRight {
            0% {
                transform: translateX(100px);
                opacity: 0;
            }
            100% {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeInUp {
            0% {
                transform: translateY(50px);
                opacity: 0;
            }
            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }
            
            .step-card::after {
                display: none;
            }
            
            .stat-number {
                font-size: 2.5rem;
            }
        }

        /* Scroll Animations */
        .animate-on-scroll {
            opacity: 0;
            transform: translateY(50px);
            transition: all 0.6s ease;
        }

        .animate-on-scroll.animated {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#home">
                <i class="fas fa-graduation-cap me-2"></i>
                Online Examination System
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#how-it-works">How It Works</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.html">Contact</a>
                    </li>
                </ul>
                
                <div class="navbar-nav">
                    <a href="login.php" class="btn-auth outline me-2">Sign In</a>
                    <a href="signup.php" class="btn-auth">Sign Up</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <h1 class="hero-title">
                            Smart Online<br>
                            <span style="color: #ffd700;">Examination</span><br>
                            System
                        </h1>
                        <p class="hero-subtitle">
                            Experience the future of digital assessment with our secure, user-friendly, and comprehensive online examination platform.
                        </p>
                        <div class="hero-buttons">
                            <a href="signup.php" class="btn btn-light btn-lg">
                                <i class="fas fa-rocket me-2"></i>Get Started
                            </a>
                            <a href="#features" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-play me-2"></i>Learn More
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image text-center">
                        <svg width="500" height="400" viewBox="0 0 500 400" style="max-width: 100%; height: auto;">
                            <!-- Laptop/Computer -->
                            <rect x="50" y="150" width="300" height="200" rx="10" fill="#2c3e50" stroke="#34495e" stroke-width="2"/>
                            <rect x="70" y="170" width="260" height="150" rx="5" fill="#3498db"/>
                            <rect x="90" y="190" width="220" height="110" rx="3" fill="#ecf0f1"/>
                            
                            <!-- Screen Content -->
                            <rect x="110" y="210" width="180" height="8" rx="4" fill="#3498db"/>
                            <rect x="110" y="230" width="140" height="6" rx="3" fill="#95a5a6"/>
                            <rect x="110" y="245" width="160" height="6" rx="3" fill="#95a5a6"/>
                            <rect x="110" y="260" width="120" height="6" rx="3" fill="#95a5a6"/>
                            
                            <!-- Floating Elements -->
                            <circle cx="400" cy="100" r="30" fill="#e74c3c" opacity="0.8">
                                <animate attributeName="cy" values="100;80;100" dur="3s" repeatCount="indefinite"/>
                            </circle>
                            <circle cx="420" cy="300" r="25" fill="#f39c12" opacity="0.8">
                                <animate attributeName="cx" values="420;400;420" dur="4s" repeatCount="indefinite"/>
                            </circle>
                            <circle cx="380" cy="200" r="20" fill="#2ecc71" opacity="0.8">
                                <animate attributeName="r" values="20;25;20" dur="2s" repeatCount="indefinite"/>
                            </circle>
                            
                            <!-- Books -->
                            <rect x="20" y="300" width="15" height="50" fill="#e74c3c" transform="rotate(-10 27 325)"/>
                            <rect x="35" y="295" width="15" height="55" fill="#3498db" transform="rotate(-5 42 322)"/>
                            <rect x="50" y="298" width="15" height="52" fill="#2ecc71"/>
                            
                            <!-- Graduation Cap -->
                            <polygon points="450,50 470,40 490,50 490,60 470,70 450,60" fill="#2c3e50"/>
                            <rect x="468" y="40" width="4" height="30" fill="#2c3e50"/>
                            <circle cx="470" cy="35" r="3" fill="#f39c12"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features-section">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 text-center animate-on-scroll">
                    <h2 class="display-4 fw-bold mb-4">Why Choose Our Platform?</h2>
                    <p class="lead text-muted">Discover the powerful features that make online examinations seamless and secure</p>
                </div>
            </div>
            
            <div class="row g-4">
                <?php
                $features = [
                    [
                        'icon' => 'fas fa-shield-alt',
                        'title' => 'Secure & Reliable',
                        'description' => 'Advanced security measures ensure exam integrity with encrypted data transmission and secure user authentication.'
                    ],
                    [
                        'icon' => 'fas fa-clock',
                        'title' => 'Real-time Monitoring',
                        'description' => 'Live exam monitoring with automatic time tracking, progress indicators, and instant submission capabilities.'
                    ],
                    [
                        'icon' => 'fas fa-chart-line',
                        'title' => 'Instant Results',
                        'description' => 'Get immediate feedback with automated grading, detailed analytics, and comprehensive performance reports.'
                    ],
                    [
                        'icon' => 'fas fa-users',
                        'title' => 'Multi-User Support',
                        'description' => 'Separate dashboards for students and staff with role-based access control and personalized experiences.'
                    ],
                    [
                        'icon' => 'fas fa-mobile-alt',
                        'title' => 'Mobile Friendly',
                        'description' => 'Responsive design ensures seamless exam experience across all devices - desktop, tablet, and mobile.'
                    ],
                    [
                        'icon' => 'fas fa-cog',
                        'title' => 'Easy Management',
                        'description' => 'Intuitive admin panel for creating exams, managing questions, and analyzing student performance effortlessly.'
                    ]
                ];

                foreach ($features as $feature): ?>
                <div class="col-lg-4 col-md-6 animate-on-scroll">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="<?php echo $feature['icon']; ?>"></i>
                        </div>
                        <h4 class="feature-title"><?php echo $feature['title']; ?></h4>
                        <p class="feature-description">
                            <?php echo $feature['description']; ?>
                        </p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item animate-on-scroll">
                        <span class="stat-number" data-count="<?php echo $stats['students']; ?>">0</span>
                        <span class="stat-label">Students Registered</span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item animate-on-scroll">
                        <span class="stat-number" data-count="<?php echo $stats['faculty']; ?>">0</span>
                        <span class="stat-label">Faculty Members</span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item animate-on-scroll">
                        <span class="stat-number" data-count="<?php echo $stats['exams']; ?>">0</span>
                        <span class="stat-label">Exams Conducted</span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item animate-on-scroll">
                        <span class="stat-number" data-count="<?php echo $stats['success_rate']; ?>">0</span>
                        <span class="stat-label">Success Rate %</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="how-it-works">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 text-center animate-on-scroll">
                    <h2 class="display-4 fw-bold mb-4">How It Works</h2>
                    <p class="lead text-muted">Simple steps to get started with online examinations</p>
                </div>
            </div>
            
            <div class="row">
                <?php
                $steps = [
                    [
                        'number' => '1',
                        'title' => 'Register Account',
                        'description' => 'Create your account as a student or staff member with secure credentials and profile information.'
                    ],
                    [
                        'number' => '2',
                        'title' => 'Access Dashboard',
                        'description' => 'Login to your personalized dashboard to view available exams, schedules, and important notifications.'
                    ],
                    [
                        'number' => '3',
                        'title' => 'Take Examination',
                        'description' => 'Start your exam with confidence using our user-friendly interface and real-time progress tracking.'
                    ],
                    [
                        'number' => '4',
                        'title' => 'View Results',
                        'description' => 'Get instant results with detailed analysis, performance metrics, and areas for improvement.'
                    ]
                ];

                foreach ($steps as $step): ?>
                <div class="col-lg-3 col-md-6 animate-on-scroll">
                    <div class="step-card">
                        <div class="step-number"><?php echo $step['number']; ?></div>
                        <h4 class="step-title"><?php echo $step['title']; ?></h4>
                        <p class="step-description">
                            <?php echo $step['description']; ?>
                        </p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container text-center">
            <div class="animate-on-scroll">
                <h2 class="cta-title">Ready to Transform Your Examination Experience?</h2>
                <p class="cta-subtitle">
                    Join thousands of students and educators who trust our platform for secure, efficient online examinations.
                </p>
                <div class="mt-4">
                    <a href="signup.php" class="btn btn-light btn-lg me-3">
                        <i class="fas fa-user-plus me-2"></i>Sign Up Now
                    </a>
                    <a href="login.php" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i>Sign In
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="features-section">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 text-center animate-on-scroll">
                    <h2 class="display-4 fw-bold mb-4">About Us</h2>
                    <p class="lead text-muted">
                        Our mission is to revolutionize online assessments by providing a platform that is both powerful and easy to use. We bring together educators and students in a secure, real-time environment.
                    </p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-6 col-md-12 animate-on-scroll">
                    <p>
                        Founded in <?php echo $current_year; ?>, Online Examination System has been at the forefront of e-assessment technology. With innovations in proctoring, instant analytics, and mobile compatibility, we have served thousands of institutions across the globe.
                    </p>
                </div>
                <div class="col-lg-6 col-md-12 animate-on-scroll">
                    <p>
                        Our core values—security, reliability, and user-centric design—drive every feature we build. From seamless integration with existing databases to customizable exam workflows, we work closely with educational institutions to tailor the experience to their exact needs.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="footer-section">
                        <h5><i class="fas fa-graduation-cap me-2"></i>Online Examination System</h5>
                        <p class="text-muted">
                            Empowering education through secure, reliable, and user-friendly online examination solutions.
                        </p>
                        <div class="social-icons mt-3">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6">
                    <div class="footer-section">
                        <h5>Quick Links</h5>
                        <ul>
                            <li><a href="#home">Home</a></li>
                            <li><a href="#features">Features</a></li>
                            <li><a href="#how-it-works">How It Works</a></li>
                            <li><a href="#about">About Us</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6">
                    <div class="footer-section">
                        <h5>Support</h5>
                        <ul>
                            <li><a href="contact.html">Help Center</a></li>
                            <li><a href="documentation.html">Documentation</a></li>
                            <li><a href="#">Contact Support</a></li>
                            <li><a href="FAQ.html">FAQ</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="footer-section">
                        <h5>Contact Info</h5>
                        <ul>
                            <li><i class="fas fa-map-marker-alt me-2"></i>123 Education Lane, Knowledge City, 560001</li>
                            <li><i class="fas fa-phone-alt me-2"></i>+91 8509630185</li>
                            <li><i class="fas fa-envelope me-2"></i>support@onlinexam.com</li>
                        </ul>
                    </div>
                </div>
            </div>
            
           
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Navbar scroll effect
        const navbar = document.querySelector('.navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Animate on scroll
        const animatedElements = document.querySelectorAll('.animate-on-scroll');
        const observerOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.15
        };
        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);
        animatedElements.forEach(el => observer.observe(el));

        // Count-up animation for stats
        const statNumbers = document.querySelectorAll('.stat-number');
        const statsSection = document.querySelector('.stats-section');
        let statsAnimated = false;

        function animateStats() {
            if (statsAnimated) return;
            const sectionPos = statsSection.getBoundingClientRect().top;
            const screenPos = window.innerHeight;
            if (sectionPos < screenPos) {
                statNumbers.forEach((span) => {
                    const endValue = parseInt(span.getAttribute('data-count'));
                    let current = 0;
                    const increment = Math.ceil(endValue / 100);
                    const counter = setInterval(() => {
                        current += increment;
                        if (current >= endValue) {
                            span.textContent = endValue;
                            clearInterval(counter);
                        } else {
                            span.textContent = current;
                        }
                    }, 20);
                });
                statsAnimated = true;
            }
        }

        window.addEventListener('scroll', animateStats);
        // In case the stats are already in view on load
        document.addEventListener('DOMContentLoaded', animateStats);
    </script>
</body>
</html>
