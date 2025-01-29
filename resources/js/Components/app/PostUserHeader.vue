<script setup>
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
        <a href="javascript:void(0)">
            <img :src="post.user.avatar_url" class="w-[52px] rounded-full">
        </a>
        <div>
            <div class="flex flex-row gap-2 font-bold text-lg">
                <h4 class="hover:underline">{{ post.user.name }}</h4>
                <template v-if="post.group">
                    >
                    <a href="javascript:void(0)" class="hover:text-[#ff4f40] hover:animate-bounce">{{ post.group.name }}</a>
                </template>
            </div>
            <small v-if="showTime">{{ post.updated_at }}</small>
        </div>
    </div>
</template>

