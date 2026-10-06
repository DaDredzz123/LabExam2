<?php
session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
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

$email = $_SESSION['email'] ?? '';
$user = strstr($email, '@', true) ?: $email;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Welcome | Betta HQ</title>
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

  .card {
    position: relative;
    z-index: 2;
    width: 400px;
    flex: 0 0 400px;
    padding: 36px 40px 32px;
    border-radius: 18px 0 0 18px;
    background: linear-gradient(180deg, #ffffff 0%, #8fc2c4 45%, var(--teal-dark) 100%);
    text-align: center;
    box-shadow: 0 10px 30px rgba(0, 0, 0, .25);
    display: flex;
    flex-direction: column;
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

  .welcome {
    margin: auto 0;
    padding: 24px 0;
  }

  .welcome p {
    font-family: 'Chelsea Market', cursive;
    font-size: 30px;
    line-height: 1.2;
    color: var(--white);
    text-shadow: 0 2px 6px rgba(0, 0, 0, .25);
    word-break: break-word;
  }

  .welcome span { display: block; margin-top: 4px; font-size: 38px; color: #ffffff; }

  .btn {
    display: block;
    width: 160px;
    margin: 0 auto;
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

  .glass {
    position: relative;
    z-index: 1;
    flex: 1;
    border-radius: 0 18px 18px 0;
    background: rgba(255, 255, 255, .12);
    backdrop-filter: blur(1.5px);
    -webkit-backdrop-filter: blur(1.5px);
    border: 1px solid rgba(255, 255, 255, .25);
  }

  .fish { position: absolute; z-index: 3; pointer-events: none; height: auto; }
  .fish-right { width: 360px; top: -45px; right: -70px; }
  .fish-left  { width: 340px; bottom: -50px; left: -290px; }

  @media (max-width: 760px) {
    .glass, .fish { display: none; }
    .card { flex: 1; width: 100%; border-radius: 18px; }
  }
</style>
</head>

<body>
  <img class="bg" src="<?= img('background') ?>" alt="">

  <main class="stage">

    <section class="card">
      <div class="brand">
        <h1>Betta<br>HQ</h1>
        <img class="logo-fish" src="<?= img('logo-fish') ?>" alt="Betta HQ logo fish">
      </div>

      <p class="tagline">Healthy Fish<br>Happy Aquarium</p>

      <div class="welcome">
        <p>Welcome,<span><?= htmlspecialchars($user, ENT_QUOTES, 'UTF-8') ?>!</span></p>
      </div>

      <a class="btn" href="logout.php">Logout</a>
    </section>

    <div class="glass"></div>

    <img class="fish fish-right" src="<?= img('betta-right') ?>" alt="">
    <img class="fish fish-left" src="<?= img('betta-left') ?>" alt="">

  </main>
</body>
</html>