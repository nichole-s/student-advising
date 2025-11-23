<?php
require "db.php";

if (!isset($_GET['star_id']) || empty($_GET['star_id'])) {
    die("Missing star_id parameter.");
}

$star_id = $_GET['star_id'];

// Fetch student info with emphasis
$stmt = $conn->prepare("SELECT s.star_id, CONCAT(s.first_name, ' ', s.last_name) AS name, e.emphasis_name
                        FROM students s
                        LEFT JOIN emphasis e ON s.emphasis_id = e.emphasis_id
                        WHERE s.star_id = ?");
$stmt->execute([$star_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    die("Student not found.");
}

// Fetch recommended courses
$recStmt = $conn->prepare("SELECT c.course_code, c.course_name
                           FROM recommended r
                           JOIN courses c ON r.course_code = c.course_code
                           WHERE r.star_id = ?");
$recStmt->execute([$star_id]);
$recommendedCourses = $recStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch advising notes
$notesStmt = $conn->prepare("SELECT note_text, DATE(note_date) AS note_date FROM notes WHERE star_id = ? ORDER BY note_date DESC");
$notesStmt->execute([$star_id]);
$notes = $notesStmt->fetchAll(PDO::FETCH_ASSOC);

// Start building content
$lines = [];
$lines[] = "Advising Summary for {$student['star_id']} - {$student['name']}";
$lines[] = "Emphasis: {$student['emphasis_name']}";
$lines[] = str_repeat("=", 50);
$lines[] = "";

$lines[] = "Recommended Courses:";
if (count($recommendedCourses) === 0) {
    $lines[] = "(none)";
} else {
    foreach ($recommendedCourses as $course) {
        $lines[] = "- {$course['course_code']}: {$course['course_name']}";
    }
}

$lines[] = "";
$lines[] = "Advising Notes:";
if (count($notes) === 0) {
    $lines[] = "(none)";
} else {
    foreach ($notes as $note) {
        $lines[] = "[{$note['note_date']}] {$note['note_text']}";
    }
}

$lines[] = "";
$lines[] = str_repeat("-", 50);
$lines[] = "Generated on: " . date("Y-m-d");

// Sanitize filename
$safe_star_id = preg_replace('/[^a-zA-Z0-9_-]/', '_', $star_id);

// Send file download
header("Content-Type: text/plain");
header("Content-Disposition: attachment; filename=advising_summary_{$safe_star_id}.txt");
echo implode("\n", $lines);
exit;
?>
