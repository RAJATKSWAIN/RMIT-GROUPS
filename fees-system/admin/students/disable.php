<!--======================================================
    File Name   : disable.php
    Project     : RMIT Groups - FMS - Fees Management System
    Module      : STUDENT MANAGEMENT
    Description : Student Registration & Profile Management
    Developed By: TrinityWebEdge
    Date Created: 06-02-2025
    Last Updated: <?php echo date("d-m-Y"); ?>
    Note        : This page defines the FMS - Fees Management System | Student Module of RMIT Groups website.
=======================================================-->

<?php
// disable.php - FMS V 1.0.0
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/config/audit.php';
require_once BASE_PATH.'/core/auth.php';

checkLogin();

$role       = $_SESSION['role_name'];
$sessInstId = $_SESSION['inst_id'];
$message    = "";

// --- 1. PAGINATION & FILTER CONFIG ---
$limit       = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$page        = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset      = ($page - 1) * $limit;
$filter_inst = $_GET['inst_id'] ?? ($role === 'SUPERADMIN' ? '' : $sessInstId);

// --- 2. HANDLE ACTIONS ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $status_to_set = ($_POST['action'] == 'disable') ? 'I' : 'A';
    
    // Bulk Action
    if (isset($_POST['student_ids']) && is_array($_POST['student_ids'])) {
        $ids = array_map('intval', $_POST['student_ids']);
        $id_str = implode(',', $ids);
        
        $sql = "UPDATE STUDENTS SET STATUS = '$status_to_set' WHERE STUDENT_ID IN ($id_str)";
        if ($role !== 'SUPERADMIN') $sql .= " AND INST_ID = $sessInstId";

        if ($conn->query($sql)) {
            $message = count($ids) . " students updated successfully.";
            audit_log($conn, 'BULK_STATUS_UPDATE', 'STUDENTS', null, "Status: Mixed", "Bulk updated " . count($ids) . " students to $status_to_set");
        }
    } 
    // Single Search Action
    elseif (!empty($_POST['search_id'])) {
        $search = $_POST['search_id'];
        $find = $conn->prepare("SELECT STUDENT_ID, FIRST_NAME FROM STUDENTS WHERE REGISTRATION_NO = ? OR ROLL_NO = ?");
        $find->bind_param("ss", $search, $search);
        $find->execute();
        $res = $find->get_result()->fetch_assoc();

        if ($res) {
            $sid = $res['STUDENT_ID'];
            $upd = "UPDATE STUDENTS SET STATUS = '$status_to_set' WHERE STUDENT_ID = $sid";
            if ($role !== 'SUPERADMIN') $upd .= " AND INST_ID = $sessInstId";
            
            $conn->query($upd);
            $message = "Student {$res['FIRST_NAME']} updated to " . ($status_to_set == 'I' ? 'Inactive' : 'Active');
            audit_log($conn, 'STATUS_CHANGE', 'STUDENTS', $sid, null, "Status manually toggled to $status_to_set via Search");
        } else {
            $message = "No student found with ID: $search";
        }
    }
}

// --- 3. FETCH DATA ---
$where = " WHERE 1=1 ";
if ($role !== 'SUPERADMIN' || !empty($filter_inst)) {
    $target_inst = !empty($filter_inst) ? (int)$filter_inst : (int)$sessInstId;
    $where .= " AND S.INST_ID = $target_inst ";
}

// Total count for pagination
$total_res = $conn->query("SELECT COUNT(*) as total FROM STUDENTS S $where");
$total_records = $total_res->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);

// Main List Query
$sql = "SELECT S.*, C.COURSE_NAME, I.INST_NAME 
        FROM STUDENTS S 
        JOIN COURSES C ON S.COURSE_ID = C.COURSE_ID 
        JOIN MASTER_INSTITUTES I ON S.INST_ID = I.INST_ID
        $where 
        ORDER BY S.STATUS ASC, S.FIRST_NAME ASC 
        LIMIT $offset, $limit";
$students = $conn->query($sql);

$institutes = [];
if ($role === 'SUPERADMIN') {
    $inst_list = $conn->query("SELECT INST_ID, INST_NAME FROM MASTER_INSTITUTES");
    while($row = $inst_list->fetch_assoc()) $institutes[] = $row;
}
?>

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<?php include BASE_PATH.'/admin/layout/sidebar.php'; ?>

