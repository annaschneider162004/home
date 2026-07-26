import { NextResponse } from "next/server";
import { isResourceKey, readData, writeData } from "@/lib/data";
import { isAdminAuthenticated } from "@/lib/auth";

export async function PUT(
  request: Request,
  { params }: { params: Promise<{ resource: string; id: string }> },
) {
  if (!(await isAdminAuthenticated())) {
    return NextResponse.json({ message: "Unauthorized" }, { status: 401 });
  }

  const { resource, id } = await params;
  if (!isResourceKey(resource)) {
    return NextResponse.json({ message: "Invalid resource" }, { status: 400 });
  }

  const payload = (await request.json()) as Record<string, string>;
  const data = await readData();
  const index = data[resource].findIndex((item) => item.id === id);

  if (index < 0) {
    return NextResponse.json({ message: "Not found" }, { status: 404 });
  }

  data[resource][index] = { ...payload, id } as never;
  await writeData(data);
  return NextResponse.json(data[resource][index]);
}

export async function DELETE(
  _request: Request,
  { params }: { params: Promise<{ resource: string; id: string }> },
) {
  if (!(await isAdminAuthenticated())) {
    return NextResponse.json({ message: "Unauthorized" }, { status: 401 });
  }

  const { resource, id } = await params;
  if (!isResourceKey(resource)) {
    return NextResponse.json({ message: "Invalid resource" }, { status: 400 });
  }

  const data = await readData();
  data[resource] = data[resource].filter((item) => item.id !== id) as never;
  await writeData(data);
  return NextResponse.json({ success: true });
}
