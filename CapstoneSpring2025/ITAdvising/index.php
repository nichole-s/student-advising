<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IT Advising</title>
    <link rel="stylesheet" href="styles.css" type="text/css">
</head>
<body>

    <!-- Persistent Header -->
    <header>
        <div class="header">
            <img src="ATC_Horizontal_color.jpg" alt="Anoka Technical College Logo" class="logo">
            <span class="header-title">IT Advising</span>
        </div>
    </header>

    <!-- Dashboard Navigation (Only Show If Logged In) -->
    <?php if (isset($_SESSION["user_id"])): ?>
        <div id="dashboard">
            <?php include "dashboard.php"; ?>
        </div>
    <?php endif; ?>

    <!-- Dynamic Content Area -->
    <main id="dynamicContent">
        <?php
        if (!isset($_SESSION["user_id"])) {
            include "login_form.php"; // Show login form if not logged in
        } 
        ?> 
    </main>
    <script src="script.js"></script>
    <script src="manage_entities.js"></script>
</body>
</html>



