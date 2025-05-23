<?php
class BookRecommendationSystem {
    private $pdo;
    
    public function __construct($database_connection) {
        $this->pdo = $database_connection;
    }
    
    /**
     * Enhanced Levenshtein distance with keyword matching
     */
    private function calculateSimilarity($str1, $str2) {
        $str1 = strtolower(trim($str1));
        $str2 = strtolower(trim($str2));
        
        // Direct match gets highest score
        if ($str1 === $str2) {
            return 100;
        }
        
        // Check for substring matches
        if (strpos($str1, $str2) !== false || strpos($str2, $str1) !== false) {
            return 85;
        }
        
        // Calculate Levenshtein distance
        $distance = levenshtein($str1, $str2);
        $maxLength = max(strlen($str1), strlen($str2));
        
        if ($maxLength == 0) return 0;
        
        // Convert distance to similarity percentage
        $similarity = (1 - ($distance / $maxLength)) * 100;
        
        // Keyword-based similarity boost
        $keywordSimilarity = $this->calculateKeywordSimilarity($str1, $str2);
        
        // Combine both scores
        return max($similarity, $keywordSimilarity);
    }
    
    /**
     * Calculate similarity based on common keywords
     */
    private function calculateKeywordSimilarity($str1, $str2) {
        // Define technology categories and keywords
        $categories = [
            'web_frontend' => ['html', 'css', 'javascript', 'react', 'vue', 'angular', 'bootstrap', 'jquery', 'frontend', 'web design', 'responsive'],
            'web_backend' => ['php', 'nodejs', 'python', 'java', 'asp.net', 'ruby', 'laravel', 'django', 'express', 'backend', 'server'],
            'database' => ['mysql', 'postgresql', 'mongodb', 'sql', 'database', 'nosql', 'redis', 'sqlite'],
            'mobile' => ['android', 'ios', 'swift', 'kotlin', 'react native', 'flutter', 'mobile', 'app development'],
            'devops' => ['docker', 'kubernetes', 'aws', 'azure', 'jenkins', 'ci/cd', 'devops', 'cloud', 'deployment'],
            'programming' => ['algorithm', 'data structure', 'object oriented', 'functional programming', 'design patterns'],
            'data_science' => ['machine learning', 'ai', 'data science', 'analytics', 'statistics', 'big data', 'python'],
            'security' => ['cybersecurity', 'encryption', 'security', 'hacking', 'network security', 'authentication']
        ];
        
        $str1_categories = [];
        $str2_categories = [];
        
        // Find categories for both strings
        foreach ($categories as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($str1, $keyword) !== false) {
                    $str1_categories[] = $category;
                }
                if (strpos($str2, $keyword) !== false) {
                    $str2_categories[] = $category;
                }
            }
        }
        
        // Remove duplicates
        $str1_categories = array_unique($str1_categories);
        $str2_categories = array_unique($str2_categories);
        
        // Calculate category overlap
        $common_categories = array_intersect($str1_categories, $str2_categories);
        $total_categories = array_unique(array_merge($str1_categories, $str2_categories));
        
        if (empty($total_categories)) {
            return 0;
        }
        
        return (count($common_categories) / count($total_categories)) * 100;
    }
    
    /**
     * Get user's borrowing history
     */
    private function getUserBorrowingHistory($user_id, $limit = 10) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT DISTINCT b.book_name, b.author_name, b.publication, 
                       CONCAT(b.book_name, ' ', COALESCE(b.author_name, ''), ' ', COALESCE(b.publication, '')) as full_text
                FROM books b 
                INNER JOIN issue_records ir ON b.book_id = ir.book_id 
                WHERE ir.user_id = ? 
                ORDER BY ir.issue_date DESC 
                LIMIT ?
            ");
            $stmt->execute([$user_id, $limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching user history: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get all available books excluding user's history
     */
    private function getAvailableBooks($user_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT b.book_id, b.book_name, b.author_name, b.publication, b.available_quantity,
                       CONCAT(b.book_name, ' ', COALESCE(b.author_name, ''), ' ', COALESCE(b.publication, '')) as full_text
                FROM books b 
                WHERE b.available_quantity > 0 
                AND b.book_id NOT IN (
                    SELECT DISTINCT book_id 
                    FROM issue_records 
                    WHERE user_id = ? AND return_date IS NULL
                )
            ");
            $stmt->execute([$user_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching available books: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Generate recommendations for a user
     */
    public function getRecommendations($user_id, $max_recommendations = 10) {
        // Get user's borrowing history
        $userHistory = $this->getUserBorrowingHistory($user_id);
        
        if (empty($userHistory)) {
            return $this->getPopularBooks($max_recommendations);
        }
        
        // Get all available books
        $availableBooks = $this->getAvailableBooks($user_id);
        
        if (empty($availableBooks)) {
            return [];
        }
        
        $recommendations = [];
        
        // Calculate similarity scores
        foreach ($availableBooks as $book) {
            $maxSimilarity = 0;
            $bestMatch = '';
            
            // Compare with each book in user's history
            foreach ($userHistory as $historyBook) {
                $similarity = $this->calculateSimilarity($historyBook['full_text'], $book['full_text']);
                
                if ($similarity > $maxSimilarity) {
                    $maxSimilarity = $similarity;
                    $bestMatch = $historyBook['book_name'];
                }
            }
            
            // Only recommend books with similarity above threshold
            if ($maxSimilarity >= 30) {
                $recommendations[] = [
                    'book_id' => $book['book_id'],
                    'book_name' => $book['book_name'],
                    'author_name' => $book['author_name'],
                    'publication' => $book['publication'],
                    'available_quantity' => $book['available_quantity'],
                    'similarity_score' => round($maxSimilarity, 2),
                    'matched_with' => $bestMatch,
                    'recommendation_reason' => $this->getRecommendationReason($maxSimilarity)
                ];
            }
        }
        
        // Sort by similarity score (descending)
        usort($recommendations, function($a, $b) {
            return $b['similarity_score'] <=> $a['similarity_score'];
        });
        
        // Return top recommendations
        return array_slice($recommendations, 0, $max_recommendations);
    }
    
    /**
     * Get recommendation reason based on similarity score
     */
    private function getRecommendationReason($score) {
        if ($score >= 80) {
            return "Highly Related";
        } elseif ($score >= 60) {
            return "Related Topic";
        } elseif ($score >= 40) {
            return "Similar Subject";
        } else {
            return "Might Interest You";
        }
    }
    
    /**
     * Get popular books as fallback for new users
     */
    private function getPopularBooks($limit = 10) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT b.book_id, b.book_name, b.author_name, b.publication, b.available_quantity,
                       COUNT(ir.book_id) as issue_count
                FROM books b 
                LEFT JOIN issue_records ir ON b.book_id = ir.book_id 
                WHERE b.available_quantity > 0 
                GROUP BY b.book_id 
                ORDER BY issue_count DESC, b.book_name ASC 
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Format as recommendations
            return array_map(function($book) {
                return [
                    'book_id' => $book['book_id'],
                    'book_name' => $book['book_name'],
                    'author_name' => $book['author_name'],
                    'publication' => $book['publication'],
                    'available_quantity' => $book['available_quantity'],
                    'similarity_score' => 0,
                    'matched_with' => 'Popular Choice',
                    'recommendation_reason' => 'Popular Among Users'
                ];
            }, $books);
        } catch (PDOException $e) {
            error_log("Error fetching popular books: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get recommendations as JSON (for AJAX calls)
     */
    public function getRecommendationsJSON($user_id, $max_recommendations = 10) {
        $recommendations = $this->getRecommendations($user_id, $max_recommendations);
        return json_encode($recommendations);
    }
}

// Usage Example and API Endpoint
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'recommendations') {
    // Database connection (adjust according to your configuration)
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=library_db", "username", "password");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $recommendationSystem = new BookRecommendationSystem($pdo);
        
        $user_id = $_GET['user_id'] ?? null;
        $limit = $_GET['limit'] ?? 10;
        
        if (!$user_id) {
            http_response_code(400);
            echo json_encode(['error' => 'User ID is required']);
            exit;
        }
        
        header('Content-Type: application/json');
        echo $recommendationSystem->getRecommendationsJSON($user_id, $limit);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database connection failed']);
    }
}

// Example usage in your existing system:
/*
// Initialize the recommendation system
$pdo = new PDO("mysql:host=localhost;dbname=library_db", "username", "password");
$recommendationSystem = new BookRecommendationSystem($pdo);

// Get recommendations for user ID 123
$user_id = 123;
$recommendations = $recommendationSystem->getRecommendations($user_id, 5);

// Display recommendations
foreach ($recommendations as $rec) {
    echo "<div class='recommendation'>";
    echo "<h4>" . htmlspecialchars($rec['book_name']) . "</h4>";
    echo "<p>Author: " . htmlspecialchars($rec['author_name']) . "</p>";
    echo "<p>Similarity: " . $rec['similarity_score'] . "% - " . $rec['recommendation_reason'] . "</p>";
    echo "<p>Because you read: " . htmlspecialchars($rec['matched_with']) . "</p>";
    echo "</div>";
}
*/
?>