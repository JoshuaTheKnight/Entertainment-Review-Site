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
            echo "<p>" . htmlspecialchars($reviewText) . "</p>";
            echo "<p>Rating: " . htmlspecialchars($rating) . "/10</p>";
        } else {
            echo "<p>Review not found.</p>";
        }

        $stmt->close();
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

.article h1{
    text-align: center;
}

.article h3{
    text-align: center;
}
</style>