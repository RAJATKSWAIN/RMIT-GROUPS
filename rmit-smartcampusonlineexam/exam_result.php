<?php
// ====== Configuration & Session Setup ======
session_start();

// Set timezone to Indian Standard Time
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
    $mysqli->query("SET time_zone = '+05:30'");
    return $mysqli;
}

$mysqli = connectDB($dbConfig);

class Auth {
    private $mysqli;
    
    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }
    
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
    
    public function validateUser() {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        $userId = $this->getUserId();
        $userType = $this->getRole();
        
        if ($userType === 'student') {
            $stmt = $this->mysqli->prepare("SELECT id FROM student WHERE id = ?");
        } elseif ($userType === 'staff') {
            $stmt = $this->mysqli->prepare("SELECT id FROM staff WHERE id = ?");
        } elseif ($userType === 'admin') {
            $stmt = $this->mysqli->prepare("SELECT id FROM admin WHERE id = ?");
        } else {
            return false;
        }
        
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
        
        return $exists;
    }
}

$auth = new Auth($mysqli);

// Enhanced validation
if (!$auth->isLoggedIn() || !$auth->validateUser()) {
    session_destroy();
    header('Location: login.php?error=invalid_session');
    exit;
}

// Only students can view their results (staff/admin can view with different permissions)
$canViewResult = false;
$studentId = null;

if ($auth->getRole() === 'student') {
    $canViewResult = true;
    $studentId = $auth->getUserId();
} elseif (in_array($auth->getRole(), ['staff', 'admin'])) {
    // Staff and admin can view any student's result if student_id is provided
    $canViewResult = true;
    $studentId = isset($_GET['student_id']) ? intval($_GET['student_id']) : null;
    
    if (!$studentId) {
        header('Location: ' . ($auth->getRole() === 'staff' ? 'staffdashboard.php' : 'admin_dashboard.php'));
        exit;
    }
}

if (!$canViewResult) {
    header('Location: login.php?error=access_denied');
    exit;
}

// Get exam ID and result ID from URL
$examId = isset($_GET['exam_id']) ? intval($_GET['exam_id']) : 0;
$resultId = isset($_GET['result_id']) ? intval($_GET['result_id']) : 0;

if ($examId <= 0 && $resultId <= 0) {
    header('Location: studentdashboard.php?error=invalid_parameters');
    exit;
}

