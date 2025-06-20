<?php
require('admin/functions.php');
session_start();
$connection = mysqli_connect("localhost", "root", "");
$db = mysqli_select_db($connection, "library");

// Check if user is logged in
$is_logged_in = isset($_SESSION['Email']) && isset($_SESSION['ID']);

// Handle book request (only for logged-in users)
if (isset($_POST['request_book'])) {
    if (!$is_logged_in) {
        // Redirect to login page if not logged in
        header("Location: index.php");
        exit();
    }
    
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

        // Check current pending requests count
        $request_count_query = "SELECT COUNT(*) as request_count FROM book_request WHERE student_id = ? AND status = 'pending'";
        $stmt = mysqli_prepare($connection, $request_count_query);
        mysqli_stmt_bind_param($stmt, "s", $student_id);
        mysqli_stmt_execute($stmt);
        $request_count_result = mysqli_stmt_get_result($stmt);
        $request_count_row = mysqli_fetch_assoc($request_count_result);
        $current_requests = $request_count_row['request_count'];

        // Check current issued books count - Handle both NULL and 0 for returned field
        $issued_count_query = "SELECT COUNT(*) as issued_count FROM issued WHERE student_id = ? AND (returned IS NULL OR returned = 0)";
        $stmt = mysqli_prepare($connection, $issued_count_query);
        mysqli_stmt_bind_param($stmt, "s", $student_id);
        mysqli_stmt_execute($stmt);
        $issued_count_result = mysqli_stmt_get_result($stmt);
        $issued_count_row = mysqli_fetch_assoc($issued_count_result);
        $current_issued = $issued_count_row['issued_count'];

        // Check if user has reached the maximum limits
        if ($current_requests >= 5) {
            echo "<script>alert('You have reached the maximum limit of 5 pending requests. Please wait for some requests to be processed.');</script>";
        } elseif ($current_issued >= 7) {
            echo "<script>alert('You have reached the maximum limit of 7 issued books. Please return some books before requesting new ones.');</script>";
        } else {
            // Get book details from form
            $book_name = $_POST['book_name'];
            $book_edition = $_POST['book_edition'];
            $author_name = $_POST['author_name'];
            $book_num = $_POST['book_num'];

            // Check book availability
            $availability_query = "SELECT available_quantity FROM books WHERE book_num = ?";
            $stmt = mysqli_prepare($connection, $availability_query);
            mysqli_stmt_bind_param($stmt, "s", $book_num);
            mysqli_stmt_execute($stmt);
            $availability_result = mysqli_stmt_get_result($stmt);
            
            if (mysqli_num_rows($availability_result) > 0) {
                $availability_row = mysqli_fetch_assoc($availability_result);
                $available_quantity = $availability_row['available_quantity'];
                
                if ($available_quantity <= 0) {
                    echo "<script>alert('Sorry, this book is currently out of stock.');</script>";
                } else {
                    // Check if the book is already requested by this student (exclude rejected/cancelled requests)
                    $check_request_query = "SELECT status FROM book_request WHERE student_id=? AND book_num=? AND status NOT IN ('rejected', 'cancelled')";
                    $stmt = mysqli_prepare($connection, $check_request_query);
                    mysqli_stmt_bind_param($stmt, "ss", $student_id, $book_num);
                    mysqli_stmt_execute($stmt);
                    $check_request_result = mysqli_stmt_get_result($stmt);

                    if (mysqli_num_rows($check_request_result) > 0) {
                        $request_row = mysqli_fetch_assoc($check_request_result);
                        $request_status = $request_row['status'];
                        echo "<script>alert('You have already requested this book. Current status: " . ucfirst($request_status) . "');</script>";
                    } else {
                        // Check if the book is already issued to this student
                        $check_issued_query = "SELECT * FROM issued WHERE student_id=? AND book_num=? AND (returned IS NULL OR returned = 0)";
                        $stmt = mysqli_prepare($connection, $check_issued_query);
                        mysqli_stmt_bind_param($stmt, "ss", $student_id, $book_num);
                        mysqli_stmt_execute($stmt);
                        $check_issued_result = mysqli_stmt_get_result($stmt);

                        if (mysqli_num_rows($check_issued_result) > 0) {
                            echo "<script>alert('This book is already issued to you.');</script>";
                        } else {
                            // Insert book request into database
                            $query = "INSERT INTO book_request (user_email, student_id, student_name, book_name, book_edition, author_name, book_num, status, request_date) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";
                            $stmt = mysqli_prepare($connection, $query);
                            mysqli_stmt_bind_param($stmt, "sssssss", $user_email, $student_id, $student_name, $book_name, $book_edition, $author_name, $book_num);

                            if (mysqli_stmt_execute($stmt)) {
                                echo "<script>alert('Book request submitted successfully! You will be notified once it is processed.'); window.location.href=window.location.href;</script>";
                            } else {
                                echo "<script>alert('Error submitting request: " . mysqli_error($connection) . "');</script>";
                            }
                        }
                    }
                }
            } else {
                echo "<script>alert('Book not found in the system.');</script>";
            }
        }
    } else {
        echo "<script>alert('Student details not found.');</script>";
    }
}

