<?php
$host     = "sql100.infinityfree.com";
$username = "if0_40697103";
$password = "rmitgroups123";
$database = "if0_40697103_lms"; // <-- replace with the exact name from control panel

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>