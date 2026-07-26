import type { Metadata } from "next";
import { readData } from "@/lib/data";

export async function getSeoMetadata(pageKey: string): Promise<Metadata> {
  const data = await readData();
  const seo = data.settings.seo[pageKey] ?? data.settings.seo.home;
  const siteName = data.settings.siteName;
  const baseUrl = process.env.NEXT_PUBLIC_SITE_URL ?? data.settings.baseUrl;
  const imageUrl = seo.ogImage.startsWith("http")
    ? seo.ogImage
    : `${baseUrl}${seo.ogImage}`;

  return {
    title: seo.title,
    description: seo.description,
    keywords: seo.keywords,
    openGraph: {
      title: seo.title,
      description: seo.description,
      siteName,
      images: [imageUrl],
      locale: "vi_VN",
      type: "website",
    },
  };
}
