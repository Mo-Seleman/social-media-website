<script setup>

    defineProps({
        post: Object
    });

</script>

<template>
    <div>
        <!-- <div>
            <img :src="post.user.avatar" alt="Users Avatar">
            <h4>{{ post.user.name }}</h4>
        </div>
        <div v-html="post.body"></div> -->
        <div>
            <img :src="post.user.avatar">
            <h4>{{ post.user.name }}</h4>
        </div>
        <div v-html="post.body"></div>
    </div>
</template>