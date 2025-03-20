<?php
session_start();
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
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book</title>
    <link rel="stylesheet" href="../style1.css">
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
            max-width: 600px;
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

        /* Form groups */
        .form-group {
            margin-bottom: 20px;
            position: relative;
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

        /* Form inputs and select */
        .editbooksdetails input,
        .editbooksdetails select {
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
        .editbooksdetails select:focus {
            border-color: #4361ee;
            outline: none;
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.15);
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

    <form action="editupdatebooks.php" method="POST" class="editbooksdetails">
        <h2>Edit Book Details</h2>
        
        <div class="form-group">
            <label for="bname">Book Name</label>
            <div class="input-icon">
                <i class="fas fa-book"></i>
                <input type="text" id="bname" name="bname" value="<?php echo $bname; ?>" required>
            </div>
        </div>
        
        <div class="form-group">
            <label for="bnum">Book Number</label>
            <div class="input-icon">
                <i class="fas fa-hashtag"></i>
                <input type="text" id="bnum" name="bnum" value="<?php echo $bnum; ?>" readonly>
            </div>
        </div>
        
        <div class="form-group">
            <label for="edition">Edition</label>
            <div class="input-icon">
                <i class="fas fa-bookmark"></i>
                <input type="number" id="edition" name="edition" value="<?php echo $edition; ?>" required>
            </div>
        </div>
        
        <div class="form-group">
            <label for="author">Author</label>
            <div class="input-icon">
                <i class="fas fa-user-edit"></i>
                <input type="text" id="author" name="author" value="<?php echo $author; ?>" required>
            </div>
        </div>
        
        <div class="form-group">
            <label for="publication">Publication</label>
            <div class="input-icon">
                <i class="fas fa-building"></i>
                <input type="text" id="publication" name="publication" value="<?php echo $publication; ?>" required>
            </div>
        </div>
        
        <div class="form-group">
            <label for="total_quantity">Total Quantity</label>
            <div class="input-icon">
                <i class="fas fa-layer-group"></i>
                <input type="number" id="total_quantity" name="total_quantity" value="<?php echo $total_quantity; ?>" required>
            </div>
        </div>
        
        <div class="form-group">
            <label for="semester">Semester</label>
            <div class="input-icon">
                <i class="fas fa-calendar-alt"></i>
                <input type="number" id="semester" name="semester" value="<?php echo $semester; ?>" required>
            </div>
        </div>
        
        <div class="form-group">
            <label for="faculty">Faculty</label>
            <div class="input-icon">
                <i class="fas fa-graduation-cap"></i>
                <select id="faculty" name="faculty">
                    <option value="Bsc.Csit" <?php echo ($faculty == 'Bsc.Csit') ? 'selected' : ''; ?>>Bsc.Csit</option>
                    <option value="BIM" <?php echo ($faculty == 'BIM') ? 'selected' : ''; ?>>BIM</option>
                    <option value="BCA" <?php echo ($faculty == 'BCA') ? 'selected' : ''; ?>>BCA</option>
                    <option value="BBM" <?php echo ($faculty == 'BBM') ? 'selected' : ''; ?>>BBM</option>
                </select>
            </div>
        </div>
        
        <button type="submit" class="update-btn" name="update">
            <i class="fas fa-sync-alt"></i> Update Book Details
        </button>
    </form>
</body>
</html>

<?php
if (isset($_POST['update'])) {
    $connection = mysqli_connect("localhost", "root", "");
    $db = mysqli_select_db($connection, "library");
    
    $bname = $_POST['bname'];
    $bnum = $_POST['bnum'];
    $edition = $_POST['edition'];
    $author = $_POST['author'];
    $publication = $_POST['publication'];
    $total_quantity = $_POST['total_quantity'];
    $semester = $_POST['semester'];
    $faculty = $_POST['faculty'];

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
                     book_edition = '$edition', 
                     author_name = '$author', 
                     publication = '$publication', 
                     total_quantity = '$total_quantity', 
                     available_quantity = '$new_available_quantity', 
                     semester = '$semester', 
                     faculty = '$faculty' 
                     WHERE book_num = '$bnum'";
    $update_result = mysqli_query($connection, $update_query);

    if ($update_result) {
        echo "<script>alert('Book details updated successfully.'); window.location.href = 'managebooks.php';</script>";
    } else {
        echo "<script>alert('Error updating book details: " . mysqli_error($connection) . "');</script>";
    }
}
?>