<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';

// Corrected JOIN logic to use ADMIN_ID instead of USER_ID
$sql = "SELECT a.*, am.FULL_NAME , am.USERNAME
        FROM AUDIT_LOG a 
        LEFT JOIN ADMIN_MASTER am ON a.ADMIN_ID = am.ADMIN_ID 
        ORDER BY a.CREATED_AT DESC";

$result = $conn->query($sql);
?>

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<?php include BASE_PATH.'/admin/layout/sidebar.php'; ?>

<div class="container-fluid mt-4">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 fw-bold"><i class="bi bi-shield-lock"></i> System Audit Trail</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="auditTable">
                    <thead class="table-light text-uppercase small">
                        <tr>
                            <th>Timestamp</th>
                            <th>Admin</th>
                            <th>Action</th>
                            <th>Table/Ref</th>
                            <th>Amount</th>
                            <th>Change Details (Old -> New)</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="small text-muted"><?= date('d-M-y H:i', strtotime($row['CREATED_AT'])) ?></td>
                            <td class="fw-bold text-primary"><?= $row['USERNAME'] ?? 'System' ?></td>
                            <td><span class="badge bg-info text-dark"><?= $row['ACTION_TYPE'] ?></span></td>
                            <td>
                                <small class="d-block fw-bold"><?= $row['REF_TABLE'] ?></small>
                                <small class="text-muted">ID: <?= $row['REF_ID'] ?></small>
                            </td>
                            <td class="fw-bold">
                                <?= ($row['AMOUNT'] > 0) ? number_format($row['AMOUNT'], 2) : '-' ?>
                            </td>
                            <td class="small">
                                <?php if($row['OLD_VALUE'] || $row['NEW_VALUE']): ?>
                                    <div class="text-danger text-decoration-line-through"><?= htmlspecialchars($row['OLD_VALUE'] ?? '') ?></div>
                                    <div class="text-success"><?= htmlspecialchars($row['NEW_VALUE'] ?? '') ?></div>
                                <?php else: ?>
                                    <span class="text-muted">No value changes</span>
                                <?php endif; ?>
                            </td>
                            <td class="small"><?= $row['IP_ADDRESS'] ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#auditTable').DataTable({
        "order": [[ 0, "desc" ]], // Newest logs first
        "pageLength": 25,
        "dom": '<"d-flex justify-content-between"fB>rtip'
    });
});
</script>

<?php include BASE_PATH.'/admin/layout/footer.php'; ?>