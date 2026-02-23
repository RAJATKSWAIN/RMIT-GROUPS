<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');

include BASE_PATH.'/admin/layout/header.php';
include BASE_PATH.'/admin/layout/sidebar.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-3">
            <div class="list-group shadow-sm">
                <div class="list-group-item bg-primary text-white fw-bold">Management Modules</div>
                <button onclick="loadSvc('ADD_COURSE')" class="list-group-item list-group-item-action">⚙️ Course Setup</button>
                <button onclick="loadSvc('REGISTER_STUDENT')" class="list-group-item list-group-item-action">🎓 Student Admission</button>
                <button onclick="loadSvc('COLLECT_PAYMENT')" class="list-group-item list-group-item-action">💰 Fee Collection</button>
                <button onclick="loadSvc('FETCH_STUDENTS')" class="list-group-item list-group-item-action">🔍 View Students</button>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 id="svcTitle">Select a Feature Service</h5>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary">JSON Input Payload</label>
                            <textarea id="payload" class="form-control font-monospace bg-dark text-warning p-3" style="height: 400px;"></textarea>
                            <button onclick="runSvc()" class="btn btn-success mt-3 w-100 fw-bold">EXECUTE APPLICATION SERVICE</button>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary">Live Application Response</label>
                            <div id="response" class="border rounded p-3 bg-light font-monospace" style="height: 400px; overflow-y: auto;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const serviceLibrary = {
    ADD_COURSE: { action: "ADD_COURSE", code: "DIPL-01", name: "Diploma ME", duration: 3 },
    REGISTER_STUDENT: { action: "REGISTER_STUDENT", reg_no: "2026/001", roll_no: "ME-01", fname: "John", lname: "Doe", course_id: 1 },
    COLLECT_PAYMENT: { action: "COLLECT_PAYMENT", student_id: 1, amount: 5000, mode: "CASH" },
    FETCH_STUDENTS: { action: "FETCH_STUDENTS" }
};

function loadSvc(key) {
    document.getElementById('svcTitle').innerText = "Service: " + key;
    document.getElementById('payload').value = JSON.stringify(serviceLibrary[key], null, 4);
}

async function runSvc() {
    const payload = document.getElementById('payload').value;
    const out = document.getElementById('response');
    out.innerHTML = "<em>Sending Request...</em>";
    
    try {
        const res = await fetch('../../services/app_gateway.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: payload
        });
        const json = await res.json();
        out.innerHTML = `<pre class="${json.status === 'success' ? 'text-success' : 'text-danger'}">${JSON.stringify(json, null, 4)}</pre>`;
    } catch (e) {
        out.innerHTML = "Network Error: " + e.message;
    }
}
</script>
