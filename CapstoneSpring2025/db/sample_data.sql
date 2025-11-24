USE college_advising;

INSERT INTO courses (course_code, course_name, course_credits) VALUES
('ENGL 2105', 'Business and Technical Writing', 4),
('MATH 1550', 'Introduction to Statistics', 4),
('PHIL 1200', 'Technology, Ethics and Society', 3),
('BDAT 1005', 'Data Analysis Fundamentals', 2),
('ITEC 1003', 'Networking Fundamentals', 2),
('ITEC 1006', 'Technology Fundamentals', 3),
('ITEC 1011', 'Programming Logic and Design', 4),
('ITEC 1016', 'Web Development Technologies', 4),
('BDAT 1025', 'Data Preparation for Analytics', 3),
('BDAT 2140', 'Business Intelligence', 3),
('ITEC 2120', 'Database Design and SQL', 4),
('BDAT 1010', 'Integrated Business Software', 3),
('BDAT 1040', 'Data Visualization', 5),
('ITEC 2700', 'Artificial Intelligence', 4),
('ITEC 1025', 'Project Management', 4),
('ITEC 2901', 'Integrated Capstone Project', 4),
('ITEC 2207', 'Windows Server Administration', 4),
('ITEC 2407', 'Internetworking Devices I', 4),
('ITEC 2450', 'Ethical Hacking', 4),
('ITEC 2215', 'Linux/Web Server Administration', 4),
('ITEC 2411', 'Networking Scripting', 2),
('ITEC 2440', 'IDS/IPS and Auditing', 4),
('ITEC 2600', 'Application Development', 4),
('ITEC 2105', 'JAVA Programming', 4),
('ITEC 2340', 'Scripting Languages', 4),
('ITEC 1035', 'Documentation Standards', 2),
('ITEC 2311', 'User Experience and Interface Design', 4),
('ITEC 2317', 'Web Interactivity Tools', 4),
('ITEC 2520', 'Mobile Application Development', 4),
('COMM 1055', 'Strengths & Wellness (Goal 1)', 3),
('ENGL 1107', 'Composition I (Goal 1)', 4),
('ENGL 1110', 'Research Project (Goal 1)', 1),
('SPCH 1120', 'Public Speaking (Goal 1)', 3),
('SPCH 1200', 'Interpersonal Communication (Goal 1)', 3),
('SPCH 1500', 'Intercultural Communication (Goal 1)', 3),
('BIOL 1106', 'Principles of Biology (Goal 2)', 4),
('BIOL 1130', 'Human Biology (Goal 2)', 4),
('BIOL 1200', 'Anatomy & Physiology I (Goal 2)', 4),
('BIOL 2106', 'Microbiology (Goal 2)', 4),
('BIOL 2200', 'Anatomy & Physiology II (Goal 2)', 4),
('ENGL 1150', 'Multicultural Literature (Goal 2)', 4),
('INTS 1000', 'Critical Thinking Applications for College (Goal 2)', 3),
('INTS 1010', 'College and Career Success (Goal 2)', 1),
('NSCI 1020', 'Plant Science (Goal 3)', 3),
('NSIC 1030', 'Introduction to Environmental Science (Goal 3)', 3),
('MATH 1500', 'Mathematical Ideas (Goal 4)', 3),
('MATH 1600', 'College Algebra (Goal 4)', 4),
('MATH 1650', 'College Trigonometry (Goal 4)', 3),
('MATH 1700', 'Pre-Calculus (Goal 4)', 5),
('PSYC 1406', 'General Psychology (Goal 5)', 4),
('PSYC 1506', 'Lifespan Development (Goal 5)', 4),
('PSYC 1605', 'Abnormal Psychology (Goal 5)', 4),
('SOSC 1010', 'Introduction to Sociology (Goal 5)', 3),
('SOSC 2000', 'Sociology of Work (Goal 5)', 4),
('ENGL 2110', 'Literature and the Environment (Goal 6, 10)', 4),
('ASL 1000', 'ASL Deaf Studies/Culture (Goal 7)', 4),
('ASL 1100', 'American Sign Language I (Goal 8)', 3);

INSERT INTO emphasis (emphasis_id, emphasis_name) VALUES
(1,'Web'),
(2,'Software'),
(3,'Networking'),
(4, 'Data Analysis'),
(5, 'Undecided');

INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (1, 'ENGL 2105');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (1, 'PHIL 1200');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (1, 'BDAT 1005');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (1, 'ITEC 1003');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (1, 'ITEC 1006');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (1, 'ITEC 1011');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (1, 'ITEC 1016');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (1, 'ITEC 2120');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (1, 'ITEC 2311');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (1, 'ITEC 2317');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (1, 'ITEC 2340');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (1, 'ITEC 2520');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (1, 'ITEC 1025');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (1, 'ITEC 1035');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (1, 'ITEC 2901');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (2, 'ENGL 2105');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (2, 'PHIL 1200');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (2, 'BDAT 1005');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (2, 'ITEC 1003');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (2, 'ITEC 1006');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (2, 'ITEC 1011');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (2, 'ITEC 1016');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (2, 'ITEC 2120');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (2, 'ITEC 2600');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (2, 'ITEC 2105');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (2, 'ITEC 2340');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (2, 'ITEC 2700');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (2, 'ITEC 1025');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (2, 'ITEC 1035');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (2, 'ITEC 2901');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (3, 'ENGL 2105');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (3, 'PHIL 1200');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (3, 'BDAT 1005');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (3, 'ITEC 1003');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (3, 'ITEC 1006');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (3, 'ITEC 1011');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (3, 'ITEC 1016');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (3, 'ITEC 2207');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (3, 'ITEC 2407');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (3, 'ITEC 2450');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (3, 'ITEC 2215');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (3, 'ITEC 2411');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (3, 'ITEC 2440');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (3, 'ITEC 1025');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (3, 'ITEC 2901');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (4, 'ENGL 2105');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (4, 'MATH 1550');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (4, 'PHIL 1200');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (4, 'BDAT 1005');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (4, 'ITEC 1003');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (4, 'ITEC 1006');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (4, 'ITEC 1011');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (4, 'ITEC 1016');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (4, 'BDAT 1025');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (4, 'BDAT 2140');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (4, 'ITEC 2120');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (4, 'BDAT 1010');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (4, 'BDAT 1040');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (4, 'ITEC 2700');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (4, 'ITEC 1025');
INSERT INTO emphasis_requirements (emphasis_id, course_code) VALUES (4, 'ITEC 2901');


INSERT INTO users (first_name, last_name, email, password_hash, password_last_changed) VALUES
('Test', 'Admin', 'admin@example.com', '$2y$10$0exSYZ1U4AwzbTrIm1lBO.mypAW1nTayEy4GuVU1ly/vUek4FEM9C', CURRENT_DATE);

INSERT INTO users (first_name, last_name, email, password_hash, password_last_changed) VALUES
('Expired', 'Expired', 'expired@example.com', '$2y$10$0exSYZ1U4AwzbTrIm1lBO.mypAW1nTayEy4GuVU1ly/vUek4FEM9C', CURDATE() - INTERVAL 120 DAY);