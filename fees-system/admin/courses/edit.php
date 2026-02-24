<!--======================================================
    File Name   : edit.php
    Project     : RMIT Groups - FMS - Fees Management System
    Description : COURSE MANAGEMENT
    Developed By: TrinityWebEdge
    Date Created: 06-02-2025
    Note        : This page defines the FMS - Fees Management System | COURSE MANAGEMENT Module of RMIT Groups website.
=======================================================-->
<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';
require_once BASE_PATH.'/config/audit.php';
require_once BASE_PATH.'/core/validator.php';

checkLogin();

$role = $_SESSION['role_name'];
$sessInstId = $_SESSION['inst_id'];
$message = "";
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

/* 1. FETCH COURSE DATA (Role-Aware) */
// If not Superadmin, we add a WHERE clause to ensure the course belongs to the user's institute
$query_sql = "SELECT * FROM COURSES WHERE COURSE_ID = $id";
if ($role !== 'SUPERADMIN') {
    $query_sql .= " AND INST_ID = $sessInstId";
}

$query = $conn->query($query_sql);
$oldData = $query->fetch_assoc();

if (!$oldData) {
    die("<div class='alert alert-danger m-5'>Course not found or access denied.</div>");
}

/* 2. HANDLE FORM SUBMISSION */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_input = [
        'inst_id'  => $oldData['INST_ID'], // Use the existing institute ID of the course
        'code'     => trim($_POST['course_code']),
        'name'     => trim($_POST['course_name']),
        'duration' => $_POST['duration_years']
    ];

    // Pass true for $is_update and the $id to ignore the current record's code during uniqueness check
    $valErrors = validateCourseData($course_input, $conn, true, $id);

    if (empty($valErrors)) {
        $code     = strtoupper($course_input['code']);
        $name     = $course_input['name'];
        $duration = intval($course_input['duration']);
        $status   = $_POST['status'];

        $stmt = $conn->prepare("UPDATE COURSES SET COURSE_CODE=?, COURSE_NAME=?, DURATION_YEARS=?, STATUS=? WHERE COURSE_ID=?");
        $stmt->bind_param("ssisi", $code, $name, $duration, $status, $id);
        
        if ($stmt->execute()) {
            // Prepare data for Audit Log
            $oldLog = [
                'code' => $oldData['COURSE_CODE'], 
                'name' => $oldData['COURSE_NAME'], 
                'duration' => $oldData['DURATION_YEARS'],
                'status' => $oldData['STATUS']
            ];
            $newLog = [
                'code' => $code, 
                'name' => $name, 
                'duration' => $duration,
                'status' => $status
            ];

            audit_log($conn, 'UPDATE_COURSE', 'COURSES', $id, $oldLog, $newLog);
            
            $message = "<div class='alert alert-success shadow-sm'><i class='bi bi-check-circle-fill me-2'></i>Course updated successfully!</div>";
            
            // Refresh oldData for the form display
            $oldData = array_merge($oldData, ['COURSE_CODE'=>$code, 'COURSE_NAME'=>$name, 'DURATION_YEARS'=>$duration, 'STATUS'=>$status]);
        } else {
            $message = "<div class='alert alert-danger'>Update failed: " . $conn->error . "</div>";
        }
    } else {
        $message = "<div class='alert alert-warning'>" . implode('<br>', $valErrors) . "</div>";
    }
}
?>

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<?php include BASE_PATH.'/admin/layout/sidebar.php'; ?>

<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                    <div>
                        <h5 class="mb-0 text-primary fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Course</h5>
                        <?php if($role === 'SUPERADMIN'): ?>
                            <span class="badge bg-info-subtle text-info small mt-1">
                                <i class="bi bi-bank me-1"></i> Institute ID: <?= $oldData['INST_ID'] ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <a href="list.php" class="btn btn-sm btn-outline-secondary">Back to List</a>
                </div>
                <div class="card-body p-4">
                    <?= $message ?>
                    
                    <form method="POST" autocomplete="off">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-uppercase">Course Code</label>
                            <input type="text" name="course_code" class="form-control bg-light" 
                                   value="<?= htmlspecialchars($oldData['COURSE_CODE']) ?>" required>
                            <div class="form-text">Example: BTECH-CS, MBA, ITI-FITTER</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-uppercase">Course Full Name</label>
                            <input type="text" name="course_name" class="form-control" 
                                   value="<?= htmlspecialchars($oldData['COURSE_NAME']) ?>" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-uppercase">Duration (Years)</label>
                                <input type="number" name="duration_years" class="form-control" 
                                       value="<?= $oldData['DURATION_YEARS'] ?>" min="1" max="7" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-uppercase">Status</label>
                                <select name="status" class="form-select">
                                    <option value="A" <?= $oldData['STATUS'] == 'A' ? 'selected' : '' ?>>Active</option>
                                    <option value="I" <?= $oldData['STATUS'] == 'I' ? 'selected' : '' ?>>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="alert alert-light border small mt-3">
                            <i class="bi bi-info-circle me-2"></i> Note: Changing the duration may affect fee scheduling for existing students.
                        </div>

                        <hr class="my-4">
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                            <i class="bi bi-save me-2"></i>Update Course & Log Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include BASE_PATH.'/admin/layout/footer.php'; ?>
