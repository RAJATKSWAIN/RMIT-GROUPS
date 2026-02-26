<!--======================================================
    File Name   : process_promotion.php
    Project     : RMIT Groups - FMS - Fees Management System
    Module      : STUDENT MANAGEMENT
    Description : Backend logic with Financial Snapshots & Fee Validation
    Developed By: TrinityWebEdge
    Date Created: 06-02-2026
    Last Updated: 25-02-2026
    Note        : This page defines the FMS - Fees Management System | Student Module of RMIT Groups website.
=======================================================-->
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';
require_once BASE_PATH.'/services/student_service.php'; 

checkLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_promote'])) {
    
    $student_ids     = $_POST['student_ids'] ?? [];
    $target_semester = intval($_POST['target_semester']);
    $course_id       = intval($_POST['course_id']);
    $inst_id         = intval($_POST['inst_id'] ?? $_SESSION['inst_id']);
    $admin_id        = $_SESSION['admin_id'];

    /* --- VALIDATION --- */
    if (empty($student_ids)) {
        $_SESSION['msg'] = "No students were selected for promotion.";
        $_SESSION['msg_type'] = "warning";
        header("Location: promote.php?course_id=$course_id&target_inst_id=$inst_id");
        exit();
    }

    if ($target_semester <= 0) {
        $_SESSION['msg'] = "Invalid target semester selected.";
        $_SESSION['msg_type'] = "danger";
        header("Location: promote.php?course_id=$course_id&target_inst_id=$inst_id");
        exit();
    }

    $conn->begin_transaction();
    $success_count = 0;

    try {
        foreach ($student_ids as $id) {
            $sid = intval($id);

            // 1. Fetch Financial Snapshot from STUDENT_FEE_LEDGER
            $snap_sql = "SELECT s.SEMESTER, l.TOTAL_FEE, l.PAID_AMOUNT, l.BALANCE_AMOUNT 
                         FROM STUDENTS s
                         LEFT JOIN STUDENT_FEE_LEDGER l ON s.STUDENT_ID = l.STUDENT_ID
                         WHERE s.STUDENT_ID = ? AND s.INST_ID = ?";
            
            $stmt_snap = $conn->prepare($snap_sql);
            $stmt_snap->bind_param("ii", $sid, $inst_id);
            $stmt_snap->execute();
            $snap = $stmt_snap->get_result()->fetch_assoc();

            $from_sem = $snap['SEMESTER'] ?? 0;
            $payable  = $snap['TOTAL_FEE'] ?? 0.00;
            $paid     = $snap['PAID_AMOUNT'] ?? 0.00;
            $balance  = $snap['BALANCE_AMOUNT'] ?? 0.00;

            // 2. Execute Promotion (isBulk set to true for manual transaction control)
            if (promoteStudent($conn, $sid, $target_semester, true)) {
                
                // 3. Log History with Snapshot
                $log_sql = "INSERT INTO STUDENT_PROMOTION_LOGS 
                           (STUDENT_ID, FROM_SEMESTER, TO_SEMESTER, TOTAL_PAYABLE, TOTAL_PAID, CURRENT_BALANCE, PROMOTED_BY, INST_ID, REMARKS) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $log_stmt = $conn->prepare($log_sql);
                
                // Determine if it was a yearly promotion for the remarks
                $isYearly = ($target_semester % 2 != 0);
                $remarks = $isYearly ? "Yearly Promotion (Fees Applied)" : "Semester Promotion";
                
                $log_stmt->bind_param("iiidddiis", 
                    $sid, $from_sem, $target_semester, $payable, $paid, $balance, $admin_id, $inst_id, $remarks
                );
                $log_stmt->execute();
                
                $success_count++;
            } else {
                throw new Exception("Critical error processing Student ID: $sid.");
            }
        }

        /* --- ADDED AUDIT LOGGING HERE --- */
        if ($success_count > 0) {
            require_once BASE_PATH.'/config/audit.php'; // Ensure audit service is loaded

            $isYearly = ($target_semester % 2 != 0);
            $action_type = "BULK_PROMOTION";
            $table_name  = "STUDENTS";
            
            // Create a descriptive payload for the audit trail
            $details = json_encode([
                'count'           => $success_count,
                'target_semester' => $target_semester,
                'course_id'       => $course_id,
                'promotion_type'  => $isYearly ? 'Yearly' : 'Semester',
                'student_list'    => implode(',', $student_ids)
            ]);

            // Call your existing audit_log function
            audit_log($conn, $action_type, $table_name, 0, null, $details);
        }
        /* --- END AUDIT LOGGING --- */
        
        
        $conn->commit();

        // 4. Dynamic Feedback Message (From your old logic)
        $isYearly = ($target_semester % 2 != 0);
        $feeNote = $isYearly ? " (New Academic Year fees applied to ledgers)" : " (Semester records updated)";
        
        $_SESSION['msg'] = "<strong>Success!</strong> Successfully promoted $success_count students to Semester $target_semester.$feeNote";
        $_SESSION['msg_type'] = "success";

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['msg'] = "<strong>Promotion Halted:</strong> " . $e->getMessage() . " No changes were saved.";
        $_SESSION['msg_type'] = "danger";
    }

    header("Location: promote.php?course_id=$course_id&target_inst_id=$inst_id");
    exit();

} else {
    header("Location: promote.php");
    exit();
}
