import Image from "next/image";
import Link from "next/link";
import SiteFooter from "@/components/SiteFooter";
import SiteHeader from "@/components/SiteHeader";
import { readData } from "@/lib/data";
import { getSeoMetadata } from "@/lib/seo";

export const dynamic = "force-dynamic";

export async function generateMetadata() {
  return getSeoMetadata("home");
}

const features = [
  ["🎯", "Lộ trình cá nhân hóa", "Thiết kế bài học phù hợp trình độ và mục tiêu từng học viên."],
  ["💻", "Học online linh hoạt", "Học mọi lúc trên laptop, máy tính bảng hoặc điện thoại."],
  ["🎓", "Giáo viên chất lượng", "Đội ngũ giảng viên có chuyên môn và giàu kinh nghiệm sư phạm."],
  ["🎼", "Nội dung đa dạng", "Bao gồm thanh nhạc, piano, violin, sáo recorder và nhiều hơn nữa."],
  ["📈", "Theo dõi tiến độ", "Bảng tiến độ trực quan để phụ huynh và học viên dễ theo dõi."],
] as const;

const levels = ["Cấp 1 (6-10 tuổi)", "Cấp 2 (11-14 tuổi)", "Cấp 3 (15-18 tuổi)"];

export default async function HomePage() {
  const data = await readData();

  return (
    <>
      <SiteHeader />
      <main>
        <section className="hero">
          <div className="container hero-grid">
            <div>
              <p className="kicker">MusicOfEveryone - Music Club</p>
              <h1>HỌC NHẠC CHO MỌI LỨA TUỔI</h1>
              <p>
                Chương trình học nhạc trực tuyến bằng tiếng Việt với phương pháp sinh động,
                dễ theo dõi và phù hợp cho trẻ em, thiếu niên.
              </p>
              <Link href="/khoa-hoc" className="btn btn-solid">
                Bắt đầu hành trình
              </Link>
            </div>
            <div className="hero-characters">
              {["hero-guitar.svg", "hero-keyboard.svg", "hero-laptop.svg"].map((img) => (
                <Image key={img} src={`/images/${img}`} alt={img} width={180} height={180} />
              ))}
            </div>
          </div>
        </section>

        <section className="section container">
          <h2>Lộ trình theo cấp độ</h2>
          <div className="badge-row">
            {levels.map((level) => (
              <span key={level} className="badge">
                {level}
              </span>
            ))}
          </div>
        </section>

        <section className="section section-muted">
          <div className="container">
            <h2>Tính năng nổi bật</h2>
            <div className="feature-grid">
              {features.map(([icon, title, description]) => (
                <article key={title} className="card">
                  <div className="icon">{icon}</div>
                  <h3>{title}</h3>
                  <p>{description}</p>
                </article>
              ))}
            </div>
          </div>
        </section>

        <section className="section container">
          <div className="section-head">
            <h2>Khóa học nổi bật</h2>
            <Link href="/khoa-hoc">Xem tất cả khóa học</Link>
          </div>
          <div className="course-grid">
            {data.courses.map((course) => (
              <article key={course.id} className="course-card">
                <Image src={course.image} alt={course.name} width={160} height={160} />
                <h3>{course.name}</h3>
                <p>{course.description}</p>
                <span className="badge">{course.level}</span>
              </article>
            ))}
          </div>
        </section>
      </main>
      <SiteFooter />
    </>
  );
}
