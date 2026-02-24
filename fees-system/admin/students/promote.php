<!--======================================================
    File Name   : promote.php
    Project     : RMIT Groups - FMS - Fees Management System
	Module		: STUDENT MANAGEMENT
    Description : Student Registration & Profile Management
    Developed By: TrinityWebEdge
    Date Created: 05-02-2025
    Last Updated: 24-02-2026
    Note        : This page defines the FMS - Fees Management System | Student Module of RMIT Groups website.
=======================================================-->

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';
require_once BASE_PATH.'/services/student_service.php';

checkLogin();

/* --- 2. MULTI-INSTITUTE CONTEXT --- */
$role = $_SESSION['role_name'];
// Security: Use target_inst_id if Superadmin, else strictly force Session ID
$instId = ($role === 'SUPERADMIN' && isset($_GET['target_inst_id'])) ? intval($_GET['target_inst_id']) : $_SESSION['inst_id'];

$course_id = $_GET['course_id'] ?? '';
$students = [];
$course_info = null;

/* --- 3. FETCH DROPDOWNS --- */
// Added sorting and status check
$courses = $conn->query("SELECT COURSE_ID, COURSE_NAME, COURSE_CODE FROM COURSES WHERE STATUS = 'A' AND INST_ID = $instId ORDER BY COURSE_NAME ASC");
$institutes = ($role === 'SUPERADMIN') ? $conn->query("SELECT INST_ID, INST_NAME FROM MASTER_INSTITUTES ORDER BY INST_NAME ASC") : null;

