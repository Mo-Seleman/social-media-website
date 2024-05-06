<script setup>
import {ref} from "vue";

const postCreating = ref(false);

</script>

<template>
    <div class="py-4">
        <div @click="postCreating = true">
            <p class="py-3 px-2 border-2 border-gray-300 rounded mb-3 cursor-pointer">Click Here To Create New Post</p>
        </div>
        <div v-if="postCreating" class="flex flex-row justify-between gap-2">
            <button class="bg-[#016b83] p-2 text-white font-bold rounded-md relative cursor-pointer hover:bg-[#018aa8]">Attach Files <input type="file" class="absolute left-0 top-0 right-0 bottom-0 opacity-0 cursor-pointer"></button>
            <button class="bg-[#016b83] p-2 text-white font-bold rounded-md hover:bg-[#018aa8]">Submit</button>
        </div>
    </div>
</template>