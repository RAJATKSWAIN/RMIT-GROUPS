<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';

checkLogin();

// 1. Handle Search Input
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$whereClause = "1=1"; // Default to show all

if (!empty($search)) {
    $whereClause = "(S.REGISTRATION_NO LIKE '%$search%' 
                    OR S.ROLL_NO LIKE '%$search%' 
                    OR S.MOBILE LIKE '%$search%' 
                    OR CONCAT(S.FIRST_NAME, ' ', S.LAST_NAME) LIKE '%$search%')";
}

// 2. The Master Query: Joining Student info, Course, Ledger, and the Last Payment
$sql = "
    SELECT 
        S.STUDENT_ID, S.REGISTRATION_NO, S.ROLL_NO, S.FIRST_NAME, S.LAST_NAME, 
        S.MOBILE, S.EMAIL, S.STATUS, S.COURSE_ID,
        C.COURSE_NAME,
        L.TOTAL_FEE, L.PAID_AMOUNT, L.BALANCE_AMOUNT, L.LAST_PAYMENT_DATE,
        (SELECT PAID_AMOUNT FROM PAYMENTS WHERE STUDENT_ID = S.STUDENT_ID ORDER BY PAYMENT_DATE DESC LIMIT 1) as LAST_PAY_AMT
    FROM STUDENTS S
    JOIN COURSES C ON S.COURSE_ID = C.COURSE_ID
    LEFT JOIN STUDENT_FEE_LEDGER L ON S.STUDENT_ID = L.STUDENT_ID
    WHERE $whereClause
    ORDER BY S.CREATED_AT DESC
";

$result = $conn->query($sql);
?>

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<?php include BASE_PATH.'/admin/layout/sidebar.php'; ?>

<div class="container-fluid mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h4 class="mb-0 text-primary fw-bold">🔎 Master Student Explorer</h4>
                </div>
                <div class="col-md-6">
                    <form method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control me-2" 
                               placeholder="Search by Roll, Reg, Mobile or Name..." 
                               value="<?= htmlspecialchars($search) ?>">
                        <button type="submit" class="btn btn-primary">Search</button>
                        <?php if($search): ?>
                            <a href="explorer.php" class="btn btn-link text-decoration-none">Clear</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="min-width: 1200px;">
                    <thead class="table-light">
                        <tr class="small text-uppercase">
                            <th>Student Details</th>
                            <th>Course & Contact</th>
                            <th>Financial Standing</th>
                            <th>Last Payment</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): 
                                $isPending = ($row['BALANCE_AMOUNT'] > 0);
                            ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark"><?= $row['FIRST_NAME'].' '.$row['LAST_NAME'] ?></div>
                                        <div class="small text-muted">Reg: <?= $row['REGISTRATION_NO'] ?></div>
                                        <div class="small text-muted">Roll: <?= $row['ROLL_NO'] ?></div>
                                    </td>

                                    <td>
                                        <div class="fw-bold"><?= $row['COURSE_NAME'] ?></div>
                                        <div class="small"><i class="bi bi-telephone"></i> <?= $row['MOBILE'] ?></div>
                                        <div class="small text-muted text-truncate" style="max-width: 150px;"><?= $row['EMAIL'] ?></div>
                                    </td>

                                    <td>
                                        <div class="small text-muted">Total: ₹<?= number_format($row['TOTAL_FEE'], 2) ?></div>
                                        <div class="small text-success">Paid: ₹<?= number_format($row['PAID_AMOUNT'], 2) ?></div>
                                        <div class="fw-bold <?= $isPending ? 'text-danger' : 'text-success' ?>">
                                            Due: ₹<?= number_format($row['BALANCE_AMOUNT'], 2) ?>
                                        </div>
                                    </td>

                                    <td>
                                        <?php if($row['LAST_PAYMENT_DATE']): ?>
                                            <div class="fw-bold text-success">₹<?= number_format($row['LAST_PAY_AMT'], 2) ?></div>
                                            <div class="small text-muted"><?= date('d M Y', strtotime($row['LAST_PAYMENT_DATE'])) ?></div>
                                        <?php else: ?>
                                            <span class="text-muted small italic">No history</span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <span class="badge rounded-pill <?= $row['STATUS'] == 'A' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?>">
                                            <?= $row['STATUS'] == 'A' ? 'Active' : 'Inactive' ?>
                                        </span>
                                    </td>

                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="view.php?id=<?= $row['STUDENT_ID'] ?>" class="btn btn-sm btn-outline-primary" title="Full View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <?php if($isPending): ?>
                                                <button class="btn btn-sm btn-danger" onclick="alert('Sending Reminder to <?= $row['MOBILE'] ?>...')">
                                                    <i class="bi bi-bell"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="bi bi-search fs-1 text-muted"></i>
                                    <p class="mt-2">No students found matching your criteria.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white text-muted small">
            Note: "Last Payment Amount" is fetched directly from the latest record in the Payments table.
        </div>
    </div>
</div>

<?php include BASE_PATH.'/admin/layout/footer.php'; ?>