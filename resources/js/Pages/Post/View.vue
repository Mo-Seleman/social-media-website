<script setup>
import PostItem from '@/Components/app/PostItem.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import PostModal from '@/Components/app/PostModal.vue';
import AttachmentPreviewModal from '@/Components/app/AttachmentPreviewModal.vue';

defineProps({
    post: Object
});

const authUser = usePage().props.auth.user
const showEditModal = ref(false)
const showAttachmentsModal = ref(false)
const editPost = ref({})
const previewAttachmentsPost = ref({})

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

</script>

<template>
    <AuthenticatedLayout>
        <div class="my-16 mx-auto max-w-[60%] scroll-smooth">
            <PostItem :post="post" @editClick="openEditModal" @attachmentClick="openAttachmentPreviewModal"/>
        </div>
        <PostModal :post="editPost" v-model="showEditModal" @hide="onHideModal"></PostModal>
        <AttachmentPreviewModal :attachments="previewAttachmentsPost.post?.attachments || []" v-model:index="previewAttachmentsPost.index" v-model="showAttachmentsModal"/>
    </AuthenticatedLayout>
</template>

