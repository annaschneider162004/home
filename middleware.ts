import { NextResponse } from "next/server";
import type { NextRequest } from "next/server";

const ADMIN_SESSION_MAX_AGE_MS = 24 * 60 * 60 * 1000;

export async function middleware(request: NextRequest) {
  const { pathname } = request.nextUrl;

  if (pathname.startsWith("/admin/login") || !pathname.startsWith("/admin")) {
    return NextResponse.next();
  }

  const token = request.cookies.get("admin_auth")?.value;
  const isAuthed = await verifyAdminToken(token);
  if (isAuthed) {
    return NextResponse.next();
  }

  return NextResponse.redirect(new URL("/admin/login", request.url));
}

export const config = {
  matcher: ["/admin/:path*"],
};

async function verifyAdminToken(token?: string): Promise<boolean> {
  if (!token) return false;
  const [payload, signature] = token.split(".");
  if (!payload || !signature || !/^\d+$/.test(payload) || !/^[a-f0-9]{64}$/i.test(signature)) {
    return false;
  }
  const issuedAt = Number(payload);
  if (!Number.isFinite(issuedAt) || Date.now() - issuedAt > ADMIN_SESSION_MAX_AGE_MS) {
    return false;
  }

  const secret = process.env.ADMIN_SESSION_SECRET;
  if (!secret) {
    return process.env.NODE_ENV !== "production";
  }

  const key = await crypto.subtle.importKey(
    "raw",
    new TextEncoder().encode(secret),
    { name: "HMAC", hash: "SHA-256" },
    false,
    ["sign"],
  );
  const digest = await crypto.subtle.sign("HMAC", key, new TextEncoder().encode(payload));
  const expected = Array.from(new Uint8Array(digest))
    .map((value) => value.toString(16).padStart(2, "0"))
    .join("");

  return signature.toLowerCase() === expected.toLowerCase();
}
