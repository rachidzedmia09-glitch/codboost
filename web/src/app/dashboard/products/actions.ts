"use server";

import { headers } from "next/headers";
import { revalidatePath } from "next/cache";
import { prisma } from "@/lib/db";
import { getTenantFromHost } from "@/lib/tenant";
import { createProductSchema } from "@/lib/validation";

export async function createProduct(formData: FormData) {
  const parsed = createProductSchema.safeParse({
    title: formData.get("title"),
    slug: formData.get("slug"),
    priceDZD: formData.get("priceDZD"),
    vatRate: formData.get("vatRate"),
    stock: formData.get("stock"),
  });

  if (!parsed.success) {
    return { ok: false, errors: parsed.error.flatten().fieldErrors } as const;
  }

  const host = headers().get("host");
  const tenant = await getTenantFromHost(host);
  if (!tenant) {
    return { ok: false, errors: { _form: ["Tenant not found"] } } as const;
  }

  try {
    await prisma.product.create({
      data: {
        tenantId: tenant.id,
        title: parsed.data.title,
        slug: parsed.data.slug,
        priceDZD: parsed.data.priceDZD,
        vatRate: parsed.data.vatRate,
        stock: parsed.data.stock,
      },
    });
  } catch (e: unknown) {
    const message = e instanceof Error ? e.message : "Unknown error";
    return { ok: false, errors: { _form: [message] } } as const;
  }

  revalidatePath("/dashboard/products");
  return { ok: true } as const;
}
