<?php
/**
 * MusicOfEveryone — Homepage
 */

require_once __DIR__ . '/includes/functions.php';

$seo = getSeo('home');
$siteName    = getSetting('site_name', 'MusicOfEveryone');
$mapEmbed   = getSetting('google_map_embed', '');
$address    = getSetting('site_address', '');
$phone      = getSetting('site_phone', '');
$email      = getSetting('site_email', '');
$facebook   = getSetting('facebook_url', '');
$youtube    = getSetting('youtube_url', '');
$footerText = getSetting('footer_text', '© 2024 MusicOfEveryone');
$ctaUrl     = getSetting('hero_btn_url', '#courses');

$levelBadges = [
    [
        'accent' => 'green',
        'level' => 'Cấp 1',
        'title' => 'Khám phá & Làm quen',
        'age' => '(6-10 tuổi)',
    ],
    [
        'accent' => 'blue',
        'level' => 'Cấp 2',
        'title' => 'Nâng cao kỹ năng',
        'age' => '(11-14 tuổi)',
    ],
    [
        'accent' => 'purple',
        'level' => 'Cấp 3',
        'title' => 'Chuyên sâu & Định hướng',
        'age' => '(15-18 tuổi)',
    ],
];

$features = [
    [
        'icon' => '▶',
        'title' => 'Lộ trình cá nhân hóa',
        'desc' => 'Thiết kế mục tiêu học tập theo độ tuổi, năng lực và nhạc cụ yêu thích của từng học viên.',
    ],
    [
        'icon' => '🖥',
        'title' => 'Học online linh hoạt',
        'desc' => 'Chủ động học mọi lúc, mọi nơi với bài giảng trực tuyến rõ ràng và dễ theo dõi.',
    ],
    [
        'icon' => '👥',
        'title' => 'Giáo viên chất lượng',
        'desc' => 'Đội ngũ giảng viên đồng hành sát sao, truyền cảm hứng và theo sát tiến độ mỗi buổi học.',
    ],
    [
        'icon' => '🏅',
        'title' => 'Nội dung đa dạng',
        'desc' => 'Kết hợp nhạc lý, thực hành, biểu diễn và cảm thụ âm nhạc trong cùng một lộ trình.',
    ],
    [
        'icon' => '↗',
        'title' => 'Theo dõi tiến độ',
        'desc' => 'Cập nhật chặng đường học tập minh bạch để phụ huynh và học viên dễ dàng nắm bắt.',
    ],
];

$infoPanels = [
    [
        'id' => 'instruments',
        'label' => 'Nhạc cụ',
        'title' => 'Piano, guitar, violin và recorder',
        'desc' => 'Danh mục nhạc cụ được giới thiệu đồng bộ để học viên dễ chọn đúng lộ trình theo sở thích.',
    ],
    [
        'id' => 'teachers',
        'label' => 'Giảng viên',
        'title' => 'Đồng hành bởi đội ngũ hướng dẫn chất lượng',
        'desc' => 'Mỗi lớp học đều được kèm cặp bởi giảng viên theo sát kỹ năng, cảm thụ và biểu diễn.',
    ],
    [
        'id' => 'library',
        'label' => 'Thư viện',
        'title' => 'Tài nguyên học tập rõ ràng, dễ theo dõi',
        'desc' => 'Bài tập, bản nhạc và ghi chú luyện tập được sắp xếp theo từng chủ đề học nhạc.',
    ],
    [
        'id' => 'community',
        'label' => 'Cộng đồng',
        'title' => 'Không gian chia sẻ và biểu diễn thân thiện',
        'desc' => 'Học viên có thể kết nối, giao lưu và tự tin thể hiện qua các hoạt động âm nhạc chung.',
    ],
];

$featuredCourseCards = [
    [
        'class' => 'theme-vocals',
        'title' => 'Thanh nhạc',
        'desc' => 'Luyện hơi thở, cảm âm và bản lĩnh sân khấu qua các ca khúc phù hợp lứa tuổi.',
        'badge' => 'Cơ bản đến nâng cao',
        'aria' => 'Minh họa bé gái mặc váy tím hát với micro trên sân khấu',
        'caption' => 'Bé gái hát với micro • váy tím',
    ],
    [
        'class' => 'theme-piano',
        'title' => 'Piano',
        'desc' => 'Khởi đầu vững chắc với tư thế đúng, kỹ thuật tay cơ bản và cảm thụ giai điệu.',
        'badge' => 'Cơ bản đến nâng cao',
        'aria' => 'Minh họa bé trai chơi grand piano với vest đen trắng',
        'caption' => 'Bé trai chơi grand piano • vest đen trắng',
    ],
    [
        'class' => 'theme-violin',
        'title' => 'Violin',
        'desc' => 'Rèn luyện âm sắc, tư thế kéo vĩ và khả năng biểu cảm qua từng bài học.',
        'badge' => 'Cơ bản',
        'aria' => 'Minh họa bé gái chơi violin ngoài trời với váy nâu',
        'caption' => 'Bé gái chơi violin • váy nâu',
    ],
    [
        'class' => 'theme-recorder',
        'title' => 'Sáo Recorder',
        'desc' => 'Học thổi sáo nhẹ nhàng, đọc nốt và phối hợp nhịp điệu trong môi trường vui nhộn.',
        'badge' => 'Cơ bản',
        'aria' => 'Minh họa bé trai thổi sáo Recorder với áo xanh lá',
        'caption' => 'Bé trai thổi sáo recorder • áo xanh lá',
    ],
];

