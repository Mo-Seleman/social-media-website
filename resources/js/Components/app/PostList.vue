<script setup>
import { ref } from 'vue';
import PostItem from './PostItem.vue';
import PostModal from './PostModal.vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    posts: Array
})

const authUser = usePage().props.auth.user
const showEditModal = ref(false)
const editPost = ref({})

function openEditModal(post){
    editPost.value = post;  
    showEditModal.value = true
}

function onHideModal(){
    editPost.value = {
        id: null,
        body: '',
        user: authUser,
    }
}

</script>

<template>
    <div class="overflow-auto">
        <PostItem v-for="post of posts" :key="post.id" :post="post" @editClick="openEditModal"/>
        <PostModal :post="editPost" v-model="showEditModal" @hide="onHideModal"></PostModal>
    </div>
</template>