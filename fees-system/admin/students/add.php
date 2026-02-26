<?php
/*======================================================
    File Name   : add.php
    Project     : RMIT Groups - FMS - Fees Management System
	Module		: STUDENT MANAGEMENT
    Description : Student Registration & Profile Management
    Developed By: TrinityWebEdge
    Date Created: 05-02-2025
    Last Updated: 25-02-2026
    Note        : This page defines the FMS - Fees Management System | Student Module of RMIT Groups website.
=======================================================*/
        
error_reporting(E_ALL);
ini_set('display_errors', 1);

/* --- 1. CONFIGURATION & BASE PATH --- */
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');

/* --- 2. REQUIRE CORE FILES --- */
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';
require_once BASE_PATH.'/services/student_service.php';
require_once BASE_PATH.'/core/validator.php'; 

/* --- 3. AUTHENTICATION & ROLE CHECK --- */
checkLogin();
$role = $_SESSION['role_name'];

/* --- 4. MULTI-INSTITUTE CONTEXT LOGIC --- */
// If Superadmin switches institute dropdown, update the context
$instId = ($role === 'SUPERADMIN' && isset($_POST['target_inst_id'])) ? intval($_POST['target_inst_id']) : $_SESSION['inst_id'];

/* --- 5. FORM SUBMISSION HANDLING --- */
$error = "";

if($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST['refresh_only'])){
    // Mapping Form Data to Database Schema
    $data = [
    'inst_id'   => $instId,
    'reg'       => trim($_POST['reg'] ?? ''),
    'roll'      => trim($_POST['roll'] ?? ''),
    'fname'     => trim($_POST['fname'] ?? ''),
    'lname'     => trim($_POST['lname'] ?? ''),
    'father_name' => trim($_POST['father_name'] ?? ''), // CHANGED from 'father'
    'mother_name' => trim($_POST['mother_name'] ?? ''), // CHANGED from 'mother'
    'gender'    => $_POST['gender'] ?? '',
    'dob'       => $_POST['dob'] ?? '',
    'mobile'    => trim($_POST['mobile'] ?? ''),
    'email'     => trim($_POST['email'] ?? ''),
    'address'   => trim($_POST['address'] ?? ''),
    'city'      => trim($_POST['city'] ?? ''),
    'state'     => trim($_POST['state'] ?? ''),
    'pincode'   => trim($_POST['pincode'] ?? ''),
    'course'    => $_POST['course'] ?? '',
    'semester'  => $_POST['semester'] ?? 1,
    'admission' => $_POST['admission'] ?? date('Y-m-d')
	];

    /* --- 6. DATA VALIDATION --- */
    $validationErrors = validateStudentData($data, $conn);

    if (empty($validationErrors)) {
        /* --- 7. DATABASE INSERTION VIA SERVICE --- */
        $id = createStudent($conn, $data);
        if($id){
            header("Location: list.php?msg=Student Registered Successfully!");
            exit;
        } else {
            $error = "Critical: A database error occurred during registration.";
        }
    } else {
        $error = implode("<br>", $validationErrors);
    }
}

/* --- 8. FETCH DROPDOWN DATA (INSTITUTE-SPECIFIC) --- */
$courses = $conn->query("SELECT COURSE_ID, COURSE_NAME FROM COURSES WHERE STATUS='A' AND INST_ID = $instId ORDER BY COURSE_NAME ASC");
$institutes = ($role === 'SUPERADMIN') ? $conn->query("SELECT INST_ID, INST_NAME FROM MASTER_INSTITUTES ORDER BY INST_NAME ASC") : null;
?>

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<?php include BASE_PATH.'/admin/layout/sidebar.php'; ?>

