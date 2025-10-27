import Link from "next/link";
import { headers } from "next/headers";
import { prisma } from "@/lib/db";
import { getTenantFromHost } from "@/lib/tenant";
import { createProduct } from "./actions";

export default async function ProductsPage() {
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
      <header className="flex items-center justify-between">
        <h1 className="text-2xl font-bold">Products</h1>
        <Link className="text-sm text-blue-600 hover:underline" href="/products">
          View storefront
        </Link>
      </header>

      <form action={createProduct} className="grid grid-cols-1 gap-4 rounded-md border p-4">
        <div>
          <label htmlFor="title" className="block text-sm font-medium">Title</label>
          <input id="title" name="title" className="mt-1 w-full rounded-md border px-2 py-1" required />
        </div>
        <div>
          <label htmlFor="slug" className="block text-sm font-medium">Slug</label>
          <input id="slug" name="slug" className="mt-1 w-full rounded-md border px-2 py-1" required />
        </div>
        <div className="grid grid-cols-3 gap-4">
          <div>
            <label htmlFor="priceDZD" className="block text-sm font-medium">Price (DZD)</label>
            <input id="priceDZD" name="priceDZD" type="number" min={0} className="mt-1 w-full rounded-md border px-2 py-1" required />
          </div>
          <div>
            <label htmlFor="vatRate" className="block text-sm font-medium">VAT %</label>
            <input id="vatRate" name="vatRate" type="number" min={0} max={100} className="mt-1 w-full rounded-md border px-2 py-1" defaultValue={19} required />
          </div>
          <div>
            <label htmlFor="stock" className="block text-sm font-medium">Stock</label>
            <input id="stock" name="stock" type="number" min={0} className="mt-1 w-full rounded-md border px-2 py-1" defaultValue={0} required />
          </div>
        </div>
        <div>
          <button type="submit" className="rounded-md bg-blue-600 px-3 py-1.5 text-white hover:bg-blue-700">Create</button>
        </div>
      </form>

      <ul className="divide-y rounded-md border">
        {products.length === 0 ? (
          <li className="p-4 text-sm text-gray-600">No products yet.</li>
        ) : (
          products.map((p) => (
            <li key={p.id} className="flex items-center justify-between p-4">
              <div>
                <div className="font-medium">{p.title}</div>
                <div className="text-xs text-gray-600">/{p.slug} · {p.priceDZD} DZD · Stock {p.stock}</div>
              </div>
              <Link href={`/products/${p.slug}`} className="text-sm text-blue-600 hover:underline">View</Link>
            </li>
          ))
        )}
      </ul>
    </div>
  );
}
