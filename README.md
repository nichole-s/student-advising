# Student Advising & Academic Planning System

A full-stack web application designed to support academic advisors in managing students, courses, emphasis requirements, and personalized academic planning. Built as part of my Capstone project for the Information Technology Management program, this system streamlines advising workflows with tools such as drag-and-drop course planning, CSV imports, and modular CRUD management.

---

## Features

### Student Management
- Add, edit, delete, and search student records  
- Dynamic table with pagination, filtering, and live search  
- CSV bulk upload using a Python script  
- Automatic UI refresh after uploads and changes  

### Advising Students
- Search for a student and load their advising dashboard  
- Three-column interface:
  - **Taken** courses  
  - **Recommended** courses  
  - **Required** courses (auto-generated based on emphasis)  
- Fully interactive drag-and-drop movement between columns  
- Promote courses from Recommended → Taken  
- Emphasis dropdown that updates required courses dynamically  
- Advising notes section to view and add notes  

### User Management & Security
- User management (add, edit, deactivate users)  
- Login/logout with session handling  
- Password expiration system requiring password updates after a set period  

---

## 🛠 Tech Stack

**Frontend**  
- HTML, CSS, JavaScript  
- Drag-and-drop DOM interactions  
- Dynamic tables with AJAX/fetch for search, sort, and update  

**Backend**  
- PHP  
- REST-like endpoints returning JSON  
- Server-side validation  
- Shared includes for database connection and session control  

**Database**  
- MariaDB / MySQL  
- InnoDB tables with foreign keys and cascades  
- Example tables:
  - `students`  
  - `courses`  
  - `enrollments`  
  - `emphasis`  
  - `emphasis_requirements`  
  - `users`  
  - `notes`  

**Automation**  
- Python script for CSV student import  
- Text parsing and data cleanup before inserting into the database  

**Environment**  
- WAMP/LAMP local environment  
- Adaptable to XAMPP or standalone Apache/PHP setups  

---

## Architecture Overview

Frontend (HTML/CSS/JS)  
→ AJAX/fetch calls (JSON)  
→ Backend (PHP controllers / endpoints)  
→ MariaDB database

- Reusable JavaScript modules handle table rendering, search, modals, and drag-and-drop behavior  
- PHP endpoints return clean JSON responses for consistent front-end integration  
- Database schema is normalized for accurate emphasis requirements and course tracking  
- Enrollment logic is simplified to `star_id + course_code` for clarity and reliability  

---

## Key Technical Challenges & Solutions

**JSON parse errors**  
- Cause: Extra whitespace or stray output around `json_encode`.  
- Fix: Ensured endpoints only output JSON and set appropriate headers.

**Modal and selector issues**  
- Cause: Inconsistent IDs/classes between pages.  
- Fix: Standardized naming conventions and ensured DOM elements exist before attaching listeners.

**Optional fields causing SQL errors**  
- Allowed `NULL` for optional fields like `min_accuplacer_score`.  
- Added conditional binding logic to avoid inserting empty strings where numeric values are expected.

**Simplifying course offerings and schedule**  
- Removed older `course_offerings` and `course_schedule` tables.  
- Consolidated taken courses into a simplified `enrollments` table.

---

## Getting Started

1. Clone the repository:

    git clone https://github.com/nichole-s/student-advising  
    cd student-advising

2. Import the database:

    - Import `db/schema.sql` into your MariaDB/MySQL server.  
    - (Optional) Import `db/sample_data.sql` for test data.

### 3. Configure database connection:

- Open the file that manages DB connection (for example: `config.php` or wherever `mysqli`/PDO is initialized)
- Update:

  - `$host`
  - `$dbname`
  - `$username`
  - `$password`

to match your environment.

4. Run locally:

    - Place the project folder in your web root (for example, `htdocs` or `www`).  
    - Navigate to:

      http://localhost/student-advising

---

## My Role

I served as the primary full-stack developer, responsible for:

- Database schema design and relationships  
- PHP endpoint structure and JSON responses  
- Front-end JavaScript for tables, search, and drag-and-drop interactions  
- Course form logic, asynchronous course handling, and validation  
- User management and password expiration logic  
- CSV upload integration using Python  
- Debugging and refactoring across the application  

---

## Future Improvements

- Migrate to a fully RESTful API  
- Rebuild the UI with a modern framework (React/Vue)  
- Add more granular role-based authorization  
- Improve mobile responsiveness  
- Add automated tests and CI integration  

---

## License

This project was created for educational and portfolio demonstration purposes.

