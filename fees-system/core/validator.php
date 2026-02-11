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
 * 3. Validates Student Data (Shared for Single and Bulk)
 */
function validateStudentData($data, $conn) {
    $errors = [];

    /* =====================================
       A. NULL / EMPTY CHECK
    ===================================== */
    $required_fields = [
        'reg'    => 'Registration No',
        'roll'   => 'Roll No',
        'fname'  => 'First Name',
        'course' => 'Course ID',
        'mobile' => 'Mobile No',
        'email'  => 'Email'
    ];

    foreach ($required_fields as $key => $label) {
        if (empty(trim($data[$key]))) {
            $errors[] = "$label is required and cannot be empty.";
        }
    }

    // Return early if basic fields are missing to avoid unnecessary DB queries
    if (!empty($errors)) return $errors;

    /* =====================================
       B. COURSE EXISTENCE CHECK
    ===================================== */
    $course_id = intval($data['course']);
    $course_check = $conn->query("SELECT COURSE_ID FROM COURSES WHERE COURSE_ID = $course_id LIMIT 1");
    
    if ($course_check->num_rows === 0) {
        $errors[] = "Course ID '$course_id' does not exist in the COURSES table.";
    }

    /* =====================================
       C. UNIQUENESS CHECK (Reg, Roll, Mobile, Email)
    ===================================== */
    $reg    = $conn->real_escape_string(trim($data['reg']));
    $roll   = $conn->real_escape_string(trim($data['roll']));
    $mobile = $conn->real_escape_string(trim($data['mobile']));
    $email  = $conn->real_escape_string(trim($data['email']));

    // We check all unique constraints in one query for better performance
    $sql = "SELECT REGISTRATION_NO, ROLL_NO, MOBILE, EMAIL FROM STUDENTS 
            WHERE REGISTRATION_NO = '$reg' 
            OR ROLL_NO = '$roll' 
            OR MOBILE = '$mobile' 
            OR EMAIL = '$email' 
            LIMIT 1";
            
    $check = $conn->query($sql);

    if ($check && $check->num_rows > 0) {
        $row = $check->fetch_assoc();
        
        if ($row['REGISTRATION_NO'] === $reg) $errors[] = "Registration No '$reg' is already registered.";
        if ($row['ROLL_NO'] === $roll) $errors[] = "Roll No '$roll' is already assigned.";
        if ($row['MOBILE'] === $mobile) $errors[] = "Mobile number '$mobile' is already in use.";
        if ($row['EMAIL'] === $email) $errors[] = "Email address '$email' is already in use.";
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