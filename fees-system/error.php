<?php
$code = isset($_GET['code']) ? (int)$_GET['code'] : 500;

$messages = [
  400 => 'Bad Request',
  401 => 'Unauthorized Access',
  403 => 'Access Forbidden',
  404 => 'Page Not Found',
  500 => 'Internal Server Error',
  502 => 'Bad Gateway',
  503 => 'Service Unavailable',
  504 => 'Gateway Timeout'
];

$title = $messages[$code] ?? 'Unexpected Error';
http_response_code($code);

/* ===============================
   Fees Management System Branding
   =============================== */
$home       = '/fees-system/index.php';
$institute  = 'Fees Management System v1.0.0';
$logo       = '/fees-system/assets/logos/FMS-v1.0.0.png';   // place your logo here
$themeColor = '#2c3e50';   // deep slate blue/gray
$bodyClass  = 'fms';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($title) ?> – <?= htmlspecialchars($institute) ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
:root {
  --theme: <?= htmlspecialchars($themeColor) ?>;
  --bg1: #0f172a;
  --bg2: #020617;
  --card-bg: rgba(255,255,255,.12);
  --card-border: rgba(255,255,255,.25);
  --text: #e5e7eb;
}

/* ===== Light mode auto detect ===== */
@media (prefers-color-scheme: light) {
  :root {
    --bg1: #f5f7fb;
    --bg2: #e3e8f0;
    --card-bg: rgba(255,255,255,.65);
    --card-border: rgba(255,255,255,.85);
    --text: #111827;
  }
}

/* ===== FMS subtle pattern ===== */
body.fms { --pattern: radial-gradient(circle at 1px 1px, rgba(44,62,80,.18) 1px, transparent 0); }

/* ===== Animated gradient background ===== */
@keyframes gradientMove {
  0%   { background-position: 0% 50%; }
  50%  { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

body {
  font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
  margin: 0;
  color: var(--text);
  background:
    linear-gradient(-45deg, var(--bg1), var(--bg2), #111827, var(--bg1)),
    var(--pattern);
  background-size: 400% 400%, 26px 26px;
  animation: gradientMove 18s ease infinite;
}

.wrap {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

/* ===== Glassmorphism card ===== */
.box {
  background: var(--card-bg);
  border: 1px solid var(--card-border);
  backdrop-filter: blur(14px) saturate(160%);
  -webkit-backdrop-filter: blur(14px) saturate(160%);
  padding: 42px 52px;
  border-radius: 18px;
  box-shadow: 0 20px 50px rgba(0,0,0,.35);
  max-width: 560px;
  width: 100%;
  text-align: center;
  border-top: 8px solid var(--theme);
}

.logo {
  max-height: 72px;
  width:120px;
  margin-bottom: 18px;
}

h1 {
  font-size: 72px;
  margin: 8px 0 0;
  color: var(--theme);
}

h2 {
  margin: 12px 0 18px;
  font-weight: 600;
}

p {
  color: #cbd5e1;
  line-height: 1.7;
  font-size: 15px;
}

a.btn {
  display: inline-block;
  margin-top: 24px;
  padding: 14px 30px;
  background: linear-gradient(135deg, var(--theme), #000);
  color: #fff;
  border-radius: 999px;
  text-decoration: none;
  font-weight: 700;
  letter-spacing: .3px;
  transition: .25s ease;
  box-shadow: 0 10px 24px rgba(0,0,0,.35);
}

a.btn:hover {
  transform: translateY(-2px) scale(1.02);
  box-shadow: 0 16px 36px rgba(0,0,0,.45);
}

.inst {
  margin-top: 22px;
  font-size: 13px;
  color: #9ca3af;
}
</style>
</head>

<body class="<?= htmlspecialchars($bodyClass) ?>">
<div class="wrap">
  <div class="box">

    <img src="<?= htmlspecialchars($logo) ?>" class="logo" alt="<?= htmlspecialchars($institute) ?>">

    <!--<h1><?= $code ?></h1>
    <h2><?= htmlspecialchars($title) ?></h2> -->

    <p>
      This section of the Fees Management System is currently under development or temporarily unavailable.<br>
      Our academic and technical teams are working to make it accessible soon.<br>
      Please return to the homepage to continue managing your records.
    </p>

    <a href="<?= htmlspecialchars($home) ?>" class="btn">
      Return to <?= htmlspecialchars($institute) ?> ➡︎ Home
    </a>

    <div class="inst"><?= htmlspecialchars($institute) ?></div>

  </div>
</div>
</body>
</html>
