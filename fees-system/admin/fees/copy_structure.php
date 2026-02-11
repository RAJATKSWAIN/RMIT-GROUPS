<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';
require_once BASE_PATH.'/core/validator.php';
require_once BASE_PATH.'/services/FeeService.php';
require_once BASE_PATH.'/config/audit.php';

checkLogin();
$feeService = new FeeService($conn);
$message = "";

if (isset($_POST['copy_action'])) {
    $from = $_POST['from_course'];
    $to = $_POST['to_course'];

    if ($from == $to) {
        $message = "<div class='alert alert-warning shadow-sm'>Source and Target course cannot be the same.</div>";
    } else {
        $rowCount = $feeService->copyFeeStructure($from, $to);

        if ($rowCount > 0) {
            // Case 1: Something was actually copied
            $message = "<div class='alert alert-success shadow-sm'><strong>Success!</strong> $rowCount new fee(s) copied to the target course.</div>";
        } elseif ($rowCount === 0) {
            // Case 2: Query ran fine, but NOT IN filter blocked everything
            $message = "<div class='alert alert-info shadow-sm'><i class='bi bi-info-circle me-2'></i>Mapping already exists! No new fees were added.</div>";
        } else {
            // Case 3: Database error (returns -1)
            $message = "<div class='alert alert-danger shadow-sm'><strong>Error!</strong> A system error occurred during duplication.</div>";
        }
    }
}

// Fetch courses (Order by name for easier selection)
$courses = $conn->query("SELECT COURSE_ID, COURSE_NAME FROM COURSES WHERE STATUS = 'A' ORDER BY COURSE_NAME ASC");
?>

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<?php include BASE_PATH.'/admin/layout/sidebar.php'; ?>

<div class="container mt-4">
    <div class="card shadow-sm border-0 mx-auto" style="max-width: 500px;">
        <div class="card-header bg-dark text-white py-3 fw-bold">
            <i class="bi bi-files me-2"></i> Copy Fee Structure
        </div>
        <div class="card-body p-4">
            <?= $message ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted text-uppercase">From (Source Course)</label>
                    <select name="from_course" class="form-select bg-light" required>
                        <option value="">-- Select Source Course --</option>
                        <?php $courses->data_seek(0); while($c = $courses->fetch_assoc()): ?>
                            <option value="<?= $c['COURSE_ID'] ?>"><?= $c['COURSE_NAME'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="text-center mb-3">
                    <div class="bg-light d-inline-block rounded-circle p-2 border">
                        <i class="bi bi-arrow-down fs-4 text-primary"></i>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted text-uppercase">To (Target Course)</label>
                    <select name="to_course" class="form-select bg-light" required>
                        <option value="">-- Select Target Course --</option>
                        <?php $courses->data_seek(0); while($c = $courses->fetch_assoc()): ?>
                            <option value="<?= $c['COURSE_ID'] ?>"><?= $c['COURSE_NAME'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <button type="submit" name="copy_action" class="btn btn-primary w-100 py-2 fw-bold shadow-sm" onclick="return confirm('Are you sure you want to duplicate this structure?')">
                    Duplicate Fee Structure
                </button>
            </form>
        </div>
    </div>
</div>

<?php include BASE_PATH.'/admin/layout/footer.php'; ?>