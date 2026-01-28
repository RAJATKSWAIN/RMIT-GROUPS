<?php
// Update with your actual InfinityFree credentials
$servername = "sql100.infinityfree.com";
$username   = "if0_40697103";
$password   = "rmitgroups123";
$dbname     = "if0_40697103_rmit_smartcampus";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("DB connection failed: " . $conn->connect_error); }
$conn->set_charset("utf8mb4");
?>
