<?php
/**
 * MusicOfEveryone — Homepage
 */

require_once __DIR__ . '/includes/functions.php';

$seo = getSeo('home');
$siteName    = getSetting('site_name', 'MusicOfEveryone');
$heroTitle   = getSetting('hero_title', 'Âm Nhạc Dành Cho Tất Cả');
$heroSub     = getSetting('hero_subtitle', 'Khám phá niềm đam mê âm nhạc của bạn với đội ngũ giảng viên chuyên nghiệp.');
$heroBtnText = getSetting('hero_btn_text', 'Đăng Ký Ngay');
$heroBtnUrl  = getSetting('hero_btn_url', '#courses');
$mapEmbed    = getSetting('google_map_embed', '');
$address     = getSetting('site_address', '');
$phone       = getSetting('site_phone', '');
$email       = getSetting('site_email', '');
$facebook    = getSetting('facebook_url', '');
$youtube     = getSetting('youtube_url', '');
$footerText  = getSetting('footer_text', '© 2024 MusicOfEveryone');

// Featured courses
$featuredCourses = Database::fetchAll("
    SELECT c.*, t.name AS teacher_name
    FROM courses c
    LEFT JOIN teachers t ON c.teacher_id = t.id
    WHERE c.featured = 1 AND c.status = 'active'
    ORDER BY c.id DESC
    LIMIT 6
");

// Stats
$totalStudents = (int)Database::fetchScalar("SELECT COUNT(*) FROM students WHERE status != 'inactive'");
$totalCourses  = (int)Database::fetchScalar("SELECT COUNT(*) FROM courses WHERE status = 'active'");
$totalTeachers = (int)Database::fetchScalar("SELECT COUNT(*) FROM teachers WHERE status = 'active'");

$pageTitle = $seo['title'] ?? "$siteName - Âm Nhạc Dành Cho Tất Cả";
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
  <!-- Open Graph -->
  <meta property="og:title" content="<?= e($pageTitle) ?>">
  <meta property="og:description" content="<?= e($metaDesc) ?>">
  <meta property="og:type" content="website">
  <?php if ($ogImage): ?><meta property="og:image" content="<?= e($ogImage) ?>"><?php endif; ?>
  <!-- Canonical -->
  <link rel="canonical" href="<?= e(APP_URL) ?>/">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
</head>
<body>

<!-- ===== HEADER ===== -->
<header id="header">
  <div class="container">
    <div class="header-inner">
      <a href="/" class="logo">
        <div class="logo-icon">🎵</div>
        Music<span>OfEveryone</span>
      </a>
      <nav>
        <ul>
          <li><a href="/" class="active">Trang Chủ</a></li>
          <li><a href="#levels">Lộ Trình</a></li>
          <li><a href="#courses">Khóa Học</a></li>
          <li><a href="#features">Tính Năng</a></li>
          <li><a href="#contact">Liên Hệ</a></li>
        </ul>
      </nav>
      <a href="#courses" class="btn btn-primary nav-cta">Đăng Ký Học</a>
      <div class="hamburger" aria-label="Menu">
        <span></span><span></span><span></span>
      </div>
    </div>
  </div>
</header>

<!-- ===== HERO ===== -->
<section id="hero">
  <div class="container">
    <div class="hero-inner">
      <div class="hero-content">
        <div class="hero-badge">🎶 Trung Tâm Âm Nhạc Hàng Đầu</div>
        <h1><?= nl2br(e($heroTitle)) ?></h1>
        <p><?= e($heroSub) ?></p>
        <div class="hero-actions">
          <a href="<?= e($heroBtnUrl) ?>" class="btn btn-gold"><?= e($heroBtnText) ?></a>
          <a href="#courses" class="btn btn-outline">Xem Khóa Học</a>
        </div>
        <div class="hero-stats">
          <div class="hero-stat">
            <strong><span data-target="<?= $totalStudents ?: 500 ?>">0</span>+</strong>
            <span>Học Viên</span>
          </div>
          <div class="hero-stat">
            <strong><span data-target="<?= $totalCourses ?: 20 ?>">0</span>+</strong>
            <span>Khóa Học</span>
          </div>
          <div class="hero-stat">
            <strong><span data-target="<?= $totalTeachers ?: 15 ?>">0</span>+</strong>
            <span>Giảng Viên</span>
          </div>
          <div class="hero-stat">
            <strong><span data-target="10">0</span>+</strong>
            <span>Năm Kinh Nghiệm</span>
          </div>
        </div>
      </div>
      <div class="hero-visual">
        <div class="hero-card">
          <div class="hero-card-top">
            <div class="hero-card-icon">🎹</div>
            <div class="hero-card-info">
              <h4>Piano Cơ Bản</h4>
              <p>Học viên: Nguyễn Thị Lan</p>
            </div>
          </div>
          <div class="hero-card-progress"><span style="width:75%"></span></div>
          <p style="font-size:.8rem;opacity:.7;margin-top:.5rem">Tiến độ: 75% — Tuần 9/12</p>
        </div>
        <div class="hero-card">
          <div class="hero-card-top">
            <div class="hero-card-icon">🎸</div>
            <div class="hero-card-info">
              <h4>Guitar Đệm Hát</h4>
              <p>Học viên: Trần Văn Hùng</p>
            </div>
          </div>
          <div class="hero-card-progress"><span style="width:40%"></span></div>
          <p style="font-size:.8rem;opacity:.7;margin-top:.5rem">Tiến độ: 40% — Tuần 5/12</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ===== LEARNING PATH / LEVELS ===== -->
<section id="levels">
  <div class="container">
    <h2 class="section-title">Lộ Trình <span class="text-green">Cấp Độ</span></h2>
    <p class="section-subtitle">Chúng tôi thiết kế chương trình học rõ ràng theo từng cấp độ, phù hợp với mọi lứa tuổi và khả năng.</p>
    <div class="levels-grid">
      <div class="level-card green">
        <div class="level-icon">🌱</div>
        <h3>Cơ Bản</h3>
        <p>Làm quen với nhạc cụ, nhạc lý cơ bản, tư thế và kỹ thuật nền tảng. Phù hợp cho người mới bắt đầu.</p>
        <span class="level-tag">0 – 3 tháng</span>
      </div>
      <div class="level-card blue">
        <div class="level-icon">📘</div>
        <h3>Trung Cấp</h3>
        <p>Nâng cao kỹ thuật, học các tác phẩm có độ khó trung bình, phát triển khả năng diễn đạt âm nhạc.</p>
        <span class="level-tag">3 – 12 tháng</span>
      </div>
      <div class="level-card orange">
        <div class="level-icon">🔥</div>
        <h3>Nâng Cao</h3>
        <p>Thành thạo các tác phẩm phức tạp, hòa tấu nhóm, biểu diễn sân khấu và thi chứng chỉ âm nhạc.</p>
        <span class="level-tag">1 – 3 năm</span>
      </div>
      <div class="level-card purple">
        <div class="level-icon">🏆</div>
        <h3>Chuyên Nghiệp</h3>
        <p>Đào tạo chuyên sâu cho học viên có định hướng nghề nghiệp âm nhạc, biểu diễn quốc tế.</p>
        <span class="level-tag">3+ năm</span>
      </div>
    </div>
  </div>
</section>

<!-- ===== FEATURES ===== -->
<section id="features" class="section-alt">
  <div class="container">
    <h2 class="section-title">Tại Sao Chọn <span class="text-green">MusicOfEveryone?</span></h2>
    <p class="section-subtitle">Chúng tôi mang đến môi trường học tập tốt nhất với đội ngũ giảng viên tài năng và cơ sở vật chất hiện đại.</p>
    <div class="features-grid">
      <div class="feature-card">
        <div class="feature-icon">👨‍🏫</div>
        <div class="feature-text">
          <h3>Giảng Viên Chuyên Nghiệp</h3>
          <p>100% giảng viên tốt nghiệp từ các nhạc viện hàng đầu Việt Nam và quốc tế, có nhiều năm kinh nghiệm giảng dạy thực tế.</p>
        </div>
      </div>
      <div class="feature-card">
        <div class="feature-icon">📚</div>
        <div class="feature-text">
          <h3>Chương Trình Học Bài Bản</h3>
          <p>Giáo trình được biên soạn kỹ lưỡng, kết hợp phương pháp truyền thống và hiện đại, đảm bảo học viên tiến bộ nhanh và bền vững.</p>
        </div>
      </div>
      <div class="feature-card">
        <div class="feature-icon">🏠</div>
        <div class="feature-text">
          <h3>Lớp Học Nhỏ & Cá Nhân Hóa</h3>
          <p>Mỗi lớp không quá 5 học viên, đảm bảo giảng viên có thể chú ý và hướng dẫn từng cá nhân một cách tỉ mỉ nhất.</p>
        </div>
      </div>
      <div class="feature-card">
        <div class="feature-icon">🎤</div>
        <div class="feature-text">
          <h3>Biểu Diễn Thực Tế</h3>
          <p>Định kỳ tổ chức các buổi hòa nhạc, recital cho học viên thực hành trên sân khấu thật, xây dựng sự tự tin và kinh nghiệm biểu diễn.</p>
        </div>
      </div>
      <div class="feature-card">
        <div class="feature-icon">🕐</div>
        <div class="feature-text">
          <h3>Lịch Học Linh Hoạt</h3>
          <p>Học vào buổi tối và cuối tuần, phù hợp với lịch trình bận rộn của học sinh, sinh viên và người đi làm.</p>
        </div>
      </div>
      <div class="feature-card">
        <div class="feature-icon">🎵</div>
        <div class="feature-text">
          <h3>Đa Dạng Nhạc Cụ & Thể Loại</h3>
          <p>Từ Piano, Guitar, Violin, Thanh nhạc đến các nhạc cụ dân tộc — chúng tôi đáp ứng mọi đam mê âm nhạc của bạn.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ===== NUMBERS ===== -->
<section id="numbers">
  <div class="container">
    <div class="numbers-grid">
      <div class="number-item">
        <strong><span data-target="<?= $totalStudents ?: 500 ?>">0</span>+</strong>
        <span>Học Viên Đã Tốt Nghiệp</span>
      </div>
      <div class="number-item">
        <strong><span data-target="<?= $totalCourses ?: 20 ?>">0</span>+</strong>
        <span>Khóa Học Đa Dạng</span>
      </div>
      <div class="number-item">
        <strong><span data-target="<?= $totalTeachers ?: 15 ?>">0</span>+</strong>
        <span>Giảng Viên Chuyên Nghiệp</span>
      </div>
      <div class="number-item">
        <strong><span data-target="98">0</span>%</strong>
        <span>Học Viên Hài Lòng</span>
      </div>
    </div>
  </div>
</section>

<!-- ===== FEATURED COURSES ===== -->
<section id="courses" class="section-alt">
  <div class="container">
    <h2 class="section-title">Khóa Học <span class="text-green">Nổi Bật</span></h2>
    <p class="section-subtitle">Khám phá những khóa học được học viên đánh giá cao nhất, giúp bạn phát triển toàn diện về âm nhạc.</p>

    <?php if (empty($featuredCourses)): ?>
      <p style="text-align:center;color:#666;">Chưa có khóa học. Vui lòng kiểm tra lại sau.</p>
    <?php else: ?>
    <div class="courses-grid">
      <?php
      $icons = ['🎹','🎸','🎤','🎻','🥁','🎺'];
      $i = 0;
      foreach ($featuredCourses as $course):
        $lvl = $course['level'];
        $badgeClass = 'badge-' . levelColor($lvl);
        $icon = $icons[$i % count($icons)];
        $i++;
      ?>
      <div class="course-card">
        <div class="course-thumb" style="background:linear-gradient(135deg,<?= match($lvl){
          'co_ban'=>'#0f5c35,#25a06a','trung_cap'=>'#1e3a8a,#3b82f6',
          'nang_cao'=>'#7c2d12,#f97316','chuyen_nghiep'=>'#4c1d95,#8b5cf6',
          default=>'#0f5c35,#25a06a'
        } ?>)">
          <?= $icon ?>
          <span class="course-level-badge badge-<?= levelColor($lvl) ?>"><?= levelLabel($lvl) ?></span>
        </div>
        <div class="course-body">
          <h3 class="course-title"><?= e($course['title']) ?></h3>
          <p class="course-desc"><?= e($course['short_desc'] ?? '') ?></p>
          <div class="course-meta">
            <span class="course-teacher">👤 <?= e($course['teacher_name'] ?? 'Giảng viên') ?></span>
            <span class="course-price <?= $course['price'] <= 0 ? 'free' : '' ?>"><?= formatPrice((float)$course['price']) ?></span>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="courses-cta">
      <a href="#contact" class="btn btn-primary btn-lg">Đăng Ký Khóa Học Ngay</a>
    </div>
  </div>
