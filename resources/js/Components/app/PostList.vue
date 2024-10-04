<script setup>
import PostItem from './PostItem.vue';
const post1 = {
    user: {
        id: 1,
        avatar: 'https://randomuser.me/api/portraits/men/88.jpg',
        name: 'Elon Musk'
    },
    group: null,
    attachments: [
        {
            id: 1,
            name: 'test.png',
            url: 'https://picsum.photos/1000',
            mime: 'image/png'
        },
        {
            id: 2,
            name: 'test2.png',
            url: 'https://picsum.photos/1000',
            mime: 'image/png'
        },
        {
            id: 3,
            name: 'MyDocument.docx',
            url: '#',
            mime: 'application/msword'
        }
    ],
    body: `
        <p>Post Description line One. Testing a Post Desciption. Need Some More Text In Here To Test Out The Read More Read Less Functionality.</p>
        <p>Post Description line Two. Checking How It Will Look When A User Creates a Post. Its Actually Smart The Way He Done It Im Not Sure Wether I Would Have Thought Of That.</p>
    `,
    created_at: '2023-11-19 15:12'
};

const post2 = {
    user: {
        id: 2,
        avatar: 'https://randomuser.me/api/portraits/men/89.jpg',
        name: 'Jeff Bezoz'
    },
    group: {
        id: 1,
        name: 'Laravel Developers'
    },
    body: `
        <p>Post Description line One. Testing a Post Desciption. Need Some More Text In Here To Test Out The Read More Read Less Functionality.</p>
        <p>Post Description line Two. Checking How It Will Look When A User Creates a Post. Its Actually Smart The Way He Done It Im Not Sure Wether I Would Have Thought Of That.</p>
    `,
    created_at: '2023-11-19 14:54'
};

</script>

<template>
    <PostItem :post="post1"/>
    <PostItem :post="post2"/>
    <PostItem :post="post2"/>
    <PostItem :post="post2"/>
    <PostItem :post="post2"/>
    <PostItem :post="post1"/>
    <PostItem :post="post2"/>
    <PostItem :post="post2"/>
    <PostItem :post="post2"/>
    <PostItem :post="post2"/>
</template>