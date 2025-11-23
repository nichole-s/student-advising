<?php
session_start();
require "db.php";

header("Content-Type: application/json");

$response = ["status" => "error", "message" => "Invalid request"];

try {
    // Must be logged in to change password
    if (!isset($_SESSION["user_id"])) {
        $response["message"] = "Not logged in.";
        echo json_encode($response);
        exit;
    }

    $userId = $_SESSION["user_id"];
    $currentPassword = $_POST["currentPassword"] ?? '';
    $newPassword = $_POST["newPassword"] ?? '';
    $confirmNewPassword = $_POST["confirmNewPassword"] ?? '';

    if (!$currentPassword || !$newPassword || !$confirmNewPassword) {
        $response["message"] = "All fields are required.";
        echo json_encode($response);
        exit;
    }

    if ($newPassword !== $confirmNewPassword) {
        $response["message"] = "New passwords do not match.";
        echo json_encode($response);
        exit;
    }

    // Fetch current password
    $stmt = $conn->prepare("SELECT password_hash FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($currentPassword, $user["password_hash"])) {
        $response["message"] = "Current password is incorrect.";
        echo json_encode($response);
        exit;
    }

    // Hash and update new password
    $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
    $updateStmt = $conn->prepare("UPDATE users SET password_hash = ?, password_last_changed = CURDATE() WHERE user_id = ?");
    $updateStmt->execute([$newHash, $userId]);

    // Clear the password_expired flag
    unset($_SESSION["password_expired"]);

    $response = ["status" => "success"];

} catch (Exception $e) {
    $response["message"] = "Server error.";
    $response["debug"] = $e->getMessage();
}

echo json_encode($response);
