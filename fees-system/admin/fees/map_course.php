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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['map_fee'])) {
    $res = $feeService->mapFeeToCourse($_POST['course_id'], $_POST['fees_hdr_id'], $_POST['amount']);
    $message = $res['success'] ? "<div class='alert alert-success'>Mapped Successfully!</div>" : "<div class='alert alert-danger'>".$res['message']."</div>";
}

$courses = $conn->query("SELECT COURSE_ID, COURSE_NAME FROM COURSES WHERE STATUS = 'A'");
$headers = $conn->query("SELECT FEES_HDR_ID, FEES_NAME FROM MASTER_FEES_HDR WHERE ACTIVE_FLAG = 'A'");
?>

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<?php include BASE_PATH.'/admin/layout/sidebar.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 fw-bold text-primary"><i class="bi bi-link"></i> Assign Fee to Course</div>
                <div class="card-body p-4">
                    <?= $message ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Target Course</label>
                            <select name="course_id" class="form-select" required>
                                <?php while($c = $courses->fetch_assoc()): ?>
                                    <option value="<?= $c['COURSE_ID'] ?>"><?= $c['COURSE_NAME'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Fee Header</label>
                            <select name="fees_hdr_id" class="form-select" required>
                                <?php while($h = $headers->fetch_assoc()): ?>
                                    <option value="<?= $h['FEES_HDR_ID'] ?>"><?= $h['FEES_NAME'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Amount (₹)</label>
                            <input type="number" step="0.01" name="amount" class="form-control" required>
                        </div>
                        <button type="submit" name="map_fee" class="btn btn-primary w-100 fw-bold">Confirm Assignment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include BASE_PATH.'/admin/layout/footer.php'; ?>