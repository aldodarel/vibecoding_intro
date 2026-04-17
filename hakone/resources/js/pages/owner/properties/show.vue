<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed, type PropType } from 'vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';

const props = defineProps({
    property: {
        type: Object as PropType<Record<string, any>>,
        required: true,
    },
});

const imageUrl = computed(() => {
    if (props.property.cover_image_url) {
        return props.property.cover_image_url;
    }

    if (props.property.cover_image) {
        return `/storage/${props.property.cover_image}`;
    }

    return null;
});

const statusLabel = computed(() => {
    return props.property.status === 'active' ? 'Aktif' : 'Tidak Aktif';
});

const statusClasses = computed(() => {
    return props.property.status === 'active'
        ? 'inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700'
        : 'inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700';
});
</script>

<template>

    <Head :title="`Detail Property: ${props.property.name}`" />

    <div class="p-6 space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-2xl font-semibold text-foreground">{{ props.property.name }}</h1>
                    <span :class="statusClasses">{{ statusLabel }}</span>
                </div>
                <p class="text-sm text-slate-500">Detail lengkap properti Anda dengan ringkasan informasi dasar.</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <Link :href="route('owner.properties.edit', props.property.id)">
                    <Button variant="outline" class="rounded-md">Edit</Button>
                </Link>
                <Link :href="route('owner.properties.index')">
                    <Button class="rounded-md">Kembali</Button>
                </Link>
                <Button as-child>
                    <Link :href="route('owner.properties.units.index', property.id)">
                        Kelola Units
                    </Link>
                </Button>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.4fr_1fr]">
            <Card>
                <CardHeader>
                    <CardTitle>Informasi Properti</CardTitle>
                    <CardDescription>Ringkasan detail penting untuk properti ini.</CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="space-y-1">
                            <p class="text-xs font-medium uppercase tracking-[0.12em] text-slate-500">Tipe</p>
                            <p class="text-sm text-slate-900">{{ props.property.property_type?.name ?? '-' }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-xs font-medium uppercase tracking-[0.12em] text-slate-500">Kota</p>
                            <p class="text-sm text-slate-900">{{ props.property.city ?? '-' }}</p>
                        </div>
                        <div class="sm:col-span-2 space-y-1">
                            <p class="text-xs font-medium uppercase tracking-[0.12em] text-slate-500">Alamat</p>
                            <p class="text-sm text-slate-900">{{ props.property.address ?? 'Belum diisi' }}</p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <p class="text-xs font-medium uppercase tracking-[0.12em] text-slate-500">Deskripsi</p>
                        <p class="whitespace-pre-line text-sm leading-6 text-slate-700">{{ props.property.description ??
                            'Tidak ada deskripsi untuk property ini.' }}</p>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Cover Image</CardTitle>
                    <CardDescription>Gambar properti yang menjadi tampilan utama.</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">
                        <template v-if="imageUrl">
                            <img :src="imageUrl" alt="Cover property" class="h-72 w-full object-cover" />
                        </template>
                        <template v-else>
                            <div
                                class="flex h-72 w-full items-center justify-center bg-slate-100 text-center text-sm text-slate-500">
                                Tidak ada gambar tersedia untuk properti ini.
                            </div>
                        </template>
                    </div>
                </CardContent>
            </Card>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <Card>
                <CardHeader>
                    <CardTitle>Jumlah Unit</CardTitle>
                    <CardDescription>Placeholder untuk data unit yang akan ditambahkan di lesson berikutnya.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <p class="text-3xl font-semibold text-slate-900">—</p>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Jumlah Penyewa</CardTitle>
                    <CardDescription>Placeholder untuk informasi penyewa yang akan melengkapi halaman ini.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <p class="text-3xl font-semibold text-slate-900">—</p>
                </CardContent>
            </Card>
        </div>

    </div>
</template>
