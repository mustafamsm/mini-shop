<template>
    <Head title="Dashboard" />
    <AppLayout>
        <div class="dashboard-shell min-h-full p-4 sm:p-6 lg:p-8">
            <div class="mx-auto max-w-7xl space-y-6">
                <section class="dashboard-hero overflow-hidden rounded-2xl border border-[#31483a]/20 bg-[#31483a] text-[#f3f0e8] shadow-lg">
                    <div class="relative px-6 py-7 sm:px-8 sm:py-9">
                        <div class="relative z-10 flex flex-col gap-7 lg:flex-row lg:items-end lg:justify-between">
                            <div class="max-w-xl">
                                <div class="mb-4 flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-[#d7b77a]">
                                    <span class="size-2 rounded-full bg-[#d7b77a]" />
                                    Store overview
                                </div>
                                <h1 class="font-display text-4xl leading-none tracking-tight sm:text-5xl">
                                    Good morning, keep the shelves moving.
                                </h1>
                                <p class="mt-4 max-w-md text-sm leading-6 text-[#d8ded5] sm:text-base">
                                    A quick read on revenue, orders, and the products that need your attention today.
                                </p>
                            </div>

                            <Button as-child size="lg" class="w-full bg-[#d7b77a] text-[#26362d] hover:bg-[#e2c68e] sm:w-auto">
                                <Link href="/admin/products">
                                    <Package class="size-4" />
                                    Manage products
                                    <ArrowUpRight class="size-4" />
                                </Link>
                            </Button>
                        </div>
                        <div class="hero-mark" aria-hidden="true">MS</div>
                    </div>
                </section>

                <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    <Card v-for="card in statCards" :key="card.label" class="stat-card border-border/70 bg-card/90 shadow-sm">
                        <CardContent class="flex items-start justify-between gap-4 p-5">
                            <div>
                                <CardDescription class="font-medium">{{ card.label }}</CardDescription>
                                <div class="mt-3 font-display text-3xl tracking-tight text-foreground">
                                    {{ card.value }}
                                </div>
                                <p class="mt-2 text-xs text-muted-foreground">{{ card.detail }}</p>
                            </div>
                            <div :class="['rounded-xl p-2.5', card.iconClass]">
                                <component :is="card.icon" class="size-5" />
                            </div>
                        </CardContent>
                    </Card>
                </section>

                <div class="grid gap-6 lg:grid-cols-[1.35fr_0.65fr]">
                    <Card class="border-border/70 shadow-sm">
                        <CardHeader class="flex-row items-start justify-between space-y-0">
                            <div>
                                <CardTitle class="font-display text-2xl">Business pulse</CardTitle>
                                <CardDescription class="mt-1">Your current sales snapshot at a glance.</CardDescription>
                            </div>
                            <Badge variant="outline" class="border-[#b08d57]/40 bg-[#b08d57]/10 text-[#806333]">
                                Live store data
                            </Badge>
                        </CardHeader>
                        <CardContent>
                            <div class="rounded-xl bg-[#f1eee6] p-5 dark:bg-muted/40">
                                <div class="flex items-end justify-between gap-4">
                                    <div>
                                        <p class="text-sm text-muted-foreground">Average order value</p>
                                        <p class="mt-2 font-display text-4xl text-foreground">${{ averageOrderValue }}</p>
                                    </div>
                                    <CircleDollarSign class="mb-1 size-8 text-[#b08d57]" />
                                </div>
                                <div class="mt-7 space-y-2">
                                    <div class="flex justify-between text-xs font-medium text-muted-foreground">
                                        <span>Revenue collected</span>
                                        <span>${{ Number(stats.total_revenue).toFixed(2) }}</span>
                                    </div>
                                    <div class="h-2 overflow-hidden rounded-full bg-[#d9d5ca] dark:bg-background">
                                        <div class="h-full w-[72%] rounded-full bg-[#b08d57]" />
                                    </div>
                                    <p class="text-xs text-muted-foreground">Based on {{ stats.orders_today }} order{{ stats.orders_today === 1 ? '' : 's' }} today.</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card class="border-border/70 shadow-sm">
                        <CardHeader>
                            <CardTitle class="font-display text-2xl">Inventory watch</CardTitle>
                            <CardDescription class="mt-1">Stay ahead of your next restock.</CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-5">
                            <div class="flex items-center gap-4">
                                <div class="rounded-xl bg-[#f3e3d5] p-3 text-[#a45e31] dark:bg-[#a45e31]/15">
                                    <AlertTriangle class="size-5" />
                                </div>
                                <div>
                                    <p class="font-display text-3xl">{{ stats.low_stock_variants }}</p>
                                    <p class="text-sm text-muted-foreground">low-stock variants</p>
                                </div>
                            </div>
                            <Separator />
                            <div class="flex items-center justify-between gap-3">
                                <Badge :variant="stats.low_stock_variants > 0 ? 'destructive' : 'secondary'">
                                    {{ stats.low_stock_variants > 0 ? 'Action needed' : 'All clear' }}
                                </Badge>
                                <Button as-child variant="ghost" size="sm" class="text-muted-foreground">
                                    <Link href="/admin/products">
                                        Review stock
                                        <ArrowRight class="size-4" />
                                    </Link>
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <section class="flex flex-col justify-between gap-4 rounded-xl border border-dashed border-border bg-muted/20 px-5 py-4 sm:flex-row sm:items-center">
                    <div class="flex items-center gap-3">
                        <div class="rounded-lg bg-primary p-2 text-primary-foreground">
                            <ClipboardList class="size-4" />
                        </div>
                        <div>
                            <p class="text-sm font-semibold">Ready for the next order?</p>
                            <p class="text-xs text-muted-foreground">Keep products and fulfilment moving from one place.</p>
                        </div>
                    </div>
                    <Button as-child variant="outline" size="sm" class="w-full sm:w-auto">
                        <Link href="/admin/orders">
                            Open orders
                            <ArrowRight class="size-4" />
                        </Link>
                    </Button>
                </section>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { AlertTriangle, ArrowRight, ArrowUpRight, CircleDollarSign, ClipboardList, Package } from '@lucide/vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import AppLayout from '@/layouts/AppLayout.vue';
