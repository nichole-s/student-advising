<?php
require "db.php";
header("Content-Type: application/json");

// Read raw JSON input
$rawInput = file_get_contents("php://input");
error_log("Raw Input Received: " . $rawInput);


if (!$rawInput) {
    echo json_encode(["success" => false, "message" => "No input received"]);
    exit;
}

$data = json_decode($rawInput, true);

if (!is_array($data)) {
    echo json_encode(["success" => false, "message" => "Invalid JSON input"]);
    exit;
}

// Extract parameters safely
$courseCode = $data["courseId"] ?? null;
$columnType = $data["columnType"] ?? null;
$starId = $data["starId"] ?? null;

// Validate parameters
if (empty($courseCode) || empty($columnType) || empty($starId)) {
    echo json_encode(["success" => false, "message" => "Missing parameters"]);
    exit;
}

try {
    if ($columnType === "taken") {
        // Remove from recommended (if exists)
        $removeRec = $conn->prepare("DELETE FROM recommended WHERE star_id = ? AND course_code = ?");
        $removeRec->execute([$starId, $courseCode]);

        // Add to enrollments if not already present
        $insertEnroll = $conn->prepare("INSERT IGNORE INTO enrollments (star_id, course_code) VALUES (?, ?)");
        $insertEnroll->execute([$starId, $courseCode]);

    } elseif ($columnType === "recommended") {
        // Remove from enrollments (if exists)
        $removeEnroll = $conn->prepare("DELETE FROM enrollments WHERE star_id = ? AND course_code = ?");
        $removeEnroll->execute([$starId, $courseCode]);

        // Add to recommended if not already present
        $insertRec = $conn->prepare("INSERT IGNORE INTO recommended (star_id, course_code) VALUES (?, ?)");
        $insertRec->execute([$starId, $courseCode]);

    } elseif ($columnType === "required") {
        // Remove from both recommended and enrollments
        $removeRec = $conn->prepare("DELETE FROM recommended WHERE star_id = ? AND course_code = ?");
        $removeRec->execute([$starId, $courseCode]);

        $removeEnroll = $conn->prepare("DELETE FROM enrollments WHERE star_id = ? AND course_code = ?");
        $removeEnroll->execute([$starId, $courseCode]);

    } else {
        echo json_encode(["success" => false, "message" => "Invalid columnType"]);
        exit;
    }

    echo json_encode(["success" => true]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
?>

