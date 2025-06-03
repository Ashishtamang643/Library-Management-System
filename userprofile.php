<?php
require('admin/functions.php');
session_start();
$connection = mysqli_connect("localhost", "root", "");
$db = mysqli_select_db($connection, "library");

// Default filter values
$returnedFilter = isset($_GET['returned']) ? $_GET['returned'] : 'all'; // Get filter from the URL
$bookNameFilter = isset($_GET['book_name']) ? $_GET['book_name'] : ''; // Book Name filter
$bookNumFilter = isset($_GET['book_num']) ? $_GET['book_num'] : ''; // Book Number filter
$semesterFilter = isset($_GET['semester']) ? $_GET['semester'] : ''; // Semester filter
$facultyFilter = isset($_GET['faculty']) ? $_GET['faculty'] : ''; // Faculty filter

// SQL query to get currently issued books for the logged-in student - INCLUDING PICTURE COLUMN
$query = "SELECT book_name, book_num, book_author, issue_date, returned_date, publication, faculty, semester, returned, picture FROM issued WHERE student_id = $_SESSION[ID]";

// Add conditions to filter by return status, book name, book number, semester, and faculty
if ($returnedFilter === 'returned') {
    $query .= " AND returned = 1";
} elseif ($returnedFilter === 'not_returned') {
    $query .= " AND (returned = 0 OR returned IS NULL)";
}

if ($bookNameFilter) {
    $query .= " AND book_name LIKE '%$bookNameFilter%'";
}

if ($bookNumFilter) {
    $query .= " AND book_num LIKE '%$bookNumFilter%'";
}

if ($semesterFilter) {
    $query .= " AND semester LIKE '%$semesterFilter%'";
}

if ($facultyFilter) {
    $query .= " AND faculty LIKE '%$facultyFilter%'";
}

// Query to get total issued books
$totalIssuedQuery = "SELECT COUNT(*) AS totalIssued FROM issued WHERE student_id = $_SESSION[ID]";

// Query to get total returned books
$totalReturnedQuery = "SELECT COUNT(*) AS totalReturned FROM issued WHERE student_id = $_SESSION[ID] AND returned = 1";
$CurrentlyIssuedQuery = "SELECT COUNT(*) AS currentlyissued FROM issued WHERE student_id = $_SESSION[ID] AND returned IS NULL";

// Run the queries
$totalIssuedResult = mysqli_query($connection, $totalIssuedQuery);
$totalIssuedData = mysqli_fetch_assoc($totalIssuedResult);

$totalReturnedResult = mysqli_query($connection, $totalReturnedQuery);
$totalReturnedData = mysqli_fetch_assoc($totalReturnedResult);

$CurrentlyIssuedResult = mysqli_query($connection, $CurrentlyIssuedQuery);
$CurrentlyIssuedData = mysqli_fetch_assoc($CurrentlyIssuedResult);

// Query to get requested books for the logged-in student
$requestedBooksQuery = "SELECT book_name, book_num, author_name, request_date, status
                        FROM book_request 
                        WHERE student_id = $_SESSION[ID] 
                        AND status != ''";
