<?php
session_start();
include '../includes/Database.php';
include '../includes/functions.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $user = loginUser($username, $password);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        header("Location: index.php");
        exit();
    } else {
        $message = "Nieprawidłowe dane logowania. Spróbuj ponownie.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Logowanie</h2>
    <?php if (!empty($message)): ?>
        <p class="error-message"><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="POST" action="login.php">
        <label for="username">Nazwa użytkownika:</label><br>
        <input type="text" id="username" name="username" required><br><br>
        <label for="password">Hasło:</label><br>
        <input type="password" id="password" name="password" required><br><br>
        <button type="submit">Zaloguj się</button>
    </form>
    <p>Nie masz konta? <a href="register.php">Zarejestruj się</a></p>
</div>
</body>
</html>
