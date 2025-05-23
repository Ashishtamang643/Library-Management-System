<?php
session_start();
if (!isset($_SESSION['Name'])) {
    echo "<script>alert('Please login to continue.'); window.location.href='index.php';</script>";
    exit();
}

if (isset($_GET['id'])) {
    $connection = mysqli_connect("localhost", "root", "", "library");

    $user_id = mysqli_real_escape_string($connection, $_GET['id']);

    $query = "DELETE FROM users WHERE ID = '$user_id'";
    if (mysqli_query($connection, $query)) {
        echo "<script>alert('User deleted successfully.'); window.location.href='registeruser.php';</script>";
    } else {
        echo "<script>alert('Failed to delete user.'); window.location.href='registeruser.php';</script>";
    }
}
?>
