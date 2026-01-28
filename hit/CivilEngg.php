<!--======================================================
    File Name   : CivilEngg.php
    Project     : RMIT Groups - HIT
    Description : Courses Page 
    Developed By: TrinityWebEdge
    Date Created: 17-12-2025
    Last Updated: <?php echo date("d-m-Y"); ?>
    Note         : This page defines the Civil Engineering page of RMIT Groups website.
=======================================================-->
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
	background: linear-gradient(90deg, #5e1212 0%, #7b1e1e 20%, #a83232 100%);
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
			background: linear-gradient(135deg, #C99700, #FFD24C, #B8860B);
			border-radius:0 80px 80px 0;
            box-shadow: 0 0 30px rgba(201, 151, 0, 0.65);            
			z-index:1;
			"></div>
	
			<img src="images/civil.jpg" alt="ME Logo" loading="lazy" decoding="async"
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
			background:linear-gradient(135deg, #C99700, #FFD24C, #B8860B);			
            box-shadow: 0 0 30px rgba(201, 151, 0, 0.65);  ">
            
			<h3 style="
			margin:0;
			font-size:20px;
			font-weight:800;
			display: inline-block;
			background: linear-gradient(90deg, #5e1212 0%, #7b1e1e 45%, #a83232 100%);
			-webkit-background-clip:text;
			-webkit-text-fill-color:transparent;
			text-shadow:0 1px 4px rgba(30,136,229,0.25);
			letter-spacing:0.3px;
			">
			Diploma in Civil Engineering
			</h3>
		</div>
	
		</div>
	</div>
	
	</section>
	<!-- =============== End of Page Header Section =============== -->


    
<!-- ===== Start of Main Wrapper Mechanical Engineering Profile Section ===== -->
<section class="pb80" id="candidate-profile-mech" style="background: linear-gradient(135deg, #fbeeee, #efd1d1); padding-top: 50px;">
    <div class="container">
        
        <!--======== Image Gallery Below Profile Info =========-->
            <div class="col-md-12 text-center" style="padding:30px;">
                <div class="row">
                    <div class="col-md-4 col-xs-12 mb20">
                        <img src="/hit/images/branch/civil1.png" alt="Civil Lab" loading="lazy" decoding="async"
                             style="width:100%; border:2px solid #8b0000; border-radius:8px; margin-bottom:5px; height: 180px">
                    </div>
                    <div class="col-md-4 col-xs-12 mb20">
                        <img src="/hit/images/branch/civil2.png" alt="Workshop Training" loading="lazy" decoding="async"
                             style="width:100%; border:2px solid #8b0000; border-radius:8px; margin-bottom:5px;  height: 180px">
                    </div>
                    <div class="col-md-4 col-xs-12 mb20">
                        <img src="/hit/images/branch/civil3.png" alt="CAD Design" loading="lazy" decoding="async"
                             style="width:100%; border:2px solid #8b0000; border-radius:8px; margin-bottom:5px;  height: 180px">
                    </div>
                </div>
            </div>
            <!--========== End of Image Gallery ============-->
        <!-- Start of Row -->
<div class="row candidate-profile">

    <!-- Start of Profile Description -->
    <div class="col-md-7 col-xs-12 mt60">
        <div class="profile-descr">
            <!-- Profile Title -->
            <div class="profile-title text-left">
                <h2 class="capitalize">Civil Engineering</h2>                       
                <div style="height: 5px; background: linear-gradient(135deg, #b22222, #8b0000, #ff4500); margin-top: 10px; margin-bottom: 10px; border-radius: 50px; width:37%"></div>
            </div>

            <!-- Profile Details -->
            <div class="profile-details mt30">
                <p>
                    The <strong>Diploma in Civil Engineering</strong> at HIT provides a strong foundation in construction technology, structural design, and infrastructure development. Students are trained to plan, execute, and supervise civil engineering projects in both public and private sectors.
                </p>
                <br>
                <p>
                    The program covers <strong>building materials, surveying, structural engineering, construction planning, environmental engineering, and transportation engineering</strong>. With practical fieldwork and lab sessions, students gain hands-on experience in real-world construction practices.
                </p>
                <br>
                <p>
                    Diploma holders in Civil Engineering can work as <strong>Site Supervisors, Junior Engineers, Surveyors, Quantity Estimators, Draftsmen</strong>, or pursue higher studies through <strong>Lateral Entry into B.Tech (Civil Engineering)</strong> and related programs.
                </p>
                <br>
                                            
                <!-- Eligibility Section -->
                <h3 class="capitalize">Eligibility Criteria</h3>
                <div style="height: 5px; background: linear-gradient(135deg, #b22222, #ff6347); margin-top: 7px; margin-bottom: 15px; border-radius: 50px; width:25%"></div>
                <p>Candidates must meet the following requirements:</p>
                <ul style="margin-top:5px; line-height:1.8; list-style:none; padding-left:0;">
                    <li>🏗️ <strong>Educational Qualification:</strong> Successful completion of <strong>10th (Secondary Examination)</strong> or equivalent from a recognized board.</li>
                    <li>📘 <strong>Subject Requirement:</strong> Mathematics and Science must be studied at the 10th level.</li>
                    <li>📊 <strong>Minimum Marks:</strong> A minimum aggregate of <strong>35–50%</strong> marks (as per HIT norms).</li>
                    <li>🔍 <strong>Admission Process:</strong> Based on <strong>merit</strong> or through an <strong>entrance examination/interview</strong> conducted by HIT.</li>
                    <li>➡️ <strong>Lateral Entry:</strong> Candidates with <strong>10+2 (Science with PCM)</strong> or an <strong>ITI in Civil / relevant trade</strong> may be admitted directly into the <strong>second year</strong>.
                    </li>
                </ul>
                <br>
                <p><strong>📅 Duration:</strong> 3 Years (6 Semesters)</p>
                <p><strong>🎓 Mode of Study:</strong> Regular</p>
            </div>
        </div>
    </div>
    <!-- End of Profile Description -->

    <!-- Start of Profile Info -->
    <div class="col-md-5 col-xs-12 mt80 text-center">
        <ul class="profile-info" style="list-style:none; padding:20px; margin-top:60px;">
            <li style="margin-bottom:30px;">
                <i class="fa fa-building" style="color:#8b0000; font-size:20px; margin-right:15px;"></i>
                <span style="font-size:22px; font-weight:bold;">Branch Details:</span>
                <span style="margin-left:15px;">Diploma in Civil Engineering</span>
            </li>
            <li>
                <i class="fa fa-users" style="color:#8b0000; font-size:20px; margin-right:15px;"></i>
                <span style="font-size:22px; font-weight:bold;">Total Seats:</span>
                <span style="margin-left:15px;">60 Nos</span>
            </li>
        </ul>
    </div>
    <!-- End of Profile Info -->

</div>
<!-- End of Row -->


    </div>
</section>
<!-- ===== End of Main Wrapper Mechanical Engineering Profile Section ===== -->

<!--===== Start CSS for Mechanical Engineering Profile Section =====-->
<style>
    /* Header spacer */
    .profile-header {
        background: linear-gradient(135deg, #f5f5f5, #dcdcdc);
        height: 120px;
        border-bottom: 5px solid #8b0000;
    }

    /* Profile Title */
    .profile-title h2 {
        font-weight: 800;
        font-size: 30px;
        margin-top: 30px;
        background: linear-gradient(90deg, #5e1212 0%, #7b1e1e 45%, #a83232 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        letter-spacing: 0.5px;
        text-shadow: 0 1px 3px rgba(139,0,0,0.35);
    }

    /* Section Headings */
    .profile-details h3 {
        font-weight: 800;
        font-size: 22px;
        background: linear-gradient(90deg, #5e1212 0%, #7b1e1e 45%, #a83232 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-top: 10px;
    }

    /* Paragraph text */
    .profile-details p {
        color: #2c2c2c;
        line-height: 1.5;
        font-size: 15px;
    }

    /* Profile Info Box */
    .profile-info {
        background: linear-gradient(135deg, #f9f9f9, #e6e6e6);
        border-radius: 14px;
        box-shadow: 0 4px 14px rgba(139,0,0,0.25);
        padding: 25px;
    }
    .profile-info li {
        margin-bottom: 25px;
    }
    .profile-info span {
        color: #2c2c2c;
    }

    /* Gallery Images */
    .profile-info img,
    .row img {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .profile-info img:hover,
    .row img:hover {
        transform: scale(1.05);
        box-shadow: 0 6px 20px rgba(139,0,0,0.25);
    }
</style>
<!--===== End CSS for Mechanical Engineering Profile Section =====-->

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