$pageTitle = $seo['title'] ?? "$siteName - Học nhạc cho mọi lứa tuổi";
$metaDesc  = $seo['meta_description'] ?? '';
$metaKw    = $seo['meta_keywords'] ?? '';
$ogImage   = $seo['og_image'] ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle) ?></title>
  <?php if ($metaDesc): ?><meta name="description" content="<?= e($metaDesc) ?>"><?php endif; ?>
  <?php if ($metaKw): ?><meta name="keywords" content="<?= e($metaKw) ?>"><?php endif; ?>
  <meta property="og:title" content="<?= e($pageTitle) ?>">
  <meta property="og:description" content="<?= e($metaDesc) ?>">
  <meta property="og:type" content="website">
  <?php if ($ogImage): ?><meta property="og:image" content="<?= e($ogImage) ?>"><?php endif; ?>
  <link rel="canonical" href="<?= e(APP_URL) ?>/">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
</head>
<body>

<header id="header">
  <div class="container">
    <div class="header-inner">
      <a href="/" class="logo">
        <span class="logo-icon" aria-hidden="true">♪</span>
        <span class="logo-text">
          <strong>MusicOfEveryone</strong>
          <small>MUSIC CLUB</small>
        </span>
      </a>

      <nav id="primary-nav" aria-label="Điều hướng chính">
        <ul>
          <li><a href="#hero" class="active">Trang chủ</a></li>
          <li><a href="#courses">Khóa học</a></li>
          <li><a href="#instruments">Nhạc cụ</a></li>
          <li><a href="#teachers">Giảng viên</a></li>
          <li><a href="#levels">Lộ trình</a></li>
          <li><a href="#library">Thư viện</a></li>
          <li><a href="#community">Cộng đồng</a></li>
          <li><a href="#about">Về chúng tôi</a></li>
        </ul>
      </nav>

      <div class="header-actions">
        <a href="/admin/login.php" class="btn btn-outline-dark">Đăng nhập</a>
        <a href="#courses" class="btn btn-primary">Đăng ký</a>
      </div>

      <button class="hamburger" aria-label="Mở menu điều hướng" aria-controls="primary-nav" type="button">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</header>

<section id="hero">
  <div class="container">
    <div class="hero-inner">
      <div class="hero-copy">
        <h1>
          <span>HỌC NHẠC</span>
          <span>CHO MỌI LỨA TUỔI</span>
        </h1>
        <p>Từ cơ bản đến nâng cao – Lộ trình rõ ràng – Học mọi lúc, mọi nơi</p>
        <a href="<?= e($ctaUrl) ?>" class="btn btn-primary hero-cta">Bắt đầu hành trình <span aria-hidden="true">→</span></a>
      </div>

      <div class="hero-showcase">
        <span class="hero-orb hero-orb-green" aria-hidden="true"></span>
        <span class="hero-orb hero-orb-purple" aria-hidden="true"></span>
        <span class="floating-note note-one" aria-hidden="true">♪</span>
        <span class="floating-note note-two" aria-hidden="true">♫</span>
        <span class="floating-note note-three" aria-hidden="true">♬</span>

        <div class="hero-character hero-guitar" role="img" aria-label="Bé trai chơi guitar mặc áo hoodie vàng cam">
          <span class="character-badge">Guitar</span>
          <span class="character-caption">Bé trai • hoodie vàng cam</span>
        </div>
        <div class="hero-character hero-keyboard" role="img" aria-label="Bé gái chơi keyboard mặc áo hồng">
          <span class="character-badge">Keyboard</span>
          <span class="character-caption">Bé gái • áo hồng</span>
        </div>
        <div class="hero-character hero-laptop" role="img" aria-label="Bạn nam tóc xoăn đeo kính dùng laptop mặc áo hoodie đen">
          <span class="character-badge">Laptop</span>
          <span class="character-caption">Bạn nam • hoodie đen</span>
        </div>
      </div>
    </div>
  </div>
</section>

