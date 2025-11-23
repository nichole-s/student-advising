<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require "db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    header("Content-Type: application/json");

    $star_id = $_POST["star_id"] ?? "";
    $first_name = $_POST["first_name"] ?? "";
    $last_name = $_POST["last_name"] ?? "";
    $email = $_POST["email"] ?? ""; 
    $emphasis_id = $_POST["emphasis_id"] ?? "";

    if (empty($star_id) || empty($first_name) || empty($last_name) || empty($email) || empty($emphasis_id)) {
        echo json_encode(["status" => "error", "message" => "All fields are required."]);
        exit;
    }

    try {
        $stmt = $conn->prepare("UPDATE Students SET first_name = ?, last_name = ?, email = ?, emphasis_id = ? WHERE star_id = ?");
        $stmt->execute([$first_name, $last_name, $email, $emphasis_id, $star_id]);

        echo json_encode(["status" => "success", "message" => "Student updated successfully!"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Error: " . $e->getMessage()]);
    }
    exit;
}

// Handle GET request (Fetch student data)
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["id"])) {
    $student_id = $_GET["id"];

    // Get student data
    $stmt = $conn->prepare("SELECT * FROM Students WHERE star_id = :id");
    $stmt->bindParam(":id", $student_id, PDO::PARAM_STR);
    $stmt->execute();
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        die("<p style='color: red;'>❌ No student found with ID: $student_id</p>");
    }

    // Get emphasis options for dropdown
    $stmt = $conn->query("SELECT emphasis_id, emphasis_name FROM Emphasis ORDER BY emphasis_name ASC");
    $emphasisOptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <h3>Edit Student</h3>
    <form id="editStudentsForm" onsubmit="event.preventDefault(); saveEntityEdit('students');">
        <input type="hidden" id="edit_star_id" name="star_id" value="<?= htmlspecialchars($student['star_id']) ?>">

        <label>First Name:
            <input type="text" id="edit_first_name" name="first_name" value="<?= htmlspecialchars($student['first_name']) ?>" required>
        </label>
        <label>Last Name:
            <input type="text" id="edit_last_name" name="last_name" value="<?= htmlspecialchars($student['last_name']) ?>" required>
        </label>
        <label>Email:
            <input type="email" id="edit_email" name="email" value="<?= htmlspecialchars($student['email'] ?? '') ?>" required>
        </label>
        <label>Emphasis:
            <select id="edit_emphasis_id" name="emphasis_id" required>
                <option value="">-- Select Emphasis --</option>
                <?php foreach ($emphasisOptions as $emphasis): ?>
                    <option value="<?= $emphasis['emphasis_id'] ?>" <?= $student['emphasis_id'] == $emphasis['emphasis_id'] ? "selected" : "" ?>>
                        <?= htmlspecialchars($emphasis['emphasis_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <button type="submit">Save Changes</button>
        <button type="button" onclick="closeEditModal()">Cancel</button>
    </form>
    <?php
}
?>





