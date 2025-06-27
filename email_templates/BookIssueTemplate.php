<?php
// File: email_templates/BookIssueTemplate.php
require_once 'BaseTemplate.php';

class BookIssueTemplate extends BaseTemplate {
    /**
     * Generate book issue confirmation email
     */
    public function generate($studentName, $bookDetailsJson, $customMessage = '') {
        $bookDetails = json_decode($bookDetailsJson, true);
        
        if (!$bookDetails) {
            throw new Exception('Invalid book details provided.');
        }
        
        $bookData = $this->processBookData($bookDetails);
        $styles = $this->getBookIssueStyles();
        
        return $this->buildEmailHTML($studentName, $bookData, $customMessage, $styles);
    }
    
    /**
     * Process and sanitize book data
     */
    private function processBookData($bookDetails) {
        $data = [
            'book_name' => htmlspecialchars($bookDetails['book_name'] ?? ''),
            'book_num' => htmlspecialchars($bookDetails['book_num'] ?? ''),
            'author' => htmlspecialchars($bookDetails['author_name'] ?? $bookDetails['author'] ?? ''),
            'due_date' => htmlspecialchars($bookDetails['due_date'] ?? ''),
            'issue_date' => htmlspecialchars($bookDetails['issue_date'] ?? ''),
            'semester' => htmlspecialchars($bookDetails['semester'] ?? ''),
            'faculty' => htmlspecialchars($bookDetails['faculty'] ?? ''),
            'publication' => htmlspecialchars($bookDetails['publication'] ?? ''),
            'student_id' => htmlspecialchars($bookDetails['student_id'] ?? ''),
        ];
        
        // Format dates
        $data['due_date_formatted'] = date('F j, Y', strtotime($data['due_date']));
        $data['issue_date_formatted'] = date('F j, Y', strtotime($data['issue_date']));
        
        // Calculate loan period
        $issueTime = strtotime($data['issue_date']);
        $dueTime = strtotime($data['due_date']);
        $data['loan_period'] = ceil(($dueTime - $issueTime) / (60 * 60 * 24));
        
        return $data;
    }
    
