<?php
require_once(__DIR__ . "/../includes/config.php");
require_once(__DIR__ . "/../includes/db.php");

$defaultAdmins = [
  ['HIT',   'admin.hit@rmitgroups.org',   'Admin HIT'],
  ['RMIT',  'admin.rmit@rmitgroups.org',  'Admin RMIT'],
  ['RMITC', 'admin.rmitc@rmitgroups.org', 'Admin RMITC'],
  ['CPS',   'admin.cps@rmitgroups.org',   'Admin CPS'],
];

$defaultPassword = 'Admin@123'; // change after first login

foreach ($defaultAdmins as [$code, $email, $name]) {

    $check = $conn->prepare("SELECT id FROM users WHERE email=?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {

        $hash = password_hash($defaultPassword, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("
            INSERT INTO users (institute_code, role, email, password_hash, full_name)
            VALUES (?, 'admin', ?, ?, ?)
        ");

        $stmt->bind_param("ssss", $code, $email, $hash, $name);
        $stmt->execute();

        echo "✅ Created admin for $code<br>";
    } else {
        echo "ℹ️ Admin already exists for $code<br>";
    }
}
