<!--======================================================
    File Name   : student_service.php
    Project     : RMIT Groups - FMS - Fees Management System
    Description : SERVICE FOR STUDENT MANAGEMENT
    Developed By: TrinityWebEdge
    Date Created: 06-02-2025
    Last Updated: 24-02-2026
    Note        : This page defines the FMS - Fees Management System | BACKEND SERVICE Module of RMIT Groups website.
=======================================================-->        
<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once BASE_PATH.'/config/audit.php'; 

/* =====================================
   STUDENT SERVICE
===================================== */

/**
 * Student Service - Handles Registration & Ledger Initialization
 */
function createStudent($conn, $data, $isBulk = false) {
    // Start transaction if not part of a bulk process
    if (!$isBulk) { $conn->begin_transaction(); }

    try {
        /* --- 1. INSERT STUDENT WITH PARENT DETAILS --- */
        // We count 18 columns, so we need 18 '?' placeholders
        $sql = "INSERT INTO STUDENTS (
                    REGISTRATION_NO, ROLL_NO, FIRST_NAME, LAST_NAME, 
                    FATHER_NAME, MOTHER_NAME, GENDER, DOB, 
                    MOBILE, EMAIL, ADDRESS, CITY, 
                    STATE, PINCODE, COURSE_ID, INST_ID, 
                    SEMESTER, ADMISSION_DATE
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        // bind_param: s = string, i = integer
        // Mapping types: 14 strings, 1 integer (course), 1 integer (inst), 1 integer (sem), 1 string (date)
        // Corrected type string: "sssssssssssssiisis"
        $stmt->bind_param("ssssssssssssssiiis", 
			$data['reg'], 
			$data['roll'], 
			$data['fname'], 
			$data['lname'], 
			$data['father_name'], 
			$data['mother_name'], 
			$data['gender'], 
			$data['dob'], 
			$data['mobile'], 
			$data['email'], 
			$data['address'], 
			$data['city'],
			$data['state'], 
			$data['pincode'], // String
			$data['course'],  // Integer
			$data['inst_id'], // Integer
			$data['semester'],// Integer
			$data['admission']// String (Date)
		);

        if (!$stmt->execute()) {
            throw new Exception("Profile Save Failed: " . $stmt->error);
        }
        
        $studentId = $conn->insert_id;

        /* --- 2. CALCULATE MANDATORY FEES (Validate via Course-Institute Join) --- */
		// Joining with COURSES to ensure we only get fees for this specific Institute
        $feeQuery = "SELECT SUM(D.AMOUNT) as total 
                     FROM MASTER_FEES_DTL D
                     INNER JOIN MASTER_FEES_HDR H ON D.FEES_HDR_ID = H.FEES_HDR_ID
					 INNER JOIN COURSES C ON D.COURSE_ID = C.COURSE_ID
                     WHERE D.COURSE_ID = ? 
					 AND C.INST_ID = ? 
                     AND D.ACTIVE_FLAG = 'A' 
					 AND H.MANDATORY_FLAG = 'Y'";
        
        $feeStmt = $conn->prepare($feeQuery);
        $feeStmt->bind_param("ii", $data['course'], $data['inst_id']);
        $feeStmt->execute();
        $feeResult = $feeStmt->get_result()->fetch_assoc();
		
        // Force totalFees to 0.00 if nothing found to prevent NULL Ledger errors
		$totalFees = isset($feeResult['total']) ? (float)$feeResult['total'] : 0.00;

        /* --- 3. INITIALIZE FEE LEDGER --- */
        $ledgerSql = "INSERT INTO STUDENT_FEE_LEDGER (STUDENT_ID, TOTAL_FEE, BALANCE_AMOUNT, LAST_UPDATED) 
                      VALUES (?, ?, ?, NOW())";
        $ledger = $conn->prepare($ledgerSql);
        $ledger->bind_param("idd", $studentId, $totalFees, $totalFees);
        
        if (!$ledger->execute()) {
            throw new Exception("Ledger Initialization Failed: " . $ledger->error);
        }

        /* --- 4. AUDIT LOG THE ACTION (Uses the corrected audit.php you just created) --- */
        if (function_exists('audit_log')) {
            $remark = "New Student Registered: " . $data['fname'] . " " . $data['lname'] . " (Roll: " . $data['roll'] . ")";
            audit_log(
                $conn, 
                'STUDENT_REGISTRATION', 
                'STUDENTS', 
                $studentId, 
                null, 
                $remark, 
                $totalFees
            );
        }

        // Commit everything if we reached here
        if (!$isBulk) { $conn->commit(); }
        return $studentId;

    } catch (Exception $e) {
        // If anything fails, undo the whole process
        if (!$isBulk) { $conn->rollback(); }
        error_log("Student Service Error: " . $e->getMessage());
        return false;
    }
}

