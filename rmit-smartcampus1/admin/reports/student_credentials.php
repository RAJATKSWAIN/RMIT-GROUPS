<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    exit('Unauthorized');
}

require_once("../../includes/db.php");

$inst = $_SESSION['user']['institute_code'];

header('Content-Type: text/csv');
header('Pragma: no-cache');
header('Expires: 0');


header('Content-Disposition: attachment; filename="student_credentials_'.$inst.'.csv"');
$out = fopen('php://output', 'w');

fputcsv($out, ['Student Name','Roll No','Username','Password']);

$stmt = $conn->prepare("
SELECT s.roll_no, u.full_name, u.email
FROM students s
JOIN users u ON s.user_id=u.id
WHERE u.institute_code=?
");
$stmt->bind_param("s",$inst);
$stmt->execute();
$res=$stmt->get_result();

while($r=$res->fetch_assoc()){
  fputcsv($out, [$r['full_name'],$r['roll_no'],$r['email'],'pass@123']);
}
fclose($out); exit;
?>
