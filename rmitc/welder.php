<!--======================================================
    File Name   : welder.php
    Project     : RMIT Groups - RMITC
    Description : Trades Page 
    Developed By: TrinityWebEdge
    Date Created: 17-12-2025
    Last Updated: <?php echo date("d-m-Y"); ?>
    Note         : This page defines the Welder Trade page of RMIT Groups website.
=======================================================-->
<!DOCTYPE html>

<html lang="en">

<head>
    
    <meta charset="UTF-8">

    <!-- Mobile viewport optimized -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1.0, user-scalable=no">

    <!-- Primary Meta Tags -->
	<meta name="title" content="RMITC - Rajiv Memorial Industrial Training Centre, Berhampur">
	<meta name="description" content="Rajiv Memorial Industrial Training Centre (RMIT ITI), Berhampur — A premier ITI institute under RMIT Group of Institutions, offering industry-oriented technical training and skill development in Odisha.">
	<meta name="keywords" content="RMITC, Rajiv Memorial Industrial Training Centre, ITI Berhampur, RMIT Group of Institutions, Industrial Training Institute Odisha, Skill Development Odisha">
	<meta name="robots" content="index, follow">
	<meta name="author" content="Rajiv Memorial Industrial Training Centre - RMIT Group of Institutions">
	<meta name="language" content="English">
	<meta name="theme-color" content="#8b0000">
	
	<link rel="canonical" href="https://rmitgroupsorg.infinityfree.me/rmitc">
	
	<!-- Open Graph / Facebook -->
	<meta property="og:type" content="website">
	<meta property="og:url" content="https://rmitgroupsorg.infinityfree.me/rmitc">
	<meta property="og:title" content="RMIT ITI - Rajiv Memorial Industrial Training Centre, Berhampur">
	<meta property="og:description" content="Industry-ready technical training and skill development under RMIT Group of Institutions.">
	<meta property="og:image" content="https://rmitgroupsorg.infinityfree.me/rmit/images/rmit-campus.jpg">
		
	<!-- Twitter -->
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:url" content="https://rmitgroupsorg.infinityfree.me/rmitc">
	<meta name="twitter:title" content="RMIT ITI - Rajiv Memorial Industrial Training Centre, Berhampur">
	<meta name="twitter:description" content="Industry-ready technical training and skill development under RMIT Group of Institutions.">
	<meta name="twitter:image" content="https://rmitgroupsorg.infinityfree.me/rmit/images/rmit-campus.jpg">
	
	<!-- Website Title -->
	<title>RMITC - Rajiv Memorial Industrial Training Centre, Berhampur | Empowering Minds, Building Futures</title>
	
	<!-- Favicons -->
	<link rel="icon" href="https://rmitgroupsorg.infinityfree.me/rmitc/images/favicon.ico" type="image/x-icon">
	<link rel="apple-touch-icon" href="https://rmitgroupsorg.infinityfree.me/rmitc/images/favicon-64.png">
	<link rel="icon" href="images/favicon.ico" type="image/x-icon">
	<link rel="icon" href="images/favicon_32.png" sizes="32x32" type="image/png">
	<link rel="icon" href="images/favicon-64.png" sizes="64x64" type="image/png">
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
	background: linear-gradient(90deg, #050F24, #0A2A66, #1E88E5);
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
			right:-10px;
			width:220px;
			height:100%;
			background:linear-gradient(135deg, #0a1f44, #0b3c7d, #1e90ff);
			border-radius:0 80px 80px 0;
			z-index:1;
			"></div>
	
			<img src="/rmitc/images/trades/welderlogo.png" alt="Trade Logo" loading="lazy" decoding="async"
				style="
				max-height:260px;
				width:60%;
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
			Welder Trade
			</h3>
		</div>
	
		</div>
	</div>
	
	</section>
	<!-- =============== End of Page Header Section =============== -->


    
<!-- ===== Start of Main Wrapper Trade Profile Section ===== -->
<section class="pb80" id="candidate-profile2" style="background: linear-gradient(135deg, #e0f7ff, #b3e9fc); padding-top: 90px;">
    <div class="container">
        <div class="row candidate-profile">

            <!-- Start of Profile Description -->
            <div class="col-md-6 col-xs-12 mt80">
                <div class="profile-descr">

                    <div class="profile-title">
                        <h2 class="capitalize">Welder Trade (ITI – NCVT)</h2>
                        <div style="height:5px; background:linear-gradient(135deg,#00c6ff,#1e90ff,#87cefa); margin:7px 0; border-radius:70px; width:60%"></div>
                    </div>

                    <div class="profile-details mt40">
						<p>
							The <strong>Welder Trade</strong> is a core technical trade under the NCVT scheme designed to train students in various welding techniques used for joining 							metal parts in fabrication, construction, and manufacturing industries.
						</p>
					
						<p>
							Students gain hands-on experience in gas welding, arc welding, MIG/TIG welding basics, cutting operations, joint preparation, blueprint reading, and quality 							inspection of welds. The program emphasizes workshop safety, precision workmanship, and adherence to industrial standards.
						</p>
					
						<p>
							After completing the Welder ITI course, trainees are prepared for technical roles in fabrication units, shipyards, construction projects, manufacturing 								plants, railways, and heavy engineering industries.
						</p>
						<br>
					
						<h3 class="capitalize">ELIGIBILITY CRITERIA</h3>
						<div style="height:5px; background:linear-gradient(135deg,#00c6ff,#1e90ff,#87cefa); margin:7px 0; border-radius:70px; width:50%"></div>
					
						<ul style="margin-top:15px; line-height:1.8; list-style:none; padding-left:0;">
							<li>🎓 <strong>Qualification:</strong> Passed <strong>8th / 10th Standard</strong> or equivalent (as per NCVT norms).</li>
							<li>📘 <strong>Subjects:</strong> Basic Mathematics and Science preferred.</li>
							<li>📊 <strong>Minimum Marks:</strong> As per NCVT / State ITI admission guidelines.</li>
							<li>📝 <strong>Admission:</strong> Merit-based or as per ITI admission process.</li>
						</ul>
					
						<p><strong>📅 Duration:</strong> 1 Year or 2 Years (as per NCVT syllabus)</p>
						<p><strong>🎓 Certification:</strong> NCVT / SCVT</p>
						<p><strong>🛠 Mode:</strong> Regular (Workshop + Theory)</p>
					</div>

                </div>
            </div>

            <!-- Start of Profile Info -->
            <div class="col-md-4 col-md-offset-2 col-xs-12 mt80 text-center">
                <ul class="profile-info" style="list-style:none; padding:10px; margin-top:100px;">
                    <li style="margin-bottom:30px;">
                        <i class="fa fa-cogs" style="color:#0056b3; font-size:18px; margin-right:20px;"></i>
                        <span style="font-size:22px; font-weight:bold;">Trade:</span>
                        <span style="margin-left:20px;">Welder (ITI)</span>
                    </li>
                    <li>
                        <i class="fa fa-users" style="color:#0056b3; font-size:18px; margin-right:20px;"></i>
                        <span style="font-size:22px; font-weight:bold;">Total Seats:</span>
                        <span style="margin-left:20px;">60 Nos</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>
<!-- ===== End of Main Wrapper Trade Profile Section ===== -->


	<!--===== Strat CSS for Trade Profile Section =====-->
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
	<!--===== End CSS for Trade Profile Section =====-->

	<!-- ===== Start of Recruiting Companies Section ===== -->
    <section class="get-started ptb40" style="background: linear-gradient(90deg, #050F24, #0A2A66, #1E88E5);">
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
