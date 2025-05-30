<!-- navbar.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Navbar</title>
    <style>
        /* Global Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        /* Navigation Bar */
        .navigation-bar {
            background: #000;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 999;
        }

        /* Logo */
        .navigation-bar h1 a {
            color: white;
            text-decoration: none;
            font-size: 24px;
            font-weight: bold;
            transition: 0.3s;
        }

        .navigation-bar h1 a:hover {
            color: #f1c40f; /* Yellow hover */
        }

        /* Navigation Links */
        .profile-logout {
            display: flex;
            align-items: center;
            gap: 20px;
            background:black;
            opacity:1;
        }

        .profile-logout a {
            text-decoration: none;
            color: white;
            font-size: 16px;
            font-weight: 500;
            padding: 10px 15px;
            border-radius: 5px;
            transition: 0.3s ease-in-out;
        }

        .profile-logout a:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.05);
        }

        /* Logout Button */
        .logout-btn a {
            background: #e74c3c;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 5px;
        }

        .logout-btn a:hover {
            background: #c0392b;
        }

        /* Hamburger Icon */
        .hamburger {
            display: none;
            font-size: 26px;
            color: white;
            cursor: pointer;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hamburger {
                display: block;
            }

            .profile-logout {
                display: none;
                flex-direction: column;
                background: #000;
                position: absolute;
                top: 60px;
                right: 0;
                width: 100%;
                padding: 20px 0;
                border-top: 1px solid #222;
            }

            .profile-logout.show {
                display: flex;
            }

            .profile-logout a {
                width: 100%;
                text-align: center;
                padding: 12px 20px;
            }
        }
    </style>
</head>
<body>

<div class="navigation-bar">
    <h1><a href="userprofile.php">📚 Library Management System</a></h1>
    <div class="hamburger" onclick="toggleMenu()">☰</div>
    <div class="profile-logout" id="navLinks">
        <a href="viewbooks.php">📖 Books</a>
        <a href="viewprofile.php">👤 Profile</a>
        <li class="logout-btn"><a href="logout.php">🚪 Logout</a></li>
    </div>
</div>

<script>
    function toggleMenu() {
        const navLinks = document.getElementById('navLinks');
        navLinks.classList.toggle('show');
    }
</script>

</body>
</html>
