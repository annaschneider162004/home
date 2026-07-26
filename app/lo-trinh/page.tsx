import SiteFooter from "@/components/SiteFooter";
import SiteHeader from "@/components/SiteHeader";
import { getSeoMetadata } from "@/lib/seo";

export const dynamic = "force-dynamic";

export async function generateMetadata() {
  return getSeoMetadata("roadmap");
}

export default function RoadmapPage() {
  return (
    <>
      <SiteHeader />
      <main className="container section">
        <h1>Lộ trình học</h1>
        <ul className="roadmap-list">
          <li><strong>Cấp 1 (6-10 tuổi):</strong> Làm quen nhịp điệu và nhạc lý nền tảng.</li>
          <li><strong>Cấp 2 (11-14 tuổi):</strong> Phát triển kỹ thuật biểu diễn và cảm thụ.</li>
          <li><strong>Cấp 3 (15-18 tuổi):</strong> Hoàn thiện phong cách cá nhân và luyện thi.</li>
        </ul>
      </main>
      <SiteFooter />
    </>
  );
}
