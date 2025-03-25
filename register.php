<?php
session_start();
$connection = mysqli_connect("localhost", "root", "", "library");

if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Validation Functions
function validateUsername($fname) {
    // Allow only alphabets and spaces
    return preg_match("/^[A-Za-z\s]+$/", $fname);
}


function validatePassword($password) {
    // Password must be at least 6 characters long and contain at least one number
    return (strlen($password) >= 6 && preg_match("/\d/", $password));
}

function validateCellNumber($cell) {
    // Cell number must be 10 digits and start with 97 or 98
    return preg_match("/^(97|98)\d{8}$/", $cell);
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Trim and sanitize inputs
    $fname = trim($_POST['fname']);
    $email = trim($_POST['email']);
    // $username = trim($_POST['username']);
    $password = $_POST['password'];
    $cell = trim($_POST['cell']);
    $address = trim($_POST['address']);
    $faculty = trim($_POST['faculty']);

    // Validate Username
    if (!validateUsername($fname)) {
        $errors[] = "Username must contain only alphabets.";
    }

    // Validate Password
    if (!validatePassword($password)) {
        $errors[] = "Password must be at least 6 characters long and contain at least one number.";
    }

    // Validate Cell Number
    if (!validateCellNumber($cell)) {
        $errors[] = "Cell number must be 10 digits and start with 97 or 98.";
    }

    // Check if email already exists
    $check_query = "SELECT * FROM users WHERE Email = ?";
    $stmt = mysqli_prepare($connection, $check_query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        $errors[] = "Email already registered!";
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Insert new user
        $query = "INSERT INTO users (Name, Email, Password, Cell, Address, Faculty) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($connection, $query);

        // Bind the parameters
        mysqli_stmt_bind_param($stmt, "ssssss", $fname, $email, $hashed_password, $cell, $address, $faculty);

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>
                    alert('Registration Successful!');
                    window.location.href = 'index.php';
                  </script>";
            exit();
        } else {
            $errors[] = "Error in Registration. Try Again!";
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
        .validation-errors { 
            background-color: #ffdddd; 
            border: 1px solid #ff4444; 
            color: #ff4444; 
            padding: 10px; 
            margin-bottom: 20px; 
            border-radius: 8px; 
        }
        @media (max-width: 768px) { 
            .image-container { display: none; } 
            .signup-container { max-width: 100%; } 
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <h1 class="signup-title">SIGNUP</h1>
        
        <?php
        // Display validation errors
        if (!empty($errors)) {
            echo "<div class='validation-errors'>";
            foreach ($errors as $error) {
                echo "<p>• $error</p>";
            }
            echo "</div>";
        }
        ?>
        
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="input-group">
                <span>👤</span>
                <input type="text" name="fname" placeholder="Full Name" value="<?php echo isset($fname) ? htmlspecialchars($fname) : ''; ?>" required>
            </div>
            <div class="input-group">
                <span>@</span>
                <input type="email" name="email" placeholder="Email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
            </div>
            <!-- <div class="input-group">
                <span>👤</span>
                <input type="text" name="username" placeholder="Username" value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>" required>
            </div> -->
            <div class="input-group">
                <span>🔒</span>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <div class="input-group">
                <span>📱</span>
                <input type="tel" name="cell" placeholder="Cell Number (98/97 xxxxxxxx)" value="<?php echo isset($cell) ? htmlspecialchars($cell) : ''; ?>" required>
            </div>
            <div class="input-group">
                <span>📍</span>
                <input type="text" name="address" placeholder="Address" value="<?php echo isset($address) ? htmlspecialchars($address) : ''; ?>" required>
            </div>
            <div class="input-group">
                <span>🎓</span>
                <select name="faculty" required>
                    <option value="" disabled <?php echo !isset($faculty) ? 'selected' : ''; ?>>Select Faculty</option>
                    <option value="Bsc.Csit" <?php echo (isset($faculty) && $faculty == 'Bsc.Csit') ? 'selected' : ''; ?>>Bsc.Csit</option>
                    <option value="BIM" <?php echo (isset($faculty) && $faculty == 'BIM') ? 'selected' : ''; ?>>BIM</option>
                    <option value="BCA" <?php echo (isset($faculty) && $faculty == 'BCA') ? 'selected' : ''; ?>>BCA</option>
                    <option value="BBM" <?php echo (isset($faculty) && $faculty == 'BBM') ? 'selected' : ''; ?>>BBM</option>
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