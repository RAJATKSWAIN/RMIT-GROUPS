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

// Enhanced validation - only students can take exams
if (!$auth->isLoggedIn() || !$auth->validateUser() || $auth->getRole() !== 'student') {
    session_destroy();
    header('Location: login.php?error=access_denied');
    exit;
}

$studentId = $auth->getUserId();

// Get exam ID from URL
$examId = isset($_GET['exam_id']) ? intval($_GET['exam_id']) : 0;

if ($examId <= 0) {
    header('Location: studentdashboard.php?error=invalid_exam_id');
    exit;
}

// Fetch exam details
$stmt = $mysqli->prepare("
    SELECT e.*, 
           (SELECT COUNT(*) FROM exam_results er WHERE er.exam_id = e.id AND er.student_id = ?) as already_taken
    FROM exams e 
    WHERE e.id = ? AND e.status = 'approved'
");
$stmt->bind_param('ii', $studentId, $examId);
$stmt->execute();
$exam = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$exam) {
    header('Location: studentdashboard.php?error=exam_not_found');
    exit;
}

// Check if student has already taken this exam
if ($exam['already_taken'] > 0) {
    header('Location: exam_result.php?exam_id=' . $examId . '&student_id=' . $studentId);
    exit;
}

// Check if exam time has started
$currentTime = time();
$examStartTime = strtotime($exam['exam_datetime']);

if ($currentTime < $examStartTime) {
    header('Location: studentdashboard.php?error=exam_not_started');
    exit;
}

