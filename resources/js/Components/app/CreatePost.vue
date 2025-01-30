<script setup>

import { ref } from "vue";
import { usePage } from "@inertiajs/vue3";
import PostModal from "./PostModal.vue";
import { PencilSquareIcon } from "@heroicons/vue/24/solid";

const authUser = usePage().props.auth.user;

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
        <button @click="showCreatePostModal" class="flex items-center justify-center mb-1 py-4 w-full border-1 border-gray-300 text-white text-lg rounded-md shadow-sm bg-[#016b83] hover:bg-[#018aa8] font-bold">Click here to create a new post <PencilSquareIcon class="w-5 h-5 mx-2"/> </button>
        <PostModal :post="newPost" v-model="showModal"></PostModal>
    </div>
</template>