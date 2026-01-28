<?php
// ==========================
// signup.php (Student Only Version)
// ==========================

// 1) Show errors (for debugging — only in dev; hide in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database configuration
$dbConfig = [
    'host' => 'sql303.infinityfree.com',
    'user' => 'if0_39529641',
    'pass' => 'nIzoiCglOv',
    'name' => 'if0_39529641_online_exam_system'
];


// Function to connect to database
function connectDB($config) {
    $mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['name']);
    if ($mysqli->connect_errno) {
        throw new Exception("Database connection failed: " . $mysqli->connect_error);
    }
    $mysqli->set_charset("utf8");
    return $mysqli;
}

// Function to validate student input data
function validateStudentInput($data) {
    $errors = [];
    
    // Required fields for students
    $required = ['name', 'gender', 'contactNumber', 'department', 'semester', 'email', 'password', 'confirmPassword'];
    foreach($required as $field) {
        if (empty($data[$field])) {
            $errors[] = ucfirst($field) . " is required.";
        }
    }
    
    // Validate email format
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    
    // Validate password match
    if ($data['password'] !== $data['confirmPassword']) {
        $errors[] = "Passwords do not match.";
    }
    
    // Validate password strength (minimum 5 characters)
    if (strlen($data['password']) < 5) {
        $errors[] = "Password must be at least 5 characters long.";
    }
    
    // Validate phone number (basic check)
    if (!preg_match('/^[0-9]{10,15}$/', $data['contactNumber'])) {
        $errors[] = "Contact number must be 10-15 digits.";
    }
    
    return $errors;
}

