<script setup lang="ts">
import CartController from '@/actions/App/Http/Controllers/CartController';
import CheckoutController from '@/actions/App/Http/Controllers/CheckoutController';
import { Button } from '@/components/ui/button';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import { Minus, Plus, ShoppingBag, Trash2 } from '@lucide/vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { toast } from 'vue-sonner';

type CartPageProps = {
    cart?: {
        items: CartLineItem[];
    };
};

type CartLineItem = {
    id: number;
    quantity: number;
    product_variant?: {
        id: number;
        stock: number;
        price_override?: number | string | null;
        product?: {
            name: string;
            base_price?: number | string | null;
            image_urls?: { id: number; url: string; thumb_url: string }[];
        };
    };
};

const page = usePage<CartPageProps>();
const cart = computed(() => page.props.cart ?? { items: [] });

const itemCount = computed(() =>
    cart.value.items.reduce(
        (total, item) => total + Number(item.quantity || 0),
        0,
    ),
);

const subtotal = computed(() =>
    cart.value.items.reduce(
        (total, item) => total + lineItemPrice(item) * Number(item.quantity || 0),
        0,
    ),
);

function lineItemPrice(item: CartLineItem): number {
    const value =
        item.product_variant?.price_override ??
        item.product_variant?.product?.base_price ??
        0;

    const numericValue = Number(value);

    return Number.isFinite(numericValue) ? numericValue : 0;
}

function incrementQuantity(item: CartLineItem) {
    const nextQuantity = Number(item.quantity || 0) + 1;
    updateQuantity(item, nextQuantity);
}

function decrementQuantity(item: CartLineItem) {
    const nextQuantity = Number(item.quantity || 0) - 1;
    updateQuantity(item, nextQuantity);
}

function updateQuantity(item: CartLineItem, quantity: number) {
    const maxAllowed = item.product_variant?.stock ?? 0;

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
                const validationErrors = Object.values(errors ?? {})
                    .flat()
                    .filter(Boolean);
                const message = validationErrors.length
                    ? validationErrors.join(' ')
                    : 'Unable to update quantity.';

                toast.error(message);
            },
        },
    );
}

function removeItem(item: CartLineItem) {
    router.delete(CartController.destroy(item.id).url, {
        preserveScroll: true,
    });
}
</script>