<section id="levels">
  <div class="container">
    <div class="levels-list">
      <?php foreach ($levelBadges as $badge): ?>
        <article class="level-pill level-<?= e($badge['accent']) ?>">
          <span class="level-circle"><?= e($badge['level']) ?></span>
          <div class="level-copy">
            <strong><?= e($badge['title']) ?></strong>
            <span><?= e($badge['age']) ?></span>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section id="features">
  <div class="container">
    <div class="features-grid">
      <?php foreach ($features as $feature): ?>
        <article class="feature-card">
          <div class="feature-icon" aria-hidden="true"><?= e($feature['icon']) ?></div>
          <h2><?= e($feature['title']) ?></h2>
          <p><?= e($feature['desc']) ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="info-panels-section">
  <div class="container">
    <div class="info-panels-grid">
      <?php foreach ($infoPanels as $panel): ?>
        <article id="<?= e($panel['id']) ?>" class="info-panel-card">
          <span class="info-panel-label"><?= e($panel['label']) ?></span>
          <h2><?= e($panel['title']) ?></h2>
          <p><?= e($panel['desc']) ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section id="courses">
  <div class="container">
    <div class="section-head">
      <h2 class="section-title-inline">🎵 KHÓA HỌC NỔI BẬT</h2>
      <a href="#footer" class="section-link">Xem tất cả khóa học <span aria-hidden="true">→</span></a>
    </div>

    <div class="featured-courses-grid">
      <?php foreach ($featuredCourseCards as $course): ?>
        <article class="featured-course-card <?= e($course['class']) ?>" aria-label="<?= e($course['aria']) ?>">
          <div class="featured-course-overlay"></div>
          <span class="featured-course-badge"><?= e($course['badge']) ?></span>
          <div class="featured-course-content">
            <small><?= e($course['caption']) ?></small>
            <h3><?= e($course['title']) ?></h3>
            <p><?= e($course['desc']) ?></p>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<footer id="footer">
  <div class="container">
    <div class="footer-top">
      <div class="footer-brand" id="about">
        <a href="/" class="logo">
          <span class="logo-icon" aria-hidden="true">♪</span>
          <span class="logo-text">
            <strong>MusicOfEveryone</strong>
            <small>MUSIC CLUB</small>
          </span>
        </a>
        <p>MusicOfEveryone mang đến lộ trình học nhạc thân thiện, rõ ràng và truyền cảm hứng cho mọi lứa tuổi.</p>
        <div class="footer-socials">
          <?php if ($facebook): ?><a href="<?= e($facebook) ?>" target="_blank" rel="noopener" title="Facebook">f</a><?php endif; ?>
          <?php if ($youtube): ?><a href="<?= e($youtube) ?>" target="_blank" rel="noopener" title="YouTube">▶</a><?php endif; ?>
          <?php if ($email): ?><a href="mailto:<?= e($email) ?>" title="Email">✉</a><?php endif; ?>
        </div>
      </div>

      <div class="footer-col">
        <h4>Khóa học</h4>
        <ul>
          <li><a href="#courses">Thanh nhạc</a></li>
          <li><a href="#courses">Piano</a></li>
          <li><a href="#courses">Violin</a></li>
          <li><a href="#courses">Sáo Recorder</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h4>Lộ trình</h4>
        <ul>
          <li><a href="#levels">Cấp 1</a></li>
          <li><a href="#levels">Cấp 2</a></li>
          <li><a href="#levels">Cấp 3</a></li>
          <li><a href="/admin/">Quản trị</a></li>
        </ul>
      </div>

      <div class="footer-col" id="contact">
        <h4>Liên hệ</h4>
        <ul class="footer-contact">
          <?php if ($address): ?><li><span>📍</span><span><?= e($address) ?></span></li><?php endif; ?>
          <?php if ($phone): ?>
            <li>
              <span>📞</span>
              <span>
                <a href="tel:<?= e(preg_replace('/\s+/', '', $phone)) ?>"><?= e($phone) ?></a>
              </span>
            </li>
          <?php endif; ?>
          <?php if ($email): ?><li><span>✉</span><span><a href="mailto:<?= e($email) ?>"><?= e($email) ?></a></span></li><?php endif; ?>
        </ul>
      </div>
    </div>

    <?php if ($mapEmbed): ?>
      <div class="footer-map">
        <h4>📍 Bản đồ đường đến</h4>
        <div class="map-embed">
          <?= sanitizeMapEmbed($mapEmbed) ?>
        </div>
      </div>
    <?php endif; ?>

    <div class="footer-bottom">
      <span><?= e($footerText) ?></span>
      <span><a href="/sitemap.xml">Sitemap</a> · <a href="/robots.txt">Robots.txt</a></span>
    </div>
  </div>
</footer>

<button id="scroll-top" aria-label="Lên đầu trang">↑</button>

<script src="assets/js/main.js"></script>
</body>
</html>
