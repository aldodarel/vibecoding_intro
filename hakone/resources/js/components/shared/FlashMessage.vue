<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed, onUnmounted, ref, watch } from 'vue';

type FlashProps = {
    success?: string;
    error?: string;
    warning?: string;
};

type ToastItem = {
    id: number;
    type: 'success' | 'error' | 'warning';
    message: string;
};

const page = usePage();
const flash = computed(() => (page.props.flash as FlashProps) ?? {});
const toasts = ref<ToastItem[]>([]);
let nextToastId = 1;
const timers = new Map<number, number>();

const toastClasses = (type: ToastItem['type']) => {
    switch (type) {
        case 'success':
            return 'border-emerald-200 bg-emerald-50 text-emerald-900';
        case 'error':
            return 'border-rose-200 bg-rose-50 text-rose-900';
        case 'warning':
            return 'border-amber-200 bg-amber-50 text-amber-900';
        default:
            return 'border-slate-200 bg-white text-slate-900';
    }
};

const addToast = (type: ToastItem['type'], message: string) => {
    const id = nextToastId++;
    toasts.value.push({ id, type, message });

    const timer = window.setTimeout(() => {
        removeToast(id);
    }, 4000);

    timers.set(id, timer);
};

const removeToast = (id: number) => {
    toasts.value = toasts.value.filter((toast) => toast.id !== id);
    const timer = timers.get(id);
    if (timer) {
        window.clearTimeout(timer);
        timers.delete(id);
    }
};

watch(
    flash,
    (value) => {
        if (value.success) {
            addToast('success', value.success);
        }
        if (value.error) {
            addToast('error', value.error);
        }
        if (value.warning) {
            addToast('warning', value.warning);
        }
    },
    { immediate: true }
);

onUnmounted(() => {
    timers.forEach((timeout) => window.clearTimeout(timeout));
    timers.clear();
});
</script>

<template>
    <Teleport to="body">
        <div class="fixed right-4 top-4 z-50 flex max-w-sm flex-col items-end gap-3">
            <TransitionGroup
                name="flash"
                tag="div"
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="translate-x-4 opacity-0"
                enter-to-class="translate-x-0 opacity-100"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0 -translate-x-4"
            >
                <div
                    v-for="toast in toasts"
                    :key="toast.id"
                    :class="['pointer-events-auto w-full overflow-hidden rounded-2xl border p-4 shadow-lg', toastClasses(toast.type)]"
                >
                    <div class="flex items-start gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-white/80 text-sm font-semibold shadow-sm">
                            <span v-if="toast.type === 'success'">✓</span>
                            <span v-else-if="toast.type === 'error'">✕</span>
                            <span v-else>!</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold capitalize">{{ toast.type }}</p>
                            <p class="mt-1 text-sm leading-5">{{ toast.message }}</p>
                        </div>
                        <button
                            type="button"
                            class="ml-2 rounded-full p-1 text-slate-700 transition hover:bg-slate-200 hover:text-slate-900"
                            @click="removeToast(toast.id)"
                        >
                            <span class="sr-only">Tutup</span>
                            ×
                        </button>
                    </div>
                </div>
            </TransitionGroup>
        </div>
    </Teleport>
</template>
