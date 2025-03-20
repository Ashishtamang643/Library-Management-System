<?php
session_start();
$connection = mysqli_connect("localhost", "root", "");
$db = mysqli_select_db($connection, "library");

$name = "";
$email = "";
$cell = "";
$address = "";
$faculty = "";

// Fetch user details from the database
$query = "SELECT * FROM users WHERE ID ='$_SESSION[ID]'";
$query_run = mysqli_query($connection, $query);
while ($row = mysqli_fetch_assoc($query_run)) {
    $name = $row['Name'];
    $email = $row['Email'];
    $cell = $row['Cell'];
    $address = $row['Address'];
    $faculty = $row['faculty']; // Add faculty
}

// Check if the form is submitted to update profile
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $cell = trim($_POST['cell']);
    $address = trim($_POST['address']);
    $faculty = trim($_POST['faculty']); // Get selected faculty

    // Update profile details in the database
    $update_query = "UPDATE users SET Name = ?, Email = ?, Cell = ?, Address = ?, faculty = ? WHERE Username = ?";
    $stmt = mysqli_prepare($connection, $update_query);
    mysqli_stmt_bind_param($stmt, "ssssss", $name, $email, $cell, $address, $faculty, $_SESSION['Username']);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Profile updated successfully!'); window.location.href = 'viewprofile.php';</script>";
    } else {
        echo "<script>alert('Error updating profile. Try again.');</script>";
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profile</title>
    <link rel="stylesheet" href="style1.css">
</head>
<body>
<?php include('navbar.php'); ?>

    <form action="viewprofile.php" method="post" class="profiledetails" style="display:grid;justify-content: center;margin-top:35px;color: rgb(98, 98, 106);">
        <h2 class="h2-header">Profile Details</h2>
        
        <h3>Full Name</h3>
        <input type="text" name="name" value="<?php echo $name; ?>" required>
        
        <h3>Email</h3>
        <input type="email" name="email" value="<?php echo $email; ?>" required>
        
        <h3>Cell</h3>
        <input type="text" name="cell" value="<?php echo $cell; ?>" required>
        
        <h3>Address</h3>
        <input type="text" name="address" value="<?php echo $address; ?>" required>

        <!-- Faculty Selection -->
        <h3>Faculty</h3>
        <select name="faculty" required>
            <option value="Bsc.Csit" <?php echo $faculty == 'Bsc.Csit' ? 'selected' : ''; ?>>Bsc.Csit</option>
            <option value="BIM" <?php echo $faculty == 'BIM' ? 'selected' : ''; ?>>BIM</option>
            <option value="BCA" <?php echo $faculty == 'BCA' ? 'selected' : ''; ?>>BCA</option>
            <option value="BBM" <?php echo $faculty == 'BBM' ? 'selected' : ''; ?>>BBM</option>
        </select>

        <a href="changepassword.php" style="color: red;font-family: arial;font-weight: 700;margin-top:30px;">Change password?</a>

        <button type="submit" class="edit-btn">Update</button>
    </form>
</body>
</html>