// Fetch filter values from the form
$selected_faculty = isset($_GET['faculty']) ? $_GET['faculty'] : '';
$selected_semester = isset($_GET['semester']) ? $_GET['semester'] : '';
$selected_book_name = isset($_GET['book_name']) ? $_GET['book_name'] : '';
$selected_book_num = isset($_GET['book_num']) ? $_GET['book_num'] : '';
$selected_author = isset($_GET['author']) ? $_GET['author'] : '';
$selected_publication = isset($_GET['publication']) ? $_GET['publication'] : '';

// Pagination settings
$books_per_page = 20;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $books_per_page;

// Build the base query for counting total records
$count_query = "SELECT COUNT(DISTINCT book_num) as total FROM books WHERE 1=1";
$query = "SELECT DISTINCT book_num, available_quantity, book_name, book_edition, author_name, faculty, semester, publication, description, picture FROM books WHERE 1=1";

// Build parameter arrays first
$types = '';
$params = [];

if (!empty($selected_faculty)) {
    $faculty_condition = " AND REPLACE(CONCAT(',', REPLACE(faculty, ' ', ''), ','), ' ', '') LIKE CONCAT('%,', ?, ',%')";
    $query .= $faculty_condition;
    $count_query .= $faculty_condition;
    $types .= 's';
    $params[] = str_replace(' ', '', $selected_faculty);
}

if (!empty($selected_semester)) {
    $semester_condition = " AND REPLACE(CONCAT(',', REPLACE(semester, ' ', ''), ','), ' ', '') LIKE CONCAT('%,', ?, ',%')";
    $query .= $semester_condition;
    $count_query .= $semester_condition;
    $types .= 's';
    $params[] = str_replace(' ', '', $selected_semester);
}

if (!empty($selected_book_name)) {
    $book_name_condition = " AND book_name LIKE ?";
    $query .= $book_name_condition;
    $count_query .= $book_name_condition;
    $types .= 's';
    $params[] = "%$selected_book_name%";
}

if (!empty($selected_book_num)) {
    $book_num_condition = " AND book_num LIKE ?";
    $query .= $book_num_condition;
    $count_query .= $book_num_condition;
    $types .= 's';
    $params[] = "%$selected_book_num%";
}

if (!empty($selected_author)) {
    $author_condition = " AND author_name LIKE ?";
    $query .= $author_condition;
    $count_query .= $author_condition;
    $types .= 's';
    $params[] = "%$selected_author%";
}

if (!empty($selected_publication)) {
    $publication_condition = " AND publication LIKE ?";
    $query .= $publication_condition;
    $count_query .= $publication_condition;
    $types .= 's';
    $params[] = "%$selected_publication%";
}

