<?php

$host = "sql100.infinityfree.com";
$db   = "if0_40697103_fms";
$user = "if0_40697103";
$pass = "rmitgroups123";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("DB Connection Failed: " . $conn->connect_error);
}

$conn->set_charset("utf8");
?>
