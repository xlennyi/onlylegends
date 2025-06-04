<?php
session_start();

$isLoggedIn = isset($_SESSION['user_id']);
$userId = $_SESSION['user_id'] ?? null;

$config = require __DIR__ . '/config.php';

$host = $config['host'];
$db   = $config['db'];
$user = $config['user'];
$pass = $config['pass'];


try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Błąd połączenia z bazą danych: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subscribe_to_id'])) {
    $subscriberId = $_SESSION['user_id'];
    $subscribedToId = $_POST['subscribe_to_id'];

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM subscriptions WHERE subscriber_id = ? AND subscribed_to_id = ?");
    $stmt->execute([$subscriberId, $subscribedToId]);
    $alreadySubscribed = $stmt->fetchColumn() > 0;

    if (!$alreadySubscribed) {
        $stmt = $pdo->prepare("INSERT INTO subscriptions (subscriber_id, subscribed_to_id) VALUES (?, ?)");
        $stmt->execute([$subscriberId, $subscribedToId]);
    }

    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$subscribedToId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        header("Location: profile.php?username=" . urlencode($user['username']));
        exit;
    } else {
        echo "Nie znaleziono użytkownika.";
    }
}
