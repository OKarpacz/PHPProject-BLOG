<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dziękujemy za kontakt</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
    <h1>The Financial Insight</h1>
    <nav>
        <a href="index.php">Strona główna</a>
        <?php if (isset($_SESSION['username'])): ?>
            <a href="logout.php">Wyloguj się</a>
            <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'author'): ?>
                <a href="../admin/dashboard.php">Panel Administratora</a>
            <?php endif; ?>
        <?php else: ?>
            <a href="login.php">Zaloguj się</a>
            <a href="register.php">Zarejestruj się</a>
        <?php endif; ?>
    </nav>
</header>

<div class="container">
    <h2>Dziękujemy za kontakt!</h2>
    <p>Twoja wiadomość została pomyślnie wysłana. Skontaktujemy się z Tobą wkrótce!</p>
</div>

<footer>
    <p>&copy; The Financial Insight.</p>
</footer>

</body>
</html>
