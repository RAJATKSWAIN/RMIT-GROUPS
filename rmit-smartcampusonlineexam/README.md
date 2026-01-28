# 📘 Project Documentation
![Online Examination System Banner](https://github.com/Falguni35/online-examination-system/blob/main/background.jpg)

## 📌 Overview

This project is a **web-based examination and user management system** built with **PHP** and integrated with **Composer** for dependency management. It provides features for different user roles, including **students, staff, and administrators**.

---

## ✨ Features

- 🔐 **Authentication System**
  - Login, Signup, Forgot Password, and Reset Password functionality.
- 👩‍🎓 **Student Dashboard**
  - Take exams, view results.
- 👨‍🏫 **Staff Dashboard**
  - Manage exams and students.
- 🛠️ **Admin Dashboard**
  - Manage overall system operations.
- 📊 **Exam Module**
  - Take and submit exams.
  - View exam results.
- 📄 **Informational Pages**
  - About Us, Contact, FAQ, and Documentation.
- 📝 **Logging System**
  - Tracks email activity (`email_activity.log`).

---

## 📂 Project Structure

```
project/
│── aboutus.html              # About Us page
│── admindashboard.php        # Admin dashboard
│── background.jpg            # Background image
│── composer.json             # Composer dependencies
│── composer.lock             # Composer lock file
│── contact.html              # Contact page
│── documentation.html        # Documentation page
│── email_activity.log        # Log file for email activity
│── exam_result.php           # Exam result module
│── FAQ.html                  # FAQ page
│── forgotpassword.php        # Forgot password module
│── index.php                 # Homepage / landing page
│── login.php                 # Login page
│── logout.php                # Logout functionality
│── reset_password.php        # Reset password module
│── signup.php                # Signup page
│── staffdashboard.php        # Staff dashboard
│── studentdashboard.php      # Student dashboard
│── take_exam.php             # Exam taking module
│── .git/                     # Git repository data
```

---

## ⚙️ Requirements

- **Web Server:** Apache 
- **PHP:** v7.4+
- **Composer:** Dependency manager for PHP
- **Database:** MySQL / MariaDB (likely required for user & exam data)

---

## 🚀 Installation

1. Clone the repository:
   ```bash
   git clone <https://github.com/Falguni35/online-examination-system>
   cd project
   ```
2. Install dependencies via Composer:
   ```bash
   composer install
   ```
3. Configure the database in a `config.php` file (not included in repo).
4. Import the database schema (if provided).
5. Start the local server:
   ```bash
   php -S localhost:8000
   ```
6. Access the app at: [http://localhost:8000](http://localhost:8000)

---

## 🔑 User Roles

- **Admin:** Full system control (manage users, staff, exams).
- **Staff:** Manage exams and students.
- **Students:** Take exams and view results.

---

## 📜 Logging

- Email-related activities are logged in `email_activity.log`.

---

## 📌 Roadmap / To-Do

- Add **database schema** documentation.
- Improve **UI/UX** for dashboards.
- Enhance **security** (hashed passwords, prepared SQL statements).
- Add **API support** for mobile app integration.

---

## 🤝 Contributing

Contributions are welcome! Please fork the repository and submit a pull request.

---


