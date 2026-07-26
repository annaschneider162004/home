-- ============================================================
-- MusicOfEveryone - Database Schema + Sample Data
-- Import via phpMyAdmin on cPanel
-- Requires MySQL 5.7+ or MariaDB 10.3+
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- Table: users (Admin users)
-- ============================================================
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(255) DEFAULT NULL,
  `role` ENUM('admin','staff') NOT NULL DEFAULT 'admin',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default admin: username=admin, ******
INSERT INTO `users` (`username`, `email`, `password`, `full_name`, `role`) VALUES
('admin', 'admin@musicofeveryone.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Quản Trị Viên', 'admin');

-- ============================================================
-- Table: teachers (Giảng viên)
-- ============================================================
DROP TABLE IF EXISTS `teachers`;
CREATE TABLE `teachers` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) DEFAULT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `bio` TEXT DEFAULT NULL,
  `speciality` VARCHAR(255) DEFAULT NULL,
  `image` VARCHAR(500) DEFAULT NULL,
  `experience_years` TINYINT UNSIGNED DEFAULT 0,
  `status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `teachers` (`name`, `email`, `bio`, `speciality`, `experience_years`, `status`) VALUES
('Nguyễn Thanh Hương', 'huong@musicofeveryone.vn', 'Giảng viên Piano với hơn 15 năm kinh nghiệm giảng dạy, tốt nghiệp Nhạc viện Hà Nội.', 'Piano, Nhạc lý', 15, 'active'),
('Trần Minh Khoa', 'khoa@musicofeveryone.vn', 'Chuyên gia Guitar cổ điển và đương đại, từng biểu diễn tại nhiều sân khấu lớn trong nước.', 'Guitar, Nhạc đệm', 10, 'active'),
('Lê Thị Mai', 'mai@musicofeveryone.vn', 'Giảng viên Thanh nhạc, đào tạo nhiều học viên đạt giải tại các cuộc thi âm nhạc toàn quốc.', 'Thanh nhạc, Thanh nhạc pop', 12, 'active'),
('Phạm Đức Thịnh', 'thinh@musicofeveryone.vn', 'Nhạc sĩ và giảng viên Violin, tốt nghiệp xuất sắc Nhạc viện TP.HCM.', 'Violin, Nhạc thính phòng', 8, 'active');

-- ============================================================
-- Table: courses (Khóa học)
-- ============================================================
DROP TABLE IF EXISTS `courses`;
CREATE TABLE `courses` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `description` TEXT DEFAULT NULL,
  `short_desc` VARCHAR(500) DEFAULT NULL,
  `level` ENUM('co_ban','trung_cap','nang_cao','chuyen_nghiep') NOT NULL DEFAULT 'co_ban',
  `teacher_id` INT UNSIGNED DEFAULT NULL,
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `duration_weeks` TINYINT UNSIGNED DEFAULT NULL,
  `lessons_per_week` TINYINT UNSIGNED DEFAULT 2,
  `image` VARCHAR(500) DEFAULT NULL,
  `featured` TINYINT(1) NOT NULL DEFAULT 0,
  `status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`teacher_id`) REFERENCES `teachers`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `courses` (`title`, `slug`, `description`, `short_desc`, `level`, `teacher_id`, `price`, `duration_weeks`, `lessons_per_week`, `featured`, `status`) VALUES
