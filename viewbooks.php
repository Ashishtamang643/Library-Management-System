<?php
require('admin/functions.php');
session_start();
$connection = mysqli_connect("localhost", "root", "");
$db = mysqli_select_db($connection, "library");

// Check if user is logged in
if (!isset($_SESSION['Email']) || !isset($_SESSION['ID'])) {
    echo "<script>alert('Please login to continue.'); window.location.href='login.php';</script>";
    exit();
}

if (isset($_POST['request_book'])) {
    $user_email = $_SESSION['Email']; // Get the user's email from the session
    $student_id = $_SESSION['ID']; // Get the student_id from the session

    // Get student name from database
    $student_query = "SELECT name FROM users WHERE ID = ?";
    $stmt = mysqli_prepare($connection, $student_query);
    mysqli_stmt_bind_param($stmt, "s", $student_id);
    mysqli_stmt_execute($stmt);
    $student_result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($student_result) > 0) {
        $student_row = mysqli_fetch_assoc($student_result);
        $student_name = $student_row['name'];

        // Get book details from form
        $book_name = $_POST['book_name'];
        $book_edition = $_POST['book_edition'];
        $author_name = $_POST['author_name'];
        $book_num = $_POST['book_num'];

        // Check if the book is already requested by this student
        $check_request_query = "SELECT * FROM book_request WHERE student_id=? AND book_num=?";
        $stmt = mysqli_prepare($connection, $check_request_query);
        mysqli_stmt_bind_param($stmt, "ss", $student_id, $book_num);
        mysqli_stmt_execute($stmt);
        $check_request_result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($check_request_result) > 0) {
            echo "<script>alert('You have already requested this book.');</script>";
        } else {
            // Check if the book is already issued to this student
            $check_issued_query = "SELECT * FROM issued WHERE student_id=? AND book_num=?";
            $stmt = mysqli_prepare($connection, $check_issued_query);
            mysqli_stmt_bind_param($stmt, "ss", $student_id, $book_num);
            mysqli_stmt_execute($stmt);
            $check_issued_result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($check_issued_result) > 0) {
                echo "<script>alert('This book is already issued to you.');</script>";
            } else {
                // Insert book request into database
                $query = "INSERT INTO book_request (user_email, student_id, student_name, book_name, book_edition, author_name, book_num, status) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')";
                $stmt = mysqli_prepare($connection, $query);
                mysqli_stmt_bind_param($stmt, "sssssss", $user_email, $student_id, $student_name, $book_name, $book_edition, $author_name, $book_num);

                if (mysqli_stmt_execute($stmt)) {
                    echo "<script>alert('Book request submitted successfully!'); window.location.href=window.location.href;</script>";
                } else {
                    echo "<script>alert('Error: " . mysqli_error($connection) . "');</script>";
                }
            }
        }
    } else {
        echo "<script>alert('Student details not found.');</script>";
    }
}

// Fetch filter values from the form
$selected_faculty = isset($_GET['faculty']) ? $_GET['faculty'] : '';
$selected_semester = isset($_GET['semester']) ? $_GET['semester'] : '';

// Build the query based on filters
$query = "SELECT DISTINCT book_num, available_quantity, book_name, book_edition, author_name, faculty, semester FROM books WHERE 1=1";
if (!empty($selected_faculty)) {
    $query .= " AND faculty = ?";
}
if (!empty($selected_semester)) {
    $query .= " AND semester = ?";
}

// Prepare the query with placeholders
$stmt = mysqli_prepare($connection, $query);

// Bind parameters if they exist
if (!empty($selected_faculty) && !empty($selected_semester)) {
    mysqli_stmt_bind_param($stmt, "ss", $selected_faculty, $selected_semester);
} elseif (!empty($selected_faculty)) {
    mysqli_stmt_bind_param($stmt, "s", $selected_faculty);
} elseif (!empty($selected_semester)) {
    mysqli_stmt_bind_param($stmt, "s", $selected_semester);
}

// Execute the query
mysqli_stmt_execute($stmt);
$query_result = mysqli_stmt_get_result($stmt);

// Check the issued table and update book_request table
$student_id = $_SESSION['ID'];
$check_issued_query = "SELECT br.request_id, br.book_name, br.book_edition, br.author_name 
                       FROM book_request br 
                       JOIN issued i ON br.book_name = i.book_name 
                       AND br.author_name = i.book_author 
                       AND br.book_num = i.book_num
                       WHERE br.status = 'pending' 
                       AND i.student_id = ?";
$stmt = mysqli_prepare($connection, $check_issued_query);
mysqli_stmt_bind_param($stmt, "s", $student_id);
mysqli_stmt_execute($stmt);
$check_issued_result = mysqli_stmt_get_result($stmt);

