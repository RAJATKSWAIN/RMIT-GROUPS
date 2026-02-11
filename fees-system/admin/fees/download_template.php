<?php
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=fee_header_template.csv');

$output = fopen('php://output', 'w');

// Output the column headings based on your FeeService requirements
fputcsv($output, [
    'FEES_CODE', 
    'FEES_NAME', 
    'FEES_DESCRIPTION', 
    'APPLICABLE_LEVEL', 
    'MANDATORY_FLAG', 
    'REFUNDABLE_FLAG', 
    'DISPLAY_ORDER'
]);

// Add one example row so the user knows what to enter
fputcsv($output, [
    'TUI_FEE', 
    'Tuition Fee', 
    'Main Academic Fee', 
    'YEAR', 
    'Y', 
    'N', 
    '1'
]);

fclose($output);
exit;