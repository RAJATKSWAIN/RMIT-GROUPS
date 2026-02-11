<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';

checkLogin();

$message = "";

// --- HANDLE ACTION ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $status_to_set = ($_POST['action'] == 'disable') ? 'I' : 'A';
    
    // 1. Bulk Disable Logic
    if (isset($_POST['student_ids']) && is_array($_POST['student_ids'])) {
        $ids = implode(',', array_map('intval', $_POST['student_ids']));
        $conn->query("UPDATE STUDENTS SET STATUS = '$status_to_set' WHERE STUDENT_ID IN ($ids)");
        $message = "Selected students updated successfully.";
    } 
    // 2. Single Search Disable Logic
    elseif (!empty($_POST['search_id'])) {
        $search = $_POST['search_id'];
        $stmt = $conn->prepare("UPDATE STUDENTS SET STATUS = '$status_to_set' WHERE REGISTRATION_NO = ? OR ROLL_NO = ?");
        $stmt->bind_param("ss", $search, $search);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $message = "Student status updated successfully.";
        } else {
            $message = "No student found with that ID.";
        }
    }
}

// Fetch only Active students for the list (to be disabled)
$students = $conn->query("SELECT S.*, C.COURSE_NAME FROM STUDENTS S JOIN COURSES C ON S.COURSE_ID = C.COURSE_ID WHERE S.STATUS = 'A' ORDER BY S.FIRST_NAME ASC");

//include BASE_PATH.'/admin/layout/slider.php'; 
?>

<?php include BASE_PATH.'/admin/layout/header.php'; ?>

<?php include BASE_PATH.'/admin/layout/sidebar.php'; ?>

<div class="container-fluid">
    <?php if ($message): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <?= $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-danger">Single Disable</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label small">Reg No or Roll No</label>
                            <input type="text" name="search_id" class="form-control" placeholder="Enter ID..." required>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" name="action" value="disable" class="btn btn-danger">Disable Student</button>
                            <button type="submit" name="action" value="activate" class="btn btn-outline-success">Re-Activate</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card shadow-sm border-0 mt-4 bg-light">
                <div class="card-body small text-muted">
                    <strong>Note:</strong> Disabling a student will set their status to 'I' (Inactive). They will still remain in the database for financial records.
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <form method="POST" id="bulkForm">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Active Students List</h5>
                        <button type="submit" name="action" value="disable" class="btn btn-sm btn-danger" onclick="return confirm('Disable selected students?')">
                            Disable Selected
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40" class="text-center">
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                        <th>Student Details</th>
                                        <th>Course</th>
                                        <th>Reg No</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($students->num_rows > 0): ?>
                                        <?php while($r = $students->fetch_assoc()): ?>
                                        <tr>
                                            <td class="text-center">
                                                <input type="checkbox" name="student_ids[]" value="<?= $r['STUDENT_ID'] ?>" class="form-check-input student-checkbox">
                                            </td>
                                            <td>
                                                <div class="fw-bold"><?= $r['FIRST_NAME'] ?> <?= $r['LAST_NAME'] ?></div>
                                                <div class="small text-muted"><?= $r['MOBILE'] ?></div>
                                            </td>
                                            <td><?= $r['COURSE_NAME'] ?></td>
                                            <td class="text-primary fw-bold"><?= $r['REGISTRATION_NO'] ?></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="4" class="text-center py-4">No active students found.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Checkbox "Select All" Logic
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.student-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });
</script>

<?php include BASE_PATH.'/admin/layout/footer.php'; ?>