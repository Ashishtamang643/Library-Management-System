<?php
    require('functions.php');
    session_start();
    $connection = mysqli_connect("localhost","root","");
    $db = mysqli_select_db($connection,"library");

    // Initialize filter variables
    $book_name_filter = isset($_GET['book_name']) ? $_GET['book_name'] : '';
    $publication_filter = isset($_GET['publication']) ? $_GET['publication'] : '';
    $author_filter = isset($_GET['author']) ? $_GET['author'] : '';
    $faculty_filter = isset($_GET['faculty']) ? $_GET['faculty'] : '';
    $semester_filter = isset($_GET['semester']) ? $_GET['semester'] : '';

    // Base query
    $query = "SELECT * FROM books WHERE 1=1";

    // Add filters to the query
    if (!empty($book_name_filter)) {
        $query .= " AND book_name LIKE '%$book_name_filter%'";
    }
    if (!empty($publication_filter)) {
        $query .= " AND publication LIKE '%$publication_filter%'";
    }
    if (!empty($author_filter)) {
        $query .= " AND author_name LIKE '%$author_filter%'";
    }
    if (!empty($faculty_filter)) {
        $query .= " AND faculty = '$faculty_filter'";
    }
    if (!empty($semester_filter)) {
        $query .= " AND semester = '$semester_filter'";
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Books</title>
    <link rel="stylesheet" href="../style1.css">
    <style>
        .filter-container {
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }

        .filter-container label {
            font-weight: bold;
        }

        .filter-container input,
        .filter-container select {
            padding: 5px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .filter-container button {
            padding: 5px 10px;
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
    <?php include('adminnavbar.php'); ?>

    <h2 class="h2-register-header">Registered Books</h2>

    <!-- Filter Section -->
    <div class="filter-container">
        <form method="GET" action="">
            <label for="book_name">Book Name:</label>
            <input type="text" id="book_name" name="book_name" value="<?php echo $book_name_filter; ?>">

            <label for="publication">Publication:</label>
            <input type="text" id="publication" name="publication" value="<?php echo $publication_filter; ?>">

            <label for="author">Author:</label>
            <input type="text" id="author" name="author" value="<?php echo $author_filter; ?>">

            <label for="faculty">Faculty:</label>
            <select name="faculty" id="faculty">
                <option value="">All</option>
                <option value="BSc.CSIT" <?php echo ($faculty_filter == 'BSc.CSIT') ? 'selected' : ''; ?>>BSc.CSIT</option>
                <option value="BIM" <?php echo ($faculty_filter == 'BIM') ? 'selected' : ''; ?>>BIM</option>
                <option value="BCA" <?php echo ($faculty_filter == 'BCA') ? 'selected' : ''; ?>>BCA</option>
                <option value="BBM" <?php echo ($faculty_filter == 'BBM') ? 'selected' : ''; ?>>BBM</option>
            </select>

            <label for="semester">Semester:</label>
            <select name="semester" id="semester">
                <option value="">All</option>
                <?php for ($i = 1; $i <= 8; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php echo ($semester_filter == $i) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                <?php endfor; ?>
            </select>

            <button type="submit">Apply Filters</button>
            <button type="button" onclick="window.location.href = window.location.pathname;">Clear Filters</button>
        </form>
    </div>

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
          </tr>
        </thead>
        <?php
             $query_run = mysqli_query($connection,$query);
             while($row = mysqli_fetch_assoc($query_run))
             {
                $bname = $row['book_name'];
                $bnum = $row['book_num'];
                $bedition = $row['book_edition'];
                $author = $row['author_name'];
                $publication = $row['publication'];
                $faculty = $row['faculty'];
                $semester = $row['semester'];
                $total_quantity = $row['total_quantity'];
                $available_quantity = $row['available_quantity'];
                $issued_quantity = $total_quantity - $available_quantity;
                ?>
        <tr>
            <td><?php echo $bname;?></td>
            <td><?php echo $bnum;?></td>
            <td><?php echo $bedition;?></td>
            <td><?php echo $author;?></td>
            <td><?php echo $publication;?></td>
            <td><?php echo $faculty;?></td>
            <td><?php echo $semester;?></td>
            <td><?php echo $total_quantity;?></td>
            <td><?php echo $available_quantity;?></td>
            <td><?php echo $issued_quantity;?></td>
        </tr>
        <?php
             }
            ?>
    </table>
</body>
</html>