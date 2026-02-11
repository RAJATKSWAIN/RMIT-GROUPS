<?php
$host     = "sql100.infinityfree.com";
$username = "if0_40697103";
$password = "rmitgroups123";
$database = "if0_40697103_XXX"; // replace XXX with your actual DB name

// Create connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>