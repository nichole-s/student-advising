<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Only allow these if password is expired
$allowedIfExpired = ['change_password_form.php', 'change_password.php', 'logout.php', 'login.php'];

$requestedPage = isset($_GET['page']) ? basename($_GET['page']) : '';

if (
    isset($_SESSION["password_expired"]) &&
    $_SESSION["password_expired"] === true
) {
    // If trying to load a disallowed page or no page is set
    if (!in_array($requestedPage, $allowedIfExpired)) {
        echo "<script>alert('Your password has expired. Please change it to continue.');</script>";
        echo "<script>window.location.href = 'change_password_form.php?expired=1';</script>";
        exit;
    }
}
?>

<div id="dashboardContent">
    <ul class="dashboard-nav">
        <li><a href="#" class="nav-link" data-page="manage_students.php">Manage Students</a></li>
        <!-- <li><a href="#" class="nav-link" data-page="manage_courses.php">Manage Courses</a></li> -->
        <li><a href="#" class="nav-link" data-page="manage_users.php">Manage Users</a></li>
        <!--<li><a href="#" class="nav-link" data-page="advise_students.php">Advise Students</a></li> -->
        <!-- <li><a href="#" class="nav-link" data-page="generate_reports.php">Generate Reports</a></li> -->
        <li><a href="logout.php">Log Out</a></li>
    </ul>
    <h2 class="welcome-message">Welcome to the IT Advising System</h2>
</div>

