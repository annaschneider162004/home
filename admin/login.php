<?php
require_once __DIR__ . '/bootstrap.php';

// Already logged in → redirect to dashboard
if (isLoggedIn()) {
    redirect(ADMIN_URL . '/');
}

$error = '';
$redirect = $_GET['redirect'] ?? (ADMIN_URL . '/');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu.';
    } else {
        $user = Database::fetchOne("SELECT * FROM users WHERE username = ? LIMIT 1", [$username]);
        if ($user && password_verify($password, $user['password'])) {
            // Login success
            session_regenerate_id(true);
            $_SESSION['admin_id']   = $user['id'];
            $_SESSION['admin_user'] = $user['username'];
            $_SESSION['admin_name'] = $user['full_name'] ?? $user['username'];
            $_SESSION['admin_role'] = $user['role'];
            $_SESSION['last_regen'] = time();
            redirect($redirect);
        } else {
            $error = 'Tên đăng nhập hoặc mật khẩu không đúng.';
            // Small delay to slow brute force
            sleep(1);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đăng Nhập Admin — <?= e(getSetting('site_name','MusicOfEveryone')) ?></title>
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="login-wrap">
  <div class="login-box">
    <div class="login-logo">
      <div class="logo-icon">🎵</div>
      <h1>MusicOfEveryone</h1>
      <p>Trang Quản Trị</p>
    </div>
    <?php if ($error): ?>
      <div class="alert alert-danger" data-auto-close><?= e($error) ?></div>
    <?php endif; ?>
    <form method="POST" action="">
      <?php csrfField(); ?>
      <input type="hidden" name="redirect" value="<?= e($redirect) ?>">
      <div class="form-group">
        <label class="form-label" for="username">Tên đăng nhập <span class="required">*</span></label>
        <input type="text" id="username" name="username" class="form-control" required
               value="<?= e($_POST['username'] ?? '') ?>" autocomplete="username" placeholder="admin">
      </div>
      <div class="form-group">
        <label class="form-label" for="password">Mật khẩu <span class="required">*</span></label>
        <input type="password" id="password" name="password" class="form-control" required
               autocomplete="current-password" placeholder="••••••••">
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:.75rem">
        🔐 Đăng Nhập
      </button>
    </form>
    <p style="text-align:center;margin-top:1.5rem;font-size:.82rem;color:var(--text-light)">
      <a href="/">← Quay về trang chủ</a>
    </p>
  </div>
</div>
<script src="../assets/js/main.js"></script>
</body>
</html>