<template>
    <Sheet>
        <SheetTrigger as-child>
            <Button
                variant="outline"
                class="relative rounded-full border-neutral-200 bg-white px-3.5 py-2 text-sm font-medium text-neutral-900 shadow-sm transition hover:border-neutral-300 hover:bg-neutral-50"
            >
                Cart
                <span
                    v-if="itemCount > 0"
                    class="ml-2 inline-flex min-w-5 items-center justify-center rounded-full bg-neutral-900 px-1.5 py-0.5 text-[10px] font-semibold text-white"
                >
                    {{ itemCount }}
                </span>
            </Button>
        </SheetTrigger>

        <SheetContent
            side="right"
            class="flex w-full max-w-md flex-col border-l bg-neutral-50 p-0"
        >
            <SheetHeader class="flex items-center justify-between border-b bg-white px-5 py-4">
                <div class="space-y-1 text-left">
                    <SheetTitle class="text-lg font-semibold text-neutral-900">
                        Your cart
                    </SheetTitle>
                    <p class="text-sm text-neutral-500">
                        {{ itemCount }} item{{ itemCount === 1 ? '' : 's' }}
                    </p>
                </div>
                <div class="rounded-full bg-neutral-100 px-2.5 py-1 text-xs font-medium text-neutral-700">
                    {{ itemCount }}
                </div>
            </SheetHeader>

            <div class="flex-1 overflow-y-auto px-4 py-5">
                <div
                    v-if="cart.items.length === 0"
                    class="flex h-full min-h-72 flex-col items-center justify-center rounded-2xl border border-dashed border-neutral-300 bg-white px-6 text-center"
                >
                    <ShoppingBag class="size-10 text-neutral-400" stroke-width="1.5" />
                    <h3 class="mt-4 text-base font-semibold text-neutral-900">
                        Your cart is empty
                    </h3>
                    <p class="mt-1 text-sm text-neutral-500">
                        Add a few essentials and they’ll appear here.
                    </p>
                </div>

                <div v-else class="space-y-4">
                    <div
                        v-for="item in cart.items"
                        :key="item.id"
                        class="rounded-2xl border border-neutral-200 bg-white p-3 shadow-sm"
                    >
                        <div class="flex items-start gap-3">
                            <div class="h-20 w-20 shrink-0 overflow-hidden rounded-xl bg-neutral-100 ring-1 ring-neutral-200">
                                <img
                                    v-if="item.product_variant?.product?.image_urls?.[0]"
                                    :src="item.product_variant.product.image_urls[0].thumb_url || item.product_variant.product.image_urls[0].url"
                                    :alt="item.product_variant.product.name"
                                    class="h-full w-full object-cover"
                                />
                                <div
                                    v-else
                                    class="flex h-full items-center justify-center text-neutral-400"
                                >
                                    <ShoppingBag class="size-5" stroke-width="1.5" />
                                </div>
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-3">
                                    <p class="line-clamp-2 text-sm font-medium text-neutral-900">
                                        {{ item.product_variant?.product?.name ?? 'Product' }}
                                    </p>
                                    <button
                                        type="button"
                                        class="text-neutral-400 transition hover:text-red-600"
                                        aria-label="Remove item"
                                        @click="removeItem(item)"
                                    >
                                        <Trash2 class="size-4" stroke-width="1.8" />
                                    </button>
                                </div>

                                <p class="mt-1 text-xs text-neutral-500">
                                    ${{ lineItemPrice(item).toFixed(2) }} each
                                </p>

                                <div class="mt-3 flex items-center justify-between gap-3">
                                    <div class="inline-flex items-center rounded-full border border-neutral-200 bg-neutral-50 p-1">
                                        <button
                                            type="button"
                                            class="flex size-7 items-center justify-center rounded-full text-neutral-700 transition hover:bg-white"
                                            :disabled="Number(item.quantity) <= 1"
                                            aria-label="Decrease quantity"
                                            @click="decrementQuantity(item)"
                                        >
                                            <Minus class="size-3.5" stroke-width="2" />
                                        </button>
                                        <span class="min-w-8 text-center text-sm font-medium text-neutral-900">
                                            {{ item.quantity }}
                                        </span>
                                        <button
                                            type="button"
                                            class="flex size-7 items-center justify-center rounded-full text-neutral-700 transition hover:bg-white"
                                            :disabled="Number(item.quantity) >= (item.product_variant?.stock ?? 1)"
                                            aria-label="Increase quantity"
                                            @click="incrementQuantity(item)"
                                        >
                                            <Plus class="size-3.5" stroke-width="2" />
                                        </button>
                                    </div>

                                    <span class="text-sm font-semibold text-neutral-900">
                                        ${{ (lineItemPrice(item) * Number(item.quantity)).toFixed(2) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="cart.items.length > 0" class="border-t bg-white p-4">
                <div class="mb-4 flex items-center justify-between text-sm text-neutral-600">
                    <span>Subtotal</span>
                    <span class="text-base font-semibold text-neutral-900">
                        ${{ subtotal.toFixed(2) }}
                    </span>
                </div>

                <div class="grid gap-2">
                    <Button as-child class="w-full rounded-xl bg-neutral-900 text-white hover:bg-neutral-800">
                        <Link :href="CartController.index().url">View cart</Link>
                    </Button>
                    <Button as-child variant="outline" class="w-full rounded-xl border-neutral-200 bg-white text-neutral-900 hover:bg-neutral-50">
                        <Link :href="CheckoutController.index().url">Checkout</Link>
                    </Button>
                </div>
            </div>
        </SheetContent>
    </Sheet>
</template>
