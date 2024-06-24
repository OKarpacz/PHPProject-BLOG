<?php
include('../includes/Database.php');
include('../includes/functions.php');

session_start();

$db = Database::getInstance();
$conn = $db->getConnection();

$query = "SELECT * FROM users";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    // Otwarcie pliku do zapisu
    $file = fopen('user.txt', 'w');

    while ($row = $result->fetch_assoc()) {
        $userData = "ID: " . $row['id'] . "\n";
        $userData .= "Username: " . $row['username'] . "\n";
        $userData .= "Email: " . $row['email'] . "\n";
        $userData .= "--------------------------------------\n";

        fwrite($file, $userData);
    }

    fclose($file);

    echo "Dane użytkowników zostały zapisane do pliku user.txt.";
} else {
    echo "Brak użytkowników.";
}

$conn->close();
?>
