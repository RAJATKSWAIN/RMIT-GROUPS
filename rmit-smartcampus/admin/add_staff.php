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

/* ================= STAFF DEPARTMENT MAP ================= */
$DEPARTMENT_MAP = [
    'RMIT' => [
        'BCA' => 'RMIT – BCA Department',
        'BES' => 'RMIT – BES Department'
    ],
    'HIT' => [
        'DME' => 'HIT - Mechanical Department',
        'DEE' => 'HIT - Electrical Department',
        'DCE' => 'HIT - Civil Department',
        'DCSE'=> 'HIT - Computer Science Department'
    ],
    'RMITC' => [
        'FIT' => 'RMITC - Fitter Department',
        'ELC' => 'RMITC - Electrician Department',
        'EM'  => 'RMITC - Electronics Department',
        'WLD' => 'RMITC - Welder Department'
    ]
];

/* ================= STAFF DESIGNATIONS ================= */
$DESIGNATIONS = [
    'Assistant Lecturer',
    'Lecturer',
    'Trainer',
    'Placement Officer'
];

/* ================= STAFF ID GENERATOR ================= */
function generateStaffId($conn, $institute, $deptLabel) {

    $prefix = strtoupper("$institute-");

    $stmt = $conn->prepare("
        SELECT COUNT(*) 
        FROM staff 
        WHERE department = ?
    ");
    $stmt->bind_param("s", $deptLabel);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    return $prefix . str_pad($count + 1, 3, "0", STR_PAD_LEFT);
}

/* ================= CSV TEMPLATE DOWNLOAD ================= */
if (isset($_GET['download_template'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="staff_upload_template.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, [
        'Name',
        'Email',
        'Phone',
        'Department',
        'Designation',
        'Gender',
        'DOB',
        'Address'
    ]);
    fclose($out);
    exit;
}


/* ================= FAILED CSV DOWNLOAD ================= */
if (isset($_GET['download_failed']) && isset($_SESSION['failed_rows'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="failed_staff.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, [
        'Name',
        'Email',
        'Phone',
        'Program',
        'Department',
        'Designation',
        'Gender',
        'DOB',
        'Address',
        'error'
    ]);
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

if (isset($_POST['upload']) && isset($_POST['staff'])) {

    if ($_POST['csrf'] !== $_SESSION['csrf']) {
        die("Invalid CSRF token");
    }

    $conn->begin_transaction();

    try {

        foreach ($_POST['staff'] as $row) {

            $summary['total']++;

            $name   = trim($row['name'] ?? '');
            $email  = trim($row['email'] ?? '');
            $phone  = trim($row['phone'] ?? '');
            $dept   = trim($row['department'] ?? '');
            $desig  = trim($row['designation'] ?? '');
            $gender = trim($row['gender'] ?? '');
            $dob    = $row['dob'] ?: null;
            $addr   = trim($row['address'] ?? '');

            /* ===== VALIDATION ===== */
            if (!$name || !filter_var($email,FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid name/email: $email");
            }

            if (!preg_match('/^[0-9]{10}$/',$phone)) {
                throw new Exception("Invalid phone: $phone");
            }

            if (!in_array($dept, array_values($DEPARTMENT_MAP[$instituteCd] ?? []))) {
                throw new Exception("Invalid department: $dept");
            }

            if (!in_array($desig,$DESIGNATIONS)) {
                throw new Exception("Invalid designation: $desig");
            }

            /* ===== DUP EMAIL ===== */
            $chk = $conn->prepare("SELECT id FROM users WHERE email=?");
            $chk->bind_param("s",$email);
            $chk->execute();
            $chk->store_result();

            if ($chk->num_rows > 0) {
                throw new Exception("Duplicate email: $email");
            }
            $chk->close();

            /* ===== CREATE USER ===== */
            $pass = password_hash("pass@123", PASSWORD_DEFAULT);
            $role = 'staff';

            $u = $conn->prepare("
                INSERT INTO users
                (institute_code, role, email, password_hash, full_name, status)
                VALUES (?,?,?,?,?,'active')
            ");
            $u->bind_param("sssss",
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

            /* ===== STAFF ===== */
            $staffId = generateStaffId($conn,$instituteCd,$dept);

            $s = $conn->prepare("
                INSERT INTO staff
                (user_id, department, designation, phone, gender, dob, address)
                VALUES (?,?,?,?,?,?,?)
            ");

            $s->bind_param(
                "issssss",
                $uid,
                $dept,
                $desig,
                $phone,
                $gender,
                $dob,
                $addr
            );

            if (!$s->execute()) {
                throw new Exception("Staff insert failed: $email");
            }

            $s->close();

            $summary['success']++;
        }

        $conn->commit();

    } catch (Exception $e) {

        $conn->rollback();

        $summary['failed'] = $summary['total'] - $summary['success'];
        $failed_rows[] = [
            'error' => $e->getMessage()
        ];

        $_SESSION['failed_rows'] = $failed_rows;
    }
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
	<meta name="keywords" content="RMIT SmartCampus, RMIT Group of Institutions, Academic Management System, Student Portal, Faculty Portal, Administration Portal, RMIT Registration, RMIT Admission, RMIT 			Online 	Services">
	
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
    <h5 class="mb-0">Bulk Staff Upload (CSV)</h5>
  </div>
   
  <!-- DOWNLOAD CSV TEMPLATE BUTTON -->  
  <div class="small text-muted mb-2"> Default password for all Staff : <b>pass@123</b> 
      <a href="?download_template=1" class="btn btn-sm btn-outline-secondary float-end">Download Template CSV</a> 
  </div>

  <div class="card-body">

    <!-- 🔽 START FORM (IMPORTANT) -->
    <form method="post" enctype="multipart/form-data">

      <!-- CSV FILE -->
      <label class="form-label fw-bold">Upload CSV File</label>
      <input type="file"  id="csvFile"  name="csvFile"  accept=".csv" class="form-control" required>
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
    			<th>Department</th>
    			<th>Designation</th>
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


<!--================= Updated JS Script =================-->
<script>
let csvData = [];
let currentPage = 1;
const rowsPerPage = 50;

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
        if (!c || c.length < 8) continue;

        const v = c.map(x => x.replace(/^"|"$/g,'').trim());

        rows.push({
            name: v[0],
            email: v[1],
            phone: v[2],
            department: v[3],
            designation: v[4],
            gender: v[5],
            dob: v[6],
            address: v[7]
        });
    }
    return rows;
}

/* ===== VALIDATION ===== */
function validate(row) {
    if (!row.name) return "Name required";
    if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(row.email)) return "Invalid email";
    if (!/^[0-9]{10}$/.test(row.phone)) return "Phone must be 10 digits";
    if (!row.department) return "Department required";
    if (!row.designation) return "Designation required";
    return "OK";
}

/* ===== RENDER PAGE ===== */
    
const DEPARTMENTS = <?= json_encode($DEPARTMENT_MAP[$instituteCd] ?? []) ?>;
const DESIGNATIONS = <?= json_encode($DESIGNATIONS) ?>;

function renderPage() {
    const tbody = document.querySelector("#previewTable tbody");
    tbody.innerHTML = "";

    const start = (currentPage - 1) * rowsPerPage;
    const end = Math.min(start + rowsPerPage, csvData.length);

    for (let i = start; i < end; i++) {
        const r = csvData[i];
        const remark = validate(r);
        const roll = remark === "OK" ? previewRoll(r, i) : "-";

        const tr = document.createElement("tr");
        if (remark !== "OK") tr.classList.add("table-danger");
        tr.innerHTML = `
        <td>${i+1}</td>
        <td>
<input class="form-control"
 name="staff[${i}][name]"
 value="${r.name}"
 oninput="updateRow(${i},'name',this.value)">
</td>

<td>
<input class="form-control"
 name="staff[${i}][email]"
 value="${r.email}"
 oninput="updateRow(${i},'email',this.value)">
</td>

<td>
<input class="form-control"
 name="staff[${i}][phone]"
 value="${r.phone}"
 oninput="updateRow(${i},'phone',this.value)">
</td>

<td>
  <select class="form-select"
          name="staff[${i}][department]"
          onchange="updateRow(${i},'department',this.value)">
    ${Object.values(DEPARTMENTS).map(d => `
      <option value="${d}" ${r.department===d?'selected':''}>${d}</option>
    `).join("")}
  </select>
</td>

<td>
  <select class="form-select"
          name="staff[${i}][designation]"
          onchange="updateRow(${i},'designation',this.value)">
    ${DESIGNATIONS.map(d => `
      <option value="${d}" ${r.designation===d?'selected':''}>${d}</option>
    `).join("")}
  </select>
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
    renderPage(); // re-validate + re-render instantly
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

document.querySelector("form").onsubmit = () => {
    if (hasErrors()) {
        alert("Please fix all errors before uploading.");
        return false;
    }

    const bar = document.getElementById("uploadProgress");
    bar.style.display = "block";
    let p = 0;
    const i = setInterval(() => {
        p += 10;
        bar.value = p;
        if (p >= 100) clearInterval(i);
    }, 120);

    return true;
};

</script>


</body>

</html>