<script setup>

defineProps({
    preview: {
        type: Object,
        required: true,
    },
    url: {
        type: String,
        required: false,
    },
});

</script>

<template>
<div>
    <a v-if="preview.title" :href="url" target="_blank">
        <img :src="preview.image" :alt="preview.title" class="max-w-full rounded-xl">
        <div class="p-2">
            <h3 class="font-semibold">{{preview.title}}</h3>
            <p class="text-sm text-gray-600 pb-2">{{preview.description}}</p>
        </div>
    </a>
</div>
</template>