<!--======================================================
    File Name   : Gallery.php
    Project     : RMIT Groups - HIT
    Description : Gallery Page 
    Developed By: TrinityWebEdge
    Date Created: 17-12-2025
    Last Updated: <?php echo date("d-m-Y"); ?>
    Note         : This page defines the Gallery page of RMIT Groups website.
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
    
  <!-- Polyfill.io automatically loads needed polyfills -->
  <script src="https://polyfill.io/v3/polyfill.min.js"></script>

  <!-- Modernizr for feature detection -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>


  <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Owl Carousel CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    
  <link rel="preconnect" href="https://chatling.ai">

</head>

<body>

    <!-- =============== Start of Header 1 Navigation =============== -->
    <?php include('includes/header.php'); ?>                              
    <!-- =============== End of Header 1 Navigation =============== -->


    <!-- =============== Start of Page Header 1 Section =============== -->
    <section class="page-header" style="background: linear-gradient(90deg, #5e1212 0%, #7b1e1e 45%, #a83232 100%);">
        <div class="container">

            <!-- Start of Page Title -->
            <div class="row">
                <div class="col-md-12">
                    <h2>Gallery</h2>
                </div>
            </div>
            <!-- End of Page Title -->

            <!-- Start of Breadcrumb -->
            <div class="row">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li><a href="/hit/index.php">home</a></li>
                        <li class="active">Gallery</li>
                    </ul>
                </div>
            </div>
            <!-- End of Breadcrumb -->

        </div>
    </section>
    <!-- =============== End of Page Header 1 Section =============== -->
    
    
    <!-- =========== Start of Gallery Section =========== -->
    <section class="shop ptb80" style="background: linear-gradient(135deg, #fbeeee, #efd1d1);">
      <div class="container-fluid">
      <div class="row text-center">
         <h2 class="gallery-heading" 
    		 style="font-weight:800;
					font-size:38px;
					background:linear-gradient(90deg, #5e1212 0%, #7b1e1e 45%, #a83232 100%);
					-webkit-background-clip:text;
					-webkit-text-fill-color:transparent;
					text-shadow:0 1px 2px rgba(0,0,0,0.2);
					margin-bottom:10px;">
				Our College Gallery
		 </h2>

		 
		 
		 <p style="font-size:16px; color:#001f4d; font-weight:500;margin-top:10px; ">
		 Glimpses of campus life, achievements, events, and world-class infrastructure that shape future leaders.
		 </p>
         
         <!-- Divider line -->
		 <div style="height:8px;
					 background:linear-gradient(90deg, #5e1212 0%, #7b1e1e 45%, #a83232 100%);
					 border-radius:70px;
					 width:55%;
					 margin:10px auto 20px auto;">
		 </div>
                                    
         <!-- Image Slider -->
         <div id="collegeGallery" class="carousel slide" data-ride="carousel">
             <!-- Indicators -->
             <ol class="carousel-indicators">
                 <li data-target="#collegeGallery" data-slide-to="0" class="active"></li>
                 <li data-target="#collegeGallery" data-slide-to="1"></li>
                 <li data-target="#collegeGallery" data-slide-to="2"></li>
                 <li data-target="#collegeGallery" data-slide-to="3"></li>
                 <li data-target="#collegeGallery" data-slide-to="4"></li>
                 <li data-target="#collegeGallery" data-slide-to="5"></li>
                 <li data-target="#collegeGallery" data-slide-to="6"></li>
                 <li data-target="#collegeGallery" data-slide-to="7"></li>
                 <li data-target="#collegeGallery" data-slide-to="8"></li>
                 <li data-target="#collegeGallery" data-slide-to="9"></li>
                 <li data-target="#collegeGallery" data-slide-to="10"></li>
                 <li data-target="#collegeGallery" data-slide-to="11"></li>
                 <li data-target="#collegeGallery" data-slide-to="12"></li>
             </ol>
             <!-- Wrapper for slides -->
             <div class="carousel-inner">
                 <div class="item active">
                     <img src="/rmit/images/gallery/aboutsu.jpg" alt="College Campus" class="img-responsive center-block">
                     <div class="carousel-caption">
                         <h3>College Campus</h3>
                     </div>
                 </div>
                 <div class="item">
                     <img src="/rmit/images/gallery/college_campus.jpg" alt="College Campus" class="img-responsive center-block">
                     <div class="carousel-caption">
                         <h3>College Campus</h3>
                     </div>
                 </div>
                 <div class="item">
                     <img src="/rmit/images/gallery/library.jpg" alt="Library" class="img-responsive center-block">
                     <div class="carousel-caption">
                         <h3>Library</h3>
                     </div>
                 </div>
                 <div class="item">
                     <img src="/rmit/images/gallery/computerlab.jpg" alt="Computer Lab" class="img-responsive center-block">
                     <div class="carousel-caption">
                         <h3>Computer Lab</h3>
                     </div>
                 </div>
                 <div class="item">
                     <img src="/rmit/images/gallery/classroom1.jpeg" alt="Classrooms" class="img-responsive center-block">
                     <div class="carousel-caption">
                         <h3>Classrooms</h3>
                     </div>
                 </div>
                 <div class="item">
                     <img src="/rmit/images/gallery/classroom.jpeg" alt="Classrooms" class="img-responsive center-block">
                     <div class="carousel-caption">
                         <h3>Classrooms</h3>
                     </div>
                 </div>
                 <div class="item">
                     <img src="/rmit/images/gallery/classroom3.jpg" alt="Classrooms" class="img-responsive center-block">
                     <div class="carousel-caption">
                         <h3>Classrooms</h3>
                     </div>
                 </div>
                 <div class="item">
                     <img src="/rmit/images/gallery/culture1.jpeg" alt="Cultural Event" class="img-responsive center-block">
                     <div class="carousel-caption">
                         <h3>Annual Function</h3>
                     </div>
                 </div>
                 <div class="item">
                     <img src="/rmit/images/gallery/ganeshpooja.jpg" alt="Cultural Event" class="img-responsive center-block">
                     <div class="carousel-caption">
                         <h3>Ganesh Puja</h3>
                     </div>
                 </div>
                 <div class="item">
                     <img src="/rmit/images/gallery/culture2.jpg" alt="Cultural Event" class="img-responsive center-block">
                     <div class="carousel-caption">
                         <h3>Cultural Performance</h3>
                     </div>
                 </div>
                 <div class="item">
                     <img src="images/gallery/sports1.jpg" alt="Cultural Event" class="img-responsive center-block">
                     <div class="carousel-caption">
                         <h3>Annual Soprts</h3>
                     </div>
                 </div>
                 <div class="item">
                     <img src="images/gallery/sports2.jpg" alt="Cultural Event" class="img-responsive center-block">
                     <div class="carousel-caption">
                         <h3>Annual Soprts</h3>
                     </div>
                 </div>
             </div>
             <!-- Controls -->
             <a class="carousel-control left" href="#collegeGallery" role="button" data-slide="prev">
                 <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                 <span class="sr-only">Previous</span>
             </a>
             <a class="carousel-control right" href="#collegeGallery" role="button" data-slide="next">
                 <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                 <span class="sr-only">Next</span>
             </a>
         </div>
         <!-- End of Image Slider -->
        </div>
    </section>
	<!-- =========== End of Gallery Section =========== -->
	
          <!-- ===== Styling Enhancements ===== -->
          <style>
              #collegeGallery {
                  max-width: 100%;
                  margin: 0 auto;
              }

              .carousel-inner > .item > img {
                  width: 100%;
                  height: 600px;
                  object-fit: cover;
                  padding: 15px;
                  /* space between image and frame */
                  /* Frame effect using background */
                  background: linear-gradient(90deg, #5e1212 0%, #7b1e1e 45%, #a83232 100%);
                  border-radius: 30px;
                  box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
              }

              .carousel-caption {
                  background: rgba(0, 0, 0, 0.6);
                  padding: 10px 20px;
                  border-radius: 5px;
                  bottom: 40px;
              }

              .carousel-caption h3 {
                  font-size: 24px;
                  color: #fff;
                  margin: 0;
              }

              .carousel-control.left, .carousel-control.right {
                  width: 5%;
                  top: 50%;
                  transform: translateY(-50%);
                  font-size: 40px;
                  color: #fff;
                  background: none;
              }
          </style>
		  <!-- =====End Gallery Styling Enhancements ===== -->
		  
    <!-- =========== End of Gallery Section =========== -->
		  
                            
    
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
