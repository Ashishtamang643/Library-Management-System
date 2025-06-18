<?php
// This snippet expects session_start() to have already run in viewprofile.php

if (!isset($_SESSION['Email'])) {
    echo "<p>Please log in to see recommendations.</p>";
    return;
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=library", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "<p>Database Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    return;
}

$email = $_SESSION['Email'];

// 1) Get the user's numeric ID
$stmt = $pdo->prepare("SELECT ID FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    echo "<p>User not found.</p>";
    return;
}
$userId = $user['ID'];

// 2) Get comprehensive borrowing history with detailed information
$stmt = $pdo->prepare("
    SELECT 
        i.book_author,
        i.publication,
        i.faculty,
        i.semester,
        i.issue_date,
        i.returned_date,
        b.book_name,
        b.book_num
    FROM issued i
    LEFT JOIN books b ON i.book_num = b.book_num
    WHERE i.student_id = ?
    ORDER BY i.issue_date DESC
");
$stmt->execute([$userId]);
$borrowingHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($borrowingHistory)) {
    echo "<div class='recommendation-container'><div class='no-history'><p>No borrowing history to generate recommendations.</p></div></div>";
    return;
}

// 3) Analyze user preferences
$authorPrefs = [];
$publicationPrefs = [];
$facultyPrefs = [];
$semesterPrefs = [];
$recentActivity = [];

foreach ($borrowingHistory as $record) {
    // Count author preferences
    if (!empty($record['book_author'])) {
        $authorPrefs[$record['book_author']] = ($authorPrefs[$record['book_author']] ?? 0) + 1;
    }
    
    // Count publication preferences
    if (!empty($record['publication'])) {
        $publicationPrefs[$record['publication']] = ($publicationPrefs[$record['publication']] ?? 0) + 1;
    }
    
    // Count faculty preferences
    if (!empty($record['faculty'])) {
        $facultyPrefs[$record['faculty']] = ($facultyPrefs[$record['faculty']] ?? 0) + 1;
    }
    
    // Count semester preferences
    if (!empty($record['semester'])) {
        $semesterPrefs[$record['semester']] = ($semesterPrefs[$record['semester']] ?? 0) + 1;
    }
    
    // Track recent activity (last 6 months for recency boost)
    if (!empty($record['issue_date'])) {
        $issueDate = new DateTime($record['issue_date']);
        $sixMonthsAgo = new DateTime('-6 months');
        if ($issueDate > $sixMonthsAgo) {
            $recentActivity[] = $record;
        }
    }
}

// 4) Get books user hasn't borrowed that are currently available
$stmt = $pdo->prepare("
    SELECT 
        b.book_num,
        b.book_name,
        b.author_name,
        b.publication,
        b.faculty,
        b.semester,
        b.available_quantity,
        b.book_edition
    FROM books b
    WHERE b.book_num NOT IN (
        SELECT DISTINCT book_num 
        FROM issued 
        WHERE student_id = ?
    )
    AND b.available_quantity > 0
");
$stmt->execute([$userId]);
$candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 5) Enhanced scoring algorithm
$scoredBooks = [];
foreach ($candidates as $book) {
    $score = 0;
    $reasons = [];
    
    // Author preference score (highest weight)
    if (!empty($book['author_name']) && isset($authorPrefs[$book['author_name']])) {
        $authorScore = $authorPrefs[$book['author_name']] * 5;
        $score += $authorScore;
        $reasons[] = "You've read {$authorPrefs[$book['author_name']]} book(s) by this author";
    }
    
    // Publication preference score
    if (!empty($book['publication']) && isset($publicationPrefs[$book['publication']])) {
        $pubScore = $publicationPrefs[$book['publication']] * 2;
        $score += $pubScore;
        $reasons[] = "You like books from {$book['publication']}";
    }
    
    // Faculty/subject preference score
    if (!empty($book['faculty']) && isset($facultyPrefs[$book['faculty']])) {
        $facultyScore = $facultyPrefs[$book['faculty']] * 3;
        $score += $facultyScore;
        $reasons[] = "Matches your interest in {$book['faculty']}";
    }
    
    // Semester relevance score
    if (!empty($book['semester']) && isset($semesterPrefs[$book['semester']])) {
        $semesterScore = $semesterPrefs[$book['semester']] * 2;
        $score += $semesterScore;
        $reasons[] = "Relevant to your {$book['semester']} studies";
    }
    
    // Popularity boost (books with limited availability might be in demand)
    if ($book['available_quantity'] <= 2) {
        $score += 1;
        $reasons[] = "Limited availability - popular book";
    }
    
    // Recent activity boost
    foreach ($recentActivity as $recent) {
        if (!empty($recent['book_author']) && $recent['book_author'] === $book['author_name']) {
            $score += 2;
            $reasons[] = "Recently read author";
            break;
        }
        if (!empty($recent['faculty']) && $recent['faculty'] === $book['faculty']) {
            $score += 1;
            break;
        }
    }
    
    $book['score'] = $score;
    $book['reasons'] = $reasons;
    
    if ($score > 0) {
        $scoredBooks[] = $book;
    }
}

