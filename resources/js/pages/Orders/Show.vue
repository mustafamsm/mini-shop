<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, CheckCircle2, Clock3, MapPin, Package } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';

interface OrderItem {
    id: number;
    product_name: string;
    quantity: number;
    unit_price: number | string;
}

interface Address {
    label?: string | null;
    line1: string;
    line2?: string | null;
    city: string;
    state?: string | null;
    postal_code: string;
    country: string;
}

interface Order {
    id: number;
    order_number?: string | null;
    status: string;
    subtotal: number | string;
    tax: number | string;
    shipping: number | string;
    total: number | string;
    created_at: string | null;
    items: OrderItem[];
    address?: Address | null;
}

defineProps<{
    order: Order;
}>();

const formatCurrency = (value: number | string): string => `$${Number(value).toFixed(2)}`;

const statusLabel = (status: string): string => status.replace(/[_-]/g, ' ');

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
    <Head :title="`Order ${order.order_number ?? `#${order.id}`}`" />
    <AppLayout>
        <div class="min-h-full bg-muted/20 p-4 sm:p-6 lg:p-8">
            <div class="mx-auto max-w-5xl space-y-6">
                <Link href="/orders" class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground">
                    <ArrowLeft class="size-4" />
                    Back to orders
                </Link>

                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground">Placed {{ order.created_at }}</p>
                        <h1 class="mt-1 text-3xl font-semibold tracking-tight">
                            Order {{ order.order_number ?? `#${order.id}` }}
                        </h1>
                    </div>
                    <span :class="['inline-flex w-fit items-center gap-2 rounded-full px-3 py-1.5 text-sm font-medium capitalize', statusClass(order.status)]">
                        <CheckCircle2 v-if="['completed', 'delivered'].includes(order.status)" class="size-4" />
                        <Clock3 v-else class="size-4" />
                        {{ statusLabel(order.status) }}
                    </span>
                </div>

                <div class="grid gap-6 lg:grid-cols-[1fr_20rem]">
                    <section class="rounded-xl border bg-card shadow-sm">
                        <div class="flex items-center gap-3 border-b p-5">
                            <Package class="size-5 text-primary" />
                            <div>
                                <h2 class="font-semibold">Items in this order</h2>
                                <p class="text-sm text-muted-foreground">{{ order.items.length }} item{{ order.items.length === 1 ? '' : 's' }}</p>
                            </div>
                        </div>
                        <div class="divide-y px-5">
                            <div v-for="item in order.items" :key="item.id" class="flex items-center justify-between gap-4 py-4">
                                <div>
                                    <p class="font-medium">{{ item.product_name }}</p>
                                    <p class="text-sm text-muted-foreground">Quantity: {{ item.quantity }}</p>
                                </div>
                                <p class="font-medium">{{ formatCurrency(Number(item.unit_price) * item.quantity) }}</p>
                            </div>
                        </div>
                    </section>

                    <aside class="space-y-6">
                        <section class="rounded-xl border bg-card p-5 shadow-sm">
                            <h2 class="font-semibold">Order summary</h2>
                            <dl class="mt-4 space-y-3 text-sm">
                                <div class="flex justify-between gap-4"><dt class="text-muted-foreground">Subtotal</dt><dd>{{ formatCurrency(order.subtotal) }}</dd></div>
                                <div class="flex justify-between gap-4"><dt class="text-muted-foreground">Tax</dt><dd>{{ formatCurrency(order.tax) }}</dd></div>
                                <div class="flex justify-between gap-4"><dt class="text-muted-foreground">Shipping</dt><dd>{{ formatCurrency(order.shipping) }}</dd></div>
                                <div class="flex justify-between gap-4 border-t pt-3 text-base font-semibold"><dt>Total</dt><dd>{{ formatCurrency(order.total) }}</dd></div>
                            </dl>
                        </section>

                        <section v-if="order.address" class="rounded-xl border bg-card p-5 shadow-sm">
                            <div class="flex items-center gap-3">
                                <MapPin class="size-5 text-primary" />
                                <h2 class="font-semibold">Shipping address</h2>
                            </div>
                            <address class="mt-4 text-sm not-italic leading-6 text-muted-foreground">
                                <strong v-if="order.address.label" class="block text-foreground">{{ order.address.label }}</strong>
                                {{ order.address.line1 }}<br />
                                <template v-if="order.address.line2">{{ order.address.line2 }}<br /></template>
                                {{ order.address.city }}, {{ order.address.state }} {{ order.address.postal_code }}<br />
                                {{ order.address.country }}
                            </address>
                        </section>
                    </aside>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
