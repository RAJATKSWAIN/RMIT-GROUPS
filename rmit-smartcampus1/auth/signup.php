<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once(__DIR__ . "/../includes/config.php");
require_once(__DIR__ . "/../includes/db.php");

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $role     = $_POST["role"]; // NEW: role selector
    $name     = trim($_POST["name"]);
    $gender   = $_POST["gender"];
    $contact  = $_POST["contactNumber"];
    $address  = $_POST["address"];
    $dob      = $_POST["dob"];
    $dept     = $_POST["department"];
    $semester = $_POST["semester"] ?? null; // only for students
    $email    = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm  = $_POST["confirmPassword"];

    if ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        // Check if email already exists
        $check = $conn->prepare("SELECT id FROM users WHERE email=?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Email already registered.";
        } else {
            // Insert into users table
            $stmt = $conn->prepare("INSERT INTO users (role, email, password_hash, full_name) VALUES (?,?,?,?)");
            $stmt->bind_param("ssss", $role, $email, $password_hash, $name);

            if ($stmt->execute()) {
                $user_id = $stmt->insert_id;

                if ($role === "student") {
                    // Insert into students table
                    $stmt2 = $conn->prepare("INSERT INTO students (user_id, roll_no, program, semester, section, phone) VALUES (?,?,?,?,?,?)");
                    $roll_no = strtoupper(substr($dept,0,3)) . rand(1000,9999);
                    $section = "A";
                    $stmt2->bind_param("isssis", $user_id, $roll_no, $dept, $semester, $section, $contact);

                    if ($stmt2->execute()) {
                        $success = "✅ Student registration successful. You can now login.";
                    } else {
                        $error = "❌ Error saving student details.";
                    }
                } elseif ($role === "staff") {
                    // Insert into staff table
                    $stmt3 = $conn->prepare("INSERT INTO staff (user_id, department, phone, gender, dob, address) VALUES (?,?,?,?,?,?)");
                    $stmt3->bind_param("isssss", $user_id, $dept, $contact, $gender, $dob, $address);

                    if ($stmt3->execute()) {
                        $success = "✅ Staff registration successful. You can now login.";
                    } else {
                        $error = "❌ Error saving staff details.";
                    }
                }
            } else {
                $error = "❌ Error creating account.";
            }
        }
    }
}
?>

<!DOCTYPE html>

<html lang="en">

<head>
    
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Mobile viewport optimized -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	
  <title>RMIT SmartCampus - Registration</title>
    
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,400i,700,800|Varela+Round" rel="stylesheet">

  <!-- CSS links -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="assets/css/styles.css?v=1.0" rel="stylesheet">
  <link href="assets/css/style.css?v=1.0" rel="stylesheet">
  
  <style>
  /* Background */
body {
  background: url('background.jpg') no-repeat center center fixed;
  font-family: 'Poppins', sans-serif;
  background-size: cover;
  background-position: center;
  min-height: 100vh;
  color: #001f4d;
}

/* Signup Box */
.signup-box {
  max-width: 500px;
  margin: 6% auto;
  padding: 40px;
  background: #ffffff;
  border-radius: 20px;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.25);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.signup-box:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 35px rgba(0, 0, 0, 0.35);
}

/* Heading */
.signup-box h2 {
  text-align: center;
  margin-bottom: 25px;
  font-weight: 700;
  font-size: 1.6rem;
  color: #1E90FF;
  letter-spacing: 0.5px;
}

