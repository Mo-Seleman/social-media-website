<script setup>
import { Head, Link } from '@inertiajs/vue3';
import GroupList from '../Components/app/GroupList.vue';
import FollowingList from '../Components/app/FollowingList.vue';
import CreatePost from '../Components/app/CreatePost.vue';
import PostList from '../Components/app/PostList.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    posts: Object,
    groups: Array,
});

function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
    document.getElementById('docs-card')?.classList.add('!row-span-1');
    document.getElementById('docs-card-content')?.classList.add('!flex-row');
    document.getElementById('background')?.classList.add('!hidden');
}
</script>

<template>
    <Head title="Social Media Website" />
       <AuthenticatedLayout>
        <main>
            <div class="grid tablet:grid-cols-12 gap-3 mx-5">
                <div class="hidden tablet:block col-span-3">
                    <GroupList :groups="groups"/>
                </div>
                <div class="col-span-6 h-lvh overflow-y-scroll">
                    <CreatePost />
                    <PostList :posts="posts.data"/>
                </div>
                <div class="hidden tablet:block col-span-3">
                    <FollowingList />
                </div>
            </div>
        </main>
       </AuthenticatedLayout>
</template>