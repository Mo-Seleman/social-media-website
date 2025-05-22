<script setup>
import { ArrowDownTrayIcon, PaperClipIcon } from '@heroicons/vue/24/solid';
import { isImage, isVideo } from '@/helpers';

defineProps({
    attachments: Array
});

defineEmits(['attachmentClick']);


</script>

<template>
    <div @click="$emit('attachmentClick', index)" v-for="(attachment, index) of attachments.slice(0, 4)" :key="index" class="relative cursor-pointer transition-all hover:scale-[0.98]">
        <div v-if="index === 3 && attachments.length > 4" class="absolute left-0 top-0 right-0 bottom-0 z-10 bg-black/60 text-white flex items-center justify-center text-2xl">
            <p>+{{ attachments.length - 4}}</p>
        </div>
        <a v-if="!isVideo(attachment)" @click.stop :href="route('post.download', attachment)" class="z-20 group-hover:opacity-100 transition-all w-7 h-7 flex items-center justify-center text-gray-100 bg-gray-700 rounded absolute right-2 top-2 cursor-pointer hover:bg-gray-800">
            <ArrowDownTrayIcon class="size-4"/>
        </a>
        <img v-if="isImage(attachment)" :src="attachment.url" class="bg-cover bg-no-repeat object-contain aspect-square rounded-lg h-full">
        <div v-else-if="isVideo(attachment)" class="flex items-center">
            <video :src="attachment.url" class="rounded-lg" autoplay muted loop></video>
        </div>
        <div v-else class="aspect-square bg-blue-100 flex flex-col items-center justify-center text-gray-500 hover:text-black active:text-black cursor-pointer rounded-lg">
            <PaperClipIcon size="4"/>
            <small>{{ attachment.name }}</small>
        </div>
    </div>
</template>