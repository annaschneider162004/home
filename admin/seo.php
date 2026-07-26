<?php
require_once __DIR__ . '/bootstrap.php';
requireLogin();
require_once __DIR__ . '/layout.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $rows = $_POST['seo'] ?? [];
    foreach ($rows as $pageKey => $fields) {
        $pageKey = preg_replace('/[^a-z0-9_]/', '', $pageKey);
        if (!$pageKey) continue;
        $update = [
            'title'            => trim($fields['title'] ?? ''),
            'meta_description' => trim($fields['meta_description'] ?? ''),
            'meta_keywords'    => trim($fields['meta_keywords'] ?? ''),
            'og_image'         => trim($fields['og_image'] ?? ''),
        ];
        // Upsert
        $exists = Database::fetchOne("SELECT id FROM seo_settings WHERE page_key = ?", [$pageKey]);
        if ($exists) {
            Database::update('seo_settings', $update, 'page_key = :pk', ['pk' => $pageKey]);
        } else {
            $update['page_key'] = $pageKey;
            Database::insert('seo_settings', $update);
        }
    }

    // Also handle adding new page
    $newKey = preg_replace('/[^a-z0-9_]/', '', strtolower(trim($_POST['new_key'] ?? '')));
    if ($newKey) {
        $exists = Database::fetchOne("SELECT id FROM seo_settings WHERE page_key = ?", [$newKey]);
        if (!$exists) {
            Database::insert('seo_settings', [
                'page_key'  => $newKey,
                'page_name' => trim($_POST['new_name'] ?? $newKey),
                'title'     => '',
                'meta_description' => '',
                'meta_keywords'    => '',
                'og_image'  => '',
            ]);
        }
    }

    // Regenerate sitemap & robots
    generateSitemap();
    generateRobots();

    setFlash('success', 'Đã lưu cài đặt SEO và cập nhật sitemap.xml, robots.txt.');
    redirect(ADMIN_URL . '/seo.php');
}

$seoPages = Database::fetchAll("SELECT * FROM seo_settings ORDER BY id");

adminHead('Quản Lý SEO');
?>

<div class="page-head">
  <div>
    <h1>Cài Đặt SEO</h1>
    <div class="breadcrumb"><a href="<?= e(ADMIN_URL) ?>/">Dashboard</a> › SEO</div>
  </div>
  <div style="display:flex;gap:.5rem">
    <a href="<?= e(APP_URL) ?>/sitemap.xml" target="_blank" class="btn btn-secondary btn-sm">📄 Sitemap.xml</a>
    <a href="<?= e(APP_URL) ?>/robots.txt" target="_blank" class="btn btn-secondary btn-sm">🤖 Robots.txt</a>
  </div>
</div>

<?php renderFlash(); ?>

<div class="card" style="margin-bottom:1.5rem">
  <div class="card-header">
    <h3>🔍 SEO Theo Trang</h3>
  </div>
  <div class="card-body">
    <p style="color:var(--text-mid);margin-bottom:1.5rem;font-size:.9rem">Cài đặt title, meta description và Open Graph cho từng trang. Nhấn <strong>Lưu Tất Cả</strong> để cập nhật và tái tạo sitemap/robots.txt.</p>
    <form method="POST">
      <?php csrfField(); ?>
      <?php foreach ($seoPages as $row): ?>
      <div style="border:1px solid var(--border);border-radius:var(--radius-sm);padding:1.25rem;margin-bottom:1.25rem">
        <h4 style="margin-bottom:1rem;color:var(--green-dark)">📄 <?= e($row['page_name'] ?? $row['page_key']) ?> <small style="font-weight:400;color:var(--text-light)">(<?= e($row['page_key']) ?>)</small></h4>
        <div class="form-row cols-2">
          <div class="form-group">
            <label class="form-label">Title Tag</label>
            <input type="text" name="seo[<?= e($row['page_key']) ?>][title]" class="form-control"
                   value="<?= e($row['title'] ?? '') ?>" maxlength="255" placeholder="Tiêu đề trang (50–60 ký tự)">
          </div>
          <div class="form-group">
            <label class="form-label">OG Image URL</label>
            <input type="text" name="seo[<?= e($row['page_key']) ?>][og_image]" class="form-control"
                   value="<?= e($row['og_image'] ?? '') ?>" placeholder="https://...">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Meta Description</label>
          <textarea name="seo[<?= e($row['page_key']) ?>][meta_description]" class="form-control" rows="2" maxlength="500" placeholder="Mô tả ngắn 120–160 ký tự"><?= e($row['meta_description'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
          <label class="form-label">Meta Keywords</label>
          <input type="text" name="seo[<?= e($row['page_key']) ?>][meta_keywords]" class="form-control"
                 value="<?= e($row['meta_keywords'] ?? '') ?>" placeholder="từ khóa 1, từ khóa 2, ...">
        </div>
      </div>
      <?php endforeach; ?>

      <div style="border:1px dashed var(--border);border-radius:var(--radius-sm);padding:1.25rem;margin-bottom:1.25rem">
        <h4 style="margin-bottom:1rem;color:var(--text-mid)">+ Thêm Trang Mới</h4>
        <div class="form-row cols-2">
          <div class="form-group">
            <label class="form-label">Key (ký tự thường, không dấu)</label>
            <input type="text" name="new_key" class="form-control" placeholder="vd: about, contact">
          </div>
          <div class="form-group">
            <label class="form-label">Tên Trang</label>
            <input type="text" name="new_name" class="form-control" placeholder="Giới Thiệu">
          </div>
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary">💾 Lưu Tất Cả &amp; Cập Nhật Sitemap</button>
      </div>
    </form>
  </div>
</div>

<?php adminFoot(); ?>
