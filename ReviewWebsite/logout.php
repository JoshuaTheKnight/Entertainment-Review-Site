<?php
session_start(); // Start the session

// Unset all session variables
$_SESSION = array();

// If there's a session cookie, destroy it
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to the login page or homepage
header("Location: loginsignup.php");
exit;
?>
