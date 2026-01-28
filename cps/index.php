<!--======================================================
    File Name   : index.php
    Project     : RMIT Groups - CPS
    Description : About Us Page 
    Developed By: TrinityWebEdge
    Date Created: 17-12-2025
    Last Updated: <?php echo date("d-m-Y"); ?>
    Note         : This page defines the Home page of RMIT Groups website.
=======================================================-->
<!DOCTYPE html>

<head>
  <meta charset="UTF-8">

  <!-- Compatibility & Mobile -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

  <!-- Primary Meta Tags -->
  <title>CPS - Chirag Public School, Berhampur | RMIT Group of Institutions | Empowering Minds, Building Futures</title>
  <meta name="description"
        content="Chirag Public School (CPS) is a CBSE pattern English medium school in Berhampur, Odisha, under the RMIT Group of Institutions. We nurture knowledge, build character, and 					empower young minds for a brighter future.">
  <meta name="keywords"
        content="Chirag Public School, CPS Berhampur, RMIT Group, CBSE School Odisha, English Medium School Berhampur, Best School in Berhampur, Admissions CPS">
  <meta name="author" content="RMIT Group of Institutions">
  <meta name="robots" content="index, follow">

  
  <!-- Favicons -->
  <link rel="icon" href="https://rmitgroupsorg.infinityfree.me//cps/images/logos/favicon.ico" type="image/x-icon">
  <link rel="apple-touch-icon" href="https://rmitgroupsorg.infinityfree.me//cps/images/logos/favicon-64.png">
  <link rel="icon" href="images/logos/favicon.ico" type="image/x-icon">
  <link rel="icon" href="images/logos/favicon_32.png" sizes="32x32" type="image/png">
  <link rel="icon" href="images/logos/favicon-64.png" sizes="64x64" type="image/png">
  <link rel="icon" href="images/logos/favicon_180.png" sizes="180x180" type="image/png">
  <link rel="apple-touch-icon" href="images/logos/favicon_180.png">
  
  <!-- Google Fonts (Optimized) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;600;700;800&family=Varela+Round&display=swap" rel="stylesheet">

  <!-- All CSS links -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="css/cps-header.css">
  <link rel="stylesheet" type="text/css" href="css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <!--<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">-->
  <link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
  <link rel="stylesheet" type="text/css" href="css/owl.carousel.min.css">
  <link rel="stylesheet" type="text/css" href="css/style.css">
  <link rel="stylesheet" type="text/css" href="css/responsive.css">

		<style>
		/* ===== Global ===== */
		body {
			margin: 0;
			font-family: 'Segoe UI', sans-serif;
			padding-top: 105px; /* header + top bars */
			background-color: #f0f4f8;
		}
		
		@media (max-width:768px){ body{padding-top:100px;} }
		
		/* ===== Announcement ===== */
		.announcement-bar{
			background:linear-gradient(90deg,#ff9800,#ff5722);
			color:#fff;
			padding:12px 0;
			font-weight:600;
			overflow:hidden;
			text-align:center;
			margin-top:28px;
		}
		
		.announcement-text{
			display:inline-block;
			white-space:nowrap;
			animation:slideText 20s linear infinite;
		}
		
		@keyframes slideText{
			0%{transform:translateX(100%)}
			100%{transform:translateX(-100%)}
		}
		
		/* ===== Top Info Bar ===== */
		.cps-topbar{
			background:linear-gradient(90deg,#003c8f,#1fa2ff);
			color:white;font-size:13px;padding:6px 0;
		}
		
        .hero {
			margin-top: 0;
			padding-top: 0;
			position: relative;
			top: 0;
		}

		/* push hero down only by header height */
		.cps-header-fixed + .hero {
			margin-top: 115px;
		}

		@media (max-width:768px){
		.cps-header-fixed + .hero {
			margin-top: 105px;
			}
		}
		
		/* Hero Slider */
		.hero img{
			width:100%;
			aspect-ratio:3/2; /* 1536x1024 approx */
			object-fit:cover;
		}
		
		/* Showcase Section */
		.showcase{
			background:#f4f8ff;
			padding:70px 0;
		}
		
		.showcase .card{
			border:none;
			border-radius:16px;
			box-shadow:0 8px 20px rgba(0,0,0,0.08);
		}
                    
		
		/* WhatsApp Button */
		.whatsapp-btn{
			position:fixed;
			bottom:20px;right:20px;
			background:#25d366;color:white;
			padding:14px 16px;border-radius:50%;
			font-size:26px;
			z-index:9999;
			box-shadow:0 4px 12px rgba(0,0,0,0.3);
		}
		
		/* Modal Header */
		#admissionModal .modal-header{
			background:linear-gradient(90deg,#003c8f,#1fa2ff);
			color:white;
        
		}
        .modal-dialog {
  			margin-left: auto !important;
  			margin-right: auto !important;
		}

            
     	/* Popup Notification */    
		.cps-popup-overlay{
			position:fixed;
			inset:0;
			background:rgba(0,0,0,0.7);
			z-index:100000;
			display:flex;
			justify-content:center;
			align-items:center;
		}

	.cps-popup-box{
		background:#6f74f8;
		width:360px;
		max-width:90%;
		border-radius:18px;
		overflow:hidden;
		position:relative;
		box-shadow:0 15px 40px rgba(0,0,0,0.4);
	}
	
	.cps-popup-close{
	position:absolute;
	top:8px;
	right:10px;
	background:white;
	border:none;
	border-radius:50%;
	width:30px;
	height:30px;
	font-size:18px;
	cursor:pointer;
	}
	
	.cps-popup-header{
	background:#d05a8b;
	color:#ffe800;
	text-align:center;
	font-size:22px;
	font-weight:800;
	padding:14px;
	}
	
	.cps-popup-body{
	padding:18px;
	text-align:center;
	color:white;
	}
	
	.popup-top{
	display:flex;
	justify-content:space-between;
	align-items:center;
	margin-bottom:10px;
	}
	
	.popup-logo{height:65px;}
	.popup-75{height:65px;}
	
	.blue{background:#0d47a1;padding:6px 12px;border-radius:8px;}
	.yellow{background:#ffeb3b;color:#000;padding:6px 12px;border-radius:8px;}
	.red{background:#e91e63;padding:6px 14px;border-radius:12px;}
	
	.discount-badge{
	background:white;
	color:#e65100;
	padding:10px;
	border-radius:50%;
	width:140px;
	height:140px;
	display:flex;
	justify-content:center;
	align-items:center;
	font-weight:800;
	margin:12px auto;
	}
	
	.popup-tagline{
	background:#ffeb3b;
	color:#000;
	padding:10px;
	border-radius:10px;
	font-weight:700;
	margin-bottom:12px;
	}
	
	.popup-campus{
	width:60%;
	height:50px
	border-radius:12px;
	margin-bottom:12px;
	}
	
	.popup-apply-btn{
	display:block;
	background:#ff9800;
	color:#003c8f;
	padding:12px;
	border-radius:12px;
	font-weight:800;
	text-decoration:none;
	}
	.popup-apply-btn:hover{background:#ffc107;}
	
				.stats-section {
	background:  #fff9e6;
	padding: 70px 20px;
	}
	/* Popup Notification */ 
            
    /* Stats Student count*/        
	.stats-container {
	max-width: 1200px;
	margin: auto;
	display: grid;
	grid-template-columns: repeat(4, 1fr);
	gap: 30px;
	text-align: center;
	}
	
	.stat-box {
	padding: 20px;
	}
	
	.stat-number {
	font-size: 64px;
	font-weight: 800;
	color: #f9a602; /* golden/orange like your screenshot */
	line-height: 1.1;
	}
	
	.stat-label {
	margin-top: 10px;
	font-size: 16px;
	font-weight: 600;
	letter-spacing: 1px;
	color: #222;
	text-transform: uppercase;
	}
	
	/* Responsive */
	@media (max-width: 992px) {
	.stats-container {
		grid-template-columns: repeat(2, 1fr);
	}
	}
	
	@media (max-width: 576px) {
	.stats-container {
		grid-template-columns: 1fr;
	}
	}
	/* Stats Student count*/ 
            
	/*Abouts Us Section*/
	.about-cps {
	background: #eaf6ff; /* light sky blue */
	padding: 80px 20px;
	}
	
	.about-wrapper {
	display: flex;
	align-items: center;
	justify-content: space-between;
	gap: 40px;
	flex-wrap: wrap;
	}
	
	.about-text {
	max-width: 600px;
	}
	
	.about-tag {
	background: #2b6cb0;
	color: #fff;
	display: inline-block;
	padding: 4px 10px;
	font-size: 14px;
	letter-spacing: 1px;
	}
	
	.about-title {
	font-size: 32px;
	margin: 10px 0;
	color: #003366;
	}
	
	.about-underline {
	display: block;
	width: 60px;
	height: 3px;
	background: #ff9800;
	margin-bottom: 20px;
	}
	
	.about-text p {
	font-size: 16px;
	line-height: 1.7;
	margin-bottom: 15px;
	color: #333;
	}
	
	.about-btn {
	display: inline-block;
	margin-top: 15px;
	padding: 10px 25px;
	background: #ff9800;
	color: #fff;
	text-decoration: none;
	border-radius: 25px;
	font-weight: 600;
	transition: 0.3s;
	}
	
	.about-btn:hover {
	background: #e68900;
	}
	
	.about-image {
	position: relative;
	max-width: 420px;
	}
	
	.about-image img {
	width: 100%;
	border-radius: 12px;
	object-fit: cover;
	}
	
	.since-badge {
	position: absolute;
	bottom: 20px;
	left: -20px;
	background: #355c8c;
	color: #fff;
	padding: 18px 22px;
	border-radius: 50%;
	text-align: center;
	font-weight: bold;
	line-height: 1.2;
	box-shadow: 0 4px 10px rgba(0,0,0,0.15);
	}
	
	/* Responsive */
	@media (max-width: 900px) {
	.about-wrapper {
		flex-direction: column;
		text-align: center;
	}
	
	.since-badge {
		left: 50%;
		transform: translateX(-50%);
	}
	}
	/*Abouts Us Section*/
				
	/* ================== TOPPERS & NEWS SECTION STYLES ================== */
	
	.toppers-news {
	background:#fff9e6;
	padding:80px 20px;
	font-family:'Segoe UI',sans-serif;
	}
	
	.tn-container {
	max-width:1200px;
	margin:auto;
	display:flex;
	gap:50px;
	align-items:flex-start;
	flex-wrap:wrap;
	}
	
	/* ===== STUDENT SLIDER ===== */
	
	.slider-wrapper {
	display:flex;
	align-items:center;
	gap:10px;
	}
	
	.slider-window {
	overflow:hidden;
	width:690px; /* 3 cards × 220px + gaps */
	}
	
	.slider-track {
	display:flex;
	gap:50px;
	transition:transform 0.5s ease;
	will-change:transform;
	}
	
	.student-card {
	width:220px;
	flex-shrink:0;
	text-align:center;
	}
	
	.student-card img {
	width:120px;
	height:120px;
	border-radius:50%;
	border:5px solid #eaeaea;
	object-fit:cover;
	}
	
	.student-card h5 {
	margin-top:12px;
	font-size:16px;
	color:#003c8f;
	}
	
	.student-card span {
	font-size:14px;
	color:#555;
	}
	
	.slider-btn {
	background:none;
	border:none;
	font-size:28px;
	cursor:pointer;
	color:#aaa;
	padding:10px;
	}
	
	.slider-btn:hover {
	color:#003c8f;
	}
	
	/* ===== NEWS ===== */
	
	.news-right {
	width:260px;
	}
	
	.news-box {
	height:160px;
	overflow:hidden;
	border-left:3px solid #ff4081;
	padding-left:15px;
	}
	
	.news-track {
	display:flex;
	flex-direction:column;
	animation:scrollNews 12s linear infinite;
	}
	
	.news-track p {
	margin:10px 0;
	font-size:15px;
	color:#333;
	}
	
	/* Smooth vertical loop */
	@keyframes scrollNews {
	0%   { transform:translateY(0); }
	100% { transform:translateY(-50%); }
	}
	
	/* ===== Responsive ===== */
	
	@media(max-width:768px){
	.slider-window { width:220px; } /* 1 card */
	.news-right { width:100%; margin-top:40px; }
	}
	
	/* ================== TOPPERS & NEWS SECTION STYLES ================== */
/* Memories Gallery Section*/
.memories-section {
  background:#fff9e6;
  padding: 0 0 80px;
  text-align: center;
  font-family: 'Segoe UI', sans-serif;
}

/* Banner */
.memories-banner {
  position: relative;
  width: 100%;
  height: 280px;
  overflow: hidden;
}

.memories-banner img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.memories-play {
  position: absolute;
  inset: 0;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 70px;
  color: white;
  background: rgba(0,0,0,0.2);
  cursor: pointer;
}

/* Heading */
.memories-heading {
  margin: 50px 0 30px;
}

.memories-sub {
  font-size: 14px;
  letter-spacing: 2px;
  color: #888;
}

.memories-heading h2 {
  font-size: 32px;
  font-weight: 700;
  color: #f9a602;
  margin: 10px 0;
}

.memories-underline {
  width: 60px;
  height: 3px;
  background: #ff4081;
  margin: auto;
  border-radius: 5px;
}

/* Gallery */
.memories-gallery {
  max-width: 1200px;
  margin: auto;
  display: flex;
  gap: 20px;
  padding: 0 20px;
  overflow-x: auto;
}

.memories-card {
  min-width: 220px;
  height: 160px;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 8px 20px rgba(0,0,0,0.1);
  flex-shrink: 0;
}

.memories-card img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: 0.4s;
}

.memories-card:hover img {
  transform: scale(1.05);
}

/* Mobile */
@media(max-width:768px){
  .memories-banner{ height:200px; }
  .memories-heading h2{ font-size:26px; }
}
/* Memories Gallery Section*/

		</style>
    
</head>

<body>
    
	<?php
	$showPopup = true; // you can later control this via date or session
	?>

	<?php if($showPopup): ?>
	<div id="admissionPopup" class="cps-popup-overlay">
	<div class="cps-popup-box">
		<button class="cps-popup-close" onclick="closePopup()">✕</button>
	
		<div class="cps-popup-header">
		Admission Open 2026 – 27
		</div>
	
		<div class="cps-popup-body">
		<div class="popup-top">
			<img src="images/logos/cpslogosvg.png" class="popup-logo" alt="CPS Logo">
			<img src="images/5years.png" class="popup-75" alt="75 Years">
		</div>
	
		<h2><span class="blue">School</span> <span class="yellow">Admission</span> <span class="red">2026</span></h2>
	
		<div class="discount-badge">20% Discount<br>for Nursery Admission</div>
	
		<p class="popup-tagline">
			Join a Legacy of Excellence<br>Your future starts here — Apply Today
		</p>
	
		<img src="images/campus.jpg" class="popup-campus" alt="Campus Image">
	
		<a href="https://forms.gle/1pn1PnsjpiSc72Ab9" target="_blank" class="popup-apply-btn">
			Apply Now
		</a>
		</div>
	</div>
	</div>
	<?php endif; ?>



	<!--===============Start Announcement ===============-->
	<div class="announcement-bar">
  		<div class="announcement-text">
    	🎓 Admissions Open 2025–26 • Smart Classrooms • CBSE Curriculum • Transport Available • Limited Seats!
  		</div>
	</div>
    <!--===============End Announcement ===============-->

	<!--===============Start Top Info Bar ===============-->
	<div class="cps-topbar">
	<div class="container d-flex justify-content-between">
		<div>📞 +91-XXXXXXXXXX | ✉️ info@chiragpublicschool.in</div>
		<div>CBSE Pattern • English Medium School</div>
	</div>
	</div>
	<!--===============End Top Info Bar ===============-->
    
	<!-- =============== Start of Header 1 Navigation =============== -->
    <?php include('includes/header.php'); ?>                              
    <!-- =============== End of Header 1 Navigation =============== -->


	<!--=============== Start Hero Slider ===============-->
	<section class="hero" style="margin-top:0;">
	<div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
		<div class="carousel-inner">
		<div class="carousel-item active"><img src="images/sliders/cpsslider1.png"></div>
		<div class="carousel-item"><img src="https://davberhampur.edu.in/File/13090/Message_9c82937d-8fae-46b0-8234-29d25c44c74f_Assembley%20Ground.jpeg"></div>
        <div class="carousel-item"><img src="https://media.istockphoto.com/id/1146896065/photo/school-children-jumping-and-celebrating-in-school-campus.jpg?s=612x612&w=0&k=20&c=Uu7MqBBXaQ_r1qspBuSq5JE8xVoS_p1C73F0fsDivrQ="></div>
            <div class="carousel-item"><img src="https://media.istockphoto.com/id/1148232091/photo/teacher-explaining-to-students-using-digital-tablet.jpg?s=612x612&w=0&k=20&c=jT-_JQ_IEBXhKUGtbtI98dJtPIb20ovr0WgrvvMsXvU="></div>
		</div>
	</div>
	</section>
	<!--=============== End Hero Slider ===============-->
    
	<!-- ===== Start Counter Section ===== -->
	<section class="stats-section">
	<div class="stats-container">
		<div class="stat-box">
		<div class="stat-number" data-target="450">0</div>
		<div class="stat-label">STUDENT STRENGTH</div>
		</div>
		<div class="stat-box">
		<div class="stat-number" data-target="40">0</div>
		<div class="stat-label">TEACHING STAFF</div>
		</div>
		<div class="stat-box">
		<div class="stat-number" data-target="15">0</div>
		<div class="stat-label">NON-TEACHING STAFF</div>
		</div>
		<div class="stat-box">
		<div class="stat-number" data-target="1">0</div>
		<div class="stat-label">INSTITUTIONS</div>
		</div>
	</div>
	</section>
	<!-- ===== End Start Section ===== -->

	<!-- ===== Start Abouts US Section Home Page ===== -->
	<section class="about-cps">
	<div class="container">
		<div class="about-wrapper">
	
		<div class="about-text">
			<h4 class="about-tag">KNOW</h4>
			<h2 class="about-title">ABOUT US</h2>
			<span class="about-underline"></span>
	
			<p>
			Chirag Public School (CPS) was founded in the year 2020 with a vision to provide
			high-quality, value-based education that nurtures young minds and builds strong character.
			</p>
	
			<p>
			Since its inception, CPS has focused on holistic development through academics,
			co-curricular activities, and moral education — creating a safe and inspiring
			environment for students to learn, grow, and succeed.
			</p>
	
			<a href="aboutus.php" class="about-btn">READ MORE</a>
		</div>
	
		<div class="about-image">
			<div class="since-badge">SINCE<br>2020</div>
			<img src="images/campus.jpg" alt="Chirag Public School">
		</div>
	
		</div>
	</div>
	</section>
	<!-- ===== End Abouts US Section Home Page ===== -->

	<!-- ================= START TOPPERS + NEWS SECTION ================= -->
	<section class="toppers-news">
	<div class="tn-container">
	
		<!-- LEFT : STUDENT SLIDER -->
		<div class="toppers-left">
		<h4 class="about-tag">ACADEMIC YEAR 2024–25</h4>
		<h2 class="about-title">GRADE 10 CBSE & SSLC STATE TOPPERS</h2>
		<span class="about-underline"></span>
	
		<div class="slider-wrapper">
			<button class="slider-btn prev" onclick="slideLeft()">&#10094;</button>
	
			<div class="slider-window">
			<div class="slider-track" id="studentSlider">
	
				<!-- Student Card 1-->
				<div class="student-card">
				<img src="images/students/1.png">
				<h5>Anatha Krishna R</h5>
				<span>96.32%</span>
				</div>
	
				<!-- Student Card 2-->
				<div class="student-card">
				<img src="images/students/2.png">
				<h5>Ranjitha Shekhar</h5>
				<span>96.32%</span>
				</div>
	
				<!-- Student Card 3-->
				<div class="student-card">
				<img src="images/students/3.png">
				<h5>Bhanu Prakash R</h5>
				<span>94.40%</span>
				</div>
	
				<!-- Student Card 4-->
				<div class="student-card">
				<img src="images/students/4.png">
				<h5>Sakshathalva</h5>
				<span>93.12%</span>
				</div>
				
				<!-- Student Card 5-->
				<div class="student-card">
				<img src="images/students/5.png">
				<h5>Rajesh Kumar Shaoo</h5>
				<span>92.82%</span>
				</div>
				
				<!-- Student Card 6-->
				<div class="student-card">
				<img src="images/students/6.png">
				<h5>Simran Panigrahy</h5>
				<span>91.93%</span>
				</div>
				
				<!-- Student Card 7-->
				<div class="student-card">
				<img src="images/students/7.png">
				<h5>Himanshu Swain</h5>
				<span>90.93%</span>
				</div>
	
			</div>
			</div>
	
			<button class="slider-btn next" onclick="slideRight()">&#10095;</button>
		</div>
		</div>
	
		<!-- RIGHT : NEWS -->
		<div class="news-right">
		<h4 class="about-tag">LATEST</h4>
		<h2 class="about-title">NEWS</h2>
		<span class="about-underline"></span>
	
		<div class="news-box">
			<div class="news-track">
			<p>🎉 5 Years Completetion Celebrations</p>
			<p>📢 Admission Open for 2026–27</p>
			<p>🏫 CPS Ranked Among Top Schools</p>
			<p>🎓 Smart Classrooms Introduced</p>
	
			<!-- Duplicate for loop -->
			<p>🎉 5 Years Completetion Celebrations</p>
			<p>📢 Admission Open for 2026–27</p>
			<p>🏫 CPS Ranked Among Top Schools</p>
			<p>🎓 Smart Classrooms Introduced</p>
			</div>
	
		</div>
		</div>
	
	</div>
	</section>
	<!-- ================== END : TOPPERS & NEWS SECTION ================== -->

	
<!-- ===== Start Founder Section ===== -->
<section class="about-cps">
  <div class="container">
    <div class="about-wrapper">

      <!-- LEFT : Founder Image -->
      <div class="about-image" style="justify-content:center;">
        <img src="images/founder.jpg" 
             alt="Founder Mr. XXXXX"
             style="width:280px;height:280px;border-radius:50%;object-fit:cover;">
      </div>

      <!-- RIGHT : Founder Content -->
      <div class="about-text">
        <h4 class="about-tag">FOUNDER</h4>
        <h2 class="about-title" style="color:#ffb300;">MR. XXXXX</h2>
        <span class="about-underline"></span>

        <p style="margin-top:20px;">
          Chirag Public School (CPS) was founded under the guidance and vision of Mr. XXXXX with the aim of providing
          quality, value-based education that empowers young minds and shapes responsible future citizens.
        </p>

        <p>
          With a deep belief in the transformative power of education, Mr. XXXXX laid the foundation of CPS in 2020
          to create a learning environment that nurtures academic excellence, discipline, creativity, and moral values.
        </p>

        <p>
          Under his leadership, CPS has grown into a modern educational institution equipped with smart classrooms,
          experienced faculty, and a student-centered approach that focuses on holistic development.
        </p>

      </div>

    </div>
  </div>
</section>
<!-- ===== End Founder Section ===== -->
    
<!-- ================= Memories Gallery Section ================= -->
<section class="memories-section">

  <!-- Top Banner -->
  <div class="memories-banner">
    <img src="https://media.istockphoto.com/id/1148218795/photo/children-cheering-in-classroom.jpg?s=612x612&w=0&k=20&c=gDHpvQfL4-El6mAFCKSsg_OVke-Y_cQT1DEGMXzkuQk=" alt="School Memories Banner">
    <div class="memories-play">▶</div>
  </div>

  <!-- Heading -->
  <div class="memories-heading">
    <span class="about-tag">MEMORIES</span>
    <h2>PHOTO GALLERY</h2>
    <div class="memories-underline"></div>
  </div>

  <!-- Gallery Row -->
  <div class="memories-gallery">
    <div class="memories-card"><img src="https://media.istockphoto.com/id/1392743492/photo/happy-kids-in-uniform-waving-indian-flag-by-looking-camera-at-school-corridor-concept-of.jpg?s=612x612&w=0&k=20&c=W0q0xN5Yb2yJEBR27rePpShR5ynx4f2DtQBoBdSCRnQ=" alt=""></div>
    <div class="memories-card"><img src="images/gallery/2.jpg" alt=""></div>
    <div class="memories-card"><img src="images/gallery/3.jpg" alt=""></div>
    <div class="memories-card"><img src="images/gallery/4.jpg" alt=""></div>
    <div class="memories-card"><img src="images/gallery/5.jpg" alt=""></div>
  </div>

</section>
<!-- ================= End Memories Gallery Section ================= -->



	
	<!--=============== Start WhatsApp Button ===============-->
	<a href="https://wa.me/919777499997" class="whatsapp-btn">💬</a>
	
	<!--=============== Start Admission Modal ===============-->
    
	<!-- Apply Button -->
	<div class="text-center my-4">
  		<button type="button" class="cps-apply-btn btn btn-warning"
          data-bs-toggle="modal" data-bs-target="#applyModal">
    	Apply Now
  		</button>
	</div>
    
	<!-- Model Trey -->
<div class="modal fade" id="applyModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Admission Application</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-0">
        <iframe 
          src="https://docs.google.com/forms/d/e/1FAIpQLSdt1s_43Y7nfcETDas0KFtZQtRc5CjDVBWw4ofSQ2LkgI6hRQ/viewform?embedded=true"
          style="width:100%;height:80vh;border:none;">
        </iframe>
      </div>
    </div>
  </div>
</div>

   <!--=============== End Admission Modal ===============-->
	
	
    
    <!-- =============== Start of Footer 1 Navigation =============== -->
    <?php include('includes/footer.php'); ?>                              
    <!-- =============== End of Footer 1 Navigation =============== -->

	
	<!-- ===== All Javascript at the bottom of the page for faster page loading ===== -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!--=============== Start of Toggle Menu  ===============-->
    <script>
	function toggleMenu(){
  	const nav = document.getElementById("cpsNav");
  	nav.classList.toggle("active");
	}
	</script>
    <!--=============== End of Toggle Menu  ===============-->
    <!--=============== Start of admission Popup  ===============-->
    <script>
	function closePopup(){
  	document.getElementById("admissionPopup").style.display="none";
	}
	</script>
    <!--=============== End of admission Popup  ===============-->
	
    <!--=============== Start of Animated Counting  ===============-->
    <script>
	document.addEventListener("DOMContentLoaded", function () {
	const counters = document.querySelectorAll('.stat-number');
	const duration = 5000; // total animation time in ms (3 seconds)
	
	counters.forEach(counter => {
		const target = +counter.getAttribute('data-target');
		const start = 0;
		const startTime = performance.now();
	
		function update(currentTime) {
		const elapsed = currentTime - startTime;
		const progress = Math.min(elapsed / duration, 1);
		const value = Math.floor(progress * target);
	
		counter.innerText = value;
	
		if (progress < 1) {
			requestAnimationFrame(update);
		} else {
			counter.innerText = target;
		}
		}
	
		requestAnimationFrame(update);
	});
	});
	</script>
	<!--=============== End of Animated Counting  ===============-->
    
    <!--=============== Start of Topper & News Section  ===============-->
    <script>
document.addEventListener("DOMContentLoaded", function () {

  const slider = document.getElementById('studentSlider');
  if (!slider) return;

  const cardWidth = 270; // 220 + 50 gap

  slider.innerHTML += slider.innerHTML;

  let position = 0;

  function slideRight() {
    position -= cardWidth;
    if (Math.abs(position) >= slider.scrollWidth / 2) {
      position = 0;
    }
    slider.style.transform = `translateX(${position}px)`;
  }

  function slideLeft() {
    position += cardWidth;
    if (position > 0) {
      position = -slider.scrollWidth / 2 + cardWidth;
    }
    slider.style.transform = `translateX(${position}px)`;
  }

  window.slideRight = slideRight;
  window.slideLeft = slideLeft;

  setInterval(slideRight, 3000);
});
</script>

	
	<!--=============== End of Topper & News Section  ===============-->




</body>
</html>
