<script setup>
import { onMounted, onUpdated, ref, watch } from 'vue';
import PostItem from './PostItem.vue';
import PostModal from './PostModal.vue';
import { usePage } from '@inertiajs/vue3';
import AttachmentPreviewModal from './AttachmentPreviewModal.vue';
import axiosClient from "@/axiosClient.js";

const page = usePage();

const authUser = usePage().props.auth.user
const showEditModal = ref(false)
const showAttachmentsModal = ref(false)
const editPost = ref({})
const previewAttachmentsPost = ref({})
const loadMoreIntersect = ref(null)

const allPosts = ref({
    data: [],
    next: null
})

const props = defineProps({
    posts: Array
})

function openEditModal(post){
    editPost.value = post;  
    showEditModal.value = true
}

function openAttachmentPreviewModal(post, index){
    showAttachmentsModal.value = true
    previewAttachmentsPost.value = {
        post,
        index
    }
}

function onHideModal(){
    editPost.value = {
        id: null,
        body: '',
        user: authUser,
    }
}

function loadMore(){

    if (!allPosts.value.next){
        return;
    }

    axiosClient.get(allPosts.value.next)
        .then(({data}) => {
            allPosts.value.data = [...allPosts.value.data, ...data.data]
            allPosts.value.next = data.links.next
        })
}

watch(() => page.props.posts, (newPosts) => {
    console.log("New Posts", newPosts)
    if (newPosts) {
        allPosts.value = {
            data: newPosts.data,
            next: newPosts.links?.next
        }
    }
}, { immediate: true })

onMounted(() => {
    const observer = new IntersectionObserver(
        (entries) => entries.forEach(entry => entry.isIntersecting && loadMore()), {
            rootMargin: '-250px 0px 0px 0px'
        })

        observer.observe(loadMoreIntersect.value)
});

</script>

<template>
    <div class="overflow-auto">
        <PostItem v-for="post of allPosts.data" :key="post.id" :post="post" @editClick="openEditModal" @attachmentClick="openAttachmentPreviewModal"/>
        <div ref="loadMoreIntersect"></div>
        <PostModal :post="editPost" v-model="showEditModal" @hide="onHideModal"></PostModal>
        <AttachmentPreviewModal :attachments="previewAttachmentsPost.post?.attachments || []" v-model:index="previewAttachmentsPost.index" v-model="showAttachmentsModal"/>
    </div>
</template>