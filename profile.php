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

    $userId = $_SESSION['user_id'] ?? null;

    $username = $_GET['username'];

    $sql = "
    SELECT posts.*, users.username, users.pfp, 
        (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) AS like_count,
        (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id AND likes.user_id = ?) AS user_liked
    FROM users
    JOIN posts ON users.id = posts.user_id
    WHERE users.username = ?
    ORDER BY posts.id DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId, $username]);
    
    $selected_posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sql_user = "SELECT * FROM users WHERE username = ?";
    $stmt_user = $pdo->prepare($sql_user);
    $stmt_user->execute([$username]);
    $selected_user = $stmt_user->fetch(PDO::FETCH_ASSOC);
    

    $sql2 = "SELECT id, username, pfp FROM users ORDER BY id ASC";
    $stmt2 = $pdo->query($sql2);
    $users = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    
    $blad = [];

    function hasSubscription($pdo, $subscriberId, $subscribedToId) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM subscriptions WHERE subscriber_id = ? AND subscribed_to_id = ?");
    $stmt->execute([$subscriberId, $subscribedToId]);
    return $stmt->fetchColumn() > 0;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_content'], $_POST['post_id'])) {


    if (!$userId) {
        $blad[] = "Musisz być zalogowany, aby komentować.";
    } else {
        $commentContent = trim($_POST['comment_content']);
        $postId = (int)$_POST['post_id'];

        error_log("User ID: " . $userId);
        error_log("Comment content: " . $commentContent);
        error_log("Post ID: " . $postId);

        if ($commentContent === '') {
            $blad[] = "Treść komentarza nie może być pusta.";
        }

        if (empty($blad)) {
            $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())");
            if (!$stmt->execute([$postId, $userId, $commentContent])) {
                $errorInfo = $stmt->errorInfo();
                error_log("SQL insert error: " . implode(", ", $errorInfo));
            } else {
                error_log("Komentarz dodany poprawnie.");
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit;
            }
        }
    }
}


    $postIds = array_column($selected_posts, 'id');
    $comments = [];

    if (!empty($postIds)) {
        $inQuery = implode(',', array_fill(0, count($postIds), '?'));
        $stmt = $pdo->prepare("
            SELECT comments.*, users.username, users.pfp 
            FROM comments 
            JOIN users ON comments.user_id = users.id 
            WHERE comments.post_id IN ($inQuery)
            ORDER BY comments.created_at ASC
        ");
        $stmt->execute($postIds);
        $commentsRaw = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($commentsRaw as $c) {
            $comments[$c['post_id']][] = $c;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['like_post_id'])) {
        if (!$userId) {
            $blad[] = "Musisz być zalogowany, aby polubić post.";
        } else {
            $postId = (int)$_POST['like_post_id'];

            $stmt = $pdo->prepare("SELECT * FROM likes WHERE user_id = ? AND post_id = ?");
            $stmt->execute([$userId, $postId]);
            $alreadyLiked = $stmt->fetch();

            if ($alreadyLiked) {
                $stmt = $pdo->prepare("DELETE FROM likes WHERE user_id = ? AND post_id = ?");
                $stmt->execute([$userId, $postId]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO likes (user_id, post_id) VALUES (?, ?)");
                $stmt->execute([$userId, $postId]);
            }

            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        }
    }

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <title>OnlyLegends - Moj profil</title>
    <style>

        .post-image {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        }

        .post-image-blur {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-size: cover;
        background-position: center;
        filter: blur(10px);
        }

        .subscribe-button{
        padding: 10px 20px;
        border: 1px solid rgb(255, 255, 255);
        box-shadow: 0px 0px 5px rgb(255, 255, 255);
        border-radius: 999px;
        text-decoration: none;
        color: white;
        font-weight: 600;
        background-color: #079edf;
        transition: transform 0.2s ease, background-color 0.2s ease, color 0.2s ease;
        margin: 20px 0 0 24px;
        cursor: pointer;
        margin-top: 50px;
        }


        #info-about-user{
            display: flex;
        }


        #info-about-user-buttons img{
            background-color: #0878a8;
            border-radius: 999px;
        }

        @media screen and (max-width: 850px) {


            .edit-account-button-icon, .add-post-button-icon{
                display: flex;
                padding: 5px;
                width: 40%;
                margin:0;
            }


        }
    
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
                <img src="images/home-agreement.png" alt="home" width="32">
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

    <section id="main-mid">
        <div id="info-about-user">
            <div id="info-about-user-text">
            <h2>
                Konto uzytkownika <?= htmlspecialchars($selected_user['username']) ?>
                <?php if (isset($_SESSION['user']) && $_SESSION['user'] === $username): ?>
            </div>
            <div id="info-about-user-buttons">
                <a href="edit_account.php" class="edit-account-button">Edytuj konto</a>
                <a href="add_post.php" class="add-post-button">Dodaj post</a>
                
                <?php endif; ?>
            </div>
            </h2>
        </div>
        <a href="#" class="post-user">
            <img class="post-user-head" src="<?= htmlspecialchars($selected_user['pfp']) ?>" alt="post-pfp">
            <div class="post-user-info">
                <p class="post-username"><?= htmlspecialchars($selected_user['username']) ?></p>
                <p class="post-under-username">@<?= htmlspecialchars($selected_user['username']) ?></p>
            </div>
        </a>
        <h3>Opis</h3>
        <p class="user-description"><?= htmlspecialchars($selected_user['description']) ?></p>

        <hr>


    <div id="post_subscribe">
        <h2>Posty</h2>
        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $selected_user['id']): ?>
            <?php if (!hasSubscription($pdo, $_SESSION['user_id'], $selected_user['id'])): ?>
                <form action="subscriptions.php" method="POST">
    <input type="hidden" name="subscribe_to_id" value="<?= htmlspecialchars($selected_user['id']) ?>">
    <button type="submit" class="subscribe-button">SUBSKRYBUJ</button>
