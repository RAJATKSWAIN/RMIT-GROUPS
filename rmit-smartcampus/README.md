# RMIT Online Examination System

![RMIT Banner](assets/images/rmitmainlogo.png)

## Overview
The RMIT Online Examination System is a secure, web-based platform built with PHP + MySQL for Rajiv Memorial Institute of Technology. It supports Admin, Faculty, and Student roles to manage exams, attendance, and fees.

## Features
- Authentication (Login, Logout, Role-based access)
- Student Portal: Take exams, view results, attendance, fees
- Faculty Portal: Create exams, mark attendance, manage students
- Admin Portal: Manage users, courses, fee plans, system settings
- Exam Module: Conduct exams, auto-submit, results
- Attendance Module: Daily tracking by faculty
- Fees Module: Dues, payments, admin reports
- Logging: email_activity.log

## Structure
See folders and files in this package; configure includes/db.php and import rmit_portal.sql.

## Install
1) composer install
2) Configure includes/db.php
3) Import rmit_portal.sql
4) Deploy to Apache/PHP host (InfinityFree compatible)
