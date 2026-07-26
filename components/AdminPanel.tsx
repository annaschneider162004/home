"use client";

import { useMemo, useState } from "react";

type ResourceKey = "courses" | "teachers" | "users" | "posts";

type ResourceItem = Record<string, string> & { id: string };

type SettingsData = {
  map: { address: string; latitude: string; longitude: string; embedIframe: string };
  seo: Record<string, { title: string; description: string; keywords: string; ogImage: string }>;
  baseUrl: string;
};

const menu = [
  ["dashboard", "Dashboard"],
  ["courses", "Khóa học"],
  ["teachers", "Giảng viên"],
  ["users", "Học viên/Người dùng"],
  ["posts", "Thư viện/Bài viết"],
  ["settings", "SEO & Google Map"],
] as const;

const fieldMap: Record<ResourceKey, string[]> = {
  courses: ["name", "description", "level", "price", "image"],
  teachers: ["name", "specialty", "bio", "image"],
  users: ["name", "email", "role"],
  posts: ["title", "summary", "category"],
};

export default function AdminPanel({
  initialRecords,
  initialSettings,
}: {
  initialRecords: Record<ResourceKey, ResourceItem[]>;
  initialSettings: SettingsData;
}) {
  const [active, setActive] = useState<(typeof menu)[number][0]>("dashboard");
  const [records, setRecords] = useState<Record<ResourceKey, ResourceItem[]>>(initialRecords);
  const [query, setQuery] = useState("");
  const [page, setPage] = useState(1);
  const [settings, setSettings] = useState<SettingsData>(initialSettings);

  const pageSize = 5;

  async function loadResource(resource: ResourceKey) {
    const res = await fetch(`/api/admin/${resource}`, { cache: "no-store" });
    const data = (await res.json()) as ResourceItem[];
    setRecords((prev) => ({ ...prev, [resource]: data }));
  }

  async function loadSettings() {
    const res = await fetch("/api/admin/settings", { cache: "no-store" });
    const data = (await res.json()) as SettingsData;
    setSettings(data);
  }

  const currentResource = active as ResourceKey;
  const filtered = useMemo(() => {
    if (!["courses", "teachers", "users", "posts"].includes(active)) {
      return [] as ResourceItem[];
    }

    return records[currentResource].filter((item) =>
      JSON.stringify(item).toLowerCase().includes(query.toLowerCase()),
    );
  }, [active, currentResource, query, records]);

  const paged = filtered.slice((page - 1) * pageSize, page * pageSize);
  const totalPages = Math.max(1, Math.ceil(filtered.length / pageSize));

  async function saveResource(resource: ResourceKey, payload: ResourceItem) {
    const isUpdate = Boolean(payload.id);
    const endpoint = isUpdate ? `/api/admin/${resource}/${payload.id}` : `/api/admin/${resource}`;

    await fetch(endpoint, {
      method: isUpdate ? "PUT" : "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    });

    await loadResource(resource);
  }

  async function deleteResource(resource: ResourceKey, id: string) {
    await fetch(`/api/admin/${resource}/${id}`, { method: "DELETE" });
    await loadResource(resource);
  }

  async function updateSettings(updated: SettingsData) {
    await fetch("/api/admin/settings", {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(updated),
    });
    await loadSettings();
  }

  return (
    <div className="admin-layout">
      <aside className="admin-sidebar">
        <h2>Admin Panel</h2>
        {menu.map(([key, label]) => (
          <button
            key={key}
            className={active === key ? "active" : ""}
            onClick={() => {
              setActive(key);
              setPage(1);
            }}
          >
            {label}
          </button>
        ))}
        <button
          onClick={async () => {
            await fetch("/api/admin/logout", { method: "POST" });
            window.location.href = "/admin/login";
          }}
        >
          Đăng xuất
        </button>
      </aside>

      <section className="admin-content">
        {active === "dashboard" && (
          <div className="dashboard-grid">
            <article className="card"><h3>Học viên/Người dùng</h3><p>{records.users.length}</p></article>
            <article className="card"><h3>Khóa học</h3><p>{records.courses.length}</p></article>
            <article className="card"><h3>Giảng viên</h3><p>{records.teachers.length}</p></article>
            <article className="card"><h3>Bài viết</h3><p>{records.posts.length}</p></article>
          </div>
        )}

        {(["courses", "teachers", "users", "posts"] as ResourceKey[]).includes(active as ResourceKey) && (
          <ResourceManager
            resource={currentResource}
            fields={fieldMap[currentResource]}
            query={query}
            onQuery={(value) => {
              setQuery(value);
              setPage(1);
            }}
            items={paged}
            page={page}
            totalPages={totalPages}
            onPage={setPage}
            onSave={saveResource}
            onDelete={deleteResource}
          />
        )}

        {active === "settings" && <SettingsManager settings={settings} onSave={updateSettings} />}
      </section>
    </div>
  );
}

function ResourceManager({
  resource,
  fields,
  query,
  onQuery,
  items,
  page,
  totalPages,
  onPage,
  onSave,
  onDelete,
}: {
  resource: ResourceKey;
  fields: string[];
  query: string;
  onQuery: (value: string) => void;
  items: ResourceItem[];
  page: number;
  totalPages: number;
  onPage: (page: number) => void;
  onSave: (resource: ResourceKey, payload: ResourceItem) => Promise<void>;
  onDelete: (resource: ResourceKey, id: string) => Promise<void>;
}) {
  const [form, setForm] = useState<ResourceItem>({ id: "" });

  return (
    <div>
      <h2>Quản lý {resource}</h2>
      <input placeholder="Tìm kiếm..." value={query} onChange={(e) => onQuery(e.target.value)} />

      <div className="admin-form-grid">
        {fields.map((field) => (
          <input
            key={field}
            placeholder={field}
            value={form[field] ?? ""}
            onChange={(e) => setForm((prev) => ({ ...prev, [field]: e.target.value }))}
          />
        ))}
      </div>
      <button
        className="btn btn-solid"
        onClick={async () => {
          await onSave(resource, form);
          setForm({ id: "" });
        }}
      >
        Lưu
      </button>

      <table>
        <thead>
          <tr>
            {fields.map((field) => (
              <th key={field}>{field}</th>
            ))}
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody>
          {items.map((item) => (
            <tr key={item.id}>
              {fields.map((field) => (
                <td key={field}>{item[field]}</td>
              ))}
              <td>
                <button onClick={() => setForm(item)}>Sửa</button>
                <button onClick={() => onDelete(resource, item.id)}>Xóa</button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>

      <div className="pagination">
        <button disabled={page <= 1} onClick={() => onPage(page - 1)}>
          Trước
        </button>
        <span>
          {page}/{totalPages}
        </span>
        <button disabled={page >= totalPages} onClick={() => onPage(page + 1)}>
          Sau
        </button>
      </div>
    </div>
  );
}

function SettingsManager({
  settings,
  onSave,
}: {
  settings: SettingsData;
  onSave: (settings: SettingsData) => Promise<void>;
}) {
  const [form, setForm] = useState(settings);

  return (
    <div>
      <h2>Quản lý cấu hình SEO & Google Map</h2>
      <div className="admin-form-grid">
        <input
          placeholder="Base URL"
          value={form.baseUrl}
          onChange={(e) => setForm((prev) => ({ ...prev, baseUrl: e.target.value }))}
        />
        <input
          placeholder="Địa chỉ"
          value={form.map.address}
          onChange={(e) =>
            setForm((prev) => ({ ...prev, map: { ...prev.map, address: e.target.value } }))
          }
        />
        <input
          placeholder="Latitude"
          value={form.map.latitude}
          onChange={(e) =>
            setForm((prev) => ({ ...prev, map: { ...prev.map, latitude: e.target.value } }))
          }
        />
        <input
          placeholder="Longitude"
          value={form.map.longitude}
          onChange={(e) =>
            setForm((prev) => ({ ...prev, map: { ...prev.map, longitude: e.target.value } }))
          }
        />
        <textarea
          placeholder="Google Maps iframe"
          value={form.map.embedIframe}
          onChange={(e) =>
            setForm((prev) => ({ ...prev, map: { ...prev.map, embedIframe: e.target.value } }))
          }
        />
      </div>

      {Object.entries(form.seo).map(([key, value]) => (
        <div key={key} className="seo-block">
          <h3>SEO - {key}</h3>
          <div className="admin-form-grid">
            <input
              placeholder="Title"
              value={value.title}
              onChange={(e) =>
                setForm((prev) => ({
                  ...prev,
                  seo: { ...prev.seo, [key]: { ...prev.seo[key], title: e.target.value } },
                }))
              }
            />
            <input
              placeholder="Description"
              value={value.description}
              onChange={(e) =>
                setForm((prev) => ({
                  ...prev,
                  seo: { ...prev.seo, [key]: { ...prev.seo[key], description: e.target.value } },
                }))
              }
            />
            <input
              placeholder="Keywords"
              value={value.keywords}
              onChange={(e) =>
                setForm((prev) => ({
                  ...prev,
                  seo: { ...prev.seo, [key]: { ...prev.seo[key], keywords: e.target.value } },
                }))
              }
            />
            <input
              placeholder="OG Image"
              value={value.ogImage}
              onChange={(e) =>
                setForm((prev) => ({
                  ...prev,
                  seo: { ...prev.seo, [key]: { ...prev.seo[key], ogImage: e.target.value } },
                }))
              }
            />
          </div>
        </div>
      ))}

      <button className="btn btn-solid" onClick={() => onSave(form)}>
        Lưu cấu hình
      </button>
    </div>
  );
}
