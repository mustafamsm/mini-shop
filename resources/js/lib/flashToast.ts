import { router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import type { FlashToast } from '@/types/ui';

export function initializeFlashToast(): void {
    let lastToast = '';
    let lastToastAt = 0;

    function showToast(data: FlashToast | undefined): void {
        if (!data || !toast[data.type]) {
            return;
        }

        const toastKey = `${data.type}:${data.message}`;
        const now = Date.now();

        if (toastKey === lastToast && now - lastToastAt < 1000) {
            return;
        }

        lastToast = toastKey;
        lastToastAt = now;
        toast[data.type](data.message);
    }

    router.on('flash', (event) => {
        const data = (event as CustomEvent).detail?.flash?.toast as FlashToast | undefined;
        showToast(data);
    });

    router.on('navigate', (event) => {
        const data = (event as CustomEvent).detail?.page?.props?.flash?.toast as FlashToast | undefined;
        showToast(data);
    });

    router.on('success', (event) => {
        const data = (event as CustomEvent).detail?.page?.props?.flash?.toast as FlashToast | undefined;
        showToast(data);
    });

    // router.on('exception', () => {
    //     toast.error('A network error occurred. Please check your connection.')
    // })
}
