<!--======================================================
    File Name   : index.php
    Project     : RMIT Groups - MAIN
    Description : About Us Page 
    Developed By: TrinityWebEdge
    Date Created: 17-12-2025
    Last Updated: <?php echo date("d-m-Y"); ?>
    Note         : This page defines the Home page of RMIT Groups website.
=======================================================-->

<!DOCTYPE HTML>
<html lang="en">

<head>
	
    <!-- Mobile viewport optimized -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	
	<!-- Meta Tags - SEO Optimized -->
	<meta name="description" content="RMIT Group of Institution is a premier educational institute in Berhampur, Odisha, offering quality academic programs, professional training, 										  and skill development initiatives. We empower students with clarity, confidence, and innovation in their learning journey.">
	<meta name="keywords" content="RMIT Group of Institution, Berhampur education, Odisha colleges, professional training, skill development, academic excellence, trusted institute, 										 higher education, RMIT Berhampur">
	<meta name="author" content="TrinityWebEdge - RMIT Group of Institution Team">
	<meta name="robots" content="index, follow">
	<meta name="language" content="English">
	
	<!-- Website Title -->
	<title>RMIT Group of Institutions, Berhampur | Empowering Minds, Building Futures</title>
	
	<!-- Standard favicon -->
	<link rel="icon" href="images/favicon.ico" type="image/x-icon">
	
	<!-- Retina PNG versions -->
	<link rel="icon" href="images/favicon_32.png" sizes="32x32" type="image/png">
	<link rel="icon" href="images/favicon_64.png" sizes="64x64" type="image/png">
	<link rel="icon" href="images/favicon_180.png" sizes="180x180" type="image/png">
	
	<!-- Apple devices -->
	<link rel="apple-touch-icon" href="images/favicon_180.png">
	
	<!-- Google Fonts -->
	<link href="https://fonts.googleapis.com/css?family=Raleway:300,400,400i,700,800|Varela+Round" rel="stylesheet">
	
    <!--=============== css  ===============-->	
    <link type="text/css" rel="stylesheet" href="css/reset.css">
    <link type="text/css" rel="stylesheet" href="css/plugins.css">
    <link type="text/css" rel="stylesheet" href="css/style.css">
    <link type="text/css" rel="stylesheet" href="css/custom.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        
    <!--=============== favicons ===============-->
    <link rel="shortcut icon" href="images/favicon.ico">
        
</head>
           
<body>
     
<style>
body {
  margin: 0;
  font-family: 'Raleway', sans-serif;
}
.gallery-item {
  opacity: 1;
}
</style>

     
  <div class="loader">
     <div class="tm-loader">
	   <div id="circle"></div>
	 </div>
  </div>
     
  <!--================= main start ================-->
  <div id="main">
            
       <!--===============Start header ===============-->
        <header>
            <!-- Nav button-->
            <div class="nav-button">
                <span  class="nos"></span>
                <span class="ncs"></span>
                <span class="nbs"></span>
            </div>
            <!-- Nav button end -->
            <!-- Logo--> 
            <div class="logo-holder">
                <a href="#" class="ajax"><img src="images/logo.png" alt="RMIT Group of Institution logo" loading="lazy" decoding="async" ></a>
            </div>
            <!-- Logo  end--> 
            <!-- Header  title --> 
            
            <!-- Header  title  end-->
            <!-- share -->
            <div class="show-share isShare">
                <a href="index.php">
                    <span>Share</span>
                    <i class="fa fa-chain-broken"></i>  
                </a>          
            </div>
            <!-- share  end-->
         </header>            
        <!--===============End Header  ===============-->
      
<!--=============== Affiliation Logos ===============-->
<div class="shop ptb80" style="
  background: linear-gradient(
  to right,
  #0b3c7d 0%,
  #5faeff 25%,
  #eaf4ff 50%,
  #5faeff 75%,
  #0b3c7d 100%
);
  margin-top:50px; 
  margin-bottom:50px;
  padding: 05px;
