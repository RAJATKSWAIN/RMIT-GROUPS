/*======================================================
    File Name   : list.php 
    Project     : RMIT Groups - FMS - Fees Management System
	Module		: STUDENT MANAGEMENT
    Description : Student Registration & Profile Management
    Developed By: TrinityWebEdge
    Date Created: 05-02-2026
    Last Updated: 24-02-2026
    Note        : This page defines the FMS - Fees Management System | Student Module of RMIT Groups website.
=======================================================*/
<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';
checkLogin();

$role = $_SESSION['role_name'];
$sessInstId = $_SESSION['inst_id'];

/* --- 1. HANDLE INSTITUTE FILTERING --- */
// If Superadmin, get inst_id from GET; otherwise use session
$instFilter = ($role === 'SUPERADMIN' && isset($_GET['inst_id']) && $_GET['inst_id'] !== '') 
              ? intval($_GET['inst_id']) 
              : null;

/* --- 2. DYNAMIC SQL CONSTRUCTION --- */
$search = isset($_GET['s']) ? $conn->real_escape_string($_GET['s']) : '';

// Base conditions
$conditions = [];

// Search Logic
if($search) {
    $conditions[] = "(S.FIRST_NAME LIKE '%$search%' OR S.REGISTRATION_NO LIKE '%$search%' OR S.MOBILE LIKE '%$search%')";
}

// Role-Based Filtering
if ($role === 'SUPERADMIN') {
    if ($instFilter) {
        $conditions[] = "S.INST_ID = $instFilter";
    }
} else {
    // Admin is strictly locked to their session Institute
    $conditions[] = "S.INST_ID = $sessInstId";
}

$whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

$sql = "SELECT S.*, C.COURSE_NAME, I.INST_NAME
        FROM STUDENTS S
        JOIN COURSES C ON C.COURSE_ID = S.COURSE_ID
        JOIN MASTER_INSTITUTES I ON S.INST_ID = I.INST_ID
        $whereClause
        ORDER BY S.STUDENT_ID DESC
        LIMIT 100";

$q = $conn->query($sql);

// Fetch Institutes for Superadmin Dropdown
$institutes = ($role === 'SUPERADMIN') ? $conn->query("SELECT INST_ID, INST_NAME FROM MASTER_INSTITUTES ORDER BY INST_NAME ASC") : null;
?>

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<?php include BASE_PATH.'/admin/layout/sidebar.php'; ?>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($_GET['msg']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0 p-2 p-md-4">
    <div class="d-flex flex-column mb-4 gap-3">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0 text-primary fw-bold">🎓 Student Profile Details</h4><hr>
            <div class="d-flex gap-2">
                <a href="add.php" class="btn btn-primary btn-sm">+ Add Student</a>
                <a href="bulk_upload.php" class="btn btn-success btn-sm">📤 Bulk Upload</a>
            </div>
        </div>

        <form method="GET" class="row g-2 bg-light p-3 rounded border">
            <div class="col-md-4">
                <input type="text" name="s" class="form-control form-control-sm" placeholder="Search Reg, Name, Mobile..." value="<?= htmlspecialchars($search) ?>">
            </div>
            
            <?php if ($role === 'SUPERADMIN'): ?>
            <div class="col-md-4">
                <select name="inst_id" class="form-select form-select-sm">
                    <option value="">-- All Institutes --</option>
                    <?php while($ins = $institutes->fetch_assoc()): ?>
                        <option value="<?= $ins['INST_ID'] ?>" <?= ($instFilter == $ins['INST_ID']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ins['INST_NAME']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <?php endif; ?>

            <div class="col-md-2">
                <button type="submit" class="btn btn-dark btn-sm w-100">Apply Filters</button>
            </div>
            <?php if($search || $instFilter): ?>
            <div class="col-md-2">
                <a href="list.php" class="btn btn-outline-secondary btn-sm w-100">Clear</a>
            </div>
            <?php endif; ?>
        </form>
    </div>

    <div class="table-responsive custom-table-container">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-dark"> <tr>
                <th>Reg No</th>
                <th>Name</th>
                <?php if($role === 'SUPERADMIN'): ?><th>Institute</th><?php endif; ?>
                <th>Course</th>
                <th>Mobile</th>
                <th>Status</th>
                <th class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if($q->num_rows > 0): ?>
                <?php while($r=$q->fetch_assoc()): ?>
                <tr>
                    <td class="fw-bold text-primary"><?= $r['REGISTRATION_NO'] ?></td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm me-2 bg-light rounded-circle text-center" style="width:30px; height:30px; line-height:30px;">
                                <i class="bi bi-person-fill text-secondary"></i>
                            </div>
                            <?= htmlspecialchars($r['FIRST_NAME'].' '.$r['LAST_NAME']) ?>
                        </div>
                    </td>
                    <?php if($role === 'SUPERADMIN'): ?>
                        <td><span class="badge bg-info text-dark"><?= htmlspecialchars($r['INST_NAME']) ?></span></td>
                    <?php endif; ?>
                    <td><?= htmlspecialchars($r['COURSE_NAME']) ?></td>
                    <td><?= $r['MOBILE'] ?></td>
                    <td>
                        <span class="badge rounded-pill <?= $r['STATUS']=='A'?'bg-success':'bg-danger' ?>">
                            <?= $r['STATUS']=='A'?'Active':'Disabled' ?>
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-2 justify-content-center">
                            <a href="view.php?id=<?= $r['STUDENT_ID'] ?>" class="btn btn-sm btn-outline-info" title="View"><i class="bi bi-eye"></i></a>
                            <a href="edit.php?id=<?= $r['STUDENT_ID'] ?>" class="btn btn-sm btn-outline-warning" title="Edit"><i class="bi bi-pencil"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-folder-x display-4 d-block mb-2"></i>
                        No student records found.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<style>
    /* 1. Full Page Fit for Big Screens */
    .card {
        width: 100%;
        border-radius: 12px;
    }

    .table {
        width: 100% !important; /* Forces table to stretch */
        border-collapse: collapse;
    }

    /* 2. Slider Logic for Small Screens */
    .custom-table-container {
        overflow-x: auto; /* Enables horizontal scroll */
        scrollbar-width: thin; /* Firefox slider style */
        scrollbar-color: #6c757d #f8f9fa;
    }

    /* Custom Scrollbar (Slider) for Chrome/Safari/Edge */
    .custom-table-container::-webkit-scrollbar {
        height: 8px; /* Height of the horizontal slider */
    }
    .custom-table-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .custom-table-container::-webkit-scrollbar-thumb {
        background: #adb5bd; 
        border-radius: 10px;
    }
    .custom-table-container::-webkit-scrollbar-thumb:hover {
        background: #6c757d; 
    }

    /* 3. Prevent Text Squeezing */
    .table th, .table td {
        white-space: nowrap; /* Prevents text from breaking into 2 lines */
        padding: 1rem !important;
    }

    /* 4. Desktop Optimization */
    @media (min-width: 992px) {
        .table {
            table-layout: auto; /* Lets columns distribute naturally */
        }
    }
</style>

<?php include BASE_PATH.'/admin/layout/footer.php'; ?>
