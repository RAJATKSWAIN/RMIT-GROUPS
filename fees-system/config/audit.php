<?php
/* ===========================================*/
/* -- FMS Global Universal Audit Logger (Refined) -- */
/*=========================================== */
if (!function_exists('audit_log')) {
    /**
     * @param mysqli $conn    The active database connection
     * @param string $action  e.g., 'INSERT', 'UPDATE', 'DELETE', 'LOGIN'
     * @param string|null $table   The table name (e.g., 'STUDENTS')
     * @param int|null $refId      The PK/ID of the record
     * @param mixed $old           Previous data
     * @param mixed $new           New data or Remarks
     * @param float $amount        Financial value
     */
    function audit_log($conn, $action, $table = null, $refId = null, $old = null, $new = null, $amount = 0) {
        try {
            // 1. Connection & Session Guard
            if (!$conn || $conn->connect_error) return;
            if (session_status() === PHP_SESSION_NONE) session_start();

            // 2. ADMIN_ID Identification
            $adminId = $_SESSION['admin_id'] ?? $_SESSION['user_id'] ?? null;

            // If no admin is logged in, we use a "System" ID (0) or skip.
            // Check if your DB allows 0 in ADMIN_MASTER; if not, keep the return.
            if (empty($adminId) || !is_numeric($adminId)) {
                error_log("Audit Log Skipped: No valid ADMIN_ID session found for action: $action");
                return;
            }

            // 3. Data Preparation
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
            // Split in case of multiple proxy IPs
            $ip = explode(',', $ip)[0];

            $refIdVal  = ($refId !== null) ? intval($refId) : null;
            $amountVal = (float)$amount;

            // JSON_UNESCAPED_SLASHES prevents URLs from looking messy
            $oldValue = is_array($old) ? json_encode($old, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : (string)$old;
            $newValue = is_array($new) ? json_encode($new, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : (string)$new;

            // 4. Database Insertion
            // Note: If REF_ID is null, we need to handle that in bind_param
            $sql = "INSERT INTO AUDIT_LOG 
                    (ACTION_TYPE, REF_TABLE, REF_ID, OLD_VALUE, NEW_VALUE, AMOUNT, ADMIN_ID, IP_ADDRESS, CREATED_AT) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

            $stmt = $conn->prepare($sql);
            if ($stmt) {
                // We use 's' for refId if it can be null, or handle it specifically
                $stmt->bind_param("ssissdis", 
                    $action, 
                    $table, 
                    $refIdVal, 
                    $oldValue, 
                    $newValue, 
                    $amountVal, 
                    $adminId, 
                    $ip
                );
                
                if (!$stmt->execute()) {
                    // Check for Foreign Key failure (1452)
                    if ($stmt->errno === 1452) {
                        error_log("Audit Log FK Error: ADMIN_ID $adminId does not exist in ADMIN_MASTER.");
                    } else {
                        error_log("Audit Log Execution Error: " . $stmt->error);
                    }
                }
                $stmt->close();
            }
        } catch (Throwable $e) {
            // Throwable catches both Errors and Exceptions (PHP 7+)
            error_log("Universal Audit Error: " . $e->getMessage());
        }
    }
}
