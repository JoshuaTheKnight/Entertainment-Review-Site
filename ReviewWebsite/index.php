<?php 
include 'nav.php';
include 'connection.php'; 
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Article Review Page</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    

    <main class="articles">
    <?php
        // Prepared statement to get reviews with entertainment name and author username
        $stmt = $conn->prepare("SELECT entertainments.title, users.username, reviews.review_text, reviews.review_id FROM reviews JOIN entertainments ON reviews.entertainment_id = entertainments.entertainment_id JOIN users ON reviews.user_id = users.user_id LIMIT 3");
        $stmt->execute();
        $stmt->bind_result($entertainmentTitle, $username, $reviewText, $reviewId);

        while ($stmt->fetch()) {
            echo "<div class='article-card'>";
            echo "<h2>" . htmlspecialchars($entertainmentTitle) . " Review</h2>";
            echo "<p>Review by: " . htmlspecialchars($username) . "</p>";
            echo "<p>" . htmlspecialchars($reviewText) . "</p>";
            echo "<a href = 'review.php?review_id=" . htmlspecialchars($reviewId) . "'> Read More </a>";
            echo "</div>";
        }

        $stmt->close();
        $conn->close();
        ?>
    </main>
</body>
</html>

<style>
/* CSS Styles for the page */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
}

.articles {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 2rem;
    padding: 2rem;
}

.article-card {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
}

.article-card h2 {
    font-size: 1.5rem;
}

.article-card a {
    color: #007bff;
    text-decoration: none;
}
</style>
