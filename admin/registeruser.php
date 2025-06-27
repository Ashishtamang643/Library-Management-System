<?php
     require('functions.php');
     session_start();
     if (!isset($_SESSION['Name'])) {
        echo "<script>alert('Please login to continue.'); window.location.href='index.php';</script>";
        exit();
    }
     $connection = mysqli_connect("localhost","root","");
     $db = mysqli_select_db($connection,"library");
     $id="";
     $name="";
     $email="";
     $username="";
     $address="";
     $cell="";
     $query = "select * from users";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Users</title>
    <link rel="stylesheet" href="../style2.css">
    <link rel="stylesheet" href="adminstyle.css">
    <style>
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
            border-color: #4CAF50;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
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
            background-color: #4CAF50;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #45a049;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        
        .btn-small {
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 3px;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn-delete {
            background-color: #dc3545;
            color: white;
        }
        
        .btn-delete:hover {
            background-color: #c82333;
        }
        
        .btn-email {
            background-color: #007bff;
            color: white;
        }
        
        .btn-email:hover {
            background-color: #0056b3;
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
        <h2 class="h2-register-header">Registered Users</h2>
        
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
        <table>
            <thead>
              <tr>
                 <th>ID</th>
                 <th>Name</th>
                 <th>Email</th>
                 <th>Faculty</th>
                 <th>Address</th>
                 <th>Cell</th>
                 <th>Actions</th>
              </tr>
            </thead>
            <tbody>
            <?php
                 $query_run = mysqli_query($connection,$query);
                 while($row = mysqli_fetch_assoc($query_run))
                 {
                    $id = $row['ID'];
                    $name = $row['Name'];
                    $email = $row['Email'];
                    $faculty = $row['faculty'];
                    $address = $row['Address'];
                    $cell = $row['Cell'];
                    ?>
            <tr>
                <td><?php echo htmlspecialchars($id);?></td>
                <td><?php echo htmlspecialchars($name);?></td>
                <td><?php echo htmlspecialchars($email);?></td>
                <td><?php echo htmlspecialchars($faculty);?></td>
                <td><?php echo htmlspecialchars($address);?></td>
                <td><?php echo htmlspecialchars($cell);?></td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-small btn-email" onclick="openEmailDialog('<?php echo htmlspecialchars($name); ?>', '<?php echo htmlspecialchars($email); ?>')">
                            Send Message
                        </button>
                        <a href="delete_user.php?id=<?php echo $id; ?>" 
                           class="btn-small btn-delete"
                           onclick="return confirm('Are you sure you want to delete this user?');">
                           Delete
                        </a>
                    </div>
                </td>
            </tr>
            <?php
                 }
            ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Email Dialog -->
<div id="emailDialog" class="email-dialog">
    <div class="dialog-content">
        <div class="dialog-header">
            <h3>Send Message</h3>
            <button class="close-btn" onclick="closeEmailDialog()">&times;</button>
        </div>
        
        <form action="../send_mail.php" method="post" id="emailForm">
            <!-- Hidden field to track where to redirect back to -->
            <input type="hidden" name="redirect_url" value="admin/registeruser.php">
            
            <div class="form-group">
                <label for="receipt_name">Recipient Name:</label>
                <input type="text" id="receipt_name" name="receipt_name" class="readonly-field" readonly>
            </div>
            
            <div class="form-group">
                <label for="receipt_email">Recipient Email:</label>
                <input type="email" id="receipt_email" name="receipt_email" class="readonly-field" readonly>
            </div>
            
            <div class="form-group">
                <label for="subject">Subject: <span style="color: red;">*</span></label>
                <input type="text" id="subject" name="subject" placeholder="Enter email subject" required>
            </div>
            
            <div class="form-group">
                <label for="message">Message: <span style="color: red;">*</span></label>
                <textarea id="message" name="message" placeholder="Enter your message here..." required></textarea>
            </div>
            
            <div class="dialog-buttons">
                <button type="button" class="btn btn-secondary" onclick="closeEmailDialog()">Cancel</button>
                <button type="submit" class="btn btn-primary">Send Message</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEmailDialog(name, email) {
    document.getElementById('receipt_name').value = name;
    document.getElementById('receipt_email').value = email;
    document.getElementById('subject').value = '';
    document.getElementById('message').value = '';
    document.getElementById('emailDialog').style.display = 'block';
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
}

function closeEmailDialog() {
    document.getElementById('emailDialog').style.display = 'none';
    document.body.style.overflow = 'auto'; // Restore scrolling
}

// Close dialog when clicking outside of it
window.onclick = function(event) {
    const dialog = document.getElementById('emailDialog');
    if (event.target == dialog) {
        closeEmailDialog();
    }
}

// Close dialog with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeEmailDialog();
    }
});

// Form validation
document.getElementById('emailForm').addEventListener('submit', function(e) {
    const subject = document.getElementById('subject').value.trim();
    const message = document.getElementById('message').value.trim();
    
    if (!subject || !message) {
        e.preventDefault();
        alert('Please fill in both subject and message fields.');
        return false;
    }
    
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