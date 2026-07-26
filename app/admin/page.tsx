import AdminPanel from "@/components/AdminPanel";
import { readData } from "@/lib/data";

export const dynamic = "force-dynamic";

export default async function AdminPage() {
  const data = await readData();

  return (
    <AdminPanel
      initialRecords={{
        courses: data.courses,
        teachers: data.teachers,
        users: data.users,
        posts: data.posts,
      }}
      initialSettings={{
        baseUrl: data.settings.baseUrl,
        map: data.settings.map,
        seo: data.settings.seo,
      }}
    />
  );
}
