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

$blad = [];
$content = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isLoggedIn) {
    $content = $_POST['content'] ?? '';
    $image_url = null;

    if (trim($content) === '') {
        $blad[] = "Wypełnij wszystkie pola.";
    } else {
        // Obsługa zdjęcia
        if (!empty($_FILES['image']['tmp_name'])) {
            $uploadDir = 'posts/';
            $fileName = basename($_FILES['image']['name']);
            $targetPath = $uploadDir . time() . "_" . $fileName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                $image_url = $targetPath;
            } else {
                $blad[] = "Nie udało się przesłać zdjęcia.";
            }
        }

        if (empty($blad)) {
            $stmt = $pdo->prepare("INSERT INTO posts (user_id, content, image_url) VALUES (?, ?, ?)");
            $stmt->execute([$userId, $content, $image_url]);

            header("Location: index.php"); // lub inna strona po dodaniu posta
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>OnlyLegends - Dodaj post</title>
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <style>
        @media screen and (min-width: 1340px) {
            #rules-button, #logout-button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <header>
        <img src="images/logo2.png" alt="Logo OnlyLegends">
    </header>
    <main>
<section id="main-left">
        <div class="sidebar">
            <img id="logo" src="images/logo2.png" alt="logo">
            <br>

            <?php if ($isLoggedIn = isset($_SESSION['user_id'])): ?>
                <a href="profile.php?username=<?= urlencode($_SESSION['user']) ?>" class="menu-item">
                    <img src="images/user.png" alt="home" width="32">
                    <span class="menu-text">Moje konto</span>
                </a>
            <?php else: ?>
                <a href="login.php" class="menu-item">
                    <img src="images/user.png" alt="home" width="32">
                    <span class="menu-text">Logowanie</span>
                </a>
            <?php endif; ?>

            <a href="index.php" class="menu-item">
                <img src="images/home-agreement.png" alt="menu" width="32">
                <span class="menu-text">Glówna</span>
            </a>

            <a href="rules.html" class="menu-item" id="rules-button">
                <img src="images/rules.png" alt="rules" width="32">
                <span class="menu-text">Regulamin</span>
            </a>
            <?php if ($isLoggedIn = isset($_SESSION['user_id'])): ?>
            <a href="logout.php" class="menu-item" id="logout-button">
                <img src="images/logout.png" alt="logout" width="32">
                <span class="menu-text">Wyloguj sie</span>
            </a>
            <?php endif; ?>

            <div class="dropdown">
                <button id="more-button">
                    <img src="images/more.png" alt="more" width="32">
                    <span class="menu-text">Więcej</span>
                </button>
                <div id="more-menu" class="dropdown-menu hidden">
                    <a href="rules.html">Regulamin</a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="logout.php">Wyloguj się</a>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </section>

<section id="main-edit">
    <form method="POST" action="" enctype="multipart/form-data" id="add-post-form">
        <h2>Dodaj nowy post</h2>

        <textarea id="post-content" name="content" rows="5" cols="120" placeholder="Treść posta"><?= htmlspecialchars($content) ?></textarea><br>

        <p>Dodaj zdjęcie:</p>
        <input id="add-img" type="file" name="image" accept="image/*" />

        <button id="add-submit" type="submit">DODAJ POST</button>
        <a href="profile.php?username=<?= urlencode($_SESSION['user']) ?>">
            <button type="button" id="register-submit">COFNIJ</button>
        </a>

        <?php if (!empty($blad)): ?>
            <div class="blad">
                <ul>
                    <?php foreach ($blad as $komunikat): ?>
                        <li><?= htmlspecialchars($komunikat) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
            </form>
        </section>
        </main>


<script src="js/menu.js"></script>
<script src="js/facilities.js?v=<?= time(); ?>"></script>
<button id="scrollToTopBtn" aria-label="Przewiń do góry">
  <img src="images/arrow-up.png" alt="Strzałka do góry">
</button>


    <script>
    const scrollToTopBtn = document.getElementById('scrollToTopBtn');

    window.addEventListener('scroll', () => {
    if (window.pageYOffset > 50) {
        scrollToTopBtn.classList.add('show');
    } else {
        scrollToTopBtn.classList.remove('show');
    }
    });

    scrollToTopBtn.addEventListener('click', () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
    });


</script>
</body>
</html>
