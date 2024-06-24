<?php
include('../includes/Database.php');
include('../includes/functions.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];

    $db = Database::getInstance();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour')); // Ustawienie ważności tokenu na 1 godzinę

        $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?");
        $stmt->bind_param('sss', $token, $expiry, $email);

        if ($stmt->execute()) {
            $reset_link = "http://localhost/path/to/reset_password.php?token=$token";
            $subject = "Resetowanie hasła - The Financial Insight";
            $message = "Aby zresetować hasło, kliknij w poniższy link:\n\n$reset_link";
            $headers = "From: s30375@pjwstk.edu.pl";
            if (mail($email, $subject, $message, $headers)) {
                $_SESSION['success'] = "Wysłano email z linkiem resetującym hasło.";
            } else {
                $_SESSION['error'] = "Wystąpił problem podczas wysyłania emaila z linkiem resetującym.";
            }
        } else {
            $_SESSION['error'] = "Wystąpił problem podczas zapisywania tokenu resetującego.";
        }
    } else {
        $_SESSION['error'] = "Użytkownik o podanym adresie email nie istnieje.";
    }

    header('Location: ../WebPage/reset_password_form.php');
    exit();
}

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    header("Location: ../WebPage/reset_password.php?token=$token");
    exit();
}
?>
