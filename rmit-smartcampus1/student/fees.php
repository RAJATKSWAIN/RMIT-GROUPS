<?php
require_once("../includes/config.php");
require_once("../includes/db.php");
require_once("../includes/auth.php"); require_role(["student"]);
$uid = $_SESSION["user"]["id"];
$stu = $conn->query("SELECT id, program, semester FROM students WHERE user_id={$uid}")->fetch_assoc();
?>
<!DOCTYPE html><html><head>
<meta charset="UTF-8"><title>Fees | RMIT</title>
<link rel="stylesheet" href="../assets/css/styles.css">
</head><body>
<div class="container">
  <h2>Fee Status</h2>
  <?php
    $plans = $conn->query("SELECT fp.id, amount, due_date FROM fee_plans fp 
                           WHERE fp.program='".$conn->real_escape_string($stu['program'])."' 
                           AND fp.semester=".$stu['semester']." AND active=1");
    while($p=$plans->fetch_assoc()){
      $paid = $conn->query("SELECT COALESCE(SUM(amount_paid),0) s FROM fee_payments WHERE student_id=".$stu['id']." AND fee_plan_id=".$p['id'])->fetch_assoc()['s'];
      $balance = $p['amount'] - $paid;
      echo "<div class='card'><strong>Due:</strong> ₹{$p['amount']} by {$p['due_date']} — <strong>Paid:</strong> ₹{$paid} — <strong>Balance:</strong> ₹{$balance}</div>";
      if($balance > 0){
        echo "<form method='post'>
                <input type='hidden' name='plan_id' value='{$p['id']}'>
                <label>Amount to Pay</label><input type='number' name='amount' max='{$balance}' required>
                <label>Transaction Ref</label><input type='text' name='txn_ref' required>
                <button type='submit' name='pay' class='btn'>Pay Now</button>
              </form>";
      }
    }
    if(isset($_POST['pay'])){
      $plan_id = (int)$_POST['plan_id'];
      $amount  = (float)$_POST['amount'];
      $txn_ref = $_POST['txn_ref'];
      $stmt = $conn->prepare("INSERT INTO fee_payments (student_id, fee_plan_id, amount_paid, txn_ref) VALUES (?,?,?,?)");
      $stmt->bind_param("iiis", $stu['id'], $plan_id, $amount, $txn_ref);
      echo $stmt->execute() ? "<p>✅ Payment recorded.</p>" : "<p class='error'>❌ Error saving payment.</p>";
    }
  ?>
</div>
</body></html>
