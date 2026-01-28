<!--======================================================
    File Name   : index.php
    Project     : RMIT Groups - HIT
    Description : Home Page 
    Developed By: TrinityWebEdge
    Date Created: 17-11-2025
    Last Updated: <?php echo date("d-m-Y"); ?>
    Note        : This page defines the Home page of RMIT Groups website.
=======================================================-->

<?php
// Latest News - Holy Institute of Technology (HIT)
$latest_news = [
    "Admissions Open for Diploma Engineering (Academic Year 2025–26)",
    "Semester Examination Schedule Announced for All Diploma Branches",
    "Application Open for Rechecking / Retotalling of Semester Results",
    "Internal Assessment & Practical Examination Timetable Published",
    "Technical Workshop on Emerging Engineering Technologies at HIT",
    "Campus Training & Placement Drive Announced for Final Year Diploma Students",
    "Thought of the Day: Engineering transforms ideas into reality"
];
?>


<!DOCTYPE html>

<head>
    <meta charset="UTF-8">

    <!-- Compatibility & Mobile -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

    <!-- Primary Meta Tags -->
    <title>HIT - Holy Institute of Technology, Berhampur | RMIT Group of Institutions | Empowering Minds, Building Futures</title>
    <meta name="description"
          content="Holy Institute of Technology (HIT) is a premier Diploma Engineering College in Berhampur, Odisha, under the RMIT Group of Institutions, offering quality technical 					   education.">
    <meta name="keywords"
          content="Holy Institute of Technology, HIT Berhampur, RMIT Group, Diploma Engineering College, Polytechnic College Odisha, Technical Education Berhampur">
    <meta name="author" content="RMIT Group of Institutions">
    <meta name="robots" content="index, follow">

    <!-- Favicons -->
	<link rel="icon" href="images/favicon.ico" type="image/x-icon">
	<link rel="icon" href="images/favicon_32.png" sizes="32x32" type="image/png">
	<link rel="icon" href="images/favicon_64.png" sizes="64x64" type="image/png">
	<link rel="icon" href="images/favicon_180.png" sizes="180x180" type="image/png">
	<link rel="apple-touch-icon" href="images/favicon_180.png">

    <!-- Open Graph / Social Media -->
    <meta property="og:title" content="HIT - Holy Institute of Technology, Berhampur | Empowering Minds, Building Futures">
    <meta property="og:description"
          content="Empowering future engineers through quality diploma education at Holy Institute of Technology, Berhampur.">
    <meta property="og:type" content="website">
    <meta property="og:image" content="images/og-hit.jpg">
    <meta property="og:site_name" content="Holy Institute of Technology">

    <!-- Google Fonts (Optimized) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;600;700;800&family=Varela+Round&display=swap" rel="stylesheet">

    <!-- CSS Files -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/swiper.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">
              
    <link rel="preconnect" href="https://chatling.ai">

    <!-- IE8 Support -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    
</head>

