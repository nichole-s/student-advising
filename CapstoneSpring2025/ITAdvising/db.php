<?php
$host = "127.0.0.1";  // Use 127.0.0.1 for WAMP
$port = "3307";       // WAMP uses port 3307 for MariaDB
$dbname = "college_advising"; 
$username = "root"; 
$password = "";       // Default WAMP password is blank

try {
    $conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

?>