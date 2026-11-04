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
        $message = "Please input your username and password";
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
            $message = "Invalid username or password";
        }
        catch (PDOException $e) {
            $message = "Welp i suck at coding :(";
        }
    }
}
?>