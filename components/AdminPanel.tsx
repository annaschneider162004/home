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
  const [error, setError] = useState("");

  const pageSize = 5;

  async function loadResource(resource: ResourceKey) {
    try {
      setError("");
      const res = await fetch(`/api/admin/${resource}`, { cache: "no-store" });
      if (!res.ok) throw new Error("Không thể tải dữ liệu");
      const data = (await res.json()) as ResourceItem[];
      setRecords((prev) => ({ ...prev, [resource]: data }));
    } catch {
      setError("Không thể tải dữ liệu quản trị. Vui lòng thử lại.");
    }
  }

  async function loadSettings() {
    try {
      setError("");
      const res = await fetch("/api/admin/settings", { cache: "no-store" });
      if (!res.ok) throw new Error("Không thể tải cấu hình");
      const data = (await res.json()) as SettingsData;
      setSettings(data);
    } catch {
      setError("Không thể tải cấu hình SEO/Map.");
    }
  }

  const currentResource = active as ResourceKey;
  const filtered = useMemo(() => {
    if (!["courses", "teachers", "users", "posts"].includes(active)) {
      return [] as ResourceItem[];
    }

    return records[currentResource].filter((item) =>
      Object.values(item).some((value) => value.toLowerCase().includes(query.toLowerCase())),
    );
  }, [active, currentResource, query, records]);

  const paged = filtered.slice((page - 1) * pageSize, page * pageSize);
  const totalPages = Math.max(1, Math.ceil(filtered.length / pageSize));

  async function saveResource(resource: ResourceKey, payload: ResourceItem) {
    try {
      const isUpdate = Boolean(payload.id);
      const endpoint = isUpdate ? `/api/admin/${resource}/${payload.id}` : `/api/admin/${resource}`;
      const res = await fetch(endpoint, {
        method: isUpdate ? "PUT" : "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });
      if (!res.ok) throw new Error("Save failed");
      await loadResource(resource);
    } catch {
      setError("Lưu dữ liệu thất bại.");
    }
  }

  async function deleteResource(resource: ResourceKey, id: string) {
    try {
      const res = await fetch(`/api/admin/${resource}/${id}`, { method: "DELETE" });
      if (!res.ok) throw new Error("Delete failed");
      await loadResource(resource);
    } catch {
      setError("Xóa dữ liệu thất bại.");
    }
  }

  async function updateSettings(updated: SettingsData) {
    try {
      const res = await fetch("/api/admin/settings", {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(updated),
      });
      if (!res.ok) throw new Error("Update failed");
      await loadSettings();
    } catch {
      setError("Lưu cấu hình thất bại.");
    }
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
            try {
              const res = await fetch("/api/admin/logout", { method: "POST" });
              if (!res.ok) throw new Error("Logout failed");
              window.location.href = "/admin/login";
            } catch {
              setError("Đăng xuất thất bại. Vui lòng thử lại.");
            }
          }}
        >
          Đăng xuất
        </button>
      </aside>

      <section className="admin-content">
        {error && <p className="error">{error}</p>}
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

        {active === "settings" && (
          <SettingsManager settings={settings} onChange={setSettings} onSave={updateSettings} />
        )}
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
  onChange,
  onSave,
}: {
  settings: SettingsData;
  onChange: (settings: SettingsData) => void;
  onSave: (settings: SettingsData) => Promise<void>;
}) {
  return (
    <div>
      <h2>Quản lý cấu hình SEO & Google Map</h2>
      <div className="admin-form-grid">
        <input
          placeholder="Base URL"
          value={settings.baseUrl}
          onChange={(e) => onChange({ ...settings, baseUrl: e.target.value })}
        />
        <input
          placeholder="Địa chỉ"
          value={settings.map.address}
          onChange={(e) =>
            onChange({ ...settings, map: { ...settings.map, address: e.target.value } })
          }
        />
        <input
          placeholder="Latitude"
          value={settings.map.latitude}
          onChange={(e) =>
            onChange({ ...settings, map: { ...settings.map, latitude: e.target.value } })
          }
        />
        <input
          placeholder="Longitude"
          value={settings.map.longitude}
          onChange={(e) =>
            onChange({ ...settings, map: { ...settings.map, longitude: e.target.value } })
          }
        />
        <textarea
          placeholder="Google Maps iframe"
          value={settings.map.embedIframe}
          onChange={(e) =>
            onChange({ ...settings, map: { ...settings.map, embedIframe: e.target.value } })
          }
        />
      </div>

      {Object.entries(settings.seo).map(([key, value]) => (
        <div key={key} className="seo-block">
          <h3>SEO - {key}</h3>
          <div className="admin-form-grid">
            <input
              placeholder="Title"
              value={value.title}
              onChange={(e) =>
                onChange({
                  ...settings,
                  seo: { ...settings.seo, [key]: { ...settings.seo[key], title: e.target.value } },
                })
              }
            />
            <input
              placeholder="Description"
              value={value.description}
              onChange={(e) =>
                onChange({
                  ...settings,
                  seo: { ...settings.seo, [key]: { ...settings.seo[key], description: e.target.value } },
                })
              }
            />
            <input
              placeholder="Keywords"
              value={value.keywords}
              onChange={(e) =>
                onChange({
                  ...settings,
                  seo: { ...settings.seo, [key]: { ...settings.seo[key], keywords: e.target.value } },
                })
              }
            />
            <input
              placeholder="OG Image"
              value={value.ogImage}
              onChange={(e) =>
                onChange({
                  ...settings,
                  seo: { ...settings.seo, [key]: { ...settings.seo[key], ogImage: e.target.value } },
                })
              }
            />
          </div>
        </div>
      ))}

      <button className="btn btn-solid" onClick={() => onSave(settings)}>
        Lưu cấu hình
      </button>
    </div>
  );
}
