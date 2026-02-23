<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>System Configuration | FMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-gear-fill me-2"></i>Master Configuration</h2>
        <a href="../dashboard.php" class="btn btn-outline-secondary btn-sm">Back to Dashboard</a>
    </div>

    <ul class="nav nav-tabs shadow-sm bg-white rounded-top" id="configTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#course-tab">Add Course</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#fee-tab">Add Fee Head</button>
        </li>
    </ul>

    <div class="tab-content border border-top-0 p-4 bg-white shadow-sm rounded-bottom">
        
        <div class="tab-pane fade show active" id="course-tab">
            <form id="courseForm">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Course Name</label>
                        <input type="text" name="course_name" class="form-control" placeholder="e.g. Bachelor of Arts" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Course Code</label>
                        <input type="text" name="course_code" class="form-control" placeholder="e.g. BA" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Save Course</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="tab-pane fade" id="fee-tab">
            <form id="feeForm">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label">Fee Name</label>
                        <input type="text" name="fees_name" class="form-control" placeholder="e.g. Library Fee" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Applicable Level</label>
                        <select name="level" class="form-select">
                            <option value="YEAR">Yearly</option>
                            <option value="SEMESTER">Semester</option>
                            <option value="ONETIME">One Time</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-success w-100">Save Fee Head</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="responseMsg" class="mt-3 alert d-none"></div>
</div>

<script>
// Generic function to handle API inserts
function handleInsert(formId, apiPath) {
    document.getElementById(formId).addEventListener('submit', function(e) {
        e.preventDefault();
        const msgDiv = document.getElementById('responseMsg');
        const formData = new FormData(this);

        fetch(apiPath, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            msgDiv.className = `mt-3 alert alert-${data.status === 'success' ? 'success' : 'danger'}`;
            msgDiv.innerText = data.message;
            msgDiv.classList.remove('d-none');
            if(data.status === 'success') this.reset();
        })
        .catch(err => {
            msgDiv.className = 'mt-3 alert alert-danger';
            msgDiv.innerText = "Fatal Error: Could not connect to API.";
            msgDiv.classList.remove('d-none');
        });
    });
}

// Map forms to their respective backend scripts
handleInsert('courseForm', '../../api/insert_course.php');
handleInsert('feeForm', '../../api/insert_fee_head.php');
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
