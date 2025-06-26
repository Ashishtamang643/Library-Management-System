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
        *{
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .form-wrapper {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .form-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .form-header h2 {
            font-size: 32px;
            color: #2c3e50;
            font-weight: 300;
            margin-bottom: 10px;
        }

        .form-header p {
            color: #7f8c8d;
            font-size: 16px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin-bottom: 30px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full-width {
            grid-column: span 3;
        }

        .form-group.half-width {
            grid-column: span 2;
        }

        .form-group label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 15px 20px;
            border: 2px solid #e1e8ed;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: white;
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
            font-family: inherit;
        }

        .multi-select {
            min-height: 120px;
            padding: 10px 15px;
        }

        .multi-select option {
            padding: 8px;
            margin: 2px 0;
        }

        .select-help {
            font-size: 12px;
            color: #95a5a6;
            margin-top: 5px;
            font-style: italic;
        }

        .image-upload-section {
            background: #f8f9fc;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            border: 2px dashed #d1d9e6;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .image-upload-section:hover {
            border-color: #667eea;
            background: #f0f3ff;
        }

        .image-upload-section input[type="file"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .upload-content {
            pointer-events: none;
        }

        .upload-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }

        .upload-text {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 10px;
            font-weight: 500;
        }

        .upload-subtext {
            font-size: 14px;
            color: #7f8c8d;
            margin-bottom: 15px;
        }

        .file-info {
            font-size: 12px;
            color: #95a5a6;
        }

        .image-preview {
            margin-top: 20px;
            max-width: 200px;
            max-height: 200px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            border: 3px solid white;
        }

        .submit-section {
            display: flex;
            justify-content: center;
            margin-top: 40px;
        }

        .submit-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 18px 60px;
            font-size: 18px;
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
        }

        .submit-btn:active {
            transform: translateY(-1px);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .form-group.full-width,
            .form-group.half-width {
                grid-column: span 1;
            }

            .form-wrapper {
                padding: 30px 20px;
                margin: 20px;
            }

            .form-header h2 {
                font-size: 24px;
            }

            .container {
                margin: 20px auto;
                padding: 0 10px;
            }
        }

        @media (max-width: 1024px) {
            .form-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .form-group.half-width {
                grid-column: span 2;
            }
        }

        /* Animation for form appearance */
        .form-wrapper {
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
<?php include('adminnavbar.php'); ?>
<div class="main">
<?php include('sidebar.php'); ?>

<div class="container">
    <div class="form-wrapper">
        <div class="form-header">
            <h2>Add New Book</h2>
            <p>Fill in the details below to add a new book to the library</p>
        </div>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-group">
                    <label for="bname">Book Name</label>
                    <input type="text" name="bname" id="bname" pattern="[A-Za-z\s]+" placeholder="Enter book title" required>
                </div>

                <div class="form-group">
                    <label for="bnum">Book Number</label>
                    <input type="text" name="bnum" id="bnum" pattern="\d{13}" placeholder="Enter unique book ID" required>
                </div>

                <div class="form-group">
                    <label for="bedition">Book Edition</label>
                    <input type="text" name="bedition" id="bedition" placeholder="e.g., 1st, 2nd, Latest" required>
                </div>

                <div class="form-group">
                    <label for="author_name">Author Name</label>
                    <input type="text" name="author_name" id="author_name" placeholder="Enter author's name" required>
                </div>

                <div class="form-group">
                    <label for="publication">Publication</label>
                    <input type="text" name="publication" id="publication" placeholder="Enter publisher name" required>
                </div>

                <div class="form-group">
                    <label for="total_quantity">Total Quantity</label>
                    <input type="number" name="total_quantity" id="total_quantity" min="1" placeholder="Number of copies" required>
                </div>

                <div class="form-group half-width">
                    <label for="faculty">Faculty</label>
                    <select multiple name="faculty[]" id="faculty" class="multi-select" required>
                        <option value="Bsc.Csit">Bsc.Csit</option>
                        <option value="BIM">BIM</option>
                        <option value="BCA">BCA</option>
                        <option value="BBM">BBM</option>
                    </select>
                    <p class="select-help">Hold Ctrl (Cmd on Mac) to select multiple faculties</p>
                </div>

                <div class="form-group full-width">
                    <label for="semester">Semester</label>
                    <select multiple name="semester[]" id="semester" class="multi-select" required>
                        <?php for ($i = 1; $i <= 8; $i++) echo "<option value='$i'>Semester $i</option>"; ?>
                    </select>
                    <p class="select-help">Hold Ctrl (Cmd on Mac) to select multiple semesters</p>
                </div>

                <div class="form-group full-width">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" placeholder="Write a brief description about the book, its content, and target audience..." required></textarea>
                </div>

                <div class="form-group full-width">
                    <label for="picture">Book Cover Image</label>
                    <div class="image-upload-section">
                        <input type="file" name="picture" id="picture" accept="image/*" onchange="previewImage(this)">
                        <div class="upload-content">
                            <div class="upload-icon">📚</div>
                            <div class="upload-text">Click to upload book cover</div>
                            <div class="upload-subtext">or drag and drop your image here</div>
                            <div class="file-info">
                                <p>Supported formats: JPG, JPEG, PNG, GIF | Maximum size: 5MB</p>
                                <span id="file-name"></span>
                            </div>
                            <img id="image-preview" class="image-preview" style="display: none;">
                        </div>
                    </div>
                </div>
            </div>

            <div class="submit-section">
                <button type="submit" class="submit-btn">Add Book to Library</button>
            </div>
        </form>
    </div>
</div>
</div>

<script>
function previewImage(input) {
    const fileName = document.getElementById('file-name');
    const preview = document.getElementById('image-preview');

    if (input.files && input.files[0]) {
        const file = input.files[0];
        fileName.textContent = `Selected: ${file.name}`;
        fileName.style.color = '#667eea';
        fileName.style.fontWeight = '600';

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