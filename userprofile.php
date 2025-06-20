
<?php
require('admin/functions.php');
session_start();
$connection = mysqli_connect("localhost", "root", "");
$db = mysqli_select_db($connection, "library");

// Default filter values
$returnedFilter = isset($_GET['returned']) ? $_GET['returned'] : 'all';
$bookNameFilter = isset($_GET['book_name']) ? $_GET['book_name'] : '';
$bookNumFilter = isset($_GET['book_num']) ? $_GET['book_num'] : '';
$semesterFilter = isset($_GET['semester']) ? $_GET['semester'] : '';
$facultyFilter = isset($_GET['faculty']) ? $_GET['faculty'] : '';
$dueDateFilter = isset($_GET['due_date']) ? $_GET['due_date'] : '';

// Check if the user is logged in
if (!isset($_SESSION['Name'])) {
    header("Location: index.php");
    exit();
}

// SQL query to get currently issued books for the logged-in student - INCLUDING DUE_DATE
$query = "SELECT book_name, book_num, book_author, issue_date, returned_date, publication, faculty, semester, returned, picture, due_date FROM issued WHERE student_id = $_SESSION[ID]";

// Add conditions to filter by return status
if ($returnedFilter === 'returned') {
    $query .= " AND returned = 1";
} elseif ($returnedFilter === 'not_returned') {
    $query .= " AND (returned = 0 OR returned IS NULL)";
} elseif ($returnedFilter === 'overdue') {
    $query .= " AND (returned = 0 OR returned IS NULL) AND due_date < CURDATE()";
} elseif ($returnedFilter === 'due_soon') {
    $query .= " AND (returned = 0 OR returned IS NULL) AND due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
}

// Add other filters
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

if ($dueDateFilter) {
    $query .= " AND due_date = '$dueDateFilter'";
}

// Query to get total issued books
$totalIssuedQuery = "SELECT COUNT(*) AS totalIssued FROM issued WHERE student_id = $_SESSION[ID]";

// Query to get total returned books
$totalReturnedQuery = "SELECT COUNT(*) AS totalReturned FROM issued WHERE student_id = $_SESSION[ID] AND returned = 1";

// Query to get currently issued books
$CurrentlyIssuedQuery = "SELECT COUNT(*) AS currentlyissued FROM issued WHERE student_id = $_SESSION[ID] AND (returned IS NULL OR returned = 0)";

// Query to get overdue books
$overdueQuery = "SELECT COUNT(*) AS overdue FROM issued WHERE student_id = $_SESSION[ID] AND (returned IS NULL OR returned = 0) AND due_date < CURDATE()";

// Query to get books due soon (within 7 days)
$dueSoonQuery = "SELECT COUNT(*) AS dueSoon FROM issued WHERE student_id = $_SESSION[ID] AND (returned IS NULL OR returned = 0) AND due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)";

// Run the queries
$totalIssuedResult = mysqli_query($connection, $totalIssuedQuery);
$totalIssuedData = mysqli_fetch_assoc($totalIssuedResult);

$totalReturnedResult = mysqli_query($connection, $totalReturnedQuery);
$totalReturnedData = mysqli_fetch_assoc($totalReturnedResult);

$CurrentlyIssuedResult = mysqli_query($connection, $CurrentlyIssuedQuery);
$CurrentlyIssuedData = mysqli_fetch_assoc($CurrentlyIssuedResult);

$overdueResult = mysqli_query($connection, $overdueQuery);
$overdueData = mysqli_fetch_assoc($overdueResult);

$dueSoonResult = mysqli_query($connection, $dueSoonQuery);
$dueSoonData = mysqli_fetch_assoc($dueSoonResult);

// Query to get requested books for the logged-in student
$requestedBooksQuery = "SELECT book_name, book_num, author_name, request_date, status FROM book_request WHERE student_id = $_SESSION[ID] AND status != ''";
$requestedBooksResult = mysqli_query($connection, $requestedBooksQuery);

// Function to calculate days difference
function getDaysDifference($date) {
    $today = new DateTime();
    $dueDate = new DateTime($date);
    $diff = $today->diff($dueDate);
    return $diff->invert ? -$diff->days : $diff->days;
}

