<?php
require('functions.php');
session_start();
if (!isset($_SESSION['Name'])) {
    echo "<script>alert('Please login to continue.'); window.location.href='index.php';</script>";
    exit();
}
$connection = mysqli_connect("localhost","root","");
$db = mysqli_select_db($connection,"library");

// Initialize filter variables
$filter_student_id = isset($_GET['student_id']) ? mysqli_real_escape_string($connection, $_GET['student_id']) : '';
$filter_book_name = isset($_GET['book_name']) ? mysqli_real_escape_string($connection, $_GET['book_name']) : '';
$filter_book_num = isset($_GET['book_num']) ? mysqli_real_escape_string($connection, $_GET['book_num']) : '';
$filter_returned = isset($_GET['returned']) ? mysqli_real_escape_string($connection, $_GET['returned']) : '';

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

// Build query with filters
$query = "SELECT issued.student_id, issued.book_name, issued.book_author, issued.book_num,
          issued.issue_date, issued.returned, issued.returned_date, users.Name 
          FROM issued LEFT JOIN users ON issued.student_id = users.ID
          WHERE 1=1";

if (!empty($filter_student_id)) {
    $query .= " AND issued.student_id LIKE '%$filter_student_id%'";
}
if (!empty($filter_book_name)) {
    $query .= " AND issued.book_name LIKE '%$filter_book_name%'";
}
if (!empty($filter_book_num)) {
    $query .= " AND issued.book_num LIKE '%$filter_book_num%'";
}
if ($filter_returned !== '') {
    $query .= " AND issued.returned = '$filter_returned'";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issued Books</title>
    <link rel="stylesheet" href="../style2.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            /* padding: 20px; */
        }

        .filter-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
            align-items: center;
            max-width:1500px;
            left:50%;
            position:relative;
            transform: translateX(-50%);
        }

        .filter-container label {
            margin-right: 8px;
            color: #333;
            font-weight: bold;
        }

        .filter-container input[type="text"],
        .filter-container select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 200px;
            transition: border-color 0.3s ease;
        }

        .filter-container input[type="text"]:focus,
        .filter-container select:focus {
            outline: none;
            border-color: #4CAF50;
        }

        .filter-container button {
            padding: 8px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .filter-container button:hover {
            background-color: #45a049;
        }

        /* table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        table th {
            background-color: #f8f8f8;
            font-weight: bold;
        } */

        .return-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .return-btn:hover {
            background-color: #45a049;
        }

        .returned-status {
            color: #888;
            font-style: italic;
        }
    </style>
</head>
<body>
<?php include('adminnavbar.php'); ?>

    <h2 class="h2-register-header">Issued Books</h2>

    <!-- Filter Form -->
    <div class="filter-container">
        <form method="GET" action="">
            <label for="student_id">Student ID:</label>
            <input type="text" name="student_id" id="student_id" 
                   value="<?php echo htmlspecialchars($filter_student_id); ?>" 
                   placeholder="Search Student ID">

            <label for="book_name">Book Name:</label>
            <input type="text" name="book_name" id="book_name" 
                   value="<?php echo htmlspecialchars($filter_book_name); ?>" 
                   placeholder="Search Book Name">

            <label for="book_num">Book Number:</label>
            <input type="text" name="book_num" id="book_num" 
                   value="<?php echo htmlspecialchars($filter_book_num); ?>" 
                   placeholder="Search Book Number">

            <label for="returned">Status:</label>
            <select name="returned" id="returned">
                <option value="">All</option>
                <option value="0" <?php echo ($filter_returned === '0') ? 'selected' : ''; ?>>Pending</option>
                <option value="1" <?php echo ($filter_returned === '1') ? 'selected' : ''; ?>>Returned</option>
            </select>

            <button type="submit">Filter</button>
        </form>
    </div>

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
        <tbody>
            <?php
            $query_run = mysqli_query($connection, $query);
            while($row = mysqli_fetch_assoc($query_run)) {
                $bname = $row['book_name'];
                $bnum = $row['book_num'];
                $author = $row['book_author'];
                $date = $row['issue_date'];
                $student = $row['Name'];
                $student_id = $row['student_id'];
                $returned = $row['returned'] ?? 0;
            ?>
            <tr>
                <td><?php echo htmlspecialchars($bname);?></td>
                <td><?php echo htmlspecialchars($student_id);?></td>
                <td><?php echo htmlspecialchars($student);?></td>
                <td><?php echo htmlspecialchars($bnum);?></td>
                <td><?php echo htmlspecialchars($author);?></td>
                <td><?php echo htmlspecialchars($date);?></td>
                <td>
                    <?php if($returned == 1): ?>
                        <span class="returned-status">Returned</span>
                    <?php else: ?>
                        <form method="post" style="margin: 0;">
                            <input type="hidden" name="book_num" value="<?php echo htmlspecialchars($bnum); ?>">
                            <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>">
                            <button type="submit" name="return_book" class="return-btn">Return</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>