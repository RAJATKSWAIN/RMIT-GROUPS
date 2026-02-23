<?php
/*======================================================
    File Name   : search_student.php
    Project     : RMIT Groups - FMS - Fees Management System
    Description : Student Registration & Profile Management
    Developed By: TrinityWebEdge
    Date Created: 06-02-2025
    Last Updated: <?php echo date("d-m-Y"); ?>
    Note        : This page defines the FMS - Fees Management System | Student Module of RMIT Groups website.
=======================================================*/

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 1. MUST start session to access $_SESSION['inst_id']
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/db.php';
require_once '../core/auth.php';

// 2. Capture session and get variables
$instId = $_SESSION['inst_id'] ?? 0;
$term = $_GET['term'] ?? ''; 

header('Content-Type: application/json');

if (empty($term) || empty($instId)) {
    echo json_encode(['error' => 'Unauthorized access or missing search term.']);
    exit;
}

// 3. Updated SQL with INST_ID
$sql = "SELECT s.STUDENT_ID, s.COURSE_ID, s.REGISTRATION_NO, s.FIRST_NAME, s.LAST_NAME, 
               c.COURSE_NAME, c.COURSE_CODE, l.BALANCE_AMOUNT 
        FROM STUDENTS s
        JOIN COURSES c ON s.COURSE_ID = c.COURSE_ID
        JOIN STUDENT_FEE_LEDGER l ON s.STUDENT_ID = l.STUDENT_ID
        WHERE (s.REGISTRATION_NO = ? OR s.ROLL_NO = ?) 
        AND s.INST_ID = ? 
        AND s.STATUS = 'A' 
        LIMIT 1";

$stmt = $conn->prepare($sql);
// We have three '?' so we need "ssi" (string, string, integer)
$stmt->bind_param("ssi", $term, $term, $instId);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if ($student) {
    $course_id = $student['COURSE_ID'];
    $availableFees = [];

    // Step 2: Fetch Fees (Note: Filter by Course)
    $feeSql = "SELECT MH.FEES_NAME, MH.APPLICABLE_LEVEL, MD.AMOUNT 
               FROM MASTER_FEES_HDR MH
               JOIN MASTER_FEES_DTL MD ON MH.FEES_HDR_ID = MD.FEES_HDR_ID
               WHERE MD.COURSE_ID = ? AND MH.ACTIVE_FLAG = 'A'";
    
    $feeStmt = $conn->prepare($feeSql);
    $feeStmt->bind_param("i", $course_id);
    $feeStmt->execute();
    $feeResult = $feeStmt->get_result();

    while ($row = $feeResult->fetch_assoc()) {
        $availableFees[] = [
            'fees_name' => $row['FEES_NAME'],
            'level'     => $row['APPLICABLE_LEVEL'],
            'amount'    => (float)$row['AMOUNT']
        ];
    }

    // Miscellaneous Fallback
    $foundMisc = false;
    foreach($availableFees as $f) {
        if(strcasecmp($f['fees_name'], 'Miscellaneous') == 0) $foundMisc = true;
    }

    if (!$foundMisc) {
        $availableFees[] = [
            'fees_name' => 'Miscellaneous',
            'level'     => 'GLOBAL',
            'amount'    => (float)$student['BALANCE_AMOUNT'] 
        ];
    }

    $student['available_fees'] = $availableFees;
    echo json_encode($student);
} else {
    echo json_encode(['error' => 'Student not found in your institute.']);
}
