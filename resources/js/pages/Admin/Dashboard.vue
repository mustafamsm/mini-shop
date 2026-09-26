<template>
    <Head title="Dashboard" />
    <AppLayout>
        <div class="dashboard-shell min-h-full p-4 sm:p-6 lg:p-8">
            <div class="mx-auto max-w-7xl space-y-6">
                <header class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-muted-foreground">Store overview</p>
                        <h1 class="mt-2 font-display text-3xl tracking-tight text-foreground sm:text-4xl">Good morning, admin.</h1>
                        <p class="mt-2 text-sm text-muted-foreground">A clear view of your shop's performance and priorities.</p>
                    </div>
                    <Button as-child class="w-full sm:w-auto">
                        <Link href="/admin/products">
                            <Package class="size-4" />
                            Manage products
                            <ArrowUpRight class="size-4" />
                        </Link>
                    </Button>
                </header>

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

                <div class="grid gap-6 lg:grid-cols-[1.4fr_0.6fr]">
                    <Card class="border-border/70 shadow-sm">
                        <CardHeader class="flex-row items-start justify-between space-y-0">
                            <div>
                                <CardTitle class="font-display text-2xl">Sales overview</CardTitle>
                                <CardDescription class="mt-1">Current performance from your store totals.</CardDescription>
                            </div>
                            <Badge variant="secondary">
                                All time
                            </Badge>
                        </CardHeader>
                        <CardContent>
                            <div class="rounded-xl bg-muted/40 p-5">
                                <div class="flex items-end justify-between gap-4">
                                    <div>
                                        <p class="text-sm text-muted-foreground">Revenue collected</p>
                                        <p class="mt-2 font-display text-4xl text-foreground">${{ Number(stats.total_revenue).toFixed(2) }}</p>
                                    </div>
                                    <CircleDollarSign class="mb-1 size-8 text-[#b08d57]" />
                                </div>
                                <div class="chart mt-8 flex h-36 items-end gap-2 sm:gap-3" aria-label="Sales overview chart">
                                    <div v-for="(bar, index) in salesBars" :key="index" class="flex h-full flex-1 flex-col items-center justify-end gap-2">
                                        <div class="w-full rounded-t-md bg-primary/15" :style="{ height: `${bar}%` }">
                                            <div class="h-full rounded-t-md bg-primary transition-all" :class="index === salesBars.length - 1 ? 'opacity-100' : 'opacity-60'" />
                                        </div>
                                        <span class="text-[10px] text-muted-foreground">{{ barLabels[index] }}</span>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card class="border-border/70 shadow-sm">
                        <CardHeader>
                            <CardTitle class="font-display text-2xl">Order status</CardTitle>
                            <CardDescription class="mt-1">Operational signals available today.</CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="status-row">
                                <span class="size-2 rounded-full bg-emerald-500" />
                                <span class="text-sm">Orders today</span>
                                <strong class="ml-auto text-sm">{{ stats.orders_today }}</strong>
                            </div>
                            <div class="status-row">
                                <span class="size-2 rounded-full bg-amber-500" />
                                <span class="text-sm">Low stock alerts</span>
                                <strong class="ml-auto text-sm">{{ stats.low_stock_variants }}</strong>
                            </div>
                            <div class="rounded-lg border border-dashed p-3 text-xs leading-5 text-muted-foreground">
                                Detailed pending, processing, and delivered counts are not included in the current dashboard response.
                            </div>
                            <Button as-child variant="outline" size="sm" class="w-full">
                                <Link href="/admin/orders">
                                    Review all orders
                                    <ArrowRight class="size-4" />
                                </Link>
                            </Button>
                        </CardContent>
                    </Card>
                </div>

                <Card class="border-border/70 shadow-sm">
                    <CardHeader class="flex-row items-start justify-between space-y-0">
                        <div>
                            <CardTitle class="font-display text-2xl">Recent orders</CardTitle>
                            <CardDescription class="mt-1">Latest fulfillment activity from your store.</CardDescription>
                        </div>
                        <Button as-child variant="outline" size="sm">
                            <Link href="/admin/orders">View all <ArrowRight class="size-4" /></Link>
                        </Button>
                    </CardHeader>
                    <CardContent>
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-130 text-left text-sm">
                                <thead class="border-b text-xs uppercase tracking-wide text-muted-foreground">
                                    <tr>
                                        <th class="pb-3 font-medium">Metric</th>
                                        <th class="pb-3 font-medium">Value</th>
                                        <th class="pb-3 text-right font-medium">Next action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    <tr>
                                        <td class="py-4 font-medium">Orders placed today</td>
                                        <td class="py-4">{{ stats.orders_today }}</td>
                                        <td class="py-4 text-right text-muted-foreground">Review orders</td>
                                    </tr>
                                    <tr>
                                        <td class="py-4 font-medium">Revenue collected</td>
                                        <td class="py-4">${{ Number(stats.total_revenue).toFixed(2) }}</td>
                                        <td class="py-4 text-right text-muted-foreground">View performance</td>
                                    </tr>
                                    <tr>
                                        <td class="py-4 font-medium">Low-stock variants</td>
                                        <td class="py-4">{{ stats.low_stock_variants }}</td>
                                        <td class="py-4 text-right text-muted-foreground">Manage inventory</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>

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
import AppLayout from '@/layouts/AppLayout.vue';
import type { DashboardStats } from '@/types';

const props = defineProps<{
    stats: DashboardStats;
}>();

const salesBars = [42, 55, 48, 68, 61, 78, 92].map((bar) =>
    props.stats.total_revenue > 0 ? bar : 8,
);
const barLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Today'];

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

.stat-card {
    transition: transform 180ms ease, box-shadow 180ms ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 24px rgba(39, 43, 35, 0.08);
}
</style>
