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


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordRepeat = $_POST['password_repeat'] ?? '';

    // Walidacja
    if (empty($username) || empty($password) || empty($passwordRepeat)) {
        $blad = '❌ Wypełnij wszystkie pola!';
    } elseif ($password !== $passwordRepeat) {
        $blad = '❌ Hasła się nie zgadzają!';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $blad = '❌ Użytkownik już istnieje!';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $defaultPfp = 'pfp/default.png';
            $insert = $pdo->prepare("INSERT INTO users (username, password, pfp) VALUES (:username, :password, :pfp)");
            $insert->bindParam(':username', $username);
            $insert->bindParam(':password', $hashedPassword);
            $insert->bindParam(':pfp', $defaultPfp);


            if ($insert->execute()) {
                $_SESSION['zalogowany'] = true;
                $_SESSION['user'] = $username;
                $_SESSION['user_id'] = $pdo->lastInsertId();
                setcookie("auth", "ok", time() + 3600, "/");
                header('Location: index.php');
                exit();
            } else {
                $blad = '❌ Wystąpił błąd przy zapisie do bazy!';
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <title>OnlyLegends - Rejestracja</title>
<style>
        @media screen and (min-width: 1340px) {
            #rules-button {
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


    <section id="main-login">
    <form method="POST" action="" id="register-form">
        <h2>🔐 Zarejestruj się</h2>
        <input id="register-username" type="text" name="username" placeholder="Nazwa użytkownika" required>
        <input id="register-password" type="password" name="password" placeholder="Hasło" required>
        <input id="register-password-again" type="password" name="password_repeat" placeholder="Powtórz hasło" required>

        <div class="checkbox-container">
            <input type="checkbox" id="rules-check" required>
            <label for="rules-check"></label>Akceptuję <a href="rules.html">regulamin</a>OnlyLegends<span>(wymagane)</span>
        </div>

        <button id="register-submit-submit" type="submit">ZAREJESTRUJ</button>
        <a href="login.php"><button type="button" id="register-submit">MASZ KONTO? ZALOGUJ SIE</button></a>

        <?php if (!empty($blad)): ?>
            <div class="blad"><?= $blad ?></div>
        <?php endif; ?>
    </form>
</section>

    </section>
        </main>
<footer>
        <p>&copy; <?= date('Y') ?> <strong>OnlyLegends</strong> Wszelkie prawa zastrzeżone.</p>
        <p>Tylko dla legend.</p>
        <p>Dołącz do spicy społeczności</p>
    </footer>


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