<div class="container-fluid mt-4">
    <div class="card shadow border-0 p-4">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-0 text-primary fw-bold">🎓 Student Registration</h4>
                <small class="text-muted">FMS Ver 1.0.0 | RMIT Group Management</small>
            </div>
            <a href="list.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back to List</a>
        </div>

        <?php if($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="post" autocomplete="off">
            <input type="hidden" name="refresh_only" id="refresh_only" value="">

            <div class="row g-3">
                
                <?php if ($role === 'SUPERADMIN'): ?>
                <div class="col-12">
                    <div class="card bg-light border-0 mb-3">
                        <div class="card-body">
                            <label class="form-label fw-bold text-primary small text-uppercase">Context: Registering For Institute</label>
                            <select name="target_inst_id" class="form-select" onchange="document.getElementById('refresh_only').value='1'; this.form.submit();">
                                <?php while($ins = $institutes->fetch_assoc()): ?>
                                    <option value="<?= $ins['INST_ID'] ?>" <?= ($instId == $ins['INST_ID']) ? 'selected' : '' ?>>
                                        <?= $ins['INST_NAME'] ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="col-12">
                    <h6 class="text-primary border-bottom pb-2 mb-3 mt-2"><i class="bi bi-mortarboard-fill me-2"></i>Academic Details</h6>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold small">Registration No *</label>
                    <input type="text" name="reg" class="form-control" required value="<?= htmlspecialchars($_POST['reg'] ?? '') ?>">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label fw-bold small">Roll No *</label>
                    <input type="text" name="roll" class="form-control" required value="<?= htmlspecialchars($_POST['roll'] ?? '') ?>">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label fw-bold small">Course *</label>
                    <select name="course" class="form-select" required>
                        <option value="">-- Select Course --</option>
                        <?php if($courses): while($c = $courses->fetch_assoc()): ?>
                            <option value="<?= $c['COURSE_ID'] ?>" <?= (isset($_POST['course']) && $_POST['course'] == $c['COURSE_ID']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['COURSE_NAME']) ?>
                            </option>
                        <?php endwhile; endif; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold small">Current Semester</label>
                    <input type="number" name="semester" class="form-control" min="1" value="<?= $_POST['semester'] ?? 1 ?>">
                </div>

                <div class="col-12 mt-4">
                    <h6 class="text-primary border-bottom pb-2 mb-3"><i class="bi bi-person-lines-fill me-2"></i>Personal & Family Details</h6>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold small">Student First Name *</label>
                    <input type="text" name="fname" class="form-control" required value="<?= htmlspecialchars($_POST['fname'] ?? '') ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold small">Student Last Name</label>
                    <input type="text" name="lname" class="form-control" value="<?= htmlspecialchars($_POST['lname'] ?? '') ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold small">Gender</label>
                    <select name="gender" class="form-select">
                        <option value="MALE" <?= (($_POST['gender'] ?? '') == 'MALE') ? 'selected' : '' ?>>Male</option>
                        <option value="FEMALE" <?= (($_POST['gender'] ?? '') == 'FEMALE') ? 'selected' : '' ?>>Female</option>
                        <option value="OTHER" <?= (($_POST['gender'] ?? '') == 'OTHER') ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold small">Father's Name *</label>
                    <input type="text" name="father_name" class="form-control" required placeholder="Full Name" value="<?= htmlspecialchars($_POST['father_name'] ?? '') ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold small">Mother's Name *</label>
                    <input type="text" name="mother_name" class="form-control" required placeholder="Full Name" value="<?= htmlspecialchars($_POST['mother_name'] ?? '') ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold small">Date of Birth</label>
                    <input type="date" name="dob" class="form-control" value="<?= $_POST['dob'] ?? '' ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold small">Mobile Number</label>
                    <input type="text" name="mobile" class="form-control" placeholder="10-digit number" value="<?= htmlspecialchars($_POST['mobile'] ?? '') ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold small">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="example@rmit.edu" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>

                <div class="col-12 mt-4">
                    <h6 class="text-primary border-bottom pb-2 mb-3"><i class="bi bi-geo-alt-fill me-2"></i>Address & Contact</h6>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold small">Residential Address</label>
                    <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-bold small">City</label>
                    <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($_POST['city'] ?? '') ?>">
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-bold small">State</label>
                    <input type="text" name="state" class="form-control" value="<?= htmlspecialchars($_POST['state'] ?? '') ?>">
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-bold small">Pincode</label>
                    <input type="text" name="pincode" class="form-control" value="<?= htmlspecialchars($_POST['pincode'] ?? '') ?>">
                </div>

                <div class="col-md-4 mt-4">
                    <label class="form-label fw-bold small">Admission Date</label>
                    <input type="date" name="admission" class="form-control" value="<?= $_POST['admission'] ?? date('Y-m-d') ?>">
                </div>

                <div class="col-12 mt-5">
                    <hr>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-5 py-2 shadow-sm fw-bold">
                            <i class="bi bi-check2-circle me-2"></i>Save Student & Initialize Fees
                        </button>
                        <a href="list.php" class="btn btn-light px-4 py-2 border">Cancel</a>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

<?php include BASE_PATH.'/admin/layout/footer.php'; ?>
