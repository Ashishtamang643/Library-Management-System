<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input fields
    $bname = htmlspecialchars(trim($_POST['bname']));
    $bedition = htmlspecialchars(trim($_POST['bedition']));
    $bnum = htmlspecialchars(trim($_POST['bnum']));
    $author = htmlspecialchars(trim($_POST['author_name']));
    
    // Handle multiple faculty selection
    if (isset($_POST['faculty']) && is_array($_POST['faculty'])) {
        $faculty = implode(", ", array_map('htmlspecialchars', $_POST['faculty']));
    } else {
        $faculty = "";
    }
    
    // Handle multiple semester selection
    if (isset($_POST['semester']) && is_array($_POST['semester'])) {
        $semester = implode(", ", array_map('htmlspecialchars', $_POST['semester']));
    } else {
        $semester = "";
    }
    
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
        .container {
            max-width: 600px;
            margin: 30px auto;
            height: fit-content;
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
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        .form-grid label {
            font-weight: bold;
            color: #555;
        }
        .form-grid input, .form-grid select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        .form-grid .full-width {
            grid-column: span 2;
        }
        .addbooksdetails button {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 18px;
            cursor: pointer;
            margin-top: 20px;
        }
        .addbooksdetails button:hover {
            background-color: #45a049;
        }
        /* Style for multiple select */
        .multi-select {
            height: auto;
            min-height: 100px;
        }
        .select-help {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
<?php include('adminnavbar.php'); ?>

<div class="main">
<?php include('sidebar.php'); ?>

    <div class="container">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="addbooksdetails" method="post">
            <h2>Add Books</h2>
            <div class="form-grid">
                <label for="bname">Book Name</label>
                <input type="text" id="bname" name="bname" required>
                
                <label for="bnum">Book Number</label>
                <input type="text" id="bnum" name="bnum" required>
                
                <label for="bedition">Book Edition</label>
                <input type="text" id="bedition" name="bedition" required>
                
                <label for="author_name">Author Name</label>
                <input type="text" id="author_name" name="author_name" required>
                
                <div class="full-width">
                    <label for="faculty">Faculty</label>
                    <select multiple id="faculty" name="faculty[]" class="multi-select" required>
                        <option value="Bsc.Csit">Bsc.Csit</option>
                        <option value="BIM">BIM</option>
                        <option value="BCA">BCA</option>
                        <option value="BBM">BBM</option>
                    </select>
                    <p class="select-help">Hold Ctrl (PC) or Command (Mac) to select multiple faculties</p>
                </div>
                
                <div class="full-width">
                    <label for="semester">Semester</label>
                    <select multiple id="semester" name="semester[]" class="multi-select" required>
                        <?php for ($i = 1; $i <= 8; $i++) { ?>
                            <option value="<?php echo $i; ?>">Semester <?php echo $i; ?></option>
                        <?php } ?>
                    </select>
                    <p class="select-help">Hold Ctrl (PC) or Command (Mac) to select multiple semesters</p>
                </div>
                
                <label for="publication">Publication</label>
                <input type="text" id="publication" name="publication" required>
                
                <label for="total_quantity">Total Quantity</label>
                <input type="number" id="total_quantity" name="total_quantity" min="1" required>
            </div>
            <button type="submit">Add Book</button>
        </form>
    </div>
</div>
</body>
</html>