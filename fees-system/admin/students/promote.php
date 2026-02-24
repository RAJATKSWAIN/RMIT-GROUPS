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

/* --- 1. CONFIGURATION & CORE FILES --- */
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';
require_once BASE_PATH.'/services/student_service.php';

checkLogin();

/* --- 2. MULTI-INSTITUTE CONTEXT --- */
$role = $_SESSION['role_name'];
// Allow Superadmin to change institute context via GET/POST if needed, else use Session
$instId = ($role === 'SUPERADMIN' && isset($_GET['target_inst_id'])) ? intval($_GET['target_inst_id']) : $_SESSION['inst_id'];

$course_id = $_GET['course_id'] ?? '';
$students = [];
$course_info = null;

/* --- 3. FETCH DROPDOWNS (INSTITUTE SPECIFIC) --- */
$courses = $conn->query("SELECT COURSE_ID, COURSE_NAME, COURSE_CODE FROM COURSES WHERE STATUS = 'A' AND INST_ID = $instId");
$institutes = ($role === 'SUPERADMIN') ? $conn->query("SELECT INST_ID, INST_NAME FROM MASTER_INSTITUTES ORDER BY INST_NAME ASC") : null;

/* --- 4. FETCH STUDENT LIST BASED ON SELECTION --- */
if (!empty($course_id)) {
    // Validate that the course actually belongs to the selected Institute
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
                            <label class="form-label fw-bold text-primary small text-uppercase">Institute Context</label>
                            <select name="target_inst_id" class="form-select" onchange="this.form.submit()">
                                <?php while($ins = $institutes->fetch_assoc()): ?>
                                    <option value="<?= $ins['INST_ID'] ?>" <?= ($instId == $ins['INST_ID']) ? 'selected' : '' ?>>
                                        <?= $ins['INST_NAME'] ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <?php endif; ?>

                        <div class="col-md-5">
                            <label class="form-label fw-bold text-secondary">Course Category</label>
                            <select name="course_id" class="form-select" required onchange="this.form.submit()">
                                <option value="">-- Choose Course --</option>
                                <?php if($courses): while($c = $courses->fetch_assoc()): ?>
                                    <option value="<?= $c['COURSE_ID'] ?>" <?= ($course_id == $c['COURSE_ID']) ? 'selected' : '' ?>>
                                        <?= $c['COURSE_CODE'] ?> - <?= $c['COURSE_NAME'] ?>
                                    </option>
                                <?php endwhile; endif; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <a href="promote_student.php" class="btn btn-outline-secondary w-100">Reset</a>
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
                        <div class="p-3 bg-white border-bottom d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-primary px-3 py-2"><?= $course_info['COURSE_CODE'] ?></span>
                                <span class="ms-2 text-muted fw-bold">Max Semesters: <?= $max_sem ?></span>
                            </div>
                            
                            <div class="d-flex gap-2 align-items-center">
                                <label class="fw-bold small text-uppercase">Promote To:</label>
                                <select name="target_semester" class="form-select fw-bold border-success" style="width: 150px;" required>
                                    <option value="">-- Select --</option>
                                    <?php 
                                    for($i=2; $i<=$max_sem; $i++) {
                                        $label = ($i % 2 != 0) ? "Sem $i (New Year)" : "Sem $i";
                                        echo "<option value='$i'>$label</option>";
                                    }
                                    ?>
                                </select>
                                <button type="submit" name="bulk_promote" class="btn btn-success px-4" onclick="return confirm('Promote selected students? Ledger will be updated if it is a New Year.')">
                                    Execute Promotion
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="ps-4" width="40"><input type="checkbox" class="form-check-input" id="selectAll"></th>
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
                                            <td class="ps-4"><input type="checkbox" name="student_ids[]" value="<?= $row['STUDENT_ID'] ?>" class="form-check-input student-checkbox"></td>
                                            <td><span class="text-primary fw-bold"><?= $row['REGISTRATION_NO'] ?></span></td>
                                            <td><span class="badge bg-light text-dark border"><?= $row['ROLL_NO'] ?></span></td>
                                            <td class="text-uppercase"><?= $row['FIRST_NAME'] ?> <?= $row['LAST_NAME'] ?></td>
                                            <td class="text-center">
                                                <span class="badge rounded-pill bg-info text-dark px-3">Sem <?= $row['SEMESTER'] ?></span>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="5" class="text-center py-5 text-muted">No students found for promotion.</td></tr>
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
    // Selection Logic
    document.getElementById('selectAll')?.addEventListener('click', function() {
        document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = this.checked);
    });

    // Validation Logic
    document.getElementById('promotionForm')?.addEventListener('submit', function(e) {
        const checked = document.querySelectorAll('.student-checkbox:checked').length;
        if (checked === 0) {
            alert('Please select at least one student.');
            e.preventDefault();
        }
    });
</script>
