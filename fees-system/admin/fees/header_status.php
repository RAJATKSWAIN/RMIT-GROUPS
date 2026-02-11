<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';

checkLogin();

$id = intval($_GET['id']);
$status = ($_GET['status'] == 'I') ? 'I' : 'A';

$stmt = $conn->prepare("UPDATE MASTER_FEES_HDR SET ACTIVE_FLAG = ? WHERE FEES_HDR_ID = ?");
$stmt->bind_param("si", $status, $id);
$stmt->execute();

header("Location: header_add.php");
exit();