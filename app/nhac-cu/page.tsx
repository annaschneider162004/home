import SiteFooter from "@/components/SiteFooter";
import SiteHeader from "@/components/SiteHeader";
import { getSeoMetadata } from "@/lib/seo";

export const dynamic = "force-dynamic";

export async function generateMetadata() {
  return getSeoMetadata("instruments");
}

export default function InstrumentsPage() {
  return (
    <>
      <SiteHeader />
      <main className="container section">
        <h1>Nhạc cụ</h1>
        <p>Piano, Violin, Guitar và Sáo Recorder được thiết kế bài học theo từng cấp độ.</p>
      </main>
      <SiteFooter />
    </>
  );
}
