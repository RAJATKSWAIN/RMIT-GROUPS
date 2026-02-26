<!--======================================================
    File Name   : edit.php
    Project     : RMIT Groups - FMS - Fees Management System
    Module      : STUDENT MANAGEMENT
    Description : Student Registration & Profile Management
    Developed By: TrinityWebEdge
    Date Created: 06-02-2026
    Last Updated: 25-02-2026
    Note        : This page defines the FMS - Fees Management System | Student Module of RMIT Groups website.
=======================================================-->
<?php
// edit.php - FMS V 1.0.0
error_reporting(E_ALL);
ini_set('display_errors', 1);
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');

require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/config/audit.php';
require_once BASE_PATH.'/core/auth.php';

checkLogin();

$role       = $_SESSION['role_name'];
$sessInstId = $_SESSION['inst_id'];
$id         = isset($_GET['id']) ? intval($_GET['id']) : 0;

if($id == 0) { header("Location: list.php"); exit; }

// 1. Fetch current student data
$check_sql = "SELECT * FROM STUDENTS WHERE STUDENT_ID = $id";
if ($role !== 'SUPERADMIN') { $check_sql .= " AND INST_ID = $sessInstId"; }

$res = $conn->query($check_sql);
if($res->num_rows == 0) { die("Access Denied: Student not found."); }
$old_data = $res->fetch_assoc();

// 2. Fetch dependencies
// For Superadmin: Fetch ALL institutes
// For Admin: Fetch ONLY courses for their institute
$institutes = ($role === 'SUPERADMIN') ? $conn->query("SELECT INST_ID, INST_NAME FROM MASTER_INSTITUTES ORDER BY INST_NAME ASC") : null;

// Initial course list (Based on the student's current institute)
$current_inst_id = $old_data['INST_ID'];
$courses = $conn->query("SELECT COURSE_ID, COURSE_NAME FROM COURSES WHERE INST_ID = $current_inst_id ORDER BY COURSE_NAME ASC");

// 3. Handle Form Submission
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $fname      = $_POST['fname'];
    $lname      = $_POST['lname'];
    $father     = $_POST['father_name'];
    $mother     = $_POST['mother_name'];
    $gender     = $_POST['gender'];
    $dob        = $_POST['dob'];
    $mobile     = $_POST['mobile'];
    $email      = $_POST['email'];
    $address    = $_POST['address'];
    $city       = $_POST['city'];
    $state      = $_POST['state'];
    $pincode    = $_POST['pincode'];
    $course_id  = intval($_POST['course_id']);
    $semester   = intval($_POST['semester']);
    $adm_date   = $_POST['admission_date'];
    $status     = $_POST['status'];
    $target_inst = ($role === 'SUPERADMIN') ? intval($_POST['inst_id']) : $sessInstId;

    $update_sql = "UPDATE STUDENTS SET 
                    FIRST_NAME=?, LAST_NAME=?, FATHER_NAME=?, MOTHER_NAME=?, 
                    GENDER=?, DOB=?, MOBILE=?, EMAIL=?, ADDRESS=?, 
                    CITY=?, STATE=?, PINCODE=?, COURSE_ID=?, SEMESTER=?, 
                    ADMISSION_DATE=?, STATUS=?, INST_ID=?, UPDATED_AT=NOW() 
                   WHERE STUDENT_ID=?";
    
    if ($role !== 'SUPERADMIN') { $update_sql .= " AND INST_ID = $sessInstId"; }

    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssssssssssssiissii", 
        $fname, $lname, $father, $mother, $gender, $dob, $mobile, $email, 
        $address, $city, $state, $pincode, $course_id, $semester, $adm_date, $status, $target_inst, $id
    );

    if($stmt->execute()){
        audit_log($conn, 'UPDATE_STUDENT_PROFILE', 'STUDENTS', $id, $old_data, $_POST);
        header("Location: list.php?msg=updated");
        exit;
    }
}
?>

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<?php include BASE_PATH.'/admin/layout/sidebar.php'; ?>

