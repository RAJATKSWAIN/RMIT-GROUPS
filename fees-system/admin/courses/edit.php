<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';
require_once BASE_PATH.'/config/audit.php';
require_once BASE_PATH.'/core/validator.php';

checkLogin();

$message = "";
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 1. Fetch Current Course Data
$query = $conn->query("SELECT * FROM COURSES WHERE COURSE_ID = $id");
$oldData = $query->fetch_assoc();

if (!$oldData) {
    die("<div class='alert alert-danger'>Course not found.</div>");
}

// 2. Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_input = [
        'code'     => trim($_POST['course_code']),
        'name'     => trim($_POST['course_name']),
        'duration' => $_POST['duration_years']
    ];

    // Pass true for $is_update and the $id to ignore the current record's code
    $valErrors = validateCourseData($course_input, $conn, true, $id);

    if (empty($valErrors)) {
        $code     = strtoupper($course_input['code']);
        $name     = $course_input['name'];
        $duration = intval($course_input['duration']);
        $status   = $_POST['status'];

        $stmt = $conn->prepare("UPDATE COURSES SET COURSE_CODE=?, COURSE_NAME=?, DURATION_YEARS=?, STATUS=? WHERE COURSE_ID=?");
        $stmt->bind_param("ssisi", $code, $name, $duration, $status, $id);
        
        if ($stmt->execute()) {
            // Prepare data for Audit Log (Comparing Old vs New)
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

            // Log the update with Old and New values
            audit_log($conn, 'UPDATE_COURSE', 'COURSES', $id, $oldLog, $newLog);
            
            $message = "<div class='alert alert-success'>Course updated successfully!</div>";
            // Refresh oldData to show updated values in form
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
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary fw-bold"><i class="bi bi-pencil-square"></i> Edit Course</h5>
                    <a href="list.php" class="btn btn-sm btn-outline-secondary">Back</a>
                </div>
                <div class="card-body p-4">
                    <?= $message ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Course Code</label>
                            <input type="text" name="course_code" class="form-control" 
                                   value="<?= htmlspecialchars($oldData['COURSE_CODE']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Course Full Name</label>
                            <input type="text" name="course_name" class="form-control" 
                                   value="<?= htmlspecialchars($oldData['COURSE_NAME']) ?>" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Duration (Years)</label>
                                <input type="number" name="duration_years" class="form-control" 
                                       value="<?= $oldData['DURATION_YEARS'] ?>" min="1" max="7" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Status</label>
                                <select name="status" class="form-select">
                                    <option value="A" <?= $oldData['STATUS'] == 'A' ? 'selected' : '' ?>>Active</option>
                                    <option value="I" <?= $oldData['STATUS'] == 'I' ? 'selected' : '' ?>>Inactive</option>
                                </select>
                            </div>
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-primary w-100 fw-bold">Update Course & Log Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include BASE_PATH.'/admin/layout/footer.php'; ?>