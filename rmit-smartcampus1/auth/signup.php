<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once(__DIR__ . "/../includes/config.php");
require_once(__DIR__ . "/../includes/db.php");

$error = "";
$success = "";

/* ================= ROLL NUMBER GENERATOR ================= */
function generateRollNo($conn, $institute, $program, $semester) {

    $programMap = [
        "Diploma in Mechanical Engineering" => "DME",
        "Diploma in Electrical Engineering" => "DEE",
        "Diploma in Civil Engineering" => "DCE",
        "Diploma Computer Science Engineering" => "DCSE",
        "Bachelor in Computer Application" => "BCA",
        "Bachelor in Electronic Science" => "BES",
        "Fitter" => "FIT",
        "Electrician" => "ELC",
        "Electronics Mechanic" => "EM",
        "Welder" => "WLD"
    ];

    $programCode = $programMap[$program] ?? strtoupper(substr($program, 0, 3));

    $currentYear = (int)date("Y");
    $yearOffset  = floor(((int)$semester - 1) / 2);
    $joiningYear = $currentYear - $yearOffset;
    $yy = substr((string)$joiningYear, -2);

    $prefix = strtoupper("$institute-$programCode-$yy");

    $stmt = $conn->prepare("
        SELECT MAX(roll_no) 
        FROM students 
        WHERE roll_no LIKE CONCAT(?, '%')
    ");
    $stmt->bind_param("s", $prefix);
    $stmt->execute();
    $stmt->bind_result($lastRoll);
    $stmt->fetch();
    $stmt->close();

    $next = 1;
    if ($lastRoll) {
        $next = (int)substr($lastRoll, -2) + 1;
    }

    $sequence = str_pad($next, 2, "0", STR_PAD_LEFT);
    return "$prefix$sequence";
}

/* Normalize "1st Sem", "2nd Year" → 1,2 */
function normalizeSemester($raw) {
    if (!$raw) return null;
    if (is_numeric($raw)) return (int)$raw;
    preg_match('/\d+/', $raw, $m);
    return isset($m[0]) ? (int)$m[0] : null;
}

/* ================= SIGNUP HANDLER ================= */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    try {

        $institute = $_POST["institute"] ?? null;
        $role      = $_POST["role"] ?? null;

        if ($role === "admin") {
            throw new Exception("Admin accounts cannot be created via public signup.");
        }

        $name     = trim($_POST["name"] ?? "");
        $email    = trim($_POST["email"] ?? "");
        $password = $_POST["password"] ?? "";
        $confirm  = $_POST["confirmPassword"] ?? "";

        $gender   = $_POST["gender"] ?? null;
        $contact  = $_POST["contactNumber"] ?? null;
        $address  = $_POST["address"] ?? null;
        $dob      = $_POST["dob"] ?? null;
        $dept     = $_POST["department"] ?? null;
        $designation = $_POST["designation"] ?? null;

        $semesterRaw = $_POST["semester"] ?? null;
        $semester    = normalizeSemester($semesterRaw);

        if (!$institute || !$role || !$name || !$email || !$password) {
            throw new Exception("Missing required fields.");
        }

        if ($password !== $confirm) {
            throw new Exception("Passwords do not match.");
        }

        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        /* Check email */
        $check = $conn->prepare("SELECT id FROM users WHERE email=?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            throw new Exception("Email already registered.");
        }

        $conn->begin_transaction();

        /* Insert into users */
        $stmt = $conn->prepare("
            INSERT INTO users (institute_code, role, email, password_hash, full_name)
            VALUES (?,?,?,?,?)
        ");
        $stmt->bind_param("sssss", $institute, $role, $email, $password_hash, $name);

        if (!$stmt->execute()) {
            throw new Exception("Failed to create user.");
        }

        $user_id = $stmt->insert_id;

        /* ========== STUDENT ========== */
        if ($role === "student") {

            if (!$dept || !$semester) {
                throw new Exception("Program and semester are required for students.");
            }

            $roll_no = generateRollNo($conn, $institute, $dept, (int)$semester);
            $section = "A";

            $stmt2 = $conn->prepare("
                INSERT INTO students 
                (user_id, roll_no, program, semester, section, phone, gender, dob, address)
                VALUES (?,?,?,?,?,?,?,?,?)
            ");

            $stmt2->bind_param(
                "ississsss",
                $user_id,
                $roll_no,
                $dept,
                $semester,
                $section,
                $contact,
                $gender,
                $dob,
                $address
            );

            if (!$stmt2->execute()) {
                throw new Exception("Error saving student data.");
            }

            $success = "Student registered successfully. Roll No: $roll_no";
        }

        /* ========== STAFF ========== */
        elseif ($role === "staff") {

            if (!$dept || !$designation) {
                throw new Exception("Department and designation are required for staff.");
            }

            $stmt3 = $conn->prepare("
                INSERT INTO staff 
                (user_id, department, designation, phone, gender, dob, address)
                VALUES (?,?,?,?,?,?,?)
            ");

            $stmt3->bind_param(
                "issssss",
                $user_id,
                $dept,
                $designation,
                $contact,
                $gender,
                $dob,
                $address
            );

            if (!$stmt3->execute()) {
                throw new Exception("Error saving staff data.");
            }

            $success = "Staff registered successfully.";
        }

        $conn->commit();

    } catch (Exception $e) {
        $conn->rollback();
        $error = $e->getMessage();
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

  <!-- Page Title -->
  <title>RMIT SmartCampus Portal | Academic Management System for Students, Faculty & Administration</title>

  <!-- Meta Description -->
  <meta name="description" content="RMIT SmartCampus Portal is the academic management system for RMIT Group of Institutions. Access student registration, faculty services, administration tools, admissions, 		and online campus resources in one secure platform.">

  <!-- Meta Keywords -->
  <meta name="keywords" content="RMIT SmartCampus, RMIT Group of Institutions, Academic Management System, Student Portal, Faculty Portal, Administration Portal, RMIT Registration, RMIT Admission, RMIT Online 	Services">

  <!-- Author -->
  <meta name="author" content="RMIT Group of Institutions">

  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="/rmit-smartcampus/assets/img/favicon.ico">
  <link rel="icon" type="image/png" sizes="32x32" href="/rmit-smartcampus/assets/img/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="64x64" href="/rmit-smartcampus/images/favicon-64.png">
  <link rel="apple-touch-icon" sizes="180x180" href="/rmit-smartcampus/assets/img/apple-touch-icon.png">
  <link rel="manifest" href="/rmit-smartcampus/assets/img/site.webmanifest">
  <link rel="mask-icon" href="/rmit-smartcampus/assets/img/safari-pinned-tab.svg" color="#5bbad5">
  <meta name="msapplication-TileColor" content="#2d89ef">
  <meta name="theme-color" content="#ffffff">
    
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,400i,700,800|Varela+Round" rel="stylesheet">

  <!-- CSS links -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"> 
  <link href="/rmit-smartcampus/assets/css/footer.css?v=1.0" rel="stylesheet">
      
  <style>
  :root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    --warning-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
	}
      
  body {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%), url('background.jpg') no-repeat center center fixed;
  background-size: cover;
  min-height: 100vh;
  font-family: 'Arial', sans-serif;
  display: flex;
  flex-direction: column;
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
      
    /* ================= Signup Box ================= */
	/* Signup Box */
	.signup-box {
		max-width: 580px;
		margin: 5% auto; 
		padding: 40px;
		background: #ffffff;
		border-radius: 16px;
		box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
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
	     		
	/* ---------- Mobile Layout ---------- */
	@media (max-width: 768px) {
	
	.hero-section {
		margin: 20px 0px;
		padding: 10px 10px;
		border-radius: 12px;
		max-width: 110%;
	}
	
	.signup-box {
		margin: 70px 25px;
		padding: 30px 20px;
		border-radius: 12px;
		max-width: 100%;
	}
	
	.signup-box h2 {
		font-size: 1.3rem;
	}
	
	.student-badge {
		font-size: 0.85rem;
		padding: 8px 14px;
	}
	
	.navbar-brand img {
		height: 20px;
	}
	
	.navbar-nav {
		background: white;
		padding: 10px;
		border-radius: 10px;
		margin-top: 10px;
	}
	
	.navbar-nav .nav-link {
		padding: 10px 15px;
	}
    
     /* Navbar */
  /* Ensure brand + toggle align horizontally */
.navbar .navbar-brand {
  display: flex;
  align-items: center;
  margin-right: auto; /* push logos to the left */
}

/* Place toggle right next to logos in mobile */
.navbar .navbar-toggler {
  margin-left: 10px; /* small spacing */
  order: 2;          /* force toggle after logos */
}

/* Optional: adjust spacing between logos */
.navbar .navbar-brand img:first-child {
  margin-right: 10px;
}  
    
  .navbar-brand img {
    height: 18px !important;
  }
 
  .navbar-brand img {
		height: 25px;
		width: 180px;
		margin: 0 05px;
	}
	
	.form-control,
	.form-select {
		font-size: 0.95rem;
		padding: 10px;
	}
	
	.btn-primary {
		padding: 12px;
		font-size: 1rem;
	}
	
	footer.footer {
		font-size: 0.8rem;
		padding: 10px 0;
	}
	
	}
	
	/* ---------- Small Screens ---------- */
	@media (max-width: 576px) {
	
	.form-check-inline {
		display: block;
		margin-bottom: 5px;
	}
	
	}
	
	/* ---------- Touch Accessibility ---------- */
	.form-control,
	.form-select,
	.btn {
	min-height: 44px;
	}

  </style>
  
</head>

<body>

<!-- =================Start Navbar Header================= -->
<nav class="navbar navbar-expand-lg navbar-light fixed-top" >
  <div class="container">
    
    <!-- Brand Logo + Text -->
    <a class="navbar-brand d-flex align-items-center" href="/rmit-smartcampus/index.php">
      <img src="/rmit-smartcampus/images/rmitsclogo1.png" alt="RMIT Logo" style="height:45px; width:auto; "> 
        <span style="border-left:5px solid #ff2f00; height:30px; margin:0 20px; display:inline-block;"></span>
      <img src="https://rmitgroups.org/images/logo.png" alt="RMIT Logo" style="height:20px; margin-right:10px;">      
    </a>

    <!-- Toggle Button (RIGHT side of logo) -->
    <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>
      
    <!-- Navbar Links -->
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="/rmit-smartcampus/index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="/rmit-smartcampus/index.php#features">Features</a></li>
        <li class="nav-item"><a class="nav-link" href="/rmit-smartcampus/index.php#how-it-works">How It Works</a></li>
      </ul>
    </div>
  </div>
</nav>
<!-- =================End Navbar Header================= -->
	
	<!-- =================Start Account Registration================= -->
    <section class="hero-section">
	<div class="signup-box">
	<div class="text-center">
		<div class="student-badge">🎓 RMIT Group – Universal Registration Portal</div>
	</div>
	
	<h2>Create Your Account</h2>
	
	<?php if(!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
	<?php if(!empty($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
	
	<form id="signupForm" action="signup.php" method="post">
     
    <!-- Institute Selector -->
    <div class="mb-3">
  		<label class="form-label fw-bold">Select Institute:</label>
  		<select class="form-select" id="institute" name="institute" required>
    	<option selected disabled>Select Institute</option>
    	<option value="HIT">HIT - Holy Institute of Technology</option>
    	<option value="RMIT">RMIT - Rajiv Memorial Institute of Technology</option>
    	<option value="RMITC">RMITC - Industrial Training Center</option>
    	<option value="CPS">CPS - Chirag Public School</option>
  		</select>
	</div>

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
        
    <!-- Designation only for staff -->
	<div class="mb-3" id="designationBlock" style="display:none;">
  		<select id="designation" name="designation" class="form-select">
    	<option selected disabled>Select Designation</option>
    	<option>Assistant Lecturer</option>
    	<option>Lecturer</option>
    	<option>Trainer</option>
    	<option>Non Teaching Staff</option>
    	<option>Placement Officer</option>
  		</select>
	</div>

	
	<div class="mb-3">
		<input type="email" id="email" name="email" class="form-control" placeholder="e.g. s12345@student.rmit.edu" required>
	</div>
	
	<div class="mb-3">
		<input type="password" id="password" name="password" class="form-control" placeholder="Min 6 characters"  required>
	</div>
	
	<div class="mb-3">
		<input type="password" id="confirmPassword" name="confirmPassword" class="form-control" placeholder="Confirm Password" required>
	</div>
	
	<button type="submit" class="btn btn-primary w-100">Create Account</button>
	</form>
	
	<div class="text-center mt-3">
		<small>Already have an account? <a href="/rmit-smartcampus/auth/login.php">Sign In</a></small>
	</div>
	</div>
	</section>
    
	<style>
	.signup-box {
	max-width: 680px;
	margin: 5% auto;
	padding: 40px;
	background: #ffffff;
	border-radius: 16px;
	box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
	}
	
	.signup-box h2 {
	text-align: center;
	margin-bottom: 25px;
	font-weight: 700;
	font-size: 1.6rem;
	color: #1E90FF;
	}
	
	.student-badge {
	background: linear-gradient(90deg, #28a745, #20c997);
	color: #fff;
	padding: 12px 25px;
	border-radius: 30px;
	font-weight: 600;
	margin-bottom: 25px;
	}
	
	.btn-primary {
	background: linear-gradient(45deg, #007bff, #0056b3);
	border: none;
	}
	
	.form-control:focus, .form-select:focus {
	border-color: #1E90FF;
	box-shadow: 0 0 6px rgba(30, 144, 255, 0.4);
	}
	</style>
    
<!-- ================= FOOTER ================= -->
<footer class="footer-main">
  <div class="container footer-container">

    <!-- Left -->
    <div class="footer-left">
      <span>
        © <script>document.write(new Date().getFullYear());</script>
        <a href="https://rmitgroupsorg.infinityfree.me/">RMIT 
           <span class="footer-highlight">GROUP OF INSTITUTIONS</span>
        </a>
        – Student Management Portal. All rights reserved.
      </span>
    </div>

    <!-- Right -->
    <div class="footer-right">
      <span class="dev-by">Developed by</span>

      <img src="/images/trinitywebedge.png" alt="TrinityWebEdge Logo" class="dev-logo">

      <a href="https://trinitywebedge.infinityfree.me" target="_blank" class="dev-link">
        TrinityWebEdge
      </a>
    </div>

  </div>
</footer>
<!-- ================= FOOTER END ================= -->
    
	<script>
	document.getElementById('role').addEventListener('change', function() {
  	const semesterBlock = document.getElementById('semesterBlock');
  	const designationBlock = document.getElementById('designationBlock');

  	if (this.value === 'student') {
    	semesterBlock.style.display = 'block';
    	designationBlock.style.display = 'none';
  	} else {
    	semesterBlock.style.display = 'none';
    	designationBlock.style.display = 'block';
  		}
	});

	</script>
    
    <script>
	const instituteMap = {
  	HIT: ["Diploma in Civil Engineering", "Diploma in Mechanical Engineering", "Diploma in Electrical Engineering", "Diploma Computer Science Engineering"],
  	RMIT: ["Bachelor in Computer Application", "Bachelor in Electronic Science"],
  	RMITC: ["Fitter", "Electrician", "Electronics Mechanic", "Welder"],
  	CPS: ["Primary", "Middle School", "High School"]
	};

	document.getElementById("institute").addEventListener("change", function () {
  	const dept = document.getElementById("department");
  	dept.innerHTML = '<option selected disabled>Select Department</option>';
  	instituteMap[this.value].forEach(d => {
    const o = document.createElement("option");
    o.value = d;
    o.textContent = d;
    dept.appendChild(o);
  	});
	});
	</script>
    
	<script>
	const semesterMap = {
	HIT: {
		"Diploma in Civil Engineering": ["1st Sem","2nd Sem","3rd Sem","4th Sem","5th Sem","6th Sem"],
		"Diploma in Mechanical Engineering": ["1st Sem","2nd Sem","3rd Sem","4th Sem","5th Sem","6th Sem"],
		"Diploma in Electrical Engineering": ["1st Sem","2nd Sem","3rd Sem","4th Sem","5th Sem","6th Sem"],
		"Diploma Computer Science Engineering": ["1st Sem","2nd Sem","3rd Sem","4th Sem","5th Sem","6th Sem"]
	},
	RMIT: {
		"Bachelor in Computer Application": ["1st Sem","2nd Sem","3rd Sem","4th Sem","5th Sem","6th Sem"],
		"Bachelor in Electronic Science": ["1st Sem","2nd Sem","3rd Sem","4th Sem","5th Sem","6th Sem"]
	},
	RMITC: {
		"Fitter": ["1st Year","2nd Year"],
		"Electrician": ["1st Year","2nd Year"],
		"Electronics Mechanic": ["1st Year","2nd Year"],
		"Welder": ["1st Year","2nd Year"]
	},
	CPS: {
		"Primary": ["Nursery","LKG","UKG","STD I","STD II","STD III","STD IV","STD V"],
		"Middle School": ["STD VI","STD VII","STD VIII"],
		"High School": ["STD IX","STD X"]
	}
	};
	
	function updateSemester() {
	const institute = document.getElementById("institute").value;
	const dept = document.getElementById("department").value;
	const sem = document.getElementById("semester");
	
	sem.innerHTML = '<option selected disabled>Select Semester</option>';
	
	if (!semesterMap[institute] || !semesterMap[institute][dept]) return;
	
	semesterMap[institute][dept].forEach(s => {
		const o = document.createElement("option");
		o.value = s;
		o.textContent = s;
		sem.appendChild(o);
	});
	}
	
	document.getElementById("institute").addEventListener("change", updateSemester);
	document.getElementById("department").addEventListener("change", updateSemester);
	</script>
    
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    
</body>
    
</html>
