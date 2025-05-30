<?php
    require('functions.php');
    session_start();
    if (!isset($_SESSION['Name'])) {
        echo "<script>alert('Please login to continue.'); window.location.href='index.php';</script>";
        exit();
    }
    $connection = mysqli_connect("localhost","root","");
    $db = mysqli_select_db($connection,"library");

    // Initialize filter variables
    $book_name_filter = isset($_GET['book_name']) ? $_GET['book_name'] : '';
    $publication_filter = isset($_GET['publication']) ? $_GET['publication'] : '';
    $author_filter = isset($_GET['author']) ? $_GET['author'] : '';
    $faculty_filter = isset($_GET['faculty']) ? $_GET['faculty'] : '';
    $semester_filter = isset($_GET['semester']) ? $_GET['semester'] : '';

    // Base query
    $query = "SELECT * FROM books WHERE 1=1";

    // Add filters to the query
    if (!empty($book_name_filter)) {
        $query .= " AND book_name LIKE '%$book_name_filter%'";
    }
    if (!empty($publication_filter)) {
        $query .= " AND publication LIKE '%$publication_filter%'";
    }
    if (!empty($author_filter)) {
        $query .= " AND author_name LIKE '%$author_filter%'";
    }
    if (!empty($faculty_filter)) {
        // Check if the selected faculty exists in the comma-separated list
        $query .= " AND (faculty = '$faculty_filter' OR faculty LIKE '$faculty_filter,%' OR faculty LIKE '%, $faculty_filter,%' OR faculty LIKE '%, $faculty_filter')";
    }
    if (!empty($semester_filter)) {
        // Check if the selected semester exists in the comma-separated list
        $query .= " AND (semester = '$semester_filter' OR semester LIKE '$semester_filter,%' OR semester LIKE '%, $semester_filter,%' OR semester LIKE '%, $semester_filter')";
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Books</title>
    <link rel="stylesheet" href="../style2.css">
    <link rel="stylesheet" href="adminstyle.css">
    <style>
        .filter-container {
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }

        .filter-container label {
            font-weight: bold;
        }

        .filter-container input,
        .filter-container select {
            padding: 5px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .filter-container button {
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .filter-container button:hover {
            background-color: #45a049;
        }

        /* Book image and name styling */
        .book-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .book-image {
            width: 50px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #ddd;
            flex-shrink: 0;
        }

        .book-name {
            flex: 1;
            font-weight: 500;
        }

        .no-image {
            width: 50px;
            height: 60px;
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: #666;
            text-align: center;
            flex-shrink: 0;
        }

        /* Adjust table cell padding for better image display */
        table td {
            padding: 8px;
            vertical-align: middle;
        }

        /* Make the book name column wider to accommodate images */
        table th:first-child,
        table td:first-child {
            width: 200px;
            min-width: 200px;
        }

        /* Image modal styles */
        .image-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.8);
            cursor: pointer;
        }

        .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            max-width: 90%;
            max-height: 90%;
            border-radius: 8px;
        }

        .close-modal {
            position: absolute;
            top: 15px;
            right: 25px;
            color: white;
            font-size: 35px;
            font-weight: bold;
            cursor: pointer;
        }

        .close-modal:hover {
            opacity: 0.7;
        }
    </style>
