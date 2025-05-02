<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue'
import { TrashIcon } from '@heroicons/vue/24/solid';

defineProps({
    user: Object,
    forApprove: {
        type: Boolean,
        default: false
    },
    showRoleDropdown: {
        type: Boolean,
        default: false
    },
    disableRoleDropdown: {
        type: Boolean,
        default: false
    }
})

defineEmits(['approve', 'reject', 'roleChange', 'delete']);

const selected = ref('');

</script>

<template>
    <div class="transition-transform duration-300 transform">
        <div class="flex items-center gap-3 bg-[#016b83] p-3 rounded-md">
            <Link :href="route('profile', user.username)">
                <img :src="user.avatar_url" class="w-[38px] rounded-full" />
            </Link>
            <div class="flex justify-between items-center flex-1">
                <Link :href="route('profile', user.username)">
                    <h3 class="semibold text-lg text-white hover:underline">{{ user.name }}</h3>
                </Link>
                <div v-if="forApprove" class="flex gap-1">
                    <button @click.prevent.stop="$emit('approve', user)"
                        class="px-3 py-1 bg-gray-800 text-white text-sm rounded-md hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-700">Approve</button>
                    <button @click.prevent.stop="$emit('reject', user)"
                        class="px-3 py-1 bg-red-500 text-white text-sm rounded-md hover:bg-red-600 focus:bg-red-600 active:bg-red-600">Reject</button>
                </div>
                <div v-if="showRoleDropdown" class="flex justify-center gap-2">
                    <select @change="$emit('roleChange', user, $event.target.value)" :disabled="disableRoleDropdown" class="rounded-md bg-black/30 py-1.5 pr-8 pl-3 text-base text-white border-none sm:text-sm/6">
                        <option :selected="user.role === 'admin'">Admin</option>
                        <option :selected="user.role === 'user'">User</option>
                    </select>
                    <button @click="$emit('delete', user)" :disabled="disableRoleDropdown" class="text-xs px-3 rounded text-white font-medium bg-red-600/90 hover:bg-red-600 disabled:bg-gray-500">
                        <TrashIcon class="size-5"/>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>