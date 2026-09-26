<script setup lang="ts">
import { computed, ref } from 'vue'
import { useNotifications } from '@/composables/useNotifications'

const { notifications, unreadCount, markAsRead } = useNotifications()
const open = ref(false)

const notificationList = computed(() => {
  const list = notifications && 'value' in notifications ? notifications.value : notifications

  return Array.isArray(list) ? list : []
})

const unreadTotal = computed(() => {
  const count = unreadCount && 'value' in unreadCount ? unreadCount.value : unreadCount

  return Number(count ?? 0)
})

function toggle(): void {
  open.value = !open.value
}
</script>

<template>
  <div class="relative">
    <button @click="toggle" class="relative p-2">
      🔔
      <span
        v-if="unreadTotal > 0"
        class="absolute -top-1 -right-1 bg-red-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center"
      >
        {{ unreadTotal }}
      </span>
    </button>

    <div v-if="open" class="absolute right-0 mt-2 w-80 bg-white border rounded shadow-lg z-50">
      <div v-if="notificationList.length === 0" class="p-4 text-sm text-neutral-500">
        No notifications yet.
      </div>
      <div
        v-for="n in notificationList"
        :key="n.id"
        @click="markAsRead(n.id)"
        class="p-3 border-b text-sm cursor-pointer hover:bg-neutral-50"
        :class="n.read_at ? 'opacity-50' : 'font-medium'"
      >
        New order {{ n.data.order_number }} — ${{ n.data.total }}
      </div>
    </div>
  </div>
</template>
