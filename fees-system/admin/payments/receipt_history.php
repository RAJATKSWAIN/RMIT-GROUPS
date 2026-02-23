<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';
checkLogin();

// Get the institute ID from the session (populated during login)
$adminId   = $_SESSION['admin_id'];
$adminName = $_SESSION['admin_name'];
$instId 	= $_SESSION['inst_id'];

// Enhanced SQL: Fetching Full Name, Reg No, Roll No, and Course Details
$search = $_GET['search'] ?? '';
$whereClause = "";
$params = [];
$types = "";

if(!empty($search)) {
    $whereClause = "WHERE p.RECEIPT_NO LIKE ? OR s.REGISTRATION_NO LIKE ? OR s.ROLL_NO LIKE ? OR s.FIRST_NAME LIKE ? OR s.LAST_NAME LIKE ? AND s.INST_ID = ?";
    $param = "%$search%";
    $params = [$param, $param, $param, $param, $param, $instId];
    $types = "sssssi";
}

$sql = "SELECT p.*, s.FIRST_NAME, s.LAST_NAME, s.REGISTRATION_NO, s.ROLL_NO, c.COURSE_NAME 
        FROM PAYMENTS p 
        JOIN STUDENTS s ON p.STUDENT_ID = s.STUDENT_ID 
        LEFT JOIN COURSES c ON s.COURSE_ID = c.COURSE_ID
        $whereClause 
        ORDER BY p.PAYMENT_DATE DESC LIMIT 50";

$stmt = $conn->prepare($sql);
if(!empty($search)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$receipts = $stmt->get_result();
?>

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<?php include BASE_PATH.'/admin/layout/sidebar.php'; ?>

<div class="container-fluid px-4">
    <h3 class="mt-4">Receipt History</h3>
    
    <form action="bulk_print.php" method="POST" target="_blank">
        <div class="card p-3 mb-3 shadow-sm border-0">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Search by Receipt #, Name, Reg No, or Roll No" value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" formmethod="GET" formaction="receipt_history.php" class="btn btn-primary w-100">Search</button>
                </div>
                <div class="col-md-4 text-end">
                    <div class="form-check form-switch d-inline-block me-3">
                        <input class="form-check-input" type="checkbox" id="masterSelect">
                        <label class="form-check-label fw-bold" for="masterSelect">Select All Results</label>
                    </div>
                    <button type="submit" class="btn btn-success"><i class="bi bi-printer"></i> Bulk Print Selected</button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover bg-white shadow-sm rounded">
                <thead class="table-dark">
                    <tr>
                        <th width="40">#</th>
                        <th>Date</th>
                        <th>Receipt #</th>
                        <th>Student Details (Full Name | Reg | Roll)</th>
                        <th>Course</th>
                        <th>Amount</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $receipts->fetch_assoc()): ?>
                    <tr>
                        <td><input type="checkbox" name="receipt_ids[]" value="<?= $row['PAYMENT_ID'] ?>" class="receipt-checkbox"></td>
                        <td><?= date('d-M-Y', strtotime($row['PAYMENT_DATE'])) ?></td>
                        <td><span class="badge bg-light text-dark border"><?= $row['RECEIPT_NO'] ?></span></td>
                        <td>
                            <div class="fw-bold"><?= strtoupper($row['FIRST_NAME'] . ' ' . $row['LAST_NAME']) ?></div>
                            <small class="text-muted">Reg: <?= $row['REGISTRATION_NO'] ?> | Roll: <?= $row['ROLL_NO'] ?></small>
                        </td>
                        <td><?= $row['COURSE_NAME'] ?></td>
                        <td class="fw-bold text-primary">₹<?= number_format($row['PAID_AMOUNT'], 2) ?></td>
                        <td class="text-center">
                            <a href="receipt.php?id=<?= $row['PAYMENT_ID'] ?>" target="_blank" class="btn btn-sm btn-outline-info">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </form>
</div>

<script>
document.getElementById('masterSelect').addEventListener('change', function() {
    let checkboxes = document.querySelectorAll('.receipt-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});
</script>

<?php include BASE_PATH.'/admin/layout/footer.php'; ?>