// Fetch questions for this exam
$stmt = $mysqli->prepare("
    SELECT id, question_text, option_a, option_b, option_c, option_d, marks, question_type
    FROM questions 
    WHERE exam_id = ? 
    ORDER BY id ASC
");
$stmt->bind_param('i', $examId);
$stmt->execute();
$questions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if (empty($questions)) {
    header('Location: studentdashboard.php?error=no_questions');
    exit;
}

// Handle exam submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_exam') {
    $submittedAnswers = $_POST['answers'] ?? [];
    $totalScore = 0;
    $totalMarks = 0;

    // Calculate score for MCQ questions
    foreach ($questions as $question) {
        $totalMarks += $question['marks'];
        $questionId = $question['id'];
        
        if ($question['question_type'] === 'mcq') {
            // Fetch correct answer for this question
            $stmt = $mysqli->prepare("SELECT correct_option FROM questions WHERE id = ?");
            $stmt->bind_param('i', $questionId);
            $stmt->execute();
            $correctAnswer = $stmt->get_result()->fetch_assoc()['correct_option'];
            $stmt->close();
            
            if (isset($submittedAnswers[$questionId]) && 
                (string)$submittedAnswers[$questionId] === (string)$correctAnswer) {
                $totalScore += $question['marks'];
            }
        }
        // Subjective questions need manual grading, so no automatic scoring
    }

    // Store the exam result
    $answersJson = json_encode($submittedAnswers, JSON_UNESCAPED_UNICODE);
    $stmt = $mysqli->prepare("
        INSERT INTO exam_results (student_id, exam_id, answers, score, total_marks, submitted_at) 
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param('iisii', $studentId, $examId, $answersJson, $totalScore, $totalMarks);
    
    if ($stmt->execute()) {
        $stmt->close();
        // Redirect to exam result page
        header('Location: exam_result.php?exam_id=' . $examId . '&student_id=' . $studentId);
        exit;
    } else {
        $stmt->close();
        $error = "Failed to submit exam. Please try again.";
    }
}

// Calculate remaining time
$timeElapsed = $currentTime - $examStartTime;
$remainingTime = ($exam['duration'] * 60) - $timeElapsed;

if ($remainingTime <= 0) {
    // Time's up, auto-submit with current answers
    header('Location: studentdashboard.php?error=time_expired');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Exam - <?php echo htmlspecialchars($exam['title']); ?></title>
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

        .exam-header {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            padding: 25px;
        }

        .timer-card {
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--danger-color);
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 1.2rem;
            z-index: 1000;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .question-card {
            background: white;
            border-radius: 15px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .question-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 20px;
        }

        .question-number {
            background: rgba(255,255,255,0.2);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 15px;
        }

        .question-body {
            padding: 25px;
        }

        .option-item {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .option-item:hover {
            border-color: var(--primary-color);
            background: #e3f2fd;
        }

        .option-item.selected {
            border-color: var(--primary-color);
            background: #e3f2fd;
            box-shadow: 0 3px 10px rgba(102, 126, 234, 0.3);
        }

        .option-radio {
            margin-right: 15px;
            transform: scale(1.2);
        }

        .submit-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-top: 30px;
        }

        .progress-indicator {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .progress-bar-custom {
            height: 10px;
            border-radius: 5px;
            background: var(--primary-color);
            transition: width 0.3s;
        }

        .subjective-textarea {
            width: 100%;
            min-height: 120px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 15px;
            font-size: 16px;
            resize: vertical;
        }

        .subjective-textarea:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        @media (max-width: 768px) {
            .timer-card {
                position: relative;
                top: auto;
                right: auto;
                margin-bottom: 20px;
                text-align: center;
            }
        }

        .warning-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            z-index: 2000;
        }

        .warning-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            max-width: 400px;
        }
    </style>
</head>
<body>
    <!-- Timer -->
    <div class="timer-card" id="timer">
        <i class="fas fa-clock me-2"></i>
        <span id="timeRemaining">Loading...</span>
    </div>

    <!-- Warning Modal -->
    <div class="warning-modal" id="warningModal">
        <div class="warning-content">
            <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
            <h4>Time Warning!</h4>
            <p>Only <span id="warningTime"></span> minutes remaining!</p>
            <button class="btn btn-primary" onclick="closeWarning()">Continue</button>
        </div>
    </div>

    <div class="container mt-4">
        <!-- Exam Header -->
        <div class="exam-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="mb-2"><?php echo htmlspecialchars($exam['title']); ?></h2>
                    <p class="text-muted mb-0">
                        <i class="fas fa-user me-2"></i><?php echo htmlspecialchars($auth->getUserName()); ?>
                        <span class="ms-3"><i class="fas fa-calendar me-2"></i><?php echo date('d F Y, g:i A', strtotime($exam['exam_datetime'])); ?></span>
                        <span class="ms-3"><i class="fas fa-clock me-2"></i><?php echo $exam['duration']; ?> minutes</span>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <h5 class="text-muted">Total Questions: <?php echo count($questions); ?></h5>
                    <h5 class="text-muted">Total Marks: <?php echo array_sum(array_column($questions, 'marks')); ?></h5>
                </div>
            </div>
        </div>

        <!-- Progress Indicator -->
        <div class="progress-indicator">
            <div class="d-flex justify-content-between mb-2">
                <span>Progress</span>
                <span id="progressText">0 / <?php echo count($questions); ?> answered</span>
            </div>
            <div class="progress">
                <div class="progress-bar-custom" id="progressBar" style="width: 0%"></div>
            </div>
        </div>

        <!-- Exam Form -->
        <form method="POST" id="examForm">
            <input type="hidden" name="action" value="submit_exam">
            
            <?php foreach ($questions as $index => $question): ?>
                <div class="question-card" data-question="<?php echo $index + 1; ?>">
                    <div class="question-header">
                        <div class="d-flex align-items-center">
                            <div class="question-number"><?php echo $index + 1; ?></div>
                            <div class="flex-grow-1">
                                <h5 class="mb-1">Question <?php echo $index + 1; ?></h5>
                                <small>Marks: <?php echo $question['marks']; ?> | Type: <?php echo ucfirst($question['question_type']); ?></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="question-body">
                        <h6 class="mb-4"><?php echo htmlspecialchars($question['question_text']); ?></h6>
                        
                        <?php if ($question['question_type'] === 'mcq'): ?>
                            <?php 
                            $options = ['a', 'b', 'c', 'd'];
                            foreach ($options as $optIndex => $opt): 
                                $optionText = $question['option_' . $opt];
                                if (!empty($optionText)):
                            ?>
                                <div class="option-item" onclick="selectOption(<?php echo $question['id']; ?>, <?php echo $optIndex; ?>)">
                                    <input 
                                        type="radio" 
                                        name="answers[<?php echo $question['id']; ?>]" 
                                        id="q<?php echo $question['id']; ?>_opt<?php echo $optIndex; ?>" 
                                        value="<?php echo $optIndex; ?>"
                                        class="option-radio"
                                        onchange="updateProgress()"
                                    >
                                    <label for="q<?php echo $question['id']; ?>_opt<?php echo $optIndex; ?>" class="mb-0">
                                        <strong><?php echo strtoupper($opt); ?>.</strong> <?php echo htmlspecialchars($optionText); ?>
                                    </label>
                                </div>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        <?php else: ?>
                            <textarea 
                                name="answers[<?php echo $question['id']; ?>]" 
                                class="subjective-textarea"
                                placeholder="Write your answer here..."
                                onchange="updateProgress()"
                            ></textarea>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Submit Section -->
            <div class="submit-section">
                <h4 class="mb-3">Ready to Submit?</h4>
                <p class="text-muted mb-4">Please review your answers before submitting. You cannot change your answers after submission.</p>
                <button type="button" class="btn btn-warning btn-lg me-3" onclick="reviewAnswers()">
                    <i class="fas fa-eye me-2"></i>Review Answers
                </button>
                <button type="submit" class="btn btn-success btn-lg" onclick="return confirmSubmit()">
                    <i class="fas fa-paper-plane me-2"></i>Submit Exam
                </button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Timer functionality
        let remainingSeconds = <?php echo $remainingTime; ?>;
        let warningShown = false;

        function updateTimer() {
            if (remainingSeconds <= 0) {
                // Auto-submit when time is up
                document.getElementById('examForm').submit();
                return;
            }

            const hours = Math.floor(remainingSeconds / 3600);
            const minutes = Math.floor((remainingSeconds % 3600) / 60);
            const seconds = remainingSeconds % 60;

            const timeString = hours > 0 
                ? `${hours}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
                : `${minutes}:${seconds.toString().padStart(2, '0')}`;

            document.getElementById('timeRemaining').textContent = timeString;

            // Show warning when 5 minutes remaining
            if (remainingSeconds === 300 && !warningShown) {
                showTimeWarning(5);
                warningShown = true;
            }

            // Change timer color when less than 5 minutes
            const timerElement = document.getElementById('timer');
            if (remainingSeconds <= 300) {
                timerElement.style.background = '#f56565';
            } else if (remainingSeconds <= 600) {
                timerElement.style.background = '#ed8936';
            }

            remainingSeconds--;
        }

        function showTimeWarning(minutes) {
            document.getElementById('warningTime').textContent = minutes;
            document.getElementById('warningModal').style.display = 'block';
        }

        function closeWarning() {
            document.getElementById('warningModal').style.display = 'none';
        }

        // Start timer
        updateTimer();
        setInterval(updateTimer, 1000);

        // Option selection
        function selectOption(questionId, optionIndex) {
            const radio = document.getElementById(`q${questionId}_opt${optionIndex}`);
            radio.checked = true;
            
            // Update visual selection
            const questionCard = radio.closest('.question-card');
            const options = questionCard.querySelectorAll('.option-item');
            options.forEach(opt => opt.classList.remove('selected'));
            radio.closest('.option-item').classList.add('selected');
            
            updateProgress();
        }

        // Progress tracking
        function updateProgress() {
            const totalQuestions = <?php echo count($questions); ?>;
            const answeredQuestions = document.querySelectorAll('input[type="radio"]:checked, textarea:not(:empty)').length;
            
            const percentage = (answeredQuestions / totalQuestions) * 100;
            document.getElementById('progressBar').style.width = percentage + '%';
            document.getElementById('progressText').textContent = `${answeredQuestions} / ${totalQuestions} answered`;
        }

        // Review answers
        function reviewAnswers() {
            const unanswered = [];
            const questionCards = document.querySelectorAll('.question-card');
            
            questionCards.forEach((card, index) => {
                const questionNum = index + 1;
                const radio = card.querySelector('input[type="radio"]:checked');
                const textarea = card.querySelector('textarea');
                
                if (!radio && (!textarea || textarea.value.trim() === '')) {
                    unanswered.push(questionNum);
                }
            });

            if (unanswered.length > 0) {
                alert(`You have not answered questions: ${unanswered.join(', ')}\n\nPlease review before submitting.`);
                // Scroll to first unanswered question
                const firstUnanswered = document.querySelector(`[data-question="${unanswered[0]}"]`);
                firstUnanswered.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                alert('All questions have been answered. You can now submit your exam.');
            }
        }

        // Confirm submission
        function confirmSubmit() {
            return confirm('Are you sure you want to submit your exam? You cannot change your answers after submission.');
        }

        // Prevent accidental page refresh
        window.addEventListener('beforeunload', function(e) {
            e.preventDefault();
            e.returnValue = 'Are you sure you want to leave? Your progress will be lost.';
        });

        // Remove beforeunload when form is submitted
        document.getElementById('examForm').addEventListener('submit', function() {
            window.removeEventListener('beforeunload', function() {});
        });

        // Auto-save functionality (optional)
        function autoSave() {
            const formData = new FormData(document.getElementById('examForm'));
            const answers = {};
            
            for (let [key, value] of formData.entries()) {
                if (key.startsWith('answers[')) {
                    answers[key] = value;
                }
            }
            
            localStorage.setItem('exam_<?php echo $examId; ?>_answers', JSON.stringify(answers));
        }

        // Load saved answers
        function loadSavedAnswers() {
            const saved = localStorage.getItem('exam_<?php echo $examId; ?>_answers');
            if (saved) {
                const answers = JSON.parse(saved);
                for (let [key, value] of Object.entries(answers)) {
                    const input = document.querySelector(`[name="${key}"]`);
                    if (input) {
                        if (input.type === 'radio') {
                            const radio = document.querySelector(`[name="${key}"][value="${value}"]`);
                            if (radio) {
                                radio.checked = true;
                                radio.closest('.option-item').classList.add('selected');
                            }
                        } else {
                            input.value = value;
                        }
                    }
                }
                updateProgress();
            }
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadSavedAnswers();
            
            // Auto-save every 30 seconds
            setInterval(autoSave, 30000);
            
            // Save on input change
            document.addEventListener('change', autoSave);
        });
    </script>
</body>
</html>