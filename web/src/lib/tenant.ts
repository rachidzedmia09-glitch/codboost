import { prisma } from "@/lib/db";

export type Tenant = { id: string; name: string; subdomain: string };

export async function getTenantFromHost(host?: string | null): Promise<Tenant | null> {
  // In dev, allow fallback to DEFAULT_TENANT_SUBDOMAIN
  const fallback = process.env.DEFAULT_TENANT_SUBDOMAIN ?? "demo";
  const subdomain = extractSubdomain(host) ?? fallback;

  const tenant = await prisma.tenant.findUnique({ where: { subdomain } });
  if (!tenant) return null;
  return { id: tenant.id, name: tenant.name, subdomain: tenant.subdomain };
}

export function extractSubdomain(host?: string | null): string | null {
  if (!host) return null;
  const withoutPort = host.split(":")[0];
  const parts = withoutPort.split(".");
  if (parts.length < 3) return null; // e.g. localhost or example.com
  return parts[0];
}
