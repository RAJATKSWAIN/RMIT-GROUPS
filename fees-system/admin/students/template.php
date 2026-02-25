<?php
// generate_template.php - FMS V 1.0.0
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';

checkLogin();

$role = $_SESSION['role_name'];
$sessInstId = $_SESSION['inst_id'];

// Define the filename
$filename = "student_bulk_template_" . date('Y-m-d') . ".csv";

// Set headers to force download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

// Open the output stream
$output = fopen('php://output', 'w');

// 1. Define the 17 Headers (Matching your corrected bulk_upload.php)
$headers = [
    'REGISTRATION_NO', 
    'ROLL_NO', 
    'FIRST_NAME', 
    'LAST_NAME', 
    'FATHER_NAME',      // Added
    'MOTHER_NAME',      // Added
    'GENDER',           // MALE, FEMALE, or OTHER
    'DOB',              // YYYY-MM-DD
    'MOBILE', 
    'EMAIL', 
    'ADDRESS', 
    'CITY', 
    'STATE', 
    'PINCODE', 
    'COURSE_ID',        // Must be a valid ID from the database
    'SEMESTER', 
    'ADMISSION_DATE'    // YYYY-MM-DD
];

fputcsv($output, $headers);

// 2. Fetch a valid sample Course ID
// If Superadmin, get any active course. If Admin, get course from their institute.
$course_sql = "SELECT COURSE_ID FROM COURSES WHERE STATUS='A'";
if ($role !== 'SUPERADMIN') {
    $course_sql .= " AND INST_ID = $sessInstId";
}
$course_sql .= " LIMIT 1";

$course_query = $conn->query($course_sql);
$course = $course_query->fetch_assoc();
$sample_course_id = $course ? $course['COURSE_ID'] : '1';

// 3. Add Sample Data Row (17 columns)
$sample_row = [
    'REG/2024/001', 
    'ROLL101', 
    'John', 
    'Doe', 
    'Mr. Robert Doe',    // Father Name Sample
    'Mrs. Mary Doe',      // Mother Name Sample
    'MALE', 
    '2005-08-15', 
    '9876543210', 
    'john@example.com', 
    'Street 10, Sector 4', 
    'Brahmapur', 
    'Odisha', 
    '760001', 
    $sample_course_id,
    '1', 
    date('Y-m-d')
];

fputcsv($output, $sample_row);

fclose($output);
exit;
