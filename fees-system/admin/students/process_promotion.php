<?php
/* ======================================================
    File Name   : process_promotion.php
    Project     : RMIT Groups - FMS - Fees Management System
    Description : Backend logic for bulk student promotion
    Module      : STUDENT MANAGEMENT
======================================================= */
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';
require_once BASE_PATH.'/services/student_service.php'; 
require_once BASE_PATH.'/config/audit.php';          

checkLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_promote'])) {
    
    $student_ids     = $_POST['student_ids'] ?? [];
    $target_semester = intval($_POST['target_semester']);
    $course_id       = intval($_POST['course_id']);
    // Capture inst_id to maintain Superadmin view context
    $inst_id         = intval($_POST['inst_id'] ?? $_SESSION['inst_id']);

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

    /* --- BATCH PROCESSING --- */
    $conn->begin_transaction();
    $success_count = 0;

    try {
        foreach ($student_ids as $id) {
            $student_id = intval($id);

            // Call promoteStudent with $isBulk = true to prevent individual commits
            $result = promoteStudent($conn, $student_id, $target_semester, true);

            if ($result) {
                $success_count++;
            } else {
                throw new Exception("Critical error processing Student ID: $student_id.");
            }
        }

        $conn->commit();
        
        // Dynamic Message: Explain if fees were added or just semester records updated
        $isYearly = ($target_semester % 2 != 0);
        $feeNote = $isYearly ? " (New Academic Year fees applied to ledgers)" : " (Semester records updated)";
        
        $_SESSION['msg'] = "<strong>Success!</strong> Successfully promoted $success_count students to Semester $target_semester.$feeNote";
        $_SESSION['msg_type'] = "success";

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['msg'] = "<strong>Promotion Halted:</strong> " . $e->getMessage() . " No changes were saved.";
        $_SESSION['msg_type'] = "danger";
    }

    // Redirect with parameters to keep the filter state
    header("Location: promote.php?course_id=$course_id&target_inst_id=$inst_id");
    exit();

} else {
    header("Location: promote.php");
    exit();
}
