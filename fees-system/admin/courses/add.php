<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';
require_once BASE_PATH.'/config/audit.php'; // Your refined logger  
require_once BASE_PATH.'/core/validator.php'; // validations called

checkLogin();

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_input = [
        'code'     => $_POST['course_code'],
        'name'     => $_POST['course_name'],
        'duration' => $_POST['duration_years']
    ];

    $valErrors = validateCourseData($course_input, $conn);

    if (empty($valErrors)) {
        $code     = strtoupper(trim($course_input['code']));
        $name     = trim($course_input['name']);
        $duration = intval($course_input['duration']);

        $stmt = $conn->prepare("INSERT INTO COURSES (COURSE_CODE, COURSE_NAME, DURATION_YEARS) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $code, $name, $duration);
        
        if ($stmt->execute()) {
            audit_log($conn, 'CREATE_COURSE', 'COURSES', $conn->insert_id, null, $course_input);
            $message = "<div class='alert alert-success'>Course created successfully!</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>" . implode('<br>', $valErrors) . "</div>";
    }
}
?>

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<?php include BASE_PATH.'/admin/layout/sidebar.php'; ?>

<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-primary"><i class="bi bi-journal-plus"></i> Create New Course</h5>
                </div>
                <div class="card-body p-4">
                    <?= $message ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Course Code (e.g., BTECH-CS)</label>
                            <input type="text" name="course_code" class="form-control" required placeholder="BTECH-CS">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Course Full Name</label>
                            <input type="text" name="course_name" class="form-control" required placeholder="Bachelor of Technology in CS">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Duration (Years)</label>
                            <input type="number" name="duration_years" class="form-control" required min="1" max="6" value="4">
                        </div>
                        <hr>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Save Course</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include BASE_PATH.'/admin/layout/footer.php'; ?>