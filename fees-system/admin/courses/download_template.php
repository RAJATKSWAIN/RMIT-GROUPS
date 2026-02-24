<?php
// Ensure no whitespace exists before the <?php tag to avoid corrupting the CSV
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/core/auth.php';
checkLogin(); // Security check to ensure only logged-in users can download

// Clear any previous output buffers
if (ob_get_length()) ob_end_clean();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=course_import_template.csv');

// Open the "output" stream
$output = fopen('php://output', 'w');

// 1. Output the headers
// These must strictly match the keys used in your fgetcsv import logic later
fputcsv($output, ['Course_Code', 'Course_Name', 'Duration_Years']);

// 2. Output sample data rows (Educational examples)
fputcsv($output, ['BTECH-CS', 'Bachelor of Technology in Computer Science', '4']);
fputcsv($output, ['BCA', 'Bachelor of Computer Applications', '3']);
fputcsv($output, ['MBA', 'Master of Business Administration', '2']);
fputcsv($output, ['DME', 'Diploma in Mechanical Engineering', '3']);

fclose($output);
exit;
?>
