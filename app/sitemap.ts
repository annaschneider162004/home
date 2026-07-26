import type { MetadataRoute } from "next";
import { readData } from "@/lib/data";

export default async function sitemap(): Promise<MetadataRoute.Sitemap> {
  const data = await readData();
  const base = process.env.NEXT_PUBLIC_SITE_URL ?? data.settings.baseUrl;
  const routes = [
    "",
    "/khoa-hoc",
    "/nhac-cu",
    "/giang-vien",
    "/lo-trinh",
    "/thu-vien",
    "/cong-dong",
    "/ve-chung-toi",
    "/admin/login",
  ];

  return routes.map((route) => ({
    url: `${base}${route}`,
    lastModified: new Date(),
  }));
}
