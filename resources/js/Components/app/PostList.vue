<script setup>
import PostItem from './PostItem.vue';
const post1 = {
    user: {
        id: 1,
        avatar: 'https://randomuser.me/api/portraits/men/88.jpg',
        name: 'John Smith'
    },
    group: null,
    body: `
        <p>Post Description line One</p>
        <p>Post Description line Two</p>
    `,
    created_at: '2023-11-19 15:12'
};

</script>

<template>
    <PostItem :post="post1" />
    <PostItem />
</template>