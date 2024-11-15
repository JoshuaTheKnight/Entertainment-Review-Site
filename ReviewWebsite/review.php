<?php 
include 'nav.php';
include 'connection.php'; 

// Ensure connection is established
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Article Review Page</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <main class="article">
    <?php
    // Check if review_id is set in the URL
    if (isset($_GET['review_id'])) {
        $reviewId = $_GET['review_id'];

        // Prepared statement to get specific review details
        $stmt = $conn->prepare("SELECT entertainments.title, users.username, reviews.review_text, reviews.rating, reviews.review_date 
                                FROM reviews 
                                JOIN entertainments ON reviews.entertainment_id = entertainments.entertainment_id 
                                JOIN users ON reviews.user_id = users.user_id 
                                WHERE reviews.review_id = ?");
        $stmt->bind_param("i", $reviewId);
        $stmt->execute();
        $stmt->bind_result($entertainmentTitle, $username, $reviewText, $rating, $reviewDate);

        if ($stmt->fetch()) {
            echo "<h1>" . htmlspecialchars($entertainmentTitle) . " Review</h1>";
            echo "<h3>Review by: " . htmlspecialchars($username) . "</h3>";
            echo "<p>Date: " . htmlspecialchars($reviewDate) . "</p>";
            echo "<p>" . nl2br(htmlspecialchars($reviewText)) . "</p>";
            echo "<p>Rating: " . htmlspecialchars($rating) . "/10</p>";
        } else {
            echo "<p>Review not found.</p>";
        }

        $stmt->close();

        // Comment submission handler
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment_text'])) {
            // Assume user_id is stored in session after login
            if (isset($_SESSION['user_id'])) {
                $userId = $_SESSION['user_id'];
                $commentText = $_POST['comment_text'];

                $stmt_insert = $conn->prepare("INSERT INTO comments (review_id, user_id, comment_text, comment_date) VALUES (?, ?, ?, NOW())");
                $stmt_insert->bind_param("iis", $reviewId, $userId, $commentText);

                if ($stmt_insert->execute()) {
                    echo "<p>Comment added successfully!</p>";
                } else {
                    echo "<p>Error adding comment: " . $stmt_insert->error . "</p>";
                }

                $stmt_insert->close();
            } else {
                echo "<p>You must be logged in to comment.</p>";
            }
        }

        // Display comment form
        if (isset($_SESSION['user_id'])) {
            echo '<h3>Add a Comment</h3>';
            echo '<form action="review.php?review_id=' . htmlspecialchars($reviewId) . '" method="POST">';
            echo '    <textarea name="comment_text" required></textarea><br><br>';
            echo '    <button type="submit">Submit Comment</button>';
            echo '</form>';
        } else {
            echo '<p>You must be <a href="loginsignup.php">logged in</a> to add a comment.</p>';
        }

        // Display comments for this review
        $stmt_comments = $conn->prepare("SELECT users.username, comments.comment_text, comments.comment_date
                                         FROM comments
                                         JOIN users ON comments.user_id = users.user_id
                                         WHERE comments.review_id = ?
                                         ORDER BY comments.comment_date DESC");
        $stmt_comments->bind_param("i", $reviewId);
        $stmt_comments->execute();
        $stmt_comments->bind_result($commentUsername, $commentText, $commentDate);

        echo '<h3>Comments</h3>';
        while ($stmt_comments->fetch()) {
            echo '<div class="comment">';
            echo '<p><strong>' . htmlspecialchars($commentUsername) . ':</strong> (' . htmlspecialchars($commentDate) . ')</p>';
            echo '<p>' . htmlspecialchars($commentText) . '</p>';
            echo '</div><br>';
        }

        $stmt_comments->close();
        $conn->close();
    } else {
        echo "<p>No review ID provided.</p>";
    }
    ?>
    </main>
</body>
<style>
/* CSS Styles for the page */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
}

.article {
    max-width: 800px;
    margin: 2rem auto;
    padding: 2rem;
    background-color: white;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}

.article h1 {
    text-align: center;
    font-size: 2rem;
    margin-bottom: 1rem;
}

.article h3 {
    font-size: 1.2rem;
    margin-bottom: 1rem;
    color: #555;
}

.article p {
    font-size: 1rem;
    line-height: 1.6;
    margin-bottom: 1rem;
    color: #333;
}

.comment {
    background-color: #f9f9f9;
    padding: 1rem;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.comment p {
    margin: 0.5rem 0;
}

textarea {
    width: 100%;
    height: 100px;
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
}

button {
    padding: 0.75rem;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

button:hover {
    background-color: #0056b3;
}
</style>
