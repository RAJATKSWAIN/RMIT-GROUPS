<!--======================================================
    File Name   : bulk_upload.php
    Project     : RMIT Groups - FMS - Fees Management System
    Module      : STUDENT MANAGEMENT
    Description : Student Registration & Profile Management
    Developed By: TrinityWebEdge
    Date Created: 06-02-2026
    Last Updated: 25-02-2026
    Note        : This page defines the FMS - Fees Management System | Student Module of RMIT Groups website.
=======================================================-->
<?php
// bulk_upload.php - FMS V 1.0.0
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/fees-system');

require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/audit.php';
require_once BASE_PATH . '/core/auth.php';
require_once BASE_PATH . '/services/student_service.php';
require_once BASE_PATH . '/core/validator.php';

checkLogin();

$role = $_SESSION['role_name'];
$sessInstId = $_SESSION['inst_id'];
$message = "";
$error = "";
$preview_data = [];
$is_preview = false;
$can_import = true;

// 1. Fetch Institutes for Superadmin Dropdown
$institutes = ($role === 'SUPERADMIN') ? $conn->query("SELECT INST_ID, INST_NAME FROM MASTER_INSTITUTES ORDER BY INST_NAME ASC") : null;

// --- STEP A: HANDLE CSV UPLOAD & VALIDATION ---
if (isset($_POST['upload'])) {
    try {
        $target_inst = ($role === 'SUPERADMIN') ? intval($_POST['target_inst_id']) : $sessInstId;
        
        $file_err = validateCSV($_FILES['csv_file']);
        if ($file_err) throw new Exception($file_err);

        $handle = fopen($_FILES['csv_file']['tmp_name'], "r");
        fgetcsv($handle); // Skip header row

        while (($row = fgetcsv($handle)) !== FALSE) {
            if (empty(array_filter($row))) continue;

            // Map row for validation
            $row_data = [
                'reg' => trim($row[0]), 'roll' => trim($row[1]), 'fname' => trim($row[2]),
                'lname' => trim($row[3]), 'father_name' => trim($row[4]), 'mother_name' => trim($row[5]),
                'gender' => strtoupper(trim($row[6])), 'dob' => trim($row[7]), 'mobile' => trim($row[8]),
                'email' => trim($row[9]), 'address' => trim($row[10]), 'city' => trim($row[11]),
                'state' => trim($row[12]), 'pincode' => trim($row[13]), 'course' => intval($row[14]),
                'semester' => intval($row[15]), 'admission' => trim($row[16]), 'inst_id' => $target_inst
            ];

            $remarks = [];
            // Security: Check if Course ID exists for this specific Institute
            $stmt = $conn->prepare("SELECT COURSE_ID FROM COURSES WHERE COURSE_ID = ? AND INST_ID = ?");
            $stmt->bind_param("ii", $row_data['course'], $target_inst);
            $stmt->execute();
            if ($stmt->get_result()->num_rows == 0) $remarks[] = "Course ID {$row_data['course']} not linked to this Institute.";

            // Standard Field Validation
            $val_errs = validateStudentData($row_data, $conn);
            if (!empty($val_errs)) $remarks = array_merge($remarks, $val_errs);

            $row['status'] = empty($remarks) ? 'VALID' : 'INVALID';
            $row['remarks'] = empty($remarks) ? 'Ready' : implode(" | ", $remarks);
            
            if ($row['status'] === 'INVALID') $can_import = false;
            $preview_data[] = $row;
        }
        fclose($handle);
        $is_preview = true;
    } catch (Exception $e) {
        $error = "Validation Error: " . $e->getMessage();
    }
}

// --- STEP B: HANDLE FINAL DATABASE IMPORT ---
if (isset($_POST['confirm_import'])) {
    $target_inst = intval($_POST['final_inst_id']);
    $rows = json_decode($_POST['serialized_data'], true);

    $conn->begin_transaction();
    try {
        $inserted = 0;
        foreach ($rows as $row) {
            $data = [
                'reg' => $row[0], 'roll' => $row[1], 'fname' => $row[2], 'lname' => $row[3],
                'father_name' => $row[4], 'mother_name' => $row[5], 'gender' => $row[6],
                'dob' => $row[7], 'mobile' => $row[8], 'email' => $row[9], 'address' => $row[10],
                'city' => $row[11], 'state' => $row[12], 'pincode' => $row[13],
                'course' => $row[14], 'semester' => $row[15], 'admission' => $row[16],
                'inst_id' => $target_inst
            ];

            if (!createStudent($conn, $data, true)) {
                throw new Exception("SQL Error at Reg No: " . $data['reg']);
            }
            $inserted++;
        }

        audit_log($conn, 'BULK_IMPORT', 'STUDENTS', 0, null, json_encode(['count' => $inserted, 'inst' => $target_inst]));
        $conn->commit();
        $message = "Success! $inserted students have been imported.";
        $is_preview = false;
    } catch (Exception $e) {
        $conn->rollback();
        $error = "Import Failed: " . $e->getMessage();
    }
}
?>

