<?php
/**
 * Database Configuration
 * Chỉnh sửa các thông tin sau theo cấu hình MySQL của bạn trên cPanel
 */

define('DB_HOST', 'localhost');        // Thường là 'localhost' trên cPanel
define('DB_NAME', 'your_db_name');     // Tên database bạn tạo trong cPanel MySQL Databases
define('DB_USER', 'your_db_user');     // Username MySQL của bạn (thường: cpanelusername_dbuser)
define('DB_PASS', 'your_db_password'); // Mật khẩu database
define('DB_CHARSET', 'utf8mb4');

// Cài đặt ứng dụng
define('APP_URL', 'https://yourdomain.com'); // URL gốc của website — dùng HTTPS sau khi cài SSL (không có / cuối)
define('APP_NAME', 'MusicOfEveryone');
define('APP_VERSION', '1.0.0');

// Đường dẫn thư mục gốc
define('BASE_PATH', dirname(__DIR__));
define('INCLUDES_PATH', BASE_PATH . '/includes');
define('ADMIN_PATH', BASE_PATH . '/admin');
define('ASSETS_URL', APP_URL . '/assets');

// Cài đặt session
define('SESSION_NAME', 'moe_session');
define('SESSION_LIFETIME', 7200); // 2 giờ (giây)

// Múi giờ
date_default_timezone_set('Asia/Ho_Chi_Minh');