<style>
    .fms-card { border-radius: 12px; transition: transform 0.2s; }
    .status-badge { font-size: 0.75rem; padding: 5px 12px; border-radius: 20px; }
    .pagination .page-link { border-radius: 8px; margin: 0 3px; border: none; color: #555; }
    .pagination .active .page-link { background: #0d6efd; color: white; }
    .table-hover tbody tr:hover { background-color: rgba(13, 110, 253, 0.04); }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Student Status Management</h3>
            <p class="text-muted small">Update student activity and system access permissions.</p>
        </div>
        <?php if ($role === 'SUPERADMIN'): ?>
        <div class="col-md-3">
            <form method="GET" id="instFilter">
                <select name="inst_id" class="form-select shadow-sm" onchange="this.form.submit()">
                    <option value="">All Institutes (Global)</option>
                    <?php foreach($institutes as $i): ?>
                        <option value="<?= $i['INST_ID'] ?>" <?= $filter_inst == $i['INST_ID'] ? 'selected' : '' ?>><?= $i['INST_NAME'] ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
        <?php endif; ?>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-primary border-0 shadow-sm alert-dismissible fade show">
            <i class="bi bi-info-circle me-2"></i> <?= $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-3">
            <div class="card fms-card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-search me-2"></i>Quick Search & Action</h6>
                    <form method="POST">
                        <div class="mb-3">
                            <input type="text" name="search_id" class="form-control" placeholder="Reg No / Roll No" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" name="action" value="activate" class="btn btn-success"><i class="bi bi-person-check"></i> Re-Activate</button>
                            <button type="submit" name="action" value="disable" class="btn btn-outline-danger"><i class="bi bi-person-x"></i> Disable Student</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card fms-card border-0 shadow-sm bg-primary text-white">
                <div class="card-body">
                    <div class="small opacity-75">Total Records Found</div>
                    <h2 class="fw-bold mb-0"><?= $total_records ?></h2>
                    <hr class="my-2 opacity-25">
                    <div class="small">Page <?= $page ?> of <?= $total_pages ?></div>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <form method="POST">
                <div class="card fms-card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="fw-bold mb-0">Student Registry</h6>
                            </div>
                            <div class="col-auto d-flex align-items-center">
                                <label class="me-2 small text-muted">Show:</label>
                                <select class="form-select form-select-sm me-3" style="width: 80px;" onchange="location.href='?limit='+this.value">
                                    <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10</option>
                                    <option value="25" <?= $limit == 25 ? 'selected' : '' ?>>25</option>
                                    <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50</option>
                                    <option value="100" <?= $limit == 100 ? 'selected' : '' ?>>100</option>
                                </select>
                                <button type="submit" name="action" value="disable" class="btn btn-sm btn-danger px-3 shadow-sm rounded-pill" onclick="return confirm('Disable selected?')">
                                    <i class="bi bi-person-x-fill me-1"></i> Bulk Disable
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                    <tr>
                                        <th width="50" class="text-center"><input type="checkbox" id="selectAll" class="form-check-input"></th>
                                        <th>Student Info</th>
                                        <th>Course & Institute</th>
                                        <th>Reg No</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($students->num_rows > 0): while($r = $students->fetch_assoc()): ?>
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox" name="student_ids[]" value="<?= $r['STUDENT_ID'] ?>" class="form-check-input student-checkbox">
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark"><?= $r['FIRST_NAME'].' '.$r['LAST_NAME'] ?></div>
                                            <div class="small text-muted"><?= $r['MOBILE'] ?></div>
                                        </td>
                                        <td>
                                            <div class="small fw-bold"><?= $r['COURSE_NAME'] ?></div>
                                            <div class="text-primary small" style="font-size: 0.7rem;"><?= $r['INST_NAME'] ?></div>
                                        </td>
                                        <td><code class="fw-bold text-dark"><?= $r['REGISTRATION_NO'] ?></code></td>
                                        <td class="text-center">
                                            <?php if($r['STATUS'] == 'A'): ?>
                                                <span class="status-badge bg-success-soft text-success border border-success">Active</span>
                                            <?php else: ?>
                                                <span class="status-badge bg-danger-soft text-danger border border-danger">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; else: ?>
                                        <tr><td colspan="5" class="text-center py-5">No students found for this selection.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-white py-3">
                        <nav class="d-flex justify-content-between align-items-center">
                            <div class="small text-muted">Showing <?= $offset+1 ?>-<?= min($offset+$limit, $total_records) ?> of <?= $total_records ?></div>
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $page-1 ?>&limit=<?= $limit ?>&inst_id=<?= $filter_inst ?>"><i class="bi bi-chevron-left"></i></a>
                                </li>
                                <?php for($i=1; $i<=$total_pages; $i++): ?>
                                    <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?>&limit=<?= $limit ?>&inst_id=<?= $filter_inst ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $page+1 ?>&limit=<?= $limit ?>&inst_id=<?= $filter_inst ?>"><i class="bi bi-chevron-right"></i></a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('selectAll').addEventListener('change', function() {
        document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = this.checked);
    });
</script>
<?php include BASE_PATH.'/admin/layout/footer.php'; ?>
