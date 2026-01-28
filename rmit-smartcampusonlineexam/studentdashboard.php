<?php
// ====== Configuration & Session Setup ======
session_start();

// Database configuration (mysqli version)
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

// FIXED: Define variables BEFORE using them
$studentId = $auth->getUserId();
$fullName = $auth->getUserName() ?? 'Student';
$profileMessage = '';
$profileMessageType = '';

// FIXED: Profile update with correct column names
if ($_POST && isset($_POST['update_profile'])) {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    
    // Basic validation
    $errors = [];
    if (empty($fullName)) {
        $errors[] = "Full name is required";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }
    
    if (empty($errors)) {
        // FIXED: Use correct column name 'contact' instead of 'phone'
        $stmt = $mysqli->prepare("
            UPDATE student 
            SET name = ?, email = ?, contact = ?, address = ? 
            WHERE id = ?
        ");
        $stmt->bind_param('ssssi', $fullName, $email, $phone, $address, $studentId);
        
        if ($stmt->execute()) {
            // Update session data
            $_SESSION['userName'] = $fullName;
            $profileMessage = "Profile updated successfully!";
            $profileMessageType = "success";
        } else {
            $profileMessage = "Error updating profile: " . $mysqli->error;
            $profileMessageType = "danger";
        }
        $stmt->close();
    } else {
        $profileMessage = implode(", ", $errors);
        $profileMessageType = "danger";
    }
}

// ADDED: Password change functionality
$passwordMessage = '';
$passwordMessageType = '';

if ($_POST && isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validation
    $passwordErrors = [];
    
    if (empty($currentPassword)) {
        $passwordErrors[] = "Current password is required";
    }
    
    if (empty($newPassword)) {
        $passwordErrors[] = "New password is required";
    } elseif (strlen($newPassword) < 6) {
        $passwordErrors[] = "New password must be at least 6 characters long";
    }
    
    if ($newPassword !== $confirmPassword) {
        $passwordErrors[] = "New passwords do not match";
    }
    
    if (empty($passwordErrors)) {
        // Verify current password
        $stmt = $mysqli->prepare("SELECT password FROM student WHERE id = ?");
        $stmt->bind_param('i', $studentId);
        $stmt->execute();
        $result = $stmt->get_result();
        $userData = $result->fetch_assoc();
        $stmt->close();
        
        if ($userData && password_verify($currentPassword, $userData['password'])) {
            // Update password
            $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $mysqli->prepare("UPDATE student SET password = ? WHERE id = ?");
            $stmt->bind_param('si', $hashedNewPassword, $studentId);
            
            if ($stmt->execute()) {
                $passwordMessage = "Password changed successfully!";
                $passwordMessageType = "success";
            } else {
                $passwordMessage = "Error updating password: " . $mysqli->error;
                $passwordMessageType = "danger";
            }
            $stmt->close();
        } else {
            $passwordMessage = "Current password is incorrect";
            $passwordMessageType = "danger";
        }
    } else {
        $passwordMessage = implode(", ", $passwordErrors);
        $passwordMessageType = "danger";
    }
}

// FIXED: Fetch Current Student Data with correct column names
$stmt = $mysqli->prepare("
    SELECT name, email, contact, address, created_at 
    FROM student 
    WHERE id = ?
");
$stmt->bind_param('i', $studentId);
$stmt->execute();
$studentData = $stmt->get_result()->fetch_assoc();
$stmt->close();

// FIXED: Set default values with correct variable references
$currentName = $studentData['name'] ?? $fullName;
$currentEmail = $studentData['email'] ?? '';
$currentPhone = $studentData['contact'] ?? ''; // FIXED: Use 'contact' column
$currentAddress = $studentData['address'] ?? '';
$memberSince = $studentData['created_at'] ? date('F j, Y', strtotime($studentData['created_at'])) : 'N/A';

// --------------------------------------------
// 1) Fetch dynamic data for the dashboard
// --------------------------------------------

// FIXED: Count of available exams (approved, not yet taken by this student, AND within exam window)
// Changed: Now includes exams that have started but not expired (assuming exam duration defines the window)
$stmt = $mysqli->prepare("
    SELECT COUNT(*) AS cnt
    FROM exams e
    WHERE e.status = 'approved' 
      AND DATE_ADD(e.exam_datetime, INTERVAL e.duration MINUTE) > NOW()
      AND e.id NOT IN (
          SELECT DISTINCT er.exam_id 
          FROM exam_results er 
          WHERE er.student_id = ?
      )
");
$stmt->bind_param('i', $studentId);
$stmt->execute();
$availableCount = $stmt->get_result()->fetch_assoc()['cnt'];
$stmt->close();

// b) Count of completed exams and average score
$stmt = $mysqli->prepare("
    SELECT 
      COUNT(*) AS completed_count,
      IFNULL(ROUND(AVG((score/total_marks)*100), 2), 0) AS avg_score
    FROM exam_results
    WHERE student_id = ?
");
$stmt->bind_param('i', $studentId);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$completedCount = $row['completed_count'];
$avgScore       = $row['avg_score'];
$stmt->close();

// c) (Optional) Compute rank by average score among all students
$rank = 'Unranked';

if ($completedCount > 0) {
    // Get this student's average score
    $stmt = $mysqli->prepare("
        SELECT ROUND(AVG((score/total_marks)*100), 2) AS student_avg_score
        FROM exam_results
        WHERE student_id = ?
    ");
    $stmt->bind_param('i', $studentId);
    $stmt->execute();
    $studentAvgResult = $stmt->get_result()->fetch_assoc();
    $studentAvgScore = $studentAvgResult['student_avg_score'] ?? 0;
    $stmt->close();

    // Calculate rank - count students with better average scores
    $stmt = $mysqli->prepare("
        SELECT COUNT(DISTINCT student_id) + 1 AS student_rank
        FROM (
            SELECT student_id, AVG((score/total_marks)*100) AS avg_score
            FROM exam_results
            GROUP BY student_id
            HAVING avg_score > ?
        ) AS better_students
    ");
    $stmt->bind_param('d', $studentAvgScore);
    $stmt->execute();
    $rankResult = $stmt->get_result()->fetch_assoc();
    $rank = '#' . $rankResult['student_rank'];
    $stmt->close();
}

// FIXED: Fetch list of available exams for this student (includes started but not expired exams)
$stmt = $mysqli->prepare("
    SELECT e.id, e.title, e.exam_datetime, e.duration, 
           (SELECT COUNT(*) FROM questions q WHERE q.exam_id = e.id) as question_count,
           CASE 
               WHEN NOW() < e.exam_datetime THEN 'upcoming'
               WHEN NOW() >= e.exam_datetime AND NOW() <= DATE_ADD(e.exam_datetime, INTERVAL e.duration MINUTE) THEN 'active'
               ELSE 'expired'
           END as exam_status
    FROM exams e
    WHERE e.status = 'approved'
      AND DATE_ADD(e.exam_datetime, INTERVAL e.duration MINUTE) > NOW()
      AND e.id NOT IN (
          SELECT DISTINCT er.exam_id 
          FROM exam_results er 
          WHERE er.student_id = ?
      )
    ORDER BY e.exam_datetime ASC
");
$stmt->bind_param('i', $studentId);
$stmt->execute();
$availableExams = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// e) Fetch recent results (limit latest 5)
$stmt = $mysqli->prepare("
    SELECT er.score, er.total_marks, er.submitted_at, e.title, er.exam_id
    FROM exam_results er
    JOIN exams e ON er.exam_id = e.id
    WHERE er.student_id = ?
    ORDER BY er.submitted_at DESC
    LIMIT 5
");
$stmt->bind_param('i', $studentId);
$stmt->execute();
$recentResults = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// --------------------------------------------
// 2) Fetch complete table data (keeping original queries)
// --------------------------------------------

// Fetch all exams data
$stmt = $mysqli->prepare("
    SELECT id, title, exam_datetime, duration, status, 
           created_at, created_by
    FROM exams
    ORDER BY exam_datetime DESC
");
$stmt->execute();
$allExams = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch all exam results data
$stmt = $mysqli->prepare("
    SELECT er.id, er.exam_id, er.student_id, er.score, er.total_marks, er.submitted_at,
           e.title as exam_title, s.name as student_name
    FROM exam_results er
    LEFT JOIN exams e ON er.exam_id = e.id
    LEFT JOIN student s ON er.student_id = s.id
    ORDER BY er.submitted_at DESC
");
$stmt->execute();
$allExamResults = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch all questions data
$stmt = $mysqli->prepare("
    SELECT q.id, q.exam_id, q.question_text, q.option_a, q.option_b, 
           q.option_c, q.option_d, q.correct_option, q.marks,
           e.title as exam_title
    FROM questions q
    LEFT JOIN exams e ON q.exam_id = e.id
    ORDER BY q.exam_id, q.id
");
$stmt->execute();
$allQuestions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch exam statistics and marks data
$stmt = $mysqli->prepare("
    SELECT 
        e.id, e.title, 
        COUNT(q.id) as total_questions,
        SUM(q.marks) as total_marks,
        e.duration, e.status, e.exam_datetime
    FROM exams e
    LEFT JOIN questions q ON e.id = q.exam_id
    GROUP BY e.id, e.title, e.duration, e.status, e.exam_datetime
    ORDER BY e.exam_datetime DESC
");
$stmt->execute();
$examStats = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Online Examination System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar .nav-link {
            color: white;
            padding: 15px 20px;
            margin: 5px 0;
            border-radius: 10px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
        .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }
        .main-content {
            background: #f8f9fa;
            min-height: 100vh;
        }
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: all 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .stats-card-2 {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        .stats-card-3 {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }
        .stats-card-4 {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            color: white;
        }
        .exam-card {
            border-left: 4px solid #007bff;
        }
        .exam-card.completed {
            border-left-color: #28a745;
        }
        .exam-card.upcoming {
            border-left-color: #ffc107;
        }
        .exam-card.active {
            border-left-color: #28a745;
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        }
        .exam-card.overdue {
            border-left-color: #dc3545;
        }
        .content-section {
            display: none;
        }
        .content-section.active {
            display: block;
        }
        .table-container {
            max-height: 600px;
            overflow-y: auto;
        }
        .status-badge {
            font-size: 0.75rem;
        }
        .data-table {
            font-size: 0.9rem;
        }
        .question-text {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 sidebar">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h4 class="text-white">Student Portal</h4>
                        <p class="text-white-50">Welcome, <?php echo htmlspecialchars($fullName); ?></p>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#" onclick="showSection('dashboard')">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="showSection('available-exams')">
                                <i class="fas fa-clipboard-list me-2"></i>
                                Available Exams
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="showSection('my-results')">
                                <i class="fas fa-chart-line me-2"></i>
                                My Results
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="showSection('practice')">
                                <i class="fas fa-dumbbell me-2"></i>
                                Practice Tests
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="showSection('profile')">
                                <i class="fas fa-user me-2"></i>
                                Profile
                            </a>
                        </li>
                        <li class="nav-item mt-4">
                            <a class="nav-link" href="logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                
                <!-- Dashboard Section -->
                <div class="content-section active" id="dashboard">
                    <div class="pt-3 pb-2 mb-3">
                        <h1 class="h2">Student Dashboard</h1>
                        <p class="text-muted">Track your progress and take exams</p>
                    </div>

                    <!-- Stats Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="card stats-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5 class="card-title">Available Exams</h5>
                                            <h2 class="mb-0"><?php echo $availableCount; ?></h2>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-clipboard-list fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stats-card-2">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5 class="card-title">Completed</h5>
                                            <h2 class="mb-0"><?php echo $completedCount; ?></h2>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-check-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stats-card-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5 class="card-title">Average Score</h5>
                                            <h2 class="mb-0"><?php echo $avgScore; ?>%</h2>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-chart-line fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stats-card-4">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5 class="card-title">Rank</h5>
                                            <h2 class="mb-0"><?php echo htmlspecialchars($rank); ?></h2>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-trophy fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Available Exams Quick View -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Available Exams</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <?php if (empty($availableExams)): ?>
                                            <div class="col-12">
                                                <div class="alert alert-info">
                                                    <i class="fas fa-info-circle me-2"></i>
                                                    No exams are currently available. All exams may be completed or expired.
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <?php 
                                            // Display only the first 4 exams in the dashboard
                                            $dashboardExams = array_slice($availableExams, 0, 4);
                                            foreach ($dashboardExams as $exam): 
                                                $examDateTime = strtotime($exam['exam_datetime']);
                                                $dueDate = date('M j, Y H:i', $examDateTime);
                                                $cardClass = $exam['exam_status']; // Use the status from query
                                                
                                                // Set status text and class based on exam status
                                                if ($exam['exam_status'] == 'active') {
                                                    $statusText = 'Available Now';
                                                    $statusClass = 'bg-success';
                                                    $cardClass .= ' pulse-animation'; // Add animation for active exams
                                                } else {
                                                    $statusText = 'Upcoming';
                                                    $statusClass = 'bg-warning text-dark';
                                                }
                                            ?>
                                            <div class="col-md-6 mb-3">
                                                <div class="card exam-card <?php echo $cardClass; ?>">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                            <h6 class="card-title"><?php echo htmlspecialchars($exam['title']); ?></h6>
                                                            <span class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                                                        </div>
                                                        <p class="card-text text-muted">
                                                            <i class="fas fa-clock me-1"></i>Duration: <?php echo intval($exam['duration']); ?> minutes<br>
                                                            <i class="fas fa-calendar me-1"></i>Scheduled: <?php echo $dueDate; ?><br>
                                                            <i class="fas fa-question-circle me-1"></i>Questions: <?php echo $exam['question_count']; ?>
                                                        </p>
                                                        <button 
                                                            class="btn <?php echo $exam['exam_status'] == 'active' ? 'btn-success' : 'btn-primary'; ?> btn-sm"
                                                            onclick="checkExamTime(<?php echo $exam['id']; ?>, '<?php echo $exam['exam_datetime']; ?>', '<?php echo $exam['exam_status']; ?>')"
                                                        >
                                                            <i class="fas fa-play me-1"></i>
                                                            <?php echo $exam['exam_status'] == 'active' ? 'Start Now' : 'Start Exam'; ?>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                            <?php if (count($availableExams) > 4): ?>
                                                <div class="col-12 text-center mt-2">
                                                    <button class="btn btn-link" onclick="showSection('available-exams')">
                                                        View all <?php echo count($availableExams); ?> available exams
                                                    </button>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Results Quick View -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Recent Results</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Exam Name</th>
                                                    <th>Date Taken</th>
                                                    <th>Score</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($recentResults)): ?>
                                                    <tr>
                                                        <td colspan="4" class="text-center text-muted">No results to display.</td>
                                                    </tr>
                                                <?php else: ?>
                                                    <?php foreach ($recentResults as $res): 
                                                        $dateTaken = date('Y-m-d', strtotime($res['submitted_at']));
                                                        $scorePct  = $res['total_marks'] > 0 ? round(($res['score'] / $res['total_marks']) * 100, 2) : 0;
                                                        // Determine grade badge based on score
                                                        if ($scorePct >= 90) {
                                                            $badgeClass = 'bg-success';
                                                            $gradeText  = 'A';
                                                        } elseif ($scorePct >= 80) {
                                                            $badgeClass = 'bg-primary';
                                                            $gradeText  = 'B';
                                                        } elseif ($scorePct >= 70) {
                                                            $badgeClass = 'bg-warning';
                                                            $gradeText  = 'C';
                                                        } else {
                                                            $badgeClass = 'bg-danger';
                                                            $gradeText  = 'D';
                                                        }
                                                    ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($res['title']); ?></td>
                                                        <td><?php echo $dateTaken; ?></td>
                                                        <td><?php echo $scorePct; ?>%</td>
                                                        <td>
                                                            <span class="badge <?php echo $badgeClass; ?>">
                                                                <?php echo $gradeText; ?>
                                                            </span>
                                                            <button 
                                                                class="btn btn-sm btn-info ms-2"
                                                                onclick="window.location.href='exam_result.php?exam_id=<?php echo $res['exam_id']; ?>&student_id=<?php echo $studentId; ?>'"
                                                                >
                                                                View Details
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Available Exams Section -->
                <div class="content-section" id="available-exams">
                    <div class="pt-3 pb-2 mb-3">
                        <h1 class="h2">Available Exams</h1>
                        <p class="text-muted">Exams that you can take</p>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <?php if (empty($availableExams)): ?>
                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <strong>No exams available</strong><br>
                                            All exams have either been completed by you or have expired. Please check back later for new exams.
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($availableExams as $exam): 
                                        $examDateTime = strtotime($exam['exam_datetime']);
                                        $dueDate = date('M j, Y H:i', $examDateTime);
                                        $cardClass = $exam['exam_status'];
                                        
                                        // Set status text and class based on exam status
                                        if ($exam['exam_status'] == 'active') {
                                            $statusText = 'Available Now';
                                            $statusClass = 'bg-success';
                                            $cardClass .= ' pulse-animation';
                                        } else {
                                            $statusText = 'Upcoming';
                                            $statusClass = 'bg-warning text-dark';
                                        }
                                    ?>
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card exam-card <?php echo $cardClass; ?>">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h6 class="card-title"><?php echo htmlspecialchars($exam['title']); ?></h6>
                                                    <span class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                                                </div>
                                                <p class="card-text text-muted">
                                                    <i class="fas fa-clock me-1"></i>Duration: <?php echo intval($exam['duration']); ?> minutes<br>
                                                    <i class="fas fa-calendar me-1"></i>Scheduled: <?php echo $dueDate; ?><br>
                                                    <i class="fas fa-question-circle me-1"></i>Questions: <?php echo $exam['question_count']; ?>
                                                </p>
                                                <button 
                                                    class="btn <?php echo $exam['exam_status'] == 'active' ? 'btn-success' : 'btn-primary'; ?> btn-sm"
                                                    onclick="checkExamTime(<?php echo $exam['id']; ?>, '<?php echo $exam['exam_datetime']; ?>', '<?php echo $exam['exam_status']; ?>')"
                                                >
                                                    <i class="fas fa-play me-1"></i>
                                                    <?php echo $exam['exam_status'] == 'active' ? 'Start Now' : 'Start Exam'; ?>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- My Results Section -->
                <div class="content-section" id="my-results">
                    <div class="pt-3 pb-2 mb-3">
                        <h1 class="h2">My Results</h1>
                        <p class="text-muted">Your exam results and performance</p>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive table-container">
                                <table class="table table-striped data-table">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Exam Name</th>
                                            <th>Date Taken</th>
                                            <th>Score</th>
                                            <th>Grade</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($recentResults)): ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">No results to display.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($recentResults as $res): 
                                                $dateTaken = date('Y-m-d H:i', strtotime($res['submitted_at']));
                                                $scorePct  = $res['total_marks'] > 0 ? round(($res['score'] / $res['total_marks']) * 100, 2) : 0;
                                                if ($scorePct >= 90) {
                                                    $badgeClass = 'bg-success';
                                                    $gradeText  = 'A';
                                                } elseif ($scorePct >= 80) {
                                                    $badgeClass = 'bg-primary';
                                                    $gradeText  = 'B';
                                                } elseif ($scorePct >= 70) {
                                                    $badgeClass = 'bg-warning text-dark';
                                                    $gradeText  = 'C';
                                                } else {
                                                    $badgeClass = 'bg-danger';
                                                    $gradeText  = 'D';
                                                }
                                            ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($res['title']); ?></td>
                                                <td><?php echo $dateTaken; ?></td>
                                                <td><?php echo $scorePct; ?>%</td>
                                                <td>
                                                    <span class="badge <?php echo $badgeClass; ?> status-badge">
                                                        <?php echo $gradeText; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button 
                                                        class="btn btn-sm btn-info"
                                                        onclick="window.location.href='exam_result.php?exam_id=<?php echo $res['exam_id']; ?>&student_id=<?php echo $studentId; ?>'"
                                                    >
                                                        <i class="fas fa-eye"></i> View
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Practice Tests Section -->
                <div class="content-section" id="practice">
                    <div class="pt-3 pb-2 mb-3">
                        <h1 class="h2">Practice Tests</h1>
                        <p class="text-muted">Practice tests to improve your skills</p>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="text-center py-5">
                                <i class="fas fa-dumbbell fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Practice Tests Coming Soon</h5>
                                <p class="text-muted">Practice test functionality will be available in the next update.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profile Section -->
                <div class="content-section" id="profile">
                    <div class="pt-3 pb-2 mb-3">
                        <h1 class="h2">Profile</h1>
                        <p class="text-muted">Manage your profile information</p>
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
                                        <small class="text-muted">Student ID: <?php echo $studentId; ?></small>
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

                <!-- All Exams Table Section -->
                <div class="content-section" id="all-exams">
                    <div class="pt-3 pb-2 mb-3">
                        <h1 class="h2">All Exams Data</h1>
                        <p class="text-muted">Complete exams table data from database</p>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-table me-2"></i>Exams Table 
                                <span class="badge bg-primary"><?php echo count($allExams); ?> records</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive table-container">
                                <table class="table table-striped table-hover data-table">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Title</th>
                                            <th>Date & Time</th>
                                            <th>Duration</th>
                                            <th>Status</th>
                                            <th>Created By</th>
                                            <th>Created At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($allExams)): ?>
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">No exam data available.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($allExams as $exam): 
                                                $statusClass = '';
                                                switch($exam['status']) {
                                                    case 'approved': $statusClass = 'bg-success'; break;
                                                    case 'pending': $statusClass = 'bg-warning text-dark'; break;
                                                    case 'draft': $statusClass = 'bg-secondary'; break;
                                                    default: $statusClass = 'bg-info';
                                                }
                                                $examDate = $exam['exam_datetime'] ? date('Y-m-d H:i', strtotime($exam['exam_datetime'])) : 'Not set';
                                                $createdDate = $exam['created_at'] ? date('Y-m-d', strtotime($exam['created_at'])) : 'N/A';
                                            ?>
                                            <tr>
                                                <td><?php echo $exam['id']; ?></td>
                                                <td><?php echo htmlspecialchars($exam['title']); ?></td>
                                                <td><?php echo $examDate; ?></td>
                                                <td><?php echo $exam['duration']; ?> min</td>
                                                <td>
                                                    <span class="badge <?php echo $statusClass; ?> status-badge">
                                                        <?php echo ucfirst($exam['status']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo $exam['created_by'] ?? 'N/A'; ?></td>
                                                <td><?php echo $createdDate; ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- All Results Section -->
                <div class="content-section" id="all-results">
                    <div class="pt-3 pb-2 mb-3">
                        <h1 class="h2">All Exam Results</h1>
                        <p class="text-muted">Complete exam results data from database</p>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-bar me-2"></i>Exam Results Table 
                                <span class="badge bg-primary"><?php echo count($allExamResults); ?> records</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive table-container">
                                <table class="table table-striped table-hover data-table">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Exam</th>
                                            <th>Student</th>
                                            <th>Score</th>
                                            <th>Grade</th>
                                            <th>Submitted At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($allExamResults)): ?>
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">No exam results available.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($allExamResults as $result): 
                                                $scorePct = $result['total_marks'] > 0 ? round(($result['score'] / $result['total_marks']) * 100, 2) : 0;
                                                if ($scorePct >= 90) {
                                                    $badgeClass = 'bg-success';
                                                    $gradeText = 'A';
                                                } elseif ($scorePct >= 80) {
                                                    $badgeClass = 'bg-primary';
                                                    $gradeText = 'B';
                                                } elseif ($scorePct >= 70) {
                                                    $badgeClass = 'bg-warning text-dark';
                                                    $gradeText = 'C';
                                                } else {
                                                    $badgeClass = 'bg-danger';
                                                    $gradeText = 'D';
                                                }
                                                $submittedDate = $result['submitted_at'] ? date('Y-m-d H:i', strtotime($result['submitted_at'])) : 'N/A';
                                            ?>
                                            <tr>
                                                <td><?php echo $result['id']; ?></td>
                                                <td><?php echo htmlspecialchars($result['exam_title'] ?? 'N/A'); ?></td>
                                                <td><?php echo htmlspecialchars($result['student_name'] ?? 'N/A'); ?></td>
                                                <td><?php echo $result['score']; ?>/<?php echo $result['total_marks']; ?> (<?php echo $scorePct; ?>%)</td>
                                                <td>
                                                    <span class="badge <?php echo $badgeClass; ?> status-badge">
                                                        <?php echo $gradeText; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo $submittedDate; ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-info" onclick="viewResultDetails(<?php echo $result['id']; ?>)">
                                                        <i class="fas fa-eye"></i> View
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- All Questions Section -->
                <div class="content-section" id="all-questions">
                    <div class="pt-3 pb-2 mb-3">
                        <h1 class="h2">All Questions</h1>
                        <p class="text-muted">Complete questions data from database</p>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-question-circle me-2"></i>Questions Table 
                                <span class="badge bg-primary"><?php echo count($allQuestions); ?> records</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive table-container">
                                <table class="table table-striped table-hover data-table">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Exam</th>
                                            <th>Question</th>
                                            <th>Options</th>
                                            <th>Correct</th>
                                            <th>Marks</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($allQuestions)): ?>
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">No questions available.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($allQuestions as $question): ?>
                                            <tr>
                                                <td><?php echo $question['id']; ?></td>
                                                <td><?php echo htmlspecialchars($question['exam_title'] ?? 'N/A'); ?></td>
                                                <td>
                                                    <div class="question-text" title="<?php echo htmlspecialchars($question['question_text']); ?>">
                                                        <?php echo htmlspecialchars(substr($question['question_text'], 0, 50)); ?>
                                                        <?php if (strlen($question['question_text']) > 50) echo '...'; ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <small>
                                                        A: <?php echo htmlspecialchars(substr($question['option_a'], 0, 20)); ?><br>
                                                        B: <?php echo htmlspecialchars(substr($question['option_b'], 0, 20)); ?><br>
                                                        C: <?php echo htmlspecialchars(substr($question['option_c'], 0, 20)); ?><br>
                                                        D: <?php echo htmlspecialchars(substr($question['option_d'], 0, 20)); ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success"><?php echo chr(65 + intval($question['correct_option'])); ?></span>
                                                </td>
                                                <td><?php echo $question['marks']; ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-info" onclick="viewQuestionDetails(<?php echo $question['id']; ?>)">
                                                        <i class="fas fa-eye"></i> View
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Exam Statistics Section -->
                <div class="content-section" id="exam-stats">
                    <div class="pt-3 pb-2 mb-3">
                        <h1 class="h2">Exam Statistics</h1>
                        <p class="text-muted">Detailed exam statistics with questions and marks</p>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-pie me-2"></i>Exam Statistics 
                                <span class="badge bg-primary"><?php echo count($examStats); ?> exams</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive table-container">
                                <table class="table table-striped table-hover data-table">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Exam Title</th>
                                            <th>Total Questions</th>
                                            <th>Total Marks</th>
                                            <th>Duration (min)</th>
                                            <th>Status</th>
                                            <th>Exam Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($examStats)): ?>
                                            <tr>
                                                <td colspan="8" class="text-center text-muted">No exam data available.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($examStats as $stat): 
                                                $statusClass = '';
                                                switch($stat['status']) {
                                                    case 'approved': $statusClass = 'bg-success'; break;
                                                    case 'pending': $statusClass = 'bg-warning text-dark'; break;
                                                    case 'draft': $statusClass = 'bg-secondary'; break;
                                                    default: $statusClass = 'bg-info';
                                                }
                                                $examDate = $stat['exam_datetime'] ? date('Y-m-d H:i', strtotime($stat['exam_datetime'])) : 'Not set';
                                            ?>
                                            <tr>
                                                <td><?php echo $stat['id']; ?></td>
                                                <td><?php echo htmlspecialchars($stat['title']); ?></td>
                                                <td>
                                                    <span class="badge bg-info"><?php echo $stat['total_questions'] ?? 0; ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary"><?php echo $stat['total_marks'] ?? 0; ?></span>
                                                </td>
                                                <td><?php echo $stat['duration']; ?></td>
                                                <td>
                                                    <span class="badge <?php echo $statusClass; ?> status-badge">
                                                        <?php echo ucfirst($stat['status']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo $examDate; ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary" onclick="showExamDetails(<?php echo $stat['id']; ?>)">
                                                        <i class="fas fa-eye"></i> Details
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">
                        <i class="fas fa-lock me-2"></i>Change Password
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="" id="changePasswordForm">
                    <div class="modal-body">
                        <input type="hidden" name="change_password" value="1">
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label">
                                <i class="fas fa-key me-1"></i>Current Password <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="password" 
                                class="form-control" 
                                id="current_password" 
                                name="current_password"
                                required
                                placeholder="Enter your current password"
                            >
                        </div>
                        
                        <div class="mb-3">
                            <label for="new_password" class="form-label">
                                <i class="fas fa-lock me-1"></i>New Password <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="password" 
                                class="form-control" 
                                id="new_password" 
                                name="new_password"
                                required
                                minlength="6"
                                placeholder="Enter new password (min 6 characters)"
                            >
                            <div class="form-text">Password must be at least 6 characters long</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">
                                <i class="fas fa-lock me-1"></i>Confirm New Password <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="password" 
                                class="form-control" 
                                id="confirm_password" 
                                name="confirm_password"
                                required
                                minlength="6"
                                placeholder="Confirm your new password"
                            >
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Security Tips:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Use a strong password with letters, numbers, and symbols</li>
                                <li>Don't use personal information in your password</li>
                                <li>Don't share your password with anyone</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-key me-1"></i>Change Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Navigation function to show different sections
        function showSection(sectionId) {
            // Hide all sections
            const sections = document.querySelectorAll('.content-section');
            sections.forEach(section => {
                section.classList.remove('active');
            });
            
            // Show the selected section
            const targetSection = document.getElementById(sectionId);
            if (targetSection) {
                targetSection.classList.add('active');
            }
            
            // Update active nav link
            const navLinks = document.querySelectorAll('.sidebar .nav-link');
            navLinks.forEach(link => {
                link.classList.remove('active');
            });
            
            // Find and activate the clicked nav link
            const activeLink = document.querySelector(`[onclick="showSection('${sectionId}')"]`);
            if (activeLink) {
                activeLink.classList.add('active');
            }
        }

        // Function to show exam details (placeholder)
        function showExamDetails(examId) {
            alert(`Showing details for exam ID: ${examId}`);
            // In a real application, this would open a modal or navigate to a details page
        }

        // Function to view result details (placeholder)
        function viewResultDetails(resultId) {
            alert(`Viewing result details for ID: ${resultId}`);
            // In a real application, this would open a modal or navigate to a details page
        }

        // Function to view question details (placeholder)
        function viewQuestionDetails(questionId) {
            alert(`Viewing question details for ID: ${questionId}`);
            // In a real application, this would open a modal or navigate to a details page
        }

        function resetForm() {
            if (confirm('Are you sure you want to reset all changes?')) {
                document.getElementById('profileForm').reset();
            }
        }

        // Password change form validation
        document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
            const currentPassword = document.getElementById('current_password').value;
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (!currentPassword) {
                alert('Current password is required');
                e.preventDefault();
                return;
            }
            
            if (newPassword.length < 6) {
                alert('New password must be at least 6 characters long');
                e.preventDefault();
                return;
            }
            
            if (newPassword !== confirmPassword) {
                alert('New passwords do not match');
                e.preventDefault();
                return;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Changing...';
            submitBtn.disabled = true;
            
            // Re-enable after a delay (the form will submit)
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 2000);
        });

        // Real-time password confirmation validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword && newPassword !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
                this.classList.add('is-invalid');
            } else {
                this.setCustomValidity('');
                this.classList.remove('is-invalid');
            }
        });

        // Clear form when modal is closed
        document.getElementById('changePasswordModal').addEventListener('hidden.bs.modal', function() {
            document.getElementById('changePasswordForm').reset();
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        });

        // Profile form validation
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            const fullName = document.getElementById('full_name').value.trim();
            const email = document.getElementById('email').value.trim();
            
            if (!fullName) {
                alert('Full name is required');
                e.preventDefault();
                return;
            }
            
            if (!email || !isValidEmail(email)) {
                alert('Valid email address is required');
                e.preventDefault();
                return;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Updating...';
            submitBtn.disabled = true;
            
            // Re-enable after a delay (the form will submit)
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 2000);
        });

        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        // Auto-save draft functionality (optional)
        let autoSaveTimeout;
        function autoSaveProfile() {
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = setTimeout(() => {
                // You could implement auto-save to localStorage here
                console.log('Auto-saving profile draft...');
            }, 2000);
        }

        // Add auto-save listeners to form fields
        document.querySelectorAll('#profileForm input, #profileForm textarea').forEach(field => {
            field.addEventListener('input', autoSaveProfile);
        });

        // Auto-refresh functionality (optional)
        function refreshData() {
            // This would typically make AJAX calls to refresh data
            console.log('Refreshing dashboard data...');
        }

        // Initialize tooltips (if using Bootstrap tooltips)
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // Search functionality for tables (basic implementation)
        function searchTable(tableId, searchInputId) {
            const input = document.getElementById(searchInputId);
            const table = document.getElementById(tableId);
            const rows = table.getElementsByTagName('tr');
            
            input.addEventListener('keyup', function() {
                const filter = input.value.toLowerCase();
                
                for (let i = 1; i < rows.length; i++) {
                    const row = rows[i];
                    const cells = row.getElementsByTagName('td');
                    let found = false;
                    
                    for (let j = 0; j < cells.length; j++) {
                        if (cells[j].textContent.toLowerCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                    
                    row.style.display = found ? '' : 'none';
                }
            });
        }

        // Confirmation for logout
        document.querySelector('a[href="logout.php"]').addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to logout?')) {
                e.preventDefault();
            }
        });

        // UPDATED: Function to check if exam can be started
        function checkExamTime(examId, examDateTime, examStatus) {
            const now = new Date();
            const examTime = new Date(examDateTime);
            
            // If exam is already marked as active, allow immediate start
            if (examStatus === 'active') {
                if (confirm('🎯 Exam is now available!\n\nAre you ready to start the exam? Once started, the timer will begin immediately.')) {
                    window.location.href = `take_exam.php?exam_id=${examId}`;
                }
                return;
            }
            
            // Check if current time is before exam start time
            if (now < examTime) {
                const timeDiff = examTime - now;
                const hours = Math.floor(timeDiff / (1000 * 60 * 60));
                const minutes = Math.floor((timeDiff % (1000 * 60 * 60)) / (1000 * 60));
                
                let timeMessage = '';
                if (hours > 0) {
                    timeMessage = `${hours} hour(s) and ${minutes} minute(s)`;
                } else {
                    timeMessage = `${minutes} minute(s)`;
                }
                
                alert(`⏰ Exam Not Started Yet!\n\nThis exam is scheduled to start on:\n${examTime.toLocaleString()}\n\nTime remaining: ${timeMessage}\n\nPlease wait until the scheduled time to start the exam.`);
                return false;
            }
            
            // If exam time has arrived, proceed to exam
            if (confirm('Are you ready to start the exam? Once started, the timer will begin immediately.')) {
                window.location.href = `take_exam.php?exam_id=${examId}`;
            }
        }

        // Auto-refresh page every 60 seconds to update exam statuses
        setInterval(function() {
            // Only refresh if we're on the dashboard or available exams section
            const activeSection = document.querySelector('.content-section.active');
            if (activeSection && (activeSection.id === 'dashboard' || activeSection.id === 'available-exams')) {
                location.reload();
            }
        }, 60000); // Refresh every 60 seconds
    </script>

</body>
</html>
