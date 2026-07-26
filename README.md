# 🎵 MusicOfEveryone — Website Trung Tâm Âm Nhạc

**MusicOfEveryone** là website trung tâm âm nhạc được xây dựng bằng **PHP thuần + MySQL**, tương thích hoàn toàn với môi trường **hosting cPanel** (shared hosting truyền thống).

---

## 📋 Yêu Cầu Hệ Thống

| Thành phần | Yêu cầu tối thiểu |
|---|---|
| PHP | **8.0+** (khuyến nghị 8.1 hoặc 8.2) |
| MySQL | 5.7+ hoặc MariaDB 10.3+ |
| Apache | 2.4+ với mod_rewrite |
| PHP Extensions | `pdo_mysql`, `session`, `mbstring` (thường có sẵn trên cPanel) |
| Composer | **Không bắt buộc** — PHP thuần, không dùng framework |

---

## 🚀 Hướng Dẫn Triển Khai Lên cPanel

### Bước 1: Cài Đặt PHP Version

1. Đăng nhập **cPanel** → tìm **"MultiPHP Manager"** (hoặc "PHP Version")
2. Chọn domain/subdomain của bạn → đặt PHP version **8.0** trở lên
3. Tại **"PHP Extensions"**, đảm bảo đã bật: `pdo_mysql`, `mbstring`, `session`

---

### Bước 2: Tạo Database MySQL trong cPanel

1. Đăng nhập cPanel → tìm **"MySQL Databases"**
2. **Tạo Database mới:**
   - Nhập tên: `musicofeveryone` → Click **"Create Database"**
   - Tên đầy đủ sẽ là: `cpanelusername_musicofeveryone`

3. **Tạo MySQL User:**
   - Cuộn xuống phần "MySQL Users"
   - Nhập Username: `moe_user` → đặt Password mạnh → **"Create User"**
   - Tên đầy đủ sẽ là: `cpanelusername_moe_user`

4. **Gán quyền User vào Database:**
   - Phần "Add User to Database"
   - Chọn User và Database vừa tạo → **"Add"**
   - Chọn **"ALL PRIVILEGES"** → **"Make Changes"**

> 💡 **Lưu ý**: Username MySQL trên cPanel thường có prefix là username cPanel của bạn. Ví dụ nếu cPanel username là `john`, database sẽ là `john_musicofeveryone`.

---

### Bước 3: Import Database

1. Đăng nhập cPanel → **"phpMyAdmin"**
2. Click vào database `cpanelusername_musicofeveryone` ở sidebar trái
3. Click tab **"Import"**
4. Click **"Choose File"** → chọn file `database.sql` từ project
5. Đảm bảo **Character set: utf8mb4**
6. Click **"Go"** — quá trình import sẽ tạo tất cả bảng và dữ liệu mẫu

---

### Bước 4: Cấu Hình Kết Nối Database

Mở file `config/database.php` và chỉnh sửa:

```php
define('DB_HOST', 'localhost');              // Thường là 'localhost'
define('DB_NAME', 'cpanelusername_musicofeveryone'); // Thay bằng tên DB của bạn
define('DB_USER', 'cpanelusername_moe_user');        // Thay bằng MySQL user
define('DB_PASS', 'your_strong_password');           // Mật khẩu MySQL user

define('APP_URL', 'https://yourdomain.com'); // URL website của bạn (không có / cuối)
```

> ⚠️ **Quan trọng**: Thay `cpanelusername` bằng username cPanel thực tế của bạn.

---

### Bước 5: Upload Mã Nguồn

#### Cách A: Qua File Manager của cPanel

1. Đăng nhập cPanel → **"File Manager"**
2. Điều hướng đến `public_html/` (hoặc thư mục subdomain)
3. Click **"Upload"** → upload toàn bộ file dự án
4. Hoặc: Nén thành `.zip` → upload → click chuột phải → **"Extract"**

#### Cách B: Qua FTP/SFTP

Dùng phần mềm **FileZilla** hoặc **WinSCP**:
- Host: ftp.yourdomain.com
- Username: cPanel username
- Password: cPanel password
- Port: 21 (FTP) hoặc 22 (SFTP nếu có)
- Upload toàn bộ files vào thư mục `public_html/`

#### Cấu Trúc File Sau Khi Upload

```
public_html/
├── .htaccess          ← Quan trọng! Đảm bảo file này được upload
├── index.php          ← Trang chủ
├── sitemap.php        ← Sitemap động
├── robots.txt         ← SEO robots
├── database.sql       ← (Có thể xóa sau khi import)
├── config/
│   └── database.php   ← Cấu hình DB (đã chỉnh ở bước 4)
├── includes/
├── admin/
│   └── ...
└── assets/
    ├── css/
    ├── js/
    └── images/
```

> ⚠️ **Lưu ý**: Đảm bảo file `.htaccess` được upload (file ẩn). Trong File Manager cPanel, bật **"Show Hidden Files"** để kiểm tra.

---

### Bước 6: Kiểm Tra Website

1. Truy cập `https://yourdomain.com/` → Trang chủ hiện ra ✅
2. Truy cập `https://yourdomain.com/admin/` → Redirect về trang đăng nhập ✅
3. Truy cập `https://yourdomain.com/sitemap.xml` → Sitemap XML ✅
4. Truy cập `https://yourdomain.com/robots.txt` → Robots.txt ✅

---

## 🔐 Thông Tin Đăng Nhập Admin Mặc Định

| Thông tin | Giá Trị |
|---|---|
| URL Admin | `https://yourdomain.com/admin/` |
| Username | `admin` |
| Password | `Admin@2024` |

