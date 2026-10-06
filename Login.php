<?php
session_start();

$DB_HOST = 'localhost';
$DB_NAME = 'register_db';
$DB_USER = 'root';
$DB_PASS = '';

$HOME_PAGE = 'home.php';
$REGISTER_PAGE = 'register.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: ' . $HOME_PAGE);
    exit;
}

function img(string $name): string {
    $dir = __DIR__ . '/images';
    if (is_dir($dir)) {
        foreach (scandir($dir) as $f) {
            $info = pathinfo($f);
            if (isset($info['extension'])
                && strtolower($info['filename']) === strtolower($name)
                && in_array(strtolower($info['extension']), ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                return 'images/' . rawurlencode($f);
            }
        }
    }
    return 'images/' . $name . '.png';
}

$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $errors[] = 'Enter your email and password.';
    } else {
        try {
            $pdo = new PDO(
                "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
                $DB_USER,
                $DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            $stmt = $pdo->prepare('SELECT id, email, password_hash FROM users WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                header('Location: ' . $HOME_PAGE);
                exit;
            }

            $errors[] = 'Incorrect email or password.';
        } catch (PDOException $e) {
            $errors[] = 'Could not connect to the database. Check your settings.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login | Betta HQ</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Chelsea+Market&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

<style>
  :root {
    --teal: #3b8a94;
    --teal-dark: #2f6f78;
    --ink: #1f3a40;
    --white: #ffffff;
  }

  * { box-sizing: border-box; margin: 0; padding: 0; }

  html, body { height: 100%; }

  body {
    font-family: 'Inter', sans-serif;
    color: var(--ink);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px;
    overflow-x: hidden;
    background: linear-gradient(160deg, #1f7a6a 0%, #1a5f55 45%, #0f3f3f 100%);
  }

  .bg {
    position: fixed; inset: 0; z-index: -1;
    width: 100%; height: 100%; object-fit: cover;
  }

  .stage {
    position: relative;
    display: flex;
    width: min(100%, 1000px);
    min-height: 520px;
  }

  .glass {
    position: relative;
    z-index: 1;
    flex: 1;
    border-radius: 18px 0 0 18px;
    background: rgba(255, 255, 255, .12);
    backdrop-filter: blur(1.5px);
    -webkit-backdrop-filter: blur(1.5px);
    border: 1px solid rgba(255, 255, 255, .25);
  }

  .card {
    position: relative;
    z-index: 3;
    width: 400px;
    flex: 0 0 400px;
    padding: 36px 40px 32px;
    border-radius: 0 18px 18px 0;
    background: linear-gradient(180deg, #ffffff 0%, #8fc2c4 45%, var(--teal-dark) 100%);
    text-align: center;
    box-shadow: 0 10px 30px rgba(0, 0, 0, .25);
  }

  .brand { display: flex; align-items: center; justify-content: center; gap: 14px; }

  .brand h1 {
    font-family: 'Chelsea Market', cursive;
    font-size: 44px;
    line-height: 1.05;
    color: var(--teal);
    text-align: center;
  }

  .logo-fish { width: 115px; height: auto; }

  .tagline {
    margin-top: 16px;
    font-family: 'Chelsea Market', cursive;
    font-size: 20px;
    line-height: 1.35;
    color: var(--ink);
  }

  form { margin-top: 26px; text-align: left; }

  label { display: block; font-size: 14px; font-weight: 600; margin: 0 0 6px 6px; color: #12262b; }

  .field {
    width: 100%;
    height: 56px;
    padding: 0 20px;
    margin-bottom: 22px;
    border: 0;
    border-radius: 18px;
    background: var(--white);
    font: 400 15px 'Inter', sans-serif;
    color: var(--ink);
    box-shadow: 0 1px 3px rgba(0, 0, 0, .15);
  }
  .field::placeholder { color: #888; }
  .field:focus { outline: 3px solid #ffd27a; }

  .buttons { display: flex; gap: 14px; margin-top: 10px; }

  .btn {
    flex: 1;
    height: 42px;
    border: 0;
    border-radius: 21px;
    background: var(--white);
    color: var(--ink);
    font: 500 16px 'Inter', sans-serif;
    text-align: center;
    text-decoration: none;
    line-height: 42px;
    cursor: pointer;
    box-shadow: 0 1px 3px rgba(0, 0, 0, .2);
    transition: transform .15s ease, background .15s ease;
  }
  .btn:hover { background: #e9f6f6; transform: translateY(-1px); }
  .btn:focus-visible { outline: 3px solid #ffd27a; }

  .small { margin-top: 16px; font-size: 13px; text-align: center; color: #e6f3f4; }
  .small a { color: #0f3a40; text-decoration: none; font-weight: 600; }
  .small a:hover { text-decoration: underline; }

  .errors, .success {
    margin: 0 0 14px;
    padding: 10px 14px;
    border-radius: 10px;
    font-size: 13px;
    list-style: none;
    text-align: left;
  }
  .errors { background: #fff1f0; color: #a12622; }
  .success { background: #eafaf0; color: #1d6b3a; margin-top: 18px; }

  .fish { position: absolute; pointer-events: none; height: auto; }
  .fish-left { z-index: 4; width: 340px; bottom: 15px; left: -140px; }
  .fish-right { z-index: 2; width: 360px; top: -45px; right: -230px; }

  @media (max-width: 760px) {
    .glass, .fish { display: none; }
    .card { flex: 1; width: 100%; padding-bottom: 40px; border-radius: 18px; }
  }
</style>
</head>

<body>
  <img class="bg" src="<?= img('background') ?>" alt="">

  <main class="stage">

    <div class="glass"></div>

    <section class="card">
      <div class="brand">
        <h1>Betta<br>HQ</h1>
        <img class="logo-fish" src="<?= img('logo-fish') ?>" alt="Betta HQ logo fish">
      </div>

      <p class="tagline">Healthy Fish<br>Happy Aquarium</p>

      <?php if ($flash): ?>
        <p class="success" role="status"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></p>
      <?php endif; ?>

      <form method="post" action="login.php" novalidate>
        <label for="email">Email</label>
        <input class="field" type="email" id="email" name="email"
               placeholder="Enter Email..." autocomplete="email" required
               value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>">

        <label for="password">Password</label>
        <input class="field" type="password" id="password" name="password"
               placeholder="Enter Password...." autocomplete="current-password" required>

        <?php if ($errors): ?>
          <ul class="errors" role="alert">
            <?php foreach ($errors as $e): ?>
              <li><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>

        <div class="buttons">
          <a class="btn" href="<?= htmlspecialchars($REGISTER_PAGE) ?>">Register</a>
          <button class="btn" type="submit">Login</button>
        </div>
      </form>

      <p class="small">No account? <a href="<?= htmlspecialchars($REGISTER_PAGE) ?>">Register here</a></p>
    </section>

    <img class="fish fish-right" src="<?= img('betta-right') ?>" alt="">
    <img class="fish fish-left" src="<?= img('betta-left') ?>" alt="">

  </main>
</body>
</html>