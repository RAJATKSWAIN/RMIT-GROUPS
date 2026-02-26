<!--======================================================
    File Name   : view.php
    Project     : RMIT Groups - FMS - Fees Management System
    Module      : STUDENT MANAGEMENT
    Description : Student Registration & Profile Management
    Developed By: TrinityWebEdge
    Date Created: 06-02-2026
    Last Updated: 25-02-2026
    Note        : This page defines the FMS - Fees Management System | Student Module of RMIT Groups website.
=======================================================-->
<?php
// Define Base Path for local file includes
define('BASE_PATH', rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/fees-system');

require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/core/auth.php';

// Access Control
checkLogin();

// 1. Sanitize the ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id === 0) { die("Invalid Student ID."); }

/**
 * 2. Fetch data with JOINs
 * Using ALL CAPS columns as per your database structure
 */
$query = $conn->query("
    SELECT S.*, C.COURSE_NAME, I.INST_NAME, I.BRAND_COLOR 
    FROM STUDENTS S
    JOIN COURSES C ON S.COURSE_ID = C.COURSE_ID
    LEFT JOIN MASTER_INSTITUTES I ON S.INST_ID = I.INST_ID
    WHERE S.STUDENT_ID = $id
");

$s = $query->fetch_assoc();
if (!$s) { die("Student record not found."); }

$theme_color = $s['BRAND_COLOR'] ?? '#0f172a'; // Deep slate for premium feel
?>

<?php include BASE_PATH . '/admin/layout/header.php'; ?>
<?php include BASE_PATH . '/admin/layout/sidebar.php'; ?>

<style>
    :root { 
        --brand-color: <?= $theme_color ?>; 
        --surface-bg: #f1f5f9;
        --card-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    }
    
    body { background-color: var(--surface-bg); }

    /* Profile Header */
    .profile-banner {
        background: #ffffff;
        border-radius: 24px;
        border: 1px solid #e2e8f0;
        box-shadow: var(--card-shadow);
        overflow: hidden;
    }
    
    .accent-bar {
        height: 6px;
        background: var(--brand-color);
    }

    /* Modern Data Grid */
    .data-card {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        padding: 1.5rem;
        height: 100%;
        box-shadow: var(--card-shadow);
    }

    .data-label {
        font-size: 0.7rem;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 4px;
    }

    .data-value {
        font-size: 0.95rem;
        font-weight: 600;
        color: #334155;
    }

    .section-title1 {
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--brand-color);
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 1.5rem;
    }

    .id-badge {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 4px 12px;
        border-radius: 8px;
        font-family: monospace;
        font-weight: 700;
        color: var(--brand-color);
    }
</style>

<div class="container-fluid py-4">
    <div class="profile-banner mb-4">
        <div class="accent-bar"></div>
        <div class="p-4 p-md-5">
            <div class="row align-items-center">
                <div class="col-md-auto text-center mb-3 mb-md-0">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white shadow-sm" 
                         style="width: 100px; height: 100px; background: var(--brand-color); font-size: 2.5rem; font-weight: 700;">
                        <?= substr($s['FIRST_NAME'], 0, 1) . substr($s['LAST_NAME'], 0, 1) ?>
                    </div>
                </div>
                <div class="col-md">
                    <div class="d-flex align-items-center gap-3 mb-1">
                        <h1 class="h2 fw-bold text-dark mb-0"><?= htmlspecialchars($s['FIRST_NAME'] . ' ' . $s['LAST_NAME']) ?></h1>
                        <span class="badge rounded-pill <?= $s['STATUS'] == 'A' ? 'bg-success' : 'bg-danger' ?> px-3">
                            <?= $s['STATUS'] == 'A' ? 'ACTIVE' : 'INACTIVE' ?>
                        </span>
                    </div>
                    <p class="text-muted mb-0 fw-medium">
                        <i class="bi bi-building me-1"></i> <?= htmlspecialchars($s['INST_NAME'] ?? 'Institute Not Assigned') ?>
                    </p>
                </div>
                <div class="col-md-auto mt-4 mt-md-0 d-print-none">
                    <div class="d-flex gap-2">
                        <button onclick="window.print()" class="btn btn-outline-secondary px-4 rounded-pill fw-bold">
                            <i class="bi bi-printer me-2"></i> Print
                        </button>
                        <a href="list.php" class="btn btn-dark px-4 rounded-pill fw-bold">
                            Close View
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="data-card">
                <div class="section-title1">
                    <i class="bi bi-mortarboard-fill"></i> Academic Profile
                </div>
                <div class="mb-4">
                    <div class="data-label">Registration Number</div>
                    <div class="id-badge d-inline-block mt-1"><?= htmlspecialchars($s['REGISTRATION_NO']) ?></div>
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <div class="data-label">Roll Number</div>
                        <div class="data-value"><?= htmlspecialchars($s['ROLL_NO']) ?></div>
                    </div>
                    <div class="col-6">
                        <div class="data-label">Current Phase</div>
                        <div class="data-value">Semester <?= htmlspecialchars($s['SEMESTER'] ?? '1') ?></div>
                    </div>
                    <div class="col-12">
                        <div class="data-label">Enrolled Course</div>
                        <div class="data-value"><?= htmlspecialchars($s['COURSE_NAME']) ?></div>
                    </div>
                    <div class="col-12">
                        <div class="data-label">Admission Date</div>
                        <div class="data-value"><?= !empty($s['ADMISSION_DATE']) ? date('d M, Y', strtotime($s['ADMISSION_DATE'])) : 'Not Recorded' ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="data-card">
                <div class="section-title1">
                    <i class="bi bi-person-heart"></i> Personal & Family
                </div>
                <div class="mb-4">
                    <div class="data-label">Father's Full Name</div>
                    <div class="data-value fs-6"><?= htmlspecialchars($s['FATHER_NAME'] ?? 'N/A') ?></div>
                </div>
                <div class="mb-4">
                    <div class="data-label">Mother's Full Name</div>
                    <div class="data-value fs-6"><?= htmlspecialchars($s['MOTHER_NAME'] ?? 'N/A') ?></div>
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <div class="data-label">Gender</div>
                        <div class="data-value"><?= htmlspecialchars($s['GENDER']) ?></div>
                    </div>
                    <div class="col-6">
                        <div class="data-label">Date of Birth</div>
                        <div class="data-value"><?= !empty($s['DOB']) ? date('d M, Y', strtotime($s['DOB'])) : 'N/A' ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="data-card">
                <div class="section-title1 text-info">
                    <i class="bi bi-geo-alt-fill"></i> Contact & Address
                </div>
                <div class="mb-3">
                    <div class="data-label">Mobile Number</div>
                    <div class="data-value fw-bold text-dark"><?= htmlspecialchars($s['MOBILE']) ?></div>
                </div>
                <div class="mb-4">
                    <div class="data-label">Email Address</div>
                    <div class="data-value"><?= htmlspecialchars($s['EMAIL']) ?></div>
                </div>
                <div class="p-3 rounded-4" style="background: #f8fafc; border: 1px dashed #cbd5e1;">
                    <div class="data-label">Residential Address</div>
                    <div class="data-value small" style="line-height: 1.6;">
                        <?= htmlspecialchars($s['ADDRESS']) ?><br>
                        <?= htmlspecialchars($s['CITY']) ?>, <?= htmlspecialchars($s['STATE']) ?> - <?= htmlspecialchars($s['PINCODE']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

   <div class="mt-5 pb-5 d-print-none">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-11">
                    <div class="d-flex align-items-center mb-4">
                        <hr class="flex-grow-1" style="opacity: 0.1;">
                        <span class="mx-3 text-muted small fw-bold" style="letter-spacing: 2px; font-size: 0.6rem;">SYSTEM DISCLOSURE</span>
                        <hr class="flex-grow-1" style="opacity: 0.1;">
                    </div>
                    
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-4">
                        
                        <div class="text-center text-md-start">
                            <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-2 mb-1">
                                <i class="bi bi-database-fill text-primary"></i> 
                                <span class="fw-bold text-dark small" style="letter-spacing: 0.5px;">SRN: #<?= str_pad($id, 6, '0', STR_PAD_LEFT) ?></span>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-1 ms-1" style="font-size: 0.65rem;">
                                    INTEGRITY_VERIFIED
                                </span>
                            </div>
                            <div class="text-muted" style="font-size: 0.7rem;">
                                <i class="bi bi-shield-lock-fill me-1" style="font-size: 0.6rem;"></i> Security Protocol: AES-256 Bit Encryption
                            </div>
                        </div>

                        <div class="text-center border-start border-end px-4 d-none d-lg-block">
                            <div class="text-muted mb-2" style="font-size: 0.65rem; text-transform: uppercase; font-weight: 800; letter-spacing: 1px;">
                                <i class="bi bi-clipboard-data me-1"></i> Data Audit Trail
                            </div>
                            <div class="d-flex align-items-center gap-3 justify-content-center">
                                <div class="small">
                                    <i class="bi bi-plus-circle text-success me-1"></i>
                                    <span class="text-muted small">Initialized:</span> 
                                    <span class="text-dark fw-bold" style="font-size: 0.8rem;"><?= date('d-M-Y H:i', strtotime($s['CREATED_AT'] ?? 'now')) ?></span>
                                </div>
                                <div class="small">
                                    <i class="bi bi-arrow-repeat text-info me-1"></i>
                                    <span class="text-muted small">Data Commit:</span> 
                                    <span class="text-dark fw-bold" style="font-size: 0.8rem;"><?= isset($s['UPDATED_AT']) ? date('d-M-Y H:i', strtotime($s['UPDATED_AT'])) : date('d-M-Y H:i') ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="text-center text-md-end">
                            <div class="text-uppercase text-danger fw-bold mb-1" style="font-size: 0.65rem; letter-spacing: 1px;">
                                <i class="bi bi-shield-shaded me-1"></i> Classified: Internal Use Only
                            </div>
                            <div class="text-muted mb-1" style="font-size: 0.75rem;">
                                <span class="fw-bold text-primary">EduRemit<sup>TM</sup></span> | A Product of <strong>TRINITYWEBEDGE</strong>
                            </div>
                            <div class="text-muted italic" style="font-size: 0.7rem;">
                                Authorized by: <?= defined('ORG_NAME') ? ORG_NAME : 'Academic Administration' ?>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <p class="text-muted" style="font-size: 0.65rem; line-height: 1.6; max-width: 700px; margin: 0 auto; opacity: 0.7;">
                            This digital profile is an official Electronic Record under the IT Act. 
                            Database integrity is maintained via automated <strong>System Audit Daemons (SAD)</strong>. 
                            Unauthorized access to internal classification <code>[INTERNAL_USE_ONLY]</code> is strictly monitored.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>

<?php include BASE_PATH . '/admin/layout/footer.php'; ?>
