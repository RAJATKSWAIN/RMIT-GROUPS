<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';
require_once BASE_PATH.'/core/validator.php';
require_once BASE_PATH.'/services/FeeService.php';
require_once BASE_PATH.'/config/audit.php';

checkLogin();
$feeService = new FeeService($conn);
$message = "";

if (isset($_POST['bulk_map_upload'])) {
    if (!empty($_FILES['csv_file']['tmp_name'])) {
        $res = $feeService->bulkMapFees($_FILES['csv_file']['tmp_name']);
        $message = "<div class='alert alert-info shadow-sm'>Successful: {$res['success_count']} | Errors: ".count($res['errors'])."</div>";
    }
}
?>

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<?php include BASE_PATH.'/admin/layout/sidebar.php'; ?>

<div class="container mt-4">
    <div class="card shadow-sm border-0 mx-auto" style="max-width: 600px;">
        <div class="card-header bg-success text-white py-3">
            <h5 class="mb-0 fw-bold"><i class="bi bi-upload"></i> Bulk Mapping CSV</h5>
        </div>
        <div class="card-body p-4 text-center">
            <?= $message ?>
            <p class="text-muted small mb-4">Upload a CSV in the format:<br><strong>CourseID, FeeHdrID, Amount</strong></p>
            <form method="POST" enctype="multipart/form-data">
                <input type="file" name="csv_file" class="form-control mb-3" required>
                <button type="submit" name="bulk_map_upload" class="btn btn-success w-100">Start Import</button>
            </form>
        </div>
    </div>
</div>
<?php include BASE_PATH.'/admin/layout/footer.php'; ?>