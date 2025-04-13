# Student_enrollment_system

-- Create the database
CREATE DATABASE IF NOT EXISTS student_system;

-- Use the database
USE student_system;

-- Create students table
CREATE TABLE students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  student_id VARCHAR(50) NOT NULL UNIQUE,
  department VARCHAR(50),
  major VARCHAR(50)
);

-- Create enrollments table
CREATE TABLE enrollments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id VARCHAR(50) NOT NULL,
  course_code VARCHAR(50) NOT NULL,
  course_title VARCHAR(100),
  semester VARCHAR(50),
  FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE
);
