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
        <button @click="showCreatePostModal" class="flex items-center justify-center mb-1 py-4 w-full border-1 border-gray-300 text-white text-md mobile:text-lg rounded-md shadow-sm bg-[#016b83] hover:bg-[#018aa8] dark:bg-gray-300 dark:text-gray-700 dark: font-semibold">Click here to create a new post <PencilSquareIcon class="size-5 mx-2"/> </button>
        <PostModal :post="newPost" :group="group" v-model="showModal"></PostModal>
    </div>
</template>