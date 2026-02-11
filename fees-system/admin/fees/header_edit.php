<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
    
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';
require_once BASE_PATH.'/core/validator.php';
require_once BASE_PATH.'/services/FeeService.php';
require_once BASE_PATH.'/config/audit.php';

checkLogin();
$feeService = new FeeService($conn);
$message = "";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$fee = $feeService->getHeaderById($id);

if (!$fee) {
    die("Fee Header not found.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_header'])) {
    // 1. Call the service and get the result array
    $result = $feeService->updateHeader($id, $_POST);
    
    if ($result['success']) {
        // 2. Audit log is already handled inside FeeService->updateHeader, 
        // but if you want to keep this specific one here:
        audit_log($conn, 'UPDATE_FEE_HDR', 'MASTER_FEES_HDR', $id, null, "Updated Code: " . $fee['FEES_CODE']);
        
        $message = "<div class='alert alert-success shadow-sm animate__animated animate__fadeIn'>
                        <i class='bi bi-check-circle-fill me-2'></i> 
                        Header updated successfully! 
                        <a href='header_add.php' class='alert-link'>Go Back</a>
                    </div>";
        
        // 3. Refresh data to show updated values in the form
        $fee = $feeService->getHeaderById($id);
    } else {
        // 4. Display the specific validation or SQL error message
        $message = "<div class='alert alert-danger animate__animated animate__shakeX'>
                        <i class='bi bi-exclamation-triangle-fill me-2'></i> 
                        Update failed: " . $result['message'] . "
                    </div>";
    }
}

?>

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<?php include BASE_PATH.'/admin/layout/sidebar.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary fw-bold">Edit Fee Header: <?= $fee['FEES_CODE'] ?></h5>
                    <a href="header_add.php" class="btn btn-sm btn-outline-secondary">Back to List</a>
                </div>
                <div class="card-body p-4">
                    <?= $message ?>
                    <form method="POST">
                        <div class="row g-3">
                            
                            <div class="col-md-6">
    							<label class="form-label small fw-bold">Fee Code (Read Only)</label>
    							<input type="text" class="form-control bg-light" value="<?= $fee['FEES_CODE'] ?>" readonly>
    							<input type="hidden" name="fees_code" value="<?= $fee['FEES_CODE'] ?>"> 
							</div>
                            
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Fee Name</label>
                                <input type="text" name="fees_name" class="form-control" value="<?= $fee['FEES_NAME'] ?>" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">Description</label>
                                <textarea name="fees_description" class="form-control" rows="2"><?= $fee['FEES_DESCRIPTION'] ?></textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Applicable Level</label>
                                <select name="applicable_level" class="form-select">
                                    <?php 
                                    $levels = ['COURSE','SEMESTER','YEAR','ONETIME','GLOBAL'];
                                    foreach($levels as $l) {
                                        $sel = ($fee['APPLICABLE_LEVEL'] == $l) ? 'selected' : '';
                                        echo "<option value='$l' $sel>$l</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Mandatory</label>
                                <select name="mandatory_flag" class="form-select">
                                    <option value="Y" <?= $fee['MANDATORY_FLAG'] == 'Y' ? 'selected' : '' ?>>Yes</option>
                                    <option value="N" <?= $fee['MANDATORY_FLAG'] == 'N' ? 'selected' : '' ?>>No</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Refundable</label>
                                <select name="refundable_flag" class="form-select">
                                    <option value="Y" <?= $fee['REFUNDABLE_FLAG'] == 'Y' ? 'selected' : '' ?>>Yes</option>
                                    <option value="N" <?= $fee['REFUNDABLE_FLAG'] == 'N' ? 'selected' : '' ?>>No</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Display Order</label>
                                <input type="number" name="display_order" class="form-control" value="<?= $fee['DISPLAY_ORDER'] ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Status (A-Active / I-Inactive)</label>
                                <select name="active_flag" class="form-select border-<?= $fee['ACTIVE_FLAG'] == 'A' ? 'success' : 'danger' ?>">
                                    <option value="A" <?= $fee['ACTIVE_FLAG'] == 'A' ? 'selected' : '' ?>>Active (Visible)</option>
                                    <option value="I" <?= $fee['ACTIVE_FLAG'] == 'I' ? 'selected' : '' ?>>Inactive (Hidden)</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-top">
                            <button type="submit" name="update_header" class="btn btn-primary px-5 fw-bold">Update Fee Header</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include BASE_PATH.'/admin/layout/footer.php'; ?>