<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Package, Search } from '@lucide/vue';
import { computed, onUnmounted, ref, watch } from 'vue';
import ShopLayout from '@/layouts/ShopLayout.vue';
import type { Product, Category, Paginated } from '@/types/models';

const props = defineProps<{
    products: Paginated<Product>;
    categories: Category[];
    filters: { category?: string };
    search?: string;
}>();

const category = ref(props.filters.category ?? '');
const search = ref(props.search ?? '');
const isLoading = ref(false);
const failedImageIds = ref(new Set<number>());
const activeCategory = computed(
    () => props.categories.find((item) => item.slug === category.value),
);

const removeStartListener = router.on('start', () => {
    isLoading.value = true;
});
const removeFinishListener = router.on('finish', () => {
    isLoading.value = false;
});

onUnmounted(() => {
    removeStartListener();
    removeFinishListener();
});

let debounceTimer: ReturnType<typeof setTimeout> | undefined;

function buildQueryParams(): Record<string, string> {
    const params: Record<string, string> = {};

    if (category.value) {
        params.category = category.value;
    }

    if (search.value.trim()) {
        params.search = search.value.trim();
    }

    return params;
}

function applyFilters(): void {
    router.get('/', buildQueryParams(), {
        preserveState: true,
        preserveScroll: true,
    });
}

function debounceApplyFilters(): void {
    window.clearTimeout(debounceTimer);
    debounceTimer = window.setTimeout(() => {
        applyFilters();
    }, 350);
}

function filterByCategory(slug?: string): void {
    category.value = slug ?? '';
    applyFilters();
}

function clearFilters(): void {
    category.value = '';
    search.value = '';
    router.get('/', {}, { preserveState: true, preserveScroll: true });
}

function isImageAvailable(product: Product): boolean {
    return Boolean(product.image_urls?.[0]?.thumb_url || product.image_urls?.[0]?.url) && !failedImageIds.value.has(product.id);
}

function markImageUnavailable(productId: number): void {
    failedImageIds.value = new Set(failedImageIds.value).add(productId);
}

watch(search, () => {
    debounceApplyFilters();
});
</script>

