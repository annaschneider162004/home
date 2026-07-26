import type { ResourceKey, SiteData } from "@/lib/types";

const resourceFields: Record<ResourceKey, string[]> = {
  courses: ["name", "description", "level", "price", "image"],
  teachers: ["name", "specialty", "bio", "image"],
  users: ["name", "email", "role"],
  posts: ["title", "summary", "category"],
};

export function sanitizeResourcePayload(resource: ResourceKey, payload: Record<string, unknown>) {
  const clean: Record<string, string> = {};
  for (const field of resourceFields[resource]) {
    const value = payload[field];
    clean[field] = typeof value === "string" ? value.trim() : "";
  }

  if (Object.values(clean).some((value) => value.length === 0)) {
    return null;
  }

  return clean;
}

export function validateSettingsPayload(payload: unknown): SiteData["settings"] | null {
  if (!payload || typeof payload !== "object") {
    return null;
  }

  const value = payload as SiteData["settings"];
  if (!value.baseUrl || !value.map || !value.seo) {
    return null;
  }

  if ([value.map.address, value.map.latitude, value.map.longitude, value.map.embedIframe].some((item) => typeof item !== "string")) {
    return null;
  }

  for (const seoValue of Object.values(value.seo)) {
    if (
      !seoValue ||
      typeof seoValue.title !== "string" ||
      typeof seoValue.description !== "string" ||
      typeof seoValue.keywords !== "string" ||
      typeof seoValue.ogImage !== "string"
    ) {
      return null;
    }
  }

  return value;
}