import type { DashboardStats } from '@/types';

const props = defineProps<{
    stats: DashboardStats;
}>();

const averageOrderValue = Number(
    props.stats.orders_today > 0
        ? props.stats.total_revenue / props.stats.orders_today
        : 0,
).toFixed(2);

const statCards = [
    {
        label: 'Total revenue',
        value: `$${Number(props.stats.total_revenue).toFixed(2)}`,
        detail: 'Across all non-cancelled orders',
        icon: CircleDollarSign,
        iconClass: 'bg-[#e6eee5] text-[#48624f] dark:bg-[#48624f]/20',
    },
    {
        label: 'Orders today',
        value: String(props.stats.orders_today),
        detail: 'Placed since midnight',
        icon: ClipboardList,
        iconClass: 'bg-[#eee8d9] text-[#8d6b31] dark:bg-[#8d6b31]/20',
    },
    {
        label: 'Low stock',
        value: String(props.stats.low_stock_variants),
        detail: props.stats.low_stock_variants > 0 ? 'Variants below five units' : 'No variants need attention',
        icon: AlertTriangle,
        iconClass: 'bg-[#f3e3d5] text-[#a45e31] dark:bg-[#a45e31]/20',
    },
];
</script>

<style scoped>
.dashboard-shell {
    background: radial-gradient(circle at 82% 0%, rgba(176, 141, 87, 0.12), transparent 26rem), var(--background);
}

.font-display {
    font-family: var(--font-display);
}

.dashboard-hero {
    position: relative;
    isolation: isolate;
}

.dashboard-hero::after {
    position: absolute;
    inset: 0;
    z-index: -1;
    background: linear-gradient(115deg, transparent 45%, rgba(216, 192, 145, 0.12) 45%, transparent 72%);
    content: '';
}

.hero-mark {
    position: absolute;
    right: 2rem;
    bottom: -2rem;
    color: rgba(243, 240, 232, 0.07);
    font-family: var(--font-display);
    font-size: clamp(8rem, 20vw, 15rem);
    font-weight: 700;
    line-height: 1;
    letter-spacing: -0.08em;
}

.stat-card {
    transition: transform 180ms ease, box-shadow 180ms ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 24px rgba(39, 43, 35, 0.08);
}
</style>
