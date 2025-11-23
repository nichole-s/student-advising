<?php
session_start();
require "db.php";

header("Content-Type: application/json");

// Initialize response
$response = ["status" => "error", "message" => "Invalid request"];

try {
    if (empty($_POST["email"]) || empty($_POST["password"])) {
        $response["message"] = "Missing email or password";
        echo json_encode($response);
        exit;
    }

    // Fetch user info including password_last_changed
    $stmt = $conn->prepare("SELECT user_id, password_hash, password_last_changed FROM users WHERE email = ?");
    $stmt->execute([$_POST["email"]]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $response["message"] = "User not found";
        echo json_encode($response);
        exit;
    }

    if (!password_verify($_POST["password"], $user["password_hash"])) {
        $response["message"] = "Incorrect password";
        echo json_encode($response);
        exit;
    }

    // Store session
    $_SESSION["user_id"] = $user["user_id"];

    // Password expiration check
    $lastChanged = new DateTime($user["password_last_changed"]);
    $now = new DateTime();
    $daysOld = $lastChanged->diff($now)->days;

    $expired = $daysOld > 90;
    $_SESSION["password_expired"] = $expired;

    // Determine redirect
    $redirectPage = $expired ? "change_password_form.php" : "index.php";
    $response = ["status" => "success", "redirect" => $redirectPage];

} catch (Exception $e) {
    $response["message"] = "Internal Server Error";
    $response["debug"] = $e->getMessage();
}

echo json_encode($response);

if (!headers_sent() && empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    header("Location: index.php");
    exit;
}
?>