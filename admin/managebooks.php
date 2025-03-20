<?php
require('functions.php');
session_start();
$connection = mysqli_connect("localhost", "root", "");
$db = mysqli_select_db($connection, "library");

// Fetch filter values from the form
$selected_faculty = isset($_GET['faculty']) ? $_GET['faculty'] : '';
$selected_semester = isset($_GET['semester']) ? $_GET['semester'] : '';

// Build the query based on filters
$query = "SELECT * FROM books WHERE 1=1";
if (!empty($selected_faculty)) {
    $query .= " AND faculty = '$selected_faculty'";
}
if (!empty($selected_semester)) {
    $query .= " AND semester = '$selected_semester'";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books</title>
    <link rel="stylesheet" href="../style1.css">
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
        .second-nav-bar {
            background-color: #444;
            padding: 10px;
        }
        .second-nav-bar .container {
            display: flex;
            align-items: center;
        }
        .second-nav-bar h3 {
            margin: 0 15px;
            color: white;
        }
        .dropdown {
            position: relative;
            display: inline-block;
        }
        .dropbtn {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            font-size: 16px;
            border: none;
            cursor: pointer;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }
        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }
        .dropdown:hover .dropdown-content {
            display: block;
        }
        .h2-register-header {
            text-align: center;
            margin-top: 20px;
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
        tr:nth-child(even) {
            background-color: #f2f2f2;
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
        .editbooks-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            text-decoration: none;
        }
        .editbooks-btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
<?php include('adminnavbar.php'); ?>


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

            <button type="submit">Filter</button>
        </form>
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
                <th>total_quantity</th>
                <th>available_quantity</th>
                <th>$issued_quantity</th>
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
                        <a href="deletebooks.php?bn=<?php echo $row['book_num']; ?>" class="editbooks-btn">Delete</a>
                    </td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
</body>
</html>