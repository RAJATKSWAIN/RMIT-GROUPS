<!--======================================================
    File Name   : bca.php
    Project     : RMIT Groups - RMIT
    Description : Courses Page 
    Developed By: TrinityWebEdge
    Date Created: 17-12-2025
    Last Updated: <?php echo date("d-m-Y"); ?>
    Note         : This page defines the Bachelor in Computer Application (BCA) page of RMIT Groups website.
=======================================================-->
<!DOCTYPE html>

<html lang="en">

	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	
	<!-- Primary Meta Tags -->
	<meta name="title" content="RMIT - Rajiv Memorial Institute of Technology, Berhampur">
	<meta name="description" content="Rajiv Memorial Institute of Technology (RMIT), Berhampur — Empowering Minds, Building Futures. A premier institute committed to academic 							excellence, innovation, and leadership in Odisha.">
	<meta name="keywords" content="RMIT Berhampur, Rajiv Memorial Institute of Technology, RMIT Group of Institutions, Engineering Odisha, Diploma Programs, Degree College Odisha">
	<meta name="robots" content="index, follow">
	<meta name="author" content="Rajiv Memorial Institute of Technology - RMIT Group of Institutions">
	<meta name="language" content="English">
	<meta name="theme-color" content="#8b0000">
	
	<link rel="canonical" href="https://rmitgroupsorg.infinityfree.me/rmit">
	
	<!-- Open Graph / Facebook -->
	<meta property="og:type" content="website">
	<meta property="og:url" content="https://rmitgroupsorg.infinityfree.me/rmit">
	<meta property="og:title" content="RMIT - Rajiv Memorial Institute of Technology, Berhampur">
	<meta property="og:description" content="Empowering Minds, Building Futures — A premier institute committed to academic excellence, innovation, and leadership.">
	<meta property="og:image" content="https://rmitgroupsorg.infinityfree.me/rmit/images/rmit-campus.jpg">
	
	<!-- Twitter -->
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:url" content="https://rmitgroupsorg.infinityfree.me/rmit">
	<meta name="twitter:title" content="RMIT - Rajiv Memorial Institute of Technology, Berhampur">
	<meta name="twitter:description" content="Empowering Minds, Building Futures — A premier institute committed to academic excellence, innovation, and leadership.">
	<meta name="twitter:image" content="https://rmitgroupsorg.infinityfree.me/rmit/images/rmit-campus.jpg">
	
	<!-- Website Title -->
	<title>RMIT - Rajiv Memorial Institute of Technology, Berhampur | Empowering Minds, Building Futures</title>
	
	<!-- Favicons -->
	<link rel="icon" href="https://rmitgroupsorg.infinityfree.me/rmit/images/favicon.ico" type="image/x-icon">
	<link rel="apple-touch-icon" href="https://rmitgroupsorg.infinityfree.me/rmit/images/favicon-180.png">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="icon" href="images/favicon_32.png" sizes="32x32" type="image/png">
    <link rel="icon" href="images/favicon_64.png" sizes="64x64" type="image/png">
    <link rel="icon" href="images/favicon_180.png" sizes="180x180" type="image/png">
    <link rel="apple-touch-icon" href="images/favicon_180.png">

	<!-- Google Fonts -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;700;800&family=Varela+Round&display=swap" rel="stylesheet">

  <!-- CSS links -->
  <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
  <link rel="stylesheet" type="text/css" href="css/owl.carousel.min.css">
  <link rel="stylesheet" type="text/css" href="css/swiper.min.css">
  <link rel="stylesheet" type="text/css" href="css/style.css">
  <link rel="stylesheet" type="text/css" href="css/responsive.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" type="text/css" href="css/common.css">

  <!--[if lt IE 9]
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <!--[endif]-->

  <!-- Owl Carousel CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
      
  <link rel="preconnect" href="https://chatling.ai">

</head>

