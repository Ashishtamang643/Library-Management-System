<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System - Admin Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        
        body {
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .container {
            background: blue;
            display: flex;
            width: 100%;
            max-width: 1200px;
            height: 85vh;
        }
        
        .login-section {
            background-color: rgba(255, 255, 255, 0.9);
            width: 35%;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .login-header {
            margin-bottom: 30px;
        }
        
        .login-header h1 {
            font-size: 32px;
            color: #333;
            margin-bottom: 10px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border 0.3s;
        }
        
        .form-group input:focus {
            border-color: #6c63ff;
            outline: none;
        }
        
        .btn-login {
            background-color: #6c63ff;
            color: white;
            border: none;
            padding: 12px 0;
            width: 100%;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
            margin-top: 10px;
            transition: background-color 0.3s;
        }
        
        .btn-login:hover {
            background-color: #5a52d5;
        }
        
        .additional-links {
            margin-top: 20px;
            text-align: center;
        }
        
        .additional-links a {
            color: #6c63ff;
            text-decoration: none;
            font-size: 14px;
            margin: 0 10px;
        }
        
        .additional-links a:hover {
            text-decoration: underline;
        }
        
        .image-section {
            width: 65%;
            background-image: url('https://images.unsplash.com/photo-1521587760476-6c12a4b040da');
            background-size: cover;
            background-position: center;
        }
        
        .error-message {
            color: #ff3333;
            font-size: 14px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-section">
            <div class="login-header">
                <h1>ADMIN LOGIN</h1>
                <p>Please enter your credentials to login</p>
            </div>
            
            <form action="" method="post">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Enter your username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                
                <?php
    session_start();
    
    
    if(isset($_SESSION['Name'])) {
        header("Location: adminprofile.php");
        exit();
    }

    $connection = mysqli_connect("localhost", "root", "", "library");
    if (!$connection) {
        die("Database Connection Failed: " . mysqli_connect_error());
    }

    if (isset($_POST['submit-btn'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Query to fetch admin data
        $query = "SELECT * FROM admin WHERE Username = '$username'";
        $query_run = mysqli_query($connection, $query);
        $row = mysqli_fetch_assoc($query_run);

        if ($row) {
            if ($row['Password'] == $password) {
                $_SESSION['Name'] = $row['Name'];
                header("Location: adminprofile.php");
                exit();
            } else {
                $error_message = "Wrong Password! Please try again.";
            }
        } else {
            $error_message = "Invalid username. Please try again.";
        }
    }
?>

                
                <button type="submit" class="btn-login" name="submit-btn">LOGIN</button>
            </form>
            
            <div class="additional-links">
                <a href="../index.php">Back to Home</a>
                <a href="#">Forgot Password?</a>
            </div>
        </div>
        
        <div class="image-section"></div>
    </div>
</body>
</html>