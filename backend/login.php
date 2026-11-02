<?php

require_once 'config.php';

if (isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

$message = '';
$username = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $message = "Veuillez renseigner votre nom d'utilisateur et votre mot de passe.";
    }
    else {
        try {
            $sth = $dbh->prepare('SELECT `id`, `username`, `password_hash` FROM `users` WHERE `username` = :username LIMIT 1');
            $sth->execute(['username' => $username]);
            $user = $sth->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = (int) $user['id'];
                $_SESSION['username'] = $user['username'];
                header('Location: index.php');
                exit;
            }
            $message = "Nom d'utilisateur ou mot de passe incorrect.";
        }
        catch (PDOException $e) {
            $message = "Une erreur est survenue, réessaie plus tard.";
        }
    }
}
?>