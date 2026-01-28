<!--======================================================
    File Name   : index.php
    Project     : RMIT Groups
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
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

  <!-- Meta Tags - SEO Optimized -->
  <meta name="description" content="Rajiv Memorial Institute of Technology (RMIT), Berhampur — Empowering Minds, Building Futures. A premier institute committed to academic excellence, innovation, and leadership.">
    
  <!-- Multiple Meta Keywords for RMIT -->
  <meta name="keywords" content="RMIT Berhampur Odisha" />
  <meta name="keywords" content="Rajiv Memorial Institute of Technology" />
  <meta name="keywords" content="RMIT Group of Institutes" />
  <meta name="keywords" content="Engineering college in Berhampur" />
  <meta name="keywords" content="Technology institute in Odisha" />
  <meta name="keywords" content="Higher education in Berhampur" />
  <meta name="keywords" content="Best college in Ganjam Odisha" />
  <meta name="keywords" content="BCA course in Berhampur" />
  <meta name="keywords" content="Bachelor in Computer Application Odisha" />
  <meta name="keywords" content="BES course in Berhampur" />
  <meta name="keywords" content="Bachelor in Electronic Science Odisha" />
  <meta name="keywords" content="Computer Science education Berhampur" />
  <meta name="keywords" content="Berhampur University affiliated college" />
  <meta name="keywords" content="Govt of Odisha recognized institute" />
  <meta name="keywords" content="Career guidance RMIT Berhampur" />
  <meta name="keywords" content="Placement assistance RMIT Odisha" />
  <meta name="keywords" content="Internship opportunities in Berhampur" />
  <meta name="keywords" content="Scholarships for students in Odisha" />
  <meta name="keywords" content="Innovation and technology education Odisha" />
  <meta name="keywords" content="Best BCA college in Ganjam" />
  <meta name="keywords" content="Best BES college in Berhampur" />
  <meta name="keywords" content="Top technology institute Odisha" />
  <meta name="keywords" content="Professional mentorship RMIT Berhampur" />
  <meta name="keywords" content="Industry-focused education Odisha" />
  <meta name="keywords" content="Academic excellence in Berhampur" />
  <meta name="author" content="Rajiv Memorial Institute of Technology - RMIT Group of Institutes">
  <meta name="robots" content="index, follow">
  <meta name="language" content="English">

  <!-- Website Title -->
  <title>RMIT - Rajiv Memorial Institute of Technology, Berhampur | RMIT Group of Institutions | Empowering Minds, Building Futures</title>

  <!-- Favicons -->
  <link rel="icon" href="images/favicon.ico" type="image/x-icon">
  <link rel="icon" href="images/favicon_32.png" sizes="32x32" type="image/png">
  <link rel="icon" href="images/favicon_64.png" sizes="64x64" type="image/png">
  <link rel="icon" href="images/favicon_180.png" sizes="180x180" type="image/png">
  <link rel="apple-touch-icon" href="images/favicon_180.png">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,400i,700,800|Varela+Round&display=swap" rel="stylesheet">

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
    
    <!-- ===== Start Fee Payment Section ===== -->
<section class="ptb80" style="background:linear-gradient(135deg, #e0f7ff, #b3e9fc);">
    <div class="container">

        <!-- Section Heading -->
        <div class="text-center mb40">
            <h2 class="text-blue">Fee Payment</h2>
            <div style="
                width:80px;
                height:4px;
                background:#0044cc;
                margin:12px auto 0;
                border-radius:4px;">
            </div>
            <p class="mt20">
                Please take a screenshot after successful payment and submit it to the college office.
            </p>
        </div>

        <!-- Payment Details Card -->
        <div style="
            max-width:900px;
            margin:0 auto;
            background:#fff;
            border-radius:10px;
            overflow:hidden;
            box-shadow:0 10px 30px rgba(0,0,0,0.1);
        ">

            <!-- Row -->
            <div style="display:flex; border-bottom:1px solid #e5e5e5;">
                <div style="width:35%; background:#0044cc; color:#fff; padding:18px; font-weight:600;">
                    Bank Account Name
                </div>
                <div style="width:65%; padding:18px; background:#fdfefe;">
                    RMIT GROUP OF INSTITUTIONS
                </div>
            </div>

            <div style="display:flex; border-bottom:1px solid #e5e5e5;">
                <div style="width:35%; background:#0044cc; color:#fff; padding:18px; font-weight:600;">
                    Account Number
                </div>
                <div style="width:65%; padding:18px;">
                    304602000000222
                </div>
            </div>

            <div style="display:flex; border-bottom:1px solid #e5e5e5;">
                <div style="width:35%; background:#0044cc; color:#fff; padding:18px; font-weight:600;">
                    Bank Name
                </div>
                <div style="width:65%; padding:18px; background:#fdfefe;">
                    Indian Overseas Bank
                </div>
            </div>

            <div style="display:flex; border-bottom:1px solid #e5e5e5;">
                <div style="width:35%; background:#0044cc; color:#fff; padding:18px; font-weight:600;">
                    IFSC Code
                </div>
                <div style="width:65%; padding:18px;">
                    IOBA0003046
                </div>
            </div>

            <div style="display:flex;">
                <div style="width:35%; background:#0044cc; color:#fff; padding:18px; font-weight:600;">
                    Branch
                </div>
                <div style="width:65%; padding:18px; background:#fdfefe;">
                    KTN PUR
                </div>
            </div>

        </div>

        <!-- Note -->
        <div class="text-center mt30">
            <p style="color:#cc0000; font-weight:600;">
                ⚠ Do not share your payment details with anyone.
            </p>
        </div>

    </div>
</section>
<!-- ===== End Fee Payment Section ===== -->
    
    
    
    
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