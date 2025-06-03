<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Library Management - Admin Navigation</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
    }

    /* Navigation Bar */
    .second-nav-bar {
      background-color: #333;
      padding: 20px 0;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .admin-container {
      max-width: 1500px;
      min-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
    }

    /* Logo and Admin Title */
    .logo-section {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .logo {
      width: 40px;
      height: 40px;
      background-color: white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      color: #333;
    }

    .admin-title {
      color: white;
      font-size: 20px;
      font-weight: bold;
    }

    /* Nav Links */
    .nav-links {
      display: flex;
      gap: 25px;
      flex-wrap: wrap;
      justify-content: center;
    }

    .nav-item {
      color: #fff;
      text-decoration: none;
      font-size: 18px;
      padding: 10px 15px;
      border-radius: 5px;
      transition: background-color 0.3s ease;
    }

    .nav-item:hover {
      background-color: #6c63ff;
    }

    .nav-item.active {
  background-color: #6c63ff;
  color: white;
}


    /* Logout Button */
    .logout-btn {
      background-color: #e74c3c;
      color: white;
      padding: 10px 18px;
      border: none;
      font-size: 16px;
      cursor: pointer;
      border-radius: 5px;
      transition: background-color 0.3s ease;
    }

    .logout-btn:hover {
      background-color: #c0392b;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .admin-container {
        flex-direction: column;
        align-items: center;
        gap: 15px;
      }

      .nav-links {
        flex-direction: column;
        gap: 10px;
      }

      .logout-btn {
        align-self: center;
      }
    }
  </style>
</head>
<body>

  <!-- Navigation Bar -->
  <div class="second-nav-bar">
    <div class="admin-container">

      <!-- Left: Logo and Admin -->
      <div class="logo-section">
        <div class="logo">📚</div>
        <div class="admin-title">Library Admin</div>
      </div>

      <!-- Center: Nav Links -->
      <div class="nav-links">
        <a href="adminprofile.php" class="nav-item">Dashboard</a>
        <a href="books.php" class="nav-item">Add Book</a>
        <a href="issue.php" class="nav-item">Issue Book</a>
      </div>

      <!-- Right: Logout -->
      <form action="logout.php" method="post">
        <button type="submit" class="logout-btn">Logout</button>
      </form>

    </div>
  </div>
<script>
  // Get current pathname (e.g., "/books.php")
  const currentPath = window.location.pathname.split("/").pop();

  // Get all nav links
  const navLinks = document.querySelectorAll(".nav-item");

  navLinks.forEach(link => {
    // Extract the href file from link
    const linkPath = link.getAttribute("href");

    // Compare pathname with href
    if (linkPath === currentPath) {
      link.classList.add("active");
    }
  });
</script>

</body>
</html>
    