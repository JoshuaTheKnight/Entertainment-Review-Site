<?php 
session_start();
include 'nav.php';
include 'connection.php'; 

$error_login = '';
$error_signup = '';
$success_signup = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login_form'])) {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        echo $password;
        
        if (empty($username) || empty($password)) {
            $error_login = "Please enter both username and password.";
        } else {
            // Verify user
            $sql = "SELECT * FROM Users WHERE username = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
    
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                echo "Stored Hashed Password: " . $user['user_password']; // Debug statement
    
                // Check password using correct column name for hashed password
                if (password_verify($password, $user['user_password'])) {
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['user_role_id'] = $user['user_role_id'];
                    
                    header("Location: index.php"); // Redirect to a dashboard page
                    exit();
                } else {
                    $error_login = "Invalid password.";
                }
            } else {
                $error_login = "User not found.";
            }
            $stmt->close();
        }   
    } elseif (isset($_POST['signup_form'])) {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $dateJoined = date("Y-m-d");
        $role = 3;

        if (empty($username) || empty($email) || empty($password)) {
            $error_signup = "All fields are required!";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_signup = "Invalid email format.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO Users (username, email, user_password, date_joined, user_role_id) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $username, $email, $hashed_password, $dateJoined, $role);
    
            if ($stmt->execute()) {
                $success_signup = "Account created successfully! You can now log in.";
            } else {
                $error_signup = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login and Sign-Up Page</title>
    <style>
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            display: flex;
            justify-content: space-between;
            width: 80%;
            margin: auto;
        }
        .form-box {
            width: 45%;
            padding: 20px;
            border: 1px solid #ccc;
        }
        .message {
            color: white;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .error {
            background-color: #ff4c4c;
        }
        .success {
            background-color: #4CAF50;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Login Section -->
        <div class="form-box">
            <h2>Login</h2>
            <?php if ($error_login): ?>
                <div class="message error"><?php echo htmlspecialchars($error_login); ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <input type="hidden" name="login_form" value="1">
                <label for="username">Username:</label><br>
                <input type="text" id="username" name="username" required><br><br>
                <label for="password">Password:</label><br>
                <input type="password" id="password" name="password" required><br><br>
                <button type="submit">Login</button>
            </form>
        </div>

        <!-- Sign-Up Section -->
        <div class="form-box">
            <h2>Sign Up</h2>
            <?php if ($error_signup): ?>
                <div class="message error"><?php echo htmlspecialchars($error_signup); ?></div>
            <?php elseif ($success_signup): ?>
                <div class="message success"><?php echo htmlspecialchars($success_signup); ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <input type="hidden" name="signup_form" value="1">
                <label for="username">Username:</label><br>
                <input type="text" id="username" name="username" required><br><br>
                <label for="email">Email:</label><br>
                <input type="email" id="email" name="email" required><br><br>
                <label for="password">Password:</label><br>
                <input type="password" id="password" name="password" required><br><br>
                <button type="submit">Sign Up</button>
            </form>
        </div>
    </div>
</body>
</html>
