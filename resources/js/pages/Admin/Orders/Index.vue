<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import OrderController from '@/actions/App/Http/Controllers/Admin/OrderController';
import { usePermissions } from '@/composables/usePermissions';

const { canAccess } = usePermissions();

interface Order {
    id: number;
    order_number: string;
    status: string;
    total: number;
    user: {
        name: string;
    };
}

defineProps<{
    orders: { data: Order[] };
    filters: { status?: string };
}>();

const statuses = [
    'pending',
    'paid',
    'processing',
    'shipped',
    'delivered',
    'cancelled',
];
function updateStatus(order: Order, status: string): void {
    router.patch(
        OrderController.updateStatus(order.id).url,
        { status },
        { preserveScroll: true },
    );
}
</script>

<template>
    <Head title="Orders" />
    <AppLayout>
        <div class="p-6">
            <h1 class="mb-6 text-2xl font-semibold">Orders</h1>
            <table class="w-full overflow-hidden rounded border text-sm">
                <thead class="bg-neutral-100 text-left">
                    <tr>
                        <th class="p-3">Order</th>
                        <th class="p-3">Customer</th>
                        <th class="p-3">Total</th>
                        <th class="p-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="o in orders.data" :key="o.id" class="border-t">
                        <td class="p-3 font-mono text-xs">
                            {{ o.order_number }}
                        </td>
                        <td class="p-3">{{ o.user.name }}</td>
                        <td class="p-3">${{ Number(o.total).toFixed(2) }}</td>
                        <td class="p-3">
                            <select
                                v-if="canAccess('edit orders')"
                                :value="o.status"
                                @change="
                                    updateStatus(
                                        o,
                                        ($event.target as HTMLSelectElement)
                                            .value,
                                    )
                                "
                                class="rounded border px-2 py-1 text-xs"
                            >
                                <option
                                    v-for="s in statuses"
                                    :key="s"
                                    :value="s"
                                >
                                    {{ s }}
                                </option>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>

<style scoped></style>