// 6) Sort by score and get recommendations
usort($scoredBooks, function($a, $b) {
    if ($a['score'] == $b['score']) {
        // If scores are equal, prefer books with higher availability
        return $b['available_quantity'] - $a['available_quantity'];
    }
    return $b['score'] - $a['score'];
});

// Get different types of recommendations
$topRecommendations = array_slice($scoredBooks, 0, 5);

// Get books from new authors (diversification)
$newAuthorBooks = [];
$userAuthors = array_keys($authorPrefs);
foreach ($candidates as $book) {
    if (!empty($book['author_name']) && !in_array($book['author_name'], $userAuthors)) {
        // Score based on other factors
        $score = 0;
        if (!empty($book['faculty']) && isset($facultyPrefs[$book['faculty']])) {
            $score += $facultyPrefs[$book['faculty']];
        }
        if (!empty($book['publication']) && isset($publicationPrefs[$book['publication']])) {
            $score += $publicationPrefs[$book['publication']];
        }
        if ($score > 0) {
            $book['score'] = $score;
            $newAuthorBooks[] = $book;
        }
    }
}
usort($newAuthorBooks, fn($a, $b) => $b['score'] - $a['score']);
$diverseRecommendations = array_slice($newAuthorBooks, 0, 3);

?>

<div class="recommendation-container">
    <div class="recommendation-header">
        <h2>📚 Personalized Book Recommendations</h2>
    </div>
    
    <?php if (!empty($topRecommendations)): ?>
        <div class="recommendation-section">
            <h3>🎯 Based on Your Reading History</h3>
            <div class="table-container">
                <table class="recommendation-table">
                    <thead>
                        <tr>
                            <th>Book Title</th>
                            <th>Author</th>
                            <th>Publication</th>
                            <th>Match Score</th>
                            <th>Available</th>
                            <th>Why Recommended</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topRecommendations as $book): ?>
                            <tr>
                                <td class="book-title"><?= htmlspecialchars($book['book_name']) ?></td>
                                <td class="author-name"><?= htmlspecialchars($book['author_name']) ?></td>
                                <td class="publication"><?= htmlspecialchars($book['publication'] ?? 'N/A') ?></td>
                                <td class="match-score">
                                    <span class="score-badge"><?= $book['score'] ?>pts</span>
                                </td>
                                <td class="availability">
                                    <span class="qty-badge"><?= $book['available_quantity'] ?></span>
                                </td>
                                <td class="reasons">
                                    <?= !empty($book['reasons']) ? htmlspecialchars(implode(' • ', $book['reasons'])) : 'General match' ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($diverseRecommendations)): ?>
        <div class="recommendation-section">
            <h3>🌟 Discover New Authors</h3>
            <div class="table-container">
                <table class="recommendation-table diverse-table">
                    <thead>
                        <tr>
                            <th>Book Title</th>
                            <th>Author</th>
                            <th>Faculty</th>
                            <th>Available</th>
                            <th>Why Try This</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($diverseRecommendations as $book): ?>
                            <tr>
                                <td class="book-title"><?= htmlspecialchars($book['book_name']) ?></td>
                                <td class="author-name"><?= htmlspecialchars($book['author_name']) ?></td>
                                <td class="faculty"><?= htmlspecialchars($book['faculty'] ?? 'N/A') ?></td>
                                <td class="availability">
                                    <span class="qty-badge"><?= $book['available_quantity'] ?></span>
                                </td>
                                <td class="reasons">New author in your preferred subjects</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (empty($topRecommendations) && empty($diverseRecommendations)): ?>
        <div class="no-recommendations">
            <div class="no-rec-content">
                <h3>🔍 Building Your Recommendations</h3>
                <p>We need more data to create personalized suggestions.</p>
                <p>Try borrowing a few more books to unlock custom recommendations!</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.recommendation-container {
    max-width: 1800px;
    margin: 0 auto;
    padding: 20px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    color: #333;
}

