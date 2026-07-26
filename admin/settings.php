<?php
require_once __DIR__ . '/bootstrap.php';
requireLogin();
require_once __DIR__ . '/layout.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $postAction = $_POST['post_action'] ?? 'settings';

    if ($postAction === 'password') {
        // Change admin password
        $currentPwd = $_POST['current_password'] ?? '';
        $newPwd     = $_POST['new_password'] ?? '';
        $confirmPwd = $_POST['confirm_password'] ?? '';
        $user = Database::fetchOne("SELECT * FROM users WHERE id = ?", [adminUser()['id']]);
        if (!password_verify($currentPwd, $user['password'])) {
            setFlash('error', 'Mật khẩu hiện tại không đúng.');
        } elseif (strlen($newPwd) < 8) {
            setFlash('error', 'Mật khẩu mới phải có ít nhất 8 ký tự.');
        } elseif ($newPwd !== $confirmPwd) {
            setFlash('error', 'Xác nhận mật khẩu không khớp.');
        } else {
            Database::update('users', ['password' => password_hash($newPwd, PASSWORD_DEFAULT)], 'id = :id', ['id' => adminUser()['id']]);
            setFlash('success', 'Đã đổi mật khẩu thành công.');
        }
        redirect(ADMIN_URL . '/settings.php?tab=password');
    }

    // Allowed settings keys per group (whitelist)
    $allowedKeys = [
        'site_name','site_slogan','footer_text',
        'hero_title','hero_subtitle','hero_btn_text','hero_btn_url',
        'site_address','site_phone','site_email','google_map_embed',
        'facebook_url','youtube_url',
    ];

    // Save site settings
    $settingsToSave = $_POST['settings'] ?? [];
    foreach ($settingsToSave as $key => $value) {
        // Sanitize key: lowercase alphanumeric + underscore, then check whitelist
        $key = strtolower(preg_replace('/[^a-z0-9_]/i', '', $key));
        if (!$key || !in_array($key, $allowedKeys, true)) continue;
        $exists = Database::fetchOne("SELECT id FROM site_settings WHERE setting_key = ?", [$key]);
        if ($exists) {
            Database::update('site_settings', ['setting_value' => trim($value)], 'setting_key = :k', ['k' => $key]);
        }
    }

    // Regenerate sitemap & robots after settings change
    generateSitemap();
    generateRobots();

    setFlash('success', 'Đã lưu cài đặt thành công.');
    redirect(ADMIN_URL . '/settings.php');
}

$settings = getAllSettings();
$tab = $_GET['tab'] ?? 'general';

adminHead('Cài Đặt Website');
?>

<div class="page-head">
  <div>
    <h1>Cài Đặt Website</h1>
    <div class="breadcrumb"><a href="<?= e(ADMIN_URL) ?>/">Dashboard</a> › Cài Đặt</div>
  </div>
</div>

<?php renderFlash(); ?>

<!-- Tab nav -->
<div style="display:flex;gap:.5rem;margin-bottom:1.5rem;border-bottom:2px solid var(--border);padding-bottom:0">
  <?php
  $tabs = ['general'=>'⚙️ Tổng Quan','homepage'=>'🏠 Trang Chủ','contact'=>'📍 Liên Hệ & Bản Đồ','social'=>'📱 Mạng Xã Hội','password'=>'🔒 Đổi Mật Khẩu'];
  foreach ($tabs as $k=>$label):
  ?>
  <a href="?tab=<?= $k ?>" style="padding:.6rem 1.25rem;border-bottom:2px solid <?= $tab===$k?'var(--green)':'transparent' ?>;margin-bottom:-2px;font-weight:<?= $tab===$k?'700':'500' ?>;color:<?= $tab===$k?'var(--green)':'var(--text-mid)' ?>;font-size:.9rem;white-space:nowrap"><?= $label ?></a>
  <?php endforeach; ?>
</div>

