<?php
require "db.php";
header("Content-Type: application/json");

$stmt = $conn->prepare("SELECT emphasis_id, emphasis_name FROM emphasis ORDER BY emphasis_name");
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($rows);
?>
