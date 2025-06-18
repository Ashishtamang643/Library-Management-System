<?php
require('functions.php');
session_start();
if (!isset($_SESSION['Name'])) {
    echo "<script>alert('Please login to continue.'); window.location.href='index.php';</script>";
    exit();
}
$connection = mysqli_connect("localhost", "root", "");
$db = mysqli_select_db($connection, "library");

// Handle book deletion
if (isset($_GET['delete_id'])) {
    $book_num = $_GET['delete_id'];
    
    // First, check if there are any issued copies of this book
    $check_query = "SELECT COUNT(*) as issued_count FROM issued WHERE book_num = '$book_num' AND returned IS NULL";
    $check_result = mysqli_query($connection, $check_query);
    $check_data = mysqli_fetch_assoc($check_result);
    
    if ($check_data['issued_count'] > 0) {
        echo "<script>alert('Cannot delete this book as there are issued copies. Please collect all issued copies first.'); window.location.href='managebooks.php';</script>";
    } else {
        // Delete the book
        $delete_query = "DELETE FROM books WHERE book_num = '$book_num'";
        $delete_result = mysqli_query($connection, $delete_query);
        
        if ($delete_result) {
            echo "<script>alert('Book deleted successfully.'); window.location.href='managebooks.php';</script>";
        } else {
            echo "<script>alert('Error deleting book.'); window.location.href='managebooks.php';</script>";
        }
    }
    exit();
}

// Initialize filter variables
$book_name_filter = isset($_GET['book_name']) ? $_GET['book_name'] : '';
$publication_filter = isset($_GET['publication']) ? $_GET['publication'] : '';
$author_filter = isset($_GET['author']) ? $_GET['author'] : '';
$faculty_filter = isset($_GET['faculty']) ? $_GET['faculty'] : '';
$semester_filter = isset($_GET['semester']) ? $_GET['semester'] : '';
$book_num_filter = isset($_GET['book_num']) ? $_GET['book_num'] : '';

// Pagination variables
$books_per_page = 20;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $books_per_page;

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
if (!empty($book_num_filter)) {
    $query .= " AND book_num LIKE '%$book_num_filter%'";
}

// Count total books based on the filtered query
$count_query = str_replace("SELECT *", "SELECT COUNT(*) as total_books", $query);
$count_result = mysqli_query($connection, $count_query);
$total_books_row = mysqli_fetch_assoc($count_result);
$total_books = $total_books_row['total_books'];

// Calculate total pages
$total_pages = ceil($total_books / $books_per_page);

// Add pagination to the main query
$query .= " LIMIT $books_per_page OFFSET $offset";

