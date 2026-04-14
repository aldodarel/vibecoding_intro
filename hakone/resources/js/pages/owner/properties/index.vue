<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch, computed, onBeforeUnmount, type PropType } from 'vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';

const props = defineProps({
    properties: {
        type: Object as PropType<{
            data: Array<Record<string, any>>;
            links: Array<Record<string, any>>;
            meta: { current_page: number; last_page: number; total: number };
        }>,
        required: true,
    },
    filters: {
        type: Object as PropType<{ search: string | null }>,
        required: true,
    },
});

const search = ref(props.filters.search ?? '');
const showDeleteDialog = ref(false);
const propertyToDelete = ref<Record<string, any> | null>(null);
let debounceTimeout: number;

const requestDelete = (property: Record<string, any>) => {
    propertyToDelete.value = property;
    showDeleteDialog.value = true;
};

const confirmDelete = () => {
    if (!propertyToDelete.value) {
        return;
    }

    const url = route('owner.properties.destroy', String(propertyToDelete.value.id));
    showDeleteDialog.value = false;

    router.delete(url, {
        preserveState: true,
        preserveScroll: true,
    });
};

watch(
    () => search.value,
    (value) => {
        window.clearTimeout(debounceTimeout);
        debounceTimeout = window.setTimeout(() => {
            router.get(
                route('owner.properties.index'),
                { search: value || undefined },
                { preserveState: true, replace: true }
            );
        }, 300);
    }
);

watch(
    () => props.filters.search,
    (value) => {
        search.value = value ?? '';
    }
);

onBeforeUnmount(() => {
    window.clearTimeout(debounceTimeout);
});

const statusBadgeClasses = (status: string) =>
    status === 'active'
        ? 'inline-flex rounded-full bg-emerald-100 px-2 py-1 text-xs font-medium text-emerald-700'
        : 'inline-flex rounded-full bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700';

const paginationLinks = computed(() => props.properties.links || []);
</script>

<template>

    <Head title="Properties Saya" />

    <div class="p-6 space-y-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-foreground">Properties Saya</h1>
                <p class="mt-2 text-sm text-muted-foreground">Kelola semua property Anda dalam satu tampilan.</p>
            </div>

            <Link :href="route('owner.properties.create')">
                <Button class="bg-[#0ea5e9] hover:bg-[#0ea5e9]/90 text-white">Tambah Property</Button>
            </Link>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Daftar Properti</CardTitle>
                <CardDescription>Gunakan pencarian untuk memfilter property berdasarkan nama atau kota.
                </CardDescription>
            </CardHeader>

            <CardContent class="space-y-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <label class="flex w-full max-w-md flex-col gap-2 text-sm font-medium text-foreground">
                        <span>Cari properti</span>
                        <input v-model="search" type="search" placeholder="Cari nama atau kota..."
                            class="w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm transition focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200" />
                    </label>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 bg-white text-left text-sm">
                        <thead class="bg-slate-50 text-slate-700">
                            <tr>
                                <th class="px-4 py-3 font-medium uppercase tracking-wide">Nama</th>
                                <th class="px-4 py-3 font-medium uppercase tracking-wide">Kota</th>
                                <th class="px-4 py-3 font-medium uppercase tracking-wide">Tipe</th>
                                <th class="px-4 py-3 font-medium uppercase tracking-wide">Status</th>
                                <th class="px-4 py-3 font-medium uppercase tracking-wide">Jumlah Unit</th>
                                <th class="px-4 py-3 font-medium uppercase tracking-wide">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-200">
                            <tr v-if="!props.properties.data.length">
                                <td colspan="6" class="px-4 py-12 text-center text-sm text-slate-500">
                                    Tidak ada property yang cocok dengan pencarian Anda.
                                </td>
                            </tr>

                            <tr v-for="property in props.properties.data" :key="property.id">
                                <td class="px-4 py-4 align-top">
                                    <div class="text-sm font-medium text-slate-900">{{ property.name }}</div>
                                    <div class="mt-1 text-xs text-slate-500">{{ property.address ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-4 align-top text-sm text-slate-700">{{ property.city ?? '-' }}</td>
                                <td class="px-4 py-4 align-top text-sm text-slate-700">{{ property.property_type?.name
                                    ?? property.type ?? '-' }}</td>
                                <td class="px-4 py-4 align-top">
                                    <span :class="statusBadgeClasses(property.status)">
                                        {{ property.status === 'active' ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 align-top text-sm text-slate-700">{{ property.unit_count ??
                                    property.units_count ?? 0 }}</td>
                                <td class="px-4 py-4 align-top">
                                    <div class="flex flex-wrap gap-2">
                                        <Link :href="route('owner.properties.show', property.id)">
                                            <Button variant="secondary" size="sm"
                                                class="rounded-md px-3 py-2 text-xs text-slate-700">Lihat</Button>
                                        </Link>
                                        <Link :href="route('owner.properties.edit', property.id)">
                                            <Button variant="outline" size="sm"
                                                class="rounded-md px-3 py-2 text-xs">Edit</Button>
                                        </Link>
                                        <Button variant="destructive" size="sm" class="rounded-md px-3 py-2 text-xs"
                                            type="button" @click="requestDelete(property)">
                                            Hapus
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <nav class="flex flex-wrap items-center justify-center gap-2 rounded-md bg-slate-50 px-3 py-3 text-sm"
                    aria-label="Pagination">
                    <template v-for="link in paginationLinks" :key="link.label + link.url">
                        <span v-if="!link.url"
                            class="inline-flex items-center rounded-md border border-slate-200 bg-slate-200 px-3 py-2 text-slate-500"
                            v-html="link.label"></span>
                        <Link v-else :href="link.url"
                            class="inline-flex items-center rounded-md border border-slate-200 bg-white px-3 py-2 text-slate-700 transition hover:border-sky-300 hover:bg-sky-50">
                            <span v-html="link.label"></span>
                        </Link>
                    </template>
                </nav>

                <Dialog v-model:open="showDeleteDialog">
                    <DialogContent>
                        <DialogHeader>
                            <DialogTitle>Hapus Property?</DialogTitle>
                            <DialogDescription>
                                Action ini tidak bisa dibatalkan. Property "{{ propertyToDelete?.name }}" akan dihapus
                                permanen.
                            </DialogDescription>
                        </DialogHeader>
                        <DialogFooter class="justify-end gap-2">
                            <Button variant="outline" @click="showDeleteDialog = false">Batal</Button>
                            <Button variant="destructive" @click="confirmDelete">Hapus</Button>
                        </DialogFooter>
                    </DialogContent>
                </Dialog>
            </CardContent>
        </Card>
    </div>
</template>
