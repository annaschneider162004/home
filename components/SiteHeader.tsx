import Link from "next/link";

const navItems = [
  ["Trang chủ", "/"],
  ["Khóa học", "/khoa-hoc"],
  ["Nhạc cụ", "/nhac-cu"],
  ["Giảng viên", "/giang-vien"],
  ["Lộ trình", "/lo-trinh"],
  ["Thư viện", "/thu-vien"],
  ["Cộng đồng", "/cong-dong"],
  ["Về chúng tôi", "/ve-chung-toi"],
] as const;

export default function SiteHeader() {
  return (
    <header className="site-header">
      <div className="container header-row">
        <Link href="/" className="logo">
          <span>MusicOfEveryone</span>
          <small>Music Club</small>
        </Link>
        <nav className="top-nav">
          {navItems.map(([label, href]) => (
            <Link key={href} href={href}>
              {label}
            </Link>
          ))}
        </nav>
        <div className="auth-buttons">
          <button type="button" className="btn btn-outline">
            Đăng nhập
          </button>
          <button type="button" className="btn btn-solid">
            Đăng ký
          </button>
        </div>
      </div>
    </header>
  );
}
