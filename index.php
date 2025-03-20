<?php
session_start();

// If user is already logged in, redirect to user profile
if (isset($_SESSION['Email'])) {
    header("Location: userprofile.php");
    exit();
}

// Check if login form is submitted
if (isset($_POST['submit-btn'])) {
    if (!empty($_POST['email']) && !empty($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Database connection
        $connection = mysqli_connect("localhost", "root", "", "library");

        if (!$connection) {
            die("Database connection failed: " . mysqli_connect_error());
        }

        // Fetch user data
        $query = "SELECT * FROM users WHERE Email = ?";
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            // Verify hashed password
            if (password_verify($password, $row['Password'])) {
                $_SESSION['Email'] = $row['Email'];
                $_SESSION['ID'] = $row['ID'];  // Save ID to session
                header("Location: userprofile.php");
                exit();
            } else {
                $error = "Wrong Password";
            }
        } else {
            $error = "User not found";
        }

        // Close database connection
        mysqli_close($connection);
    } else {
        $error = "Please fill in all fields";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { display: flex; min-height: 100vh; overflow: hidden; }
        .login-container { flex: 1; max-width: 500px; padding: 40px; display: flex; flex-direction: column; justify-content: center; }
        .image-container { flex: 1; height: 100vh; }
        .image-container img { height: 100%; width: 100%; object-fit: cover; }
        .login-title { font-size: 48px; font-weight: bold; margin-bottom: 40px; }
        .input-group { position: relative; margin-bottom: 20px; }
        .input-group input { width: 100%; padding: 15px; padding-left: 45px; border-radius: 8px; border: none; background-color: #f0e6ff; font-size: 16px; }
        .input-group span { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #8860ff; }
        .login-btn { width: 100%; padding: 15px; border: none; border-radius: 8px; background-color: #8860ff; color: white; font-size: 16px; cursor: pointer; margin-top: 20px; }
        .create-account { margin-top: 20px; text-align: center; color: #666; }
        .create-account a { color: #8860ff; text-decoration: none; }
        .admin-login { margin-top: 10px; text-align: center; }
        .admin-login a { color: #ff5733; text-decoration: none; font-weight: bold; }
        .error { color: red; text-align: center; margin-top: 10px; }
        @media (max-width: 768px) { .image-container { display: none; } .login-container { max-width: 100%; } }
    </style>
</head>
<body>
    <div class="login-container">
        <h1 class="login-title">LOGIN</h1>
        
        <form method="post">
            <div class="input-group">
                <span>@</span>
                <input type="email" name="email" placeholder="Email" required>
            </div>
            
            <div class="input-group">
                <span>🔒</span>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            
            <button type="submit" name="submit-btn" class="login-btn">LOGIN</button>
            
            <div class="create-account">
                New here? <a href="register.php">Create an Account</a>
            </div>
            <div class="admin-login">
                <a href="admin/adminindex.php">Admin Login</a>
            </div>
        </form>

        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
    </div>
    
    <div class="image-container">
        <img src="./Images/background.jpg" alt="">
    </div>
</body>
</html>