$requestedBooksResult = mysqli_query($connection, $requestedBooksQuery);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Library Dashboard</title>
    <link rel="stylesheet" href="style2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Add your existing styles here */
        .cards-container {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background-color: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            flex: 1;
            text-align: center;
        }

        .card h3 {
            font-size: 2rem;
            margin-bottom: 10px;
            color: #2d3748;
        }

        .card p {
            font-size: 1.1rem;
            color: #4a5568;
        }

        .card .icon {
            font-size: 3rem;
            color: #4361ee;
            margin-bottom: 15px;
        }

        .filter-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .filter-container select,
        .filter-container input {
            padding: 10px;
            font-size: 1rem;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-right: 10px;
        }

        .table-container {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            padding: 20px;
        }

        /* NEW Book Cards Styles - Matching the image design */
        .books-cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .book-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            padding: 16px;
            border: 1px solid #f0f0f0;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            height: fit-content;
        }

        .book-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        }

        .book-image-container {
            width: 100%;
            border-radius: 8px;
            overflow: hidden;
            aspect-ratio: 1 / 1.1;

            margin-bottom: 12px;
            position: relative;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .book-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .placeholder-image {
            color: white;
            font-size: 4rem;
            opacity: 0.7;
            object-fit: contain;
        }

        .book-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2d3748;
            margin: 0 0 8px 0;
            line-height: 1.3;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .book-author {
            font-size: 0.9rem;
            color: #718096;
            margin-bottom: 12px;
            font-style: italic;
        }

        .book-details {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .book-detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
        }

        .detail-label {
            color: #4a5568;
            font-weight: 500;
        }

        .detail-value {
            color: #2d3748;
            font-weight: 600;
            text-align: right;
        }

        .semester-faculty {
            display: flex;
            gap: 8px;
            margin-top: 8px;
        }

        .semester-badge, .faculty-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .semester-badge {
            background: #e6f3ff;
            color: #0066cc;
        }

        .faculty-badge {
            background: #f0f9ff;
            color: #0284c7;
        }

        .status-container {
            margin-top: 12px;
            text-align: center;
            position:absolute;
            top: 0;
            right:10px;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            width: 100%;
        }

        .status-returned {
            background: #10b981;
            color: white;
        }

        .status-not-returned {
            background: #ef4444;
            color: white;
        }

        .availability-count {
            position: absolute;
            top: 12px;
            right: 12px;
            background: #10b981;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .empty-state {
            padding: 40px;
            text-align: center;
            color: #a0aec0;
            grid-column: 1 / -1;
        }

        .requested-books-section {
            margin-top: 40px;
        }

        .requested-books-section table {
            width: 100%;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <?php include('navbar.php'); ?>

    <div class="container">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <div class="welcome-section">
                <span class="welcome-user">Welcome, <span class="user-email"><?php echo $_SESSION['Name']; ?></span></span>
            </div>
        </div>

        <!-- Cards Section -->
        <div class="cards-container">
            <!-- Total Issued Books -->
            <div class="card">
                <i class="fas fa-book icon"></i>
                <h3><?php echo $totalIssuedData['totalIssued']; ?></h3>
                <p>Total Issued Books</p>
            </div>

            <!-- Total Returned Books -->
            <div class="card">
                <i class="fas fa-undo icon"></i>
                <h3><?php echo $totalReturnedData['totalReturned']; ?></h3>
                <p>Total Returned Books</p>
            </div>

            <div class="card">
                <i class="fas fa-book-open icon"></i>
                <h3><?php echo $CurrentlyIssuedData['currentlyissued']; ?></h3>
                <p>Currently Issued Books</p>
            </div>
        </div>

        <!-- Filter Section for Currently Issued Books -->
        <div class="filter-container">
            <form method="GET" action="">
                <label for="returned">Filter by Return Status:</label>
                <select name="returned" id="returned" onchange="this.form.submit()">
                    <option value="all" <?php echo $returnedFilter === 'all' ? 'selected' : ''; ?>>All</option>
                    <option value="returned" <?php echo $returnedFilter === 'returned' ? 'selected' : ''; ?>>Returned</option>
                    <option value="not_returned" <?php echo $returnedFilter === 'not_returned' ? 'selected' : ''; ?>>Not Returned</option>
                </select>
                <input type="text" name="book_name" value="<?php echo $bookNameFilter; ?>" placeholder="Filter by Book Name" onchange="this.form.submit()">
                <input type="text" name="book_num" value="<?php echo $bookNumFilter; ?>" placeholder="Filter by Book Number" onchange="this.form.submit()">
                <input type="text" name="semester" value="<?php echo $semesterFilter; ?>" placeholder="Filter by Semester" onchange="this.form.submit()">
                <input type="text" name="faculty" value="<?php echo $facultyFilter; ?>" placeholder="Filter by Faculty" onchange="this.form.submit()">
            </form>
        </div>

        <!-- Currently Issued Books Section - Updated Card View -->
        <div class="books-cards-container">
            <?php
            $query_run = mysqli_query($connection, $query);
            $row_count = mysqli_num_rows($query_run);

            if ($row_count > 0) {
                while ($row = mysqli_fetch_assoc($query_run)) {
                    $bname = $row['book_name'];
                    $bnum = $row['book_num'];
                    $author = $row['book_author'];
                    $publication = $row['publication'];
                    $faculty = $row['faculty'];
                    $semester = $row['semester'];
                    $date = $row['issue_date'];
                    $returned = $row['returned'];
                    $returned_date = $row['returned_date'];
                    $picture = $row['picture']; // Get the picture column

                    // Determine the return status
                    $status = ($returned == 1) ? 'Returned' : 'Not Returned';
                    $statusClass = ($returned == 1) ? 'status-returned' : 'status-not-returned';
            ?>
                    <div class="book-card">
                        <div class="book-image-container">
                            <?php if (!empty($picture) && file_exists("./admin/upload/{$picture}")) { ?>
                        <img src="./admin/upload/<?php echo htmlspecialchars($picture); ?>" alt="<?php echo htmlspecialchars($bname); ?>">
                    <?php } else { ?>
                       <img src="./admin/upload/placeholder.png" alt="No Image Available">
                    <?php } ?>
                        </div>
                        
                        <h3 class="book-title"><?php echo $bname; ?></h3>
                        <p class="book-author">by <?php echo $author; ?></p>
                        
                        <div class="book-details">
                            <div class="book-detail-row">
                                <span class="detail-label">Book #:</span>
                                <span class="detail-value"><?php echo $bnum; ?></span>
                            </div>
                            <div class="book-detail-row">
                                <span class="detail-label">Issue Date:</span>
                                <span class="detail-value"><?php echo date('M d, Y', strtotime($date)); ?></span>
                            </div>
                            <?php if ($returned_date) { ?>
                            <div class="book-detail-row">
                                <span class="detail-label">Return Date:</span>
                                <span class="detail-value"><?php echo date('M d, Y', strtotime($returned_date)); ?></span>
                            </div>
                            <?php } ?>
                        </div>
                        
                        <div class="semester-faculty">
                            <span class="semester-badge">Sem <?php echo $semester; ?></span>
                            <span class="faculty-badge"><?php echo $faculty; ?></span>
                        </div>
                        
                        <div class="status-container">
                            <span class="status-badge <?php echo $statusClass; ?>"><?php echo $status; ?></span>
                        </div>
                    </div>
            <?php
                }
            } else {
            ?>
                <div class="empty-state">
                    <i class="fas fa-books"></i>
                    <p>You haven't borrowed any books yet.</p>
                </div>
            <?php
            }
            ?>
        </div>

        <!-- Button to Show Requested Books -->
        <div class="requested-books-section">
            <button onclick="window.location.href='?show_requested=true'" style="padding: 10px 20px; background-color: #4361ee; color: white; border-radius: 5px; border: none; cursor: pointer;">Show Requested Books</button>

            <?php
            if (isset($_GET['show_requested']) && $_GET['show_requested'] == 'true') {
            ?>
                <h3>Requested Books</h3>
                <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Book Name</th>
                            <th>Book Number</th>
                            <th>Author</th>
                            <th>Request Date</th>
                            <th>Status</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($requestedBooksResult) > 0) {
                            while ($row = mysqli_fetch_assoc($requestedBooksResult)) {
                                $requestedBookName = $row['book_name'];
                                $requestedBookNum = $row['book_num'];
                                $requestedAuthor = $row['author_name'];
                                $requestDate = $row['request_date'];
                                $status = $row['status'];
                        ?>
                                <tr>
                                    <td><?php echo $requestedBookName; ?></td>
                                    <td><?php echo $requestedBookNum; ?></td>
                                    <td><?php echo $requestedAuthor; ?></td>
                                    <td><?php echo $requestDate; ?></td>
                                    <td><?php echo $status; ?></td>
                                </tr>
                        <?php
                            }
                        } else {
                        ?>
                            <tr>
                                <td colspan="5" class="empty-state">No requested books found.</td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
    <?php include("Recommended_Books.php"); ?>

</body>

</html>