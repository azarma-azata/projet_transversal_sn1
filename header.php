<?php require_once 'backend/config.php';?>
<header class="site-header">
    <link rel="stylesheet" href="css/header.css">
    <div class="container">
        <nav class="menu-container">
        <input type="checkbox" aria-label="Toggle menu" />
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
                <form method="post"><button class="btn" name="logout">Logout</button></form>
            </li>
            <?php else: ?>
            <li>
                <a href="./register.php">
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