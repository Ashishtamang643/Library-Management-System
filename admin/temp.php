<?php
/**
 * Integration Guide for adding Book Recommendation System 
 * to your existing Library Management System
 */

// Step 1: Include the recommendation system class
require_once 'BookRecommendationSystem.php';

// Step 2: Initialize the system with your existing database connection
// Assuming you already have a database connection in your system
function initializeRecommendationSystem($existing_pdo_connection) {
    return new BookRecommendationSystem($existing_pdo_connection);
}

// Step 3: Add recommendation methods to your existing user dashboard
class LibraryDashboard {
    private $pdo;
    private $recommendationSystem;
    
    public function __construct($database_connection) {
        $this->pdo = $database_connection;
        $this->recommendationSystem = new BookRecommendationSystem($database_connection);
    }
    
    /**
     * Display user dashboard with recommendations
     */
    public function displayUserDashboard($user_id) {
        echo "<div class='user-dashboard'>";
        
        // Your existing dashboard content
        $this->displayUserInfo($user_id);
        $this->displayCurrentBooks($user_id);
        
        // Add recommendations section
        $this->displayRecommendations($user_id);
        
        echo "</div>";
    }
    
    /**
     * Display recommendations section
     */
    private function displayRecommendations($user_id) {
        $recommendations = $this->recommendationSystem->getRecommendations($user_id, 6);
        
        echo "<div class='recommendations-section' style='margin-top: 30px;'>";
        echo "<h3 style='color: #333; border-bottom: 2px solid #667eea; padding-bottom: 10px;'>📚 Recommended for You</h3>";
        
        if (empty($recommendations)) {
            echo "<p style='color: #666; font-style: italic;'>Start borrowing books to get personalized recommendations!</p>";
        } else {
            echo "<div class='recommendations-grid' style='display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-top: 20px;'>";
            
            foreach ($recommendations as $book) {
                $this->displayRecommendationCard($book, $user_id);
            }
            
            echo "</div>";
        }
        
        echo "</div>";
    }
    
    /**
     * Display individual recommendation card
     */
    private function displayRecommendationCard($book, $user_id) {
        $book_name = htmlspecialchars($book['book_name']);
        $author_name = htmlspecialchars($book['author_name'] ?? 'Unknown Author');
        $publication = htmlspecialchars($book['publication'] ?? 'Unknown Publisher');
        $similarity_score = $book['similarity_score'];
        $reason = htmlspecialchars($book['recommendation_reason']);
        $matched_with = htmlspecialchars($book['matched_with']);
        $available_qty = $book['available_quantity'];
        $book_id = $book['book_id'];
        
        echo "<div class='recommendation-card' style='
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-left: 4px solid #667eea;
            transition: transform 0.3s ease;
        ' onmouseover='this.style.transform=\"translateY(-5px)\"' onmouseout='this.style.transform=\"none\"'>";
        
        echo "<h4 style='margin: 0 0 10px 0; color: #333; font-size: 16px;'>{$book_name}</h4>";
        echo "<p style='margin: 5px 0; color: #666; font-size: 14px;'>📝 {$author_name}</p>";
        echo "<p style='margin: 5px 0; color: #888; font-size: 12px;'>🏢 {$publication}</p>";
        
        echo "<div style='background: #e3f2fd; padding: 10px; border-radius: 5px; margin: 15px 0;'>";
        echo "<div style='font-weight: bold; color: #1976d2; font-size: 14px;'>{$similarity_score}% Match - {$reason}</div>";
        echo "<div style='font-size: 11px; color: #666; margin-top: 5px;'>💡 Because you read: \"{$matched_with}\"</div>";
        echo "</div>";
        
        echo "<div style='margin-bottom: 15px;'>";
        echo "<span style='background: #4caf50; color: white; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: bold;'>{$available_qty} Available</span>";
        echo "</div>";
        
        echo "<button onclick='issueRecommendedBook({$book_id}, {$user_id})' style='
            background: #667eea;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 13px;
            width: 100%;
            transition: background 0.3s ease;
        ' onmouseover='this.style.background=\"#5a6fd8\"' onmouseout='this.style.background=\"#667eea\"'>
            📖 Issue This Book
        </button>";
        
        echo "</div>";
    }
    
    // Your existing methods
    private function displayUserInfo($user_id) {
        // Your existing user info display code
    }
    
    private function displayCurrentBooks($user_id) {
        // Your existing current books display code
    }
}

// Step 4: AJAX handler for recommendations (add to your existing AJAX handlers)
if (isset($_POST['action']) && $_POST['action'] === 'get_recommendations') {
    header('Content-Type: application/json');
    
    $user_id = $_POST['user_id'] ?? null;
    $limit = $_POST['limit'] ?? 6;
    
    if (!$user_id) {
        echo json_encode(['error' => 'User ID is required']);
        exit;
    }
    
    try {
        // Use your existing database connection
        $recommendationSystem = new BookRecommendationSystem($your_existing_pdo_connection);
        $recommendations = $recommendationSystem->getRecommendations($user_id, $limit);
        
        echo json_encode([
            'success' => true,
            'recommendations' => $recommendations
        ]);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Failed to get recommendations: ' . $e->getMessage()]);
    }
}

// Step 5: JavaScript functions to add to your existing JS file
?>
<script>
// Add these functions to your existing JavaScript file

// Function to load recommendations dynamically
async function loadUserRecommendations(userId, containerId = 'recommendations-container') {
    try {
        const formData = new FormData();
        formData.append('action', 'get_recommendations');
        formData.append('user_id', userId);
        formData.append('limit', 6);
        
        const response = await fetch('your_ajax_handler.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            displayRecommendationsInContainer(data.recommendations, containerId, userId);
        } else {
            console.error('Error loading recommendations:', data.error);
        }
    } catch (error) {
        console.error('Failed to load recommendations:', error);
    }
}

