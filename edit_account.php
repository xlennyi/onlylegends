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

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $userId = $_SESSION['user_id'];

        if (isset($_FILES['pfp']) && $_FILES['pfp']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['pfp']['tmp_name'];
            $fileName = $_FILES['pfp']['name'];
            $fileSize = $_FILES['pfp']['size'];
            $fileType = $_FILES['pfp']['type'];

            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($fileType, $allowedTypes)) {
                $blad[] = "Nieobsługiwany format pliku. Dozwolone: JPG, PNG, GIF, WEBP.";
            } else {
                $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                $newFileName = uniqid('pfp_', true) . '.' . $ext;

                $uploadFileDir = __DIR__ . '/pfp/';
                $destPath = $uploadFileDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $stmt = $pdo->prepare("UPDATE users SET pfp = ? WHERE id = ?");
                    $stmt->execute(['pfp/' . $newFileName, $userId]);
                    $blad[] = "Zdjęcie zostało pomyślnie przesłane.";
                } else {
                    $blad[] = "Wystąpił błąd podczas zapisywania pliku.";
                }
            }
        }

        $newUsername = trim($_POST['username'] ?? '');
        if ($newUsername !== '') {
            $checkStmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
            $checkStmt->execute([$newUsername, $userId]);
            if ($checkStmt->rowCount() > 0) {
                $blad[] = "Nazwa użytkownika jest już zajęta.";
            } else {
                $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
                $stmt->execute([$newUsername, $userId]);
                $_SESSION['user'] = $newUsername;
                $blad[] = "Nazwa użytkownika została zmieniona.";
            }
        }

        $newDescription = trim($_POST['description'] ?? '');
        if ($newDescription !== '') {
            $stmt = $pdo->prepare("UPDATE users SET description = ? WHERE id = ?");
            $stmt->execute([$newDescription, $userId]);
            $blad[] = "Opis został zaktualizowany.";
        }

        if (empty($blad)) {
            $blad[] = "Nie wprowadzono żadnych zmian.";
        }
    }
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <title>OnlyLegends - Edycja konta</title>
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
    <form method="POST" action="" id="edit-form" enctype="multipart/form-data">
        <h2>Edytuj Konto</h2>
        <p>Pozostaw puste, aby nic nie zmieniać</p>
        <input id="edit-username" type="text" name="username" placeholder="Nazwa użytkownika">

        <textarea id="desc-content" name="description" placeholder="Opis" rows="5" cols="120"></textarea>
                        
        <p>Dodaj zdjęcie profilowe:</p>
        <input id="edit-pfp" type="file" name="pfp" accept="image/*">

        <button id="edit-submit" type="submit">ZAPISZ</button>

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