<script setup>
    import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
    import { PencilIcon, TrashIcon, EllipsisVerticalIcon } from '@heroicons/vue/20/solid'
    import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue';
    import { ref } from 'vue';
    import PostUserHeader from './PostUserHeader.vue';
    import { router, usePage } from '@inertiajs/vue3';
    import { isImage } from '@/helpers';
    import { ArrowDownTrayIcon, ChatBubbleLeftRightIcon, HandThumbUpIcon, PaperClipIcon } from '@heroicons/vue/24/solid';
    import axiosClient from "@/axiosClient.js"
    import TextareaInput from '../TextareaInput.vue';
    import IndigoButton from './indigoButton.vue';

    const authUser = usePage().props.auth.user
 
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

</script>

<template>
    <div class="p-6 mb-4 bg-[#f2f8f3] rounded-md shadow">
        <div class="flex items-center justify-between">
           <PostUserHeader :post="post" />
                <Menu as="div" class="relative z-10 inline-block text-left">
                <div>
                    <MenuButton class="w-8 h-8 rounded-full hover:bg-black/5 transition flex items-center justify-center">
                        <EllipsisVerticalIcon class="w-5 h-5" aria-hidden="true"/>
                    </MenuButton>
                </div>

                <transition
                    enter-active-class="transition duration-100 ease-out"
                    enter-from-class="transform scale-95 opacity-0"
                    enter-to-class="transform scale-100 opacity-100"
                    leave-active-class="transition duration-75 ease-in"
                    leave-from-class="transform scale-100 opacity-100"
                    leave-to-class="transform scale-95 opacity-0"
                >
                    <MenuItems class="absolute right-0 mt-2 w-44 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none">
                    <div class="px-1 py-1">
                        <MenuItem v-slot="{ active }">
                        <button @click="openEditModal" :class="[ active ? 'bg-[#016b83] text-white' : 'text-gray-900', 'group flex w-full items-center rounded-md px-2 py-2 text-sm', ]" >
                            <PencilIcon class="mr-2 h-4 w-4" aria-hidden="true" />
                            Edit
                        </button>
                        </MenuItem>
                        <MenuItem v-slot="{ active }">
                        <button @click="deletePost" :class="[ active ? 'bg-[#016b83] text-white' : 'text-gray-900', 'group flex w-full items-center rounded-md px-2 py-2 text-sm', ]">
                            <TrashIcon class="mr-2 h-4 w-4" aria-hidden="true" />
                            Delete
                        </button>
                        </MenuItem>
                    </div>
                    </MenuItems>
                </transition>
                </Menu>
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
                        <IndigoButton @click="createComment" class="w-[80px] rounded-l-none">Submit</IndigoButton>
                    </div>
                </div>
                <div class="overflow-scroll max-h-[440px]">
                    <div v-for="comment of post.comments" :key="comment.id" class="mb-4">
                        <div class="flex gap-2">
                            <a href="javascript:void(0)">
                                <img :src="comment.user.avatar_url" class="w-[52px] rounded-full">
                            </a>
                            <div>
                                <h4 class="hover:underline">{{ comment.user.name }}</h4>
                                <small class="text-xs text-gray-500">{{ comment.updated_at }}</small>
                            </div>
                        </div>
                        <div class="text-sm flex flex-1 ml-16 [line-break:anywhere]" v-html="comment.comment">
                        </div>
                    </div>
                </div>
        </DisclosurePanel>
      </Disclosure>
    </div>
</template>