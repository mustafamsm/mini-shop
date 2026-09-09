import { usePage } from '@inertiajs/vue3'


interface PageProps {
    auth: {
        user: { id: number; name: string; email: string } | null
        permissions: string[]
    }
    flash: { success?: string; error?: string }
    [key: string]: any
}


export function usePermissions() {
    const page = usePage<PageProps>()

    function canAccess(permission: string): boolean {
        return page.props.auth.permissions.includes(permission)
    }

    return { canAccess }
}
