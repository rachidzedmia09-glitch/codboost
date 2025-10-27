import { z } from "zod";

export const createProductSchema = z.object({
  title: z.string().min(1, "Title is required").max(120),
  slug: z
    .string()
    .min(1, "Slug is required")
    .max(140)
    .regex(/^[a-z0-9-]+$/, "Use lowercase letters, numbers, and hyphens"),
  priceDZD: z.coerce.number().int().nonnegative(),
  vatRate: z.coerce.number().int().min(0).max(100),
  stock: z.coerce.number().int().min(0),
});

export type CreateProductInput = z.infer<typeof createProductSchema>;
