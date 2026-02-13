<!--======================================================
    File Name   : index.php
    Project     : RMIT Groups - FMS - Fees Management System
    Description : FMS Home Page
    Developed By: TrinityWebEdge
    Date Created: 06-02-2025
    Last Updated: <?php echo date("d-m-Y"); ?>
    Note        : This page defines the FMS - Fees Management System | FMS Home Page of RMIT Groups website.
=======================================================-->
<?php
require_once 'config/config.php';
// We don't force redirect here; we show the landing page.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> FMS V1.0.0 | RMIT Group of Institutions</title>
    
    <!-- SEO Meta Tags -->
<meta name="description" content="Secure Admin Dashboard for the Fees Management System (FMS). Manage students, fee collection, reports, and audits with a modern ERP interface.">
<meta name="keywords" content="Fees Management System, FMS, Admin Dashboard, Student Fees, ERP, College ERP, School ERP, Fee Collection, Education Management">
<meta name="author" content="TrinityWebEdge">

<!-- Retina PNG versions -->
<link rel="icon" href="images/favicon_32.png" sizes="32x32" type="image/png">
<link rel="icon" href="images/favicon_64.png" sizes="64x64" type="image/png">
<link rel="icon" href="images/favicon_180.png" sizes="180x180" type="image/png">
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'> <text y='12' font-size='8' fill='white'>FMS 1.0</text></svg>">


