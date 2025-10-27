import Link from "next/link";
import { headers } from "next/headers";
import { prisma } from "@/lib/db";
import { getTenantFromHost } from "@/lib/tenant";

export default async function StorefrontProductsPage() {
  const host = headers().get("host");
  const tenant = await getTenantFromHost(host ?? undefined);
  if (!tenant) {
    return (
      <div className="p-6">
        <h1 className="text-xl font-semibold">No tenant configured</h1>
        <p className="text-sm text-gray-600 mt-2">Seed a tenant to continue.</p>
      </div>
    );
  }

  const products = await prisma.product.findMany({
    where: { tenantId: tenant.id },
    orderBy: { createdAt: "desc" },
  });

  return (
    <div className="mx-auto max-w-3xl p-6 space-y-8">
      <h1 className="text-2xl font-bold">Products</h1>
      <ul className="grid grid-cols-1 gap-4">
        {products.length === 0 ? (
          <li className="text-sm text-gray-600">No products yet.</li>
        ) : (
          products.map((p) => (
            <li key={p.id} className="rounded-md border p-4">
              <div className="flex items-center justify-between">
                <div>
                  <div className="font-medium">{p.title}</div>
                  <div className="text-xs text-gray-600">{p.priceDZD} DZD</div>
                </div>
                <Link href={`/products/${p.slug}`} className="text-sm text-blue-600 hover:underline">
                  View
                </Link>
              </div>
            </li>
          ))
        )}
      </ul>
    </div>
  );
}
