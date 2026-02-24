<!--======================================================
    File Name   : list.php
    Project     : RMIT Groups - FMS - Fees Management System
    Description : FMS - Fees Management System | Course View Page
    Developed By: TrinityWebEdge
    Date Created: 05-02-2026
    Last Updated: 24-02-2026
    Note         : This page defines the FMS - Fees Management System | Course View Page of RMIT Groups website.
=======================================================-->

<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';

checkLogin();

// 1. Role & Session Setup
$role       = $_SESSION['role_name'];
$sessInstId = $_SESSION['inst_id'];

// 2. Filter Logic (For Superadmin)
$filter_inst = $_GET['filter_inst'] ?? (($role === 'SUPERADMIN') ? 'ALL' : $sessInstId);

// 3. Build Role-Aware SQL
$sql = "SELECT c.*, i.INST_NAME 
        FROM COURSES c
        JOIN MASTER_INSTITUTES i ON c.INST_ID = i.INST_ID";

if ($role !== 'SUPERADMIN') {
    // Standard Admin: Lock to their own institute
    $sql .= " WHERE c.INST_ID = $sessInstId";
} elseif ($filter_inst !== 'ALL') {
    // Superadmin: Filter by specific institute if selected
    $sql .= " WHERE c.INST_ID = " . intval($filter_inst);
}

$sql .= " ORDER BY i.INST_NAME ASC, c.COURSE_NAME ASC";
$result = $conn->query($sql);

// Fetch Colleges for the Filter Dropdown (Superadmin only)
$colleges = ($role === 'SUPERADMIN') ? $conn->query("SELECT INST_ID, INST_NAME FROM MASTER_INSTITUTES ORDER BY INST_NAME ASC") : null;
?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<?php include BASE_PATH.'/admin/layout/sidebar.php'; ?>

<div class="container-fluid mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center border-bottom">
            <div class="mb-2 mb-md-0">
                <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-book-half me-2"></i> Course Master List</h5>
                <small class="text-muted">Manage academic programs and durations</small>
            </div>
            
            <div class="d-flex gap-2">
                <?php if ($role === 'SUPERADMIN'): ?>
                <form method="GET" class="d-flex gap-2">
                    <select name="filter_inst" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="ALL">-- All Colleges --</option>
                        <?php while($ins = $colleges->fetch_assoc()): ?>
                            <option value="<?= $ins['INST_ID'] ?>" <?= ($filter_inst == $ins['INST_ID']) ? 'selected' : '' ?>>
                                <?= $ins['INST_NAME'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </form>
                <?php endif; ?>
                <a href="add.php" class="btn btn-primary btn-sm px-3"><i class="bi bi-plus-lg"></i> Add New</a>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="courseTable" class="table table-hover align-middle">
                    <thead class="table-light text-uppercase small fw-bold">
                        <tr>
                            <th>ID</th>
                            <?php if($role === 'SUPERADMIN'): ?>
                                <th>Institute</th>
                            <?php endif; ?>
                            <th>Code</th>
                            <th>Course Name</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['COURSE_ID'] ?></td>
                            <?php if($role === 'SUPERADMIN'): ?>
                                <td class="small fw-bold text-primary"><?= $row['INST_NAME'] ?></td>
                            <?php endif; ?>
                            <td><span class="badge bg-light text-dark border font-monospace"><?= $row['COURSE_CODE'] ?></span></td>
                            <td class="fw-bold"><?= $row['COURSE_NAME'] ?></td>
                            <td><?= $row['DURATION_YEARS'] ?> <span class="text-muted small">Years</span></td>
                            <td>
                                <?php if($row['STATUS'] == 'A'): ?>
                                    <span class="badge rounded-pill bg-success-subtle text-success border border-success">Active</span>
                                <?php else: ?>
                                    <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="edit.php?id=<?= $row['COURSE_ID'] ?>" class="btn btn-sm btn-outline-warning" title="Edit Course">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete(<?= $row['COURSE_ID'] ?>)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
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
    $('#courseTable').DataTable({
        "pageLength": 10,
        "order": [[ 0, "desc" ]],
        "language": {
            "searchPlaceholder": "Search courses..."
        }
    });
});

function confirmDelete(id) {
    if(confirm('Are you sure you want to deactivate/delete this course? This may affect fee structures.')) {
        window.location.href = 'delete.php?id=' + id;
    }
}
</script>

<?php include BASE_PATH.'/admin/layout/footer.php'; ?>
