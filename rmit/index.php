<!--======================================================
    File Name   : index.php
    Project     : RMIT Groups - RMIT
    Description : About Us Page 
    Developed By: TrinityWebEdge
    Date Created: 17-11-2025
    Last Updated: <?php echo date("d-m-Y"); ?>
    Note         : This page defines the Home page of RMIT Groups website.
=======================================================-->
<?php
// Latest News (can be replaced with DB data)
$latest_news = [
    "Admissions Open for Academic Year 2025–26",
    "3rd Semester BCA & BES Examinations Coming Soon",
    "Rechecking / Retotalling Application for Special Examination Now Available",
    "Internal Assessment Schedule Published for All Departments",
    "Workshop on Emerging Technologies Organized by RMIT",
    "Campus Placement Drive Announced for Final Year Students",
    "Thought of the Day: Education is the most powerful weapon to change the world"
];
?>

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
  <link rel="stylesheet" href="assets/css/home.css">

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

    <!-- =============== Start of Main Slider Section =============== -->
    <section class="main2">

        <!-- ===== Start of Swiper Slider ===== -->
        <div class="swiper-container">
            <div class="swiper-wrapper">
                <div class="swiper-slide overlay-black" style="background: url('/rmit/images/sliders/slider1.jpg'); background-size: cover; background-position: 50% 50%;">
  				<div class="slider-content container">
    			  <div class="text-center">
      			  <div class="section-title">
        			<h2 class="text-white">RAJIV MEMORIAL INSTITUTE OF TECHNOLOGY</h2>
      			  </div>
      				<p class="text-white">Permanent Affiliation To Berhampur University and Govt. Of Odisha</p>
    			</div>
  			</div>
		</div>
        <!--======== End of Slide 1 ==========-->


        <!--============== Start of Slide 2 ============-->
        <div class="swiper-slide overlay-black" style="background: url('/rmit/images/sliders/slider2.png'); background-size: cover; background-position: 50% 50%;">
            <div class="slider-content container">
                <div class="text-center">
                    <div class="section-title">
                        <h2 class="text-white">your career start now!</h2>
                    </div>
                    <p class="text-white">The World is Beaming with Technological Innovation . RMIT Train Students to Keep Pace with the Advancement .
                    </p>
                </div>
            </div>
        </div>
        <!--=============== End of Slide 2 ==============-->
        
        <!--============== Start of Slide 3 ============-->
        <div class="swiper-slide overlay-black" style="background: url('/rmit/images/sliders/slider3.png'); background-size: cover; background-position: 50% 50%;">
            <div class="slider-content container">
                <div class="text-center">
                    <div class="section-title">
                        <h2 class="text-white">Begin Your Journey Toward Digital Excellence and Technological Innovation</h2>
                    </div>
                    <p class="text-white">Where ambition meets education, and potential becomes profession.
                    </p>
                </div>
            </div>
        </div>
        <!--=============== End of Slide 3 ==============-->

        </div>
        <!-- End of Swiper Wrapper -->

        <!-- Navigation Buttons -->
        <div class="swiper-button-prev"><i class="fa fa-angle-left"></i></div>
        <div class="swiper-button-next"><i class="fa fa-angle-right"></i></div>

        </div>
        <!-- ===== End of Swiper Slider ===== -->
    </section>
    <!-- =============== End of Main Slider Section =============== -->
    
	<!--=============== Start of RMIT Latest News Section ===============-->   

	<section>
	<div class="latest-news-bar">
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
			<div class="news-marquee" data-speed="60">
	
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
    <script>
