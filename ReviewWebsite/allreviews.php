<?php 
include 'nav.php';
include 'connection.php'; 

// Fetching dropdown options from the database for genres and entertainments
$entertainments = $conn->query("SELECT entertainment_id, title FROM entertainments ORDER BY title ASC");
$genres = $conn->query("SELECT genre_id, genre_name FROM genres ORDER BY genre_name ASC");
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Article Review Page</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <main class="filter-container">
        <!-- Sorting Form -->
        <form method="GET" action="allreviews.php" class="filter-form">
            <div class="filter-options">
                <!-- Entertainment Sorting Dropdown -->
                <div class="filter-group">
                    <label for="entertainment">Entertainment:</label>
                    <select name="entertainment" id="entertainment">
                        <option value="" <?php echo (!isset($_GET['entertainment']) || $_GET['entertainment'] == '') ? 'selected' : ''; ?>>None</option>
                        <option value="title_asc" <?php echo (isset($_GET['entertainment']) && $_GET['entertainment'] == 'title_asc') ? 'selected' : ''; ?>>All Entertainment A-Z</option>
                        <option value="title_desc" <?php echo (isset($_GET['entertainment']) && $_GET['entertainment'] == 'title_desc') ? 'selected' : ''; ?>>All Entertainment Z-A</option>
                        <?php while ($row = $entertainments->fetch_assoc()): ?>
                            <option value="<?php echo $row['entertainment_id']; ?>" <?php echo (isset($_GET['entertainment']) && $_GET['entertainment'] == $row['entertainment_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($row['title']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Genre Filtering Dropdown -->
                <div class="filter-group">
                    <label for="genre">Genre:</label>
                    <select name="genre" id="genre">
                        <option value="" <?php echo (!isset($_GET['genre']) || $_GET['genre'] == '') ? 'selected' : ''; ?>>None</option>
                        <?php while ($row = $genres->fetch_assoc()): ?>
                            <option value="<?php echo $row['genre_id']; ?>" <?php echo (isset($_GET['genre']) && $_GET['genre'] == $row['genre_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($row['genre_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Review Score Filtering Dropdown -->
                <div class="filter-group">
                    <label for="rating">Review Score:</label>
                    <select name="rating" id="rating">
                        <option value="" <?php echo (!isset($_GET['rating']) || $_GET['rating'] == '') ? 'selected' : ''; ?>>None</option>
                        <option value="rating_desc" <?php echo (isset($_GET['rating']) && $_GET['rating'] == 'rating_desc') ? 'selected' : ''; ?>>Highest Rating</option>
                        <option value="rating_asc" <?php echo (isset($_GET['rating']) && $_GET['rating'] == 'rating_asc') ? 'selected' : ''; ?>>Lowest Rating</option>
                        <?php for ($i = 10; $i >= 1; $i--): ?>
                            <option value="<?php echo $i; ?>" <?php echo (isset($_GET['rating']) && $_GET['rating'] == (string)$i) ? 'selected' : ''; ?>>
                                <?php echo $i; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <!-- Sort By Date -->
                <div class="filter-group">
                    <label for="sort">Sort By:</label>
                    <select name="sort" id="sort">
                        <option value="" <?php echo (!isset($_GET['sort']) || $_GET['sort'] == '') ? 'selected' : ''; ?>>None</option>
                        <option value="review_date_desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'review_date_desc') ? 'selected' : ''; ?>>Most Recent</option>
                        <option value="review_date_asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'review_date_asc') ? 'selected' : ''; ?>>Oldest</option>
                    </select>
                </div>
            </div>
            <button type="submit">Apply</button>
        </form>

        <?php
        // Building SQL query based on selected filters
        $whereClauses = [];
        $params = [];
        $types = "";

        // Entertainment filter
        if (isset($_GET['entertainment']) && $_GET['entertainment'] !== "") {
            if ($_GET['entertainment'] == 'title_asc') {
                $orderBy = "ORDER BY entertainments.title ASC";
            } elseif ($_GET['entertainment'] == 'title_desc') {
                $orderBy = "ORDER BY entertainments.title DESC";
            } else {
                $whereClauses[] = "reviews.entertainment_id = ?";
                $params[] = $_GET['entertainment'];
                $types .= "i";
            }
        }

        // Genre filter
        if (isset($_GET['genre']) && $_GET['genre'] !== "") {
            $whereClauses[] = "entertainment_genres.genre_id = ?";
            $params[] = $_GET['genre'];
            $types .= "i";
        }

        // Rating filter
        if (isset($_GET['rating']) && $_GET['rating'] !== "") {
            if ($_GET['rating'] == 'rating_desc') {
                $orderBy = "ORDER BY reviews.rating DESC";
            } elseif ($_GET['rating'] == 'rating_asc') {
                $orderBy = "ORDER BY reviews.rating ASC";
            } else {
                $whereClauses[] = "reviews.rating = ?";
                $params[] = $_GET['rating'];
                $types .= "i";
            }
        }

        // Sort by review date
        if (isset($_GET['sort']) && $_GET['sort'] !== "") {
            if ($_GET['sort'] == 'review_date_desc') {
                $orderBy = "ORDER BY reviews.review_date DESC";
            } elseif ($_GET['sort'] == 'review_date_asc') {
                $orderBy = "ORDER BY reviews.review_date ASC";
            }
        }

        // Default sorting if no order has been set
        $orderBy = isset($orderBy) ? $orderBy : "ORDER BY reviews.review_date DESC";

        // Join condition for genres and entertainments
        $joinGenre = isset($_GET['genre']) && $_GET['genre'] !== "" ? "JOIN entertainment_genres ON entertainments.entertainment_id = entertainment_genres.entertainment_id" : "";

        // Building the final SQL query
        $sql = "SELECT entertainments.title, users.username, reviews.review_id, reviews.rating 
                FROM reviews 
                JOIN entertainments ON reviews.entertainment_id = entertainments.entertainment_id 
                JOIN users ON reviews.user_id = users.user_id 
                $joinGenre 
                " . (count($whereClauses) > 0 ? "WHERE " . implode(" AND ", $whereClauses) : "") . " 
                $orderBy";

        $stmt = $conn->prepare($sql);

        if (count($params) > 0) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $stmt->bind_result($entertainmentTitle, $username, $reviewId, $rating);

        while ($stmt->fetch()) {
            echo "<div class='article-card'>";
            echo "<h2>" . htmlspecialchars($entertainmentTitle) . " Review</h2>";
            echo "<p>Review by: " . htmlspecialchars($username) . "</p>";
            echo "<p>Rating: " . htmlspecialchars($rating) . "/10</p>";
            echo "<a href='review.php?review_id=" . htmlspecialchars($reviewId) . "'> Read More </a>";
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

.filter-container {
    width: 100%;
    padding: 0;
    margin: 0;
}

.filter-form {
    width: 100%;
    background-color: #f5f5f5;
    padding: 1rem 2rem;
    box-sizing: border-box;
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    gap: 1.5rem;
    justify-content: space-between;
}

.filter-options {
    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem;
    width: 100%;
}

.filter-group {
    display: flex;
    flex-direction: column;
    min-width: 200px;
}

button[type="submit"] {
    padding: 0.75rem 1.5rem;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    align-self: center;
}

button[type="submit"]:hover {
    background-color: #0056b3;
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
