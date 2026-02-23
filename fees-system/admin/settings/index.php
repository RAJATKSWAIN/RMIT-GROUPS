<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';
checkLogin();

include BASE_PATH.'/admin/layout/header.php';
include BASE_PATH.'/admin/layout/sidebar.php';
?>

<div class="container-fluid px-4">
    <h3 class="mt-4">Master Configuration</h3>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Settings</li>
    </ol>

    <div class="row">
        <div class="col-xl-6">
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-book me-1"></i> Add New Course
                </div>
                <div class="card-body">
                    <form id="courseForm">
                        <div class="mb-3">
                            <label class="form-label">Course Name</label>
                            <input type="text" name="course_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Course Code</label>
                            <input type="text" name="course_code" class="form-control" placeholder="e.g., BCA">
                        </div>
                        <button type="submit" class="btn btn-primary">Save Course</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-success text-white">
                    <i class="bi bi-tags me-1"></i> Add Fee Head
                </div>
                <div class="card-body">
                    <form id="feeForm">
                        <div class="mb-3">
                            <label class="form-label">Fee Head Name</label>
                            <input type="text" name="fees_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Frequency</label>
                            <select name="level" class="form-select">
                                <option value="YEAR">Yearly</option>
                                <option value="SEMESTER">Semester</option>
                                <option value="ONETIME">One-Time</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">Save Fee Head</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div id="responseMsg" class="alert d-none"></div>
</div>

<script>
// API Logic
function setupForm(formId, apiEndpoint) {
    document.getElementById(formId).onsubmit = function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const msg = document.getElementById('responseMsg');

        fetch(`../../api/${apiEndpoint}`, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            msg.className = `alert mt-3 alert-${data.status === 'success' ? 'success' : 'danger'}`;
            msg.innerText = data.message;
            msg.classList.remove('d-none');
            if(data.status === 'success') this.reset();
        });
    };
}

setupForm('courseForm', 'insert_course.php');
setupForm('feeForm', 'insert_fee_head.php');
</script>

<?php include BASE_PATH.'/admin/layout/footer.php'; ?>
