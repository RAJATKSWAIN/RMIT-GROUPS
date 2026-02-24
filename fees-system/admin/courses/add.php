<!--======================================================
    File Name   : add.php
    Project     : RMIT Groups - FMS - Fees Management System
    Description : FMS - Fees Management System | Course Creation Page
    Developed By: TrinityWebEdge
    Date Created: 05-02-2026
    Last Updated: 24-02-2026
    Note         : This page defines the FMS - Fees Management System | Course Creation Page of RMIT Groups website.
=======================================================-->
<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';
require_once BASE_PATH.'/config/audit.php'; // Your refined logger  
require_once BASE_PATH.'/core/validator.php'; // validations called


checkLogin();

$message = "";
$role = $_SESSION['role_name'];
$sessInstId = $_SESSION['inst_id'];

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Determine target Institute ID
    $targetInstId = ($role === 'SUPERADMIN') ? $_POST['inst_id'] : $sessInstId;

    $course_input = [
        'inst_id'  => $targetInstId,
        'code'     => $_POST['course_code'],
        'name'     => $_POST['course_name'],
        'duration' => $_POST['duration_years']
    ];

    // Note: Ensure your validateCourseData function in validator.php 
    // is updated to check for duplicates within the same INST_ID
    $valErrors = validateCourseData($course_input, $conn);

    if (empty($valErrors)) {
        $inst_id  = intval($course_input['inst_id']);
        $code     = strtoupper(trim($course_input['code']));
        $name     = trim($course_input['name']);
        $duration = intval($course_input['duration']);

        $stmt = $conn->prepare("INSERT INTO COURSES (INST_ID, COURSE_CODE, COURSE_NAME, DURATION_YEARS, STATUS) VALUES (?, ?, ?, ?, 'A')");
        $stmt->bind_param("issi", $inst_id, $code, $name, $duration);
        
        if ($stmt->execute()) {
            audit_log($conn, 'CREATE_COURSE', 'COURSES', $conn->insert_id, null, $course_input);
            $message = "<div class='alert alert-success shadow-sm'><i class='bi bi-check-circle-fill me-2'></i>Course created successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Database Error: " . $conn->error . "</div>";
        }
    } else {
        $message = "<div class='alert alert-danger shadow-sm'><i class='bi bi-exclamation-triangle-fill me-2'></i>" . implode('<br>', $valErrors) . "</div>";
    }
}

// Fetch Colleges list only for Superadmin
$colleges = ($role === 'SUPERADMIN') ? $conn->query("SELECT INST_ID, INST_NAME FROM MASTER_INSTITUTES ORDER BY INST_NAME ASC") : null;
?>

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<?php include BASE_PATH.'/admin/layout/sidebar.php'; ?>

<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-book-half me-2"></i> Create New Course</h5>
                    <small class="text-muted">Configuration for Academic Programs</small>
                </div>
                <div class="card-body p-4">
                    <?= $message ?>
                    
                    <form method="POST">
                        <?php if ($role === 'SUPERADMIN'): ?>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-uppercase text-primary">Assign to Institute</label>
                            <select name="inst_id" class="form-select border-primary" required>
                                <option value="">-- Select College --</option>
                                <?php while($ins = $colleges->fetch_assoc()): ?>
                                    <option value="<?= $ins['INST_ID'] ?>"><?= $ins['INST_NAME'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <hr>
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label small fw-bold">Course Code</label>
                                <input type="text" name="course_code" class="form-control text-uppercase" required placeholder="BTECH-CS">
                                <div class="form-text small">Short unique identifier.</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label small fw-bold">Duration (Years)</label>
                                <input type="number" name="duration_years" class="form-control" required min="1" max="6" value="3">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Course Full Name</label>
                            <input type="text" name="course_name" class="form-control" required placeholder="Bachelor of Technology in CS">
                        </div>

                        <div class="alert alert-light border small text-muted">
                            <i class="bi bi-info-circle me-1"></i> New courses are set to <strong>Active</strong> status by default.
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary fw-bold">
                                <i class="bi bi-plus-lg me-2"></i>Save & Register Course
                            </button>
                            <a href="list.php" class="btn btn-link btn-sm text-decoration-none">View All Courses</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include BASE_PATH.'/admin/layout/footer.php'; ?>