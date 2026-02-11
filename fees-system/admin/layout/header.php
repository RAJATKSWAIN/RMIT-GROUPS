<?php
/* =========================
   GLOBAL HEADER
   Works from ANY folder
========================= */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ROOT PATH (important) */
$ROOT = $_SERVER['DOCUMENT_ROOT'] . "/fees-system";

/* Optional login protection */
if (!isset($_SESSION['admin_id']) && basename($_SERVER['PHP_SELF']) != 'login.php') {
    header("Location: /fees-system/admin/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>

<!-- Essential Meta -->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- SEO Title -->
<title>Admin Dashboard | Fees Management System (FMS)</title>

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
    
<!--<link rel="stylesheet" href="https://eduremit.likesyou.org/assets/css/portal-theme.css"> -->
    
<style>
		:root {--sidebar-width: 260px;}
	
		body {background: #f4f6fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; overflow-x: hidden;    }
	
		/* --- SIDEBAR LOGIC --- */
		.sidebar {
			width: var(--sidebar-width);
			height: 100vh;
			position: fixed;
			left: 0;
			top: 0;
			background: #1f2937;
			color: #fff;
			overflow-y: auto;
			transition: all 0.3s ease;
			z-index: 1050; /* Above everything */
		}
	
		/* --- MAIN CONTENT LOGIC --- */
		.main {
			margin-left: var(--sidebar-width);
			padding: 25px;
			transition: all 0.3s ease;
		}
	
		/* --- MOBILE STATES --- */
		@media (max-width: 992px) {
			.sidebar {
				left: calc(-1 * var(--sidebar-width));
			}
			.main { margin-left: 0;}
			/* When active, slide sidebar in */
			.sidebar.active {left: 0;}
			/* Overlay to darken background when sidebar is open */
			.sidebar-overlay { display: none;  position: fixed;  width: 100vw;  height: 100vh; background: rgba(0,0,0,0.5);  z-index: 1040; }
			.sidebar-overlay.active {display: block;}
		}
	
		/* Sidebar Styling */
		.section-title { font-size: 12px; margin-top: 3px; color: #6c757d; font-weight: 800; padding: 15px 20px 5px 20px; text-transform: uppercase; letter-spacing: 1.2px;}
		.sidebar a { display: block; color: #d1d5db; text-decoration: none; padding: 10px 15px; font-size: 12px; transition: 0.5s;}
		.sidebar a:hover { background: #374151; color: #fbbf24; padding-left: 22px; /*text-decoration: underline;  underline only on hover */ }
		.sidebar a i, .sidebar a bi { margin-right: 10px; width: 20px; text-align: center; }
	
		/* Top Mobile Bar */
		.mobile-top-nav {display: none; background: #fff; padding: 10px 15px; border-bottom: 1px solid #dee2e6; }
		@media (max-width: 992px) {
			.mobile-top-nav { display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 1000; }
		}
		.card-box{ border-radius:14px;box-shadow:0 4px 10px rgba(0,0,0,.06)}
		.stat{font-size:22px;font-weight:600}
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
