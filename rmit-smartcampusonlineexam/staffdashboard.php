<?php
session_start();

// Set timezone
date_default_timezone_set('Asia/Kolkata');

// -----------------------------------------------------------------------------
// 1) Database configuration (mysqli version)
// -----------------------------------------------------------------------------
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

// -----------------------------------------------------------------------------
// 2) Auth Class (mysqli-based). Relies on $_SESSION['userId'] and $_SESSION['userType'].
// -----------------------------------------------------------------------------
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
    public function logout() {
        session_destroy();
        header('Location: login.php');
        exit;
    }
}

$auth = new Auth($mysqli);

// Redirect if not logged in
if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// -----------------------------------------------------------------------------
// 3) Profile Management
// -----------------------------------------------------------------------------
$profileMessage = '';
$profileMessageType = '';
$passwordMessage = '';
$passwordMessageType = '';

// Get current user details
$currentUserId = $auth->getUserId();
$currentRole = $auth->getRole();

// Fetch current user data based on role
if ($currentRole === 'staff') {
    $stmt = $mysqli->prepare("SELECT name, email, contact, address, created_at FROM staff WHERE id = ?");
} else {
    $stmt = $mysqli->prepare("SELECT name, email, contact, address, created_at FROM student WHERE id = ?");
}
$stmt->bind_param('i', $currentUserId);
$stmt->execute();
$userData = $stmt->get_result()->fetch_assoc();
$stmt->close();

