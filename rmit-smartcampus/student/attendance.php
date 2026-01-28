<?php
require_once("../includes/config.php");
require_once("../includes/db.php");
require_once("../includes/auth.php"); require_role(["student"]);
$uid = $_SESSION["user"]["id"];
$sid = $conn->query("SELECT id FROM students WHERE user_id=".$uid)->fetch_assoc()["id"];
?>
<!DOCTYPE html><html><head>
<meta charset="UTF-8"><title>Attendance | RMIT</title>
<link rel="stylesheet" href="../assets/css/styles.css">
</head><body>
<div class="container">
  <h3>Attendance</h3>
  <?php
    $rows = $conn->query("SELECT c.code, c.title, attendance_date, status FROM attendance a JOIN courses c ON c.id=a.course_id WHERE a.student_id=".$sid." ORDER BY attendance_date DESC");
    while($r=$rows->fetch_assoc()){
      echo "<div class='card'><strong>".htmlspecialchars($r['code'])."</strong> — ".htmlspecialchars($r['title'])." — ".$r['attendance_date']." — ".$r['status']."</div>";
    }
  ?>
</div>
</body></html>
