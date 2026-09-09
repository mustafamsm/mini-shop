<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import RoleController from '@/actions/App/Http/Controllers/Admin/RoleController'

interface Role {
  id: number
  name: string
  permissions: { name: string }[]
}

defineProps<{
  roles: Role[]
  permissions: string[]
}>()

function togglePermission(role: Role, permission: string, checked: boolean): void {
  const current = role.permissions.map((p) => p.name)
  const updated = checked ? [...current, permission] : current.filter((p) => p !== permission)

  router.patch(RoleController.update(role.id).url, { permissions: updated }, { preserveScroll: true })
}
</script>

<template>
  <Head title="Roles & Permissions" />
  <AppLayout>
    <div class="p-6 max-w-2xl">
      <h1 class="text-2xl font-semibold mb-6">Roles & Permissions</h1>

      <div class="space-y-4">
        <div v-for="role in roles" :key="role.id" class="border rounded p-4">
          <h2 class="font-medium capitalize mb-3">{{ role.name }}</h2>

          <div class="grid grid-cols-2 gap-2">
            <label v-for="perm in permissions" :key="perm" class="flex items-center gap-2 text-sm">
              <input
                type="checkbox"
                :checked="role.permissions.some((p) => p.name === perm)"
                @change="togglePermission(role, perm, ($event.target as HTMLInputElement).checked)"
              />
              {{ perm }}
            </label>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
