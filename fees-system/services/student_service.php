<?php
/* =====================================
   STUDENT SERVICE
===================================== */

function createStudent($conn, $data, $isBulk = false)
{
    if (!$isBulk) { $conn->begin_transaction(); }

    try {
        /* 1. INSERT STUDENT */
        $stmt = $conn->prepare("
            INSERT INTO STUDENTS 
            (REGISTRATION_NO, ROLL_NO, FIRST_NAME, LAST_NAME, GENDER, DOB, 
             MOBILE, EMAIL, ADDRESS, CITY, STATE, PINCODE, 
             COURSE_ID, SEMESTER, ADMISSION_DATE) 
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
        ");

        $stmt->bind_param(
            "sssssssssssiiis",
            $data['reg'], $data['roll'], $data['fname'], $data['lname'],
            $data['gender'], $data['dob'], $data['mobile'], $data['email'],
            $data['address'], $data['city'], $data['state'], $data['pincode'],
            $data['course'], $data['semester'], $data['admission']
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
            audit_log($conn, 'STUDENT_CREATE', 'STUDENTS', $studentId, null, "Bulk: " . ($isBulk ? 'Yes' : 'No'), $total_mandatory_fees);
        }

        if (!$isBulk) { $conn->commit(); }
        return $studentId;

    } catch(Exception $e) {
        if (!$isBulk) { $conn->rollback(); }
        return false;
    }
}