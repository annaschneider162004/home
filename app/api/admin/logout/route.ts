import { cookies } from "next/headers";
import { NextResponse } from "next/server";

export async function POST() {
  (await cookies()).set("admin_auth", "", { httpOnly: true, path: "/", maxAge: 0, sameSite: "lax" });
  return NextResponse.json({ success: true });
}
