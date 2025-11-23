<?php
require "db.php";
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);
$star_id = $data["star_id"] ?? null;
$emphasis_id = $data["emphasis_id"] ?? null;

if (!$star_id || !$emphasis_id) {
    echo json_encode(["success" => false, "message" => "Missing data"]);
    exit;
}

try {
    $stmt = $conn->prepare("UPDATE students SET emphasis_id = ? WHERE star_id = ?");
    $stmt->execute([$emphasis_id, $star_id]);
    echo json_encode(["success" => true]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
