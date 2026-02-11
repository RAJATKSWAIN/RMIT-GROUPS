<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');

require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';

checkLogin();

// Sanitize the ID from URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if($id == 0) {
    header("Location: list.php");
    exit;
}

// Handle Form Submission
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $stmt = $conn->prepare("
        UPDATE STUDENTS SET 
        FIRST_NAME=?, LAST_NAME=?, MOBILE=?, EMAIL=? 
        WHERE STUDENT_ID=?");

    $stmt->bind_param("ssssi", 
        $_POST['fname'], 
        $_POST['lname'], 
        $_POST['mobile'], 
        $_POST['email'], 
        $id
    );

    if($stmt->execute()){
        // Optional: Add a success message to session here
        header("Location: list.php?msg=updated");
        exit;
    }
}

// Fetch current student data
$res = $conn->query("SELECT * FROM STUDENTS WHERE STUDENT_ID=$id");
if($res->num_rows == 0) {
    die("Student not found.");
}
$s = $res->fetch_assoc();
?>

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<?php include BASE_PATH.'/admin/layout/slider.php'; ?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb small">
                    <li class="breadcrumb-item"><a href="list.php">Students</a></li>
                    <li class="breadcrumb-item active">Edit Profile</li>
                </ol>
            </nav>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-primary">✏️ Edit Student Details</h5>
                    <small class="text-muted">Updating record for: <strong><?= $s['REGISTRATION_NO'] ?></strong></small>
                </div>
                
                <div class="card-body p-4">
                    <form method="post">
                        
                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <label class="form-label small fw-bold">First Name</label>
                                <input type="text" name="fname" value="<?= htmlspecialchars($s['FIRST_NAME']) ?>" class="form-control" required>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label class="form-label small fw-bold">Last Name</label>
                                <input type="text" name="lname" value="<?= htmlspecialchars($s['LAST_NAME']) ?>" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Mobile Number</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-phone"></i></span>
                                <input type="text" name="mobile" value="<?= htmlspecialchars($s['MOBILE']) ?>" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" value="<?= htmlspecialchars($s['EMAIL']) ?>" class="form-control">
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i> Update Record
                            </button>
                            <a href="list.php" class="btn btn-light">Cancel</a>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include BASE_PATH.'/admin/layout/footer.php'; ?>