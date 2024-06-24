<?php
include('../includes/Database.php');
include('../includes/functions.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post_id = $_POST['post_id'];
    $content = $_POST['content'];

    if (strlen($content) > 200) {
        $_SESSION['error'] = "Komentarz jest za długi, maksymalna długość to 200 znaków.";
        header("Location: read_more.php? id=$post_id");
        exit();
    }

    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    $db = Database::getInstance();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, content, publish_date) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param('iis', $post_id, $user_id, $content);

    if ($stmt->execute()) {
        header("Location: read_more.php?id=$post_id");
        exit();
    } else {
        echo "Błąd: " . $stmt->error;
    }
}
?>