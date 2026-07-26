<?php
/**
 * Helper functions
 */

require_once __DIR__ . '/db.php';

// -------------------------------------------------------
// Output escaping
// -------------------------------------------------------
function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

// -------------------------------------------------------
// Site settings
// -------------------------------------------------------
function getSetting(string $key, string $default = ''): string {
    static $cache = [];
    if (!isset($cache[$key])) {
        $row = Database::fetchOne("SELECT setting_value FROM site_settings WHERE setting_key = ?", [$key]);
        $cache[$key] = $row ? (string)$row['setting_value'] : $default;
    }
    return $cache[$key];
}

function getAllSettings(): array {
    $rows = Database::fetchAll("SELECT setting_key, setting_value FROM site_settings");
    $out = [];
    foreach ($rows as $r) $out[$r['setting_key']] = $r['setting_value'];
    return $out;
}

// -------------------------------------------------------
// SEO settings
// -------------------------------------------------------
function getSeo(string $pageKey): array {
    $row = Database::fetchOne("SELECT * FROM seo_settings WHERE page_key = ?", [$pageKey]);
    return $row ?: ['title' => getSetting('site_name'), 'meta_description' => '', 'meta_keywords' => '', 'og_image' => ''];
}

// -------------------------------------------------------
// Slug generation
// -------------------------------------------------------
function makeSlug(string $str): string {
    $str = mb_strtolower(trim($str), 'UTF-8');
    $map = [
        'à'=>'a','á'=>'a','ả'=>'a','ã'=>'a','ạ'=>'a',
        'ă'=>'a','ằ'=>'a','ắ'=>'a','ẳ'=>'a','ẵ'=>'a','ặ'=>'a',
        'â'=>'a','ầ'=>'a','ấ'=>'a','ẩ'=>'a','ẫ'=>'a','ậ'=>'a',
        'đ'=>'d',
        'è'=>'e','é'=>'e','ẻ'=>'e','ẽ'=>'e','ẹ'=>'e',
        'ê'=>'e','ề'=>'e','ế'=>'e','ể'=>'e','ễ'=>'e','ệ'=>'e',
        'ì'=>'i','í'=>'i','ỉ'=>'i','ĩ'=>'i','ị'=>'i',
        'ò'=>'o','ó'=>'o','ỏ'=>'o','õ'=>'o','ọ'=>'o',
        'ô'=>'o','ồ'=>'o','ố'=>'o','ổ'=>'o','ỗ'=>'o','ộ'=>'o',
        'ơ'=>'o','ờ'=>'o','ớ'=>'o','ở'=>'o','ỡ'=>'o','ợ'=>'o',
        'ù'=>'u','ú'=>'u','ủ'=>'u','ũ'=>'u','ụ'=>'u',
        'ư'=>'u','ừ'=>'u','ứ'=>'u','ử'=>'u','ữ'=>'u','ự'=>'u',
        'ỳ'=>'y','ý'=>'y','ỷ'=>'y','ỹ'=>'y','ỵ'=>'y',
    ];
    $str = strtr($str, $map);
    $str = preg_replace('/[^a-z0-9\s-]/', '', $str);
    $str = preg_replace('/[\s-]+/', '-', $str);
    return trim($str, '-');
}

// -------------------------------------------------------
// Pagination
// -------------------------------------------------------
function paginate(string $table, string $where = '1', array $params = [], int $page = 1, int $perPage = 10): array {
    $total = (int)Database::fetchScalar("SELECT COUNT(*) FROM `$table` WHERE $where", $params);
    $totalPages = max(1, (int)ceil($total / $perPage));
    $page = max(1, min($page, $totalPages));
    $offset = ($page - 1) * $perPage;
    return [
        'total' => $total,
        'page' => $page,
        'perPage' => $perPage,
        'totalPages' => $totalPages,
        'offset' => $offset,
    ];
}

