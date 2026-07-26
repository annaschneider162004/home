import { NextResponse } from "next/server";
import { createId, isResourceKey, readData, writeData } from "@/lib/data";
import { isAdminAuthenticated } from "@/lib/auth";

export async function GET(
  _request: Request,
  { params }: { params: Promise<{ resource: string }> },
) {
  if (!(await isAdminAuthenticated())) {
    return NextResponse.json({ message: "Unauthorized" }, { status: 401 });
  }

  const { resource } = await params;
  if (!isResourceKey(resource)) {
    return NextResponse.json({ message: "Invalid resource" }, { status: 400 });
  }

  const data = await readData();
  return NextResponse.json(data[resource]);
}

export async function POST(
  request: Request,
  { params }: { params: Promise<{ resource: string }> },
) {
  if (!(await isAdminAuthenticated())) {
    return NextResponse.json({ message: "Unauthorized" }, { status: 401 });
  }

  const { resource } = await params;
  if (!isResourceKey(resource)) {
    return NextResponse.json({ message: "Invalid resource" }, { status: 400 });
  }

  const payload = (await request.json()) as Record<string, string>;
  const data = await readData();
  const newItem = { ...payload, id: createId(resource.slice(0, 1)) };

  data[resource].push(newItem as never);
  await writeData(data);
  return NextResponse.json(newItem, { status: 201 });
}
