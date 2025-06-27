<?php
// File: email_templates/BookReturnTemplate.php
require_once 'BaseTemplate.php';

class BookReturnTemplate extends BaseTemplate {
    /**
     * Generate book return reminder email
     */
    public function generate($studentName, $bookDetailsJson, $customMessage = '') {
        $bookDetails = json_decode($bookDetailsJson, true);
        
        if (!$bookDetails) {
            throw new Exception('Invalid book details provided.');
        }
        
        $bookData = $this->processBookData($bookDetails);
        $styles = $this->getBookReturnStyles($bookData);
        
        return $this->buildEmailHTML($studentName, $bookData, $customMessage, $styles);
    }
    
    /**
     * Process and sanitize book data
     */
    private function processBookData($bookDetails) {
        $data = [
            'book_name' => htmlspecialchars($bookDetails['book_name'] ?? ''),
            'book_num' => htmlspecialchars($bookDetails['book_num'] ?? ''),
            'author' => htmlspecialchars($bookDetails['author'] ?? ''),
            'due_date' => htmlspecialchars($bookDetails['due_date'] ?? ''),
            'issue_date' => htmlspecialchars($bookDetails['issue_date'] ?? ''),
            'is_overdue' => $bookDetails['is_overdue'] ?? false,
            'days_until_due' => $bookDetails['days_until_due'] ?? 0,
        ];
        
        // Format dates
        $data['due_date_formatted'] = date('F j, Y', strtotime($data['due_date']));
        $data['issue_date_formatted'] = date('F j, Y', strtotime($data['issue_date']));
        
        // Determine urgency
        $data['urgency_message'] = $data['is_overdue'] 
            ? "<strong style='color: #fff;'>OVERDUE by " . abs($data['days_until_due']) . " day(s)</strong>"
            : "<strong style='color: #fff;'>Due in " . $data['days_until_due'] . " day(s)</strong>";
        
        $data['header_color'] = $data['is_overdue'] ? '#dc3545' : '#fd7e14';
        $data['header_text'] = $data['is_overdue'] ? 'URGENT: Book Return Required' : 'Reminder: Book Due Soon';
        
        return $data;
    }
    
    /**
     * Get specific styles for book return template
     */
    private function getBookReturnStyles($bookData) {
        $baseStyles = $this->getCommonStyles();
        $headerColor = $bookData['header_color'];
        $secondaryColor = $bookData['is_overdue'] ? '#c82333' : '#e8690b';
        
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
                background-color: $headerColor;
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
        ";
    }
    
    /**
     * Build the complete HTML email
     */
    private function buildEmailHTML($studentName, $bookData, $customMessage, $styles) {
        $actionSection = $this->getActionSection($bookData);
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
                    <h1>Book Return {$bookData['header_text']}</h1>
                    <p>Library Management System</p>
                </div>
                
                <div class='content'>
                    <div class='greeting'>
                        Dear " . htmlspecialchars($studentName) . ",
                    </div>
                    
                    <p>This is a " . ($bookData['is_overdue'] ? 'urgent reminder' : 'friendly reminder') . " regarding a book that you have borrowed from our library.</p>
                    
                    <div class='book-details'>
                        <h3>Book Information</h3>
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
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Issue Date:</span>
                            <span class='detail-value'>{$bookData['issue_date_formatted']}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Due Date:</span>
                            <span class='detail-value'><strong>{$bookData['due_date_formatted']}</strong></span>
                        </div>
                    </div>
                    
                    <div class='status-badge'>
                        Status: {$bookData['urgency_message']}
                    </div>
                    
                    $actionSection
                    $customMessageSection
                   
                    <p style='margin-top: 30px;'>Thank you for using our library services. We appreciate your prompt attention to this matter.</p>
                    
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
     * Get action section based on overdue status
     */
    private function getActionSection($bookData) {
        if ($bookData['is_overdue']) {
            return "
            <div class='action-section' style='background-color: #f8d7da; border-color: #f5c6cb;'>
                <div class='action-title' style='color: #721c24;'>IMMEDIATE ACTION REQUIRED</div>
                <p style='color: #721c24; margin-bottom: 15px;'>Your book is overdue. Please return it immediately to avoid additional late fees.</p>
                <ul class='action-list'>
                    <li>Return the book to the library circulation desk</li>
                    <li>Late fees may apply for overdue books</li>
                    <li>Contact the library if you need assistance</li>
                    <li>Failure to return may result in account suspension</li>
                </ul>
            </div>";
        } else {
            return "
            <div class='action-section'>
                <div class='action-title'>ACTION REQUIRED</div>
                <p>Your book is due soon. Please take one of the following actions:</p>
                <ul class='action-list'>
                    <li>Return the book before the due date</li>
                    <li>Visit the library circulation desk for assistance</li>
                </ul>
            </div>";
        }
    }
    
    /**
     * Get custom message section if message exists
     */
    private function getCustomMessageSection($customMessage) {
        if (!empty($customMessage)) {
            return "
            <div class='custom-message'>
                <h4>Additional Message from Library Staff:</h4>
                <p>" . nl2br(htmlspecialchars($customMessage)) . "</p>
            </div>";
        }
        return '';
    }
}
?>