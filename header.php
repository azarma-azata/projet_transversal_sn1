<header class="site-header">
    <div class="container">
        <a class="logo" href="index.php">± Plus ou Moins<span>.</span></a>
        <nav class="nav">
            <a class="play_btn" href="jeux.php">Jouer</a>
            <?php if (isset($_SESSION['id'], $_SESSION['name'])): ?>
                <span><?php echo htmlspecialchars($_SESSION['name'], ENT_QUOTES, 'UTF-8'); ?></span>
                <a class="log_btn" href="Deconnexion.php">Se déconnecter</a>
            <?php else: ?>
                <a class="log_btn" href="Connexion.php">Connexion</a>
                <a href="Register.php">Créer un compte</a>
            <?php endif; ?>
        </nav>
    </div>
</header>