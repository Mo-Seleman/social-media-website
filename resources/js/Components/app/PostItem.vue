<script setup>
    import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'
    import PostUserHeader from './PostUserHeader.vue'
    import { router, useForm, usePage } from '@inertiajs/vue3'
    import { ChatBubbleLeftRightIcon, HandThumbUpIcon } from '@heroicons/vue/24/solid'
    import axiosClient from "@/axiosClient.js"
    import EditDeleteDropdown from './EditDeleteDropdown.vue'
    import PostAttachments from './PostAttachments.vue'
    import CommentList from './CommentList.vue'
    import { computed, ref } from 'vue'
    import UrlPreview from '../common/UrlPreview.vue'

    const props = defineProps({
        post: Object
    });

    const authUser = usePage().props.auth.user
    const group = usePage().props.group


    const emit = defineEmits(['editClick', 'attachmentClick'])

    const postBody = computed(() => props.post.body.replace(/(?:(\s+)|<p>)((#\w+)(?![^<]*<\/a>))/g, (match, group1, group2) => {
        const encodedGroup = encodeURIComponent(group2);
        return `${group1 || ''}<a href="/search/${encodedGroup}" class="hashtags">${group2}</a>`;
    }))

    const isPinned = computed(() => {
        if(group?.id){
            return group?.pinned_post_id === props.post.id
        }

        return authUser?.pinned_post_id === props.post.id
    })

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

    function pinPostToggle(){
        const form = useForm({
            forGroup: group?.id
        })

        let isPinned = false;
        if (group?.id){
           isPinned = group?.pinned_post_id === props.post.id;
        } else {
            isPinned = authUser?.pinned_post_id === props.post.id;
        }

        form.post(route('post.pinPostToggle', props.post.id), {
            preserveScroll: true,
            onSuccess: () => {
                if(group?.id){
                    group.pinned_post_id = isPinned ? null : props.post.id
                 } else {
                    authUser.pinned_post_id = isPinned ? null : props.post.id
                 }
            }
        })
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

    const isOpen = ref(false)

    const toggleOpen = () => {
    isOpen.value = !isOpen.value
    }

</script>

<template>
    <div class="p-6 mb-4 bg-[#282828] text-white dark:bg-white dark:text-gray-700 rounded-md shadow">
        <div class="flex items-center justify-between">
           <PostUserHeader :post="post" />
           <div class="flex items-center">
            <template v-if="isPinned">
                <svg class="mr-1 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M32 32C32 14.3 46.3 0 64 0L320 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-29.5 0 11.4 148.2c36.7 19.9 65.7 53.2 79.5 94.7l1 3c3.3 9.8 1.6 20.5-4.4 28.8s-15.7 13.3-26 13.3L32 352c-10.3 0-19.9-4.9-26-13.3s-7.7-19.1-4.4-28.8l1-3c13.8-41.5 42.8-74.8 79.5-94.7L93.5 64 64 64C46.3 64 32 49.7 32 32zM160 384l64 0 0 96c0 17.7-14.3 32-32 32s-32-14.3-32-32l0-96z"/></svg>
                <span class="text-gray-600 text-sm">Pinned</span>
            </template>
            <EditDeleteDropdown :user="post.user" :post="post" @edit="openEditModal" @delete="deletePost" @pin="pinPostToggle"/>
           </div>
        </div>
        <div class="mobile:px-2">
            <Disclosure v-slot="{ open }">
                <div v-if="!open || post.body.length <= 200" v-html="post.body.substring(0, 150)" class="ck-content-output py-1 mobile:py-3"/>
                <div v-if="post.preview && post.preview.title">
                    <UrlPreview :preview="post.preview" :url="previewUrl"/>
                </div>
                <template v-if="post.body.length > 200">
                    <DisclosurePanel>
                        <div v-html="postBody" class="ck-content-output py-3"/>
                    </DisclosurePanel>
                    <div class="flex justify-end">
                        <DisclosureButton class="pb-6">
                            <span class="text-[#016b83] font-medium p-2 rounded-md hover:underline">{{ open ? 'Read Less' : 'Read More' }}</span>
                        </DisclosureButton>
                    </div>
                </template>
            </Disclosure>
        </div>
        <div class="grid gap-3" :class="[ post.attachments.length === 1 ? 'grid-cols-1' : 'grid-cols-2' ]">
            <PostAttachments :attachments="post.attachments" @attachmentClick="openAttachment"/>
        </div>
        <Disclosure v-slot="{ open }">
        <div class="flex gap-3 py-4 justify-end">
            <button @click="sendReaction" class="flex justify-center items-center gap-2 text-white dark:text-gray-800 hover:scale-[0.98] transition-all">
                <span class="flex items-center justify-center bg-[#8E8E8E] p-2 rounded-full">
                    <HandThumbUpIcon class="size-5"/>
                </span>
                 {{ post.num_of_reactions }} Likes
            </button>
            <button @click="toggleOpen" class="hover:scale-[0.98] transition-all text-white font-medium p-2 rounded-md flex justify-center items-center gap-2">
                <span class="flex items-center justify-center bg-[#8E8E8E] p-2 rounded-full">
                    <ChatBubbleLeftRightIcon class="size-5"/>
                </span>
                {{ post.num_of_comments }} Comments
            </button>
        </div>
        <div class="p-3 border-t-[1.5px] pt-6 border-[#8E8E8E]">
            <CommentList :open="isOpen" :post="post" :data="{comments: post.comments}"/>
        </div>
      </Disclosure>
    </div>
</template>