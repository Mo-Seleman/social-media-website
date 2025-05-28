<script setup>
import { PencilIcon, TrashIcon, EllipsisVerticalIcon, EyeIcon, ClipboardIcon,  } from '@heroicons/vue/20/solid'
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
import { usePage, Link } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
    post: {
        type: Object,
        default: null
    },
    comment: {
        type: Object,
        default: null
    }
})

const emits = defineEmits(['edit', 'delete', 'pin']);

const authUser = usePage().props.auth.user;
const group = usePage().props.group;

const user = computed(() =>  props.comment?.user || props.post?.user)
const isPinned = computed(() => {
        if(group?.id){
            return group?.pinned_post_id === props.post.id
        }

        return authUser?.pinned_post_id === props.post.id
    })

const editAllowed = computed(() => {
    return user.value.id === authUser.id
})

const pinAllowed = computed(() => {
    return user.value.id === authUser.id || props.post.group && props.post.group.role === 'admin'
})

const deleteAllowed = computed(() => {
    if (user.value.id === authUser.id) return true

    if (props.post.user.id === authUser.id) return true

    return !props.comment && props.post.group?.role === 'admin'
})

function copyToClipboard(){
    
    const textToCopy = route('post.view', props.post.id);
    const tempInput = document.createElement('input');
    
    tempInput.value = textToCopy;
    document.body.appendChild(tempInput);

    tempInput.select();
    document.execCommand('copy');

    document.body.removeChild(tempInput);
}

</script>

<template>
<Menu as="div" class="relative inline-block text-left">
    <div>
        <MenuButton class="z-10 w-8 h-8 rounded-full hover:bg-black/5 transition flex items-center justify-center">
            <EllipsisVerticalIcon class="w-5 h-5" aria-hidden="true"/>
        </MenuButton>
    </div>
    <transition enter-active-class="transition duration-100 ease-out" enter-from-class="transform scale-95 opacity-0" enter-to-class="transform scale-100 opacity-100" leave-active-class="transition duration-75 ease-in" leave-from-class="transform scale-100 opacity-100" leave-to-class="transform scale-95 opacity-0">
        <MenuItems class="absolute z-20 right-0 mt-2 w-44 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none">
            <div class="px-1 py-1">
                <MenuItem v-slot="{ active }">
                <Link :href="route('post.view', post.id)" :class="[ active ? 'bg-[#ff4f40] text-white' : 'text-gray-900', 'group flex w-full items-center rounded-md px-2 py-2 text-sm', ]">
                    <EyeIcon class="mr-2 size-4" aria-hidden="true" />
                    Open Post
                </Link>
                </MenuItem>
                <MenuItem v-slot="{ active }">
                <button @click="copyToClipboard" :class="[ active ? 'bg-[#ff4f40] text-white' : 'text-gray-900', 'group flex w-full items-center rounded-md px-2 py-2 text-sm', ]">
                    <ClipboardIcon class="mr-2 size-4" aria-hidden="true" />
                    Copy URL
                </button>
                </MenuItem>
                <MenuItem v-if="editAllowed" v-slot="{ active }">
                <button @click="$emit('edit')" :class="[ active ? 'bg-[#ff4f40] text-white' : 'text-gray-900', 'group flex w-full items-center rounded-md px-2 py-2 text-sm', ]" >
                    <PencilIcon class="mr-2 size-4" aria-hidden="true" />
                    Edit
                </button>
                </MenuItem>
                <MenuItem v-if="pinAllowed" v-slot="{ active }">
                <button @click="$emit('pin')" :class="[ active ? 'bg-[#ff4f40] text-white' : 'text-gray-900', 'group flex w-full items-center rounded-md px-2 py-2 text-sm', ]" >
                    <svg class="mr-2 size-4 group-hover:fill-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M32 32C32 14.3 46.3 0 64 0L320 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-29.5 0 11.4 148.2c36.7 19.9 65.7 53.2 79.5 94.7l1 3c3.3 9.8 1.6 20.5-4.4 28.8s-15.7 13.3-26 13.3L32 352c-10.3 0-19.9-4.9-26-13.3s-7.7-19.1-4.4-28.8l1-3c13.8-41.5 42.8-74.8 79.5-94.7L93.5 64 64 64C46.3 64 32 49.7 32 32zM160 384l64 0 0 96c0 17.7-14.3 32-32 32s-32-14.3-32-32l0-96z"/></svg>
                    {{ isPinned ? 'Unpin' : 'Pin' }}
                </button>
                </MenuItem>
                <MenuItem v-if="deleteAllowed" v-slot="{ active }">
                <button @click="$emit('delete')" :class="[ active ? 'bg-[#ff4f40] text-white' : 'text-gray-900', 'group flex w-full items-center rounded-md px-2 py-2 text-sm', ]">
                    <TrashIcon class="mr-2 size-4" aria-hidden="true" />
                    Delete
                </button>
                </MenuItem>
            </div>
        </MenuItems>
    </transition>
</Menu>
</template>