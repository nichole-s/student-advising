<?php
require "db.php";

header("Content-Type: application/json");

$q = isset($_GET["q"]) ? trim($_GET["q"]) : "";

if (strlen($q) < 2) {
    echo json_encode([]);
    exit;
}

// Updated: Include emphasis name using LEFT JOIN
$searchQuery = "SELECT s.star_id, CONCAT(s.first_name, ' ', s.last_name) AS name, 
                       s.emphasis_id, e.emphasis_name
                FROM students s
                LEFT JOIN emphasis e ON s.emphasis_id = e.emphasis_id
                WHERE s.star_id LIKE :term 
                   OR s.first_name LIKE :term 
                   OR s.last_name LIKE :term";

$stmt = $conn->prepare($searchQuery);
$searchTerm = "%" . $q . "%";
$stmt->bindValue(":term", $searchTerm, PDO::PARAM_STR);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

$students = [];

foreach ($results as $row) {
    $star_id = $row["star_id"];
    $emphasis_id = $row["emphasis_id"];

    // TAKEN COURSES
    $takenQuery = "SELECT c.course_code, c.course_name
                   FROM enrollments e
                   JOIN courses c ON e.course_code = c.course_code
                   WHERE e.star_id = :star_id";
    $takenStmt = $conn->prepare($takenQuery);
    $takenStmt->bindValue(":star_id", $star_id, PDO::PARAM_STR);
    $takenStmt->execute();
    $takenCourses = $takenStmt->fetchAll(PDO::FETCH_ASSOC);

    // RECOMMENDED COURSES
    $recommendedQuery = "SELECT c.course_code, c.course_name
                         FROM recommended r
                         JOIN courses c ON r.course_code = c.course_code
                         WHERE r.star_id = :star_id";
    $recommendedStmt = $conn->prepare($recommendedQuery);
    $recommendedStmt->bindValue(":star_id", $star_id, PDO::PARAM_STR);
    $recommendedStmt->execute();
    $recommendedCourses = $recommendedStmt->fetchAll(PDO::FETCH_ASSOC);

    // REQUIRED COURSES
    $requiredQuery = "SELECT c.course_code, c.course_name 
                      FROM emphasis_requirements er
                      JOIN courses c ON er.course_code = c.course_code
                      WHERE er.emphasis_id = :emphasis_id
                        AND c.course_code NOT IN (
                            SELECT course_code FROM enrollments WHERE star_id = :star_id_1
                            UNION
                            SELECT course_code FROM recommended WHERE star_id = :star_id_2
                      )";
    $requiredStmt = $conn->prepare($requiredQuery);
    $requiredStmt->bindValue(":emphasis_id", $emphasis_id, PDO::PARAM_INT);
    $requiredStmt->bindValue(":star_id_1", $star_id, PDO::PARAM_STR);
    $requiredStmt->bindValue(":star_id_2", $star_id, PDO::PARAM_STR);
    $requiredStmt->execute();
    $requiredCourses = $requiredStmt->fetchAll(PDO::FETCH_ASSOC);

    // ELECTIVES (not required, taken, or recommended)
    $electivesQuery = "SELECT c.course_code, c.course_name
                       FROM courses c
                       WHERE c.course_code NOT IN (
                           SELECT course_code FROM enrollments WHERE star_id = :star_id_1
                           UNION
                           SELECT course_code FROM recommended WHERE star_id = :star_id_2
                           UNION
                           SELECT course_code FROM emphasis_requirements WHERE emphasis_id = :emphasis_id
                       )";
    $electivesStmt = $conn->prepare($electivesQuery);
    $electivesStmt->bindValue(":star_id_1", $star_id, PDO::PARAM_STR);
    $electivesStmt->bindValue(":star_id_2", $star_id, PDO::PARAM_STR);
    $electivesStmt->bindValue(":emphasis_id", $emphasis_id, PDO::PARAM_INT);
    $electivesStmt->execute();
    $electives = $electivesStmt->fetchAll(PDO::FETCH_ASSOC);

    $students[] = [
        "id" => $star_id,
        "name" => $row["name"],
        "emphasis_id" => $emphasis_id,
        "emphasis_name" => $row["emphasis_name"], 
        "taken" => $takenCourses,
        "recommended" => $recommendedCourses,
        "required" => $requiredCourses,
        "electives" => $electives
    ];
}

echo json_encode($students);
?>

