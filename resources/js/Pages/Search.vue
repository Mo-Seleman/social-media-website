<script setup>
import GroupItem from '@/Components/app/GroupItem.vue';
import PostList from '@/Components/app/PostList.vue';
import UserListItem from '@/Components/app/UserListItem.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';


const props = defineProps({
    search: String,
    posts: Object,
    users: Array,
    groups: Array
});

</script>

<template>
    <AuthenticatedLayout>
        <div class="p-4 flex flex-col gap-3">
            <div v-if="!search.startsWith('#')" class="grid grid-cols-2 gap-3">
            <div class="border-gray-600 border-2 p-3 rounded">
                <h2 class="text-lg font-bold text-white">Users</h2>
                <UserListItem  v-if="users.length" v-for="user of users" :user="user" />
                <div v-else class="py-8 text-white">No Users Found</div>
            </div>
            <div class="text-white border-gray-600 border-2 p-3 rounded">
                <h2 class="text-lg font-bold">Groups</h2>
                <GroupItem v-if="groups.length" v-for="group of groups" :group="group" />
                <div v-else class="py-8 text-white">No Groups Found</div>
            </div>
        </div>
        <div class="border-gray-600 border-2 p-3 rounded">
            <h2 class="text-lg font-bold text-white">Posts</h2>
            <PostList v-if="posts.data.length" :posts="posts.data" class="flex-1" />
            <div v-else class="py-8 text-center text-white">
                No posts were found
            </div>
        </div>
        </div>
    </AuthenticatedLayout>
</template>
