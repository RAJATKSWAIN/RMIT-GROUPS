<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php'; // Ensures $_SESSION['admin_id'] is available
require_once BASE_PATH.'/vendor/autoload.php';

use Google\Client;
use Google\Service\Drive;

// 1. Setup File Paths
$admin_id = $_SESSION['admin_id']; // From ADMIN_MASTER
$backup_file = 'fees_db_' . date('Y-m-d_His') . '.sql';
$backup_path = BASE_PATH . '/backups/files/' . $backup_file;

// 2. Execute SQL Dump
// Replace with your actual DB credentials
$command = "mysqldump -u root fees_db > " . escapeshellarg($backup_path);
exec($command, $output, $return_var);

if ($return_var === 0) {
    // 3. Calculate File Size in MB
    $file_size_mb = filesize($backup_path) / (1024 * 1024);

    try {
        // 4. Google Drive Upload Logic
        $client = new Client();
        $client->setAuthConfig(BASE_PATH . '/config/google-service-account.json');
        $client->addScope(Drive::DRIVE_FILE);
        $service = new Drive($client);

        $fileMetadata = new Drive\DriveFile([
            'name' => $backup_file,
            'parents' => ['YOUR_GDRIVE_FOLDER_ID'] 
        ]);

        $content = file_get_contents($backup_path);
        $drive_file = $service->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => 'application/sql',
            'uploadType' => 'multipart'
        ]);

        if ($drive_file->id) {
            // 5. POST DATA TO BACKUP_LOG
            $logSql = "INSERT INTO BACKUP_LOG (FILE_NAME, FILE_SIZE_MB, CREATED_BY) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($logSql);
            $stmt->bind_param("sdi", $backup_file, $file_size_mb, $admin_id);
            $stmt->execute();

            // 6. LOG TO AUDIT_LOG
            $details = "Manual Backup created and uploaded to GDrive. File: $backup_file";
            $auditSql = "INSERT INTO AUDIT_LOG (USER_ID, ACTION_TYPE, DESCRIPTION, IP_ADDRESS) VALUES (?, 'BACKUP', ?, ?)";
            $ip = $_SERVER['REMOTE_ADDR'];
            $aStmt = $conn->prepare($auditSql);
            $aStmt->bind_param("iss", $admin_id, $details, $ip);
            $aStmt->execute();

            header("Location: view_backups.php?status=success");
        }
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}