// Function to display recommendations in a container
function displayRecommendationsInContainer(recommendations, containerId, userId) {
    const container = document.getElementById(containerId);
    
    if (!container) {
        console.error('Recommendations container not found:', containerId);
        return;
    }
    
    if (!recommendations || recommendations.length === 0) {
        container.innerHTML = '<p style="text-align: center; color: #666; font-style: italic;">No recommendations available. Start borrowing books to get personalized suggestions!</p>';
        return;
    }
    
    let html = '<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">';
    
    recommendations.forEach(book => {
        html += createRecommendationCardHTML(book, userId);
    });
    
    html += '</div>';
    container.innerHTML = html;
}

// Function to create recommendation card HTML
function createRecommendationCardHTML(book, userId) {
    return `
        <div class="recommendation-card" style="
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-left: 4px solid #667eea;
            transition: transform 0.3s ease;
        " onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='none'">
            
            <h4 style="margin: 0 0 10px 0; color: #333; font-size: 16px;">${escapeHtml(book.book_name)}</h4>
            <p style="margin: 5px 0; color: #666; font-size: 14px;">📝 ${escapeHtml(book.author_name || 'Unknown Author')}</p>
            <p style="margin: 5px 0; color: #888; font-size: 12px;">🏢 ${escapeHtml(book.publication || 'Unknown Publisher')}</p>
            
            <div style="background: #e3f2fd; padding: 10px; border-radius: 5px; margin: 15px 0;">
                <div style="font-weight: bold; color: #1976d2; font-size: 14px;">${book.similarity_score}% Match - ${book.recommendation_reason}</div>
                <div style="font-size: 11px; color: #666; margin-top: 5px;">💡 Because you read: "${escapeHtml(book.matched_with)}"</div>
            </div>
            
            <div style="margin-bottom: 15px;">
                <span style="background: #4caf50; color: white; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: bold;">${book.available_quantity} Available</span>
            </div>
            
            <button onclick="issueRecommendedBook(${book.book_id}, ${userId})" style="
                background: #667eea;
                color: white;
                border: none;
                padding: 10px 15px;
                border-radius: 5px;
                cursor: pointer;
                font-size: 13px;
                width: 100%;
                transition: background 0.3s ease;
            " onmouseover="this.style.background='#5a6fd8'" onmouseout="this.style.background='#667eea'">
                📖 Issue This Book
            </button>
        </div>
    `;
}

// Function to issue a recommended book (integrate with your existing issue system)
function issueRecommendedBook(bookId, userId) {
    if (confirm('Do you want to issue this recommended book?')) {
        // Call your existing book issue function
        // Replace 'issueBook' with your actual function name
        if (typeof issueBook === 'function') {
            issueBook(bookId, userId);
        } else {
            // Fallback: redirect to issue page or show form
            window.location.href = `issue_book.php?book_id=${bookId}&user_id=${userId}&from=recommendations`;
        }
    }
}

// Utility function for HTML escaping
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text || '';
    return div.innerHTML;
}

// Auto-load recommendations when user dashboard loads
document.addEventListener('DOMContentLoaded', function() {
    // Get user ID from your existing system (adjust as needed)
    const userId = getCurrentUserId(); // Replace with your method to get current user ID
    
    if (userId && document.getElementById('recommendations-container')) {
        loadUserRecommendations(userId);
    }
});

// Function to get current user ID (implement based on your system)
function getCurrentUserId() {
    // Method 1: From URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const userIdFromUrl = urlParams.get('user_id');
    if (userIdFromUrl) return userIdFromUrl;
    
    // Method 2: From hidden input field
    const userIdInput = document.getElementById('current_user_id');
    if (userIdInput) return userIdInput.value;
    
    // Method 3: From data attribute
    const userElement = document.querySelector('[data-user-id]');
    if (userElement) return userElement.getAttribute('data-user-id');
    
    // Method 4: From session/cookie (if stored in DOM)
    const sessionUserElement = document.getElementById('session_user_id');
    if (sessionUserElement) return sessionUserElement.textContent;
    
    return null;
}
</script>

<?php
// Step 6: HTML template to add to your user dashboard page
function renderRecommendationsSection() {
    echo '
    <!-- Add this section to your user dashboard HTML -->
    <div class="recommendations-wrapper" style="margin-top: 30px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="color: #333; border-bottom: 2px solid #667eea; padding-bottom: 10px; margin: 0;">
                📚 Recommended Books for You
            </h3>
            <button onclick="loadUserRecommendations(getCurrentUserId())" style="
                background: #667eea; 
                color: white; 
                border: none; 
                padding: 8px 15px; 
                border-radius: 5px; 
                cursor: pointer;
                font-size: 12px;
            ">
                🔄 Refresh Recommendations
            </button>
        </div>
        
        <div id="recommendations-container" style="min-height: 200px;">
            <div style="text-align: center; padding: 40px; color: #666;">
                <p>🔍 Loading personalized recommendations...</p>
            </div>
        </div>
    </div>
    ';
}

// Step 7: Usage example in your existing user dashboard page
/*
// In your user_dashboard.php or similar file:

session_start();
$user_id = $_SESSION['user_id']; // Get from your session management

// Your existing dashboard code...

// Add recommendations section
echo '<div class="dashboard-section">';
renderRecommendationsSection();
echo '</div>';

// Don't forget to include the JavaScript functions above
*/
?>