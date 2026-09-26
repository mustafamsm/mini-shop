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
                <div
                    class="h-16 w-16 shrink-0 overflow-hidden rounded bg-neutral-100"
                >
                    <img
                        v-if="item.product_variant.product.image_urls?.[0]"
                        :src="
                            item.product_variant.product.image_urls[0].thumb_url
                        "
                        :alt="item.product_variant.product.name"
                        class="h-full w-full object-cover"
                    />
                </div>
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
                        :min="1"
                        :max="item.product_variant.stock ?? 1"
                        class="w-16 rounded border px-2 py-1"
                        @change="
                            updateQuantity(
                                item,
                                Number(($event.target as HTMLInputElement).value),
                            )
                        "
                    />
                    <span class="w-20 text-right"
                        >${{
                            (itemPrice(item) * Number(item.quantity)).toFixed(2)
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
import { toast } from 'vue-sonner';

interface CartItem {
    id: number;
    quantity: number | string;
    product_variant: {
        id: number;
        stock: number;
        price_override: number | string | null;
        product: {
            name: string;
            base_price: number | string;
            image_urls?: { id: number; url: string; thumb_url: string }[];
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
    const maxAllowed = item.product_variant.stock ?? 0;

    if (quantity < 1) {
        toast.error('Quantity must be at least 1.');
        return;
    }

    if (quantity > maxAllowed) {
        toast.error(`Only ${maxAllowed} item(s) left in stock.`);
        quantity = maxAllowed;
    }

    router.patch(
        CartController.update(item.id).url,
        { quantity },
        {
            preserveScroll: true,
            onError: (errors) => {
                const validationErrors = Object.values(errors ?? {}).flat().filter(Boolean);
                const message = validationErrors.length
                    ? validationErrors.join(' ')
                    : 'Unable to update quantity.';

                toast.error(message);
            },
        },
    );
}
function removeItem(item: CartItem) {
    router.delete(CartController.destroy(item.id).url, {
        preserveScroll: true,
    });
}
</script>

<style scoped></style>
