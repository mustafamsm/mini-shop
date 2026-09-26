import { onMounted, onUnmounted, ref } from "vue"
import NotificationController from "@/actions/App/Http/Controllers/Admin/NotificationController"




interface DbNotification {
    id: string
    data: {
        order_id: number
        order_number: string
        total: string
    }
    read_at: string | null
    created_at: string
}

export function useNotifications() {
    const notifications = ref<DbNotification[]>([])
    const unreadCount = ref(0)
    let intervalId: ReturnType<typeof setInterval> | null = null

    async function fetchNotifications(): Promise<void> {
        const response = await fetch(NotificationController.index().url, {
            headers: { Accept: 'application/json' }
        })

        if (!response.ok) {
            notifications.value = []
            unreadCount.value = 0
            return 
        }

        const data = (await response.json()) as {
            notifications?: DbNotification[]
            unread_count?: number
        }

        notifications.value = Array.isArray(data.notifications) ? data.notifications : []
        unreadCount.value = Number(data.unread_count ?? 0)
    }
    async function markAsRead(id:string):Promise<void>{
        await fetch(NotificationController.markAsRead(id).url, {
            method: 'POST',
            headers: {
                        'X-CSRF-TOKEN': document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? '',
            }
        })
        await fetchNotifications()
    }
    onMounted(()=>{
        fetchNotifications()
        intervalId=setInterval(fetchNotifications, 30000)
    })
    onUnmounted(()=>{
        if(intervalId){
            clearInterval(intervalId)
        }
    })


    return {
        notifications,
        unreadCount,
        markAsRead,
        fetchNotifications
    }

}
