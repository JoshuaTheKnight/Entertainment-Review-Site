<?php 
include 'nav.php';
include 'connection.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entertainment Reviews - Home</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* General Styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f8f8;
            color: #000; /* Set default text color to black */
        }

        /* Hero Section */
        .hero {
            background-image: url('hero-banner.jpg'); /* Use an eye-catching entertainment-themed image */
            background-size: cover;
            background-position: center;
            color: #000; /* Set text color to black */
            padding: 4rem 2rem;
            text-align: center;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #000; /* Set text color to black */
        }

        .hero p {
            font-size: 1.5rem;
            margin-bottom: 2rem;
            color: #000; /* Set text color to black */
        }

        .hero .cta-buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        .cta-buttons a {
            background-color: #007bff;
            color: white;
            padding: 1rem 2rem;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .cta-buttons a:hover {
            background-color: #0056b3;
        }

        /* Main Content Section */
        .content-section {
            padding: 3rem 2rem;
        }

        .content-section h2 {
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2rem;
            color: #000; /* Set text color to black */
        }

        .articles {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            width: 90%;
            margin: 0 auto;
        }

        .article-card {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .article-card:hover {
            transform: translateY(-5px);
            box-shadow: 0px 6px 10px rgba(0, 0, 0, 0.15);
        }

        .article-card h3 {
            font-size: 1.5rem;
            color: #000; /* Set text color to black */
            margin-bottom: 0.5rem;
        }

        .article-card p {
            color: #000; /* Set text color to black */
        }

        .article-card a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        .article-card a:hover {
            text-decoration: underline;
        }

        /* Footer */
        .footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 2rem;
            margin-top: 3rem;
        }

        .footer p {
            margin: 0.5rem;
            color: white;
        }

    </style>
</head>
<body>

    <!-- Hero Section -->
    <section class="hero">
        <h1>Welcome to Entertainment Reviews</h1>
        <p>Your go-to platform for the latest reviews of movies, shows, and more!</p>
        <div class="cta-buttons">
            <a href="createreview.php">Write a Review</a>
            <a href="allreviews.php">View All Reviews</a>
        </div>
    </section>

    <!-- Main Content Section -->
    <section class="content-section">
        <h2>Recent Reviews</h2>
        <main class="articles">
            <?php
                // Prepared statement to get recent reviews with entertainment name and author username
                $stmt = $conn->prepare("SELECT entertainments.title, users.username, reviews.review_id FROM reviews JOIN entertainments ON reviews.entertainment_id = entertainments.entertainment_id JOIN users ON reviews.user_id = users.user_id ORDER BY reviews.review_date DESC LIMIT 3");
                $stmt->execute();
                $stmt->bind_result($entertainmentTitle, $username, $reviewId);

                while ($stmt->fetch()) {
                    echo "<div class='article-card'>";
                    echo "<h3>" . htmlspecialchars($entertainmentTitle) . " Review</h3>";
                    echo "<p>Review by: <strong>" . htmlspecialchars($username) . "</strong></p>";
                    echo "<a href='review.php?review_id=" . htmlspecialchars($reviewId) . "'>Read More</a>";
                    echo "</div>";
                }

                $stmt->close();
                $conn->close();
            ?>
        </main>
    </section>

    <!-- Footer Section -->
    <footer class="footer">
        <p>&copy; 2024 Entertainment Reviews. All rights reserved.</p>
        <p>Follow us on social media for the latest updates and entertainment news!</p>
    </footer>

</body>
</html>
