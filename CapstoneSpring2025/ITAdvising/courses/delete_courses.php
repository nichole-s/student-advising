<?php
require "db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $course_code = $_POST["id"] ?? "";

    if (empty($course_code)) {
        echo json_encode(["status" => "error", "message" => "Course code is required."]);
        exit;
    }

    try {
        $stmt = $conn->prepare("DELETE FROM courses WHERE course_code = ?");
        $stmt->execute([$course_code]);

        echo json_encode(["status" => "success", "message" => "Course deleted successfully!"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Error: " . $e->getMessage()]);
    }
}
?>