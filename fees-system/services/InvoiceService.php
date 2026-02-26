<!--======================================================
    File Name   : InvoiceService.php
    Project     : RMIT Groups - FMS - Fees Management System
    Description : SERVICE FOR INVOICE GENERATION
    Developed By: TrinityWebEdge
    Date Created: 06-02-2025
    Last Updated: 66-02-2025
    Note        : This page defines the FMS - Fees Management System | BACKEND SERVICES MODULE of RMIT Groups website.
=======================================================-->
<?php
/**
 * UPDATED INVOICE SERVICE
 * Fixes: ValueError Path cannot be empty (Font Cache Issue)
 */
require_once BASE_PATH . '/vendor/dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

class InvoiceService {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function generateDigitalInvoice($payment_id) {
        // 1. FETCH FULL DATA
        $query = "SELECT p.*, s.FIRST_NAME, s.LAST_NAME, s.REGISTRATION_NO, 
                  c.COURSE_NAME, c.COURSE_CODE, l.BALANCE_AMOUNT 
                  FROM PAYMENTS p 
                  JOIN STUDENTS s ON p.STUDENT_ID = s.STUDENT_ID 
                  JOIN COURSES c ON s.COURSE_ID = c.COURSE_ID 
                  JOIN STUDENT_FEE_LEDGER l ON s.STUDENT_ID = l.STUDENT_ID
                  WHERE p.PAYMENT_ID = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $payment_id);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_assoc();

        if (!$data) {
            throw new Exception("Unable to find payment record for ID: " . $payment_id);
        }
        
        // --- CRITICAL FIX START ---
        // We define $conn here so the included template can "see" it inside this method's scope.
        $conn = $this->conn; 
        // --- CRITICAL FIX END ---

        // 2. Capture HTML Template
        ob_start();
        include BASE_PATH . '/admin/payments/receipt_template.php'; 
        $html = ob_get_clean();

        // 3. CONFIGURE DOMPDF (Critical Fixes for InfinityFree)
        $options = new Options();
        
        /** * FIX: Path cannot be empty error 
         * We must explicitly set font directories to writable paths within htdocs
         */
        $tempDir = BASE_PATH . '/temp';
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        $options->set('fontDir', $tempDir);
        $options->set('fontCache', $tempDir);
        $options->set('tempDir', $tempDir);

        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true); 
        $options->set('defaultFont', 'DejaVu Sans'); // Set to DejaVu to support Rupee Symbol

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        
        try {
            $dompdf->render();
        } catch (Exception $e) {
            throw new Exception("PDF Rendering Error: " . $e->getMessage());
        }

        // 4. Prepare File Path
        $filename = "INV_" . $data['RECEIPT_NO'] . "_" . time() . ".pdf";
        $filePath = 'invoices/generated/' . $filename;
        $fullPath = BASE_PATH . '/' . $filePath;

        // Ensure target directory exists
        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0777, true);
        }

        // 5. Save File
        if (!file_put_contents($fullPath, $dompdf->output())) {
            throw new Exception("Permission Denied: Cannot write to $fullPath");
        }

        // 6. Update Database
        $this->saveToDatabase($payment_id, $data['STUDENT_ID'], $data['RECEIPT_NO'], $filePath);

        return $filePath;
    }

    private function saveToDatabase($payment_id, $student_id, $invoice_no, $path) {
    	// USE ON DUPLICATE KEY UPDATE so you don't get multiple rows for one payment
    	$sql = "INSERT INTO INVOICES (PAYMENT_ID, STUDENT_ID, INVOICE_NO, FILE_PATH) 
            	VALUES (?, ?, ?, ?)
            	ON DUPLICATE KEY UPDATE FILE_PATH = VALUES(FILE_PATH)";
    	$stmt = $this->conn->prepare($sql);
    	$stmt->bind_param("iiss", $payment_id, $student_id, $invoice_no, $path);
    	$stmt->execute();
	}
}
