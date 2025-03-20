<?php
session_start();
$connection = mysqli_connect("localhost", "root", "", "library");

if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = trim($_POST['fname']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Encrypt password
    $cell = trim($_POST['cell']);
    $address = trim($_POST['address']);
    $faculty = trim($_POST['faculty']);  // Get selected faculty

    // Check if email already exists
    $check_query = "SELECT * FROM users WHERE Email = ?";
    $stmt = mysqli_prepare($connection, $check_query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        echo "<script>alert('Email already registered!');</script>";
    } else {
        // Insert new user
        $query = "INSERT INTO users (Name, Email, Password, Cell, Address, Faculty) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($connection, $query);

        // Bind the parameters with the correct data types (s = string)
        mysqli_stmt_bind_param($stmt, "ssssss", $fname, $email, $password, $cell, $address, $faculty);

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Registration Successful!'); window.location.href = 'index.php';</script>";
        } else {
            echo "<script>alert('Error in Registration. Try Again!');</script>";
        }
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
    <title>Registration</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { display: flex; min-height: 100vh; }
        .signup-container { flex: 1; max-width: 500px; padding: 40px; display: flex; flex-direction: column; justify-content: center; }
        .image-container { flex: 1; height: 100vh; }
        .image-container img { height: 100%; width: 100%; object-fit: cover; }
        .signup-title { font-size: 48px; font-weight: bold; margin-bottom: 40px; }
        .input-group { position: relative; margin-bottom: 20px; }
        .input-group input, .input-group select { width: 100%; padding: 15px; padding-left: 45px; border-radius: 8px; border: none; background-color: #f0e6ff; font-size: 16px; }
        .input-group span { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #8860ff; }
        .signup-btn { width: 100%; padding: 15px; border: none; border-radius: 8px; background-color: #8860ff; color: white; font-size: 16px; cursor: pointer; margin-top: 20px; }
        .login-link { margin-top: 20px; text-align: center; color: #666; }
        .login-link a { color: #8860ff; text-decoration: none; }
        .error-message { color: #ff4444; text-align: center; margin-top: 10px; }
        @media (max-width: 768px) { .image-container { display: none; } .signup-container { max-width: 100%; } }
    </style>
</head>
<body>
    <div class="signup-container">
        <h1 class="signup-title">SIGNUP</h1>
        
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="input-group">
                <span>👤</span>
                <input type="text" name="fname" placeholder="Full Name" required>
            </div>
            <div class="input-group">
                <span>@</span>
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <!-- <div class="input-group">
                <span>👤</span>
                <input type="text" name="username" placeholder="Username" required>
            </div> -->
            <div class="input-group">
                <span>🔒</span>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <div class="input-group">
                <span>📱</span>
                <input type="tel" name="cell" placeholder="Cell Number" required>
            </div>
            <div class="input-group">
                <span>📍</span>
                <input type="text" name="address" placeholder="Address" required>
            </div>
            <div class="input-group">
                <span>🎓</span>
                <select name="faculty" required>
                    <option value="" disabled selected>Select Faculty</option>
                    <option value="Bsc.Csit">Bsc.Csit</option>
                    <option value="BIM">BIM</option>
                    <option value="BCA">BCA</option>
                    <option value="BBM">BBM</option>
                </select>
            </div>
            <button type="submit" class="signup-btn">SIGNUP</button>
            <div class="login-link">Already have an account? <a href="index.php">Login</a></div>
        </form>
    </div>
    <div class="image-container">
        <img src="./Images/background.jpg" alt="Background Image">
    </div>
</body>
</html>
