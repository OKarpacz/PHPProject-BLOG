<?php
include('../includes/Database.php');
include('../includes/functions.php');
session_start();

$db = Database::getInstance();
$conn = $db->getConnection();

$date_filter = '';
if (isset($_GET['date'])) {
    $date_filter = $_GET['date'];
    $stmt = $conn->prepare("SELECT * FROM posts WHERE DATE(publish_date) = ? ORDER BY publish_date DESC");
    $stmt->bind_param('s', $date_filter);
} else {
    $stmt = $conn->prepare("SELECT * FROM posts ORDER BY publish_date DESC");
}


$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Strona Główna</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
<header>
    <h1>The Financial Insight</h1>
    <nav>
        <a href="index.php">Strona główna</a>
        <a href="contact_me.php">Kontakt</a>
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
    <br><br>
    <form action="index.php" method="GET">
        <label for="date-filter">Sortuj po dacie:</label><br><br>
        <select name="date" id="date-filter">
            <option value="">Wybierz datę</option>
            <?php
            $dates_query = "SELECT DISTINCT DATE(publish_date) AS publish_date FROM posts ORDER BY publish_date DESC";
            $dates_result = $conn->query($dates_query);
            while ($row = $dates_result->fetch_assoc()) {
                $selected = ($date_filter == $row['publish_date']) ? 'selected' : '';
                echo "<option value='" . $row['publish_date'] . "' $selected>" . date('d.m.Y', strtotime($row['publish_date'])) . "</option>";
            }
            ?>
        </select><br><br>
        <input type="submit" value="Sortuj">
    </form>
</header>

<div class="container">
    <h2>Opis Bloga</h2>
    <h3>Witaj na The Financial Insight!</h3>
    <p>The Financial Insight to miejsce, gdzie znajdziesz inspirujące artykuły dotyczące finansów osobistych, inwestycji oraz aktualności gospodarczych. Naszym celem jest dostarczanie czytelnikom rzetelnych informacji, które wspierają rozwój osobisty oraz edukację finansową.</p>

    <h2>Najnowsze posty:</h2>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="post">
            <h3><?php echo $row['title']; ?></h3>
            <?php if (!empty($row['image'])): ?>
                <img src="../IMAGES/<?php echo $row['image']; ?>" alt="<?php echo $row['title']; ?>" style="max-width: 100%;">
            <?php endif; ?>
            <p><?php echo substr($row['content'], 0, 200); ?>...</p>
            <a class="read-more-button" href="read_more.php?id=<?php echo $row['id']; ?>">Czytaj więcej</a>
            <p>Opublikowano: <?php echo $row['publish_date']; ?></p>
        </div>
    <?php endwhile; ?>
</div>


<footer>
    <p>&copy; The Financial Insight.</p>
</footer>
</body>
</html>
