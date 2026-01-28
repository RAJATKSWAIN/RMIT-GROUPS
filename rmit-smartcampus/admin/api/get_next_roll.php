<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    exit("Unauthorized");
}

$dbConfig = [
  'host'=>'sql100.infinityfree.com',
  'user'=>'if0_40697103',
  'pass'=>'rmitgroups123',
  'name'=>'if0_40697103_rmit_smartcampus'
];

$conn = new mysqli(
  $dbConfig['host'],
  $dbConfig['user'],
  $dbConfig['pass'],
  $dbConfig['name']
);
if ($conn->connect_error) {
    http_response_code(500);
    exit("DB Error");
}

$institute = $_GET['institute'] ?? '';
$program   = $_GET['program'] ?? '';
$semester  = (int)($_GET['semester'] ?? 1);

/* ===== SAME JOIN YEAR LOGIC ===== */
$currentYear = (int)date('Y');

if ($semester <= 2)        $joinYear = $currentYear;
elseif ($semester <= 4)    $joinYear = $currentYear - 1;
else                       $joinYear = $currentYear - 2;

$yy = substr((string)$joinYear, -2);

/* ===== FETCH CURRENT SEQUENCE (NO UPDATE) ===== */
$stmt = $conn->prepare("
    SELECT prefix, start_value, current_value
    FROM roll_sequences
    WHERE institute_code=? AND program=?
");
$stmt->bind_param("ss", $institute, $program);
$stmt->execute();
$stmt->bind_result($basePrefix, $start, $current);

if ($stmt->fetch()) {
    $next = ($current == 0 ? $start : $current + 1);
} else {
    $basePrefix = "$institute-$program";
    $next = 1;
}

$stmt->close();

$rollNo = $basePrefix . '-' . $yy . str_pad($next, 3, "0", STR_PAD_LEFT);

echo json_encode([
    'roll' => $rollNo
]);
