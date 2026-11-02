<?php require 'backend/login.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Pendu</title>
    <link rel="stylesheet" href="css/background.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="stars"></div>
    <?php require 'header.php'; ?>
    <div class="auth">
        <p class="auth-intro">Login to your account</p>
        <?php if ($message !== ''): ?>
            <p class="auth-error" role="alert"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        <form method="post" class="auth-form">
            <label for="username">Username :</label>
            <input id="username" name="username" maxlength="32" autocomplete="username" required value="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>">
            <label for="password">Password :</label>
            <input id="password" type="password" name="password" maxlength="200" autocomplete="current-password" required>
            <button type="submit">login</button>
        </form>
        <p class="auth-switch">Need an account ? <a href="register.php">Register</a></p>
        </div>
</body>
</html>