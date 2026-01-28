<!--======================================================
    File Name   : infrastructure.php
    Project     : RMIT Groups - HIT
    Description : Infrastructure Page 
    Developed By: TrinityWebEdge
    Date Created: 17-12-2025
    Last Updated: <?php echo date("d-m-Y"); ?>
    Note         : This page defines the Infrastructure page of RMIT Groups website.
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
                    <h2>Infrastructure</h2>
                </div>
            </div>
            <!-- End of Page Title -->

            <!-- Start of Breadcrumb -->
            <div class="row">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li><a href="/hit/index.php">home</a></li>
                        <li class="active">Infrastructure</li>
                    </ul>
                </div>
            </div>
            <!-- End of Breadcrumb -->

        </div>
    </section>
    <!-- =============== End of Page Header 1 Section =============== -->

    <!-- =============== Start of Infrastructure Grid Section =============== -->
    <!-- =============== Start of Infrastructure Grid Section =============== -->
        <section class= "ptb40" Style="background: linear-gradient(135deg, #fbeeee, #efd1d1); color: #001f4d;">
           <div class="container"  style="background: linear-gradient(135deg, #fbeeee, #efd1d1); color: #001f4d; margin-top: 20px;">
              <div class="row text-center">
                  <h2 style="font-weight:800;
					background: linear-gradient(90deg, #7b1e1e, #b03030, #e05c5c);
					-webkit-background-clip:text;
					-webkit-text-fill-color:transparent;">Modern Infrastructure, Timeless Values</h2>
      				<p class="lead">Our campus blends advanced technology with a nurturing environment to support academic brilliance and personal growth.</p>
      					<!-- Centered Divider -->
			<div style="
					height:5px;
					background: linear-gradient(90deg, #7b1e1e, #e05c5c);
					margin:10px auto 15px;
					border-radius:70px;
					width:55%;
				"></div>
       
              <?php
               $infrastructure = [
                   ["/rmit/images/Infrastructure/college_campus.jpg", "Campus", "A lush green and eco-friendly campus designed to promote learning and creativity."],
                   ["/rmit/images/Infrastructure/library.jpg", "Central Library", "Fully equipped library with thousands of volumes, journals, and digital resources."],
                   ["/rmit/images/Infrastructure/lab.jpg", "Computer Laboratories", "State-of-the-art computer labs with high-speed internet and latest software tools."],
                   ["/rmit/images/Infrastructure/classroom.jpg", "Smart Classrooms", "Digital learning environment with modern audio-visual teaching aids."],
                   ["/rmit/images/Infrastructure/auditorium.jpg", "Auditorium", "Spacious and well-equipped auditorium for seminars, events, and cultural programs."],
                   ["/rmit/images/Infrastructure/sports.jpg", "Sports & Recreation", "Outdoor and indoor sports facilities encouraging students’ overall development."],
                   ["/rmit/images/Infrastructure/hostel.jpg", "Hostel Accommodation", "Comfortable, safe, and hygienic hostel facilities with Wi-Fi and healthy dining."],
                   ["/rmit/images/Infrastructure/canteen.jpg", "Cafeteria", "Clean and vibrant canteen offering nutritious and delicious meals at affordable prices."],
                   ["/rmit/images/Infrastructure/transport1.jpg", "Transport Facility", "Fleet of college buses ensuring safe and convenient transport for students and staff."]
                  ];

               foreach ($infrastructure as $item) {
                    echo '
                    <div class="col-md-4 col-sm-6" style="margin-bottom: 30px;">
                    <div style="background: #ffffff; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); transition: 0.3s; padding: 15px;">
                        	<img src="'.$item[0].'" alt="'.$item[1].'" style="width: 100%; border-radius: 10px; height: 220px; object-fit: cover;">
                        	<h4 style="margin-top: 15px; color: #333;">'.$item[1].'</h4>
                        	<p style="color: #666;">'.$item[2].'</p>
                    	</div>
                	</div>';
            	}
            	?>
        		</div>
    		</div>
	   </section>
	   <!-- =============== End of Infrastructure Grid Section =============== -->   
	   <!--========== Strat CSS Infrastructure =========-->
  		 <style>
			.gradient-heading {
  				font-weight: 800;
  				background: linear-gradient(90deg, #0044cc, #00aaff);
  				-webkit-background-clip: text;
  				-webkit-text-fill-color: transparent;
  				text-shadow: 0 1px 2px rgba(0,0,0,0.2);
  				margin: 10px auto;
			  }
             
			.divider {
  				height: 8px;
  				background: linear-gradient(135deg, #00c6ff, #1e90ff, #87cefa);
  				margin: 10px auto;
  				border-radius: 70px;
  				width: 60%;
			  }
		</style>
	  <!--========== End CSS Infrastructure =========-->
    
<!-- =============== End of Infrastructure Grid Section =============== -->
	
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