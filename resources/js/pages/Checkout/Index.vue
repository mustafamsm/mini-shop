<template>
    <Head title="Checkout" />
    <ShopLayout>
        <h1 class="mb-8 text-3xl font-semibold">Checkout</h1>

        <div class="grid grid-cols-2 gap-12">
            <div>
                <h2 class="mb-4 text-lg font-medium">Shipping address</h2>

                <div
                    v-if="!showAddressForm && addresses.length > 0"
                    class="mb-4 space-y-3"
                >
                    <label
                        v-for="address in addresses"
                        :key="address.id"
                        class="flex cursor-pointer items-start gap-3 rounded border p-4"
                        :class="
                            selectedAddressId === address.id
                                ? 'border-black'
                                : 'border-neutral-200'
                        "
                    >
                        <input
                            type="radio"
                            :value="address.id"
                            v-model="selectedAddressId"
                        />
                        <div class="text-sm">
                            <p class="font-medium">
                                {{ address.label ?? 'Address' }}
                            </p>
                            <p>
                                {{ address.line1 }}, {{ address.city }}
                                {{ address.postal_code }}
                            </p>
                        </div>
                    </label>

                    <button
                        @click="showAddressForm = true"
                        class="text-sm underline"
                    >
                        + Add a new address
                    </button>
                </div>

                <form v-else @submit.prevent="saveAddress" class="space-y-3">
                    <input
                        v-model="addressForm.label"
                        placeholder="Label (Home, Work...)"
                        class="w-full rounded border px-3 py-2"
                    />
                    <input
                        v-model="addressForm.line1"
                        placeholder="Address line 1"
                        class="w-full rounded border px-3 py-2"
                        required
                    />
                    <input
                        v-model="addressForm.line2"
                        placeholder="Address line 2"
                        class="w-full rounded border px-3 py-2"
                    />
                    <div class="grid grid-cols-2 gap-3">
                        <input
                            v-model="addressForm.city"
                            placeholder="City"
                            class="rounded border px-3 py-2"
                            required
                        />
                        <input
                            v-model="addressForm.state"
                            placeholder="State"
                            class="rounded border px-3 py-2"
                        />
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <input
                            v-model="addressForm.postal_code"
                            placeholder="Postal code"
                            class="rounded border px-3 py-2"
                            required
                        />
                        <input
                            v-model="addressForm.country"
                            placeholder="Country"
                            class="rounded border px-3 py-2"
                            required
                        />
                    </div>
                    <button
                        type="submit"
                        class="rounded bg-black px-6 py-2 text-white"
                        :disabled="addressForm.processing"
                    >
                        Save address
                    </button>
                </form>
            </div>

            <div>
                <h2 class="mb-4 text-lg font-medium">Order summary</h2>
                <div class="divide-y rounded border">
                    <div
                        v-for="item in cart.items"
                        :key="item.id"
                        class="flex justify-between p-4 text-sm"
                    >
                        <span
                            >{{ item.product_variant.product.name }} ×
                            {{ item.quantity }}</span
                        >
                        <span
                            >${{
                                (itemPrice(item) * item.quantity).toFixed(2)
                            }}</span
                        >
                    </div>
                    <div class="flex justify-between p-4 font-medium">
                        <span>Total</span>
                        <span>${{ subtotal().toFixed(2) }}</span>
                    </div>
                </div>

                <button
                    @click="placeOrder"
                    :disabled="!selectedAddressId || placeOrderForm.processing"
                    class="mt-6 w-full rounded bg-black px-6 py-3 text-white disabled:opacity-40"
                >
                    Place order
                </button>

                <p
                    v-if="placeOrderForm.errors.cart"
                    class="mt-2 text-sm text-red-600"
                >
                    {{ placeOrderForm.errors.cart }}
                </p>
            </div>
        </div>
    </ShopLayout>
</template>

<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import ShopLayout from '@/layouts/ShopLayout.vue';
import AddressController from '@/actions/App/Http/Controllers/AddressController';
import CheckoutController from '@/actions/App/Http/Controllers/CheckoutController';

interface Address {
    id: number;
    label: string | null;
    line1: string;
    city: string;
    postal_code: string;
}

interface CartItem {
    id: number;
    quantity: number;
    product_variant: {
        price_override: number | null;
        product: {
            name: string;
            base_price: number;
        };
    };
}

const props = defineProps<{
    cart: { items: CartItem[] };
    addresses: Address[];
}>();
const selectedAddressId = ref<number | null>(props.addresses[0]?.id ?? null);
const showAddressForm = ref(props.addresses.length === 0);

const addressForm = useForm({
    label: '',
    line1: '',
    line2: '',
    city: '',
    state: '',
    postal_code: '',
    country: '',
});

function saveAddress() {
    addressForm.submit(AddressController.store(), {
        preserveScroll: true,
        onSuccess: () => {
            showAddressForm.value = false;
            addressForm.reset();
        },
    });
}

const placeOrderForm = useForm({
    address_id: selectedAddressId.value,
});
function placeOrder() {
    placeOrderForm.address_id = selectedAddressId.value;
    placeOrderForm.submit(CheckoutController.store());
}

function itemPrice(item: CartItem): number {
    return (
        item.product_variant.price_override ??
        item.product_variant.product.base_price
    );
}

function subtotal(): number {
    return props.cart.items.reduce(
        (sum, item) => sum + itemPrice(item) * item.quantity,
        0,
    );
}
</script>

<style scoped></style>