.recommendation-header {
    text-align: center;
    margin-bottom: 40px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f0f0f0;
}

.recommendation-header h2 {
    font-size: 2em;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
}

.recommendation-section {
    margin-bottom: 40px;
}

.recommendation-section h3 {
    font-size: 1.4em;
    font-weight: 600;
    color: #34495e;
    margin-bottom: 20px;
    padding-left: 10px;
    border-left: 4px solid #3498db;
}

.table-container {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    overflow: hidden;
    border: 1px solid #e9ecef;
}

.recommendation-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.95em;
}

.recommendation-table thead {
    background: #f8f9fa;
}

.recommendation-table th {
    padding: 16px 12px;
    text-align: left;
    font-weight: 600;
    color: #dee2e6;
    border-bottom: 2px solid #dee2e6;
    font-size: 0.9em;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.recommendation-table td {
    padding: 16px 12px;
    border-bottom: 1px solid #f1f3f4;
    vertical-align: top;
}

.recommendation-table tbody tr:hover {
    background-color: #f8f9fa;
    transition: background-color 0.2s ease;
}

.recommendation-table tbody tr:last-child td {
    border-bottom: none;
}

.book-title {
    font-weight: 600;
    color: #2c3e50;
    min-width: 200px;
}

.author-name {
    color: #5a6c7d;
    font-weight: 500;
}

.publication, .faculty {
    color: #6c757d;
    font-size: 0.9em;
}

.match-score {
    text-align: center;
}

.score-badge {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8em;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.availability {
    text-align: center;
}

.qty-badge {
    background: #28a745;
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.85em;
    font-weight: 600;
}

.reasons {
    color: #6c757d;
    font-size: 0.9em;
    line-height: 1.4;
    max-width: 300px;
}

.diverse-table thead {
    background: #f1f3f4;
}

.diverse-table .score-badge {
    background: linear-gradient(135deg, #fd79a8 0%, #fdcb6e 100%);
}

.no-recommendations {
    text-align: center;
    padding: 60px 20px;
    background: #f8f9fa;
    border-radius: 12px;
    margin: 40px 0;
}

.no-rec-content h3 {
    color: #495057;
    margin-bottom: 10px;
}

.no-rec-content p {
    color: #6c757d;
    margin: 8px 0;
}

.no-history {
    text-align: center;
    padding: 60px 20px;
    background: #f8f9fa;
    border-radius: 12px;
    color: #6c757d;
}

.profile-summary {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    border: 1px solid #e9ecef;
    margin-top: 40px;
}

.profile-summary h3 {
    color: #2c3e50;
    margin-bottom: 25px;
    font-size: 1.3em;
    font-weight: 600;
}

.stat-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.stat-card {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    border-left: 4px solid #3498db;
}

.stat-card h4 {
    color: #495057;
    margin: 0 0 10px 0;
    font-size: 0.9em;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-card p {
    color: #2c3e50;
    margin: 0;
    font-weight: 500;
    line-height: 1.4;
}

.stat-number {
    font-size: 1.8em !important;
    font-weight: 700 !important;
    color: #3498db !important;
}

@media (max-width: 1200px) {
    .recommendation-container {
        padding: 15px;
    }
    
    .recommendation-table {
        font-size: 0.9em;
    }
    
    .recommendation-table th,
    .recommendation-table td {
        padding: 12px 8px;
    }
}

@media (max-width: 768px) {
    .recommendation-header h2 {
        font-size: 1.8em;
    }
    
    .table-container {
        overflow-x: auto;
    }
    
    .recommendation-table {
        min-width: 800px;
    }
    
    .stat-grid {
        grid-template-columns: 1fr;
    }
}
</style>