<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';

// Fetch backup history from your BACKUP_LOG table
$sql = "SELECT b.*, am.FULL_NAME 
        FROM BACKUP_LOG b 
        JOIN ADMIN_MASTER am ON b.CREATED_BY = am.ADMIN_ID 
        ORDER BY b.CREATED_AT DESC";
$backups = $conn->query($sql);
?>

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<?php include BASE_PATH.'/admin/layout/sidebar.php'; ?>

<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h3 class="fw-bold text-dark"><i class="bi bi-shield-lock-fill"></i> Security & Backup Center</h3>
            <p class="text-muted">Generate database snapshots and monitor system integrity.</p>
        </div>
        <div class="col-md-4 text-end mt-2">
            <a href="backup.php" class="btn btn-primary btn-lg shadow-sm">
                <i class="bi bi-cloud-arrow-up"></i> Create New Backup
            </a>
        </div>
    </div>

    <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> Database backup generated and logged successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-secondary">Backup History</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Date & Time</th>
                                    <th>File Name</th>
                                    <th>Size (MB)</th>
                                    <th>Triggered By</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($backups->num_rows > 0): ?>
                                    <?php while($row = $backups->fetch_assoc()): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <span class="d-block fw-bold"><?= date('d M, Y', strtotime($row['CREATED_AT'])) ?></span>
                                            <small class="text-muted"><?= date('h:i A', strtotime($row['CREATED_AT'])) ?></small>
                                        </td>
                                        <td><code class="text-primary"><?= $row['FILE_NAME'] ?></code></td>
                                        <td><span class="badge bg-light text-dark border"><?= $row['FILE_SIZE_MB'] ?> MB</span></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-person-circle me-2 text-secondary"></i>
                                                <?= $row['FULL_NAME'] ?>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <a href="../../backups/files/<?= $row['FILE_NAME'] ?>" class="btn btn-sm btn-outline-primary" download>
                                                    <i class="bi bi-download"></i>
                                                </a>
                                                <button class="btn btn-sm btn-outline-danger" onclick="confirmRestore('<?= $row['FILE_NAME'] ?>')">
                                                    <i class="bi bi-arrow-counterclockwise"></i> Restore
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="bi bi-folder-x display-4 d-block mb-2"></i>
                                            No backups found in the system.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmRestore(fileName) {
    if(confirm("CRITICAL WARNING: Restoring '" + fileName + "' will overwrite your current live data. This cannot be undone. Are you sure?")) {
        // Logic to redirect to restore processor
        window.location.href = "process_restore.php?file=" + fileName;
    }
}
</script>

<?php include BASE_PATH.'/admin/layout/footer.php'; ?>