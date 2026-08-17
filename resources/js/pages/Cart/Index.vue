<template>
    <Head title="Your Cart" />
    <ShopLayout>
        <h1 class="mb-8 text-3xl font-semibold">Your cart</h1>

        <div v-if="cart.items.length === 0" class="text-neutral-500">
            Your cart is empty.
        </div>

        <div v-else class="space-y-4">
            <div
                v-for="item in cart.items"
                :key="item.id"
                class="flex items-center justify-between rounded border p-4"
            >
                <div>
                    <p class="font-medium">
                        {{ item.product_variant.product.name }}
                    </p>
                    <p class="text-sm text-neutral-500">
                        ${{ itemPrice(item).toFixed(2) }} each
                    </p>
                </div>

                <div class="flex items-center gap-4">
                    <input
                        type="number"
                        :value="item.quantity"
                        min="1"
                        class="w-16 rounded border px-2 py-1"
                        @change="
                            updateQuantity(
                                item,
                                +($event.target as HTMLInputElement).value,
                            )
                        "
                    />
                    <span class="w-20 text-right"
                        >${{
                            (itemPrice(item) * item.quantity).toFixed(2)
                        }}</span
                    >
                    <button
                        @click="removeItem(item)"
                        class="text-sm text-red-600"
                    >
                        Remove
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between border-t pt-6">
                <span class="text-lg font-medium"
                    >Total: ${{ subtotal().toFixed(2) }}</span
                >
                <a
                    :href="CheckoutController.index().url"
                    class="rounded bg-black px-6 py-3 text-white"
                >
                    Proceed to checkout
                </a>
            </div>
        </div>
    </ShopLayout>
</template>

<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import ShopLayout from '@/layouts/ShopLayout.vue';
import CartController from '@/actions/App/Http/Controllers/CartController';
import CheckoutController from '@/actions/App/Http/Controllers/CheckoutController';

interface CartItem {
    id: number;
    quantity: number | string;
    product_variant: {
        id: number;
        price_override: number | string | null;
        product: {
            name: string;
            base_price: number | string;
        };
    };
}

const props = defineProps<{
    cart: { items: CartItem[] };
}>();

function toCurrencyNumber(value: number | string | null | undefined): number {
    const numericValue = Number(value);

    return Number.isFinite(numericValue) ? numericValue : 0;
}

function itemPrice(item: CartItem): number {
    return toCurrencyNumber(
        item.product_variant.price_override ??
            item.product_variant.product.base_price,
    );
}
function subtotal(): number {
    return props.cart.items.reduce(
        (sum, item) => sum + itemPrice(item) * Number(item.quantity),
        0,
    );
}

function updateQuantity(item: CartItem, quantity: number) {
    if (quantity < 1) {
        return;
    }

    router.patch(
        CartController.update(item.id).url,
        { quantity },
        { preserveScroll: true },
    );
}
function removeItem(item: CartItem) {
    router.delete(CartController.destroy(item.id).url, {
        preserveScroll: true,
    });
}
</script>

<style scoped></style>
