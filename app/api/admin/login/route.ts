import { cookies } from "next/headers";
import { NextResponse } from "next/server";
import { readData } from "@/lib/data";

export async function POST(request: Request) {
  const body = (await request.json()) as { username?: string; password?: string };
  const data = await readData();

  if (body.username !== data.admin.username || body.password !== data.admin.password) {
    return NextResponse.json({ message: "Invalid credentials" }, { status: 401 });
  }

  (await cookies()).set("admin_auth", "1", { httpOnly: true, path: "/", sameSite: "lax" });
  return NextResponse.json({ success: true });
}
