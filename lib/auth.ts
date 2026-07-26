import { cookies } from "next/headers";

export async function isAdminAuthenticated(): Promise<boolean> {
  return (await cookies()).get("admin_auth")?.value === "1";
}
