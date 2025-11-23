<?php
session_start();
header("Content-Type: application/json");

// Debugging: Print session status and session data
echo json_encode([
    "logged_in" => isset($_SESSION["user_id"]),
    "user_id" => $_SESSION["user_id"] ?? null,
    "session_id" => session_id(),
    "debug" => $_SESSION
]);
?>
