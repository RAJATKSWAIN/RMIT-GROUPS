<!--======================================================
    File Name   : bulk_upload.php
    Project     : RMIT Groups - FMS - Fees Management System
    Description : COURSE MANAGEMENT
    Developed By: TrinityWebEdge
    Date Created: 06-02-2025
    Note        : This page defines the FMS - Fees Management System | COURSE MANAGEMENT Module of RMIT Groups website.
=======================================================-->
<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';
require_once BASE_PATH.'/config/audit.php';
require_once BASE_PATH.'/core/validator.php'; 

checkLogin();

// 1. Role & Session Setup
$role = $_SESSION['role_name'];
$sessInstId = $_SESSION['inst_id'];

$count = 0; 
$skipped = 0; 
$errors_list = [];
$success_msg = "";

if (isset($_POST['upload'])) {
    // Determine the target Institute ID
    $targetInstId = ($role === 'SUPERADMIN') ? $_POST['inst_id'] : $sessInstId;

    if (empty($targetInstId)) {
        $errors_list[] = "Global: Please select an institute first.";
    } elseif (!empty($_FILES['csv_file']['tmp_name'])) {
        
        $file = fopen($_FILES['csv_file']['tmp_name'], "r");
        fgetcsv($file); // Skip header row

        while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
            // Skip empty rows
            if (empty(array_filter($data))) continue;

            if (count($data) < 3) {
                $skipped++;
                $errors_list[] = "Row " . ($count + $skipped + 1) . ": Insufficient columns.";
                continue;
            }

            $course_row = [
                'inst_id'  => $targetInstId, // Pass target ID to validator
                'code'     => $data[0],
                'name'     => $data[1],
                'duration' => $data[2]
            ];

            // Note: validateCourseData should check uniqueness within the same INST_ID
            $valErrors = validateCourseData($course_row, $conn);

            if (empty($valErrors)) {
                $code     = strtoupper(trim($data[0]));
                $name     = trim($data[1]);
                $duration = intval($data[2]);

                $stmt = $conn->prepare("INSERT INTO COURSES (INST_ID, COURSE_CODE, COURSE_NAME, DURATION_YEARS, STATUS) VALUES (?, ?, ?, ?, 'A')");
                $stmt->bind_param("issi", $targetInstId, $code, $name, $duration);
                
                if($stmt->execute()) {
                    audit_log($conn, 'BULK_IMPORT', 'COURSES', $conn->insert_id, null, $course_row);
                    $count++;
                } else {
                    $skipped++;
                    $errors_list[] = "Row " . ($count + $skipped + 1) . ": Database error (" . $conn->error . ")";
                }
                $stmt->close();
            } else {
                $skipped++;
                $errors_list[] = "Row " . ($count + $skipped + 1) . ": " . implode(', ', $valErrors);
            }
        }
        fclose($file);
        $success_msg = "Import finished. $count courses added, $skipped rows skipped.";
    }
}

// Fetch Colleges only for Superadmin dropdown
$colleges = ($role === 'SUPERADMIN') ? $conn->query("SELECT INST_ID, INST_NAME FROM MASTER_INSTITUTES ORDER BY INST_NAME ASC") : null;
?>

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<?php include BASE_PATH.'/admin/layout/sidebar.php'; ?>

<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <?php if ($success_msg): ?>
                <div class="alert alert-info border-0 shadow-sm mb-4">
                    <i class="bi bi-check-all me-2"></i> <?= $success_msg ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors_list)): ?>
                <div class="alert alert-danger border-0 shadow-sm mb-4">
                    <h6 class="fw-bold"><i class="bi bi-exclamation-triangle me-2"></i> Row-level Failures:</h6>
                    <div style="max-height: 150px; overflow-y: auto;">
                        <ul class="mb-0 small">
                            <?php foreach ($errors_list as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-file-earmark-spreadsheet me-2"></i>Bulk Course Import</h5>
                    <a href="download_template.php" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-download"></i> Template
                    </a>
                </div>
                
                <div class="card-body p-4">
                    <div class="bg-light p-3 rounded mb-4">
                        <h6 class="fw-bold small mb-2 text-uppercase">CSV Format Instruction:</h6>
                        <table class="table table-sm table-bordered bg-white small mb-0">
                            <thead class="table-secondary">
                                <tr>
                                    <th>Course_Code</th>
                                    <th>Course_Name</th>
                                    <th>Duration_Years</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>BTECH-CS</td>
                                    <td>Bachelor of Technology in CS</td>
                                    <td>4</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <?php if ($role === 'SUPERADMIN'): ?>
                        <div class="mb-4">
                            <label class="form-label fw-bold text-primary small">TARGET INSTITUTE</label>
                            <select name="inst_id" class="form-select border-primary" required>
                                <option value="">-- Select College for this Import --</option>
                                <?php while($ins = $colleges->fetch_assoc()): ?>
                                    <option value="<?= $ins['INST_ID'] ?>"><?= $ins['INST_NAME'] ?></option>
                                <?php endwhile; ?>
                            </select>
                            <div class="form-text">All courses in the CSV will be assigned to this college.</div>
                        </div>
                        <?php endif; ?>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Select CSV File</label>
                            <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="list.php" class="text-decoration-none text-muted small"><i class="bi bi-arrow-left"></i> Back to Courses</a>
                            <button type="submit" name="upload" class="btn btn-success px-5 fw-bold">
                                <i class="bi bi-cloud-arrow-up me-2"></i>Process Upload
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include BASE_PATH.'/admin/layout/footer.php'; ?>
