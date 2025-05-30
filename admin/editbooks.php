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
    <link rel="stylesheet" href="../style2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', 'Arial', sans-serif;
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }

        /* Form container */
        .editbooksdetails {
            max-width: 800px;
            margin: 40px auto;
            padding: 35px 40px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            position: relative;
            border-top: 5px solid #4361ee;
        }

        /* Form heading */
        .editbooksdetails h2 {
            font-size: 28px;
            color: #2d3748;
            margin-bottom: 30px;
            text-align: center;
            padding-bottom: 15px;
            border-bottom: 2px solid #edf2f7;
            position: relative;
            font-weight: 600;
        }

        .editbooksdetails h2:after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 2px;
            background-color: #4361ee;
        }

        /* Form grid */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-grid .full-width {
            grid-column: span 2;
        }

        /* Form labels */
        .editbooksdetails label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #4a5568;
            font-size: 15px;
            transition: all 0.3s;
        }

        /* Form inputs, select, and textarea */
        .editbooksdetails input,
        .editbooksdetails select,
        .editbooksdetails textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
            background-color: #fff;
            color: #2d3748;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .editbooksdetails input:focus,
        .editbooksdetails select:focus,
        .editbooksdetails textarea:focus {
            border-color: #4361ee;
            outline: none;
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.15);
        }

        .editbooksdetails textarea {
            resize: vertical;
            min-height: 100px;
        }

        /* Multi-select styling */
        .multi-select {
            height: auto;
            min-height: 100px;
        }

        .select-help {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }

        /* Icon styling for inputs */
        .input-icon {
            position: relative;
        }

        .input-icon i {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: 15px;
            color: #a0aec0;
            z-index: 1;
        }

        .input-icon input,
        .input-icon select {
            padding-left: 45px;
        }

        /* Read-only input styling */
        .editbooksdetails input[readonly] {
            background-color: #f8fafc;
            cursor: not-allowed;
            color: #718096;
            border: 1px dashed #cbd5e0;
        }

        /* Image upload container */
        .image-upload-container {
            border: 2px dashed #ccc;
            padding: 20px;
            text-align: center;
            background-color: #fafafa;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .image-upload-container:hover {
            border-color: #4361ee;
        }

        .image-upload-container input[type="file"] {
            display: none;
        }

        .upload-label {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4361ee;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .upload-label:hover {
            background-color: #3050e0;
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

        .current-image {
            margin-bottom: 15px;
        }

        .current-image img {
            max-width: 150px;
            max-height: 150px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .current-image p {
            margin-top: 10px;
            font-size: 14px;
            color: #666;
        }

        /* Button styling */
        .update-btn {
            display: block;
            width: 100%;
            background-color: #4361ee;
            color: white;
            border: none;
            padding: 14px;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 25px;
            transition: all 0.3s ease;
            font-weight: 600;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 12px rgba(66, 99, 235, 0.15);
        }

        .update-btn:hover {
            background-color: #3050e0;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(66, 99, 235, 0.25);
        }

        .update-btn:active {
            transform: translateY(0);
        }

        /* Optional animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .editbooksdetails {
            animation: fadeIn 0.5s ease-out;
        }

        /* Responsive adjustments */
        @media (max-width: 800px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .form-grid .full-width {
                grid-column: span 1;
            }
        }

        @media (max-width: 700px) {
            .editbooksdetails {
                margin: 25px 15px;
                padding: 25px 20px;
            }
            
            .editbooksdetails h2 {
                font-size: 22px;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
<?php include('adminnavbar.php'); ?>

<div class="main">
<?php include('sidebar.php'); ?>

    <div class="container">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?bn=' . urlencode($_GET['bn']); ?>" method="POST" class="editbooksdetails" enctype="multipart/form-data">
            <h2>Edit Book Details</h2>
            
            <div class="form-grid">
                <div>
                    <label for="bname">Book Name</label>
                    <div class="input-icon">
                        <i class="fas fa-book"></i>
                        <input type="text" id="bname" name="bname" value="<?php echo htmlspecialchars($bname); ?>" required>
                    </div>
                </div>
                
                <div>
                    <label for="bnum">Book Number</label>
                    <div class="input-icon">
                        <i class="fas fa-hashtag"></i>
                        <input type="text" id="bnum" name="bnum" value="<?php echo htmlspecialchars($bnum); ?>" readonly>
                    </div>
                </div>
                
                <div>
                    <label for="bedition">Book Edition</label>
                    <div class="input-icon">
                        <i class="fas fa-bookmark"></i>
                        <input type="text" id="bedition" name="bedition" value="<?php echo htmlspecialchars($edition); ?>" required>
                    </div>
                </div>
                
                <div>
                    <label for="author_name">Author Name</label>
                    <div class="input-icon">
                        <i class="fas fa-user-edit"></i>
                        <input type="text" id="author_name" name="author_name" value="<?php echo htmlspecialchars($author); ?>" required>
                    </div>
                </div>
                
                <div class="full-width">
                    <label for="faculty">Faculty</label>
                    <div class="input-icon">
                        <i class="fas fa-graduation-cap"></i>
                        <select multiple name="faculty[]" id="faculty" class="multi-select" required>
                            <option value="Bsc.Csit" <?php echo in_array('Bsc.Csit', $faculty_array) ? 'selected' : ''; ?>>Bsc.Csit</option>
                            <option value="BIM" <?php echo in_array('BIM', $faculty_array) ? 'selected' : ''; ?>>BIM</option>
                            <option value="BCA" <?php echo in_array('BCA', $faculty_array) ? 'selected' : ''; ?>>BCA</option>
                            <option value="BBM" <?php echo in_array('BBM', $faculty_array) ? 'selected' : ''; ?>>BBM</option>
                        </select>
                    </div>
                    <p class="select-help">Hold Ctrl or Cmd to select multiple</p>
                </div>

                <div class="full-width">
                    <label for="semester">Semester</label>
                    <div class="input-icon">
                        <i class="fas fa-calendar-alt"></i>
                        <select multiple name="semester[]" id="semester" class="multi-select" required>
                            <?php for ($i = 1; $i <= 8; $i++) { ?>
                                <option value="<?php echo $i; ?>" <?php echo in_array((string)$i, $semester_array) ? 'selected' : ''; ?>>Semester <?php echo $i; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <p class="select-help">Hold Ctrl or Cmd to select multiple</p>
                </div>
                
                <div>
                    <label for="publication">Publication</label>
                    <div class="input-icon">
                        <i class="fas fa-building"></i>
                        <input type="text" id="publication" name="publication" value="<?php echo htmlspecialchars($publication); ?>" required>
                    </div>
                </div>
                
                <div>
                    <label for="total_quantity">Total Quantity</label>
                    <div class="input-icon">
                        <i class="fas fa-layer-group"></i>
                        <input type="number" id="total_quantity" name="total_quantity" value="<?php echo htmlspecialchars($total_quantity); ?>" min="1" required>
                    </div>
                </div>
                
                <div class="full-width">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" placeholder="Write short book description..." required><?php echo htmlspecialchars($description); ?></textarea>
                </div>

                <div class="full-width">
                    <label for="picture">Book Cover Image</label>
                    
                    <?php if (!empty($picture) && file_exists("upload/" . $picture)) { ?>
                        <div class="current-image">
                            <img src="upload/<?php echo htmlspecialchars($picture); ?>" alt="Current book cover">
                            <p>Current Image</p>
                        </div>
                    <?php } ?>
                    
                    <div class="image-upload-container">
                        <input type="file" name="picture" id="picture" accept="image/*" onchange="previewImage(this)">
                        <label for="picture" class="upload-label">
                            <i class="fas fa-upload"></i> Choose New Image
                        </label>
                        <div class="file-info">
                            <p>Supported formats: JPG, JPEG, PNG, GIF</p>
                            <p>Maximum size: 5MB</p>
                            <p><em>Leave empty to keep current image</em></p>
                            <span id="file-name"></span>
                        </div>
                        <img id="image-preview" class="image-preview" style="display: none;">
                    </div>
                </div>
            </div>
            
            <button type="submit" class="update-btn" name="update">
                <i class="fas fa-sync-alt"></i> Update Book Details
            </button>
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