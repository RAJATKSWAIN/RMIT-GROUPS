<?php
session_start();
$_SESSION['csrf'] ??= bin2hex(random_bytes(32));

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: /rmit-smartcampus/auth/login.php");
    exit;
}

$dbConfig = [
  'host'=>'sql100.infinityfree.com',
  'user'=>'if0_40697103',
  'pass'=>'rmitgroups123',
  'name'=>'if0_40697103_rmit_smartcampus'
];

$conn = new mysqli($dbConfig['host'],$dbConfig['user'],$dbConfig['pass'],$dbConfig['name']);
if($conn->connect_error) die("DB Error");
$conn->set_charset("utf8");

$adminId = $_SESSION['user']['id'];

/* ================= ADMIN PROFILE ================= */
$stmt = $conn->prepare("
SELECT u.full_name,u.email,u.institute_code,i.name institute_name
FROM users u
LEFT JOIN institutes i ON i.code=u.institute_code
WHERE u.id=?
");
$stmt->bind_param("i",$adminId);
$stmt->execute();
$profile = $stmt->get_result()->fetch_assoc();
if(!$profile) die("Admin not found");

$instituteCd = $profile['institute_code'];
$institute   = $profile['institute_name'] ?? 'Unknown Institute';

/* ================= THEME ================= */
$themes = [
  'HIT'=>['color'=>'#1e3c72','logo'=>'/rmit-smartcampus/assets/img/hit-logo.png'],
  'RMIT'=>['color'=>'#667eea','logo'=>'/rmit-smartcampus/assets/img/rmit-logo.png'],
  'RMITC'=>['color'=>'#11998e','logo'=>'/rmit-smartcampus/assets/img/rmitc-logo.png'],
  'CPS'=>['color'=>'#f46b45','logo'=>'/rmit-smartcampus/assets/img/cps-logo.png'],
];
$theme = $themes[$instituteCd] ?? ['color'=>'#667eea','logo'=>'/rmit-smartcampus/assets/img/default-logo.png'];

/* ================= PROGRAM MAP BY INSTITUTE ================= */
$PROGRAM_MAP = [];

switch ($instituteCd) {
    case 'HIT': // Diploma
        $PROGRAM_MAP = [
            "DME"=>"Diploma Mechanical",
            "DEE"=>"Diploma Electrical",
            "DCE"=>"Diploma Civil",
            "DCSE"=>"Diploma CSE"
        ];
        break;

    case 'RMIT': // Degree
        $PROGRAM_MAP = [
            "BCA"=>"Bachelor of Computer Application",
            "BES"=>"Bachelor of Electronics Science"
        ];
        break;

    case 'RMITC': // Trades
        $PROGRAM_MAP = [
            "FIT"=>"Fitter",
            "ELC"=>"Electrician",
            "EM"=>"Electronics Mechanic",
            "WLD"=>"Welder"
        ];
        break;

    case 'CPS': // School
        $PROGRAM_MAP = [
            "NUR"=>"Nursery",
            "LKG"=>"LKG",
            "UKG"=>"UKG",
            "STD1"=>"STD-I",
            "STD2"=>"STD-II",
            "STD3"=>"STD-III",
            "STD4"=>"STD-IV",
            "STD5"=>"STD-V",
            "STD6"=>"STD-VI",
            "STD7"=>"STD-VII",
            "STD8"=>"STD-VIII",
            "STD9"=>"STD-IX",
            "STD10"=>"STD-X"
        ];
        break;
}

$SEMESTER_LIMITS = [
    'HIT'   => 6,
    'RMIT'  => 6,
    'RMITC'=> 2,
    'CPS'   => 12
];
$maxSemester = $SEMESTER_LIMITS[$instituteCd] ?? 6;

/* ================= ATOMIC ROLL NUMBER GENERATOR ================= */
function generateRollNo($conn, $institute, $program, $semester) {

    /* ===== SEMESTER → JOIN YEAR LOGIC ===== */
    $currentYear = (int)date('Y');

    if ($semester <= 2)        $joinYear = $currentYear;
    elseif ($semester <= 4)    $joinYear = $currentYear - 1;
    else                       $joinYear = $currentYear - 2;

    $yy = substr((string)$joinYear, -2);   // e.g. "26"

    /* ===== FETCH & LOCK MASTER PREFIX ROW ===== */
    $conn->begin_transaction();

    $stmt = $conn->prepare("
        SELECT prefix, start_value, current_value
        FROM roll_sequences
        WHERE institute_code=? AND program=?
        FOR UPDATE
    ");
    $stmt->bind_param("ss", $institute, $program);
    $stmt->execute();
    $stmt->bind_result($basePrefix, $start, $current);

    if ($stmt->fetch()) {

        // Compute next sequence
        $next = ($current == 0 ? $start : $current + 1);
        $stmt->close();

        // Update sequence atomically
        $upd = $conn->prepare("
            UPDATE roll_sequences
            SET current_value=?
            WHERE institute_code=? AND program=?
        ");
        $upd->bind_param("iss", $next, $institute, $program);
        $upd->execute();
        $upd->close();

    } else {

        // Safety fallback: auto-create master row if missing
        $stmt->close();

        $basePrefix = "$institute-$program";
        $start = 1;
        $next  = 1;

        $ins = $conn->prepare("
            INSERT INTO roll_sequences
            (institute_code, program, prefix, start_value, current_value)
            VALUES (?, ?, ?, 1, 1)
        ");
        $ins->bind_param("sss", $institute, $program, $basePrefix);
        $ins->execute();
        $ins->close();
    }

    $conn->commit();

    /* ===== FINAL FORMAT: RMIT-BCA-YY001 ===== */
    $rollNo = $basePrefix . '-' . $yy . str_pad($next, 3, "0", STR_PAD_LEFT);

    return $rollNo;
}


/* ================= CSV TEMPLATE DOWNLOAD ================= */
if (isset($_GET['download_template'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="student_upload_template.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Name','Email','Phone','Program','Semester','Section','Gender', 'Dob','Address' ]);
    fclose($out);
    exit;
}

/* ================= FAILED CSV DOWNLOAD ================= */
if (isset($_GET['download_failed']) && isset($_SESSION['failed_rows'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="failed_students.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Name','Email','Phone','Program','Semester','Section','Gender','Dob','Address','Error']);
    foreach ($_SESSION['failed_rows'] as $row) {
        fputcsv($out, $row);
    }
    fclose($out);
    exit;
}

/* ================= INSERT LOGIC ================= */

$summary = [
    'total'   => 0,
    'success' => 0,
    'failed'  => 0
];

$failed_rows = [];

if (isset($_POST['upload']) && isset($_POST['students'])) {

    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
        die("Invalid CSRF token");
    }

    $conn->begin_transaction();

    try {

        foreach ($_POST['students'] as $row) {

            $summary['total']++;

            $name     = trim($row['name'] ?? '');
            $email    = trim($row['email'] ?? '');
            $phone    = trim($row['phone'] ?? '');
            $program  = trim($row['program'] ?? '');
            $semester = (int)($row['semester'] ?? 0);
            $section  = trim($row['section'] ?? '');

            $genderRaw = trim($row['gender'] ?? '');

			$gender = ucfirst(strtolower($genderRaw));

			if ($gender === 'M') $gender = 'Male';
			if ($gender === 'F') $gender = 'Female';

            $dob      = !empty($row['dob']) ? $row['dob'] : null;
            $address  = trim($row['address'] ?? '');

            /* ===== VALIDATION ===== */
            if (!array_key_exists($program, $PROGRAM_MAP)) {
                $summary['failed']++;
                $failed_rows[] = [...$row, 'Invalid program for this institute'];
                continue;
            }

            if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $summary['failed']++;
                $failed_rows[] = [...$row, 'Invalid name or email'];
                continue;
            }

            if (!preg_match('/^[0-9]{10}$/', $phone)) {
                $summary['failed']++;
                $failed_rows[] = [...$row, 'Invalid phone number'];
                continue;
            }

            if ($semester < 1 || $semester > $maxSemester) {
                $summary['failed']++;
                $failed_rows[] = [...$row, "Semester must be 1–$maxSemester"];
                continue;
            }

            $allowedGender = ['Male','Female','Other'];
			if (!in_array($gender, $allowedGender, true)) {
    			$summary['failed']++;
    			$failed_rows[] = [...$row, "Invalid gender: $genderRaw"];
    			continue;
			}


            if ($dob && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dob)) {
                $summary['failed']++;
                $failed_rows[] = [...$row, 'Invalid DOB format (YYYY-MM-DD)'];
                continue;
            }

            /* ===== DUP EMAIL ===== */
            $chk = $conn->prepare("SELECT id FROM users WHERE email=?");
            $chk->bind_param("s", $email);
            $chk->execute();
            $chk->store_result();

            if ($chk->num_rows > 0) {
                $summary['failed']++;
                $failed_rows[] = [...$row, 'Duplicate email'];
                $chk->close();
                continue;
            }
            $chk->close();

            /* ===== DUP PHONE ===== */
            $chk = $conn->prepare("SELECT id FROM students WHERE phone=?");
            $chk->bind_param("s", $phone);
            $chk->execute();
            $chk->store_result();

            if ($chk->num_rows > 0) {
                $summary['failed']++;
                $failed_rows[] = [...$row, 'Duplicate phone number'];
                $chk->close();
                continue;
            }
            $chk->close();

            /* ===== CREATE USER ===== */
            $pass = password_hash("pass@123", PASSWORD_DEFAULT);
            $role = 'student';

            $u = $conn->prepare("
                INSERT INTO users
                (institute_code, role, email, password_hash, full_name, status)
                VALUES (?,?,?,?,?,'active')
            ");
            $u->bind_param(
                "sssss",
                $instituteCd,
                $role,
                $email,
                $pass,
                $name
            );

            if (!$u->execute()) {
                throw new Exception("User insert failed: $email");
            }

            $uid = $u->insert_id;
            $u->close();

            /* ===== STUDENT ===== */
            $roll = generateRollNo($conn, $instituteCd, $program, $semester);

            $s = $conn->prepare("
                INSERT INTO students
                (user_id, roll_no, program, semester, section, phone, gender, dob, address)
                VALUES (?,?,?,?,?,?,?,?,?)
            ");

            $s->bind_param(
                "ississsss",
                $uid,
                $roll,
                $program,
                $semester,
                $section,
                $phone,
                $gender,
                $dob,
                $address
            );

            if (!$s->execute()) {
                throw new Exception("Student insert failed: $email");
            }

            $s->close();

            $summary['success']++;
        }

        $conn->commit();
        $_SESSION['failed_rows'] = $failed_rows;

    } catch (Exception $e) {

        $conn->rollback();

        $summary['failed'] = $summary['total'] - $summary['success'];
        $failed_rows[] = ['error' => $e->getMessage()];

        $_SESSION['failed_rows'] = $failed_rows;
    }

    /* ===== LOG ===== */
    $log = $conn->prepare("
        INSERT INTO upload_logs
        (admin_id, institute_code, total, success, failed)
        VALUES (?,?,?,?,?)
    ");
    $log->bind_param(
        "issii",
        $adminId,
        $instituteCd,
        $summary['total'],
        $summary['success'],
        $summary['failed']
    );
    $log->execute();
    $log->close();
}

?>

<!DOCTYPE html>

<html lang="en">
    
<head>
    
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
		
	<title>RMIT SmartCampus | Admin Dashboard | <?= htmlspecialchars($instituteCd) ?></title>
		
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
	
	<!-- Google Fonts & Icons -->
	<link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;700&display=swap" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
	
	<!-- Bootstrap -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="/rmit-smartcampus/assets/css/footer.css?v=1.0" rel="stylesheet">
    
	<style>
	:root {
	--primary-color: <?= $theme['color'] ?>;
	--primary-gradient: linear-gradient(135deg, var(--primary-color), #764ba2);
	--success-gradient: linear-gradient(135deg,#4facfe,#00f2fe);
	--warning-gradient: linear-gradient(135deg,#43e97b,#38f9d7);
	--danger-gradient: linear-gradient(135deg,#ff512f,#dd2476);
	--light-bg: #f8f9fa;
	--card-radius: 12px;
	}
	
	/* Reset & Layout */
	body {
	font-family: 'Raleway', sans-serif;
	background-color: var(--primary-color);
	margin: 0;
	padding: 0;
	display: flex;
	min-height: 100vh;
	flex-direction: column;
	}
		
	/* Dashboard Layout */
	.dashboard {
	display: flex;
	gap: 20px;
	padding: 20px;
	margin-top: 80px;
	}
	
	.main {
	flex: 1;
	display: flex;
	flex-direction: column;
	gap: 20px;
	margin-top:80px;
	}
	
	/* Navbar */
	.navbar {
	background: #fff;
	box-shadow: 0 2px 8px rgba(0,0,0,0.1);
	padding: 0.5rem 2rem;
	}
	.navbar-brand img {
	height: 40px;
	}
	.navbar .btn-logout {
	background: none;
	border: 1px solid var(--danger-gradient);
	color: var(--danger-gradient);
	border-radius: 6px;
	transition: 0.3s;
	}
	.navbar .btn-logout:hover {
	background: var(--danger-gradient);
	color: #fff;
	}
	.card{border-radius:14px}
	
	/* Cards */
	.card {
	border-radius: var(--card-radius);
	padding: 15px;
	color: #fff;
	gap: 20px;
	margin:25px;
	margin-top:90px;
	text-align: center;
	font-weight: 600;
	box-shadow: 0 4px 15px rgba(0,0,0,0.08);
	transition: transform 0.2s;
	}
	.card:hover {
	transform: translateY(-3px);
	}
	
	.preview-table th{background:#f5f5f5}
		
	.alert-success {
			border-radius: 8px;
			padding: 15px 20px;
			background: var(--success-gradient);
			margin: 25px;
			margin-top:80px;
			box-shadow: 0 2px 6px rgba(0,0,0,0.1);
			transition: all 0.3s ease;
		}
	
	</style>
	
</head>
    

<body>

<!-- =================Start Navbar Header================= -->
<nav class="navbar navbar-expand-lg navbar-light fixed-top" >
  <div class="container justify-content-center">
    <a class="navbar-brand d-flex align-items-center" href="/rmit-smartcampus/index.php">
      <img src="/rmit-smartcampus/images/rmitsclogo1.png" alt="RMIT Logo" style="height:45px; width:auto; "> 
        <span style="border-left:5px solid #ff2f00; height:30px; margin:0 20px; display:inline-block;"></span>
      <img src="<?= $theme['logo'] ?>" alt="Institute Logo" style="height:35px;">      
    </a>
      
    <!-- Push logout to right -->
    <a class="ms-auto">
      <a href="/rmit-smartcampus/auth/logout.php" class="btn btn-logout btn-sm">
    	<i class="fas fa-sign-out-alt"></i> Logout
  	  </a>
    </a>      
  </div>
</nav>
<!-- =================End Navbar Header================= -->
   
<!-- ================= Start of Bulk Student Upload ================= -->  
<div class="card">
  <div class="card-header bg-primary text-white">
    <h5 class="mb-0">Bulk Student Upload (CSV)</h5>
  </div>
   
  <!-- DOWNLOAD CSV TEMPLATE BUTTON -->  
  <div class="small text-muted mb-2"> Default password for all students: <b>pass@123</b> 
      <a href="?download_template=1" class="btn btn-sm btn-outline-secondary float-end">Download CSV</a> 
  </div>

  <div class="card-body">

    <!-- 🔽 START FORM (IMPORTANT) -->
    <form method="post">

      <!-- CSV FILE -->
      <label class="form-label fw-bold">Upload CSV File</label>
      <input type="file" id="csvFile" accept=".csv" class="form-control" required>
      <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">


      <!-- PREVIEW BUTTON -->
      <button type="button"
              onclick="previewCSV()"
              class="btn btn-secondary mt-3">
        🔍 Preview CSV
      </button>
        
      

      <!-- PREVIEW TABLE -->
      <div class="table-responsive mt-3">
          
       <small class="text-muted d-block mt-2">
    		ℹ Roll numbers shown are <b>provisional</b>.
    		Final roll numbers will be assigned during upload.
  		</small>
          
        <table class="table table-bordered table-sm" id="previewTable">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Full Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Program</th>
              <th>Semester</th>
              <th>Section</th>
              <th>Roll No</th>
              <th>Remarks</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
            
<!--========== Pagination Rounded Pills (Modern UI) ============-->  
<div class="d-flex justify-content-center align-items-center mt-3">

  <button type="button"
          class="btn btn-light border rounded-pill me-3"
          onclick="prevPage()">
    ⬅ Prev
  </button>

  <span id="pageInfo"
        class="badge bg-primary fs-6 px-4 py-2">
    Page 1 of 1
  </span>

  <button type="button"
          class="btn btn-light border rounded-pill ms-3"
          onclick="nextPage()">
    Next ➡
  </button>

</div>
<!--========== Pagination Rounded Pills (Modern UI) ============-->

  <!-- Progress -->
  <div class="mt-3">
    <progress id="uploadProgress" value="0" max="100" style="width:100%; display:none;"></progress>
  </div>
          
      </div>

      <!-- FINAL SUBMIT -->
      <button type="submit"
              name="upload"
              class="btn btn-success mt-3">
        ⬆ Final Upload
      </button>

    </form>
    <!-- 🔼 END FORM -->

  </div>
        
  <div style="text-align: center; margin-top: 15px;">
  		<a href="/rmit-smartcampus/admin/dashboard.php" class="btn btn-outline-primary">Return to Dashboard</a>
  </div>
        
</div>
<!-- ================= End of Bulk Student Upload ================= -->

</div>
    
	<style>
    .alert {
        border-radius: 12px;
        padding: 15px 20px;
        margin: 25px;
        font-family: "Segoe UI", Arial, sans-serif;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    
    .alert b {
        font-size: 1.1rem;
        color: #333;
    }

    .alert-info {
        background: #e8f4fd;
        border-left: 5px solid #2196f3;
        color: #0d47a1;
    }

    .alert-danger {
        background: #fdecea;
        border-left: 5px solid #f44336;
        color: #b71c1c;
    }

    .btn {
        display: inline-block;
        padding: 10px 30px;
        border-radius: 12px;
        text-decoration: none;
        font-size: 0.95rem;
        font-weight: 600;
        transition: background 0.3s ease, transform 0.2s ease;
        cursor: pointer;
    }

    .btn-download {
        background: var(--warning-gradient);
        color: #fff;
        border: none;
        text-align: center;
    }

    .btn-download:hover {
        background: var(--danger-gradient);
        transform: translateY(-2px);
    }

    .btn-container {
        text-align: center; /* center the button */
        margin: 20px 0;
    }

    .mt-2 { margin-top: 10px; }
    .mt-3 { margin-top: 15px; }
	</style>

	<?php if($summary['total'] > 0): ?>
	<div class="alert alert-info mt-3">
		<b>Upload Summary</b><br>
		Total Records: <?= $summary['total'] ?><br>
		Successfully Inserted: <?= $summary['success'] ?><br>
		Failed Records: <?= $summary['failed'] ?>
	</div>
	<?php endif; ?>
	
	<?php if(!empty($failed_rows)): ?>
	<div class="alert alert-danger mt-2">
		<b>Failure Reasons:</b><br>
		<?php foreach ($failed_rows as $r): ?>
			<?= htmlspecialchars(end($r)) ?><br>
		<?php endforeach; ?>
	</div>
	
	<div class="btn-container">
		<a href="?download_failed=1" class="btn btn-download">
			Download Failed Rows CSV
		</a>
	</div>
	<?php endif; ?>
   
</div>
   
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

<!--====== ALL JS Script ======-->
<script>
const INSTITUTE_CODE = "<?= $instituteCd ?>";
</script>

<script>
const ALLOWED_PROGRAMS = <?= json_encode(array_keys($PROGRAM_MAP)) ?>;
</script>


<!--================= Updated JS Script =================-->
<script>
let csvData = [];
let currentPage = 1;
const rowsPerPage = 50;
    
function injectHiddenRows() {
    const form = document.querySelector("form");

    // Remove previous hidden rows
    document.querySelectorAll(".hidden-row").forEach(e => e.remove());

    csvData.forEach((r, i) => {
        for (const k in r) {
            const input = document.createElement("input");
            input.type = "hidden";
            input.name = `students[${i}][${k}]`;
            input.value = r[k];
            input.classList.add("hidden-row");
            form.appendChild(input);
        }
    });
}


/* ===== PREVIEW CSV ===== */
function previewCSV() {
    const file = document.getElementById('csvFile').files[0];
    if (!file) return alert("Select CSV file");

    if (!file.name.endsWith(".csv")) {
        alert("Only CSV allowed");
        return;
    }

    const reader = new FileReader();
    reader.onload = e => {
        csvData = parseCSV(e.target.result);
        if (csvData.length === 0) {
            alert("Invalid or empty CSV");
            return;
        }
        currentPage = 1;
        renderPage();
    };
    reader.readAsText(file);
}

/* ===== CSV PARSER ===== */
function parseCSV(text) {
    const lines = text.trim().split(/\r?\n/);
    const rows = [];

    for (let i = 1; i < lines.length; i++) {
        const c = lines[i].match(/(".*?"|[^",]+)(?=\s*,|\s*$)/g);
        if (!c || c.length < 9) continue;

        const v = c.map(x => x.replace(/^"|"$/g,'').trim());

        rows.push({
            name: v[0],
            email: v[1],
            phone: v[2],
            program: v[3],
            semester: v[4],
            section: v[5],
            gender: v[6],
            dob: v[7],
            address: v[8]
        });
    }
    return rows;
}

/* ===== VALIDATION ===== */
function validate(row) {
    if (!row.name) return "Name required";
    if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(row.email)) return "Invalid email";
    if (!/^[0-9]{10}$/.test(row.phone)) return "Phone must be 10 digits";
    if (!ALLOWED_PROGRAMS.includes(row.program)) return "Invalid program";
    if (isNaN(row.semester) || row.semester < 1 || row.semester > <?= $maxSemester ?>)
        return "Invalid semester";
    if (!["Male","Female","Other"].includes(row.gender))
        return "Invalid gender";
    if (row.dob && !/^\d{4}-\d{2}-\d{2}$/.test(row.dob))
        return "DOB must be YYYY-MM-DD";
    return "OK";
}

/* ===== JOIN YEAR LOGIC ===== */
function joinYear(semester) {
    const y = new Date().getFullYear();
    if (semester <= 2) return y;
    if (semester <= 4) return y - 1;
    return y - 2;
}

/* ===== ROLL PREVIEW ===== */
/*function previewRoll(row, index) {
    const yy = String(joinYear(row.semester)).slice(-2);
    return `${INSTITUTE_CODE}-${row.program}-${yy}${String(index+1).padStart(3,'0')}`;
}
*/
    
let rollSeedCache = {};     // program_semester → base roll from DB
let rollRowOffsets = {};   // rowIndex → offset

async function getBaseRoll(program, semester) {
    const key = program + "_" + semester;

    if (!rollSeedCache[key]) {
        const url = `/rmit-smartcampus/admin/api/get_next_roll.php`
            + `?institute=${INSTITUTE_CODE}`
            + `&program=${encodeURIComponent(program)}`
            + `&semester=${encodeURIComponent(semester)}`;

        const res = await fetch(url);
        const data = await res.json();

        rollSeedCache[key] = data.roll;  // e.g. RMIT-BCA-26001
    }

    return rollSeedCache[key];
}

function incrementRoll(roll, offset) {
    const match = roll.match(/^(.*-)(\d{2})(\d{3})$/);
    if (!match) return roll;

    const prefix = match[1];
    const yy     = match[2];
    const seq    = parseInt(match[3], 10) + offset;

    return prefix + yy + String(seq).padStart(3, '0');
}

async function getRowRoll(row, rowIndex) {
    const key = row.program + "_" + row.semester;

    if (rollRowOffsets[rowIndex] === undefined) {
        const sameGroupRows = csvData.filter(r =>
            r.program === row.program && r.semester == row.semester
        );

        const position = sameGroupRows.findIndex(r => r === row);
        rollRowOffsets[rowIndex] = position;
    }

    const base = await getBaseRoll(row.program, row.semester);
    return incrementRoll(base, rollRowOffsets[rowIndex]);
}
    
    

/* ===== RENDER PAGE ===== */
function renderPage() {
    const tbody = document.querySelector("#previewTable tbody");
    tbody.innerHTML = "";

    const start = (currentPage - 1) * rowsPerPage;
    const end = Math.min(start + rowsPerPage, csvData.length);

    for (let i = start; i < end; i++) {
        const r = csvData[i];
        const remark = validate(r);
        
        //const roll = remark === "OK" ? previewRoll(r, i) : "-";
        
		let roll = "-";

		if (remark === "OK") {

    		roll = "Loading...";

    		getRowRoll(r, i).then(realRoll => {
        		const cell = document.getElementById(`roll-${i}`);
        		if (cell) cell.innerText = realRoll;
    		});
		}

        const tr = document.createElement("tr");
        if (remark !== "OK") tr.classList.add("table-danger");

        tr.innerHTML = `
        <td>${i+1}</td>
        <td>
		<input class="form-control"
         name="students[${i}][name]"
         value="${r.name}"
         oninput="updateRow(${i}, 'name', this.value)">
		</td>

		<td>
		<input class="form-control"
				name="students[${i}][email]"
				value="${r.email}"
				oninput="updateRow(${i}, 'email', this.value)">
		</td>
		
		<td>
		<input class="form-control"
				name="students[${i}][phone]"
				value="${r.phone}"
				oninput="updateRow(${i}, 'phone', this.value)">
		</td>
		
		<td>
		<select class="form-select"
				name="students[${i}][program]"
				onchange="updateRow(${i},'program',this.value)">
		${ALLOWED_PROGRAMS.map(p =>
			`<option value="${p}" ${r.program===p?'selected':''}>${p}</option>`
		).join("")}
		</select>
		</td>
		
		
		<td>
		<input class="form-control"
				name="students[${i}][semester]"
				value="${r.semester}"
				oninput="updateRow(${i}, 'semester', this.value)">
		</td>
		
		<td>
		<input class="form-control"
				name="students[${i}][section]"
				value="${r.section}"
				oninput="updateRow(${i}, 'section', this.value)">
		</td>

        <td>
  			<span class="badge bg-info" id="roll-${i}">
    			${roll}
  			</span>
		</td>

        <td>
            <span class="badge ${remark==='OK'?'bg-success':'bg-danger'}">
                ${remark}
            </span>
        </td>
        `;
        tbody.appendChild(tr);
    }

    document.getElementById("pageInfo").innerText =
        `Page ${currentPage} of ${Math.ceil(csvData.length / rowsPerPage)}`;
}
    
function updateRow(index, field, value) {

    csvData[index][field] = value;

    if (field === "program" || field === "semester") {
        rollSeedCache = {};
        rollRowOffsets = {};
    }

    renderPage();
}

    
/* ===== PAGINATION ===== */
function nextPage() {
    if (currentPage * rowsPerPage < csvData.length) {
        currentPage++;
        renderPage();
    }
}
function prevPage() {
    if (currentPage > 1) {
        currentPage--;
        renderPage();
    }
}

  
function hasErrors() {
    return csvData.some(r => validate(r) !== "OK");
}
    
/* ===== SUBMIT ALL ROWS AND PROGRESS BAR ===== */
document.querySelector("form").onsubmit = () => {

    if (hasErrors()) {
        alert("Please fix all errors before uploading.");
        return false;
    }

    injectHiddenRows(); // submit all rows

    const bar = document.getElementById("uploadProgress");
    bar.style.display = "block";
    let p = 0;
    const i = setInterval(() => {
        p += 10;
        bar.value = p;
        if (p >= 100) clearInterval(i);
    }, 150);

    return true;
};
</script>

<script>
let rollSeedCache = {};     // program_semester → base roll from DB
let rollRowOffsets = {};   // rowIndex → offset

async function getBaseRoll(program, semester) {
    const key = program + "_" + semester;

    if (!rollSeedCache[key]) {
        const url = `/rmit-smartcampus/admin/api/get_next_roll.php`
            + `?institute=${INSTITUTE_CODE}`
            + `&program=${encodeURIComponent(program)}`
            + `&semester=${encodeURIComponent(semester)}`;

        const res = await fetch(url);
        const data = await res.json();

        rollSeedCache[key] = data.roll;  // e.g. RMIT-BCA-26001
    }

    return rollSeedCache[key];
}

function incrementRoll(roll, offset) {
    const match = roll.match(/^(.*-)(\d{2})(\d{3})$/);
    if (!match) return roll;

    const prefix = match[1];
    const yy     = match[2];
    const seq    = parseInt(match[3], 10) + offset;

    return prefix + yy + String(seq).padStart(3, '0');
}

async function getRowRoll(row, rowIndex) {
    const key = row.program + "_" + row.semester;

    if (rollRowOffsets[rowIndex] === undefined) {
        const sameGroupRows = csvData.filter(r =>
            r.program === row.program && r.semester == row.semester
        );

        const position = sameGroupRows.findIndex(r => r === row);
        rollRowOffsets[rowIndex] = position;
    }

    const base = await getBaseRoll(row.program, row.semester);
    return incrementRoll(base, rollRowOffsets[rowIndex]);
}
</script>



</body>

</html>