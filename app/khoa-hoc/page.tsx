import Image from "next/image";
import SiteFooter from "@/components/SiteFooter";
import SiteHeader from "@/components/SiteHeader";
import { readData } from "@/lib/data";
import { getSeoMetadata } from "@/lib/seo";

export const dynamic = "force-dynamic";

export async function generateMetadata() {
  return getSeoMetadata("courses");
}

export default async function CoursesPage() {
  const data = await readData();

  return (
    <>
      <SiteHeader />
      <main className="container section">
        <h1>Danh sách khóa học</h1>
        <div className="course-grid">
          {data.courses.map((course) => (
            <article key={course.id} className="course-card">
              <Image src={course.image} alt={course.name} width={160} height={160} />
              <h3>{course.name}</h3>
              <p>{course.description}</p>
              <p>
                Học phí:{" "}
                {Number.isFinite(Number(course.price))
                  ? `${Number(course.price).toLocaleString("vi-VN")}đ`
                  : "Liên hệ"}
              </p>
              <span className="badge">{course.level}</span>
            </article>
          ))}
        </div>
      </main>
      <SiteFooter />
    </>
  );
}