</section>

<!-- ===== CONTACT / FOOTER ===== -->
<footer id="footer">
  <div class="container">
    <div class="footer-top">
      <div class="footer-brand">
        <a href="/" class="logo">
          <div class="logo-icon">🎵</div>
          Music<span>OfEveryone</span>
        </a>
        <p>Trung tâm âm nhạc MusicOfEveryone — nơi mọi người đều có thể khám phá và phát triển niềm đam mê âm nhạc của mình.</p>
        <div class="footer-socials">
          <?php if ($facebook): ?><a href="<?= e($facebook) ?>" target="_blank" rel="noopener" title="Facebook">f</a><?php endif; ?>
          <?php if ($youtube): ?><a href="<?= e($youtube) ?>" target="_blank" rel="noopener" title="YouTube">▶</a><?php endif; ?>
          <a href="mailto:<?= e($email) ?>" title="Email">✉</a>
        </div>
      </div>
      <div class="footer-col">
        <h4>Khóa Học</h4>
        <ul>
          <li><a href="#courses">Piano</a></li>
          <li><a href="#courses">Guitar</a></li>
          <li><a href="#courses">Violin</a></li>
          <li><a href="#courses">Thanh Nhạc</a></li>
          <li><a href="#courses">Nhạc Lý</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Liên Kết</h4>
        <ul>
          <li><a href="/">Trang Chủ</a></li>
          <li><a href="#levels">Lộ Trình</a></li>
          <li><a href="#features">Tính Năng</a></li>
          <li><a href="#contact">Liên Hệ</a></li>
          <li><a href="/admin/">Quản Trị</a></li>
        </ul>
      </div>
      <div class="footer-col" id="contact">
        <h4>Liên Hệ</h4>
        <ul class="footer-contact">
          <?php if ($address): ?><li><span>📍</span><span><?= e($address) ?></span></li><?php endif; ?>
          <?php if ($phone): ?><li><span>📞</span><span><a href="tel:<?= e(preg_replace('/\s+/','',$phone)) ?>"><?= e($phone) ?></a></span></li><?php endif; ?>
          <?php if ($email): ?><li><span>✉</span><span><a href="mailto:<?= e($email) ?>"><?= e($email) ?></a></span></li><?php endif; ?>
        </ul>
      </div>
    </div>
    <?php if ($mapEmbed): ?>
    <div class="footer-map">
      <h4>📍 Bản Đồ Đường Đến</h4>
      <div class="map-embed">
        <?= $mapEmbed /* already sanitized in admin — trusted HTML */ ?>
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
