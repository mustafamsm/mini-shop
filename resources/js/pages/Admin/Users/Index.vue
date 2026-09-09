<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import UserController from '@/actions/App/Http/Controllers/Admin/UserController';
import { usePermissions } from '@/composables/usePermissions';
import { onMounted } from 'vue';
import ProductController from '@/actions/App/Http/Controllers/ProductController';

interface User {
    id: number;
    name: string;
    email: string;
    roles: { name: string }[];
}

defineProps<{
    users: {
        data: User[];
    };

    roles: string[];
}>();

const { canAccess } = usePermissions();

onMounted(() => {
    if (!canAccess('view users')) {
        router.get(ProductController.index());
    }
});

function updateRole(user:User,role:string):void{
router.patch(UserController.update(user.id).url,{role},{
    preserveScroll:true
})
}
</script>

<template>
    <Head title="Users" />
  <AppLayout>
    <div class="p-6">
      <h1 class="text-2xl font-semibold mb-6">Users</h1>
      <table class="w-full text-sm border rounded overflow-hidden">
        <thead class="bg-neutral-100 text-left">
          <tr><th class="p-3">Name</th><th class="p-3">Email</th><th class="p-3">Role</th></tr>
        </thead>
        <tbody>
          <tr v-for="u in users.data" :key="u.id" class="border-t">
            <td class="p-3">{{ u.name }}</td>
            <td class="p-3">{{ u.email }}</td>
            <td class="p-3">
              <select
                v-if="canAccess('edit users')"
                :value="u.roles[0]?.name"
                @change="updateRole(u, ($event.target as HTMLSelectElement).value)"
                class="border rounded px-2 py-1 text-xs"
              >
                <option v-for="r in roles" :key="r" :value="r">{{ r }}</option>
              </select>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </AppLayout>
</template>

<style scoped></style>
