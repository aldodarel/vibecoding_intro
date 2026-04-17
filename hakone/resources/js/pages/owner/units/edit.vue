<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, type PropType } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import InputError from '@/components/InputError.vue';

const props = defineProps({
    property: {
        type: Object as PropType<{ id: number; name: string }>,
        required: true,
    },
    unit: {
        type: Object as PropType<Record<string, any>>,
        required: true,
    },
});

const formatRupiah = (value: string): string => {
    const digits = value.replace(/\D/g, '');
    if (!digits) {
        return '';
    }

    return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
};

const priceInput = ref(formatRupiah(String(props.unit.price ?? '')));
const form = useForm({
    name: props.unit.name ?? '',
    price: Number(props.unit.price) ?? 0,
    status: props.unit.status ?? 'available',
    floor: props.unit.floor ?? undefined,
    description: props.unit.description ?? '',
    facilities: Array.isArray(props.unit.facilities) ? props.unit.facilities : [],
});

const handlePriceInput = (event: Event): void => {
    const input = event.target as HTMLInputElement;
    const formatted = formatRupiah(input.value);
    priceInput.value = formatted;
    form.price = Number(input.value.replace(/\D/g, '')) || 0;
};

const addFacility = (): void => {
    form.facilities.push('');
};

const removeFacility = (index: number): void => {
    form.facilities.splice(index, 1);
};

const facilityError = (index: number): string | undefined => {
    const errors = form.errors as Record<string, string | undefined>;
    return errors[`facilities.${index}`];
};

const submit = (): void => {
    form.price = Number(priceInput.value.replace(/\D/g, '')) || 0;
    form.put(route('owner.properties.units.update', [props.property.id, props.unit.id]));
};
</script>

<template>

    <Head :title="`Edit Unit: ${props.unit.name}`" />

    <div class="p-6 space-y-6">
        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-foreground">Edit Unit</h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Perbarui data unit untuk property {{ props.property.name }}.
                </p>
            </div>
            <Link :href="route('owner.properties.units.index', props.property.id)">
                <Button variant="outline">Kembali</Button>
            </Link>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Form Edit Unit</CardTitle>
            </CardHeader>

            <CardContent class="space-y-6">
                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="space-y-2">
                        <Label for="name">Nama Unit</Label>
                        <Input id="name" v-model="form.name" placeholder="Contoh: Unit A1" />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="space-y-2">
                        <Label for="price">Harga (Rp)</Label>
                        <Input id="price" type="text" inputmode="numeric" v-model="priceInput" @input="handlePriceInput"
                            placeholder="1.500.000" />
                        <InputError :message="form.errors.price" />
                    </div>

                    <div class="space-y-2">
                        <Label for="status">Status</Label>
                        <select id="status" v-model="form.status"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base text-slate-900 ring-offset-background transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm">
                            <option value="available">Available</option>
                            <option value="occupied">Occupied</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                        <InputError :message="form.errors.status" />
                    </div>

                    <div class="space-y-2">
                        <Label for="floor">Lantai</Label>
                        <Input id="floor" type="number" min="1" max="99" v-model.number="form.floor"
                            placeholder="Opsional" />
                        <InputError :message="form.errors.floor" />
                    </div>

                    <div class="sm:col-span-2 space-y-2">
                        <Label for="description">Deskripsi</Label>
                        <textarea id="description" v-model="form.description" rows="4"
                            placeholder="Deskripsi singkat tentang unit (opsional)"
                            class="min-h-[128px] w-full rounded-md border border-input bg-background px-3 py-2 text-base text-slate-900 outline-none transition focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"></textarea>
                        <InputError :message="form.errors.description" />
                    </div>

                    <div class="sm:col-span-2 space-y-3">
                        <div class="flex items-center justify-between gap-3">
                            <Label>Fasilitas</Label>
                            <Button type="button" variant="outline" size="sm" @click="addFacility">
                                + Tambah Fasilitas
                            </Button>
                        </div>

                        <div class="space-y-3">
                            <div v-for="(facility, index) in form.facilities" :key="`facility-${index}`"
                                class="flex flex-col gap-2 rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-sm font-medium text-slate-700">Fasilitas {{ index + 1 }}</span>
                                    <Button type="button" variant="ghost" class="text-slate-500 hover:text-slate-900"
                                        @click="removeFacility(index)">
                                        Hapus
                                    </Button>
                                </div>
                                <Input v-model="form.facilities[index]" placeholder="Contoh: AC" />
                                <InputError :message="facilityError(index)" />
                            </div>
                        </div>
                        <InputError :message="form.errors.facilities" />
                    </div>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                    <Link :href="route('owner.properties.units.index', props.property.id)">
                        <Button variant="outline" class="w-full sm:w-auto">Batal</Button>
                    </Link>
                    <Button type="button" class="w-full sm:w-auto" :disabled="form.processing" @click="submit">
                        Simpan Perubahan
                    </Button>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
