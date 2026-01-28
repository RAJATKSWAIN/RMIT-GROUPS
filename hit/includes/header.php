<!-- =============== Start of Header 1 Navigation =============== -->
<header class="header1">

    <nav class="navbar navbar-default navbar-static-top fluid_header centered"
        style="
            background: linear-gradient(90deg, #5e1212 0%, #7b1e1e 45%, #a83232 100%);
            border-radius: 0;
            border: none;
        ">

        <div class="container"
            style="
                background: linear-gradient(135deg, #fbeeee, #efd1d1);
				color: #3a0f0f;
                border-radius: 20px;
                padding: 18px 30px;
            ">

            <!-- Logo -->
            <div class="col-md-2 col-sm-6 col-xs-8 nopadding me-3">
                <a class="navbar-brand" href="index.php">
                    <img src="/hit/images/hitlogo.png"
                         alt="HIT Logo"
                         loading="lazy"
                         decoding="async"
                         style="max-width:145%;">
                </a>
            </div>

            <!-- ======== Start of Main Menu ======== -->
            <div class="col-md-10 col-sm-6 col-xs-4 nopadding">

                <!-- Mobile Toggle -->
                <div class="navbar-header page-scroll">
                    <button type="button"
                            class="navbar-toggle toggle-menu menu-right push-body"
                            data-toggle="collapse"
                            data-target="#main-nav"
                            aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>

                <!-- Main Nav -->
                <div class="collapse navbar-collapse cbp-spmenu cbp-spmenu-vertical cbp-spmenu-right"
                     id="main-nav">

                    <ul class="nav navbar-nav pull-right">

                        <!-- Mobile Menu Title -->
                        <li class="mobile-title">
                            <h4>Main Menu</h4>
                        </li>

                        <!-- Home -->
                        <li class="dropdown simple-menu active">
                            <a href="index.php">  <i class="fa fa-home"></i> Home </a>
                        </li>

                        <!-- About -->
                        <li class="dropdown simple-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                About <i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="aboutus.php">About Us</a></li>
                                <li><a href="Mission&Vision.php">Mission & Vision</a></li>
                                <li><a href="Infrastructure.php">Infrastructure</a></li>
                                <li><a href="facilities.php">Facilities</a></li>
                                <li><a href="Affiliation.php">Affiliation</a></li>
                                <li><a href="contact.php">Contact Us</a></li>
                                <li><a href="message.php">Message</a></li>
                                <li><a href="Gallery.php">Gallery</a></li>
                                <li><a href="Documents.php">Documents</a></li>
                            </ul>
                        </li>

                        <!-- Branch -->
                        <li class="dropdown simple-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                Courses Offered <i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="MechanicalEngg.php">Diploma in Mechanical Engg</a></li>
                                <li><a href="ElectricalEngg.php">Diploma in Electrical Engg</a></li>
                                <li><a href="CivilEngg.php">Diploma in Civil Engg</a></li>
                                <li><a href="ComputerScienceEngg.php">Diploma in Computer Science Engg</a></li>
                            </ul>
                        </li>

                        <!-- Administration -->
                        <li class="dropdown simple-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                Administration <i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="Admistration.php">Administration</a></li>
                                <li><a href="Staffinformation.php">Staff Information</a></li>
                                <li><a href="RighttoInformation.php">Right to Information</a></li>
                                <li><a href="insurance.php">Insurance</a></li>
                            </ul>
                        </li>

                        <!-- Admission -->
                        <li class="dropdown simple-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                Admission <i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="EnquireDownloadBrochure.php">Enquire & Download Brochure</a></li>
                                <li><a href="DownloadAdmissionForm.php">Download Admission Form</a></li>
                                <li><a href="admissionlist.php">Admission List</a></li>
                            </ul>
                        </li>

                        <!-- Placements -->
                        <li class="dropdown simple-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                Placements <i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="recrutingcompanies.php">Recruiting Companies</a></li>
                                <li><a href="recruittedstudents.php">Recruited Students</a></li>
                                <li><a href="Questions.php">Questions</a></li>
                                <li><a href="Career.php">Career</a></li>
                            </ul>
                        </li>

                        <!-- Student -->
                        <!--<li class="dropdown simple-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                Student <i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="studentcorner.php?pagetype=TimeTable">Time Table</a></li>
                                <li><a href="studentcorner.php?pagetype=Syllabus">Syllabus</a></li>
                                <li><a href="studentcorner.php?pagetype=Teaching Note">Teachers Note</a></li>
                                <li><a href="studentcorner.php?pagetype=LessonPlan">Lesson Plan</a></li>
                            </ul>
                        </li>-->

                        <!-- Notice -->
                        <li class="menu-item login-btn">
                            <a href="notice.php">
                                <i class="fa fa-file"></i> Notice
                            </a>
                        </li>

                    </ul>
                </div>
                <!-- End Main Nav -->

            </div>
            <!-- ======== End of Main Menu ======== -->

        </div>
    </nav>
</header>
<!-- =============== End of Header 1 Navigation =============== -->

<!-- ====== HIT Header CSS ====== -->
<style>
    .navbar-brand {
        margin-right: 10px;
    }

    .navbar-brand img {
        height: 32px;
        vertical-align: middle;
    }

    /* Default menu text */
    .navbar-nav > li > a {
        line-height: 15px;
        padding-top: 0;
        padding-bottom: 0;
        color: #800000 !important; /* maroon */
        font-weight: 600;
        transition: all 0.3s ease;
    }

    /* Hover effect */
    .navbar-nav > li > a:hover {
        background: #7b1e1e !important;
        color: #ffffff !important;
        border-radius: 6px;
    }

    /* Active item default (keep maroon, no background) */
    .navbar-nav > li.active > a {
        background: transparent !important;
        color: #800000 !important;
    }

    /* Active item hover */
    .navbar-nav > li.active > a:hover {
        background: #7b1e1e !important;
        color: #ffffff !important;
    }

    .dropdown-menu {
        background: #fff5f5;
        border: 1px solid #e2bcbc;
    }

    .dropdown-menu > li > a {
        color: #800000;
        font-weight: 500;
    }

    .dropdown-menu > li > a:hover {
        background: #7b1e1e;
        color: #ffffff;
    }
</style>
<!-- ====== End HIT Header CSS ====== -->
