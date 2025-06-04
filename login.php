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
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT id, password FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['zalogowany'] = true;
            $_SESSION['user'] = $username;
            $_SESSION['user_id'] = $user['id'];
            setcookie("auth", "ok", time() + 3600, "/");
            header('Location: index.php');
            exit();
        } else {
            $blad = '❌ Nieprawidłowy login lub hasło!';
        }
    } else {
        $blad = '❌ Wprowadź login i hasło!';
    }
}
?>



<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <title>OnlyLegends - Logowanie</title>
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
                <a href="my_account.php" class="menu-item">
                    <img src="images/user.png" alt="my_account" width="32">
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
    <form method="POST" action="" id="login-form">
        <h2>🔐 Zaloguj się</h2>
        <input id="login-username" type="text" name="username" placeholder="Nazwa użytkownika" required>
        <input id="login-password" type="password" name="password" placeholder="Hasło" required>
        <button id="login-submit" type="submit">ZALOGUJ</button>


            <a href="register.php"><button type="button" id="register-submit">ZAREJESTRUJ</button></a>

        <?php if (!empty($blad)): ?>
            <div class="blad"><?= $blad ?></div>
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