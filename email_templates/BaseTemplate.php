<?php
// File: email_templates/BaseTemplate.php
abstract class BaseTemplate {
    /**
     * Generate email content
     */
    abstract public function generate($recipientName, $data, $customMessage = '');
    
    /**
     * Get common email styles
     */
    protected function getCommonStyles() {
        return "
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
            .content { 
                padding: 30px 20px; 
            }
            .greeting {
                font-size: 18px;
                margin-bottom: 20px;
                color: #2c3e50;
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
        ";
    }
    
    /**
     * Get footer HTML
     */
    protected function getFooter() {
        return "
            <div class='footer'>
                <p><strong>Library Management System</strong></p>
                <p>This is an automated email. Please do not reply to this address.</p>
                <p>For assistance, please contact the library directly.</p>
            </div>
        ";
    }
}
?>