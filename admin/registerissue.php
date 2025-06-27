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
$filter_student_id = isset($_GET['student_id']) ? mysqli_real_escape_string($connection, $_GET['student_id']) : '';
$filter_book_name = isset($_GET['book_name']) ? mysqli_real_escape_string($connection, $_GET['book_name']) : '';
$filter_book_num = isset($_GET['book_num']) ? mysqli_real_escape_string($connection, $_GET['book_num']) : '';
$filter_returned = isset($_GET['returned']) ? mysqli_real_escape_string($connection, $_GET['returned']) : '';

// Process return action if submitted
if(isset($_POST['return_book'])) {
    $return_book_num = $_POST['book_num'];
    $return_student_id = $_POST['student_id'];
    $current_date = date("Y-m-d");

    // Update the issued table to mark the book as returned
    $return_query = "UPDATE issued SET returned = 1, returned_date = '$current_date' 
                     WHERE book_num = '$return_book_num' AND student_id = '$return_student_id'";
    mysqli_query($connection, $return_query);

    // Increase available_quantity in the books table by 1 for the returned book
    $update_quantity_query = "UPDATE books SET available_quantity = available_quantity + 1 
                              WHERE book_num = '$return_book_num'";
    mysqli_query($connection, $update_quantity_query);
    
    // Redirect to avoid form resubmission on page refresh
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Build query with filters
$query = "SELECT issued.student_id, issued.book_name, issued.book_author, issued.book_num,
          issued.issue_date, issued.due_date, issued.returned, issued.returned_date, 
          users.Name, users.Email 
          FROM issued LEFT JOIN users ON issued.student_id = users.ID
          WHERE 1=1";

if (!empty($filter_student_id)) {
    $query .= " AND issued.student_id LIKE '%$filter_student_id%'";
}
if (!empty($filter_book_name)) {
    $query .= " AND issued.book_name LIKE '%$filter_book_name%'";
}
if (!empty($filter_book_num)) {
    $query .= " AND issued.book_num LIKE '%$filter_book_num%'";
}
if ($filter_returned !== '') {
    $query .= " AND issued.returned = '$filter_returned'";
}

// Order by latest issued first
$query .= " ORDER BY issued.createdAt DESC";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issued Books</title>
    <link rel="stylesheet" href="../style2.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
        }

        .filter-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
            align-items: center;
            max-width:1500px;
            left:50%;
            position:relative;
            transform: translateX(-50%);
        }

        .filter-container label {
            margin-right: 8px;
            color: #333;
            font-weight: bold;
        }

        .filter-container input[type="text"],
        .filter-container select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 200px;
            transition: border-color 0.3s ease;
        }

        .filter-container input[type="text"]:focus,
        .filter-container select:focus {
            outline: none;
            border-color: #4CAF50;
        }

        .filter-container button {
            padding: 8px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .filter-container button:hover {
            background-color: #45a049;
        }

        .filter-container .reset-btn {
            background-color: #6c757d;
        }

        .filter-container .reset-btn:hover {
            background-color: #5a6268;
        }

        .button-group {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .return-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-right: 5px;
        }

        .return-btn:hover {
            background-color: #45a049;
        }

        .notify-btn {
            background-color: #ff9800;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-size: 12px;
        }

        .notify-btn:hover {
            background-color: #f57c00;
        }

        .returned-status {
            color: #888;
            font-style: italic;
        }

        .due-soon {
            background-color: #fff3cd;
            color: #856404;
        }

        .overdue {
            background-color: #f8d7da;
            color: #721c24;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
            align-items: center;
        }

        /* Email Dialog Styles */
        .email-dialog {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .dialog-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: none;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        
        .dialog-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .dialog-header h3 {
            margin: 0;
            color: #333;
            font-size: 1.4em;
        }
        
        .close-btn {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #999;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .close-btn:hover {
            color: #333;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #555;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            font-family: inherit;
            box-sizing: border-box;
        }
        
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #ff9800;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }
        
        .readonly-field {
            background-color: #f5f5f5;
            color: #666;
        }
        
        .dialog-buttons {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: background-color 0.3s;
        }
        
        .btn-primary {
            background-color: #ff9800;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #f57c00;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
        }

        /* Alert Styles */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
            position: relative;
        }
        
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        
        .alert-error {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        
        .alert {
            animation: slideDown 0.3s ease-out;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
<?php include('adminnavbar.php'); ?>

<div class="main">
<?php include('sidebar.php'); ?>

    <div class="container">

    <h2 class="h2-register-header">Issued Books</h2>

    <?php
    // Display email status messages
    if (isset($_SESSION['email_status'])) {
        $status = $_SESSION['email_status'];
        $message = $_SESSION['email_message'];
        $alertClass = ($status === 'success') ? 'alert-success' : 'alert-error';
        echo "<div class='alert $alertClass' id='statusAlert'>$message</div>";
        
        // Clear the session variables
        unset($_SESSION['email_status']);
        unset($_SESSION['email_message']);
    }
    ?>

    <!-- Filter Form -->
    <div class="filter-container">
        <form method="GET" action="">
            <label for="student_id">Student ID:</label>
            <input type="text" name="student_id"  pattern="\d{2,5}" id="student_id" 
                   value="<?php echo htmlspecialchars($filter_student_id); ?>" 
                   placeholder="Search Student ID">

            <label for="book_name">Book Name:</label>
            <input type="text" name="book_name" id="book_name" pattern="[A-Za-z\s.]+"
                   value="<?php echo htmlspecialchars($filter_book_name); ?>" 
                   placeholder="Search Book Name">

            <label for="book_num">Book Number:</label>
            <input type="text" name="book_num" id="book_num" pattern="\d{13}"
                   value="<?php echo htmlspecialchars($filter_book_num); ?>" 
                   placeholder="Search Book Number">

            <label for="returned">Status:</label>
            <select name="returned" id="returned">
                <option value="">All</option>
                <option value="0" <?php echo ($filter_returned === '0') ? 'selected' : ''; ?>>Pending</option>
                <option value="1" <?php echo ($filter_returned === '1') ? 'selected' : ''; ?>>Returned</option>
            </select>

            <div class="button-group">
                <button type="submit">Filter</button>
                <button type="button" class="reset-btn" onclick="window.location.href='<?php echo $_SERVER['PHP_SELF']; ?>'">Reset</button>
            </div>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Book Name</th>
                <th>Student ID</th>
                <th>Student</th>
                <th>Book Num</th>
                <th>Author</th>
                <th>Issue Date</th>
                <th>Due Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query_run = mysqli_query($connection, $query);
            $today = new DateTime();
            
            while($row = mysqli_fetch_assoc($query_run)) {
                $bname = $row['book_name'];
                $bnum = $row['book_num'];
                $author = $row['book_author'];
                $date = $row['issue_date'];
                $duedate = $row['due_date'];
                $student = $row['Name'];
                $student_id = $row['student_id'];
                $student_email = $row['Email'];
                $returned = $row['returned'] ?? 0;
                
                // Calculate days until due date
                $due_date_obj = new DateTime($duedate);
                $days_until_due = $today->diff($due_date_obj)->days;
                $is_overdue = $today > $due_date_obj;
                $is_due_soon = !$is_overdue && $days_until_due <= 7;
                
                // Set row class based on due date
                $row_class = '';
                if (!$returned) {
                    if ($is_overdue) {
                        $row_class = 'overdue';
                    } elseif ($is_due_soon) {
                        $row_class = 'due-soon';
                    }
                }
            ?>
            <tr class="<?php echo $row_class; ?>">
                <td><?php echo htmlspecialchars($bname);?></td>
                <td><?php echo htmlspecialchars($student_id);?></td>
                <td><?php echo htmlspecialchars($student);?></td>
                <td><?php echo htmlspecialchars($bnum);?></td>
                <td><?php echo htmlspecialchars($author);?></td>
                <td><?php echo htmlspecialchars($date);?></td>
                <td><?php echo htmlspecialchars($duedate);?></td>
                <td>
                    <?php if($returned == 1): ?>
                        <span class="returned-status">Returned</span>
                    <?php else: ?>
                        <div class="action-buttons">
                            <form method="post" style="margin: 0; display: inline;">
                                <input type="hidden" name="book_num" value="<?php echo htmlspecialchars($bnum); ?>">
                                <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>">
                                <button type="submit" name="return_book" class="return-btn">Return</button>
                            </form>
                            
                            <?php if ($is_due_soon || $is_overdue): ?>
                                <button class="notify-btn" onclick="openNotificationDialog(
                                    '<?php echo htmlspecialchars($student); ?>', 
                                    '<?php echo htmlspecialchars($student_email); ?>',
                                    '<?php echo htmlspecialchars($bname); ?>',
                                    '<?php echo htmlspecialchars($bnum); ?>',
                                    '<?php echo htmlspecialchars($author); ?>',
                                    '<?php echo htmlspecialchars($duedate); ?>',
                                    '<?php echo htmlspecialchars($date); ?>',
                                    <?php echo $is_overdue ? 'true' : 'false'; ?>,
                                    <?php echo $days_until_due; ?>
                                )">
                                    <?php echo $is_overdue ? 'Overdue!' : 'Notify'; ?>
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    </div>
</div>

<!-- Notification Dialog -->
<div id="notificationDialog" class="email-dialog">
    <div class="dialog-content">
        <div class="dialog-header">
            <h3>Send Return Reminder</h3>
            <button class="close-btn" onclick="closeNotificationDialog()">&times;</button>
        </div>
        
        <form action="../send_mail.php" method="post" id="notificationForm">
            <!-- Hidden field to track where to redirect back to -->
            <input type="hidden" name="redirect_url" value="admin/registerissue.php">
            <input type="hidden" name="notification_type" value="book_return">
            <input type="hidden" id="book_details" name="book_details" value="">
            
            <div class="form-group">
                <label for="receipt_name_notify">Student Name:</label>
                <input type="text" id="receipt_name_notify" name="receipt_name" class="readonly-field" readonly>
            </div>
            
            <div class="form-group">
                <label for="receipt_email_notify">Student Email:</label>
                <input type="email" id="receipt_email_notify" name="receipt_email" class="readonly-field" readonly>
            </div>
            
            <div class="form-group">
                <label for="subject_notify">Subject:</label>
                <input type="text" id="subject_notify" name="subject" class="readonly-field" value="Book Return Reminder" readonly>
            </div>
            
            <div class="form-group">
                <label for="message_notify">Message: <span style="color: red;">*</span></label>
                <textarea id="message_notify" name="message" placeholder="Add additional message (optional)"></textarea>
            </div>
            
            <div class="dialog-buttons">
                <button type="button" class="btn btn-secondary" onclick="closeNotificationDialog()">Cancel</button>
                <button type="submit" class="btn btn-primary">Send Reminder</button>
            </div>
        </form>
    </div>
</div>

<script>
function openNotificationDialog(studentName, studentEmail, bookName, bookNum, author, dueDate, issueDate, isOverdue, daysUntilDue) {
    // Set form values
    document.getElementById('receipt_name_notify').value = studentName;
    document.getElementById('receipt_email_notify').value = studentEmail;
    
    // Set subject based on overdue status
    const subject = isOverdue ? 'Urgent: Overdue Book Return Required' : 'Reminder: Book Due Soon';
    document.getElementById('subject_notify').value = subject;
    
    // Create book details JSON
    const bookDetails = {
        book_name: bookName,
        book_num: bookNum,
        author: author,
        due_date: dueDate,
        issue_date: issueDate,
        is_overdue: isOverdue,
        days_until_due: daysUntilDue
    };
    document.getElementById('book_details').value = JSON.stringify(bookDetails);
    
    // Clear custom message
    document.getElementById('message_notify').value = '';
    
    // Show dialog
    document.getElementById('notificationDialog').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeNotificationDialog() {
    document.getElementById('notificationDialog').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Close dialog when clicking outside of it
window.onclick = function(event) {
    const dialog = document.getElementById('notificationDialog');
    if (event.target == dialog) {
        closeNotificationDialog();
    }
}

// Close dialog with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeNotificationDialog();
    }
});

// Form validation
document.getElementById('notificationForm').addEventListener('submit', function(e) {
    // Show loading state
    const submitBtn = e.target.querySelector('button[type="submit"]');
    submitBtn.textContent = 'Sending...';
    submitBtn.disabled = true;
});

// Auto-hide status alert after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alert = document.getElementById('statusAlert');
    if (alert) {
        setTimeout(function() {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(function() {
                alert.style.display = 'none';
            }, 300);
        }, 5000);
    }
});
</script>

</body>
</html>