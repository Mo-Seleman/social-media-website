<script setup>
    import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
    import { PencilIcon, TrashIcon, EllipsisVerticalIcon } from '@heroicons/vue/20/solid'

    import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue';
    import { ref } from 'vue';
    import PostUserHeader from './PostUserHeader.vue';
    import { router } from '@inertiajs/vue3';
    import { isImage } from '@/helpers';
import { ArrowDownTrayIcon, ChatBubbleLeftRightIcon, HandThumbUpIcon } from '@heroicons/vue/24/solid';
 
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
            <div v-for="(attachment, index) of post.attachments.slice(0, 4)" :key="index" class="relative">
                <div v-if="index === 3 && post.attachments.length > 4" class="absolute left-0 top-0 right-0 bottom-0 z-10 bg-black/60 text-white flex items-center justify-center text-2xl">
                    <p>+{{ post.attachments.length - 4}}</p>
                </div>
                <a :href="route('post.download', attachment)" class="z-20 group-hover:opacity-100 transition-all w-8 h-8 flex items-center justify-center text-gray-100 bg-gray-700 rounded absolute right-2 top-2 cursor-pointer hover:bg-gray-800">
                    <ArrowDownTrayIcon class="size-4"/>
                </a>
                <img v-if="isImage(attachment)" :src="attachment.url" class="bg-cover bg-no-repeat object-contain aspect-square rounded-lg">
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
                <HandThumbUpIcon class="size-5 mr-1"/>
                Like
            </button>
            <button class="bg-[#016b83] min-w-[100px] hover:bg-[#018aa8] text-white font-bold p-2 rounded-md flex justify-center items-center gap-1 flex-1">
                <ChatBubbleLeftRightIcon class="size-5 mr-1"/>
                Comment
            </button>
        </div>
    </div>
</template>