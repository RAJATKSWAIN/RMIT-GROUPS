<?php
// reset_password.php
session_start();
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

$message = '';
$messageType = '';
$validToken = false;
$email = '';

// Check if token is provided and valid
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    // Verify token
    $stmt = $mysqli->prepare("SELECT email, user_type, expires_at FROM password_resets WHERE token = ? AND expires_at > NOW()");
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $resetData = $result->fetch_assoc();
        $validToken = true;
        $email = $resetData['email'];
        $userType = $resetData['user_type'];
    } else {
        $message = 'Invalid or expired reset token. Please request a new password reset.';
        $messageType = 'danger';
    }
    $stmt->close();
} else {
    $message = 'No reset token provided.';
    $messageType = 'danger';
}

// Handle password reset form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validToken) {
    $newPassword = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (empty($newPassword) || empty($confirmPassword)) {
        $message = 'Please fill in all fields.';
        $messageType = 'danger';
    } elseif (strlen($newPassword) < 6) {
        $message = 'Password must be at least 6 characters long.';
        $messageType = 'danger';
    } elseif ($newPassword !== $confirmPassword) {
        $message = 'Passwords do not match.';
        $messageType = 'danger';
    } else {
        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        // Update password in appropriate table
        $table = $userType === 'student' ? 'student' : 'staff';
        $stmt = $mysqli->prepare("UPDATE $table SET password = ? WHERE email = ?");
        $stmt->bind_param('ss', $hashedPassword, $email);
        
        if ($stmt->execute()) {
            // Delete the used reset token
            $deleteStmt = $mysqli->prepare("DELETE FROM password_resets WHERE token = ?");
            $deleteStmt->bind_param('s', $token);
            $deleteStmt->execute();
            $deleteStmt->close();
            
            $message = 'Password has been reset successfully. You can now login with your new password.';
            $messageType = 'success';
            $validToken = false; // Hide the form
        } else {
            $message = 'Failed to update password. Please try again.';
            $messageType = 'danger';
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Online Exam System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
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
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .form-control {
            border-radius: 25px;
            padding: 12px 20px;
            padding-right: 50px; /* Make room for toggle button */
            border: 2px solid #e9ecef;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .back-link {
            color: #667eea;
            text-decoration: none;
        }
        .back-link:hover {
            color: #764ba2;
            text-decoration: underline;
        }
        
        /* Fixed password toggle positioning */
        .password-input-wrapper {
            position: relative;
        }
        
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            padding: 8px;
            color: #6c757d;
            cursor: pointer;
            z-index: 10;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s ease;
        }
        
        .toggle-password:hover {
            background-color: rgba(0,0,0,0.05);
            color: #495057;
        }
        
        .toggle-password:focus {
            outline: 2px solid #667eea;
            outline-offset: 2px;
        }
        
        .password-strength {
            font-size: 0.8rem;
            margin-top: 5px;
        }
        .strength-weak { color: #dc3545; }
        .strength-medium { color: #ffc107; }
        .strength-strong { color: #28a745; }
        
        /* Responsive adjustments */
        @media (max-width: 576px) {
            .card-header {
                padding: 1.5rem 1rem;
            }
            .card-header h3 {
                font-size: 1.5rem;
            }
            .card-body {
                padding: 1.5rem !important;
            }
            .form-control {
                padding: 10px 15px;
                padding-right: 45px;
                font-size: 16px; /* Prevents zoom on iOS */
            }
            .toggle-password {
                right: 10px;
                width: 28px;
                height: 28px;
            }
        }
        
        @media (max-width: 400px) {
            .container {
                padding: 10px;
            }
            .form-control {
                padding: 8px 12px;
                padding-right: 40px;
            }
            .toggle-password {
                right: 8px;
                width: 24px;
                height: 24px;
            }
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
                            <i class="fas fa-lock me-2"></i>
                            Reset Password
                        </h3>
                        <p class="mb-0 mt-2">Enter your new password</p>
                    </div>
                    <div class="card-body p-4">
                        <?php if (!empty($message)): ?>
                            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                                <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
                                <?php echo htmlspecialchars($message); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if ($validToken): ?>
                            <div class="mb-3">
                                <p class="text-muted">
                                    <i class="fas fa-user me-2"></i>
                                    Resetting password for: <strong><?php echo htmlspecialchars($email); ?></strong>
                                </p>
                            </div>

                            <form method="POST" action="" id="resetForm">
                                <div class="mb-3">
                                    <label for="password" class="form-label">
                                        <i class="fas fa-key me-2"></i>New Password
                                    </label>
                                    <div class="password-input-wrapper">
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter new password" required minlength="6">
                                        <button type="button" class="toggle-password" onclick="togglePasswordVisibility('password', 'eyeIcon')" aria-label="Toggle password visibility">
                                            <i class="fas fa-eye" id="eyeIcon"></i>
                                        </button>
                                    </div>
                                    <div id="passwordStrength" class="password-strength"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">
                                        <i class="fas fa-check-double me-2"></i>Confirm Password
                                    </label>
                                    <div class="password-input-wrapper">
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required minlength="6">
                                        <button type="button" class="toggle-password" onclick="togglePasswordVisibility('confirm_password', 'eyeConfirm')" aria-label="Toggle confirm password visibility">
                                            <i class="fas fa-eye" id="eyeConfirm"></i>
                                        </button>
                                    </div>
                                    <div id="passwordMatch" class="password-strength"></div>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-key me-2"></i>Reset Password
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>

                        <div class="text-center mt-3">
                            <p class="mb-0">
                                <a href="login.php" class="back-link">
                                    <i class="fas fa-arrow-left me-1"></i>Back to Login
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility function
        function togglePasswordVisibility(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const eyeIcon = document.getElementById(iconId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }

        // Password strength checker
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthDiv = document.getElementById('passwordStrength');
            
            if (password.length === 0) {
                strengthDiv.innerHTML = '';
                return;
            }
            
            let strength = 0;
            let feedback = [];
            
            if (password.length >= 8) strength++;
            else feedback.push('at least 8 characters');
            
            if (/[a-z]/.test(password)) strength++;
            else feedback.push('lowercase letter');
            
            if (/[A-Z]/.test(password)) strength++;
            else feedback.push('uppercase letter');
            
            if (/[0-9]/.test(password)) strength++;
            else feedback.push('number');
            
            if (/[^a-zA-Z0-9]/.test(password)) strength++;
            else feedback.push('special character');
            
            let strengthText = '';
            let strengthClass = '';
            
            if (strength < 2) {
                strengthText = 'Weak';
                strengthClass = 'strength-weak';
            } else if (strength < 4) {
                strengthText = 'Medium';
                strengthClass = 'strength-medium';
            } else {
                strengthText = 'Strong';
                strengthClass = 'strength-strong';
            }
            
            strengthDiv.innerHTML = `<span class="${strengthClass}">Password strength: ${strengthText}</span>`;
            if (feedback.length > 0 && strength < 4) {
                strengthDiv.innerHTML += `<br><small>Add: ${feedback.join(', ')}</small>`;
            }
        });

        // Password match checker
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            const matchDiv = document.getElementById('passwordMatch');
            
            if (confirmPassword.length === 0) {
                matchDiv.innerHTML = '';
                return;
            }
            
            if (password === confirmPassword) {
                matchDiv.innerHTML = '<span class="strength-strong"><i class="fas fa-check me-1"></i>Passwords match</span>';
            } else {
                matchDiv.innerHTML = '<span class="strength-weak"><i class="fas fa-times me-1"></i>Passwords do not match</span>';
            }
        });

        // Form validation
        document.getElementById('resetForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long!');
                return false;
            }
        });

        // Prevent form submission on toggle button click
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
            });
        });
    </script>
</body>
</html>