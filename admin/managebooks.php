<?php
require('functions.php');
session_start();
if (!isset($_SESSION['Name'])) {
    echo "<script>alert('Please login to continue.'); window.location.href='index.php';</script>";
    exit();
}
$connection = mysqli_connect("localhost", "root", "");
$db = mysqli_select_db($connection, "library");

// Handle book deletion
if (isset($_GET['delete_id'])) {
    $book_num = $_GET['delete_id'];
    
    // First, check if there are any issued copies of this book
    $check_query = "SELECT COUNT(*) as issued_count FROM issued WHERE book_num = '$book_num' AND returned IS NULL";
    $check_result = mysqli_query($connection, $check_query);
    $check_data = mysqli_fetch_assoc($check_result);
    
    if ($check_data['issued_count'] > 0) {
        echo "<script>alert('Cannot delete this book as there are issued copies. Please collect all issued copies first.'); window.location.href='managebooks.php';</script>";
    } else {
        // Delete the book
        $delete_query = "DELETE FROM books WHERE book_num = '$book_num'";
        $delete_result = mysqli_query($connection, $delete_query);
        
        if ($delete_result) {
            echo "<script>alert('Book deleted successfully.'); window.location.href='managebooks.php';</script>";
        } else {
            echo "<script>alert('Error deleting book.'); window.location.href='managebooks.php';</script>";
        }
    }
    exit();
}

// Fetch filter values from the form
$selected_faculty = isset($_GET['faculty']) ? $_GET['faculty'] : '';
$selected_semester = isset($_GET['semester']) ? $_GET['semester'] : '';
$search_book_name = isset($_GET['book_name']) ? $_GET['book_name'] : '';
$search_author = isset($_GET['author']) ? $_GET['author'] : '';
$search_book_num = isset($_GET['book_num']) ? $_GET['book_num'] : '';
$search_publication = isset($_GET['publication']) ? $_GET['publication'] : '';

// Build the query based on filters
$query = "SELECT * FROM books WHERE 1=1";
if (!empty($selected_faculty)) {
    $query .= " AND faculty = '$selected_faculty'";
}
if (!empty($selected_semester)) {
    $query .= " AND semester = '$selected_semester'";
}
if (!empty($search_book_name)) {
    $query .= " AND book_name LIKE '%$search_book_name%'";
}
if (!empty($search_author)) {
    $query .= " AND author_name LIKE '%$search_author%'";
}
if (!empty($search_book_num)) {
    $query .= " AND book_num LIKE '%$search_book_num%'";
}
if (!empty($search_publication)) {
    $query .= " AND publication LIKE '%$search_publication%'";
}

// Count total books based on the filtered query
$count_query = str_replace("SELECT *", "SELECT COUNT(*) as total_books", $query);
$count_result = mysqli_query($connection, $count_query);
$total_books_row = mysqli_fetch_assoc($count_result);
$total_books = $total_books_row['total_books'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books</title>
    <link rel="stylesheet" href="../style2.css">
    <style>
        /* Previous styles remain the same */
        
        /* Enhanced Filter Styling */
        .filter-container {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin: 20px auto;
            max-width: 900px;
            transition: all 0.3s ease;
        }

        .filter-container:hover {
            box-shadow: 0 6px 8px rgba(0,0,0,0.15);
        }

        .filter-container form {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 15px;
        }

        .filter-container label {
            color: #333;
            font-weight: bold;
            margin-right: 8px;
        }

        .filter-container select,
        .filter-container input[type="text"] {
            padding: 10px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.3s ease;
            outline: none;
            width: 180px;
        }

        .filter-container select:focus,
        .filter-container input[type="text"]:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
        }

        .filter-container button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .filter-container button:hover {
            background-color: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
        }

        .filter-container button:active {
            transform: translateY(1px);
            box-shadow: 0 1px 2px rgba(0,0,0,0.2);
        }

        .total-books-container {
            text-align: center;
            margin: 15px 0;
            font-size: 16px;
            color: #555;
            background-color: #f1f1f1;
            padding: 10px;
            border-radius: 6px;
            max-width: 300px;
            margin-left: auto;
            margin-right: auto;
        }

        .editbooks-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            text-decoration: none;
        }
        .delete-btn {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            text-decoration: none;
        }
        .editbooks-btn:hover {
            background-color: #45a049;
        }
        .delete-btn:hover {
            background-color: #d32f2f;
        }

        
    </style>
</head>
<body>

<?php include('adminnavbar.php'); ?>


<div class="main">
<?php include('sidebar.php'); ?>

    <div class="container">


    <h2 class="h2-register-header">Manage Books</h2>

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

            <label for="book_name">Book Name:</label>
            <input type="text" name="book_name" id="book_name" value="<?php echo htmlspecialchars($search_book_name); ?>" placeholder="Search book name">

            <label for="author">Author:</label>
            <input type="text" name="author" id="author" value="<?php echo htmlspecialchars($search_author); ?>" placeholder="Search author">

            <label for="book_num">Book Number:</label>
            <input type="text" name="book_num" id="book_num" value="<?php echo htmlspecialchars($search_book_num); ?>" placeholder="Search book number">

            <label for="publication">Publication:</label>
            <input type="text" name="publication" id="publication" value="<?php echo htmlspecialchars($search_publication); ?>" placeholder="Search publication">

            <button type="submit">Filter</button>
        </form>
    </div>

    <!-- Total Books Count -->
    <div class="total-books-container">
        Total Books: <?php echo $total_books; ?>
    </div>

    <!-- Books Table -->
    <table>
        <thead>
            <tr>
                <th>Book Name</th>
                <th>Book Num</th>
                <th>Edition</th>
                <th>Author</th>
                <th>Publication</th>
                <th>Faculty</th>
                <th>Semester</th>
                <th>Total Quantity</th>
                <th>Available Quantity</th>
                <th>Issued Quantity</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query_run = mysqli_query($connection, $query);
            while ($row = mysqli_fetch_assoc($query_run)) {
                $bname = $row['book_name'];
                $bnum = $row['book_num'];
                $bedition = $row['book_edition'];
                $author = $row['author_name'];
                $publication = $row['publication'];
                $faculty = $row['faculty'];
                $semester = $row['semester'];
                $total_quantity = $row['total_quantity'];
                $available_quantity = $row['available_quantity'];
                $issued_quantity = $row['total_quantity'] - $row['available_quantity'];
            ?>
                <tr>
                    <td><?php echo $bname; ?></td>
                    <td><?php echo $bnum; ?></td>
                    <td><?php echo $bedition; ?></td>
                    <td><?php echo $author; ?></td>
                    <td><?php echo $publication; ?></td>
                    <td><?php echo $faculty; ?></td>
                    <td><?php echo $semester; ?></td>
                    <td><?php echo $total_quantity; ?></td>
                    <td><?php echo $available_quantity; ?></td>
                    <td><?php echo $issued_quantity; ?></td>
                    <td>
                        <a href="editbooks.php?bn=<?php echo $row['book_num']; ?>" class="editbooks-btn">Edit</a>
                        <a href="managebooks.php?delete_id=<?php echo $row['book_num']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this book?')">Delete</a>
                    </td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
            
    </div>
</div>
</body>
</html>