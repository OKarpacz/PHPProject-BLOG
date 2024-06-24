<?php
include('../includes/Database.php');
include('../includes/functions.php');
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'author')) {
    header('Location: ../index.php');
    exit();
}

$db = Database::getInstance();
$conn = $db->getConnection();

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $post = $stmt->get_result()->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $image = $post['image'];

    if ($_FILES['image']['name']) {
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        $image = basename($_FILES["image"]["name"]);
    }

    $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ?, image = ? WHERE id = ?");
    $stmt->bind_param('sssi', $title, $content, $image, $id);

    if ($stmt->execute()) {
        header('Location: ../WebPage/index.php');
        exit();
    } else {
        echo "Błąd: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edycja posta</title>
    <link rel="stylesheet" type="text/css" href="../WebPage/styles.css">
</head>
<body>
<header class="header">
    <h1>Edycja posta</h1>
    <nav>
        <a href="../admin/dashboard.php">Strona główna</a>
        <a href="../admin/add_post.php">Dodaj post</a>
        <a href="../WebPage/logout.php">Wyloguj się</a>
    </nav>
</header>
<div class="container">
    <form method="POST" enctype="multipart/form-data">
        <h2>Edytuj post</h2>
        <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
        <label for="title">Tytuł:</label><br><br>
        <input type="text" name="title" id="title" value="<?php echo $post['title']; ?>" required><br>
        <label for="content">Treść:</label><br><br>
        <textarea name="content" style="min-height: 50px" id="content" required><?php echo $post['content']; ?></textarea><br>
        <label for="image">Zdjęcie:</label><br><br>
        <input type="file" name="image" id="image"><br><br>
        <input type="submit" value="Aktualizuj post">
    </form>
</div>
<footer>
    <p>&copy; The Financial Insight.</p>
</footer>
</body>
</html>