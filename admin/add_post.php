<?php
include('../includes/Database.php');
include('../includes/functions.php');
session_start();

// Sprawdzenie, czy użytkownik ma odpowiednią rolę (admin albo autor)
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'author')) {
    header('Location: ../WebPage/index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $author_id = $_SESSION['user_id'];
    $image = '';

    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../IMAGES/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);

        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image = basename($_FILES["image"]["name"]);
            } else {
                $error = "Błąd podczas przesyłania pliku.";
            }
        } else {
            $error = "Przesłany plik nie jest obrazem.";
        }
    }

    $db = Database::getInstance();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("INSERT INTO posts (title, content, author_id, image, publish_date) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param('ssis', $title, $content, $author_id, $image);

    if ($stmt->execute()) {
        header('Location: ../WebPage/index.php');
        exit();
    } else {
        $error = "Błąd dodawania posta.";
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Dodaj post</title>
    <link rel="stylesheet" type="text/css" href="../WebPage/styles.css">
</head>
<body>
<header class="header">
    <h1>Dodawanie postu</h1>
    <nav>
        <a href="../WebPage/index.php">Strona główna</a>
        <a href="../WebPage/logout.php">Wyloguj się</a>
    </nav>
</header>
<div class="container">
    <form method="POST" enctype="multipart/form-data">
        <h2>Dodaj nowy post</h2>
        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <label for="title">Tytuł:</label><br><br>
        <input type="text" name="title" id="title" required><br>
        <label for="content">Treść:</label><br><br>
        <textarea name="content" id="content" rows="10" required></textarea><br>
        <label for="image">Zdjęcie:</label><br><br>
        <input type="file" name="image" id="image"><br><br>
        <br><br>
        <input type="submit" value="Dodaj post">
    </form>
</div>
<footer>
    <p>&copy; The Financial Insight.</p>
</footer>
</body>
</html>
