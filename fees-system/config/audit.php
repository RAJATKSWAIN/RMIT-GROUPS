<?php
/*
|--------------------------------------------------------------------------
| FMS Global Audit Logger (Refined for InfinityFree/PHP 8.x)
|--------------------------------------------------------------------------
*/

if (!function_exists('audit_log')) {

    function audit_log(
        $conn,
        $action,
        $table = null,
        $refId = null,
        $old = null,
        $new = null,
        $amount = 0
    ) {
        try {
            // Safety check for connection
            if (!$conn || $conn->connect_error) return;

            // Ensure session is started to get admin_id
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $adminId = $_SESSION['admin_id'] ?? null;
            $ip      = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';

            // Convert arrays to JSON strings, ensure they are strings or NULL
            $oldValue = !is_null($old) ? json_encode($old) : null;
            $newValue = !is_null($new) ? json_encode($new) : null;
            
            // Format amount to ensure it's a double/float for the 'd' type
            $amountVal = (float)$amount;

            $sql = "INSERT INTO AUDIT_LOG 
                    (ACTION_TYPE, REF_TABLE, REF_ID, OLD_VALUE, NEW_VALUE, AMOUNT, ADMIN_ID, IP_ADDRESS, CREATED_AT) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

            $stmt = $conn->prepare($sql);
            
            if ($stmt) {
                // Type mapping: 
                // s=string, i=integer, d=double
                // action(s), table(s), refId(i), old(s), new(s), amount(d), adminId(i), ip(s)
                $stmt->bind_param(
                    "ssissdis", 
                    $action, 
                    $table, 
                    $refId, 
                    $oldValue, 
                    $newValue, 
                    $amountVal, 
                    $adminId, 
                    $ip
                );

                $stmt->execute();
                $stmt->close();
            }

        } catch (Exception $e) {
            // Silently fail so the main business logic (like a payment) still finishes
            error_log("Audit Log Error: " . $e->getMessage());
        }
    }
}