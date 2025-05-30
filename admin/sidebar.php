<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="adminstyle.css" />
    <style>
        :root {
            --primary-color: #6c5ce7;
            --text-color: #333;
        }

        .side-bar {
            width: 250px;
            background-color: white;
            padding: 20px 15px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            gap: 15px;
            min-height: calc(100vh - 100px);
            position: sticky;
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

        .side-bar a.active {
            background-color:var(--primary-color) !important; /* Purple background for active */
            color: white !important;
        }

        .side-bar a i {
            font-size: 18px;
        }
    </style>
</head>
<body>
<div class="side-bar">
    <a href="adminprofile.php" data-page="adminprofile.php"><i class="fas fa-dashboard"></i> Dashboard</a>
    <a href="registeruser.php" data-page="registeruser.php"><i class="fas fa-users"></i> View Users</a>
    <a href="registerbooks.php" data-page="registerbooks.php"><i class="fas fa-book"></i> View Books</a>
    <a href="registerauthors.php" data-page="registerauthors.php"><i class="fas fa-pen-nib"></i> View Authors</a>
    <a href="registerissue.php" data-page="registerissue.php"><i class="fas fa-bookmark"></i> View Issues</a>
    <a href="bookrequest.php" data-page="bookrequest.php"><i class="fas fa-paper-plane"></i> View Requested Book</a>
</div>

<script>
// Function to set active sidebar link based on current pathname
function setActiveSidebarLink() {
    // Get current pathname
    const currentPath = window.location.pathname;
    
    // Extract just the filename from the path
    const currentPage = currentPath.split('/').pop();
    
    // Get all sidebar links
    const sidebarLinks = document.querySelectorAll('.side-bar a');
    
    // Remove active class from all links
    sidebarLinks.forEach(link => {
        link.classList.remove('active');
    });
    
    // Find and activate the matching link
    sidebarLinks.forEach(link => {
        const linkPage = link.getAttribute('data-page');
        if (linkPage === currentPage) {
            link.classList.add('active');
        }
    });
}

// Run when page loads
document.addEventListener('DOMContentLoaded', setActiveSidebarLink);

// Optional: Also run when hash changes (for single page apps)
window.addEventListener('hashchange', setActiveSidebarLink);
</script>
</body>
</html>