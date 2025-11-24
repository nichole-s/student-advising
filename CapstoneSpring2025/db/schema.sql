CREATE DATABASE IF NOT EXISTS college_advising;
USE college_advising;

CREATE TABLE IF NOT EXISTS emphasis (
    emphasis_id INT AUTO_INCREMENT PRIMARY KEY,
    emphasis_name VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS students (
    star_id VARCHAR(20) PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100),
    emphasis_id INT,
    FOREIGN KEY (emphasis_id) REFERENCES emphasis(emphasis_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS courses (
    course_code VARCHAR(20) PRIMARY KEY,
    course_name VARCHAR(150) NOT NULL,
    course_credits INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS emphasis_requirements (
    emphasis_id INT NOT NULL,
    course_code VARCHAR(20) NOT NULL,
    PRIMARY KEY (emphasis_id, course_code),
    FOREIGN KEY (emphasis_id) REFERENCES emphasis(emphasis_id),
    FOREIGN KEY (course_code) REFERENCES courses(course_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS enrollments (
    enrollment_id INT AUTO_INCREMENT PRIMARY KEY,
    star_id VARCHAR(20) NOT NULL,
    course_code VARCHAR(20) NOT NULL,
    FOREIGN KEY (star_id) REFERENCES students(star_id) ON DELETE CASCADE,
    FOREIGN KEY (course_code) REFERENCES courses(course_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS recommended (
    recommendation_id INT AUTO_INCREMENT PRIMARY KEY,
    star_id VARCHAR(20) NOT NULL,
    course_code VARCHAR(20) NOT NULL,
    FOREIGN KEY (star_id) REFERENCES students(star_id) ON DELETE CASCADE,
    FOREIGN KEY (course_code) REFERENCES courses(course_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS notes (
    note_id INT AUTO_INCREMENT PRIMARY KEY,
    star_id VARCHAR(20) NOT NULL,
    note_text TEXT NOT NULL,
    note_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (star_id) REFERENCES students(star_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    password_last_changed DATE NOT NULL DEFAULT CURRENT_DATE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS password_reset (
    reset_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    reset_token VARCHAR(255) NOT NULL,
    expiration DATETIME NOT NULL,
    FOREIGN KEY (email) REFERENCES users(email) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci; 

