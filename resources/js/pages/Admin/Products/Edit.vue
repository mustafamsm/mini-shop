<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { ArrowLeft, Check, CircleDollarSign, FileText, Package, Plus, Save, Tag, Trash2, Warehouse } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Product } from '@/types';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import ProductController from '@/actions/App/Http/Controllers/Admin/ProductController';
import ProductVariantController from '@/actions/App/Http/Controllers/Admin/ProductVariantController';
const props = defineProps<{
    product: Product;
    categories: {
        id: number;
        name: string;
    }[];
}>();
const form = useForm({
    category_id: props.product.category_id ?? null,
    name: props.product.name,
    slug: props.product.slug,
    description: props.product.description ?? '',
    base_price: props.product.base_price,
    is_active: props.product.is_active,
});
function slugify(): void {
    form.slug = form.name
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/(^-|-$)/g, '');
}
function submit(): void {
    form.submit(ProductController.update(props.product.id));
}
const newVariant = useForm({ sku: '', name: '', stock: 0 });

function addVariant(): void {
    newVariant.submit(ProductVariantController.store(props.product.id), {
        preserveScroll: true,
        onSuccess: () => newVariant.reset(),
    });
}
function updateStock(variantId: number, stock: number): void {
    router.patch(
            ProductVariantController.update(variantId).url,
        { stock },
        {
            preserveScroll: true,
        },
    );
}
function removeVariant(variantId: number): void {
    router.delete(ProductVariantController.destroy(variantId).url, {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head :title="`Edit ${product.name}`" />
    <AppLayout>
        <div class="product-edit-shell min-h-full p-4 sm:p-6 lg:p-8">
            <div class="mx-auto max-w-6xl space-y-6">
                <div class="flex items-center gap-3">
                    <Button as-child variant="ghost" size="icon" aria-label="Back to products">
                        <a href="/admin/products"><ArrowLeft class="size-4" /></a>
                    </Button>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#9b7a43] dark:text-[#d7b77a]">Catalogue</p>
                        <h1 class="mt-1 truncate font-display text-3xl tracking-tight sm:text-4xl">Edit product</h1>
                    </div>
                    <Badge :variant="form.is_active ? 'secondary' : 'outline'" class="ml-auto hidden gap-1.5 sm:flex">
                        <span :class="['size-1.5 rounded-full', form.is_active ? 'bg-emerald-500' : 'bg-muted-foreground']" />
                        {{ form.is_active ? 'Active listing' : 'Draft' }}
                    </Badge>
                </div>

                <div class="grid gap-6 lg:grid-cols-[1fr_340px]">
                    <Card class="border-border/70 shadow-sm">
                        <CardHeader class="border-b border-border/70 bg-muted/20">
                            <div class="flex items-start gap-3">
                                <div class="rounded-xl bg-[#eee8d9] p-3 text-[#8d6b31] dark:bg-[#8d6b31]/25 dark:text-[#e8cb92]"><Package class="size-5" /></div>
                                <div>
                                    <CardTitle class="font-display text-2xl">Product details</CardTitle>
                                    <CardDescription class="mt-1">Update the information shown in your shop.</CardDescription>
                                </div>
                            </div>
                        </CardHeader>
                        <CardContent class="p-6 sm:p-8">
                            <form @submit.prevent="submit" class="space-y-6">
                                <div class="grid gap-6 sm:grid-cols-2">
                                    <div class="space-y-2 sm:col-span-2">
                                        <Label for="name">Product name</Label>
                                        <Input id="name" v-model="form.name" @blur="slugify" />
                                        <p v-if="form.errors.name" class="text-xs text-destructive">{{ form.errors.name }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="category">Category</Label>
                                        <select id="category" v-model="form.category_id" class="h-9 w-full rounded-md border border-input bg-transparent px-3 text-sm shadow-xs outline-none transition focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 dark:bg-input/30">
                                            <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                                        </select>
                                        <p v-if="form.errors.category_id" class="text-xs text-destructive">{{ form.errors.category_id }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="price">Base price</Label>
                                        <div class="relative">
                                            <CircleDollarSign class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                                            <Input id="price" v-model.number="form.base_price" type="number" min="0" step="0.01" class="pl-9" />
                                        </div>
                                        <p v-if="form.errors.base_price" class="text-xs text-destructive">{{ form.errors.base_price }}</p>
                                    </div>
                                    <div class="space-y-2 sm:col-span-2">
                                        <Label for="slug">URL slug</Label>
                                        <div class="relative">
                                            <Tag class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                                            <Input id="slug" v-model="form.slug" class="pl-9 font-mono text-sm" />
                                        </div>
                                        <p v-if="form.errors.slug" class="text-xs text-destructive">{{ form.errors.slug }}</p>
                                    </div>
                                    <div class="space-y-2 sm:col-span-2">
                                        <Label for="description">Description</Label>
                                        <div class="relative">
                                            <FileText class="pointer-events-none absolute left-3 top-3 size-4 text-muted-foreground" />
                                            <textarea id="description" v-model="form.description" rows="5" class="w-full resize-y rounded-md border border-input bg-transparent px-3 py-2 pl-9 text-sm shadow-xs outline-none transition placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 dark:bg-input/30" />
                                        </div>
                                        <p v-if="form.errors.description" class="text-xs text-destructive">{{ form.errors.description }}</p>
                                    </div>
                                </div>
                                <div class="flex flex-col justify-between gap-4 rounded-xl border border-dashed border-border bg-muted/20 p-4 sm:flex-row sm:items-center">
                                    <div class="flex items-start gap-3">
                                        <Checkbox id="active" v-model="form.is_active" />
                                        <div>
                                            <Label for="active" class="cursor-pointer">Publish product</Label>
                                            <p class="mt-1 text-xs text-muted-foreground">Make this product visible in the shop.</p>
                                        </div>
                                    </div>
                                    <Badge variant="secondary" class="w-fit gap-1.5"><Check class="size-3.5" /> Changes ready</Badge>
                                </div>
                                <div class="flex flex-col-reverse gap-3 border-t border-border/70 pt-6 sm:flex-row sm:justify-end">
                                    <Button as-child type="button" variant="outline"><a href="/admin/products">Cancel</a></Button>
                                    <Button type="submit" :disabled="form.processing"><Save class="size-4" />{{ form.processing ? 'Saving...' : 'Save changes' }}</Button>
                                </div>
                            </form>
                        </CardContent>
                    </Card>

                    <Card class="h-fit border-border/70 shadow-sm">
                        <CardHeader>
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <CardTitle class="font-display text-2xl">Variants</CardTitle>
                                    <CardDescription class="mt-1">Manage options and stock levels.</CardDescription>
                                </div>
                                <div class="rounded-xl bg-[#e6eee5] p-2.5 text-[#48624f] dark:bg-[#48624f]/25 dark:text-[#a8d0ae]"><Warehouse class="size-5" /></div>
                            </div>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div v-if="!product.variants?.length" class="rounded-xl border border-dashed border-border bg-muted/20 px-4 py-6 text-center text-sm text-muted-foreground">No variants yet. Add one below.</div>
                            <div v-for="variant in product.variants" :key="variant.id" class="rounded-xl border border-border/70 p-3 transition-colors hover:bg-muted/30">
                                <div class="flex items-center gap-3">
                                    <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-muted text-muted-foreground"><Tag class="size-4" /></div>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-semibold">{{ variant.name ?? 'Unnamed variant' }}</p>
                                        <p class="truncate font-mono text-[11px] text-muted-foreground">{{ variant.sku }}</p>
                                    </div>
                                    <Button variant="ghost" size="icon-sm" class="text-muted-foreground hover:text-destructive" aria-label="Remove variant" @click="removeVariant(variant.id)"><Trash2 class="size-4" /></Button>
                                </div>
                                <div class="mt-3 flex items-center justify-between gap-3 border-t border-border/60 pt-3">
                                    <Label :for="`stock-${variant.id}`" class="text-xs text-muted-foreground">Stock on hand</Label>
                                    <Input :id="`stock-${variant.id}`" type="number" min="0" :value="variant.stock" class="h-8 w-24 text-right text-sm" @change="updateStock(variant.id, +($event.target as HTMLInputElement).value)" />
                                </div>
                            </div>
                            <form @submit.prevent="addVariant" class="space-y-3 rounded-xl bg-muted/30 p-4">
                                <p class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Add variant</p>
                                <Input v-model="newVariant.sku" placeholder="SKU" required />
                                <Input v-model="newVariant.name" placeholder="Variant name, e.g. Large" />
                                <div class="flex gap-2">
                                    <Input v-model.number="newVariant.stock" type="number" min="0" placeholder="Stock" class="flex-1" />
                                    <Button type="submit" size="icon" aria-label="Add variant"><Plus class="size-4" /></Button>
                                </div>
                            </form>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.product-edit-shell {
    background: radial-gradient(circle at 82% 0%, rgba(176, 141, 87, 0.1), transparent 26rem), var(--background);
}

.font-display {
    font-family: var(--font-display);
}
</style>
