<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Check, CircleDollarSign, FileText, PackagePlus, Tag } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';
import ProductController from '@/actions/App/Http/Controllers/Admin/ProductController';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

defineProps<{
    categories: {
        id: number;
        name: string;
    }[];
}>();
const form = useForm({
    category_id: null as number | null,
    name: '',
    slug: '',
    description: '',
    base_price: 0,
    is_active: true,
});
function slugify(): void {
    form.slug = form.name
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/(^-|-$)/g, '');
}
function submit(): void {
    form.submit(ProductController.store());
}
</script>

<template>
    <Head title="New product" />
    <AppLayout>
        <div class="product-form-shell min-h-full p-4 sm:p-6 lg:p-8">
            <div class="mx-auto max-w-5xl space-y-6">
                <div class="flex items-center gap-3">
                    <Button as-child variant="ghost" size="icon" aria-label="Back to products">
                        <a href="/admin/products"><ArrowLeft class="size-4" /></a>
                    </Button>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#9b7a43] dark:text-[#d7b77a]">Catalogue</p>
                        <h1 class="mt-1 font-display text-3xl tracking-tight sm:text-4xl">New product</h1>
                    </div>
                </div>

                <div class="grid gap-6 lg:grid-cols-[1fr_320px]">
                    <Card class="border-border/70 shadow-sm">
                        <CardHeader class="border-b border-border/70 bg-muted/20">
                            <div class="flex items-start gap-3">
                                <div class="rounded-xl bg-[#e6eee5] p-3 text-[#48624f] dark:bg-[#48624f]/25 dark:text-[#a8d0ae]"><PackagePlus class="size-5" /></div>
                                <div>
                                    <CardTitle class="font-display text-2xl">Product details</CardTitle>
                                    <CardDescription class="mt-1">Add the information customers need to make a decision.</CardDescription>
                                </div>
                            </div>
                        </CardHeader>

                        <CardContent class="p-6 sm:p-8">
                            <form @submit.prevent="submit" class="space-y-6">
                                <div class="grid gap-6 sm:grid-cols-2">
                                    <div class="space-y-2 sm:col-span-2">
                                        <Label for="name">Product name</Label>
                                        <Input id="name" v-model="form.name" placeholder="e.g. Everyday canvas tote" @blur="slugify" />
                                        <p v-if="form.errors.name" class="text-xs text-destructive">{{ form.errors.name }}</p>
                                    </div>

                                    <div class="space-y-2">
                                        <Label for="category">Category</Label>
                                        <select id="category" v-model="form.category_id" class="h-9 w-full rounded-md border border-input bg-transparent px-3 text-sm shadow-xs outline-none transition focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 dark:bg-input/30">
                                            <option :value="null" disabled>Select a category</option>
                                            <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                                        </select>
                                        <p v-if="form.errors.category_id" class="text-xs text-destructive">{{ form.errors.category_id }}</p>
                                    </div>

                                    <div class="space-y-2">
                                        <Label for="price">Base price</Label>
                                        <div class="relative">
                                            <CircleDollarSign class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                                            <Input id="price" v-model.number="form.base_price" type="number" min="0" step="0.01" class="pl-9" placeholder="0.00" />
                                        </div>
                                        <p v-if="form.errors.base_price" class="text-xs text-destructive">{{ form.errors.base_price }}</p>
                                    </div>

                                    <div class="space-y-2 sm:col-span-2">
                                        <Label for="slug">URL slug</Label>
                                        <div class="relative">
                                            <Tag class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                                            <Input id="slug" v-model="form.slug" class="pl-9 font-mono text-sm" placeholder="everyday-canvas-tote" />
                                        </div>
                                        <p class="text-xs text-muted-foreground">A clean slug makes your product link easier to share.</p>
                                        <p v-if="form.errors.slug" class="text-xs text-destructive">{{ form.errors.slug }}</p>
                                    </div>

                                    <div class="space-y-2 sm:col-span-2">
                                        <Label for="description">Description</Label>
                                        <div class="relative">
                                            <FileText class="pointer-events-none absolute left-3 top-3 size-4 text-muted-foreground" />
                                            <textarea id="description" v-model="form.description" rows="5" placeholder="Tell customers what makes this product worth choosing..." class="w-full resize-y rounded-md border border-input bg-transparent px-3 py-2 pl-9 text-sm shadow-xs outline-none transition placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 dark:bg-input/30" />
                                        </div>
                                        <p v-if="form.errors.description" class="text-xs text-destructive">{{ form.errors.description }}</p>
                                    </div>
                                </div>

                                <div class="flex flex-col justify-between gap-4 rounded-xl border border-dashed border-border bg-muted/20 p-4 sm:flex-row sm:items-center">
                                    <div class="flex items-start gap-3">
                                        <Checkbox id="active" v-model="form.is_active" />
                                        <div>
                                            <Label for="active" class="cursor-pointer">Publish product</Label>
                                            <p class="mt-1 text-xs text-muted-foreground">Make this product visible in the shop immediately.</p>
                                        </div>
                                    </div>
                                    <Badge variant="secondary" class="w-fit gap-1.5"><Check class="size-3.5" /> Ready to list</Badge>
                                </div>

                                <div class="flex flex-col-reverse gap-3 border-t border-border/70 pt-6 sm:flex-row sm:justify-end">
                                    <Button as-child type="button" variant="outline">
                                        <a href="/admin/products">Cancel</a>
                                    </Button>
                                    <Button type="submit" :disabled="form.processing">
                                        <PackagePlus class="size-4" />
                                        {{ form.processing ? 'Creating...' : 'Create product' }}
                                    </Button>
                                </div>
                            </form>
                        </CardContent>
                    </Card>

                    <Card class="h-fit border-border/70 bg-[#31483a] text-[#f3f0e8] shadow-sm">
                        <CardHeader>
                            <Badge class="w-fit border-[#d7b77a]/30 bg-[#d7b77a]/15 text-[#e8cb92]">Quick guide</Badge>
                            <CardTitle class="mt-3 font-display text-2xl text-[#f3f0e8]">Make it easy to choose.</CardTitle>
                            <CardDescription class="text-[#d8ded5]">A few details help shoppers understand the product before they add it to their cart.</CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-4 text-sm text-[#d8ded5]">
                            <div class="flex gap-3"><span class="font-mono text-[#d7b77a]">01</span><p>Use a clear, recognisable product name.</p></div>
                            <div class="flex gap-3"><span class="font-mono text-[#d7b77a]">02</span><p>Set a price that matches the product and category.</p></div>
                            <div class="flex gap-3"><span class="font-mono text-[#d7b77a]">03</span><p>Describe the practical details customers care about.</p></div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.product-form-shell {
    background: radial-gradient(circle at 82% 0%, rgba(176, 141, 87, 0.1), transparent 26rem), var(--background);
}

.font-display {
    font-family: var(--font-display);
}
</style>
