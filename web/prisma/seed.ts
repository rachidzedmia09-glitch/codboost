import { PrismaClient } from "../src/generated/prisma/client";

const prisma = new PrismaClient();

async function main() {
  const subdomain = process.env.DEFAULT_TENANT_SUBDOMAIN ?? "demo";

  const tenant = await prisma.tenant.upsert({
    where: { subdomain },
    update: {},
    create: { name: "Demo Shop", subdomain },
  });

  const exists = await prisma.product.findFirst({
    where: { tenantId: tenant.id, slug: "sample-product" },
  });
  if (!exists) {
    await prisma.product.create({
      data: {
        tenantId: tenant.id,
        title: "Sample Product",
        slug: "sample-product",
        priceDZD: 1500,
        vatRate: 19,
        stock: 10,
      },
    });
  }
}

main().finally(async () => {
  await prisma.$disconnect();
});
