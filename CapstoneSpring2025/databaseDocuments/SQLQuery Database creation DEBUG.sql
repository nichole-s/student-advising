IF NOT EXISTS(SELECT * FROM sys.databases WHERE name = 'CollegeAdvising')
BEGIN
CREATE DATABASE CollegeAdvising;

END
GO
	USE CollegeAdvising;
GO
-- 1. Emphasis Table
IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='emphasis' and xtype='U')
BEGIN
	CREATE TABLE emphasis (
		emphasis_id INT IDENTITY(1,1) PRIMARY KEY,
		emphasis_name VARCHAR(50) NOT NULL
	);
END
-- 2. Students Table
IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='students' and xtype='U')
BEGIN
	CREATE TABLE students (
		star_id VARCHAR(20) PRIMARY KEY,
		first_name VARCHAR(50) NOT NULL,
		last_name VARCHAR(50) NOT NULL,
		emphasis_id INT,
		FOREIGN KEY (emphasis_id) REFERENCES emphasis(emphasis_id)
	);
END
-- 3. Courses Table (with an optional minimum Accuplacer score)
IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='courses' and xtype='U')
BEGIN
	CREATE TABLE courses (
		course_code VARCHAR(20) PRIMARY KEY,
		course_name VARCHAR(100) NOT NULL,
		course_credits INT NOT NULL,
		min_accuplacer_score DECIMAL(5,2) DEFAULT NULL	-- Optional field
	);
END
-- 4. Emphasis_Requirements Table (Many-to-Many relationship between Emphasis and Courses)
IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='emphasis_requirements' and xtype='U')
BEGIN
	CREATE TABLE emphasis_requirements (
		emphasis_id INT NOT NULL,
		course_code VARCHAR(20) NOT NULL,
		PRIMARY KEY (emphasis_id, course_code),
		FOREIGN KEY (emphasis_id) REFERENCES emphasis(emphasis_id),
		FOREIGN KEY (course_code) REFERENCES courses(course_code)
	);
END
-- 5. Course_Offerings Table (Tracks when courses are available by semester)
IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='course_offerings' and xtype='U')
BEGIN
	CREATE TABLE course_offerings (
		offering_id INT IDENTITY(1,1) PRIMARY KEY,
		course_code VARCHAR(20) NOT NULL,
		semester VARCHAR(20) NOT NULL,
		FOREIGN KEY (course_code) REFERENCES courses(course_code)
	);
END
-- 6. Course_Schedule Table (Stores day and time details for each course offering)
IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='course_schedule' and xtype='U')
BEGIN
	CREATE TABLE course_schedule (
		schedule_id INT IDENTITY(1,1) PRIMARY KEY,
		offering_id INT NOT NULL,
		day_of_week VARCHAR(10) NOT NULL,  -- e.g., "Mon", "Tue", etc.
		start_time TIME NOT NULL,
		end_time TIME NOT NULL,
		FOREIGN KEY (offering_id) REFERENCES course_offerings(offering_id)
	);
END
-- 7. Enrollments Table (Tracks which student took which course offering and their grade)
IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='enrollments' and xtype='U')
BEGIN
	CREATE TABLE enrollments (
		enrollment_id INT IDENTITY(1,1) PRIMARY KEY,
		star_id VARCHAR(20) NOT NULL,
		offering_id INT NOT NULL,
		grade VARCHAR(5) DEFAULT NULL,  -- Optional grade field (e.g., "A", "B+", etc.)
		FOREIGN KEY (star_id) REFERENCES students(star_id),
		FOREIGN KEY (offering_id) REFERENCES course_offerings(offering_id)
	);
END
-- 8. accuplacer_results table (optional table to record accuplacer test results)
IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='accuplacer_results' and xtype='U')
BEGIN
	CREATE TABLE accuplacer_results (
		result_id INT IDENTITY(1,1) PRIMARY KEY,
		star_id VARCHAR(20) NOT NULL,
		test_date DATE NOT NULL,
		test_type VARCHAR(50) NOT NULL,  -- e.g., "Math", "Reading"
		score DECIMAL(5,2) NOT NULL,
		FOREIGN KEY (star_id) REFERENCES Students(star_id)
	);
