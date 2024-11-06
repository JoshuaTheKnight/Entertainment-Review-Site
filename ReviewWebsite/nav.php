<?php
// Start the session if it's not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar">
    <div class="nav-logo">
        <a href='index.php'> <img src="logo.png" alt="Logo"> </a>
    </div>
    <div class="nav-links">
        <a href="#" class="nav-link left">Create Review</a>
        <a href="#" class="nav-link middle">View All Reviews</a>
        <a href="viewaccount.php" class="nav-link right">Account</a>
    </div>
    <div class="nav-login">
        <?php if (isset($_SESSION['username'])): ?>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="loginsignup.php">Login</a>
        <?php endif; ?>
    </div>
</nav>

<style>
    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #333;
        padding: 1rem;
        color: white;
    }

    .nav-logo img {
        height: 40px;
    }

    .nav-login a {
        color: white;
        text-decoration: none;
        margin-right: 1rem;
    }

    .nav-links {
        display: flex;
        gap: 1.5rem;
    }

    .nav-link {
        color: white;
        text-decoration: none;
    }

    .nav-login span {
        margin-right: 1rem;
    }
</style>

