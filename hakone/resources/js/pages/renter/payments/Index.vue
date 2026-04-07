<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Pembayaran" />

    <div class="p-6">
        <h1 class="text-2xl font-semibold text-foreground">Daftar Pembayaran</h1>
        <p class="mt-2 text-muted-foreground">Halaman daftar pembayaran penyewa.</p>

        <Link :href="route('renter.payments.show', 1)" class="mt-4 inline-flex text-primary hover:underline">
            Lihat contoh detail pembayaran
        </Link>
    </div>
</template>
