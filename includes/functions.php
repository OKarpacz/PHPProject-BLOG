<?php

include_once 'Database.php';

// Funkcje aplikacji

// Funkcja do logowania użytkownika
function loginUser($username, $password) {
    $db = Database::getInstance()->getConnection();
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        die('Nie można przygotować zapytania: ' . $db->error);
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        return true;
    }
    return false;
}

// Funkcja do rejestracji nowego użytkownika
function registerUser($username, $email, $password) {
    $db = Database::getInstance()->getConnection();
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    $role = 'user';
    $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        die('Nie można przygotować zapytania: ' . $db->error);
    }
    $stmt->bind_param("ssss", $username, $email, $passwordHash, $role);
    return $stmt->execute();
}

// Funkcja sprawdzająca, czy użytkownik jest zalogowany
function isLoggedIn() {
    return isset($_SESSION['username']);
}

// Funkcja do wylogowania użytkownika
function logout() {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Funkcja do dodawania nowego posta
function addPost($title, $content, $authorId, $publishDate, $image = null) {
    $db = Database::getInstance()->getConnection();
    $sql = "INSERT INTO posts (title, content, author_id, publish_date, image) VALUES (?, ?, ?, ?, ?)";
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        die('Nie można przygotować zapytania: ' . $db->error);
    }
    $stmt->bind_param("ssiss", $title, $content, $authorId, $publishDate, $image);
    return $stmt->execute();
}

// Funkcja do pobierania wszystkich postów
function getPosts($dateFilter = null) {
    $db = Database::getInstance()->getConnection();
    if ($dateFilter) {
        $stmt = $db->prepare("SELECT * FROM posts WHERE DATE(publish_date) = ? ORDER BY publish_date DESC");
        $stmt->bind_param('s', $dateFilter);
    } else {
        $stmt = $db->prepare("SELECT * FROM posts ORDER BY publish_date DESC");
    }
    $stmt->execute();
    $result = $stmt->get_result();
    return $result;
}

// Funkcja do dodawania komentarza do posta
function addComment($postId, $authorId, $content) {
    $db = Database::getInstance()->getConnection();
    $sql = "INSERT INTO comments (post_id, author_id, content) VALUES (?, ?, ?)";
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        die('Nie można przygotować zapytania: ' . $db->error);
    }
    $stmt->bind_param("iis", $postId, $authorId, $content);
    return $stmt->execute();
}

// Funkcja do pobierania komentarzy dla danego posta
function getComments($postId) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT comments.*, users.username FROM comments JOIN users ON comments.author_id = users.id WHERE comments.post_id = ?");
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result;
}

?>
