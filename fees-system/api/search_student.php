<?php
/*======================================================
    File Name   : search_student.php
    Project     : RMIT Groups - FMS - Fees Management System
    Description : Student Registration & Profile Management
    Developed By: TrinityWebEdge
    Date Created: 06-02-2025
    Last Updated: 23-02-2026
    Note        : This page defines the FMS - Fees Management System | Student Module.
=======================================================*/

// 1. Session & Error Handling
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Disable error display for the final JSON output to prevent corruption
// but keep logging active for your server logs
ini_set('display_errors', 0); 
error_reporting(E_ALL);

require_once '../config/db.php';
require_once '../core/auth.php';

// 2. Identity Mapping
$instId = $_SESSION['inst_id'] ?? 0;
$term   = $_GET['term'] ?? '';

header('Content-Type: application/json');

$response = null;

if (!empty($term) && $instId > 0) {
    
    // Step 1: Get Student, Course, and Ledger Balance
    // FIXED: Added "=" after INST_ID
    $sql = "SELECT s.STUDENT_ID, s.COURSE_ID, s.REGISTRATION_NO, s.FIRST_NAME, s.LAST_NAME, 
                   c.COURSE_NAME, c.COURSE_CODE, l.BALANCE_AMOUNT 
            FROM STUDENTS s
            JOIN COURSES c ON s.COURSE_ID = c.COURSE_ID
            JOIN STUDENT_FEE_LEDGER l ON s.STUDENT_ID = l.STUDENT_ID
            WHERE (s.REGISTRATION_NO = ? OR s.ROLL_NO = ?) 
            AND s.STATUS = 'A' 
            AND s.INST_ID = ? 
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $term, $term, $instId);
    $stmt->execute();
    $student = $stmt->get_result()->fetch_assoc();

    if ($student) {
        $course_id = $student['COURSE_ID'];
        $availableFees = [];

        // Step 2: Fetch specific Fees from Master Configuration
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

        // Step 3: Miscellaneous / Total Balance Handling
        // ENHANCEMENT: We ensure Miscellaneous allows clearing the whole ledger balance
        $foundMisc = false;
        foreach($availableFees as &$f) {
            if(strcasecmp($f['fees_name'], 'Miscellaneous') == 0) {
                $foundMisc = true;
                // Update existing Misc amount to reflect current Ledger Balance if it's dynamic
                $f['amount'] = (float)$student['BALANCE_AMOUNT'];
            }
        }

        if (!$foundMisc) {
            $availableFees[] = [
                'fees_name' => 'Miscellaneous',
                'level'     => 'GLOBAL',
                'amount'    => (float)$student['BALANCE_AMOUNT'] 
            ];
        }

        $student['available_fees'] = $availableFees;
        $response = $student;
    }
}

// 3. Clean JSON Output
if ($response) {
    echo json_encode($response);
} else {
    echo json_encode(['error' => 'Student not found or unauthorized access.']);
}
exit;
