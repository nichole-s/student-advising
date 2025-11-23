<?php
session_start();
$expired = $_SESSION["password_expired"] ?? false;
?>

<form id="changePasswordForm" action="change_password.php" method="POST">
    <h2>Change Password</h2>

    <?php if ($expired): ?>
        <p style="color: red;"><strong>Your password has expired. Please set a new one to continue.</strong></p>
    <?php endif; ?>

    <label for="currentPassword">Current Password:</label>
    <input type="password" id="currentPassword" name="currentPassword" required>

    <label for="newPassword">New Password:</label>
    <input type="password" id="newPassword" name="newPassword" required>

    <label for="confirmNewPassword">Confirm New Password:</label>
    <input type="password" id="confirmNewPassword" name="confirmNewPassword" required>

    <button type="submit" class="button-secondary">Change Password</button>
</form>

<p id="passwordChangeMessage"></p>

<script>
document.getElementById("changePasswordForm").addEventListener("submit", function (e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    fetch("change_password.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        const msg = document.getElementById("passwordChangeMessage");
        if (data.status === "success") {
            msg.style.color = "green";
            msg.textContent = "Password changed successfully. Redirecting...";

            // Clear the password_expired session and redirect
            setTimeout(() => {
                window.location.href = "index.php";
            }, 1500);
        } else {
            msg.style.color = "red";
            msg.textContent = data.message;
        }
    })
    .catch(err => {
        console.error("Error changing password:", err);
        document.getElementById("passwordChangeMessage").textContent = "Something went wrong.";
    });
});
</script>
