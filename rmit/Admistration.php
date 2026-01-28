<!--======================================================
    File Name   : Admistration.php
    Project     : RMIT Groups - RMIT
    Description : Admistration Page 
    Developed By: TrinityWebEdge
    Date Created: 17-12-2025
    Last Updated: <?php echo date("d-m-Y"); ?>
    Note         : This page defines the Admistration page of RMIT Groups website.
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

  <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Owl Carousel CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
  
  <!-- Polyfill.io automatically loads needed polyfills -->
  <script src="https://polyfill.io/v3/polyfill.min.js"></script>

  <!-- Modernizr for feature detection -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>
    
  <link rel="preconnect" href="https://chatling.ai">

</head>

<body>

    <!-- =============== Start of Header 1 Navigation =============== -->
    <?php include('includes/header.php'); ?>                              
    <!-- =============== End of Header 1 Navigation =============== -->
    
    <!-- =============== Start of Page Header 1 Section =============== -->
    <section class="page-header" style="background: linear-gradient(90deg, #0044cc, #00aaff);">
        <div class="container">

            <!-- Start of Page Title -->
            <div class="row">
                <div class="col-md-12">
                    <h2>Administration</h2>
                </div>
            </div>
            <!-- End of Page Title -->

            <!-- Start of Breadcrumb -->
            <div class="row">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li><a href="/rmit/index.php">home</a></li>
                        <li class="active">Administration</li>
                    </ul>
                </div>
            </div>
            <!-- End of Breadcrumb -->

        </div>
    </section>
    <!-- =============== End of Page Header 1 Section =============== -->
    
        
        <!-- ===== Start of Admistration Section ===== -->
        <section class="shop ptb80" style="background: linear-gradient(135deg, #e0f7ff, #b3e9fc);">
            <div class="container">
                <div class="row">
                    <!-- ==== Modified By Rajat Kumar on 29-10-2025 =====-->
                    <!-- Intro Text -->
                    <!-- Inline Bullet Points -->
                    <div class="col-md-12 text-center" style="margin-bottom:40px;">
                        <p style="font-size:16px; color:#555; line-height:1.8;">👥 Meet the Administrative Team Behind RMIT’s Excellence in Operations and Student Services &nbsp;&nbsp;|| 🏛️ The Backbone of RMIT: Our Dedicated Administrative Leaders &nbsp;&nbsp;|| ⚙️ Guiding RMIT with Precision, Integrity, and Operational Excellence &nbsp;&nbsp;|| 💼 RMIT’s Administrative Department — Empowering Education Through Efficient Leadership &nbsp;&nbsp;|| 🛡️ Our Administrative Pillars: Driving RMIT’s Vision with Commitment and Care </p>
                    </div>
                    <!-- ==== Modified By Rajat Kumar on 29-10-2025 =====-->
                    <!-- Start Admistration Profiles -->
                    <div class="col-md-12 col-xs-12 shop-products-wrapper">
                        <!-- Row 1: Profiles 1–4 -->
                        <div class="row">
                            <!-- Profile 1 -->
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="admin-card" style="text-align:center; margin-bottom:30px;">
                                    <img src="images/administration/admini_head.jpg" class="img-responsive img-circle" alt="Profile" style="margin:auto; max-width:180px;">
                                    <h4 style="margin-top:15px;">Admin Head</h4>
                                    <p style="color:#777;">Head of Administration</p>
                                </div>
                            </div>
                            <!-- Profile 2 -->
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="admin-card" style="text-align:center; margin-bottom:30px;">
                                    <img src="images/administration/admin_staff.jpg" class="img-responsive img-circle" alt="Profile" style="margin:auto; max-width:180px;">
                                    <h4 style="margin-top:15px;">Junior Administrative Officer</h4>
                                    <p style="color:#777;">Junior Administrative Officer</p>
                                </div>
                            </div>
                            <!-- Profile 3 -->
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="admin-card" style="text-align:center; margin-bottom:30px;">
                                    <img src="images/administration/accountant.jpg" class="img-responsive img-circle" alt="Profile" style="margin:auto; max-width:180px;">
                                    <h4 style="margin-top:15px;">Account Officer</h4>
                                    <p style="color:#777;">Account Officer</p>
                                </div>
                            </div>
                            <!-- Profile 4 -->
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="admin-card" style="text-align:center; margin-bottom:30px;">
                                    <img src="images/administration/Super.jpg" class="img-responsive img-circle" alt="Profile" style="margin:auto; max-width:180px;">
                                    <h4 style="margin-top:15px;">Training & Placement Officer</h4>
                                    <p style="color:#777;">Training & Placement Officer</p>
                                </div>
                            </div>
                        </div>
                        <!-- Row 2: Profiles 5–8 -->
                        <div class="row">
                            <!-- Profile 5 -->
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="admin-card" style="text-align:center; margin-bottom:30px;">
                                    <img src="images/administration/admin_staff.jpg" class="img-responsive img-circle" alt="Profile" style="margin:auto; max-width:180px;">
                                    <h4 style="margin-top:15px;">Public Relations & Outreach Officer</h4>
                                    <p style="color:#777;">Public Relations & Outreach Officer</p>
                                </div>
                            </div>
                            <!-- Profile 6 -->
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="admin-card" style="text-align:center; margin-bottom:30px;">
                                    <img src="images/administration/coordinator.jpg" class="img-responsive img-circle" alt="Profile" style="margin:auto; max-width:180px;">
                                    <h4 style="margin-top:15px;">Admissions & Records Coordinator</h4>
                                    <p style="color:#777;">Admissions & Records Coordinator</p>
                                </div>
                            </div>
                            <!-- Profile 7 -->
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="admin-card" style="text-align:center; margin-bottom:30px;">
                                    <img src="images/administration/supervisor.jpg" class="img-responsive img-circle" alt="Profile" style="margin:auto; max-width:180px;">
                                    <h4 style="margin-top:15px;">Campus Operations Supervisor</h4>
                                    <p style="color:#777;">Campus Operations Supervisor</p>
                                </div>
                            </div>
                            <!-- Profile 8 -->
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="admin-card" style="text-align:center; margin-bottom:30px;">
                                    <img src="images/administration/it_admin.jpg" class="img-responsive img-circle" alt="Profile" style="margin:auto; max-width:180px;">
                                    <h4 style="margin-top:15px;">IT Admin</h4>
                                    <p style="color:#777;">IT & Systems Administrator</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End of Administration Profiles -->
                </div>
            </div>
        </section>
        <!-- ===== End of Admistration Section ===== -->
    
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
