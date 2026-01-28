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
			background:#fff9e6;
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
	max-width: 480px;
	}
	
	.about-image img {
	width: 100%;
	border-radius: 12px;
	object-fit: cover;
    height: 570px;
   	width: 420px
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
            
</style>
    
</head>

<body>
    
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
		<div>📞 91 7682857729 | ✉️ info_cps@gmail.com</div>
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
    
<!-- ===== Start Abouts US Section Home Page ===== -->
<section class="about-cps">
  <div class="container">
    <div class="about-wrapper">

      <!-- ===== CPS HISTORY & INSTITUTION DETAILS ===== -->
<div class="about-text">

  <h4 class="about-tag">KNOW</h4>
  <h2 class="about-title">ABOUT US</h2>
  <span class="about-underline"></span>

  <p>
    Chirag Public School (CPS) was founded in the year 2020 with a vision to provide
    high-quality, value-based education that nurtures young minds and builds strong character.
    We follow the CBSE pattern and emphasize academic excellence, discipline, creativity, and leadership.
  </p>

  <p>
    Since its establishment, CPS has steadily grown into a trusted educational institution
    known for its student-centric approach, modern teaching methods, and strong parent-school partnership.
  </p>

  <p>
    The school was started with a small group of dedicated educators and today stands as a
    progressive learning community that blends traditional values with contemporary education.
  </p>

  <p>
    CPS focuses on:
  </p>

  <ul style="padding-left:18px; margin-bottom:15px;">
    <li>📘 Strong academic foundation with CBSE curriculum</li>
    <li>🧠 Critical thinking, creativity, and problem solving</li>
    <li>⚽ Sports, arts, yoga, and life skills development</li>
    <li>🛡️ Student safety, discipline, and moral values</li>
    <li>💡 Technology-enabled learning through smart classrooms</li>
  </ul>

  <p>
    Over the years, CPS has achieved consistent academic success, introduced innovative
    learning tools, and created a nurturing campus where every child feels valued, supported,
    and inspired to reach their full potential.
  </p>

</div>
<!-- ===== End CPS HISTORY & INSTITUTION DETAILS ===== -->


      <div class="about-image">
        <div class="since-badge">SINCE<br>2020</div>
        <img src="images/campus.jpg" alt="Chirag Public School Campus">
      </div>

    </div>
  </div>
</section>
<!-- ===== End Abouts US Section Home Page ===== -->

<!-- ===== Start Vision, Mission & Values Section ===== -->
<section class="about-cps">
  <div class="container">
    <div class="about-wrapper">

      <div class="about-text">

        <h4 class="about-tag">KNOW</h4>
        <h2 class="about-title">VISION & MISSION </h2>
        <span class="about-underline"></span>

        <!-- Vision -->
        <h3 class="about-title" >Vision</h3>
        <p>
          To nurture confident, knowledgeable, and responsible individuals who are prepared
          to thrive in a global society while upholding strong moral and ethical values.
        </p>

        <!-- Mission -->
        <h3 class="about-title" > Mission</h3>
        <p>
          To deliver high-quality education through innovative teaching, a supportive learning environment,
          and a strong emphasis on discipline, creativity, and holistic development.
        </p>
        <ul style="padding-left:18px;margin-bottom:15px;">
          <li>✔ Encourage curiosity and critical thinking</li>
          <li>✔ Promote discipline, respect, and responsibility</li>
          <li>✔ Foster creativity, leadership, and teamwork</li>
          <li>✔ Support emotional, social, and academic growth</li>
        </ul>

        <!-- Values -->
        <div class="about-text">

        <h4 class="about-tag" style="margin-bottom:30px;"> KNOW CORE VALUES - CPS</h4><br><br>
                
        <p>🌟 Integrity and honesty in all actions</p>
        <p>🌟 Respect for self, others, and the environment</p>
        <p>🌟 Excellence in learning and teaching</p>
        <p>🌟 Responsibility and accountability</p>
        <p>🌟 Compassion and service to the community</p>

       </div>
           
      </div>

      <div class="about-image">
        <img src="images/vision.jpg" alt="Vision Mission Values">
      </div>

    </div>
  </div>
</section>
<!-- ===== End Vision, Mission & Values Section ===== -->

    
<!--=============== Start Showcase ===============-->
<section class="showcase">
  <div class="container">
    <h2 class="text-center mb-5">Why Choose Chirag Public School?</h2>

    <div class="row g-4">

      <div class="col-md-4">
        <div class="card p-4 text-center">
          <h5>Smart Classrooms</h5>
          <p>Interactive learning with modern technology.</p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card p-4 text-center">
          <h5>Experienced Faculty</h5>
          <p>Qualified teachers with passion for education.</p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card p-4 text-center">
          <h5>Holistic Development</h5>
          <p>Sports, arts and academics together.</p>
        </div>
      </div>

      <!-- NEW ITEMS -->

      <div class="col-md-4">
        <div class="card p-4 text-center">
          <h5>Safe & Secure Campus</h5>
          <p>CCTV surveillance and child-friendly environment.</p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card p-4 text-center">
          <h5>Transport Facility</h5>
          <p>Safe and reliable school transport across the city.</p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card p-4 text-center">
          <h5>Activity-Based Learning</h5>
          <p>Practical exposure through projects and activities.</p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card p-4 text-center">
          <h5>Digital Learning Tools</h5>
          <p>Tablets, smart boards and multimedia support.</p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card p-4 text-center">
          <h5>Sports & Fitness</h5>
          <p>Indoor and outdoor games for physical development.</p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card p-4 text-center">
          <h5>Parent-Teacher Interaction</h5>
          <p>Regular feedback and student progress updates.</p>
        </div>
      </div>

    </div>
  </div>
</section>
<!--=============== End Showcase ===============-->

	
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