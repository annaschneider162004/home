import { NextResponse } from "next/server";
import { isAdminAuthenticated } from "@/lib/auth";
import { readData, writeData } from "@/lib/data";
import type { SiteData } from "@/lib/types";

export async function GET() {
  if (!(await isAdminAuthenticated())) {
    return NextResponse.json({ message: "Unauthorized" }, { status: 401 });
  }

  const data = await readData();
  return NextResponse.json({
    baseUrl: data.settings.baseUrl,
    map: data.settings.map,
    seo: data.settings.seo,
  });
}

export async function PUT(request: Request) {
  if (!(await isAdminAuthenticated())) {
    return NextResponse.json({ message: "Unauthorized" }, { status: 401 });
  }

  const payload = (await request.json()) as SiteData["settings"];
  const data = await readData();
  data.settings.baseUrl = payload.baseUrl;
  data.settings.map = payload.map;
  data.settings.seo = payload.seo;
  await writeData(data);
  return NextResponse.json({ success: true });
}