<?php if ($tab === 'password'): ?>
<!-- PASSWORD TAB -->
<div class="card">
  <div class="card-header"><h3>🔒 Đổi Mật Khẩu Admin</h3></div>
  <div class="card-body">
    <form method="POST" style="max-width:480px">
      <?php csrfField(); ?>
      <input type="hidden" name="post_action" value="password">
      <div class="form-group">
        <label class="form-label">Mật Khẩu Hiện Tại <span class="required">*</span></label>
        <input type="password" name="current_password" class="form-control" required>
      </div>
      <div class="form-group">
        <label class="form-label">Mật Khẩu Mới <span class="required">*</span></label>
        <input type="password" name="new_password" class="form-control" required minlength="8">
        <div class="form-hint">Tối thiểu 8 ký tự.</div>
      </div>
      <div class="form-group">
        <label class="form-label">Xác Nhận Mật Khẩu Mới <span class="required">*</span></label>
        <input type="password" name="confirm_password" class="form-control" required minlength="8">
      </div>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary">💾 Đổi Mật Khẩu</button>
      </div>
    </form>
  </div>
</div>

<?php else: ?>
<!-- SETTINGS TABS -->
<div class="card">
  <div class="card-header"><h3><?= $tabs[$tab] ?? '' ?></h3></div>
  <div class="card-body">
    <form method="POST">
      <?php csrfField(); ?>
      <input type="hidden" name="post_action" value="settings">

      <?php
      $groups = [
        'general' => ['site_name','site_slogan','footer_text'],
        'homepage' => ['hero_title','hero_subtitle','hero_btn_text','hero_btn_url'],
        'contact'  => ['site_address','site_phone','site_email','google_map_embed'],
        'social'   => ['facebook_url','youtube_url'],
      ];
      $labels = [
        'site_name'       => ['Tên Website','text'],
        'site_slogan'     => ['Slogan','text'],
        'footer_text'     => ['Chữ Footer','text'],
        'hero_title'      => ['Tiêu Đề Hero','text'],
        'hero_subtitle'   => ['Mô Tả Hero','textarea'],
        'hero_btn_text'   => ['Nút Hero: Văn bản','text'],
        'hero_btn_url'    => ['Nút Hero: URL','text'],
        'site_address'    => ['Địa Chỉ','text'],
        'site_phone'      => ['Số Điện Thoại','text'],
        'site_email'      => ['Email','email'],
        'google_map_embed'=> ['Google Map Embed (iframe HTML)','textarea'],
        'facebook_url'    => ['Facebook URL','url'],
        'youtube_url'     => ['YouTube URL','url'],
      ];
      $keys = $groups[$tab] ?? [];
      foreach ($keys as $key):
        [$lbl, $type] = $labels[$key] ?? [$key, 'text'];
        $val = $settings[$key] ?? '';
      ?>
      <div class="form-group">
        <label class="form-label"><?= e($lbl) ?></label>
        <?php if ($type === 'textarea'): ?>
          <textarea name="settings[<?= e($key) ?>]" class="form-control" rows="<?= $key==='google_map_embed'?6:3 ?>"><?= e($val) ?></textarea>
          <?php if ($key === 'google_map_embed'): ?>
          <div class="form-hint">Dán mã nhúng &lt;iframe&gt; từ Google Maps. <a href="https://maps.google.com" target="_blank">→ Mở Google Maps</a></div>
          <?php endif; ?>
        <?php else: ?>
          <input type="<?= e($type) ?>" name="settings[<?= e($key) ?>]" class="form-control" value="<?= e($val) ?>">
        <?php endif; ?>
      </div>
      <?php endforeach; ?>

      <?php if ($tab === 'contact' && !empty($settings['google_map_embed'])): ?>
      <div class="form-group">
        <label class="form-label">Xem Trước Bản Đồ</label>
        <div style="border:1px solid var(--border);border-radius:var(--radius-sm);overflow:hidden">
          <?= sanitizeMapEmbed($settings['google_map_embed']) ?>
        </div>
      </div>
      <?php endif; ?>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary">💾 Lưu Cài Đặt</button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<?php adminFoot(); ?>
