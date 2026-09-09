import { usePage } from '@inertiajs/vue3'
import type { PageProps } from '@inertiajs/core'
import { watch } from 'vue'
import { toast } from 'vue-sonner'


interface FlashProps extends PageProps {
    flash: {
        success?: string
        error?: string
    }
}


export function useFlashToasts(): void {
    const page = usePage<FlashProps>()

    watch(
        () => page.props.flash,
        (flash) => {
            if (flash?.success) {
                toast.success(flash.success)
            }

            if (flash?.error){
                toast.error(flash.error)
            }
        },
        {
            deep:true
        }
    )
}
