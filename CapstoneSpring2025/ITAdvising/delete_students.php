<?php
require "db.php";

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (empty($_POST["id"])) {
        echo json_encode(["status" => "error", "message" => "STAR ID is required."]);
        exit;
    }

    $star_id = $_POST["id"];

    try {
        // Check if the student exists before deleting
        $stmt = $conn->prepare("SELECT * FROM Students WHERE star_id = :star_id");
        $stmt->bindParam(":star_id", $star_id, PDO::PARAM_STR);
        $stmt->execute();
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$student) {
            echo json_encode(["status" => "error", "message" => "Student not found."]);
            exit;
        }

        // Proceed to delete
        $stmt = $conn->prepare("DELETE FROM Students WHERE star_id = :star_id");
        $stmt->bindParam(":star_id", $star_id, PDO::PARAM_STR);
        $stmt->execute();

        echo json_encode(["status" => "success", "message" => "Student deleted successfully."]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Error: " . $e->getMessage()]);
    }
}
?>