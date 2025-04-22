<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="adminstyle.css" />
    <style>
         .side-bar {
            width: 250px;
            background-color: white;
            padding: 20px 15px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            gap: 15px;
            min-height: calc(100vh - 100px); /* Corrected spacing */
            position:sticky;
            top: 100px;
        }

        .side-bar a {
            text-decoration: none;
            color: var(--text-color);
            padding: 12px 15px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: background-color 0.3s, color 0.3s;
        }

        .side-bar a:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .side-bar a i {
            font-size: 18px;
        }
    </style>
</head>
<body>
<div class="side-bar">
    <a href="adminprofile.php"><i class="fas fa-dashboard"></i> Dashboard</a>
    <a href="registeruser.php"><i class="fas fa-users"></i> View Users</a>
    <a href="registerbooks.php"><i class="fas fa-book"></i> View Books</a>
    <a href="registerauthors.php"><i class="fas fa-pen-nib"></i> View Authors</a>
    <a href="registerissue.php"><i class="fas fa-bookmark"></i> View Issues</a>
    <a href="bookrequest.php"><i class="fas fa-paper-plane"></i> View Requested Book</a>
</div>
</body>
</html>