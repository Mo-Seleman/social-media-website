<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    errorTitle: String,
    body: String,
});
</script>

<template>
    <AuthenticatedLayout>
        <div class="flex items-center justify-center h-[100vh]">
            <div class="text-xl bg-red-300 text-white uppercase font-bold text-center p-4 border-l-4 border-l-red-500">{{ errorTitle }}</div>
        </div>
    </AuthenticatedLayout>
</template>
