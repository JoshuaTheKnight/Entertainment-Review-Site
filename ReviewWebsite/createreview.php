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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the submitted data
    $entertainment_id = $_POST['entertainment_id'];
    $rating = $_POST['rating'];
    $review_text = $_POST['review_text'];

    // Insert the review into the database using a prepared statement
    $stmt = $conn->prepare("INSERT INTO reviews (entertainment_id, user_id, rating, review_text, review_date) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("iiis", $entertainment_id, $user_id, $rating, $review_text);

    // $user_id is retrieved from the session (since users need to be logged in)
    $user_id = $_SESSION['user_id']; // Example

    // Execute the statement
    if ($stmt->execute()) {
        // Get the ID of the last inserted review
        $review_id = $conn->insert_id;
        header("Location: review.php?review_id=" . $review_id);
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit a Review</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<main class="review-form-container">
    <form action="" method="POST">
        <label for="entertainment">Select Entertainment:</label>
        <select id="entertainment" name="entertainment_id" required>
            <option value="">-- Select Entertainment --</option>
            <?php
                // Prepared statement to get all entertainment titles
                $stmt = $conn->prepare("SELECT entertainment_id, title FROM entertainments");
                $stmt->execute();
                $stmt->bind_result($entertainmentId, $entertainmentTitle);

                while ($stmt->fetch()) {
                    echo "<option value='" . htmlspecialchars($entertainmentId) . "'>" . htmlspecialchars($entertainmentTitle) . "</option>";
                }

                $stmt->close();
            ?>
        </select>

        <br><br>

        <label for="rating">Rating (1-10):</label>
        <input type="number" id="rating" name="rating" min="1" max="10" required>

        <br><br>

        <label for="review">Review:</label>
        <textarea id="review" name="review_text" required></textarea>

        <br><br>

        <button type="submit">Submit Review</button>
    </form>
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

.review-form-container {
    max-width: 600px;
    margin: 0 auto;
    padding: 2rem;
}

form {
    display: flex;
    flex-direction: column;
}

label {
    margin-bottom: 0.5rem;
    font-weight: bold;
}

select, input, textarea {
    margin-bottom: 1rem;
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    width: 100%;
    box-sizing: border-box;
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