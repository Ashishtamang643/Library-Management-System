<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

session_start();

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'temp@gmail.com';
    $mail->Password = 'nothing';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    
    // Get form data with validation
    $receipt_email = $_POST['receipt_email'] ?? '';
    $receipt_name = $_POST['receipt_name'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    $notification_type = $_POST['notification_type'] ?? 'custom';
    $book_details = $_POST['book_details'] ?? '';
    
    // Get the redirect URL (where to go back)
    $redirect_url = $_POST['redirect_url'] ?? '';
    
    // If no redirect URL provided, try to get from HTTP_REFERER
    if (empty($redirect_url)) {
        $redirect_url = $_SERVER['HTTP_REFERER'] ?? 'admin/registeruser.php';
        
        // Make sure it's a relative path for security
        $parsed_url = parse_url($redirect_url);
        if (isset($parsed_url['path'])) {
            // Extract just the path part (remove domain if present)
            $redirect_url = ltrim($parsed_url['path'], '/');
            
            // If it contains the full path from document root, extract relevant part
            if (strpos($redirect_url, 'lms/') !== false) {
                $redirect_url = substr($redirect_url, strpos($redirect_url, 'lms/') + 4);
            }
        }
    }
    
    // Basic validation
    if (empty($receipt_email) || empty($subject)) {
        throw new Exception('Required fields are missing.');
    }
    
    if (!filter_var($receipt_email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email address.');
    }
    
    // Recipients
    $mail->setFrom('digitaloasis100@gmail.com', 'Library Management System');
    $mail->addAddress($receipt_email, $receipt_name);
    
    // Content
    $mail->isHTML(true);
    $mail->Subject = $subject;
    
    // Generate email body based on notification type
    if ($notification_type === 'book_return' && !empty($book_details)) {
        $emailBody = generateBookReturnEmail($receipt_name, $book_details, $message);
    } else {
        $emailBody = generateCustomEmail($receipt_name, $message);
    }
    
    $mail->Body = $emailBody;
    $mail->AltBody = strip_tags($emailBody); // Plain text version
    
    $mail->send();
    
    // Success message
    $_SESSION['email_status'] = 'success';
    $_SESSION['email_message'] = "Message successfully sent to " . htmlspecialchars($receipt_name) . " (" . htmlspecialchars($receipt_email) . ")";
    
} catch (Exception $e) {
    // Error message
    $_SESSION['email_status'] = 'error';
    $_SESSION['email_message'] = "Message could not be sent. Error: " . $e->getMessage();
}

// Dynamic redirect back to the source page
header("Location: " . $redirect_url);
exit();

/**
 * Generate email template for book return reminders
 */
function generateBookReturnEmail($studentName, $bookDetailsJson, $customMessage = '') {
    $bookDetails = json_decode($bookDetailsJson, true);
    
    if (!$bookDetails) {
        throw new Exception('Invalid book details provided.');
    }
    
    $bookName = htmlspecialchars($bookDetails['book_name'] ?? '');
    $bookNum = htmlspecialchars($bookDetails['book_num'] ?? '');
    $author = htmlspecialchars($bookDetails['author'] ?? '');
    $dueDate = htmlspecialchars($bookDetails['due_date'] ?? '');
    $issueDate = htmlspecialchars($bookDetails['issue_date'] ?? '');
    $isOverdue = $bookDetails['is_overdue'] ?? false;
    $daysUntilDue = $bookDetails['days_until_due'] ?? 0;
    
    // Format dates for better readability
    $dueDateFormatted = date('F j, Y', strtotime($dueDate));
    $issueDateFormatted = date('F j, Y', strtotime($issueDate));
    
    // Determine urgency and styling
    $urgencyMessage = $isOverdue 
        ? "<strong style='color: #fff;'>OVERDUE by " . abs($daysUntilDue) . " day(s)</strong>"
        : "<strong style='color: #fff;'>Due in " . $daysUntilDue . " day(s)</strong>";
    
    $headerColor = $isOverdue ? '#dc3545' : '#fd7e14';
    $headerText = $isOverdue ? 'URGENT: Book Return Required' : 'Reminder: Book Due Soon';
    
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { 
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                line-height: 1.6; 
                color: #333; 
                margin: 0; 
                padding: 0;
                background-color: #f8f9fa;
            }
            .container { 
                max-width: 650px; 
                margin: 20px auto; 
                background-color: #ffffff;
                border-radius: 10px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                overflow: hidden;
            }
            .header { 
                background: linear-gradient(135deg, $headerColor, " . ($isOverdue ? '#c82333' : '#e8690b') . ");
                color: white;
                padding: 30px 20px; 
                text-align: center;
            }
            .header h1 {
                margin: 0;
                font-size: 24px;
                font-weight: 600;
            }
            .header p {
                margin: 10px 0 0 0;
                font-size: 16px;
                opacity: 0.9;
            }
            .content { 
                padding: 30px 20px; 
            }
            .greeting {
                font-size: 18px;
                margin-bottom: 20px;
                color: #2c3e50;
            }
            .book-details {
                background-color: #f8f9fa;
                border-left: 4px solid $headerColor;
                padding: 20px;
                margin: 20px 0;
                border-radius: 0 8px 8px 0;
            }
            .book-details h3 {
                margin-top: 0;
                color: #2c3e50;
                font-size: 18px;
            }
            .detail-row {
                display: flex;
                margin-bottom: 8px;
                align-items: center;
            }
            .detail-label {
                font-weight: 600;
                min-width: 120px;
                color: #495057;
            }
            .detail-value {
                color: #6c757d;
            }
            .status-badge {
                display: inline-block;
                padding: 6px 12px;
                border-radius: 20px;
                font-size: 14px;
                font-weight: 600;
                margin: 15px 0;
                background-color: " . ($isOverdue ? '#dc3545' : '#fd7e14') . ";
                color: white;
            }
            .action-section {
                background-color: #e3f2fd;
                border-radius: 8px;
                padding: 20px;
                margin: 20px 0;
                border: 1px solid #90caf9;
            }
            .action-title {
                font-size: 16px;
                font-weight: 600;
                color: #1565c0;
                margin-bottom: 10px;
            }
            .action-list {
                margin: 10px 0;
                padding-left: 20px;
            }
            .action-list li {
                margin-bottom: 8px;
                color: #424242;
            }
            .custom-message {
                background-color: #fff3cd;
                border: 1px solid #ffeaa7;
                border-radius: 8px;
                padding: 15px;
                margin: 20px 0;
            }
            .custom-message h4 {
                margin-top: 0;
                color: #856404;
            }
            .footer { 
                background-color: #f8f9fa; 
                padding: 20px; 
                text-align: center; 
                font-size: 14px; 
                color: #6c757d;
                border-top: 1px solid #dee2e6;
            }
            .footer p {
                margin: 5px 0;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Book Return $headerText</h1>
                <p>Library Management System</p>
            </div>
            
            <div class='content'>
                <div class='greeting'>
                    Dear " . htmlspecialchars($studentName) . ",
                </div>
                
                <p>This is a " . ($isOverdue ? 'urgent reminder' : 'friendly reminder') . " regarding a book that you have borrowed from our library.</p>
                
                <div class='book-details'>
                    <h3>Book Information</h3>
                    <div class='detail-row'>
                        <span class='detail-label'>Title:</span>
                        <span class='detail-value'><strong>$bookName</strong></span>
                    </div>
                    <div class='detail-row'>
                        <span class='detail-label'>Author:</span>
                        <span class='detail-value'>$author</span>
                    </div>
                    <div class='detail-row'>
                        <span class='detail-label'>Book Number:</span>
                        <span class='detail-value'>$bookNum</span>
                    </div>
                    <div class='detail-row'>
                        <span class='detail-label'>Issue Date:</span>
                        <span class='detail-value'>$issueDateFormatted</span>
                    </div>
                    <div class='detail-row'>
                        <span class='detail-label'>Due Date:</span>
                        <span class='detail-value'><strong>$dueDateFormatted</strong></span>
                    </div>
                </div>
                
                <div class='status-badge'>
                    Status: $urgencyMessage
                </div>
                
                " . ($isOverdue ? "
                <div class='action-section' style='background-color: #f8d7da; border-color: #f5c6cb;'>
                    <div class='action-title' style='color: #721c24;'>IMMEDIATE ACTION REQUIRED</div>
                    <p style='color: #721c24; margin-bottom: 15px;'>Your book is overdue. Please return it immediately to avoid additional late fees.</p>
                    <ul class='action-list'>
                        <li>Return the book to the library circulation desk</li>
                        <li>Late fees may apply for overdue books</li>
                        <li>Contact the library if you need assistance</li>
                        <li>Failure to return may result in account suspension</li>
                    </ul>
                </div>
                " : "
                <div class='action-section'>
                    <div class='action-title'>ACTION REQUIRED</div>
                    <p>Your book is due soon. Please take one of the following actions:</p>
                    <ul class='action-list'>
                        <li>Return the book before the due date</li>
                        <li>Visit the library circulation desk for assistance</li>
                    </ul>
                </div>
                ") . "
                
                " . (!empty($customMessage) ? "
                <div class='custom-message'>
                    <h4>Additional Message from Library Staff:</h4>
                    <p>" . nl2br(htmlspecialchars($customMessage)) . "</p>
                </div>
                " : "") . "
               
                <p style='margin-top: 30px;'>Thank you for using our library services. We appreciate your prompt attention to this matter.</p>
                
                <p style='margin-top: 20px;'>
                    Best regards,<br>
                    <strong>Library Management Team</strong>
                </p>
            </div>
            
            <div class='footer'>
                <p><strong>Library Management System</strong></p>
                <p>This is an automated email. Please do not reply to this address.</p>
                <p>For assistance, please contact the library directly.</p>
            </div>
        </div>
    </body>
    </html>";
}

/**
 * Generate email template for custom messages
 */
function generateCustomEmail($recipientName, $message) {
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { 
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                line-height: 1.6; 
                color: #333; 
                margin: 0; 
                padding: 0;
                background-color: #f8f9fa;
            }
            .container { 
                max-width: 600px; 
                margin: 20px auto; 
                background-color: #ffffff;
                border-radius: 10px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                overflow: hidden;
            }
            .header { 
                background: linear-gradient(135deg, #4CAF50, #45a049);
                color: white;
                padding: 30px 20px; 
                text-align: center;
            }
            .header h2 {
                margin: 0;
                font-size: 24px;
                font-weight: 600;
            }
            .content { 
                padding: 30px 20px; 
            }
            .greeting {
                font-size: 18px;
                margin-bottom: 20px;
                color: #2c3e50;
            }
            .message-content {
                background-color: #f8f9fa;
                border-left: 4px solid #4CAF50;
                padding: 20px;
                margin: 20px 0;
                border-radius: 0 8px 8px 0;
                font-size: 16px;
                line-height: 1.8;
            }
            .footer { 
                background-color: #f8f9fa; 
                padding: 20px; 
                text-align: center; 
                font-size: 14px; 
                color: #6c757d;
                border-top: 1px solid #dee2e6;
            }
            .footer p {
                margin: 5px 0;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Library Management System</h2>
            </div>
            
            <div class='content'>
                " . (!empty($recipientName) ? "
                <div class='greeting'>
                    Dear " . htmlspecialchars($recipientName) . ",
                </div>
                " : "") . "
                
                <div class='message-content'>
                    " . nl2br(htmlspecialchars($message)) . "
                </div>
                
                <p style='margin-top: 30px;'>
                    Best regards,<br>
                    <strong>Library Management Team</strong>
                </p>
            </div>
            
            <div class='footer'>
                <p><strong>Library Management System</strong></p>
                <p>This email was sent from the Library Management System.</p>
                <p>Please do not reply to this email address.</p>
            </div>
        </div>
    </body>
    </html>";
}
?>
