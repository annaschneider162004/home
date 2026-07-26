import SiteFooter from "@/components/SiteFooter";
import SiteHeader from "@/components/SiteHeader";
import { mapEmbedSrc, readData } from "@/lib/data";
import { getSeoMetadata } from "@/lib/seo";

export const dynamic = "force-dynamic";

export async function generateMetadata() {
  return getSeoMetadata("about");
}

export default async function AboutPage() {
  const data = await readData();
  const mapSrc = mapEmbedSrc(data);

  return (
    <>
      <SiteHeader />
      <main className="container section">
        <h1>Về chúng tôi</h1>
        <p>
          MusicOfEveryone là câu lạc bộ học nhạc trực tuyến, tập trung vào phương pháp học
          cá nhân hóa cho học viên từ 6 đến 18 tuổi.
        </p>
        <iframe
          title="Bản đồ MusicOfEveryone"
          src={mapSrc}
          width="100%"
          height="300"
          loading="lazy"
          referrerPolicy="no-referrer-when-downgrade"
        />
      </main>
      <SiteFooter />
    </>
  );
}
