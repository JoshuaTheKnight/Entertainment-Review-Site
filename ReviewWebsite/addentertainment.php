<?php
include 'nav.php'; // Include the navigation bar
include 'connection.php'; // Include your database connection

// Start the session if it's not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and has right role
if (!isset($_SESSION['user_role_id']) || ($_SESSION['user_role_id'] != 1 && $_SESSION['user_role_id'] != 2)) {
    echo "You do not have permission to access this page.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form inputs
    $title = $_POST['title'];
    $release_date = $_POST['release_date'];
    $description = $_POST['description'];
    $entertainment_type_id = $_POST['entertainment_type_id'];
    $genres = isset($_POST['genres']) ? $_POST['genres'] : [];

    // Insert the new entertainment into the entertainments table
    $stmt = $conn->prepare("INSERT INTO entertainments (title, release_date, entertainment_description, entertainment_type_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $title, $release_date, $description, $entertainment_type_id);

    if ($stmt->execute()) {
        $entertainment_id = $conn->insert_id;

        // Insert genres into the entertainment_genres table
        foreach ($genres as $genre_id) {
            $stmt_genre = $conn->prepare("INSERT INTO entertainment_genres (entertainment_id, genre_id) VALUES (?, ?)");
            $stmt_genre->bind_param("ii", $entertainment_id, $genre_id);
            $stmt_genre->execute();
            $stmt_genre->close();
        }

        echo "Entertainment and genres added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Entertainment</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<main class="add-entertainment-container">
    <h2>Add New Entertainment</h2>
    <form action="addentertainment.php" method="POST">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>

        <br><br>

        <label for="release_date">Release Date:</label>
        <input type="date" id="release_date" name="release_date" required>

        <br><br>

        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea>

        <br><br>

        <label for="entertainment_type_id">Type:</label>
        <select id="entertainment_type_id" name="entertainment_type_id" required>
            <option value="">-- Select Type --</option>
            <?php
                // Fetch all entertainment types from the database
                $stmt = $conn->prepare("SELECT entertainment_type_id, entertainment_name FROM entertainment_types");
                $stmt->execute();
                $stmt->bind_result($typeId, $typeName);

                while ($stmt->fetch()) {
                    echo "<option value='" . htmlspecialchars($typeId) . "'>" . htmlspecialchars($typeName) . "</option>";
                }

                $stmt->close();
            ?>
        </select>

        <br><br>

        <label for="genres">Genres:</label>
        <select id="genres" name="genres[]" multiple required>
            <?php
                // Fetch all genres from the database
                $stmt = $conn->prepare("SELECT genre_id, genre_name FROM genres");
                $stmt->execute();
                $stmt->bind_result($genreId, $genreName);

                while ($stmt->fetch()) {
                    echo "<option value='" . htmlspecialchars($genreId) . "'>" . htmlspecialchars($genreName) . "</option>";
                }

                $stmt->close();
            ?>
        </select>

        <br><br>

        <button type="submit">Add Entertainment</button>
    </form>
</main>

</body>
</html>

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
    }

    .add-entertainment-container {
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

    input, textarea, select {
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

