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

require_once '../config/db.php';
require_once '../core/auth.php';

$term = $_GET['term'] ?? '';

// Step 1: Get Student and Course ID (Filtered for Active Students)
$sql = "SELECT s.STUDENT_ID, s.COURSE_ID, s.REGISTRATION_NO, s.FIRST_NAME, s.LAST_NAME, 
               c.COURSE_NAME, c.COURSE_CODE, l.BALANCE_AMOUNT 
        FROM STUDENTS s
        JOIN COURSES c ON s.COURSE_ID = c.COURSE_ID
        JOIN STUDENT_FEE_LEDGER l ON s.STUDENT_ID = l.STUDENT_ID
        WHERE (s.REGISTRATION_NO = ? OR s.ROLL_NO = ?) 
        AND s.STATUS = 'A' 
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $term, $term);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if ($student) {
    $course_id = $student['COURSE_ID'];
    $availableFees = [];

    // Step 2: Fetch Fees Name, Level, and Amount
    // We must alias the columns or use specific keys so JS can read them
    $feeSql = "SELECT MH.FEES_NAME, MH.APPLICABLE_LEVEL, MD.AMOUNT 
               FROM MASTER_FEES_HDR MH
               JOIN MASTER_FEES_DTL MD ON MH.FEES_HDR_ID = MD.FEES_HDR_ID
               WHERE MD.COURSE_ID = ? AND MH.ACTIVE_FLAG = 'A'";
    
    $feeStmt = $conn->prepare($feeSql);
    $feeStmt->bind_param("i", $course_id);
    $feeStmt->execute();
    $feeResult = $feeStmt->get_result();

    while ($row = $feeResult->fetch_assoc()) {
        // Create an object for each fee instead of just a string
        $availableFees[] = [
            'fees_name' => $row['FEES_NAME'],
            'level'     => $row['APPLICABLE_LEVEL'],
            'amount'    => (float)$row['AMOUNT']
        ];
    }

    // Always add Miscellaneous as a fallback if not in table
    // We give it a 'GLOBAL' level so it behaves like a standard fee
    $foundMisc = false;
    foreach($availableFees as $f) {
        if(strcasecmp($f['fees_name'], 'Miscellaneous') == 0) $foundMisc = true;
    }

    if (!$foundMisc) {
        $availableFees[] = [
            'fees_name' => 'Miscellaneous',
            'level'     => 'GLOBAL',
            // Defaulting the amount to the student's balance for easy matching
            'amount'    => (float)$student['BALANCE_AMOUNT'] 
        ];
    }

    $student['available_fees'] = $availableFees;
}

header('Content-Type: application/json');
echo json_encode($student);
