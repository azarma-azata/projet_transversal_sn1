<?php

require_once 'config.php';

//auto redirect if user is already logged in
if (isset($_SESSION['username'], $_SESSION['username'])) {
    header('Location: index.php');
}

//check if the password match CNIL requirements 
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

//main logic activated by receiving the form
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (isset($_POST['logout'])) {
        session_unset();
        session_destroy();
    } 
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '' || $username === '' ) {
        $message = "Please input an username, email and strong password.";
    } 
    elseif (!passwd_strenght($password)){
        $message = "Weak password! please follow instruction down below.";
    } 
    else {
        try {
            $sth = $dbh->prepare('INSERT INTO `users` (`username`, `email`, `password_hash`) VALUES (:username, :email, :password)');
            $sth->execute([
                'username' => $username,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
            ]);
            $_SESSION['username'] = $username;
            header('Location: index.php');
        } 
        catch (PDOException $e) {
            $message = ($e->errorInfo[1] ?? null) === 1062
                ? "This username is already taken."
                : "I have no idea what happen :(";
        }

    }
}
?>