<template>
    <Head title="Shop" />
    <ShopLayout>
        <section class="relative mb-12 overflow-hidden bg-[#171717] text-white">
            <div class="absolute -right-20 -top-20 size-72 rounded-full bg-[#ff5c35] sm:size-96" aria-hidden="true" />
            <div class="absolute bottom-0 right-1/3 size-40 translate-y-1/2 rounded-full bg-[#d7f36b]" aria-hidden="true" />
            <div class="relative grid min-h-96 items-center gap-8 px-6 py-10 sm:px-10 md:grid-cols-[1fr_0.8fr] md:px-14 md:py-14">
                <div class="relative z-10 max-w-xl">
                    <p class="mb-5 font-mono text-[10px] font-semibold uppercase tracking-[0.28em] text-[#d7f36b]">
                        Foundry Goods / New season
                    </p>
                    <h1 class="max-w-lg text-5xl font-semibold leading-[0.9] tracking-[-0.045em] sm:text-6xl md:text-7xl">
                        Good things.<br /><span class="text-[#ff5c35]">No filler.</span>
                    </h1>
                    <p class="mt-6 max-w-md text-sm leading-6 text-white/65 sm:text-base">
                        Useful objects, selected for how they look, feel, and live with you.
                    </p>
                    <div class="mt-8 flex flex-wrap items-center gap-4">
                        <button
                            type="button"
                            @click="filterByCategory()"
                            class="rounded-full bg-[#d7f36b] px-5 py-3 text-sm font-semibold text-[#171717] transition hover:bg-white"
                        >
                            Shop everything
                        </button>
                        <button
                            type="button"
                            @click="filterByCategory(categories[0]?.slug ?? '')"
                            class="text-sm font-medium text-white underline decoration-[#ff5c35] decoration-2 underline-offset-4 transition hover:text-[#d7f36b]"
                        >
                            Shop {{ categories[0]?.name ?? 'featured' }}
                        </button>
                    </div>
                </div>
                <div class="relative z-10 mx-auto w-full max-w-xs md:mr-0">
                    <div class="relative aspect-square rotate-2 overflow-hidden bg-[#f2eee7] shadow-2xl transition duration-500 hover:rotate-0">
                        <img
                            v-if="products.data[0] && isImageAvailable(products.data[0])"
                            :src="products.data[0].image_urls?.[0]?.url || products.data[0].image_urls?.[0]?.thumb_url"
                            :alt="products.data[0].name"
                            class="h-full w-full object-cover"
                            @error="markImageUnavailable(products.data[0].id)"
                        />
                        <div v-else class="flex h-full items-center justify-center text-center text-[#171717]">
                            <span class="font-mono text-[10px] uppercase tracking-[0.2em]">New arrivals</span>
                        </div>
                        <div class="absolute bottom-3 left-3 bg-[#d7f36b] px-3 py-1.5 font-mono text-[10px] uppercase tracking-[0.12em] text-[#171717]">
                            Featured object
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mb-10">
            <div class="mb-4 flex items-end justify-between gap-4">
                <div>
                    <p class="mb-2 font-mono text-[10px] uppercase tracking-[0.22em] text-neutral-500">Browse by mood</p>
                    <h2 class="font-(family-name:--font-display) text-3xl text-neutral-900">Find your everyday.</h2>
                </div>
                <button
                    type="button"
                    @click="filterByCategory()"
                    class="shrink-0 text-sm font-medium text-neutral-600 underline decoration-neutral-300 underline-offset-4 hover:text-neutral-900"
                >
                    View all
                </button>
            </div>
            <div class="flex gap-2 overflow-x-auto border-y border-neutral-200 py-3">
                <button
                    v-for="c in categories"
                    :key="c.id"
                    @click="filterByCategory(c.slug)"
                    :class="[
                        'shrink-0 px-3 py-2 text-sm font-medium transition',
                        category === c.slug ? 'bg-[#17251f] text-white' : 'text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900'
                    ]"
                >
                    {{ c.name }}
                </button>
            </div>
        </section>

        <section class="shop-filter-bar mb-10 border-y border-neutral-200 bg-[#faf9f6] py-4 sm:py-5">
            <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-center">
                <div class="relative flex-1">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-neutral-500">
                        <svg viewBox="0 0 24 24" class="h-4 w-4 fill-current" aria-hidden="true">
                            <path d="M10 2a8 8 0 015.94 13.31l4.39 4.39 1.41-1.41-4.39-4.39A8 8 0 1110 2zm0 2a6 6 0 104.24 10.24A6 6 0 0010 4z"/>
                        </svg>
                    </span>

                    <input
                        v-model="search"
                        type="search"
                        placeholder="Search products..."
                        class="w-full border border-neutral-200 bg-white py-3 pl-10 pr-4 text-sm outline-none transition placeholder:text-neutral-400 focus:border-neutral-900"
                    />
                </div>

                <button
                    type="button"
                    @click="applyFilters"
                    class="rounded-full bg-[#17251f] px-5 py-3 text-sm font-medium text-white transition hover:bg-[#31463b]"
                >
                    Search
                </button>
            </div>

            <div class="mb-4 flex flex-wrap items-center gap-2">
                <button
                    v-if="!category && !search"
                    type="button"
                    class="rounded-full bg-[#17251f] px-3 py-1.5 text-xs font-medium text-white"
                >
                    All products
                </button>

                <button
                    v-else
                    type="button"
                    @click="clearFilters"
                    class="rounded-full border border-neutral-200 bg-white px-3 py-1.5 text-xs font-medium text-neutral-700 transition hover:border-neutral-400"
                >
                    {{ activeCategory?.name ?? 'Search: ' + search.trim() }}
                </button>

                <button
                    v-if="search || category"
                    type="button"
                    @click="clearFilters"
                    class="rounded-full border border-neutral-200 bg-white px-3 py-1.5 text-xs font-medium text-neutral-700 transition hover:border-neutral-400"
                >
                    Clear
                </button>
            </div>

            <div class="mb-2 flex items-center justify-between gap-3 border-b border-neutral-200 pb-3">
                <p class="text-sm text-neutral-600">
                    {{ products.total }} result{{ products.total === 1 ? '' : 's' }}
                    <span v-if="search || category" class="text-neutral-500">
                        • filtered
                    </span>
                </p>
            </div>
        </section>

        <div class="mb-6 flex items-end justify-between gap-4 border-b border-neutral-200 pb-4">
            <div>
                <p class="mb-2 font-mono text-[10px] uppercase tracking-[0.22em] text-neutral-500">The latest selection</p>
                <h2 class="font-(family-name:--font-display) text-3xl text-neutral-900">New arrivals</h2>
            </div>
            <p class="hidden font-mono text-[10px] uppercase tracking-[0.18em] text-neutral-500 sm:block">Objects for daily use</p>
        </div>

        <div
            v-if="isLoading"
            class="grid grid-cols-2 gap-x-4 gap-y-10 sm:gap-x-6 lg:grid-cols-4 lg:gap-x-8"
            aria-label="Loading products"
            aria-live="polite"
        >
            <div v-for="slot in 8" :key="slot" class="animate-pulse">
                <div class="aspect-4/5 rounded-[2px] bg-neutral-200" />
                <div class="mt-3 h-4 w-3/4 rounded bg-neutral-200" />
                <div class="mt-2 h-4 w-1/3 rounded bg-neutral-200" />
            </div>
        </div>

        <div
            v-else-if="products.data.length > 0"
            class="grid grid-cols-2 gap-x-4 gap-y-10 sm:gap-x-6 lg:grid-cols-4 lg:gap-x-8"
            aria-live="polite"
        >
            <Link
                v-for="p in products.data"
                :key="p.id"
                :href="`/products/${p.slug}`"
                class="group block rounded-[2px] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900 focus-visible:ring-offset-4"
            >
                <div class="relative aspect-4/5 overflow-hidden rounded-[2px] bg-[#e9e7e1] ring-1 ring-neutral-200 transition duration-500 group-hover:-translate-y-1 group-hover:shadow-xl group-focus-visible:-translate-y-1 group-focus-visible:shadow-xl">
                    <img
                        v-if="isImageAvailable(p)"
                        :src="p.image_urls?.[0]?.thumb_url || p.image_urls?.[0]?.url"
                        :alt="p.name"
                        class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                        loading="lazy"
                        @error="markImageUnavailable(p.id)"
                    />
                    <div v-else class="flex h-full flex-col items-center justify-center gap-3 px-4 text-center text-neutral-400" role="img" :aria-label="`${p.name} image unavailable`">
                        <Package class="size-10" stroke-width="1.25" aria-hidden="true" />
                        <span class="text-xs font-medium uppercase tracking-[0.16em]">Image unavailable</span>
                    </div>
                </div>
                <div class="mt-4 space-y-1">
                    <div class="flex items-start justify-between gap-3 text-sm">
                        <span class="font-(family-name:--font-display) text-base leading-5 text-neutral-800 transition-colors group-hover:text-neutral-950">{{ p.name }}</span>
                        <span class="shrink-0 font-mono text-xs text-neutral-900">${{ Number(p.base_price).toFixed(2) }}</span>
                    </div>
                    <span class="inline-flex items-center gap-1 font-mono text-[10px] uppercase tracking-[0.14em] text-neutral-500 transition-colors group-hover:text-neutral-700">
                        View detail
                        <Search class="size-3" aria-hidden="true" />
                    </span>
                </div>
            </Link>
        </div>

        <div v-else class="mt-2 border border-dashed border-neutral-300 bg-white px-6 py-12 text-center">
            <Package class="mx-auto size-10 text-neutral-400" stroke-width="1.25" aria-hidden="true" />
            <h3 class="mt-4 text-base font-semibold text-neutral-900">No products found</h3>
            <p class="mt-1 text-sm text-neutral-500">Try a different search or browse all products.</p>
            <button
                v-if="search || category"
                type="button"
                class="mt-5 rounded-xl bg-neutral-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-neutral-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900 focus-visible:ring-offset-2"
                @click="clearFilters"
            >
                Clear filters
            </button>
        </div>

        <div class="mt-8 flex justify-center">
            <nav class="inline-flex items-center gap-1" aria-label="Pagination">
                <template v-for="(l, idx) in products.links" :key="idx">
                    <Link
                        v-if="l.url"
                        :href="l.url"
                        :class="[
                            'border px-3 py-1.5 text-sm transition',
                            l.active ? 'border-[#17251f] bg-[#17251f] text-white' : 'border-neutral-200 bg-white text-neutral-700 hover:border-neutral-400'
                        ]"
                        :preserve-state="true"
                    >
                        <span v-html="l.label" />
                    </Link>

                    <span
                        v-else
                        class="border border-neutral-200 bg-neutral-100 px-3 py-1.5 text-sm text-neutral-400"
                        v-html="l.label"
                    />
                </template>
            </nav>
        </div>
    </ShopLayout>
</template>

<style scoped>
@media (max-width: 767px) {
    .shop-filter-bar {
        position: sticky;
        top: 0;
        z-index: 10;
        backdrop-filter: blur(10px);
        background: rgba(250, 250, 249, 0.9);
        padding-top: 0.75rem;
        padding-bottom: 0.75rem;
        margin-top: -0.5rem;
    }
}

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
