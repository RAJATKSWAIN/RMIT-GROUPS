<?php
// forgot_password.php
session_start();
date_default_timezone_set('Asia/Kolkata');

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Email configuration - Update these with your actual email settings
$emailConfig = [
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_secure' => PHPMailer::ENCRYPTION_STARTTLS,
    'smtp_username' => 'onlineexaminationsystem01@gmail.com',
    'smtp_password' => 'xhst nibi btlq obuz',
    'from_email' => 'onlineexaminationsystem01@gmail.com',
    'from_name' => 'Online Exam System',
    'use_phpmailer' => true
];

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

$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $message = 'Please enter your email address.';
        $messageType = 'danger';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address.';
        $messageType = 'danger';
    } else {
        // Check if email exists in students or teachers table
        $userFound = false;
        $userType = '';
        $userName = '';
        
        // Check students table
        $stmt = $mysqli->prepare("SELECT id, name FROM student WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $userFound = true;
            $userType = 'student';
            $userName = $user['name'];
            $userId = $user['id'];
        }
        $stmt->close();
        
        // Check teachers table if not found in students
        if (!$userFound) {
            $stmt = $mysqli->prepare("SELECT id, name FROM staff WHERE email = ?");
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                $userFound = true;
                $userType = 'staff';
                $userName = $user['name'];
                $userId = $user['id'];
            }
            $stmt->close();
        }
        
        if ($userFound) {
            // Generate reset token
            $resetToken = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Store reset token in database
            $stmt = $mysqli->prepare("INSERT INTO password_resets (email, token, user_type, expires_at, created_at) VALUES (?, ?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE token = ?, expires_at = ?, created_at = NOW()");
            $stmt->bind_param('ssssss', $email, $resetToken, $userType, $expiry, $resetToken, $expiry);
            $stmt->execute();
            $stmt->close();
            
            // FIXED: Proper URL construction to avoid encoding issues
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            
            // Get the directory path and ensure it uses forward slashes
            $scriptDir = dirname($_SERVER['PHP_SELF']);
            $scriptDir = str_replace('\\', '/', $scriptDir); // Convert backslashes to forward slashes
            $scriptDir = rtrim($scriptDir, '/'); // Remove trailing slash if present
            
            // Construct the reset URL properly
            $resetUrl = $protocol . '://' . $host . $scriptDir . '/reset_password.php?token=' . $resetToken;
            
            if (sendResetEmail($email, $userName, $resetUrl, $emailConfig)) {
                $message = 'Password reset link has been sent to your email address.';
                $messageType = 'success';
            } else {
                $message = 'Failed to send reset email. Please try again later.';
                $messageType = 'danger';
            }
        } else {
            $message = 'No account found with that email address.';
            $messageType = 'danger';
        }
    }
}

// Enhanced email sending function with PHPMailer
function sendResetEmail($email, $name, $resetUrl, $config) {
    if (!isset($config['use_phpmailer']) || !$config['use_phpmailer']) {
        return sendResetEmailDev($email, $name, $resetUrl);
    }
    
    // Production mode - use PHPMailer
    try {
        $mail = new PHPMailer(true);
        
        // Server settings
        $mail->isSMTP();
        $mail->Host = $config['smtp_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['smtp_username'];
        $mail->Password = $config['smtp_password'];
        $mail->SMTPSecure = $config['smtp_secure'];
        $mail->Port = $config['smtp_port'];
        
        // Recipients
        $mail->setFrom($config['from_email'], $config['from_name']);
        $mail->addAddress($email, $name);
        $mail->addReplyTo($config['from_email'], $config['from_name']);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset - Online Exam System';
        $mail->Body = getEmailTemplate($name, $resetUrl);
        $mail->AltBody = getPlainTextEmail($name, $resetUrl);
        
        // Send email
        $result = $mail->send();
        
        // Log successful email sending
        logEmailActivity($email, $name, $resetUrl, 'SUCCESS');
        
        return $result;
        
    } catch (Exception $e) {
        // Log error
        error_log("PHPMailer Error: " . $mail->ErrorInfo);
        logEmailActivity($email, $name, $resetUrl, 'ERROR: ' . $mail->ErrorInfo);
        
        // Fallback to development mode if PHPMailer fails
        return sendResetEmailDev($email, $name, $resetUrl);
    }
}

// Development mode email function (saves to file)
function sendResetEmailDev($email, $name, $resetUrl) {
    $logFile = 'password_reset_links.txt';
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "=== PASSWORD RESET REQUEST (DEV MODE) ===\n";
    $logEntry .= "Time: {$timestamp}\n";
    $logEntry .= "Email: {$email}\n";
    $logEntry .= "Name: {$name}\n";
    $logEntry .= "Reset URL: {$resetUrl}\n";
    $logEntry .= "Expires: " . date('Y-m-d H:i:s', strtotime('+1 hour')) . "\n";
    $logEntry .= "==========================================\n\n";
    
    // Save to file
    return file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX) !== false;
}