END
-- 9. Users Table (for instructors)
IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='users' and xtype='U')
BEGIN
	CREATE TABLE users (
		user_id INT IDENTITY(1,1) PRIMARY KEY,
		first_name VARCHAR(50) NOT NULL,
		last_name VARCHAR(50) NOT NULL,
		email VARCHAR(100) NOT NULL UNIQUE,
		password_hash VARCHAR(255) NOT NULL,
		password_last_changed DATE NOT NULL DEFAULT GetDate()
	);
END

-- Report: Classes Taken by the Student
SELECT 
    s.star_id,
    s.first_name,
    s.last_name,
    c.course_code,
    c.course_name,
    c.course_credits,
    e.grade,
    co.semester
FROM Students s
JOIN enrollments e ON s.star_id = e.star_id
JOIN course_offerings co ON e.offering_id = co.offering_id
JOIN courses c ON co.course_code = c.course_code
WHERE s.star_id = 'S001';

-- Report: Remaining Required Courses Offered in the Current Semester
SELECT 
    er.emphasis_id,
    c.course_code,
    c.course_name,
    c.course_credits,
    co.semester
FROM students s
JOIN emphasis_requirements er ON s.emphasis_id = er.emphasis_id
JOIN courses c ON er.course_code = c.course_code
JOIN course_offerings co ON c.course_code = co.course_code
WHERE s.star_id = 'S001'
  AND co.semester = 'Fall 2023'
  AND c.course_code NOT LIKE '%CAPSTONE%'
  AND er.course_code NOT IN (
      SELECT c2.course_code
      FROM enrollments e
      JOIN course_offerings co2 ON e.offering_id = co2.offering_id
      JOIN courses c2 ON co2.course_code = c2.course_code
      WHERE e.star_id = 'S001'
  );

  -- Report: Capstone Readiness Indicator
SELECT 
    s.star_id,
    s.first_name,
    s.last_name,
    CASE
        WHEN (
            SELECT COUNT(*)
            FROM emphasis_requirements er
            JOIN courses c ON er.course_code = c.course_code
            WHERE er.emphasis_id = s.emphasis_id
              AND c.course_code NOT LIKE '%CAPSTONE%'
        ) = (
            SELECT COUNT(DISTINCT c2.course_code)
            FROM enrollments e
            JOIN course_offerings co ON e.offering_id = co.offering_id
            JOIN courses c2 ON co.course_code = c2.course_code
            WHERE e.star_id = s.star_id
              AND c2.course_code NOT LIKE '%CAPSTONE%'
        )
        THEN 'Ready'
        ELSE 'Not Ready'
    END AS capstone_readiness
FROM students s
WHERE s.star_id = 'S001';

-- ADDING NEW RECORDS

-- Add a New Student
INSERT INTO students (star_id, first_name, last_name)
VALUES ('S001', 'Alice', 'Johnson');

-- Add a New Course
INSERT INTO courses (course_code, course_name, course_credits, min_accuplacer_score)
VALUES ('ITEC2143', 'Introduction to Networking', 3, 70.00);

-- Add a New Enrollment Record
INSERT INTO enrollments (star_id, offering_id, grade)
VALUES ('S001', 101, 'A');

-- UPDATING RECORDS

-- Update Student Information
UPDATE students
SET first_name = 'Alice', last_name = 'Williams'
WHERE star_id = 'S001';

-- Update a Course’s Details
UPDATE courses
SET course_name = 'Intro to Network Systems', course_credits = 4
WHERE course_code = 'ITEC2143';

-- Update an Enrollment Record (e.g., update grade)
UPDATE enrollments
SET grade = 'A-'
WHERE enrollment_id = 1;  

-- DELETING RECORDS

-- Delete a Student
DELETE FROM students
WHERE star_id = 'S001';

-- Delete a Course
DELETE FROM courses
WHERE course_code = 'ITEC2143';
-- ***referential integrity constraints may require you to delete or update related rows in other tables first.

-- Delete an Enrollment
DELETE FROM enrollments
WHERE enrollment_id = 1;