<script setup>
    import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
    import { PencilIcon, TrashIcon, EllipsisVerticalIcon } from '@heroicons/vue/20/solid'

    import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue';
    import { ref } from 'vue';
    import PostUserHeader from './PostUserHeader.vue';
    import { router } from '@inertiajs/vue3';
    import { isImage } from '@/helpers';
 
    const props = defineProps({
        post: Object
    });

    const emit = defineEmits(['editClick'])

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

</script>

<template>
    <div class="px-6 pt-6 mb-4 bg-[#f2f8f3] rounded-md shadow">
        <div class="flex items-center justify-between">
           <PostUserHeader :post="post" />
                <Menu as="div" class="relative inline-block text-left">
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
                        <button
                            @click="deletePost"
                            :class="[
                            active ? 'bg-[#016b83] text-white' : 'text-gray-900',
                            'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                            ]"
                        >
                            <TrashIcon
                            class="mr-2 h-4 w-4"
                            aria-hidden="true"
                            />
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
        <div class="grid grid-cols-2 desktop:grid-cols-3 gap-3">
            <div v-for="(attachment, index) of post.attachments" :key="index" class="relative">
                <button :class="['w-6 h-6 absolute right-3 top-3 cursor-pointer rounded-sm', { 'text-white bg-black': showPostMenu, 'text-black bg-white': !showPostMenu }]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" />
                    </svg>
                </button>
                <Transition>
                    <div v-if="showPostMenu" class="absolute right-3 top-11 cursor-pointer">
                        <div class="bg-white rounded-lg text-gray-600">
                            <p class="hover:text-black w-[130px] px-3 py-1">Download</p>
                            <p class="hover:text-black w-[130px] px-3 py-1">Share</p>
                        </div>
                    </div>
                </Transition>
                <img v-if="isImage(attachment)" :src="attachment.url" class="bg-cover bg-no-repeat object-cover aspect-square rounded-lg">
                <div v-else class="aspect-square bg-blue-100 flex flex-col items-center justify-center text-gray-500 hover:text-black active:text-black cursor-pointer rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>

                    {{ attachment.name }}
                </div>
            </div>
        </div>
        <div class="flex gap-3 py-4">
            <button class="bg-[#016b83] min-w-[100px] hover:bg-[#018aa8] text-white font-bold p-2 rounded-md flex justify-center items-center gap-1 flex-1">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.25c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282m0 0h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 0 1-2.649 7.521c-.388.482-.987.729-1.605.729H13.48c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 0 0-1.423-.23H5.904m10.598-9.75H14.25M5.904 18.5c.083.205.173.405.27.602.197.4-.078.898-.523.898h-.908c-.889 0-1.713-.518-1.972-1.368a12 12 0 0 1-.521-3.507c0-1.553.295-3.036.831-4.398C3.387 9.953 4.167 9.5 5 9.5h1.053c.472 0 .745.556.5.96a8.958 8.958 0 0 0-1.302 4.665c0 1.194.232 2.333.654 3.375Z" />
                </svg>
                Like
            </button>
            <button class="bg-[#016b83] min-w-[100px] hover:bg-[#018aa8] text-white font-bold p-2 rounded-md flex justify-center items-center gap-1 flex-1">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                </svg>
                Comment
            </button>
        </div>
    </div>
</template>