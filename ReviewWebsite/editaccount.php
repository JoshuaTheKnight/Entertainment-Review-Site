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

$user_id = $_SESSION['user_id'];
$feedback = [
    'username' => '',
    'email' => '',
    'password' => '',
    'delete' => ''
];

// Process Username Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_username'])) {
    $current_username = $_POST['current_username'];
    $new_username = $_POST['new_username'];

    // Fetch current username from the database
    $sql = "SELECT username FROM Users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($db_username);
    $stmt->fetch();
    $stmt->close();

    // Check if the current username matches the one in the database
    if ($current_username !== $db_username) {
        $feedback['username'] = "Current username does not match our records.";
    } else {
        // Check if the new username is already taken
        $sql_check = "SELECT user_id FROM Users WHERE username = ?";
        $stmt = $conn->prepare($sql_check);
        $stmt->bind_param("s", $new_username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $feedback['username'] = "Username is already taken. Please choose a different one.";
        } else {
            // Update the username
            $stmt->close();
            $sql_update = "UPDATE Users SET username = ? WHERE user_id = ?";
            $stmt = $conn->prepare($sql_update);
            $stmt->bind_param("si", $new_username, $user_id);
            if ($stmt->execute()) {
                // Update the session variable to reflect the new username
                $_SESSION['username'] = $new_username;
                $feedback['username'] = "Username successfully updated.";
            } else {
                $feedback['username'] = "Error updating username. Please try again.";
            }
        }
        $stmt->close();
    }
}

// Process Email Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_email'])) {
    $current_email = $_POST['current_email'];
    $new_email = $_POST['new_email'];

    // Fetch current email from the database
    $sql = "SELECT email FROM Users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($db_email);
    $stmt->fetch();
    $stmt->close();

    // Check if the current email matches the one in the database
    if ($current_email !== $db_email) {
        $feedback['email'] = "Current email does not match our records.";
    } else {
        // Check if the new email is already taken
        $sql_check = "SELECT user_id FROM Users WHERE email = ?";
        $stmt = $conn->prepare($sql_check);
        $stmt->bind_param("s", $new_email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $feedback['email'] = "Email is already taken. Please use a different one.";
        } else {
            // Update the email
            $stmt->close();
            $sql_update = "UPDATE Users SET email = ? WHERE user_id = ?";
            $stmt = $conn->prepare($sql_update);
            $stmt->bind_param("si", $new_email, $user_id);
            if ($stmt->execute()) {
                $feedback['email'] = "Email successfully updated.";
            } else {
                $feedback['email'] = "Error updating email. Please try again.";
            }
        }
        $stmt->close();
    }
}

// Process Password Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];

    // Fetch current password hash from the database
    $sql = "SELECT user_password FROM Users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($db_password_hash);
    $stmt->fetch();
    $stmt->close();

    // Verify current password
    if (!password_verify($current_password, $db_password_hash)) {
        $feedback['password'] = "Current password is incorrect.";
    } else {
        // Hash the new password
        $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

        // Update the password
        $sql_update = "UPDATE Users SET user_password = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("si", $new_password_hash, $user_id);
        if ($stmt->execute()) {
            $feedback['password'] = "Password successfully updated.";
        } else {
            $feedback['password'] = "Error updating password. Please try again.";
        }
        $stmt->close();
    }
}
// Process Delete Account
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_account'])) {
    $delete_username = $_POST['delete_username'];
    $delete_email = $_POST['delete_email'];
    $delete_password = $_POST['delete_password'];

    // Fetch current credentials from the database
    $sql = "SELECT username, email, user_password FROM Users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($db_username, $db_email, $db_password_hash);
    $stmt->fetch();
    $stmt->close();

    // Validate credentials
    if ($delete_username !== $db_username || $delete_email !== $db_email || !password_verify($delete_password, $db_password_hash)) {
        $feedback['delete'] = "Incorrect credentials. Please verify your username, email, and password.";
    } else {
        // If credentials are correct, proceed to confirm deletion
        $_SESSION['delete_verified'] = true; // Set session flag to confirm verification
        header("Location: confirmdeletion.php");
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
    <title>Edit Account</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: 1fr 1fr;
            gap: 2rem;
            width: 90%;
            margin: 2rem auto;
            height: 600px;
        }

        .block {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        h2 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        input[type="text"], input[type="email"], input[type="password"] {
            padding: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            padding: 0.75rem 1.5rem;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .feedback-message {
            color: #f44336;
            margin-bottom: 1rem;
        }

        .success-message {
            color: #4caf50;
        }

        .delete-block {
            border: 2px solid #f44336;
        }

        .delete-block button {
            background-color: #f44336;
        }

        .delete-block button:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Edit Username Block -->
        <div class="block">
            <h2>Edit Username</h2>
            <?php if ($feedback['username']): ?>
                <div class="feedback-message <?php echo strpos($feedback['username'], 'successfully') !== false ? 'success-message' : ''; ?>">
                    <?php echo htmlspecialchars($feedback['username']); ?>
                </div>
            <?php endif; ?>
            <form method="POST">
                <label for="current_username">Current Username:</label>
                <input type="text" name="current_username" id="current_username" required>
                
                <label for="new_username">New Username:</label>
                <input type="text" name="new_username" id="new_username" required>
                
                <button type="submit" name="update_username">Update Username</button>
            </form>
        </div>

        <!-- Edit Email Block -->
        <div class="block">
            <h2>Edit Email</h2>
            <?php if ($feedback['email']): ?>
                <div class="feedback-message <?php echo strpos($feedback['email'], 'successfully') !== false ? 'success-message' : ''; ?>">
                    <?php echo htmlspecialchars($feedback['email']); ?>
                </div>
            <?php endif; ?>
            <form method="POST">
                <label for="current_email">Current Email:</label>
                <input type="email" name="current_email" id="current_email" required>
                
                <label for="new_email">New Email:</label>
                <input type="email" name="new_email" id="new_email" required>
                
                <button type="submit" name="update_email">Update Email</button>
            </form>
        </div>

        <!-- Edit Password Block -->
        <div class="block">
            <h2>Edit Password</h2>
            <?php if ($feedback['password']): ?>
                <div class="feedback-message <?php echo strpos($feedback['password'], 'successfully') !== false ? 'success-message' : ''; ?>">
                    <?php echo htmlspecialchars($feedback['password']); ?>
                </div>
            <?php endif; ?>
            <form method="POST">
                <label for="current_password">Current Password:</label>
                <input type="password" name="current_password" id="current_password" required>
                
                <label for="new_password">New Password:</label>
                <input type="password" name="new_password" id="new_password" required>
                
                <button type="submit" name="update_password">Update Password</button>
            </form>
        </div>

        <!-- Delete Account Block -->
        <div class="block delete-block">
            <h2>Delete Account</h2>
            <?php if ($feedback['delete']): ?>
                <div class="feedback-message">
                    <?php echo htmlspecialchars($feedback['delete']); ?>
                </div>
            <?php endif; ?>
            <form method="POST">
                <label for="delete_username">Username:</label>
                <input type="text" name="delete_username" id="delete_username" required>
                
                <label for="delete_email">Email:</label>
                <input type="email" name="delete_email" id="delete_email" required>
                
                <label for="delete_password">Password:</label>
                <input type="password" name="delete_password" id="delete_password" required>
                
                <button type="submit" name="delete_account">Delete Account</button>
            </form>
        </div>
    </div>
</body>
</html>