<?php include BASE_PATH . '/admin/layout/header.php'; ?>
<?php include BASE_PATH . '/admin/layout/sidebar.php'; ?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-excel me-2"></i>Bulk Student Import</h5>
                </div>
                <div class="card-body p-4">
                    
                    <?php if ($message): ?>
                        <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i><?= $message ?></div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= $error ?></div>
                    <?php endif; ?>

                    <?php if (!$is_preview): ?>
                        <form action="" method="POST" enctype="multipart/form-data">
                            
                            <?php if ($role === 'SUPERADMIN'): ?>
                                <div class="mb-4 p-3 bg-light border rounded">
                                    <label class="form-label fw-bold text-danger">Target Institute</label>
                                    <select name="target_inst_id" class="form-select border-danger" required>
                                        <option value="">-- Choose Institute to Upload Into --</option>
                                        <?php while($inst = $institutes->fetch_assoc()): ?>
                                            <option value="<?= $inst['INST_ID'] ?>"><?= $inst['INST_NAME'] ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            <?php endif; ?>

                            <div class="mb-4">
                                <label class="form-label fw-bold">1. Download Template</label>
                                <br>
                                <a href="template.php" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-download"></i> Get Correct CSV Structure
                                </a>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">2. Upload Filled CSV</label>
                                <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                                <div class="form-text mt-2">
                                    <strong>Required Columns (17):</strong><br>
                                    <code class="small text-primary">
                                        RegNo, RollNo, FName, LName, FatherName, MotherName, Gender, DOB, Mobile, Email, Address, City, State, Pincode, CourseID, Semester, AdmDate
                                    </code>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" name="upload" class="btn btn-primary btn-lg">
                                    <i class="bi bi-search me-2"></i> Validate & Review Records
                                </button>
                            </div>
                        </form>

                    <?php else: ?>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0 text-primary">Pre-Import Validation Report</h6>
                            <button onclick="downloadReport()" type="button" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-download me-1"></i> Download Report for Reference
                            </button>
                        </div>

                        <div class="table-responsive border rounded mb-4" style="max-height: 450px;">
                            <table class="table table-sm table-hover align-middle mb-0" id="valTable">
                                <thead class="table-dark sticky-top">
                                    <tr>
                                        <th>Reg No</th><th>Student Name</th><th>Course</th><th>Status</th><th>Remark</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($preview_data as $row): ?>
                                        <tr class="<?= $row['status'] === 'INVALID' ? 'table-danger' : '' ?>">
                                            <td><?= htmlspecialchars($row[0]) ?></td>
                                            <td><?= htmlspecialchars($row[2].' '.$row[3]) ?></td>
                                            <td><?= htmlspecialchars($row[14]) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $row['status'] === 'VALID' ? 'success' : 'danger' ?>">
                                                    <?= $row['status'] ?>
                                                </span>
                                            </td>
                                            <td class="small text-<?= $row['status'] === 'INVALID' ? 'danger' : 'muted' ?>">
                                                <?= htmlspecialchars($row['remarks']) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="bulk_upload.php" class="btn btn-light border">Discard & Go Back</a>
                            
                            <?php if ($can_import): ?>
                                <form action="" method="POST">
                                    <input type="hidden" name="final_inst_id" value="<?= $target_inst ?>">
                                    <input type="hidden" name="serialized_data" value='<?= json_encode($preview_data) ?>'>
                                    <button type="submit" name="confirm_import" class="btn btn-success px-5 shadow-sm">
                                        <i class="bi bi-cloud-check-fill me-2"></i> Confirm Final Import
                                    </button>
                                </form>
                            <?php else: ?>
                                <div class="alert alert-warning py-2 mb-0 small">
                                    <i class="bi bi-exclamation-circle me-1"></i> 
                                    Import blocked. Please fix the <strong>INVALID</strong> rows in your CSV and re-upload.
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
function downloadReport() {
    let csv = [];
    const rows = document.querySelectorAll("#valTable tr");
    for (let i = 0; i < rows.length; i++) {
        let row = [], cols = rows[i].querySelectorAll("td, th");
        for (let j = 0; j < cols.length; j++) row.push('"' + cols[j].innerText + '"');
        csv.push(row.join(","));
    }
    const blob = new Blob([csv.join("\n")], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = "Validation_Report_<?= date('Ymd_His') ?>.csv";
    a.click();
}
</script>

<?php include BASE_PATH . '/admin/layout/footer.php'; ?>
