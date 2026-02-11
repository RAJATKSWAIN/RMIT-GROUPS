<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/fees-system');

require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/core/auth.php';
require_once BASE_PATH . '/services/student_service.php';
require_once BASE_PATH . '/core/validator.php';

checkLogin();

$message = "";
$error = "";

if (isset($_POST['upload'])) {
    // 1. Check File Validity
    $file_err = validateCSV($_FILES['csv_file']);
    if ($file_err) {
        $error = $file_err;
    } else {
        // 2. Check CSV Content Structure
        $content_errs = validateCSVContent($_FILES['csv_file']['tmp_name'], 15);
        if ($content_errs) {
            $error = is_array($content_errs) ? implode("<br>", array_slice($content_errs, 0, 5)) : $content_errs;
        } else {
            $handle = fopen($_FILES['csv_file']['tmp_name'], "r");
            fgetcsv($handle); // Skip header

            $inserted_count = 0;
            $failed_rows = []; // To store rows that didn't validate
            $row_num = 2; // Start from 2 because 1 is header

            $conn->begin_transaction();
            try {
                while (($row = fgetcsv($handle)) !== FALSE) {
                    if (empty(array_filter($row))) continue;

                    $data = [
                        'reg'       => trim($row[0]),
                        'roll'      => trim($row[1]),
                        'fname'     => trim($row[2]),
                        'lname'     => trim($row[3]),
                        'gender'    => strtoupper(trim($row[4])),
                        'dob'       => trim($row[5]),
                        'mobile'    => trim($row[6]),
                        'email'     => trim($row[7]),
                        'address'   => trim($row[8]),
                        'city'      => trim($row[9]),
                        'state'     => trim($row[10]),
                        'pincode'   => trim($row[11]),
                        'course'    => trim($row[12]),
                        'semester'  => trim($row[13]),
                        'admission' => trim($row[14])
                    ];

                    // Validate this specific row
                    $data_errs = validateStudentData($data, $conn);
                    
                    if (empty($data_errs)) {
                        // Pass 'true' for $isBulk to keep the transaction management here
                        if (createStudent($conn, $data, true)) {
                            $inserted_count++;
                        } else {
                            $failed_rows[] = "Row $row_num (Reg: {$data['reg']}): Database insertion failed.";
                        }
                    } else {
                        // Instead of throwing an exception, we record the error and continue
                        $failed_rows[] = "Row $row_num (Reg: {$data['reg']}): " . $data_errs[0];
                    }
                    $row_num++;
                }

                $conn->commit();
                $message = "Import completed! Successfully added: $inserted_count.";
                
                if (!empty($failed_rows)) {
                    $error = "The following rows failed:<br>" . implode("<br>", array_slice($failed_rows, 0, 10));
                    if (count($failed_rows) > 10) $error .= "<br>...and " . (count($failed_rows) - 10) . " more errors.";
                }

            } catch (Exception $e) {
                $conn->rollback();
                $error = "Critical System Error: " . $e->getMessage();
            }
            fclose($handle);
        }
      }
   
} // End of upload isset
?>

<?php include BASE_PATH . '/admin/layout/header.php'; ?>
<?php include BASE_PATH . '/admin/layout/sidebar.php'; ?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 mt-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-primary"><i class="bi bi-upload"></i> Bulk Student Upload</h5>
                </div>
                <div class="card-body p-4">
                    <?php if ($message): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?= $message ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?= $error ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="alert alert-info small">
                        <strong>Required CSV Columns (15):</strong><br>
                        RegNo, RollNo, FirstName, LastName, Gender, DOB, Mobile, Email, Address, City, State, Pincode, CourseID, Semester, AdmissionDate
                    </div>

                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Select CSV File</label>
                            <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" name="upload" class="btn btn-primary">
                                <i class="bi bi-cloud-arrow-up"></i> Start Import
                            </button>
                        </div>
                    </form>

                    <div class="mt-4 text-center">
                        <a href="template.php" class="text-decoration-none small">
                            <i class="bi bi-download"></i> Download CSV Template
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include BASE_PATH . '/admin/layout/footer.php'; ?>