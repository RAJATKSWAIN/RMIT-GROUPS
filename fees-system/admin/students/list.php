<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';
checkLogin();

// Simple Search Logic
$search = isset($_GET['s']) ? $conn->real_escape_string($_GET['s']) : '';
$where = "";
if($search) {
    $where = "WHERE S.FIRST_NAME LIKE '%$search%' OR S.REGISTRATION_NO LIKE '%$search%' OR S.MOBILE LIKE '%$search%'";
}

$q = $conn->query("
    SELECT S.*, C.COURSE_NAME
    FROM STUDENTS S
    JOIN COURSES C ON C.COURSE_ID=S.COURSE_ID
    $where
    ORDER BY S.STUDENT_ID DESC
    LIMIT 100
");
?>

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<?php include BASE_PATH.'/admin/layout/sidebar.php'; ?>

<div class="card shadow-sm border-0 p-2 p-md-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <h4 class="mb-0">🎓 Students Profiles</h4>
        
        <form class="d-flex gap-2 w-100 w-md-auto">
            <input type="text" name="s" class="form-control form-control-sm" placeholder="Search Reg, Name, Mobile..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-dark btn-sm">Search</button>
        </form>

        <div class="d-flex gap-2 w-100 w-md-auto">
            <a href="add.php" class="btn btn-primary btn-sm flex-fill">+ Add Student</a>
            <a href="bulk_upload.php" class="btn btn-success btn-sm flex-fill">📤 Bulk Upload</a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle resp-table mb-0">
            <thead class="table-light">
                <tr>
                    <th>Reg No</th>
                    <th>Name</th>
                    <th>Course</th>
                    <th>Mobile</th>
                    <th>Status</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($r=$q->fetch_assoc()): ?>
                <tr>
                    <td class="fw-bold"><?= $r['REGISTRATION_NO'] ?></td>
                    <td><?= htmlspecialchars($r['FIRST_NAME'].' '.$r['LAST_NAME']) ?></td>
                    <td><?= htmlspecialchars($r['COURSE_NAME']) ?></td>
                    <td><?= $r['MOBILE'] ?></td>
                    <td>
                        <span class="badge <?= $r['STATUS']=='A'?'bg-success':'bg-danger' ?>">
                            <?= $r['STATUS']=='A'?'Active':'Disabled' ?>
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-2 justify-content-center">
                            <a href="view.php?id=<?= $r['STUDENT_ID'] ?>" class="btn btn-sm btn-info text-white"><i class="bi bi-eye"></i></a>
                            <a href="edit.php?id=<?= $r['STUDENT_ID'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    
    /* Force the table to allow horizontal scrolling on small screens */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        margin-bottom: 1rem;
    }

    /* Prevent text from wrapping so the table keeps its width while sliding */
    .resp-table th, .resp-table td {
        white-space: nowrap;
        padding: 12px 15px;
    }

    /* Make buttons stay a clickable size on mobile */
    .btn-sm {
        padding: 0.4rem 0.6rem;
    }
</style>

<?php include BASE_PATH.'/admin/layout/footer.php'; ?>