// Get total count for pagination
$count_stmt = mysqli_prepare($connection, $count_query);
if (!empty($params)) {
    if (version_compare(PHP_VERSION, '5.6.0', '>=')) {
        mysqli_stmt_bind_param($count_stmt, $types, ...$params);
    } else {
        $bind_params = array($count_stmt, $types);
        foreach ($params as $key => $value) {
            $bind_params[] = &$params[$key];
        }
        call_user_func_array('mysqli_stmt_bind_param', $bind_params);
    }
}
mysqli_stmt_execute($count_stmt);
$count_result = mysqli_stmt_get_result($count_stmt);
$total_books = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_books / $books_per_page);

// Add LIMIT clause to main query with RANDOM ordering
$query .= " ORDER BY RAND() LIMIT ? OFFSET ?";
$types .= 'ii';
$params[] = $books_per_page;
$params[] = $offset;

// Prepare the query with placeholders
$stmt = mysqli_prepare($connection, $query);

// Bind parameters if they exist
if (!empty($params)) {
    if (version_compare(PHP_VERSION, '5.6.0', '>=')) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    } else {
        $bind_params = array($stmt, $types);
        foreach ($params as $key => $value) {
            $bind_params[] = &$params[$key];
        }
        call_user_func_array('mysqli_stmt_bind_param', $bind_params);
    }
}

// Execute the query
mysqli_stmt_execute($stmt);
$query_result = mysqli_stmt_get_result($stmt);

// Function to get book status for current user (only if logged in)
function getBookStatus($connection, $student_id, $book_num) {
    if (!$student_id) return null;
    
    // Check if book is requested (excluding rejected/cancelled)
    $request_query = "SELECT status FROM book_request WHERE student_id = ? AND book_num = ? AND status NOT IN ('rejected', 'cancelled') ORDER BY request_date DESC LIMIT 1";
    $stmt = mysqli_prepare($connection, $request_query);
    mysqli_stmt_bind_param($stmt, "ss", $student_id, $book_num);
    mysqli_stmt_execute($stmt);
    $request_result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($request_result) > 0) {
        $row = mysqli_fetch_assoc($request_result);
        return $row['status'];
    }
    
    // Check if book is issued
    $issued_query = "SELECT * FROM issued WHERE student_id = ? AND book_num = ? AND (returned IS NULL OR returned = 0)";
    $stmt = mysqli_prepare($connection, $issued_query);
    mysqli_stmt_bind_param($stmt, "ss", $student_id, $book_num);
    mysqli_stmt_execute($stmt);
    $issued_result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($issued_result) > 0) {
        return 'issued';
    }
    
    return null;
}

// Initialize user status variables for logged-in users
$current_requests = 0;
$current_issued = 0;

