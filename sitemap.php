<?php
/**
 * Dynamic sitemap.xml generator
 * Access via: /sitemap.php or configure .htaccess to serve this as sitemap.xml
 */
require_once __DIR__ . '/includes/functions.php';

header('Content-Type: application/xml; charset=UTF-8');

$baseUrl = rtrim(getSetting('app_url', APP_URL), '/');
$courses = Database::fetchAll("SELECT slug, updated_at FROM courses WHERE status='active'");
$posts   = Database::fetchAll("SELECT slug, updated_at FROM posts WHERE status='published'");

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc><?= htmlspecialchars($baseUrl) ?>/</loc>
    <lastmod><?= date('Y-m-d') ?></lastmod>
    <changefreq>daily</changefreq>
    <priority>1.0</priority>
  </url>
  <?php foreach ($courses as $c): ?>
  <url>
    <loc><?= htmlspecialchars($baseUrl) ?>/course/<?= htmlspecialchars($c['slug']) ?></loc>
    <lastmod><?= date('Y-m-d', strtotime($c['updated_at'])) ?></lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
  </url>
  <?php endforeach; ?>
  <?php foreach ($posts as $p): ?>
  <url>
    <loc><?= htmlspecialchars($baseUrl) ?>/post/<?= htmlspecialchars($p['slug']) ?></loc>
    <lastmod><?= date('Y-m-d', strtotime($p['updated_at'])) ?></lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>
  <?php endforeach; ?>
</urlset>
