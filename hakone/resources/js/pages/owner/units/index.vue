<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref, type PropType } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';

const props = defineProps({
    property: {
        type: Object as PropType<{ id: number; name: string; city: string }>,
        required: true,
    },
    units: {
        type: Array as PropType<Array<Record<string, any>>>,
        required: true,
    },
});

const showDeleteDialog = ref(false);
const unitToDelete = ref<Record<string, any> | null>(null);

const openDeleteDialog = (unit: Record<string, any>): void => {
    unitToDelete.value = unit;
    showDeleteDialog.value = true;
};

const confirmDelete = (): void => {
    if (!unitToDelete.value) {
        return;
    }

    const url = route(
        'owner.properties.units.destroy',
        [props.property.id, unitToDelete.value.id]
    );

    showDeleteDialog.value = false;

    router.delete(url, {
        preserveState: true,
        preserveScroll: true,
    });
};

const badgeClass = (status: string): string => {
    if (status === 'available') {
        return 'inline-flex rounded-full bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-800';
    }

    if (status === 'occupied') {
        return 'inline-flex rounded-full bg-sky-100 px-2 py-1 text-xs font-semibold text-sky-800';
    }

    return 'inline-flex rounded-full bg-amber-100 px-2 py-1 text-xs font-semibold text-amber-800';
};

const visibleFacilities = (facilities: unknown): string[] => {
    if (!Array.isArray(facilities)) {
        return [];
    }

    return facilities.slice(0, 3).filter((item) => typeof item === 'string');
};

const extraFacilitiesCount = (facilities: unknown): number => {
    if (!Array.isArray(facilities)) {
        return 0;
    }

    return Math.max(0, facilities.length - 3);
};

const hasUnits = computed(() => props.units.length > 0);
</script>

<template>

    <Head title="Units - {{ props.property.name }}" />

    <div class="p-6 space-y-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <nav class="text-sm text-slate-500">
                    <Link :href="route('owner.properties.index')" class="hover:text-slate-900">Properties</Link>
                    <span class="mx-2">/</span>
                    <Link :href="route('owner.properties.show', props.property.id)" class="hover:text-slate-900">
                        {{ props.property.name }}
                    </Link>
                    <span class="mx-2">/</span>
                    <span class="font-semibold text-slate-900">Units</span>
                </nav>
                <h1 class="mt-2 text-2xl font-semibold text-foreground">
                    Units - {{ props.property.name }}
                </h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Kelola semua unit di property {{ props.property.name }}, {{ props.property.city }}.
                </p>
            </div>

            <Link :href="route('owner.properties.units.create', props.property.id)">
                <Button class="bg-sky-600 text-white hover:bg-sky-700">Tambah Unit</Button>
            </Link>
        </div>

        <div v-if="!hasUnits" class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-12 text-center">
            <p class="text-lg font-semibold text-slate-900">Belum ada unit untuk property ini.</p>
            <p class="mt-2 text-sm text-slate-600">Klik tombol Tambah Unit untuk membuat unit baru.</p>
        </div>

        <div v-else class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
            <Card v-for="unit in props.units" :key="unit.id">
                <CardHeader>
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <CardTitle>{{ unit.name }}</CardTitle>
                            <p class="mt-1 text-sm text-slate-500">{{ unit.price_formatted }}</p>
                        </div>
                        <span :class="badgeClass(unit.status)">
                            {{ unit.status === 'available' ? 'Available' : unit.status === 'occupied' ? 'Occupied' :
                            'Maintenance' }}
                        </span>
                    </div>
                </CardHeader>

                <CardContent class="space-y-4">
                    <div class="space-y-2 text-sm text-slate-700">
                        <div>
                            <span class="font-semibold">Lantai:</span>
                            <span>{{ unit.floor ?? '-' }}</span>
                        </div>

                        <div>
                            <span class="font-semibold">Fasilitas:</span>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <template v-if="visibleFacilities(unit.facilities).length">
                                    <span v-for="(facility, index) in visibleFacilities(unit.facilities)"
                                        :key="`facility-${unit.id}-${index}`"
                                        class="rounded-full border border-slate-200 bg-slate-100 px-3 py-1 text-xs text-slate-700">
                                        {{ facility }}
                                    </span>
                                </template>
                                <span v-if="extraFacilitiesCount(unit.facilities) > 0"
                                    class="rounded-full border border-slate-200 bg-slate-100 px-3 py-1 text-xs text-slate-700">
                                    +{{ extraFacilitiesCount(unit.facilities) }} lagi
                                </span>
                                <span v-if="!visibleFacilities(unit.facilities).length" class="text-slate-500">
                                    Tidak ada fasilitas
                                </span>
                            </div>
                        </div>

                        <div v-if="unit.status === 'occupied'" class="text-sm">
                            <span class="font-semibold">Renter:</span>
                            <span>{{ unit.renter?.name ?? 'Tidak tersedia' }}</span>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <Link :href="route('owner.properties.units.edit', [props.property.id, unit.id])"
                            class="w-full sm:w-auto">
                            <Button variant="outline" class="w-full sm:w-auto">Edit</Button>
                        </Link>
                        <Button variant="destructive" class="w-full sm:w-auto" type="button"
                            @click="openDeleteDialog(unit)">
                            Hapus
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>

        <Dialog v-model:open="showDeleteDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Hapus Unit?</DialogTitle>
                </DialogHeader>
                <div class="space-y-3 pt-4 text-sm text-slate-600">
                    <p>
                        Unit "{{ unitToDelete?.name }}" akan dihapus. Aksi ini tidak dapat dibatalkan.
                    </p>
                </div>
                <DialogFooter class="justify-end gap-2">
                    <Button variant="outline" @click="showDeleteDialog = false">Batal</Button>
                    <Button variant="destructive" @click="confirmDelete">Hapus</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
