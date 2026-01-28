<!--======================================================
    File Name   : Staffinformation.php
    Project     : RMIT Groups - RMIT
    Description : Staff Information Page 
    Developed By: TrinityWebEdge
    Date Created: 17-12-2025
    Last Updated: <?php echo date("d-m-Y"); ?>
    Note         : This page defines the Staff Information page of RMIT Groups website.
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
	<meta property="og:image" content="https://rmitgroupsorg.infinityfree.me/images/rmit.png">
	
	<!-- Twitter -->
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:url" content="https://rmitgroupsorg.infinityfree.me/rmit">
	<meta name="twitter:title" content="RMIT - Rajiv Memorial Institute of Technology, Berhampur">
	<meta name="twitter:description" content="Empowering Minds, Building Futures — A premier institute committed to academic excellence, innovation, and leadership.">
	<meta name="twitter:image" content="https://rmitgroupsorg.infinityfree.me/images/rmit.png">
	
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
                    <h2>Staff Information</h2>
                </div>
            </div>
            <!-- End of Page Title -->

            <!-- Start of Breadcrumb -->
            <div class="row">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li><a href="/rmit/index.php">home</a></li>
                        <li class="active">Staff Information</li>
                    </ul>
                </div>
            </div>
            <!-- End of Breadcrumb -->

        </div>
    </section>
    <!-- =============== End of Page Header 1 Section =============== -->
    
	
    <!-- =============== Start of Staff Information Section =============== -->
	<section class="staff-info-section">
	<div class="container">
	
		<!-- Section Header -->
		<div class="row">
		<div class="col-12 text-center">
			<h3>Our Faculty Members</h3>
			<div style="
					height: 8px;
					background: linear-gradient(135deg, #00c6ff, #1e90ff, #87cefa);
					border-radius: 70px;
					width: 40%;
					margin: 15px auto 15px auto;">
			</div>
			<p class="lead">
			Meet our dedicated educators and experienced mentors who shape the future
			of students at RMIT through knowledge, innovation, and excellence.
			</p>
		</div>
		</div>
	
		<!-- Staff Cards -->
		<div class="row staff-row">
	
		<div class="col-md-3 col-sm-6 col-xs-12">
			<div class="staff-card">
			<div class="staff-photo">
				<img src="images/staffinfo/staff_p.png" alt="Principal"
					onerror="handleMissingPhoto(this)">
			</div>
			<div class="staff-info">
				<h5>Mr. Durga Prasad Padhi</h5>
				<p class="designation">Principal</p>
				<p class="dept">Computer Science</p>
				<div class="contact">📞 +91 7682857729</div>
			</div>
			</div>
		</div>
	
		<div class="col-md-3 col-sm-6 col-xs-12">
			<div class="staff-card">
			<div class="staff-photo">
				<img src="images/staffinfo/staff2.jpg" alt="Faculty"
					onerror="handleMissingPhoto(this)">
			</div>
			<div class="staff-info">
				<h5>Ms. Anjali Das</h5>
				<p class="designation">Lecturer</p>
				<p class="dept">Electronics</p>
				<div class="contact">📞 +91 9348776650</div>
			</div>
			</div>
		</div>
	
		<!-- Card 3 -->
		<div class="col-md-3 col-sm-6 col-xs-12">
			<div class="staff-card">
			<div class="staff-photo">
				<img src="images/staffinfo/staff3.jpg" alt="Faculty"
					onerror="handleMissingPhoto(this)">
			</div>
			<div class="staff-info">
				<h5>Mr. S. K. Mishra</h5>
				<p class="designation">Assistant Professor</p>
				<p class="dept">Physics</p>
				<div class="contact">📞 +91 9348776650</div>
			</div>
			</div>
		</div>
	
		<!-- Card 4 -->
		<div class="col-md-3 col-sm-6 col-xs-12">
			<div class="staff-card">
			<div class="staff-photo">
				<img src="images/staffinfo/staff4.jpg" alt="Faculty"
					onerror="handleMissingPhoto(this)">
			</div>
			<div class="staff-info">
				<h5>Mrs. Manasmita Panigrahy</h5>
				<p class="designation">Assistant Professor</p>
				<p class="dept">Physics</p>
				<div class="contact">📞 +91 9123456780</div>              
			</div>
			</div>
		</div>
			
			<!-- card 5 -->
			<div class="col-md-3 col-sm-6 col-xs-12">
			<div class="staff-card">
			<div class="staff-photo">
						<img src="images/staffinfo/staff5.jpg" alt="Mr Ajit Kumar Dash"
							onerror="handleMissingPhoto(this)">
					</div>
					<div class="staff-info">
						<div>
						<h5>Mr. Ajit Kumar Dash</h5>
						<p class="designation">Lecturer</p>
						<p class="dept">Department: Computer Science</p>
						</div>
						<div class="contact">📞 +91 9348776650</div>
					</div>
				</div>
				</div>
			
			<!-- card 6 -->
			<div class="col-md-3 col-sm-6 col-xs-12">
			<div class="staff-card">
			<div class="staff-photo">
				<img src="images/staffinfo/staff6.jpg" alt="Ms. Sunita Behera"
					onerror="handleMissingPhoto(this)">
				</div>
				<div class="staff-info">
				<div>
					<h5>Ms. Sunita Behera</h5>
					<p class="designation">Assistant Professor</p>
					<p class="dept">Department: Electronics</p>
				</div>
				<div class="contact">📞 +91 9876543211</div>
				</div>
			</div>
			</div>
			
			<!-- card 7 -->
			<div class="col-md-3 col-sm-6 col-xs-12">
			<div class="staff-card">
			<div class="staff-photo">
				<img src="images/staffinfo/staff7.jpg" alt="Dr. Pradeep Kumar Rout"
					onerror="handleMissingPhoto(this)">
				</div>
				<div class="staff-info">
				<div>
					<h5>Dr. Pradeep Kumar Rout</h5>
					<p class="designation">Professor</p>
					<p class="dept">Department: Physics</p>
				</div>
				<div class="contact">📞 +91 9937456023</div>
				</div>
			</div>
			</div>
			
			<!-- card 8 -->
			<div class="col-md-3 col-sm-6 col-xs-12">
			<div class="staff-card">
			<div class="staff-photo">
				<img src="images/staffinfo/staff8.jpg" alt="Mr. Rajesh Mohanty"
					onerror="handleMissingPhoto(this)">
				</div>
				<div class="staff-info">
				<div>
					<h5>Mr. Rajesh Mohanty</h5>
					<p class="designation">Lab Assistant</p>
					<p class="dept">Department: Chemistry</p>
				</div>
				<div class="contact">📞 +91 9865432099</div>
				</div>
			</div>
			</div>
			
			<!-- card 9 -->
			<div class="col-md-3 col-sm-6 col-xs-12">
			<div class="staff-card">
			<div class="staff-photo">
						<img src="images/staffinfo/staff5.jpg" alt="Mr Ajit Kumar Dash"
							onerror="handleMissingPhoto(this)">
					</div>
					<div class="staff-info">
						<div>
						<h5>Mr. Ajit Kumar Dash</h5>
						<p class="designation">Lecturer</p>
						<p class="dept">Department: Computer Science</p>
						</div>
						<div class="contact">📞 +91 9348776650</div>
					</div>
					</div>
				</div>
			
			<!-- card 10 -->
			<div class="col-md-3 col-sm-6 col-xs-12">
			<div class="staff-card">
			<div class="staff-photo">
				<img src="images/staffinfo/staff6.jpg" alt="Ms. Sunita Behera"
					onerror="handleMissingPhoto(this)">
				</div>
				<div class="staff-info">
				<div>
					<h5>Ms. Sunita Behera</h5>
					<p class="designation">Assistant Professor</p>
					<p class="dept">Department: Electronics</p>
				</div>
				<div class="contact">📞 +91 9876543211</div>
				</div>
			</div>
			</div>
			
			<!-- card 11 -->
			<div class="col-md-3 col-sm-6 col-xs-12">
			<div class="staff-card">
			<div class="staff-photo">
				<img src="images/staffinfo/staff7.jpg" alt="Dr. Pradeep Kumar Rout"
					onerror="handleMissingPhoto(this)">
				</div>
				<div class="staff-info">
				<div>
					<h5>Dr. Pradeep Kumar Rout</h5>
					<p class="designation">Professor</p>
					<p class="dept">Department: Physics</p>
				</div>
				<div class="contact">📞 +91 9937456023</div>
				</div>
			</div>
			</div>
			
			<!-- card 12 -->
			<div class="col-md-3 col-sm-6 col-xs-12">
			<div class="staff-card">
			<div class="staff-photo">
				<img src="images/staffinfo/staff3.jpg" alt="Mr. Rajesh Mohanty"
					onerror="handleMissingPhoto(this)">
				</div>
				<div class="staff-info">
				<div>
					<h5>Mr. Rajesh Mohanty</h5>
					<p class="designation">Lab Assistant</p>
					<p class="dept">Department: Chemistry</p>
				</div>
				<div class="contact">📞 +91 9865432099</div>
				</div>
			</div>
			</div>
	
		</div>
	</div>
	</section>

		<script>
		function handleMissingPhoto(imgEl) {
		const parent = imgEl.parentElement;
		imgEl.remove();
		const fallback = document.createElement('div');
		fallback.className = 'staff-photo no-photo';
		fallback.innerText = 'NO PHOTO AVAILABLE';
		fallback.style.display = 'flex';
		fallback.style.alignItems = 'center';
		fallback.style.justifyContent = 'center';
		fallback.style.fontWeight = '700';
		fallback.style.color = '#888';
		parent.appendChild(fallback);
		}
		</script>

	<!-- =============== Start of Staff Information (card/grid)  =============== -->

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