<!--======================================================
    File Name   : insurance.php
    Project     : RMIT Groups - HIT
    Description : Insurance Page 
    Developed By: TrinityWebEdge
    Date Created: 17-12-2025
    Last Updated: <?php echo date("d-m-Y"); ?>
    Note         : This page defines the Insurance Page of RMIT Groups website.
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
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/responsive.css">
    
    
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]-->
	<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<!--[endif]-->
    
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
                    <h2>Insurance</h2>
                </div>
            </div>
            <!-- End of Page Title -->

            <!-- Start of Breadcrumb -->
            <div class="row">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li><a href="/hit/index.php">home</a></li>
                        <li class="active">Insurance</li>
                    </ul>
                </div>
            </div>
            <!-- End of Breadcrumb -->

        </div>
    </section>
    <!-- =============== End of Page Header 1 Section =============== -->





<!-- ===== Start of Insurance Information Section ===== -->
<section class="ptb80 insurance-page" id="insurance" style="background: linear-gradient(135deg, #fbeeee, #efd1d1);">	
  <div class="container">

    <!-- Page Header -->
    <div class="text-center mb40">
      <h2 class="gradient-heading">On-Campus Student Insurance Support</h2>
      <p class="lead">
        Insurance coverage provided exclusively for incidents occurring within the HIT campus.
      </p>
      <div class="divider"></div>
    </div>

    <!-- Introduction -->
    <div class="row mb40">
      <div class="col-md-12">
        <p>
          Holy Institute of Technology (HIT) provides limited insurance coverage
          to support students in case of accidental injuries or medical emergencies that
          occur strictly <strong>within the HIT campus premises</strong>.
        </p>
        <p>
          This facility is intended to ensure immediate assistance during institute-related
          activities and does not extend to off-campus, personal, or non-academic incidents.
        </p>
      </div>
    </div>

    <!-- Insurance Coverage -->
    <div class="row mt40">
      <div class="col-md-6">
        <div class="info-card">
          <h4 class="gradient-heading">🏥 Covered Under Insurance</h4>
            <div style = "height:5px;
						  background:linear-gradient(90deg, #5e1212 0%, #7b1e1e 45%, #a83232 100%);
						  border-radius:70px;
						  width:65%;
						  margin-bottom:15px;
        				  margin-top:15px;">
            </div> 
          <ul>
            <li>Accidental injuries inside HIT campus</li>
            <li>Medical emergencies during class hours</li>
            <li>Incidents in laboratories, workshops, and classrooms</li>
            <li>College-approved academic activities within campus</li>
          </ul>
        </div>
      </div>

      <div class="col-md-6">
        <div class="info-card">
          <h4 class="gradient-heading">🚫 Not Covered</h4>
            <div style = "height:5px;
						  background:linear-gradient(90deg, #5e1212 0%, #7b1e1e 45%, #a83232 100%);
						  border-radius:70px;
						  width:36%;
						  margin-bottom:15px;
        				  margin-top:15px;">
            </div>
          <ul>
            <li>Off-campus incidents or travel</li>
            <li>Personal medical conditions</li>
            <li>Unauthorized activities</li>
            <li>Events outside college premises</li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Claim Process -->
    <div class="row mt40">
      <div class="col-md-12">
        <div class="info-card">
          <h4 class="gradient-heading">📄 On-Campus Claim Procedure</h4>
            <div style = "height:5px;
						  background:linear-gradient(90deg, #5e1212 0%, #7b1e1e 45%, #a83232 100%);
						  border-radius:70px;
						  width:34%;
						  margin-bottom:15px;
        				  margin-top:15px;">
            </div>
          <ol>
            <li>Report the incident immediately to faculty or administration</li>
            <li>Visit the campus medical facility or assigned hospital</li>
            <li>Submit required documents to the administration office</li>
            <li>Claims are processed only after internal verification</li>
          </ol>
        </div>
      </div>
    </div>

    <!-- Contact -->
    <div class="row mt40">
      <div class="col-md-12 ">
        <h4 class="gradient-heading">Need Assistance?</h4>         
        <p>
          For on-campus insurance support, please contact the HIT Administration Office
          during working hours.
        </p>
        <a href="contact.php" class="btn btn-danger btn-effect mt20">
          Contact Administration
        </a>
      </div>
    </div>

  </div>
    
    <p style="font-size:13px; color:#666; text-align:center; margin-top:30px;">
  		Disclaimer: Insurance coverage is subject to institute rules and insurance provider policies.
	</p>
</section>
<!-- ===== End of Insurance Information Section ===== -->
    
<!-- ===== Start of CSS for Insurance Section ===== -->
  <style>
      .insurance-page p {
  font-size: 16px;
  line-height: 1.7;
  color: #333;
}

.info-card {
  background: #ffffff;
  padding: 25px;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  transition: all 0.3s ease;
}

.info-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

.info-card h4 {
  font-weight: 700;
  margin-bottom: 15px;
}

.info-card ul,
.info-card ol {
  padding-left: 20px;
}

.info-card li {
  margin-bottom: 8px;
  font-weight: 500;
}
</style>
<!-- ===== End of CSS for Insurance Section ===== -->


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