// Fetch exam result data
if ($resultId > 0) {
    // Fetch by result ID
    $stmt = $mysqli->prepare("
        SELECT er.*, e.title, e.duration, e.exam_datetime, s.name as student_name, s.email as student_email
        FROM exam_results er
        JOIN exams e ON er.exam_id = e.id
        JOIN student s ON er.student_id = s.id
        WHERE er.id = ? AND er.student_id = ?
    ");
    $stmt->bind_param('ii', $resultId, $studentId);
} else {
    // Fetch by exam ID and student ID
    $stmt = $mysqli->prepare("
        SELECT er.*, e.title, e.duration, e.exam_datetime, s.name as student_name, s.email as student_email
        FROM exam_results er
        JOIN exams e ON er.exam_id = e.id
        JOIN student s ON er.student_id = s.id
        WHERE er.exam_id = ? AND er.student_id = ?
        ORDER BY er.submitted_at DESC
        LIMIT 1
    ");
    $stmt->bind_param('ii', $examId, $studentId);
}

$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$result) {
    header('Location: studentdashboard.php?error=result_not_found');
    exit;
}

// Calculate percentage and grade
$scorePercentage = $result['total_marks'] > 0 ? round(($result['score'] / $result['total_marks']) * 100, 2) : 0;

// Determine grade based on percentage
function getGrade($percentage) {
    if ($percentage >= 90) return ['grade' => 'A+', 'class' => 'success'];
    if ($percentage >= 80) return ['grade' => 'A', 'class' => 'success'];
    if ($percentage >= 70) return ['grade' => 'B+', 'class' => 'info'];
    if ($percentage >= 60) return ['grade' => 'B', 'class' => 'info'];
    if ($percentage >= 50) return ['grade' => 'C+', 'class' => 'warning'];
    if ($percentage >= 40) return ['grade' => 'C', 'class' => 'warning'];
    if ($percentage >= 33) return ['grade' => 'D', 'class' => 'danger'];
    return ['grade' => 'F', 'class' => 'danger'];
}

$gradeInfo = getGrade($scorePercentage);

// Fetch questions and answers for detailed analysis
$stmt = $mysqli->prepare("
    SELECT q.id, q.question_text, q.option_a, q.option_b, q.option_c, q.option_d, 
           q.correct_option, q.marks, q.question_type
    FROM questions q
    WHERE q.exam_id = ?
    ORDER BY q.id ASC
");
$stmt->bind_param('i', $result['exam_id']);
$stmt->execute();
$questions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Parse student answers
$studentAnswers = json_decode($result['answers'], true) ?? [];

// Calculate detailed statistics
$totalQuestions = count($questions);
$correctAnswers = 0;
$wrongAnswers = 0;
$unansweredQuestions = 0;
$questionAnalysis = [];

foreach ($questions as $question) {
    $questionId = $question['id'];
    $studentAnswer = $studentAnswers[$questionId] ?? null;
    $isCorrect = false;
    $status = 'unanswered';
    
    if ($studentAnswer !== null && $studentAnswer !== '') {
        if ($question['question_type'] === 'mcq') {
            $isCorrect = (string)$studentAnswer === (string)$question['correct_option'];
            $status = $isCorrect ? 'correct' : 'wrong';
            
            if ($isCorrect) {
                $correctAnswers++;
            } else {
                $wrongAnswers++;
            }
        } else {
            // Subjective question - marked as answered but needs manual grading
            $status = 'subjective';
        }
    } else {
        $unansweredQuestions++;
    }
    
    $questionAnalysis[] = [
        'question' => $question,
        'student_answer' => $studentAnswer,
        'is_correct' => $isCorrect,
        'status' => $status
    ];
}

// Get class statistics for comparison
$stmt = $mysqli->prepare("
    SELECT 
        COUNT(*) as total_attempts,
        AVG((score/total_marks)*100) as class_average,
        MAX((score/total_marks)*100) as highest_score,
        MIN((score/total_marks)*100) as lowest_score
    FROM exam_results 
    WHERE exam_id = ?
");
$stmt->bind_param('i', $result['exam_id']);
$stmt->execute();
$classStats = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Calculate rank
$stmt = $mysqli->prepare("
    SELECT COUNT(*) + 1 as rank
    FROM exam_results 
    WHERE exam_id = ? AND (score/total_marks) > (? / ?)
");
$stmt->bind_param('iii', $result['exam_id'], $result['score'], $result['total_marks']);
$stmt->execute();
$rankResult = $stmt->get_result()->fetch_assoc();
$rank = $rankResult['rank'];
$stmt->close();

// Time taken calculation
$examStartTime = strtotime($result['exam_datetime']);
$examSubmitTime = strtotime($result['submitted_at']);
$timeTaken = $examSubmitTime - $examStartTime;
$timeTakenFormatted = gmdate('H:i:s', $timeTaken);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Result - <?php echo htmlspecialchars($result['title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.css" rel="stylesheet">
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

        .result-header {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            overflow: hidden;
        }

        .result-banner {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .score-circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: white;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
            margin: 0 auto 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .grade-badge {
            font-size: 2rem;
            padding: 15px 25px;
            border-radius: 50px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            margin-bottom: 20px;
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
            margin: 0 auto 15px;
        }

        .question-card {
            background: white;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .question-header {
            padding: 15px 20px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: between;
            align-items: center;
        }

        .question-number {
            background: var(--primary-color);
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 15px;
        }

        .option-item {
            padding: 10px 20px;
            border-bottom: 1px solid #f8f9fa;
            display: flex;
            align-items: center;
        }

        .option-item:last-child {
            border-bottom: none;
        }

        .option-item.correct {
            background: #d4edda;
            border-left: 4px solid #28a745;
        }

        .option-item.wrong {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
        }

        .option-item.selected {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            text-transform: uppercase;
        }

        .performance-chart {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            height: 400px; /* Fixed height */
            overflow: scroll; /* Prevent overflow */
        }
        .print-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 500;
        }

        @media print {
            body {
                background: white !important;
            }
            .print-btn, .btn {
                display: none !important;
            }
            .result-header, .stat-card, .question-card, .performance-chart {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }
        }

        .comparison-bar {
            height: 20px;
            background: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            position: relative;
        }

        .comparison-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            border-radius: 10px;
            transition: width 0.5s ease;
        }

        .comparison-marker {
            position: absolute;
            top: -5px;
            width: 2px;
            height: 30px;
            background: #dc3545;
            transform: translateX(-1px);
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <!-- Navigation -->
        <div class="row mb-4">
            <div class="col-12">
                <a href="<?php echo $auth->getRole() === 'student' ? 'studentdashboard.php' : ($auth->getRole() === 'staff' ? 'staffdashboard.php' : 'admindashboard.php'); ?>" class="btn btn-light">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
                <?php if ($auth->getRole() !== 'student'): ?>
                    <span class="text-white ms-3">
                        <i class="fas fa-user me-2"></i>Viewing result for: <?php echo htmlspecialchars($result['student_name']); ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Result Header -->
        <div class="result-header">
            <div class="result-banner">
                <div class="score-circle">
                    <?php echo $scorePercentage; ?>%
                </div>
                <h2 class="mb-3"><?php echo htmlspecialchars($result['title']); ?></h2>
                <h4 class="mb-3">
                    <i class="fas fa-user me-2"></i><?php echo htmlspecialchars($result['student_name']); ?>
                </h4>
                <span class="grade-badge badge bg-<?php echo $gradeInfo['class']; ?>">
                    Grade: <?php echo $gradeInfo['grade']; ?>
                </span>
            </div>
            
            <div class="p-4">
                <div class="row text-center">
                    <div class="col-md-3">
                        <h6 class="text-muted">Score</h6>
                        <h4><?php echo $result['score']; ?> / <?php echo $result['total_marks']; ?></h4>
                    </div>
                    <div class="col-md-3">
                        <h6 class="text-muted">Percentage</h6>
                        <h4><?php echo $scorePercentage; ?>%</h4>
                    </div>
                    <div class="col-md-3">
                        <h6 class="text-muted">Rank</h6>
                        <h4><?php echo $rank; ?> / <?php echo $classStats['total_attempts']; ?></h4>
                    </div>
                    <div class="col-md-3">
                        <h6 class="text-muted">Time Taken</h6>
                        <h4><?php echo $timeTakenFormatted; ?></h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background: var(--success-color);">
                        <i class="fas fa-check"></i>
                    </div>
                    <h3><?php echo $correctAnswers; ?></h3>
                    <p class="text-muted mb-0">Correct Answers</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background: var(--danger-color);">
                        <i class="fas fa-times"></i>
                    </div>
                    <h3><?php echo $wrongAnswers; ?></h3>
                    <p class="text-muted mb-0">Wrong Answers</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background: var(--warning-color);">
                        <i class="fas fa-question"></i>
                    </div>
                    <h3><?php echo $unansweredQuestions; ?></h3>
                    <p class="text-muted mb-0">Unanswered</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background: var(--info-color);">
                        <i class="fas fa-list"></i>
                    </div>
                    <h3><?php echo $totalQuestions; ?></h3>
                    <p class="text-muted mb-0">Total Questions</p>
                </div>
            </div>
        </div>

        <!-- Replace your existing Performance Comparison section with this -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="performance-chart">
                    <h5 class="mb-4"><i class="fas fa-chart-bar me-2"></i>Performance Analysis</h5>
                    <div class="chart-wrapper">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="performance-chart">
                    <h5 class="mb-4"><i class="fas fa-users me-2"></i>Class Comparison</h5>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Your Score</span>
                            <span class="fw-bold"><?php echo $scorePercentage; ?>%</span>
                        </div>
                        <div class="comparison-bar">
                            <div class="comparison-fill" style="width: <?php echo $scorePercentage; ?>%"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Class Average</span>
                            <span><?php echo round($classStats['class_average'], 1); ?>%</span>
                        </div>
                        <div class="comparison-bar">
                            <div class="comparison-fill" style="width: <?php echo $classStats['class_average']; ?>%; background: #6c757d;"></div>
                            <div class="comparison-marker" style="left: <?php echo $scorePercentage; ?>%;"></div>
                        </div>
                    </div>

                    <div class="row text-center mt-4">
                        <div class="col-6">
                            <h6 class="text-success">Highest</h6>
                            <h5><?php echo round($classStats['highest_score'], 1); ?>%</h5>
                        </div>
                        <div class="col-6">
                            <h6 class="text-danger">Lowest</h6>
                            <h5><?php echo round($classStats['lowest_score'], 1); ?>%</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Exam Details -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="performance-chart">
                    <h5 class="mb-4"><i class="fas fa-info-circle me-2"></i>Exam Details</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Exam Date:</strong> <?php echo date('d F Y, g:i A', strtotime($result['exam_datetime'])); ?></p>
                            <p><strong>Duration:</strong> <?php echo $result['duration']; ?> minutes</p>
                            <p><strong>Submitted At:</strong> <?php echo date('d F Y, g:i A', strtotime($result['submitted_at'])); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Total Questions:</strong> <?php echo $totalQuestions; ?></p>
                            <p><strong>Total Marks:</strong> <?php echo $result['total_marks']; ?></p>
                            <p><strong>Student Email:</strong> <?php echo htmlspecialchars($result['student_email']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Question-wise Analysis -->
        <div class="row">
            <div class="col-12">
                <div class="performance-chart">
                    <h5 class="mb-4"><i class="fas fa-clipboard-list me-2"></i>Question-wise Analysis</h5>
                    
                    <?php foreach ($questionAnalysis as $index => $analysis): ?>
                        <div class="question-card">
                            <div class="question-header">
                                <div class="d-flex align-items-center flex-grow-1">
                                    <div class="question-number"><?php echo $index + 1; ?></div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($analysis['question']['question_text']); ?></h6>
                                        <small class="text-muted">Marks: <?php echo $analysis['question']['marks']; ?></small>
                                    </div>
                                </div>
                                <div>
                                    <?php
                                    $statusClass = [
                                        'correct' => 'bg-success',
                                        'wrong' => 'bg-danger',
                                        'unanswered' => 'bg-warning',
                                        'subjective' => 'bg-info'
                                    ];
                                    $statusText = [
                                        'correct' => 'Correct',
                                        'wrong' => 'Wrong',
                                        'unanswered' => 'Not Answered',
                                        'subjective' => 'Subjective'
                                    ];
                                    ?>
                                    <span class="status-badge <?php echo $statusClass[$analysis['status']]; ?>">
                                        <?php echo $statusText[$analysis['status']]; ?>
                                    </span>
                                </div>
                            </div>
                            
                            <?php if ($analysis['question']['question_type'] === 'mcq'): ?>
                                <div class="question-body">
                                    <?php 
                                    $options = ['a', 'b', 'c', 'd'];
                                    foreach ($options as $optIndex => $opt): 
                                        $optionText = $analysis['question']['option_' . $opt];
                                        if (!empty($optionText)):
                                            $isCorrect = $optIndex == $analysis['question']['correct_option'];
                                            $isSelected = $analysis['student_answer'] == $optIndex;
                                            
                                            $optionClass = '';
                                            if ($isCorrect) {
                                                $optionClass = 'correct';
                                            } elseif ($isSelected && !$isCorrect) {
                                                $optionClass = 'wrong';
                                            } elseif ($isSelected) {
                                                $optionClass = 'selected';
                                            }
                                    ?>
                                        <div class="option-item <?php echo $optionClass; ?>">
                                            <div class="me-3">
                                                <?php if ($isCorrect): ?>
                                                    <i class="fas fa-check-circle text-success"></i>
                                                <?php elseif ($isSelected && !$isCorrect): ?>
                                                    <i class="fas fa-times-circle text-danger"></i>
                                                <?php elseif ($isSelected): ?>
                                                    <i class="fas fa-dot-circle text-primary"></i>
                                                <?php else: ?>
                                                    <i class="far fa-circle text-muted"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-grow-1">
                                                <strong><?php echo strtoupper($opt); ?>.</strong> <?php echo htmlspecialchars($optionText); ?>
                                                <?php if ($isCorrect): ?>
                                                    <span class="badge bg-success ms-2">Correct Answer</span>
                                                <?php endif; ?>
                                                <?php if ($isSelected && !$isCorrect): ?>
                                                    <span class="badge bg-danger ms-2">Your Answer</span>
                                                <?php elseif ($isSelected): ?>
                                                    <span class="badge bg-primary ms-2">Your Answer</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </div>
                            <?php else: ?>
                                <div class="question-body">
                                    <div class="option-item">
                                        <div class="flex-grow-1">
                                            <strong>Your Answer:</strong><br>
                                            <?php if (!empty($analysis['student_answer'])): ?>
                                                <div class="mt-2 p-3 bg-light rounded">
                                                    <?php echo nl2br(htmlspecialchars($analysis['student_answer'])); ?>
                                                </div>
                                            <?php else: ?>
                                                <em class="text-muted">No answer provided</em>
                                            <?php endif; ?>
                                            <small class="text-info d-block mt-2">
                                                <i class="fas fa-info-circle me-1"></i>
                                                This is a subjective question. Manual evaluation required.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row mt-4 mb-5">
            <div class="col-12 text-center">
                <button onclick="window.print()" class="btn btn-primary btn-lg me-3">
                    <i class="fas fa-print me-2"></i>Print Result
                </button>
                <a href="<?php echo $auth->getRole() === 'student' ? 'studentdashboard.php' : ($auth->getRole() === 'staff' ? 'staffdashboard.php' : 'admin_dashboard.php'); ?>" class="btn btn-secondary btn-lg">
                    <i class="fas fa-home me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Print Button (Fixed Position) -->
    <button onclick="window.print()" class="btn btn-primary btn-lg print-btn rounded-circle">
        <i class="fas fa-print"></i>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script>
        // Performance Chart
        const ctx = document.getElementById('performanceChart').getContext('2d');
        const performanceChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Correct', 'Wrong', 'Unanswered'],
                datasets: [{
                    data: [<?php echo $correctAnswers; ?>, <?php echo $wrongAnswers; ?>, <?php echo $unansweredQuestions; ?>],
                    backgroundColor: [
                        '#48bb78',
                        '#f56565',
                        '#ed8936'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // This is crucial
                aspectRatio: 1, // Makes it square
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            font: {
                                size: 12
                            }
                        }
                    }
                },
                layout: {
                    padding: {
                        top: 10,
                        bottom: 10
                    }
                }
            }
        });
        // Animate comparison bars
        document.addEventListener('DOMContentLoaded', function() {
            const fills = document.querySelectorAll('.comparison-fill');
            fills.forEach(fill => {
                const width = fill.style.width;
                fill.style.width = '0%';
                setTimeout(() => {
                    fill.style.width = width;
                }, 500);
            });
        });

        // Auto-scroll to first wrong answer
        function scrollToFirstWrong() {
            const wrongQuestion = document.querySelector('.question-card .status-badge.bg-danger');
            if (wrongQuestion) {
                wrongQuestion.closest('.question-card').scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
            }
        }

        // Add scroll to first wrong answer button if there are wrong answers
        <?php if ($wrongAnswers > 0): ?>
        document.addEventListener('DOMContentLoaded', function() {
            const actionButtons = document.querySelector('.row.mt-4.mb-5 .col-12');
            const scrollButton = document.createElement('button');
            scrollButton.className = 'btn btn-warning btn-lg me-3';
            scrollButton.innerHTML = '<i class="fas fa-search me-2"></i>Review Wrong Answers';
            scrollButton.onclick = scrollToFirstWrong;
            actionButtons.insertBefore(scrollButton, actionButtons.firstChild);
        });
        <?php endif; ?>
    </script>
</body>
</html>