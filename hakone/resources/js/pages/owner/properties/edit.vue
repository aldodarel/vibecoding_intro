<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, type PropType } from 'vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';

const props = defineProps({
    property: {
        type: Object as PropType<Record<string, any>>,
        required: true,
    },
    propertyTypes: {
        type: Array as PropType<Array<{ id: number; name: string }>>,
        required: true,
    },
});

const imagePreview = ref<string | null>(null);
const existingImageUrl = props.property.cover_image_url ?? (props.property.cover_image ? `/storage/${props.property.cover_image}` : null);

const form = useForm({
    name: props.property.name || '',
    property_type_id: props.property.property_type_id ?? props.property.property_type?.id ?? '',
    address: props.property.address || '',
    city: props.property.city || '',
    description: props.property.description || '',
    status: props.property.status || 'active',
    cover_image: null as File | null,
});

const handleImageChange = (event: Event) => {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];
    if (!file) return;

    form.cover_image = file;

    const reader = new FileReader();
    reader.onload = (e) => {
        imagePreview.value = e.target?.result as string;
    };
    reader.readAsDataURL(file);
};

const submit = (): void => {
    form.submit('patch', route('owner.properties.update', props.property.id), {
        forceFormData: true,
        preserveState: false,
        preserveScroll: false,
    });
};
</script>

<template>

    <Head :title="`Edit Property: ${props.property.name}`" />

    <div class="p-6">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-foreground">Edit Property: {{ props.property.name }}</h1>
                <p class="mt-2 text-sm text-muted-foreground">Perbarui detail property Anda di sini.</p>
            </div>
            <Link :href="route('owner.properties.index')">
                <Button variant="outline">Batal</Button>
            </Link>
        </div>

        <form @submit.prevent="submit">
            <Card>
                <CardHeader>
                    <CardTitle>Form Edit Property</CardTitle>
                    <CardDescription>Ubah data property dan simpan perubahan.</CardDescription>
                </CardHeader>

                <CardContent class="space-y-6">
                    <div class="grid gap-6 sm:grid-cols-2">
                    <div class="space-y-2">
                        <Label for="name">Nama Property</Label>
                        <Input id="name" v-model="form.name" placeholder="Masukkan nama property" />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="space-y-2">
                        <Label for="property_type_id">Tipe Property</Label>
                        <select id="property_type_id" v-model="form.property_type_id"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base text-slate-900 ring-offset-background transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm">
                            <option value="" disabled>Pilih tipe property</option>
                            <option v-for="type in props.propertyTypes" :key="type.id" :value="type.id">{{ type.name }}
                            </option>
                        </select>
                        <InputError :message="form.errors.property_type_id" />
                    </div>

                    <div class="sm:col-span-2 space-y-2">
                        <Label for="address">Alamat</Label>
                        <textarea id="address" v-model="form.address" rows="3" placeholder="Masukkan alamat lengkap"
                            class="min-h-[96px] w-full rounded-md border border-input bg-background px-3 py-2 text-base text-slate-900 outline-none transition focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"></textarea>
                        <InputError :message="form.errors.address" />
                    </div>

                    <div class="space-y-2">
                        <Label for="city">Kota</Label>
                        <Input id="city" v-model="form.city" placeholder="Masukkan kota" />
                        <InputError :message="form.errors.city" />
                    </div>

                    <div class="space-y-2">
                        <Label for="status">Status</Label>
                        <select id="status" v-model="form.status"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base text-slate-900 ring-offset-background transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <InputError :message="form.errors.status" />
                    </div>

                    <div class="sm:col-span-2 space-y-2">
                        <Label for="description">Deskripsi</Label>
                        <textarea id="description" v-model="form.description" rows="4"
                            placeholder="Tambahkan deskripsi property (opsional)"
                            class="min-h-[128px] w-full rounded-md border border-input bg-background px-3 py-2 text-base text-slate-900 outline-none transition focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"></textarea>
                        <InputError :message="form.errors.description" />
                    </div>

                    <div class="sm:col-span-2 space-y-2">
                        <Label for="cover_image">Cover Image</Label>
                        <div v-if="existingImageUrl && !imagePreview" class="mb-3">
                            <img :src="existingImageUrl" alt="Foto saat ini"
                                class="h-32 w-auto rounded-lg object-cover border border-border" />
                            <p class="text-xs text-muted-foreground mt-1">Foto saat ini</p>
                        </div>
                        <input id="cover_image" type="file" accept="image/*" @change="handleImageChange"
                            class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm text-slate-900 file:mr-4 file:rounded-full file:border-0 file:bg-slate-100 file:px-4 file:py-2 file:text-sm file:font-medium file:text-slate-900 focus-visible:outline-none" />
                        <InputError :message="form.errors.cover_image" />

                        <div v-if="imagePreview" class="mt-2">
                            <img :src="imagePreview" alt="Preview"
                                class="h-32 w-auto rounded-lg object-cover border border-border" />
                        </div>
                    </div>
                </div>

                <div class="flex flex-col items-start gap-3 sm:flex-row sm:items-center sm:justify-end">
                    <Link :href="route('owner.properties.index')" class="w-full sm:w-auto">
                        <Button variant="outline" class="w-full sm:w-auto">Batal</Button>
                    </Link>
                    <Button type="submit" class="w-full sm:w-auto" :disabled="form.processing">
                        <span v-if="form.processing"
                            class="mr-2 inline-block h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></span>
                        Simpan Perubahan
                    </Button>
                </div>
            </CardContent>
        </Card>
        </form>
    </div>
</template>
