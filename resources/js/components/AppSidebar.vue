<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { BookOpen, FolderGit2, LayoutGrid, ShoppingCart, Layers, Tag, Users, Lock } from '@lucide/vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
 import type { NavItem } from '@/types';
 import admin, { dashboard } from '@/routes/admin';
 import { usePermissions } from '@/composables/usePermissions';
 import { computed } from 'vue';

const { canAccess } = usePermissions();

const allNavItems: (NavItem & { permission?: string })[] = [
    {
        title: 'Dashboard',
        href: dashboard.url(),
        icon: LayoutGrid,
    },
    {
        title: 'Products',
        href: admin.products.index().url,
        icon: ShoppingCart,
        permission: 'view products',
    },
    {
        title: 'Categories',
        href: admin.categories.index().url,
        icon: Layers,
        permission: 'view categories',
    },
    {
        title: 'Orders',
        href: admin.orders.index().url,
        icon: Tag,
        permission: 'view orders',
    },
    {
        title: 'Users',
        href: admin.users.index().url,
        icon: Users,
        permission: 'view users',
    },
    {
        title: 'Roles',
        href: admin.roles.index().url,
        icon: Lock,
        permission: 'view roles',
    },

];

const mainNavItems = computed(() =>
    allNavItems.filter(item => !item.permission || canAccess(item.permission))
);

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: FolderGit2,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
