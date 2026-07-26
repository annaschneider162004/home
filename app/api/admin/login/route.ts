import { cookies } from "next/headers";
import { NextResponse } from "next/server";
import {
  ADMIN_SESSION_MAX_AGE_SECONDS,
  createAdminSessionToken,
  verifyAdminCredentials,
} from "@/lib/auth";

export async function POST(request: Request) {
  const body = (await request.json()) as { username?: string; password?: string };
  const username = body.username?.trim() ?? "";
  const password = body.password ?? "";

  if (!(await verifyAdminCredentials(username, password))) {
    return NextResponse.json({ message: "Invalid credentials" }, { status: 401 });
  }

  (await cookies()).set("admin_auth", createAdminSessionToken(), {
    httpOnly: true,
    path: "/",
    sameSite: "lax",
    secure: process.env.NODE_ENV === "production",
    maxAge: ADMIN_SESSION_MAX_AGE_SECONDS,
  });
  return NextResponse.json({ success: true });
}
