<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');

require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';

checkLogin();

// 1. Sanitize the ID to prevent SQL Injection
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id === 0) {
    die("Invalid Student ID.");
}

// 2. Fetch data with a JOIN for course name
$query = $conn->query("
    SELECT S.*, C.COURSE_NAME 
    FROM STUDENTS S
    JOIN COURSES C ON S.COURSE_ID = C.COURSE_ID
    WHERE S.STUDENT_ID = $id
");

$s = $query->fetch_assoc();

if (!$s) {
    die("Student record not found.");
}
?>

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<?php include BASE_PATH.'/admin/layout/sidebar.php'; ?>

<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary"><i class="bi bi-person-badge"></i> Student Information</h5>
                    <a href="list.php" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Back to List
                    </a>
                </div>
                
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <h3 class="mb-1"><?= htmlspecialchars($s['FIRST_NAME'].' '.$s['LAST_NAME']) ?></h3>
                        <span class="badge <?= $s['STATUS'] == 'A' ? 'bg-success' : 'bg-danger' ?>">
                            <?= $s['STATUS'] == 'A' ? 'Active Account' : 'Disabled' ?>
                        </span>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="text-muted small text-uppercase fw-bold border-bottom pb-2">Academic Profile</h6>
                            <table class="table table-sm table-borderless mt-2">
                                <tr><th class="ps-0 w-50">Registration No:</th><td class="text-dark fw-bold"><?= htmlspecialchars($s['REGISTRATION_NO']) ?></td></tr>
                                <tr><th class="ps-0">Roll No:</th><td><?= htmlspecialchars($s['ROLL_NO']) ?></td></tr>
                                <tr><th class="ps-0">Course:</th><td><?= htmlspecialchars($s['COURSE_NAME']) ?></td></tr>
                                <tr><th class="ps-0">Current Semester:</th><td>Semester <?= $s['SEMESTER'] ?? '1' ?></td></tr>
                                <tr><th class="ps-0">Admission Date:</th><td><?= date('d M Y', strtotime($s['ADMISSION_DATE'] ?? 'now')) ?></td></tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-muted small text-uppercase fw-bold border-bottom pb-2">Contact Details</h6>
                            <table class="table table-sm table-borderless mt-2">
                                <tr><th class="ps-0 w-40">Mobile:</th><td><?= htmlspecialchars($s['MOBILE']) ?></td></tr>
                                <tr><th class="ps-0">Email:</th><td><?= htmlspecialchars($s['EMAIL']) ?></td></tr>
                                <tr><th class="ps-0">Gender:</th><td><?= ucfirst(strtolower($s['GENDER'])) ?></td></tr>
                                <tr><th class="ps-0">Date of Birth:</th><td><?= !empty($s['DOB']) ? date('d M Y', strtotime($s['DOB'])) : 'N/A' ?></td></tr>
                            </table>
                        </div>

                        <div class="col-12">
                            <h6 class="text-muted small text-uppercase fw-bold border-bottom pb-2">Residential Address</h6>
                            <p class="mt-2 mb-0 text-dark">
                                <?= htmlspecialchars($s['ADDRESS']) ?><br>
                                <?= htmlspecialchars($s['CITY']) ?>, <?= htmlspecialchars($s['STATE']) ?> - <?= htmlspecialchars($s['PINCODE']) ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer bg-light text-center py-3">
                    <small class="text-muted italic">This is a read-only system record. Profile last updated on: <?= date('d M Y', strtotime($s['CREATED_AT'] ?? 'now')) ?></small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include BASE_PATH.'/admin/layout/footer.php'; ?>