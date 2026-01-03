<?php
include 'db.php';

$username = 'admin';
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO admins (username, password_hash) VALUES ('$username', '$hash') ON DUPLICATE KEY UPDATE password_hash='$hash'";
if (mysqli_query($conn, $sql)) {
    echo "Admin user created/updated successfully. Username: admin, Password: admin123";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>