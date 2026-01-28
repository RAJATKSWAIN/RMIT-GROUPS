<?php
function e($s){ return htmlspecialchars($s, ENT_QUOTES, "UTF-8"); }
function flash($m){ $_SESSION["flash"] = $m; }
function flash_show(){ if(!empty($_SESSION["flash"])) { echo "<div class=\"alert\">".e($_SESSION["flash"])."</div>"; unset($_SESSION["flash"]); } }
?>