> ⚠️ **BẮT BUỘC**: Đổi mật khẩu ngay sau khi đăng nhập lần đầu!
> 
> Đăng nhập admin → **Cài Đặt** → tab **"Đổi Mật Khẩu"** → nhập mật khẩu mới an toàn.

---

## ⚙️ Cấu Hình Sau Khi Deploy

### Cấu Hình Google Map

1. Truy cập admin → **Cài Đặt** → tab **"Liên Hệ & Bản Đồ"**
2. Vào [Google Maps](https://maps.google.com/) → tìm địa chỉ trung tâm âm nhạc
3. Click **"Chia sẻ"** → **"Nhúng bản đồ"** → Copy mã `<iframe>`
4. Dán vào ô **"Google Map Embed"** trong admin → Lưu
5. Bản đồ sẽ tự động hiển thị ở footer trang chủ ✅

### Cấu Hình SEO

1. Truy cập admin → **SEO**
2. Chỉnh sửa **Title Tag** và **Meta Description** cho từng trang
3. Nhập **OG Image URL** (ảnh thumbnail khi chia sẻ lên mạng xã hội)
4. Click **"Lưu Tất Cả & Cập Nhật Sitemap"**
5. `sitemap.xml` và `robots.txt` sẽ được tự động cập nhật ✅

### Thêm Khóa Học

1. Admin → **Khóa Học** → **"+ Thêm Khóa Học"**
2. Điền đầy đủ thông tin: tên, mô tả, cấp độ, giảng viên, học phí
3. Tick **"Khóa học nổi bật"** để hiển thị trên trang chủ
4. Lưu ✅

### Thêm Giảng Viên

Admin → **Giảng Viên** → **"+ Thêm Giảng Viên"** → điền thông tin → Lưu

### Cài Đặt Thông Tin Liên Hệ

Admin → **Cài Đặt** → tab **"Liên Hệ & Bản Đồ"** → cập nhật địa chỉ, SĐT, email → Lưu

---

## 📁 Cấu Trúc Thư Mục

```
/
├── .htaccess              # Cấu hình Apache (bảo mật + URL)
├── index.php              # Trang chủ
├── sitemap.php            # Sitemap động (PHP)
├── sitemap.xml            # Sitemap tĩnh (tự sinh bởi admin)
├── robots.txt             # Robots.txt (tự sinh bởi admin)
├── database.sql           # Schema + dữ liệu mẫu
├── config/
│   ├── .htaccess          # Chặn truy cập trực tiếp
│   └── database.php       # ⚠️ Cấu hình DB (cần chỉnh sửa)
├── includes/
│   ├── .htaccess          # Chặn truy cập trực tiếp
│   ├── db.php             # Database PDO wrapper
│   └── functions.php      # Helper functions
├── admin/
│   ├── .htaccess          # Bảo mật admin
│   ├── bootstrap.php      # Khởi tạo admin
│   ├── auth.php           # Xác thực session
│   ├── layout.php         # Layout chung admin
│   ├── index.php          # Dashboard
│   ├── login.php          # Trang đăng nhập
│   ├── logout.php         # Đăng xuất
│   ├── courses.php        # CRUD Khóa học
│   ├── teachers.php       # CRUD Giảng viên
│   ├── students.php       # CRUD Học viên
│   ├── posts.php          # CRUD Bài viết
│   ├── seo.php            # Quản lý SEO
│   └── settings.php       # Cài đặt website
└── assets/
    ├── css/
    │   ├── style.css      # CSS trang chủ (theme xanh lá)
    │   └── admin.css      # CSS admin panel
    ├── js/
    │   └── main.js        # JavaScript chính
    └── images/            # Thư mục upload ảnh
```

---

## 🛡️ Bảo Mật

- Mật khẩu admin được hash bằng `password_hash()` (bcrypt)
- CSRF token trên tất cả form
- Session security: `httponly`, `samesite=Lax`, regenerate ID
- `.htaccess` chặn truy cập trực tiếp vào `config/` và `includes/`
- Prepared statements (PDO) chống SQL injection
- Output escaping (`htmlspecialchars`) chống XSS

---

## 🔧 Xử Lý Sự Cố Thường Gặp

### Lỗi "kết nối cơ sở dữ liệu"
- Kiểm tra lại thông tin trong `config/database.php`
- Đảm bảo DB_NAME có prefix cPanel username (vd: `john_musicofeveryone`)
- Đảm bảo MySQL user đã được gán đủ quyền

### Trang 403 Forbidden
- File `.htaccess` chưa được upload hoặc bị lỗi
- Apache `mod_rewrite` chưa được bật (liên hệ hosting support)

### Trang 500 Internal Server Error
- Kiểm tra PHP version (cần 8.0+) trong MultiPHP Manager
- Kiểm tra `error_log` trong File Manager cPanel

### Không thấy file `.htaccess` sau upload
- Trong File Manager cPanel: click **Settings** → tick **"Show Hidden Files"**
- Upload lại nếu thiếu

### Google Map không hiển thị
- Admin → Cài Đặt → tab "Liên Hệ & Bản Đồ"
- Dán mã iframe Google Maps vào ô "Google Map Embed"

---

## 📞 Hỗ Trợ

Sau khi deploy thành công:
1. **Đổi mật khẩu admin** ngay lập tức
2. **Xóa file `database.sql`** khỏi server (hoặc chuyển ra ngoài public_html)
3. **Cấu hình SSL/HTTPS** trong cPanel (Let's Encrypt miễn phí)
4. Cập nhật `APP_URL` trong `config/database.php` thành URL HTTPS sau khi có SSL
