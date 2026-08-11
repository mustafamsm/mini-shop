<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import ShopLayout from '@/layouts/ShopLayout.vue';
import type { Product, Category, Paginated } from '@/types/models';

defineProps<{
    products: Paginated<Product>;
    categories: Category[];
    filters: { category?: string };
}>();

function filterByCategory(slug: string): void {
    router.get('/', { category: slug }, { preserveState: true });
}
</script>

<template>
    <Head title="Shop" />
    <ShopLayout>

        <div class="mb-8 flex flex-wrap gap-2">
            <button
                v-for="c in categories"
                :key="c.id"
                @click="filterByCategory(c.slug)"
                class="rounded-full border px-4 py-1.5 text-sm"
            >
                {{ c.name }}
            </button>
        </div>

        <div class="grid grid-cols-2 gap-8 md:grid-cols-4">
            <Link
                v-for="p in products.data"
                :key="p.id"
                :href="`/products/${p.slug}`"
            >
                <div class="aspect-[4/5] overflow-hidden rounded bg-white">
                    <img
                        v-if="p.images?.[0]"
                        :src="p.images[0].path"
                        :alt="p.name"
                        class="h-full w-full object-cover"
                    />
                </div>
                <div class="mt-2 flex justify-between text-sm">
                    <span>{{ p.name }}</span>
                    <span>${{ Number(p.base_price).toFixed(2) }}</span>
                </div>
            </Link>
        </div>
    </ShopLayout>
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