('Piano Cơ Bản', 'piano-co-ban', 'Khóa học Piano dành cho người mới bắt đầu, từ nhận biết phím đàn đến thực hành các bản nhạc đơn giản.', 'Học Piano từ con số 0 — phù hợp mọi lứa tuổi', 'co_ban', 1, 1500000, 12, 2, 1, 'active'),
('Guitar Đệm Hát', 'guitar-dem-hat', 'Học đệm Guitar cho các thể loại nhạc Việt phổ biến: ballad, pop, folk. Có bài tập thực hành mỗi buổi.', 'Đệm Guitar mọi bài hát chỉ trong 3 tháng', 'co_ban', 2, 1200000, 12, 2, 1, 'active'),
('Thanh Nhạc Cơ Bản', 'thanh-nhac-co-ban', 'Rèn luyện giọng hát đúng kỹ thuật, mở rộng âm vực, kiểm soát hơi thở và biểu cảm âm nhạc.', 'Khai phá giọng hát tiềm năng của bạn', 'co_ban', 3, 1800000, 16, 2, 1, 'active'),
('Violin Trung Cấp', 'violin-trung-cap', 'Nâng cao kỹ năng Violin: bowing techniques, vibrato, các tác phẩm cổ điển Baroque và Romantic.', 'Chinh phục những bản Violin kinh điển', 'trung_cap', 4, 2500000, 16, 2, 1, 'active'),
('Piano Nâng Cao', 'piano-nang-cao', 'Khóa học Piano chuyên sâu: Chopin, Beethoven, kỹ thuật pedal, biểu diễn sân khấu.', 'Tiến lên trình độ chuyên nghiệp với Piano', 'nang_cao', 1, 3000000, 20, 2, 1, 'active'),
('Nhạc Lý Toàn Diện', 'nhac-ly-toan-dien', 'Hiểu âm nhạc từ gốc: ký hiệu âm nhạc, hòa âm, đối âm, phân tích tác phẩm.', 'Nền tảng lý thuyết cho mọi nhạc cụ', 'co_ban', 1, 800000, 8, 2, 0, 'active');

