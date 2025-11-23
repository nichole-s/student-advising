<?php
require 'db.php';

/* Function to fetch courses already taken
function getCoursesTaken($starId) {
    global $conn;  
    $sql = "SELECT c.* FROM Courses c
            JOIN Enrollments e ON c.course_id = e.course_id
            WHERE e.star_id = :starId AND e.status = 'taken'";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['starId' => $starId]);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $courses;
}

// Function to fetch courses wanted
function getCoursesWanted($starId) {
    global $conn;
    $sql = "SELECT c.* FROM Courses c
            JOIN Enrollments e ON c.course_id = e.course_id
            WHERE e.star_id = :starId AND e.status = 'wanted'";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['starId' => $starId]);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $courses;
}

// Example student ID
$starId = 1;
try {
    $coursesTaken = getCoursesTaken($starId);
    $coursesWanted = getCoursesWanted($starId);
} catch (PDOException $e) {
    die("Error accessing database: " . $e->getMessage());
}
?>*/


    <h1>Student Advising</h1>
    <div class="courses-container">
        <div class="taken-courses">
            <h2>Courses Taken</h2>
            <ul id="takenList" class="course-list">
                <?php foreach ($coursesTaken as $course): ?>
                    <li draggable="true" class="draggable"><?= htmlspecialchars($course['course_name']) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="wanted-courses">
            <h2>Courses Wanted</h2>
            <ul id="wantedList" class="course-list">
                <?php foreach ($coursesWanted as $course): ?>
                    <li draggable="true" class="draggable"><?= htmlspecialchars($course['course_name']) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <script src="script.js"></script>
?>