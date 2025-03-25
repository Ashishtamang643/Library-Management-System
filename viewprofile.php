<?php
session_start();
$connection = mysqli_connect("localhost", "root", "");
$db = mysqli_select_db($connection, "library");
if (!isset($_SESSION['Email']) || !isset($_SESSION['ID'])) {
    echo "<script>alert('Please login to continue.'); window.location.href='index.php';</script>";
    exit();
}

$name = "";
$email = "";
$cell = "";
$address = "";
$faculty = "";
$nameError = "";
$cellError = "";

// Fetch user details from the database
$query = "SELECT * FROM users WHERE ID ='$_SESSION[ID]'";
$query_run = mysqli_query($connection, $query);
while ($row = mysqli_fetch_assoc($query_run)) {
    $name = $row['Name'];
    $email = $row['Email'];
    $cell = $row['Cell'];
    $address = $row['Address'];
    $faculty = $row['faculty'];
}

// Validation functions
function validateName($name) {
    // Allows only alphabets and spaces, minimum 2 characters
    return preg_match("/^[A-Za-z ]{2,}$/", $name);
}

function validateCellNumber($cell) {
    // Validates cell number starting with 97/98 and exactly 10 digits
    return preg_match("/^(97|98)\d{8}$/", $cell);
}

// Check if the form is submitted to update profile
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $cell = trim($_POST['cell']);
    $address = trim($_POST['address']);
    $faculty = trim($_POST['faculty']);

    // Validate name
    if (!validateName($name)) {
        $nameError = "Name must contain only alphabets and spaces, minimum 2 characters.";
    }

    // Validate cell number
    if (!validateCellNumber($cell)) {
        $cellError = "Cell number must start with 97/98 and be 10 digits long.";
    }

    // If no validation errors, proceed with update
    if (empty($nameError) && empty($cellError)) {
        // Update profile details in the database
        $update_query = "UPDATE users SET Name = ?, Cell = ?, Address = ?, faculty = ? WHERE ID = ?";
        $stmt = mysqli_prepare($connection, $update_query);
        mysqli_stmt_bind_param($stmt, "sssss", $name, $cell, $address, $faculty, $_SESSION['ID']);

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Profile updated successfully!'); window.location.href = 'viewprofile.php';</script>";
        } else {
            echo "<script>alert('Error updating profile. Try again.');</script>";
        }

        mysqli_stmt_close($stmt);
    }
}

mysqli_close($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profile</title>
    <style>
        .profiledetails {
            background: #fff;
            left:50%;
            position:relative;
            transform:translateX(-50%);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            margin-top: 50px;
        }

        .profiledetails h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
            font-size: 24px;
        }

        .profiledetails h3 {
            color: #555;
            margin: 15px 0 5px;
            font-size: 16px;
        }

        .profiledetails input[type="text"],
        .profiledetails input[type="email"],
        .profiledetails select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        .profiledetails input[type="text"]:focus,
        .profiledetails input[type="email"]:focus,
        .profiledetails select:focus {
            border-color: #007bff;
            outline: none;
        }

        .error-message {
            color: red;
            font-size: 12px;
            margin-top: -10px;
            margin-bottom: 10px;
        }

        .profiledetails a {
            color: #007bff;
            text-decoration: none;
            font-size: 14px;
            text-align: center;
            display: block;
            margin-top: 10px;
        }

        .profiledetails a:hover {
            text-decoration: underline;
        }

        .profiledetails button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
        }

        .profiledetails button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <?php include('navbar.php'); ?>

    <form action="viewprofile.php" method="post" class="profiledetails" onsubmit="return validateForm()">
        <h2>Profile Details</h2>
        
        <h3>Full Name</h3>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
        <?php if (!empty($nameError)): ?>
            <div class="error-message"><?php echo $nameError; ?></div>
        <?php endif; ?>
        
        <h3>Email</h3>
        <input type="email" name="email" disabled value="<?php echo htmlspecialchars($email); ?>" required>
        
        <h3>Cell</h3>
        <input type="text" id="cell" name="cell" value="<?php echo htmlspecialchars($cell); ?>" required>
        <?php if (!empty($cellError)): ?>
            <div class="error-message"><?php echo $cellError; ?></div>
        <?php endif; ?>
        
        <h3>Address</h3>
        <input type="text" name="address" value="<?php echo htmlspecialchars($address); ?>" required>

        <h3>Faculty</h3>
        <select name="faculty" required>
            <option value="Bsc.Csit" <?php echo $faculty == 'Bsc.Csit' ? 'selected' : ''; ?>>Bsc.Csit</option>
            <option value="BIM" <?php echo $faculty == 'BIM' ? 'selected' : ''; ?>>BIM</option>
            <option value="BCA" <?php echo $faculty == 'BCA' ? 'selected' : ''; ?>>BCA</option>
            <option value="BBM" <?php echo $faculty == 'BBM' ? 'selected' : ''; ?>>BBM</option>
        </select>

        <a href="changepassword.php">Change password?</a>

        <button type="submit" class="edit-btn">Update</button>
    </form>

    <script>
    function validateForm() {
        // Client-side validation
        const name = document.getElementById('name').value.trim();
        const cell = document.getElementById('cell').value.trim();
        
        // Name validation
        const nameRegex = /^[A-Za-z ]{2,}$/;
        if (!nameRegex.test(name)) {
            alert('Name must contain only alphabets and spaces, minimum 2 characters.');
            return false;
        }

        // Cell number validation
        const cellRegex = /^(97|98)\d{8}$/;
        if (!cellRegex.test(cell)) {
            alert('Cell number must start with 97/98 and be 10 digits long.');
            return false;
        }

        return true;
    }
    </script>
</body>
</html>