$currentName = $userData['name'] ?? '';
$currentEmail = $userData['email'] ?? '';
$currentPhone = $userData['contact'] ?? '';
$currentAddress = $userData['address'] ?? '';
$memberSince = $userData['created_at'] ? date('M d, Y', strtotime($userData['created_at'])) : 'Unknown';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $newName = trim($_POST['full_name']);
    $newEmail = trim($_POST['email']);
    $newPhone = trim($_POST['phone']);
    $newAddress = trim($_POST['address']);
    
    // Validate input
    if (empty($newName) || empty($newEmail)) {
        $profileMessage = 'Name and email are required.';
        $profileMessageType = 'danger';
    } elseif (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        $profileMessage = 'Please enter a valid email address.';
        $profileMessageType = 'danger';
    } else {
        // Check if email already exists for other users
        if ($currentRole === 'staff') {
            $checkStmt = $mysqli->prepare("SELECT id FROM staff WHERE email = ? AND id != ?");
        } else {
            $checkStmt = $mysqli->prepare("SELECT id FROM student WHERE email = ? AND id != ?");
        }
        $checkStmt->bind_param('si', $newEmail, $currentUserId);
        $checkStmt->execute();
        $existingUser = $checkStmt->get_result()->fetch_assoc();
        $checkStmt->close();
        
        if ($existingUser) {
            $profileMessage = 'Email address is already in use by another user.';
            $profileMessageType = 'danger';
        } else {
            // Update user profile
            if ($currentRole === 'staff') {
                $updateStmt = $mysqli->prepare("UPDATE staff SET name = ?, email = ?, contact = ?, address = ? WHERE id = ?");
            } else {
                $updateStmt = $mysqli->prepare("UPDATE student SET name = ?, email = ?, contact = ?, address = ? WHERE id = ?");
            }
            $updateStmt->bind_param('ssssi', $newName, $newEmail, $newPhone, $newAddress, $currentUserId);
            
            if ($updateStmt->execute()) {
                $profileMessage = 'Profile updated successfully!';
                $profileMessageType = 'success';
                
                // Update session data
                $_SESSION['userName'] = $newName;
                
                // Update current variables
                $currentName = $newName;
                $currentEmail = $newEmail;
                $currentPhone = $newPhone;
                $currentAddress = $newAddress;
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
        if ($currentRole === 'staff') {
            $stmt = $mysqli->prepare("SELECT password FROM staff WHERE id = ?");
        } else {
            $stmt = $mysqli->prepare("SELECT password FROM student WHERE id = ?");
        }
        $stmt->bind_param('i', $currentUserId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if ($result && password_verify($currentPassword, $result['password'])) {
            // Update password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            if ($currentRole === 'staff') {
                $updateStmt = $mysqli->prepare("UPDATE staff SET password = ? WHERE id = ?");
            } else {
                $updateStmt = $mysqli->prepare("UPDATE student SET password = ? WHERE id = ?");
            }
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

// -----------------------------------------------------------------------------
// 4) ExamManager Class (mysqli-based, separate tables: exams, questions, exam_results)
// -----------------------------------------------------------------------------
class ExamManager {
    private $mysqli;

    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }

    // 3a) Create a new pending exam (staff only), then insert questions
    public function createExam($title, $datetime, $duration, $questions, $staff_id) {
        // Insert into exams
        $stmt = $this->mysqli->prepare("
            INSERT INTO exams 
              (title, exam_datetime, duration, status, created_by, created_at) 
            VALUES (?, ?, ?, 'pending', ?, NOW())
        ");
        $stmt->bind_param('ssii', $title, $datetime, $duration, $staff_id);
        $ok = $stmt->execute();
        if (!$ok) {
            $stmt->close();
            return false;
        }
        $exam_id = $stmt->insert_id;
        $stmt->close();

        // Insert each question into questions table
        foreach ($questions as $q) {
            $question_text = $q['question'];
            $question_type = $q['type'];
            $marks = isset($q['marks']) ? intval($q['marks']) : 1; // Default 1 mark per question

            if ($question_type === 'mcq' && isset($q['options'])) {
                // Extract options
                $optA = $q['options'][0] ?? null;
                $optB = $q['options'][1] ?? null;
                $optC = $q['options'][2] ?? null;
                $optD = $q['options'][3] ?? null;
                // FIXED: Correct syntax for converting 0-based index to database value
                $correct_option = intval($q['correct_answer']);

                $stmtQ = $this->mysqli->prepare("
                    INSERT INTO questions
                      (exam_id, question_text, question_type, option_a, option_b, option_c, option_d, correct_option, marks)
                    VALUES (?, ?, 'mcq', ?, ?, ?, ?, ?, ?)
                ");
                $stmtQ->bind_param(
                    'isssssii',
                    $exam_id,
                    $question_text,
                    $optA,
                    $optB,
                    $optC,
                    $optD,
                    $correct_option,
                    $marks
                );
            } else {
                // Subjective question: options and correct_option are NULL
                $stmtQ = $this->mysqli->prepare("
                    INSERT INTO questions
                      (exam_id, question_text, question_type, option_a, option_b, option_c, option_d, correct_option, marks)
                    VALUES (?, ?, 'subjective', NULL, NULL, NULL, NULL, NULL, ?)
                ");
                $stmtQ->bind_param('isi', $exam_id, $question_text, $marks);
            }

            $stmtQ->execute();
            $stmtQ->close();
        }

        return true;
    }

    // 3b) Fetch all pending exams (for admin)
    public function getPendingExams() {
        $sql = "
            SELECT e.id, e.title, e.exam_datetime, e.duration, u.name AS creator_name
            FROM exams e
            JOIN staff u ON e.created_by = u.id
            WHERE e.status = 'pending'
            ORDER BY e.created_at DESC
        ";
        $res = $this->mysqli->query($sql);
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    // 3c) Approve an exam (admin only)
    public function approveExam($exam_id) {
        $stmt = $this->mysqli->prepare("UPDATE exams SET status = 'approved' WHERE id = ?");
        $stmt->bind_param('i', $exam_id);
        $stmt->execute();
        $ok = $stmt->affected_rows > 0;
        $stmt->close();
        return $ok;
    }

    // 3d) Reject an exam (admin only)
    public function rejectExam($exam_id) {
        $stmt = $this->mysqli->prepare("UPDATE exams SET status = 'rejected' WHERE id = ?");
        $stmt->bind_param('i', $exam_id);
        $stmt->execute();
        $ok = $stmt->affected_rows > 0;
        $stmt->close();
        return $ok;
    }

    // 3e) Get all approved exams (for students to take)
    public function getApprovedExams() {
        $sql = "
            SELECT id, title, exam_datetime, duration 
            FROM exams 
            WHERE status = 'approved' 
            ORDER BY exam_datetime ASC
        ";
        $res = $this->mysqli->query($sql);
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    // 3f) Get one exam's details by its ID
    public function getExamById($id) {
        $stmt = $this->mysqli->prepare("SELECT * FROM exams WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $exam = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $exam;
    }

    // 3g) Fetch all questions for a given exam_id
    public function getQuestionsByExamId($exam_id) {
        $stmt = $this->mysqli->prepare("
            SELECT id, question_text, question_type, option_a, option_b, option_c, option_d, correct_option, marks
            FROM questions
            WHERE exam_id = ?
            ORDER BY id ASC
        ");
        $stmt->bind_param('i', $exam_id);
        $stmt->execute();
        $questions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $questions;
    }

    // 3h) Insert a student's submitted result - FIXED SCORING LOGIC
    public function submitExamResult($student_id, $exam_id, $answers, $totalScore, $totalMarks) {
        // Store answers as JSON: key = question_id, value = chosen index or text
        $jsonA = json_encode($answers, JSON_UNESCAPED_UNICODE);
        $stmt = $this->mysqli->prepare("
            INSERT INTO exam_results 
              (student_id, exam_id, answers, score, total_marks, submitted_at) 
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->bind_param('iisii', $student_id, $exam_id, $jsonA, $totalScore, $totalMarks);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    // 3i) Get all results for a given student
    public function getStudentResults($student_id) {
        $stmt = $this->mysqli->prepare("
            SELECT er.score, er.total_marks, er.submitted_at, e.title 
            FROM exam_results er 
            JOIN exams e ON er.exam_id = e.id
            WHERE er.student_id = ? 
            ORDER BY er.submitted_at DESC
        ");
        $stmt->bind_param('i', $student_id);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    // 3j) Get aggregated statistics for a student
    public function getStudentStats($student_id) {
        $stmt = $this->mysqli->prepare("
            SELECT 
                s.name, s.email,
                COUNT(er.id) AS total_exams,
                IFNULL(ROUND(AVG((er.score/er.total_marks)*100),2),0) AS avg_percentage,
                IFNULL(MAX((er.score/er.total_marks)*100),0) AS max_percentage,
                IFNULL(MIN((er.score/er.total_marks)*100),0) AS min_percentage
            FROM student s
            LEFT JOIN exam_results er ON s.id = er.student_id
            WHERE s.id = ?
            GROUP BY s.id
        ");
        $stmt->bind_param('i', $student_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row;
    }

    // 3k) Fetch just this staff's own exams (any status)
    public function getExamsByStaff($staff_id) {
        $stmt = $this->mysqli->prepare("
            SELECT id, title, exam_datetime, duration, status
            FROM exams
            WHERE created_by = ?
            ORDER BY created_at DESC
        ");
        $stmt->bind_param('i', $staff_id);
        $stmt->execute();
        $exams = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $exams;
    }
}

$examManager = new ExamManager($mysqli);

// -----------------------------------------------------------------------------
// 5) Handle form submissions (create/approve/reject/submit exam)
// -----------------------------------------------------------------------------
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {

        // 4a) Staff creates a new exam
        case 'create_exam':
            if ($auth->getRole() === 'staff') {
                $title     = trim($_POST['title']);
                $datetime  = $_POST['datetime'];
                $duration  = intval($_POST['duration']);
                $questions = [];

                if (!empty($_POST['questions']) && is_array($_POST['questions'])) {
                    foreach ($_POST['questions'] as $q) {
                        $questions[] = [
                            'question'      => trim($q['question']),
                            'type'          => $q['type'],
                            'options'       => $q['options'] ?? null,
                            'correct_answer'=> $q['correct_answer'] ?? null,
                            'marks'         => isset($q['marks']) ? intval($q['marks']) : 1
                        ];
                    }
                }

                if ($examManager->createExam($title, $datetime, $duration, $questions, $auth->getUserId())) {
                    $success = "Exam created successfully and sent for approval!";
                } else {
                    $error = "Failed to create exam. Please try again.";
                }
            } else {
                $error = "Access denied: only staff can create exams.";
            }
            break;

        // 4b) Admin approves an exam
        case 'approve_exam':
            if ($auth->getRole() === 'admin') {
                $exam_id = intval($_POST['exam_id']);
                if ($examManager->approveExam($exam_id)) {
                    $success = "Exam approved successfully!";
                } else {
                    $error = "Failed to approve exam.";
                }
            } else {
                $error = "Access denied: only admin can approve exams.";
            }
            break;

        // 4c) Admin rejects an exam
        case 'reject_exam':
            if ($auth->getRole() === 'admin') {
                $exam_id = intval($_POST['exam_id']);
                if ($examManager->rejectExam($exam_id)) {
                    $success = "Exam rejected.";
                } else {
                    $error = "Failed to reject exam.";
                }
            } else {
                $error = "Access denied: only admin can reject exams.";
            }
            break;

        // 4d) Student submits an exam - FIXED SCORING LOGIC
        case 'submit_exam':
            if ($auth->getRole() === 'student') {
                $student_id = $auth->getUserId();
                $exam_id    = intval($_POST['exam_id']);
                $exam       = $examManager->getExamById($exam_id);

                if ($exam && $exam['status'] === 'approved') {
                    // Fetch questions to grade MCQs
                    $questions = $examManager->getQuestionsByExamId($exam_id);
                    $submittedAnswers = $_POST['answers'] ?? [];
                    $totalScore = 0;
                    $totalMarks = 0;

                    // FIXED: Use the same scoring logic as take_exam.php
                    foreach ($questions as $q) {
                        $totalMarks += $q['marks'];
                        $questionId = $q['id'];
                        
                        if ($q['question_type'] === 'mcq') {
                            // Convert both values to strings for comparison
                            if (isset($submittedAnswers[$questionId]) && 
                                (string)$submittedAnswers[$questionId] === (string)$q['correct_option']) {
                                $totalScore += $q['marks'];
                            }
                        }
                        // Note: Subjective questions need manual grading
                    }

                    // Calculate percentage for display
                    $scorePercentage = $totalMarks > 0 ? round(($totalScore / $totalMarks) * 100, 2) : 0;

                    if ($examManager->submitExamResult($student_id, $exam_id, $submittedAnswers, $totalScore, $totalMarks)) {
                        $success = "Exam submitted! Your score: {$scorePercentage}%";
                    } else {
                        $error = "Failed to submit exam. Please try again.";
                    }
                } else {
                    $error = "Exam not found or not available.";
                }
            } else {
                $error = "Access denied: only students can submit exams.";
            }
            break;
    }
}

// -----------------------------------------------------------------------------
// 6) Determine which "page" to show (dashboard, create_exam, approve_exams, take_exam, students)
// -----------------------------------------------------------------------------
$page = $_GET['page'] ?? 'dashboard';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - Online Examination System</title>
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

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .btn {
            border-radius: 8px;
            padding: 8px 20px;
            font-weight: 500;
        }

        .question-item {
            border: 2px solid #ddd;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            background: #f8f9fa;
        }

        .exam-timer {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #f56565;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            z-index: 1000;
        }

        .password-toggle {
            position: relative;
        }

        .password-toggle .toggle-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            user-select: none;
            z-index: 10;
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
                <i class="fas fa-chalkboard-teacher text-primary me-2"></i>
                Staff Panel
            </h5>
            <small class="text-muted">Welcome, <?php echo htmlspecialchars($auth->getUserName()); ?></small>
        </div>
        
        <nav class="nav flex-column p-2">
            <a href="?page=dashboard" class="nav-link <?php echo $page === 'dashboard' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
            </a>
            <?php if ($auth->getRole() === 'staff'): ?>
                <a href="?page=create_exam" class="nav-link <?php echo $page === 'create_exam' ? 'active' : ''; ?>">
                    <i class="fas fa-plus-circle me-2"></i> Create Exam
                </a>
            <?php endif; ?>
            <?php if ($auth->getRole() === 'admin'): ?>
                <a href="?page=approve_exams" class="nav-link <?php echo $page === 'approve_exams' ? 'active' : ''; ?>">
                    <i class="fas fa-check-circle me-2"></i> Approve Exams
                </a>
            <?php endif; ?>
            <a href="?page=students" class="nav-link <?php echo $page === 'students' ? 'active' : ''; ?>">
                <i class="fas fa-user-graduate me-2"></i> Student Stats
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
        // -------------------------------------------------------------------------
        // 7) PAGE SWITCH: Which "view" to render
        // -------------------------------------------------------------------------
        switch ($page) {
            // -----------------------------------------------------------------------
            case 'profile':
                ?>
                <div class="content-section" id="profile">
                    <div class="pt-3 pb-2 mb-3">
                        <h1 class="h2 text-white">Profile</h1>
                        <p class="text-white-50">Manage your profile information</p>
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
                                        
                                        <div class="mb-3">
                                            <label for="phone" class="form-label">
                                                <i class="fas fa-phone me-1"></i>Phone Number
                                            </label>
                                            <input 
                                                type="tel" 
                                                class="form-control" 
                                                id="phone" 
                                                name="phone"
                                                value="<?php echo htmlspecialchars($currentPhone); ?>"
                                                maxlength="20"
                                                placeholder="e.g., +1234567890"
                                            >
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="address" class="form-label">
                                                <i class="fas fa-map-marker-alt me-1"></i>Address
                                            </label>
                                            <textarea 
                                                class="form-control" 
                                                id="address" 
                                                name="address"
                                                rows="3"
                                                maxlength="255"
                                                placeholder="Enter your full address"
                                            ><?php echo htmlspecialchars($currentAddress); ?></textarea>
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
                                        <small class="text-muted"><?php echo ucfirst($currentRole); ?> ID: <?php echo $currentUserId; ?></small>
                                    </div>
                                    
                                    <hr>
                                    
                                    <div class="mb-2">
                                        <small class="text-muted">Email:</small><br>
                                        <span><?php echo htmlspecialchars($currentEmail ?: 'Not provided'); ?></span>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <small class="text-muted">Phone:</small><br>
                                        <span><?php echo htmlspecialchars($currentPhone ?: 'Not provided'); ?></span>
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
                                    <p class="text-muted small">Keep your account secure</p>
                                    <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                        <i class="fas fa-key me-1"></i>Change Password
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                break;

            // -----------------------------------------------------------------------
            case 'dashboard':
                // STUDENT VIEW
                if ($auth->getRole() === 'student'):
                    $approvedExams  = $examManager->getApprovedExams();
                    $studentResults = $examManager->getStudentResults($auth->getUserId());
                ?>
                    <div class="row mb-4">
                        <div class="col-12">
                            <h2 class="text-white mb-4">
                                <i class="fas fa-user-graduate me-2"></i>Student Dashboard
                            </h2>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-clock me-2"></i>Upcoming Exams
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php foreach ($approvedExams as $exam): ?>
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <h6 class="card-title"><?php echo htmlspecialchars($exam['title']); ?></h6>
                                                <p class="card-text">
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar me-1"></i><?php echo date('M d, Y g:i A', strtotime($exam['exam_datetime'])); ?><br>
                                                        <i class="fas fa-clock me-1"></i><?php echo $exam['duration']; ?> minutes
                                                    </small>
                                                </p>
                                                <?php if (strtotime($exam['exam_datetime']) <= time()): ?>
                                                    <a href="take_exam.php?exam_id=<?php echo $exam['id']; ?>" class="btn btn-success btn-sm">
                                                        <i class="fas fa-play me-1"></i>Start Exam
                                                    </a>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">Not Available Yet</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-history me-2"></i>Your Exam History
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Exam</th>
                                            <th>Date Taken</th>
                                            <th>Score</th>
                                            <th>Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($studentResults as $res): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($res['title']); ?></td>
                                                <td><?php echo date('M d, Y g:i A', strtotime($res['submitted_at'])); ?></td>
                                                <td><?php echo $res['score']; ?>/<?php echo $res['total_marks']; ?></td>
                                                <td>
                                                    <?php 
                                                    $percentage = $res['total_marks'] > 0 ? round(($res['score']/$res['total_marks'])*100, 2) : 0;
                                                    $badgeClass = $percentage >= 80 ? 'bg-success' : ($percentage >= 60 ? 'bg-warning' : 'bg-danger');
                                                    ?>
                                                    <span class="badge <?php echo $badgeClass; ?>"><?php echo $percentage; ?>%</span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                <?php
                // ADMIN VIEW
                elseif ($auth->getRole() === 'admin'):
                    $pendingCount = count($examManager->getPendingExams());
                ?>
                    <div class="row mb-4">
                        <div class="col-12">
                            <h2 class="text-white mb-4">
                                <i class="fas fa-user-shield me-2"></i>Admin Dashboard
                            </h2>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body text-center">
                            <div class="row">
                                <div class="col-md-6 offset-md-3">
                                    <div class="p-4" style="background: linear-gradient(135deg, #f6ad55, #dd6b20); border-radius: 15px; color: white;">
                                        <h2 class="mb-2"><?php echo $pendingCount; ?></h2>
                                        <h5>Pending Approvals</h5>
                                        <p class="mb-0">Exams waiting for your review</p>
                                    </div>
                                </div>
                            </div>
                            <p class="mt-4 text-muted">Use the navigation menu to approve or reject exams and view student statistics.</p>
                        </div>
                    </div>

                <?php
                // STAFF VIEW
                else:
                    $myExams = $examManager->getExamsByStaff($auth->getUserId());
                ?>
                    <div class="row mb-4">
                        <div class="col-12">
                            <h2 class="text-white mb-4">
                                <i class="fas fa-chalkboard-teacher me-2"></i>Online Examination System(Staff Dashboard)
                            </h2>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-clipboard-list me-2"></i>My Exams
                            </h5>
                            <a href="?page=create_exam" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i>Create New Exam
                            </a>
                        </div>
                        <div class="card-body">
                            <?php if (empty($myExams)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-clipboard fa-3x text-muted mb-3"></i>
                                    <h5>No Exams Created Yet</h5>
                                    <p class="text-muted">You haven't created any exams yet.</p>
                                    <a href="?page=create_exam" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i>Create Your First Exam
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Exam Title</th>
                                                <th>Date & Time</th>
                                                <th>Duration</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($myExams as $ex): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($ex['title']); ?></strong>
                                                    </td>
                                                    <td><?php echo date('M d, Y g:i A', strtotime($ex['exam_datetime'])); ?></td>
                                                    <td><?php echo $ex['duration']; ?> minutes</td>
                                                    <td>
                                                        <?php 
                                                        $stat = $ex['status'];
                                                        if ($stat === 'pending') {
                                                            echo "<span class='badge bg-warning'>Pending</span>";
                                                        } elseif ($stat === 'approved') {
                                                            echo "<span class='badge bg-success'>Approved</span>";
                                                        } else {
                                                            echo "<span class='badge bg-danger'>Rejected</span>";
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php
                endif;
                break;

            // -----------------------------------------------------------------------
            case 'create_exam':
                if ($auth->getRole() !== 'staff'):
                    echo "<div class='alert alert-danger'>Access denied: only staff may create exams.</div>";
                    break;
                endif;
                ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <h2 class="text-white mb-4">
                            <i class="fas fa-plus-circle me-2"></i>Create New Exam
                        </h2>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-edit me-2"></i>Exam Details
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="examForm">
                            <input type="hidden" name="action" value="create_exam">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Exam Title:</label>
                                        <input type="text" name="title" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Duration (minutes):</label>
                                        <input type="number" name="duration" min="1" class="form-control" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Date & Time:</label>
                                <input type="datetime-local" name="datetime" class="form-control" required>
                            </div>

                            <hr>

                            <h5 class="mb-3">
                                <i class="fas fa-question-circle me-2"></i>Questions
                            </h5>
                            <div id="questionsContainer">
                                <div class="question-item">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label class="form-label">Question:</label>
                                                <textarea name="questions[0][question]" class="form-control" rows="3" required></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Marks:</label>
                                                <input type="number" name="questions[0][marks]" min="1" value="1" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Question Type:</label>
                                                <select name="questions[0][type]" class="form-select" onchange="toggleOptions(this, 0)">
                                                    <option value="mcq">Multiple Choice</option>
                                                    <option value="subjective">Subjective</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="options-section">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Option A:</label>
                                                    <input type="text" name="questions[0][options][]" class="form-control" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Option B:</label>
                                                    <input type="text" name="questions[0][options][]" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Option C:</label>
                                                    <input type="text" name="questions[0][options][]" class="form-control" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Option D:</label>
                                                    <input type="text" name="questions[0][options][]" class="form-control" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Correct Answer:</label>
                                            <select name="questions[0][correct_answer]" class="form-select">
                                                <option value="0">Option A</option>
                                                <option value="1">Option B</option>
                                                <option value="2">Option C</option>
                                                <option value="3">Option D</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-2 mb-3">
                                <button type="button" onclick="addQuestion()" class="btn btn-outline-primary">
                                    <i class="fas fa-plus me-1"></i>Add Question
                                </button>
                            </div>

                            <hr>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-paper-plane me-1"></i>Create Exam
                                </button>
                                <button type="reset" class="btn btn-secondary">
                                    <i class="fas fa-undo me-1"></i>Reset Form
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <script>
                    let questionCount = 1;

                    function addQuestion() {
                        const container = document.getElementById('questionsContainer');
                        const html = `
                            <div class="question-item">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label class="form-label">Question:</label>
                                            <textarea name="questions[${questionCount}][question]" class="form-control" rows="3" required></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Marks:</label>
                                            <input type="number" name="questions[${questionCount}][marks]" min="1" value="1" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Question Type:</label>
                                            <select name="questions[${questionCount}][type]" class="form-select" onchange="toggleOptions(this, ${questionCount})">
                                                <option value="mcq">Multiple Choice</option>
                                                <option value="subjective">Subjective</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="options-section">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Option A:</label>
                                                <input type="text" name="questions[${questionCount}][options][]" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Option B:</label>
                                                <input type="text" name="questions[${questionCount}][options][]" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Option C:</label>
                                                <input type="text" name="questions[${questionCount}][options][]" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Option D:</label>
                                                <input type="text" name="questions[${questionCount}][options][]" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Correct Answer:</label>
                                        <select name="questions[${questionCount}][correct_answer]" class="form-select">
                                            <option value="0">Option A</option>
                                            <option value="1">Option B</option>
                                            <option value="2">Option C</option>
                                            <option value="3">Option D</option>
                                        </select>
                                    </div>
                                </div>

                                <button type="button" onclick="removeQuestion(this)" class="btn btn-danger btn-sm mb-3">
                                    <i class="fas fa-trash me-1"></i>Remove Question
                                </button>
                            </div>
                        `;
                        container.insertAdjacentHTML('beforeend', html);
                        questionCount++;
                    }

                    function removeQuestion(btn) {
                        btn.closest('.question-item').remove();
                    }

                    function toggleOptions(select, idx) {
                        const questionItem = select.closest('.question-item');
                        const opts = questionItem.querySelector('.options-section');
                        if (select.value === 'subjective') {
                            opts.style.display = 'none';
                        } else {
                            opts.style.display = 'block';
                        }
                    }
                </script>
                <?php
                break;

            // -----------------------------------------------------------------------
            case 'approve_exams':
                if ($auth->getRole() !== 'admin'):
                    echo "<div class='alert alert-danger'>Access denied: only admin can view this page.</div>";
                    break;
                endif;

                $pendingExams = $examManager->getPendingExams();
                ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <h2 class="text-white mb-4">
                            <i class="fas fa-check-circle me-2"></i>Pending Exam Approvals
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
                                        <small class="text-muted">Created by: <?php echo htmlspecialchars($exam['creator_name']); ?></small>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <span class="badge bg-warning">Pending Review</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <p><strong>Date & Time:</strong> <?php echo date('M d, Y g:i A', strtotime($exam['exam_datetime'])); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Duration:</strong> <?php echo $exam['duration']; ?> minutes</p>
                                    </div>
                                </div>

                                <h6>Questions:</h6>
                                <?php 
                                $questions = $examManager->getQuestionsByExamId($exam['id']);
                                foreach ($questions as $i => $q): ?>
                                    <div class="question-item mb-3">
                                        <h6>Question <?php echo $i + 1; ?>: (<?php echo $q['marks']; ?> marks)</h6>
                                        <p><?php echo htmlspecialchars($q['question_text']); ?></p>
                                        <small class="text-muted">Type: <?php echo ucfirst($q['question_type']); ?></small>
                                        <?php if ($q['question_type'] === 'mcq'): ?>
                                            <div class="row mt-2">
                                                <div class="col-md-6">
                                                    <div class="p-2 border rounded mb-2 <?php echo $q['correct_option'] == 0 ? 'bg-success text-white' : ''; ?>">
                                                        A. <?php echo htmlspecialchars($q['option_a']); ?>
                                                        <?php if ($q['correct_option'] == 0): ?>
                                                            <i class="fas fa-check ms-2"></i>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="p-2 border rounded mb-2 <?php echo $q['correct_option'] == 1 ? 'bg-success text-white' : ''; ?>">
                                                        B. <?php echo htmlspecialchars($q['option_b']); ?>
                                                        <?php if ($q['correct_option'] == 1): ?>
                                                            <i class="fas fa-check ms-2"></i>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="p-2 border rounded mb-2 <?php echo $q['correct_option'] == 2 ? 'bg-success text-white' : ''; ?>">
                                                        C. <?php echo htmlspecialchars($q['option_c']); ?>
                                                        <?php if ($q['correct_option'] == 2): ?>
                                                            <i class="fas fa-check ms-2"></i>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="p-2 border rounded mb-2 <?php echo $q['correct_option'] == 3 ? 'bg-success text-white' : ''; ?>">
                                                        D. <?php echo htmlspecialchars($q['option_d']); ?>
                                                        <?php if ($q['correct_option'] == 3): ?>
                                                            <i class="fas fa-check ms-2"></i>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>

                                <div class="d-flex gap-2 mt-3">
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="approve_exam">
                                        <input type="hidden" name="exam_id" value="<?php echo $exam['id']; ?>">
                                        <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to approve this exam?')">
                                            <i class="fas fa-check me-1"></i>Approve
                                        </button>
                                    </form>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="reject_exam">
                                        <input type
                                        <input type="hidden" name="action" value="reject_exam">
                                        <input type="hidden" name="exam_id" value="<?php echo $exam['id']; ?>">
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to reject this exam?')">
                                            <i class="fas fa-times me-1"></i>Reject
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <?php
                break;

            // -----------------------------------------------------------------------
            case 'take_exam':
                if ($auth->getRole() !== 'student'):
                    echo "<div class='alert alert-danger'>Access denied: only students can take exams.</div>";
                    break;
                endif;

                $exam_id = intval($_GET['id'] ?? 0);
                $exam = $examManager->getExamById($exam_id);
                if (!$exam || $exam['status'] !== 'approved'):
                    echo "<div class='alert alert-danger'>Exam not found or not available.</div>";
                    break;
                endif;

                $questions = $examManager->getQuestionsByExamId($exam_id);
                ?>
                <div class="exam-timer" id="timer"></div>

                <div class="row mb-4">
                    <div class="col-12">
                        <h2 class="text-white mb-4">
                            <i class="fas fa-edit me-2"></i><?php echo htmlspecialchars($exam['title']); ?>
                        </h2>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5 class="mb-0">
                                    <i class="fas fa-clock me-2"></i>Duration: <?php echo $exam['duration']; ?> minutes
                                </h5>
                            </div>
                            <div class="col-md-6 text-end">
                                <span class="badge bg-info">Questions: <?php echo count($questions); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="takeExamForm">
                            <input type="hidden" name="action" value="submit_exam">
                            <input type="hidden" name="exam_id" value="<?php echo $exam['id']; ?>">

                            <?php foreach ($questions as $index => $q): ?>
                                <div class="question-item">
                                    <h6>Question <?php echo $index + 1; ?> (<?php echo $q['marks']; ?> marks):</h6>
                                    <p><?php echo htmlspecialchars($q['question_text']); ?></p>

                                    <?php if ($q['question_type'] === 'mcq'): ?>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-check mb-2">
                                                    <input 
                                                        class="form-check-input" 
                                                        type="radio" 
                                                        name="answers[<?php echo $q['id']; ?>]" 
                                                        id="q<?php echo $q['id']; ?>_opt0" 
                                                        value="0"
                                                    >
                                                    <label class="form-check-label" for="q<?php echo $q['id']; ?>_opt0">
                                                        A. <?php echo htmlspecialchars($q['option_a']); ?>
                                                    </label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input 
                                                        class="form-check-input" 
                                                        type="radio" 
                                                        name="answers[<?php echo $q['id']; ?>]" 
                                                        id="q<?php echo $q['id']; ?>_opt1" 
                                                        value="1"
                                                    >
                                                    <label class="form-check-label" for="q<?php echo $q['id']; ?>_opt1">
                                                        B. <?php echo htmlspecialchars($q['option_b']); ?>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check mb-2">
                                                    <input 
                                                        class="form-check-input" 
                                                        type="radio" 
                                                        name="answers[<?php echo $q['id']; ?>]" 
                                                        id="q<?php echo $q['id']; ?>_opt2" 
                                                        value="2"
                                                    >
                                                    <label class="form-check-label" for="q<?php echo $q['id']; ?>_opt2">
                                                        C. <?php echo htmlspecialchars($q['option_c']); ?>
                                                    </label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input 
                                                        class="form-check-input" 
                                                        type="radio" 
                                                        name="answers[<?php echo $q['id']; ?>]" 
                                                        id="q<?php echo $q['id']; ?>_opt3" 
                                                        value="3"
                                                    >
                                                    <label class="form-check-label" for="q<?php echo $q['id']; ?>_opt3">
                                                        D. <?php echo htmlspecialchars($q['option_d']); ?>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="mb-3">
                                            <textarea 
                                                name="answers[<?php echo $q['id']; ?>]" 
                                                rows="4" 
                                                class="form-control"
                                                placeholder="Your answer here..."
                                            ></textarea>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>

                            <div class="text-center">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-paper-plane me-1"></i>Submit Exam
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <script>
                    (function(){
                        var totalSeconds = <?php echo intval($exam['duration']) * 60; ?>;
                        var timerEl = document.getElementById('timer');
                        var formEl = document.getElementById('takeExamForm');

                        function formatTime(sec) {
                            var m = Math.floor(sec / 60),
                                s = sec % 60;
                            if (s < 10) s = '0' + s;
                            return m + ':' + s;
                        }

                        function countdown() {
                            if (totalSeconds <= 0) {
                                formEl.submit();
                                return;
                            }
                            timerEl.textContent = 'Time Remaining: ' + formatTime(totalSeconds);
                            totalSeconds--;
                            setTimeout(countdown, 1000);
                        }
                        countdown();
                    })();
                </script>
                <?php
                break;

            // -----------------------------------------------------------------------
            case 'students':
                if (!in_array($auth->getRole(), ['admin','staff','student'])) {
                    echo "<div class='alert alert-danger'>Access denied.</div>";
                    break;
                }

                if ($auth->getRole() !== 'student' && isset($_GET['sid'])) {
                    $sid = intval($_GET['sid']);
                } else {
                    $sid = ($auth->getRole() === 'student') ? $auth->getUserId() : 0;
                }

                if ($sid <= 0) {
                    ?>
                    <div class="row mb-4">
                        <div class="col-12">
                            <h2 class="text-white mb-4">
                                <i class="fas fa-user-graduate me-2"></i>Student Statistics
                            </h2>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-search me-2"></i>Search for Student Stats
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="GET">
                                <input type="hidden" name="page" value="students">
                                <div class="mb-3">
                                    <label class="form-label">Search Student (name or email):</label>
                                    <input type="text" name="q" class="form-control" placeholder="Enter name/email..." required>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>Search
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php

                    if (!empty($_GET['q'])) {
                        $q = $mysqli->real_escape_string(trim($_GET['q']));
                        $sql = "
                            SELECT id, name, email
                            FROM student
                            WHERE name LIKE '%{$q}%' OR email LIKE '%{$q}%'
                            LIMIT 10
                        ";
                        $res = $mysqli->query($sql);
                        $matches = $res->fetch_all(MYSQLI_ASSOC);

                        if (empty($matches)) {
                            echo "<div class='alert alert-warning'>No students found for '" . htmlspecialchars($q) . "'.</div>";
                        } else {
                            ?>
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Search Results</h5>
                                </div>
                                <div class="card-body">
                                    <div class="list-group">
                                        <?php foreach ($matches as $st): ?>
                                            <a href="?page=students&sid=<?php echo $st['id']; ?>" class="list-group-item list-group-item-action">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($st['name']); ?></h6>
                                                </div>
                                                <p class="mb-1"><?php echo htmlspecialchars($st['email']); ?></p>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    }
                }

                if ($sid > 0) {
                    $stats = $examManager->getStudentStats($sid);
                    if (!$stats) {
                        echo "<div class='alert alert-danger'>Student not found.</div>";
                    } else {
                        $results = $examManager->getStudentResults($sid);
                        ?>
                        <div class="row mb-4">
                            <div class="col-12">
                                <h2 class="text-white mb-4">
                                    <i class="fas fa-user-graduate me-2"></i>Student Statistics
                                </h2>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-chart-bar me-2"></i><?php echo htmlspecialchars($stats['name']); ?>
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="col-md-3">
                                        <div class="text-center p-3 bg-primary text-white rounded">
                                            <h4><?php echo $stats['total_exams']; ?></h4>
                                            <small>Total Exams</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center p-3 bg-success text-white rounded">
                                            <h4><?php echo $stats['avg_percentage']; ?>%</h4>
                                            <small>Average</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center p-3 bg-info text-white rounded">
                                            <h4><?php echo $stats['max_percentage']; ?>%</h4>
                                            <small>Highest</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center p-3 bg-warning text-white rounded">
                                            <h4><?php echo $stats['min_percentage']; ?>%</h4>
                                            <small>Lowest</small>
                                        </div>
                                    </div>
                                </div>

                                <p><strong>Email:</strong> <?php echo htmlspecialchars($stats['email']); ?></p>

                                <h6 class="mt-4 mb-3">All Exam Attempts</h6>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Exam</th>
                                                <th>Date Taken</th>
                                                <th>Score</th>
                                                <th>Percentage</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($results as $row): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                                                    <td><?php echo date('M d, Y g:i A', strtotime($row['submitted_at'])); ?></td>
                                                    <td><?php echo $row['score']; ?>/<?php echo $row['total_marks']; ?></td>
                                                    <td>
                                                        <?php 
                                                        $percentage = $row['total_marks'] > 0 ? round(($row['score']/$row['total_marks'])*100, 2) : 0;
                                                        $badgeClass = $percentage >= 80 ? 'bg-success' : ($percentage >= 60 ? 'bg-warning' : 'bg-danger');
                                                        ?>
                                                        <span class="badge <?php echo $badgeClass; ?>"><?php echo $percentage; ?>%</span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
                break;

            default:
                echo "<div class='alert alert-danger'>Page not found.</div>";
                break;
        }
        ?>
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
    </script>
</body>
</html>
