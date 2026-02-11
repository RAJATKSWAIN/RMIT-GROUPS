<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';
checkLogin();

$upload_status = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['bank_file'])) {
    $file = $_FILES['bank_file']['tmp_name'];
    $handle = fopen($file, "r");
    $headers = fgetcsv($handle); // Skip header row

    $success_count = 0;
    $error_count = 0;

    // Start Transaction
    $conn->begin_transaction();

    try {
        while (($row = fgetcsv($handle)) !== FALSE) {
            // ASSUMING CSV STRUCTURE: 0: Date, 1: Ref No, 2: Description (with Reg No), 3: Amount
            $date = date('Y-m-d', strtotime($row[0]));
            $ref_no = $row[1];
            $description = $row[2];
            $amount = floatval($row[3]);

            // MAGIC LOGIC: Extract Registration Number from Bank Description
            // Searches for patterns like "REG-2023-001" or "2023001" inside the description
            preg_match('/[A-Z0-9-]{5,}/', $description, $matches);
            $reg_no = $matches[0] ?? null;

            if ($reg_no) {
                // 1. Find Student ID
                $stmt = $conn->prepare("SELECT STUDENT_ID FROM STUDENTS WHERE REGISTRATION_NO = ? OR ROLL_NO = ?");
                $stmt->bind_param("ss", $reg_no, $reg_no);
                $stmt->execute();
                $res = $stmt->get_result();
                
                if ($res->num_rows > 0) {
                    $student = $res->fetch_assoc();
                    $sid = $student['STUDENT_ID'];

                    // 2. Generate Unique Receipt No
                    $receipt_no = "BNK-" . time() . rand(10, 99);

                    // 3. Insert Payment
                    $ins = $conn->prepare("INSERT INTO PAYMENTS (STUDENT_ID, RECEIPT_NO, PAID_AMOUNT, PAYMENT_MODE, TXN_REF, PAYMENT_DATE, REMARKS, COLLECTED_BY) VALUES (?, ?, ?, 'BANK_TRANSFER', ?, ?, ?, ?)");
                    $remarks = "Bulk Bank Upload: " . $description;
                    $admin_id = $_SESSION['user_id'];
                    $ins->bind_param("isdsssi", $sid, $receipt_no, $amount, $ref_no, $date, $remarks, $admin_id);
                    $ins->execute();

                    // 4. Update Ledger
                    $upd = $conn->prepare("UPDATE STUDENT_FEE_LEDGER SET BALANCE_AMOUNT = BALANCE_AMOUNT - ?, LAST_PAYMENT_DATE = NOW() WHERE STUDENT_ID = ?");
                    $upd->bind_param("di", $amount, $sid);
                    $upd->execute();

                    $success_count++;
                } else {
                    $error_count++;
                }
            }
        }
        $conn->commit();
        $upload_status = "success|Successfully processed $success_count records. $error_count records failed (No matching Reg No).";
    } catch (Exception $e) {
        $conn->rollback();
        $upload_status = "danger|Error: " . $e->getMessage();
    }
    fclose($handle);
}
?>

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<?php include BASE_PATH.'/admin/layout/sidebar.php'; ?>


<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white p-3">
                    <h5 class="mb-0"><i class="bi bi-bank"></i> Bulk Bank/UPI Import</h5>
                </div>
                <div class="card-body p-4">
                    <?php if($upload_status): 
                        list($type, $msg) = explode('|', $upload_status); ?>
                        <div class="alert alert-<?= $type ?>"><?= $msg ?></div>
                    <?php endif; ?>

                    <div class="alert alert-info small">
                        <strong>Expected CSV Format:</strong><br>
                        Date, Reference No, Description (must contain Reg No), Amount
                    </div>

                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Select CSV File</label>
                            <input type="file" name="bank_file" class="form-control" accept=".csv" required>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-cloud-upload"></i> Process Transactions
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="mt-3 text-center">
                <a href="../dashboard.php" class="text-decoration-none text-muted small"><i class="bi bi-speedometer2"></i> Back to Dashboard</a>
            </div>
        </div>
    </div>
    
    
    
</div>

<?php include BASE_PATH.'/admin/layout/footer.php'; ?>	

