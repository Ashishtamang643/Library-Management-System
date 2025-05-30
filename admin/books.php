<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input fields
    $bname = htmlspecialchars(trim($_POST['bname']));
    $bedition = htmlspecialchars(trim($_POST['bedition']));
    $bnum = htmlspecialchars(trim($_POST['bnum']));
    $author = htmlspecialchars(trim($_POST['author_name']));
    $description = htmlspecialchars(trim($_POST['description']));
    
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
    $total_quantity = intval($_POST['total_quantity']);

    // Handle image upload
    $picture = "";
    if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'upload/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileExtension = strtolower(pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($fileExtension, $allowedExtensions)) {
            $fileName = uniqid() . '_' . time() . '.' . $fileExtension;
            $targetPath = $uploadDir . $fileName;
            
            if ($_FILES['picture']['size'] <= 5 * 1024 * 1024) {
                if (move_uploaded_file($_FILES['picture']['tmp_name'], $targetPath)) {
                    $picture = $fileName;
                } else {
                    echo "<script>alert('Error uploading image!'); window.location.href = 'books.php';</script>";
                    exit();
                }
            } else {
                echo "<script>alert('Image size must be less than 5MB!'); window.location.href = 'books.php';</script>";
                exit();
            }
        } else {
            echo "<script>alert('Only JPG, JPEG, PNG, and GIF files are allowed!'); window.location.href = 'books.php';</script>";
            exit();
        }
    }

    // Validate required fields
    if (empty($bname) || empty($bedition) || empty($bnum) || empty($author) || empty($faculty) || empty($semester) || empty($publication) || empty($total_quantity) || empty($description)) {
        echo "<script>alert('All fields are required!'); window.location.href = 'books.php';</script>";
        exit();
    }

    // DB connection
    $connection = mysqli_connect("localhost", "root", "", "library");
    if (!$connection) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Check if book number exists
    $query = "SELECT * FROM books WHERE book_num = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "s", $bnum);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        echo "<script>alert('Book number already exists!'); window.location.href = 'books.php';</script>";
        exit();
    }

    // Insert into DB
    $insertquery = "INSERT INTO books (book_name, book_num, book_edition, author_name, faculty, semester, publication, total_quantity, available_quantity, picture, description) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($connection, $insertquery);
    mysqli_stmt_bind_param($stmt, "sssssssiiss", $bname, $bnum, $bedition, $author, $faculty, $semester, $publication, $total_quantity, $total_quantity, $picture, $description);

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
            background-color: #f4f4f4;
            margin: 0;
        }
        .navigation-bar {
            background-color: #333;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
        }
        .navigation-bar a {
            color: white;
            text-decoration: none;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .addbooksdetails h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        .form-grid label {
            font-weight: bold;
        }
        .form-grid input, .form-grid select, .form-grid textarea {
            width: 100%;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-size: 16px;
        }
        .form-grid textarea {
            resize: vertical;
            min-height: 100px;
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
        .multi-select {
            height: auto;
            min-height: 100px;
        }
        .select-help {
            font-size: 12px;
            color: #666;
        }
        .image-upload-container {
            border: 2px dashed #ccc;
            padding: 20px;
            text-align: center;
            background-color: #fafafa;
        }
        .image-upload-container:hover {
            border-color: #4CAF50;
        }
        .image-upload-container input[type="file"] {
            display: none;
        }
        .upload-label {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border-radius: 4px;
            cursor: pointer;
        }
        .upload-label:hover {
            background-color: #45a049;
        }
        .file-info {
            font-size: 14px;
            margin-top: 10px;
            color: #666;
        }
        .image-preview {
            margin-top: 15px;
            max-width: 200px;
            max-height: 200px;
            border-radius: 4px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
<?php include('adminnavbar.php'); ?>
<div class="main">
<?php include('sidebar.php'); ?>

<div class="container">
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="addbooksdetails" enctype="multipart/form-data">
        <h2>Add Books</h2>
        <div class="form-grid">
            <label for="bname">Book Name</label>
            <input type="text" name="bname" id="bname" required>

            <label for="bnum">Book Number</label>
            <input type="text" name="bnum" id="bnum" required>

            <label for="bedition">Book Edition</label>
            <input type="text" name="bedition" id="bedition" required>

            <label for="author_name">Author Name</label>
            <input type="text" name="author_name" id="author_name" required>

            <div class="full-width">
                <label for="faculty">Faculty</label>
                <select multiple name="faculty[]" id="faculty" class="multi-select" required>
                    <option value="Bsc.Csit">Bsc.Csit</option>
                    <option value="BIM">BIM</option>
                    <option value="BCA">BCA</option>
                    <option value="BBM">BBM</option>
                </select>
                <p class="select-help">Hold Ctrl or Cmd to select multiple</p>
            </div>

            <div class="full-width">
                <label for="semester">Semester</label>
                <select multiple name="semester[]" id="semester" class="multi-select" required>
                    <?php for ($i = 1; $i <= 8; $i++) echo "<option value='$i'>Semester $i</option>"; ?>
                </select>
                <p class="select-help">Hold Ctrl or Cmd to select multiple</p>
            </div>

            <label for="publication">Publication</label>
            <input type="text" name="publication" id="publication" required>

            <label for="total_quantity">Total Quantity</label>
            <input type="number" name="total_quantity" id="total_quantity" min="1" required>

            <div class="full-width">
                <label for="description">Description</label>
                <textarea name="description" id="description" placeholder="Write short book description..." required></textarea>
            </div>

            <div class="full-width">
                <label for="picture">Book Cover Image</label>
                <div class="image-upload-container">
                    <input type="file" name="picture" id="picture" accept="image/*" onchange="previewImage(this)">
                    <label for="picture" class="upload-label">Choose Image</label>
                    <div class="file-info">
                        <p>Supported formats: JPG, JPEG, PNG, GIF</p>
                        <p>Maximum size: 5MB</p>
                        <span id="file-name"></span>
                    </div>
                    <img id="image-preview" class="image-preview" style="display: none;">
                </div>
            </div>
        </div>
        <button type="submit">Add Book</button>
    </form>
</div>
</div>

<script>
function previewImage(input) {
    const fileName = document.getElementById('file-name');
    const preview = document.getElementById('image-preview');

    if (input.files && input.files[0]) {
        const file = input.files[0];
        fileName.textContent = `Selected: ${file.name}`;

        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        fileName.textContent = '';
        preview.style.display = 'none';
    }
}
</script>
</body>
</html>
