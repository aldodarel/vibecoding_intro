<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

defineProps<{
    id: string;
}>();
</script>

<template>
    <Head title="Detail Pembayaran" />

    <div class="p-6">
        <h1 class="text-2xl font-semibold text-foreground">Detail Pembayaran</h1>
        <p class="mt-2 text-muted-foreground">Menampilkan detail pembayaran dengan ID: {{ id }}</p>
    </div>
</template>
