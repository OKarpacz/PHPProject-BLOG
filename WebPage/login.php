<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

include('../includes/Database.php');
include('../includes/functions.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $db = Database::getInstance();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header('Location: index.php');
            exit();
        } else {
            $error = "Nieprawidłowe hasło.";
        }
    } else {
        $error = "Nieprawidłowy login.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Logowanie</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
<header class="header">
    <h1>Logowanie</h1>
</header>
<div class="container">
    <form method="POST" class="universal-form">
        <h2>Logowanie</h2>
        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <label for="username">Nazwa użytkownika:</label><br><br>
        <input type="text" name="username" id="username" required><br>
        <label for="password">Hasło:</label><br><br>
        <input type="password" name="password" id="password" required><br>
        <input type="submit" value="Login">
        <br><br>
        <a href ="reset_password.php" class="register-link">Zapomniałeś hasła?</a><br><br>
        <a href="register.php" class="register-link">Nie masz konta? Zarejestruj się tutaj</a>

    </form>
</div>
<footer>
    <p>&copy; The Financial Insight.</p>
</footer>
</body>
</html>
