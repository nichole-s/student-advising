<?php
require "db.php";
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $star_id = $_POST["star_id"] ?? "";
    $first_name = $_POST["first_name"] ?? "";
    $last_name = $_POST["last_name"] ?? "";
    $email = $_POST["email"] ?? "";
    $emphasis_id = $_POST["emphasis_id"] ?? "";

    if (empty($star_id) || empty($first_name) || empty($last_name) || empty($email) || empty($emphasis_id)) {
        echo json_encode(["status" => "error", "message" => "All fields are required."]);
        exit;
    }

    try {
        $stmt = $conn->prepare("INSERT INTO Students (star_id, first_name, last_name, email, emphasis_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$star_id, $first_name, $last_name, $email, $emphasis_id]);

        echo json_encode(["status" => "success", "message" => "Student added successfully!"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Error: " . $e->getMessage()]);
    }
}
?>



