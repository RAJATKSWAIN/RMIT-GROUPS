<!--======================================================
    File Name   : admissionlist.php
    Project     : RMIT Groups - HIT
    Description : admissionlist Page 
    Developed By: TrinityWebEdge
    Date Created: 17-11-2025
    Last Updated: <?php echo date("d-m-Y"); ?>
    Note         : This page defines the admissionlist page of RMIT Groups website.
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

    <!-- CSS links -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="css/owl.carousel.min.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/responsive.css">
    
    <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,400i,700,800|Varela+Round&display=swap" rel="stylesheet">

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
                    <h2>Admission List</h2>
                </div>
            </div>
            <!-- End of Page Title -->

            <!-- Start of Breadcrumb -->
            <div class="row">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li><a href="/hit/index.php">home</a></li>
                        <li class="active">Admission List</li>
                    </ul>
                </div>
            </div>
            <!-- End of Breadcrumb -->

        </div>
    </section>
    <!-- =============== End of Page Header 1 Section =============== -->
    
        
        <!-- ===== Start of Admission & Passout Summary Section (HIT) ===== -->
		<section class="shop ptb80" style="background: linear-gradient(135deg, #fbeeee, #efd1d1);">	
			<div class="container-fluid" style="max-width: 1200px; margin: 0 auto;">
				<div class="row">
					<h2 style="
						text-align: center;
						background: linear-gradient(90deg, #5e1212 0%, #7b1e1e 45%, #a83232 100%);
						color: #ffffff;
						padding: 18px 12px;
						border-radius: 8px;
						letter-spacing: 0.5px;
						font-weight: 700;
						box-shadow: 0 3px 10px rgba(168,50,50,0.45);
					">
							HIT Admission Overview – Diploma Engineering Programs
					</h2>
		
					<p style="
						text-align:center;
						font-size:16px;
						color:#4a1a1a;
						margin-bottom: 40px;
						margin-top: 10px;
						font-style:italic;
					">
						“For over 10 years, Holy Institute of Technology has been nurturing skilled Diploma Engineers 
						who contribute to infrastructure, manufacturing, power, and technology-driven industries.”
					</p>


            <div class="col-md-12 cart-wrapper">

                <!-- Start of Admission Table -->
                <div style="width: 100%; margin: 20px auto; padding: 20px; background-color: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <h3 style="text-align: center; margin-bottom: 20px; color: #7b1e1e;">Annual Intake – Diploma Programs</h3>

                    <div style="overflow-x: auto; width: 100%;">
                        <table style="width: 100%; border-collapse: collapse; text-align: center; font-family: Arial, sans-serif;">
                            <thead>
                                <tr style="background-color: #7b1e1e; color: #fff;">
                                    <th style="padding: 14px;">Engineering Branch</th>
                                    <th style="padding: 14px;">Approved Intake / Year</th>
                                    <th style="padding: 14px;">Average Admitted</th>
                                    <th style="padding: 14px;">Study Mode</th>
                                    <th style="padding: 14px;">Program Duration</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="background-color: #f9f9f9;">
                                    <td style="padding: 12px;">Diploma in Mechanical Engineering</td>
                                    <td style="padding: 12px;">120</td>
                                    <td style="padding: 12px;">115</td>
                                    <td style="padding: 12px;">Regular</td>
                                    <td style="padding: 12px;">3 Years</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px;">Diploma in Electrical Engineering</td>
                                    <td style="padding: 12px;">90</td>
                                    <td style="padding: 12px;">85</td>
                                    <td style="padding: 12px;">Regular</td>
                                    <td style="padding: 12px;">3 Years</td>
                                </tr>
                                <tr style="background-color: #f9f9f9;">
                                    <td style="padding: 12px;">Diploma in Civil Engineering</td>
                                    <td style="padding: 12px;">60</td>
                                    <td style="padding: 12px;">55</td>
                                    <td style="padding: 12px;">Regular</td>
                                    <td style="padding: 12px;">3 Years</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px;">Diploma in Computer Science Engineering</td>
                                    <td style="padding: 12px;">30</td>
                                    <td style="padding: 12px;">28</td>
                                    <td style="padding: 12px;">Regular</td>
                                    <td style="padding: 12px;">3 Years</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- End of Admission Table -->

                <!-- Start of Passout Summary Table -->
                <div style="width: 100%; margin: 40px auto; padding: 20px; background-color: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <h3 style="text-align: center; margin-bottom: 20px; color: #7b1e1e;">Passout Summary (2015-2025)</h3>

                    <div style="overflow-x: auto; width: 100%;">
                        <table style="width: 100%; border-collapse: collapse; text-align: center; font-family: Arial, sans-serif;">
                            <thead>
                                <tr style="background-color: #7b1e1e; color: #fff;">
                                    <th style="padding: 14px;">Branch</th>
                                    <th style="padding: 14px;">Avg. Annual Intake</th>
                                    <th style="padding: 14px;">Estimated Total Pass-outs</th>
                                    <th style="padding: 14px;">Placement / Higher Study Rate</th>
                                    <th style="padding: 14px;">Common Career Paths</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="background-color: #f9f9f9;">
                                    <td style="padding: 12px;">Mechanical Engineering</td>
                                    <td style="padding: 12px;">115</td>
                                    <td style="padding: 12px;">1150+</td>
                                    <td style="padding: 12px;">80%</td>
                                    <td style="padding: 12px;">Manufacturing, Maintenance, Production</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px;">Electrical Engineering</td>
                                    <td style="padding: 12px;">85</td>
                                    <td style="padding: 12px;">850+</td>
                                    <td style="padding: 12px;">60%</td>
                                    <td style="padding: 12px;">Power Plants, Electrical Services, Utilities</td>
                                </tr>
                                <tr style="background-color: #f9f9f9;">
                                    <td style="padding: 12px;">Civil Engineering</td>
                                    <td style="padding: 12px;">55</td>
                                    <td style="padding: 12px;">550+</td>
                                    <td style="padding: 12px;">60%</td>
                                    <td style="padding: 12px;">Construction, Infrastructure, Site Supervision</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px;">Computer Science Engineering</td>
                                    <td style="padding: 12px;">28</td>
                                    <td style="padding: 12px;">280+</td>
                                    <td style="padding: 12px;">75%</td>
                                    <td style="padding: 12px;">IT Support, Software, Networking</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- End of Passout Summary Table -->

            </div>
        </div>
    </div>
</section>
<!-- ===== End of Admission & Passout Summary Section (HIT) ===== -->


     
    
    
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
