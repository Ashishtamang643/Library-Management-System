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
    echo "<div class='recommendation-box'><p>No borrowing history to generate recommendations.</p></div>";
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

<div class="recommendation-box">
    <h3>📚 Personalized Recommendations</h3>
    
    <?php if (!empty($topRecommendations)): ?>
        <div class="recommendation-section">
            <h4>🎯 Based on Your Reading History</h4>
            <ul class="recommendation-list">
                <?php foreach ($topRecommendations as $book): ?>
                    <li class="recommendation-item">
                        <div class="book-info">
                            <strong><?= htmlspecialchars($book['book_name']) ?></strong>
                            <br>
                            <span class="author">by <?= htmlspecialchars($book['author_name']) ?></span>
                            <?php if (!empty($book['publication'])): ?>
                                <span class="publication"> • <?= htmlspecialchars($book['publication']) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="recommendation-score">
                            <span class="score-badge">Match: <?= $book['score'] ?>pts</span>
                            <span class="availability"><?= $book['available_quantity'] ?> available</span>
                        </div>
                        <?php if (!empty($book['reasons'])): ?>
                            <div class="reasons">
                                <small><?= implode(' • ', $book['reasons']) ?></small>
                            </div>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($diverseRecommendations)): ?>
        <div class="recommendation-section">
            <h4>🌟 Discover New Authors</h4>
            <ul class="recommendation-list diverse">
                <?php foreach ($diverseRecommendations as $book): ?>
                    <li class="recommendation-item">
                        <div class="book-info">
                            <strong><?= htmlspecialchars($book['book_name']) ?></strong>
                            <br>
                            <span class="author">by <?= htmlspecialchars($book['author_name']) ?></span>
                            <?php if (!empty($book['faculty'])): ?>
                                <span class="faculty"> • <?= htmlspecialchars($book['faculty']) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="recommendation-score">
                            <span class="availability"><?= $book['available_quantity'] ?> available</span>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <?php if (empty($topRecommendations) && empty($diverseRecommendations)): ?>
        <div class="no-recommendations">
            <p>🔍 We're analyzing your reading patterns...</p>
            <p>Try borrowing a few more books to get personalized recommendations!</p>
        </div>
    <?php endif; ?>
    
    <!-- User Preferences Summary -->
    <div class="preferences-summary">
        <h4>📊 Your Reading Profile</h4>
        <div class="preference-stats">
            <?php if (!empty($authorPrefs)): ?>
                <div class="stat-item">
                    <strong>Favorite Authors:</strong>
                    <?php 
                    arsort($authorPrefs);
                    $topAuthors = array_slice(array_keys($authorPrefs), 0, 3);
                    echo htmlspecialchars(implode(', ', $topAuthors));
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($facultyPrefs)): ?>
                <div class="stat-item">
                    <strong>Preferred Subjects:</strong>
                    <?php 
                    arsort($facultyPrefs);
                    $topFaculties = array_slice(array_keys($facultyPrefs), 0, 3);
                    echo htmlspecialchars(implode(', ', $topFaculties));
                    ?>
                </div>
            <?php endif; ?>
            
            <div class="stat-item">
                <strong>Books Read:</strong> <?= count($borrowingHistory) ?>
            </div>
        </div>
    </div>
</div>
<style>
.recommendation-box {
    background: #ffffff;
    color: #333333;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
    border: 1px solid #e0e0e0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.recommendation-section {
    margin-bottom: 25px;
}

.recommendation-section h4 {
    margin-bottom: 15px;
    color: #555555;
    font-size: 1.1em;
}

.recommendation-list {
    list-style: none;
    padding: 0;
}

.recommendation-item {
    background: #f8f9fa;
    margin-bottom: 12px;
    padding: 15px;
    border-radius: 6px;
    border-left: 3px solid #cccccc;
}

.recommendation-list.diverse .recommendation-item {
    border-left-color: #999999;
}

.book-info {
    margin-bottom: 8px;
}

.book-info strong {
    font-size: 1.05em;
    display: block;
    margin-bottom: 5px;
    color: #333333;
}

.author, .publication, .faculty {
    color: #666666;
    font-size: 0.9em;
}

.recommendation-score {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.score-badge {
    background: #e9ecef;
    color: #495057;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 0.8em;
    font-weight: bold;
}

.availability {
    color: #666666;
    font-size: 0.85em;
}

.reasons {
    color: #777777;
    font-style: italic;
    margin-top: 5px;
}

.preferences-summary {
    background: #f1f3f4;
    padding: 15px;
    border-radius: 6px;
    margin-top: 20px;
}

.preference-stats {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.stat-item {
    font-size: 0.9em;
    color: #555555;
}

.no-recommendations {
    text-align: center;
    padding: 30px;
    background: #f8f9fa;
    border-radius: 6px;
    color: #666666;
}

@media (max-width: 768px) {
    .recommendation-score {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
}
</style>