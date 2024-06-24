<?php
include('../includes/Database.php');
include('../includes/functions.php');
session_start();

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'author')) {
    header('Location: ../WebPage/index.php');
    exit();
}

$db = Database::getInstance();
$conn = $db->getConnection();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Panel Administratora</title>
    <link rel="stylesheet" type="text/css" href="../WebPage/styles.css">
</head>
<body>
<header class="header-dash">
    <h1>Administracyjny Panel Sterowania</h1>
    <nav>
        <a href="../WebPage/index.php">Strona główna</a>
        <br><br>
        <a href="add_post.php">Dodaj Post</a>
        <br><br>
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href = "../WebPage/download_users.php">Pobierz Użytkowników</a>
        <?php endif; ?>
        <br><br>
        <a href="../WebPage/logout.php">Wyloguj się</a>
    </nav>
</header>
<footer>
    <p>&copy; The Financial Insight.</p>
</footer>
</body>
</html>