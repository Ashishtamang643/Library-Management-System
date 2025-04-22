<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System - Admin Navigation</title>
    <style>
/* Base Styles for Navigation Bar */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Arial', sans-serif;
}

body {
    font-family: Arial, sans-serif;
}
.second-nav-bar {
    background-color: #333; /* Dark gray background */
    padding: 15px 0;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Light shadow for depth */
    height: 100px;
}

.admin-container {
    display: flex;
    justify-content: center;
    align-items: center;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.nav-item {
    font-size: 24px;
    color: #fefefe;
    font-weight: bold;
    text-decoration: none;
    margin: 0;
}

.nav-item:hover {
    color: #6c63ff; /* Add color on hover */
}

.dropdown {
    position: relative;
    display: inline-block;
}

/* Dropdown Button */
.dropbtn {
    background:transparent;
color:white;
    padding: 25px;
    font-size: 16px;
    border: none;
  }
  /* The container <div> - needed to position the dropdown content */
.dropdown {
    position: relative;
    display: inline-block;
  }
  /* Dropdown Content (Hidden by Default) */
.dropdown-content {
    background: whitesmoke;
    display: none;
    position: absolute;
    min-width: 160px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 1;
  }

  /* Links inside the dropdown */
.dropdown-content a {
    color: black;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
  }
.dropdown-content a:hover {background-color: #ddd;}

.dropdown:hover .dropdown-content {display: block;}

.dropdown:hover .dropbtn {background-color: transparent;}

/* Logout Button */
.logout-btn {
    background-color: #e74c3c;
    color: white;
    padding: 12px 16px;
    border: none;
    font-size: 16px;
    cursor: pointer;
    border-radius: 4px;
    transition: background-color 0.3s ease;
}

.logout-btn:hover {
    background-color: #c0392b;
}

/* Responsive Design for Smaller Screens */
@media (max-width: 768px) {
    .container {
        display: flex;
        flex-direction: row;
        align-items: flex-start;

    }
    
    .nav-item {
        font-size: 20px;
    }
    
    .dropbtn {
        width: 100%;
        text-align: left;
    }
    
    .dropdown-content {
        min-width: 100%;
    }
}

    </style>
</head>
<body>

    <!-- Navigation Bar -->
    <div class="second-nav-bar">
        <div class="admin-container">
            <a href="adminprofile.php">
                <h3 class="nav-item">Dashboard</h3>
            </a>
            <div class="dropdown">
                <button class="dropbtn">Books</button>
                <div class="dropdown-content">
                    <a href="books.php">Add new Book</a>
                    <a href="managebooks.php">Manage Book</a>
                </div>
            </div>
            <div class="dropdown">
                <button class="dropbtn">Issue Books</button>
                <div class="dropdown-content">
                    <a href="issue.php">Issue Book</a>
                </div>
            </div>
            <!-- Logout Button -->
            <form action="logout.php" method="post">
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </div>
    
</body>
</html>