if ($is_logged_in) {
    $student_id = $_SESSION['ID'];
    
    // Get current pending requests count
    $request_count_query = "SELECT COUNT(*) as request_count FROM book_request WHERE student_id = ? AND status = 'pending'";
    $stmt = mysqli_prepare($connection, $request_count_query);
    mysqli_stmt_bind_param($stmt, "s", $student_id);
    mysqli_stmt_execute($stmt);
    $request_count_result = mysqli_stmt_get_result($stmt);
    $request_count_row = mysqli_fetch_assoc($request_count_result);
    $current_requests = $request_count_row['request_count'];

    // Get current issued books count
    $issued_count_query = "SELECT COUNT(*) as issued_count FROM issued WHERE student_id = ? AND (returned IS NULL OR returned = 0)";
    $stmt = mysqli_prepare($connection, $issued_count_query);
    mysqli_stmt_bind_param($stmt, "s", $student_id);
    mysqli_stmt_execute($stmt);
    $issued_count_result = mysqli_stmt_get_result($stmt);
    $issued_count_row = mysqli_fetch_assoc($issued_count_result);
    $current_issued = $issued_count_row['issued_count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_logged_in ? 'Book Requests' : 'Browse Books'; ?></title>
    <link rel="stylesheet" href="style2.css">
        <style>
        .status-indicator {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 10px;
            display: inline-block;
        }
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-approved { background-color: #d4edda; color: #155724; }
        .status-issued { background-color: #d1ecf1; color: #0c5460; }
        .status-rejected { background-color: #f8d7da; color: #721c24; }
        .request-btn:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
            opacity: 0.65;
        }
        .user-limits {
            background-color: #e9ecef;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            font-size: 14px;
        }
        .alert {
            padding: 12px;
            margin: 10px 0;
            border-radius: 4px;
            font-weight: bold;
        }
        .alert-info { background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .alert-warning { background-color: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        
        .welcome-user {
            display: block;
            text-align: center;
            padding: 20px;
            font-size: 18px;
            color: #333;
            background-color: #e9ecef;
            margin: 0;
        }
        
        h2 {
            text-align: center;
            color: #333;
            margin: 30px 0;
        }
        
        .filter-container {
            background: white;
            padding: 20px;
            margin: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .filter-container label {
            font-weight: bold;
            margin-right: 5px;
        }
        
        .filter-container select, 
        .filter-container input {
            padding: 8px 12px;
            margin: 5px 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            width: 180px;
        }
        
        .filter-container button {
            padding: 10px 20px;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            margin: 5px;
            transition: background-color 0.3s ease;
        }
        
        .filter-btn {
            background-color: #007bff;
        }
        
        .filter-btn:hover {
            background-color: #0056b3;
        }
        
        .reset-btn {
            background-color: #6c757d;
        }
        
        .reset-btn:hover {
            background-color: #545b62;
        }
        
        .filter-buttons {
            margin-top: 15px;
        }
        
        .books-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .book-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
            position: relative;
        }
        
        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        
        .book-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
            background-color: #f8f9fa;
            border: 2px dashed #dee2e6;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-size: 14px;
        }
        
        .book-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .book-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
            line-height: 1.4;
        }
        
        .book-author {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .book-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 12px;
        }
        
        .book-semester, .book-faculty {
            background-color: #e9ecef;
            padding: 4px 8px;
            border-radius: 15px;
            font-size: 11px;
            color: #495057;
        }
        
        .book-availability {
            text-align: center;
            margin: 15px 0;
            font-weight: bold;
        }
        
        .available {
            color: #28a745;
        }
        
        .unavailable {
            color: #dc3545;
        }
        
        .book-status {
            position: absolute;
            top: 15px;
            right: 15px;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-pending {
            background-color: #ffc107;
            color: #856404;
        }
        
        .status-approved {
            background-color: #28a745;
            color: white;
        }
        
        .status-issued {
            background-color: #17a2b8;
            color: white;
        }
        
        .status-rejected {
            background-color: #dc3545;
            color: white;
        }
        
        /* Pagination Styles */
        .pagination-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 30px 0;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .pagination-info {
            margin: 0 20px;
            color: #666;
            font-size: 14px;
        }
        
        .pagination {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 0;
            gap: 5px;
        }
        
        .pagination li {
            display: inline-block;
        }
        
        .pagination a {
            display: block;
            padding: 10px 15px;
            text-decoration: none;
            color: #007bff;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        .pagination a:hover {
            background-color: #e9ecef;
            border-color: #adb5bd;
        }
        
        .pagination .active a {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        .pagination .disabled a {
            color: #6c757d;
            pointer-events: none;
            background-color: #fff;
            border-color: #dee2e6;
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 0;
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
            position: relative;
        }
        
        .modal-header {
            padding: 20px;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin: 0;
        }
        
        .close {
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            color: #aaa;
        }
        
        .close:hover {
            color: #333;
        }
        
        .modal-body {
            padding: 20px;
        }
        
        .modal-book-image {
            width: 150px;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            float: left;
            margin-right: 20px;
            margin-bottom: 10px;
            background-color: #f8f9fa;
            border: 2px dashed #dee2e6;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-size: 12px;
        }
        
        .modal-book-info h3 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .modal-book-info p {
            margin: 8px 0;
            color: #555;
        }
        
        .modal-book-info strong {
            color: #333;
        }
        
        .book-description {
            clear: both;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }
        
        .modal-footer {
            padding: 20px;
            border-top: 1px solid #dee2e6;
            text-align: center;
        }
        
        .req-form {
            display: none;
        }
        
        .request-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }
        
        .request-btn:hover {
            background-color: #218838;
        }
        
        .request-btn:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }
        
        .status-message {
            color: #6c757d;
            font-weight: bold;
            padding: 12px 24px;
            background-color: #f8f9fa;
            border-radius: 6px;
        }
        
        @media (max-width: 768px) {
            .books-container {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                padding: 10px;
                gap: 15px;
            }
            
            .filter-container {
                margin: 10px;
                padding: 15px;
            }
            
            .filter-container select,
            .filter-container input {
                width: 100%;
                margin: 5px 0;
            }
            
            .modal-content {
                width: 95%;
                margin: 10% auto;
            }
            
            .modal-book-image {
                float: none;
                display: block;
                margin: 0 auto 15px;
            }
            
            .pagination-container {
                flex-direction: column;
                gap: 15px;
            }
            
            .pagination a {
                padding: 8px 12px;
                font-size: 14px;
            }
        }
</style>
</head>
<body>
    <!-- Include the navigation bar -->
    <?php include('navbar.php'); ?>

    <?php if ($is_logged_in) { ?>
        
        <div class="user-limits">
            <strong>Your Current Status:</strong> 
            Pending Requests: <?php echo $current_requests; ?>/5 | 
            Issued Books: <?php echo $current_issued; ?>/7
            
            <?php if ($current_requests >= 5): ?>
                <div class="alert alert-warning">
                    ⚠️ You have reached the maximum limit of pending requests (5/5). Please wait for some requests to be processed.
                </div>
            <?php elseif ($current_issued >= 7): ?>
                <div class="alert alert-warning">
                    ⚠️ You have reached the maximum limit of issued books (7/7). Please return some books before requesting new ones.
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    ℹ️ You can request <?php echo (5 - $current_requests); ?> more book(s) and issue <?php echo (7 - $current_issued); ?> more book(s).
                </div>
            <?php endif; ?>
        </div>
        
        <h2>Available Books</h2>
    <?php } else { ?>
        <div class="guest-welcome">
            <h2>📚 Browse Our Book Collection</h2>
            <div class="alert alert-info">
                <strong>Welcome, Guest!</strong> You can browse our collection of books below. 
                <a href="index.php" style="color: #007bff; text-decoration: underline;">Login</a> to request books from our library.
            </div>
        </div>
    <?php } ?>

    <!-- Filter Form -->
    <div class="filter-container">
        <form method="GET" action="" id="filterForm">
            <input type="hidden" name="page" value="1">
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

            <label for="book_name">Book Name:</label>
            <input type="text" name="book_name" id="book_name" value="<?php echo htmlspecialchars($selected_book_name); ?>" placeholder="Enter book name">

            <label for="book_num">Book Number:</label>
            <input type="text" name="book_num" id="book_num" value="<?php echo htmlspecialchars($selected_book_num); ?>" placeholder="Enter book number">

            <label for="author">Author:</label>
            <input type="text" name="author" id="author" value="<?php echo htmlspecialchars($selected_author); ?>" placeholder="Enter author name">

            <label for="publication">Publication:</label>
            <input type="text" name="publication" id="publication" value="<?php echo htmlspecialchars($selected_publication); ?>" placeholder="Enter publication">

            <div class="filter-buttons">
                <button type="submit" class="filter-btn">Filter</button>
                <button type="button" class="reset-btn" onclick="resetFilters()">Reset Filters</button>
            </div>
        </form>
    </div>

    <!-- Pagination Info -->
    <?php if ($total_books > 0) { ?>
        <div class="pagination-container">
            <div class="pagination-info">
                Showing <?php echo (($current_page - 1) * $books_per_page + 1); ?> to 
                <?php echo min($current_page * $books_per_page, $total_books); ?> of 
                <?php echo $total_books; ?> books
            </div>
        </div>
    <?php } ?>

    <!-- Books Cards Container -->
    <div class="books-container">
        <?php
        if (mysqli_num_rows($query_result) > 0) {
            while ($row = mysqli_fetch_assoc($query_result)) {
                $bname = $row['book_name'];
                $bedition = $row['book_edition'];
                $author = $row['author_name'];
                $faculty = $row['faculty'];
                $semester = $row['semester'];
                $book_num = $row['book_num'];
                $available_quantity = $row['available_quantity'];
                $publication = $row['publication'];
                $description = isset($row['description']) ? $row['description'] : '';
                $picture = isset($row['picture']) ? $row['picture'] : '';

                // Get book status for current user (only if logged in)
                $status = $is_logged_in ? getBookStatus($connection, $_SESSION['ID'], $book_num) : null;
        ?>
            <div class="book-card" onclick="openModal('<?php echo htmlspecialchars($book_num); ?>')">
                <?php if ($is_logged_in && !empty($status)) { ?>
                    <div class="book-status status-<?php echo htmlspecialchars($status); ?>">
                        <?php 
                        switch($status) {
                            case 'pending':
                                echo '⏳ Requested';
                                break;
                            case 'approved':
                                echo '✅ Approved';
                                break;
                            case 'issued':
                                echo '📖 Issued to You';
                                break;
                            case 'rejected':
                                echo '❌ Rejected';
                                break;
                            default:
                                echo ucfirst(htmlspecialchars($status));
                        }
                        ?>
                    </div>
                <?php } ?>
                
                <div class="book-image">
                    <?php if (!empty($picture) && file_exists("./admin/upload/{$picture}")) { ?>
                        <img src="./admin/upload/<?php echo htmlspecialchars($picture); ?>" alt="<?php echo htmlspecialchars($bname); ?>">
                    <?php } else { ?>
                       <img src="./admin/upload/placeholder.png" alt="No Image Available">
                    <?php } ?>
                </div>
                
                <div class="book-title"><?php echo htmlspecialchars($bname); ?></div>
                <div class="book-author">by <?php echo htmlspecialchars($author); ?></div>
                
                <div class="book-details">
                    <span class="book-semester">Sem <?php echo htmlspecialchars($semester); ?></span>
                    <span class="book-faculty"><?php echo htmlspecialchars($faculty); ?></span>
                </div>
                
                <div class="book-availability <?php echo ($available_quantity > 0) ? 'available' : 'unavailable'; ?>">
                    <?php echo ($available_quantity > 0) ? $available_quantity . ' Available' : 'Out of Stock'; ?>
                </div>
            </div>

            <!-- Modal for each book -->
            <div id="modal_<?php echo htmlspecialchars($book_num); ?>" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title"><?php echo htmlspecialchars($bname); ?></h2>
                        <span class="close" onclick="closeModal('<?php echo htmlspecialchars($book_num); ?>')">&times;</span>
                    </div>
                    <div class="modal-body">
                        <div class="modal-book-image">
                            <?php if (!empty($picture) && file_exists("./admin/upload/{$picture}")) { ?>
                                <img src="./admin/upload/<?php echo htmlspecialchars($picture); ?>" alt="<?php echo htmlspecialchars($bname); ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                            <?php } else { ?>
                                <img src="./admin/upload/placeholder.png" alt="No Image Available" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                            <?php } ?>
                        </div>
                        <div class="modal-book-info">
                            <h3><?php echo htmlspecialchars($bname); ?></h3>
                            <p><strong>Author:</strong> <?php echo htmlspecialchars($author); ?></p>
                            <p><strong>Edition:</strong> <?php echo htmlspecialchars($bedition); ?></p>
                            <p><strong>Book Number:</strong> <?php echo htmlspecialchars($book_num); ?></p>
                            <p><strong>Faculty:</strong> <?php echo htmlspecialchars($faculty); ?></p>
                            <p><strong>Semester:</strong> <?php echo htmlspecialchars($semester); ?></p>
                            <p><strong>Publication:</strong> <?php echo htmlspecialchars($publication); ?></p>
                            <p><strong>Available Quantity:</strong> 
                                <span class="<?php echo ($available_quantity > 0) ? 'available' : 'unavailable'; ?>">
                                    <?php echo htmlspecialchars($available_quantity); ?>
                                </span>
                            </p>
                            
                            <?php if ($is_logged_in && !empty($status)) { ?>
                                <p><strong>Your Status:</strong> 
                                    <span class="status-indicator status-<?php echo htmlspecialchars($status); ?>">
                                        <?php 
                                        switch($status) {
                                            case 'pending':
                                                echo '⏳ Request Pending';
                                                break;
                                            case 'approved':
                                                echo '✅ Request Approved - Visit Library';
                                                break;
                                            case 'issued':
                                                echo '📖 Book Issued to You';
                                                break;
                                            case 'rejected':
                                                echo '❌ Request Rejected';
                                                break;
                                            default:
                                                echo ucfirst(htmlspecialchars($status));
                                        }
                                        ?>
                                    </span>
                                </p>
                            <?php } ?>
                        </div>
                        <div class="book-description">
                            <h4>Description:</h4>
                            <p><?php 
                                if (!empty($description)) {
                                    echo htmlspecialchars($description);
                                } else {
                                    echo "No description available.";
                                }
                            ?></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <?php if (!$is_logged_in) { ?>
                            <!-- Guest user - show login button -->
                            <div class="guest-action">
                                <p style="margin-bottom: 10px; color: #666;">
                                    <strong>Want to request this book?</strong>
                                </p>
                                <a href="index.php" class="request-btn" style="text-decoration: none; display: inline-block; text-align: center;">
                                    🔐 Login to Request Book
                                </a>
                            </div>
                        <?php } else {
                            // Logged-in user - show request button with validation
                            $can_request = true;
                            $button_message = "Request Book";
                            $disable_reason = "";
                            
                            // Check various conditions
                            if (!empty($status)) {
                                $can_request = false;
                                switch($status) {
                                    case 'pending':
                                        $disable_reason = "Request already submitted and pending";
                                        break;
                                    case 'approved':
                                        $disable_reason = "Request approved - Visit library to collect";
                                        break;
                                    case 'issued':
                                        $disable_reason = "Book already issued to you";
                                        break;
                                    case 'rejected':
                                        $disable_reason = "Previous request was rejected";
                                        break;
                                }
                            } elseif ($available_quantity <= 0) {
                                $can_request = false;
                                $disable_reason = "Book is out of stock";
                            } elseif ($current_requests >= 5) {
                                $can_request = false;
                                $disable_reason = "Maximum pending requests limit reached (5/5)";
                            } elseif ($current_issued >= 7) {
                                $can_request = false;
                                $disable_reason = "Maximum issued books limit reached (7/7)";
                            }
                            
                            if ($can_request) { ?>
                                <form method="POST" action="" class='req-form' style="display:none;" id="request_form_<?php echo htmlspecialchars($book_num); ?>">
                                    <input type="hidden" name="book_name" value="<?php echo htmlspecialchars($bname); ?>">
                                    <input type="hidden" name="book_edition" value="<?php echo htmlspecialchars($bedition); ?>">
                                    <input type="hidden" name="author_name" value="<?php echo htmlspecialchars($author); ?>">
                                    <input type="hidden" name="book_num" value="<?php echo htmlspecialchars($book_num); ?>">
                                    <input type="hidden" name="request_book" value="1">
                                </form>
                                <button type="button" class="request-btn" onclick="confirmRequest('<?php echo htmlspecialchars(addslashes($bname)); ?>', '<?php echo htmlspecialchars($book_num); ?>')">
                                    📚 Request Book
                                </button>
                            <?php } else { ?>
                                <button type="button" class="request-btn" disabled title="<?php echo htmlspecialchars($disable_reason); ?>">
                                    <?php echo htmlspecialchars($disable_reason); ?>
                                </button>
                            <?php } 
                        } ?>
                    </div>
                </div>
            </div>
        <?php
            }
        } else {
            echo "<div style='text-align: center; padding: 50px; color: #666;'>";
            echo "<h3>📚 No books found matching your criteria.</h3>";
            echo "<p>Try adjusting your search filters or browse all available books.</p>";
            echo "</div>";
        }
        ?>
    </div>
    
    <!-- Pagination -->
    <?php if ($total_pages > 1) { ?>
        <div class="pagination-container">
            <ul class="pagination">
                <!-- Previous Page -->
                <li class="<?php echo ($current_page <= 1) ? 'disabled' : ''; ?>">
                    <a href="<?php echo ($current_page > 1) ? buildPaginationUrl($current_page - 1) : '#'; ?>">
                        &laquo; Previous
                    </a>
                </li>

                <?php
                // Calculate pagination range
                $start_page = max(1, $current_page - 2);
                $end_page = min($total_pages, $current_page + 2);

                // Show first page if not in range
                if ($start_page > 1) {
                    echo '<li><a href="' . buildPaginationUrl(1) . '">1</a></li>';
                    if ($start_page > 2) {
                        echo '<li class="disabled"><a href="#">...</a></li>';
                    }
                }

                // Show page numbers in range
                for ($i = $start_page; $i <= $end_page; $i++) {
                    $active_class = ($i == $current_page) ? 'active' : '';
                    echo '<li class="' . $active_class . '">';
                    echo '<a href="' . buildPaginationUrl($i) . '">' . $i . '</a>';
                    echo '</li>';
                }

                // Show last page if not in range
                if ($end_page < $total_pages) {
                    if ($end_page < $total_pages - 1) {
                        echo '<li class="disabled"><a href="#">...</a></li>';
                    }
                    echo '<li><a href="' . buildPaginationUrl($total_pages) . '">' . $total_pages . '</a></li>';
                }
                ?>

                <!-- Next Page -->
                <li class="<?php echo ($current_page >= $total_pages) ? 'disabled' : ''; ?>">
                    <a href="<?php echo ($current_page < $total_pages) ? buildPaginationUrl($current_page + 1) : '#'; ?>">
                        Next &raquo;
                    </a>
                </li>
            </ul>
        </div>
    <?php } ?>

    <script>
    function openModal(bookNum) {
        document.getElementById("modal_" + bookNum).style.display = "block";
        document.body.style.overflow = "hidden"; // Prevent background scrolling
    }

    function closeModal(bookNum) {
        document.getElementById("modal_" + bookNum).style.display = "none";
        document.body.style.overflow = "auto"; // Restore background scrolling
    }

    // Close modal when clicking outside of it
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = "none";
            document.body.style.overflow = "auto";
        }
    }

    function confirmRequest(bookName, bookNum) {
        if (confirm("Are you sure you want to request the book: " + bookName + "?")) {
            document.getElementById("request_form_" + bookNum).submit();
        }
    }

    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                if (modal.style.display === 'block') {
                    modal.style.display = 'none';
                    document.body.style.overflow = "auto";
                }
            });
        }
    });

    // Reset filter function
    function resetFilters() {
        // Clear all form fields
        document.getElementById('faculty').value = '';
        document.getElementById('semester').value = '';
        document.getElementById('book_name').value = '';
        document.getElementById('book_num').value = '';
        document.getElementById('author').value = '';
        document.getElementById('publication').value = '';
        
        // Redirect to the same page without query parameters
        window.location.href = window.location.pathname;
    }
    </script>

<?php
// Function to build pagination URLs with current filters
function buildPaginationUrl($page) {
    $params = $_GET;
    $params['page'] = $page;
    return '?' . http_build_query($params);
}
?>
</body>
</html>