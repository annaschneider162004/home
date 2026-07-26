import Image from "next/image";
import SiteFooter from "@/components/SiteFooter";
import SiteHeader from "@/components/SiteHeader";
import { readData } from "@/lib/data";
import { getSeoMetadata } from "@/lib/seo";

export const dynamic = "force-dynamic";

export async function generateMetadata() {
  return getSeoMetadata("teachers");
}

export default async function TeachersPage() {
  const data = await readData();

  return (
    <>
      <SiteHeader />
      <main className="container section">
        <h1>Giảng viên</h1>
        <div className="feature-grid">
          {data.teachers.map((teacher) => (
            <article key={teacher.id} className="card">
              <Image src={teacher.image} alt={teacher.name} width={140} height={140} />
              <h3>{teacher.name}</h3>
              <p>{teacher.specialty}</p>
              <p>{teacher.bio}</p>
            </article>
          ))}
        </div>
      </main>
      <SiteFooter />
    </>
  );
}
