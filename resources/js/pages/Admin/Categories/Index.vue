<script setup lang="ts">
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import {
    FolderTree,
    Hash,
    Package,
    Pencil,
    Plus,
    Save,
    Tags,
    Trash2,
    X,
} from '@lucide/vue';
import { computed, ref } from 'vue';
import { Badge } from '@/components/ui/badge';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import CategoryController from '@/actions/App/Http/Controllers/Admin/CategoryController';
import { usePermissions } from '@/composables/usePermissions'

interface Category {
    id: number;
    name: string;
    slug: string;
    products_count: number;
}

const props = defineProps<{ categories: Category[] }>();
const page = usePage();
const form = useForm({ name: '', slug: '' });
const editForm = useForm({ name: '', slug: '' });
const editingId = ref<number | null>(null);

const { canAccess } = usePermissions()
const totalProducts = computed(() =>
    props.categories.reduce(
        (total, category) => total + category.products_count,
        0,
    ),
);
const summaryStats = computed(() => [
    {
        label: 'Total categories',
        value: props.categories.length,
        icon: FolderTree,
        iconClass:
            'bg-[#e6e3ef] text-[#665e85] dark:bg-[#665e85]/25 dark:text-[#bbb2e2]',
    },
    {
        label: 'Products organised',
        value: totalProducts.value,
        icon: Package,
        iconClass:
            'bg-[#e6eee5] text-[#48624f] dark:bg-[#48624f]/25 dark:text-[#a8d0ae]',
    },
    {
        label: 'Average per group',
        value: props.categories.length
            ? Math.round(totalProducts.value / props.categories.length)
            : 0,
        icon: Tags,
        iconClass:
            'bg-[#eee8d9] text-[#8d6b31] dark:bg-[#8d6b31]/25 dark:text-[#e8cb92]',
    },
]);

function slugify(name: string): string {
    return name
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/(^-|-$)/g, '');
}

function updateCreateSlug(): void {
    form.slug = slugify(form.name);
}

function updateEditSlug(): void {
    editForm.slug = slugify(editForm.name);
}

