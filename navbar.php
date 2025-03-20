<!-- navbar.php -->
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
        background: #222; /* Dark Gray Background */
        color: white;
        padding: 15px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
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
        color: #f1c40f; /* Yellow Hover Effect */
    }

    /* Navigation Links */
    .profile-logout {
        display: flex;
        align-items: center;
        gap: 20px;
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

    /* Hover Effects */
    .profile-logout a:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: scale(1.05);
    }

    /* Logout Button */
    .profile-logout .logout-btn a {
        background: #e74c3c; /* Red Button */
        font-weight: bold;
        padding: 10px 20px;
        border-radius: 5px;
    }

    .profile-logout .logout-btn a:hover {
        background: #c0392b;
    }


    /* Responsive Design */
    @media (max-width: 768px) {
        .navigation-bar {
            flex-direction: column;
            text-align: center;
        }

        .profile-logout {
            flex-direction: column;
            gap: 10px;
        }

        .profile-logout a {
            width: 100%;
            text-align: center;
        }
    }
</style>

<div class="navigation-bar">
    <h1><a href="userprofile.php">📚 Library Management System</a></h1>
    <div class="profile-logout">
        <a href="viewbooks.php">📖 Books</a>
        <a href="viewprofile.php">👤 Profile</a>
        <li class="logout-btn"><a href="logout.php">🚪 Logout</a></li>
    </div>
</div>
