<?php
/**
 * LedgerService - Centralized Financial Management
 * Version 1.0.1
 */
class LedgerService {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * INITIALIZE: Sets up a student's ledger during registration.
     * Logic extracted from your current student_service.php.
     */
    public function initializeLedger($studentId, $courseId) {
        // Calculate mandatory fees for the course
        $sql = "SELECT SUM(D.AMOUNT) as total 
                FROM MASTER_FEES_DTL D
                INNER JOIN MASTER_FEES_HDR H ON D.FEES_HDR_ID = H.FEES_HDR_ID
                WHERE D.COURSE_ID = ? 
                AND D.ACTIVE_FLAG = 'A' 
                AND H.ACTIVE_FLAG = 'A'
                AND H.MANDATORY_FLAG = 'Y'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $total = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

        // Insert fresh ledger record
        $ledgerSql = "INSERT INTO STUDENT_FEE_LEDGER (STUDENT_ID, TOTAL_FEE, BALANCE_AMOUNT, LAST_UPDATED) 
                      VALUES (?, ?, ?, NOW())
                      ON DUPLICATE KEY UPDATE TOTAL_FEE = ?, BALANCE_AMOUNT = ?";
        
        $lstmt = $this->conn->prepare($ledgerSql);
        $lstmt->bind_param("idddd", $studentId, $total, $total, $total, $total);
        return $lstmt->execute();
    }

    /**
     * TRANSACTION: Updates balance after a successful payment.
     * Logic extracted from your current PaymentService.php.
     */
    public function updateBalance($studentId, $amount) {
        $sql = "UPDATE STUDENT_FEE_LEDGER 
                SET PAID_AMOUNT = PAID_AMOUNT + ?, 
                    BALANCE_AMOUNT = BALANCE_AMOUNT - ?, 
                    LAST_PAYMENT_DATE = NOW(), 
                    LAST_UPDATED = NOW() 
                WHERE STUDENT_ID = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ddi", $amount, $amount, $studentId);
        
        if (!$stmt->execute()) {
            throw new Exception("Ledger Sync Error: " . $this->conn->error);
        }
        
        if ($stmt->affected_rows === 0) {
            throw new Exception("Ledger Error: Record not found for Student ID $studentId.");
        }
        return true;
    }

    /**
     * GET SUMMARY: Helper for Dashboard/Profile
     */
    public function getStudentSummary($studentId) {
        $sql = "SELECT * FROM STUDENT_FEE_LEDGER WHERE STUDENT_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    // For reports/dues.php
    public function getPendingDues() {
        $sql = "SELECT s.FIRST_NAME, s.LAST_NAME, s.REGISTRATION_NO, c.COURSE_CODE, 
                       l.TOTAL_FEE, l.PAID_AMOUNT, l.BALANCE_AMOUNT 
                FROM STUDENT_FEE_LEDGER l
                JOIN STUDENTS s ON l.STUDENT_ID = s.STUDENT_ID
                JOIN COURSES c ON s.COURSE_ID = c.COURSE_ID
                WHERE l.BALANCE_AMOUNT > 0
                ORDER BY l.BALANCE_AMOUNT DESC";
        return $this->conn->query($sql);
    }

    // For reports/total.php
    public function getCollectionSummary() {
        $sql = "SELECT SUM(TOTAL_FEE) as total_expected, 
                       SUM(PAID_AMOUNT) as total_collected, 
                       SUM(BALANCE_AMOUNT) as total_outstanding 
                FROM STUDENT_FEE_LEDGER";
        return $this->conn->query($sql)->fetch_assoc();
    }
    
    
}