// Function to get status info based on due date
function getDueDateStatus($dueDate, $returned) {
    if ($returned == 1) {
        return ['status' => 'returned', 'class' => 'status-returned', 'text' => 'Returned'];
    }
    
    if (empty($dueDate)) {
        return ['status' => 'no_due', 'class' => 'status-no-due', 'text' => 'No Due Date'];
    }
    
    $daysDiff = getDaysDifference($dueDate);
    
    if ($daysDiff < 0) {
        return ['status' => 'overdue', 'class' => 'status-overdue', 'text' => 'Overdue (' . abs($daysDiff) . ' days)'];
    } elseif ($daysDiff <= 7) {
        return ['status' => 'due_soon', 'class' => 'status-due-soon', 'text' => 'Due in ' . $daysDiff . ' days'];
    } else {
        return ['status' => 'active', 'class' => 'status-active', 'text' => 'Active'];
    }
}
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
        .container {
            max-width: 1800px;
            margin: 0 auto;
            padding: 20px;
        }

        .cards-container {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            flex-direction: column;
        }

        @media (min-width: 768px) {
            .cards-container {
                flex-direction: row;
                justify-content: center;
                align-items: center;
                display: flex;
            }
        }

        .card {
            background-color: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            flex: 1;
            text-align: center;
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
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
            margin-bottom: 15px;
        }

        .card-total .icon { color: #4361ee; }
        .card-returned .icon { color: #10b981; }
        .card-current .icon { color: #3b82f6; }
        .card-overdue .icon { color: #ef4444; }
        .card-due-soon .icon { color: #f59e0b; }

        .card-overdue {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            border: 2px solid #fca5a5;
        }

        .card-due-soon {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 2px solid #fbbf24;
        }

        .filter-container {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .filter-group label {
            font-weight: 600;
            color: #374151;
            font-size: 0.9rem;
        }

        .filter-container select,
        .filter-container input {
            padding: 10px 12px;
            font-size: 1rem;
            border-radius: 8px;
            border: 2px solid #e5e7eb;
            transition: border-color 0.2s ease;
            min-width: 150px;
        }

        .filter-container select:focus,
        .filter-container input:focus {
            outline: none;
            border-color: #4361ee;
        }

        .filter-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 15px;
        }

        .filter-btn {
            padding: 12px 24px;
            font-size: 1rem;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .filter-btn-primary {
            background-color: #4361ee;
            color: white;
        }

        .filter-btn-primary:hover {
            background-color: #3651db;
            transform: translateY(-1px);
        }

        .filter-btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .filter-btn-secondary:hover {
            background-color: #5a6268;
            transform: translateY(-1px);
        }

        .books-cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }

        .book-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 20px;
            border: 2px solid transparent;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .book-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
        }

        /* Due date specific card styling */
        .book-card-overdue {
            border-color: #ef4444;
            background: linear-gradient(135deg, #ffffff 0%, #fef2f2 100%);
        }

        .book-card-due-soon {
            border-color: #f59e0b;
            background: linear-gradient(135deg, #ffffff 0%, #fffbeb 100%);
        }

        .book-card-returned {
            border-color: #10b981;
            background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%);
        }

        .book-image-container {
            width: 100%;
            border-radius: 12px;
            overflow: hidden;
            aspect-ratio: 1 / 1.1;
            margin-bottom: 16px;
            position: relative;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f3f4f6;
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
        }

        .book-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #1f2937;
            margin: 0 0 8px 0;
            line-height: 1.3;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .book-author {
            font-size: 1rem;
            color: #6b7280;
            margin-bottom: 16px;
            font-style: italic;
        }

        .book-details {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .book-detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9rem;
        }

        .detail-label {
            color: #4b5563;
            font-weight: 600;
        }

        .detail-value {
            color: #1f2937;
            font-weight: 700;
            text-align: right;
        }

        .due-date-row {
            background: #f8fafc;
            padding: 8px 12px;
            border-radius: 8px;
            margin: 8px 0;
        }

        .due-date-overdue {
            background: #fef2f2;
            border: 1px solid #fecaca;
        }

        .due-date-soon {
            background: #fffbeb;
            border: 1px solid #fed7aa;
        }

        .semester-faculty {
            display: flex;
            gap: 8px;
            margin-top: 12px;
        }

        .semester-badge, .faculty-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .semester-badge {
            background: #e0f2fe;
            color: #0277bd;
        }

        .faculty-badge {
            background: #f3e5f5;
            color: #7b1fa2;
        }

        .status-container {
            position: absolute;
            top: 16px;
            right: 16px;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-returned {
            background: #10b981;
            color: white;
        }

        .status-overdue {
            background: #ef4444;
            color: white;
            animation: pulse 2s infinite;
        }

        .status-due-soon {
            background: #f59e0b;
            color: white;
        }

        .status-active {
            background: #3b82f6;
            color: white;
        }

        .status-no-due {
            background: #6b7280;
            color: white;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .empty-state {
            padding: 60px;
            text-align: center;
            color: #9ca3af;
            grid-column: 1 / -1;
            background: #f9fafb;
            border-radius: 12px;
            border: 2px dashed #d1d5db;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 16px;
            color: #d1d5db;
        }

        .requested-books-section {
            margin-top: 40px;
        }

        .table-container {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            padding: 20px;
            overflow-x: auto;
        }

        .table-container table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-container th,
        .table-container td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .table-container th {
            background-color: #f9fafb;
            font-weight: 600;
            color: #374151;
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

        <!-- Enhanced Cards Section -->
        <div class="cards-container">
            <!-- Total Issued Books -->
            <div class="card card-total">
                <i class="fas fa-book icon"></i>
                <h3><?php echo $totalIssuedData['totalIssued']; ?></h3>
                <p>Total Issued Books</p>
            </div>

            <!-- Total Returned Books -->
            <div class="card card-returned">
                <i class="fas fa-check-circle icon"></i>
                <h3><?php echo $totalReturnedData['totalReturned']; ?></h3>
                <p>Total Returned Books</p>
            </div>

            <!-- Currently Issued Books -->
            <div class="card card-current">
                <i class="fas fa-book-open icon"></i>
                <h3><?php echo $CurrentlyIssuedData['currentlyissued']; ?></h3>
                <p>Currently Issued Books</p>
            </div>

            <!-- Overdue Books -->
            <div class="card card-overdue">
                <i class="fas fa-exclamation-triangle icon"></i>
                <h3><?php echo $overdueData['overdue']; ?></h3>
                <p>Overdue Books</p>
            </div>

            <!-- Due Soon Books -->
            <div class="card card-due-soon">
                <i class="fas fa-clock icon"></i>
                <h3><?php echo $dueSoonData['dueSoon']; ?></h3>
                <p>Due Soon (7 days)</p>
            </div>
        </div>

        <!-- Enhanced Filter Section -->
        <div class="filter-container">
            <form method="GET" action="">
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="returned">Return Status:</label>
                        <select name="returned" id="returned">
                            <option value="all" <?php echo $returnedFilter === 'all' ? 'selected' : ''; ?>>All Books</option>
                            <option value="returned" <?php echo $returnedFilter === 'returned' ? 'selected' : ''; ?>>Returned</option>
                            <option value="not_returned" <?php echo $returnedFilter === 'not_returned' ? 'selected' : ''; ?>>Not Returned</option>
                            <option value="overdue" <?php echo $returnedFilter === 'overdue' ? 'selected' : ''; ?>>Overdue</option>
                            <option value="due_soon" <?php echo $returnedFilter === 'due_soon' ? 'selected' : ''; ?>>Due Soon</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="book_name">Book Name:</label>
                        <input type="text" name="book_name" id="book_name" pattern="[A-Za-z\s.]+" value="<?php echo $bookNameFilter; ?>" placeholder="Search by book name">
                    </div>

                    <div class="filter-group">
                        <label for="book_num">Book Number:</label>
                        <input type="text" name="book_num" id="book_num" pattern="\d{13}" value="<?php echo $bookNumFilter; ?>" placeholder="Enter 13-digit number">
                    </div>
                </div>

                <div class="filter-row">
                    <div class="filter-group">
                        <label for="semester">Semester:</label>
                        <input type="text" name="semester" id="semester" pattern="\d{1}" value="<?php echo $semesterFilter; ?>" placeholder="Semester (1-8)">
                    </div>

                    <div class="filter-group">
                        <label for="faculty">Faculty:</label>
                        <input type="text" name="faculty" id="faculty" pattern="[A-Za-z\s.]+" value="<?php echo $facultyFilter; ?>" placeholder="Faculty name">
                    </div>

                    <div class="filter-group">
                        <label for="due_date">Due Date:</label>
                        <input type="date" name="due_date" id="due_date" value="<?php echo $dueDateFilter; ?>">
                    </div>
                </div>

                <div class="filter-buttons">
                    <button type="submit" class="filter-btn filter-btn-primary">
                        <i class="fas fa-search"></i> Apply Filters
                    </button>
                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="filter-btn filter-btn-secondary">
                        <i class="fas fa-times"></i> Reset Filters
                    </a>
                </div>
            </form>
        </div>

        <!-- Enhanced Books Cards Section -->
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
                    $picture = $row['picture'];
                    $due_date = $row['due_date'];

                    // Get status information
                    $statusInfo = getDueDateStatus($due_date, $returned);
                    $cardClass = '';
                    
                    switch($statusInfo['status']) {
                        case 'overdue':
                            $cardClass = 'book-card-overdue';
                            break;
                        case 'due_soon':
                            $cardClass = 'book-card-due-soon';
                            break;
                        case 'returned':
                            $cardClass = 'book-card-returned';
                            break;
                    }
            ?>
                    <div class="book-card <?php echo $cardClass; ?>">
                        <div class="book-image-container">
                            <?php if (!empty($picture) && file_exists("./admin/upload/{$picture}")) { ?>
                                <img src="./admin/upload/<?php echo htmlspecialchars($picture); ?>" alt="<?php echo htmlspecialchars($bname); ?>" class="book-image">
                            <?php } else { ?>
                                <i class="fas fa-book placeholder-image"></i>
                            <?php } ?>
                        </div>
                        
                        <h3 class="book-title"><?php echo htmlspecialchars($bname); ?></h3>
                        <p class="book-author">by <?php echo htmlspecialchars($author); ?></p>
                        
                        <div class="book-details">
                            <div class="book-detail-row">
                                <span class="detail-label">Book #:</span>
                                <span class="detail-value"><?php echo $bnum; ?></span>
                            </div>
                            <div class="book-detail-row">
                                <span class="detail-label">Issue Date:</span>
                                <span class="detail-value"><?php echo date('M d, Y', strtotime($date)); ?></span>
                            </div>
                            
                            <?php if (!empty($due_date)) { 
                                $dueClass = '';
                                if ($statusInfo['status'] == 'overdue') {
                                    $dueClass = 'due-date-overdue';
                                } elseif ($statusInfo['status'] == 'due_soon') {
                                    $dueClass = 'due-date-soon';
                                }
                            ?>
                            <div class="book-detail-row due-date-row <?php echo $dueClass; ?>">
                                <span class="detail-label">
                                    <i class="fas fa-calendar-alt"></i> Due Date:
                                </span>
                                <span class="detail-value"><?php echo date('M d, Y', strtotime($due_date)); ?></span>
                            </div>
                            <?php } ?>
                            
                            <?php if ($returned_date) { ?>
                            <div class="book-detail-row">
                                <span class="detail-label">Return Date:</span>
                                <span class="detail-value"><?php echo date('M d, Y', strtotime($returned_date)); ?></span>
                            </div>
                            <?php } ?>
                        </div>
                        
                        <div class="semester-faculty">
                            <span class="semester-badge">Sem <?php echo $semester; ?></span>
                            <span class="faculty-badge"><?php echo htmlspecialchars($faculty); ?></span>
                        </div>
                        
                        <div class="status-container">
                            <span class="status-badge <?php echo $statusInfo['class']; ?>">
                                <?php echo $statusInfo['text']; ?>
                            </span>
                        </div>
                    </div>
            <?php
                }
            } else {
            ?>
                <div class="empty-state">
                    <i class="fas fa-book-open"></i>
                    <h3>No books found</h3>
                    <p>No books match your current filter criteria.</p>
                </div>
            <?php
            }
            ?>
        </div>

        <!-- Requested Books Section -->
        <div class="requested-books-section">
            <button onclick="window.location.href='?show_requested=true'" style="padding: 12px 24px; margin-bottom:20px; background-color: #4361ee; color: white; border-radius: 8px; border: none; cursor: pointer; font-weight: 600;">
                <i class="fas fa-list"></i> Show Requested Books
            </button>

            <?php if (isset($_GET['show_requested']) && $_GET['show_requested'] == 'true') { ?>
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
                                    <tr style="<?php 
                                        if ($status == 'rejected') {
                                            echo 'background-color: #fef2f2;';
                                        } elseif ($status == 'approved') {
                                            echo 'background-color: #f0fdf4;';
                                        }
                                    ?>">
                                        <td><?php echo htmlspecialchars($requestedBookName); ?></td>
                                        <td><?php echo htmlspecialchars($requestedBookNum); ?></td>
                                        <td><?php echo htmlspecialchars($requestedAuthor); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($requestDate)); ?></td>
                                        <td><span style="padding: 4px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: 600; 
                                            <?php 
                                            if ($status == 'approved') echo 'background: #dcfce7; color: #166534;';
                                            elseif ($status == 'rejected') echo 'background: #fee2e2; color: #991b1b;';
                                            else echo 'background: #fef3c7; color: #92400e;';
                                            ?>">
                                            <?php echo ucfirst($status); ?>
                                        </span></td>
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
            <?php } ?>
        </div>
    </div>
    <?php include("Recommended_Books.php"); ?>

</body>

</html>