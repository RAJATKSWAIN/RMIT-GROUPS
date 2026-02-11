<?php
class PaymentService {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function processPayment($data) {
        $this->conn->begin_transaction();
        try {
            $studentId = intval($data['student_id']);
            $actualPaidAmount = floatval($data['amount']); 
            $adminId = intval($data['admin_id']);
            $feeNames = is_array($data['fee_names']) ? $data['fee_names'] : [];

            // 1. GLOBAL VALIDATION: Total Paid vs Total Ledger Outstanding
            // 1. DYNAMIC GLOBAL VALIDATION
			$currentBalance = $this->getStudentBalance($studentId);

			// We need to know if the user is paying for any "Service" fees 
			// (SEMESTER, ONETIME, GLOBAL) before we block based on Balance.
			$hasServiceFee = false;
			foreach ($feeNames as $name) {
    		$feeInfo = $this->getFeeDetails($studentId, $name);
    			if ($feeInfo && in_array($feeInfo['APPLICABLE_LEVEL'], ['SEMESTER', 'ONETIME', 'GLOBAL'])) {
        			$hasServiceFee = true;
       			 break; 
    			}
			}

			// Validation Logic:
			// If NO service fee is selected, we enforce the Ledger Balance cap.
			// If a service fee IS selected, we bypass the balance cap (as long as it passes the Master Cap in Step 3).
			if (!$hasServiceFee && $actualPaidAmount > $currentBalance) {
    		throw new Exception("Validation Error: Total payment (₹$actualPaidAmount) cannot exceed the outstanding ledger balance (₹$currentBalance).");
			}

            $selectedFeesDetails = [];
            $totalSelectedMaxAmount = 0;

            foreach ($feeNames as $name) {
                $feeInfo = $this->getFeeDetails($studentId, $name);
                if (!$feeInfo) continue;

                // 2. FREQUENCY VALIDATION
                $existingCount = $this->getFeeCountInYear($studentId, $name);
                if ($feeInfo['APPLICABLE_LEVEL'] === 'SEMESTER' && $existingCount >= 2) {
                    throw new Exception("Validation Error: $name has already been paid 2 times this year.");
                }
                if ($feeInfo['APPLICABLE_LEVEL'] === 'ONETIME' && $existingCount >= 1) {
                    throw new Exception("Validation Error: $name is a one-time fee and has already been paid.");
                }

                $totalSelectedMaxAmount += floatval($feeInfo['AMOUNT']);
                $feeInfo['FEES_NAME'] = $name; 
                $selectedFeesDetails[] = $feeInfo;
            }

            // 3. SPECIFIC FEE CAP VALIDATION
            if ($actualPaidAmount > $totalSelectedMaxAmount) {
                throw new Exception("Validation Error: Maximum charge for selected fees is ₹$totalSelectedMaxAmount.");
            }

            // 4. SETTLEMENT LOGIC: Sort Max to Min
            usort($selectedFeesDetails, function($a, $b) {
                return $b['AMOUNT'] <=> $a['AMOUNT'];
            });

            $remainingToDistribute = $actualPaidAmount;
            $settledRemarksArray= [];
            $totalLedgerReduction = 0;
            $counter = 1; // Initialize counter for numbering

            foreach ($selectedFeesDetails as $fee) {
                if ($remainingToDistribute <= 0) break;

                $maxPossibleForThisFee = floatval($fee['AMOUNT']);
                $allocation = min($remainingToDistribute, $maxPossibleForThisFee);

                // CHANGE: Format with Numbering and Currency Symbol
    			// Use the {Name} wrapper to keep your Frequency Validation (Step 2) working
   				// Use 'Rs.' which is safe for all encodings, or just leave it as numbers
				$settledRemarksArray[] = $counter . ". {" . $fee['FEES_NAME'] . "} (Rs. " . number_format($allocation, 2, '.', '') . ")";
    
    			if (in_array($fee['APPLICABLE_LEVEL'], ['YEAR' ,'ONETIME'])) {
        			$totalLedgerReduction += $allocation;
    				}
    
    				$remainingToDistribute -= $allocation;
    				$counter++; // Increment numbering
				}

            // 5. RECORD PAYMENT
            // Change this line inside processPayment():
			$receiptNo = $this->generateReceiptNo($studentId);
            
            // Store as [Settled: Fee Name (#Amount), Fee Name (#Amount)]
            $finalRemarks = "Settled: " . implode(", ", $settledRemarksArray) . " " . ($data['remarks'] ?? '');
            
            $stmt = $this->conn->prepare("INSERT INTO PAYMENTS (STUDENT_ID, RECEIPT_NO, PAID_AMOUNT, PAYMENT_MODE, TXN_REF, REMARKS, COLLECTED_BY) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isdsssi", $studentId, $receiptNo, $actualPaidAmount, $data['mode'], $data['ref'], $finalRemarks, $adminId);
            
            if (!$stmt->execute()) throw new Exception("Failed to record payment.");
            $paymentId = $this->conn->insert_id;

            // 6. UPDATE LEDGER
            // 6. UPDATE LEDGER (Production Ready)
			if ($totalLedgerReduction > 0) {
    		// We increment PAID_AMOUNT and decrement BALANCE_AMOUNT simultaneously
    			$upd = $this->conn->prepare("UPDATE STUDENT_FEE_LEDGER 
        									SET PAID_AMOUNT = PAID_AMOUNT + ?, 
            									BALANCE_AMOUNT = BALANCE_AMOUNT - ?, 
            									LAST_PAYMENT_DATE = NOW(), 
            									LAST_UPDATED = NOW() 
        										WHERE STUDENT_ID = ?
    										");

    			$upd->bind_param("ddi", $totalLedgerReduction, $totalLedgerReduction, $studentId);

    		if (!$upd->execute()) {
        		throw new Exception("Critical Error: Payment was authorized but Ledger failed to update. Transaction rolled back.");
    			}

    		// Optional but recommended: Verify a row was actually changed
    		if ($upd->affected_rows === 0) {
        		throw new Exception("Ledger Error: Student ledger record not found for Student ID: $studentId");
    			}
			}

            $this->conn->commit();
            return ['success' => true, 'receipt_no' => $receiptNo, 'payment_id' => $paymentId];

        } catch (Exception $e) {
            $this->conn->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function getStudentBalance($studentId) {
        $stmt = $this->conn->prepare("SELECT BALANCE_AMOUNT FROM STUDENT_FEE_LEDGER WHERE STUDENT_ID = ?");
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return floatval($res['BALANCE_AMOUNT'] ?? 0);
    }

    private function getFeeCountInYear($studentId, $feeName) {
    // 1. Calculate the start of the current Academic Year (April 1st)
    $month = (int)date('m');
    $currentYear = (int)date('Y');
    $fiscalYearStart = ($month >= 4) ? "$currentYear-04-01" : ($currentYear - 1) . "-04-01";

    // 2. Specific search pattern
    $searchTerm = "%{" . $feeName . "}%"; 
    
    // 3. Query based on the date range of the academic session
    $sql = "SELECT COUNT(*) as total FROM PAYMENTS 
            WHERE STUDENT_ID = ? 
            AND REMARKS LIKE ? 
            AND PAYMENT_DATE >= ?";
            
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("iss", $studentId, $searchTerm, $fiscalYearStart);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    return (int)$res['total'];
	}

    private function getFeeDetails($studentId, $feeName) {
    	// Added MH.ACTIVE_FLAG = 'A' to ensure deactivated fees cannot be processed
    	$sql = "SELECT MH.APPLICABLE_LEVEL, MD.AMOUNT 
            FROM MASTER_FEES_HDR MH
            JOIN MASTER_FEES_DTL MD ON MH.FEES_HDR_ID = MD.FEES_HDR_ID
            JOIN STUDENTS S ON S.COURSE_ID = MD.COURSE_ID
            WHERE S.STUDENT_ID = ? 
            AND MH.FEES_NAME = ? 
            AND MH.ACTIVE_FLAG = 'A' 
            LIMIT 1";
    	$stmt = $this->conn->prepare($sql);
    	$stmt->bind_param("is", $studentId, $feeName);
    	$stmt->execute();
    	return $stmt->get_result()->fetch_assoc();
	}

    private function generateReceiptNo($studentId) {
    // 1. Fetch the Course Code for this specific student
    $sqlCourse = "SELECT C.COURSE_CODE FROM STUDENTS S 
                  JOIN COURSES C ON S.COURSE_ID = C.COURSE_ID 
                  WHERE S.STUDENT_ID = ? LIMIT 1";
    $stmtC = $this->conn->prepare($sqlCourse);
    $stmtC->bind_param("i", $studentId);
    $stmtC->execute();
    $resC = $stmtC->get_result()->fetch_assoc();
    $courseCode = strtoupper($resC['COURSE_CODE'] ?? '');

    // 2. Map College Code based on Course Code
    $prefix = "HGI"; // Default Global Prefix
    if (in_array($courseCode, ['BCA', 'BES'])) {
        $prefix = "RMIT";
    } elseif (in_array($courseCode, ['DME', 'DCE', 'DEE', 'DCSE'])) {
        $prefix = "HIT";
    } elseif (in_array($courseCode, ['FIT', 'EMC', 'WLD', 'ELT'])) {
        $prefix = "RMITC";
    }

    // 3. Academic Financial Year Logic (April to March)
    $month = intval(date('m'));
    $year = intval(date('Y'));
    $fy = ($month >= 4) ? substr($year, -2) . "-" . substr($year + 1, -2) : substr($year - 1, -2) . "-" . substr($year, -2);

    // 4. Sequential Counter for the Specific College & FY
    $sqlSeq = "SELECT COUNT(*) + 1 as next_val FROM PAYMENTS WHERE RECEIPT_NO LIKE ?";
    $searchPattern = $prefix . "/" . $fy . "/%"; 
    $stmtS = $this->conn->prepare($sqlSeq);
    $stmtS->bind_param("s", $searchPattern);
    $stmtS->execute();
    $count = $stmtS->get_result()->fetch_assoc()['next_val'];

    // 5. Final Format: RMIT/25-26/00001
    return $prefix . "/" . $fy . "/" . str_pad($count, 5, "0", STR_PAD_LEFT);
	}
    
} // Cloeser of PaymentServices.php