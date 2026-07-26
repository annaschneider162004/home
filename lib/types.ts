export type SeoConfig = {
  title: string;
  description: string;
  keywords: string;
  ogImage: string;
};

export type SiteData = {
  admin: { username: string; passwordSalt: string; passwordHash: string };
  settings: {
    siteName: string;
    baseUrl: string;
    map: {
      address: string;
      latitude: string;
      longitude: string;
      embedIframe: string;
    };
    seo: Record<string, SeoConfig>;
  };
  courses: Array<{
    id: string;
    name: string;
    description: string;
    level: string;
    price: string;
    image: string;
  }>;
  teachers: Array<{
    id: string;
    name: string;
    specialty: string;
    bio: string;
    image: string;
  }>;
  users: Array<{
    id: string;
    name: string;
    email: string;
    role: string;
  }>;
  posts: Array<{
    id: string;
    title: string;
    summary: string;
    category: string;
  }>;
};

export type ResourceKey = "courses" | "teachers" | "users" | "posts";