// Function to check if student exists
function studentExists($mysqli, $email) {
    $stmt = $mysqli->prepare("SELECT id FROM student WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result->num_rows > 0;
    $stmt->close();
    return $exists;
}

// Function to insert new student
function insertStudent($mysqli, $data) {
    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO student (name, gender, contact, address, dob, department, semester, email, password) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('sssssssss', 
        $data['name'], 
        $data['gender'], 
        $data['contactNumber'], 
        $data['address'], 
        $data['dob'], 
        $data['department'], 
        $data['semester'], 
        $data['email'], 
        $hashedPassword
    );
    
    $success = $stmt->execute();
    if (!$success) {
        throw new Exception("Database error: " . $stmt->error);
    }
    $stmt->close();
    return $success;
}

// 2) Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Collect and sanitize input
        $formData = [
            'name' => trim($_POST['name'] ?? ''),
            'gender' => trim($_POST['gender'] ?? ''),
            'contactNumber' => trim($_POST['contactNumber'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'dob' => !empty($_POST['dob']) ? trim($_POST['dob']) : null,
            'department' => trim($_POST['department'] ?? ''),
            'semester' => trim($_POST['semester'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'confirmPassword' => $_POST['confirmPassword'] ?? ''
        ];
        
        // Validate input
        $errors = validateStudentInput($formData);
        if (!empty($errors)) {
            $errorMessage = implode("\\n", $errors);
            echo "<script>
                    alert('$errorMessage');
                    window.location.href = 'signup.php';
                  </script>";
            exit;
        }
        
        // Connect to database
        $mysqli = connectDB($dbConfig);
        
        // Check if student already exists
        if (studentExists($mysqli, $formData['email'])) {
            echo "<script>
                    alert('Student already exists with this email!');
                    window.location.href = 'login.php';
                  </script>";
            $mysqli->close();
            exit;
        }
        
        // Insert new student
        if (insertStudent($mysqli, $formData)) {
            echo "<script>
                    alert('Registration successful! Please login.');
                    window.location.href = 'login.php';
                  </script>";
        }
        
        $mysqli->close();
        
    } catch (Exception $e) {
        $errorMsg = addslashes($e->getMessage());
        echo "<script>
                alert('Error: $errorMsg');
                window.location.href = 'signup.php';
              </script>";
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Online Examination System - Student Registration</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background: url('background.jpg') no-repeat center center fixed;
      font-family: 'Arial', sans-serif;
      background-size: cover;
      background-position: center;
      min-height: 100vh;
    }
    .signup-box {
      max-width: 450px;
      margin: 2% auto;
      padding: 40px;
      background-color: rgba(255, 255, 255, 0.95);
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }
    h2 {
      text-align: center;
      margin-bottom: 30px;
      font-weight: bold;
      color: #333;
    }
    .btn-warning {
      background-color: #ffc107;
      border-color: #ffc107;
    }
    .navbar {
      background: rgba(255, 255, 255, 0.95) !important;
      backdrop-filter: blur(10px);
    }
    .password-toggle {
      position: relative;
    }
    .password-toggle .toggle-icon {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      user-select: none;
    }
    .form-control:focus {
      box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
      border-color: #007bff;
    }
    .btn-primary {
      background: linear-gradient(45deg, #007bff, #0056b3);
      border: none;
      padding: 12px;
      font-weight: bold;
      transition: transform 0.2s ease;
    }
    .btn-primary:hover {
      transform: translateY(-2px);
    }
    .student-badge {
      background: linear-gradient(45deg, #28a745, #20c997);
      color: white;
      padding: 10px 20px;
      border-radius: 25px;
      display: inline-block;
      margin-bottom: 20px;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold text-primary" href="#">Online Examination System</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="aboutus.html">About Us</a></li>
        <li class="nav-item">
          <a class="nav-link btn btn-warning text-white px-3 mx-2" href="login.php">Sign In</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="signup-box">
  <div class="text-center">
    <div class="student-badge">
      🎓 Student Registration
    </div>
  </div>
  
  <h2>Create Student Account</h2>
  
  <form id="signupForm" action="signup.php" method="post" onsubmit="return handleSubmit(event)">
    <div class="mb-3">
      <input type="text" id="name" name="name" class="form-control" placeholder="Full Name" required>
    </div>

    <div class="mb-3">
      <label class="form-label fw-bold">Gender:</label><br>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="gender" id="male" value="Male" required>
        <label class="form-check-label" for="male">Male</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="gender" id="female" value="Female">
        <label class="form-check-label" for="female">Female</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="gender" id="other" value="Other">
        <label class="form-check-label" for="other">Other</label>
      </div>
    </div>

    <div class="mb-3">
      <input type="tel" id="contactNumber" name="contactNumber" class="form-control" placeholder="Contact Number" required>
    </div>

    <div class="mb-3">
      <textarea id="address" name="address" class="form-control" placeholder="Address" rows="2"></textarea>
    </div>

    <div class="form-floating mb-3">
      <input type="date" class="form-control" id="dob" name="dob" placeholder="Date of Birth">
      <label for="dob">Date of Birth</label>
    </div>

    <div class="mb-3">
      <select class="form-select" id="department" name="department" required>
        <option selected disabled>Select Department</option>
        <option value="BCA">Bachelor of Computer Applications</option>
        <option value="MCA">Master of Computer Applications</option>
        <option value="CS">Computer Science</option>
      </select>
    </div>

    <div class="mb-3">
      <select id="semester" name="semester" class="form-select" required>
        <option selected disabled>Select Semester</option>
        <option value="1">1st Semester</option>
        <option value="2">2nd Semester</option>
        <option value="3">3rd Semester</option>
        <option value="4">4th Semester</option>
        <option value="5">5th Semester</option>
        <option value="6">6th Semester</option>
        <option value="7">7th Semester</option>
        <option value="8">8th Semester</option>
      </select>
    </div>

    <div class="mb-3">
      <input type="email" id="email" name="email" class="form-control" placeholder="Email Address" required>
    </div>

    <div class="password-toggle mb-3">
      <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
      <span class="toggle-icon" onclick="togglePassword('password')">👁️</span>
    </div>

    <div class="password-toggle mb-3">
      <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" placeholder="Confirm Password" required>
      <span class="toggle-icon" onclick="togglePassword('confirmPassword')">👁️</span>
    </div>

    <div id="passwordError" class="text-danger mb-3" style="display:none;">Passwords do not match.</div>

    <button type="submit" class="btn btn-primary w-100">Create Student Account</button>
  </form>

  <div class="text-center mt-3">
    <small>Already have an account? <a href="login.php">Sign In</a></small>
  </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  function togglePassword(id) {
    const input = document.getElementById(id);
    const icon = input.nextElementSibling;
    
    if (input.type === 'password') {
      input.type = 'text';
      icon.textContent = '🙈';
    } else {
      input.type = 'password';
      icon.textContent = '👁️';
    }
  }

  function handleSubmit(event) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const errorDiv = document.getElementById('passwordError');

    if (password !== confirmPassword) {
      errorDiv.style.display = 'block';
      event.preventDefault();
      return false;
    } else {
      errorDiv.style.display = 'none';
    }

    if (password.length < 5) {
      alert("Password must be at least 5 characters long.");
      event.preventDefault();
      return false;
    }

    return true;
  }

  // Add some interactive feedback
  document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('input, select, textarea');
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
