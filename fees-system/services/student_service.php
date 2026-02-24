<!--======================================================
    File Name   : student_service.php
    Project     : RMIT Groups - FMS - Fees Management System
    Description : SERVICE FOR STUDENT MANAGEMENT
    Developed By: TrinityWebEdge
    Date Created: 06-02-2025
    Note        : This page defines the FMS - Fees Management System | BACKEND SERVICE Module of RMIT Groups website.
=======================================================-->
        
<?php
/* =====================================
   STUDENT SERVICE
===================================== */

function createStudent($conn, $data, $isBulk = false)
{
    if (!$isBulk) { $conn->begin_transaction(); }

    try {
        // Use the ID directly from the session
        $instId = $_SESSION['inst_id'];
        
        /* 1. INSERT STUDENT */
        $stmt = $conn->prepare("
            INSERT INTO STUDENTS 
            (REGISTRATION_NO, ROLL_NO, FIRST_NAME, LAST_NAME, GENDER, DOB, 
             MOBILE, EMAIL, ADDRESS, CITY, STATE, PINCODE, 
             COURSE_ID, INST_ID, SEMESTER, ADMISSION_DATE) 
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
        ");

        $stmt->bind_param(
            "sssssssssssiisis",
            $data['reg'], $data['roll'], $data['fname'], $data['lname'],
            $data['gender'], $data['dob'], $data['mobile'], $data['email'],
            $data['address'], $data['city'], $data['state'], $data['pincode'],
            $data['course'], $instId , $data['semester'], $data['admission']
        );

        if (!$stmt->execute()) throw new Exception($stmt->error);
        $studentId = $conn->insert_id;

        /* 2. CALCULATE TOTAL MANDATORY FEES */
        $course_id = intval($data['course']);
        $feeQ = $conn->query("
            SELECT SUM(D.AMOUNT) as total 
            FROM MASTER_FEES_DTL D
            INNER JOIN MASTER_FEES_HDR H ON D.FEES_HDR_ID = H.FEES_HDR_ID
            WHERE D.COURSE_ID = $course_id 
            AND D.ACTIVE_FLAG = 'A' 
            AND H.ACTIVE_FLAG = 'A'
            AND H.MANDATORY_FLAG = 'Y'
        ");

        $feeRow = $feeQ->fetch_assoc();
        $total_mandatory_fees = $feeRow['total'] ?? 0;

        /* 3. CREATE LEDGER - Corrected $fee to $total_mandatory_fees */
        $conn->query("
            INSERT INTO STUDENT_FEE_LEDGER (STUDENT_ID, TOTAL_FEE, BALANCE_AMOUNT, LAST_UPDATED) 
            VALUES ($studentId, $total_mandatory_fees, $total_mandatory_fees, NOW())
        ");

        /* 4. AUDIT */
        if(function_exists('audit_log')){
            audit_log($conn, 'STUDENT_REGISTRATION', 'STUDENTS', $studentId, null, "Bulk: " . ($isBulk ? 'Yes' : 'No'), $total_mandatory_fees);
        }

        if (!$isBulk) { $conn->commit(); }
        return $studentId;

    } catch(Exception $e) {
        if (!$isBulk) { $conn->rollback(); }
        return false;
    }
}

/* =====================================
   PROMOTION LOGIC (Add this to StudentService)
===================================== */
function promoteStudent($conn, $studentId, $targetSemester, $isBulk = false)
{
    if (!$isBulk) { $conn->begin_transaction(); }

    try {
        // 1. Fetch Student, Course, and College Data
        $stmt = $conn->prepare("
            SELECT s.SEMESTER, s.COURSE_ID, c.COURSE_CODE, c.DURATION_YEARS 
            FROM STUDENTS s
            JOIN COURSES c ON s.COURSE_ID = c.COURSE_ID
            WHERE s.INST_ID = c.INST_ID
            AND s.STUDENT_ID = ?
        ");
        $stmt->execute([$studentId]);
        $res = $stmt->get_result()->fetch_assoc();
        
        if (!$res) throw new Exception("Student not found.");
        
        $currentSem = intval($res['SEMESTER']);
        $targetSem = intval($targetSemester);
        $courseCode = strtoupper($res['COURSE_CODE']);
        $maxYears = intval($res['DURATION_YEARS']);
        $maxSem = $maxYears * 2;

        // --- VALIDATION 1: Boundary Check ---
        if ($targetSem > $maxSem) {
            throw new Exception("Cannot promote beyond $maxSem Semesters for this course.");
        }

        // --- VALIDATION 2: Year-to-Year Logic (1->3, 3->5, 5->7) ---
        // Only trigger if target is ODD and strictly skipping a year level
        $isYearlyJump = ($targetSem % 2 != 0 && $targetSem > $currentSem);
        
        // Custom Rule: If current is 1, they must jump to 3 to trigger yearly fees
        // This ensures they don't just move 1->2 without paying the 'Year 2' fee at 3
        
        if ($isYearlyJump) {
            
            // --- VALIDATION 3: RMITC Specific Logic ---
            // If it's an ITI course (RMITC), they usually only have 2 years (max 4 sems)
            if (in_array($courseCode, ['FIT', 'WLD', 'EMC', 'ELT']) && $targetSem > 4) {
                 throw new Exception("RMITC Courses cannot exceed 4 Semesters (2 Years).");
            }

            // --- EXECUTE FINANCIAL UPDATE ---
            
            // A. Calculate Fees
            $feeQ = $conn->query("
                SELECT SUM(D.AMOUNT) as total 
                FROM MASTER_FEES_DTL D
                INNER JOIN MASTER_FEES_HDR H ON D.FEES_HDR_ID = H.FEES_HDR_ID
                WHERE D.COURSE_ID = {$res['COURSE_ID']} 
                AND D.ACTIVE_FLAG = 'A' AND H.ACTIVE_FLAG = 'A' AND H.MANDATORY_FLAG = 'Y'
            ");
            $feeRow = $feeQ->fetch_assoc();
            $new_yearly_fees = (float)($feeRow['total'] ?? 0);

            // B. Snapshot & Add Fees
            $ledgerStmt = $conn->prepare("
                UPDATE STUDENT_FEE_LEDGER 
                SET PREVIOUS_DUES = BALANCE_AMOUNT,
                    TOTAL_FEE = TOTAL_FEE + ?, 
                    BALANCE_AMOUNT = BALANCE_AMOUNT + ?, 
                    LAST_UPDATED = NOW() 
                WHERE STUDENT_ID = ?
            ");
            $ledgerStmt->execute([$new_yearly_fees, $new_yearly_fees, $studentId]);
            
            $auditRemarks = "Yearly Promotion: $currentSem to $targetSem. Added ₹$new_yearly_fees";
        } else {
            // Just a semester update (e.g., 1->2 or 3->4) - No fee addition
            $auditRemarks = "Semester Update: $currentSem to $targetSem. No fees added.";
        }

        // 4. FINAL UPDATE TO STUDENT RECORD
        $updateStmt = $conn->prepare("UPDATE STUDENTS SET SEMESTER = ?, UPDATED_AT = NOW() WHERE STUDENT_ID = ?");
        $updateStmt->execute([$targetSem, $studentId]);

        // 5. AUDIT
        if(function_exists('audit_log')){
            audit_log($conn, 'STUDENT_PROMOTION', 'STUDENTS', $studentId, null, $auditRemarks);
        }

        if (!$isBulk) { $conn->commit(); }
        return true;

    } catch(Exception $e) {
        if (!$isBulk) { $conn->rollback(); }
        error_log("Promotion Error: " . $e->getMessage());
        return false;
    }
}
