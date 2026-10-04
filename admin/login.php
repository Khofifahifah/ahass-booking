<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/layout.php';

if (!empty($_SESSION['admin_id'])) {
    redirect(BASE_URL . '/admin/index.php');
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $stmt = pdo()->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['admin_id'] = (int) $user['id'];
        $_SESSION['admin_nama'] = $user['nama'];
        redirect(BASE_URL . '/admin/index.php');
    }
    $error = 'Username atau password salah.';
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin · AHASS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(BASE_URL) ?>/assets/css/style.css">
</head>
<body class="login-wrap">
    <form class="panel login-card" method="post">
        <h2>Masuk panel AHASS</h2>
        <p class="muted">Kelola pendaftaran, slot jam, dan paket servis.</p>
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
        <div class="field">
            <label for="username">Username</label>
            <input id="username" name="username" value="admin" required>
        </div>
        <div class="field">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" required>
        </div>
        <button class="btn" type="submit">Masuk</button>
        <p class="muted" style="margin-top:12px">Default: admin / admin123</p>
        <p><a href="<?= e(BASE_URL) ?>/index.php">Kembali ke situs</a></p>
    </form>
</body>
</html>
