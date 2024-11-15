<?php
// Start session and include necessary files
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'nav.php';
include 'connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: loginsignup.php");
    exit();
}

// Get account information
$user_id = $_SESSION['user_id'];
$sql = "SELECT username, email, date_joined FROM Users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Get total reviews and comments
$sql_reviews_count = "SELECT COUNT(*) AS total_reviews FROM Reviews WHERE user_id = ?";
$stmt = $conn->prepare($sql_reviews_count);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_reviews = $stmt->get_result()->fetch_assoc()['total_reviews'];
$stmt->close();

$sql_comments_count = "SELECT COUNT(*) AS total_comments FROM Comments WHERE user_id = ?";
$stmt = $conn->prepare($sql_comments_count);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_comments = $stmt->get_result()->fetch_assoc()['total_comments'];
$stmt->close();

// Get reviews and comments details
$sql_reviews = "SELECT r.review_id, r.review_text, r.rating, r.review_date, e.title AS entertainment_title 
                FROM Reviews r 
                JOIN Entertainments e ON r.entertainment_id = e.entertainment_id 
                WHERE r.user_id = ? 
                ORDER BY r.review_date DESC";
$stmt = $conn->prepare($sql_reviews);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$reviews = $stmt->get_result();
$stmt->close();

$sql_comments = "SELECT c.*, r.review_id, u.username AS review_author, e.title AS entertainment_title 
                 FROM comments c 
                 JOIN reviews r ON c.review_id = r.review_id 
                 JOIN users u ON r.user_id = u.user_id 
                 JOIN entertainments e ON r.entertainment_id = e.entertainment_id 
                 WHERE c.user_id = ?
                 ORDER BY r.review_date ASC";
$stmt = $conn->prepare($sql_comments);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$comments = $stmt->get_result();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Information</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .account-info-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #444;
            color: white;
            padding: 1rem 2rem;
        }

        .account-info-bar div {
            margin: 0.5rem;
        }

        .content {
            display: flex;
            justify-content: space-between;
            width: 95%;
            margin: auto;
            padding: 2rem;
            gap: 2rem;
        }

        .articles {
            display: flex;
            flex-direction: column;
            gap: 2rem;
            width: 48%;
            height: 600px;
            overflow-y: auto;
        }

        .articles .article-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            min-height: 200px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .article-card h2 {
            font-size: 1.5rem;
        }

        .article-card a {
            color: #007bff;
            text-decoration: none;
        }

        .articles h2 {
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="account-info-bar">
        <div class="account-name">Username: <?php echo htmlspecialchars($user_result['username']); ?></div>
        <div class="account-date">Joined: <?php echo htmlspecialchars($user_result['date_joined']); ?></div>
        <div class="account-stats">
            Total Reviews: <?php echo htmlspecialchars($total_reviews); ?> | Total Comments: <?php echo htmlspecialchars($total_comments); ?>
        </div>
    </div>

    <div class="content">
        <!-- Reviews Section -->
        <div class="articles">
            <h2>Your Reviews</h2>
            <?php while ($review = $reviews->fetch_assoc()): ?>
                <div class="article-card">
                <h3>
                    <a href="review.php?review_id=<?php echo htmlspecialchars($review['review_id']); ?>">
                        <?php echo htmlspecialchars($review['entertainment_title']); ?>
                    </a>
                </h3>
                <p>Rating: <?php echo htmlspecialchars($review['rating']); ?></p>
                <p>Date: <?php echo htmlspecialchars($review['review_date']); ?></p>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Comments Section -->
        <div class="articles">
            <h2>Your Comments</h2>
            <?php while ($comment = $comments->fetch_assoc()): ?>
                <div class="article-card">
                    <h3>
                        <a href="review.php?review_id=<?php echo htmlspecialchars($comment['review_id']); ?>">
                            <?php echo htmlspecialchars($comment['entertainment_title']); ?> Review by <?php echo htmlspecialchars($comment['review_author']); ?>
                        </a>
                        
                    </h3>
                    <p><?php echo htmlspecialchars($comment['comment_text']); ?></p>
                    <p>Date: <?php echo htmlspecialchars($comment['comment_date']); ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>



