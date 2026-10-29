<?php require 'backend/register.php'; ?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Compte utilisateur</title>
    </head>
    <body>
        <?php if (isset($_SESSION['username'])): ?>
            <p>Bonjour, <?= htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') ?> !</p>
            <form method="post"><button name="logout">Se déconnecter</button></form>
        <?php else: ?>
            <?php if ($message !== ''): ?><p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            <form method="post">
                <label>Username : <input name="username" required></label><br>
                <label>email : <input name="email" required></label><br>
                <label>Password : <input type="password" name="password" required></label><br>
                <button name="action" value="register">S'inscrire</button>
            </form>
        <?php endif; ?>
    </body>
</html>