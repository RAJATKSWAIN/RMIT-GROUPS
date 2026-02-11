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

// 1. Single Add Logic
if (isset($_POST['save_single'])) {
    $valErrors = validateFeeHeader($_POST, $conn);
    if (empty($valErrors)) {
        $res = $feeService->createHeader($_POST);
        if ($res['success']) {
            audit_log($conn, 'CREATE_FEE_HDR', 'MASTER_FEES_HDR', $res['id'], null, $_POST['fees_code']);
            $message = "<div class='alert alert-success shadow-sm'>Fee Header created successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>".$res['message']."</div>";
        }
    } else {
        $message = "<div class='alert alert-warning'>".implode('<br>', $valErrors)."</div>";
    }
}

// 2. Bulk Upload Logic
if (isset($_POST['bulk_upload'])) {
    if (!empty($_FILES['csv_file']['tmp_name'])) {
        $res = $feeService->bulkUploadHeaders($_FILES['csv_file']['tmp_name']);
        $message = "<div class='alert alert-info'>Imported: {$res['success_count']} | Failed: ".count($res['errors'])."</div>";
    }
}

// Fetch all headers to display in the table
$headers = $conn->query("SELECT * FROM MASTER_FEES_HDR ORDER BY DISPLAY_ORDER ASC");
?>

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<?php include BASE_PATH.'/admin/layout/sidebar.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-7 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 fw-bold text-primary">Add Fee Header</div>
                <div class="card-body">
                    <?= $message ?>
                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Fee Code</label>
                                <input type="text" name="fees_code" class="form-control" required placeholder="TUI_FEE">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Fee Name</label>
                                <input type="text" name="fees_name" class="form-control" required placeholder="Tuition Fee">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">Description</label>
                                <input type="text" name="fees_description" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Applicable Level</label>
                                <select name="applicable_level" class="form-select">
                                    <option value="COURSE">COURSE</option>
                                    <option value="SEMESTER">SEMESTER</option>
                                    <option value="YEAR">YEAR</option>
                                    <option value="ONETIME">ONETIME</option>
                                    <option value="GLOBAL">GLOBAL</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Mandatory</label>
                                <select name="mandatory_flag" class="form-select"><option value="Y">Yes</option><option value="N">No</option></select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Refundable</label>
                                <select name="refundable_flag" class="form-select"><option value="N">No</option><option value="Y">Yes</option></select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Display Order</label>
                                <input type="number" name="display_order" class="form-control" value="1">
                            </div>
                        </div>
                        <button type="submit" name="save_single" class="btn btn-primary w-100 mt-4 fw-bold">Save Header</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-5">
    <div class="card shadow-sm border-0 border-top border-success border-4">
        <div class="card-header bg-white fw-bold">
            <i class="bi bi-file-earmark-excel"></i> Bulk Import
        </div>
        <div class="card-body text-center">
            <p class="text-muted small mb-2">
                Required: Code, Name, Desc, Level, Mandatory, Refundable, Order
            </p>
            
            <div class="mb-3">
                <a href="download_template.php" class="btn btn-sm btn-outline-success">
                    <i class="bi bi-download"></i> Download Sample Template
                </a>
            </div>

            <form method="POST" enctype="multipart/form-data">
                <input type="file" name="csv_file" class="form-control mb-3" required>
                <button type="submit" name="bulk_upload" class="btn btn-success w-100 fw-bold">
                    Upload CSV Headers
                </button>
            </form>
            
            <div class="mt-3 text-start">
                <small class="text-danger fw-bold">Instructions:</small>
                <ul class="text-muted" style="font-size: 0.75rem;">
                    <li><strong>Level:</strong> COURSE, SEMESTER, YEAR, ONETIME, GLOBAL</li>
                    <li><strong>Flags:</strong> Use 'Y' for Yes, 'N' for No</li>
                    <li><strong>Order:</strong> Use numbers (1, 2, 3...)</li>
                </ul>
            </div>
        </div>
    </div>
</div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white fw-bold py-3">Existing Fee Headers</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Order</th>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Level</th>
                                    <th>Mandatory</th>
                                    <th>Refundable</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
							<?php while($row = $headers->fetch_assoc()): ?>
							<tr>
								<td><?= $row['DISPLAY_ORDER'] ?></td>
								<td><span class="badge bg-light text-dark border"><?= $row['FEES_CODE'] ?></span></td>
								<td><?= $row['FEES_NAME'] ?></td>
								<td><small class="fw-bold"><?= $row['APPLICABLE_LEVEL'] ?></small></td>
								<td>
									<span class="badge <?= $row['MANDATORY_FLAG'] == 'Y' ? 'bg-info' : 'bg-secondary' ?>">
										<?= $row['MANDATORY_FLAG'] == 'Y' ? 'Yes' : 'No' ?>
									</span>
								</td>
								<td>
									<span class="badge <?= $row['REFUNDABLE_FLAG'] == 'Y' ? 'bg-warning text-dark' : 'bg-light text-muted border' ?>">
										<?= $row['REFUNDABLE_FLAG'] == 'Y' ? 'Yes' : 'No' ?>
									</span>
								</td>
								<td>
									<?php if($row['ACTIVE_FLAG'] == 'A'): ?>
										<span class="text-success small fw-bold"><i class="bi bi-check-circle-fill"></i> Active</span>
									<?php else: ?>
										<span class="text-danger small fw-bold"><i class="bi bi-x-circle-fill"></i> Inactive</span>
									<?php endif; ?>
								</td>
								<td>
									<div class="btn-group">
										<a href="header_edit.php?id=<?= $row['FEES_HDR_ID'] ?>" class="btn btn-sm btn-outline-primary" title="Edit">
											<i class="bi bi-pencil"></i>
										</a>
						
										<?php if($row['ACTIVE_FLAG'] == 'A'): ?>
											<a href="header_status.php?id=<?= $row['FEES_HDR_ID'] ?>&status=I" 
											class="btn btn-sm btn-outline-danger" 
											onclick="return confirm('Mark this fee as Inactive? It will no longer be available for new mappings.')" title="Deactivate">
												<i class="bi bi-trash"></i>
											</a>
										<?php else: ?>
											<a href="header_status.php?id=<?= $row['FEES_HDR_ID'] ?>&status=A" 
											class="btn btn-sm btn-outline-success" title="Activate">
												<i class="bi bi-arrow-counterclockwise"></i>
											</a>
										<?php endif; ?>
									</div>
								</td>
							</tr>
							<?php endwhile; ?>
							
							<?php if($headers->num_rows == 0): ?>
								<tr><td colspan="8" class="text-center py-4 text-muted">No fee headers found. Add one above!</td></tr>
							<?php endif; ?>
							</tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include BASE_PATH.'/admin/layout/footer.php'; ?>