<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';

// 1. Get Admin ID from session
$admin_id = $_SESSION['admin_id'] ?? 0; 
if (!$admin_id) {
    die("Error: Admin session not found.");
}

// 2. Setup Directory
$target_dir = BASE_PATH . '/backups/files/';
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// 3. File details
$backup_file = 'db_backup_' . date('Y-m-d_His') . '.sql';
$backup_path = $target_dir . $backup_file;

/**
 * Manual Export Function
 */
function exportDatabase($conn, $savePath) {
    $sqlContent = "-- PHP MySQL Dump\nSET FOREIGN_KEY_CHECKS=0;\n\n";
    $result = $conn->query("SHOW TABLES");
    while ($row = $result->fetch_array()) {
        $table = $row[0];
        $res = $conn->query("SHOW CREATE TABLE `$table` ");
        $row_struct = $res->fetch_array();
        $sqlContent .= "DROP TABLE IF EXISTS `$table`;\n" . $row_struct[1] . ";\n\n";

        $res_data = $conn->query("SELECT * FROM `$table` ");
        while ($data = $res_data->fetch_assoc()) {
            $values = array_map(function($v) use ($conn) {
                return is_null($v) ? "NULL" : "'" . $conn->real_escape_string($v) . "'";
            }, array_values($data));
            $sqlContent .= "INSERT INTO `$table` VALUES (" . implode(", ", $values) . ");\n";
        }
    }
    $sqlContent .= "\nSET FOREIGN_KEY_CHECKS=1;";
    return file_put_contents($savePath, $sqlContent);
}

// 4. Run and Insert Logs
if (exportDatabase($conn, $backup_path)) {
    // Calculate values for DB
    $file_size = filesize($backup_path) / (1024 * 1024); // Size in MB
    $file_size_formatted = number_format($file_size, 2, '.', '');

    // --- INSERT INTO BACKUP_LOG ---
    $logSql = "INSERT INTO BACKUP_LOG (FILE_NAME, FILE_SIZE_MB, CREATED_BY) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($logSql);
    $stmt->bind_param("sdi", $backup_file, $file_size_formatted, $admin_id);
    $stmt->execute();
    $new_backup_id = $stmt->insert_id; // Capture the ID for Audit Ref

    // --- INSERT INTO AUDIT_LOG (Matching your exact Schema) ---
    // Column Mapping:
    // ACTION_TYPE -> 'DATABASE_BACKUP'
    // REF_TABLE   -> 'BACKUP_LOG'
    // REF_ID      -> The ID from BACKUP_LOG
    // NEW_VALUE   -> The Backup Filename
    // AMOUNT      -> The File Size in MB
    
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $auditSql = "INSERT INTO AUDIT_LOG 
                 (ACTION_TYPE, REF_TABLE, REF_ID, OLD_VALUE, NEW_VALUE, AMOUNT, ADMIN_ID, IP_ADDRESS) 
                 VALUES ('BACKUP', 'BACKUP_LOG', ?, 'N/A', ?, ?, ?, ?)";
    
    $aStmt = $conn->prepare($auditSql);
    $aStmt->bind_param("isdis", $new_backup_id, $backup_file, $file_size_formatted, $admin_id, $ip);
    $aStmt->execute();

    header("Location: restore.php?status=success");
    exit();
} else {
    die("Error: Could not write file to disk.");
}