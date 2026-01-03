<?php
include 'db.php';
session_start();

// Admin protection
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // Optional: Delete photo file too
    $fetch_sql = "SELECT photo FROM issues WHERE id = '$id'";
    $fetch_res = mysqli_query($conn, $fetch_sql);
    $row = mysqli_fetch_assoc($fetch_res);
    if ($row && $row['photo']) {
        @unlink("uploads/" . $row['photo']);
    }

    $sql = "DELETE FROM issues WHERE id = '$id'";

    if (mysqli_query($conn, $sql)) {
        header("Location: admin_dashboard.php?msg=deleted");
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }

    mysqli_close($conn);
} else {
    header("Location: admin_dashboard.php");
}
?>