while ($row = mysqli_fetch_assoc($check_issued_result)) {
    $request_id = $row['request_id'];
    // Update the status to "issued" in the book_request table
    $update_query = "UPDATE book_request SET status = 'issued' WHERE request_id = ?";
    $stmt = mysqli_prepare($connection, $update_query);
    mysqli_stmt_bind_param($stmt, "s", $request_id);
    mysqli_stmt_execute($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Requests</title>
    <link rel="stylesheet" href="style1.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .navigation-bar {
            background-color: #333;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navigation-bar a {
            color: white;
            text-decoration: none;
        }
        .profile-logout {
            display: flex;
            align-items: center;
        }
        .profile-logout h3 {
            margin: 0 20px;
        }
        .welcome-user {
            display: block;
            padding: 20px;
            background-color: #e2e2e2;
            margin-bottom: 20px;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #333;
            color: white;
        }
        tr {
            border-bottom: 1px solid #ccc;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .request-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
        .request-btn:hover {
            background-color: #45a049;
        }
        .status-message {
            color: #333;
            font-weight: bold;
        }
        .filter-container {
            text-align: center;
            margin: 20px 0;
        }
        .filter-container select {
            padding: 8px;
            margin: 0 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        .filter-container button {
            padding: 8px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .filter-container button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <!-- Include the navigation bar -->
    <?php include('navbar.php'); ?>

    <span class="welcome-user">Welcome: <?php echo $_SESSION['Email']; ?></span>
    <h2 style="margin-top: 20px; text-align: center;">Available Books</h2>

    <!-- Filter Form -->
    <div class="filter-container">
        <form method="GET" action="">
            <label for="faculty">Faculty:</label>
            <select name="faculty" id="faculty">
                <option value="">All</option>
                <option value="Bsc.Csit" <?php echo ($selected_faculty == 'Bsc.Csit') ? 'selected' : ''; ?>>Bsc.Csit</option>
                <option value="BIM" <?php echo ($selected_faculty == 'BIM') ? 'selected' : ''; ?>>BIM</option>
                <option value="BCA" <?php echo ($selected_faculty == 'BCA') ? 'selected' : ''; ?>>BCA</option>
                <option value="BBM" <?php echo ($selected_faculty == 'BBM') ? 'selected' : ''; ?>>BBM</option>
            </select>

            <label for="semester">Semester:</label>
            <select name="semester" id="semester">
                <option value="">All</option>
                <?php for ($i = 1; $i <= 8; $i++) { ?>
                    <option value="<?php echo $i; ?>" <?php echo ($selected_semester == $i) ? 'selected' : ''; ?>>
                        Semester <?php echo $i; ?>
                    </option>
                <?php } ?>
            </select>

            <button type="submit">Filter</button>
        </form>
    </div>

    <!-- Books Table -->
    <table>
        <thead>
            <tr>
                <th>Book Name</th>
                <th>Edition</th>
                <th>Book Num</th>
                <th>Author</th>
                <th>Faculty</th>
                <th>Available Qty</th>
                <th>Semester</th>
                <th>Action / Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = mysqli_fetch_assoc($query_result)) {
                $bname = $row['book_name'];
                $bedition = $row['book_edition'];
                $author = $row['author_name'];
                $faculty = $row['faculty'];
                $semester = $row['semester'];
                $book_num = $row['book_num'];
                $available_quantity=$row['available_quantity'];

                // Check if the user has already requested this book with the same edition and author
                $student_id = $_SESSION['ID'];
                $check_query = "SELECT status FROM book_request 
                                WHERE student_id = ? 
                                AND book_num = ?";
                $stmt = mysqli_prepare($connection, $check_query);
                mysqli_stmt_bind_param($stmt, "ss", $student_id, $book_num);
                mysqli_stmt_execute($stmt);
                $check_result = mysqli_stmt_get_result($stmt);
                $status = "";
                if (mysqli_num_rows($check_result) > 0) {
                    $status_row = mysqli_fetch_assoc($check_result);
                    $status = $status_row['status'];
                }

                // Check if the book is issued to the current user
                $check_issued_query = "SELECT * FROM issued 
                                       WHERE student_id = ? 
                                       AND book_num = ?";
                $stmt = mysqli_prepare($connection, $check_issued_query);
                mysqli_stmt_bind_param($stmt, "ss", $student_id, $book_num);
                mysqli_stmt_execute($stmt);
                $check_issued_result = mysqli_stmt_get_result($stmt);
                if (mysqli_num_rows($check_issued_result) > 0) {
                    $status = "issued";
                }
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($bname); ?></td>
                    <td><?php echo htmlspecialchars($bedition); ?></td>
                    <td><?php echo htmlspecialchars($book_num); ?></td>
                    <td><?php echo htmlspecialchars($author); ?></td>
                    <td><?php echo htmlspecialchars($faculty); ?></td>
                    <td><?php echo htmlspecialchars($available_quantity); ?></td>
                    <td><?php echo htmlspecialchars($semester); ?></td>
                    <td>
                        <?php if (empty($status)) { ?>
                            <!-- Show the "Request" button if the book hasn't been requested -->
                            <form method="POST" action="" style="display:inline;" id="request_form_<?php echo htmlspecialchars($book_num); ?>">
                                <input type="hidden" name="book_name" value="<?php echo htmlspecialchars($bname); ?>">
                                <input type="hidden" name="book_edition" value="<?php echo htmlspecialchars($bedition); ?>">
                                <input type="hidden" name="author_name" value="<?php echo htmlspecialchars($author); ?>">
                                <input type="hidden" name="book_num" value="<?php echo htmlspecialchars($book_num); ?>">
                                <input type="hidden" name="request_book" value="1">
                                <button type="button" class="request-btn" onclick="confirmRequest('<?php echo htmlspecialchars(addslashes($bname)); ?>', '<?php echo htmlspecialchars($book_num); ?>')">Request</button>
                            </form>
                        <?php } else { ?>
                            <!-- Show the status message if the book has already been requested or issued -->
                            <span class="status-message">Status: <?php echo ucfirst(htmlspecialchars($status)); ?></span>
                        <?php } ?>
                    </td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>

    <script>
    function confirmRequest(bookName, bookNum) {
        // Ask the user for confirmation
        if (confirm("Are you sure you want to request the book: " + bookName + "?")) {
            // If the user confirms, submit the form associated with the book
            document.getElementById("request_form_" + bookNum).submit();
        }
    }
    </script>

</body>
</html>