<body>

    <!-- =============== Start of Header 1 Navigation =============== -->
    
    <!-- =============== Start of Header 1 Navigation =============== -->
    	<?php include('includes/header.php'); ?>                              
    <!-- =============== End of Header 1 Navigation =============== -->


    <!-- =============== End of Header 1 Navigation =============== -->

    <!-- ========== Start of Main Slider Section ========== -->
    <section class="main2">

        <!-- ===== Start of Swiper Slider ===== -->
        <div class="swiper-container">
            <div class="swiper-wrapper">

                <!-- Start of Slide 1 -->
                <div class="swiper-slide overlay-black" style="background: url('/hit/images/slider1.jpg'); background-size: cover; background-position: 50% 50%;">                 
                    <div class="slider-content container">
                        <div class="col-md-6 col-md-offset-6 col-xs-12 text-center">
                            <div class="section-title">
                            <h2 class="text-white">your career start now!</h2>
                            </div>
                            <p class="text-white">Approved by AICTE, New Delhi, Affiliated to S. C. T. E & V. T., Odisha and Recognised by Govt. of Odisha
							</p>
                        </div>
                    </div>
                </div>
                <!-- End of Slide 1 -->


                <!-- Start of Slide 2 -->
                <div class="swiper-slide overlay-black" style="background: url('/hit/images/slider2.jpg'); background-size: cover; background-position: 50% 50%;">
                    <div class="slider-content container">
                        <div class="col-md-6 col-xs-12 text-center">
                            <div class="section-title">
                                <h2 class="text-white">your career start now!</h2>
                            </div>
                            <p class="text-white">DIPLOMA ENGINEERING</p>
                        </div>
                    </div>
                </div>
                <!-- End of Slide 2 -->
                
                 <!-- Start of Slide 3 -->
                <div class="swiper-slide overlay-black" style="background: url('/hit/images/slider3.png'); background-size: cover; background-position: 50% 50%;">
                    <div class="slider-content container">
                        <div class="col-md-6 col-xs-12 text-center">
                            <div class="section-title">
                                <h2 class="text-white">Building Skilled Engineers for a Better Tomorrow</h2>
                            </div>
                            <p class="text-white">HIT empowers students with strong technical foundations, practical training, and industry-ready skills across all engineering 										disciplines.
                            </p>
                        </div>
                    </div>
                </div>
                <!-- End of Slide 3 -->
                
                <!-- Start of Slide 3 -->
                <div class="swiper-slide overlay-black" style="background: url('/hit/images/slider4.png'); background-size: cover; background-position: 50% 50%;">
                    <div class="slider-content container">
                        <div class="col-md-6 col-xs-12 text-center">
                            <div class="section-title">
                                <h2 class="text-white">Your Future Starts Here at HIT</h2>
                            </div>
                            <p class="text-white">At Holy Institute of Technology, we inspire learning, innovation, and confidence to shape leaders of tomorrow.</p>
                        </div>
                    </div>
                </div>
                <!-- End of Slide 3 -->

            </div>
            <!-- End of Swiper Wrapper -->

            <!-- Navigation Buttons -->
            <div class="swiper-button-prev"><i class="fa fa-angle-left"></i></div>
            <div class="swiper-button-next"><i class="fa fa-angle-right"></i></div>

        </div>
        <!-- ===== End of Swiper Slider ===== -->

    </section>
    <!-- ========== End of Main Slider Section ========== -->

   <!--=============== Start of HIT Latest News Section ===============-->   

	<section>
	<div class="latest-news-bar" style="background: linear-gradient(90deg, #5e1212 0%, #7b1e1e 45%, #a83232 100%);">
	<div class="container-fluid">
		<div class="row align-items-center no-gutters">
	
		<!-- Label -->
		<div class="col-auto p-0">
			<div class="latest-news-label">
			<i class="fa fa-bullhorn"></i> Latest News
			</div>
		</div>
	
		<!-- Marquee -->
		<div class="col overflow-hidden">
			<div class="news-marquee">
	
			<?php foreach ($latest_news as $news) { ?>
				<span class="news-item">
				<i class="fa fa-circle news-dot"></i>
				<?= htmlspecialchars($news) ?>
				</span>
			<?php } ?>
	
			<!-- Duplicate for seamless loop -->
			<?php foreach ($latest_news as $news) { ?>
				<span class="news-item">
				<i class="fa fa-circle news-dot"></i>
				<?= htmlspecialchars($news) ?>
				</span>
			<?php } ?>
	
			</div>
		</div>
	
		</div>
	</div>
	</div>
	</section>
	<!-- ===== End of HIT Latest News Section ===== -->

    <!-- ========== Start of Home Page Hero Section ==========-->
	<section class="shop ptb80" 
	style="background: linear-gradient(135deg, #fff2f2, #f4dada);">	
	
	<div class="container">
	
	<!-- Start of Row -->
	<div class="row">
	<div class="col-md-12 product-wrapper">
	
	<!-- ===== Start of Row ===== -->
	<div class="row">
	
	<!-- ===== Left Image Column ===== -->
	<div class="col-md-6 col-xs-12 about-video">                
		<div class="item">
			<img src="/images/hit.png"
			class="img-responsive"
			alt="Holy Institute of Technology"
			loading="lazy"
			decoding="async"
			style="width:100%; height:600px; border-radius:16px;
			box-shadow:0 15px 40px rgba(123,30,30,0.35);">
		</div>
	</div>
	
	<!-- ===== Right Content Column ===== -->
	<div class="col-md-6 col-xs-12">
	
		<h3 class="pt05"
		style="font-weight:800;
		background: linear-gradient(90deg, #7b1e1e, #b03030, #e05c5c);
		-webkit-background-clip:text;
		-webkit-text-fill-color:transparent;">
		Empowering Minds & Building Futures
		</h3>
		
		<!-- Divider -->
		<div style="height:5px;
		background: linear-gradient(90deg, #7b1e1e, #e05c5c);
		margin:10px 0 15px;
		border-radius:70px;
		width:100%;">
		</div>
		
		<p class="pt20" style="color:#3a0f0f; font-weight:500; line-gap:1.5px">
		The vision of Holy Institute of Technology (HIT) is centered on building a strong and purposeful engineering ecosystem driven by knowledge, ethics, and social responsibility.
		Our foremost objective is to nurture technically competent diploma engineers who are innovative, skilled, and ethically grounded, with a strong commitment to applying 					engineering solutions for the betterment of society. We aim to instill a sense of responsibility, professionalism, and integrity in every learner, ensuring that technical 				excellence is matched with moral values.In alignment with the demands of the 21st century, HIT strives to develop leadership qualities among students, faculty, and alumni by 			fostering adaptability, critical thinking, and collaborative problem-solving. We recognize that modern engineering professionals must be capable leaders who can navigate rapid 		technological and societal changes.
		The institution is equally dedicated to the creation and dissemination of practical knowledge, emerging technologies, and sustainable engineering practices that positively 			impact communities and contribute to national development. Through education, innovation, and industry engagement, HIT seeks to support social well-being and environmental 			sustainability.
		</p><br>
		<p style="color:#3a0f0f; font-weight:500;">Choosing HIT means choosing a future driven by skills, ethics, and engineering excellence.
		</p>
		
		<a href="aboutus.php"
		class="btn btn-danger btn-effect mt20">
		Explore HIT
		</a>
		
		</div>
	</div>
	<!-- ===== End Row ===== -->
	
	
	<!-- ================= Important Links ================= -->
	<div class="ptb80">
		
		<h2 style="
		text-align:center;
		font-size:36px;
		font-weight:800;
		background: linear-gradient(90deg, #7b1e1e, #b03030, #e05c5c);
		-webkit-background-clip:text;
		-webkit-text-fill-color:transparent;
		margin-bottom:50px;">
		Important Links
		<span style="
		display:block;
		width:20%;
		height:5px;
		background:linear-gradient(90deg, #7b1e1e, #e05c5c);
		margin:12px auto;
		margin-bottom:20px;
		border-radius:50px;">
		</span>
		</h2>
		
		<div class="mt30" style="
		display:grid;
		grid-template-columns:repeat(auto-fit, minmax(260px, 1fr));
		gap:60px;
		max-width:1100px;
		margin:auto;">
		
		<a href="DownloadAdmissionForm.php" class="rmit-link admission">
		<img src="/rmit/images/icons/admission.png" class="img-responsive" alt="admission logo" loading="lazy" decoding="async">
		<span>Admission</span>
		<i class="fa fa-arrow-right"></i>
		</a>
		
		<a href="feepayment.php" target="_balnk" class="rmit-link payment">
		<img src="/rmit/images/icons/payment.png" class="img-responsive" alt="Payment logo" loading="lazy" decoding="async">
		<span>Fee Payment</span>
		<i class="fa fa-arrow-right"></i>
		</a>
		
		<a href="assets/pdf/HIT_Holiday_List.pdf" target="_balnk" class="rmit-link holiday" >
		<img src="/rmit/images/icons/holiday.png" class="img-responsive" alt="holiday logo" loading="lazy" decoding="async">
		<span>Holiday List</span>
		<i class="fa fa-arrow-right"></i>
		</a>
		
		<a href="#" class="rmit-link alumni">
		<img src="/rmit/images/icons/alumni.png" class="img-responsive" alt="alumni logo" loading="lazy" decoding="async">
		<span>Alumni</span>
		<i class="fa fa-arrow-right"></i>
		</a>
		
		<a href="#" target="_balnk" class="rmit-link feedback">
		<img src="/rmit/images/icons/feedback1.png" class="img-responsive" alt="feedback logo" loading="lazy" decoding="async">
		<span>Feedback</span>
		<i class="fa fa-arrow-right"></i>
		</a>
		
		<a href="/rmit-smartcampus/index.php" target="_balnk" class="rmit-link smartcampus">
		<!--<img src="/rmit/images/icons/rmitsmart1.png" class="img-responsive">-->
		<img src="/rmit/images/icons/rmit-smrtclogo.png" class="img-responsive" alt="RMIT-SmartCampus logo" loading="lazy" decoding="async">
		<span>RMIT-SmartCampus</span>
		<i class="fa fa-arrow-right"></i>
		</a>
		
		</div>
	</div>
	<!-- ================= End Important Links ================= -->
	
	
	<!-- ================= Branches ================= -->
	<div class="row mt60">
		<div class="col-md-12">
		
		<h3 style="font-weight:800;
		background: linear-gradient(90deg, #7b1e1e, #b03030);
		-webkit-background-clip:text;
		-webkit-text-fill-color:transparent;">
		Engineering Courses
		</h3>
		
		<div style="height:5px;
		background: linear-gradient(90deg, #7b1e1e, #e05c5c);
		margin:10px 0 20px;
		border-radius:70px;
		width:20%;">
		</div>
	
	</div>
	
	<!-- Branch Cards -->
	<div class="col-md-3 col-sm-6 col-xs-12 mt40">
		<div class="product nomargin">
		<div class="product-image">
		<img src="images/mechanical.jpg" class="img-responsive">
			<!-- Seats Badge -->
			<div style="
				position:absolute;
				top:25%;
				right:0;
				transform:translateY(-50%);
				background:linear-gradient(90deg, #7b1e1e, #b03030, #e05c5c);
				color:#fff;
				padding:12px 18px;
				font-size:18px;
				font-weight:600;
				border-radius:8px 0 0 8px;
				box-shadow:0 4px 12px rgba(0,0,0,0.3);
			">
				120 Seats
			</div>
			<!-- Seats Badge -->
		</div>
		<div class="product-descr">
		<a href="MechanicalEngg.php"><h5>Mechanical Engg</h5></a>
		</div>
		</div>
		</div>

		
		<div class="col-md-3 col-sm-6 col-xs-12 mt40">
		<div class="product nomargin">
		<div class="product-image">
		<img src="images/electrical.jpg" class="img-responsive">
			<!-- Seats Badge -->
			<div style="
				position:absolute;
				top:25%;
				right:0;
				transform:translateY(-50%);
				background:linear-gradient(90deg, #7b1e1e, #b03030, #e05c5c);
				color:#fff;
				padding:12px 18px;
				font-size:18px;
				font-weight:600;
				border-radius:8px 0 0 8px;
				box-shadow:0 4px 12px rgba(0,0,0,0.3);
			">
				90 Seats
			</div>
			<!-- Seats Badge -->
		</div>
		<div class="product-descr">
		<a href="ElectricalEngg.php"><h5>Electrical Engg</h5></a>
		</div>
		</div>
		</div>
		
		<div class="col-md-3 col-sm-6 col-xs-12 mt40">
		<div class="product nomargin">
		<div class="product-image">
		<img src="images/civil.jpg" class="img-responsive">
			<!-- Seats Badge -->
			<div style="
				position:absolute;
				top:25%;
				right:0;
				transform:translateY(-50%);
				background:linear-gradient(90deg, #7b1e1e, #b03030, #e05c5c);
				color:#fff;
				padding:12px 18px;
				font-size:18px;
				font-weight:600;
				border-radius:8px 0 0 8px;
				box-shadow:0 4px 12px rgba(0,0,0,0.3);
			">
				60 Seats
			</div>
			<!-- Seats Badge -->
		</div>
		<div class="product-descr">
		<a href="CivilEngg.php"><h5>Civil Engg</h5></a>
		</div>
		</div>
		</div>
		
		<div class="col-md-3 col-sm-6 col-xs-12 mt40">
		<div class="product nomargin">
		<div class="product-image">
		<img src="images/computersc.jpg" class="img-responsive">
			<!-- Seats Badge -->
			<div style="
				position:absolute;
				top:25%;
				right:0;
				transform:translateY(-50%);
				background:linear-gradient(90deg, #7b1e1e, #b03030, #e05c5c);
				color:#fff;
				padding:12px 18px;
				font-size:18px;
				font-weight:600;
				border-radius:8px 0 0 8px;
				box-shadow:0 4px 12px rgba(0,0,0,0.3);
			">
				30 Seats
			</div>
			<!-- Seats Badge -->
		</div>
		<div class="product-descr">
		<a href="ComputerScienceEngg.php"><h5>Computer Science Engg</h5></a>
		</div>
		</div>
	    </div>
	
	</div>
	<!-- ================= End Branches ================= -->
	
		<!-- ========== End of Home Page Hero Section ==========-->
				
		<!-- ========== Start of HIT Attraction & Impact Section ========== -->
	<div class="container" style="margin-top:25px">
	
	<!--==== Section Title =====-->
	<div class="text-center mb40" style="margin-top: 20px;">
		<h2 style="font-weight:800;
					background: linear-gradient(90deg, #7b1e1e, #b03030, #e05c5c);
					-webkit-background-clip:text;
					-webkit-text-fill-color:transparent;">
			Shaping Skilled Engineers, Building a Better Tomorrow</h2>
		<p style="font-size:16px; font-weight:600; color:black;">Student Growth & Technical Excellence</p>
		<!-- Centered Divider -->
			<div style="
					height:5px;
					background: linear-gradient(90deg, #7b1e1e, #e05c5c);
					margin:10px auto 15px;
					border-radius:70px;
					width:55%;
				">
			</div>
	</div>
	
		<!--===== Start Student & Academic Attraction =====-->
		<div class="row mt40">
			<div class="col-md-6">
			<div class="card-box">
				<h3 style="
					text-align:left;
					font-size:36px;
					font-weight:800;
					background: linear-gradient(90deg, #7b1e1e, #b03030, #e05c5c);
					-webkit-background-clip:text;
					-webkit-text-fill-color:transparent;
					margin-bottom:50px;">
				🎓 Student Attraction</h3>
				<hr>
				<ul class="attraction-list">
				<li>✔ Student-centric learning environment with strong mentoring support</li>
				<li>✔ Well-equipped laboratories and modern classrooms</li>
				<li>✔ Active participation in technical events, workshops, and industrial visits</li>
				<li>✔ Skill development programs focused on employability and entrepreneurship</li>
				<li>✔ Safe, disciplined, and value-based campus culture</li>
				<li>✔ Scholarships and government support schemes for eligible students</li>
				</ul>
			</div>
			</div>
		
			<div class="col-md-6">
			<div class="card-box">
				<h3 style="
					text-align:left;
					font-size:36px;
					font-weight:800;
					background: linear-gradient(90deg, #7b1e1e, #b03030, #e05c5c);
					-webkit-background-clip:text;
					-webkit-text-fill-color:transparent;
					margin-bottom:50px;">
				📚 Academic Excellence</h3>
				<hr>
				<ul class="attraction-list">
				<li>✔ Diploma engineering programs approved by AICTE, New Delhi, Affiliated to S. C. T. E & V. T., Odisha and Recognised by Govt. of Odisha</li>
				<li>✔ Experienced faculty with strong academic and industry background</li>
				<li>✔ Practice-oriented curriculum with hands-on training</li>
				<li>✔ Emphasis on ethical values, discipline, and professional skills</li>
				<li>✔ Industry-relevant training preparing students for jobs and higher education</li>
				</ul>
			</div>
			</div>
		</div>
		<!--===== End Student & Academic Attraction =====-->
		
		<!--===== Start Impact Stats =====-->
		<div class="text-center mb40 mt60">
			<h2 style="font-weight:800;
					background: linear-gradient(90deg, #7b1e1e, #b03030, #e05c5c);
					-webkit-background-clip:text;
					-webkit-text-fill-color:transparent;">
				Numbers that inspire confidence, milestones</h2>
			<p style="font-size:16px; font-weight:600; color:black;">A legacy of scale, excellence, and innovation</p><br>
			<!-- Centered Divider -->
			<div style="
					height:5px;
					background: linear-gradient(90deg, #7b1e1e, #e05c5c);
					margin:10px auto 15px;
					border-radius:70px;
					width:55%;
				">
			</div>
			</div>
		
			<div class="row text-center">
			<!--==== Stat Box 1 ====-->
			<div class="col-md-2 col-sm-4 col-xs-6 stat-box">
				<div class="stat-card">
				<i class="fas fa-user-graduate fa-2x"></i>
				<h4>300+</h4>
				<p>Students</p>
				</div>
			</div>
			
			<!--==== Stat Box 2 ====-->
			<div class="col-md-2 col-sm-4 col-xs-6 stat-box">
				<div class="stat-card">
				<i class="fas fa-chalkboard-teacher fa-2x"></i>
				<h4>45+</h4>
				<p>Faculty & Staff</p>
				</div>
			</div>
			
			<!--==== Stat Box 3 =====-->
			<div class="col-md-2 col-sm-4 col-xs-6 stat-box">
				<div class="stat-card">
				<i class="fas fa-user-tie fa-2x"></i>
				<h4>4500+</h4>
				<p>Alumni</p>
				</div>
			</div>
			
			<!--==== Stat Box 4 =====-->
			<div class="col-md-2 col-sm-4 col-xs-6 stat-box">
				<div class="stat-card">
				<i class="fas fa-brain fa-2x"></i>
				<h4>7500+</h4>
				<p>Projects</p>
				</div>
			</div>
			
			<!--==== Stat Box 5 ====-->
			<div class="col-md-2 col-sm-4 col-xs-6 stat-box">
				<div class="stat-card">
				<i class="fas fa-laptop-code fa-2x"></i>
				<h4>7,000+</h4>
				<p>Conferences</p>
				</div>
			</div>
			
			<!--==== Stat Box 6 ====-->
			<div class="col-md-2 col-sm-4 col-xs-6 stat-box">
				<div class="stat-card">
				<i class="fas fa-trophy fa-2x"></i>
				<h4>150+</h4>
				<p>Achievements</p>
				</div>
			</div>
			</div>
		
		
			<!-- Call to Action -->
			<!--<div class="text-center mt40">
			<a href="admissions.php" class="btn btn-blue btn-effect">Apply Now</a>
			<a href="aboutus.php" class="btn btn-blue btn-effect">Discover More</a>
			</div>-->
			
		</div>
			<style>
			/* ====== Stats ====== */
			.stat-card {
					background: #ffffff;
					border-radius: 14px;
					padding: 20px;
					box-shadow: 0 4px 12px rgba(0,0,0,0.12);
					transition: all 0.35s ease;
					color: #3a0f0f;
					}
			
			/* Radiant maroon hover */
			.stat-card:hover {
					background: linear-gradient(
						135deg,
						#5e1212 0%,
						#7b1e1e 65%,
						#a83232 10%
					);
			color: #ffffff;
					transform: translateY(-8px);
					box-shadow:
						0 10px 28px rgba(123, 30, 30, 0.55),
						0 0 18px rgba(168, 50, 50, 0.45);
					}
			
			/* Optional: icons & numbers glow */
					.stat-card:hover i,
					.stat-card:hover h4 {
					text-shadow: 0 0 10px rgba(255, 200, 200, 0.9);
					}
			</style>
		
	</section>
	<!-- ========== End of RMIT Attraction & Impact Section ========== -->
	
    <!-- ========== End of Home Page Mian Section ==========-->
        
    <!-- ===== Start of Career Outcomes Section ===== -->
<section class="get-started ptb40" style="background: linear-gradient(90deg, #5e1212, #7b1e1e, #a83232);">
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-sm-9 col-xs-12">
                <h3 class="text-white text-center">Career Outcomes & Placements at HIT</h3>
            </div>
        </div>
    </div>
</section>
<!-- ===== End of Career Outcomes Section ===== -->


<!-- ===== Start of HIT Placement Highlights Section ===== -->
<section class="ptb80" id="hit-placements" style="background: linear-gradient(135deg, #fbeeee, #efd1d1);">
<div class="container">

    <!-- Section Intro -->
    <div class="text-center mb60">
        <p style="font-size:16px; font-weight:600; color:#3a0f0f; max-width:850px; margin:auto;">
            Our <strong>Diploma Engineering students and alumni</strong> are building successful careers across
			manufacturing, infrastructure, electrical, and technology-driven industries.
        </p>
    </div>

    <!-- Stats Row -->
    <div class="row text-center mb60">

        <div class="col-md-3 col-sm-6 mb30">
            <div class="stat-card">
                <i class="fas fa-chart-line fa-2x"></i>
                <h4>70%+</h4>
                <p>Placement Rate</p>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb30">
            <div class="stat-card">
                <i class="fas fa-user-graduate fa-2x"></i>
                <h4>3200+</h4>
                <p>Successful Alumni</p>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb30">
            <div class="stat-card">
                <i class="fas fa-building fa-2x"></i>
                <h4>25+</h4>
                <p>Industry Partners</p>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb30">
            <div class="stat-card">
                <i class="fas fa-briefcase fa-2x"></i>
                <h4>₹1.8–3.5 LPA</h4>
                <p>Average Package</p>
            </div>
        </div>

    </div>
    <!-- End Stats Row -->


    <!-- Placement Showcase -->
    <div class="row text-center placement-row">

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="placement-card">
                <div class="student-img">
                    <img src="/hit/images/students/student1.jpg" alt="Rakesh Panda">
                </div>
                <h5>Rakesh Panda</h5>
                <p class="degree">Diploma – Mechanical</p>
                <span class="company">Maintenance Engineer – Tata Steel</span>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="placement-card">
                <div class="student-img">
                    <img src="/hit/images/students/student2.jpg" alt="Suman Sahu">
                </div>
                <h5>Suman Sahu</h5>
                <p class="degree">Diploma – Electrical</p>
                <span class="company">Electrical Supervisor – L&T</span>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="placement-card">
                <div class="student-img">
                    <img src="/hit/images/students/student3.jpg" alt="Anil Rout">
                </div>
                <h5>Anil Rout</h5>
                <p class="degree">Diploma – Civil</p>
                <span class="company">Site Engineer – NCC Ltd</span>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="placement-card">
                <div class="student-img">
                    <img src="/hit/images/students/student4.jpg" alt="Pooja Mishra">
                </div>
                <h5>Pooja Mishra</h5>
                <p class="degree">Diploma – Computer Engg</p>
                <span class="company">IT Support Engineer – Wipro</span>
            </div>
        </div>

    </div>
    <!-- End Placement Showcase -->


    <!-- CTA -->
    <div class="text-center mt40">
        <a href="recruittedstudents.php" class="btn btn-danger btn-effect">
            View All
        </a>
    </div>

</div>
</section>


<style>
.placement-card {
    border-radius: 12px;
    text-align: center;
    padding: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    background: linear-gradient(135deg, #7b1e1e, #a83232, #d46a6a);
    transition: transform 0.3s ease;
}

.placement-card:hover {
    transform: translateY(-5px);
}

.student-img {
    margin: 0 auto 15px;
    width: 140px;
    height: 140px;
}

.student-img img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #f2dede;
    box-shadow: 0 6px 18px rgba(0,0,0,0.25);
}

.placement-card h5,
.placement-card .degree,
.placement-card .company {
    color: #ffffff;
}
</style>
<!-- ===== End of HIT Placement Highlights Section ===== -->

                   



    <!-- ===== Start of Recruiting Companies Section ===== -->
    <section class="get-started ptb40" style="background: linear-gradient(90deg, #5e1212 0%, #7b1e1e 45%, #a83232 100%);">
        <div class="container">
            <div class="row align-items-center">

                <!-- Column -->
                <div class="col-md-10 col-sm-9 col-xs-12">
                    <h3 class="text-white">Recruiting Companies</h3>
                </div>
                <!-- Column -->
                <div class="col-md-2 col-sm-3 col-xs-12">
                    <a href="recrutingcompanies.php" class="btn btn-danger btn-effect mt20">View Recruiters</a>
                </div>

            </div>
        </div>
    </section>
    <!-- ===== End of Recruiting Companies Section ===== -->



    <!-- ===== Start of Latest Job Section ===== -->
    


    <!-- ===== End of CountUp Section ===== -->



    <!-- ===== Start of Testimonial Section ===== -->
    <section class="blog-masonry ptb80" style="background: linear-gradient(135deg, #fff2f2, #f4dada);">	
        <div class="container" style="background: linear-gradient(135deg, #fff2f2, #f4dada);">	
            <div class="row blog-grid">
                
                <!-- Start of Blog Post 1 with Image Thumbnail -->
                


	<!-- Database Connection Start  -->


	<!-- Database Connection End  -->

 

                <!-- End of Blog Post 1 -->

   
            </div>
            <!-- End of Row -->
            
           
            
        </div>
    </section>
    <!-- ===== End of Blog Masonry Section ===== -->
  
    <!-- ===== Start of Partners ===== -->
    <section class="ptb40" id="partners" style="background: linear-gradient(135deg, #fff2f2, #f4dada);">	
        <div class="container">
            <!-- Start of Owl Slider -->
            <div class="owl-carousel partners-slider">
                <!-- Partner Logo -->
                    <div class="item">
                        <a href="#"><img src="/rmit/images/companies/aa1.png" alt=""></a>
                    </div>
                    <div class="item">
                        <a href="#"><img src="/rmit/images/companies/aa2.png" alt=""></a>
                    </div>
                    <div class="item">
                        <a href="#"><img src="/rmit/images/companies/aa3.png" alt=""></a>
                    </div>
                    <div class="item">
                        <a href="#"><img src="/rmit/images/companies/aa4.png" alt=""></a>
                    </div>
                    <div class="item">
                        <a href="#"><img src="/rmit/images/companies/aa5.png" alt=""></a>
                    </div>
                    <div class="item">
                        <a href="#"><img src="/rmit/images/companies/aa6.png" alt=""></a>
                    </div>
                    <div class="item">
                        <a href="#"><img src="/rmit/images/companies/aa7.png" alt=""></a>
                    </div>
                    <div class="item">
                        <a href="#"><img src="/rmit/images/companies/aa8.png" alt=""></a>
                    </div>
                    <div class="item">
                        <a href="#"><img src="/rmit/images/companies/aa9.png" alt=""></a>
                    </div>
                    <div class="item">
                        <a href="#"><img src="/rmit/images/companies/aa10.png" alt=""></a>
                    </div>
                    <div class="item">
                        <a href="#"><img src="/rmit/images/companies/aa11.png" alt=""></a>
                    </div>
                    <div class="item">
                        <a href="#"><img src="/rmit/images/companies/aa12.jpg" alt=""></a>
                    </div>
                    <div class="item">
                        <a href="#"><img src="/rmit/images/companies/aa13.jpg" alt=""></a>
                    </div>
                    <div class="item">
                        <a href="#"><img src="/rmit/images/companies/aa14.png" alt=""></a>
                    </div>
                    <div class="item">
                        <a href="#"><img src="/rmit/images/companies/aa15.png" alt=""></a>
                    </div>
                    <div class="item">
                        <a href="#"><img src="/rmit/images/companies/aa16.png" alt=""></a>
                    </div>
                	<div class="item">
                        <a href="#"><img src="/rmit/images/companies/aa17.png" alt=""></a>
                    </div>
                	<div class="item">
                        <a href="#"><img src="/rmit/images/companies/aa18.png" alt=""></a>
                    </div>
					<div class="item">
						<a href="#"><img src="/rmit/images/companies/aa19.png" alt=""></a>
					</div>
					<div class="item">
							<a href="#"><img src="/rmit/images/companies/aa20.png" alt=""></a>
						</div>
					<div class="item">
							<a href="#"><img src="/rmit/images/companies/aa21.png" alt=""></a>
						</div>
					<div class="item">
							<a href="#"><img src="/rmit/images/companies/aa22.png" alt=""></a>
						</div>
					<div class="item">
							<a href="#"><img src="/rmit/images/companies/aa23.png" alt=""></a>
						</div>
					<div class="item">
							<a href="#"><img src="/rmit/images/companies/aa24.png" alt=""></a>
						</div>
					<div class="item">
							<a href="#"><img src="/rmit/images/companies/aa25.png" alt=""></a>
						</div>
					<div class="item">
							<a href="#"><img src="/rmit/images/companies/aa26.png" alt=""></a>
						</div>
					
					<div class="item">
							<a href="#"><img src="/rmit/images/companies/aa27.png" alt=""></a>
						</div>
					<div class="item">
							<a href="#"><img src="/rmit/images/companies/aa28.png" alt=""></a>
						</div>
					<div class="item">
							<a href="#"><img src="/rmit/images/companies/aa28.png" alt=""></a>
						</div>
					<div class="item">
							<a href="#"><img src="/rmit/images/companies/aa30.png" alt=""></a>
						</div>
					<div class="item">
							<a href="#"><img src="/rmit/images/companies/aa31.png" alt=""></a>
						</div>
					<div class="item">
							<a href="#"><img src="/rmit/images/companies/aa32.png" alt=""></a>
						</div>
                <!-- Partner Logo -->
            </div>
            <!-- End of Owl Slider -->
        </div>
    </section>
    <!-- ===== End of Partners ===== -->

<!--===== Start HIT Diploma Admission Popup =====-->
<div class="hit-popup-overlay" id="hit-popup">
  <div class="hit-popup">
    <button class="hit-close" onclick="document.getElementById('hit-popup').style.display='none'">×</button>

    <div class="hit-header">
      🎓 Diploma Admission Open 2026 – 27
    </div>

    <div class="hit-body">

      <div class="hit-logos">
        <img src="/hit/images/footerlogo.png" alt="HIT Logo">
        <img src="/hit/images/hit15years.png" alt="Diploma">
      </div>

      <div class="hit-title">
        <span class="maroon">Diploma</span>
        <span class="gold">Admission</span>
        <span class="pink">2026</span>
      </div>

      <!-- Discount -->
      <div class="hit-discount pulse-maroon">
        🎉 Early Bird Offer<br>
        <strong>10% Fee Discount</strong><br>
        <small>Limited Period Only</small>
      </div>

      <div class="hit-tagline">
        AICTE Approved Diploma Programs<br>
        Mechanical • Electrical • Civil • Computer Science<br>
        Build Skills with Industry-Focused Training
      </div>

      <img src="/hit/images/slider3.png" class="hit-image" alt="HIT Campus">

      <a href="https://forms.gle/feYaP4PR1vX6W7gZA" class="hit-btn">Apply Now</a>

      <div class="hit-tnc">*T&C Apply</div>

    </div>
  </div>
</div>
<!--===== End HIT Diploma Admission Popup =====-->

<style>
  
/* Overlay */
.hit-popup-overlay{
  position:fixed;
  inset:0;
  background:rgba(30,0,10,0.75);
  display:flex;
  justify-content:center;
  align-items:center;
  z-index:9999;
}

/* Popup Box */
.hit-popup{
  width:90%;
  max-width:420px;
  background:linear-gradient(135deg,#4b0015,#7a0026,#a40032);
  border-radius:18px;
  overflow:hidden;
  box-shadow:0 25px 60px rgba(0,0,0,0.5);
  color:#fff;
  position:relative;
}

/* Close button */
.hit-close{
  position:absolute;
  top:10px;
  right:12px;
  border:none;
  background:#fff;
  color:#7a0026;
  border-radius:50%;
  width:32px;
  height:32px;
  cursor:pointer;
  font-size:20px;
  font-weight:800;
  line-height:32px;
  text-align:center;
  box-shadow:0 2px 6px rgba(0,0,0,0.3);
  z-index:10;
}

.hit-close:hover{ background:#ffd700; }

/* Header */
.hit-header{
  background:linear-gradient(to right,#ffd700,#ffb700);
  color:#4b0015;
  text-align:center;
  font-weight:800;
  padding:12px;
  font-size:16px;
}

/* Body */
.hit-body{
  padding:25px;
  text-align:center;
}

.hit-logos{
  display:flex;
  justify-content:space-between;   /* Push left and right */
  align-items:center;
  margin-bottom:12px;
}

.hit-logos img{
  height:90px;
  width:90px;
  border-radius:14px;
  background:#fff;
  padding:10px;
}


/* Right logo glow */
		.hit-logos img:last-child{
		filter:
			drop-shadow(0 0 12px rgba(255,215,0,0.6))
			drop-shadow(0 0 25px rgba(255,165,0,0.5));
		animation:pulseGlow 2.2s infinite alternate;
		}
		
		@keyframes pulseGlow{
		from{
			filter:drop-shadow(0 0 10px rgba(255,215,0,0.5));
		}
		to{
			filter:drop-shadow(0 0 20px rgba(255,215,0,0.9));
		}
		}

/* Title */
.hit-title span{
  display:inline-block;
  padding:6px 12px;
  border-radius:8px;
  font-weight:800;
  margin:4px;
}
.hit-title .maroon{background:#7a0026;}
.hit-title .gold{background:#ffd700;color:#000;}
.hit-title .pink{background:#ff4081;}

/* Discount */
.hit-discount{
  margin:14px auto;
  padding:14px;
  width:85%;
  background:linear-gradient(135deg,#ffe6eb,#ffd1dc);
  color:#7a0026;
  border-radius:14px;
  font-weight:800;
  box-shadow:0 0 25px rgba(255,105,135,0.7);
}

/* Pulse animation */
@keyframes pulseMaroon{
  0%{transform:scale(1); box-shadow:0 0 15px rgba(255,105,135,0.6);}
  50%{transform:scale(1.05); box-shadow:0 0 30px rgba(255,105,135,0.9);}
  100%{transform:scale(1); box-shadow:0 0 15px rgba(255,105,135,0.6);}
}
.pulse-maroon{ animation:pulseMaroon 1.8s infinite; }

/* Tagline */
.hit-tagline{
  font-size:14px;
  margin:12px 0;
  line-height:1.5;
}

/* Image */
.hit-image{
  width:100%;
  height: 150px;
  border-radius:14px;
  margin:10px 0;
}

/* Button */
.hit-btn{
  display:block;
  margin:14px auto 6px;
  padding:12px;
  background:linear-gradient(to right,#ffd700,#ffb700);
  color:#4b0015;
  font-weight:800;
  border-radius:30px;
  text-decoration:none;
}

/* T&C */
.hit-tnc{
  font-size:11px;
  opacity:0.85;
  margin-top:6px;
}

/* Desktop */
@media(min-width:768px){
  .hit-popup{ max-width:480px; }
}

    
</style>
        
        
        
        

    <!-- =============== Start of Footer 1 =============== -->
		<?php include('includes/footer.php'); ?> 
    <!-- =============== End of Footer 1 =============== -->


    <!-- ===== Start of Back to Top Button ===== -->
    <a href="#" class="back-top"><i class="fa fa-chevron-up"></i></a>
    <!-- ===== End of Back to Top Button ===== -->

        
    <!-- ===== All Javascript at the bottom of the page for faster page loading ===== -->
    <script src="js/jquery-3.1.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap-select.min.js"></script>
    <script src="js/swiper.min.js"></script>
    <script src="js/jquery.ajaxchimp.js"></script>
    <script src="js/jquery.countTo.js"></script>
    <script src="js/jquery.inview.min.js"></script>
    <script src="js/jquery.magnific-popup.min.js"></script>
    <script src="js/jquery.easypiechart.min.js"></script>
    <script src="js/jquery-ui.min.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/tinymce/tinymce.min.js"></script>
    <script src="js/countdown.js"></script>
    <script src="js/isotope.min.js"></script>
    <script src="js/custom.js"></script>

</body>

</html>