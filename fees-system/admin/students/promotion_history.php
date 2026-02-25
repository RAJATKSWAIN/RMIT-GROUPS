<!--======================================================
    File Name   : promotion_history.php
    Project     : RMIT Groups - FMS - Fees Management System
	Module		: STUDENT MANAGEMENT
    Description : Student Registration & Profile Management
    Developed By: TrinityWebEdge
    Date Created: 05-02-2026
    Last Updated: 24-02-2026
    Note        : This page defines the FMS - Fees Management System | Student Module of RMIT Groups website.
=======================================================-->
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';
require_once BASE_PATH.'/services/student_service.php';

checkLogin();

// 1. Role and Context Setup
$roleName = $_SESSION['role_name'] ?? 'ADMIN'; 
$sessInstId = $_SESSION['inst_id'];
$isSuperAdmin = ($roleName === 'SUPERADMIN');

// 2. Handle Filtering
$filterInstId = isset($_GET['inst_id']) ? intval($_GET['inst_id']) : null;

// 3. Build Dynamic Query based on Role
$whereClauses = [];
if (!$isSuperAdmin) {
    // Regular Admins only see their own Institute
    $whereClauses[] = "l.INST_ID = $sessInstId";
} elseif ($filterInstId) {
    // Superadmin filtered view
    $whereClauses[] = "l.INST_ID = $filterInstId";
}

$whereSQL = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";

$history_q = "SELECT l.*, s.FIRST_NAME, s.LAST_NAME, s.REGISTRATION_NO, a.FULL_NAME as ADMIN_NAME,
              i.INST_NAME 
              FROM STUDENT_PROMOTION_LOGS l
              JOIN STUDENTS s ON l.STUDENT_ID = s.STUDENT_ID
              JOIN ADMIN_MASTER a ON l.PROMOTED_BY = a.ADMIN_ID
              LEFT JOIN MASTER_INSTITUTES i ON l.INST_ID = i.INST_ID
              $whereSQL
              ORDER BY l.PROMOTED_AT DESC";

$result = $conn->query($history_q);

// Fetch Institutes for Superadmin dropdown
$inst_list = [];
if ($isSuperAdmin) {
    $inst_list = $conn->query("SELECT INST_ID, INST_NAME FROM MASTER_INSTITUTES WHERE STATUS = 'A'");
}
?>

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<?php include BASE_PATH.'/admin/layout/sidebar.php'; ?>

<div class="container-fluid mt-1">
    <div class="row mb-3">
        <div class="col-md-6">
        </div>
        <div class="col-md-6 text-end">
             <a href="promote.php" class="btn btn-outline-primary btn-sm mt-2"><i class="bi bi-arrow-left me-1"></i> Back to Promotion</a>
        </div>
    </div>

    <?php if ($isSuperAdmin): ?>
    <div class="card shadow-sm border-0 mb-4 bg-light">
        <div class="card-body py-2">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-auto">
                    <label class="small fw-bold">Filter by Institute:</label>
                </div>
                <div class="col-md-4">
                    <select name="inst_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Institutes</option>
                        <?php while($ins = $inst_list->fetch_assoc()): ?>
                            <option value="<?= $ins['INST_ID'] ?>" <?= ($filterInstId == $ins['INST_ID']) ? 'selected' : '' ?>>
                                <?= $ins['INST_NAME'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-auto">
                    <a href="promotion_history.php" class="btn btn-sm btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    

    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white py-3">
            <h6 class="mb-0 fw-bold"><i class="bi bi-shield-lock me-2"></i>Historical Promotion Records</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Date / Time</th>
                            <?php if($isSuperAdmin): ?> <th>Institute</th> <?php endif; ?>
                            <th>Student Details</th>
                            <th>Progression</th>
                            <th>Financial snapshot (at move)</th>
                            <th class="pe-3">Processed By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="ps-3">
                                    <span class="fw-bold"><?= date('d-M-Y', strtotime($row['PROMOTED_AT'])) ?></span><br>
                                    <small class="text-muted"><?= date('h:i A', strtotime($row['PROMOTED_AT'])) ?></small>
                                </td>
                                <?php if($isSuperAdmin): ?>
                                <td><span class="badge bg-info-subtle text-info border border-info px-2"><?= $row['INST_NAME'] ?></span></td>
                                <?php endif; ?>
                                <td>
                                    <div class="fw-bold text-dark"><?= $row['FIRST_NAME'] ?> <?= $row['LAST_NAME'] ?></div>
                                    <code class="text-primary small"><?= $row['REGISTRATION_NO'] ?></code>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">Sem <?= $row['FROM_SEMESTER'] ?></span>
                                    <i class="bi bi-arrow-right-short text-primary fs-5 align-middle"></i>
                                    <span class="badge bg-success">Sem <?= $row['TO_SEMESTER'] ?></span>
                                </td>
                                <td>
                                    <div class="p-2 bg-light rounded shadow-sm border-start border-4 <?= $row['CURRENT_BALANCE'] > 0 ? 'border-danger' : 'border-success' ?>" style="max-width: 180px;">
                                        <div class="d-flex justify-content-between small mb-1">
                                            <span class="text-muted">Total:</span>
                                            <span><?= number_format($row['TOTAL_PAYABLE'], 2) ?></span>
                                        </div>
                                        <div class="d-flex justify-content-between small mb-1">
                                            <span class="text-muted">Paid:</span>
                                            <span class="text-success"><?= number_format($row['TOTAL_PAID'], 2) ?></span>
                                        </div>
                                        <hr class="my-1">
                                        <div class="d-flex justify-content-between fw-bold small">
                                            <span>Bal:</span>
                                            <span class="<?= $row['CURRENT_BALANCE'] > 0 ? 'text-danger' : 'text-dark' ?>">
                                                <?= number_format($row['CURRENT_BALANCE'], 2) ?>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="pe-3">
                                    <div class="small fw-semibold text-secondary">
                                        <i class="bi bi-person-check-fill me-1"></i> <?= $row['ADMIN_NAME'] ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="<?= $isSuperAdmin ? '6' : '5' ?>" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox display-4 d-block mb-3 opacity-25"></i>
                                    No promotion history found for the current selection.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include BASE_PATH.'/admin/layout/footer.php'; ?>