</form>

            <?php else: ?>
                <form action="subscriptions.php" method="POST">
                <input type="hidden" name="subscribe_to_id" value="<?= htmlspecialchars($selected_user['id']) ?>">
                <button type="submit" class="subscribe-button">SUBSKRYBUJESZ</button>
            </form>

            <?php endif; ?>
        <?php endif; ?>
    </div>

        <?php foreach ($selected_posts as $post): ?>
            <div class="post">
                    <a href="#" class="post-user">
                        <img class="post-user-pfp" src="<?= htmlspecialchars($post['pfp']) ?>" alt="post-pfp">
                        <div class="post-user-info">
                            <p class="post-username"><?= htmlspecialchars($post['username']) ?></p>
                            <p class="post-under-username">@<?= htmlspecialchars($post['username']) ?></p>
                        </div>
                    </a>
                    <p class="post-content"><?= htmlspecialchars($post['content']) ?></p>
                    <?php if (!empty($post['image_url'])): ?>
                        <?php
                        if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_id'] == $post['user_id']) {
        $hasSub = true;
    } else {
        $hasSub = hasSubscription($pdo, $_SESSION['user_id'], $post['user_id']);
    }
}

                    ?>
                    <div class="post-image-wrapper">
                        <?php if ($hasSub): ?>
                            <img class="post-image" src="<?= htmlspecialchars($post['image_url']) ?>" alt="post-image">
                        <?php else: ?>
                            <div class="post-image-blur" style="background-image: url('<?= htmlspecialchars($post['image_url']) ?>');">
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php endif; ?>
                </div>
                
            <div class="comm-like">
                    <button class="show-comment-form" data-post-id="<?= $post['id'] ?>">Dodaj komentarz</button>
                    <form method="POST" class="like-form">
    <input type="hidden" name="like_post_id" value="<?= $post['id'] ?>">
    <input type="hidden" name="action" value="like">
    <button type="submit" class="like-button <?= $post['user_liked'] ? 'liked' : '' ?>">
        ❤️ <?= $post['like_count'] ?>
    </button>
</form>

                </div>

                <div class="comment-form-container" id="comment-form-<?= $post['id'] ?>" style="display:none;">
                    <form method="POST" action="">
    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
    <input type="hidden" name="action" value="comment">
    <textarea class="comment-form-content" name="comment_content" rows="3" cols="60" placeholder="Napisz komentarz..."></textarea><br>
    <button class="add-comm-submit" type="submit">Wyślij komentarz</button>
</form>

                </div>

                <div class="comments">
                    <?php if (!empty($comments[$post['id']])): ?>
                        <?php foreach ($comments[$post['id']] as $comment): ?>
                            <div class="comment">
                                <img src="<?= htmlspecialchars($comment['pfp']) ?>" alt="user-pfp" class="comment-user-pfp" width="32">
                                <strong class="comment-username"><?= htmlspecialchars($comment['username']) ?>:</strong>: 
                                <span class="comment-content"><?= htmlspecialchars($comment['content']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="comments-none">Brak komentarzy.</p>
                    <?php endif; ?>
                </div>

        <?php endforeach; ?>
    </section>

    <section id="main-right">
        <div id="search-name-icon">
            <img src="images/search.png" alt="searching-tool">
        </div>
        <div id="search-name">
        <h4>Wyszukiwanie uzytkownika</h4>
        <div id="search-form">
            <input id="search-username" type="text" name="username2" placeholder="Nazwa użytkownika">
        </div>
        </div>

        <div class="right-users">
            <?php foreach ($users as $user): ?>
                <a href="profile.php?username=<?= urlencode($user['username']) ?>" class="user-href">
                    <div class="right-user">
                        <img src="<?= htmlspecialchars($user['pfp']) ?>" alt="user-pfp" class="right-user-pfp">
                        <div class="right-user-info">
                            <p class="post-username"><?= htmlspecialchars($user['username']) ?></p>
                            <p class="post-under-username">@<?= htmlspecialchars($user['username']) ?></p>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
            </main>
    <footer>
        <p>&copy; <?= date('Y') ?> <strong>OnlyLegends</strong> Wszelkie prawa zastrzeżone.</p>
        <p>Tylko dla legend.</p>
        <p>Dołącz do spicy społeczności</p>
    </footer>
    
    <script src="js/menu.js"></script>
    <script src="js/searching.js?v=<?= time(); ?>"></script>
    <script src="js/add_comm.js"></script>
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