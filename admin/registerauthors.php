<?php
require('functions.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION['Name'])) {
    echo "<script>alert('Please login to continue.'); window.location.href='index.php';</script>";
    exit();
}

// Connect to database
$connection = mysqli_connect("localhost", "root", "");
if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}
$db = mysqli_select_db($connection, "library");

// Clean SQL query to avoid blank authors
$query = "SELECT DISTINCT TRIM(author_name) AS author_name FROM books WHERE author_name IS NOT NULL AND author_name != '' ORDER BY author_name ASC";
$query_run = mysqli_query($connection, $query) or die("Query Failed: " . mysqli_error($connection));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Authors</title>
    <link rel="stylesheet" href="../style2.css">
    <style>
        td ul {
            padding-left: 20px;
        }
    </style>
</head>
<body>
<?php include('adminnavbar.php'); ?>

<div class="main">
<?php include('sidebar.php'); ?>

<div class="container">
    <h2 class="h2-register-header">Registered Authors</h2>
    
    <?php
    $num = mysqli_num_rows($query_run);
    // echo "<p><strong>$num authors found.</strong></p>";
    ?>

    <table>
        <thead>
            <tr>
                <th>Authors</th>
            </tr>
        </thead>
        <tbody>
        <?php
        while ($row = mysqli_fetch_assoc($query_run)) {
            $author = $row['author_name'];
            $uniqueId = md5($author); // Unique ID for each row
        ?>
            <!-- Clickable Author Row -->
            <tr onclick="toggleBooks('<?php echo $uniqueId; ?>')" style="cursor:pointer;">
                <td><?php echo htmlspecialchars($author); ?></td>
            </tr>

            <!-- Hidden Book List Row -->
            <tr id="books-<?php echo $uniqueId; ?>" style="display:none;">
                <td>
                    <ul>
                        <?php
                        $book_query = "SELECT book_name FROM books WHERE TRIM(author_name) = '" . mysqli_real_escape_string($connection, $author) . "'";
                        $book_result = mysqli_query($connection, $book_query);
                        while ($book = mysqli_fetch_assoc($book_result)) {
                            echo "<li>" . htmlspecialchars($book['book_name']) . "</li>";
                        }
                        ?>
                    </ul>
                </td>
            </tr>
        <?php
        }
        ?>
        </tbody>
    </table>
</div>
</div>

<script>
function toggleBooks(id) {
    const row = document.getElementById("books-" + id);
    row.style.display = (row.style.display === "none") ? "table-row" : "none";
}
</script>

</body>
</html>