/* Badge */
.student-badge {
  background: linear-gradient(90deg, #28a745, #20c997);
  color: #fff;
  padding: 12px 25px;
  border-radius: 30px;
  font-weight: 600;
  font-size: 1rem;
  display: inline-block;
  margin-bottom: 25px;
  box-shadow: 0 4px 10px rgba(40, 167, 69, 0.3);
}

/* Buttons */
.btn-primary {
  background: linear-gradient(45deg, #007bff, #0056b3);
  border: none;
  padding: 12px;
  font-weight: bold;
  border-radius: 8px;
  transition: all 0.3s ease;
}
.btn-primary:hover {
  transform: translateY(-2px);
  background: linear-gradient(45deg, #0056b3, #003f7f);
}

/* Form Inputs */
.form-control, .form-select {
  border-radius: 8px;
  border: 1px solid #ccc;
  padding: 10px;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}
.form-control:focus, .form-select:focus {
  border-color: #1E90FF;
  box-shadow: 0 0 6px rgba(30, 144, 255, 0.4);
}

/* Footer */
.footer {
  background: #2c3e50;
  color: white;
  padding: 60px 0 30px;  
  margin-top: 60px;
  text-align: center;
  box-shadow: 0 -4px 12px rgba(0,0,0,0.2);
}
.footer p {
  margin: 0;
  font-size: 0.95rem;
  font-weight: 500;
  letter-spacing: 0.3px;
}
.footer a {
  color: #ffdd57;
  text-decoration: none;
}
.footer a:hover {
  text-decoration: underline;
}
  </style>
  
</head>

<body>

	<!-- =================End Navbar Header================= -->
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
			<li class="nav-item"><a class="nav-link" href="/rmit-smartcampus/index.php">Home</a></li>
			<li class="nav-item"><a class="nav-link" href="/rmit-smartcampus/#features">Features</a></li>
			<li class="nav-item"><a class="nav-link" href="/rmit-smartcampus/#how-it-works">How It Works</a></li>
			<li class="nav-item ms-3">
			<a class="btn btn-dark" href="/rmit-smartcampus/auth/login.php">Login</a>
			</li>
		</ul>
		</div>
	</div>
	</nav>
	<!-- =================End Navbar Header================= -->
	
	<!-- =================Start Account Registration================= -->
    <section style="background:linear-gradient(135deg, rgba(102,126,234,0.85), rgba(118,75,162,0.85) ),
     	   			url('background.jpg') center/cover no-repeat;">
	<div class="signup-box">
	<div class="text-center">
		<div class="student-badge">🎓 Account Registration Portal</div>
	</div>
	
	<h2>Create Your Account</h2>
	
	<?php if(!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
	<?php if(!empty($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
	
	<form id="signupForm" action="signup.php" method="post" onsubmit="return handleSubmit(event)">
	<!-- Role Selector -->
	<div class="mb-3">
		<label class="form-label fw-bold">Register As:</label>
		<select class="form-select" id="role" name="role" required>
		<option selected disabled>Select Role</option>
		<option value="student">Student</option>
		<option value="staff">Staff</option>
		</select>
	</div>
	
	<div class="mb-3">
		<input type="text" id="name" name="name" class="form-control" placeholder="Full Name" required>
	</div>
	
	<!-- Gender -->
	<div class="mb-3">
		<label class="form-label fw-bold">Gender:</label><br>
		<div class="form-check form-check-inline">
		<input class="form-check-input" type="radio" name="gender" value="Male" required>
		<label class="form-check-label">Male</label>
		</div>
		<div class="form-check form-check-inline">
		<input class="form-check-input" type="radio" name="gender" value="Female">
		<label class="form-check-label">Female</label>
		</div>
		<div class="form-check form-check-inline">
		<input class="form-check-input" type="radio" name="gender" value="Other">
		<label class="form-check-label">Other</label>
		</div>
	</div>
	
	<div class="mb-3">
		<input type="tel" id="contactNumber" name="contactNumber" class="form-control" placeholder="Contact Number" required>
	</div>
	
	<div class="mb-3">
		<textarea id="address" name="address" class="form-control" placeholder="Address" rows="2"></textarea>
	</div>
	
	<div class="form-floating mb-3">
		<input type="date" class="form-control" id="dob" name="dob">
		<label for="dob">Date of Birth</label>
	</div>
	
	<div class="mb-3">
		<select class="form-select" id="department" name="department" required>
		<option selected disabled>Select Department</option>
		<option value="BCA">Bachelor of Computer Applications</option>
		<option value="BES">Bachelor in Electronic Science</option>
		</select>
	</div>
	
	<!-- Semester only for students -->
	<div class="mb-3" id="semesterBlock">
		<select id="semester" name="semester" class="form-select">
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
	
	<div class="mb-3">
		<input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
	</div>
	
	<div class="mb-3">
		<input type="password" id="confirmPassword" name="confirmPassword" class="form-control" placeholder="Confirm Password" required>
	</div>
	
	<button type="submit" class="btn btn-primary w-100">Create Account</button>
	</form>
	
	<div class="text-center mt-3">
		<small>Already have an account? <a href="login.php">Sign In</a></small>
	</div>
	</div>
	</section>
    
    <!-- =================Start  FOOTER ================= -->
	<footer class="footer">
    <div class="container text-center"> 
        <p class="mb-0">
            © <?php echo date("Y"); ?> RMIT Group Of Institutions – Student Management Portal
        </p>
    </div>
	</footer>
	<!-- =================End FOOTER ================= -->
    
	<script>
	document.getElementById('role').addEventListener('change', function() {
	const semesterBlock = document.getElementById('semesterBlock');
	if (this.value === 'student') {
		semesterBlock.style.display = 'block';
	} else {
		semesterBlock.style.display = 'none';
	}
	});
	</script>
    
    
	