/* =====================================
   PROMOTION LOGIC (Add this to StudentService)
===================================== */
/**
 * Handles RMITC Specific Promotion Logic with Financial Integration
 */
function promoteStudent($conn, $studentId, $targetSemester, $isBulk = false)
{
    if (!$isBulk) { $conn->begin_transaction(); }

    try {
        /* --- 1. FETCH CONTEXT --- */
        $stmt = $conn->prepare("
            SELECT s.SEMESTER, s.COURSE_ID, s.INST_ID, c.COURSE_CODE, c.DURATION_YEARS 
            FROM STUDENTS s
            JOIN COURSES c ON s.COURSE_ID = c.COURSE_ID
            WHERE s.STUDENT_ID = ?
        ");
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        
        if (!$res) throw new Exception("Student ID $studentId not found.");
        
        $currentSem = intval($res['SEMESTER']);
        $targetSem  = intval($targetSemester);
        $courseCode = strtoupper($res['COURSE_CODE']);
        $maxSem     = intval($res['DURATION_YEARS']) * 2;
        $instId     = intval($res['INST_ID']);
        $courseId   = intval($res['COURSE_ID']);

        /* --- 2. VALIDATIONS --- */
        if ($targetSem <= $currentSem) {
            throw new Exception("Target semester ($targetSem) must be greater than current semester ($currentSem).");
        }
        if ($targetSem > $maxSem) {
            throw new Exception("Promotion exceeds course limit of $maxSem semesters.");
        }
        if (in_array($courseCode, ['FIT', 'WLD', 'EMC', 'ELT']) && $targetSem > 4) {
             throw new Exception("ITI Course $courseCode capped at 4 Semesters.");
        }

        /* --- 3. FINANCIAL LOGIC --- */
        $isYearlyJump = ($targetSem % 2 != 0); 
        $auditRemarks = "";

        if ($isYearlyJump) {
            // Fetch mandatory fees for the specific course and institute
            $feeQ = $conn->prepare("
                SELECT SUM(D.AMOUNT) as total 
                FROM MASTER_FEES_DTL D
                INNER JOIN MASTER_FEES_HDR H ON D.FEES_HDR_ID = H.FEES_HDR_ID
                WHERE D.COURSE_ID = ? 
                AND D.INST_ID = ? 
                AND D.ACTIVE_FLAG = 'A' 
                AND H.ACTIVE_FLAG = 'A' 
                AND H.MANDATORY_FLAG = 'Y'
            ");
            $feeQ->bind_param("ii", $courseId, $instId);
            $feeQ->execute();
            $feeRow = $feeQ->get_result()->fetch_assoc();
            $newFees = (float)($feeRow['total'] ?? 0);

            // Update Ledger: Shift balance to dues and add new fees
            $ledgerStmt = $conn->prepare("
                UPDATE STUDENT_FEE_LEDGER 
                SET PREVIOUS_DUES = BALANCE_AMOUNT,
                    TOTAL_FEE = TOTAL_FEE + ?, 
                    BALANCE_AMOUNT = BALANCE_AMOUNT + ?, 
                    LAST_UPDATED = NOW() 
                WHERE STUDENT_ID = ?
            ");
            $ledgerStmt->bind_param("ddi", $newFees, $newFees, $studentId);
            $ledgerStmt->execute();
            
            $auditRemarks = "Yearly Promotion: Sem $currentSem -> $targetSem. Fees added: ₹$newFees";
        } else {
            $auditRemarks = "Semester Update: Sem $currentSem -> $targetSem. No financial change.";
        }

        /* --- 4. DATA UPDATES --- */
        // Update Student Table
        $updateStmt = $conn->prepare("UPDATE STUDENTS SET SEMESTER = ?, UPDATED_AT = NOW() WHERE STUDENT_ID = ?");
        $updateStmt->bind_param("ii", $targetSem, $studentId);
        $updateStmt->execute();

        // Standard Audit Log
        if(function_exists('audit_log')){
            audit_log($conn, 'STUDENT_PROMOTION', 'STUDENTS', $studentId, null, $auditRemarks);
        }

        if (!$isBulk) { $conn->commit(); }
        return true;

    } catch(Exception $e) {
        if (!$isBulk) { $conn->rollback(); }
        error_log("Promotion Failure: " . $e->getMessage());
        return false;
    }
}
