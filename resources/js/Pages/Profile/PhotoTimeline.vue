<script setup>
import { ArrowDownTrayIcon, PaperClipIcon, PhotoIcon } from '@heroicons/vue/24/solid';
import { isImage } from '@/helpers';
import { ref } from 'vue';
import AttachmentPreviewModal from '@/Components/app/AttachmentPreviewModal.vue';

defineProps({
    photos: Array,
});

const showModal = ref(false)
const currentPhotoIndex = ref({})

function openPhotoGallery(index) {
    currentPhotoIndex.value = index
    showModal.value = true
};

</script>

<template>
    <div class="grid gap-2 grid-cols-3">
        <div @click="openPhotoGallery(index)" v-for="(attachment, index) of photos" :key="index"
            class="relative cursor-pointer">
            <a @click.stop :href="route('post.download', attachment)"
                class="z-20 group-hover:opacity-100 transition-all w-7 h-7 flex items-center justify-center text-gray-100 bg-gray-700 rounded absolute right-2 top-2 cursor-pointer hover:bg-gray-800">
                <ArrowDownTrayIcon class="size-4" />
            </a>
            <img v-if="isImage(attachment)" :src="attachment.url"
                class="bg-cover bg-no-repeat object-contain aspect-square rounded-lg">
            <div v-else
                class="aspect-square bg-blue-100 flex flex-col items-center justify-center text-gray-500 hover:text-black active:text-black cursor-pointer rounded-lg">
                <PaperClipIcon size="4" />
                <small>{{ attachment.name }}</small>
            </div>
        </div>
    </div>
    <div v-if="!photos.length" class="flex flex-col items-center justify-center py-8">
        <PhotoIcon class="size-12 text-[#FF4F40]"/>
        <p>No Photos Yet</p>
    </div>
    <AttachmentPreviewModal :attachments="photos || []" v-model:index="currentPhotoIndex" v-model="showModal"/>
</template>