<div class="container-fluid py-4">
    <form method="post" id="editStudentForm">
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-person-badge me-2 text-primary"></i>Personal Information</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label small fw-bold">First Name</label><input type="text" name="fname" value="<?= htmlspecialchars($old_data['FIRST_NAME']) ?>" class="form-control" required></div>
                            <div class="col-md-6"><label class="form-label small fw-bold">Last Name</label><input type="text" name="lname" value="<?= htmlspecialchars($old_data['LAST_NAME']) ?>" class="form-control"></div>
                            <div class="col-md-6"><label class="form-label small fw-bold">Father's Name</label><input type="text" name="father_name" value="<?= htmlspecialchars($old_data['FATHER_NAME']) ?>" class="form-control" required></div>
                            <div class="col-md-6"><label class="form-label small fw-bold">Mother's Name</label><input type="text" name="mother_name" value="<?= htmlspecialchars($old_data['MOTHER_NAME']) ?>" class="form-control" required></div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Gender</label>
                                <select name="gender" class="form-select">
                                    <option value="MALE" <?= $old_data['GENDER']=='MALE'?'selected':'' ?>>Male</option>
                                    <option value="FEMALE" <?= $old_data['GENDER']=='FEMALE'?'selected':'' ?>>Female</option>
                                    <option value="OTHER" <?= $old_data['GENDER']=='OTHER'?'selected':'' ?>>Other</option>
                                </select>
                            </div>
                            <div class="col-md-4"><label class="form-label small fw-bold">Date of Birth</label><input type="date" name="dob" value="<?= $old_data['DOB'] ?>" class="form-control"></div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Status</label>
                                <select name="status" class="form-select">
                                    <option value="A" <?= $old_data['STATUS']=='A'?'selected':'' ?>>Active</option>
                                    <option value="I" <?= $old_data['STATUS']=='I'?'selected':'' ?>>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold"><i class="bi bi-geo-alt me-2 text-primary"></i>Contact & Address</h5></div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label small fw-bold">Mobile</label><input type="text" name="mobile" value="<?= $old_data['MOBILE'] ?>" class="form-control"></div>
                            <div class="col-md-6"><label class="form-label small fw-bold">Email</label><input type="email" name="email" value="<?= $old_data['EMAIL'] ?>" class="form-control"></div>
                            <div class="col-12"><label class="form-label small fw-bold">Address</label><input type="text" name="address" value="<?= htmlspecialchars($old_data['ADDRESS']) ?>" class="form-control"></div>
                            <div class="col-md-4"><label class="form-label small fw-bold">City</label><input type="text" name="city" value="<?= $old_data['CITY'] ?>" class="form-control"></div>
                            <div class="col-md-4"><label class="form-label small fw-bold">State</label><input type="text" name="state" value="<?= $old_data['STATE'] ?>" class="form-control"></div>
                            <div class="col-md-4"><label class="form-label small fw-bold">Pincode</label><input type="text" name="pincode" value="<?= $old_data['PINCODE'] ?>" class="form-control"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0 mb-4 bg-primary text-white">
                    <div class="card-body">
                        <label class="small opacity-75">Registration No</label><h4 class="fw-bold mb-3"><?= $old_data['REGISTRATION_NO'] ?></h4>
                        <label class="small opacity-75">Roll No</label><h4 class="fw-bold"><?= $old_data['ROLL_NO'] ?></h4>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold"><i class="bi bi-book me-2 text-primary"></i>Academic Details</h5></div>
                    <div class="card-body p-4">
                        
                        <?php if ($role === 'SUPERADMIN'): ?>
                        <div class="mb-3 p-3 bg-light rounded border border-dashed">
                            <label class="form-label small fw-bold text-primary">Institute/Campus</label>
                            <select name="inst_id" id="inst_id" class="form-select shadow-sm" onchange="fetchCourses(this.value)">
                                <?php while($i = $institutes->fetch_assoc()): ?>
                                    <option value="<?= $i['INST_ID'] ?>" <?= $old_data['INST_ID']==$i['INST_ID']?'selected':'' ?>>
                                        <?= htmlspecialchars($i['INST_NAME']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Course</label>
                            <select name="course_id" id="course_id" class="form-select" required>
                                <?php while($c = $courses->fetch_assoc()): ?>
                                    <option value="<?= $c['COURSE_ID'] ?>" <?= $old_data['COURSE_ID']==$c['COURSE_ID']?'selected':'' ?>>
                                        <?= htmlspecialchars($c['COURSE_NAME']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Semester</label>
                            <input type="number" name="semester" value="<?= $old_data['SEMESTER'] ?>" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Admission Date</label>
                            <input type="date" name="admission_date" value="<?= $old_data['ADMISSION_DATE'] ?>" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg shadow">Save Update</button>
                    <a href="list.php" class="btn btn-light">Discard</a>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function fetchCourses(instId) {
    const courseSelect = document.getElementById('course_id');
    courseSelect.innerHTML = '<option value="">Loading...</option>';

    fetch(`get_courses.php?inst_id=${instId}`)
        .then(response => response.json())
        .then(data => {
            courseSelect.innerHTML = '<option value="">-- Select Course --</option>';
            data.forEach(course => {
                courseSelect.innerHTML += `<option value="${course.COURSE_ID}">${course.COURSE_NAME}</option>`;
            });
        })
        .catch(err => {
            console.error('Error fetching courses:', err);
            courseSelect.innerHTML = '<option value="">Error loading courses</option>';
        });
}
</script>

<?php include BASE_PATH.'/admin/layout/footer.php'; ?>
