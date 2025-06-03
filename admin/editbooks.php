<?php
session_start();
if (!isset($_SESSION['Name'])) {
    echo "<script>alert('Please login to continue.'); window.location.href='index.php';</script>";
    exit();
}
$connection = mysqli_connect("localhost", "root", "");
$db = mysqli_select_db($connection, "library");

// Fetch book details based on the book number
$bname = "";
$bnum = "";
$edition = "";
$author = "";
$semester = "";
$faculty = "";
$publication = "";
$total_quantity = "";
$description = "";
$picture = "";

$query = "SELECT * FROM books WHERE book_num = '$_GET[bn]'";
$query_run = mysqli_query($connection, $query);
while ($row = mysqli_fetch_assoc($query_run)) {
    $bname = $row['book_name'];
    $bnum = $row['book_num'];
    $edition = $row['book_edition'];
    $author = $row['author_name'];
    $semester = $row['semester'];
    $faculty = $row['faculty'];
    $publication = $row['publication'];
    $total_quantity = $row['total_quantity'];
    $description = $row['description'] ?? "";
    $picture = $row['picture'] ?? "";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    // Validate and sanitize input fields
    $bname = htmlspecialchars(trim($_POST['bname']));
    $bedition = htmlspecialchars(trim($_POST['bedition']));
    $bnum = htmlspecialchars(trim($_POST['bnum']));
    $author = htmlspecialchars(trim($_POST['author_name']));
    $description = htmlspecialchars(trim($_POST['description']));
    
    // Handle multiple faculty selection
    if (isset($_POST['faculty']) && is_array($_POST['faculty'])) {
        $faculty_new = implode(", ", array_map('htmlspecialchars', $_POST['faculty']));
    } else {
        $faculty_new = "";
    }
    
    // Handle multiple semester selection
    if (isset($_POST['semester']) && is_array($_POST['semester'])) {
        $semester_new = implode(", ", array_map('htmlspecialchars', $_POST['semester']));
    } else {
        $semester_new = "";
    }
    
    $publication = htmlspecialchars(trim($_POST['publication']));
    $total_quantity = intval($_POST['total_quantity']);

    // Handle image upload
    $picture_new = $picture; // Keep existing image by default
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
                    // Delete old image if it exists
                    if (!empty($picture) && file_exists($uploadDir . $picture)) {
                        unlink($uploadDir . $picture);
                    }
                    $picture_new = $fileName;
                } else {
                    echo "<script>alert('Error uploading image!'); window.location.href = 'managebooks.php';</script>";
                    exit();
                }
            } else {
                echo "<script>alert('Image size must be less than 5MB!'); window.location.href = 'managebooks.php';</script>";
                exit();
            }
        } else {
            echo "<script>alert('Only JPG, JPEG, PNG, and GIF files are allowed!'); window.location.href = 'managebooks.php';</script>";
            exit();
        }
    }

    // Validate required fields
    if (empty($bname) || empty($bedition) || empty($bnum) || empty($author) || empty($faculty_new) || empty($semester_new) || empty($publication) || empty($total_quantity) || empty($description)) {
        echo "<script>alert('All fields are required!'); window.location.href = 'managebooks.php';</script>";
        exit();
    }

    // Fetch the current total quantity and available quantity
    $fetch_query = "SELECT total_quantity, available_quantity FROM books WHERE book_num = '$bnum'";
    $fetch_result = mysqli_query($connection, $fetch_query);
    $row = mysqli_fetch_assoc($fetch_result);
    $current_total_quantity = $row['total_quantity'];
    $current_available_quantity = $row['available_quantity'];

    // Update available quantity based on the difference
    $new_available_quantity = $current_available_quantity + ($total_quantity - $current_total_quantity);

    // Update the book details
    $update_query = "UPDATE books SET 
                     book_name = '$bname', 
                     book_edition = '$bedition', 
                     author_name = '$author', 
                     publication = '$publication', 
                     total_quantity = '$total_quantity', 
                     available_quantity = '$new_available_quantity', 
                     semester = '$semester_new', 
                     faculty = '$faculty_new',
                     description = '$description',
                     picture = '$picture_new'
                     WHERE book_num = '$bnum'";
    $update_result = mysqli_query($connection, $update_query);

    if ($update_result) {
        echo "<script>alert('Book details updated successfully.'); window.location.href = 'managebooks.php';</script>";
    } else {
        echo "<script>alert('Error updating book details: " . mysqli_error($connection) . "');</script>";
    }
}

