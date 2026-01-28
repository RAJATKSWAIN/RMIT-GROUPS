<?php
session_start();

// Set timezone
date_default_timezone_set('Asia/Kolkata');

// Database configuration
$dbConfig = [
    'host' => 'sql303.infinityfree.com',
    'user' => 'if0_39529641',
    'pass' => 'nIzoiCglOv',
    'name' => 'if0_39529641_online_exam_system'
];

function connectDB($config) {
    $mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['name']);
    if ($mysqli->connect_errno) {
        die("Database connection failed: " . $mysqli->connect_error);
    }
    $mysqli->set_charset("utf8");
    return $mysqli;
}

$mysqli = connectDB($dbConfig);

// Auth Class
class Auth {
    public function isLoggedIn() {
        return isset($_SESSION['userId'], $_SESSION['userType']);
    }
    public function getRole() {
        return $_SESSION['userType'] ?? null;
    }
    public function getUserId() {
        return $_SESSION['userId'] ?? null;
    }
    public function getUserName() {
        return $_SESSION['userName'] ?? null;
    }
}

$auth = new Auth($mysqli);

// Redirect if not logged in or not admin
if (!$auth->isLoggedIn() || $auth->getRole() !== 'admin') {
    header('Location: login.php');
    exit;
}

// Profile Management
$profileMessage = '';
$profileMessageType = '';
$passwordMessage = '';
$passwordMessageType = '';

// Get current admin details
$currentUserId = $auth->getUserId();
$stmt = $mysqli->prepare("SELECT name, email, created_at FROM admin WHERE id = ?");
$stmt->bind_param('i', $currentUserId);
$stmt->execute();
$adminData = $stmt->get_result()->fetch_assoc();
$stmt->close();

$currentName = $adminData['name'] ?? '';
$currentEmail = $adminData['email'] ?? '';
$memberSince = $adminData['created_at'] ? date('M d, Y', strtotime($adminData['created_at'])) : 'Unknown';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $newName = trim($_POST['full_name']);
    $newEmail = trim($_POST['email']);
    
    // Validate input
    if (empty($newName) || empty($newEmail)) {
        $profileMessage = 'Name and email are required.';
        $profileMessageType = 'danger';
    } elseif (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        $profileMessage = 'Please enter a valid email address.';
        $profileMessageType = 'danger';
    } else {
        // Check if email already exists for other admins
        $checkStmt = $mysqli->prepare("SELECT id FROM admin WHERE email = ? AND id != ?");
        $checkStmt->bind_param('si', $newEmail, $currentUserId);
        $checkStmt->execute();
        $existingAdmin = $checkStmt->get_result()->fetch_assoc();
        $checkStmt->close();
        
        if ($existingAdmin) {
            $profileMessage = 'Email address is already in use by another admin.';
            $profileMessageType = 'danger';
        } else {
            // Update admin profile
            $updateStmt = $mysqli->prepare("UPDATE admin SET name = ?, email = ? WHERE id = ?");
            $updateStmt->bind_param('ssi', $newName, $newEmail, $currentUserId);
            
            if ($updateStmt->execute()) {
                $profileMessage = 'Profile updated successfully!';
                $profileMessageType = 'success';
                
                // Update session data
                $_SESSION['userName'] = $newName;
                
                // Update current variables
                $currentName = $newName;
                $currentEmail = $newEmail;
            } else {
                $profileMessage = 'Failed to update profile. Please try again.';
                $profileMessageType = 'danger';
            }
            $updateStmt->close();
        }
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
    // Validate input
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $passwordMessage = 'All password fields are required.';
        $passwordMessageType = 'danger';
    } elseif ($newPassword !== $confirmPassword) {
        $passwordMessage = 'New passwords do not match.';
        $passwordMessageType = 'danger';
    } elseif (strlen($newPassword) < 5) {
        $passwordMessage = 'New password must be at least 5 characters long.';
        $passwordMessageType = 'danger';
    } else {
        // Verify current password
        $stmt = $mysqli->prepare("SELECT password FROM admin WHERE id = ?");
        $stmt->bind_param('i', $currentUserId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if ($result && password_verify($currentPassword, $result['password'])) {
            // Update password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            $updateStmt = $mysqli->prepare("UPDATE admin SET password = ? WHERE id = ?");
            $updateStmt->bind_param('si', $hashedPassword, $currentUserId);
            
            if ($updateStmt->execute()) {
                $passwordMessage = 'Password changed successfully!';
                $passwordMessageType = 'success';
            } else {
                $passwordMessage = 'Failed to change password. Please try again.';
                $passwordMessageType = 'danger';
            }
            $updateStmt->close();
        } else {
            $passwordMessage = 'Current password is incorrect.';
            $passwordMessageType = 'danger';
        }
    }
}

