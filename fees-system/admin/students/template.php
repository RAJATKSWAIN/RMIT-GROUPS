<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';

checkLogin();

// Define the filename
$filename = "student_import_template_" . date('Y-m-d') . ".csv";

// Set headers to force download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

// Open the output stream
$output = fopen('php://output', 'w');

// 1. Define the CSV Headers (Matching your bulk_upload.php logic)
$headers = [
    'REGISTRATION_NO', 
    'ROLL_NO', 
    'FIRST_NAME', 
    'LAST_NAME', 
    'GENDER', 
    'DOB', 
    'MOBILE', 
    'EMAIL', 
    'ADDRESS', 
    'CITY', 
    'STATE', 
    'PINCODE', 
    'COURSE_ID', 
    'SEMESTER', 
    'ADMISSION_DATE'
];

fputcsv($output, $headers);

// 2. Add a Sample Data Row to help the user
// Fetch one real Course ID from the DB to make the sample valid
$course_query = $conn->query("SELECT COURSE_ID FROM COURSES WHERE STATUS='A' LIMIT 1");
$course = $course_query->fetch_assoc();
$sample_course_id = $course ? $course['COURSE_ID'] : '1';

$sample_row = [
    'REG/2024/001', 
    'ROLL101', 
    'John', 
    'Doe', 
    'MALE', 
    '2005-05-15', 
    '9876543210', 
    'john@example.com', 
    '123, Academic Block', 
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