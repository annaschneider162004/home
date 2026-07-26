import type { Metadata } from "next";
import "./globals.css";

export const metadata: Metadata = {
  title: "MusicOfEveryone - Music Club",
  description: "Nền tảng học nhạc trực tuyến cho mọi lứa tuổi",
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="vi">
      <body>{children}</body>
    </html>
  );
}
