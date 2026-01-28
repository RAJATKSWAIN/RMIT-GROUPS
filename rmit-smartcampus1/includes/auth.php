<?php
session_start();
function require_role($roles = []) {
  if (!isset($_SESSION["user"])) { header("Location: " . BASE_URL . "auth/login.php"); exit; }
  if (!in_array($_SESSION["user"]["role"], $roles)) { http_response_code(403); exit("Access denied"); }
}
?>