</head>
<body>
    <?php include('adminnavbar.php'); ?>

    <div class="main">
    <?php include('sidebar.php'); ?>

    <div class="container">
    <h2 class="h2-register-header">Registered Books</h2>

    <!-- Filter Section -->
    <div class="filter-container">
        <form method="GET" action="">
            <label for="book_name">Book Name:</label>
            <input type="text" id="book_name" name="book_name" value="<?php echo $book_name_filter; ?>">

            <label for="publication">Publication:</label>
            <input type="text" id="publication" name="publication" value="<?php echo $publication_filter; ?>">

            <label for="author">Author:</label>
            <input type="text" id="author" name="author" value="<?php echo $author_filter; ?>">

            <label for="faculty">Faculty:</label>
            <select name="faculty" id="faculty">
                <option value="">All</option>
                <option value="Bsc.Csit" <?php echo ($faculty_filter == 'Bsc.Csit') ? 'selected' : ''; ?>>Bsc.Csit</option>
                <option value="BIM" <?php echo ($faculty_filter == 'BIM') ? 'selected' : ''; ?>>BIM</option>
                <option value="BCA" <?php echo ($faculty_filter == 'BCA') ? 'selected' : ''; ?>>BCA</option>
                <option value="BBM" <?php echo ($faculty_filter == 'BBM') ? 'selected' : ''; ?>>BBM</option>
            </select>

            <label for="semester">Semester:</label>
            <select name="semester" id="semester">
                <option value="">All</option>
                <?php for ($i = 1; $i <= 8; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php echo ($semester_filter == $i) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                <?php endfor; ?>
            </select>

            <button type="submit">Apply Filters</button>
            <button type="button" onclick="window.location.href = window.location.pathname;">Clear Filters</button>
        </form>
    </div>

    <table>
        <thead>
          <tr>
             <th>Book Info</th>
             <th>Book Num</th>
             <th>Edition</th>
             <th>Author</th>
             <th>Publication</th>
             <th>Faculty</th>
             <th>Semester</th>
             <th>Total Quantity</th>
             <th>Available Quantity</th>
             <th>Issued Quantity</th>
          </tr>
        </thead>
        <tbody>
        <?php
             $query_run = mysqli_query($connection,$query);
             while($row = mysqli_fetch_assoc($query_run))
             {
                $bname = $row['book_name'];
                $bnum = $row['book_num'];
                $bedition = $row['book_edition'];
                $author = $row['author_name'];
                $publication = $row['publication'];
                $faculty = $row['faculty'];
                $semester = $row['semester'];
                $total_quantity = $row['total_quantity'];
                $available_quantity = $row['available_quantity'];
                $issued_quantity = $total_quantity - $available_quantity;
                $picture = $row['picture'];
                ?>
        <tr>
            <td>
                <div class="book-info">
                    <?php if (!empty($picture) && file_exists('upload/' . $picture)): ?>
                        <img src="upload/<?php echo htmlspecialchars($picture); ?>" 
                             alt="<?php echo htmlspecialchars($bname); ?>" 
                             class="book-image" 
                             onclick="openImageModal('upload/<?php echo htmlspecialchars($picture); ?>', '<?php echo htmlspecialchars($bname); ?>')"
                             title="Click to view larger image">
                    <?php else: ?>
                        <div class="no-image">No Image</div>
                    <?php endif; ?>
                    <div class="book-name"><?php echo htmlspecialchars($bname); ?></div>
                </div>
            </td>
            <td><?php echo htmlspecialchars($bnum); ?></td>
            <td><?php echo htmlspecialchars($bedition); ?></td>
            <td><?php echo htmlspecialchars($author); ?></td>
            <td><?php echo htmlspecialchars($publication); ?></td>
            <td><?php echo htmlspecialchars($faculty); ?></td>
            <td><?php echo htmlspecialchars($semester); ?></td>
            <td><?php echo htmlspecialchars($total_quantity); ?></td>
            <td><?php echo htmlspecialchars($available_quantity); ?></td>
            <td><?php echo htmlspecialchars($issued_quantity); ?></td>
        </tr>
        <?php
             }
            ?>
        </tbody>
    </table>
    </div>

    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="image-modal" onclick="closeImageModal()">
        <span class="close-modal" onclick="closeImageModal()">&times;</span>
        <img class="modal-content" id="modalImage">
    </div>

    <script>
        function openImageModal(imageSrc, bookName) {
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImage');
            
            modal.style.display = 'block';
            modalImg.src = imageSrc;
            modalImg.alt = bookName;
        }

        function closeImageModal() {
            document.getElementById('imageModal').style.display = 'none';
        }

        // Close modal when pressing Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeImageModal();
            }
        });
    </script>

</body>
</html>