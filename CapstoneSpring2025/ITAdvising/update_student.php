<?php
require "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $star_id = $_POST["star_id"];
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $emphasis_id = !empty($_POST["emphasis_id"]) ? $_POST["emphasis_id"] : null;

    // Update student record
    $stmt = $conn->prepare("UPDATE Students SET first_name = ?, last_name = ?, emphasis_id = ? WHERE star_id = ?");
    $stmt->execute([$first_name, $last_name, $emphasis_id, $star_id]);

    echo json_encode(["status" => "success", "message" => "Student updated successfully"]);
}
?>
