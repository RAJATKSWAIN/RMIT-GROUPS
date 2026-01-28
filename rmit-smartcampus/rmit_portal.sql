CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  institute_code VARCHAR(10) NOT NULL,
  role ENUM('student','staff','admin') NOT NULL,
  email VARCHAR(150) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  full_name VARCHAR(150) NOT NULL,
  status ENUM('active','inactive') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  roll_no VARCHAR(20),
  program VARCHAR(100),
  semester INT,
  section VARCHAR(10),
  phone VARCHAR(20),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


CREATE TABLE staff (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  department VARCHAR(100),
  phone VARCHAR(20),
  gender VARCHAR(10),
  dob DATE,
  address TEXT,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


CREATE TABLE institutes (
  code VARCHAR(10) PRIMARY KEY,
  name VARCHAR(100)
);

INSERT INTO institutes VALUES
('HIT','Holy Institute of Technology'),
('RMIT','Rajiv Memorial Institute of Technology'),
('RMITC','Industrial Training Center'),
('CPS','Chirag Public School');

CREATE TABLE subjects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  program VARCHAR(100),
  semester INT,
  subject_code VARCHAR(20),
  subject_name VARCHAR(100)
);

CREATE TABLE exams (
  id INT AUTO_INCREMENT PRIMARY KEY,
  subject_id INT,
  exam_name VARCHAR(50),
  max_marks INT,
  exam_date DATE,
  FOREIGN KEY (subject_id) REFERENCES subjects(id)
);

CREATE TABLE student_results (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT,
  exam_id INT,
  marks INT,
  grade VARCHAR(5),
  FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
  FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE
);


CREATE TABLE student_attendance (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT,
  subject_id INT,
  attendance_percent DECIMAL(5,2),
  FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
  FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE
);

CREATE TABLE fee_structure (
  id INT AUTO_INCREMENT PRIMARY KEY,
  program VARCHAR(100),
  semester INT,
  total_fee DECIMAL(10,2)
);

CREATE TABLE student_fees (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT,
  total_fee DECIMAL(10,2),
  paid_amount DECIMAL(10,2),
  due_amount DECIMAL(10,2),
  FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

CREATE TABLE payments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT,
  amount DECIMAL(10,2),
  payment_date DATE,
  mode ENUM('cash','upi','card','netbanking'),
  reference_no VARCHAR(50),
  FOREIGN KEY (student_id) REFERENCES students(id)
);

CREATE TABLE student_activity (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT,
  activity VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);



CREATE TABLE staff_classes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  staff_id INT,
  class_name VARCHAR(50),
  program VARCHAR(100),
  semester INT,
  FOREIGN KEY (staff_id) REFERENCES staff(id)
);

CREATE TABLE class_students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  class_id INT,
  student_id INT,
  FOREIGN KEY (class_id) REFERENCES staff_classes(id),
  FOREIGN KEY (student_id) REFERENCES students(id)
);

CREATE TABLE staff_activity (
  id INT AUTO_INCREMENT PRIMARY KEY,
  staff_id INT,
  activity VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE staff_courses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  staff_id INT,
  course_name VARCHAR(100),
  program VARCHAR(100),
  semester INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (staff_id) REFERENCES staff(id)
);

CREATE TABLE assignments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  course_id INT,
  title VARCHAR(150),
  due_date DATE,
  description TEXT,
  FOREIGN KEY (course_id) REFERENCES staff_courses(id)
);

CREATE TABLE staff_tasks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  staff_id INT NOT NULL,
  title VARCHAR(150) NOT NULL,
  type ENUM('assignment','attendance','result','other') DEFAULT 'other',
  status ENUM('pending','completed') DEFAULT 'pending',
  due_date DATE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE CASCADE
);