// Function to build URL with current filters
function buildURL($page) {
    $params = $_GET;
    $params['page'] = $page;
    return '?' . http_build_query($params);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books</title>
    <link rel="stylesheet" href="../style2.css">
    <link rel="stylesheet" href="adminstyle.css">
    <style>
        /* Enhanced Filter Styling */
        .filter-container {
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            max-width: 1500px;
            padding:20px;
            transition: all 0.3s ease;
        }

        .filter-container:hover {
            box-shadow: 0 6px 8px rgba(0,0,0,0.15);
        }

        .filter-container form {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 15px;
        }

        .filter-container label {
            color: #333;
            font-weight: bold;
            margin-right: 8px;
        }

        .filter-container select,
        .filter-container input[type="text"] {
            padding: 10px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.3s ease;
            outline: none;
            width: 150px;
        }

        .filter-container select:focus,
        .filter-container input[type="text"]:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
        }

        .filter-container button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            margin: 0 5px;
        }

        .filter-container button:hover {
            background-color: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
        }

        .filter-container button:active {
            transform: translateY(1px);
            box-shadow: 0 1px 2px rgba(0,0,0,0.2);
        }

        .clear-btn {
            background-color: #6c757d !important;
        }

        .clear-btn:hover {
            background-color: #5a6268 !important;
        }

        .total-books-container {
            text-align: center;
            font-size: 18px;
            color: #555;
            background-color: #e8f5e8;
            padding: 12px;
            border-radius: 6px;
            max-width: 300px;
            margin-left: auto;
            margin-right: auto;
            font-weight: bold;
        }

        /* Book image and name styling */
        .book-info {
            display: flex;
            align-items: center;
            gap: 15px;
            min-height: 80px;
        }

        .book-image {
            width: 60px;
            height: 75px;
            object-fit: cover;
            border-radius: 6px;
            border: 2px solid #ddd;
            flex-shrink: 0;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .book-image:hover {
            border-color: #4CAF50;
            transform: scale(1.05);
        }

        .book-details {
            flex: 1;
            min-width: 0;
        }

        .book-name {
            font-weight: 600;
            font-size: 16px;
            color: #333;
            margin-bottom: 4px;
            word-wrap: break-word;
        }

        .book-meta {
            font-size: 13px;
            color: #666;
            line-height: 1.4;
        }

        .no-image {
            width: 60px;
            height: 75px;
            background-color: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            color: #6c757d;
            text-align: center;
            flex-shrink: 0;
        }

        /* Action buttons styling */
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-direction: column;
        }

        .editbooks-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 8px 16px;
            cursor: pointer;
            text-decoration: none;
            border-radius: 4px;
            font-size: 13px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .delete-btn {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 8px 16px;
            cursor: pointer;
            text-decoration: none;
            border-radius: 4px;
            font-size: 13px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .editbooks-btn:hover {
            background-color: #45a049;
            transform: translateY(-1px);
        }

        .delete-btn:hover {
            background-color: #d32f2f;
            transform: translateY(-1px);
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
            box-shadow: 0 4px 20px rgba(0,0,0,0.5);
        }

        .close-modal {
            position: absolute;
            top: 15px;
            right: 25px;
            color: white;
            font-size: 35px;
            font-weight: bold;
            cursor: pointer;
            z-index: 1001;
        }

        .close-modal:hover {
            opacity: 0.7;
        }

        /* Pagination Styles */
        .pagination-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 30px 0;
            gap: 10px;
            flex-wrap: wrap;
        }

        .pagination-info {
            color: #666;
            font-size: 14px;
            margin: 0 15px;
        }

        .pagination-btn {
            padding: 8px 12px;
            background-color: #f8f9fa;
            color: #333;
            text-decoration: none;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            transition: all 0.3s ease;
            font-size: 14px;
            min-width: 40px;
            text-align: center;
        }

        .pagination-btn:hover {
            background-color: #e9ecef;
            border-color: #adb5bd;
            transform: translateY(-1px);
        }

        .pagination-btn.active {
            background-color: #4CAF50;
            color: white;
            border-color: #4CAF50;
        }

        .pagination-btn.disabled {
            background-color: #f8f9fa;
            color: #6c757d;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .pagination-btn.disabled:hover {
            background-color: #f8f9fa;
            border-color: #dee2e6;
            transform: none;
        }

        .pagination-dots {
            color: #6c757d;
            padding: 0 5px;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .filter-container form {
                flex-direction: column;
                align-items: stretch;
            }
            
            .filter-container select,
            .filter-container input[type="text"] {
                width: 100%;
            }
            
            .book-info {
                flex-direction: column;
                text-align: center;
                gap: 10px;
            }
            
            .action-buttons {
                flex-direction: row;
                justify-content: center;
            }

            .pagination-container {
                gap: 5px;
            }

            .pagination-btn {
                padding: 6px 8px;
                font-size: 12px;
                min-width: 32px;
            }

            .pagination-info {
                font-size: 12px;
                margin: 0 10px;
            }
        }
    </style>
