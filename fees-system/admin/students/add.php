<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

/* =====================================
    BASE PATH 
   ===================================== */
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');

/* =====================================
    LOAD REQUIRED FILES
   ===================================== */
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';
require_once BASE_PATH.'/services/student_service.php';
require_once BASE_PATH.'/core/validator.php'; // 1. LOAD VALIDATOR

/* =====================================
    AUTH CHECK
   ===================================== */
checkLogin();

/* =====================================
    SAVE STUDENT
   ===================================== */
if($_SERVER['REQUEST_METHOD'] == 'POST'){

    // Map POST data
    $data = [
        'reg'       => $_POST['reg'] ?? '',
        'roll'      => $_POST['roll'] ?? '',
        'fname'     => $_POST['fname'] ?? '',
        'lname'     => $_POST['lname'] ?? '',
        'gender'    => $_POST['gender'] ?? '',
        'dob'       => $_POST['dob'] ?? '',
        'mobile'    => $_POST['mobile'] ?? '',
        'email'     => $_POST['email'] ?? '',
        'address'   => $_POST['address'] ?? '',
        'city'      => $_POST['city'] ?? '',
        'state'     => $_POST['state'] ?? '',
        'pincode'   => $_POST['pincode'] ?? '',
        'course'    => $_POST['course'] ?? '',
        'semester'  => $_POST['semester'] ?? 1,
        'admission' => $_POST['admission'] ?? date('Y-m-d')
    ];

    // 2. VALIDATE DATA BEFORE SAVING
    $validationErrors = validateStudentData($data, $conn);

    if (empty($validationErrors)) {
        // 3. CALL SERVICE ONLY IF DATA IS VALID
        $id = createStudent($conn, $data);

        if($id){
            header("Location: list.php?msg=Student Added Successfully!");
            exit;
        } else {
            $error = "A database error occurred while saving.";
        }
    } else {
        // 4. DISPLAY SPECIFIC VALIDATION ERRORS
        $error = implode("<br>", $validationErrors);
    }
}

/* =====================================
    LOAD DATA FOR DROPDOWNS
   ===================================== */
$courses = $conn->query("SELECT COURSE_ID, COURSE_NAME FROM COURSES WHERE STATUS='A'");

?>

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<?php include BASE_PATH.'/admin/layout/sidebar.php'; ?>

<div class="container-fluid mt-4">
    <div class="card shadow border-0 p-4">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">🎓 New Student Registration</h4>
            <a href="list.php" class="btn btn-outline-secondary btn-sm">Back to List</a>
        </div>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="post" autocomplete="off">
            <div class="row g-3">

                <div class="col-12">
                    <h6 class="text-primary border-bottom pb-2 mb-3">Academic Details</h6>
                </div>

                <div class="col-md-3">
					<label class="form-label fw-bold small">Registration No *</label>
					<input type="text" name="reg" class="form-control" required 
						placeholder="Unique Reg No" 
						value="<?= htmlspecialchars($_POST['reg'] ?? '') ?>">
				</div>
				
				<div class="col-md-3">
					<label class="form-label fw-bold small">Roll No *</label>
					<input type="text" name="roll" class="form-control" required 
						placeholder="Class Roll No" 
						value="<?= htmlspecialchars($_POST['roll'] ?? '') ?>">
				</div>
				
				<div class="col-md-3">
					<label class="form-label fw-bold small">Course *</label>
					<select name="course" class="form-select" required>
						<option value="">-- Select Course --</option>
						<?php 
						// Reset pointer if needed or re-query
						$courses->data_seek(0); 
						while($c = $courses->fetch_assoc()): 
						?>
							<option value="<?= $c['COURSE_ID'] ?>" 
								<?= (isset($_POST['course']) && $_POST['course'] == $c['COURSE_ID']) ? 'selected' : '' ?>>
								<?= htmlspecialchars($c['COURSE_NAME']) ?>
							</option>
						<?php endwhile; ?>
					</select>
				</div>

                <div class="col-md-3">
                    <label class="form-label fw-bold small">Current Semester</label>
                    <input type="number" name="semester" class="form-control" min="1" value="1">
                </div>


                <div class="col-12 mt-4">
                    <h6 class="text-primary border-bottom pb-2 mb-3">Personal Details</h6>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold small">First Name *</label>
                    <input type="text" name="fname" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold small">Last Name</label>
                    <input type="text" name="lname" class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold small">Gender</label>
                    <select name="gender" class="form-select">
                        <option value="MALE">Male</option>
                        <option value="FEMALE">Female</option>
                        <option value="OTHER">Other</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold small">Date of Birth</label>
                    <input type="date" name="dob" class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold small">Mobile Number</label>
                    <input type="text" name="mobile" class="form-control" placeholder="10-digit number">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold small">Email ID</label>
                    <input type="email" name="email" class="form-control" placeholder="student@example.com">
                </div>


                <div class="col-12 mt-4">
                    <h6 class="text-primary border-bottom pb-2 mb-3">Address & Location</h6>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold small">Full Address</label>
                    <input type="text" name="address" class="form-control">
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-bold small">City</label>
                    <input type="text" name="city" class="form-control">
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-bold small">State</label>
                    <input type="text" name="state" class="form-control">
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-bold small">Pincode</label>
                    <input type="text" name="pincode" class="form-control">
                </div>


                <div class="col-md-4 mt-4">
                    <label class="form-label fw-bold small">Admission Date</label>
                    <input type="date" name="admission" class="form-control" value="<?= date('Y-m-d') ?>">
                </div>

                <div class="col-12 mt-5">
                    <hr>
                    <button type="submit" class="btn btn-primary px-5 shadow-sm">
                        <i class="bi bi-save me-2"></i>Save Student & Initialize Fees
                    </button>
                    <a href="list.php" class="btn btn-light px-4 ms-2">Cancel</a>
                </div>

            </div>
        </form>

    </div>
</div>

<?php include BASE_PATH.'/admin/layout/footer.php'; ?>