<?php
require "db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_POST["id"] ?? "";

    if (empty($user_id)) {
        echo json_encode(["status" => "error", "message" => "User ID is required."]);
        exit;
    }

    try {
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);

        echo json_encode(["status" => "success", "message" => "User deleted successfully!"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Error: " . $e->getMessage()]);
    }
}
?>