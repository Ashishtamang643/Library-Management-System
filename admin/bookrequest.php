<?php
session_start();
if (!isset($_SESSION['Name'])) {
    echo "<script>alert('Please login to continue.'); window.location.href='index.php';</script>";
    exit();
}

$connection = mysqli_connect("localhost", "root", "", "library");

// Handle status update
if (isset($_POST['update_status'])) {
    $request_id = $_POST['request_id'];
    $status = $_POST['status'];

    $query = "UPDATE book_request SET status = ? WHERE request_id = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "ss", $status, $request_id);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Status updated successfully!');</script>";
    } else {
        echo "<script>alert('Error updating status: " . mysqli_error($connection) . "');</script>";
    }
}

// Handle book issue operation
if (isset($_POST['issue_book'])) {
    $request_id = $_POST['request_id'];
    $book_num = $_POST['book_num'];
    $student_id = $_POST['student_id'];
    $due_date = $_POST['due_date'];

    // Validate due date
    if (empty($due_date)) {
        echo "<script>alert('Please select a due date!'); window.location.href = window.location.href;</script>";
        exit();
    }

    // Check if due date is in the future
    $current_date = date('Y-m-d');
    if ($due_date <= $current_date) {
        echo "<script>alert('Due date must be in the future!'); window.location.href = window.location.href;</script>";
        exit();
    }

    // Check how many books are already issued to this student
    $check_issue_count_query = "SELECT COUNT(*) AS issued_count FROM issued WHERE student_id = ? AND returned IS NULL";
    $stmt = mysqli_prepare($connection, $check_issue_count_query);
    mysqli_stmt_bind_param($stmt, "s", $student_id);
    mysqli_stmt_execute($stmt);
    $issue_count_result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($issue_count_result);
    $issued_count = $row['issued_count'];

    $max_books_allowed = 7;
    if ($issued_count >= $max_books_allowed) {
        echo "<script>alert('You have already issued the maximum number of books allowed ($max_books_allowed).'); window.location.href = window.location.href;</script>";
        exit();
    }

    // Check if this specific book is already issued to the student and not returned
    $check_issued_query = "SELECT * FROM issued WHERE student_id = ? AND book_num = ? AND returned IS NULL";
    $stmt = mysqli_prepare($connection, $check_issued_query);
    mysqli_stmt_bind_param($stmt, "ss", $student_id, $book_num);
    mysqli_stmt_execute($stmt);
    $check_result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($check_result) > 0) {
        echo "<script>alert('This book is already issued to this student!'); window.location.href = window.location.href;</script>";
        exit();
    }

    $book_query = "SELECT * FROM books WHERE book_num = ?";
    $stmt = mysqli_prepare($connection, $book_query);
    mysqli_stmt_bind_param($stmt, "s", $book_num);
    mysqli_stmt_execute($stmt);
    $book_result = mysqli_stmt_get_result($stmt);
    $book = mysqli_fetch_assoc($book_result);

    $request_query = "SELECT * FROM book_request WHERE request_id = ?";
    $stmt = mysqli_prepare($connection, $request_query);
    mysqli_stmt_bind_param($stmt, "s", $request_id);
    mysqli_stmt_execute($stmt);
    $request_result = mysqli_stmt_get_result($stmt);
    $request = mysqli_fetch_assoc($request_result);

    if ($book && $request) {
        if ($book['available_quantity'] <= 0) {
            echo "<script>alert('Book is not available in stock!'); window.location.href = window.location.href;</script>";
            exit();
        }

        $issue_date = date("Y-m-d");

        $insert_issue_query = "INSERT INTO issued (student_id, book_num, book_name, book_author, issue_date, due_date, semester, faculty, publication, picture) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($connection, $insert_issue_query);
        mysqli_stmt_bind_param($stmt, "ssssssssss",
            $student_id,
            $book_num,
            $book['book_name'],
            $book['author_name'],
            $issue_date,
            $due_date,
            $book['semester'],
            $book['faculty'],
            $book['publication'],
            $book['picture']    
        );

        if (mysqli_stmt_execute($stmt)) {
            // Reduce available quantity by 1
            $update_available_quantity = "UPDATE books SET available_quantity = available_quantity - 1 WHERE book_num = ?";
            $stmt = mysqli_prepare($connection, $update_available_quantity);
            mysqli_stmt_bind_param($stmt, "s", $book_num);
            mysqli_stmt_execute($stmt);

            // Update the book request status
            $update_request_status = "UPDATE book_request SET status = 'issued' WHERE request_id = ?";
            $stmt = mysqli_prepare($connection, $update_request_status);
            mysqli_stmt_bind_param($stmt, "s", $request_id);

            if (mysqli_stmt_execute($stmt)) {
                echo "<script>alert('Book issued successfully with due date: " . $due_date . "'); window.location.href = window.location.href;</script>";
            } else {
                echo "<script>alert('Error updating request status: " . mysqli_error($connection) . "');</script>";
            }
        } else {
            echo "<script>alert('Error issuing book: " . mysqli_error($connection) . "');</script>";
        }
    } else {
        echo "<script>alert('Book or request not found.');</script>";
    }
}

