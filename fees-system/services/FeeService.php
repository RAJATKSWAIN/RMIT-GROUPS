<?php
class FeeService {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create Fee Header with Validation & Auditing
     */
    public function createHeader($data) {
        // 1. Validation Check
        $valErrors = validateFeeHeader($data, $this->conn);
        if (!empty($valErrors)) {
            return ['success' => false, 'message' => implode(", ", $valErrors)];
        }

        $code = strtoupper(trim($data['fees_code']));
        $sql = "INSERT INTO MASTER_FEES_HDR (FEES_CODE, FEES_NAME, FEES_DESCRIPTION, APPLICABLE_LEVEL, MANDATORY_FLAG, REFUNDABLE_FLAG, DISPLAY_ORDER) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssssi", $code, $data['fees_name'], $data['fees_description'], $data['applicable_level'], $data['mandatory_flag'], $data['refundable_flag'], $data['display_order']);

        if ($stmt->execute()) {
            $newId = $this->conn->insert_id;
            // 2. Audit Logging
            audit_log($this->conn, 'CREATE_FEE_HDR', 'MASTER_FEES_HDR', $newId, null, "Code: $code");
            return ['success' => true, 'id' => $newId];
        }
        return ['success' => false, 'message' => $this->conn->error];
    }

    public function bulkUploadHeaders($file_path) {
        $handle = fopen($file_path, "r");
        fgetcsv($handle); 
        $results = ['success_count' => 0, 'errors' => []];
        $row_num = 2;
        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if (count($row) < 7) { $results['errors'][] = "Row $row_num: Invalid columns"; continue; }
            $res = $this->createHeader([
                'fees_code' => $row[0], 'fees_name' => $row[1], 'fees_description' => $row[2],
                'applicable_level' => $row[3], 'mandatory_flag' => $row[4], 'refundable_flag' => $row[5], 'display_order' => $row[6]
            ]);
            $res['success'] ? $results['success_count']++ : $results['errors'][] = "Row $row_num: ".$res['message'];
            $row_num++;
        }
        fclose($handle);
        audit_log($this->conn, 'BULK_UPLOAD_HDR', 'MASTER_FEES_HDR', null, null, "Imported: {$results['success_count']}");
        return $results;
    }

    /**
     * Map Fee with Validation & Auditing
     */
    public function mapFeeToCourse($courseId, $hdrId, $amount) {
        // 1. Validation Check
        $valErrors = validateFeeMapping($courseId, $hdrId, $amount, $this->conn);
        if (!empty($valErrors)) {
            return ['success' => false, 'message' => implode(", ", $valErrors)];
        }

        $check = $this->conn->prepare("SELECT FEES_DTL_ID FROM MASTER_FEES_DTL WHERE COURSE_ID = ? AND FEES_HDR_ID = ?");
        $check->bind_param("ii", $courseId, $hdrId);
        $check->execute();
        if ($check->get_result()->num_rows > 0) return ['success' => false, 'message' => "Mapping already exists."];

        $stmt = $this->conn->prepare("INSERT INTO MASTER_FEES_DTL (COURSE_ID, FEES_HDR_ID, AMOUNT) VALUES (?, ?, ?)");
        $stmt->bind_param("iid", $courseId, $hdrId, $amount);
        
        if ($stmt->execute()) {
            $newId = $this->conn->insert_id;
            audit_log($this->conn, 'MAP_FEE_COURSE', 'MASTER_FEES_DTL', $newId, null, "Course: $courseId, Hdr: $hdrId");
            return ['success' => true, 'id' => $newId];
        }
        return ['success' => false, 'message' => $this->conn->error];
    }

    public function bulkMapFees($file_path) {
        $handle = fopen($file_path, "r");
        fgetcsv($handle);
        $res = ['success_count' => 0, 'errors' => []];
        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $status = $this->mapFeeToCourse($row[0], $row[1], $row[2]);
            $status['success'] ? $res['success_count']++ : $res['errors'][] = "Row failed: ".$status['message'];
        }
        fclose($handle);
        audit_log($this->conn, 'BULK_MAP_FEES', 'MASTER_FEES_DTL', null, null, "Mapped: {$res['success_count']}");
        return $res;
    }

    public function copyFeeStructure($from, $to) {
    if ($from == $to) return 0; // Return 0 if same course

    $sql = "INSERT INTO MASTER_FEES_DTL (COURSE_ID, FEES_HDR_ID, AMOUNT) 
            SELECT ?, FEES_HDR_ID, AMOUNT FROM MASTER_FEES_DTL 
            WHERE COURSE_ID = ? AND FEES_HDR_ID NOT IN (SELECT FEES_HDR_ID FROM MASTER_FEES_DTL WHERE COURSE_ID = ?)";
            
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("iii", $to, $from, $to);
    
    if ($stmt->execute()) {
        $count = $this->conn->affected_rows; // Get the number of rows inserted
        if ($count > 0) {
            audit_log($this->conn, 'COPY_STRUCTURE', 'MASTER_FEES_DTL', $to, "From: $from", "Rows: $count");
        }
        return $count; // Return the actual number (0, 1, 2...)
    }
    return -1; // Return -1 for a real database error
}

    public function getHeaderById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM MASTER_FEES_HDR WHERE FEES_HDR_ID = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateHeader($id, $data) {
    $valErrors = validateFeeHeader($data, $this->conn, true, $id);
    if (!empty($valErrors)) {
        // Return the errors so the UI can show them
        return ['success' => false, 'message' => implode(", ", $valErrors)];
    }

    $sql = "UPDATE MASTER_FEES_HDR SET FEES_NAME = ?, FEES_DESCRIPTION = ?, 
            APPLICABLE_LEVEL = ?, MANDATORY_FLAG = ?, REFUNDABLE_FLAG = ?, 
            DISPLAY_ORDER = ?, ACTIVE_FLAG = ?, UPDATED_DATE = CURRENT_TIMESTAMP 
            WHERE FEES_HDR_ID = ?";
            
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("sssssisi", $data['fees_name'], $data['fees_description'], 
                      $data['applicable_level'], $data['mandatory_flag'], 
                      $data['refundable_flag'], $data['display_order'], 
                      $data['active_flag'], $id);
    
    if ($stmt->execute()) {
        audit_log($this->conn, 'UPDATE_FEE_HDR', 'MASTER_FEES_HDR', $id, null, "Updated values");
        return ['success' => true];
    }
    return ['success' => false, 'message' => $this->conn->error];
}
    
}