">
  <div class="container">
    <div class="row text-center">

      <div class="affiliation-logos">
        <img src="images/icons/sctevtlogob.png" alt="SCTE & VT" loading="lazy">
        <img src="images/icons/ncvt-logo.png" alt="NCVT MIS" class="logo-ncvt"  loading="lazy">
        <img src="images/icons/bulogo.png" alt="Berhampur University" loading="lazy">
        <img src="images/icons/aictelogo.png" alt="AICTE New Delhi" loading="lazy">
        <img src="images/icons/odishassc.png" alt="Odisha Skill Sector Council" class="logo-odisha" loading="lazy">
        <img src="images/icons/cbsc-board.png" alt="CBSE logo" class="logo-odisha" loading="lazy">
      </div>

      <div class="col-12" style="margin-top:05px;">
        <h2 style="font-size:18px; font-weight:600; color:black;">
          Recognized & Affiliated with India’s Leading Academic and Regulatory Institutions
        </h2>
      </div>

    </div>
  </div>
</div>
      
<style>
    
.affiliation-logos {
  text-align: center;
  margin-top: 15px;
  /*margin-bottom: 05px;*/
  width: 100%;
}

/* Very small screens */
@media (max-width: 479px) {
  .affiliation-logos {
    display: inline-block;
  }

  .affiliation-logos img {
    height: 42px;
    width: 42px;
    margin: 6px 8px;
  }
}

/* Normal mobile / tablet */
@media (min-width: 480px) and (max-width: 767px) {
  .affiliation-logos {
    display: inline-block;
  }

  .affiliation-logos img {
    height: 35px;
    width: 35px;
    margin: 8px 10px;
  }
}

/* Desktop and larger */
@media (min-width: 768px) {
  .affiliation-logos {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 60px;
  }

  .affiliation-logos img {
    height: 45px;
    width: 45px;
    margin: 0;
  }
}

/* Mobile spacing fix */
@media (max-width: 767px) {
  .shop {
    margin-bottom: 100px; /* extra breathing room below logos */
  }

  .affiliation-logos {
    display: block; /* stack logos vertically */
    margin-bottom: 20px;
  }

  .affiliation-logos img {
    margin: 8px;
    height: 40px;
    width: 40px;
  }

  .shop h2 {
    font-size: 14px;
    line-height: 1.4;
    margin-top: 10px;
    margin-bottom: 20px;
    display: block;
  }
}

