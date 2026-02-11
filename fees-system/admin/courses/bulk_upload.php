<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';
require_once BASE_PATH.'/config/audit.php';
require_once BASE_PATH.'/core/validator.php'; 

checkLogin();

$count = 0; 
$skipped = 0; 
$errors_list = [];
$success_msg = "";

if (isset($_POST['upload'])) {
    if (!empty($_FILES['csv_file']['tmp_name'])) {
        $file = fopen($_FILES['csv_file']['tmp_name'], "r");
        fgetcsv($file); // Skip header row

        while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
            // Ensure we have enough columns to avoid "Undefined Offset"
            if (count($data) < 3) {
                $skipped++;
                $errors_list[] = "Row " . ($count + $skipped + 1) . ": Insufficient columns.";
                continue;
            }

            $course_row = [
                'code'     => $data[0],
                'name'     => $data[1],
                'duration' => $data[2]
            ];

            // Validates Course Code Uniqueness and Duration Numeric check
            $valErrors = validateCourseData($course_row, $conn);

            if (empty($valErrors)) {
                $code     = strtoupper(trim($data[0]));
                $name     = trim($data[1]);
                $duration = intval($data[2]);

                $stmt = $conn->prepare("INSERT INTO COURSES (COURSE_CODE, COURSE_NAME, DURATION_YEARS) VALUES (?, ?, ?)");
                $stmt->bind_param("ssi", $code, $name, $duration);
                
                if($stmt->execute()) {
                    audit_log($conn, 'BULK_IMPORT', 'COURSES', $conn->insert_id, null, $course_row);
                    $count++;
                } else {
                    $skipped++;
                    $errors_list[] = "Row " . ($count + $skipped + 1) . ": Database error during insert.";
                }
                $stmt->close();
            } else {
                $skipped++;
                $errors_list[] = "Row " . ($count + $skipped + 1) . ": " . implode(', ', $valErrors);
            }
        }
        fclose($file);
        $success_msg = "Import finished. $count courses added, $skipped skipped.";
    }
}
?>

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<?php include BASE_PATH.'/admin/layout/sidebar.php'; ?>

<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <?php if ($success_msg): ?>
                <div class="alert alert-info shadow-sm">
                    <i class="bi bi-info-circle-fill me-2"></i> <?= $success_msg ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors_list)): ?>
                <div class="alert alert-danger shadow-sm">
                    <h6 class="fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i> Some rows failed validation:</h6>
                    <ul class="mb-0 small">
                        <?php foreach ($errors_list as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-upload"></i> Bulk Course Upload</h5>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="mb-0 text-muted small">Required CSV format:</h6>
    <a href="download_template.php" class="btn btn-sm btn-outline-primary">
        <i class="bi bi-download"></i> Download Sample CSV
    </a>
</div>

<table class="table table-sm table-bordered bg-light small mb-3">
    <thead>
        <tr class="table-secondary">
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
                
                
                <div class="card-body p-4">
                    <p class="text-muted small">
                        Please upload a <strong>.csv</strong> file. The format should be: <br>
                        <code>Course_Code, Course_Name, Duration_Years</code>
                    </p>
                    
                    <form method="POST" enctype="multipart/form-data" class="mt-3">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Select CSV File</label>
                            <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="list.php" class="btn btn-outline-secondary">Back to List</a>
                            <button type="submit" name="upload" class="btn btn-success px-5">
                                <i class="bi bi-cloud-arrow-up"></i> Start Upload
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include BASE_PATH.'/admin/layout/footer.php'; ?>