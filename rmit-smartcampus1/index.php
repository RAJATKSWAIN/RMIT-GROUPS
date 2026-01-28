<?php
// Load configuration constants (branding, base URL, timezone)
include_once("includes/config.php");

// Load database connection
include_once("includes/db.php");

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $page_title; ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link href="assets/css/rmitscstyles.css?v=1.0" rel="stylesheet">
    

</head>

<body>

<!-- =================Start Navbar Header================= -->
<nav class="navbar navbar-expand-lg navbar-light fixed-top" >
  <div class="container">
    
    <!-- Brand Logo + Text -->
    <a class="navbar-brand d-flex align-items-center" href="index.php">
      <img src="https://rmitgroups.org/images/logo.png" alt="RMIT Logo" style="height:40px; margin-right:10px;">
      
    </a>

    <!-- Toggler for mobile -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navbar Links -->
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
        <li class="nav-item"><a class="nav-link" href="#how-it-works">How It Works</a></li>
        <li class="nav-item ms-3">
          <a class="btn btn-dark" href="/rmit-smartcampus/auth/login.php">Login</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
<!-- =================End Navbar Header================= -->
    
<!-- ================= HERO SECTION ================= -->
<section id="home" class="hero-section">
  <div class="container">
    <div class="row align-items-center">

      <div class="col-lg-6">
        <div class="hero-content">
          <h1 class="hero-title"> RMIT Student Management Portal </h1>

          <p class="hero-subtitle">
            Official digital platform of <strong>RMIT Group Of Institutions</strong>, 
            empowering students, faculty, and administrators with seamless academic management.
          </p>

          <div class="hero-buttons">
            <a href="/rmit-smartcampus/auth/signup.php" class="btn btn-light btn-lg">
              <i class="fas fa-user-plus me-2"></i>Register Now
            </a>
            <a href="#features" class="btn btn-outline-light btn-lg">
              <i class="fas fa-info-circle me-2"></i>Explore Features
            </a>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="hero-image text-center">
          <!-- You can keep your SVG illustration or replace with a student/faculty themed graphic -->
          <svg width="500" height="400" viewBox="0 0 500 400" style="max-width: 100%; height: auto;">
            <!-- Example: stylized book + graduation cap -->
            <rect x="100" y="200" width="300" height="150" rx="10" fill="#3498db" />
            <polygon points="250,100 300,80 350,100 350,120 300,140 250,120" fill="#2c3e50"/>
            <circle cx="300" cy="70" r="6" fill="#f39c12"/>
          </svg>
        </div>
      </div>

    </div>
  </div>
</section>

    
<!-- ================= FEATURES ================= -->
<section id="features" class="features-section">
  <div class="container">
    <div class="row mb-5">
      <div class="col-12 text-center animate-on-scroll">
        <h2 class="display-4 fw-bold mb-4">Why Choose RMIT Student Portal?</h2>
        <p class="lead text-muted">Discover the integrated features that make student management efficient and transparent</p>
      </div>
    </div>
    
    <div class="row g-4">
      <?php
      $features = [
        [
          'icon' => 'fas fa-user-graduate',
          'title' => 'Student Dashboard',
          'description' => 'Personalized access to courses, attendance, fees, and exam results.'
        ],
        [
          'icon' => 'fas fa-chalkboard-teacher',
          'title' => 'Faculty Panel',
          'description' => 'Faculty can manage courses, mark attendance, and monitor student progress.'
        ],
        [
          'icon' => 'fas fa-user-shield',
          'title' => 'Admin Control',
          'description' => 'Admins manage users, departments, fee plans, and academic records centrally.'
        ],
        [
          'icon' => 'fas fa-calendar-check',
          'title' => 'Attendance Tracking',
          'description' => 'Daily attendance management with instant student and faculty updates.'
        ],
        [
          'icon' => 'fas fa-credit-card',
          'title' => 'Fees Management',
          'description' => 'Students can view dues, make payments, and admins can generate reports.'
        ],
        [
          'icon' => 'fas fa-database',
          'title' => 'Centralized Records',
          'description' => 'All student data, courses, and results securely stored in one system.'
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

<!-- ================= STATS ================= -->
<section id="stats" class="stats-section bg-light py-5">
  <div class="container">
    <div class="row text-center">
      <div class="col-md-3">
        <h3 class="fw-bold">280+</h3>
        <p class="text-muted">Registered Students</p>
      </div>
      <div class="col-md-3">
        <h3 class="fw-bold">55+</h3>
        <p class="text-muted">Faculty Members</p>
      </div>
      <div class="col-md-3">
        <h3 class="fw-bold">10+</h3>
        <p class="text-muted">Courses Offered</p>
      </div>
      <div class="col-md-3">
        <h3 class="fw-bold">100%</h3>
        <p class="text-muted">Secure Data Management</p>
      </div>
    </div>
  </div>
</section>
    
    
<!--================= Start How It Works Section =================-->
<section id="how-it-works" class="how-it-works">
  <div class="container">
    <div class="row mb-5">
      <div class="col-12 text-center animate-on-scroll">
        <h2 class="display-4 fw-bold mb-4">How It Works</h2>
        <p class="lead text-muted">Simple steps to get started with the RMIT Group Student Portal</p>
      </div>
    </div>
    
    <div class="row">
      <?php
      $steps = [
        [
          'number' => '1',
          'title' => 'Register Account',
          'description' => 'Create your student account with secure credentials and complete your profile information.'
        ],
        [
          'number' => '2',
          'title' => 'Access Dashboard',
          'description' => 'Login to your personalized dashboard to view courses, attendance, fees, and notifications.'
        ],
        [
          'number' => '3',
          'title' => 'Manage Academics',
          'description' => 'Track attendance, pay fees online, and access academic resources in one place.'
        ],
        [
          'number' => '4',
          'title' => 'View Records',
          'description' => 'Get instant access to your academic records, performance reports, and institutional updates.'
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
<!--================= End How It Works Section =================-->

<!--================= Start CTA Section =================-->
<section class="cta-section">
  <div class="container text-center">
    <div class="animate-on-scroll">
      <h2 class="cta-title">Ready to Simplify Your Student Journey?</h2>
      <p class="cta-subtitle">
        Join thousands of students and faculty who trust the RMIT Group Student Portal for secure, efficient academic management.
      </p>
      <div class="mt-4">
        <a href="/rmit-smartcampus/auth/signup.php" class="btn btn-light btn-lg me-3">
          <i class="fas fa-user-plus me-2"></i>Register Now
        </a>
        <a href="/rmit-smartcampus/auth/login.php" class="btn btn-outline-light btn-lg">
          <i class="fas fa-sign-in-alt me-2"></i>Sign In
        </a>
      </div>
    </div>
  </div>
</section>
<!--================= End CTA Section =================-->
    
<!-- =================Start  FOOTER ================= -->
<footer class="footer">
    <div class="container text-center">
        <p class="mb-0">
            © <?php echo date("Y"); ?> RMIT Group Of Institutions – Student Management Portal
        </p>
    </div>
</footer>
<!-- =================End FOOTER ================= -->

  
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
