import type { MetadataRoute } from "next";
import { readData } from "@/lib/data";

export default async function robots(): Promise<MetadataRoute.Robots> {
  const data = await readData();
  const base = process.env.NEXT_PUBLIC_SITE_URL ?? data.settings.baseUrl;

  return {
    rules: {
      userAgent: "*",
      allow: "/",
      disallow: "/admin",
    },
    sitemap: `${base}/sitemap.xml`,
  };
}
