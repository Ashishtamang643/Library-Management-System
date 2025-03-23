<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <style>
       

        form {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            margin-top: 50px;
            left: 50%;
            position:relative;
            transform:translateX(-50%);
        }

        form h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
            font-size: 24px;
            
        }

        form input[type="text"] {
            width: 100%;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            background-color: #f9f9f9;
            transition: border-color 0.3s ease;
        }

        form input[type="text"]:focus {
            border-color: #007bff;
            outline: none;
            background-color: #fff;
        }

        form button {
            width: 100%;
            padding: 15px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.3s ease;
        }

        form button:hover {
            background-color: #0056b3;
        }

       

        .navbar a {
            color: #fff;
            text-decoration: none;
            margin: 0 15px;
            font-size: 16px;
        }

        .navbar a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php include('navbar.php'); ?>

    <form action="passwordchange.php" method="post">
        <h2>Change Password</h2>
        <input type="text" placeholder="Current Password" name="current_pwd" required>
        <input type="text" placeholder="New Password" name="new_pwd" required>
        <button type="submit" class="update-btn">Update</button>
    </form>
</body>
</html>