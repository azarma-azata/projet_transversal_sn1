<?php require 'backend/register.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Pendu</title>
    <link rel="stylesheet" href="css/background.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="stars"></div>
    <?php require 'header.php'; ?>
    <main class="auth">
        <p class="auth-intro">Create an account.</p>
        <?php if ($message !== ''): ?>
            <p class="auth-error" role="alert"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        <form method="post" class="auth-form">
            <label for="username">Username :</label>
            <input id="username" name="username" maxlength="32" autocomplete="username" required value="<?= htmlspecialchars($username ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <label for="email">Email :</label>
            <input id="email" type="email" name="email" maxlength="255" autocomplete="email" required value="<?= htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <label for="password">Password :</label>
            <input id="password" type="password" name="password" minlength="12" maxlength="200" autocomplete="new-password" aria-describedby="password-help" required>
            <p id="password-help" class="auth-help">12 character minimum with uppercase, lowercase, numbers and a spacial character or 14 character with one of each.</p>
            <button type="submit">S'inscrire</button>
        </form>
        <p class="auth-switch">Already have an account? <a href="login.php">login</a></p>
    </main>
</body>
</html>