<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = 'Podano niepoprawny adres email.';
        header("Location: contact_me.php");
        exit();
    }

    $to = 's30375@pjwstk.edu.pl';
    $subject = 'Nowa wiadomość ze strony kontaktowej';
    $message_body = "Od: $name\n\nEmail: $email\n\nWiadomość:\n$message";
    $headers = "From: $email\n";
    $headers .= "Reply-To: $email\n";

    if (mail($to, $subject, $message_body, $headers)) {
        $_SESSION['success_message'] = 'Twoja wiadomość została wysłana.';
        header("Location: thankyou_page.php");
        exit();
    } else {
        $_SESSION['error_message'] = 'Wystąpił problem podczas wysyłania wiadomości. Spróbuj ponownie później.';
        header("Location: contact_me.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontakt</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
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
</header>

<div class="container">
    <h2>Formularz kontaktowy</h2>
    <?php if (isset($_SESSION['success_message'])): ?>
        <p class="success"><?php echo $_SESSION['success_message']; ?></p>
        <?php unset($_SESSION['success_message']); ?>
    <?php elseif (isset($_SESSION['error_message'])): ?>
        <p class="error"><?php echo $_SESSION['error_message']; ?></p>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <form action="contact_me.php" method="POST">
        <label for="name">Imię i nazwisko:</label><br>
        <input type="text" id="name" name="name" required><br><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="message">Wiadomość:</label><br>
        <textarea id="message" name="message" rows="4" required></textarea><br><br>

        <input type="submit" value="Wyślij">
    </form>
</div>
<footer>
    <p>&copy; The Financial Insight.</p>
</footer>
</body>
</html>