    /**
     * Get specific styles for book issue template
     */
    private function getBookIssueStyles() {
        $baseStyles = $this->getCommonStyles();
        $headerColor = '#28a745'; // Green for success
        $secondaryColor = '#20c997'; // Teal accent
        
        return $baseStyles . "
            .header { 
                background: linear-gradient(135deg, $headerColor, $secondaryColor);
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
            .success-icon {
                font-size: 48px;
                margin-bottom: 15px;
                display: block;
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
                margin-bottom: 10px;
                align-items: flex-start;
            }
            .detail-label {
                font-weight: 600;
                min-width: 140px;
                color: #495057;
                flex-shrink: 0;
            }
            .detail-value {
                color: #6c757d;
                flex: 1;
            }
            .status-badge {
                display: inline-block;
                padding: 8px 16px;
                border-radius: 25px;
                font-size: 14px;
                font-weight: 600;
                margin: 15px 0;
                background: linear-gradient(135deg, $headerColor, $secondaryColor);
                color: white;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            .important-info {
                background-color: #e7f3ff;
                border: 1px solid #b3d7ff;
                border-radius: 8px;
                padding: 20px;
                margin: 20px 0;
            }
            .important-info h4 {
                margin-top: 0;
                color: #0066cc;
                font-size: 16px;
            }
            .info-list {
                margin: 10px 0;
                padding-left: 20px;
            }
            .info-list li {
                margin-bottom: 8px;
                color: #424242;
            }
            .due-date-highlight {
                background-color: #fff3cd;
                border: 1px solid #ffeaa7;
                border-radius: 8px;
                padding: 15px;
                margin: 20px 0;
                text-align: center;
            }
            .due-date-highlight h4 {
                margin: 0;
                color: #856404;
                font-size: 18px;
            }
            .due-date-highlight .date {
                font-size: 20px;
                font-weight: bold;
                color: #d39e00;
                margin-top: 5px;
            }
            .custom-message {
                background-color: #e8f5e8;
                border: 1px solid #c3e6c3;
                border-radius: 8px;
                padding: 15px;
                margin: 20px 0;
            }
            .custom-message h4 {
                margin-top: 0;
                color: #155724;
            }
            .guidelines {
                background-color: #f8f9fa;
                border-radius: 8px;
                padding: 20px;
                margin: 20px 0;
            }
            .guidelines h4 {
                margin-top: 0;
                color: #495057;
            }
        ";
    }
    
    /**
     * Build the complete HTML email
     */
    private function buildEmailHTML($studentName, $bookData, $customMessage, $styles) {
        $customMessageSection = $this->getCustomMessageSection($customMessage);
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <style>$styles</style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <span class='success-icon'>✅</span>
                    <h1>Book Successfully Issued</h1>
                    <p>Library Management System</p>
                </div>
                
                <div class='content'>
                    <div class='greeting'>
                        Dear " . htmlspecialchars($studentName) . ",
                    </div>
                    
                    <p>Great news! Your book request has been approved and the book has been successfully issued to you. Please find the details below:</p>
                    
                    <div class='status-badge'>
                        Status: Book Issued Successfully
                    </div>
                    
                    <div class='book-details'>
                        <h3>📚 Book Information</h3>
                        <div class='detail-row'>
                            <span class='detail-label'>Title:</span>
                            <span class='detail-value'><strong>{$bookData['book_name']}</strong></span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Author:</span>
                            <span class='detail-value'>{$bookData['author']}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Book Number:</span>
                            <span class='detail-value'>{$bookData['book_num']}</span>
                        </div>" . 
                        (!empty($bookData['publication']) ? "
                        <div class='detail-row'>
                            <span class='detail-label'>Publication:</span>
                            <span class='detail-value'>{$bookData['publication']}</span>
                        </div>" : "") .
                        (!empty($bookData['semester']) ? "
                        <div class='detail-row'>
                            <span class='detail-label'>Semester:</span>
                            <span class='detail-value'>{$bookData['semester']}</span>
                        </div>" : "") .
                        (!empty($bookData['faculty']) ? "
                        <div class='detail-row'>
                            <span class='detail-label'>Faculty:</span>
                            <span class='detail-value'>{$bookData['faculty']}</span>
                        </div>" : "") . "
                    </div>
                    
                    <div class='book-details'>
                        <h3>📅 Issue Details</h3>
                        <div class='detail-row'>
                            <span class='detail-label'>Student ID:</span>
                            <span class='detail-value'>{$bookData['student_id']}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Issue Date:</span>
                            <span class='detail-value'>{$bookData['issue_date_formatted']}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Due Date:</span>
                            <span class='detail-value'><strong>{$bookData['due_date_formatted']}</strong></span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Loan Period:</span>
                            <span class='detail-value'>{$bookData['loan_period']} days</span>
                        </div>
                    </div>
                    
                    <div class='due-date-highlight'>
                        <h4>⚠️ Important: Return Due Date</h4>
                        <div class='date'>{$bookData['due_date_formatted']}</div>
                        <p style='margin: 10px 0 0 0; color: #856404;'>Please return the book by this date to avoid late fees</p>
                    </div>
                    
                    <div class='important-info'>
                        <h4>📋 Important Information</h4>
                        <ul class='info-list'>
                            <li><strong>Book Collection:</strong> Please collect your book from the library circulation desk</li>
                            <li><strong>Library Card:</strong> Bring your library card or student ID when collecting the book</li>
                            <li><strong>Book Condition:</strong> Please check the book condition when collecting and report any damage</li>
                            <li><strong>Late Fees:</strong> Late fees will apply for books returned after the due date</li>
                        </ul>
                    </div>
                    
                    <div class='guidelines'>
                        <h4>📖 Library Guidelines</h4>
                        <ul class='info-list'>
                            <li>Take good care of the book and keep it in good condition</li>
                            <li>Do not lend the book to others - you are responsible for it</li>
                            <li>Return the book on or before the due date</li>
                            <li>Contact the library if you need to renew or have any issues</li>
                            <li>Lost or damaged books will incur replacement fees</li>
                        </ul>
                    </div>
                    
                    $customMessageSection
                   
                    <p style='margin-top: 30px;'>Thank you for using our library services. We hope you enjoy reading your book!</p>
                    
                    <p style='margin-top: 20px;'>
                        Best regards,<br>
                        <strong>Library Management Team</strong>
                    </p>
                </div>
                
                {$this->getFooter()}
            </div>
        </body>
        </html>";
    }
    
    /**
     * Get custom message section if message exists
     */
    private function getCustomMessageSection($customMessage) {
        if (!empty($customMessage)) {
            return "
            <div class='custom-message'>
                <h4>📝 Additional Message from Library Staff:</h4>
                <p>" . nl2br(htmlspecialchars($customMessage)) . "</p>
            </div>";
        }
        return '';
    }
}
?>