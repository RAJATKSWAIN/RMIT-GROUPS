<?php
header('Content-Type: application/json');
require_once '../config/db.php';
require_once '../core/auth.php';

// Security check
if (session_status() === PHP_SESSION_NONE) session_start();
$instId = $_SESSION['inst_id'] ?? 0;

if ($instId == 0) {
    echo json_encode(['status' => 'error', 'message' => 'Session expired.']);
    exit;
}

$name = $_POST['course_name'] ?? '';
$code = $_POST['course_code'] ?? '';

if (empty($name) || empty($code)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit;
}

$sql = "INSERT INTO COURSES (COURSE_NAME, COURSE_CODE, INST_ID, STATUS) VALUES (?, ?, ?, 'A')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $name, $code, $instId);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Course added successfully!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $conn->error]);
}