<!-- Modern UI Frameworks -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link type="text/css" rel="stylesheet" href="css/style.css">
<link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    
    <style>
        :root { 
            --glass-bg: rgba(255, 255, 255, 0.85);
            --primary-navy: #1a3a5a;
            --accent-gold: #ffc107;
            --soft-gray: #f8f9fa;
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: var(--soft-gray);
            overflow-x: hidden;
        }

        /* Modern Glass Top Bar */
        .portal-header-bg {
            background: rgba(26, 58, 90, 0.95) !important;
            backdrop-filter: blur(10px);
            border-bottom: 3px solid var(--accent-gold);
            position: sticky; top: 0; z-index: 1000;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(26, 58, 90, 0.9), rgba(26, 58, 90, 0.9)), 
                        url('assets/images/campus-bg.jpg') center/cover;
            height: 75vh;
            display: flex; align-items: center; justify-content: center;
            color: white; text-align: center; clip-path: polygon(0 0, 100% 0, 100% 85%, 0% 100%);
        }

        .hero h1 { font-weight: 800; letter-spacing: -1px; font-size: 3.5rem; }
        
        /* Floating Showcase Cards */
        .showcase-container { margin-top: -120px; position: relative; z-index: 10; }
        
        .stat-card {
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 24px;
            padding: 40px 30px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 30px 60px rgba(26, 58, 90, 0.15);
            background: #fff;
        }

        .icon-circle {
            width: 70px; height: 70px;
            background: var(--primary-navy);
            color: var(--accent-gold);
            display: flex; align-items: center; justify-content: center;
            border-radius: 20px; font-size: 1.8rem; margin: 0 auto 20px;
        }

        /* Futuristic Button */
        .btn-access {
            background: var(--accent-gold);
            color: var(--primary-navy);
            padding: 16px 45px;
            border-radius: 100px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 10px 25px rgba(255, 193, 7, 0.4);
            transition: 0.4s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-access:hover {
            background: #fff;
            color: var(--primary-navy);
            transform: scale(1.05);
            box-shadow: 0 15px 30px rgba(255, 193, 7, 0.5);
        }

        /* Subtle Animation */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        .float-img { animation: float 4s ease-in-out infinite; }
    </style>
</head>
<body>
    
<!-- Start Of Brand Heading-->
<div class="portal-header-bg p-1" style=" background:#2c3e50; border-bottom: 5px solid #ffc107; text-align:right;">
  <a href="dashboard.php" 
     style="font-family:'Raleway',sans-serif; font-weight:800; text-decoration:none; display:inline-block; color:#ffffff; font-size:1rem;">
    Edu<span style="color:#ffc107;">Remit™</span>
  </a>
  <span style="display:block; font-size:0.45rem; letter-spacing:3px; text-transform:uppercase; opacity:0.5; font-weight:bold; margin-top:0; color:#ffffff;">
    By TrinityWebEdge
  </span>
</div>
<!-- End Of Brand Heading-->

<section class="hero">
    <div class="container">
        <img class="mb-4"  src="/images/logo.png" alt= "RMIT logo" style="width:auto; height:30px;" >
        <h4 class="display-8 fw-bold">FEES MANAGEMENT SYSTEM - V 1.0.0</h4>
        <p class="lead mb-5 opacity-75 mx-auto" style="max-width: 700px;">
            A unified financial ecosystem for RMIT Group. Streamlining collections, 
            automating receipts, and securing audits with real-time precision.
        </p>
        <div class="mt-4">
            <a href="admin/login.php" class="btn-access">ADMINISTRATOR ACCESS <i class="fas fa-arrow-right ms-2"></i></a>
        </div>
    </div>
</section>

<div class="container showcase-container">
    <div class="row g-4 justify-content-center">
        <div class="col-md-4">
            <div class="stat-card text-center">
                <div class="icon-circle"><i class="fas fa-layer-group"></i></div>
                <h4 class="fw-bold"><i class="fas fa-university fa-1x mb-3 text-warning"></i> Multi-Campus</h4>
                <p class="text-muted small">Seamlessly toggle between HIT, RMIT, and RMITC. One database, multiple entities, zero friction.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card text-center">
                <div class="icon-circle"><i class="fas fa-bolt"></i></div>
                <h4 class="fw-bold"> <i class="fas fa-file-invoice-dollar fa-1x mb-3 text-primary"></i> Smart Settlement</h4>
                <p class="text-muted small">AI-driven fee allocation logic. Handles Miscellaneous, Course, and Semester fees with instant ledger sync.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card text-center">
                <div class="icon-circle"><i class="fas fa-fingerprint"></i></div>
                <h4 class="fw-bold"> <i class="fas fa-shield-alt fa-1x mb-3 text-success"></i> Bulletproof Audit</h4>
                <p class="text-muted small">Every rupee accounted for. Complete transaction transparency with computer-generated verifiable receipts.</p>
            </div>
        </div>
    </div>
</div>

<div class="container my-5 pt-5">
    <div class="row align-items-center">
        <div class="col-lg-6">
            <h2 class="fw-bold text-navy mb-4">Enterprise Grade Infrastructure</h2>
            <ul class="list-unstyled">
                <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i> Real-time Balance Calculations</li>
                <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i> Bulk Student & Course Data Processing</li>
                <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i> Automated Indian Currency Word Conversion</li>
            </ul>
        </div>
        <div class="col-lg-6 text-center">
            <div class="p-4 bg-white rounded-4 shadow-sm float-img">
                <img src="https://cdn-icons-png.flaticon.com/512/3135/3135706.png" width="150" alt="ERP Icon">
                <p class="mt-3 fw-bold text-muted">SECURED BY TRINITY-CORE</p>
            </div>
        </div>
    </div>
</div>

<footer class="mt-auto pt-5">
    <hr class="text-muted opacity-25">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <p class="mb-0 text-muted small">
                    &copy; <?= date('Y'); ?> 
                    <strong>
                        <span style="color: black;">RMIT</span> 
                        <span style="color: red;">GROUP OF INSTITUTIONS</span>
                    </strong>. All Rights Reserved.
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <small class="text-black-50" style="font-size: 0.95rem; letter-spacing: 0.3px;">
            		&copy;<span class="fw-bold">EduRemit™</span> | 
            		Product of <a href="#" class="text-decoration-none text-secondary fw-semibold">TrinityWebEdge</a>
        		</small>
            </div>
        </div>
    </div>
</footer>
<!--
<footer class="main-footer mt-auto" >
    <div class="text-end py-2 px-3">
        <small class="text-black-50" style="font-size: 0.65rem; letter-spacing: 0.3px;">
            &copy; 2026 <span class="fw-bold">EduRemit™</span> | 
            Product of <a href="#" class="text-decoration-none text-secondary fw-semibold">TrinityWebEdge</a>
        </small>
    </div>
	</footer>
                
<style>
/* Container for the footer */
	.main-footer {
    	width: 100%;
    	padding: 0 15px 10px 15px; /* Adds a little breathing room from the edges */
	}
</style>
-->

</div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>

</body>
</html>
