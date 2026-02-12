<!--======================================================
    File Name   : aboutus.php
    Project     : RMIT Groups - HIT
    Description : About Us Page 
    Developed By: TrinityWebEdge
    Date Created: 17-12-2025
    Last Updated: <?php echo date("d-m-Y"); ?>
    Note         : This page defines the NOTICE page of RMIT Groups website.
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

    <!-- CSS Files -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/swiper.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">
              
    <link rel="preconnect" href="https://chatling.ai">

    <!-- IE8 Support -->
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
                    <h2>Notice</h2>
                </div>
            </div>
            <!-- End of Page Title -->

            <!-- Start of Breadcrumb -->
            <div class="row">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li><a href="/hit/index.php">home</a></li>
                        <li class="active">Notice</li>
                    </ul>
                </div>
            </div>
            <!-- End of Breadcrumb -->

        </div>
    </section>
    <!-- =============== End of Page Header 1 Section =============== -->
    
        <!-- ===== Start of Main Wrapper Section ===== -->
        <!-- ======= Notice Board Enhanced Style Start ======= -->
        <style>
            .notice-board {
                background: #ffffff;
                border: 1px solid #e3e6eb;
                width: 85%;
                margin: 0 auto 50px;
                box-shadow: 0 8px 18px rgba(0,0,0,0.08);
                border-radius: 10px;
                overflow: hidden;
                transition: all 0.3s ease;
            }

            .notice-board:hover {
                box-shadow: 0 10px 22px rgba(0,0,0,0.12);
            }

            .notice-header {
                background: linear-gradient(90deg, #5e1212 0%, #7b1e1e 45%, #a83232 100%);
                color: #fff;
                padding: 16px 24px;
                font-size: 22px;
                font-weight: 600;
                display: flex;
                align-items: center;
                gap: 10px;
                border-top-left-radius: 10px;
                border-top-right-radius: 10px;
            }

            .notice-header i {
                font-size: 22px;
            }

            .notice-table {
                width: 100%;
                border-collapse: collapse;
            }

            .notice-table th {
                background: #f3f6fb;
                color: #a50f2d;
                text-align: left;
                padding: 14px 18px;
                font-size: 16px;
                font-weight: 600;
                border-bottom: 2px solid #dcdfe3;
            }

            .notice-table td {
                padding: 14px 18px;
                border-bottom: 1px solid #f0f0f0;
                background: #fff;
                vertical-align: middle;
                transition: background 0.3s ease;
            }

            .notice-table tr:hover td {
                background: #f9fbff;
            }

            .notice-title {
                font-size: 16px;
                color: #212529;
                font-weight: 500;
            }

            .notice-date {
                display: inline-block;
                background: #c71e32;
                color: white;
                text-align: center;
                font-weight: bold;
                padding: 8px 6px;
                border-radius: 6px;
                width: 50px;
                line-height: 1.1;
                box-shadow: 0 2px 6px rgba(0,0,0,0.15);
            }

            .notice-date small {
                display: block;
                font-weight: normal;
                font-size: 11px;
                color: #ffdadf;
            }

            .notice-info {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 15px;
            }

            .notice-datetime {
                color: #555;
                font-size: 15px;
            }

            .notice-pdf {
                display: inline-block;
                text-decoration: none;
                transition: transform 0.2s ease;
            }

            .notice-pdf img {
                width: 26px;
                vertical-align: middle;
            }

            .notice-pdf:hover {
                transform: scale(1.15);
            }

            .view-all {
                text-align: right;
                padding: 14px 20px;
                font-weight: 600;
                background: #f9f9f9;
            }

            .view-all a {
                color: #007bff;
                text-decoration: none;
            }

            .view-all a:hover {
                text-decoration: underline;
            }
        </style>
        <!-- ======= Notice Board Enhanced Style End ======= -->
        <section class="search-jobs ptb80" id="version4">
            <div class="container">
                <div class="notice-board">
                    <div class="notice-header">
                        <i class="fa fa-bullhorn"></i>
                        🎓 Institute Notice Board – Latest Announcements & Circulars 
                    </div>
                    <table class="notice-table">
                        <thead>
                            <tr>
                                <th width="60%">Notice Board</th>
                                <th>News & Events</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="notice-title">IMPORTANT ANNOUNCEMENT REGARDING CYCLONE MONTHA</div>
                                </td>
                                <td>
                                    <div class="notice-info">
                                        <div style="display:flex;align-items:center;gap:12px;">
                                            <div class="notice-date">
                                                27<small>Oct 25</small>
                                            </div>
                                            <div class="notice-datetime">27 Oct 2025, 10:10 PM</div>
                                        </div>
                                        <a href="notices/IMPORTANT ANNOUNCEMENT REGARDING CYCLONE MONTHA.pdf" class="notice-pdf" target="_blank" title="Download PDF">
                                            <img src="https://cdn-icons-png.flaticon.com/512/337/337946.png" alt="PDF">
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="notice-title">LAST MONDAY OF ODIA KARTIKA</div>
                                </td>
                                <td>
                                    <div class="notice-info">
                                        <div style="display:flex;align-items:center;gap:12px;">
                                            <div class="notice-date">
                                                05<small>Nov 25</small>
                                            </div>
                                            <div class="notice-datetime">05 Nov 2025, 01:20 PM</div>
                                        </div>
                                        <a href="notices/LAST MONDAY OF ODIA KARTIKA.pdf" class="notice-pdf" target="_blank" title="Download PDF">
                                            <img src="https://cdn-icons-png.flaticon.com/512/337/337946.png" alt="PDF">
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="notice-title">ONE TIME REGISTRATION (OTR) IN NATIONAL SCHOLARSHIP PORTAL (NSP)</div>
                                </td>
                                <td>
                                    <div class="notice-info">
                                        <div style="display:flex;align-items:center;gap:12px;">
                                            <div class="notice-date">
                                                27<small>Oct 25</small>
                                            </div>
                                            <div class="notice-datetime">27 Oct 2025, 11:33 PM</div>
                                        </div>
                                        <a href="notices/ONE TIME REGISTRATION (OTR) IN NATIONAL SCHOLARSHIP PORTAL (NSP).pdf" class="notice-pdf" target="_blank" title="Download PDF">
                                            <img src="https://cdn-icons-png.flaticon.com/512/337/337946.png" alt="PDF">
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="view-all">
                        <a href="#">View All ...</a>
                    </div>
                </div>
            </div>
        </section>
        <!-- ===== End of Main Wrapper Section ===== -->
    
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
