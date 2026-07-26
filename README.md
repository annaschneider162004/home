# MusicOfEveryone - Music Club

Website học nhạc trực tuyến (tiếng Việt) với trang công khai và khu vực quản trị `/admin`.

## Công nghệ
- Next.js (App Router)
- React + TypeScript
- Lưu dữ liệu bằng JSON file (`/data/content.json`) để chạy local nhanh

## Chạy local
```bash
npm install
npm run dev
```
Mở trình duyệt tại: `http://localhost:3000`

## Đăng nhập admin
- URL: `http://localhost:3000/admin/login`
- Tài khoản mặc định: `admin`
- Mật khẩu mặc định: `admin123`

> Khuyến nghị đổi thông tin đăng nhập trước khi triển khai thực tế.

## Cấu trúc chính
- `/app`: giao diện trang public, admin, API routes
- `/components`: component dùng chung (header, footer, admin panel)
- `/data/content.json`: dữ liệu khóa học, giảng viên, người dùng, bài viết, SEO, Google Map
- `/public/images`: ảnh minh họa placeholder
- `/lib`: helper đọc/ghi dữ liệu, SEO, auth

## Chức năng đã có
### Public
- Trang chủ theo tông xanh lá, tiếng Việt
- Menu: Trang chủ, Khóa học, Nhạc cụ, Giảng viên, Lộ trình, Thư viện, Cộng đồng, Về chúng tôi
- Hero + CTA + khu lộ trình + tính năng nổi bật + khóa học nổi bật
- Footer có liên hệ, mạng xã hội và Google Map động

### Admin (`/admin`)
- Đăng nhập bảo vệ route (cookie + middleware)
- Dashboard thống kê tổng quan
- CRUD cho: khóa học, giảng viên, người dùng, bài viết
- Quản lý SEO: title, description, keywords, OG image theo từng trang
- Quản lý Google Map: địa chỉ, lat/lng, iframe embed
- Bảng dữ liệu có tìm kiếm và phân trang

### SEO
- Metadata động lấy từ cấu hình admin
- Tự động sinh:
  - `sitemap.xml` (`/app/sitemap.ts`)
  - `robots.txt` (`/app/robots.ts`)

## Cấu hình SEO/Google Map trong admin
1. Đăng nhập `/admin/login`
2. Vào menu **SEO & Google Map**
3. Cập nhật:
   - `baseUrl`
   - map (address/lat/lng/iframe)
   - SEO fields cho từng page
4. Nhấn **Lưu cấu hình**
