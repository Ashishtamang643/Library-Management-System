<?php
// File: email_templates/CustomMessageTemplate.php
require_once 'BaseTemplate.php';

class CustomMessageTemplate extends BaseTemplate {
    /**
     * Generate custom message email
     */
    public function generate($recipientName, $message, $customMessage = '') {
        $styles = $this->getCustomMessageStyles();
        return $this->buildEmailHTML($recipientName, $message, $styles);
    }
    
    /**
     * Get specific styles for custom message template
     */
    private function getCustomMessageStyles() {
        $baseStyles = $this->getCommonStyles();
        
        return $baseStyles . "
            .container { 
                max-width: 600px;
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
            .message-content {
                background-color: #f8f9fa;
                border-left: 4px solid #4CAF50;
                padding: 20px;
                margin: 20px 0;
                border-radius: 0 8px 8px 0;
                font-size: 16px;
                line-height: 1.8;
            }
        ";
    }
    
    /**
     * Build the complete HTML email for custom message
     */
    private function buildEmailHTML($recipientName, $message, $styles) {
        $greetingSection = !empty($recipientName) 
            ? "<div class='greeting'>Dear " . htmlspecialchars($recipientName) . ",</div>" 
            : "";
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <style>$styles</style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Library Management System</h2>
                </div>
                
                <div class='content'>
                    $greetingSection
                    
                    <div class='message-content'>
                        " . nl2br(htmlspecialchars($message)) . "
                    </div>
                    
                    <p style='margin-top: 30px;'>
                        Best regards,<br>
                        <strong>Library Management Team</strong>
                    </p>
                </div>
                
                {$this->getFooter()}
            </div>
        </body>
        </html>";
    }
}
?>