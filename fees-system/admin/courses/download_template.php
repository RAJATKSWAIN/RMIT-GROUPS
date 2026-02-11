<?php
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=course_template.csv');

// Open the "output" stream
$output = fopen('php://output', 'w');

// 1. Output the headers (Must match your fgetcsv logic)
fputcsv($output, ['Course_Code', 'Course_Name', 'Duration_Years']);

// 2. Output sample data rows
fputcsv($output, ['BTECH-CS', 'Bachelor of Technology in Computer Science', '4']);
fputcsv($output, ['BCA', 'Bachelor of Computer Applications', '3']);
fputcsv($output, ['MBA', 'Master of Business Administration', '2']);
fputcsv($output, ['DIPLOMA-ME', 'Diploma in Mechanical Engineering', '3']);

fclose($output);
exit;
?>