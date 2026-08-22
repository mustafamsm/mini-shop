<template>
    <Head title="Products" />
    <AppLayout>
        <div class="products-shell min-h-full p-4 sm:p-6 lg:p-8">
            <div class="mx-auto max-w-7xl space-y-6">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#9b7a43] dark:text-[#d7b77a]">
                            Catalogue
                        </p>
                        <h1 class="mt-2 font-display text-4xl tracking-tight sm:text-5xl">
                            Products
                        </h1>
                        <p class="mt-2 max-w-xl text-sm text-muted-foreground">
                            Keep your catalogue fresh, organised, and ready for customers.
                        </p>
                    </div>
                    <Button as-child size="lg" class="w-full sm:w-auto">
                        <Link href="/admin/products/create">
                            <Plus class="size-4" />
                            New product
                        </Link>
                    </Button>
                </div>

                <div class="grid gap-4 sm:grid-cols-3">
                    <Card v-for="stat in summaryStats" :key="stat.label" class="border-border/70 shadow-sm">
                        <CardContent class="flex items-center gap-4 p-5">
                            <div :class="['rounded-xl p-3', stat.iconClass]">
                                <component :is="stat.icon" class="size-5" />
                            </div>
                            <div>
                                <p class="text-2xl font-semibold tracking-tight">{{ stat.value }}</p>
                                <p class="text-xs text-muted-foreground">{{ stat.label }}</p>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <Card class="overflow-hidden border-border/70 shadow-sm">
                    <CardHeader class="border-b border-border/70 bg-muted/20 pb-5">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <CardTitle class="font-display text-2xl">All products</CardTitle>
                                <CardDescription class="mt-1">{{ products.total }} products in your catalogue</CardDescription>
                            </div>
                            <Badge variant="outline" class="w-fit gap-1.5 border-[#48624f]/30 bg-[#e6eee5] text-[#48624f] dark:border-[#a8d0ae]/30 dark:bg-[#48624f]/20 dark:text-[#a8d0ae]">
                                <span class="size-1.5 rounded-full bg-current" />
                                Catalogue live
                            </Badge>
                        </div>
                    </CardHeader>

                    <CardContent class="p-0">
                        <div v-if="products.data.length === 0" class="flex flex-col items-center justify-center px-6 py-16 text-center">
                            <div class="rounded-2xl bg-muted p-4 text-muted-foreground">
                                <PackageOpen class="size-7" />
                            </div>
                            <h2 class="mt-4 font-display text-2xl">Your catalogue is empty</h2>
                            <p class="mt-2 max-w-sm text-sm text-muted-foreground">Create your first product to start building your shop.</p>
                            <Button as-child class="mt-5">
                                <Link href="/admin/products/create">
                                    <Plus class="size-4" />
                                    Add first product
                                </Link>
                            </Button>
                        </div>

                        <div v-else class="overflow-x-auto">
                            <table class="w-full min-w-170 text-sm">
                                <thead class="bg-muted/30 text-left text-xs uppercase tracking-wider text-muted-foreground">
                                    <tr>
                                        <th class="px-6 py-4 font-medium">Product</th>
                                        <th class="px-4 py-4 font-medium">Category</th>
                                        <th class="px-4 py-4 font-medium">Price</th>
                                        <th class="px-4 py-4 font-medium">Status</th>
                                        <th class="px-6 py-4 text-right font-medium">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border/70">
                                    <tr v-for="product in products.data" :key="product.id" class="group transition-colors hover:bg-muted/30">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-[#eee8d9] font-display text-lg text-[#8d6b31] dark:bg-[#8d6b31]/20 dark:text-[#e8cb92]">
                                                    {{ initials(product.name) }}
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="truncate font-semibold text-foreground">{{ product.name }}</p>
                                                    <p class="mt-0.5 truncate text-xs text-muted-foreground">/{{ product.slug }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-muted-foreground">{{ product.category?.name ?? 'Uncategorised' }}</td>
                                        <td class="px-4 py-4 font-medium">${{ Number(product.base_price).toFixed(2) }}</td>
                                        <td class="px-4 py-4">
                                            <Badge :variant="product.is_active ? 'secondary' : 'outline'" class="gap-1.5">
                                                <span :class="['size-1.5 rounded-full', product.is_active ? 'bg-emerald-500' : 'bg-muted-foreground']" />
                                                {{ product.is_active ? 'Active' : 'Draft' }}
                                            </Badge>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <Button as-child variant="ghost" size="sm" class="text-muted-foreground group-hover:text-foreground">
                                                <Link :href="`/admin/products/${product.id}/edit`">
                                                    Edit
                                                    <ArrowUpRight class="size-4" />
                                                </Link>
                                            </Button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div v-if="products.links.length > 3" class="flex flex-wrap items-center justify-between gap-3 border-t border-border/70 px-6 py-4">
                            <p class="text-xs text-muted-foreground">Showing {{ products.data.length }} of {{ products.total }} products</p>
                            <div class="flex flex-wrap gap-1">
                                <Button
                                    v-for="link in products.links"
                                    :key="link.label"
                                    as-child
                                    :variant="link.active ? 'default' : 'ghost'"
                                    size="sm"
                                    :disabled="!link.url"
                                    class="min-w-8"
                                >
                                    <Link v-if="link.url" :href="link.url"><span v-html="link.label" /></Link>
                                    <span v-else v-html="link.label" />
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowUpRight, Boxes, PackageOpen, Plus, Sparkles } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import type { Paginated, Product } from '@/types';

const props = defineProps<{
    products: Paginated<Product & { category: { name: string } }>;
}>();

const summaryStats = [
    {
        label: 'Total products',
        value: props.products.total,
        icon: Boxes,
        iconClass: 'bg-[#e6eee5] text-[#48624f] dark:bg-[#48624f]/25 dark:text-[#a8d0ae]',
    },
    {
        label: 'Active listings',
        value: props.products.data.filter((product) => product.is_active).length,
        icon: Sparkles,
        iconClass: 'bg-[#eee8d9] text-[#8d6b31] dark:bg-[#8d6b31]/25 dark:text-[#e8cb92]',
    },
    {
        label: 'Current page',
        value: props.products.data.length,
        icon: PackageOpen,
        iconClass: 'bg-[#e6e3ef] text-[#665e85] dark:bg-[#665e85]/25 dark:text-[#bbb2e2]',
    },
];

const initials = (name: string) =>
    name
        .split(' ')
        .slice(0, 2)
        .map((part) => part[0])
        .join('')
        .toUpperCase();
</script>

<style scoped>
.products-shell {
    background: radial-gradient(circle at 82% 0%, rgba(176, 141, 87, 0.1), transparent 26rem), var(--background);
}

.font-display {
    font-family: var(--font-display);
}
</style>
