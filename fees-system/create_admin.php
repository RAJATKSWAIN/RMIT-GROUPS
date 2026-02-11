<?php
require_once "config/db.php";

$pass = password_hash("admin1", PASSWORD_DEFAULT);

$conn->query("
INSERT INTO ADMIN_MASTER
(USERNAME,PASSWORD_HASH,FULL_NAME,EMAIL)
VALUES
('admin','$pass','Super Admin','superadmin@fms.in')
");

echo "Admin created";
?>
