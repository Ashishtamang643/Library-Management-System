<?php
    require('functions.php');
    session_start();
    if (!isset($_SESSION['Name'])) {
        echo "<script>alert('Please login to continue.'); window.location.href='index.php';</script>";
        exit();
    }
    $connection = mysqli_connect("localhost","root","", "library");

    // Process the form submission for issuing books
    if(isset($_POST['issue-book-btn'])) {
        $studentID = $_POST["studentID"];
        $bnum = $_POST["bnum"];
        $issuedate = date("Y-m-d"); // Current date

        // Check if the book is already issued to the same student
        $check_issued_query = "SELECT * FROM issued 
                               WHERE student_id = '$studentID' 
                               AND book_num = '$bnum' 
                               AND returned = 0";
        $check_issued_result = mysqli_query($connection, $check_issued_query);

        if (mysqli_num_rows($check_issued_result) > 0) {
            echo "<script>alert('This book is already issued to the student.');</script>";
        } else {
            // Check if the book exists and is available
            $check_book_query = "SELECT * FROM books WHERE book_num = '$bnum'";
            $check_book_result = mysqli_query($connection, $check_book_query);
            $book = mysqli_fetch_assoc($check_book_result);

            if ($book && $book['available_quantity'] > 0) {
                // Insert the issue record
                      // Insert the issue record into the 'issued' table
                      $issue_query = "INSERT INTO issued (student_id, book_num, issue_date, book_name, book_author, semester, faculty, publication) 
                      VALUES ('$studentID', '$bnum', '$issuedate', '" . $book['book_name'] . "', '" . $book['author_name'] . "', '" . $book['semester'] . "', '" . $book['faculty'] . "', '" . $book['publication'] . "')";
      $issue_query_run = mysqli_query($connection, $issue_query);

                // Update available quantity in books table
                $update_query = "UPDATE books SET available_quantity = available_quantity - 1 WHERE book_num = '$bnum'";
                $update_query_run = mysqli_query($connection, $update_query);

                if ($issue_query_run && $update_query_run) {
                    echo "<script>alert('Book Issued Successfully.'); window.location.href = window.location.href;</script>";
                } else {
                    echo "<script>alert('Error issuing book: " . mysqli_error($connection) . "');</script>";
                }
            } else {
                echo "<script>alert('This book is not available or does not exist.');</script>";
            }
        }
    }

    // Process the form submission for returning books
    if(isset($_POST['return-book-btn'])) {
        $studentID = $_POST["studentID"];
        $bnum = $_POST["bnum"];
        $returned_date = date("Y-m-d"); // Current date

        // Check if the book is issued to the student
        $check_issued_query = "SELECT * FROM issued 
                               WHERE student_id = '$studentID' 
                               AND book_num = '$bnum' 
                               AND returned = 0";
        $check_issued_result = mysqli_query($connection, $check_issued_query);

        if (mysqli_num_rows($check_issued_result) > 0) {
            // Update the issued table to set returned date
            $update_return_query = "UPDATE issued 
                                    SET returned_date = '$returned_date', returned = 1
                                    WHERE student_id = '$studentID' 
                                    AND book_num = '$bnum' 
                                    AND returned = 0";
            $update_return_result = mysqli_query($connection, $update_return_query);

            // Update available quantity in books table
            $update_available_query = "UPDATE books SET available_quantity = available_quantity + 1 WHERE book_num = '$bnum'";
            $update_available_result = mysqli_query($connection, $update_available_query);

            if ($update_return_result && $update_available_result) {
                echo "<script>alert('Book Returned Successfully.'); window.location.href = window.location.href;</script>";
            } else {
                echo "<script>alert('Error returning book: " . mysqli_error($connection) . "');</script>";
            }
        } else {
            echo "<script>alert('This book was not issued to the student or is already returned.');</script>";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issue/Return Books</title>
    <style>
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
}

h2 {
    text-align: center;
    color: #333;
    margin-bottom: 20px;
}

.container {
    max-width: 800px;
    background: white;
    margin: 40px auto;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    height: fit-content;
}

form {
    display: flex;
    flex-direction: column;
}

.form-group {
    margin-bottom: 15px;
}

.forms-container{

    display: flex;
    gap: 60px;
}

.form-group label {
    font-weight: bold;
    margin-bottom: 5px;
    display: block;
    color: #555;
}

.form-group input {
    width: 100%;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 16px;
    transition: all 0.3s ease;
}

.form-group input:focus {
    border-color: #4CAF50;
    outline: none;
    box-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
}

.form-group button {
    width: 100%;
    padding: 12px;
    background: linear-gradient(to right, #4CAF50, #45a049);
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 18px;
    transition: all 0.3s ease;
}

.form-group button:hover {
    background: linear-gradient(to right, #45a049, #3e8e41);
    transform: translateY(-2px);
}

hr {
    margin: 30px 0;
    border: none;
    height: 1px;
    background-color: #ddd;
}

    </style>
</head>
<body>
    <?php include('adminnavbar.php'); ?>

    <div class="main">
    <?php include('sidebar.php'); ?>

        <div class="container">


    <div class="container">
        <h2>Issue/Return Books</h2>

        <div class="forms-container">

        <!-- Issue Book Form -->
        <form method="POST">
            <div class="form-group">
                <label for="studentID">Student ID</label>
                <input type="text" id="studentID" name="studentID" required>
            </div>
            <div class="form-group">
                <label for="bnum">Book Number</label>
                <input type="text" id="bnum" name="bnum" required>
            </div>
            <div class="form-group">
                <button type="submit" name="issue-book-btn">Issue Book</button>
            </div>
        </form>

        <hr>

        <!-- Return Book Form -->
        <form method="POST">
            <div class="form-group">
                <label for="r_studentID">Student ID</label>
                <input type="text" id="r_studentID" name="studentID" required>
            </div>
            <div class="form-group">
                <label for="r_bnum">Book Number</label>
                <input type="text" id="r_bnum" name="bnum" required>
            </div>
            <div class="form-group">
                <button type="submit" name="return-book-btn">Return Book</button>
            </div>
        </form>
        </div>

    </div>
    </div>
    </div>

</body>
</html>