// Filter/search section
$filter_student_id = isset($_GET['student_id']) ? $_GET['student_id'] : '';
$filter_book_name = isset($_GET['book_name']) ? $_GET['book_name'] : '';
$filter_book_num = isset($_GET['book_num']) ? $_GET['book_num'] : '';

$query = "SELECT * FROM book_request WHERE status != ''";

if (!empty($filter_student_id)) {
    $query .= " AND student_id LIKE '%" . mysqli_real_escape_string($connection, $filter_student_id) . "%'";
}
if (!empty($filter_book_name)) {
    $query .= " AND book_name LIKE '%" . mysqli_real_escape_string($connection, $filter_book_name) . "%'";
}
if (!empty($filter_book_num)) {
    $query .= " AND book_num LIKE '%" . mysqli_real_escape_string($connection, $filter_book_num) . "%'";
}

$query .= " ORDER BY request_date DESC";
$query_run = mysqli_query($connection, $query);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Book Requests</title>
    <link rel="stylesheet" href="../style2.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .status-dropdown, .filter-input, .due-date-input {
            padding: 5px;
            border-radius: 4px;
            border: 1px solid #ccc;
            margin: 5px 0;
        }
        .update-btn, .issue-btn, .filter-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 4px;
        }
        .update-btn:hover, .issue-btn:hover, .filter-btn:hover {
            background-color: #45a049;
        }
        .issue-btn[disabled] {
            background-color: #cccccc;
            cursor: not-allowed;
        }
        .issued-text {
            color: green;
            font-weight: bold;
        }
        .returned-text {
            color: blue;
            font-weight: bold;
        }
        .title {
            text-align: center;
            margin-top: 20px;
            color: #333;
        }
        .filter-container {
            width: 92%;
            margin: 20px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #fff;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            max-width: 1250px;
        }
        .filter-group {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap:10px;
        }
        .reset-btn {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 4px;
            margin-left: 10px;
        }
        .reset-btn:hover {
            background-color: #d32f2f;
        }
        .issue-form {
            display: flex;
            flex-direction: column;
            gap: 5px;
            align-items: flex-start;
        }
        .due-date-input {
            width: 140px;
            font-size: 12px;
        }
        .issue-form label {
            font-size: 11px;
            color: #666;
            margin: 0;
        }
    </style>
</head>
<body>
<?php include('adminnavbar.php'); ?>
<div class="main">
<?php include('sidebar.php'); ?>

<div class="container">

<h2 class="title">Book Requests Management</h2>

