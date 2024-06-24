<?php
include('../includes/Database.php');
include('../includes/functions.php');
session_start();

$db = Database::getInstance();
$conn = $db->getConnection();

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();

$prevStmt = $conn->prepare("SELECT id FROM posts WHERE id < ? ORDER BY id DESC LIMIT 1");
$prevStmt->bind_param('i', $id);
$prevStmt->execute();
$prevPost = $prevStmt->get_result()->fetch_assoc();
$prevPostId = $prevPost ? $prevPost['id'] : null;

$nextStmt = $conn->prepare("SELECT id FROM posts WHERE id > ? ORDER BY id ASC LIMIT 1");
$nextStmt->bind_param('i', $id);
$nextStmt->execute();
$nextPost = $nextStmt->get_result()->fetch_assoc();
$nextPostId = $nextPost ? $nextPost['id'] : null;
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo isset($post['title']) ? htmlspecialchars($post['title']) : ''; ?></title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <style>
        .nav-arrow {
            position: fixed;
            top: 50%;
            transform: translateY(-50%);
            font-size: 2em;
            color: #333;
            text-decoration: none;
        }
        .nav-arrow.prev {
            left: 10px;
        }
        .nav-arrow.next {
            right: 10px;
        }
    </style>
</head>
<body>
<header>
    <h1>The Financial Insight</h1>
    <nav>
        <a href="index.php">Strona główna</a>
        <?php if (isset($_SESSION['username'])): ?>
            <a href="logout.php">Wyloguj się</a>
            <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'author'): ?>
                <a href="../admin/dashboard.php">Admin Panel</a>
            <?php endif; ?>
        <?php else: ?>
            <a href="login.php">Zaloguj się</a>
            <a href="register.php">Zarejestruj się</a>
        <?php endif; ?>
    </nav>
</header>
<div class="container">
    <?php if ($post): ?>
        <div class="single-post">
            <h2><?php echo isset($post['title']) ? htmlspecialchars($post['title']) : ''; ?></h2>
            <p class="single-post-meta"><small>Opublikowano <?php echo isset($post['publish_date']) ? htmlspecialchars($post['publish_date']) : ''; ?></small></p>
            <div><?php echo isset($post['content']) ? nl2br(htmlspecialchars($post['content'])) : ''; ?></div>
            <?php if ($post['image']): ?>
                <img class="single-post-image" src="../IMAGES/<?php echo isset($post['image']) ? htmlspecialchars($post['image']) : ''; ?>" alt="<?php echo isset($post['title']) ? htmlspecialchars($post['title']) : ''; ?>">
            <?php endif; ?>
        </div>
        <div class="comments">
            <h3>Komentarze</h3>
            <?php
            $stmt = $conn->prepare("SELECT comments.*, users.username FROM comments LEFT JOIN users ON comments.user_id = users.id WHERE post_id = ? ORDER BY publish_date DESC");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $comments = $stmt->get_result();

            while($comment = $comments->fetch_assoc()): ?>
                <div class="comment">
                    <p><?php echo isset($comment['content']) ? htmlspecialchars($comment['content']) : ''; ?></p>
                    <p><small>Opublikowano <?php echo isset($comment['publish_date']) ? htmlspecialchars($comment['publish_date']) : ''; ?> przez <?php echo isset($comment['username']) ? htmlspecialchars($comment['username']) : 'Gość'; ?></small></p>
                </div>
            <?php endwhile; ?>

            <form method="POST" action="add_comment.php">
                <?php if (isset($_SESSION['error'])): ?>
                    <p style="color: red;"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
                <?php endif; ?>
                <textarea name="content" maxlength="200" required></textarea><br><br>
                <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($id); ?>">
                <input type="submit" value="Dodaj komentarz">
            </form>
        </div>
        <?php if (isset($_SESSION['username'])): ?>
            <a class="read-more-button" href="edit_post.php?id=<?php echo htmlspecialchars($post['id']); ?>">Edytuj</a>
            <a class="read-more-button" href="delete_post.php?id=<?php echo htmlspecialchars($post['id']); ?>" onclick="return confirm('Czy na pewno chcesz usunąć ten post?');">Usuń</a>
        <?php endif; ?>
    <?php else: ?>
        <p>Post o podanym identyfikatorze nie istnieje.</p>
    <?php endif; ?>
</div>
<?php if ($prevPostId): ?>
    <a class="nav-arrow prev" href="read_more.php?id=<?php echo htmlspecialchars($prevPostId); ?>">&larr;</a>
<?php endif; ?>
<?php if ($nextPostId): ?>
    <a class="nav-arrow next" href="read_more.php?id=<?php echo htmlspecialchars($nextPostId); ?>">&rarr;</a>
<?php endif; ?>
<footer>
    <p>&copy; The Financial Insight.</p>
</footer>
</body>
</html>