// Convert comma-separated values back to arrays for multi-select
$faculty_array = !empty($faculty) ? explode(", ", $faculty) : [];
$semester_array = !empty($semester) ? explode(", ", $semester) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            padding: 0;
        }

        .navigation-bar {
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }

        .navigation-bar a {
            color: white;
            text-decoration: none;
            transition: opacity 0.3s ease;
        }

        .navigation-bar a:hover {
            opacity: 0.8;
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
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .form-header .edit-icon {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
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

        .form-group input[readonly] {
            background: linear-gradient(135deg, #f8f9fc, #e9ecef);
            color: #6c757d;
            cursor: not-allowed;
            border-style: dashed;
            position: relative;
        }

        .readonly-label {
            position: relative;
        }

        .readonly-label::after {
            content: "🔒 Read Only";
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 12px;
            color: #6c757d;
            background: white;
            padding: 2px 6px;
            border-radius: 4px;
            border: 1px solid #dee2e6;
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

        .current-image-section {
            background: #f8f9fc;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
            border: 2px solid #e1e8ed;
        }

        .current-image-section h4 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .current-image-section img {
            max-width: 200px;
            max-height: 200px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            border: 3px solid white;
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

        .action-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-top: 40px;
        }

        .submit-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 18px 40px;
            font-size: 18px;
            font-weight: 600;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
        }

        .submit-btn:active {
            transform: translateY(-1px);
        }

        .cancel-btn {
            background: transparent;
            color: #6c757d;
            border: 2px solid #6c757d;
            padding: 18px 40px;
            font-size: 18px;
            font-weight: 600;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .cancel-btn:hover {
            background: #6c757d;
            color: white;
            transform: translateY(-3px);
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
                flex-direction: column;
                gap: 10px;
            }

            .container {
                margin: 20px auto;
                padding: 0 10px;
            }

            .action-buttons {
                flex-direction: column;
                align-items: stretch;
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

        /* Status indicator for fields with existing data */
        .has-data {
            position: relative;
        }

        .has-data::before {
            content: "✓";
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #28a745;
            font-weight: bold;
            z-index: 1;
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
            <h2>
                <div class="edit-icon">✏️</div>
                Edit Book Details
            </h2>
            <p>Update the information below to modify the book record</p>
        </div>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?bn=' . urlencode($_GET['bn']); ?>" method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-group">
                    <label for="bname">Book Name</label>
                    <input type="text" name="bname" id="bname" value="<?php echo htmlspecialchars($bname); ?>" placeholder="Enter book title" class="has-data" required>
                </div>

                <div class="form-group">
                    <label for="bnum" class="readonly-label">Book Number</label>
                    <input type="text" name="bnum" id="bnum" value="<?php echo htmlspecialchars($bnum); ?>" readonly>
                </div>

                <div class="form-group">
                    <label for="bedition">Book Edition</label>
                    <input type="text" name="bedition" id="bedition" value="<?php echo htmlspecialchars($edition); ?>" placeholder="e.g., 1st, 2nd, Latest" class="has-data" required>
                </div>

                <div class="form-group">
                    <label for="author_name">Author Name</label>
                    <input type="text" name="author_name" id="author_name" value="<?php echo htmlspecialchars($author); ?>" placeholder="Enter author's name" class="has-data" required>
                </div>

                <div class="form-group">
                    <label for="publication">Publication</label>
                    <input type="text" name="publication" id="publication" value="<?php echo htmlspecialchars($publication); ?>" placeholder="Enter publisher name" class="has-data" required>
                </div>

                <div class="form-group">
                    <label for="total_quantity">Total Quantity</label>
                    <input type="number" name="total_quantity" id="total_quantity" value="<?php echo htmlspecialchars($total_quantity); ?>" min="1" placeholder="Number of copies" class="has-data" required>
                </div>

                <div class="form-group half-width">
                    <label for="faculty">Faculty</label>
                    <select multiple name="faculty[]" id="faculty" class="multi-select" required>
                        <option value="Bsc.Csit" <?php echo in_array('Bsc.Csit', $faculty_array) ? 'selected' : ''; ?>>Bsc.Csit</option>
                        <option value="BIM" <?php echo in_array('BIM', $faculty_array) ? 'selected' : ''; ?>>BIM</option>
                        <option value="BCA" <?php echo in_array('BCA', $faculty_array) ? 'selected' : ''; ?>>BCA</option>
                        <option value="BBM" <?php echo in_array('BBM', $faculty_array) ? 'selected' : ''; ?>>BBM</option>
                    </select>
                    <p class="select-help">Hold Ctrl (Cmd on Mac) to select multiple faculties</p>
                </div>

                <div class="form-group full-width">
                    <label for="semester">Semester</label>
                    <select multiple name="semester[]" id="semester" class="multi-select" required>
                        <?php for ($i = 1; $i <= 8; $i++) { ?>
                            <option value="<?php echo $i; ?>" <?php echo in_array((string)$i, $semester_array) ? 'selected' : ''; ?>>Semester <?php echo $i; ?></option>
                        <?php } ?>
                    </select>
                    <p class="select-help">Hold Ctrl (Cmd on Mac) to select multiple semesters</p>
                </div>

                <div class="form-group full-width">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" placeholder="Write a brief description about the book, its content, and target audience..." class="has-data" required><?php echo htmlspecialchars($description); ?></textarea>
                </div>

                <div class="form-group full-width">
                    <label for="picture">Book Cover Image</label>
                    
                    <?php if (!empty($picture) && file_exists("upload/" . $picture)) { ?>
                        <div class="current-image-section">
                            <h4>📖 Current Book Cover</h4>
                            <img src="upload/<?php echo htmlspecialchars($picture); ?>" alt="Current book cover">
                        </div>
                    <?php } ?>
                    
                    <div class="image-upload-section">
                        <input type="file" name="picture" id="picture" accept="image/*" onchange="previewImage(this)">
                        <div class="upload-content">
                            <div class="upload-icon">🖼️</div>
                            <div class="upload-text">Click to upload new cover</div>
                            <div class="upload-subtext">or drag and drop your image here</div>
                            <div class="file-info">
                                <p>Supported formats: JPG, JPEG, PNG, GIF | Maximum size: 5MB</p>
                                <p><em>Leave empty to keep current image</em></p>
                                <span id="file-name"></span>
                            </div>
                            <img id="image-preview" class="image-preview" style="display: none;">
                        </div>
                    </div>
                </div>
            </div>

            <div class="action-buttons">
                <a href="managebooks.php" class="cancel-btn">
                    ❌ Cancel
                </a>
                <button type="submit" name="update" class="submit-btn">
                    💾 Update Book
                </button>
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