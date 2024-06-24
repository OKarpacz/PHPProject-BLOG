<?php
include('../includes/Database.php');
include('../includes/functions.php');
session_start();

// Sprawdź, czy token został przekazany w URL
if (!isset($_GET['token'])) {
    die("Token nie został przekazany w URL.");
}

$token = $_GET['token'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $error_message = "Hasła nie są identyczne.";
    } else {
        $new_password_hashed = password_hash($new_password, PASSWORD_BCRYPT);

        $db = Database::getInstance();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = ? AND reset_token_expiry > NOW()");
        $stmt->bind_param('ss', $new_password_hashed, $token);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Hasło zostało zresetowane.";
            header('Location: ../public/index.php');
            exit();
        } else {
            $error_message = "Wystąpił błąd podczas resetowania hasła: " . $stmt->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Resetowanie hasła</title>
    <link rel="stylesheet" href="../WebPage/styles.css">
</head>
<body>
<div class="container">
    <h2>Resetowanie hasła</h2>
    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
    <?php endif; ?>
    <form method="POST" class="universal-form">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>" required>
        <label for="new_password">Nowe hasło:</label>
        <input type="password" id="new_password" name="new_password" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$" title="Hasło musi mieć co najmniej 8 znaków, zawierać przynajmniej jedną dużą literę, jedną małą literę, jedną cyfrę oraz jeden znak specjalny." required><br>
        <label for="confirm_password">Potwierdź hasło:</label>
        <input type="password" id="confirm_password" name="confirm_password" required><br><br>
        <a>Hasło musi mieć co najmniej 8 znaków, zawierać przynajmniej jedną dużą literę, jedną małą literę, jedną cyfrę oraz jeden znak specjalny.</a><br><br>
        <input type="submit" value="Zresetuj hasło">
    </form>
</div>
</body>
</html>
