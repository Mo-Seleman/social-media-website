<script setup>
    import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'
    import PostUserHeader from './PostUserHeader.vue'
    import { router } from '@inertiajs/vue3'
    import { ChatBubbleLeftRightIcon, HandThumbUpIcon } from '@heroicons/vue/24/solid'
    import axiosClient from "@/axiosClient.js"
    import EditDeleteDropdown from './EditDeleteDropdown.vue'
    import PostAttachments from './PostAttachments.vue'
    import CommentList from './CommentList.vue'

    const props = defineProps({
        post: Object
    });

    const emit = defineEmits(['editClick', 'attachmentClick'])

    function openEditModal(){
        emit('editClick', props.post)
    }

    function deletePost(){
        if(window.confirm("Are you sure you want to delete this post")){
            router.delete(route('post.destroy', props.post), {
                preserveScroll: true
            })        
        }
    }

    function openAttachment(index){
        emit('attachmentClick', props.post, index)
    }

    function sendReaction(){
        axiosClient.post(route('post.reaction', props.post), {
            reaction: 'like'
        })

        .then(({ data }) => {
            props.post.current_user_has_reaction = data.current_user_has_reaction
            props.post.num_of_reactions = data.num_of_reactions
        })
    }

</script>

<template>
    <div class="p-6 mb-4 bg-[#f2f8f3] rounded-md shadow">
        <div class="flex items-center justify-between">
           <PostUserHeader :post="post" />
           <EditDeleteDropdown :user="post.user"  @edit="openEditModal" @delete="deletePost"/>
        </div>
        <div>
            <Disclosure v-slot="{ open }">
                <div v-if="!open || post.body.length <= 200" v-html="post.body.substring(0, 150)" class="ck-content-output py-3"/>
                <template v-if="post.body.length > 200">
                    <DisclosurePanel>
                        <div v-html="post.body" class="ck-content-output py-3"/>
                    </DisclosurePanel>
                    <div class="flex justify-end">
                        <DisclosureButton class="pb-6">
                            <span class="text-[#016b83] font-bold p-2 rounded-md hover:underline">{{ open ? 'Read Less' : 'Read More' }}</span>
                        </DisclosureButton>
                    </div>
                </template>
            </Disclosure>
        </div>
        <div class="grid gap-3" :class="[ post.attachments.length === 1 ? 'grid-cols-1' : 'grid-cols-2' ]">
            <PostAttachments :attachments="post.attachments" @attachmentClick="openAttachment"/>
        </div>
        <Disclosure v-slot="{ open }">
        <div class="flex gap-3 py-4">
            <button @click="sendReaction" class="min-w-[100px] text-white font-bold p-2 rounded-md flex justify-center items-center gap-1 flex-1" :class="[post.current_user_has_reaction ? 'bg-[#018aa8] hover:bg-[#016b83]' : 'bg-[#016b83] hover:bg-[#018aa8]']">
                <HandThumbUpIcon class="size-5 mr-1"/>
                {{ post.current_user_has_reaction ? 'Unlike' : 'Like' }} ({{ post.num_of_reactions }})
            </button>
            <DisclosureButton class="bg-[#016b83] min-w-[100px] hover:bg-[#018aa8] text-white font-bold p-2 rounded-md flex justify-center items-center gap-1 flex-1">
                <ChatBubbleLeftRightIcon class="size-5 mr-1"/>
                Comment ({{ post.num_of_comments }})
            </DisclosureButton>
        </div>
        <DisclosurePanel class="bg-gray-200 p-5">
            <CommentList :post="post" :data="{comments: post.comments}"/>
        </DisclosurePanel>
      </Disclosure>
    </div>
</template>