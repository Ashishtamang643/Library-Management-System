<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input fields
    $bname = htmlspecialchars(trim($_POST['bname']));
    $bedition = htmlspecialchars(trim($_POST['bedition']));
    $bnum = htmlspecialchars(trim($_POST['bnum']));
    $author = htmlspecialchars(trim($_POST['author_name']));
    $faculty = htmlspecialchars(trim($_POST['faculty']));
    $semester = intval($_POST['semester']); // Ensure semester is an integer
    $publication = htmlspecialchars(trim($_POST['publication']));
    $total_quantity = intval($_POST['total_quantity']); // Ensure total_quantity is an integer

    // Validate required fields
    if (empty($bname) || empty($bedition) || empty($bnum) || empty($author) || empty($faculty) || empty($semester) || empty($publication) || empty($total_quantity)) {
        echo "<script>alert('All fields are required!'); window.location.href = 'books.php';</script>";
        exit();
    }

    // Database connection
    $connection = mysqli_connect("localhost", "root", "", "library");
    if (!$connection) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Check if the book number already exists
    $query = "SELECT * FROM books WHERE book_num = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "s", $bnum);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        echo "<script>alert('Book number already exists!'); window.location.href = 'books.php';</script>";
        exit();
    }

    // Insert the new book into the database
    $insertquery = "INSERT INTO books (book_name, book_num, book_edition, author_name, faculty, semester, publication, total_quantity, available_quantity) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($connection, $insertquery);
    mysqli_stmt_bind_param($stmt, "sssssssii", $bname, $bnum, $bedition, $author, $faculty, $semester, $publication, $total_quantity, $total_quantity);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Book added successfully!'); window.location.href = 'books.php';</script>";
    } else {
        echo "<script>alert('Error adding book: " . mysqli_error($connection) . "'); window.location.href = 'books.php';</script>";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($connection);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Books</title>
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
        .addbooksdetails {
            max-width: 500px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .addbooksdetails h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }
        .addbooksdetails label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        .addbooksdetails input, .addbooksdetails select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        .addbooksdetails button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }
        .addbooksdetails button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
<?php include('adminnavbar.php'); ?>


    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="addbooksdetails" method="post">
        <h2>Add Books</h2>
        <label for="bname">Book Name</label>
        <input type="text" id="bname" name="bname" required>
        
        <label for="bnum">Book Number</label>
        <input type="text" id="bnum" name="bnum" required>
        
        <label for="bedition">Book Edition</label>
        <input type="text" id="bedition" name="bedition" required>
        
        <label for="author_name">Author Name</label>
        <input type="text" id="author_name" name="author_name" required>
        
        <label for="faculty">Faculty</label>
        <select id="faculty" name="faculty" required>
            <option value="Bsc.Csit">Bsc.Csit</option>
            <option value="BIM">BIM</option>
            <option value="BCA">BCA</option>
            <option value="BBM">BBM</option>
        </select>
        
        <label for="semester">Semester</label>
        <select id="semester" name="semester" required>
            <?php for ($i = 1; $i <= 8; $i++) { ?>
                <option value="<?php echo $i; ?>">Semester <?php echo $i; ?></option>
            <?php } ?>
        </select>
        
        <label for="publication">Publication</label>
        <input type="text" id="publication" name="publication" required>
        
        <label for="total_quantity">Total Quantity</label>
        <input type="number" id="total_quantity" name="total_quantity" min="1" required>
        
        <button type="submit">Add Book</button>
    </form>
</body>
</html>