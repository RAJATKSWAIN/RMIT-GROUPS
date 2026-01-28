<?php
require_once("../includes/config.php");
require_once("../includes/db.php");
require_once("../includes/auth.php"); require_role(["student"]);
?>
<!DOCTYPE html><html><head>
<meta charset="UTF-8"><title>Student Dashboard | RMIT</title>
<link rel="stylesheet" href="../assets/css/styles.css">
</head><body>
<div class="container">
  <h2>Student Dashboard</h2>
  <p><a class="btn" href="exam_result.php">View Exam Results</a> <a class="btn" href="fees.php">Fees</a> <a class="btn" href="attendance.php">Attendance</a></p>
</div>
</body></html>
