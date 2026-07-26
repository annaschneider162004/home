<?php
/**
 * Admin shared layout helpers
 * adminHead($title)  — outputs <head> open to content start
 * adminFoot()        — outputs closing tags
 * adminSidebar($active) — outputs sidebar nav
 */

function adminHead(string $title = 'Dashboard'): void {
    $siteName = getSetting('site_name', 'MusicOfEveryone');
    $user = adminUser();
    // Auto-detect active nav from current script filename
    $script = basename($_SERVER['PHP_SELF'] ?? '', '.php');
    $activeMap = ['index'=>'dashboard','courses'=>'courses','teachers'=>'teachers','students'=>'students','posts'=>'posts','seo'=>'seo','settings'=>'settings'];
    $active = $activeMap[$script] ?? 'dashboard';
    echo '<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>' . e($title) . ' — Admin ' . e($siteName) . '</title>
  <link rel="stylesheet" href="' . e(ADMIN_URL) . '/../assets/css/admin.css">
  <meta name="robots" content="noindex,nofollow">
</head>
<body>
<div class="admin-layout">';
    adminSidebar($active);
    echo '<div class="main-wrap">
  <div class="topbar">
    <div style="display:flex;align-items:center;gap:.75rem">
      <button id="sidebar-toggle" class="btn btn-secondary btn-sm" style="display:none" aria-label="Menu">☰</button>
      <span class="topbar-title">' . e($title) . '</span>
    </div>
    <div class="topbar-user">
      <span>👤 ' . e($user['name']) . '</span>
      <div class="topbar-avatar">' . mb_strtoupper(mb_substr($user['name'],0,1)) . '</div>
      <a href="' . e(ADMIN_URL) . '/logout.php" class="btn btn-secondary btn-sm">Đăng xuất</a>
    </div>
  </div>
  <div class="content-area">';
}

function adminFoot(): void {
    echo '  </div><!-- /content-area -->
</div><!-- /main-wrap -->
</div><!-- /admin-layout -->
<script src="' . e(ADMIN_URL) . '/../assets/js/main.js"></script>
<script>document.getElementById("sidebar-toggle").style.display="flex";</script>
</body>
</html>';
}

function adminSidebar(string $active = ''): void {
    $base = ADMIN_URL;
    $links = [
        'dashboard' => ['url' => "$base/", 'icon' => '📊', 'label' => 'Dashboard'],
        'courses'   => ['url' => "$base/courses.php", 'icon' => '🎹', 'label' => 'Khóa Học'],
        'teachers'  => ['url' => "$base/teachers.php", 'icon' => '👨‍🏫', 'label' => 'Giảng Viên'],
        'students'  => ['url' => "$base/students.php", 'icon' => '🎓', 'label' => 'Học Viên'],
        'posts'     => ['url' => "$base/posts.php", 'icon' => '📝', 'label' => 'Bài Viết'],
        'seo'       => ['url' => "$base/seo.php", 'icon' => '🔍', 'label' => 'SEO'],
        'settings'  => ['url' => "$base/settings.php", 'icon' => '⚙️', 'label' => 'Cài Đặt'],
    ];
    $siteName = getSetting('site_name', 'MusicOfEveryone');
    echo '<aside class="sidebar">
  <div class="sidebar-brand">
    <div class="logo-icon">🎵</div>
    <div><h2>Admin</h2><span>' . e($siteName) . '</span></div>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section">Quản Trị</div>';
    foreach ($links as $key => $link) {
        $cls = $key === $active ? ' active' : '';
        echo '<a href="' . e($link['url']) . '" class="' . trim($cls) . '">
      <span class="nav-icon">' . $link['icon'] . '</span> ' . e($link['label']) . '
    </a>';
    }
    echo '</nav>
  <div class="sidebar-footer">
    <a href="/" target="_blank">🌐 Xem Trang Chủ</a>
    <a href="' . e(ADMIN_URL) . '/logout.php">🚪 Đăng Xuất</a>
  </div>
</aside>';
}