document.addEventListener("DOMContentLoaded", function() {
  const marquees = document.querySelectorAll(".news-marquee");
  marquees.forEach(marquee => {
    // Read speed value from data-speed (seconds)
    const duration = parseInt(marquee.getAttribute("data-speed"), 10) || 60;

    // Only update duration, keep other CSS animation settings intact
    marquee.style.animationDuration = `${duration}s`;
    marquee.style.animationTimingFunction = "linear";
    marquee.style.animationIterationCount = "infinite";
    marquee.style.animationName = "marquee";
  });
});
</script>

	<!-- ===== End of RMIT Latest News Section ===== -->


     
    <!-- ========== Start of Home Page Mian Section ==========-->

    <!-- ========== Start of Home Page Hero Section ==========-->
    <section class="shop ptb80" style="background: linear-gradient(135deg, #e0f7ff, #b3e9fc);">	
	  <div class="container" >
                
           <!-- Start of Row -->
            <div class="row">
                <!-- Start of Product Wrapper -->
                <div class="col-md-12 product-wrapper">
                    <!-- ===== Start of Row ===== -->
                    <div class="row">
                        <!-- Start of First Column -->
				<div class="col-md-6 col-xs-12 about-video">                
					
					<!-- Image -->
					<div class="item">
						<img src="/images/rmit.png" class="img-responsive" alt="Home Page Icon" loading="lazy" decoding="async"
							style="width:100%; height:620px; border-radius:16px; " >
					</div>
	
				</div>
				<!-- End of First Column -->
				
				<!-- Start of Second Column -->
				<div class="col-md-6 col-xs-12">
					<h3 class="pt05 text-blue" style="font-weight: 700;"> Empowering Minds & Building Futures</h3>
                    <!-- Radiant blue divider line -->
					<div style="height: 5px; background: linear-gradient(135deg, #00c6ff, #1e90ff, #87cefa); margin-top: 07px; margin-bottom: 07px; border-radius: 70px; width: 75%;">
        			</div>
					<p class="pt20">Rajiv Memorial Institute of Technology (RMIT) is a center of excellence committed to nurturing innovation, integrity, and leadership. 
								   With a vision to empower students and shape futures, RMIT blends academic rigor with practical exposure, preparing graduates to thrive in industries 								   and organizations worldwide.
								</p>
                                <p>Our faculty are dynamic, research-oriented, and deeply engaged in advancing knowledge across diverse technological domains such as Operating Systems, 									Object-Oriented Programming, Database Management, .NET Technology, Computer Architecture, and Network Technology. 
								   Their expertise ensures that students receive not only theoretical foundations but also hands-on training aligned with real-world challenges.
								</p>
                                <p>Every year, RMIT graduates achieve commendable milestones, applying their skills with clarity, creativity, and responsibility. The institute fosters a 									 vibrant learning environment enriched by modern infrastructure, digital resources, and a collaborative campus culture that encourages innovation and 									 personal growth.At RMIT, education is more than academics — it is about building character, instilling confidence, and inspiring progress. We stand as 								   a launchpad for visionaries, problem-solvers, and leaders who are ready to make a meaningful impact on society.
								</p><br>
                                <p>Choosing RMIT means joining a legacy of professionalism, pride, and purpose — where minds are empowered and futures are built.</p>
                                <a href="aboutus.php" class="btn btn-blue btn-effect mt20">Explore RMIT</a>
                            </div>
				<!-- End of Second Column -->
                    </div>
                    <!-- ===== End of Row ===== -->					
		<!-- ========== Start of Home Page Hero Section ==========-->
                    
		<!-- ================= Start of RMIT Important Links =============== -->
                    
			<!-- Title -->
				<div class= "ptb80" >                   
					<h2 style="
					text-align:center;
					font-size:36px;
					font-weight:800;
					background: linear-gradient(90deg, #b11226, #d62828, #ff4d4d);
					-webkit-background-clip: text;
					-webkit-text-fill-color: transparent;
					margin-bottom:50px;
					padding:30px 10px;
					position:relative;
					">
					Important Links
					<span style="
						display:block;
						width:20%;
						height:5px;
						background:linear-gradient(135deg, #00c6ff, #1e90ff, #87cefa);
						margin:10px auto 0;
                        margin-bottom:15px;
						border-radius:50px;
					"></span>
					</h2>
					
					<!-- Grid -->
					<div class="mt20" style="
					display:grid;
					grid-template-columns:repeat(auto-fit, minmax(260px, 1fr));
					gap:60px;
					max-width:1100px;
					margin:auto;
					padding:0 15px;
					">
					
					<a href="DownloadAdmissionForm.php" class="rmit-link admission">
						<img src="/rmit/images/icons/admission.png" class="img-responsive" alt="admission logo" loading="lazy" decoding="async">
						<span>Admission</span>
						<i class="fa fa-arrow-right"></i>
					</a>
					
					<a href="feepayment.php" target="_blank"  class="rmit-link payment">
						<img src="/rmit/images/icons/payment.png" class="img-responsive" alt="Payment logo" loading="lazy" decoding="async">
						<span>Fee Payment</span>
						<i class="fa fa-arrow-right"></i>
					</a>
					
					<a href="assets/pdf/RMIT_Holiday_List_2026-27.pdf" target="_blank" class="rmit-link holiday">
						<img src="/rmit/images/icons/holiday.png" class="img-responsive" alt="holiday logo" loading="lazy" decoding="async">
						<span>Holiday List</span>
						<i class="fa fa-arrow-right"></i>
					</a>
					
					<a href="index.php" class="rmit-link alumni"> 
						<img src="/rmit/images/icons/alumni.png" class="img-responsive" alt="alumni logo" loading="lazy" decoding="async">
						<span>Alumni</span>
						<i class="fa fa-arrow-right"></i>
					</a>
					
					<a href="https://forms.gle/7tVRtMMJmMAnPBR59" target="_blank" class="rmit-link feedback">
						<img src="/rmit/images/icons/feedback1.png" class="img-responsive" alt="feedback logo" loading="lazy" decoding="async">
						<span>Feedback</span>
						<i class="fa fa-arrow-right"></i>
					</a>
					
					<a href="/rmit-smartcampus/index.php" target="_blank" class="rmit-link smartcampus">
						<img src="/rmit/images/icons/rmit-smrtclogo.png" class="img-responsive" alt="RMIT-SmartCampus logo" loading="lazy" decoding="async">
						<span>RMIT-SmartCampus</span>
						<i class="fa fa-arrow-right"></i>
					</a>
					
				</div>    
			<!-- ================= End of RMIT Important Links ================= -->

                    <!-- ===== Start of Row ===== -->
                    <div class="row mt60">
                        <div class="col-md-12">
                            <h3 style="font-weight:800;
              						 background: linear-gradient(90deg, #0044cc, #00aaff);                                   
              						 -webkit-background-clip: text;
              						 -webkit-text-fill-color: transparent;
              						 text-shadow: 0 1px 2px rgba(0,0,0,0.2);
                                     margin-top: 30px; 
            						">Popular Courses
							</h3>
                         <div style="height: 5px; background: 
                                     linear-gradient(135deg, #00c6ff, #1e90ff, #87cefa);  
                                     margin-top: 10px; 
                                     margin-bottom: 05px; 
                                     border-radius: 70px;
                                     width:20%;
                                    ">
						</div>
                        </div>
                        
                        
                        <!-- Start of Product 1 -->
                        <div class="col-md-6 col-sm-6 col-xs-12 mt40">
                            <div class="product nomargin">
                                <!-- Product Image -->
                                <div class="product-image">                                   
                                        <img src="/rmit/images/bca.jpg" class="img-responsive" alt="BCA logo" loading="lazy" decoding="async"> 
                                        <!-- Seats Badge -->
            							<div style="
                							position:absolute;
                							top:25%;
											right:0;
											transform:translateY(-50%);
											background:linear-gradient(90deg, #0044cc, #00aaff);
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
                                    <a href="bca.php">
                                        <h5>Bachelor in Computer Application (BCA)  </h5>
                                    </a>
                                </div>

                            </div>
                        </div>
                        <!-- End of Product 1 -->

                        <!-- Start of Product 1 -->
                        <div class="col-md-6 col-sm-6 col-xs-12 mt40">
                            <div class="product nomargin">
                                <!-- Product Image -->
                                <div class="product-image">                                   
                                        <img src="/rmit/images/bes.jpg" class="img-responsive" alt="BES logo" loading="lazy" decoding="async">
										<!-- Seats Badge -->
										<div style="
											position:absolute;
											top:25%;
											right:0;
											transform:translateY(-50%);
											background:linear-gradient(90deg, #0044cc, #00aaff);
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
                                    <a href="bse.php">
                                        <h5>Bachelor in Electronic Science(BES) </h5>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!-- End of Product 1 -->
                        
                                             

                    </div>
                    <!-- ===== Start of Row ===== -->

                </div>
                <!-- End of Product Wrapper -->
            </div>
            <!-- End of Row -->
            
	<!-- ========== End of Home Page Hero Section ==========-->
              
    <!-- ========== Start of RMIT Attraction & Impact Section ========== -->
    <div class="container" style= "margin-top:25px">
    
      <!--==== Section Title =====-->
      <div class="text-center mb40" style= "margin-top: 20px;">
        <h2 class="gradient-heading">Empowering learners, inspiring leaders, transforming futures.</h2>
        <p style="font-size:16px; font-weight:600; color:black;">Student Attraction & Academic Excellence</p>
        <div class="divider"></div>
      </div>
    
      <!--===== Start Student & Academic Attraction =====-->
      <div class="row mt40">
        <div class="col-md-6">
          <div class="card-box">
            <h3 class="gradient-heading">🎓 Student Attraction</h3><hr>
            <ul class="attraction-list">
              <li>✔ Vibrant campus life with cultural festivals, symposiums, and student-led clubs</li>
              <li>✔ Modern infrastructure: digital classrooms, innovation labs, and collaborative spaces</li>
              <li>✔ Strong alumni network offering mentorship and career guidance</li>
              <li>✔ Opportunities to participate in national & international conferences</li>
              <li>✔ Safe, inclusive, and eco‑friendly campus environment</li>
              <li>✔ Scholarships and support programs for meritorious students</li>
            </ul>
          </div>
        </div>
    
        <div class="col-md-6">
          <div class="card-box">
            <h3 class="gradient-heading">📚 Academic Attraction</h3><hr>
            <ul class="attraction-list">
              <li>✔ Permanent affiliation to Berhampur University & Govt. of Odisha</li>
              <li>✔ Faculty expertise across domains: AI, Cybersecurity, Cloud Computing, IoT, and more</li>
              <li>✔ Hands-on learning through hackathons, capstone projects, and technical showcases</li>
              <li>✔ Research-driven ecosystem with publications in reputed journals</li>
              <li>✔ Industry-aligned curriculum preparing graduates for global opportunities</li>
            </ul>
          </div>
        </div>
      </div>
	  <!--=====End Student & Academic Attraction =====-->
    
      <!--===== Start Impact Stats =====-->
     <div class="text-center mb40 mt60">
    	<h2 class="gradient-heading">Numbers that inspire confidence, milestones</h2>
    	<p style="font-size:16px; font-weight:600; color:black;">A legacy of scale, excellence, and innovation</p>
    	<div class="divider"></div>
    	</div>
    
    	<div class="row text-center">
    	<!--==== Stat Box 1 ====-->
    	<div class="col-md-2 col-sm-4 col-xs-6 stat-box">
    		<div class="stat-card">
    		<i class="fas fa-user-graduate fa-2x"></i>
    		<h4>200+</h4>
    		<p>Students</p>
    		</div>
    	</div>
    	
    	<!--==== Stat Box 2 ====-->
    	<div class="col-md-2 col-sm-4 col-xs-6 stat-box">
    		<div class="stat-card">
    		<i class="fas fa-chalkboard-teacher fa-2x"></i>
    		<h4>55+</h4>
    		<p>Faculty & Staff</p>
    		</div>
    	</div>
    	
    	<!--==== Stat Box 3 =====-->
    	<div class="col-md-2 col-sm-4 col-xs-6 stat-box">
    		<div class="stat-card">
    		<i class="fas fa-user-tie fa-2x"></i>
    		<h4>1500+</h4>
    		<p>Alumni</p>
    		</div>
    	</div>
    	
    	<!--==== Stat Box 4 =====-->
    	<div class="col-md-2 col-sm-4 col-xs-6 stat-box">
    		<div class="stat-card">
    		<i class="fas fa-brain fa-2x"></i>
    		<h4>4500+</h4>
    		<p>Projects</p>
    		</div>
    	</div>
    	
    	<!--==== Stat Box 5 ====-->
    	<div class="col-md-2 col-sm-4 col-xs-6 stat-box">
    		<div class="stat-card">
    		<i class="fas fa-laptop-code fa-2x"></i>
    		<h4>5,000+</h4>
    		<p>Conferences</p>
    		</div>
    	</div>
    	
    	<!--==== Stat Box 6 ====-->
    	<div class="col-md-2 col-sm-4 col-xs-6 stat-box">
    		<div class="stat-card">
    		<i class="fas fa-trophy fa-2x"></i>
    		<h4>100+</h4>
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
	  
    </section>
    <!-- ========== End of RMIT Attraction & Impact Section ========== -->

	<!-- ========== End of Home Page Mian Section ==========-->
        
    <!-- ===== Start of Recruiting Companies Section ===== -->
    <section class="get-started ptb40" style="background: linear-gradient(90deg, #0044cc, #00aaff);">
        <div class="container">
            <div class="row">

                <!-- Column -->
                <div class="col-md-10 col-sm-9 col-xs-12">
                    <h3 class="text-white" style="text-align-center">Career Outcomes & Placements at RMIT</h3>
                </div>
                
            </div>
        </div>
    </section>
    <!-- ===== End of Recruiting Companies Section ===== -->



   
	<!-- ===== Start of RMIT Placement Highlights Section ===== -->
	<section class="ptb80" id="rmit-placements" style="background: linear-gradient(135deg, #e0f7ff, #b3e9fc);">
	<div class="container">
	
		<!-- ===== Section Title ===== -->
		<div class="text-center mb60">
		<p style="font-size:16px; font-weight:600; color:#333; max-width:850px; margin:auto;">
			Our <strong>BCA & BES graduates</strong> are building successful careers across
			leading technology and electronics organizations in India and abroad.
		</p>
		</div>
	
		<!-- ===== Start Bonus Stats Row ===== -->
		<div class="row text-center mb60">
	
		<div class="col-md-3 col-sm-6 mb30">
			<div class="stat-card">
			<i class="fas fa-chart-line fa-2x"></i>
			<h4>80%+</h4>
			<p>Placement Rate</p>
			</div>
		</div>
	
		<div class="col-md-3 col-sm-6 mb30">
			<div class="stat-card">
			<i class="fas fa-user-graduate fa-2x"></i>
			<h4>1500+</h4>
			<p>Successful Alumni</p>
			</div>
		</div>
	
		<div class="col-md-3 col-sm-6 mb30">
			<div class="stat-card">
			<i class="fas fa-building fa-2x"></i>
			<h4>30+</h4>
			<p>Recruiting Partners</p>
			</div>
		</div>
	
		<div class="col-md-3 col-sm-6 mb30">
			<div class="stat-card">
			<i class="fas fa-briefcase fa-2x"></i>
			<h4>₹2.9–5.5 LPA</h4>
			<p>Average Package</p>
			</div>
		</div>
	
		</div>
		<!-- ===== End Bonus Stats Row ===== -->
	
	<!-- ===== Start Placement Showcase ===== -->
	<div class="row text-center placement-row">
		
	<div class="col-md-3 col-sm-6 col-xs-12">
		<div class="placement-card">
		<div class="student-img">
			<img src="/rmit/images/students/rahul-patra.jpg" alt="Amit Kumar" >
		</div>
		<h5>Amit Kumar</h5>
		<p class="degree">BCA Graduate</p>
		<span class="company">Software Engineer – Infosys</span>
		</div>
	</div>
	
	<div class="col-md-3 col-sm-6 col-xs-12">
		<div class="placement-card">
		<div class="student-img">
			<img src="/rmit/images/students/rahul-patra.jpg" alt="Sneha Dash" >
		</div>
		<h5>Sneha Dash</h5>
		<p class="degree">BES Graduate</p>
		<span class="company">Electronics Technician – VIVO</span>
		</div>
	</div>
	
	<div class="col-md-3 col-sm-6 col-xs-12">
		<div class="placement-card">
		<div class="student-img">
			<img src="/rmit/images/students/rahul-patra.jpg" alt="Narayan Patra">
		</div>
		<h5 >Narayan Patra</h5>
		<p class="degree">BES Graduate</p>
		<span class="company">Quality Engineer – SAMSUNG</span>
		</div>
	</div>
	
	<div class="col-md-3 col-sm-6 col-xs-12">
		<div class="placement-card" >
		<div class="student-img">
			<img src="/rmit/images/students/rahul-patra.jpg" alt="Rahul Patra">
		</div>
		<h5>Rahul Patra</h5>
			<p class="degree" >BCA Graduate</p>
			<span class="company" >Junior Developer – TCS</span>
		</div>
	</div>
	
	</div>
	<!-- ===== End Placement Showcase ===== -->
	
	
		<!-- ===== CTA Button ===== -->
		<div class="text-center mt40">
		<a href="recruittedstudents.php" class="btn btn-blue btn-effect">
			View All
		</a>
		</div>
	
	</div>
	</section>
			
		<style>
			/* Placement Card */
			.placement-card {
				border-radius: 12px;
				text-align: center;
				padding: 20px;
				box-shadow: 0 4px 12px rgba(0,0,0,0.15);
				background: linear-gradient(135deg, #0044cc, #00c6ff, #66f2ff);
				transition: transform 0.3s ease;
				}
			
			.placement-card:hover {
				transform: translateY(-5px);
				}
			
			/* Student Image Wrapper */
			.student-img {
				margin: 0 auto 15px;
				position: relative;
				width: 140px;
				height: 140px;
				}
			
			/* Student Image */
			.student-img img {
				width: 100%;
				height: 100%;
				border-radius: 50%;          /* makes it perfectly round */
				object-fit: cover;           /* crops neatly */
				border: 2px solid #3ff;   /* highlighted border */
				box-shadow: 0 6px 18px rgba(0,0,0,0.25);
				}
			
			/* Student Name */
			.placement-card h5 {
				font-size: 18px;
				font-weight: 500;
				color: black;
				margin-bottom: 6px;
				}
			
			/* Degree */
			.placement-card .degree {
				font-size: 14px;
				color: black;
				margin-bottom: 4px;
				}
			
			/* Company */
			.placement-card .company {
				font-size: 14px;
				color: black;
				font-weight: 500;
				}
		</style>
	<!-- ===== End of RMIT Placement Highlights Section ===== -->

	<!--===== Start RMIT Degree Admission Popup =====-->
	<div class="rmit-popup-overlay" id="rmit-degree-popup">
	<div class="rmit-popup">
		<button class="rmit-close" onclick="document.querySelector('#rmit-degree-popup').style.display='none'">×</button>
	
		<div class="rmit-header">
		🎓 Admission Open 2026 – 27
		</div>
	
		<div class="rmit-body">
		<div class="rmit-logos">
			<img src="images/homelogo.png" alt="RMIT Logo" 
				style="width:95px;
						height:95px;
						background:white;
						border-radius:18px;
						display:flex;
						align-items:center;
						justify-content:center;
						padding:10px;
						box-shadow:0 6px 18px rgba(0,0,0,0.15);">
			<img src="images/15years.png" alt="Degree College">
		</div>
	
		<div class="rmit-title">
			<span class="blue">Degree</span>
			<span class="gold">Admission</span>
			<span class="pink">2026</span>
		</div>
	
		<!-- Discount -->
		<div class="rmit-discount pulse">
			🎉 Early Bird Offer<br>
			<strong>10% Fee Discount</strong><br>
			<small>Limited Period Only</small>
		</div>
	
		<div class="rmit-tagline">
			Undergraduate Programs Offered<br>
			<strong>BCA & BES</strong><br>
			Shape Your Career with RMIT Excellence
		</div>
	
		<img src="/rmit/images/sliders/slider3.png" class="rmit-image" alt="Campus">
	
		<a href="https://docs.google.com/forms/d/e/1FAIpQLSdLD_YjrHI5fCb9v6hrTzJlXPDxC-xq8tyKnz8S5vQFoeQZIQ/viewform"
			class="rmit-btn" target="_blank">
			Apply Now
		</a>
	
		<div class="rmit-tnc">*T&C Apply</div>
		</div>
	</div>
	</div>
	<!--===== End RMIT Degree Admission Popup =====-->
			
	<!--===== Start RMIT Degree Admission Popup CSS=====-->
		<style>
		/* Overlay */
		.rmit-popup-overlay{
		position:fixed;
		inset:0;
		background:rgba(10,20,50,0.75);
		display:flex;
		justify-content:center;
		align-items:center;
		z-index:9999;
		}
		
		/* Popup Box */
		.rmit-popup{
		width:90%;
		max-width:420px;
		background:linear-gradient(135deg,#0a1f44,#0b3c7d,#1e90ff);
		border-radius:18px;
		overflow:hidden;
		box-shadow:0 20px 50px rgba(0,0,0,0.4);
		color:#fff;
		position:relative;
		isolation:isolate; /* prevent glow bleeding */
		}
		
		/* Close button */
		.rmit-close{
		position:absolute;
		top:10px;
		right:12px;
		border:none;
		background:rgba(255,255,255,0.95);
		color:#0a1f44;
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
		
		.rmit-close:hover{ background:#ffd700; }
		
		/* Header */
		.rmit-header{
		background:linear-gradient(to right,#ffd700,#ffcc33);
		color:#0a1f44;
		text-align:center;
		font-weight:800;
		padding:12px;
		font-size:16px;
		}
		
		/* Body */
		.rmit-body{
		padding:20px;
		text-align:center;
		}
		
		/* Logos row */
		.rmit-logos{
		display:flex;
		justify-content:space-between;
		align-items:center;
		min-height:110px;
		padding:10px 20px;
		}
		
		.rmit-logos img{
		height:95px;
		width:95px;
		border-radius:07px;
		object-fit:contain;
		}
		
		/* Right logo glow */
		.rmit-logos img:last-child{
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
		.rmit-title span{
		display:inline-block;
		padding:6px 12px;
		border-radius:8px;
		font-weight:800;
		margin:4px;
		}
		.rmit-title .blue{background:#0d47a1;}
		.rmit-title .gold{background:#ffd700;color:#000;}
		.rmit-title .pink{background:#ff4081;}
		
		/* Discount */
		.rmit-discount{
		margin:14px auto;
		padding:14px;
		width:85%;
		background:linear-gradient(135deg,#fff3cd,#ffe066);
		color:#4a3b00;
		border-radius:14px;
		font-weight:800;
		filter:drop-shadow(0 0 20px rgba(255,215,0,0.6));
		}
		
		/* Safe pulse */
		@keyframes pulse {
		0%{transform:scale(1);}
		50%{transform:scale(1.04);}
		100%{transform:scale(1);}
		}
		.pulse{ animation:pulse 1.8s infinite; }
		
		/* Tagline */
		.rmit-tagline{
		font-size:14px;
		margin:12px 0;
		line-height:1.5;
		}
		
		/* Image */
		.rmit-image{
		width:100%;
		border-radius:12px;
		margin:10px 0;
		}
		
		/* Button */
		.rmit-btn{
		display:block;
		margin:14px auto 6px;
		padding:12px;
		background:linear-gradient(to right,#ffd700,#ffb700);
		color:#0a1f44;
		font-weight:800;
		border-radius:30px;
		text-decoration:none;
		}
		
		/* T&C */
		.rmit-tnc{
		font-size:11px;
		opacity:0.8;
		margin-top:6px;
		}
		
		@media (min-width:768px){
		.rmit-popup{ max-width:480px; }
		}
		</style>
	
	<!--===== End RMIT Degree Admission Popup CSS=====-->

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