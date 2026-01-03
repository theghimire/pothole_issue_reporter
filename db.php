<?php
// Enable error reporting for debugging (Remove this in production!)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simple database connection
$host = "localhost";
$user = "root";
$pass = "";
$db_name = "local_issues_db";

$conn = mysqli_connect($host, $user, $pass, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>