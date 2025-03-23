<?php
     require('functions.php');
     session_start();
     if (!isset($_SESSION['Name'])) {
        echo "<script>alert('Please login to continue.'); window.location.href='index.php';</script>";
        exit();
    }
     $connection = mysqli_connect("localhost","root","");
     $db = mysqli_select_db($connection,"library");
     $bname = "";
     $author = "";
     $bnum = "";
     $student = "";
     $date = "";
     
     // Process return action if submitted
     if(isset($_POST['return_book'])) {
         $return_book_num = $_POST['book_num'];
         $return_student_id = $_POST['student_id'];
         $current_date = date("Y-m-d");

         // Update the issued table to mark the book as returned
         $return_query = "UPDATE issued SET returned = 1, returned_date = '$current_date' 
                          WHERE book_num = '$return_book_num' AND student_id = '$return_student_id'";
         mysqli_query($connection, $return_query);

         // Increase available_quantity in the books table by 1 for the returned book
         $update_quantity_query = "UPDATE books SET available_quantity = available_quantity + 1 
                                  WHERE book_num = '$return_book_num'";
         mysqli_query($connection, $update_quantity_query);
         
         // Redirect to avoid form resubmission on page refresh
         header("Location: ".$_SERVER['PHP_SELF']);
         exit();
     }

     // Modified query to include returned status
     $query = "SELECT issued.student_id, issued.book_name, issued.book_author, issued.book_num,
               issued.issue_date, issued.returned, issued.returned_date, users.Name 
               FROM issued LEFT JOIN users ON issued.student_id = users.ID";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Users</title>
    <link rel="stylesheet" href="../style2.css">
</head>
<body>
<?php include('adminnavbar.php'); ?>

    <h2 class="h2-register-header">Issued Books</h2>
    <table>
        <thead>
          <tr>
             <th>Book Name</th>
             <th>Student ID</th>
             <th>Student</th>
             <th>Book Num</th>
             <th>Author</th>
             <th>Issue Date</th>
             <th>Return</th>
          </tr>
        </thead>
        <?php
             $query_run = mysqli_query($connection,$query);
             while($row = mysqli_fetch_assoc($query_run))
             {
                $bname = $row['book_name'];
                $bnum = $row['book_num'];
                $author = $row['book_author'];
                $date = $row['issue_date'];
                $student = $row['Name'];
                $student_id = $row['student_id'];
                $returned = $row['returned'] ?? 0;
                ?>
        <tr>
            <td><?php echo $bname;?></td>
            <td><?php echo $student_id;?></td>
            <td><?php echo $student;?></td>
            <td><?php echo $bnum;?></td>
            <td><?php echo $author;?></td>
            <td><?php echo $date;?></td>
            <td>
                <?php if($returned == 1): ?>
                    Returned
                <?php else: ?>
                    <form method="post" style="margin: 0;">
                        <input type="hidden" name="book_num" value="<?php echo $bnum; ?>">
                        <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">
                        <button type="submit" name="return_book" class="return-btn">Return</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php
             }
            ?>
    </table>

    <!-- Optional: Add some CSS for the return button -->
    <style>
        .return-btn {
            background-color: #4CAF50;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        .return-btn:hover {
            background-color: #45a049;
        }
    </style>
</body>
</html>
