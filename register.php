<?php

require_once 'backend/config.php';

function passwd_strenght(string $password): bool
{
    $length = mb_strlen($password);
    if ($length > 200) {
        return false;
    }

    $types = (int) preg_match('/\p{Ll}/u', $password)
           + (int) preg_match('/\p{Lu}/u', $password)
           + (int) preg_match('/[0-9]/', $password)
           + (int) preg_match('/[^\p{L}0-9]/u', $password);

    return ($length >= 12 && $types === 4) || ($length >= 14 && $types >= 3);
}


$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (isset($_POST['logout'])) {
        session_unset();
        session_destroy();
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '' || $username === '' ) {
        $message = "Veuillez renseigner un email valide, un nom d'utilisateur et un mot de passe.";
    } elseif (!passwd_strenght($password)){
        $message = "Mot de passe non sécurisé!";
    } else {
        try {
            $sth = $dbh->prepare('INSERT INTO `users` (`username`, `email`, `password_hash`) VALUES (:username, :email, :password)');
            $sth->execute([
                'username' => $username,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
            ]);
            $_SESSION['username'] = $username;
        } catch (Exception $e) {
            $message = "Une erreur est survenue :( " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<meta charset="UTF-8">
<title>Compte utilisateur</title>
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
</html>