<body>

    <!-- =============== Start of Header 1 Navigation =============== -->
    <?php include('includes/header.php'); ?>                              
    <!-- =============== End of Header 1 Navigation =============== -->
    
	<!-- =============== Start of Page Header Section =============== -->
	<section class="page-header" style="
	width:100%;
	padding:0;
	background: linear-gradient(90deg, #0044cc, #00aaff);
	">
	
	<div class="container-fluid" style="padding:0;">
		<div class="row" style="
		margin:0;
		display:flex;
		align-items:center;
		min-height:120px;
		">
	
		<!-- Left Side: BCA Image with Overlay -->
		<div class="col-xs-6" style="
			padding:10px 0 10px 30px;
			position:relative;
		">
	
			<!-- Overlay curve -->
			<div style="
			position:absolute;
			top:0;
			right:-40px;
			width:80px;
			height:100%;
			background:#2b4c7e;
			border-radius:0 80px 80px 0;
			z-index:1;
			"></div>
	
			<img src="/rmit/images/bca.jpg" alt="BCA Logo" loading="lazy" decoding="async"
				style="
				max-height:160px;
				width:100%;
				border-radius:20px;
				display: inline-block;
				position:relative;
				z-index:2;
				">
		</div>
	
		<!-- Right Side: Text Area -->
		<div class="col-xs-6" style="
			display:flex;
			align-items:center;
			padding-left:60px;
			background: linear-gradient(
			135deg,
			#e6f3ff,
			#d9ecff,
			#cbe4ff
			);
		">
			<h3 style="
			margin:0;
			font-size:20px;
			font-weight:800;
			display: inline-block;
			background: linear-gradient(90deg, #0d47a1, #1e88e5, #64b5f6);
			-webkit-background-clip:text;
			-webkit-text-fill-color:transparent;
			text-shadow:0 1px 4px rgba(30,136,229,0.35);
			letter-spacing:0.3px;
			">
			Bachelor in Computer Application (BCA)
			</h3>
		</div>
	
		</div>
	</div>
	
	</section>
	<!-- =============== End of Page Header Section =============== -->


    
<!-- ===== Start of Main Wrapper BCA Profile Section ===== -->
<section class="pb80" id="candidate-profile2" style="background: linear-gradient(135deg, #e0f7ff, #b3e9fc); padding-top: 90px; ">
            <div class="container">
                <!-- Start of Row -->
                <div class="row candidate-profile">                                       
                    
				<!-- Start of Profile Description -->
                   <div class="col-md-6 col-xs-12 mt80">
                       <div class="profile-descr">
                           <!-- Profile Title -->
                           <div class="profile-title">
                               <h2 class="capitalize">Bachelor in Computer Application (BCA) </h2>
                               	<div style="height: 5px; background: linear-gradient(135deg, #00c6ff, #1e90ff, #87cefa);  margin-top: 07px; margin-bottom: 07px; border-radius: 70px; 											width:100%">
						   </div>
                       </div>
						   
                       <!-- Profile Details -->
                       <div class="profile-details mt40">
                           <p> The <strong>Bachelor of Computer Applications (BCA)</strong>
                               is a three-year undergraduate program designed to provide students with a strong foundation in computer science, programming, and information 											technology. This course equips students with the essential knowledge and technical skills required to design, develop, and manage software 												applications, databases, and computer systems.
                           </p>
                           <br>
                           <p>Through a blend of theoretical and practical learning, students gain proficiency in programming languages, web technologies, data structures, 											operating systems, and software engineering. The BCA program also emphasizes problem-solving, analytical thinking, and project-based learning to 										prepare students for the dynamic IT industry.</p>
                           <br>
                           <p>Graduates of this course can pursue careers as Software Developers, System Analysts, Web Designers, Database Administrators, and IT Support 												Professionals, or continue their studies with advanced programs such as MCA, MBA (IT), or Data Science specializations.</p>
						   </p><br>
						<hr>
						<br>
                           
						<!-- Eligibility Section -->
						<h3 class="capitalize">ELIGIBILITY CRITERIA</h3>
                           	<div style="height: 5px; background: linear-gradient(135deg, #00c6ff, #1e90ff, #87cefa);  margin-top: 07px; margin-bottom: 07px; border-radius: 70px; 											width:42%">
							</div>
						<br>
						<br>
						<p>Candidates must meet the following requirements:</p>
						<ul style="margin-top:5px; line-height:1.8; list-style:none; padding-left:0;">
   						 <li>🎓 <strong>Educational Qualification:</strong>
        					Successful completion of <strong>10+2 (Higher Secondary Examination) or Diploma</strong> or an equivalent	qualification from a recognized board.
    					</li>
    					<li>
        					📘 <strong>Subject Requirement:</strong>
        					Candidates must have studied <strong>Mathematics</strong>  as one of the subjects at the 10+2 or Diploma level.
    					</li>
    					<li>
        					📊 <strong>Minimum Marks:</strong>  A minimum aggregate of <strong>40–50%</strong> marks (varies as per institutional or university norms).
    					</li>
    					<li>
        					🔍 <strong>Stream Eligibility:</strong>Students from <strong>Science, Commerce, Arts or Diploma</strong> streams with Mathematics are eligible to apply.
    					</li>
   						 <li>
        					📝 <strong>Admission Process:</strong> Admissions may be based on <strong>merit</strong> or through an <strong>entrance examination/interview</strong>
        					conducted by the institution.
    					</li>
						</ul>
						<br>
						<p>
						<strong>📅 Duration:</strong>
						3 Years (6 Semesters)
						</p>
						<p>
							<strong>🎓 Mode of Study:</strong>
							Regular
						</p>
						</div></div></div>
						<!-- End of Profile Description -->
						
						<!-- Start of Profile Info -->
						<div class="col-md-4 col-md-offset-2 col-xs-12 mt80 text-center">
							<ul class="profile-info" style="list-style:none; padding:10px; margin-top:100px;">
								<li style="margin-bottom:30px;">
									<i class="fa fa-building-o" style="color:#0056b3; font-size:18px; margin-right:20px;"></i>
									<span style="font-size:22px; font-weight:bold;">Branch Details:</span>
									<span style="margin-left:20px;">Bachelor of Computer Applications (BCA)</span>
								</li>
								<li>
									<i class="fa fa-users" style="color:#0056b3; font-size:18px; margin-right:20px;"></i>
									<span style="font-size:22px; font-weight:bold;">Total Seats:</span>
									<span style="margin-left:20px;">120 Nos</span>
								</li>
							</ul>
						</div>
						<!-- End of Profile Info -->
						
		<!--======== Image Gallery Below Profile Info =========-->
		<div class="col-md-4 col-md-offset-2 col-xs-12 mt80 text-center" style="margin-top:100px; padding:30px;">
			<div class="row">
				<div class="col-xs-12 mb20">
					<img src="images/branch/bac_2.png" alt="BCA Course Overview" loading="lazy" decoding="async"
                         style="width:100%; border:2px solid #0056b3; border-radius:8px; margin-bottom:30px;">
				</div>
				<div class="col-xs-12 mb20">
					<img src="images/branch/bca_3.png" alt="BCA Career Paths" loading="lazy" decoding="async"
                         style="width:100%; border:2px solid #0056b3; border-radius:8px; margin-bottom:30px;">
				</div>
				<div class="col-xs-12 mb20">
					<img src="images/branch/bac_1.png" alt="BCA Career Paths" loading="lazy" decoding="async"
                         style="width:100%; border:2px solid #0056b3; border-radius:8px;">
				</div>
			</div>
		</div>
		<!--========== End of Image Gallery ============-->

		</div> 
		<!-- End of Row -->
		
	</div>
</section>
<!-- ===== Start of Main Wrapper BCA Profile Section ===== -->

	<!--===== Strat CSS for BCA Profile Section =====-->
		<style>
		/* Header spacer (no headline text) */
		.profile-header {
				background: linear-gradient(135deg, #e6f3ff, #cfe7ff);
				height: 120px;
				border-bottom: 5px solid #0044cc;
				}
		
		/* Profile Image */
		.profile-photo img {
				border-radius: 14px;
				box-shadow: 0 6px 18px rgba(0, 68, 204, 0.25);
				border: 3px solid #1e88e5;
				max-width: 100%;
				height: auto;
				}
		
		/* Main Title (ONLY place where BCA appears) */
		.profile-title h2 {
				font-weight: 800;
				font-size: 28px;
				margin-top: 80px;
				background: linear-gradient(90deg, #0d47a1, #1e88e5, #64b5f6);
				-webkit-background-clip: text;
				-webkit-text-fill-color: transparent;
				letter-spacing: 0.4px;
				text-shadow: 0 1px 3px rgba(30,136,229,0.35);
				}
		
		/* Divider under title */
		.profile-title h5 {
				display: none; /* remove underline hack */
				}
		
		/* Section Headings (Eligibility, etc.) */
		.profile-details h3 {
				font-weight: 800;
				font-size: 22px;
				background: linear-gradient(90deg, #0044cc, #00aaff);
				-webkit-background-clip: text;
				-webkit-text-fill-color: transparent;
				margin-top: 30px;
				}
		
		/* Paragraph text */
		.profile-details p {
				color: #1f2d3d;
				line-height: 1.8;
				font-size: 15px;
				}
		
		/* Profile Info Box */
		.profile-info {
				background: linear-gradient(135deg, #e6f3ff, #cfe7ff);
				border-radius: 14px;
				box-shadow: 0 4px 14px rgba(0,68,204,0.2);
				padding: 25px;
				}
		.profile-info li {
				margin-bottom: 25px;
				}
		.profile-info span {
				color: #0b3c5d;
				}
		
		/* Gallery Images */
		.profile-info img,
				.row img {
				transition: transform 0.3s ease, box-shadow 0.3s ease;
				}
		.profile-info img:hover,
		.row img:hover {
				transform: scale(1.03);
				box-shadow: 0 6px 20px rgba(0,68,204,0.25);
				}
		</style>
	<!--===== End CSS for BCA Profile Section =====-->

	<!-- ===== Start of Recruiting Companies Section ===== -->
    <section class="get-started ptb40" style="background: linear-gradient(90deg, #0044cc, #00aaff);">
        <div class="container">
            <div class="row ">

                <!-- Column -->
                <div class="col-md-10 col-sm-9 col-xs-12">
                    <h3 class="text-white">Recruiting Companies</h3>
                </div>
                <!-- Column -->
                <div class="col-md-2 col-sm-3 col-xs-12">
                    <a href="recrutingcompanies.php" class="btn btn-blue btn-effect">View Recruiters</a>
                </div>

            </div>
        </div>
    </section>
    <!-- ===== End of Recruiting Companies Section ===== -->



    <!-- ===== Start of Latest Job Section ===== -->
    


    <!-- ===== End of CountUp Section ===== -->



    <!-- ===== Start of Testimonial Section ===== -->
    <section class="blog-masonry ptb80" style="background: linear-gradient(135deg, #e0f7ff, #b3e9fc);">
        <div class="container" style="background: linear-gradient(135deg, #e0f7ff, #b3e9fc);">
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
    <section class="ptb40" id="partners" style="background: linear-gradient(135deg, #e0f7ff, #b3e9fc);">
        <div class="container">
            <!-- Start of Owl Slider -->
            <div class="owl-carousel partners-slider">
                <!-- Partner Logo -->
                    <div class="item">
                        <a href="#"><img src="images/companies/aa1.png" alt="" loading="lazy" decoding="async"></a>
                    </div>
                    <div class="item">
                        <a href="#"><img src="images/companies/aa2.png" alt="" loading="lazy" decoding="async"></a>
                    </div>
                    <div class="item">
                        <a href="#"><img src="images/companies/aa3.png" alt="" loading="lazy" decoding="async"></a>
                    </div>
                    <div class="item">
                        <a href="#"><img src="images/companies/aa4.png" alt="" loading="lazy" decoding="async"></a>
                    </div>
                    <div class="item">
                        <a href="#"><img src="images/companies/aa5.png" alt="" loading="lazy" decoding="async"></a>
                    </div>
                    <div class="item">
                        <a href="#"><img src="images/companies/aa6.png" alt="" loading="lazy" decoding="async"></a>
                    </div>
                    <div class="item">
                        <a href="#"><img src="images/companies/aa7.png" alt="" loading="lazy" decoding="async"></a>
                    </div>
                    <div class="item">
                        <a href="#"><img src="images/companies/aa8.png" alt="" loading="lazy" decoding="async"></a>
                    </div>
                    <div class="item">
                        <a href="#"><img src="images/companies/aa9.png" alt="" loading="lazy" decoding="async"></a>
                    </div>
                    <div class="item">
                        <a href="#"><img src="images/companies/aa10.png" alt="" loading="lazy" decoding="async"></a>
                    </div>
                    <div class="item">
                        <a href="#"><img src="images/companies/aa11.png" alt="" loading="lazy" decoding="async"></a>
                    </div>
                    <div class="item">
                        <a href="#"><img src="images/companies/aa12.jpg" alt="" loading="lazy" decoding="async"></a>
                    </div>
                    <div class="item">
                        <a href="#"><img src="images/companies/aa13.jpg" alt="" loading="lazy" decoding="async"></a>
                    </div>
                    <div class="item">
                        <a href="#"><img src="images/companies/aa14.png" alt="" loading="lazy" decoding="async"></a>
                    </div>
                    <div class="item">
                        <a href="#"><img src="images/companies/aa15.png" alt="" loading="lazy" decoding="async"></a>
                    </div>
                    <div class="item">
                        <a href="#"><img src="images/companies/aa16.png" alt="" loading="lazy" decoding="async"></a>
                    </div>
                    <div class="item">
                        <a href="#"><img src="images/companies/aa17.png" alt="" loading="lazy" decoding="async"></a>
                    </div>
                	<div class="item">
                        <a href="#"><img src="images/companies/aa18.png" alt="" loading="lazy" decoding="async"></a>
                    </div>
					<div class="item">
						<a href="#"><img src="images/companies/aa19.png" alt="" loading="lazy" decoding="async"></a>
					</div>
					<div class="item">
						<a href="#"><img src="images/companies/aa20.png" alt="" loading="lazy" decoding="async"></a>
					</div>
					<div class="item">
						<a href="#"><img src="images/companies/aa21.png" alt="" loading="lazy" decoding="async"></a>
					</div>
					<div class="item">
						<a href="#"><img src="images/companies/aa22.png" alt="" loading="lazy" decoding="async"></a>
					</div>
					<div class="item">
						<a href="#"><img src="images/companies/aa23.png" alt="" loading="lazy" decoding="async"></a>
					</div>
					<div class="item">
						<a href="#"><img src="images/companies/aa24.png" alt="" loading="lazy" decoding="async"></a>
					</div>
					<div class="item">
						<a href="#"><img src="images/companies/aa25.png" alt="" loading="lazy" decoding="async"></a>
					</div>
					<div class="item">
						<a href="#"><img src="images/companies/aa26.png" alt="" loading="lazy" decoding="async"></a>
					</div>					
					<div class="item">
						<a href="#"><img src="images/companies/aa27.png" alt="" loading="lazy" decoding="async"></a>
					</div>
					<div class="item">
						<a href="#"><img src="images/companies/aa28.png" alt="" loading="lazy" decoding="async"></a>
					</div>
					<div class="item">
						<a href="#"><img src="images/companies/aa29.png" alt="" loading="lazy" decoding="async"></a>
					</div>
					<div class="item">
						<a href="#"><img src="images/companies/aa30.png" alt="" loading="lazy" decoding="async"></a>
					</div>
					<div class="item">
						<a href="#"><img src="images/companies/aa31.png" alt="" loading="lazy" decoding="async"></a>
					</div>
					<div class="item">
						<a href="#"><img src="images/companies/aa32.png" alt="" loading="lazy" decoding="async"></a>
					</div>
                   
                <!-- Partner Logo -->
            </div>
            <!-- End of Owl Slider -->
        </div>
    </section>
    <!-- ===== End of Partners ===== -->


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
