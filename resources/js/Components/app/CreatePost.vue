<script setup>

import { ref } from "vue";
import { usePage } from "@inertiajs/vue3";
import PostModal from "./PostModal.vue";
import { PencilSquareIcon } from "@heroicons/vue/24/solid";

const authUser = usePage().props.auth.user;

defineProps({
    group: {
        type: Object,
        default: null,
    }
})

const showModal = ref(false);
const newPost = ref({
    id: null,
    body: '',
    user: authUser
})

function showCreatePostModal(){
    showModal.value = true;
}

</script>

<template>
    <div class="py-4">
        <button @click="showCreatePostModal" class="flex items-center justify-center mb-1 py-4 w-full border-1 border-[#FFFD02] border-2 text-white text-lg rounded-md shadow-sm bg-[#282828] dark:bg-gray-300 dark:text-gray-700 font-bold">Create Post <PencilSquareIcon class="w-5 h-5 mx-2"/> </button>
        <PostModal :post="newPost" :group="group" v-model="showModal"></PostModal>
    </div>
</template>