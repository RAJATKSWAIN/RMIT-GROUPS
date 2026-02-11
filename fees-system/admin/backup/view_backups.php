<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';

// Fetch backup history with Admin names
$sql = "SELECT b.*, a.FULL_NAME 
        FROM BACKUP_LOG b
        JOIN ADMIN_MASTER a ON b.CREATED_BY = a.ADMIN_ID 
        ORDER BY b.CREATED_AT DESC";
$result = $conn->query($sql);
?>

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<div class="container-fluid mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Backup History & Logs</h5>
            <a href="backup.php" class="btn btn-primary btn-sm">Run New Backup</a>
        </div>
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Date & Time</th>
                        <th>File Name</th>
                        <th>Size (MB)</th>
                        <th>Created By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= date('d M Y, h:i A', strtotime($row['CREATED_AT'])) ?></td>
                        <td class="text-primary fw-bold"><?= $row['FILE_NAME'] ?></td>
                        <td><?= number_format($row['FILE_SIZE_MB'], 2) ?> MB</td>
                        <td><span class="badge bg-info text-dark"><?= $row['FULL_NAME'] ?></span></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>