</head>
<body>
    <?php include('adminnavbar.php'); ?>

    <div class="main">
        <?php include('sidebar.php'); ?>

        <div class="container">
            <h2 class="h2-register-header">Manage Books</h2>

            <!-- Filter Section -->
            <div class="filter-container">
                <form method="GET" action="">
                    <label for="book_name">Book Name:</label>
                    <input type="text" id="book_name" name="book_name" pattern="[A-Za-z\s.]+" value="<?php echo htmlspecialchars($book_name_filter); ?>" placeholder="Search book name">

                    <label for="book_num">Book Number:</label>
                    <input type="text" id="book_num" name="book_num" pattern="\d{13}" value="<?php echo htmlspecialchars($book_num_filter); ?>" placeholder="Search book number">

                    <label for="author">Author:</label>
                    <input type="text" id="author" name="author" pattern="[A-Za-z\s.]+" value="<?php echo htmlspecialchars($author_filter); ?>" placeholder="Search author">

                    <label for="publication">Publication:</label>
                    <input type="text" id="publication" name="publication" pattern="[A-Za-z\s.]+" value="<?php echo htmlspecialchars($publication_filter); ?>" placeholder="Search publication">

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
                    <button type="button" class="clear-btn" onclick="window.location.href = window.location.pathname;">Clear Filters</button>
                </form>
            </div>

            <!-- Total Books Count -->
            <div class="total-books-container">
                Total Books: <?php echo $total_books; ?>
            </div>

            <!-- Pagination Info -->
            <?php if ($total_books > 0): ?>
            <div class="pagination-info" style="text-align: center; margin: 20px 0; color: #666;">
                Showing <?php echo min(($current_page - 1) * $books_per_page + 1, $total_books); ?> to <?php echo min($current_page * $books_per_page, $total_books); ?> of <?php echo $total_books; ?> books
            </div>
            <?php endif; ?>

            <table>
                <thead>
                    <tr>
                        <th>Book Info</th>
                        <th>Book Num</th>
                        <th>Edition</th>
                        <th>Publication</th>
                        <th>Faculty</th>
                        <th>Semester</th>
                        <th>Total Qty</th>
                        <th>Available Qty</th>
                        <th>Issued Qty</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $query_run = mysqli_query($connection, $query);
                while($row = mysqli_fetch_assoc($query_run)) {
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
                            <div class="book-details">
                                <div class="book-name"><?php echo htmlspecialchars($bname); ?></div>
                                <div class="book-meta">
                                    <strong>Author:</strong> <?php echo htmlspecialchars($author); ?><br>
                                    <strong>Edition:</strong> <?php echo htmlspecialchars($bedition); ?>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td><?php echo htmlspecialchars($bnum); ?></td>
                    <td><?php echo htmlspecialchars($bedition); ?></td>
                    <td><?php echo htmlspecialchars($publication); ?></td>
                    <td><?php echo htmlspecialchars($faculty); ?></td>
                    <td><?php echo htmlspecialchars($semester); ?></td>
                    <td><?php echo htmlspecialchars($total_quantity); ?></td>
                    <td><?php echo htmlspecialchars($available_quantity); ?></td>
                    <td><?php echo htmlspecialchars($issued_quantity); ?></td>
                    <td>
                        <div class="action-buttons">
                            <a href="editbooks.php?bn=<?php echo $row['book_num']; ?>" class="editbooks-btn">Edit</a>
                            <a href="managebooks.php?delete_id=<?php echo $row['book_num']; ?>" 
                               class="delete-btn" 
                               onclick="return confirm('Are you sure you want to delete this book?')">Delete</a>
                        </div>
                    </td>
                </tr>
                <?php
                }
                ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="pagination-container">
                <!-- First Page -->
                <?php if ($current_page > 1): ?>
                    <a href="<?php echo buildURL(1); ?>" class="pagination-btn">First</a>
                    <a href="<?php echo buildURL($current_page - 1); ?>" class="pagination-btn">Previous</a>
                <?php else: ?>
                    <span class="pagination-btn disabled">First</span>
                    <span class="pagination-btn disabled">Previous</span>
                <?php endif; ?>

                <!-- Page Numbers -->
                <?php
                $start_page = max(1, $current_page - 2);
                $end_page = min($total_pages, $current_page + 2);

                // Show dots if there are pages before start_page
                if ($start_page > 1): ?>
                    <a href="<?php echo buildURL(1); ?>" class="pagination-btn">1</a>
                    <?php if ($start_page > 2): ?>
                        <span class="pagination-dots">...</span>
                    <?php endif; ?>
                <?php endif;

                // Show page numbers
                for ($i = $start_page; $i <= $end_page; $i++): ?>
                    <a href="<?php echo buildURL($i); ?>" 
                       class="pagination-btn <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor;

                // Show dots if there are pages after end_page
                if ($end_page < $total_pages): ?>
                    <?php if ($end_page < $total_pages - 1): ?>
                        <span class="pagination-dots">...</span>
                    <?php endif; ?>
                    <a href="<?php echo buildURL($total_pages); ?>" class="pagination-btn"><?php echo $total_pages; ?></a>
                <?php endif; ?>

                <!-- Next and Last Page -->
                <?php if ($current_page < $total_pages): ?>
                    <a href="<?php echo buildURL($current_page + 1); ?>" class="pagination-btn">Next</a>
                    <a href="<?php echo buildURL($total_pages); ?>" class="pagination-btn">Last</a>
                <?php else: ?>
                    <span class="pagination-btn disabled">Next</span>
                    <span class="pagination-btn disabled">Last</span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
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

        // Prevent modal from closing when clicking on the image
        document.getElementById('modalImage').addEventListener('click', function(event) {
            event.stopPropagation();
        });
    </script>

</body>
</html>