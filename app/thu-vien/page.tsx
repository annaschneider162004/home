import SiteFooter from "@/components/SiteFooter";
import SiteHeader from "@/components/SiteHeader";
import { readData } from "@/lib/data";
import { getSeoMetadata } from "@/lib/seo";

export const dynamic = "force-dynamic";

export async function generateMetadata() {
  return getSeoMetadata("library");
}

export default async function LibraryPage() {
  const data = await readData();

  return (
    <>
      <SiteHeader />
      <main className="container section">
        <h1>Thư viện</h1>
        <div className="feature-grid">
          {data.posts.map((post) => (
            <article key={post.id} className="card">
              <h3>{post.title}</h3>
              <p>{post.summary}</p>
              <span className="badge">{post.category}</span>
            </article>
          ))}
        </div>
      </main>
      <SiteFooter />
    </>
  );
}
