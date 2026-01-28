<?php
session_start();

// ==========================
// login.php (Login page using MySQL with database-based admin authentication)
// ==========================

// 1) Show errors (for debugging; hide in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2) Database configuration (must match signup.php)
$dbConfig = [
    'host' => 'sql303.infinityfree.com',
    'user' => 'if0_39529641',
    'pass' => 'nIzoiCglOv',
    'name' => 'if0_39529641_online_exam_system'
];

// 3) Function to connect to database
function connectDB($config) {
    $mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['name']);
    if ($mysqli->connect_errno) {
        throw new Exception("Database connection failed: " . $mysqli->connect_error);
    }
    $mysqli->set_charset("utf8");
    return $mysqli;
}

// 4) Handle POST submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Collect and sanitize input
        $idInput    = trim($_POST['id']       ?? '');
        $password   = trim($_POST['password'] ?? '');
        $userType   = trim($_POST['userType'] ?? '');

        if (empty($idInput) || empty($password) || empty($userType)) {
            echo "<script>alert('Please fill in all fields and select user type');</script>";
            exit;
        }

        // Connect to database
        $mysqli = connectDB($dbConfig);

        // 4a) Admin login (now database-backed)
        if ($userType === 'admin') {
            // Check if admin table exists, if not create it with default admin
            $adminTableCheck = $mysqli->query("SHOW TABLES LIKE 'admin'");
            if ($adminTableCheck->num_rows == 0) {
                // Create admin table
                $createAdminTable = "
                    CREATE TABLE admin (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        name VARCHAR(100) NOT NULL,
                        email VARCHAR(100) UNIQUE NOT NULL,
                        password VARCHAR(255) NOT NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )
                ";
                $mysqli->query($createAdminTable);
                
                // Insert default admin (you can change these credentials)
                $defaultAdminName     = 'System Administrator';
                $defaultAdminEmail    = 'admin@gmail.com';
                $defaultAdminPassword = password_hash('admin@12345', PASSWORD_DEFAULT);
                
                $insertAdmin = $mysqli->prepare("INSERT INTO admin (name, email, password) VALUES (?, ?, ?)");
                $insertAdmin->bind_param('sss', $defaultAdminName, $defaultAdminEmail, $defaultAdminPassword);
                $insertAdmin->execute();
                $insertAdmin->close();
            }

            // Fetch admin credentials from database
            $stmt = $mysqli->prepare("SELECT id, name, password FROM admin WHERE email = ?");
            $stmt->bind_param('s', $idInput);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                $stmt->close();
                $mysqli->close();
                echo "<script>alert('Admin not found. Please check your credentials.');</script>";
                exit;
            }

            $adminRow        = $result->fetch_assoc();
            $adminId         = intval($adminRow['id']);
            $adminName       = $adminRow['name'];
            $storedAdminHash = $adminRow['password'];
            $stmt->close();

            if (password_verify($password, $storedAdminHash)) {
                $_SESSION['userId']   = $adminId;
                $_SESSION['userType'] = 'admin';
                $_SESSION['userName'] = $adminName;

                echo "<script>
                        alert('Login successful as Admin!');
                        window.location.href = 'admindashboard.php';
                      </script>";
                $mysqli->close();
                exit;
            } else {
                echo "<script>alert('Incorrect Admin password.');</script>";
                $mysqli->close();
                exit;
            }
        }

        // 4b) Staff / Student login (database-backed)
        if ($userType !== 'staff' && $userType !== 'student') {
            echo "<script>alert('Invalid user type selected.');</script>";
            $mysqli->close();
            exit;
        }

        $tableName = ($userType === 'staff') ? 'staff' : 'student';
        $stmt      = $mysqli->prepare("SELECT id, name, password FROM `$tableName` WHERE email = ?");
        $stmt->bind_param('s', $idInput);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $stmt->close();
            $mysqli->close();
            echo "<script>
                    alert('{$userType} not found. Redirecting to sign-up.');
                    window.location.href = 'signup.php';
                  </script>";
            exit;
        }

        $row        = $result->fetch_assoc();
        $fetchedId   = intval($row['id']);
        $fetchedName = $row['name'];
        $storedHash  = $row['password'];
        $stmt->close();

        if (password_verify($password, $storedHash)) {
            $_SESSION['userId']   = $fetchedId;
            $_SESSION['userType'] = $userType;
            $_SESSION['userName'] = $fetchedName;

            $dashboard = ($userType === 'staff') ? 'staffdashboard.php' : 'studentdashboard.php';
            echo "<script>
                    alert('Login successful as " . ucfirst($userType) . "!');
                    window.location.href = '$dashboard';
                  </script>";
            $mysqli->close();
            exit;
        } else {
            echo "<script>alert('Incorrect password.');</script>";
            $mysqli->close();
            exit;
        }

    } catch (Exception $e) {
        $errorMsg = addslashes($e->getMessage());
        echo "<script>
                alert('Error: $errorMsg');
                window.location.href = 'login.php';
              </script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Online Examination System - Sign In</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
          rel="stylesheet" />
    <style>
        /* Main Styles */
        body {
            background: url('background.jpg') no-repeat center center fixed;
            font-family: 'Arial', sans-serif;
            background-size: cover;
            background-position: center;
            min-height: 100vh;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
        }

        .navbar-brand {
            font-size: 1.5rem;
            color: #007bff !important;
            font-weight: bold;
        }

        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
        }

        .main-content {
            height: calc(100vh - 76px);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Login Card Styles */
        .login-card {
            width: 100%;
            max-width: 450px;
            padding: 2.5rem !important;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .login-card h2 {
            color: #333;
            font-weight: 600;
            margin-bottom: 2rem;
        }

        .user-type-selection {
            margin-bottom: 1.5rem;
        }

        .user-type-selection .btn {
            border-radius: 25px;
            padding: 8px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
            margin: 0 5px;
        }

        .form-control {
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            border-radius: 10px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-primary {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .password-toggle {
            position: relative;
        }

        .password-toggle .toggle-icon {
            position: absolute;
            right: 15px ;
            top: 70%;
            transform: translateY(-50%);
            cursor: pointer;
            user-select: none;
    }

        .login-links {
            margin-top: 1.5rem;
        }

        .login-links a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .login-links a:hover {
            color: #764ba2;
        }

        /* Admin Info Box */
        .admin-info {
            background: linear-gradient(45deg, #17a2b8, #138496);
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .admin-info h6 {
            margin-bottom: 10px;
            font-weight: 600;
        }

        .admin-info p {
            margin-bottom: 5px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .login-card {
                max-width: 90%;
                padding: 2rem !important;
            }
            
            .user-type-selection .btn {
                margin: 5px 2px;
                font-size: 14px;
                padding: 6px 15px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="#">
                <i class="fas fa-graduation-cap me-2"></i>Online Examination System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="aboutus.html">About Us</a></li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-warning text-white px-3 mx-2 rounded-pill" href="signup.php">Sign Up</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="main-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="login-card" id="loginModal">
                        <h2 class="text-center">Welcome Back</h2>
                        <form id="loginForm" method="POST" action="">
                            <div class="user-type-selection text-center">
                                <button type="button" class="btn btn-outline-primary" onclick="selectUserType('admin')">
                                    <i class="fas fa-user-shield me-1"></i>Admin
                                </button>
                                <button type="button" class="btn btn-outline-primary" onclick="selectUserType('staff')">
                                    <i class="fas fa-chalkboard-teacher me-1"></i>Staff
                                </button>
                                <button type="button" class="btn btn-outline-primary" onclick="selectUserType('student')">
                                    <i class="fas fa-user-graduate me-1"></i>Student
                                </button>
                                <input type="hidden" name="userType" id="userType" value="admin" />
                            </div>
                            
                            <div class="mb-3">
                                <label for="idInput" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="idInput" name="id" placeholder="Enter your email address" required />
                            </div>
                            
                            <div class="password-toggle mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required />
                                <span class="toggle-icon" onclick="togglePassword('password')">👁️</span>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 py-2">
                                <i class="fas fa-sign-in-alt me-2"></i>Sign In
                            </button>
                            
                            <div class="login-links text-center">
                                <a href="signup.php" class="me-3">
                                    <i class="fas fa-user-plus me-1"></i>Create account
                                </a>
                                <span class="text-muted">|</span>
                                <a href="forgotpassword.php" class="ms-3">
                                    <i class="fas fa-key me-1"></i>Forgot password?
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    
    <script>
        // Toggle password visibility
        function togglePassword(id) {
            const input = document.getElementById(id);
            const icon = input.nextElementSibling;
            
            if (input.type === "password") {
                input.type = "text";
                icon.textContent = "🙈";
            } else {
                input.type = "password";
                icon.textContent = "👁️";
            }
        }

        // Select User Type (update button styles and hidden input)
        function selectUserType(userType) {
            document.getElementById("userType").value = userType;
            const buttons = document.querySelectorAll(".user-type-selection button");
            const adminInfo = document.getElementById("adminInfo");
            
            // Reset all buttons
            buttons.forEach((btn) => {
                btn.classList.remove("btn-primary");
                btn.classList.add("btn-outline-primary");
            });
            
            // Highlight selected button
            const selectedBtn = document.querySelector(`button[onclick="selectUserType('${userType}')"]`);
            selectedBtn.classList.remove("btn-outline-primary");
            selectedBtn.classList.add("btn-primary");
            
            // Show admin info only for admin selection
            if (userType === 'admin') {
                adminInfo.style.display = 'block';
            } else {
                adminInfo.style.display = 'none';
                document.getElementById('idInput').value = '';
            }
        }

        // Set default to 'admin' on page load
        document.addEventListener("DOMContentLoaded", function () {
            selectUserType('admin');
            
            // Add some interactive feedback
            const inputs = document.querySelectorAll('input');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.style.transform = 'scale(1.02)';
                    this.style.transition = 'transform 0.2s ease';
                });
                
                input.addEventListener('blur', function() {
                    this.style.transform = 'scale(1)';
                });
            });
        });
    </script>
</body>
</html>
