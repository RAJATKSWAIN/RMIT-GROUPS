<?php
require_once "../config/db.php";
require_once "../core/audit.php";

function createStudent($conn, $data)
{
    $conn->begin_transaction();

    try {

        /* =============================
           1. INSERT STUDENT
        ============================= */
        $stmt = $conn->prepare("
            INSERT INTO STUDENTS
            (REGISTRATION_NO,ROLL_NO,FIRST_NAME,LAST_NAME,GENDER,DOB,
             MOBILE,EMAIL,ADDRESS,CITY,STATE,PINCODE,
             COURSE_ID,SEMESTER,ADMISSION_DATE)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
        ");

        $stmt->bind_param("sssssssssssiiis",
            $data['reg'],
            $data['roll'],
            $data['fname'],
            $data['lname'],
            $data['gender'],
            $data['dob'],
            $data['mobile'],
            $data['email'],
            $data['address'],
            $data['city'],
            $data['state'],
            $data['pincode'],
            $data['course'],
            $data['semester'],
            $data['admission']
        );

        $stmt->execute();

        $studentId = $conn->insert_id;


        /* =============================
           2. CALCULATE TOTAL FEES
        ============================= */
        $feeQ = $conn->query("
            SELECT SUM(AMOUNT) total
            FROM MASTER_FEES_DTL
            WHERE COURSE_ID = {$data['course']}
              AND ACTIVE_FLAG='A'
        ");

        $fee = $feeQ->fetch_assoc()['total'] ?? 0;


        /* =============================
           3. CREATE LEDGER
        ============================= */
        $conn->query("
            INSERT INTO STUDENT_FEE_LEDGER
            (STUDENT_ID,TOTAL_FEE,BALANCE_AMOUNT,LAST_UPDATED)
            VALUES ($studentId,$fee,$fee,NOW())
        ");


        /* =============================
           4. AUDIT LOG
        ============================= */
        audit_log(
            $conn,
            'STUDENT_CREATE',
            'STUDENTS',
            $studentId,
            null,
            json_encode($data),
            $fee
        );

        $conn->commit();

        return true;

    } catch(Exception $e) {
        $conn->rollback();
        return false;
    }
}
?>
