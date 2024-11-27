<?php
// Start session and include necessary files
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'connection.php';

// Check if user is logged in and if the deletion has been verified
if (!isset($_SESSION['user_id']) || !isset($_SESSION['delete_verified']) || $_SESSION['delete_verified'] !== true) {
    header("Location: editaccount.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['confirm_delete']) && $_POST['confirm_delete'] === 'yes') {
        // Delete the user from the database
        $sql = "DELETE FROM Users WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);

        if ($stmt->execute()) {
            // Destroy the session and redirect to the homepage after deletion
            session_destroy();
            header("Location: index.php");
            exit();
        } else {
            $feedback = "Error deleting account. Please try again.";
        }
        $stmt->close();
    } else {
        // Redirect back to edit account page if 'No' is clicked
        header("Location: editaccount.php");
        exit();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Account Deletion</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f8f8;
        }

        .confirm-box {
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }

        .confirm-box h2 {
            color: #f44336;
            margin-bottom: 1rem;
        }

        .confirm-box p {
            margin-bottom: 2rem;
            font-size: 1rem;
            color: #555;
        }

        .confirm-buttons {
            display: flex;
            justify-content: space-around;
            gap: 1rem;
        }

        button {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
        }

        .yes-button {
            background-color: #f44336;
            color: white;
        }

        .no-button {
            background-color: #ccc;
            color: white;
        }

        .yes-button:hover {
            background-color: #d32f2f;
        }

        .no-button:hover {
            background-color: #999;
        }
    </style>
</head>
<body>
    <div class="confirm-box">
        <h2>Are You Sure?</h2>
        <p>This action cannot be undone. If you proceed, your account will be permanently deleted.</p>
        <?php if (isset($feedback)): ?>
            <div class="feedback-message"><?php echo htmlspecialchars($feedback); ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="confirm-buttons">
                <button type="submit" name="confirm_delete" value="yes" class="yes-button">Yes, Delete</button>
                <button type="submit" name="confirm_delete" value="no" class="no-button">No, Go Back</button>
            </div>
        </form>
    </div>
</body>
</html>