</style>
<!--=============== End Affiliation Logos ===============-->




                        
        <!--=============== wrapper ===============-->	
        <div id="wrapper">       
            
            <!--=============== content-holder ===============-->
            <div class="content-holder elem scale-bg2 transition3"  style="margin-top:80px; ">
                <!-- Page title -->                              
                
                <!--  Navigation end --> 
                <!--  Content -->
                <div class="content" >
                    <!--  blog-inner -->
                    <div class="blog-inner" >
                        <!--  gallery-items  -->
                        <div class="gallery-items hid-port-info grid-small-pad" >
                            <!-- 3 -->
                            <div class="gallery-item" >
                                <div class="grid-item-holder"  >
                                    <article>
                                        <ul class="blog-title" >
                                            <li><a href="hit/index.php" class="tag">Diploma Engineering</a></li>                                                
                                        </ul>
                                        <div class="blog-media">
                                            <img  src="images/hit.webp"   alt="Hit Logo" loading="lazy">
                                        </div>
                                        <div class="blog-text">
                                            <h4 style="font-weight:500;">HOLY INSTITUE OF TECHENLOGY</h4><br> 
                                                                                      
                                            <a href="hit/index.php" class="btn btn-success" ><span>Enter Site</span> <i class="fa fa-long-arrow-right"></i></a>
                                        </div>
                                    </article>
                                </div>
                            </div>
                            <div class="gallery-item" >
                                <div class="grid-item-holder" >
                                    <article>
                                        <ul class="blog-title">
                                            <li><a href="rmit/index.php" class="tag">College of Bachelor</a></li>
                                           
                                        </ul>
                                        <div class="blog-media">
                                            <img  src="images/rmit.png"   alt="RMIT Logo" loading="lazy" decoding="async">
                                        </div>
                                        <div class="blog-text">
                                            <h4 style="font-weight:500;">RAJIV MEMORIAL INSTITUTE OF TECHNOLOGY</h4>
                                            
                                            
                                            <a href="rmit/index.php" class="btn btn-success" ><span>Enter Site</span> <i class="fa fa-long-arrow-right"></i></a>
                                        </div>
                                    </article>
                                </div>
                            </div>
                            <!-- 3 end -->  
                            <!-- 2 -->
                           
                            <!-- 2 end -->
                            <!-- 3 -->
                            <div class="gallery-item">
                                <div class="grid-item-holder">
                                    <article>
                                        <ul class="blog-title">
                                            <li><a href="rmitc/index.php" class="tag">INDUSTRIAL TRAINING CENTRE</a></li>
                                            
                                        </ul>
                                        <div class="blog-media">
                                            <img  src="images/rmitc.webp"   alt="RMITC Logo" loading="lazy" decoding="async">
                                        </div>
                                        <div class="blog-text">
                                            <h4 style="font-weight:500;">RAJIV MEMORIAL INDUSTRIAL TRAINING CENTRE</h4>
                                            
                                            
                                            <a href="rmitc/index.php" class="btn btn-success" ><span>Enter Site</span> <i class="fa fa-long-arrow-right"></i></a>
                                        </div>
                                    </article>
                                </div>
                            </div>
                            <!-- 3 end -->                                   
                           
                           <!-- 3 -->
                           <div class="gallery-item">
                                <div class="grid-item-holder">
                                    <article>
                                        <ul class="blog-title">
                                            <li><a href="cps/index.php" class="tag">CHIRAG PUBLIC SCHOOL</a></li>
                                            
                                        </ul>
                                        <div class="blog-media">
                                            <img  src="images/cpslogomain.png"   alt="ChiragPS Logo" loading="lazy" decoding="async">
                                        </div>
                                        <div class="blog-text">
                                           <h4 style="font-weight:500;"> CHIRAG PUBLIC SCHOOL</h4><br>
                                               
                                            <a href="cps/index.php" class="btn btn-success" ><span>Enter Site</span> <i class="fa fa-long-arrow-right"></i></a>
                                        </div>
                                    </article>
                                </div>
                            </div>
                            <!-- 3 end -->   
                    </div>
                    <!--  blog-inner end -->
                </div>
                <!--  Content  end --> 
                <!-- share  -->
                <div class="share-inner">
                    <div class="share-container  isShare"  data-share="['facebook','googleplus','twitter','linkedin']"></div>
                    <div class="close-share"></div>
                </div>
                <!-- share end -->
            </div>
            <!-- Content holder  end -->
        </div>
        <!-- wrapper end -->
  
		
  <!--=============== footer ===============-->
  <footer>
      <!-- RMIT Group of Institution logo + text -->
	  <div class="policy-box" style="
	   	display:flex;
	   	align-items:center;
	   	justify-content:center;
	   	gap:8px;
	   	color:#001f4d;
	   	flex-wrap:wrap;
        margin-top:05px;
	   ">
	   	<span>&copy;</span>
	   	<img src="images/logo.png" alt="RMIT Group of Institution logo"
	   		style="height:12px; vertical-align:middle; display:inline-block;" loading="lazy">
	   	<span style="font-size:12px;"> - ESTD: 1991 / All rights reserved.</span>
	   </div>

  
	   <div class="footer-social" style="display:flex; justify-content:center; margin-top:05px;">
	   	<span style="display:inline-flex; align-items:center; gap:08px; flex-wrap:wrap;">
	   	<b style="font-size:12px; color:#001f4d;">Developed by</b>
	   
	   <!-- TrinityWebEdge Brand Logo -->
	   <img src="images/trinitywebedge.png" alt="TrinityWebEdge Logo"
         style="height:40px; width:40px; vertical-align:middle;" loading="lazy">

       <!-- TrinityWebEdge Brand Text -->
       <b>
         <a href="https://trinitywebedge.infinityfree.me" target="_blank"
            style="
              font-weight:700;
              font-size:12px;
              display:inline-block;
              background:linear-gradient(90deg,#a100ff,#ff3c3c);
              -webkit-background-clip:text;
              -webkit-text-fill-color:transparent;
              color:#ff3c3c; /* fallback for mobile browsers */
              text-decoration:none;
            ">
           TrinityWebEdge
         </a>
       </b>
	   </span>
	   </div>
  </footer>
  <!--=============== footer end ===============-->
  </div>
  <!--================= main End ================-->
		
        <!--=============== google map ===============-->
        
        <!--=============== scripts  ===============-->
        <script src="js/jquery.min.js" defer></script>
		<script src="js/plugins.js" defer></script>
		<script src="js/core.js" defer></script>
		<script src="js/scripts.js" defer></script>
		<script src="js/custom.js" defer></script>

            
    </body>
        
</html>