<div class="filter-container">
    <form method="GET" action="" style="display: flex; gap: 10px; flex-wrap: wrap;">
        <div class="filter-group">
            <label>Student ID</label>
            <input type="text" name="student_id" class="filter-input" pattern="\d{2,5}" placeholder="Search Student ID" value="<?php echo htmlspecialchars($filter_student_id); ?>">
        </div>
        <div class="filter-group">
            <label>Book Name</label>
            <input type="text" name="book_name" class="filter-input" pattern="[A-Za-z\s]+" placeholder="Search Book Name" value="<?php echo htmlspecialchars($filter_book_name); ?>">
        </div>
        <div class="filter-group">
            <label>Book Number</label>
            <input type="text" name="book_num" class="filter-input" pattern="\d{13}" placeholder="Search Book Number" value="<?php echo htmlspecialchars($filter_book_num); ?>">
        </div>
        <div class="filter-group" style="align-self: flex-end;">
            <button type="submit" class="filter-btn">Apply Filter</button>
            <button type="button" class="reset-btn" onclick="window.location.href='?'">Reset</button>
        </div>
    </form>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Student ID</th>
            <th>Student Name</th>
            <th>User Email</th>
            <th>Book Name</th>
            <th>Book Num</th>
            <th>Edition</th>
            <th>Author</th>
            <th>Request Date</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if (mysqli_num_rows($query_run) > 0) {
            while ($row = mysqli_fetch_assoc($query_run)) {
                $request_id = $row['request_id'];
                $student_id = $row['student_id'];
                $student_name = $row['student_name'];
                $book_num = $row['book_num'];
                $user_email = $row['user_email'];
                $book_name = $row['book_name'];
                $book_edition = $row['book_edition'];
                $author_name = $row['author_name'];
                $request_date = $row['request_date'];
                $status = $row['status'];

                // Check if book is currently issued to this student
                $check_issued_query = "SELECT * FROM issued WHERE student_id = ? AND book_num = ?";
                $stmt = mysqli_prepare($connection, $check_issued_query);
                mysqli_stmt_bind_param($stmt, "ss", $student_id, $book_num);
                mysqli_stmt_execute($stmt);
                $check_result = mysqli_stmt_get_result($stmt);
                $is_currently_issued = mysqli_num_rows($check_result) > 0;

                // Determine if book is returned based on your logic:
                // If status is empty/null, then it's returned
                $is_returned = empty($status) || is_null($status);
        ?>
        <tr>
            <td><?php echo htmlspecialchars($request_id); ?></td>
            <td><?php echo htmlspecialchars($student_id); ?></td>
            <td><?php echo htmlspecialchars($student_name); ?></td>
            <td><?php echo htmlspecialchars($user_email); ?></td>
            <td><?php echo htmlspecialchars($book_name); ?></td>
            <td><?php echo htmlspecialchars($book_num); ?></td>
            <td><?php echo htmlspecialchars($book_edition); ?></td>
            <td><?php echo htmlspecialchars($author_name); ?></td>
            <td><?php echo htmlspecialchars($request_date); ?></td>
            <td>
                <?php if ($is_returned) { ?>
                    <span class="returned-text">Returned</span>
                <?php } elseif ($status == 'issued') { ?>
                    <span class="issued-text">Issued</span>
                <?php } else { ?>
                    <form method="POST" action="" style="display:inline;">
                        <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($request_id); ?>">
                        <select name="status" class="status-dropdown">
                            <option value="pending" <?php echo ($status == 'pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="approved" <?php echo ($status == 'approved') ? 'selected' : ''; ?>>Approved</option>
                            <option value="rejected" <?php echo ($status == 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                        </select>
                        <button type="submit" name="update_status" class="update-btn">Update</button>
                    </form>
                <?php } ?>
            </td>
            <td>
                <?php if ($is_returned) { ?>
                    <span class="returned-text">Returned</span>
                <?php } elseif ($status == 'issued') { ?>
                    <span class="issued-text">Issued</span>
                <?php } elseif ($status == 'rejected') { ?>
                    <button class="issue-btn" disabled>Issue Book</button>
                <?php } else { ?>
                    <form method="POST" action="" class="issue-form">
                        <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($request_id); ?>">
                        <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>">
                        <input type="hidden" name="book_num" value="<?php echo htmlspecialchars($book_num); ?>">
                        <label for="due_date_<?php echo $request_id; ?>">Due Date:</label>
                        <input type="date" name="due_date" id="due_date_<?php echo $request_id; ?>" class="due-date-input" 
       min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" 
       value="<?php echo date('Y-m-d', strtotime('+180 days')); ?>" required>
                        <button type="submit" name="issue_book" class="issue-btn" 
                                onclick="return confirm('Are you sure you want to issue this book to <?php echo htmlspecialchars(addslashes($student_name)); ?>?');">
                            Issue Book
                        </button>
                    </form>
                <?php } ?>
            </td>
        </tr>
        <?php
            }
        } else {
            echo "<tr><td colspan='11' style='text-align: center;'>No book requests found.</td></tr>";
        }
        ?>
    </tbody>
</table>

</div>
</div>

<script>
// Set minimum date to tomorrow for all due date inputs
document.addEventListener('DOMContentLoaded', function() {
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const minDate = tomorrow.toISOString().split('T')[0];
    
    const dueDateInputs = document.querySelectorAll('.due-date-input');
    dueDateInputs.forEach(input => {
        input.setAttribute('min', minDate);
    });
});

 const today = new Date();

  // Add 1 day to get tomorrow's date
  today.setDate(today.getDate() + 1);

  // Format to YYYY-MM-DD
  const yyyy = today.getFullYear();
  const mm = String(today.getMonth() + 1).padStart(2, '0'); // Months are 0-indexed
  const dd = String(today.getDate()).padStart(2, '0');

  const minDate = `${yyyy}-${mm}-${dd}`;

  // Set the min attribute of the input
  document.getElementById('futureDate').setAttribute('min', minDate);
</script>

</body>
</html>