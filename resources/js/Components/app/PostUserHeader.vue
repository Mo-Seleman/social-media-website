<script setup>
import { ChevronRightIcon } from '@heroicons/vue/24/solid';
import { Link } from '@inertiajs/vue3';
defineProps({
    post: {
        type: Object
    },
    showTime: {
        type: Boolean,
        default: true
    }
});
</script>

<template>
    <div class="flex items-center gap-2">
        <Link :href="route('profile', post.user.username)">
        <img :src="post.user.avatar_url" class="w-[52px] rounded-full">
        </Link>
        <div>
            <div class="flex flex-row gap-2 font-semibold text-lg">
                <Link :href="route('profile', post.user.username)">
                    <h4 class="hover:underline">{{ post.user.name }}</h4>
                </Link> 
                <template v-if="post.group">
                    <ChevronRightIcon class="w-4" />
                    <Link :href="route('group.profile', post.group.slug)" class="hover:underline">{{ post.group.name }}</Link>
                </template>
            </div>
            <small v-if="showTime">{{ post.updated_at }}</small>
        </div>
    </div>
</template>
