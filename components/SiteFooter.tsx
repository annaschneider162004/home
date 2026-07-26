import { mapEmbedSrc, readData } from "@/lib/data";

export default async function SiteFooter() {
  const data = await readData();
  const mapSrc = mapEmbedSrc(data);

  return (
    <footer className="site-footer">
      <div className="container footer-grid">
        <div>
          <h3>{data.settings.siteName}</h3>
          <p>Email: support@musicofeveryone.vn</p>
          <p>Hotline: 0900 000 000</p>
          <p>Địa chỉ: {data.settings.map.address || "Đang cập nhật"}</p>
        </div>
        <div>
          <h3>Kết nối</h3>
          <p>Facebook · YouTube · TikTok</p>
          <p>Cộng đồng học viên toàn quốc.</p>
        </div>
        <div>
          <h3>Bản đồ</h3>
          <iframe
            title="Google Map"
            src={mapSrc}
            width="100%"
            height="180"
            loading="lazy"
            referrerPolicy="no-referrer-when-downgrade"
          />
        </div>
      </div>
      <p className="copyright">© {new Date().getFullYear()} MusicOfEveryone. All rights reserved.</p>
    </footer>
  );
}
