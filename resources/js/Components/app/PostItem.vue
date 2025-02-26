<script setup>
    import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue';
    import { ref } from 'vue';
    import PostUserHeader from './PostUserHeader.vue';
    import { router, usePage } from '@inertiajs/vue3';
    import { isImage } from '@/helpers';
    import { ArrowDownTrayIcon, ChatBubbleLeftEllipsisIcon, ChatBubbleLeftRightIcon, HandThumbUpIcon, PaperClipIcon } from '@heroicons/vue/24/solid';
    import { HeartIcon } from '@heroicons/vue/24/outline'
    import axiosClient from "@/axiosClient.js"
    import TextareaInput from '../TextareaInput.vue';
    import IndigoButton from './indigoButton.vue';
    import EditDeleteDropdown from './EditDeleteDropdown.vue';

    const authUser = usePage().props.auth.user
    const editingComment = ref({})
    const props = defineProps({
        post: Object
    });

    const newCommentText = ref('')

    const emit = defineEmits(['editClick', 'attachmentClick'])

    const showPostMenu = ref(false);
    const modals = ref([]);

    const togglePostMenu = (index) => {
      if (modals.value[index] === undefined) {
        modals.value[index] = false;
      }

      modals.value[index] = !modals.value[index];
    };


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

    function createComment(){
        axiosClient.post(route('post.comment.create', props.post), {
            comment: newCommentText.value
        })

        .then(({ data }) => {
            newCommentText.value = ''
            props.post.comments.unshift(data)
            props.post.num_of_comments++;            
        })
    }

    function deleteComment(comment) {
        if (window.confirm('Are you sure you want to delete this comment?')) {
                axiosClient.delete(route('comment.delete', comment.id))
                .then(({ data }) => {
                    props.post.comments = props.post.comments.filter(c => c.id !== comment.id)
                    props.post.num_of_comments--;
                })
        } {
            return false;
        }
    }

    function startCommentEdit(comment){
        console.log(comment);
        editingComment.value = {
            id: comment.id,
            comment: comment.comment.replace(/(<br\s*\/?>\s*)+/gi, '\n')
        }
    }

    function updateComment(){
        axiosClient.put(route('comment.update', editingComment.value.id), {
            comment: editingComment.value.comment
        })

        .then(({ data }) => {
            editingComment.value = null
            props.post.comments = props.post.comments.map((c) => {
                if (c.id === data.id){
                    return data
                }
                return c;
            })
        })
    }

    function sendCommentReaction(comment){
        axiosClient.post(route('comment.reaction', comment.id), {
            reaction: 'like'
        })

        .then(({ data }) => {
           comment.current_user_has_reaction = data.current_user_has_reaction
           comment.num_of_reactions = data.num_of_reactions
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
            <div @click="openAttachment(index)" v-for="(attachment, index) of post.attachments.slice(0, 4)" :key="index" class="relative cursor-pointer">
                <div v-if="index === 3 && post.attachments.length > 4" class="absolute left-0 top-0 right-0 bottom-0 z-10 bg-black/60 text-white flex items-center justify-center text-2xl">
                    <p>+{{ post.attachments.length - 4}}</p>
                </div>
                <a @click.stop :href="route('post.download', attachment)" class="z-20 group-hover:opacity-100 transition-all w-7 h-7 flex items-center justify-center text-gray-100 bg-gray-700 rounded absolute right-2 top-2 cursor-pointer hover:bg-gray-800">
                    <ArrowDownTrayIcon class="size-4"/>
                </a>
                <img v-if="isImage(attachment)" :src="attachment.url" class="bg-cover bg-no-repeat object-contain aspect-square rounded-lg">
                <div v-else class="aspect-square bg-blue-100 flex flex-col items-center justify-center text-gray-500 hover:text-black active:text-black cursor-pointer rounded-lg">
                    <PaperClipIcon size="4"/>
                    <small>{{ attachment.name }}</small>
                </div>
            </div>
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
                <div class="flex gap-2 mb-3">
                    <a href="javascript:void(0)">
                        <img :src="authUser.avatar_url" class="w-[52px] rounded-full">
                    </a>
                    <div class="flex flex-1">
                        <TextareaInput v-model="newCommentText" rows="1" maxlength="400" class="w-full overflow-auto resize-none rounded-r-none max-h-[85px]" placeholder="Enter your comment here"/>
                        <IndigoButton @click="createComment" class="w-[100px] rounded-l-none">Submit</IndigoButton>
                    </div>
                </div>
                <div class="overflow-scroll max-h-[440px]">
                    <div v-for="comment of post.comments" :key="comment.id" class="mb-6">
                        <div class="flex justify-between gap-2">
                            <div class="flex gap-2">
                                <a href="javascript:void(0)">
                                    <img :src="comment.user.avatar_url" class="w-[52px] rounded-full">
                                </a>
                                <div>
                                    <h4 class="hover:underline">{{ comment.user.name }}</h4>
                                    <small class="text-xs text-gray-500">{{ comment.updated_at }}</small>
                                </div>
                            </div>
                            <EditDeleteDropdown :user="comment.user" @edit="startCommentEdit(comment)" @delete="deleteComment(comment)"/>
                        </div>
                        <div v-if="editingComment && editingComment.id === comment.id" class="ml-12 max-w-[90%]">
                            <TextareaInput v-model="editingComment.comment" rows="1" maxlength="400" class="w-full overflow-auto resize-none rounded-r-none max-h-[85px] px-[0.94rem]" placeholder="Enter your comment here"/>
                            <div class="flex gap-2 justify-end ">
                                <button @click="editingComment = {}" class="text-gray-800">cancel</button>
                                <IndigoButton @click="updateComment" class="w-[100px] rounded-l-none">update</IndigoButton>
                            </div>
                        </div>
                        <div v-else class="pl-16">
                            <div class="flex items-center gap-5">
                                <div class="text-md flex flex-1 items-center [line-break:auto] min-h-[41px] max-w-[85%] pb-2" v-html="comment.comment"></div>
                                <div class="flex flex-col items-center">
                                    <button @click="sendCommentReaction(comment)" class="hover:scale-[1.1] transition-all">                                
                                        <HeartIcon class="size-5 stroke-[2.5]" :class="[comment.current_user_has_reaction ? 'stroke-red-500 fill-red-500' : 'stroke-gray-500']"/>
                                    </button>
                                    <span v-if="comment.num_of_reactions > 0" class="text-sm">{{ comment.num_of_reactions }}</span>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <!-- <button @click="sendCommentReaction(comment)" class="flex items-center gap-1 text-xs bg-[#ff4f40]/80 px-2 py-1 rounded-md text-white hover:scale-[1.1] transition-all font-bold tracking-wide">
                                    <HandThumbUpIcon class="size-4"/>
                                    <span class="mr-1">{{ comment.num_of_reactions }}</span>
                                    {{ comment.current_user_has_reaction ? 'Unlike' : 'Like' }}
                                </button> -->
                                <button class="flex items-center gap-1 text-xs bg-[#ff4f40]/80 px-2 py-1 rounded-md text-white hover:scale-[1.1] transition-all font-bold tracking-wide">
                                    <ChatBubbleLeftEllipsisIcon class="size-4"/>
                                    Reply
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
        </DisclosurePanel>
      </Disclosure>
    </div>
</template>