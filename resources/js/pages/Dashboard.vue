<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowRight, ClipboardList, Package, ShoppingBag } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';

interface CustomerStats {
    orders_count: number;
    pending_orders: number;
    total_spent: number;
}

interface RecentOrder {
    id: number;
    status: string;
    total: number;
    created_at: string | null;
}

const props = defineProps<{
    stats: CustomerStats;
    recentOrders: RecentOrder[];
}>();

const formatCurrency = (value: number): string => `$${Number(value).toFixed(2)}`;
</script>

<template>
    <Head title="My Dashboard" />
    <AppLayout>
        <div class="min-h-full bg-muted/20 p-4 sm:p-6 lg:p-8">
            <div class="mx-auto max-w-6xl space-y-6">
                <section class="rounded-2xl bg-primary p-6 text-primary-foreground shadow-lg sm:p-8">
                    <p class="text-sm font-medium opacity-80">Your account</p>
                    <h1 class="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl">Welcome back</h1>
                    <p class="mt-3 max-w-xl text-sm opacity-80">
                        Track your orders, review your purchase history, and continue shopping.
                    </p>
                    <Button as-child variant="secondary" class="mt-6">
                        <Link href="/">
                            <ShoppingBag class="size-4" />
                            Continue shopping
                        </Link>
                    </Button>
                </section>

                <section class="grid gap-4 sm:grid-cols-3">
                    <Card>
                        <CardContent class="flex items-center gap-4 p-5">
                            <div class="rounded-xl bg-primary/10 p-3 text-primary"><ClipboardList class="size-5" /></div>
                            <div>
                                <p class="text-sm text-muted-foreground">Total orders</p>
                                <p class="mt-1 text-2xl font-semibold">{{ stats.orders_count }}</p>
                            </div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardContent class="flex items-center gap-4 p-5">
                            <div class="rounded-xl bg-amber-500/10 p-3 text-amber-600"><Package class="size-5" /></div>
                            <div>
                                <p class="text-sm text-muted-foreground">In progress</p>
                                <p class="mt-1 text-2xl font-semibold">{{ stats.pending_orders }}</p>
                            </div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardContent class="flex items-center gap-4 p-5">
                            <div class="rounded-xl bg-emerald-500/10 p-3 text-emerald-600"><ShoppingBag class="size-5" /></div>
                            <div>
                                <p class="text-sm text-muted-foreground">Total spent</p>
                                <p class="mt-1 text-2xl font-semibold">{{ formatCurrency(stats.total_spent) }}</p>
                            </div>
                        </CardContent>
                    </Card>
                </section>

                <Card>
                    <CardHeader class="flex-row items-center justify-between space-y-0">
                        <div>
                            <CardTitle>Recent orders</CardTitle>
                            <CardDescription class="mt-1">Your latest purchases.</CardDescription>
                        </div>
                        <Button as-child variant="outline" size="sm">
                            <Link href="/orders">View all <ArrowRight class="size-4" /></Link>
                        </Button>
                    </CardHeader>
                    <CardContent>
                        <div v-if="recentOrders.length" class="divide-y">
                            <Link
                                v-for="order in recentOrders"
                                :key="order.id"
                                :href="`/orders/${order.id}`"
                                class="flex items-center justify-between gap-4 py-4 first:pt-0 last:pb-0 hover:bg-muted/30"
                            >
                                <div>
                                    <p class="font-medium">Order #{{ order.id }}</p>
                                    <p class="text-sm text-muted-foreground">{{ order.created_at }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-medium">{{ formatCurrency(order.total) }}</p>
                                    <p class="text-sm capitalize text-muted-foreground">{{ order.status }}</p>
                                </div>
                            </Link>
                        </div>
                        <div v-else class="py-8 text-center">
                            <p class="font-medium">No orders yet</p>
                            <p class="mt-1 text-sm text-muted-foreground">Your purchases will appear here.</p>
                            <Button as-child class="mt-4"><Link href="/">Browse products</Link></Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
