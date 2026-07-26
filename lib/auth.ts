import { cookies } from "next/headers";
import { createHmac, scryptSync, timingSafeEqual } from "node:crypto";
import { readData } from "@/lib/data";

export const ADMIN_SESSION_MAX_AGE_SECONDS = 60 * 60 * 24;

function getSessionSecret(): string {
  const configured = process.env.ADMIN_SESSION_SECRET;
  if (configured) {
    return configured;
  }

  if (process.env.NODE_ENV === "production") {
    throw new Error("ADMIN_SESSION_SECRET must be set in production.");
  }

  return "dev-only-admin-session-secret";
}

export async function isAdminAuthenticated(): Promise<boolean> {
  const token = (await cookies()).get("admin_auth")?.value;
  return verifyAdminSessionToken(token);
}

export async function verifyAdminCredentials(username: string, password: string): Promise<boolean> {
  const data = await readData();
  if (username !== data.admin.username) {
    return false;
  }

  const hashedInput = scryptSync(password, data.admin.passwordSalt, 64);
  const expectedHash = Buffer.from(data.admin.passwordHash, "hex");
  if (hashedInput.length !== expectedHash.length) {
    return false;
  }

  return timingSafeEqual(hashedInput, expectedHash);
}

export function createAdminSessionToken(): string {
  const secret = getSessionSecret();
  const payload = `${Date.now()}`;
  const signature = createHmac("sha256", secret).update(payload).digest("hex");
  return `${payload}.${signature}`;
}

export function verifyAdminSessionToken(token?: string): boolean {
  const secret = getSessionSecret();
  if (!token) {
    return false;
  }

  const [payload, signature] = token.split(".");
  if (!payload || !signature) {
    return false;
  }

  const issuedAt = Number(payload);
  if (!Number.isFinite(issuedAt)) {
    return false;
  }
  if (Date.now() - issuedAt > ADMIN_SESSION_MAX_AGE_SECONDS * 1000) {
    return false;
  }

  const expected = createHmac("sha256", secret).update(payload).digest("hex");
  if (signature.length !== expected.length) {
    return false;
  }
  return timingSafeEqual(Buffer.from(signature), Buffer.from(expected));
}
