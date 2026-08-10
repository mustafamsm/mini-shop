<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/front/AppLayout.vue';
import type { Product, Category, Paginated } from '@/types/models';

interface Props {
    products: Paginated<Product>;
    categories: Category[];
    filters: { category?: string; search?: string };
}

const props = defineProps<Props>();

function filterByCategory(slug: string): void {
    router.get(
        '/',
        { category: slug },
        { preserveState: true, preserveScroll: true },
    );
}
</script>

<template>
    <Head title="Shop" />
    <AppLayout>
        <div class="mb-10">
            <h1
                style="font-family: var(--font-display); font-weight: 600"
                class="mb-2 text-4xl"
            >
                Things worth keeping
            </h1>
            <p class="text-sm opacity-70">
                {{ products.total }} items, made to last longer than the trend
                cycle.
            </p>
        </div>

        <div class="mb-10 flex flex-wrap gap-2">
            <button
                v-for="category in categories"
                :key="category.id"
                @click="filterByCategory(category.slug)"
                class="rounded-full px-4 py-1.5 text-sm transition-colors"
                :style="
                    filters.category === category.slug
                        ? 'background: var(--ink); color: var(--paper)'
                        : 'border: 1px solid var(--line); background: var(--paper)'
                "
            >
                {{ category.name }}
            </button>
        </div>

        <div class="grid grid-cols-2 gap-8 md:grid-cols-3 lg:grid-cols-4">
            <Link
                v-for="product in products.data"
                :key="product.id"
                :href="`/products/${product.slug}`"
                class="group relative"
            >
                <div
                    class="relative aspect-4/5 overflow-hidden rounded-sm"
                    style="background: var(--paper)"
                >
                    <img
                        v-if="product.images?.[0]"
                        :src="product.images[0].path"
                        :alt="product.name"
                        class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-[1.03]"
                    />

                    <div class="swing-tag">
                        <span class="hole" />
                        {{ product.category?.name ?? 'Goods' }}
                    </div>
                </div>

                <div class="mt-3 flex items-start justify-between">
                    <h3 class="text-sm leading-snug" style="font-weight: 500">
                        {{ product.name }}
                    </h3>
                    <span
                        class="ml-3 shrink-0 text-sm"
                        style="
                            font-family: var(--font-mono);
                            color: var(--brass);
                        "
                    >
                        ${{ Number(product.base_price).toFixed(2) }}
                    </span>
                </div>
            </Link>
        </div>

        <div
            class="mt-14 flex justify-center gap-2"
            v-if="products.links.length > 3"
        >
            <Link
                v-for="link in products.links"
                :key="link.label"
                :href="link.url ?? ''"
                v-html="link.label"
                class="rounded px-3 py-1.5 text-sm"
                :style="
                    link.active
                        ? 'background: var(--ink); color: var(--paper)'
                        : 'opacity: 0.6'
                "
            />
        </div>
    </AppLayout>
</template>

<style scoped>
.swing-tag {
    position: absolute;
    top: 12px;
    left: -6px;
    background: var(--paper);
    color: var(--ink);
    font-family: var(--font-mono);
    font-size: 11px;
    letter-spacing: 0.02em;
    padding: 4px 10px 4px 16px;
    transform: rotate(-4deg);
    box-shadow: 1px 2px 4px rgba(0, 0, 0, 0.12);
    border: 1px solid var(--line);
}

.swing-tag .hole {
    position: absolute;
    left: 5px;
    top: 50%;
    transform: translateY(-50%);
    width: 4px;
    height: 4px;
    border-radius: 50%;
    background: var(--canvas);
    border: 1px solid var(--line);
}
</style>
