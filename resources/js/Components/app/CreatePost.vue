<script setup>

import { ref } from "vue";
import TextareaInput from "../TextareaInput.vue";
import { useForm } from "@inertiajs/vue3";

const postCreating = ref(false);

const newPostForm = useForm({
    body: ''
});

function submit(){
    newPostForm.post(route('post.store'), {
        onSuccess: () => {
            newPostForm.reset()
        }
    })
}

</script>

<template>
    <div class="py-4">
        <div @click="postCreating = true">
            <TextareaInput class="mb-3 w-full" placeholder="Click Here To Create New Post" v-model="newPostForm.body" />
        </div>
        <!-- <pre class="text-white text-xl">
            {{ newPostForm.body }}
        </pre> -->
        <div v-if="postCreating" class="flex flex-row justify-between gap-2">
            <button type="button" class="bg-[#016b83] p-2 text-white font-bold rounded-md relative cursor-pointer hover:bg-[#018aa8]">Attach Files <input type="file" class="absolute left-0 top-0 right-0 bottom-0 opacity-0 cursor-pointer"></button>
            <button @click="submit" type="submit" class="bg-[#016b83] p-2 text-white font-bold rounded-md hover:bg-[#018aa8]">Submit</button>
        </div>
    </div>
</template>