// Email activity logging function
function logEmailActivity($email, $name, $resetUrl, $status) {
    $logFile = 'email_activity.log';
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[{$timestamp}] Email: {$email} | Name: {$name} | Status: {$status}\n";
    
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

// HTML email template function
function getEmailTemplate($name, $resetUrl) {
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Password Reset</title>
        <style>
            body { 
                font-family: Arial, sans-serif; 
                line-height: 1.6; 
                color: #333; 
                margin: 0; 
                padding: 0; 
                background-color: #f4f4f4; 
            }
            .container { 
                max-width: 600px; 
                margin: 0 auto; 
                padding: 20px; 
                background-color: #ffffff; 
            }
            .header { 
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                color: white; 
                padding: 30px; 
                text-align: center; 
                border-radius: 10px 10px 0 0; 
            }
            .content { 
                background: #f9f9f9; 
                padding: 30px; 
                border-radius: 0 0 10px 10px; 
            }
            .button { 
                display: inline-block; 
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                color: white !important; 
                padding: 15px 30px; 
                text-decoration: none; 
                border-radius: 25px; 
                margin: 20px 0; 
                font-weight: bold;
            }
            .button:hover {
                opacity: 0.9;
            }
            .url-box {
                background: #fff; 
                padding: 15px; 
                border-radius: 5px; 
                word-break: break-all;
                border: 1px solid #ddd;
                font-family: monospace;
                font-size: 14px;
            }
            .footer { 
                margin-top: 30px; 
                padding-top: 20px; 
                border-top: 1px solid #ddd; 
                font-size: 12px; 
                color: #666; 
                text-align: center;
            }
            .warning {
                background: #fff3cd;
                border: 1px solid #ffeaa7;
                color: #856404;
                padding: 15px;
                border-radius: 5px;
                margin: 15px 0;
            }
            @media only screen and (max-width: 600px) {
                .container {
                    padding: 10px;
                }
                .header, .content {
                    padding: 20px;
                }
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2 style='margin: 0;'>🔐 Password Reset Request</h2>
            </div>
            <div class='content'>
                <p>Dear " . htmlspecialchars($name) . ",</p>
                <p>You have requested to reset your password for the Online Exam System.</p>
                <p>Click the button below to reset your password:</p>
                <p style='text-align: center;'>
                    <a href='" . htmlspecialchars($resetUrl) . "' class='button'>Reset Password</a>
                </p>
                <p>Or copy and paste this URL into your browser:</p>
                <div class='url-box'>" . htmlspecialchars($resetUrl) . "</div>
                
                <div class='warning'>
                    <strong>⏰ Important:</strong> This link will expire in 1 hour for security reasons.
                </div>
                <p><strong>Security Notice:</strong></p>
                <ul>
                    <li>If you did not request this password reset, please ignore this email</li>
                    <li>Never share this reset link with anyone</li>
                    <li>Choose a strong, unique password when resetting</li>
                </ul>
            </div>
            <div class='footer'>
                <p><strong>Best regards,<br>Online Exam System Team</strong></p>
                <p>This is an automated message. Please do not reply to this email.</p>
                <p style='font-size: 10px; margin-top: 15px;'>
                    If you're having trouble with the button above, copy and paste the URL into your web browser.
                </p>
            </div>
        </div>
    </body>
    </html>
    ";
}

// Plain text email template (fallback)
function getPlainTextEmail($name, $resetUrl) {
    return "
Password Reset - Online Exam System

Dear " . $name . ",

You have requested to reset your password for the Online Exam System.

Please visit the following URL to reset your password:
" . $resetUrl . "

IMPORTANT: This link will expire in 1 hour for security reasons.

Security Notice:
- If you did not request this password reset, please ignore this email
- Never share this reset link with anyone
- Choose a strong, unique password when resetting

Best regards,
Online Exam System Team

This is an automated message. Please do not reply to this email.
    ";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Online Exam System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: url('background.jpg') no-repeat center center fixed;
            min-height: 100vh;
            display:flex;
            align-items: center;
            position: relative;
            background-size: cover;
            background-position: center;
        }
        .card {
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: none;
            border-radius: 15px;
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            text-align: center;
            padding: 2rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .form-control {
            border-radius: 25px;
            padding: 12px 20px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .back-link {
            color: #667eea;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .back-link:hover {
            color: #764ba2;
            text-decoration: underline;
        }
        .alert {
            border-radius: 15px;
            border: none;
        }
        .loading-spinner {
            display: none;
        }
        .btn-primary:disabled {
            opacity: 0.7;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">
                            <i class="fas fa-key me-2"></i>
                            Forgot Password
                        </h3>
                        <p class="mb-0 mt-2">Enter your email to reset your password</p>
                    </div>
                    <div class="card-body p-4">
                        <?php if (!empty($message)): ?>
                            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                                <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
                                <?php echo htmlspecialchars($message); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="" id="resetForm">
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-2"></i>Email Address
                                </label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       placeholder="Enter your email address" required 
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                                <div class="form-text">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        We'll send a reset link to this email address
                                    </small>
                                </div>
                            </div>

                            <div class="d-grid gap-2 mb-3">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                    <span class="button-text">
                                        <i class="fas fa-paper-plane me-2"></i>
                                        Send Reset Link
                                    </span>
                                    <span class="loading-spinner">
                                        <i class="fas fa-spinner fa-spin me-2"></i>
                                        Sending...
                                    </span>
                                </button>
                            </div>
                        </form>

                        <div class="text-center">
                            <p class="mb-0">
                                Remember your password? 
                                <a href="login.php" class="back-link">
                                    <i class="fas fa-arrow-left me-1"></i>Back to Login
                                </a>
                            </p>
                        </div>
                        
                       <div class="mt-4">
                            <div class="card bg-light">
                                <div class="card-body p-3">
                                    <small class="text-muted">
                                        <i class="fas fa-shield-alt me-1"></i>
                                        <strong>Security Notice:</strong> Reset links expire in 1 hour. 
                                        <br>
                                        📧 If you don't receive an email, check your spam folder.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add loading state to form submission
        document.getElementById('resetForm').addEventListener('submit', function() {
            const submitBtn = document.getElementById('submitBtn');
            const buttonText = submitBtn.querySelector('.button-text');
            const loadingSpinner = submitBtn.querySelector('.loading-spinner');
            
            submitBtn.disabled = true;
            buttonText.style.display = 'none';
            loadingSpinner.style.display = 'inline';
        });
        
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>