-- ============================================================
-- Table: students (Học viên)
-- ============================================================
DROP TABLE IF EXISTS `students`;
CREATE TABLE `students` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `phone` VARCHAR(20) DEFAULT NULL,
  `date_of_birth` DATE DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `course_id` INT UNSIGNED DEFAULT NULL,
  `status` ENUM('active','inactive','graduated') NOT NULL DEFAULT 'active',
  `notes` TEXT DEFAULT NULL,
  `enrolled_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `students` (`name`, `email`, `phone`, `course_id`, `status`) VALUES
('Nguyễn Thị Lan', 'lan.nguyen@email.com', '0901234567', 1, 'active'),
('Trần Văn Hùng', 'hung.tran@email.com', '0912345678', 2, 'active'),
('Lê Minh Châu', 'chau.le@email.com', '0923456789', 3, 'graduated'),
('Phạm Thị Hoa', 'hoa.pham@email.com', '0934567890', 4, 'active'),
('Võ Đức Long', 'long.vo@email.com', '0945678901', 1, 'active');

-- ============================================================
-- Table: posts (Bài viết / Thư viện)
-- ============================================================
DROP TABLE IF EXISTS `posts`;
CREATE TABLE `posts` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `excerpt` VARCHAR(500) DEFAULT NULL,
  `content` TEXT DEFAULT NULL,
  `image` VARCHAR(500) DEFAULT NULL,
  `type` ENUM('bai_viet','thu_vien') NOT NULL DEFAULT 'bai_viet',
  `status` ENUM('published','draft') NOT NULL DEFAULT 'draft',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `posts` (`title`, `slug`, `excerpt`, `content`, `type`, `status`) VALUES
('Lợi ích của việc học nhạc từ nhỏ', 'loi-ich-hoc-nhac-tu-nho', 'Âm nhạc không chỉ là nghệ thuật mà còn giúp trẻ phát triển trí tuệ, cảm xúc và kỹ năng xã hội.', '<p>Nghiên cứu khoa học chứng minh rằng trẻ em học nhạc phát triển não bộ tốt hơn, có khả năng tập trung cao hơn và đạt kết quả học tập tốt hơn ở nhiều môn khác nhau.</p>', 'bai_viet', 'published'),
('Chọn đàn Piano phù hợp cho người mới', 'chon-dan-piano-phu-hop', 'Hướng dẫn chi tiết cách chọn đàn Piano hoặc Keyboard phù hợp với ngân sách và nhu cầu của bạn.', '<p>Khi bắt đầu học Piano, câu hỏi đầu tiên nhiều người đặt ra là nên mua loại đàn nào. Bài viết này sẽ giúp bạn quyết định.</p>', 'bai_viet', 'published'),
('Sheet nhạc "Cô Gái Mở Đường" - Guitar', 'sheet-nhac-co-gai-mo-duong-guitar', 'Sheet nhạc miễn phí bản "Cô Gái Mở Đường" phối cho Guitar solo và Guitar đệm hát.', '<p>Tải về sheet nhạc đầy đủ của bài "Cô Gái Mở Đường" với ký âm guitar đầy đủ cho cả solo lẫn đệm hát.</p>', 'thu_vien', 'published');

-- ============================================================
-- Table: seo_settings
-- ============================================================
DROP TABLE IF EXISTS `seo_settings`;
CREATE TABLE `seo_settings` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `page_key` VARCHAR(100) NOT NULL UNIQUE,
  `page_name` VARCHAR(255) DEFAULT NULL,
  `title` VARCHAR(255) DEFAULT NULL,
  `meta_description` TEXT DEFAULT NULL,
  `meta_keywords` TEXT DEFAULT NULL,
  `og_image` VARCHAR(500) DEFAULT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `seo_settings` (`page_key`, `page_name`, `title`, `meta_description`, `meta_keywords`, `og_image`) VALUES
('home', 'Trang Chủ', 'MusicOfEveryone - Âm Nhạc Dành Cho Tất Cả', 'Trung tâm âm nhạc MusicOfEveryone cung cấp các khóa học Piano, Guitar, Violin, Thanh nhạc cho mọi lứa tuổi tại Việt Nam.', 'học nhạc, piano, guitar, violin, thanh nhạc, trung tâm âm nhạc', ''),
('courses', 'Khóa Học', 'Khóa Học Âm Nhạc - MusicOfEveryone', 'Khám phá hàng chục khóa học âm nhạc đa dạng: Piano, Guitar, Violin, Thanh nhạc với giảng viên chuyên nghiệp.', 'khóa học piano, khóa học guitar, học nhạc online, học nhạc tại nhà', ''),
('teachers', 'Giảng Viên', 'Đội Ngũ Giảng Viên - MusicOfEveryone', 'Giảng viên âm nhạc chuyên nghiệp, nhiều năm kinh nghiệm, tốt nghiệp các nhạc viện hàng đầu Việt Nam.', 'giảng viên âm nhạc, thầy dạy nhạc, nhạc viện', ''),
('posts', 'Bài Viết', 'Kiến Thức Âm Nhạc - MusicOfEveryone', 'Chia sẻ kiến thức âm nhạc, sheet nhạc miễn phí, bí quyết học nhạc hiệu quả.', 'sheet nhạc miễn phí, kiến thức âm nhạc, blog âm nhạc', '');

-- ============================================================
-- Table: site_settings
-- ============================================================
DROP TABLE IF EXISTS `site_settings`;
CREATE TABLE `site_settings` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(100) NOT NULL UNIQUE,
  `setting_value` TEXT DEFAULT NULL,
  `setting_group` VARCHAR(100) NOT NULL DEFAULT 'general',
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `site_settings` (`setting_key`, `setting_value`, `setting_group`) VALUES
('site_name', 'MusicOfEveryone', 'general'),
('site_slogan', 'Âm Nhạc Dành Cho Tất Cả', 'general'),
('site_email', 'info@musicofeveryone.vn', 'general'),
('site_phone', '028 1234 5678', 'general'),
('site_address', '123 Đường Âm Nhạc, Quận 1, TP.HCM', 'general'),
('google_map_embed', '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.394!2d106.698!3d10.776!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTDCsDQ2JzMzLjYiTiAxMDbCsDQxJzUyLjgiRQ!5e0!3m2!1svi!2svn!4v1700000000000!5m2!1svi!2svn" width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>', 'contact'),
('facebook_url', 'https://facebook.com/musicofeveryone', 'social'),
('youtube_url', 'https://youtube.com/@musicofeveryone', 'social'),
('footer_text', '© 2024 MusicOfEveryone. Tất cả quyền được bảo lưu.', 'general'),
('hero_title', 'Âm Nhạc Dành Cho Tất Cả', 'homepage'),
('hero_subtitle', 'Khám phá niềm đam mê âm nhạc của bạn với đội ngũ giảng viên chuyên nghiệp, phương pháp giảng dạy hiện đại và không gian học tập sáng tạo.', 'homepage'),
('hero_btn_text', 'Đăng Ký Ngay', 'homepage'),
('hero_btn_url', '#courses', 'homepage');

SET FOREIGN_KEY_CHECKS = 1;