function create(): void {
    form.submit(CategoryController.store(), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}

function startEdit(category: Category): void {
    editingId.value = category.id;
    editForm.name = category.name;
    editForm.slug = category.slug;
}

function saveEdit(id: number): void {
    editForm.submit(CategoryController.update(id), {
        preserveScroll: true,
        onSuccess: () => (editingId.value = null),
    });
}

function remove(id: number): void {
    router.delete(CategoryController.destroy(id).url, { preserveScroll: true });
}
</script>

<template>
    <Head title="Categories" />
    <AppLayout>
        <div class="categories-shell min-h-full p-4 sm:p-6 lg:p-8">
            <div class="mx-auto max-w-6xl space-y-6">
                <div
                    class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between"
                >
                    <div>
                        <p
                            class="text-xs font-semibold uppercase tracking-[0.2em] text-[#9b7a43] dark:text-[#d7b77a]"
                        >
                            Catalogue structure
                        </p>
                        <h1
                            class="font-display mt-2 text-4xl tracking-tight sm:text-5xl"
                        >
                            Categories
                        </h1>
                        <p class="text-muted-foreground mt-2 max-w-xl text-sm">
                            Organise your products into clear, useful groups for
                            your shop.
                        </p>
                    </div>
                    <Badge
                        variant="outline"
                        class="w-fit gap-1.5 border-[#48624f]/30 bg-[#e6eee5] text-[#48624f] dark:border-[#a8d0ae]/30 dark:bg-[#48624f]/20 dark:text-[#a8d0ae]"
                        ><span
                            class="size-1.5 rounded-full bg-current"
                        />Catalogue live</Badge
                    >
                </div>

                <div class="grid gap-4 sm:grid-cols-3">
                    <Card
                        v-for="stat in summaryStats"
                        :key="stat.label"
                        class="border-border/70 shadow-sm"
                        ><CardContent class="flex items-center gap-4 p-5"
                            ><div :class="['rounded-xl p-3', stat.iconClass]">
                                <component :is="stat.icon" class="size-5" />
                            </div>
                            <div>
                                <p
                                    class="text-2xl font-semibold tracking-tight"
                                >
                                    {{ stat.value }}
                                </p>
                                <p class="text-muted-foreground text-xs">
                                    {{ stat.label }}
                                </p>
                            </div></CardContent
                        ></Card
                    >
                </div>

                <p
                    v-if="page.props.errors?.category"
                    class="border-destructive/30 bg-destructive/10 text-destructive rounded-md border px-3 py-2 text-sm"
                >
                    {{ page.props.errors.category }}
                </p>

                <div class="grid gap-6 lg:grid-cols-[340px_1fr]">
                    <Card class="border-border/70 h-fit shadow-sm" v-if="canAccess('create categories')">
                        <CardHeader
                            ><div class="flex items-center gap-3">
                                <div
                                    class="rounded-xl bg-[#e6e3ef] p-3 text-[#665e85] dark:bg-[#665e85]/25 dark:text-[#bbb2e2]"
                                >
                                    <Plus class="size-5" />
                                </div>
                                <div>
                                    <CardTitle class="font-display text-2xl"
                                        >New category</CardTitle
                                    ><CardDescription class="mt-1"
                                        >Create a group for your
                                        products.</CardDescription
                                    >
                                </div>
                            </div></CardHeader
                        >
                        <CardContent
                            ><form @submit.prevent="create" class="space-y-4">
                                <div class="space-y-2">
                                    <label
                                        for="category-name"
                                        class="text-sm font-medium"
                                        >Name</label
                                    ><Input
                                        id="category-name"
                                        v-model="form.name"
                                        @blur="updateCreateSlug"
                                        placeholder="e.g. Accessories"
                                        :aria-invalid="!!form.errors.name"
                                    />
                                    <p
                                        v-if="form.errors.name"
                                        class="text-destructive text-xs"
                                    >
                                        {{ form.errors.name }}
                                    </p>
                                </div>
                                <div class="space-y-2">
                                    <label
                                        for="category-slug"
                                        class="text-sm font-medium"
                                        >Slug</label
                                    >
                                    <div class="relative">
                                        <Hash
                                            class="text-muted-foreground pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2"
                                        /><Input
                                            id="category-slug"
                                            v-model="form.slug"
                                            placeholder="accessories"
                                            class="pl-9 font-mono text-sm"
                                            :aria-invalid="!!form.errors.slug"
                                        />
                                    </div>
                                    <p class="text-muted-foreground text-xs">
                                        Generated from the name when you leave
                                        it.
                                    </p>
                                    <p
                                        v-if="form.errors.slug"
                                        class="text-destructive text-xs"
                                    >
                                        {{ form.errors.slug }}
                                    </p>
                                </div>
                                <Button
                                    type="submit"
                                    class="w-full"
                                    :disabled="form.processing"
                                    ><Plus class="size-4" />{{
                                        form.processing
                                            ? 'Adding...'
                                            : 'Add category'
                                    }}</Button
                                >
                            </form></CardContent
                        >
                    </Card>

                    <Card class="border-border/70 overflow-hidden shadow-sm">
                        <CardHeader
                            class="border-border/70 bg-muted/20 border-b"
                            ><CardTitle class="font-display text-2xl"
                                >Your categories</CardTitle
                            ><CardDescription class="mt-1"
                                >Edit names and slugs or remove unused
                                groups.</CardDescription
                            ></CardHeader
                        >
                        <CardContent class="p-0">
                            <div
                                v-if="categories.length === 0"
                                class="flex flex-col items-center justify-center px-6 py-16 text-center"
                            >
                                <div
                                    class="bg-muted text-muted-foreground rounded-2xl p-4"
                                >
                                    <FolderTree class="size-7" />
                                </div>
                                <h2 class="font-display mt-4 text-2xl">
                                    No categories yet
                                </h2>
                                <p class="text-muted-foreground mt-2 text-sm">
                                    Add your first category to bring structure
                                    to the catalogue.
                                </p>
                            </div>
                            <div
                                v-for="category in categories"
                                v-else
                                :key="category.id"
                                class="border-border/70 border-b p-4 last:border-0 sm:px-6"
                            >
                                <template v-if="editingId === category.id"
                                    ><div
                                        class="flex flex-col gap-3 sm:flex-row sm:items-start"
                                    >
                                        <div class="flex-1" >
                                            <Input
                                                v-model="editForm.name"
                                                @blur="updateEditSlug"
                                                :aria-invalid="
                                                    !!editForm.errors.name
                                                "
                                            />
                                            <p
                                                v-if="editForm.errors.name"
                                                class="text-destructive mt-1 text-xs"
                                            >
                                                {{ editForm.errors.name }}
                                            </p>
                                        </div>
                                        <div class="sm:w-44">
                                            <Input
                                                v-model="editForm.slug"
                                                :aria-invalid="
                                                    !!editForm.errors.slug
                                                "
                                                class="font-mono text-sm"
                                            />
                                            <p
                                                v-if="editForm.errors.slug"
                                                class="text-destructive mt-1 text-xs"
                                            >
                                                {{ editForm.errors.slug }}
                                            </p>
                                        </div>
                                        <div class="flex gap-2">
                                            <Button
                                                size="sm"
                                                :disabled="editForm.processing"
                                                @click="saveEdit(category.id)"
                                                ><Save
                                                    class="size-4"
                                                />Save</Button
                                            ><Button
                                                size="sm"
                                                variant="ghost"
                                                @click="editingId = null"
                                                ><X
                                                    class="size-4"
                                                />Cancel</Button
                                            >
                                        </div>
                                    </div></template
                                >
                                <template v-else
                                    ><div class="flex items-center gap-3">
                                        <div
                                            class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-[#eee8d9] text-[#8d6b31] dark:bg-[#8d6b31]/25 dark:text-[#e8cb92]"
                                        >
                                            <Tags class="size-5" />
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate font-semibold">
                                                {{ category.name }}
                                            </p>
                                            <p
                                                class="text-muted-foreground mt-0.5 truncate font-mono text-xs"
                                            >
                                                /{{ category.slug }}
                                            </p>
                                        </div>
                                        <Badge
                                            variant="secondary"
                                            class="hidden gap-1.5 sm:flex"
                                            ><Package class="size-3.5" />{{
                                                category.products_count
                                            }}</Badge
                                        ><Button
                                        v-if="canAccess('edit categories')"
                                            variant="ghost"
                                            size="icon-sm"
                                            aria-label="Edit category"
                                            @click="startEdit(category)"
                                            ><Pencil class="size-4" /></Button
                                        ><Button
                                        v-if="canAccess('delete categories')"
                                            variant="ghost"
                                            size="icon-sm"
                                            class="text-muted-foreground hover:text-destructive"
                                            aria-label="Delete category"
                                            @click="remove(category.id)"
                                            ><Trash2 class="size-4"
                                        /></Button>
                                    </div>
                                    <div
                                        class="text-muted-foreground mt-3 flex items-center gap-1.5 text-xs sm:hidden"
                                    >
                                        <Package class="size-3.5" />{{
                                            category.products_count
                                        }}
                                        products
                                    </div></template
                                >
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.categories-shell {
    background:
        radial-gradient(
            circle at 82% 0%,
            rgba(176, 141, 87, 0.1),
            transparent 26rem
        ),
        var(--background);
}
.font-display {
    font-family: var(--font-display);
}
</style>
