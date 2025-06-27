<?php
// File: email_templates/EmailTemplateManager.php
require_once 'BookReturnTemplate.php';
require_once 'BookIssueTemplate.php';
require_once 'CustomMessageTemplate.php';

class EmailTemplateManager {
    private $bookReturnTemplate;
    private $bookIssueTemplate;
    private $customMessageTemplate;
    
    public function __construct() {
        $this->bookReturnTemplate = new BookReturnTemplate();
        $this->bookIssueTemplate = new BookIssueTemplate();
        $this->customMessageTemplate = new CustomMessageTemplate();
    }
    
    /**
     * Generate email content based on notification type
     */
    public function generateEmail($formData) {
        $notificationType = $formData['notification_type'];
        
        switch ($notificationType) {
            case 'book_return':
                if (!empty($formData['book_details'])) {
                    return $this->bookReturnTemplate->generate(
                        $formData['receipt_name'],
                        $formData['book_details'],
                        $formData['message']
                    );
                }
                break;
                
            case 'book_issue':
                if (!empty($formData['book_details'])) {
                    return $this->bookIssueTemplate->generate(
                        $formData['receipt_name'],
                        $formData['book_details'],
                        $formData['message']
                    );
                }
                break;
                
            default:
                return $this->customMessageTemplate->generate(
                    $formData['receipt_name'],
                    $formData['message']
                );
        }
        
        // Fallback to custom message template
        return $this->customMessageTemplate->generate(
            $formData['receipt_name'],
            $formData['message']
        );
    }
}
?>