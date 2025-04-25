<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    user: Object,
    forApprove: {
        type: Boolean,
        default: false
    }
})

defineEmits(['approve', 'reject']);

</script>

<template>
    <div class="transition-transform duration-300 transform cursor-pointer">
        <Link :href="route('profile', user.username)" class="flex items-center gap-3 bg-[#016b83] p-3 rounded-md">
        <img :src="user.avatar_url || '/img/default_avatar.jpg'" class="w-[38px] rounded-full" />
        <div class="flex justify-between items-center flex-1">
            <h3 class="font-bold text-lg text-white">{{ user.name }}</h3>
            <div v-if="forApprove" class="flex gap-1">
                <button @click.prevent.stop="$emit('approve', user)" class="px-3 py-1 bg-gray-800 text-white text-sm rounded-md hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-700">Approve</button>
                <button @click.prevent.stop="$emit('reject', user)" class="px-3 py-1 bg-red-500 text-white text-sm rounded-md hover:bg-red-600 focus:bg-red-600 active:bg-red-600">Reject</button>
            </div>
        </div>
        </Link>
    </div>
</template>