// Admin Manager Class
class AdminManager {
    private $mysqli;

    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }

    // Function to validate staff input data
    public function validateStaffInput($data) {
        $errors = [];
        
        // Required fields for staff
        $required = ['name', 'gender', 'contactNumber', 'department', 'staffId', 'email', 'password', 'confirmPassword'];
        foreach($required as $field) {
            if (empty($data[$field])) {
                $errors[] = ucfirst($field) . " is required.";
            }
        }
        
        // Validate email format
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }
        
        // Validate password match
        if ($data['password'] !== $data['confirmPassword']) {
            $errors[] = "Passwords do not match.";
        }
        
        // Validate password strength (minimum 5 characters)
        if (strlen($data['password']) < 5) {
            $errors[] = "Password must be at least 5 characters long.";
        }
        
        // Validate phone number (basic check)
        if (!preg_match('/^[0-9]{10,15}$/', $data['contactNumber'])) {
            $errors[] = "Contact number must be 10-15 digits.";
        }
        
        return $errors;
    }

    // Function to check if staff exists
    public function staffExists($email, $staffId = null) {
        if ($staffId) {
            $stmt = $this->mysqli->prepare("SELECT id FROM staff WHERE email = ? OR staffId = ?");
            $stmt->bind_param('ss', $email, $staffId);
        } else {
            $stmt = $this->mysqli->prepare("SELECT id FROM staff WHERE email = ?");
            $stmt->bind_param('s', $email);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    // Function to insert new staff
    public function insertStaff($data) {
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO staff (name, gender, contact, address, dob, department, staffId, email, password) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('sssssssss', 
            $data['name'], 
            $data['gender'], 
            $data['contactNumber'], 
            $data['address'], 
            $data['dob'], 
            $data['department'], 
            $data['staffId'], 
            $data['email'], 
            $hashedPassword
        );
        
        $success = $stmt->execute();
        if (!$success) {
            throw new Exception("Database error: " . $stmt->error);
        }
        $stmt->close();
        return $success;
    }

    // Get dashboard statistics
    public function getDashboardStats() {
        $stats = [];
        
        // Total exams by status
        $result = $this->mysqli->query("
            SELECT status, COUNT(*) as count 
            FROM exams 
            GROUP BY status
        ");
        while ($row = $result->fetch_assoc()) {
            $stats['exams'][$row['status']] = $row['count'];
        }
        
        // Total students
        $result = $this->mysqli->query("SELECT COUNT(*) as count FROM student");
        $stats['total_students'] = $result->fetch_assoc()['count'];
        
        // Total staff
        $result = $this->mysqli->query("SELECT COUNT(*) as count FROM staff");
        $stats['total_staff'] = $result->fetch_assoc()['count'];
        
        // Total exam attempts
        $result = $this->mysqli->query("SELECT COUNT(*) as count FROM exam_results");
        $stats['total_attempts'] = $result->fetch_assoc()['count'];
        
        // Recent activity (last 7 days)
        $result = $this->mysqli->query("
            SELECT COUNT(*) as count 
            FROM exam_results 
            WHERE submitted_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        ");
        $stats['recent_attempts'] = $result->fetch_assoc()['count'];
        
        return $stats;
    }

    // Get pending exams with creator details
    public function getPendingExams() {
        $stmt = $this->mysqli->prepare("
            SELECT e.id, e.title, e.exam_datetime, e.duration, e.created_at,
                   s.name as creator_name, s.email as creator_email,
                   COUNT(q.id) as question_count
            FROM exams e
            JOIN staff s ON e.created_by = s.id
            LEFT JOIN questions q ON e.id = q.exam_id
            WHERE e.status = 'pending'
            GROUP BY e.id
            ORDER BY e.created_at DESC
        ");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Get exam details with questions
    public function getExamDetails($exam_id) {
        $stmt = $this->mysqli->prepare("
            SELECT e.*, s.name as creator_name, s.email as creator_email
            FROM exams e
            JOIN staff s ON e.created_by = s.id
            WHERE e.id = ?
        ");
        $stmt->bind_param('i', $exam_id);
        $stmt->execute();
        $exam = $stmt->get_result()->fetch_assoc();
        
        if ($exam) {
            // Get questions
            $stmt = $this->mysqli->prepare("
                SELECT * FROM questions 
                WHERE exam_id = ? 
                ORDER BY id ASC
            ");
            $stmt->bind_param('i', $exam_id);
            $stmt->execute();
            $exam['questions'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        
        return $exam;
    }

    // Approve exam
    public function approveExam($exam_id) {
        $stmt = $this->mysqli->prepare("
            UPDATE exams 
            SET status = 'approved' 
            WHERE id = ? AND status = 'pending'
        ");
        $stmt->bind_param('i', $exam_id);
        $result = $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    // Reject exam
    public function rejectExam($exam_id, $reason = '') {
        $stmt = $this->mysqli->prepare("
            UPDATE exams 
            SET status = 'rejected', rejected_at = NOW(), rejection_reason = ? 
            WHERE id = ? AND status = 'pending'
        ");
        $stmt->bind_param('si', $reason, $exam_id);
        $result = $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    // Get all exams with status
    public function getAllExams($status = null) {
        $sql = "
            SELECT e.id, e.title, e.exam_datetime, e.duration, e.status, e.created_at,
                   s.name as creator_name,
                   COUNT(q.id) as question_count,
                   COUNT(DISTINCT er.student_id) as attempts_count
            FROM exams e
            JOIN staff s ON e.created_by = s.id
            LEFT JOIN questions q ON e.id = q.exam_id
            LEFT JOIN exam_results er ON e.id = er.exam_id
        ";
        
        if ($status) {
            $sql .= " WHERE e.status = ?";
        }
        
        $sql .= " GROUP BY e.id ORDER BY e.created_at DESC";
        
        if ($status) {
            $stmt = $this->mysqli->prepare($sql);
            $stmt->bind_param('s', $status);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } else {
            return $this->mysqli->query($sql)->fetch_all(MYSQLI_ASSOC);
        }
    }

    // Get student statistics
    public function getStudentStats($limit = null) {
        $sql = "
            SELECT s.id, s.name, s.email, s.created_at,
                   COUNT(er.id) as total_exams,
                   IFNULL(AVG((er.score/er.total_marks)*100), 0) as avg_percentage,
                   IFNULL(MAX((er.score/er.total_marks)*100), 0) as max_percentage,
                   IFNULL(MIN((er.score/er.total_marks)*100), 0) as min_percentage,
                   MAX(er.submitted_at) as last_exam_date
            FROM student s
            LEFT JOIN exam_results er ON s.id = er.student_id
            GROUP BY s.id
            ORDER BY total_exams DESC, avg_percentage DESC
        ";
        
        if ($limit) {
            $sql .= " LIMIT ?";
            $stmt = $this->mysqli->prepare($sql);
            $stmt->bind_param('i', $limit);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } else {
            return $this->mysqli->query($sql)->fetch_all(MYSQLI_ASSOC);
        }
    }

    // Get staff statistics
    public function getStaffStats($limit = null) {
        $sql = "
            SELECT s.id, s.name, s.email, s.staffId, s.department, s.created_at,
                   COUNT(e.id) as total_exams_created,
                   SUM(CASE WHEN e.status = 'approved' THEN 1 ELSE 0 END) as approved_exams,
                   SUM(CASE WHEN e.status = 'pending' THEN 1 ELSE 0 END) as pending_exams,
                   SUM(CASE WHEN e.status = 'rejected' THEN 1 ELSE 0 END) as rejected_exams,
                   MAX(e.created_at) as last_exam_created
            FROM staff s
            LEFT JOIN exams e ON s.id = e.created_by
            GROUP BY s.id
            ORDER BY total_exams_created DESC
        ";
        
        if ($limit) {
            $sql .= " LIMIT ?";
            $stmt = $this->mysqli->prepare($sql);
            $stmt->bind_param('i', $limit);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } else {
            return $this->mysqli->query($sql)->fetch_all(MYSQLI_ASSOC);
        }
    }

    // Get detailed student performance
    public function getStudentDetails($student_id) {
        $stmt = $this->mysqli->prepare("
            SELECT s.*, 
                   COUNT(er.id) as total_exams,
                   IFNULL(AVG((er.score/er.total_marks)*100), 0) as avg_percentage,
                   IFNULL(MAX((er.score/er.total_marks)*100), 0) as max_percentage,
                   IFNULL(MIN((er.score/er.total_marks)*100), 0) as min_percentage
            FROM student s
            LEFT JOIN exam_results er ON s.id = er.student_id
            WHERE s.id = ?
            GROUP BY s.id
        ");
        $stmt->bind_param('i', $student_id);
        $stmt->execute();
        $student = $stmt->get_result()->fetch_assoc();
        
        if ($student) {
            // Get exam history
            $stmt = $this->mysqli->prepare("
                SELECT er.score, er.total_marks, er.submitted_at, e.title
                FROM exam_results er
                JOIN exams e ON er.exam_id = e.id
                WHERE er.student_id = ?
                ORDER BY er.submitted_at DESC
            ");
            $stmt->bind_param('i', $student_id);
            $stmt->execute();
            $student['exam_history'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        
        return $student;
    }

    // Search functionality
    public function searchStudents($query) {
        $stmt = $this->mysqli->prepare("
            SELECT id, name, email 
            FROM student 
            WHERE name LIKE ? OR email LIKE ? 
            LIMIT 20
        ");
        $searchTerm = "%{$query}%";
        $stmt->bind_param('ss', $searchTerm, $searchTerm);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function searchStaff($query) {
        $stmt = $this->mysqli->prepare("
            SELECT id, name, email 
            FROM staff 
            WHERE name LIKE ? OR email LIKE ? 
            LIMIT 20
        ");
        $searchTerm = "%{$query}%";
        $stmt->bind_param('ss', $searchTerm, $searchTerm);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Remove student with all related data
    public function removeStudent($student_id) {
        $this->mysqli->begin_transaction();
        
        try {
            // Delete exam results first (foreign key constraint)
            $stmt = $this->mysqli->prepare("DELETE FROM exam_results WHERE student_id = ?");
            $stmt->bind_param('i', $student_id);
            $stmt->execute();
            
            // Delete student answers
            $stmt = $this->mysqli->prepare("DELETE FROM student_answers WHERE student_id = ?");
            $stmt->bind_param('i', $student_id);
            $stmt->execute();
            
            // Delete the student record
            $stmt = $this->mysqli->prepare("DELETE FROM student WHERE id = ?");
            $stmt->bind_param('i', $student_id);
            $stmt->execute();
            
            $affected_rows = $stmt->affected_rows;
            $this->mysqli->commit();
            
            return $affected_rows > 0;
        } catch (Exception $e) {
            $this->mysqli->rollback();
            return false;
        }
    }

    // Remove staff with all related data
    public function removeStaff($staff_id) {
        $this->mysqli->begin_transaction();
        
        try {
            // First, get all exams created by this staff
            $stmt = $this->mysqli->prepare("SELECT id FROM exams WHERE created_by = ?");
            $stmt->bind_param('i', $staff_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $exam_ids = [];
            
            while ($row = $result->fetch_assoc()) {
                $exam_ids[] = $row['id'];
            }
            
            // If there are exams, delete related data
            if (!empty($exam_ids)) {
                $exam_ids_str = implode(',', array_map('intval', $exam_ids));
                
                // Delete student answers for these exams
                $this->mysqli->query("DELETE FROM student_answers WHERE exam_id IN ($exam_ids_str)");
                
                // Delete exam results for these exams
                $this->mysqli->query("DELETE FROM exam_results WHERE exam_id IN ($exam_ids_str)");
                
                // Delete questions for these exams
                $this->mysqli->query("DELETE FROM questions WHERE exam_id IN ($exam_ids_str)");
                
                // Delete the exams
                $this->mysqli->query("DELETE FROM exams WHERE id IN ($exam_ids_str)");
            }
            
            // Finally, delete the staff member
            $stmt = $this->mysqli->prepare("DELETE FROM staff WHERE id = ?");
            $stmt->bind_param('i', $staff_id);
            $stmt->execute();
            
            $affected_rows = $stmt->affected_rows;
            $this->mysqli->commit();
            
            return $affected_rows > 0;
        } catch (Exception $e) {
            $this->mysqli->rollback();
            return false;
        }
    }

    // Get student name for confirmation
    public function getStudentName($student_id) {
        $stmt = $this->mysqli->prepare("SELECT name FROM student WHERE id = ?");
        $stmt->bind_param('i', $student_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result ? $result['name'] : null;
    }

    // Get staff name for confirmation
    public function getStaffName($staff_id) {
        $stmt = $this->mysqli->prepare("SELECT name FROM staff WHERE id = ?");
        $stmt->bind_param('i', $staff_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result ? $result['name'] : null;
    }
}

$adminManager = new AdminManager($mysqli);

// Handle form submissions
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add_staff':
            try {
                // Collect and sanitize input
                $formData = [
                    'name' => trim($_POST['name'] ?? ''),
                    'gender' => trim($_POST['gender'] ?? ''),
                    'contactNumber' => trim($_POST['contactNumber'] ?? ''),
                    'address' => trim($_POST['address'] ?? ''),
                    'dob' => !empty($_POST['dob']) ? trim($_POST['dob']) : null,
                    'department' => trim($_POST['department'] ?? ''),
                    'staffId' => trim($_POST['staffId'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'password' => $_POST['password'] ?? '',
                    'confirmPassword' => $_POST['confirmPassword'] ?? ''
                ];
                
                // Validate input
                $errors = $adminManager->validateStaffInput($formData);
                if (!empty($errors)) {
                    $error = implode(", ", $errors);
                    break;
                }
                
                // Check if staff already exists
                if ($adminManager->staffExists($formData['email'], $formData['staffId'])) {
                    $error = "Staff member already exists with this email or staff ID!";
                    break;
                }
                
                // Insert new staff
                if ($adminManager->insertStaff($formData)) {
                    $success = "Staff member added successfully!";
                } else {
                    $error = "Failed to add staff member.";
                }
                
            } catch (Exception $e) {
                $error = "Error: " . $e->getMessage();
            }
            break;
            
        case 'approve_exam':
            $exam_id = intval($_POST['exam_id'] ?? 0);
            if ($exam_id > 0 && $adminManager->approveExam($exam_id)) {
                $success = "Exam approved successfully!";
            } else {
                $error = "Failed to approve exam.";
            }
            break;
            
        case 'reject_exam':
            $exam_id = intval($_POST['exam_id'] ?? 0);
            $reason = trim($_POST['reason'] ?? '');
            if ($exam_id > 0 && $adminManager->rejectExam($exam_id, $reason)) {
                $success = "Exam rejected successfully!";
            } else {
                $error = "Failed to reject exam.";
            }
            break;
            
        default:
            // Handle profile updates and password changes that don't use 'action' field
            if (isset($_POST['update_profile'])) {
                // Profile update logic is already handled above
            } elseif (isset($_POST['change_password'])) {
                // Password change logic is already handled above
            } else {
                // Unknown POST request
                $error = "Invalid request.";
            }
            break;
    }
}

// Get current page
$page = $_GET['page'] ?? 'dashboard';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Online Examination System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --success-color: #48bb78;
            --danger-color: #f56565;
            --warning-color: #ed8936;
            --info-color: #4299e1;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar {
            background: white;
            min-height: 100vh;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            position: fixed;
            width: 250px;
            z-index: 1000;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        .nav-link {
            color: #333;
            padding: 12px 20px;
            border-radius: 8px;
            margin: 5px 10px;
            transition: all 0.3s;
        }

        .nav-link:hover, .nav-link.active {
            background: var(--primary-color);
            color: white;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .table {
            border-radius: 10px;
            overflow: hidden;
        }

        .badge {
            padding: 8px 12px;
            border-radius: 20px;
        }

        .btn {
            border-radius: 8px;
            padding: 8px 20px;
            font-weight: 500;
        }

        .question-card {
            background: #f8f9fa;
            border-left: 4px solid var(--primary-color);
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
        }

        .option-item {
            background: white;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            border: 1px solid #e9ecef;
        }

        .correct-answer {
            background: #d4edda;
            border-color: #c3e6cb;
        }

        .password-toggle {
            position: relative;
        }

        .password-toggle .toggle-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            user-select: none;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="p-3 border-bottom">
            <h5 class="mb-0">
                <i class="fas fa-user-shield text-primary me-2"></i>
                Admin Panel
            </h5>
            <small class="text-muted">Welcome, <?php echo htmlspecialchars($auth->getUserName()); ?></small>
        </div>
        
        <nav class="nav flex-column p-2">
            <a href="?page=dashboard" class="nav-link <?php echo $page === 'dashboard' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
            </a>
            <a href="?page=add_staff" class="nav-link <?php echo $page === 'add_staff' ? 'active' : ''; ?>">
                <i class="fas fa-user-plus me-2"></i> Add Staff
            </a>
            <a href="?page=pending_exams" class="nav-link <?php echo $page === 'pending_exams' ? 'active' : ''; ?>">
                <i class="fas fa-clock me-2"></i> Pending Exams
                <?php 
                $pendingCount = count($adminManager->getPendingExams());
                if ($pendingCount > 0): ?>
                    <span class="badge bg-danger ms-2"><?php echo $pendingCount; ?></span>
                <?php endif; ?>
            </a>
            <a href="?page=all_exams" class="nav-link <?php echo $page === 'all_exams' ? 'active' : ''; ?>">
                <i class="fas fa-clipboard-list me-2"></i> All Exams
            </a>
            <a href="?page=students" class="nav-link <?php echo $page === 'students' ? 'active' : ''; ?>">
                <i class="fas fa-user-graduate me-2"></i> Students
            </a>
            <a href="?page=staff" class="nav-link <?php echo $page === 'staff' ? 'active' : ''; ?>">
                <i class="fas fa-chalkboard-teacher me-2"></i> Staff
            </a>
            <a href="?page=profile" class="nav-link <?php echo $page === 'profile' ? 'active' : ''; ?>">
                <i class="fas fa-user-edit me-2"></i> Profile
            </a>
            <hr>
            <a href="logout.php" class="nav-link text-danger">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Mobile menu button -->
        <button class="btn btn-primary d-md-none mb-3" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Alerts -->
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php
        // Page content based on current page
        switch ($page) {
            case 'add_staff':
                ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <h2 class="text-white mb-4">
                            <i class="fas fa-user-plus me-2"></i>Add New Staff Member
                        </h2>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-chalkboard-teacher me-2"></i>Staff Registration Form
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" onsubmit="return validateStaffForm(event)">
                            <input type="hidden" name="action" value="add_staff">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Full Name *</label>
                                        <input type="text" id="name" name="name" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="staffId" class="form-label">Staff ID *</label>
                                        <input type="text" id="staffId" name="staffId" class="form-control" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Gender *</label><br>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="gender" id="male" value="Male" required>
                                            <label class="form-check-label" for="male">Male</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="gender" id="female" value="Female">
                                            <label class="form-check-label" for="female">Female</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="gender" id="other" value="Other">
                                            <label class="form-check-label" for="other">Other</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="contactNumber" class="form-label">Contact Number *</label>
                                        <input type="tel" id="contactNumber" name="contactNumber" class="form-control" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="department" class="form-label">Department *</label>
                                        <select class="form-select" id="department" name="department" required>
                                            <option selected disabled>Select Department</option>
                                            <option value="BCA">Bachelor of Computer Applications</option>
                                            <option value="MCA">Master of Computer Applications</option>
                                            <option value="CS">Computer Science</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="dob" class="form-label">Date of Birth</label>
                                        <input type="date" class="form-control" id="dob" name="dob">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea id="address" name="address" class="form-control" rows="2"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" id="email" name="email" class="form-control" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3 password-toggle">
                                        <label for="password" class="form-label">Password *</label>
                                        <div class="position-relative">
                                            <input type="password" id="password" name="password" class="form-control" required>
                                            <span class="toggle-icon" onclick="togglePassword('password')">👁️</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3 password-toggle">
                                        <label for="confirmPassword" class="form-label">Confirm Password *</label>
                                        <div class="position-relative">
                                            <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" required>
                                            <span class="toggle-icon" onclick="togglePassword('confirmPassword')">👁️</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="passwordError" class="text-danger mb-3" style="display:none;">Passwords do not match.</div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-user-plus me-1"></i>Add Staff Member
                                </button>
                                <button type="reset" class="btn btn-secondary">
                                    <i class="fas fa-undo me-1"></i>Reset Form
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php
                break;

            case 'dashboard':
                $stats = $adminManager->getDashboardStats();
                $recentStudents = $adminManager->getStudentStats(5);
                $recentStaff = $adminManager->getStaffStats(5);
                ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <h2 class="text-white mb-4">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard Overview
                        </h2>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="stat-card">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon" style="background: var(--info-color);">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="ms-3">
                                    <h3 class="mb-0"><?php echo $stats['total_students']; ?></h3>
                                    <p class="text-muted mb-0">Total Students</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <div class="stat-card">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon" style="background: var(--success-color);">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                </div>
                                <div class="ms-3">
                                    <h3 class="mb-0"><?php echo $stats['total_staff']; ?></h3>
                                    <p class="text-muted mb-0">Total Staff</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <div class="stat-card">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon" style="background: var(--warning-color);">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="ms-3">
                                    <h3 class="mb-0"><?php echo $stats['exams']['pending'] ?? 0; ?></h3>
                                    <p class="text-muted mb-0">Pending Exams</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <div class="stat-card">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon" style="background: var(--primary-color);">
                                    <i class="fas fa-clipboard-check"></i>
                                </div>
                                <div class="ms-3">
                                    <h3 class="mb-0"><?php echo $stats['total_attempts']; ?></h3>
                                    <p class="text-muted mb-0">Total Attempts</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Exam Status Overview -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-chart-pie me-2"></i>Exam Status Overview
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-4">
                                        <h4 class="text-success"><?php echo $stats['exams']['approved'] ?? 0; ?></h4>
                                        <small class="text-muted">Approved</small>
                                    </div>
                                    <div class="col-4">
                                        <h4 class="text-warning"><?php echo $stats['exams']['pending'] ?? 0; ?></h4>
                                        <small class="text-muted">Pending</small>
                                    </div>
                                    <div class="col-4">
                                        <h4 class="text-danger"><?php echo $stats['exams']['rejected'] ?? 0; ?></h4>
                                        <small class="text-muted">Rejected</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-chart-line me-2"></i>Recent Activity
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="text-primary"><?php echo $stats['recent_attempts']; ?></h4>
                                        <small class="text-muted">Exam attempts in last 7 days</small>
                                    </div>
                                    <i class="fas fa-chart-line fa-2x text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Performing Students -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-trophy me-2"></i>Top Performing Students
                                </h5>
                                <a href="?page=students" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            <div class="card-body">
                                <?php foreach ($recentStudents as $student): ?>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <strong><?php echo htmlspecialchars($student['name']); ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo $student['total_exams']; ?> exams taken</small>
                                        </div>
                                        <span class="badge bg-success"><?php echo round($student['avg_percentage'], 1); ?>%</span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-users me-2"></i>Active Staff Members
                                </h5>
                                <a href="?page=staff" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            <div class="card-body">
                                <?php foreach ($recentStaff as $staff): ?>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <strong><?php echo htmlspecialchars($staff['name']); ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo $staff['total_exams_created']; ?> exams created</small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-success"><?php echo $staff['approved_exams']; ?></span>
                                            <span class="badge bg-warning"><?php echo $staff['pending_exams']; ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                break;

            case 'pending_exams':
                $pendingExams = $adminManager->getPendingExams();
                ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <h2 class="text-white mb-4">
                            <i class="fas fa-clock me-2"></i>Pending Exam Approvals
                        </h2>
                    </div>
                </div>

                <?php if (empty($pendingExams)): ?>
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h4>No Pending Exams</h4>
                            <p class="text-muted">All exams have been reviewed!</p>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($pendingExams as $exam): ?>
                        <div class="card mb-4">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h5 class="mb-1"><?php echo htmlspecialchars($exam['title']); ?></h5>
                                        <small class="text-muted">
                                            Created by: <?php echo htmlspecialchars($exam['creator_name']); ?> 
                                            (<?php echo htmlspecialchars($exam['creator_email']); ?>)
                                        </small>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <span class="badge bg-warning">Pending Review</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <strong>Exam Date:</strong><br>
                                        <?php echo date('M d, Y g:i A', strtotime($exam['exam_datetime'])); ?>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Duration:</strong><br>
                                        <?php echo $exam['duration']; ?> minutes
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Questions:</strong><br>
                                        <?php echo $exam['question_count']; ?> questions
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Created:</strong><br>
                                        <?php echo date('M d, Y', strtotime($exam['created_at'])); ?>
                                    </div>
                                </div>

                                <!-- View Questions Button -->
                                <button class="btn btn-info btn-sm mb-3" onclick="toggleQuestions(<?php echo $exam['id']; ?>)">
                                    <i class="fas fa-eye me-1"></i>View Questions
                                </button>

                                <!-- Questions Container (Initially Hidden) -->
                                <div id="questions-<?php echo $exam['id']; ?>" class="questions-container" style="display: none;">
                                    <?php 
                                    $examDetails = $adminManager->getExamDetails($exam['id']);
                                    if ($examDetails && !empty($examDetails['questions'])): 
                                    ?>
                                        <h6 class="mb-3">Exam Questions:</h6>
                                        <?php foreach ($examDetails['questions'] as $index => $question): ?>
                                            <div class="question-card">
                                                <h6>Question <?php echo $index + 1; ?> (<?php echo $question['marks']; ?> marks)</h6>
                                                <p><?php echo htmlspecialchars($question['question_text']); ?></p>
                                                
                                                <?php if ($question['question_type'] === 'mcq'): ?>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="option-item <?php echo $question['correct_option'] == 0 ? 'correct-answer' : ''; ?>">
                                                                <strong>A.</strong> <?php echo htmlspecialchars($question['option_a']); ?>
                                                                <?php if ($question['correct_option'] == 0): ?>
                                                                    <i class="fas fa-check text-success ms-2"></i>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="option-item <?php echo $question['correct_option'] == 1 ? 'correct-answer' : ''; ?>">
                                                                <strong>B.</strong> <?php echo htmlspecialchars($question['option_b']); ?>
                                                                <?php if ($question['correct_option'] == 1): ?>
                                                                    <i class="fas fa-check text-success ms-2"></i>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="option-item <?php echo $question['correct_option'] == 2 ? 'correct-answer' : ''; ?>">
                                                                <strong>C.</strong> <?php echo htmlspecialchars($question['option_c']); ?>
                                                                <?php if ($question['correct_option'] == 2): ?>
                                                                    <i class="fas fa-check text-success ms-2"></i>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="option-item <?php echo $question['correct_option'] == 3 ? 'correct-answer' : ''; ?>">
                                                                <strong>D.</strong> <?php echo htmlspecialchars($question['option_d']); ?>
                                                                <?php if ($question['correct_option'] == 3): ?>
                                                                    <i class="fas fa-check text-success ms-2"></i>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <p class="text-muted"><em>Subjective question - requires manual evaluation</em></p>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-flex gap-2 mt-3">
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="approve_exam">
                                        <input type="hidden" name="exam_id" value="<?php echo $exam['id']; ?>">
                                        <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to approve this exam?')">
                                            <i class="fas fa-check me-1"></i>Approve
                                        </button>
                                    </form>
                                    
                                    <button class="btn btn-danger" onclick="showRejectModal(<?php echo $exam['id']; ?>)">
                                        <i class="fas fa-times me-1"></i>Reject
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <?php
                break;

            case 'all_exams':
                $allExams = $adminManager->getAllExams();
                ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <h2 class="text-white mb-4">
                            <i class="fas fa-clipboard-list me-2"></i>All Exams
                        </h2>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Exam Title</th>
                                        <th>Creator</th>
                                        <th>Date & Time</th>
                                        <th>Duration</th>
                                        <th>Questions</th>
                                        <th>Attempts</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($allExams as $exam): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($exam['title']); ?></strong>
                                            </td>
                                            <td><?php echo htmlspecialchars($exam['creator_name']); ?></td>
                                            <td><?php echo date('M d, Y g:i A', strtotime($exam['exam_datetime'])); ?></td>
                                            <td><?php echo $exam['duration']; ?> min</td>
                                            <td><?php echo $exam['question_count']; ?></td>
                                            <td><?php echo $exam['attempts_count']; ?></td>
                                            <td>
                                                <?php
                                                $statusClass = [
                                                    'approved' => 'bg-success',
                                                    'pending' => 'bg-warning',
                                                    'rejected' => 'bg-danger'
                                                ];
                                                ?>
                                                <span class="badge <?php echo $statusClass[$exam['status']]; ?>">
                                                    <?php echo ucfirst($exam['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($exam['created_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php
                break;

            case 'students':
                $students = $adminManager->getStudentStats();
                $selectedStudent = null;
                
                if (isset($_GET['student_id'])) {
                    $selectedStudent = $adminManager->getStudentDetails(intval($_GET['student_id']));
                }
                ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <h2 class="text-white mb-4">
                            <i class="fas fa-user-graduate me-2"></i>Student Management
                        </h2>
                    </div>
                </div>

                <?php if ($selectedStudent): ?>
                    <!-- Student Details Modal-like View -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Student Details: <?php echo htmlspecialchars($selectedStudent['name']); ?></h5>
                            <a href="?page=students" class="btn btn-sm btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Back to List
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6>Personal Information</h6>
                                    <p><strong>Name:</strong> <?php echo htmlspecialchars($selectedStudent['name']); ?></p>
                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($selectedStudent['email']); ?></p>
                                    <p><strong>Joined:</strong> <?php echo date('M d, Y', strtotime($selectedStudent['created_at'])); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Performance Statistics</h6>
                                    <p><strong>Total Exams:</strong> <?php echo $selectedStudent['total_exams']; ?></p>
                                    <p><strong>Average Score:</strong> <?php echo round($selectedStudent['avg_percentage'], 2); ?>%</p>
                                    <p><strong>Best Score:</strong> <?php echo round($selectedStudent['max_percentage'], 2); ?>%</p>
                                </div>
                            </div>

                            <h6>Exam History</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Exam</th>
                                            <th>Score</th>
                                            <th>Percentage</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($selectedStudent['exam_history'] as $exam): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($exam['title']); ?></td>
                                                <td><?php echo $exam['score']; ?>/<?php echo $exam['total_marks']; ?></td>
                                                <td>
                                                    <?php 
                                                    $percentage = $exam['total_marks'] > 0 ? ($exam['score'] / $exam['total_marks']) * 100 : 0;
                                                    $badgeClass = $percentage >= 80 ? 'bg-success' : ($percentage >= 60 ? 'bg-warning' : 'bg-danger');
                                                    ?>
                                                    <span class="badge <?php echo $badgeClass; ?>">
                                                        <?php echo round($percentage, 1); ?>%
                                                    </span>
                                                </td>
                                                <td><?php echo date('M d, Y', strtotime($exam['submitted_at'])); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Students List -->
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h5 class="mb-0">All Students</h5>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Search students..." id="studentSearch">
                                        <button class="btn btn-outline-secondary" type="button">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Total Exams</th>
                                            <th>Average Score</th>
                                            <th>Best Score</th>
                                            <th>Last Exam</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($students as $student): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($student['name']); ?></strong>
                                                </td>
                                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                                                <td><?php echo $student['total_exams']; ?></td>
                                                <td>
                                                    <?php 
                                                    $avg = round($student['avg_percentage'], 1);
                                                    $badgeClass = $avg >= 80 ? 'bg-success' : ($avg >= 60 ? 'bg-warning' : 'bg-danger');
                                                    ?>
                                                    <span class="badge <?php echo $badgeClass; ?>"><?php echo $avg; ?>%</span>
                                                </td>
                                                <td><?php echo round($student['max_percentage'], 1); ?>%</td>
                                                <td>
                                                    <?php echo $student['last_exam_date'] ? date('M d, Y', strtotime($student['last_exam_date'])) : 'Never'; ?>
                                                </td>
                                                <td>
                                                    <a href="?page=students&student_id=<?php echo $student['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye me-1"></i>View Details
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php
                break;

            case 'staff':
                $staffMembers = $adminManager->getStaffStats();
                ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <h2 class="text-white mb-4">
                            <i class="fas fa-chalkboard-teacher me-2"></i>Staff Management
                        </h2>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5 class="mb-0">All Staff Members</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Search staff..." id="staffSearch">
                                    <button class="btn btn-outline-secondary" type="button">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Staff ID</th>
                                        <th>Department</th>
                                        <th>Total Exams</th>
                                        <th>Approved</th>
                                        <th>Pending</th>
                                        <th>Rejected</th>
                                        <th>Last Activity</th>
                                        <th>Joined</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($staffMembers as $staff): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($staff['name']); ?></strong>
                                            </td>
                                            <td><?php echo htmlspecialchars($staff['email']); ?></td>
                                            <td><?php echo htmlspecialchars($staff['staffId']); ?></td>
                                            <td><?php echo htmlspecialchars($staff['department']); ?></td>
                                            <td><?php echo $staff['total_exams_created']; ?></td>
                                            <td>
                                                <span class="badge bg-success"><?php echo $staff['approved_exams']; ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-warning"><?php echo $staff['pending_exams']; ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-danger"><?php echo $staff['rejected_exams']; ?></span>
                                            </td>
                                            <td>
                                                <?php echo $staff['last_exam_created'] ? date('M d, Y', strtotime($staff['last_exam_created'])) : 'Never'; ?>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($staff['created_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php
                break;

            case 'profile':
                ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <h2 class="text-white mb-4">
                            <i class="fas fa-user-edit me-2"></i>Admin Profile
                        </h2>
                    </div>
                </div>
                
                <?php if ($profileMessage): ?>
                    <div class="alert alert-<?php echo $profileMessageType; ?> alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($profileMessage); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($passwordMessage): ?>
                    <div class="alert alert-<?php echo $passwordMessageType; ?> alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($passwordMessage); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <div class="row">
                    <!-- Profile Form -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-user-edit me-2"></i>Edit Profile Information
                                </h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="" id="profileForm">
                                    <input type="hidden" name="update_profile" value="1">
                                    
                                    <div class="mb-3">
                                        <label for="full_name" class="form-label">
                                            <i class="fas fa-user me-1"></i>Full Name <span class="text-danger">*</span>
                                        </label>
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            id="full_name" 
                                            name="full_name"
                                            value="<?php echo htmlspecialchars($currentName); ?>"
                                            required
                                            maxlength="100"
                                        >
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="email" class="form-label">
                                            <i class="fas fa-envelope me-1"></i>Email Address <span class="text-danger">*</span>
                                        </label>
                                        <input 
                                            type="email" 
                                            class="form-control" 
                                            id="email" 
                                            name="email"
                                            value="<?php echo htmlspecialchars($currentEmail); ?>"
                                            required
                                            maxlength="100"
                                        >
                                    </div>
                                    
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i>Update Profile
                                        </button>
                                        <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                            <i class="fas fa-undo me-1"></i>Reset
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Profile Information Card -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Profile Summary
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <div class="avatar-placeholder bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 24px;">
                                        <?php 
                                        $initials = '';
                                        $nameParts = explode(' ', $currentName);
                                        foreach ($nameParts as $part) {
                                            $initials .= strtoupper(substr($part, 0, 1));
                                            if (strlen($initials) >= 2) break;
                                        }
                                        echo htmlspecialchars($initials);
                                        ?>
                                    </div>
                                    <h6 class="mt-2"><?php echo htmlspecialchars($currentName); ?></h6>
                                    <small class="text-muted">Admin ID: <?php echo $currentUserId; ?></small>
                                </div>
                                
                                <hr>
                                
                                <div class="mb-2">
                                    <small class="text-muted">Email:</small><br>
                                    <span><?php echo htmlspecialchars($currentEmail); ?></span>
                                </div>
                                
                                <div class="mb-2">
                                    <small class="text-muted">Role:</small><br>
                                    <span class="badge bg-danger">System Administrator</span>
                                </div>
                                
                                <div class="mb-2">
                                    <small class="text-muted">Member Since:</small><br>
                                    <span><?php echo $memberSince; ?></span>
                                </div>
                                
                                <hr>
                                
                                <div class="text-center">
                                    <small class="text-muted">
                                        <i class="fas fa-shield-alt me-1"></i>
                                        Your information is secure and private
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Change Password Card -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-lock me-2"></i>Security
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small">Keep your admin account secure</p>
                                <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                    <i class="fas fa-key me-1"></i>Change Password
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                break;

            default:
                echo '<div class="alert alert-danger">Page not found.</div>';
                break;
        }
        ?>
    </div>

    <!-- Reject Exam Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reject Exam</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="reject_exam">
                        <input type="hidden" name="exam_id" id="rejectExamId">
                        <div class="mb-3">
                            <label for="reason" class="form-label">Reason for Rejection (Optional)</label>
                            <textarea class="form-control" name="reason" id="reason" rows="3" placeholder="Provide feedback to the staff member..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject Exam</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-lock me-2"></i>Change Password
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="change_password" value="1">
                    
                        <div class="mb-3 password-toggle">
                            <label for="current_password" class="form-label">Current Password</label>
                            <div class="position-relative">
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                                <span class="toggle-icon" onclick="togglePasswordModal('current_password')">👁️</span>
                            </div>
                        </div>
                        
                        <div class="mb-3 password-toggle">
                            <label for="new_password" class="form-label">New Password</label>
                            <div class="position-relative">
                                <input type="password" class="form-control" id="new_password" name="new_password" required minlength="5">
                                <span class="toggle-icon" onclick="togglePasswordModal('new_password')">👁️</span>
                            </div>
                            <div class="form-text">Password must be at least 5 characters long.</div>
                        </div>
                        
                        <div class="mb-3 password-toggle">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <div class="position-relative">
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                <span class="toggle-icon" onclick="togglePasswordModal('confirm_password')">👁️</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-key me-1"></i>Change Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('show');
        }

        function toggleQuestions(examId) {
            const questionsDiv = document.getElementById('questions-' + examId);
            if (questionsDiv.style.display === 'none') {
                questionsDiv.style.display = 'block';
            } else {
                questionsDiv.style.display = 'none';
            }
        }

        function showRejectModal(examId) {
            document.getElementById('rejectExamId').value = examId;
            new bootstrap.Modal(document.getElementById('rejectModal')).show();
        }

        function togglePassword(id) {
            const input = document.getElementById(id);
            const icon = input.nextElementSibling.querySelector('.toggle-icon');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.textContent = '🙈';
            } else {
                input.type = 'password';
                icon.textContent = '👁️';
            }
        }

        function validateStaffForm(event) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const errorDiv = document.getElementById('passwordError');

            if (password !== confirmPassword) {
                errorDiv.style.display = 'block';
                event.preventDefault();
                return false;
            } else {
                errorDiv.style.display = 'none';
            }

            if (password.length < 5) {
                alert("Password must be at least 5 characters long.");
                event.preventDefault();
                return false;
            }

            return true;
        }

        // Search functionality
        document.getElementById('studentSearch')?.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        document.getElementById('staffSearch')?.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                if (alert.querySelector('.btn-close')) {
                    alert.querySelector('.btn-close').click();
                }
            });
        }, 5000);

        // Add some interactive feedback for form inputs
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.style.transform = 'scale(1.02)';
                    this.style.transition = 'transform 0.2s ease';
                });
                
                input.addEventListener('blur', function() {
                    this.style.transform = 'scale(1)';
                });
            });
        });

        function togglePasswordModal(id) {
            const input = document.getElementById(id);
            const icon = input.nextElementSibling.querySelector('.toggle-icon');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.textContent = '🙈';
            } else {
                input.type = 'password';
                icon.textContent = '👁️';
            }
        }

        function resetForm() {
            document.getElementById('profileForm').reset();
        }
    </script>
</body>
</html>
