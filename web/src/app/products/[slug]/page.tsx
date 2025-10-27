import Link from "next/link";
import { headers } from "next/headers";
import { notFound } from "next/navigation";
import { prisma } from "@/lib/db";
import { getTenantFromHost } from "@/lib/tenant";

type Props = { params: { slug: string } };

export default async function ProductDetailPage({ params }: Props) {
  const { slug } = params;
  const host = headers().get("host");
  const tenant = await getTenantFromHost(host ?? undefined);
  if (!tenant) {
    notFound();
  }

  const product = await prisma.product.findFirst({
    where: { tenantId: tenant!.id, slug },
  });

  if (!product) notFound();

  return (
    <div className="mx-auto max-w-2xl p-6 space-y-6">
      <Link href="/products" className="text-sm text-blue-600 hover:underline">Back to products</Link>
      <h1 className="text-2xl font-bold">{product.title}</h1>
      <div className="text-gray-700">{product.priceDZD} DZD</div>
      <div className="text-sm text-gray-600">VAT: {product.vatRate}% · Stock: {product.stock}</div>
    </div>
  );
}