/* --- 4. FETCH STUDENT LIST --- */
if (!empty($course_id)) {
    $c_stmt = $conn->prepare("SELECT DURATION_YEARS, COURSE_CODE FROM COURSES WHERE COURSE_ID = ? AND INST_ID = ?");
    $c_stmt->execute([$course_id, $instId]);
    $course_info = $c_stmt->get_result()->fetch_assoc();

    if ($course_info) {
        $stmt = $conn->prepare("SELECT STUDENT_ID, REGISTRATION_NO, ROLL_NO, FIRST_NAME, LAST_NAME, SEMESTER 
                                FROM STUDENTS 
                                WHERE COURSE_ID = ? AND INST_ID = ? AND STATUS = 'A' 
                                ORDER BY ROLL_NO ASC");
        $stmt->execute([$course_id, $instId]);
        $students = $stmt->get_result();
    }
}
?>

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<?php include BASE_PATH.'/admin/layout/sidebar.php'; ?>

<div class="container-fluid mt-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="../dashboard.php"> Dashboard</a></li>
        <li class="breadcrumb-item active">Student Management</li>
      </ol>
    </nav>

    <div class="container-fluid mt-4">
    <?php if(isset($_SESSION['msg'])): ?>
        <div class="alert alert-<?= $_SESSION['msg_type']; ?> alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i> <?= $_SESSION['msg']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['msg'], $_SESSION['msg_type']); ?>
    <?php endif; ?>
        
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="bi bi-person-up me-2"></i>Academic Promotion Module</h5>
                </div>
                <div class="card-body bg-light">
                    <form method="GET" class="row g-3 align-items-end">
                        <?php if ($role === 'SUPERADMIN'): ?>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-primary small">INSTITUTE CONTEXT</label>
                            <select name="target_inst_id" class="form-select border-primary" onchange="this.form.submit()">
                                <?php while($ins = $institutes->fetch_assoc()): ?>
                                    <option value="<?= $ins['INST_ID'] ?>" <?= ($instId == $ins['INST_ID']) ? 'selected' : '' ?>>
                                        <?= $ins['INST_NAME'] ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <?php endif; ?>

                        <div class="col-md-5">
                            <label class="form-label fw-bold text-secondary small">COURSE CATEGORY</label>
                            <select name="course_id" class="form-select" required onchange="this.form.submit()">
                                <option value="">-- Select Course --</option>
                                <?php if($courses): while($c = $courses->fetch_assoc()): ?>
                                    <option value="<?= $c['COURSE_ID'] ?>" <?= ($course_id == $c['COURSE_ID']) ? 'selected' : '' ?>>
                                        <?= $c['COURSE_CODE'] ?> - <?= $c['COURSE_NAME'] ?>
                                    </option>
                                <?php endwhile; endif; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <a href="promote.php" class="btn btn-outline-secondary w-100">Reset</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php if ($course_info): 
            $max_sem = $course_info['DURATION_YEARS'] * 2;
        ?>
        <div class="col-md-12">
            <form action="process_promotion.php" method="POST" id="promotionForm">
                <input type="hidden" name="course_id" value="<?= $course_id ?>">
                <input type="hidden" name="inst_id" value="<?= $instId ?>">
                
                <div class="card shadow-sm border-0">
                    <div class="card-body p-0">
                        <div class="p-3 bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <span class="badge bg-primary px-3 py-2"><?= $course_info['COURSE_CODE'] ?></span>
                                <span class="ms-2 text-dark fw-bold">Duration: <?= $course_info['DURATION_YEARS'] ?> Years (Max Sem: <?= $max_sem ?>)</span>
                            </div>
                            
                            <div class="d-flex gap-2 align-items-center">
                                <label class="fw-bold small text-uppercase text-danger">Target Semester:</label>
                                <select name="target_semester" class="form-select fw-bold border-success" style="width: 180px;" required>
                                    <option value="">-- Choose --</option>
                                    <?php 
                                    for($i=2; $i<=$max_sem; $i++) {
                                        // Highlight Odd Semesters as Fee Triggering points
                                        $label = ($i % 2 != 0) ? "Sem $i (New Year Fee)" : "Sem $i";
                                        echo "<option value='$i'>$label</option>";
                                    }
                                    ?>
                                </select>
                                <button type="submit" name="bulk_promote" class="btn btn-success px-4 shadow-sm">
                                    <i class="bi bi-check-circle me-1"></i> Promote Now
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="ps-4" width="40">
    										<input type="checkbox" class="form-check-input" id="selectAll">
										</th>
                                        <th>Reg No</th>
                                        <th>Roll No</th>
                                        <th>Student Name</th>
                                        <th class="text-center">Current Semester</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($students && $students->num_rows > 0): ?>
                                        <?php while($row = $students->fetch_assoc()): ?>
                                        <tr>
                                            
											<td class="ps-4">
    											<input type="checkbox" name="student_ids[]" value="<?= $row['STUDENT_ID'] ?>" class="form-check-input student-checkbox">
											</td>
                                            <td><span class="text-primary fw-bold"><?= $row['REGISTRATION_NO'] ?></span></td>
                                            <td><span class="badge bg-light text-dark border"><?= $row['ROLL_NO'] ?></span></td>
                                            <td class="text-uppercase fw-semibold"><?= $row['FIRST_NAME'] ?> <?= $row['LAST_NAME'] ?></td>
                                            <td class="text-center">
                                                <span class="badge rounded-pill bg-info text-dark px-3">Semester <?= $row['SEMESTER'] ?></span>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="5" class="text-center py-5 text-muted">No active students found.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include BASE_PATH.'/admin/layout/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const studentCheckboxes = document.querySelectorAll('.student-checkbox');
    const promotionForm = document.getElementById('promotionForm');

    // 1. Toggle all checkboxes when header checkbox is clicked
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            studentCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }

    // 2. Form Validation: Ensure at least one student is selected before submitting
    if (promotionForm) {
        promotionForm.addEventListener('submit', function(e) {
            const checkedCount = document.querySelectorAll('.student-checkbox:checked').length;
            const targetSem = document.querySelector('select[name="target_semester"]').value;

            if (checkedCount === 0) {
                e.preventDefault();
                alert('Please select at least one student for promotion.');
                return false;
            }

            if (!targetSem) {
                e.preventDefault();
                alert('Please select a target semester.');
                return false;
            }

            if (!confirm(`Are you sure you want to promote ${checkedCount} students? This action will update their academic records.`)) {
                e.preventDefault();
            }
        });
    }
});
</script>
