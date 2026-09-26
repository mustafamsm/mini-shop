<script setup lang="ts">
import { useFlashToasts } from '@/composables/useFlashToasts';
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Toaster } from 'vue-sonner';

useFlashToasts();
const { breadcrumbs = [] } = defineProps<{
    breadcrumbs?: BreadcrumbItem[];
}>();
</script>

<template>
    <Toaster richColors position="top-right" />
    <AppLayout :breadcrumbs="breadcrumbs">
         <slot />
    </AppLayout>
</template>
