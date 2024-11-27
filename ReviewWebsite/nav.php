<?php
// Start the session if it's not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar">
    <div class="nav-logo">
        <a href='index.php'> <img src="Images/logo.webp" alt="Logo"> </a>
    </div>
    <div class="nav-links">
        <a href="createreview.php" class="nav-link left">Create Review</a>
        <a href="allreviews.php" class="nav-link middle">View All Reviews</a>
        <a href="viewaccount.php" class="nav-link right">Account</a>
        
        <?php
        // Add 'Add Entertainment' link if the user role is 1 (owner) or 2 (admin)
        if (isset($_SESSION['user_role_id']) && ($_SESSION['user_role_id'] == 1 || $_SESSION['user_role_id'] == 2)) {
            echo '<a href="addentertainment.php" class="nav-link">Add Entertainment</a>';
        }
        ?>
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
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
    }

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


