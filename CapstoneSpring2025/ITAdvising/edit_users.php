<?php
require "db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_POST["user_id"] ?? "";
    $first_name = $_POST["first_name"] ?? "";
    $last_name = $_POST["last_name"] ?? "";
    $email = $_POST["email"] ?? "";

    if (!$user_id || !$first_name || !$last_name || !$email) {
        echo json_encode(["status" => "error", "message" => "All fields are required."]);
        exit;
    }

    try {
        $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ? WHERE user_id = ?");
        $stmt->execute([$first_name, $last_name, $email, $user_id]);
        echo json_encode(["status" => "success", "message" => "User updated successfully."]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    }
    exit;
}

$user_id = $_GET["id"] ?? null;
if (!$user_id) {
    echo "<p>Error: User ID is required.</p>";
    exit;
}

$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "<p>Error: User not found.</p>";
    exit;
}
?>

<div class="modal-form-content">
    <form id="editUsersForm">
        <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['user_id']) ?>">
        <label>First Name</label>
        <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required>
        <label>Last Name</label>
        <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required>
        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
        <br><br>
        <button type="submit" class="button-primary" onclick="event.preventDefault(); saveEntityEdit('users');">Save Changes</button>
        <button type="button" class="button-cancel" onclick="closeEditModal()">Cancel</button>
    </form>
</div>
