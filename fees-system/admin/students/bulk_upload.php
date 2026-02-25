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
require_once BASE_PATH . '/core/auth.php';
require_once BASE_PATH . '/services/student_service.php';
require_once BASE_PATH . '/core/validator.php';

checkLogin();

$role = $_SESSION['role_name'];
$sessInstId = $_SESSION['inst_id'];
$message = "";
$error = "";

// 1. Fetch Institutes for Superadmin Dropdown
$institutes = ($role === 'SUPERADMIN') ? $conn->query("SELECT INST_ID, INST_NAME FROM MASTER_INSTITUTES ORDER BY INST_NAME ASC") : null;

if (isset($_POST['upload'])) {
    // Determine target institute
    $target_inst = ($role === 'SUPERADMIN') ? intval($_POST['target_inst_id']) : $sessInstId;

    // A. Check File Validity
    $file_err = validateCSV($_FILES['csv_file']);
    if ($file_err) {
        $error = $file_err;
    } else {
        // B. Check CSV Content Structure (Now 17 Columns due to Father/Mother names)
        $content_errs = validateCSVContent($_FILES['csv_file']['tmp_name'], 17);
        if ($content_errs) {
            $error = is_array($content_errs) ? implode("<br>", array_slice($content_errs, 0, 5)) : $content_errs;
        } else {
            $handle = fopen($_FILES['csv_file']['tmp_name'], "r");
            fgetcsv($handle); // Skip header

            $inserted_count = 0;
            $failed_rows = [];
            $row_num = 2;

            $conn->begin_transaction();
            try {
                while (($row = fgetcsv($handle)) !== FALSE) {
                    if (empty(array_filter($row))) continue;

                    $data = [
                        'reg'           => trim($row[0]),
                        'roll'          => trim($row[1]),
                        'fname'         => trim($row[2]),
                        'lname'         => trim($row[3]),
                        'father_name'   => trim($row[4]), // Added
                        'mother_name'   => trim($row[5]), // Added
                        'gender'        => strtoupper(trim($row[6])),
                        'dob'           => trim($row[7]),
                        'mobile'        => trim($row[8]),
                        'email'         => trim($row[9]),
                        'address'       => trim($row[10]),
                        'city'          => trim($row[11]),
                        'state'         => trim($row[12]),
                        'pincode'       => trim($row[13]),
                        'course'        => intval(trim($row[14])),
                        'semester'      => intval(trim($row[15])),
                        'admission'     => trim($row[16]),
                        'inst_id'       => $target_inst // Assigned based on UI selection or Session
                    ];

                    // C. SECURITY: Ensure Course belongs to the selected Institute
                    $courseCheck = $conn->query("SELECT COURSE_ID FROM COURSES WHERE COURSE_ID = {$data['course']} AND INST_ID = $target_inst");

                    if ($courseCheck->num_rows == 0) {
                        $failed_rows[] = "Row $row_num: Course ID {$data['course']} not found in the selected Institute.";
                        $row_num++;
                        continue;
                    }

                    // D. Validate row data (Ensure your validator handles father/mother names)
                    $data_errs = validateStudentData($data, $conn);
                    
                    if (empty($data_errs)) {
                        if (createStudent($conn, $data, true)) {
                            $inserted_count++;
                        } else {
                            $failed_rows[] = "Row $row_num (Reg: {$data['reg']}): Database error.";
                        }
                    } else {
                        $failed_rows[] = "Row $row_num: " . $data_errs[0];
                    }
                    $row_num++;
                }

                $conn->commit();
                $message = "Success! $inserted_count students imported.";
                if (!empty($failed_rows)) {
                    $error = "Errors found:<br>" . implode("<br>", array_slice($failed_rows, 0, 5));
                }

            } catch (Exception $e) {
                $conn->rollback();
                $error = "System Error: " . $e->getMessage();
            }
            fclose($handle);
        }
    }
}
?>

<?php include BASE_PATH . '/admin/layout/header.php'; ?>
<?php include BASE_PATH . '/admin/layout/sidebar.php'; ?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-excel me-2"></i>Bulk Student Import</h5>
                </div>
                <div class="card-body p-4">
                    
                    <?php if ($message): ?>
                        <div class="alert alert-success"><?= $message ?></div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger small"><?= $error ?></div>
                    <?php endif; ?>

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
                            <small class="text-muted">As Superadmin, you must select which college these students belong to.</small>
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
                                <code class="small">
                                    RegNo, RollNo, FName, LName, FatherName, MotherName, Gender, DOB, Mobile, Email, Address, City, State, Pincode, CourseID, Semester,	AdmDate
                                </code>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" name="upload" class="btn btn-primary btn-lg">
                                <i class="bi bi-cloud-check me-2"></i> Start Bulk Import
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include BASE_PATH . '/admin/layout/footer.php'; ?>
