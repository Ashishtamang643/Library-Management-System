<?php
session_start();
$connection = mysqli_connect("localhost", "root", "");
$db = mysqli_select_db($connection, "library");

if (isset($_POST['update'])) {
    $bname = $_POST['bname'];
    $bnum = $_POST['bnum'];
    $edition = $_POST['edition'];
    $author = $_POST['author'];
    $publication = $_POST['publication'];
    $total_quantity = $_POST['total_quantity'];
    $semester = $_POST['semester'];
    $faculty = $_POST['faculty'];

    // Fetch the current total quantity and available quantity
    $fetch_query = "SELECT total_quantity, available_quantity FROM books WHERE book_num = '$bnum'";
    $fetch_result = mysqli_query($connection, $fetch_query);
    $row = mysqli_fetch_assoc($fetch_result);
    $current_total_quantity = $row['total_quantity'];
    $current_available_quantity = $row['available_quantity'];

    // Update available quantity based on the difference
    $new_available_quantity = $current_available_quantity + ($total_quantity - $current_total_quantity);

    // Update the book details
    $update_query = "UPDATE books SET 
                     book_name = '$bname', 
                     book_edition = '$edition', 
                     author_name = '$author', 
                     publication = '$publication', 
                     total_quantity = '$total_quantity', 
                     available_quantity = '$new_available_quantity', 
                     semester = '$semester', 
                     faculty = '$faculty' 
                     WHERE book_num = '$bnum'";
    $update_result = mysqli_query($connection, $update_query);

    if ($update_result) {
        echo "<script>alert('Book details updated successfully.'); window.location.href = 'managebooks.php';</script>";
    } else {
        echo "<script>alert('Error updating book details: " . mysqli_error($connection) . "');</script>";
    }
}
?>