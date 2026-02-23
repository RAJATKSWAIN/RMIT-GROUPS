<?php
header('Content-Type: application/json');
require_once '../config/db.php';
require_once '../core/auth.php';
checkLogin();

$instId = $_SESSION['inst_id'];
$adminId = $_SESSION['admin_id'];
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

if (!$input || !$action) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid Service Request']);
    exit;
}

$res = ['status' => 'error', 'message' => 'Action Not Implemented'];

try {
    switch ($action) {
        /* --- CONFIGURATION SERVICES --- */
        case 'ADD_COURSE':
            $stmt = $conn->prepare("INSERT INTO COURSES (INST_ID, COURSE_CODE, COURSE_NAME, DURATION_YEARS) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("issi", $instId, $input['code'], $input['name'], $input['duration']);
            if($stmt->execute()) $res = ['status' => 'success', 'message' => 'Course Added'];
            break;

        /* --- STUDENT & ADMISSION SERVICES --- */
        case 'REGISTER_STUDENT':
            $conn->begin_transaction();
            // 1. Insert Student
            $stmt = $conn->prepare("INSERT INTO STUDENTS (REGISTRATION_NO, ROLL_NO, FIRST_NAME, LAST_NAME, COURSE_ID, INST_ID) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssii", $input['reg_no'], $input['roll_no'], $input['fname'], $input['lname'], $input['course_id'], $instId);
            $stmt->execute();
            $sid = $conn->insert_id;

            // 2. Initialize Ledger
            $stmt = $conn->prepare("INSERT INTO STUDENT_FEE_LEDGER (STUDENT_ID, BALANCE_AMOUNT) VALUES (?, 0)");
            $stmt->bind_param("i", $sid);
            $stmt->execute();

            $conn->commit();
            $res = ['status' => 'success', 'message' => 'Student Registered & Ledger Created', 'student_id' => $sid];
            break;

        /* --- FINANCE & PAYMENT SERVICES --- */
        case 'COLLECT_PAYMENT':
            $conn->begin_transaction();
            $receipt = "REC-" . time();
            // 1. Record Payment
            $stmt = $conn->prepare("INSERT INTO PAYMENTS (STUDENT_ID, RECEIPT_NO, PAID_AMOUNT, PAYMENT_MODE, COLLECTED_BY) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("isdsi", $input['student_id'], $receipt, $input['amount'], $input['mode'], $adminId);
            $stmt->execute();
            
            // 2. Update Ledger
            $stmt = $conn->prepare("UPDATE STUDENT_FEE_LEDGER SET PAID_AMOUNT = PAID_AMOUNT + ?, BALANCE_AMOUNT = BALANCE_AMOUNT - ?, LAST_PAYMENT_DATE = NOW() WHERE STUDENT_ID = ?");
            $stmt->bind_param("ddi", $input['amount'], $input['amount'], $input['student_id']);
            $stmt->execute();

            $conn->commit();
            $res = ['status' => 'success', 'message' => 'Payment Processed', 'receipt_no' => $receipt];
            break;

        /* --- FETCH SERVICES (For Lookups) --- */
        case 'FETCH_STUDENTS':
            $query = "SELECT STUDENT_ID, FIRST_NAME, LAST_NAME, ROLL_NO FROM STUDENTS WHERE INST_ID = $instId LIMIT 20";
            $data = $conn->query($query)->fetch_all(MYSQLI_ASSOC);
            $res = ['status' => 'success', 'data' => $data];
            break;
    }
} catch (Exception $e) {
    if(isset($conn)) $conn->rollback();
    $res = ['status' => 'error', 'message' => $conn->error ?: $e->getMessage()];
}

echo json_encode($res);
