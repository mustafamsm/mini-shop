<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowRight, ClipboardList, PackageOpen } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';

interface Order {
    id: number;
    order_number?: string | null;
    status: string;
    total: number | string;
    created_at: string | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

const props = defineProps<{
    orders: {
        data: Order[];
        links: PaginationLink[];
        current_page: number;
        last_page: number;
    };
}>();

const formatCurrency = (value: number | string): string => `$${Number(value).toFixed(2)}`;

const statusClass = (status: string): string => {
    if (['completed', 'delivered'].includes(status)) {
        return 'bg-emerald-100 text-emerald-700';
    }

    if (['cancelled', 'failed'].includes(status)) {
        return 'bg-red-100 text-red-700';
    }

    return 'bg-amber-100 text-amber-700';
};
</script>

<template>
    <Head title="My Orders" />
    <AppLayout>
        <div class="min-h-full bg-muted/20 p-4 sm:p-6 lg:p-8">
            <div class="mx-auto max-w-5xl space-y-6">
                <div>
                    <p class="text-sm font-medium text-primary">Purchase history</p>
                    <h1 class="mt-1 text-3xl font-semibold tracking-tight">My orders</h1>
                    <p class="mt-2 text-sm text-muted-foreground">View the status and details of your purchases.</p>
                </div>

                <section v-if="props.orders.data.length" class="overflow-hidden rounded-xl border bg-card shadow-sm">
                    <div class="divide-y">
                        <Link
                            v-for="order in props.orders.data"
                            :key="order.id"
                            :href="`/orders/${order.id}`"
                            class="flex flex-col gap-4 p-5 transition-colors hover:bg-muted/30 sm:flex-row sm:items-center sm:justify-between"
                        >
                            <div class="flex items-start gap-4">
                                <div class="rounded-xl bg-primary/10 p-3 text-primary">
                                    <ClipboardList class="size-5" />
                                </div>
                                <div>
                                    <p class="font-semibold">Order {{ order.order_number ?? `#${order.id}` }}</p>
                                    <p class="mt-1 text-sm text-muted-foreground">Placed {{  order.created_at }}</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between gap-6 sm:justify-end">
                                <div class="text-left sm:text-right">
                                    <p class="font-semibold">{{ formatCurrency(order.total) }}</p>
                                    <span :class="['mt-1 inline-flex rounded-full px-2.5 py-1 text-xs font-medium capitalize', statusClass(order.status)]">
                                        {{ order.status.replace(/[_-]/g, ' ') }}
                                    </span>
                                </div>
                                <ArrowRight class="size-5 text-muted-foreground" />
                            </div>
                        </Link>
                    </div>
                </section>

                <section v-else class="rounded-xl border bg-card p-10 text-center shadow-sm">
                    <PackageOpen class="mx-auto size-10 text-muted-foreground" />
                    <h2 class="mt-4 text-lg font-semibold">No orders yet</h2>
                    <p class="mt-1 text-sm text-muted-foreground">Your purchases will appear here after checkout.</p>
                    <Link href="/" class="mt-5 inline-flex rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground">
                        Start shopping
                    </Link>
                </section>

                <nav v-if="props.orders.last_page > 1" class="flex flex-wrap justify-center gap-2" aria-label="Orders pagination">
                    <template v-for="link in props.orders.links" :key="link.label">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            :class="['rounded-md border px-3 py-2 text-sm', link.active ? 'border-primary bg-primary text-primary-foreground' : 'bg-card hover:bg-muted']"
                            v-html="link.label"
                        />
                        <span v-else class="rounded-md border px-3 py-2 text-sm text-muted-foreground" v-html="link.label" />
                    </template>
                </nav>
            </div>
        </div>
    </AppLayout>
</template>
