"""
Filename: caCreate.py
Author: Austin Richardson
Creating date:  3/15/2025
"""

#   Import libraries
import mariadb
import sys

# Connect to MariaDB Platform
try:
   conn = mariadb.connect(
      host="localhost",
      port=3307,
      user="root",
      password="")
except mariadb.Error as e:
   print(f"Error connecting to the database: {e}")
   sys.exit(1)

#   Get Cursor
cur = conn.cursor()


try:
    #   Create and select the database
    cur.execute("CREATE DATABASE IF NOT EXISTS college_advising;")
    cur.execute("USE college_advising;")
    
    #   Create the Emphasis table
    cur.execute(
        "CREATE TABLE IF NOT EXISTS Emphasis (" +
            "emphasis_id INT AUTO_INCREMENT PRIMARY KEY," +
            "emphasis_name VARCHAR(50) NOT NULL" +
        ");"
    )
    
    #   Create the Student table
    cur.execute(
        "CREATE TABLE IF NOT EXISTS Students (" +
            "student_email VARCHAR(254) NOT NULL,"+
            "first_name VARCHAR(50) NOT NULL," +
            "last_name VARCHAR(50) NOT NULL," +
            "star_id VARCHAR(20) PRIMARY KEY," +
            "notes VARCHAR(254)," +
            "emphasis_id INT," +
            "FOREIGN KEY (emphasis_id) REFERENCES Emphasis(emphasis_id)" +
        ");"
    )
    
    #   Create the Courses table
    cur.execute(
        "CREATE TABLE IF NOT EXISTS Courses (" +
            "course_code VARCHAR(20) PRIMARY KEY, " +
            "course_name VARCHAR(100) NOT NULL, " +
            "course_credits INT NOT NULL, " +
            "min_accuplacer_score DECIMAL(5,2) DEFAULT NULL" +  # Optional field
        ");"
    )
    
    #   Create the Emphasis_Requirements table
    cur.execute(
        "CREATE TABLE IF NOT EXISTS Emphasis_Requirements (" +
            "emphasis_id INT NOT NULL, " +
            "course_code VARCHAR(20) NOT NULL, " +
            "PRIMARY KEY (emphasis_id, course_code), " +
            "FOREIGN KEY (emphasis_id) REFERENCES Emphasis(emphasis_id), " +
            "FOREIGN KEY(course_code) REFERENCES Courses(course_code)" +
        ");"
    )
    
    #   Create the Course_Offerings table
    cur.execute(
        "CREATE TABLE IF NOT EXISTS Course_Offerings (" +
            "offering_id INT AUTO_INCREMENT PRIMARY KEY, " +
            "course_code VARCHAR(20) NOT NULL, " +
            "semester VARCHAR(20) NOT NULL, " +
            "FOREIGN KEY (course_code) REFERENCES Courses(course_code)" +
        ");"
    )
    
    #   Create the Course_Schedule table
    cur.execute(
        "CREATE TABLE IF NOT EXISTS Course_Schedule (" +
            "schedule_id INT AUTO_INCREMENT PRIMARY KEY, " +
            "offering_id INT NOT NULL, " +
            "day_of_week VARCHAR(10) NOT NULL, " +  #e.g., 'Mon', 'Tue', etc.
            "start_time TIME NOT NULL, " +
            "end_time TIME NOT NULL, " +
            "FOREIGN KEY (offering_id) REFERENCES Course_Offerings(offering_id)" +
        ");"
    )
    
    #   Create the Enrollments table
    cur.execute(
        "CREATE TABLE IF NOT EXISTS Enrollments (" +
            "enrollment_id INT AUTO_INCREMENT PRIMARY KEY, " +
            "star_id VARCHAR(20) NOT NULL, " +
            "offering_id INT NOT NULL, " +
            "grade VARCHAR(5) DEFAULT NULL, " +     #Optional grade field (e.g., 'A', 'B+', etc.)
            "FOREIGN KEY (star_id) REFERENCES Students(star_id), " +
            "FOREIGN KEY (offering_id) REFERENCES Course_Offerings(offering_id) " +
        ");"
    )
    
    #   Create the Accuplacer_Results table
    cur.execute(
        "CREATE TABLE IF NOT EXISTS Accuplacer_Results (" +
            "result_id INT AUTO_INCREMENT PRIMARY KEY, " +
            "star_id VARCHAR(20) NOT NULL, " +
            "test_date DATE NOT NULL, " +
            "test_type VARCHAR(50) NOT NULL, " +    #e.g., 'Math', 'Reading'
            "score DECIMAL(5,2) NOT NULL, " +
            "FOREIGN KEY (star_id) REFERENCES Students(star_id)" +
        ");"
    )
    
    #   Create the Users table (for instructors)
    cur.execute(
        "CREATE TABLE IF NOT EXISTS Users (" +
            "user_id INT AUTO_INCREMENT PRIMARY KEY, " +
            "first_name VARCHAR(50) NOT NULL, " +
            "last_name VARCHAR(50) NOT NULL, " +
            "email VARCHAR(100) NOT NULL UNIQUE, " +
            "password_hash VARCHAR(255) NOT NULL, " +
            "password_last_changed DATE NOT NULL DEFAULT CURRENT_DATE" +
        ");"
    )
    #   Create the Password_Reset table
    cur.execute(
        "CREATE TABLE IF NOT EXISTS Password_Reset (" +
            "reset_id INT AUTO_INCREMENT PRIMARY KEY, " +
            "email VARCHAR(100) NOT NULL, " +
            "reset_token VARCHAR(255) NOT NULL, " +
            "expiration DATETIME NOT NULL, " +
            "FOREIGN KEY (email) REFERENCES Users(email) ON DELETE CASCADE" +
        ");"
    )
    
    
    # cur.execute(
    #     "DROP DATABASE college_advising;"
    # )
    
    
except mariadb.Error as e:
    print(f"Error during database creation: {e}")
    sys.exit(1)
    
# Close Connection
cur.close()
conn.close()