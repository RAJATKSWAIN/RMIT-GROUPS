<?php
require_once("../includes/config.php");
require_once("../includes/db.php");
require_once("../includes/auth.php"); require_role(["faculty"]);
?>
<!DOCTYPE html><html><head>
<meta charset="UTF-8"><title>Take Attendance | RMIT</title>
<link rel="stylesheet" href="../assets/css/styles.css">
</head><body>
<div class="container">
  <h3>Take Attendance</h3>
  <form method="post">
    <label>Course</label>
    <select name="course_id" required>
      <?php
        $fid = $_SESSION["user"]["id"];
        $q = $conn->prepare("SELECT c.id, c.code, c.title FROM course_assignments ca JOIN courses c ON c.id=ca.course_id WHERE ca.faculty_id=(SELECT id FROM faculty WHERE user_id=?)");
        $q->bind_param("i", $fid); $q->execute(); $r=$q->get_result();
        while($c=$r->fetch_assoc()) echo "<option value='{$c['id']}'>".htmlspecialchars($c['code'])." - ".htmlspecialchars($c['title'])."</option>";
      ?>
    </select>
    <label>Date</label><input type="date" name="att_date" required>
    <button type="submit" name="load" class="btn">Load Students</button>
  </form>
  <?php
  if(isset($_POST["load"])){
    $course_id = (int)$_POST["course_id"];
    $att_date = $_POST["att_date"];
    $s = $conn->prepare("SELECT s.id, u.full_name, s.roll_no FROM enrollments e JOIN students s ON s.id=e.student_id JOIN users u ON u.id=s.user_id WHERE e.course_id=? ORDER BY s.roll_no");
    $s->bind_param("i",$course_id); $s->execute(); $rs=$s->get_result();
    echo "<form method='post'><input type='hidden' name='course_id' value='$course_id'><input type='hidden' name='att_date' value='$att_date'>";
    while($st=$rs->fetch_assoc()){
      echo "<div class='card'><span>".htmlspecialchars($st['roll_no'])." - ".htmlspecialchars($st['full_name'])."</span>
      <select name='status[{$st['id']}]'><option value='present'>Present</option><option value='absent'>Absent</option><option value='late'>Late</option></select></div>";
    }
    echo "<button type='submit' name='save' class='btn'>Save Attendance</button></form>";
  }
  if(isset($_POST["save"])){
    $course_id=(int)$_POST["course_id"]; $att_date=$_POST["att_date"];
    $fid = $_SESSION["user"]["id"];
    $fidq = $conn->query("SELECT id FROM faculty WHERE user_id=".$fid)->fetch_assoc()["id"];
    foreach($_POST["status"] as $sid=>$st){
      $ins = $conn->prepare("INSERT INTO attendance (course_id, student_id, faculty_id, attendance_date, status) VALUES (?,?,?,?,?) ON DUPLICATE KEY UPDATE status=VALUES(status)");
      $ins->bind_param("iiiss", $course_id, $sid, $fidq, $att_date, $st); $ins->execute();
    }
    echo "<p>Attendance saved.</p>";
  }
  ?>
</div>
</body></html>
