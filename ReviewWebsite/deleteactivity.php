<?php
// Start session and include necessary files
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: loginsignup.php");
    exit();
}

$type = isset($_GET['type']) ? $_GET['type'] : null;
$id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$type || !$id) {
    header("Location: viewaccount.php");
    exit();
}

// Handle the form submission for deletion confirmation
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['confirm_delete']) && $_POST['confirm_delete'] === 'yes') {
        if ($type === 'review') {
            $sql = "DELETE FROM Reviews WHERE review_id = ? AND user_id = ?";
        } elseif ($type === 'comment') {
            $sql = "DELETE FROM Comments WHERE comment_id = ? AND user_id = ?";
        }

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $id, $_SESSION['user_id']);

        if ($stmt->execute()) {
            header("Location: viewaccount.php");
            exit();
        } else {
            $feedback = "Error deleting item. Please try again.";
        }
        $stmt->close();
    } else {
        // Redirect back to view account if 'No' is clicked
        header("Location: viewaccount.php");
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
    <title>Confirm Deletion</title>
    <style>
        body {
            font-family: Arial, sans-serif;
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

        .confirm-buttons {
            display: flex;
            justify-content: space-around;
            gap: 1rem;
            margin-top: 2rem;
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
        <p>This action cannot be undone. If you proceed, the selected <?php echo htmlspecialchars($type); ?> will be permanently deleted.</p>
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
