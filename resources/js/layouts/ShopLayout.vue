<template>
    <div class="flex min-h-screen flex-col bg-neutral-50">
        <Toaster richColors position="top-right" />
        <header class="border-b bg-white">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-5">
                <Link :href="shopRoute" class="text-2xl font-semibold">
                    Foundry Goods
                </Link>

                <nav class="flex items-center gap-3 text-sm">
                    <Link :href="shopRoute" class="rounded-full px-3 py-2 text-neutral-700 transition hover:bg-neutral-100 hover:text-neutral-900">
                        Shop
                    </Link>
                    <Link :href="ordersRoute" class="rounded-full px-3 py-2 text-neutral-700 transition hover:bg-neutral-100 hover:text-neutral-900">
                        Orders
                    </Link>
                    <CartDrawer />
                </nav>
            </div>
        </header>
        <main class="mx-auto w-full max-w-6xl flex-1 px-6 py-10">
            <slot />
        </main>
    </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Toaster } from 'vue-sonner';
import CartDrawer from '@/components/CartDrawer.vue';
import OrderController from '@/actions/App/Http/Controllers/OrderController';
import ProductController from '@/actions/App/Http/Controllers/ProductController';

const shopRoute = ProductController.index();
const ordersRoute = OrderController.index();
</script>

<style scoped></style>
