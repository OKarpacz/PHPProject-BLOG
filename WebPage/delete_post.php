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

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("SELECT image FROM posts WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();

    if ($post) {
        $stmt = $conn->prepare("DELETE FROM comments WHERE post_id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();

        $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            if ($post['image']) {
                $file_path = '../IMAGES/' . $post['image'];
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
            header('Location: ../WebPage/index.php');
            exit();
        } else {
            echo "Błąd: " . $stmt->error;
        }
    } else {
        echo "Nie znaleziono posta.";
    }
} else {
    echo "Nieprawidłowe ID.";
}
?>

