<?php
session_start();

// If user is already logged in, redirect to user profile
if (isset($_SESSION['Email'])) {
    header("Location: viewbooks.php");
    exit();
}

$popupMessage = ""; // Variable to hold popup message

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
                $_SESSION['ID'] = $row['ID'];
                $_SESSION['Name'] = $row['Name'];

                $popupMessage = "Logging in...";
                echo "<script>
                    setTimeout(function() {
                        window.location.href = 'viewbooks.php';
                    }, 1500);
                </script>";
            } else {
                $popupMessage = "Wrong credentials. Please try again.";
            }
        } else {
            $popupMessage = "User not found.";
        }

        mysqli_close($connection);
    } else {
        $popupMessage = "Please fill in all fields.";
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
        body { display: flex; min-height: 100vh; overflow: hidden; position: relative; }
        .login-container { flex: 1; max-width: 500px; padding: 40px; display: flex; flex-direction: column; justify-content: center; }
        .image-container { flex: 1; height: 100vh; }
        .image-container img { height: 100%; width: 100%; object-fit: cover; }
        .login-title { font-size: 48px; font-weight: bold; margin-bottom: 40px; }
        .input-group { position: relative; margin-bottom: 20px; }
        .input-group input { width: 100%; padding: 15px; padding-left: 45px; border-radius: 8px; border: none; background-color: #f0e6ff; font-size: 16px; }
        .input-group span { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #8860ff; }
        .login-btn { width: 100%; padding: 15px; border: none; border-radius: 8px; background-color: #8860ff; color: white; font-size: 16px; cursor: pointer; margin-top: 20px; transition: background 0.3s; }
        .login-btn:hover { background-color: #6f44d1; }
        .create-account, .admin-login { margin-top: 20px; text-align: center; color: #666; }
        .create-account a { color: #8860ff; text-decoration: none; }
        .admin-login a { color: #ff5733; text-decoration: none; font-weight: bold; }
        @media (max-width: 768px) { .image-container { display: none; } .login-container { max-width: 100%; } }

        /* Overlay for popup */
        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 998;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease;
        }
        .popup-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        /* Popup container styles */
        .popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            text-align: center;
            transition: opacity 0.3s ease;
            max-width: 90%;
        }
        .popup.show {
            opacity: 1;
            visibility: visible;
        }
        .popup.success { border-left: 6px solid #28a745; }
        .popup.error { border-left: 6px solid #dc3545; }

        .popup .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 20px;
            cursor: pointer;
            color: #333;
        }
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
                <a href="admin/">Admin Login</a>
            </div>
        </form>
    </div>

    <div class="image-container">
        <img src="./Images/background.jpg" alt="Background">
    </div>

    <?php if (!empty($popupMessage)): ?>
        <div id="popupOverlay" class="popup-overlay show"></div>
        <div id="popupMessage" class="popup <?php echo ($popupMessage === 'Logging in...') ? 'success' : 'error'; ?> show">
            <span class="close-btn" onclick="hidePopup()">&times;</span>
            <?php echo htmlspecialchars($popupMessage); ?>
        </div>
    <?php endif; ?>

    <script>
        function hidePopup() {
            document.getElementById('popupMessage').classList.remove('show');
            document.getElementById('popupOverlay').classList.remove('show');
        }
        document.addEventListener('DOMContentLoaded', function() {
            var popup = document.getElementById('popupMessage');
            if (popup) {
                setTimeout(hidePopup, 2000);
            }
        });
    </script>
</body>
</html>
