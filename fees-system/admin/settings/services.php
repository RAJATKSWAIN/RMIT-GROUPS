<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';
checkLogin();
include BASE_PATH.'/admin/layout/header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white fw-bold">API Services</div>
                <div class="list-group list-group-flush">
                    <button class="list-group-item list-group-item-action py-3" onclick="selectService('course')">
                        <strong>Add Course</strong><br><small class="text-muted">api/config/add_course.php</small>
                    </button>
                    <button class="list-group-item list-group-item-action py-3" onclick="selectService('fee_hdr')">
                        <strong>Add Fee Header</strong><br><small class="text-muted">api/config/add_fee_header.php</small>
                    </button>
                    <button class="list-group-item list-group-item-action py-3" onclick="selectService('fee_map')">
                        <strong>Map Fee Details</strong><br><small class="text-muted">api/config/map_fee.php</small>
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 id="serviceTitle">Select a Service to begin</h5>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Request JSON Body</label>
                            <textarea id="jsonEditor" class="form-control bg-dark text-light p-3 font-monospace" style="height: 350px;"></textarea>
                            <button id="postBtn" class="btn btn-primary mt-3 w-100 py-2 fw-bold" disabled onclick="sendData()">EXECUTE SERVICE</button>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Live Server Response</label>
                            <div id="jsonResponse" class="border rounded bg-light p-3 font-monospace" style="height: 350px; overflow-y: auto;">
                                <span class="text-muted">Waiting for execution...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const services = {
    course: {
        title: "Create New Course",
        endpoint: "../../api/config/add_course.php",
        json: { "course_code": "", "course_name": "", "duration": 3 }
    },
    fee_hdr: {
        title: "Define Master Fee Header",
        endpoint: "../../api/config/add_fee_header.php",
        json: { "fees_code": "", "fees_name": "", "level": "YEAR" }
    },
    fee_map: {
        title: "Assign Fee Amount to Course",
        endpoint: "../../api/config/map_fee.php",
        json: { "course_id": 0, "fees_hdr_id": 0, "amount": 0.00 }
    }
};

let activeEndpoint = "";

function selectService(key) {
    const s = services[key];
    activeEndpoint = s.endpoint;
    document.getElementById('serviceTitle').innerText = s.title;
    document.getElementById('jsonEditor').value = JSON.stringify(s.json, null, 4);
    document.getElementById('postBtn').disabled = false;
    document.getElementById('jsonResponse').innerHTML = '<span class="text-muted">Template Loaded. Correct values and click Execute.</span>';
}

async function sendData() {
    const responseBox = document.getElementById('jsonResponse');
    responseBox.innerText = "Processing...";
    
    try {
        const body = JSON.parse(document.getElementById('jsonEditor').value);
        const res = await fetch(activeEndpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body)
        });
        const out = await res.json();
        responseBox.innerHTML = `<pre class="${out.status === 'success' ? 'text-success' : 'text-danger'}">${JSON.stringify(out, null, 4)}</pre>`;
    } catch (e) {
        responseBox.innerHTML = `<span class="text-danger">JSON Error: ${e.message}</span>`;
    }
}
</script>
