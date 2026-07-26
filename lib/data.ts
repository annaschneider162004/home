import { randomUUID } from "node:crypto";
import { promises as fs } from "node:fs";
import path from "node:path";
import type { ResourceKey, SiteData } from "@/lib/types";

const DATA_FILE = path.join(process.cwd(), "data", "content.json");

export const RESOURCE_KEYS: ResourceKey[] = ["courses", "teachers", "users", "posts"];

export async function readData(): Promise<SiteData> {
  const file = await fs.readFile(DATA_FILE, "utf-8");
  return JSON.parse(file) as SiteData;
}

export async function writeData(data: SiteData): Promise<void> {
  await fs.writeFile(DATA_FILE, JSON.stringify(data, null, 2), "utf-8");
}

export function isResourceKey(value: string): value is ResourceKey {
  return RESOURCE_KEYS.includes(value as ResourceKey);
}

export function createId(prefix: string): string {
  return `${prefix}_${randomUUID().slice(0, 8)}`;
}

export function mapEmbedSrc(data: SiteData): string {
  const iframe = data.settings.map.embedIframe.trim();
  if (iframe) {
    const srcMatch = iframe.match(/src=["']([^"']+)["']/i);
    if (srcMatch?.[1]) {
      try {
        const parsed = new URL(srcMatch[1]);
        const allowedHosts = new Set([
          "google.com",
          "www.google.com",
          "maps.google.com",
          "www.google.com.vn",
          "maps.google.com.vn",
        ]);
        if (
          parsed.protocol === "https:" &&
          allowedHosts.has(parsed.hostname) &&
          parsed.pathname.startsWith("/maps")
        ) {
          return parsed.toString();
        }
      } catch {
        if (process.env.NODE_ENV !== "production") {
          console.warn("Invalid Google Maps iframe URL.");
        }
      }
    }
  }

  const query = data.settings.map.address?.trim()
    ? data.settings.map.address.trim()
    : `${data.settings.map.latitude},${data.settings.map.longitude}`;

  return `https://maps.google.com/maps?q=${encodeURIComponent(query)}&t=&z=14&ie=UTF8&iwloc=&output=embed`;
}