// -------------------------------------------------------
// Flash messages (session-based)
// -------------------------------------------------------
function setFlash(string $type, string $message): void {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function renderFlash(): void {
    $flash = getFlash();
    if (!$flash) return;
    $type = $flash['type'] === 'success' ? 'success' : ($flash['type'] === 'error' ? 'danger' : 'warning');
    echo '<div class="alert alert-' . e($type) . ' alert-dismissible">' . e($flash['message']) . '<button class="alert-close" onclick="this.parentElement.remove()">×</button></div>';
}

// -------------------------------------------------------
// CSRF
// -------------------------------------------------------
function csrfToken(): string {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField(): void {
    echo '<input type="hidden" name="csrf_token" value="' . e(csrfToken()) . '">';
}

function verifyCsrf(): void {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        die('CSRF token không hợp lệ.');
    }
}

// -------------------------------------------------------
// Redirect
// -------------------------------------------------------
function redirect(string $url): never {
    header('Location: ' . $url);
    exit;
}

// -------------------------------------------------------
// Level labels
// -------------------------------------------------------
function levelLabel(string $level): string {
    return match ($level) {
        'co_ban'         => 'Cơ Bản',
        'trung_cap'      => 'Trung Cấp',
        'nang_cao'       => 'Nâng Cao',
        'chuyen_nghiep'  => 'Chuyên Nghiệp',
        default          => $level,
    };
}

function levelColor(string $level): string {
    return match ($level) {
        'co_ban'         => 'green',
        'trung_cap'      => 'blue',
        'nang_cao'       => 'orange',
        'chuyen_nghiep'  => 'purple',
        default          => 'gray',
    };
}

// -------------------------------------------------------
// Format price (VND)
// -------------------------------------------------------
function formatPrice(float $price): string {
    if ($price <= 0) return 'Miễn Phí';
    return number_format($price, 0, ',', '.') . ' ₫';
}

// -------------------------------------------------------
// Sanitize Google Map embed (only allow Google Maps iframes)
// -------------------------------------------------------
function sanitizeMapEmbed(string $html): string {
    if ($html === '') return '';
    // Only allow a single <iframe> from Google Maps domains
    if (!preg_match('/<iframe\s[^>]*src=["\']https:\/\/(www\.google\.com\/maps\/|maps\.google\.com\/)[^"\']*["\'][^>]*><\/iframe>/i', $html)) {
        return ''; // Not a trusted Google Maps iframe — discard
    }
    // Strip everything except the iframe tag itself
    $clean = preg_replace('/<(?!iframe\s)[^>]+>/i', '', $html);
    // Remove event handlers and javascript: in remaining content
    $clean = preg_replace('/\s+on\w+\s*=\s*["\'][^"\']*["\']/i', '', $clean ?? '');
    $clean = preg_replace('/javascript\s*:/i', '', $clean ?? '');
    return $clean ?? '';
}


function generateSitemap(): void {
    $baseUrl = rtrim(getSetting('app_url', APP_URL), '/');
    $courses = Database::fetchAll("SELECT slug, updated_at FROM courses WHERE status='active' ORDER BY id");
    $posts   = Database::fetchAll("SELECT slug, updated_at FROM posts WHERE status='published' ORDER BY id");

    $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    $addUrl = function(string $loc, string $lastmod = '', string $changefreq = 'weekly', string $priority = '0.8') use (&$xml) {
        $xml .= "  <url>\n";
        $xml .= "    <loc>" . htmlspecialchars($loc) . "</loc>\n";
        if ($lastmod) $xml .= "    <lastmod>$lastmod</lastmod>\n";
        $xml .= "    <changefreq>$changefreq</changefreq>\n";
        $xml .= "    <priority>$priority</priority>\n";
        $xml .= "  </url>\n";
    };

    $addUrl($baseUrl . '/', date('Y-m-d'), 'daily', '1.0');

    foreach ($courses as $c) {
        $addUrl($baseUrl . '/course/' . $c['slug'], date('Y-m-d', strtotime($c['updated_at'] ?? 'now')));
    }
    foreach ($posts as $p) {
        $addUrl($baseUrl . '/post/' . $p['slug'], date('Y-m-d', strtotime($p['updated_at'] ?? 'now')));
    }

    $xml .= '</urlset>';
    file_put_contents(BASE_PATH . '/sitemap.xml', $xml);
}

// -------------------------------------------------------
// Robots.txt generation
// -------------------------------------------------------
function generateRobots(): void {
    $baseUrl = rtrim(getSetting('app_url', APP_URL), '/');
    $content  = "User-agent: *\n";
    $content .= "Disallow: /admin/\n";
    $content .= "Disallow: /config/\n";
    $content .= "Disallow: /includes/\n";
    $content .= "Allow: /\n\n";
    $content .= "Sitemap: $baseUrl/sitemap.xml\n";
    file_put_contents(BASE_PATH . '/robots.txt', $content);
}
