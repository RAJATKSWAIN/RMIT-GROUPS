<?php
/* =====================================
   CENTRAL VALIDATOR SERVICE
===================================== */

/**
 * 1. Validates File Extension and Upload Status
 */
function validateCSV($file) {
    if (empty($file['tmp_name'])) {
        return "No file uploaded.";
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    if (strtolower($ext) !== 'csv') {
        return "Invalid file extension. Please upload a .csv file.";
    }

    return null; 
}

/**
 * 2. Validates CSV Structure and Column Count
 */
function validateCSVContent($file_path, $expected_column_count = 15) {
    if (!file_exists($file_path) || !is_readable($file_path)) {
        return "Cannot read the uploaded file.";
    }

    $handle = fopen($file_path, "r");
    $headers = fgetcsv($handle);
    
    if (!$headers || count($headers) !== $expected_column_count) {
        fclose($handle);
        return "Invalid CSV format. Expected $expected_column_count columns, found " . ($headers ? count($headers) : 0);
    }

    fclose($handle);
    return null; 
}

/**
 * Universal Student Data Validator
 * Works for both add.php (Single) and Bulk Upload
 */
function validateStudentData($data, $conn) {
    $errors = [];
    $instId = intval($data['inst_id']);

    // A. Required Fields Mapping
    $required = [
        'reg'         => 'Registration No', 
        'roll'        => 'Roll No', 
        'fname'       => 'First Name', 
        'father_name' => 'Father Name', 
        'mother_name' => 'Mother Name', 
        'course'      => 'Course',
        'mobile'      => 'Mobile Number'
    ];

    foreach ($required as $key => $label) {
        if (!isset($data[$key]) || trim($data[$key]) === '') {
            $errors[] = "$label is required.";
        }
    }
    
    // Return early if basic fields are missing to save DB queries
    if (!empty($errors)) return $errors;

    // B. Course-Institute Integrity Check
    $courseId = intval($data['course']);
    $cCheck = $conn->prepare("SELECT COURSE_ID FROM COURSES WHERE COURSE_ID = ? AND INST_ID = ? AND STATUS = 'A'");
    $cCheck->bind_param("ii", $courseId, $instId);
    $cCheck->execute();
    if ($cCheck->get_result()->num_rows === 0) {
        $errors[] = "The selected Course is not valid for this Institute.";
    }

    // C. Global & Institute-Scoped Uniqueness Check
    // Reg/Roll must be unique PER Institute. Email/Mobile must be unique GLOBALLY.
    $stmt = $conn->prepare("SELECT REGISTRATION_NO, ROLL_NO, MOBILE, EMAIL FROM STUDENTS 
                            WHERE ((REGISTRATION_NO = ? OR ROLL_NO = ?) AND INST_ID = ?) 
                            OR (MOBILE = ? AND MOBILE != '') 
                            OR (EMAIL = ? AND EMAIL != '') LIMIT 1");
    
    $stmt->bind_param("ssiss", $data['reg'], $data['roll'], $instId, $data['mobile'], $data['email']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if ($row['REGISTRATION_NO'] === $data['reg']) $errors[] = "Registration No '{$data['reg']}' already exists in this institute.";
        if ($row['ROLL_NO'] === $data['roll']) $errors[] = "Roll No '{$data['roll']}' already exists in this institute.";
        if (!empty($data['mobile']) && $row['MOBILE'] === $data['mobile']) $errors[] = "Mobile '{$data['mobile']}' is already registered.";
        if (!empty($data['email']) && $row['EMAIL'] === $data['email']) $errors[] = "Email '{$data['email']}' is already registered.";
    }
    
    return $errors;
}


/**
 * 4. Validates Course Data (Updated for Duration Numeric Check)
 */
function validateCourseData($data, $conn, $is_update = false, $course_id = null) {
    $errors = [];

    // A. EMPTY & NULL CHECKS
    if (empty(trim($data['code']))) {
        $errors[] = "Course Code is required.";
    }
    if (empty(trim($data['name']))) {
        $errors[] = "Course Name is required.";
    }

    // B. DURATION NUMERIC VALIDATION
    $duration = $data['duration'] ?? null;
    if ($duration === null || trim($duration) === "") {
        $errors[] = "Course Duration is required.";
    } elseif (!is_numeric($duration)) {
        $errors[] = "Duration must be a valid number.";
    } else {
        $d_val = intval($duration);
        if ($d_val < 1 || $d_val > 7) {
            $errors[] = "Duration must be between 1 and 7 years.";
        }
    }

    if (!empty($errors)) return $errors;

    // C. UNIQUENESS CHECK (Course Code)
    $code = $conn->real_escape_string(strtoupper(trim($data['code'])));
    
    $sql = "SELECT COURSE_CODE FROM COURSES WHERE COURSE_CODE = '$code'";
    if ($is_update && $course_id) {
        $sql .= " AND COURSE_ID != " . intval($course_id);
    }
    $sql .= " LIMIT 1";

    $check = $conn->query($sql);
    if ($check && $check->num_rows > 0) {
        $errors[] = "Course Code '$code' already exists.";
    }

    return $errors;
}

/**
 * 5. Validates Fee Header Data (Creation & Update)
 */
function validateFeeHeader($data, $conn, $is_update = false, $hdr_id = null) {
    $errors = [];
    
    // Use null coalescing (?? '') to prevent errors if keys are missing
    $f_code = trim($data['fees_code'] ?? '');
    $f_name = trim($data['fees_name'] ?? '');
    $f_level = strtoupper(trim($data['applicable_level'] ?? ''));

    // During Update, the Code is often readonly/missing from POST. 
    // We only validate code presence for NEW records.
    if (!$is_update && empty($f_code)) {
        $errors[] = "Fee Code is required.";
    }
    
    if (empty($f_name)) $errors[] = "Fee Name is required.";
    
    $allowed_levels = ['COURSE', 'SEMESTER', 'YEAR', 'ONETIME', 'GLOBAL'];
    if (!empty($f_level) && !in_array($f_level, $allowed_levels)) {
        $errors[] = "Invalid Level. Allowed: " . implode(', ', $allowed_levels);
    }

    if (isset($data['display_order']) && $data['display_order'] !== '' && !is_numeric($data['display_order'])) {
        $errors[] = "Display Order must be a number.";
    }

    // Only check uniqueness if we actually have a code to check
    if (!empty($f_code)) {
        $code = $conn->real_escape_string(strtoupper($f_code));
        $sql = "SELECT FEES_HDR_ID FROM MASTER_FEES_HDR WHERE FEES_CODE = '$code'";
        if ($is_update && $hdr_id) { 
            $sql .= " AND FEES_HDR_ID != " . intval($hdr_id); 
        }
        
        $check = $conn->query($sql . " LIMIT 1");
        if ($check && $check->num_rows > 0) {
            $errors[] = "Fee Code '$code' is already in use.";
        }
    }
    
    return $errors;
}

/**
 * 6. Validates Fee to Course Mapping
 */
function validateFeeMapping($course_id, $hdr_id, $amount, $conn) {
    $errors = [];
    // Check if Course Exists & is Active
    $c_check = $conn->query("SELECT COURSE_ID FROM COURSES WHERE COURSE_ID = ".intval($course_id)." AND STATUS = 'A'");
    if ($c_check->num_rows === 0) $errors[] = "Invalid or Inactive Course ID.";

    // Check if Fee Header Exists & is Active
    $h_check = $conn->query("SELECT FEES_HDR_ID FROM MASTER_FEES_HDR WHERE FEES_HDR_ID = ".intval($hdr_id)." AND ACTIVE_FLAG = 'A'");
    if ($h_check->num_rows === 0) $errors[] = "Invalid or Inactive Fee Header ID.";

    if (!is_numeric($amount) || $amount < 0) {
        $errors[] = "Amount must be a positive number.";
    }
    return $errors;
}
