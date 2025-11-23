<?php
$password = "Admin@1234";
$hashed_password = password_hash($password, PASSWORD_BCRYPT);
echo "New Hash: " . $hashed_password;
?>
