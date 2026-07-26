import SiteFooter from "@/components/SiteFooter";
import SiteHeader from "@/components/SiteHeader";
import { getSeoMetadata } from "@/lib/seo";

export const dynamic = "force-dynamic";

export async function generateMetadata() {
  return getSeoMetadata("community");
}

export default function CommunityPage() {
  return (
    <>
      <SiteHeader />
      <main className="container section">
        <h1>Cộng đồng</h1>
        <p>Tham gia workshop, mini concert và nhóm trao đổi để học nhạc vui hơn mỗi ngày.</p>
      </main>
      <SiteFooter />
    </>
  );
}
