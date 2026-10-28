<?php require_once 'backend/config.php';?>
<header class="site-header">
    <link rel="stylesheet" href="css/header.css">
    <div class="container">
        <nav class="menu-container">
        <!-- burger menu -->
        <input type="checkbox" aria-label="Toggle menu" />
        <!-- menu items -->
        <div class="menu">
            <ul>
            <li>
                <a href="./index.php">
                Home
                </a>
            </li>
            <li>
                <a href="">
                Stats
                </a>
            </li>
            <li>
                <a href="./game.php">
                jouer
                </a>
            </li>
            </ul>
            <ul>
            <?php if (isset($_SESSION['username'], $_SESSION['username'])): ?>
            <li>
                <a><?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?></a>
            </li>
            <li>
                <a href="">
                logout
                </a>
            </li>
            <?php else: ?>
            <li>
                <a href="">
                Sign-up
                </a>
            </li>
            <li>
                <a href="">
                Login
                </a>
            </li>
            <?php endif; ?>
            </ul>
        </div>
        </nav>
    </div>
</header>