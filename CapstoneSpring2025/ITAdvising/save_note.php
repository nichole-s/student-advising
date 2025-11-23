<?php
require "db.php";
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);
$star_id = $data["star_id"] ?? null;
$note_text = $data["note_text"] ?? null;

if (!$star_id || !$note_text) {
    echo json_encode(["success" => false, "message" => "Missing star_id or note text"]);
    exit;
}

try {
    $stmt = $conn->prepare("INSERT INTO notes (star_id, note_text) VALUES (?, ?)");
    $stmt->execute([$star_id, $note_text]);
    echo json_encode(["success" => true]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
