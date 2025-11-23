<?php
require "db.php";
header("Content-Type: application/json");

$star_id = $_GET["star_id"] ?? null;

if (!$star_id) {
    echo json_encode(["success" => false, "message" => "Missing star_id"]);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT note_text, note_date FROM notes WHERE star_id = ? ORDER BY note_date DESC");
    $stmt->execute([$star_id]);
    $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(["success" => true, "notes" => $notes]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
