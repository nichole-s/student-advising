<?php
require_once "db.php"; // Connect to your database (provides $conn)

header('Content-Type: application/json'); // Tell browser we're sending JSON

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['csvFile'])) {
        throw new Exception('❌ No file uploaded.');
    }

    $uploadPath = __DIR__ . '/uploads/';
    if (!file_exists($uploadPath)) {
        mkdir($uploadPath, 0777, true);
    }

    $targetFile = $uploadPath . basename($_FILES['csvFile']['name']);
    if (!move_uploaded_file($_FILES['csvFile']['tmp_name'], $targetFile)) {
        throw new Exception('❌ Failed to move uploaded file.');
    }

    if (($handle = fopen($targetFile, "r")) === FALSE) {
        throw new Exception('❌ Could not open uploaded CSV file.');
    }

    $rowCount = 0;
    $successCount = 0;
    $errorCount = 0;
    $duplicateCount = 0;

    // Skip header row
    fgetcsv($handle);

    while (($data = fgetcsv($handle)) !== FALSE) {
        $rowCount++;

        $rawInfo = $data[0] ?? null;
        $firstName = $data[1] ?? null;
        $lastName = $data[2] ?? null;
        $starId = $data[3] ?? null;

        if (!$rawInfo || !$firstName || !$lastName || !$starId) {
            $errorCount++;
            continue;
        }

        // Extract email from "Name" <email>
        preg_match('/<([^>]+)>/', $rawInfo, $matches);
        $email = $matches[1] ?? null;

        if (!$email) {
            $errorCount++;
            continue;
        }

        $emphasis_id = 5; // 5 = 'Undecided'

        // Check if student already exists
        $checkStmt = $conn->prepare("SELECT COUNT(*) FROM students WHERE star_id = ?");
        $checkStmt->execute([$starId]);
        $exists = $checkStmt->fetchColumn();

        if ($exists) {
            $duplicateCount++;
            continue; // Skip inserting this student
        }

        // Insert new student
        $stmt = $conn->prepare("INSERT INTO students (star_id, first_name, last_name, email, emphasis_id) VALUES (?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bindValue(1, $starId, PDO::PARAM_STR);
            $stmt->bindValue(2, $firstName, PDO::PARAM_STR);
            $stmt->bindValue(3, $lastName, PDO::PARAM_STR);
            $stmt->bindValue(4, $email, PDO::PARAM_STR);
            $stmt->bindValue(5, $emphasis_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $successCount++;
            } else {
                $errorCount++;
            }
        } else {
            $errorCount++;
        }
    }
    fclose($handle);

    // Clean up: Delete uploaded file
    if (file_exists($targetFile)) {
        unlink($targetFile);
    }

    // Pretty message output
    echo "Upload complete: {$successCount} students inserted, {$duplicateCount} duplicates skipped, {$errorCount} failed.";
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>


